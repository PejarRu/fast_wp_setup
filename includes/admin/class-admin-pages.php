<?php
class Admin_Pages
{

    public function __construct()
    {
        error_log('WP Fast Setup: Admin_Pages constructor called');
        add_action('admin_menu', array($this, 'register_admin_menu'));
        add_action('admin_init', array($this, 'handle_form_submissions'));

        // AJAX actions
        add_action('wp_ajax_wp_fast_setup_install_plugins', array($this, 'ajax_install_plugins'));

        // Plugin deletion handler
        add_action('admin_post_wp_fast_setup_delete_plugin', array($this, 'handle_plugin_deletion'));

        // Enqueue scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Register admin menu
     */
    public function register_admin_menu()
    {
        error_log('WP Fast Setup: register_admin_menu called');

        add_menu_page(
            __('WP Fast Setup', 'wp-fast-setup'),    // Page title
            __('WP Fast Setup', 'wp-fast-setup'),    // Menu title
            'manage_options',                         // Capability
            'wp-fast-setup',                         // Menu slug
            array($this, 'render_admin_page'),       // Callback function
            'dashicons-admin-generic',               // Icon
            30                                       // Position
        );
        error_log('WP Fast Setup: register_admin_menu finished');
    }

    /**
     * Handle form submissions
     */
    public function handle_form_submissions()
    {
        $is_ajax = defined('WP_FAST_SETUP_AJAX') && WP_FAST_SETUP_AJAX;

        if (!$is_ajax && (
            !isset($_POST['wp_fast_setup_nonce']) ||
            !wp_verify_nonce($_POST['wp_fast_setup_nonce'], 'wp_fast_setup_action')
        )) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Site Settings
            if (!empty($_POST['nombre_sitio'])) {
                update_option('blogname', sanitize_text_field($_POST['nombre_sitio']));
            }
            if (!empty($_POST['idioma_sitio'])) {
                update_option('WPLANG', sanitize_text_field($_POST['idioma_sitio']));
            }
            if (!empty($_POST['url_sitio'])) {
                update_option('siteurl', esc_url_raw($_POST['url_sitio']));
            }

            // Google Drive Settings
            $api_key_input = sanitize_text_field($_POST['google_drive_api_key'] ?? '');
            $folder_id_input = sanitize_text_field($_POST['google_drive_folder_id'] ?? '');
            if (!empty($api_key_input) || !empty($folder_id_input) || isset($_POST['google_drive_api_key']) || isset($_POST['google_drive_folder_id'])) {
                update_option('wp_fast_setup_google_drive_api_key', $api_key_input ?: WP_FAST_SETUP_DEFAULT_API_KEY);
                update_option('wp_fast_setup_google_drive_folder_id', $folder_id_input ?: WP_FAST_SETUP_DEFAULT_FOLDER_ID);
                if (!$is_ajax) {
                    echo '<div class="notice notice-success">Configuración de Google Drive guardada.</div>';
                }
            }

            require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-theme-manager.php';
            $theme_manager = new Theme_Manager();
            // Features
            if (isset($_POST['activar_permalinks'])) {
                $theme_manager->activate_permalinks();
            }
            if (isset($_POST['activar_hello_elementor'])) {
                $theme_manager->activate_hello_theme();
            }
            if (isset($_POST['desactivar_comentarios'])) {
                $theme_manager->disable_comments();
            }
            /* if (isset($_POST['activar_usuario'])) {
                $this->create_admin_user();
            } */

            // Plugin installations
            require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-plugins-manager.php';
            $plugin_manager = new Plugin_Manager();
            $plugin_manager->handle_plugin_installations();

            // Template/Page creations
            if (isset($_POST['pages_input'])) {
                if (isset($_POST['delete_and_create_pages']) || isset($_POST['delete_and_create_pages_with_menu'])) {
                    $this->delete_all_pages();
                }
                $pages_input = stripslashes($_POST['pages_input']);

                // Check if "Crear páginas y menú" was pressed.
                if (isset($_POST['create_pages_and_menu']) || isset($_POST['delete_and_create_pages_with_menu'])) {
                    $created_pages = $this->create_pages_from_input($pages_input);
                    $this->create_menu_from_pages($created_pages);
                    if (!$is_ajax) {
                        echo '<div class="notice notice-success">Páginas y menú creados.</div>';
                    }
                } else {
                    // Default: only create pages.
                    $created_pages = $this->create_pages_from_input($pages_input);
                    if (!$is_ajax) {
                        echo '<div class="notice notice-success">Páginas creadas.</div>';
                    }
                }
            }

            add_settings_error(
                'wp_fast_setup_messages',
                'wp_fast_setup_message',
                'Configuración actualizada correctamente.',
                'updated'
            );
        }
    }

