<?php

/**
 * Plugin Manager
 * Handles plugin installation, deletion, and Google Drive integration
 */

class PluginManager
{
    public function __construct()
    {
        // AJAX actions for plugin management
        add_action('wp_ajax_wp_fast_setup_install_plugins', array($this, 'ajax_install_plugins'));
        add_action('admin_post_wp_fast_setup_delete_plugin', array($this, 'handle_plugin_deletion'));
    }

    /**
     * AJAX handler for installing plugins
     */
    public function ajax_install_plugins()
    {
        // Verify nonce - check all possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['wp_fast_setup_nonce_plugins']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_plugins'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error('Invalid nonce');
        }

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        // For AJAX requests, be less strict with permissions
        if (!wp_doing_ajax() && !current_user_can('install_plugins')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        $plugins = isset($_POST['plugins']) ? (array) $_POST['plugins'] : array();
        $local_zips = isset($_POST['local_zips']) ? (array) $_POST['local_zips'] : array();
        $drive_files = isset($_POST['drive_files']) ? (array) $_POST['drive_files'] : array();

        $plugins = array_filter(array_map('sanitize_text_field', $plugins));
        $local_zips = array_filter(array_map('wp_basename', $local_zips));

        $sanitized_drive_files = array();
        if (!empty($drive_files) && is_array($drive_files)) {
            foreach ($drive_files as $file_id => $file_name) {
                $clean_id = sanitize_text_field($file_id);
                if (empty($clean_id)) {
                    continue;
                }
                $sanitized_drive_files[$clean_id] = sanitize_text_field($file_name);
            }
        }

        if (empty($plugins) && empty($local_zips) && empty($sanitized_drive_files)) {
            wp_send_json_error('No plugins selected');
        }

        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';

        $results = array();
        $counts = array(
            'repo' => 0,
            'local' => 0,
            'drive' => 0
        );

        foreach ($plugins as $plugin_slug) {
            $result = $this->install_single_plugin($plugin_slug);
            $results[] = $result;
            $counts['repo']++;
        }

        foreach ($local_zips as $zip_file) {
            $result = $this->install_local_zip($zip_file);
            $results[] = $result;
            $counts['local']++;
        }

        foreach ($sanitized_drive_files as $file_id => $file_name) {
            $result = $this->install_drive_zip($file_id, $file_name);
            $results[] = $result;
            $counts['drive']++;
        }

        if (empty($results)) {
            wp_send_json_error('No se pudo procesar la selección de plugins.');
        }

        wp_send_json_success(array(
            'message' => 'Plugins installation completed',
            'results' => $results,
            'counts' => $counts
        ));
    }

    /**
     * Install a single plugin
     */
    private function install_single_plugin($plugin_slug)
    {
        $plugin_slug = sanitize_text_field($plugin_slug);

        $api = plugins_api('plugin_information', array(
            'slug' => $plugin_slug,
            'fields' => array(
                'short_description' => false,
                'sections' => false,
                'requires' => false,
                'rating' => false,
                'ratings' => false,
                'downloaded' => false,
                'last_updated' => false,
                'added' => false,
                'tags' => false,
                'compatibility' => false,
                'homepage' => false,
                'donate_link' => false,
            ),
        ));

        if (is_wp_error($api)) {
            return array(
                'plugin' => $plugin_slug,
                'requested' => $plugin_slug,
                'source' => 'repo',
                'success' => false,
                'activated' => false,
                'error' => $api->get_error_message()
            );
        }

        $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
        $install_result = $upgrader->install($api->download_link);

        if (is_wp_error($install_result)) {
            return array(
                'plugin' => $plugin_slug,
                'requested' => $plugin_slug,
                'source' => 'repo',
                'success' => false,
                'activated' => false,
                'error' => $install_result->get_error_message()
            );
        }

        // Try to activate the plugin
        $plugin_file = $this->get_plugin_file($plugin_slug);
        if ($plugin_file) {
            $activate_result = activate_plugin($plugin_file);
            if (is_wp_error($activate_result)) {
                return array(
                    'plugin' => $plugin_slug,
                    'requested' => $plugin_slug,
                    'source' => 'repo',
                    'success' => true,
                    'activated' => false,
                    'error' => $activate_result->get_error_message()
                );
            }
        }

        return array(
            'plugin' => $plugin_slug,
            'requested' => $plugin_slug,
            'source' => 'repo',
            'success' => true,
            'activated' => true
        );
    }

