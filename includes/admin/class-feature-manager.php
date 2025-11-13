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
        add_action('wp_ajax_wp_fast_setup_delete_inactive_plugins', array($this, 'ajax_delete_inactive_plugins'));
        add_action('wp_ajax_wp_fast_setup_delete_inactive_themes', array($this, 'ajax_delete_inactive_themes'));
        add_action('wp_ajax_wp_fast_setup_set_pages_noindex', array($this, 'ajax_set_pages_noindex'));
    }

    /**
     * AJAX handler to set Yoast noindex flag on selected pages
     */
    public function ajax_set_pages_noindex()
    {
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['wp_fast_setup_nonce_legal']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_legal'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error('Nonce inválido. Recarga la página e inténtalo de nuevo.');
        }

        if (!is_user_logged_in()) {
            wp_send_json_error('Debes iniciar sesión para realizar esta acción.');
        }

        if (!current_user_can('edit_pages')) {
            wp_send_json_error('No tienes permisos suficientes para modificar estas páginas.');
        }

        $page_ids = isset($_POST['legal_page_ids']) ? array_map('absint', (array) $_POST['legal_page_ids']) : array();
        $page_ids = array_values(array_filter($page_ids));

        if (empty($page_ids)) {
            wp_send_json_error('Selecciona al menos una página legal.');
        }

        $updated = array();
        $already = array();
        $skipped = array();

        foreach ($page_ids as $page_id) {
            $page = get_post($page_id);
            if (!$page || $page->post_type !== 'page') {
                $skipped[] = array('id' => $page_id, 'reason' => 'No es una página válida');
                continue;
            }

            if (!current_user_can('edit_post', $page_id)) {
                $skipped[] = array('id' => $page_id, 'reason' => 'Permisos insuficientes');
                continue;
            }

            $result = $this->mark_page_as_noindex($page_id);
            $title = get_the_title($page_id);

            if ('already' === $result) {
                $already[] = array('id' => $page_id, 'title' => $title);
            } elseif (true === $result) {
                $updated[] = array('id' => $page_id, 'title' => $title);
            } else {
                $skipped[] = array('id' => $page_id, 'reason' => $result ?: 'Error desconocido');
            }
        }

        if (empty($updated) && empty($already)) {
            wp_send_json_error(array(
                'message' => 'No se pudo aplicar el noindex a las páginas seleccionadas.',
                'skipped' => $skipped,
            ));
        }

        $yoast_active = class_exists('\WPSEO_Meta') || defined('WPSEO_VERSION');
        $message_parts = array();

        if (!empty($updated)) {
            $message_parts[] = sprintf('%d página(s) marcadas como noindex.', count($updated));
        }

        if (!empty($already)) {
            $message_parts[] = sprintf('%d ya estaban desindexadas.', count($already));
        }

        if (!empty($skipped)) {
            $message_parts[] = sprintf('%d página(s) se omitieron (%s).', count($skipped), implode(', ', wp_list_pluck($skipped, 'reason')));
        }

        wp_send_json_success(array(
            'message' => implode(' ', $message_parts),
            'yoast_active' => $yoast_active,
            'updated' => $updated,
            'already' => $already,
            'skipped' => $skipped,
        ));
    }

    /**
     * Apply Yoast noindex meta to a page
     *
     * @param int $page_id
     * @return true|string Returns true when updated, 'already' when it already had the flag, or error string
     */
    private function mark_page_as_noindex($page_id)
    {
        try {
            $meta_key = '_yoast_wpseo_meta-robots-noindex';
            $existing_value = get_post_meta($page_id, $meta_key, true);
            $already = ('1' === (string) $existing_value);

            if (class_exists('\\WPSEO_Meta') && method_exists('\\WPSEO_Meta', 'set_value')) {
                \WPSEO_Meta::set_value('meta-robots-noindex', '1', $page_id);
                \WPSEO_Meta::set_value('meta-robots-nofollow', '1', $page_id);
            } else {
                update_post_meta($page_id, $meta_key, '1');
                update_post_meta($page_id, '_yoast_wpseo_meta-robots-nofollow', '1');
            }

            // Ensure advanced directives reset so Yoast shows the toggles as expected
            update_post_meta($page_id, '_yoast_wpseo_meta-robots-adv', 'none');

            return $already ? 'already' : true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
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

            $feature_inputs = array(
                'create_admin' => array(
                    'username' => isset($_POST['feature_create_admin_username']) ? sanitize_user(wp_unslash($_POST['feature_create_admin_username']), true) : '',
                    'email' => isset($_POST['feature_create_admin_email']) ? sanitize_email(wp_unslash($_POST['feature_create_admin_email'])) : '',
                    'original_username' => isset($_POST['feature_create_admin_username']) ? trim(wp_unslash($_POST['feature_create_admin_username'])) : '',
                    'original_email' => isset($_POST['feature_create_admin_email']) ? trim(wp_unslash($_POST['feature_create_admin_email'])) : '',
                ),
            );

            $results = array();

            foreach ($features as $feature) {
                $sanitized_feature = sanitize_key($feature);
                $args = isset($feature_inputs[$sanitized_feature]) ? $feature_inputs[$sanitized_feature] : array();
                $result = $this->activate_feature($sanitized_feature, $args);
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
    private function activate_feature($feature, $args = array())
    {
        switch ($feature) {
            case 'disable_comments':
                return $this->disable_comments_sitewide();

            case 'set_permalinks':
                return $this->set_permalinks();

            case 'hello_elementor':
                return $this->activate_hello_elementor();

            case 'create_admin':
                return $this->create_admin_user($args);

            case 'enable_svg_upload':
                return $this->enable_svg_upload();

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
    private function create_admin_user($args = array())
    {
        try {
            $requested_username = isset($args['original_username']) ? trim($args['original_username']) : ''; // For reporting back
            $requested_email = isset($args['original_email']) ? trim($args['original_email']) : '';

            $username = isset($args['username']) ? sanitize_user($args['username'], true) : '';
            if (!$username && $requested_username) {
                $username = sanitize_user($requested_username, true);
            }

            $email = isset($args['email']) ? sanitize_email($args['email']) : '';
            if (!$email && $requested_email) {
                $email = sanitize_email($requested_email);
            }

            if (!$username) {
                return array(
                    'feature' => 'create_admin',
                    'success' => false,
                    'error' => 'Debes indicar un nombre de usuario válido para el administrador auxiliar.'
                );
            }

            if (!$email || !is_email($email)) {
                return array(
                    'feature' => 'create_admin',
                    'success' => false,
                    'error' => 'Debes indicar un correo electrónico válido para el administrador auxiliar.'
                );
            }

            if (username_exists($username)) {
                return array(
                    'feature' => 'create_admin',
                    'success' => false,
                    'error' => sprintf('El usuario "%s" ya existe en este sitio.', $username)
                );
            }

            if (email_exists($email)) {
                return array(
                    'feature' => 'create_admin',
                    'success' => false,
                    'error' => sprintf('El correo "%s" ya está asociado a otro usuario.', $email)
                );
            }

            $password = wp_generate_password(16, true);

            // Check if user already exists
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

            $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
            $login_url = wp_login_url();
            $subject = sprintf('[%s] Credenciales de acceso', $site_name);
            $message_lines = array(
                'Hola,',
                '',
                sprintf('Se ha creado un usuario administrador auxiliar en %s.', $site_name),
                '',
                sprintf('Usuario: %s', $username),
                sprintf('Contraseña: %s', $password),
                sprintf('Acceso: %s', $login_url),
                '',
                'Por seguridad, inicia sesión y cambia la contraseña cuanto antes.',
                '',
                'Este correo ha sido generado automáticamente por WP Fast Setup.'
            );
            $message = implode("\n", $message_lines);

            $email_sent = wp_mail($email, $subject, $message);

            $success_message = 'Usuario administrador auxiliar creado correctamente.';
            if (!$email_sent) {
                $success_message .= ' No se pudo enviar el correo con las credenciales.';
            }

            return array(
                'feature' => 'create_admin',
                'success' => true,
                'message' => $success_message,
                'credentials' => array(
                    'username' => $username,
                    'password' => $password,
                    'email' => $email,
                    'login_url' => $login_url,
                    'email_sent' => $email_sent,
                    'requested_username' => $requested_username ?: $username,
                    'requested_email' => $requested_email ?: $email
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
     * Enable SVG file uploads in WordPress media library
     */
    private function enable_svg_upload()
    {
        try {
            // Add SVG support option
            update_option('wp_fast_setup_svg_enabled', '1');

            return array(
                'feature' => 'enable_svg_upload',
                'success' => true,
                'message' => 'Soporte para archivos SVG habilitado correctamente'
            );
        } catch (Exception $e) {
            return array(
                'feature' => 'enable_svg_upload',
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
     * AJAX handler for deleting inactive plugins
     */
    public function ajax_delete_inactive_plugins()
    {
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_delete_inactive_plugins')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error(array(
                'message' => 'Nonce inválido para eliminar plugins inactivos.'
            ));
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => 'Debes iniciar sesión para realizar esta acción.'
            ));
        }

        if (!current_user_can('delete_plugins')) {
            wp_send_json_error(array(
                'message' => 'No tienes permisos para eliminar plugins.'
            ));
        }

        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';

        $all_plugins = get_plugins();
        if (empty($all_plugins)) {
            wp_send_json_success(array(
                'message' => 'No hay plugins instalados para revisar.',
                'deleted' => array(),
                'deleted_count' => 0,
                'errors' => array(),
                'errors_count' => 0
            ));
        }

        $network_active = array();
        if (is_multisite()) {
            $network_active = (array) get_site_option('active_sitewide_plugins', array());
        }

        $deleted = array();
        $errors = array();

        foreach ($all_plugins as $plugin_file => $plugin_data) {
            if (is_plugin_active($plugin_file)) {
                continue;
            }

            if (!empty($network_active) && isset($network_active[$plugin_file])) {
                continue;
            }

            $plugin_name = !empty($plugin_data['Name']) ? wp_strip_all_tags($plugin_data['Name']) : $plugin_file;

            $result = delete_plugins(array($plugin_file));
            if (is_wp_error($result)) {
                $errors[$plugin_name] = $result->get_error_message();
                continue;
            }

            $deleted[] = $plugin_name;
        }

        wp_clean_plugins_cache();

        if (empty($deleted) && empty($errors)) {
            wp_send_json_success(array(
                'message' => 'No hay plugins inactivos para eliminar.',
                'deleted' => array(),
                'deleted_count' => 0,
                'errors' => array(),
                'errors_count' => 0
            ));
        }

        if (empty($deleted) && !empty($errors)) {
            wp_send_json_error(array(
                'message' => 'No se pudieron eliminar los plugins inactivos.',
                'errors' => $errors,
                'errors_count' => count($errors)
            ));
        }

        $response = array(
            'message' => sprintf('Se eliminaron %d plugin(s) inactivos.', count($deleted)),
            'deleted' => $deleted,
            'deleted_count' => count($deleted)
        );

        if (!empty($errors)) {
            $response['errors'] = $errors;
            $response['errors_count'] = count($errors);
            $response['message'] .= sprintf(' %d plugin(s) no se pudieron eliminar.', count($errors));
        } else {
            $response['errors'] = array();
            $response['errors_count'] = 0;
        }

        wp_send_json_success($response);
    }

    /**
     * AJAX handler for deleting inactive themes
     */
    public function ajax_delete_inactive_themes()
    {
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_delete_inactive_themes')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error(array(
                'message' => 'Nonce inválido para eliminar temas inactivos.'
            ));
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => 'Debes iniciar sesión para realizar esta acción.'
            ));
        }

        if (!current_user_can('delete_themes')) {
            wp_send_json_error(array(
                'message' => 'No tienes permisos para eliminar temas.'
            ));
        }

        require_once ABSPATH . 'wp-admin/includes/theme.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';

        $current_theme = wp_get_theme();
        $protected_slugs = array_filter(array(
            $current_theme ? $current_theme->get_stylesheet() : '',
            $current_theme ? $current_theme->get_template() : ''
        ));

        if ($current_theme && $current_theme->parent()) {
            $protected_slugs[] = $current_theme->parent()->get_stylesheet();
            $protected_slugs[] = $current_theme->parent()->get_template();
        }

        $protected_slugs = array_filter(array_unique($protected_slugs));

        $all_themes = wp_get_themes();
        $deleted = array();
        $errors = array();

        foreach ($all_themes as $slug => $theme) {
            if (in_array($slug, $protected_slugs, true)) {
                continue;
            }

            $theme_name = wp_strip_all_tags($theme->get('Name'));
            if (empty($theme_name)) {
                $theme_name = $slug;
            }

            $result = delete_theme($slug);
            if (is_wp_error($result)) {
                $errors[$theme_name] = $result->get_error_message();
                continue;
            }

            $deleted[] = $theme_name;
        }

        wp_clean_themes_cache();

        if (empty($deleted) && empty($errors)) {
            wp_send_json_success(array(
                'message' => 'No hay temas inactivos para eliminar.',
                'deleted' => array(),
                'deleted_count' => 0,
                'errors' => array(),
                'errors_count' => 0
            ));
        }

        if (empty($deleted) && !empty($errors)) {
            wp_send_json_error(array(
                'message' => 'No se pudieron eliminar los temas inactivos.',
                'errors' => $errors,
                'errors_count' => count($errors)
            ));
        }

        $response = array(
            'message' => sprintf('Se eliminaron %d tema(s) inactivos.', count($deleted)),
            'deleted' => $deleted,
            'deleted_count' => count($deleted)
        );

        if (!empty($errors)) {
            $response['errors'] = $errors;
            $response['errors_count'] = count($errors);
            $response['message'] .= sprintf(' %d tema(s) no se pudieron eliminar.', count($errors));
        } else {
            $response['errors'] = array();
            $response['errors_count'] = 0;
        }

        wp_send_json_success($response);
    }

    /**
     * Get favorites
     */
    public function get_favorites()
    {
        return get_option('wp_fast_setup_favorites', array());
    }
}
