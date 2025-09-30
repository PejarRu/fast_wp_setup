
<?php
// Plugin installation and management
/**
 * Instalar/activar plugins desde el repositorio
 */
class Plugin_Manager
{

    // Optional constructor if you want to hook actions/filters
    public function __construct()
    {
        // For example, you could initialize something here
    }

    /**
     * Helper to show messages only if not AJAX
     */
    private function show_message($message, $type = 'success')
    {
        if (defined('WP_FAST_SETUP_ACTION') && WP_FAST_SETUP_ACTION) {
            return;
        }
        $class = $type === 'error' ? 'error' : 'updated';
        echo '<div class="' . $class . '"><p>' . $message . '</p></div>';
    }


    /**
     * Instalar y activar plugins desde el repositorio.
     * @param string $slug Plugin slug to install.
     * @param string $plugin_file Optional relative plugin file path (default: "$slug/$slug.php")
     * @return bool|string True on success, or error message on failure.
     */
    function install_plugin($slug)
    {
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $plugin_info = plugins_api('plugin_information', ['slug' => $slug]);
        if (!is_wp_error($plugin_info)) {
            $upgrader = new Plugin_Upgrader();
            $upgrader->install($plugin_info->download_link);
            activate_plugin($slug . '/' . $slug . '.php');
        }
    }

    /**
     * Instalar y activar plugin desde un ZIP local.
     * @param string $zip_file_path Ruta al archivo ZIP.
     * @param string $plugin_slug Slug del plugin para determinar el archivo de activación.
     * @return bool|string True on success, or error message on failure.
     */
    function install_plugin_from_zip($zip_file_path, $plugin_slug = '')
    {
        if (!file_exists($zip_file_path)) {
            $this->show_message('No se encontró el archivo ZIP: ' . esc_html($zip_file_path), 'error');
            return false;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        $upgrader  = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
        $installed = $upgrader->install($zip_file_path);

        if (is_wp_error($installed)) {
            $this->show_message('Error al instalar plugin desde ZIP: ' . esc_html($installed->get_error_message()), 'error');
            return false;
        } elseif (!$installed) {
            $this->show_message('No se pudo instalar el plugin desde ZIP.', 'error');
            return false;
        }

        // Intentar determinar el archivo del plugin para activación
        $plugin_file = $this->find_plugin_file($plugin_slug);

        if ($plugin_file && file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
            $activate = activate_plugin($plugin_file);
            if (is_wp_error($activate)) {
                $this->show_message('Error al activar plugin: ' . esc_html($activate->get_error_message()), 'error');
                return false;
            } else {
                $this->show_message('Plugin instalado y activado correctamente.');
                return true;
            }
        } else {
            $this->show_message('Plugin instalado pero no se pudo determinar el archivo para activación.', 'error');
            return false;
        }
    }

    /**
     * Encuentra el archivo principal del plugin para activación
     * @param string $plugin_slug Slug del plugin
     * @return string|null Ruta relativa del archivo del plugin
     */
    private function find_plugin_file($plugin_slug)
    {
        if (empty($plugin_slug)) {
            return null;
        }

        // Intentar con el patrón estándar
        $standard_file = $plugin_slug . '/' . $plugin_slug . '.php';
        if (file_exists(WP_PLUGIN_DIR . '/' . $standard_file)) {
            return $standard_file;
        }

        // Buscar archivos PHP en el directorio del plugin
        $plugin_dir = WP_PLUGIN_DIR . '/' . $plugin_slug;
        if (is_dir($plugin_dir)) {
            $files = glob($plugin_dir . '/*.php');
            foreach ($files as $file) {
                $content = file_get_contents($file);
                // Buscar header de plugin
                if (strpos($content, 'Plugin Name:') !== false) {
                    return $plugin_slug . '/' . basename($file);
                }
            }
        }

        return null;
    }

    /**
     * Obtener lista de archivos ZIP locales disponibles
     * @return array Lista de archivos ZIP
     */
    function get_local_zip_files()
    {
        $zip_dir = WP_FAST_SETUP_PLUGIN_DIR . 'zip-files/';
        $zip_files = array();

        if (is_dir($zip_dir)) {
            $files = scandir($zip_dir);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                    $zip_files[] = $file;
                }
            }
        }

