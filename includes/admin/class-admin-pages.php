<?php

/**
 * Admin Pages
 * Main admin interface class that orchestrates all modules
 */

class Admin_Pages
{
    private $site_settings;
    private $plugin_manager;
    private $page_creator;
    private $feature_manager;

    public function __construct()
    {
        error_log('WP Fast Setup: Admin_Pages constructor called');

        // Initialize modules
        $this->load_modules();

        // Register admin menu
        add_action('admin_menu', array($this, 'register_admin_menu'));

        // Enqueue scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Load all modules
     */
    private function load_modules()
    {
        // Load module classes
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-site-settings-handler.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-plugin-manager.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-page-creator.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-feature-manager.php';

        // Initialize modules
        $this->site_settings = new SiteSettingsHandler();
        $this->plugin_manager = new PluginManager();
        $this->page_creator = new PageCreator();
        $this->feature_manager = new FeatureManager();
    }

    /**
     * Register admin menu
     */
    public function register_admin_menu()
    {
        error_log('WP Fast Setup: Registering admin menu');

        add_menu_page(
            'WP Fast Setup',
            'WP Fast Setup',
            'manage_options',
            'wp-fast-setup',
            array($this, 'render_admin_page'),
            'dashicons-admin-tools',
            30
        );

        error_log('WP Fast Setup: Admin menu registered successfully');
    }

    /**
     * Render admin page
     */
    public function render_admin_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos para acceder a esta página.');
        }

        // Get current values from site settings module
        $current_settings = $this->site_settings->get_current_settings();
        $current_site_name = $current_settings['site_name'];
        $current_language = $current_settings['language'];
        $current_url = $current_settings['site_url'];
        $current_admin_email = $current_settings['admin_email'];
        $current_tagline = $current_settings['tagline'];
        $current_logo_id = $current_settings['logo_id'];
        $current_favicon_id = $current_settings['favicon_id'];
        $current_blog_public = $current_settings['blog_public'];
        $current_comment_status = $current_settings['comment_status'];
        $current_permalink_structure = $current_settings['permalink_structure'];
        $permalinks_postname = ($current_permalink_structure === '/%postname%/');
        $comments_disabled = ($current_comment_status === 'closed');
        $current_theme = wp_get_theme();
        $hello_elementor_active = ($current_theme && $current_theme->stylesheet === 'hello-elementor');
        $default_google_drive_api_key = WP_FAST_SETUP_DEFAULT_API_KEY;
        $default_google_drive_folder_id = WP_FAST_SETUP_DEFAULT_FOLDER_ID;
        $available_languages = $this->site_settings->get_available_languages();
        if (!empty($current_language) && !isset($available_languages[$current_language])) {
            $available_languages = array($current_language => $current_language) + $available_languages;
        }

        // Get Google Drive files dynamically
        $api_key_option = get_option('wp_fast_setup_google_drive_api_key', '');
        $folder_id_option = get_option('wp_fast_setup_google_drive_folder_id', '');
        $current_google_drive_api_key = !empty($api_key_option) ? $api_key_option : WP_FAST_SETUP_DEFAULT_API_KEY;
        $current_google_drive_folder_id = !empty($folder_id_option) ? $folder_id_option : WP_FAST_SETUP_DEFAULT_FOLDER_ID;
        $api_key = $current_google_drive_api_key;
        $folder_id = $current_google_drive_folder_id;
        $drive_zip_files = [];
        $drive_error = '';

        if (!empty($api_key) && !empty($folder_id)) {
            $result = $this->plugin_manager->get_drive_zip_files($api_key, $folder_id);
            if (isset($result['error'])) {
                $drive_error = $result['error'];
            } else {
                $drive_zip_files = $result;
            }
        } elseif (empty($api_key) || empty($folder_id)) {
            $drive_error = 'Configure su API Key y Folder ID de Google Drive para ver los plugins disponibles.';
        }

        // Get local ZIP files always
        $local_zip_files = [];
        $zip_dir = WP_FAST_SETUP_PLUGIN_DIR . 'zip-files/';
        if (is_dir($zip_dir)) {
            $zips = glob($zip_dir . '*.zip');
            foreach ($zips as $zip) {
                $local_zip_files[] = basename($zip);
            }
        }