    /**
     * Create pages from the input.
     * Returns an array with created pages info (ID, title, parent)
     */
    private function create_pages_from_input($input)
    {
        $created_pages = array();
        // Use the "default" template if selected; otherwise use "elementor_header_footer".
        $template = (isset($_POST['page_template']) && $_POST['page_template'] === 'default') ? '' : 'elementor_header_footer';

        // Explode input into lines.
        $lines = explode("\n", $input);
        $current_parent_id = 0; // For top-level pages.
        foreach ($lines as $line) {
            $trimmed = rtrim($line, "\r\n");

            // Skip empty lines.
            if (trim($trimmed) === '') {
                continue;
            }

            // Check if line has leading space (subpage) or is a top-level page.
            if (substr($trimmed, 0, 1) === ' ') {
                $title = trim($trimmed);
                $parent = $current_parent_id;
            } else {
                $title = trim($trimmed);
                $parent = 0;
            }

            // Skip if page already exists.
            if (get_page_by_title($title)) {
                continue;
            }

            // Create the page.
            $post_id = wp_insert_post([
                'post_title'   => $title,
                'post_content' => '',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_parent'  => $parent,
            ]);

            // Set the proper page template if using Elementor.
            if ($template === 'elementor_header_footer') {
                update_post_meta($post_id, '_wp_page_template', 'elementor_header_footer');
                update_post_meta($post_id, '_edit_lock', time() . ':1');
                update_post_meta($post_id, '_elementor_edit_mode', 'builder');
                update_post_meta($post_id, '_elementor_template_type', 'wp-page');
                update_post_meta($post_id, '_elementor_version', '3.28.3');
                update_post_meta($post_id, '_elementor_pro_version', '3.8.1');
                update_post_meta($post_id, '_edit_last', 1);
            }

            // Store info of created page.
            $created_pages[] = array(
                'ID'     => $post_id,
                'title'  => $title,
                'parent' => $parent,
            );

            // Update current_parent_id for top-level pages.
            if ($parent === 0) {
                $current_parent_id = $post_id;
            }
        }
        return $created_pages;
    }

    /**
     * Create a nav menu replicating the page structure.
     */
    private function create_menu_from_pages($pages)
    {
        $menu_name = 'Main Menu';
        // Check if the menu already exists.
        $menu_object = wp_get_nav_menu_object($menu_name);
        if (!$menu_object) {
            $menu_id = wp_create_nav_menu($menu_name);
        } else {
            $menu_id = $menu_object->term_id;
        }

        // Map created page IDs to newly created menu item IDs.
        $menu_item_map = array();

        foreach ($pages as $page) {
            $args = array(
                'menu-item-title'     => $page['title'],
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page['ID'],
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            );
            // If the page has a parent and its menu item exists, add it as a child.
            if ($page['parent'] != 0 && isset($menu_item_map[$page['parent']])) {
                $args['menu-item-parent-id'] = $menu_item_map[$page['parent']];
            }
            $menu_item_id = wp_update_nav_menu_item($menu_id, 0, $args);
            if (!is_wp_error($menu_item_id)) {
                $menu_item_map[$page['ID']] = $menu_item_id;
            }
        }
    }

    private function delete_all_pages()
    {
        $paginas = get_posts([
            'post_type'      => 'page',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);
        foreach ($paginas as $pagina) {
            wp_delete_post($pagina->ID, true);
        }
    }





    /**
     * Render admin page
     */
    public function render_admin_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos para acceder a esta página.');
        }

