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
        add_action('wp_ajax_wp_fast_setup_save_site_settings', array($this, 'ajax_save_site_settings'));
        add_action('wp_ajax_wp_fast_setup_create_pages', array($this, 'ajax_create_pages'));
        add_action('wp_ajax_wp_fast_setup_activate_features', array($this, 'ajax_activate_features'));
        add_action('wp_ajax_wp_fast_setup_save_google_drive', array($this, 'ajax_save_google_drive'));
        add_action('wp_ajax_wp_fast_setup_add_favorite', array($this, 'ajax_add_favorite'));
        add_action('wp_ajax_wp_fast_setup_create_menus', array($this, 'ajax_create_menus'));
        add_action('wp_ajax_wp_fast_setup_set_homepage', array($this, 'ajax_set_homepage'));

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
     * Handle form submissions (legacy - most operations now use AJAX)
     */
    public function handle_form_submissions()
    {
        $is_ajax = defined('WP_FAST_SETUP_ACTION') && WP_FAST_SETUP_ACTION;

        if (!$is_ajax && (
            !isset($_POST['wp_fast_setup_nonce']) ||
            !wp_verify_nonce($_POST['wp_fast_setup_nonce'], 'wp_fast_setup_action')
        )) {
            return;
        }

        // Most operations are now handled via AJAX
        // This method is kept for backward compatibility and any non-AJAX operations
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_ajax) {
            // Handle any remaining non-AJAX operations here if needed
            // Currently all main operations use AJAX
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
            'post_status'    => 'any',
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

        // Check for language change success message
        if (isset($_GET['lang_changed']) && $_GET['lang_changed'] == '1') {
            // This code is no longer needed since we handle the message directly in the form submission
        }

        // Get current values
        $current_site_name = get_option('blogname');
        $current_language    = get_option('WPLANG');
        $current_url         = get_option('siteurl');
        $current_admin_email = get_option('admin_email');
        $current_tagline     = get_option('blogdescription');
        $current_logo_id     = get_option('wp_fast_setup_site_logo');
        $current_favicon_id  = get_option('site_icon');
        $current_blog_public = get_option('blog_public', 1); // 1 = indexable, 0 = not indexable

        // Get Google Drive files dynamically
        $api_key = get_option('wp_fast_setup_google_drive_api_key', '');
        $folder_id = get_option('wp_fast_setup_google_drive_folder_id', '');
        $drive_zip_files = [];
        $drive_error = '';

        if (!empty($api_key) && !empty($folder_id)) {
            $result = $this->get_drive_zip_files($api_key, $folder_id);
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

        // Display settings errors/messages
        settings_errors('wp_fast_setup_messages');

        // Include the main admin page template.
        include WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/templates/admin-page-new.php';

        // Append the template creation form.
        //$this->handle_template_creations();
    }

    /**
     * Get ZIP files from Google Drive folder
     */
    public function get_drive_zip_files($api_key, $folder_id)
    {
        if (empty($api_key) || empty($folder_id)) {
            return array('error' => 'API Key o Folder ID no configurados');
        }

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
        if ($http_code !== 200) {
            $body = wp_remote_retrieve_body($response);
            $error_data = json_decode($body, true);
            $error_msg = isset($error_data['error']['message']) ? $error_data['error']['message'] : 'Error HTTP ' . $http_code;
            return array('error' => 'Error de API: ' . $error_msg);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return array('error' => 'Error al decodificar respuesta JSON');
        }

        if (!isset($data['files'])) {
            return array('error' => 'Respuesta inesperada de Google Drive API');
        }

        $zip_files = array();
        foreach ($data['files'] as $file) {
            if (isset($file['name']) && strpos($file['name'], '.zip') !== false) {
                $zip_files[] = array(
                    'id' => $file['id'],
                    'name' => $file['name']
                );
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
        wp_localize_script('wp-fast-setup-ajax', 'wpFastSetupAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_fast_setup_action'),
            'strings' => array(
                'saving' => __('Guardando...', 'wp-fast-setup'),
                'saved' => __('Guardado correctamente', 'wp-fast-setup'),
                'error' => __('Error al guardar', 'wp-fast-setup'),
                'processing' => __('Procesando...', 'wp-fast-setup'),
                'success' => __('Completado correctamente', 'wp-fast-setup')
            )
        ));

        // Add inline JavaScript for AJAX functionality
        wp_add_inline_script('wp-fast-setup-ajax', $this->get_ajax_javascript());
    }

    /**
     * Get AJAX JavaScript code
     */
    private function get_ajax_javascript()
    {
        return "
        jQuery(document).ready(function($) {
            console.log('WP Fast Setup: JavaScript loaded');

            // Handle site settings form
            $('#site form').on('submit', function(e) {
                e.preventDefault();
                console.log('WP Fast Setup: Site settings form submitted');

                var formData = new FormData(this);
                formData.append('action', 'wp_fast_setup_save_site_settings');
                formData.append('nonce', wpFastSetupAjax.nonce);

                console.log('WP Fast Setup: Form data keys:', Array.from(formData.keys()));
                console.log('WP Fast Setup: AJAX URL:', wpFastSetupAjax.ajaxurl);

                var submitBtn = $(this).find('button[type=\"submit\"]');
                var originalText = submitBtn.text();

                submitBtn.prop('disabled', true).text(wpFastSetupAjax.strings.saving);

                $.ajax({
                    url: wpFastSetupAjax.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('WP Fast Setup: AJAX success response:', response);
                        if (response.success) {
                            submitBtn.text(wpFastSetupAjax.strings.saved);
                            setTimeout(function() {
                                submitBtn.prop('disabled', false).text(originalText);
                            }, 2000);
                        } else {
                            console.error('WP Fast Setup: AJAX error response:', response);
                            alert(wpFastSetupAjax.strings.error + ': ' + response.data);
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('WP Fast Setup: AJAX error:', status, error);
                        console.error('WP Fast Setup: XHR response:', xhr.responseText);
                        alert(wpFastSetupAjax.strings.error);
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Handle pages creation form
            $('#content form').on('submit', function(e) {
                e.preventDefault();

                console.log('WP Fast Setup: Content form submitted');

                var formData = new FormData();
                formData.append('action', 'wp_fast_setup_create_pages');
                formData.append('nonce', wpFastSetupAjax.nonce);
                formData.append('pages_input', $(this).find('[name=\"pages_input\"]').val());
                formData.append('page_template', $(this).find('[name=\"page_template\"]:checked').val());
                formData.append('delete_existing', $(this).find('[name=\"delete_existing\"]').val());
                formData.append('create_menu', $(this).find('[name=\"create_menu\"]').val());

                console.log('WP Fast Setup: Pages form data - pages_input:', $(this).find('[name=\"pages_input\"]').val());
                console.log('WP Fast Setup: Pages form data - page_template:', $(this).find('[name=\"page_template\"]:checked').val());
                console.log('WP Fast Setup: Pages form data - delete_existing:', $(this).find('[name=\"delete_existing\"]').val());
                console.log('WP Fast Setup: Pages form data - create_menu:', $(this).find('[name=\"create_menu\"]').val());

                var submitBtn = $(this).find('button[type=\"submit\"]:focus');
                if (submitBtn.length === 0) {
                    submitBtn = $(this).find('button[type=\"submit\"]').first();
                }
                var originalText = submitBtn.text();

                submitBtn.prop('disabled', true).text(wpFastSetupAjax.strings.processing);

                $.ajax({
                    url: wpFastSetupAjax.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('WP Fast Setup: Pages creation response:', response);
                        if (response.success) {
                            submitBtn.text(wpFastSetupAjax.strings.success);
                            setTimeout(function() {
                                submitBtn.prop('disabled', false).text(originalText);
                            }, 2000);
                        } else {
                            console.error('WP Fast Setup: Pages creation error:', response.data);
                            alert(wpFastSetupAjax.strings.error + ': ' + response.data);
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('WP Fast Setup: AJAX error:', status, error);
                        console.error('WP Fast Setup: XHR response:', xhr.responseText);
                        alert(wpFastSetupAjax.strings.error);
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Handle menu creation form
            $('#menu-form').on('submit', function(e) {
                e.preventDefault();

                console.log('WP Fast Setup: Menu form submitted');

                var formData = new FormData();
                formData.append('action', 'wp_fast_setup_create_menus');
                formData.append('nonce', wpFastSetupAjax.nonce);
                formData.append('menus_input', $(this).find('[name=\"menus_input\"]').val());

                console.log('WP Fast Setup: Menus form data - menus_input:', $(this).find('[name=\"menus_input\"]').val());

                var submitBtn = $(this).find('button[type=\"submit\"]');
                var originalText = submitBtn.text();

                submitBtn.prop('disabled', true).text(wpFastSetupAjax.strings.processing);

                $.ajax({
                    url: wpFastSetupAjax.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('WP Fast Setup: Menus creation response:', response);
                        if (response.success) {
                            submitBtn.text(wpFastSetupAjax.strings.success);
                            setTimeout(function() {
                                submitBtn.prop('disabled', false).text(originalText);
                            }, 2000);
                        } else {
                            console.error('WP Fast Setup: Menus creation error:', response.data);
                            alert(wpFastSetupAjax.strings.error + ': ' + response.data);
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('WP Fast Setup: AJAX error:', status, error);
                        console.error('WP Fast Setup: XHR response:', xhr.responseText);
                        alert(wpFastSetupAjax.strings.error);
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Handle homepage settings form
            $('#homepage-form').on('submit', function(e) {
                e.preventDefault();

                console.log('WP Fast Setup: Homepage form submitted');

                var formData = new FormData();
                formData.append('action', 'wp_fast_setup_set_homepage');
                formData.append('nonce', wpFastSetupAjax.nonce);
                formData.append('homepage_page', $(this).find('[name=\"homepage_page\"]').val());
                formData.append('blog_page', $(this).find('[name=\"blog_page\"]').val());

                console.log('WP Fast Setup: Homepage form data - homepage_page:', $(this).find('[name=\"homepage_page\"]').val());
                console.log('WP Fast Setup: Homepage form data - blog_page:', $(this).find('[name=\"blog_page\"]').val());

                var submitBtn = $(this).find('button[type=\"submit\"]');
                var originalText = submitBtn.text();

                submitBtn.prop('disabled', true).text(wpFastSetupAjax.strings.processing);

                $.ajax({
                    url: wpFastSetupAjax.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('WP Fast Setup: Homepage settings response:', response);
                        if (response.success) {
                            submitBtn.text(wpFastSetupAjax.strings.success);
                            setTimeout(function() {
                                submitBtn.prop('disabled', false).text(originalText);
                            }, 2000);
                        } else {
                            console.error('WP Fast Setup: Homepage settings error:', response.data);
                            alert(wpFastSetupAjax.strings.error + ': ' + response.data);
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('WP Fast Setup: AJAX error:', status, error);
                        console.error('WP Fast Setup: XHR response:', xhr.responseText);
                        alert(wpFastSetupAjax.strings.error);
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Handle features activation form
            $('#templates form').on('submit', function(e) {
                e.preventDefault();
                
                var formData = new FormData();
                formData.append('action', 'wp_fast_setup_activate_features');
                formData.append('nonce', wpFastSetupAjax.nonce);
                
                // Add checked features
                $(this).find('input[type=\"checkbox\"]:checked').each(function() {
                    formData.append($(this).attr('name'), $(this).val() || '1');
                });
                
                var submitBtn = $(this).find('button[type=\"submit\"]');
                var originalText = submitBtn.text();
                
                submitBtn.prop('disabled', true).text(wpFastSetupAjax.strings.processing);
                
                $.ajax({
                    url: wpFastSetupAjax.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            submitBtn.text(wpFastSetupAjax.strings.success);
                            setTimeout(function() {
                                submitBtn.prop('disabled', false).text(originalText);
                            }, 2000);
                        } else {
                            alert(wpFastSetupAjax.strings.error + ': ' + response.data);
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    },
                    error: function() {
                        alert(wpFastSetupAjax.strings.error);
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Handle Google Drive settings form
            $('form').on('submit', function(e) {
                var form = $(this);
                if (form.find('[name=\"google_drive_api_key\"]').length > 0) {
                    e.preventDefault();

                    var formData = new FormData();
                    formData.append('action', 'wp_fast_setup_save_google_drive');
                    formData.append('nonce', wpFastSetupAjax.nonce);
                    formData.append('google_drive_api_key', form.find('[name=\"google_drive_api_key\"]').val());
                    formData.append('google_drive_folder_id', form.find('[name=\"google_drive_folder_id\"]').val());

                    var submitBtn = form.find('button[type=\"submit\"]');
                    var originalText = submitBtn.text();

                    submitBtn.prop('disabled', true).text(wpFastSetupAjax.strings.saving);

                    $.ajax({
                        url: wpFastSetupAjax.ajaxurl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                submitBtn.text(wpFastSetupAjax.strings.saved);
                                // Refresh the plugin list if Google Drive was configured
                                if (response.data && response.data.zip_files) {
                                    updatePluginList(response.data.zip_files);
                                }
                                setTimeout(function() {
                                    submitBtn.prop('disabled', false).text(originalText);
                                }, 2000);
                            } else {
                                alert(wpFastSetupAjax.strings.error + ': ' + (response.data || 'Error desconocido'));
                                submitBtn.prop('disabled', false).text(originalText);
                            }
                        },
                        error: function(xhr, status, error) {
                            var errorMsg = wpFastSetupAjax.strings.error;
                            if (xhr.responseJSON && xhr.responseJSON.data) {
                                errorMsg += ': ' + xhr.responseJSON.data;
                            }
                            alert(errorMsg);
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    });
                }
            });

            // Function to update plugin list with Google Drive files
            function updatePluginList(zipFiles) {
                if (!zipFiles || zipFiles.length === 0) return;

                var driveSection = $('#drive-plugins-section');
                if (driveSection.length === 0) {
                    // Create the section if it doesn't exist
                    var html = '<h3>Plugins de Google Drive</h3><div id=\"drive-plugins-section\">';
                    zipFiles.forEach(function(file) {
                        html += '<label><input type=\"checkbox\" name=\"drive_plugins[]\" value=\"' + file.id + '\"> ' + file.name + '</label><br>';
                    });
                    html += '</div>';
                    $('#plugins-section').after(html);
                } else {
                    // Update existing section
                    var html = '';
                    zipFiles.forEach(function(file) {
                        html += '<label><input type=\"checkbox\" name=\"drive_plugins[]\" value=\"' + file.id + '\"> ' + file.name + '</label><br>';
                    });
                    driveSection.html(html);
                }
            }
        });
        ";
    }

    /**
     * AJAX handler for plugin installation
     */
    public function ajax_install_plugins()
    {
        error_log('WP Fast Setup: ajax_install_plugins called');

        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            error_log('WP Fast Setup: Nonce verification failed');
            wp_send_json_error('Invalid nonce');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            error_log('WP Fast Setup: Insufficient permissions');
            wp_send_json_error('Insufficient permissions');
        }

        try {
            // Set AJAX flag
            define('WP_FAST_SETUP_ACTION', true);

            error_log('WP Fast Setup: Starting plugin installation process');

            // Log received POST data for debugging
            error_log('WP Fast Setup: POST data received: ' . print_r($_POST, true));

            // Include the plugin manager
            require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-plugins-manager.php';

            // Create plugin manager instance
            $plugin_manager = new Plugin_Manager();

            // Process form data and install plugins
            $this->process_plugin_installation_data($plugin_manager);

            error_log('WP Fast Setup: Plugin installation process completed successfully');
            wp_send_json_success('Instalación completada exitosamente');
        } catch (Exception $e) {
            error_log('WP Fast Setup: Exception during plugin installation: ' . $e->getMessage());
            error_log('WP Fast Setup: Stack trace: ' . $e->getTraceAsString());
            wp_send_json_error('Error durante la instalación: ' . $e->getMessage());
        }
    }

    /**
     * Process plugin installation data from AJAX request
     */
    private function process_plugin_installation_data($plugin_manager)
    {
        error_log('WP Fast Setup: Processing plugin installation data');

        // Load plugins list to map post keys back to slugs
        $json_file = WP_FAST_SETUP_PLUGIN_DIR . 'includes/plugins-list.json';
        $plugins_map = array();
        if (file_exists($json_file)) {
            $json_data = file_get_contents($json_file);
            $data = json_decode($json_data, true);
            if (isset($data['plugins']) && is_array($data['plugins'])) {
                // Reverse the mapping: post_key => slug
                $plugins_map = array_flip($data['plugins']);
            }
        }

        // Process plugins from WordPress repository
        if (!empty($plugins_map)) {
            foreach ($plugins_map as $post_key => $plugin_slug) {
                if (isset($_POST[$post_key])) {
                    error_log('WP Fast Setup: Installing plugin from repository: ' . $plugin_slug . ' (post_key: ' . $post_key . ')');
                    $result = $plugin_manager->install_plugin($plugin_slug);
                    if ($result !== true) {
                        error_log('WP Fast Setup: Failed to install plugin: ' . $plugin_slug);
                    }
                }
            }
        }

        // Process Google Drive ZIP files
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'install_drive_zip_') === 0) {
                $file_id = $value;
                // Extract filename from the key
                $filename_key = str_replace('install_drive_zip_', '', $key);
                $filename = str_replace('_', '.', $filename_key) . '.zip'; // Reconstruct filename
                error_log('WP Fast Setup: Installing plugin from Google Drive: ' . $filename . ' (ID: ' . $file_id . ')');
                $result = $plugin_manager->install_plugin_from_drive_zip($file_id, $filename);
                if ($result !== true) {
                    error_log('WP Fast Setup: Failed to install plugin from Google Drive: ' . $filename);
                }
            }
        }

        // Process local ZIP files
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'install_local_zip_') === 0) {
                $filename_key = str_replace('install_local_zip_', '', $key);
                $filename = str_replace('_', '.', $filename_key) . '.zip'; // Reconstruct filename
                $zip_path = WP_FAST_SETUP_PLUGIN_DIR . 'zip-files/' . $filename;
                error_log('WP Fast Setup: Installing plugin from local ZIP: ' . $zip_path);
                $result = $plugin_manager->install_plugin_from_zip($zip_path);
                if ($result !== true) {
                    error_log('WP Fast Setup: Failed to install plugin from local ZIP: ' . $filename);
                }
            }
        }

        error_log('WP Fast Setup: Plugin installation data processing completed');
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

    /**
     * Handle image upload for logo and favicon
     */
    private function handle_image_upload($field_name)
    {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $uploaded_file = $_FILES[$field_name];

        // Check if file was uploaded
        if ($uploaded_file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Handle the upload
        $upload_overrides = array('test_form' => false);
        $uploaded_file_info = wp_handle_upload($uploaded_file, $upload_overrides);

        if (isset($uploaded_file_info['error'])) {
            return false;
        }

        // Create attachment
        $attachment_id = wp_insert_attachment(
            array(
                'guid' => $uploaded_file_info['url'],
                'post_mime_type' => $uploaded_file_info['type'],
                'post_title' => sanitize_file_name(basename($uploaded_file_info['file'])),
                'post_content' => '',
                'post_status' => 'inherit'
            ),
            $uploaded_file_info['file']
        );

        if (is_wp_error($attachment_id)) {
            return false;
        }

        // Generate metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file_info['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        return $attachment_id;
    }

    /**
     * Get human readable language name from language code
     */
    private function get_language_name($lang_code)
    {
        $languages = array(
            'es_ES' => 'Español',
            'en_US' => 'English (US)',
            'en_GB' => 'English (UK)',
            'fr_FR' => 'Français',
            'de_DE' => 'Deutsch',
            'it_IT' => 'Italiano',
            'pt_PT' => 'Português',
            'pt_BR' => 'Português (Brasil)',
            'ru_RU' => 'Русский',
            'ja' => '日本語',
            'zh_CN' => '中文 (简体)',
            'zh_TW' => '中文 (繁體)',
            'ar' => 'العربية',
            'hi_IN' => 'हिन्दी'
        );

        return isset($languages[$lang_code]) ? $languages[$lang_code] : $lang_code;
    }

    /**
     * AJAX handler for saving site settings
     */
    public function ajax_save_site_settings()
    {
        error_log('WP Fast Setup: ajax_save_site_settings called');

        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            error_log('WP Fast Setup: Invalid nonce in ajax_save_site_settings');
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            error_log('WP Fast Setup: Insufficient permissions in ajax_save_site_settings');
            wp_send_json_error('Insufficient permissions');
        }

        try {
            error_log('WP Fast Setup: Processing site settings update');

            // Site Settings
            if (!empty($_POST['nombre_sitio'])) {
                $old_name = get_option('blogname');
                $new_name = sanitize_text_field($_POST['nombre_sitio']);
                update_option('blogname', $new_name);
                error_log("WP Fast Setup: Blog name updated from '$old_name' to '$new_name'");
            }

            if (!empty($_POST['idioma_sitio'])) {
                $new_language = sanitize_text_field($_POST['idioma_sitio']);
                $current_language = get_option('WPLANG');

                if ($new_language !== $current_language) {
                    update_option('WPLANG', $new_language);
                    wp_cache_flush();
                    error_log("WP Fast Setup: Language updated from '$current_language' to '$new_language'");
                }
            }

            if (!empty($_POST['url_sitio'])) {
                $old_url = get_option('siteurl');
                $new_url = esc_url_raw($_POST['url_sitio']);
                update_option('siteurl', $new_url);
                update_option('home', $new_url);
                error_log("WP Fast Setup: Site URL updated from '$old_url' to '$new_url'");
            }

            if (!empty($_POST['admin_email'])) {
                $old_email = get_option('admin_email');
                $new_email = sanitize_email($_POST['admin_email']);
                update_option('admin_email', $new_email);
                error_log("WP Fast Setup: Admin email updated from '$old_email' to '$new_email'");
            }

            if (!empty($_POST['site_tagline'])) {
                $old_tagline = get_option('blogdescription');
                $new_tagline = sanitize_text_field($_POST['site_tagline']);
                update_option('blogdescription', $new_tagline);
                error_log("WP Fast Setup: Site tagline updated from '$old_tagline' to '$new_tagline'");
            }

            // Handle blog visibility (indexing)
            $blog_public = isset($_POST['blog_public']) ? 1 : 0;
            update_option('blog_public', $blog_public);

            // Handle logo upload
            if (!empty($_FILES['site_logo']['name'])) {
                $logo_attachment_id = $this->handle_image_upload('site_logo');
                if ($logo_attachment_id) {
                    update_option('wp_fast_setup_site_logo', $logo_attachment_id);
                }
            }

            // Handle favicon upload
            if (!empty($_FILES['site_favicon']['name'])) {
                $favicon_attachment_id = $this->handle_image_upload('site_favicon');
                if ($favicon_attachment_id) {
                    update_option('site_icon', $favicon_attachment_id);
                }
            }

            wp_send_json_success('Configuración del sitio guardada correctamente');
        } catch (Exception $e) {
            wp_send_json_error('Error al guardar la configuración: ' . $e->getMessage());
        }
    }

    /**
     * AJAX handler for creating pages
     */
    public function ajax_create_pages()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
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
     * AJAX handler for creating menus
     */
    public function ajax_create_menus()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        try {
            $menus_input = stripslashes($_POST['menus_input']);

            // Create menus
            $created_menus = $this->create_menus_from_input($menus_input);

            wp_send_json_success(array(
                'message' => 'Menús creados correctamente',
                'menus_count' => count($created_menus)
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error al crear menús: ' . $e->getMessage());
        }
    }

    /**
     * Create menus from the input.
     * Returns an array with created menus info (ID, name)
     */
    private function create_menus_from_input($input)
    {
        $created_menus = array();

        // Explode input into lines.
        $lines = explode("\n", $input);

        foreach ($lines as $line) {
            $menu_name = trim($line);

            // Skip empty lines.
            if (empty($menu_name)) {
                continue;
            }

            // Check if menu already exists.
            if (wp_get_nav_menu_object($menu_name)) {
                continue;
            }

            // Create the menu.
            $menu_id = wp_create_nav_menu($menu_name);

            if (!is_wp_error($menu_id)) {
                $created_menus[] = array(
                    'ID'   => $menu_id,
                    'name' => $menu_name,
                );
            }
        }

        return $created_menus;
    }

    /**
     * AJAX handler for setting homepage and blog page
     */
    public function ajax_set_homepage()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        try {
            $homepage_id = intval($_POST['homepage_page']);
            $blogpage_id = intval($_POST['blog_page']);

            // Set homepage
            if ($homepage_id > 0) {
                update_option('page_on_front', $homepage_id);
                update_option('show_on_front', 'page');
            }

            // Set blog page
            if ($blogpage_id > 0) {
                update_option('page_for_posts', $blogpage_id);
            }

            wp_send_json_success(array(
                'message' => 'Páginas configuradas correctamente',
                'homepage_set' => $homepage_id > 0,
                'blogpage_set' => $blogpage_id > 0
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error al configurar páginas: ' . $e->getMessage());
        }
    }

    /**
     * AJAX handler for activating features
     */
    public function ajax_activate_features()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions'));
            return;
        }

        try {
            require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-theme-manager.php';
            $theme_manager = new Theme_Manager();

            $activated_features = array();

            if (isset($_POST['activar_permalinks'])) {
                $theme_manager->activate_permalinks();
                $activated_features[] = 'permalinks';
            }
            if (isset($_POST['activar_hello_elementor'])) {
                $theme_manager->activate_hello_theme();
                $activated_features[] = 'hello_elementor_theme';
            }
            if (isset($_POST['desactivar_comentarios'])) {
                $theme_manager->disable_comments();
                $activated_features[] = 'comments_disabled';
            }

            wp_send_json_success(array(
                'message' => 'Características activadas correctamente',
                'activated_features' => $activated_features
            ));
        } catch (Exception $e) {
            wp_send_json_error(array('message' => 'Error al activar características: ' . $e->getMessage()));
        }
    }

    /**
     * AJAX handler for saving Google Drive settings
     */
    public function ajax_save_google_drive()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        try {
            $api_key_input = sanitize_text_field($_POST['google_drive_api_key'] ?? '');
            $folder_id_input = sanitize_text_field($_POST['google_drive_folder_id'] ?? '');

            update_option('wp_fast_setup_google_drive_api_key', $api_key_input ?: WP_FAST_SETUP_DEFAULT_API_KEY);
            update_option('wp_fast_setup_google_drive_folder_id', $folder_id_input ?: WP_FAST_SETUP_DEFAULT_FOLDER_ID);

            // Get ZIP files from Google Drive to update the plugin list
            $api_key = get_option('wp_fast_setup_google_drive_api_key');
            $folder_id = get_option('wp_fast_setup_google_drive_folder_id');

            $zip_files = $this->get_drive_zip_files($api_key, $folder_id);

            if (isset($zip_files['error'])) {
                wp_send_json_success(array(
                    'message' => 'Configuración guardada, pero error al obtener archivos de Google Drive: ' . $zip_files['error'],
                    'zip_files' => array()
                ));
            } else {
                wp_send_json_success(array(
                    'message' => 'Configuración de Google Drive guardada correctamente',
                    'zip_files' => $zip_files
                ));
            }
        } catch (Exception $e) {
            wp_send_json_error('Error al guardar configuración de Google Drive: ' . $e->getMessage());
        }
    }

    /**
     * AJAX handler for adding a plugin to favorites
     */
    public function ajax_add_favorite()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            wp_send_json_error('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $slug = sanitize_text_field($_POST['slug']);
        $source = sanitize_text_field($_POST['source']);

        if (empty($slug) || empty($source)) {
            wp_send_json_error('Slug and source are required');
        }

        $json_file = WP_FAST_SETUP_PLUGIN_DIR . 'includes/plugins-list.json';
        if (!file_exists($json_file)) {
            wp_send_json_error('Plugins list file not found');
        }

        $data = json_decode(file_get_contents($json_file), true);
        if (!isset($data['favoritos'])) {
            $data['favoritos'] = [];
        }

        // Check if already in favorites
        $exists = false;
        foreach ($data['favoritos'] as $fav) {
            if ($fav['slug'] === $slug) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $data['favoritos'][] = ['slug' => $slug, 'source' => $source];
            file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT));
            wp_send_json_success('Plugin added to favorites');
        } else {
            wp_send_json_error('Plugin already in favorites');
        }
    }
}
