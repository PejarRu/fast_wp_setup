<?php
// Admin menu and page rendering
function savour_manager_menu() {
    add_menu_page(
        'Savour Manager v.1',
        'Savour Manager v.1',
        'manage_options',
        'configurador-sitio',
        'savour_manager_page',
        'dashicons-admin-generic'
    );
}


/**
 * Desactivar Comentarios globalmente
 */
function savour_manager_desactivar_comentarios() {
    update_option('default_comment_status', 'closed');
    update_option('page_comments', 'closed');
    update_option('comments_open', false);
    update_option('default_pingback_flag', false);
    update_option('default_ping_status', 'closed');
}



/**
 * Activar tema Hello Elementor
 */
function savour_manager_activate_hello_theme() {
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


function savour_manager_page() {
    if ( ! current_user_can('manage_options') ) {
        wp_die('No tienes permisos para acceder a esta página.');
    }

    // Procesar formularios
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // -- Ajustes del Sitio
        if (!empty($_POST['nombre_sitio'])) {
            update_option('blogname', sanitize_text_field($_POST['nombre_sitio']));
        }
        if (!empty($_POST['idioma_sitio'])) {
            update_option('WPLANG', sanitize_text_field($_POST['idioma_sitio']));
        }
        if (!empty($_POST['url_sitio'])) {
            update_option('siteurl', esc_url_raw($_POST['url_sitio']));
        }
        if (isset($_POST['activar_permalinks'])) {
            update_option('permalink_structure', '/%postname%/');
            flush_rewrite_rules();
        }

        // -- Activar Hello Elementor
        if (isset($_POST['activar_hello_elementor'])) {
            savour_manager_activate_hello_theme();
        }

        // -- Instalar/activar plugins
        if (isset($_POST['install_elementor']))      savour_manager_install_plugin('elementor');
        if (isset($_POST['install_sitekit']))        savour_manager_install_plugin('google-site-kit');
        if (isset($_POST['install_recaptcha']))      savour_manager_install_plugin('advanced-google-recaptcha');
        if (isset($_POST['install_translatepress'])) savour_manager_install_plugin('translatepress-multilingual');
        if (isset($_POST['install_wpchat']))         savour_manager_install_plugin('wp-whatsapp');
        if (isset($_POST['install_advanced']))       savour_manager_install_plugin('advanced-custom-fields');
        if (isset($_POST['install_ase']))            savour_manager_install_plugin('admin-site-enhancements');
        if (isset($_POST['install_seo']))            savour_manager_install_plugin('wordpress-seo');
        if (isset($_POST['install_updraft']))        savour_manager_install_plugin('updraftplus');
        if (isset($_POST['install_complianz']))      savour_manager_install_plugin('complianz-gdpr');
        if (isset($_POST['install_woocommerce']))      savour_manager_install_plugin('woocommerce');
        if (isset($_POST['install_wplog']))      savour_manager_install_plugin('wp-security-audit-log');
        if (isset($_POST['install_filemanager']))      savour_manager_install_plugin('wp-file-manager');
        if (isset($_POST['install_under']))      savour_manager_install_plugin('under-construction-page');




        // -- Instalar Pro Elements (ZIP local)
        if (isset($_POST['install_pro_elements_zip'])) {
            $zip_path = plugin_dir_path(__FILE__) .'/includes/zip-files/'. 'pro-elements.zip';
            savour_manager_install_plugin_from_zip($zip_path);
        }
        // -- Instalar Elementor Pro (ZIP Local)
        if (isset($_POST['install_elementor_pro'])) {
            $zip_path = plugin_dir_path(__FILE__) .'/includes/zip-files/'. 'elementor-pro.zip';
            savour_manager_install_plugin_from_zip($zip_path);
        }

        // -- Instalar CustomFastBlog By Savour
        if (isset($_POST['install_custom_fast_blog'])) {
            $zip_path = plugin_dir_path(__FILE__) .'/includes/zip-files/'. 'custom-fast-blog.zip';
            savour_manager_install_plugin_from_zip($zip_path);
        }

        // -- Instalar CustomFastBlog By Savour
        if (isset($_POST['install_metadebugger'])) {
            $zip_path = plugin_dir_path(__FILE__) .'/includes/zip-files/'. 'metadebugger.zip';
            savour_manager_install_plugin_from_zip($zip_path);
        }
        
        // -- Instalar CustomFastBlog By Savour
        if (isset($_POST['install_autoase'])) {
            $zip_path = plugin_dir_path(__FILE__) .'/includes/zip-files/'. 'autoconfigurador-ase.zip';
            savour_manager_install_plugin_from_zip($zip_path);
        }

        // -- Crear Páginas Básicas
        if (isset($_POST['crear_paginas_basicas'])) {
            savour_manager_crear_paginas_basicas();
        }

        // -- Desactivar Comentarios
        if (isset($_POST['desactivar_comentarios'])) {
            savour_manager_desactivar_comentarios();
        }

        // -- Borrar todas las páginas
        if (isset($_POST['borrar_paginas'])) {
            savour_manager_borrar_paginas();
        }

        // -- Crear usuario savour
        if (isset($_POST['activar_usuario'])) {
            savour_manager_crear_usuario_savour();
        }

        // -- Botones para crear Header, Footer, Single, Abogado
        if (isset($_POST['savour_json_create_header'])) {
            savour_manager_create_header_from_json();
        }
        if (isset($_POST['savour_json_create_footer'])) {
            savour_manager_create_footer_from_json();
        }
        if (isset($_POST['savour_json_create_single'])) {
            savour_manager_create_single_from_json();
        }
        if (isset($_POST['savour_json_create_abogado'])) {
            savour_manager_create_abogado_from_json();
        }
        if (isset($_POST['savour_json_create_personal'])) {
            savour_manager_create_personal_from_json();
        }
        if (isset($_POST['savour_json_create_estetica'])) {
            savour_manager_create_estetica_from_json();
        }

        echo '<div class="updated"><p>Configuración actualizada.</p></div>';
    }

    ?>
    
    <div class="wrap savour-manager-wrap" style="max-width: 1100px; margin: 0 auto;">
        <h1 style="font-size:2em; margin-bottom:0.5em;">Savour Manager v.1</h1>
        <p style="color: #666;">Configurador rápido para instalar plugins y crear páginas básicas, así como plantillas de Elementor / Pro Elements.</p>

        <form method="POST" action="" style="margin-top: 20px;">
            <!-- Contenedor principal -->
            <div class="savour-manager-container" style="display:flex; flex-wrap: wrap; gap:20px;">

                <!-- Columna Izquierda -->
                <div style="flex:1; min-width: 350px; background: #fff; border:1px solid #ddd; border-radius:10px; padding:20px;">
                    <h2 style="margin-top:0;">Ajustes del Sitio</h2>
                    <hr>
                    <label style="font-weight:600;">Nombre del sitio:</label>
                    <input type="text" name="nombre_sitio" value="<?php echo esc_attr(get_option('blogname')); ?>" style="width:100%; margin-bottom:10px;">

                    <label style="font-weight:600;">Idioma del sitio:</label>
                    <input type="text" name="idioma_sitio" value="<?php echo esc_attr(get_option('WPLANG')); ?>" style="width:100%; margin-bottom:10px;">

                    <label style="font-weight:600;">URL del sitio:</label>
                    <input type="text" name="url_sitio" value="<?php echo esc_url(get_option('siteurl')); ?>" style="width:100%; margin-bottom:10px;">

                    <label style="display:block; margin:10px 0;">
                        <input type="checkbox" name="activar_permalinks"> Activar Permalinks (/%postname%/)
                    </label>
                    <label style="display:block; margin:10px 0;">
                        <input type="checkbox" name="activar_hello_elementor"> Activar tema Hello Elementor y eliminar otros temas
                    </label>
                    <label style="display:block; margin:10px 0;">
                        <input type="checkbox" name="desactivar_comentarios"> Desactivar comentarios en todo el sitio
                    </label>
                    <label style="display:block; margin:10px 0;">
                        <input type="checkbox" name="activar_usuario"> Crear usuario savour (Admin)
                    </label>
                </div><!-- /col-1 -->

                <!-- Columna Derecha -->
                <div style="flex:1; min-width: 350px; background: #fff; border:1px solid #ddd; border-radius:10px; padding:20px;">
                    <h2 style="margin-top:0;">Instalar Plugins Populares</h2>
                    <hr>
                    <div style="columns: 200px 2; -webkit-columns: 200px 2; -moz-columns: 200px 2;">
                        <label><input type="checkbox" name="install_elementor"> Elementor</label><br>
                        <label><input type="checkbox" name="install_sitekit"> Google Site Kit</label><br>
                        <label><input type="checkbox" name="install_recaptcha"> reCAPTCHA</label><br>
                        <label><input type="checkbox" name="install_translatepress"> TranslatePress</label><br>
                        <label><input type="checkbox" name="install_wpchat"> WP-Chat</label><br>
                        <label><input type="checkbox" name="install_advanced"> Advanced Custom Fields</label><br>
                        <label><input type="checkbox" name="install_ase"> Admin Site Enhancements</label><br>
                        <label><input type="checkbox" name="install_seo"> Yoast SEO</label><br>
                        <label><input type="checkbox" name="install_updraft"> UpDraftPlus</label><br>
                        <label><input type="checkbox" name="install_wplog"> WP Activity Log</label><br>
                        <label><input type="checkbox" name="install_complianz"> Complianz (GDPR)</label><br>
                        <label><input type="checkbox" name="install_pro_elements_zip"> Instalar Pro Elements (ZIP local)</label><br>
                        <label><input type="checkbox" name="install_elementor_pro"> Instalar Elementor Pro (ZIP local)</label><br>
                        <label><input type="checkbox" name="install_woocommerce"> Instalar Woocommerce</label><br>
                        <label><input type="checkbox" name="install_filemanager"> Instalar FileManager</label><br>
                        <label><input type="checkbox" name="install_under"> Instalar UnderConstruction</label><br>
                        <label><input type="checkbox" name="install_custom_fast_blog"> Instalar CustomFastBlog (ZIP local)</label><br>
                        <label><input type="checkbox" name="install_metadebugger"> Instalar MetaDebugger (ZIP local)</label><br>
                        <label><input type="checkbox" name="install_autoase"> Instalar Autoconfigurador-ASE (ZIP local)</label><br>

                    </div>
                </div><!-- /col-2 -->

            </div><!-- /row-1 -->

            <!-- Segunda Fila -->
            <div class="savour-manager-container" style="display:flex; flex-wrap: wrap; gap:20px; margin-top:20px;">
                <!-- Columna Páginas -->
                <div style="flex:1; min-width: 350px; background: #fff; border:1px solid #ddd; border-radius:10px; padding:20px;">
                    <h2 style="margin-top:0;">Páginas</h2>
                    <hr>
                    <label style="display:block; margin:10px 0;">
                        <input type="checkbox" name="crear_paginas_basicas"> Crear páginas básicas
                    </label>
                    <label style="display:block; margin:10px 0;">
                        <input type="checkbox" name="borrar_paginas"> Borrar todas las páginas publicadas
                    </label>
                </div>

                <!-- Nueva columna: Crear Header, Footer, Single, Abogado vía JSON -->
                <!-- Nueva columna: Crear Header, Footer, Single, Abogado vía JSON -->
                <div style="flex:1; min-width: 350px; background: #fff; border:1px solid #ddd; border-radius:10px; padding:20px;">
                    <h2 style="margin-top:0;">Crear Plantillas</h2>
                    <hr>
                    <p>Pulsa los botones para crear las plantillas (requiere Elementor Pro o Pro Elements).  
                    Los archivos JSON deben estar en esta misma carpeta del plugin.  
                    <strong>Si tu JSON indica "type":"single-post", se creará un Single Post con sus metadatos, etc., recuerda desactivar la carga de Google fonts local desde las características de Elementor</strong>
                    </p>
                    <button type="submit" name="savour_json_create_header" class="button button-primary">
                        Crear Header JSON
                    </button>
                    <button type="submit" name="savour_json_create_footer" class="button button-primary">
                        Crear Footer JSON
                    </button>
                    <button type="submit" name="savour_json_create_single" class="button button-primary">
                        Crear Single Page (Contacto)
                    </button>
                    <hr>
                    <h2 style="margin-top:0;">Plantillas De Oficio</h2>
                    <button type="submit" name="savour_json_create_abogado" class="button button-dark">
                        Plantilla Abogado JSON
                    </button>
                    <!-- Botones NUEVOS -->
                    <button type="submit" name="savour_json_create_personal" class="button button-dark">
                        Plantilla Web Personal
                    </button>
                    <button type="submit" name="savour_json_create_estetica" class="button button-dark">
                        Plantilla Estética
                    </button>
                </div>

            </div><!-- /row-2 -->

            <!-- Botón Guardar -->
            <div style="margin-top: 20px; text-align:right;">
                <button type="submit" class="button button-primary" style="font-size:16px; padding:8px 30px;">
                    Guardar Configuraciones
                </button>
            </div>
        </form>
    </div>
    <?php
}