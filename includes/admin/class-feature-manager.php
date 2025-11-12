<?php

/**
 * Feature Manager
 * Handles advanced features like Google Drive, favorites, etc.
 */

class FeatureManager
{
    public function __construct()
    {
        // AJAX actions for advanced features
        add_action('wp_ajax_wp_fast_setup_activate_features', array($this, 'ajax_activate_features'));
        add_action('wp_ajax_wp_fast_setup_save_google_drive', array($this, 'ajax_save_google_drive'));
        add_action('wp_ajax_wp_fast_setup_test_google_drive', array($this, 'ajax_test_google_drive'));
        add_action('wp_ajax_wp_fast_setup_add_favorite', array($this, 'ajax_add_favorite'));
        add_action('wp_ajax_wp_fast_setup_toggle_favorite', array($this, 'ajax_toggle_favorite'));
    }

    /**
     * AJAX handler for activating features
     */
    public function ajax_activate_features()
    {
        // Verify nonce - check all possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['wp_fast_setup_nonce_features']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_features'], 'wp_fast_setup_action')) {
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
        if (!wp_doing_ajax() && !current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        try {
            $features = isset($_POST['features']) ? (array) $_POST['features'] : array();

            if (empty($features)) {
                wp_send_json_error('No se seleccionó ninguna característica.');
            }

            $results = array();

            foreach ($features as $feature) {
                $sanitized_feature = sanitize_key($feature);
                $result = $this->activate_feature($sanitized_feature);
                $results[] = $result;
            }

            wp_send_json_success(array(
                'message' => 'Características aplicadas correctamente',
                'results' => $results
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error al aplicar características: ' . $e->getMessage());
        }
    }

    /**
     * Activate a specific feature
     */
    private function activate_feature($feature)
    {
        switch ($feature) {
            case 'disable_comments':
                return $this->disable_comments_sitewide();

            case 'set_permalinks':
                return $this->set_permalinks();

            case 'hello_elementor':
                return $this->activate_hello_elementor();

            case 'create_admin':
                return $this->create_admin_user();

            default:
                return array(
                    'feature' => $feature,
                    'success' => false,
                    'error' => 'Característica no reconocida'
                );
        }
    }

    /**
     * Disable comments sitewide
     */
    private function disable_comments_sitewide()
    {
        try {
            // Close comments on all existing posts
            global $wpdb;
            $allowed_post_types = get_post_types(array('public' => true), 'names');
            if (!empty($allowed_post_types)) {
                $placeholders = implode(',', array_fill(0, count($allowed_post_types), '%s'));
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE $wpdb->posts SET comment_status = 'closed', ping_status = 'closed' WHERE post_type IN ($placeholders)",
                        $allowed_post_types
                    )
                );
            }

            // Set default comment status to closed for future posts
            update_option('default_comment_status', 'closed');
            update_option('default_page_comments', 0);
            update_option('default_ping_status', 'closed');
            update_option('default_pingback_flag', 0);
            update_option('thread_comments', 0);
            update_option('comments_per_page', 0);
            update_option('close_comments_for_old_posts', 1);
            update_option('close_comments_days_old', 0);
            update_option('comment_moderation', '1');
            update_option('comment_whitelist', '1');
            update_option('moderation_notify', 0);
            update_option('comment_registration', 1);
            update_option('comments_notify', 0);

            return array(
                'feature' => 'disable_comments',
                'success' => true,
                'message' => 'Comentarios deshabilitados en todo el sitio'
            );
        } catch (Exception $e) {
            return array(
                'feature' => 'disable_comments',
                'success' => false,
                'error' => $e->getMessage()
            );
        }
    }

    /**
     * Set permalinks structure
     */
    private function set_permalinks()
    {
        try {
            update_option('permalink_structure', '/%postname%/');

            // Flush rewrite rules
            global $wp_rewrite;
            $wp_rewrite->flush_rules();

            return array(
                'feature' => 'set_permalinks',
                'success' => true,
                'message' => 'Estructura de permalinks configurada'
            );
        } catch (Exception $e) {
            return array(
                'feature' => 'set_permalinks',
                'success' => false,
                'error' => $e->getMessage()
            );
        }
    }

    /**
     * Activate Hello Elementor theme
     */
    private function activate_hello_elementor()
    {
        try {
            $theme_slug = 'hello-elementor';

            $theme = wp_get_theme($theme_slug);
            $was_installed = $theme && $theme->exists();

            if (!$was_installed) {
                if (!current_user_can('install_themes')) {
                    return array(
                        'feature' => 'hello_elementor',
                        'success' => false,
                        'error' => 'Permisos insuficientes para instalar temas'
                    );
                }

                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/theme-install.php';
                require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

                WP_Filesystem();

                $api = themes_api('theme_information', array(
                    'slug'   => $theme_slug,
                    'fields' => array(
                        'sections' => false
                    )
                ));

                if (is_wp_error($api)) {
                    return array(
                        'feature' => 'hello_elementor',
                        'success' => false,
                        'error' => $api->get_error_message()
                    );
                }

                $skin = new Automatic_Upgrader_Skin(array('skip_header' => true));
                $upgrader = new Theme_Upgrader($skin);
                $install_result = $upgrader->install($api->download_link);

                if (is_wp_error($install_result)) {
                    return array(
                        'feature' => 'hello_elementor',
                        'success' => false,
                        'error' => $install_result->get_error_message()
                    );
                }

                if (!$install_result) {
                    return array(
                        'feature' => 'hello_elementor',
                        'success' => false,
                        'error' => 'No se pudo instalar el tema Hello Elementor.'
                    );
                }

                // Refresh theme data after installation
                $theme = wp_get_theme($theme_slug);
                if (!$theme || !$theme->exists()) {
                    return array(
                        'feature' => 'hello_elementor',
                        'success' => false,
                        'error' => 'El tema Hello Elementor no está disponible tras la instalación.'
                    );
                }
            }

            switch_theme($theme_slug);

            return array(
                'feature' => 'hello_elementor',
                'success' => true,
                'message' => $was_installed ? 'Tema Hello Elementor activado' : 'Tema Hello Elementor instalado y activado'
            );
        } catch (Exception $e) {
            return array(
                'feature' => 'hello_elementor',
                'success' => false,
                'error' => $e->getMessage()
            );
        }
    }

    /**
     * Create admin user
     */
    private function create_admin_user()
    {
        try {
            $username = 'admin';
            $email = 'admin@' . parse_url(get_site_url(), PHP_URL_HOST);
            $password = wp_generate_password(12, true);

            // Check if user already exists
            if (username_exists($username)) {
                return array(
                    'feature' => 'create_admin',
                    'success' => false,
                    'error' => 'El usuario admin ya existe'
                );
            }

            $user_id = wp_create_user($username, $password, $email);
            if (is_wp_error($user_id)) {
                return array(
                    'feature' => 'create_admin',
                    'success' => false,
                    'error' => $user_id->get_error_message()
                );
            }

            // Set as administrator
            $user = new WP_User($user_id);
            $user->set_role('administrator');

            return array(
                'feature' => 'create_admin',
                'success' => true,
                'message' => 'Usuario admin creado',
                'credentials' => array(
                    'username' => $username,
                    'password' => $password,
                    'email' => $email
                )
            );
        } catch (Exception $e) {
            return array(
                'feature' => 'create_admin',
                'success' => false,
                'error' => $e->getMessage()
            );
        }
    }

    /**
     * AJAX handler for saving Google Drive settings
     */
    public function ajax_save_google_drive()
    {
        // Verify nonce - check all possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['wp_fast_setup_nonce_features']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_features'], 'wp_fast_setup_action')) {
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
        if (!wp_doing_ajax() && !current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        try {
            $api_key = sanitize_text_field($_POST['google_drive_api_key']);
            $folder_id = sanitize_text_field($_POST['google_drive_folder_id']);

            update_option('wp_fast_setup_google_drive_api_key', $api_key);
            update_option('wp_fast_setup_google_drive_folder_id', $folder_id);

            wp_send_json_success(array(
                'message' => 'Configuración de Google Drive guardada correctamente'
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error al guardar configuración: ' . $e->getMessage());
        }
    }

    /**
     * AJAX handler for testing Google Drive configuration
     */
    public function ajax_test_google_drive()
    {
        // Verify nonce - check all possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['wp_fast_setup_nonce_features']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_features'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error('Invalid nonce');
        }

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        $api_key = sanitize_text_field($_POST['google_drive_api_key'] ?? '');
        $folder_id = sanitize_text_field($_POST['google_drive_folder_id'] ?? '');

        if (empty($api_key)) {
            $api_key = get_option('wp_fast_setup_google_drive_api_key', WP_FAST_SETUP_DEFAULT_API_KEY);
        }

        if (empty($folder_id)) {
            $folder_id = get_option('wp_fast_setup_google_drive_folder_id', WP_FAST_SETUP_DEFAULT_FOLDER_ID);
        }

        if (empty($api_key) || empty($folder_id)) {
            wp_send_json_error('Faltan la API key o el folder ID para probar la conexión.');
        }

        try {
            if (!class_exists('PluginManager')) {
                require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-plugin-manager.php';
            }
            $plugin_manager = new PluginManager();
            $result = $plugin_manager->get_drive_zip_files($api_key, $folder_id);

            if (isset($result['error'])) {
                wp_send_json_error($result['error']);
            }

            $zip_count = is_array($result) ? count($result) : 0;
            wp_send_json_success(array(
                'message' => 'Conexión correcta con Google Drive.',
                'files_found' => $zip_count
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error al probar la conexión: ' . $e->getMessage());
        }
    }

    /**
     * AJAX handler for adding favorites
     */
    public function ajax_add_favorite()
    {
        // Verify nonce - check all possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['wp_fast_setup_nonce_features']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_features'], 'wp_fast_setup_action')) {
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
        if (!wp_doing_ajax() && !current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        try {
            $plugin_slug = sanitize_text_field($_POST['plugin_slug']);
            $favorites = get_option('wp_fast_setup_favorites', array());

            if (!in_array($plugin_slug, $favorites)) {
                $favorites[] = $plugin_slug;
                update_option('wp_fast_setup_favorites', $favorites);
            }

            wp_send_json_success(array(
                'message' => 'Plugin agregado a favoritos'
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error al agregar favorito: ' . $e->getMessage());
        }
    }

    /**
     * AJAX handler for toggling favorites
     */
    public function ajax_toggle_favorite()
    {
        // Verify nonce - check all possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['wp_fast_setup_nonce_features']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_features'], 'wp_fast_setup_action')) {
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
        if (!wp_doing_ajax() && !current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        try {
            $plugin_slug = sanitize_text_field($_POST['plugin_slug']);
            $favorites = get_option('wp_fast_setup_favorites', array());

            if (in_array($plugin_slug, $favorites)) {
                $favorites = array_diff($favorites, array($plugin_slug));
                $action = 'removed';
            } else {
                $favorites[] = $plugin_slug;
                $action = 'added';
            }

            update_option('wp_fast_setup_favorites', $favorites);

            wp_send_json_success(array(
                'message' => 'Favorito ' . $action,
                'action' => $action
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error al cambiar favorito: ' . $e->getMessage());
        }
    }

    /**
     * Get favorites
     */
    public function get_favorites()
    {
        return get_option('wp_fast_setup_favorites', array());
    }
}