        return $zip_files;
    }

    /**
     * Obtener lista de archivos ZIP de Google Drive disponibles
     * @return array Lista de archivos ZIP con sus IDs
     */
    function get_google_drive_zip_files()
    {
        $plugins_list_file = WP_FAST_SETUP_PLUGIN_DIR . 'includes/plugins-list.json';

        if (!file_exists($plugins_list_file)) {
            return array();
        }

        $plugins_data = json_decode(file_get_contents($plugins_list_file), true);

        if (!isset($plugins_data['google_drive_zips'])) {
            return array();
        }

        $zip_files = array();
        foreach ($plugins_data['google_drive_zips'] as $filename => $file_id) {
            $zip_files[] = array(
                'name' => $filename,
                'id' => $file_id
            );
        }

        return $zip_files;
    }

    /**
     * Chequea si Elementor Pro (o Pro Elements) está activo
     */
    function has_theme_builder()
    {
        if (did_action('elementor_pro/init')) {
            return true;
        }
        if (class_exists('ProElements\Plugin')) {
            return true;
        }
        return false;
    }

    /**
     * Get ZIP files from Google Drive folder
     */
    public function get_drive_zip_files($api_key, $folder_id)
    {
        $url = "https://www.googleapis.com/drive/v3/files?q='" . urlencode($folder_id) . "'+in+parents&key=" . urlencode($api_key);
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return [];
        }
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (!isset($data['files'])) {
            return [];
        }
        $zip_files = [];
        foreach ($data['files'] as $file) {
            if (isset($file['name']) && strpos($file['name'], '.zip') !== false) {
                $zip_files[] = [
                    'id' => $file['id'],
                    'name' => $file['name']
                ];
            }
        }
        return $zip_files;
    }

    /**
     * Install plugin from Google Drive ZIP
     */
    public function install_plugin_from_drive_zip($file_id, $file_name)
    {
        // Use Google Drive API to download the file
        $api_key = get_option('wp_fast_setup_google_drive_api_key', WP_FAST_SETUP_DEFAULT_API_KEY);
        $download_url = "https://www.googleapis.com/drive/v3/files/" . urlencode($file_id) . "?alt=media&key=" . urlencode($api_key);

        $response = wp_remote_get($download_url, array(
            'timeout' => 60, // Increased timeout for large files
            'headers' => array(
                'User-Agent' => 'WP-Fast-Setup/1.0'
            )
        ));

        if (is_wp_error($response)) {
            $this->show_message('Error al descargar el archivo ZIP desde Google Drive: ' . $response->get_error_message(), 'error');
            return false;
        }

        $http_code = wp_remote_retrieve_response_code($response);
        if ($http_code !== 200) {
            $this->show_message('Error HTTP ' . $http_code . ' al descargar desde Google Drive', 'error');
            return false;
        }

        $zip_content = wp_remote_retrieve_body($response);
        if (empty($zip_content)) {
            $this->show_message('El archivo ZIP descargado está vacío.', 'error');
            return false;
        }

        // Save to temp file
        $temp_file = wp_tempnam($file_name);
        if (!$temp_file) {
            $this->show_message('No se pudo crear archivo temporal', 'error');
            return false;
        }

        $result = file_put_contents($temp_file, $zip_content);
        if ($result === false) {
            $this->show_message('Error al guardar archivo ZIP temporal', 'error');
            @unlink($temp_file);
            return false;
        }

        // Install the plugin
        $install_result = $this->install_plugin_from_zip($temp_file, $this->find_plugin_slug_from_zip($file_name));

        // Clean up temp file
        @unlink($temp_file);

        return $install_result;
    }

    /**
     * Extract plugin slug from ZIP filename
     */
    private function find_plugin_slug_from_zip($zip_filename)
    {
        // Remove .zip extension
        $name = preg_replace('/\.zip$/i', '', $zip_filename);

        // Common patterns for plugin naming
        $patterns = [
            '/^(.*)-pro$/i',
            '/^(.*)-premium$/i',
            '/^(.*)_pro$/i',
            '/^(.*)_premium$/i',
            '/^pro-(.*)$/i',
            '/^premium-(.*)$/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $name, $matches)) {
                return $matches[1];
            }
        }

        // If no pattern matches, try to use the name as-is
        return sanitize_title($name);
    }

    /**
     * Handle plugin installations
     */
    public function handle_plugin_installations()
    {
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-plugins-manager.php';
        $plugin_manager = new Plugin_Manager();

        $json_file = WP_FAST_SETUP_PLUGIN_DIR . 'includes/plugins-list.json';
        if (file_exists($json_file)) {
            $json_data = file_get_contents($json_file);
            $data = json_decode($json_data, true);
            if (isset($data['plugins']) && is_array($data['plugins'])) {
                $plugins = $data['plugins'];
            } else {
                $plugins = array();
            }
        } else {
            error_log("Plugin list JSON file not found: " . $json_file);
            $plugins = array();
        }

        foreach ($plugins as $slug => $post_key) {
            if (isset($_POST[$post_key])) {
                $plugin_manager->install_plugin($slug);
            }
        }

        // Process local ZIP installations from static inputs if any.
        $local_plugins = array(
            'pro-elements.zip'         => 'install_pro_elements_zip',
            'elementor-pro.zip'        => 'install_elementor_pro',
            'custom-fast-blog.zip'     => 'install_custom_fast_blog',
            'metadebugger.zip'         => 'install_metadebugger',
            'autoconfigurador-ase.zip' => 'install_autoase'
        );

        foreach ($local_plugins as $zip_name => $post_key) {
            if (isset($_POST[$post_key])) {
                $zip_path = WP_FAST_SETUP_PLUGIN_DIR . 'zip-files/' . $zip_name;
                $plugin_manager->install_plugin_from_zip($zip_path);
            }
        }

        // Process dynamically generated ZIP files.
        $zip_files = glob(WP_FAST_SETUP_PLUGIN_DIR . 'zip-files/*.zip');
        if ($zip_files) {
            foreach ($zip_files as $zip_file) {
                $basename   = basename($zip_file);
                $input_name = 'install_zip_' . sanitize_title($basename);
                if (isset($_POST[$input_name])) {
                    $plugin_manager->install_plugin_from_zip($zip_file);
                }
            }
        }

        // Process Google Drive ZIP files from JSON config
        $google_drive_zips = $this->get_google_drive_zip_files();
        foreach ($google_drive_zips as $file) {
            $input_name = 'install_drive_zip_' . sanitize_title($file['name']);
            if (isset($_POST[$input_name])) {
                $plugin_manager->install_plugin_from_drive_zip($file['id'], $file['name']);
            }
        }

        // Process local ZIP files as fallback
        $zip_dir = WP_FAST_SETUP_PLUGIN_DIR . 'zip-files/';
        if (is_dir($zip_dir)) {
            $zips = glob($zip_dir . '*.zip');
            if ($zips) {
                foreach ($zips as $zip_file) {
                    $basename = basename($zip_file);
                    $input_name = 'install_local_zip_' . sanitize_title($basename);
                    if (isset($_POST[$input_name])) {
                        $plugin_manager->install_plugin_from_zip($zip_file);
                    }
                }
            }
        }
    }
}