    /**
     * Install a plugin package from the local zip-files directory
     */
    private function install_local_zip($zip_file)
    {
        $zip_file = wp_basename($zip_file);
        $zip_path = trailingslashit(WP_FAST_SETUP_PLUGIN_DIR . 'zip-files') . $zip_file;

        $result = $this->install_zip_package($zip_path, array(
            'delete_after' => false,
            'source' => 'local',
            'requested' => $zip_file,
            'display_name' => $zip_file,
        ));

        return $result;
    }

    /**
     * Install a plugin package downloaded from Google Drive
     */
    private function install_drive_zip($file_id, $file_name = '')
    {
        $file_id = sanitize_text_field($file_id);
        $display_name = $file_name ? sanitize_text_field($file_name) : $file_id;

        $result = array(
            'plugin' => $display_name,
            'requested' => $file_id,
            'source' => 'drive',
            'success' => false,
            'activated' => false
        );

        if (empty($file_id)) {
            $result['error'] = 'ID de archivo de Google Drive inválido.';
            return $result;
        }

        $api_key = get_option('wp_fast_setup_google_drive_api_key', defined('WP_FAST_SETUP_DEFAULT_API_KEY') ? WP_FAST_SETUP_DEFAULT_API_KEY : '');
        if (empty($api_key)) {
            $result['error'] = 'Google Drive API key no configurada.';
            return $result;
        }

        $download_url = sprintf('https://www.googleapis.com/drive/v3/files/%s?alt=media&key=%s', urlencode($file_id), urlencode($api_key));
        $response = wp_remote_get($download_url, array('timeout' => 60));

        if (is_wp_error($response)) {
            $result['error'] = $response->get_error_message();
            return $result;
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 200) {
            $body = wp_remote_retrieve_body($response);
            $decoded = json_decode($body, true);
            $error_message = isset($decoded['error']['message']) ? $decoded['error']['message'] : 'HTTP ' . $code;
            $result['error'] = 'Google Drive: ' . $error_message;
            return $result;
        }

        $body = wp_remote_retrieve_body($response);
        $temp_file = wp_tempnam($display_name ?: $file_id);
        if (!$temp_file) {
            $result['error'] = 'No se pudo crear un archivo temporal para la descarga.';
            return $result;
        }

        $bytes_written = file_put_contents($temp_file, $body);
        if ($bytes_written === false) {
            @unlink($temp_file);
            $result['error'] = 'No se pudo escribir el archivo ZIP temporal.';
            return $result;
        }

        $install_result = $this->install_zip_package($temp_file, array(
            'delete_after' => true,
            'source' => 'drive',
            'requested' => $file_id,
            'display_name' => $display_name,
        ));

        if (file_exists($temp_file)) {
            @unlink($temp_file);
        }

        return $install_result;
    }

    /**
     * Generic ZIP package installer helper
     */
    private function install_zip_package($package_path, $args = array())
    {
        $defaults = array(
            'delete_after' => false,
            'activate' => true,
            'source' => 'local',
            'requested' => basename($package_path),
            'display_name' => basename($package_path),
        );

        $args = wp_parse_args($args, $defaults);

        $response = array(
            'plugin' => $args['display_name'],
            'requested' => $args['requested'],
            'source' => $args['source'],
            'success' => false,
            'activated' => false
        );

        if (!file_exists($package_path)) {
            $response['error'] = sprintf('El archivo %s no existe o no es accesible.', $args['requested']);
            return $response;
        }

        $existing_plugins = get_plugins();

        $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
        $install_result = $upgrader->install($package_path);

        if ($args['delete_after'] && file_exists($package_path)) {
            @unlink($package_path);
        }

        if (is_wp_error($install_result)) {
            $response['error'] = $install_result->get_error_message();
            return $response;
        }

        if (!$install_result) {
            $response['error'] = 'No se pudo instalar el paquete ZIP.';
            return $response;
        }

        $response['success'] = true;

        $all_plugins = get_plugins();
        $new_plugins = array_diff_key($all_plugins, $existing_plugins);
        $plugin_file = '';

        if (!empty($new_plugins)) {
            $plugin_file = array_key_first($new_plugins);
        } elseif (!empty($upgrader->result) && is_array($upgrader->result)) {
            $destination_name = isset($upgrader->result['destination_name']) ? $upgrader->result['destination_name'] : '';
            if ($destination_name) {
                foreach ($all_plugins as $file => $plugin_data) {
                    if (strpos($file, trailingslashit($destination_name)) === 0) {
                        $plugin_file = $file;
                        break;
                    }
                }
            }
        }

        if ($plugin_file) {
            $response['plugin_file'] = $plugin_file;
            if (!empty($args['activate'])) {
                $activate_result = activate_plugin($plugin_file);
                if (is_wp_error($activate_result)) {
                    $response['error'] = $activate_result->get_error_message();
                } else {
                    $response['activated'] = true;
                }
            }
        } else {
            $response['warning'] = 'Plugin instalado, pero no se pudo determinar el archivo principal para activarlo automáticamente.';
        }

        return $response;
    }

