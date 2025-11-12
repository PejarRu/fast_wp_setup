<?php

/**
 * Page Creator
 * Handles page creation, menu creation, and homepage setting
 */

class PageCreator
{
    public function __construct()
    {
        // AJAX actions for page and menu creation
        add_action('wp_ajax_wp_fast_setup_create_pages', array($this, 'ajax_create_pages'));
        add_action('wp_ajax_wp_fast_setup_create_menus', array($this, 'ajax_create_menus'));
        add_action('wp_ajax_wp_fast_setup_set_homepage', array($this, 'ajax_set_homepage'));
    }

    /**
     * AJAX handler for creating pages
     */
    public function ajax_create_pages()
    {
        // Verify nonce - check all possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['wp_fast_setup_nonce_content']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_content'], 'wp_fast_setup_action')) {
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
            $pages_input = stripslashes($_POST['pages_input']);

            // Delete existing pages if requested
            if (isset($_POST['delete_existing']) && $_POST['delete_existing'] == '1') {
                $this->delete_all_pages();
            }

            // Create pages
            $created_pages = $this->create_pages_from_input($pages_input);

            // Create menu if requested
            if (isset($_POST['create_menu']) && $_POST['create_menu'] == '1') {
                $this->create_menu_from_pages($created_pages);
            }

            wp_send_json_success(array(
                'message' => 'Páginas creadas correctamente',
                'pages_count' => count($created_pages)
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error al crear páginas: ' . $e->getMessage());
        }
    }

    /**
     * Create pages from input text
     */
    private function create_pages_from_input($input)
    {
        $created_pages = array();
        $lines = explode("\n", trim($input));
        $parent_stack = array();
        $current_parent_id = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $level = 0;
            while (strpos($line, '-') === 0) {
                $level++;
                $line = substr($line, 1);
            }
            $line = trim($line);

            if (empty($line)) continue;

            $page_data = array(
                'post_title' => $line,
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_parent' => $current_parent_id
            );

            $post_id = wp_insert_post($page_data);
            if (!is_wp_error($post_id)) {
                $created_pages[] = array(
                    'ID' => $post_id,
                    'title' => $line,
                    'parent' => $current_parent_id
                );

                // Update parent stack
                while (count($parent_stack) > $level) {
                    array_pop($parent_stack);
                }
                $parent_stack[] = $post_id;
                $current_parent_id = ($level > 0) ? $parent_stack[$level - 1] : 0;
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

    /**
     * Delete all pages
     */
    private function delete_all_pages()
    {
        $paginas = get_posts([
            'post_type'      => 'page',
            'posts_per_page' => -1,
            'post_status'    => 'any',
        ]);
        foreach ($paginas as $pagina) {
            wp_delete_post($pagina->ID, true);
        }
    }

    /**
     * AJAX handler for creating menus
     */
    public function ajax_create_menus()
    {
        // Verify nonce - check all possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['wp_fast_setup_nonce_menus']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_menus'], 'wp_fast_setup_action')) {
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
            $raw_menus_input = '';
            $legacy_menu_input = '';
            if (isset($_POST['menus_input'])) {
                $raw_menus_input = wp_unslash($_POST['menus_input']);
            }
            if (isset($_POST['menu_input'])) {
                $legacy_menu_input = wp_unslash($_POST['menu_input']);
                if ('' === $raw_menus_input) {
                    $raw_menus_input = $legacy_menu_input;
                }
            }

            if ('' === trim($raw_menus_input)) {
                wp_send_json_error('No se proporcionaron menús para crear.');
            }

            $use_legacy_flow = isset($_POST['menu_input']) && !isset($_POST['menus_input']);
            if ($use_legacy_flow) {
                $menu_name = sanitize_text_field($_POST['menu_name'] ?? 'Main Menu');
                if ('' === trim($legacy_menu_input)) {
                    wp_send_json_error('No se proporcionaron elementos para el menú.');
                }

                $created_menu = $this->create_menu_from_input($legacy_menu_input, $menu_name ?: 'Main Menu');

                wp_send_json_success(array(
                    'message' => 'Menú creado correctamente',
                    'menu_id' => $created_menu,
                    'created' => array($menu_name ?: 'Main Menu'),
                    'legacy' => true,
                ));
            }

            $menu_lines = preg_split('/\r\n|\r|\n/', $raw_menus_input);
            $menu_lines = array_filter(array_map('trim', $menu_lines));

            if (empty($menu_lines)) {
                wp_send_json_error('No se encontraron menús válidos.');
            }

            $created = array();
            $skipped = array();
            $errors = array();

            foreach ($menu_lines as $menu_line) {
                $menu_name = sanitize_text_field($menu_line);
                if ('' === $menu_name) {
                    continue;
                }

                $existing_menu = wp_get_nav_menu_object($menu_name);
                if ($existing_menu) {
                    $skipped[] = $menu_name;
                    continue;
                }

                $menu_id = wp_create_nav_menu($menu_name);
                if (is_wp_error($menu_id)) {
                    $errors[] = $menu_name . ': ' . $menu_id->get_error_message();
                    continue;
                }

                $created[] = $menu_name;
            }

            if (!empty($errors) && empty($created)) {
                wp_send_json_error('Error al crear menús: ' . implode('; ', $errors));
            }

            $response = array(
                'message' => 'Menús procesados correctamente',
                'created' => $created,
                'skipped' => $skipped,
                'errors' => $errors,
            );

            if (empty($created) && !empty($skipped)) {
                $response['message'] = 'Todos los menús ya existían';
            }

            wp_send_json_success($response);
        } catch (Exception $e) {
            wp_send_json_error('Error al crear menú: ' . $e->getMessage());
        }
    }

    /**
     * Create menu from input
     */
    private function create_menu_from_input($input, $menu_name)
    {
        // Check if the menu already exists.
        $menu_object = wp_get_nav_menu_object($menu_name);
        if (!$menu_object) {
            $menu_id = wp_create_nav_menu($menu_name);
        } else {
            $menu_id = $menu_object->term_id;
        }

        $lines = explode("\n", trim($input));
        $menu_item_map = array();
        $parent_stack = array();

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $level = 0;
            while (strpos($line, '-') === 0) {
                $level++;
                $line = substr($line, 1);
            }
            $line = trim($line);

            if (empty($line)) continue;

            // Parse menu item (format: "Title|URL" or just "Title")
            $parts = explode('|', $line, 2);
            $title = trim($parts[0]);
            $url = isset($parts[1]) ? trim($parts[1]) : '#';

            $args = array(
                'menu-item-title' => $title,
                'menu-item-url' => $url,
                'menu-item-status' => 'publish',
            );

            // If the item has a parent, add it as a child
            if ($level > 0 && isset($parent_stack[$level - 1])) {
                $args['menu-item-parent-id'] = $parent_stack[$level - 1];
            }

            $menu_item_id = wp_update_nav_menu_item($menu_id, 0, $args);
            if (!is_wp_error($menu_item_id)) {
                $parent_stack[$level] = $menu_item_id;
            }
        }

        return $menu_id;
    }

    /**
     * AJAX handler for setting homepage
     */
    public function ajax_set_homepage()
    {
        // Verify nonce - check all possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['wp_fast_setup_nonce_homepage']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_homepage'], 'wp_fast_setup_action')) {
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
            $homepage_id = intval($_POST['homepage_id']);

            if ($homepage_id > 0) {
                update_option('page_on_front', $homepage_id);
                update_option('show_on_front', 'page');
            }

            wp_send_json_success(array(
                'message' => 'Página de inicio configurada correctamente'
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error al configurar página de inicio: ' . $e->getMessage());
        }
    }
}