        // Get current values
        $current_site_name = get_option('blogname');
        $current_language    = get_option('WPLANG');
        $current_url         = get_option('siteurl');

        // Get Google Drive files
        $api_key = get_option('wp_fast_setup_google_drive_api_key', WP_FAST_SETUP_DEFAULT_API_KEY);
        $folder_id = get_option('wp_fast_setup_google_drive_folder_id', WP_FAST_SETUP_DEFAULT_FOLDER_ID);
        $drive_zip_files = [];
        if (!empty($api_key) && !empty($folder_id)) {
            $drive_zip_files = $this->get_drive_zip_files($api_key, $folder_id);
        }

        // Get local ZIP files as fallback
        $local_zip_files = [];
        if (empty($drive_zip_files)) {
            $zip_dir = WP_FAST_SETUP_PLUGIN_DIR . 'zip-files/';
            if (is_dir($zip_dir)) {
                $zips = glob($zip_dir . '*.zip');
                foreach ($zips as $zip) {
                    $local_zip_files[] = basename($zip);
                }
            }
        }

        // Include the main admin page template.
        include WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/templates/admin-page.php';

        // Append the template creation form.
        //$this->handle_template_creations();
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
     * Enqueue scripts and styles
     */
    public function enqueue_scripts($hook)
    {
        if ($hook !== 'toplevel_page_wp-fast-setup') {
            return;
        }

        wp_enqueue_script('wp-fast-setup-ajax', '', array('jquery'), '1.0', true);
        wp_localize_script('wp-fast-setup-ajax', 'ajaxurl', admin_url('admin-ajax.php'));
    }

    /**
     * AJAX handler for plugin installation
     */
    public function ajax_install_plugins()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_ajax')) {
            wp_send_json_error('Invalid nonce');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        try {
            // Set AJAX flag
            define('WP_FAST_SETUP_AJAX', true);

            // Process form data
            $this->handle_form_submissions();

            wp_send_json_success('Instalación completada exitosamente');
        } catch (Exception $e) {
            wp_send_json_error('Error durante la instalación: ' . $e->getMessage());
        }
    }

    /**
     * Handle plugin self-deletion
     */
    public function handle_plugin_deletion()
    {
        error_log('WP Fast Setup: handle_plugin_deletion called');

        // Check permissions
        if (!current_user_can('delete_plugins') || !current_user_can('manage_options')) {
            error_log('WP Fast Setup: Insufficient permissions for plugin deletion');
            wp_die(__('No tienes permisos para eliminar plugins.', 'wp-fast-setup'));
        }

        // Check nonce for security
        if (
            !isset($_POST['wp_fast_setup_delete_nonce']) ||
            !wp_verify_nonce($_POST['wp_fast_setup_delete_nonce'], 'wp_fast_setup_delete_plugin')
        ) {
            error_log('WP Fast Setup: Nonce verification failed');
            wp_die(__('Verificación de seguridad fallida.', 'wp-fast-setup'));
        }

        // Check if this is test mode
        if (isset($_POST['test_mode']) && $_POST['test_mode'] == '1') {
            error_log('WP Fast Setup: Test mode activated - plugin will not be deleted');
            wp_die(__('Test completado exitosamente. El plugin NO fue eliminado.', 'wp-fast-setup'));
        }

        $plugin_file = plugin_basename(WP_FAST_SETUP_PLUGIN_DIR . 'wp-fast-setup-installer.php');
        error_log('WP Fast Setup: Plugin file to delete: ' . $plugin_file);

        // Deactivate first
        deactivate_plugins($plugin_file);
        error_log('WP Fast Setup: Plugin deactivated');

        // Delete the plugin
        if (delete_plugins(array($plugin_file))) {
            error_log('WP Fast Setup: Plugin deleted successfully');
            // Redirect to plugins page with success message
            wp_redirect(admin_url('plugins.php?deleted=1'));
            exit;
        } else {
            error_log('WP Fast Setup: Failed to delete plugin');
            wp_die(__('Error al eliminar el plugin. Puede que tengas dependencias activas.', 'wp-fast-setup'));
        }
    }
}