    /**
     * Get plugin file path from slug
     */
    private function get_plugin_file($plugin_slug)
    {
        $plugins = get_plugins();
        foreach ($plugins as $plugin_file => $plugin_data) {
            if (strpos($plugin_file, $plugin_slug . '/') === 0) {
                return $plugin_file;
            }
        }
        return false;
    }

    /**
     * Handle plugin self-deletion
     */
    public function handle_plugin_deletion()
    {
        // Check permissions
        if (!current_user_can('delete_plugins') || !current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para eliminar plugins.', 'wp-fast-setup'));
        }

        // Check nonce for security
        if (
            !isset($_POST['wp_fast_setup_delete_nonce']) ||
            !wp_verify_nonce($_POST['wp_fast_setup_delete_nonce'], 'wp_fast_setup_delete_plugin')
        ) {
            wp_die(__('Verificación de seguridad fallida.', 'wp-fast-setup'));
        }

        // Check if this is test mode
        if (isset($_POST['test_mode']) && $_POST['test_mode'] == '1') {
            wp_die(__('Test completado exitosamente. El plugin NO fue eliminado.', 'wp-fast-setup'));
        }

        $plugin_file = plugin_basename(WP_FAST_SETUP_PLUGIN_DIR . 'wp-fast-setup-installer.php');

        // Deactivate first
        deactivate_plugins($plugin_file);

        // Delete the plugin
        $delete_result = delete_plugins(array($plugin_file));

        if ($delete_result) {
            wp_die(__('Plugin eliminado exitosamente.', 'wp-fast-setup'));
        } else {
            wp_die(__('Error al eliminar el plugin.', 'wp-fast-setup'));
        }
    }

    /**
     * Get ZIP files from Google Drive folder
     */
    public function get_drive_zip_files($api_key, $folder_id)
    {
        if (empty($api_key) || empty($folder_id)) {
            return array('error' => 'API Key o Folder ID no configurados');
        }

        // Primero intentar con la query original (archivos en el folder)
        $url = "https://www.googleapis.com/drive/v3/files?q='" . urlencode($folder_id) . "'+in+parents&key=" . urlencode($api_key);

        $response = wp_remote_get($url, array(
            'timeout' => 15,
            'headers' => array(
                'User-Agent' => 'WP-Fast-Setup/1.0'
            )
        ));

        if (is_wp_error($response)) {
            return array('error' => 'Error de conexión: ' . $response->get_error_message());
        }

        $http_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if ($http_code !== 200) {
            $error_data = json_decode($body, true);
            $error_msg = isset($error_data['error']['message']) ? $error_data['error']['message'] : 'Error HTTP ' . $http_code;
            return array('error' => 'Error de API: ' . $error_msg);
        }

        $data = json_decode($body, true);
        $zip_files = array();

        if (isset($data['files'])) {
            foreach ($data['files'] as $file) {
                if (!isset($file['name']) || pathinfo($file['name'], PATHINFO_EXTENSION) !== 'zip') {
                    continue;
                }

                $zip_files[] = array(
                    'id' => isset($file['id']) ? $file['id'] : '',
                    'name' => $file['name'],
                    'mimeType' => isset($file['mimeType']) ? $file['mimeType'] : '',
                    'modifiedTime' => isset($file['modifiedTime']) ? $file['modifiedTime'] : '',
                );
            }
        }

        return $zip_files;
    }

    /**
     * Test Google Drive connection
     */
    public function test_drive_connection($api_key, $folder_id)
    {
        $result = $this->get_drive_zip_files($api_key, $folder_id);
        return !isset($result['error']);
    }
}