        // Get favorites
        $favorites = $this->feature_manager->get_favorites();

        $elementor_logo_id = 0;
        $elementor_favicon_id = 0;
        $elementor_logo_url = '';
        $elementor_favicon_url = '';

        if (did_action('elementor/loaded') && class_exists('\Elementor\\Plugin')) {
            try {
                $kits_manager = \Elementor\Plugin::$instance->kits_manager ?? null;
                if ($kits_manager && method_exists($kits_manager, 'get_active_kit')) {
                    $kit = $kits_manager->get_active_kit();
                    if (!$kit && method_exists($kits_manager, 'get_active_kit_for_frontend')) {
                        $kit = $kits_manager->get_active_kit_for_frontend();
                    }

                    if ($kit && method_exists($kit, 'get_settings')) {
                        $kit_settings = $kit->get_settings();

                        if (!empty($kit_settings['site_logo'])) {
                            $logo_setting = $kit_settings['site_logo'];
                            if (is_array($logo_setting) && !empty($logo_setting['id'])) {
                                $elementor_logo_id = absint($logo_setting['id']);
                                $elementor_logo_url = !empty($logo_setting['url']) ? $logo_setting['url'] : '';
                            } elseif (is_numeric($logo_setting)) {
                                $elementor_logo_id = absint($logo_setting);
                            }
                        }

                        if (!empty($kit_settings['site_favicon'])) {
                            $favicon_setting = $kit_settings['site_favicon'];
                            if (is_array($favicon_setting) && !empty($favicon_setting['id'])) {
                                $elementor_favicon_id = absint($favicon_setting['id']);
                                $elementor_favicon_url = !empty($favicon_setting['url']) ? $favicon_setting['url'] : '';
                            } elseif (is_numeric($favicon_setting)) {
                                $elementor_favicon_id = absint($favicon_setting);
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                error_log('WP Fast Setup: Error al obtener identidad de Elementor: ' . $e->getMessage());
            }
        }

        if ($elementor_logo_id && empty($elementor_logo_url)) {
            $elementor_logo_url = wp_get_attachment_image_url($elementor_logo_id, 'medium');
        }

        if ($elementor_favicon_id && empty($elementor_favicon_url)) {
            $elementor_favicon_url = wp_get_attachment_image_url($elementor_favicon_id, 'thumbnail');
        }

        // Display settings errors/messages
        settings_errors('wp_fast_setup_messages');

        // Include the main admin page template.
        include WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/templates/admin-page-new.php';
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts($hook)
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        $screen_id = $screen ? $screen->id : '';
        $allowed_ids = array('toplevel_page_wp-fast-setup', 'toplevel_page_wp_fast_setup');
        if (!in_array($screen_id, $allowed_ids, true)) {
            return;
        }

        // Load media library scripts for logo/favico selectors
        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        }

        // Enqueue CSS
        wp_enqueue_style(
            'wp-fast-setup-admin',
            WP_FAST_SETUP_PLUGIN_URL . 'styles/admin-styles.css',
            array(),
            WP_FAST_SETUP_VERSION
        );

        // Enqueue JavaScript
        wp_enqueue_script(
            'wp-fast-setup-admin',
            WP_FAST_SETUP_PLUGIN_URL . 'assets/js/wp-fast-setup.js',
            array('jquery'),
            WP_FAST_SETUP_VERSION,
            true
        );

        // Simple inline marker to confirm the script was printed
        // Localize script with AJAX data
        wp_localize_script('wp-fast-setup-admin', 'wpFastSetupAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_fast_setup_action'),
            'strings' => array(
                'confirm_delete' => __('¿Estás seguro de que quieres eliminar todas las páginas existentes?', 'wp-fast-setup'),
                'saving' => __('Guardando...', 'wp-fast-setup'),
                'saved' => __('Guardado correctamente', 'wp-fast-setup'),
                'error' => __('Error al guardar', 'wp-fast-setup'),
            )
        ));
    }
}
