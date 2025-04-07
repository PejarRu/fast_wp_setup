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

            // Features
            if (isset($_POST['activar_permalinks'])) {
                $this->activate_permalinks();
            }
            if (isset($_POST['activar_hello_elementor'])) {
                $this->activate_hello_theme();
            }
            if (isset($_POST['desactivar_comentarios'])) {
                $this->disable_comments();
            }
            if (isset($_POST['activar_usuario'])) {
                $this->create_admin_user();
            }

            // Plugin installations
            $this->handle_plugin_installations();

            // Template creations
            $this->handle_template_creations();

            add_settings_error(
                'wp_fast_setup_messages',
                'wp_fast_setup_message',
                'Configuración actualizada correctamente.',
                'updated'
            );
        }
    }

    /**
     * Placeholder for handling template creations.
     * Add your logic here for creating templates.
     */
    private function handle_template_creations() {
        if ( isset($_POST['pages_input']) ) {

            // If the user wants to delete all pages first.
            if ( isset($_POST['delete_and_create_pages']) ) {
                $this->delete_all_pages();
            }

            $pages_input = stripslashes($_POST['pages_input']);
            $this->create_pages_from_input($pages_input);
            echo '<div class="notice notice-success">Pages processed.</div>';
        }
        
    }
    
    private function create_pages_from_input($input) {
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
    
            // Check if line has leading space, indicating subpage.
            if (substr($trimmed, 0, 1) === ' ') {
                // It is a subpage => child of current parent.
                $title = trim($trimmed);
                $parent = $current_parent_id;
            } else {
                // Top-level page.
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
    
            // Set the proper page template.
            // If using Elementor Full Width, set the proper meta values.
            if ( $template === 'elementor_header_footer' ) {
                update_post_meta($post_id, '_wp_page_template', 'elementor_header_footer');
                update_post_meta($post_id, '_edit_lock', time() . ':1');
                update_post_meta($post_id, '_elementor_edit_mode', 'builder');
                update_post_meta($post_id, '_elementor_template_type', 'wp-page');
                update_post_meta($post_id, '_elementor_version', '3.28.3');
                update_post_meta($post_id, '_elementor_pro_version', '3.8.1');
                update_post_meta($post_id, '_edit_last', 1);
            }


            // For top-level pages, update parent id for upcoming subpages.
            if ( $parent === 0 ) {
                $current_parent_id = $post_id;
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
     * Activate permalinks
     */
    private function activate_permalinks()
    {
        update_option('permalink_structure', '/%postname%/');
        flush_rewrite_rules();
    }

    /**
     * Disable comments globally
     */
    private function disable_comments()
    {
        // Ajustes predeterminados en nuevas entradas
        update_option('default_pingback_flag', false);    // Intentar avisar a cualquier blog enlazado: unchecked
        update_option('default_ping_status', 'closed');     // No permitir pingbacks/trackbacks (unchecked)
        update_option('default_comment_status', 'closed');  // Permitir comentarios en nuevas entradas: unchecked

        // Otros ajustes de comentarios
        update_option('require_name_email', true);          // El autor debe rellenar nombre y email
        update_option('comment_registration', true);        // Usuarios deben estar registrados para comentar [checked]
        update_option('close_comments_for_old_posts', true);  // Cerrar comentarios en entradas antiguas [checked]
        update_option('close_comments_age', '0');             // Antigüedad en días: 0

        update_option('thread_comments', false);            // Desactivar comentarios en hilos [unchecked]
        update_option('thread_comments_depth', 2);          // Niveles de hilos: 2

        update_option('page_comments', false);              // Desglosar comentarios en páginas [unchecked]
        update_option('comments_per_page', 0);              // Cantidad de comentarios por página: 0

        // Configurar aprobación de comentarios:
        
        update_option('comment_moderation', true); // Hacer que el comentario deba aprobarse manualmente [checked]
        update_option('comment_previously_approved', true); // El autor debe tener un comentario aprobado previamente [checked]

         // Notificaciones por email: desactivarlas
        update_option('new_comment_notify', false);         // No notificar por email cuando se envíe un comentario (unchecked)
        update_option('moderation_notify', false);          // No notificar por email cuando haya un comentario para moderar (unchecked)


        // Moderación de comentarios: abandonar comentario si contiene más de 0 enlaces
        update_option('comment_max_links', 0);
    }

    /**
     * Handle plugin installations
     */
    private function handle_plugin_installations()
    {
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-plugins-manager.php';
        $plugin_manager = new Plugin_Manager();

        $json_file = WP_FAST_SETUP_PLUGIN_DIR . 'includes/plugins-list.json';
        if ( file_exists($json_file) ) {
            $json_data = file_get_contents($json_file);
            $data = json_decode($json_data, true);
            if ( isset($data['plugins']) && is_array($data['plugins']) ) {
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
                if ( isset($_POST[$input_name]) ) {
                    $plugin_manager->install_plugin_from_zip($zip_file);
                }
            }
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
        $this->handle_template_creations();
    }



    /**
     * Activar tema Hello Elementor
     */
    public function activate_hello_theme()
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $theme_slug = 'hello-elementor';
        $all_themes = wp_get_themes();

        if (!array_key_exists($theme_slug, $all_themes)) {
            $response = wp_remote_get("https://api.wordpress.org/themes/info/1.2/?action=theme_information&request[slug]=$theme_slug");
            if (is_wp_error($response)) {
                echo "Error al conectar con el repositorio de temas de WordPress.";
                return;
            }
            $theme_info = json_decode(wp_remote_retrieve_body($response));
            if (empty($theme_info->download_link)) {
                echo "No se encontró la URL de descarga para 'Hello Elementor'.";
                return;
            }
            $upgrader  = new Theme_Upgrader();
            $installed = $upgrader->install($theme_info->download_link);
            if (is_wp_error($installed)) {
                echo "Error al instalar el tema 'Hello Elementor'.";
                return;
            }
        }

        // Eliminar otros temas y activar Hello
        $all_themes = wp_get_themes();
        foreach ($all_themes as $slug => $theme_obj) {
            if ($slug !== $theme_slug) {
                delete_theme($slug);
            }
        }
        switch_theme($theme_slug);
    }
    
}
