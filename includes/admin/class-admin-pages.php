<?php
class Admin_Pages
{

    public function __construct()
    {
        error_log('WP Fast Setup: Admin_Pages constructor called');
        add_action('admin_menu', array($this, 'register_admin_menu'));
        add_action('admin_init', array($this, 'handle_form_submissions'));
    }

    /**
     * Register admin menu
     */
    public function register_admin_menu() {
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
        if (
            !isset($_POST['wp_fast_setup_nonce']) ||
            !wp_verify_nonce($_POST['wp_fast_setup_nonce'], 'wp_fast_setup_action')
        ) {
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
                    echo '<div class="notice notice-success">Páginas y menú creados.</div>';
                }
                else {
                    // Default: only create pages.
                    $created_pages = $this->create_pages_from_input($pages_input);
                    echo '<div class="notice notice-success">Páginas creadas.</div>';
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
    private function create_pages_from_input($input) {
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
            if ( get_page_by_title($title) ) {
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
            if ( $template === 'elementor_header_footer' ) {
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
            if ( $parent === 0 ) {
                $current_parent_id = $post_id;
            }
        }
        return $created_pages;
    }

    /**
     * Create a nav menu replicating the page structure.
     */
    private function create_menu_from_pages($pages) {
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

    private function delete_all_pages() {
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

        // Include the main admin page template.
        include WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/templates/admin-page.php';

        // Append the template creation form.
        //$this->handle_template_creations();
    }



   
    
}
