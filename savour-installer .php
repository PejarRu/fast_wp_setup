<?php
/*
Plugin Name: Savour Manager v.1
Description: Configura el sitio, instala plugins habituales, crea páginas básicas. Carga JSON para header, footer, single y abogado desde archivos .json externos, con metadatos extra para reconocer plantillas (header, footer, single-post, etc.). Incluye parche: elimina _elementor_pro_version si solo está Pro Elements.
Version: 3.0
Author: Alex Parra
Text Domain: savour-manager
*/

// Evitar acceso directo
if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * Chequea si Elementor Pro (o Pro Elements) está activo
 */
function savour_manager_has_theme_builder() {
    if ( did_action('elementor_pro/init') ) {
        return true;
    }
    if ( class_exists('ProElements\Plugin') ) {
        return true;
    }
    return false;
}

/**
 * Añade el menú del plugin en el Escritorio
 */
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
add_action('admin_menu', 'savour_manager_menu');

/**
 * Página Principal (formulario) del plugin
 */
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
            $zip_path = plugin_dir_path(__FILE__) . 'pro-elements.zip';
            savour_manager_install_plugin_from_zip($zip_path);
        }
        // -- Instalar Elementor Pro (ZIP Local)
        if (isset($_POST['install_elementor_pro'])) {
            $zip_path = plugin_dir_path(__FILE__) . 'elementor-pro.zip';
            savour_manager_install_plugin_from_zip($zip_path);
        }

        // -- Instalar CustomFastBlog By Savour
        if (isset($_POST['install_custom_fast_blog'])) {
            $zip_path = plugin_dir_path(__FILE__) . 'custom-fast-blog.zip';
            savour_manager_install_plugin_from_zip($zip_path);
        }

        // -- Instalar CustomFastBlog By Savour
        if (isset($_POST['install_metadebugger'])) {
            $zip_path = plugin_dir_path(__FILE__) . 'metadebugger.zip';
            savour_manager_install_plugin_from_zip($zip_path);
        }
        
        // -- Instalar CustomFastBlog By Savour
        if (isset($_POST['install_autoase'])) {
            $zip_path = plugin_dir_path(__FILE__) . 'autoconfigurador-ase.zip';
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

/**
 * Instalar/activar plugins desde el repositorio
 */
function savour_manager_install_plugin($slug) {
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    $plugin_info = plugins_api('plugin_information', [ 'slug' => $slug ]);
    if (!is_wp_error($plugin_info)) {
        $upgrader = new Plugin_Upgrader();
        $upgrader->install($plugin_info->download_link);
        activate_plugin($slug . '/' . $slug . '.php');
    }
}

/**
 * Instalar/Activar Pro Elements desde un ZIP local
 */
function savour_manager_install_plugin_from_zip($zip_file_path) {
    if (!file_exists($zip_file_path)) {
        echo '<div class="error"><p>No se encontró el archivo ZIP: ' . esc_html($zip_file_path) . '</p></div>';
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/misc.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    $upgrader  = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
    $installed = $upgrader->install($zip_file_path);

    if (is_wp_error($installed)) {
        echo '<div class="error"><p>Error al instalar plugin desde ZIP: ' . esc_html($installed->get_error_message()) . '</p></div>';
        return;
    } elseif (!$installed) {
        echo '<div class="error"><p>No se pudo instalar el plugin desde ZIP.</p></div>';
        return;
    }

    // Activar
    $plugin_relative_path = 'pro-elements/pro-elements.php';
    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_relative_path)) {
        $activate = activate_plugin($plugin_relative_path);
        if (is_wp_error($activate)) {
            echo '<div class="error"><p>Error al activar Pro Elements: ' . esc_html($activate->get_error_message()) . '</p></div>';
        } else {
            echo '<div class="updated"><p>Pro Elements instalado y activado correctamente.</p></div>';
        }
    }
}

/**
 * Crear Páginas Básicas
 */
function savour_manager_crear_paginas_basicas() {
    $contact_email = "alejandroparra@savour.es";
    $year = date("Y");

    $paginas = [
        'Inicio'        => '',
        'Blog'          => '',
        'Servicios'     => '',
        'Contacto'      => ''
    ];

    foreach ($paginas as $titulo => $contenido) {
        if (!get_page_by_title($titulo)) {
            $post_id = wp_insert_post([
                'post_title'   => $titulo,
                'post_content' => $contenido,
                'post_status'  => 'publish',
                'post_type'    => 'page'
            ]);
            update_post_meta($post_id, '_wp_page_template', 'elementor_canvas');
        }
    }
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
 * Borrar Todas las Páginas
 */
function savour_manager_borrar_paginas() {
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
 * Crear usuario savour con rol de administrador
 */
function savour_manager_crear_usuario_savour() {
    $username = 'savour';
    $password = 'antonelchuleta?';
    $email    = 'alejandroparra@savour.es';

    if (username_exists($username) || email_exists($email)) {
        echo '<div class="notice notice-error"><p>El usuario savour o el correo ya existen.</p></div>';
        return;
    }

    $user_id = wp_create_user($username, $password, $email);
    if (!is_wp_error($user_id)) {
        $user = new WP_User($user_id);
        $user->set_role('administrator');
        echo '<div class="notice notice-success"><p>Usuario savour creado como administrador.</p></div>';
    } else {
        echo '<div class="notice notice-error"><p>Error al crear el usuario savour: ' . $user_id->get_error_message() . '</p></div>';
    }
}

/* =========================
   FUNCIONES PARA CARGAR Y CREAR PLANTILLAS DESDE .json
   (Permitiendo múltiples 'single' con export_type)
   ========================= */

/**
 * Lee un archivo .json (en la misma carpeta del plugin) y devuelve su contenido.
 */
function savour_manager_load_json_file( $filename ) {
    $filepath = plugin_dir_path(__FILE__) . $filename;
    if ( ! file_exists($filepath) ) {
        echo '<div class="notice notice-error"><p>No se encontró el archivo JSON: ' . esc_html($filename) . '</p></div>';
        return '';
    }
    return file_get_contents($filepath);
}

/**
 * Crear Header (JSON) si no existe ya (header.json).
 */
function savour_manager_create_header_from_json() {
    if ( ! savour_manager_has_theme_builder() ) {
        echo '<div class="notice notice-error"><p>No se detecta Elementor Pro / Pro Elements activo. No se puede crear la cabecera.</p></div>';
        return;
    }
    $existing = savour_manager_get_template_by_type('header');
    if ($existing) {
        echo '<div class="notice notice-warning"><p>Ya existe un header (ID ' . $existing->ID . '). No se crea otro.</p></div>';
        return;
    }
    $json_content = savour_manager_load_json_file('header.json');
    if ( empty($json_content) ) {
        return;
    }
    savour_manager_insert_elementor_template($json_content, 'Cabecera Savour JSON', 'header', 'include/general');
}

/**
 * Crear Footer (JSON) si no existe ya (footer.json).
 */
function savour_manager_create_footer_from_json() {
    if ( ! savour_manager_has_theme_builder() ) {
        echo '<div class="notice notice-error"><p>No se detecta Elementor Pro / Pro Elements activo. No se puede crear el footer.</p></div>';
        return;
    }
    $existing = savour_manager_get_template_by_type('footer');
    if ($existing) {
        echo '<div class="notice notice-warning"><p>Ya existe un footer (ID ' . $existing->ID . '). No se crea otro.</p></div>';
        return;
    }
    $json_content = savour_manager_load_json_file('footer.json');
    if ( empty($json_content) ) {
        return;
    }

    savour_manager_insert_elementor_template($json_content, 'Footer Savour JSON', 'footer', 'include/general');
}

/**
 * Crear Single Page (JSON) si no existe ya (single.json).
 * Aplica la condición => 'include/singular/page' para TODAS las páginas.
 */
function savour_manager_create_single_from_json() {
    if ( ! savour_manager_has_theme_builder() ) {
        echo '<div class="notice notice-error"><p>No se detecta Elementor Pro / Pro Elements activo. No se puede crear la single page (contacto).</p></div>';
        return;
    }
    $existing = savour_manager_get_template_by_type('single','contacto');
    if ($existing) {
        echo '<div class="notice notice-warning"><p>Ya existe la single de contacto (ID ' . $existing->ID . '). No se crea otra.</p></div>';
        return;
    }
    $json_content = savour_manager_load_json_file('single.json');
    if ( empty($json_content) ) {
        return;
    }

    savour_manager_insert_elementor_template($json_content, 'Single Contacto JSON', 'single', 'include/singular/page','contacto');
}

function savour_manager_create_personal_from_json() {
    if ( ! savour_manager_has_theme_builder() ) {
        echo '<div class="notice notice-error"><p>No se detecta Elementor Pro / Pro Elements activo. No se puede crear la plantilla Personal.</p></div>';
        return;
    }
    $template_type = 'single';  
    $export_type   = 'personal';  // p.ej. para distinguir
    $condition     = 'include/general'; // Ajusta si quieres otra

    // Verifica si existe ya
    $existing = savour_manager_get_template_by_type($template_type, $export_type);
    if ($existing) {
        echo '<div class="notice notice-warning"><p>Ya existe la single "personal" (ID ' . $existing->ID . '). No se crea otra.</p></div>';
        return;
    }
    // Cargar el JSON
    $json_content = savour_manager_load_json_file('personal.json');
    if ( empty($json_content) ) {
        return;
    }

    // Insertar
    savour_manager_insert_elementor_template($json_content, 'Plantilla Web Personal', $template_type, $condition, $export_type);
}

function savour_manager_create_estetica_from_json() {
    if ( ! savour_manager_has_theme_builder() ) {
        echo '<div class="notice notice-error"><p>No se detecta Elementor Pro / Pro Elements activo. No se puede crear la plantilla Estética.</p></div>';
        return;
    }
    $template_type = 'single';  
    $export_type   = 'estetica'; 
    $condition     = 'include/general'; // o lo que necesites

    $existing = savour_manager_get_template_by_type($template_type, $export_type);
    if ($existing) {
        echo '<div class="notice notice-warning"><p>Ya existe la single "estetica" (ID ' . $existing->ID . '). No se crea otra.</p></div>';
        return;
    }
    $json_content = savour_manager_load_json_file('estetica.json');
    if ( empty($json_content) ) {
        return;
    }

    savour_manager_insert_elementor_template($json_content, 'Plantilla Estética JSON', $template_type, $condition, $export_type);
}


/**
 * Crear Plantilla Abogado (JSON) si no existe ya (abogado.json).
 */
function savour_manager_create_abogado_from_json() {
    if ( ! savour_manager_has_theme_builder() ) {
        echo '<div class="notice notice-error"><p>No se detecta Elementor Pro / Pro Elements activo. No se puede crear la plantilla Abogado.</p></div>';
        return;
    }
    $template_type = 'single';  
    $export_type   = 'abogado'; 
    $condition     = 'include/general'; // o 'include/singular/page'

    $existing = savour_manager_get_template_by_type($template_type, $export_type);
    if ($existing) {
        echo '<div class="notice notice-warning"><p>Ya existe la single "abogado" (ID ' . $existing->ID . '). No se crea otra.</p></div>';
        return;
    }
    $json_content = savour_manager_load_json_file('abogado.json');
    if ( empty($json_content) ) {
        return;
    }

    savour_manager_insert_elementor_template($json_content, 'Plantilla Abogado JSON', $template_type, $condition, $export_type);
}

/**
 * Insertar la plantilla en `elementor_library`, con parche para single-post / single-page
 */
function savour_manager_insert_elementor_template($full_exported_json, $post_title, $template_type, $condition = 'include/general', $export_type = '') {

    $decoded = json_decode($full_exported_json, true);
    if (! is_array($decoded) || empty($decoded['content'])) {
        echo '<div class="notice notice-error"><p>No se pudo decodificar el JSON o no se encuentra "content".</p></div>';
        return;
    }

    // 1) Detectar "type" del JSON
    $json_type = !empty($decoded['type']) ? $decoded['type'] : $template_type;

    // Por defecto
    $elementor_template_type = $template_type;
    $theme_template_type     = '';
    $display_conditions      = '';
    $conditions_data         = [];

    // 2) Ajustar si es single-post => single + theme=single + conditions...
    if ( $json_type === 'header' ) {
        $elementor_template_type = 'header';
        $theme_template_type     = 'header';
        $display_conditions      = 'yes';
        $conditions_data         = [
            ['type' => 'general','value' => 'entire_site'],
        ];
    }
    elseif ( $json_type === 'footer' ) {
        $elementor_template_type = 'footer';
        $theme_template_type     = 'footer';
        $display_conditions      = 'yes';
        $conditions_data         = [
            ['type' => 'general','value' => 'entire_site'],
        ];
    }
    elseif ( $json_type === 'single-post' ) {
        // single para posts
        $elementor_template_type = 'single-post';
        $theme_template_type     = 'single';
        $display_conditions      = 'yes';
        $conditions_data         = [
            ['type' => 'post_type','value' => 'post'],
        ];
    }
    elseif ( $json_type === 'single-page' ) {
        $elementor_template_type = 'single-page';
        $theme_template_type     = 'single';
        $display_conditions      = 'yes';
        $conditions_data         = [
            ['type' => 'post_type','value' => 'page'],
        ];
    }
    // Agrega más si quieres 'archive', etc.

    // 3) Convertir content a string JSON
    $elementor_data_string = wp_json_encode($decoded['content']);

    // 4) Armamos meta
    $meta_input = [
        '_elementor_edit_mode'     => 'builder',
        '_elementor_template_type' => $elementor_template_type,
        '_elementor_version'       => '3.26.4',
        '_elementor_pro_version'   => '3.25.3',
        '_wp_page_template'        => 'default',
        '_elementor_data'          => $elementor_data_string,
        // Aplica la condición básica, 
        '_elementor_conditions'    => serialize([$condition]),
        '_elementor_page_assets'   => [
            'styles'  => ['widget-heading','widget-nav-menu'],
            'scripts' => ['smartmenus'],
        ],
    ];
    // theme_template_type
    if ($theme_template_type) {
        $meta_input['_elementor_theme_template_type'] = $theme_template_type;
    }
    // display_conditions
    if ($display_conditions) {
        $meta_input['_elementor_display_conditions'] = $display_conditions;
    }
    // conditions_data
    if ($conditions_data) {
        $meta_input['_elementor_conditions_data'] = serialize($conditions_data);
    }
    // export_type
    if ($export_type) {
        $meta_input['_elementor_export_type'] = $export_type;
    }

    // Quitar _elementor_pro_version si NO hay Elementor Pro y sí Pro Elements
    if ( ! did_action('elementor_pro/init') && class_exists('ProElements\Plugin') ) {
        unset($meta_input['_elementor_pro_version']);
    }

    // 5) Insertar en elementor_library
    $postarr = [
        'post_title'  => $post_title,
        'post_type'   => 'elementor_library',
        'post_status' => 'publish',
        'meta_input'  => $meta_input,
    ];
    $post_id = wp_insert_post($postarr);

    if ( is_wp_error($post_id) ) {
        echo '<div class="notice notice-error"><p>Error al crear plantilla (' . $elementor_template_type . '): ' . $post_id->get_error_message() . '</p></div>';
    } else {
        echo '<div class="notice notice-success"><p>' . ucfirst($elementor_template_type) . ' "' . $export_type . '" creado con ID ' . $post_id . '. Se configuró "type" como <strong>' . $json_type . '</strong>.</p></div>';
    }
}

/**
 * Función auxiliar para detectar si ya existe una plantilla de cierto tipo + export_type.
 */
function savour_manager_get_template_by_type($type, $export_type = '') {
    $meta_query = [
        'relation' => 'AND',
        [
            'key'   => '_elementor_template_type',
            'value' => $type,
        ],
    ];
    if ($export_type) {
        $meta_query[] = [
            'key'   => '_elementor_export_type',
            'value' => $export_type,
        ];
    }

    $args = [
        'post_type'      => 'elementor_library',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'meta_query'     => $meta_query,
    ];
    $q = new WP_Query($args);
    if ($q->have_posts()) {
        return $q->posts[0];
    }
    return false;
}

/* =========================
   ESTILOS DE ADMIN
   ========================= */
function savour_manager_admin_bar_style() {
    echo '<style>
        #wpadminbar {
            background-color: #212121 !important;
        }
        #wpadminbar .ab-item, #wpadminbar a.ab-item {
            color: white !important;
        }
        #wpadminbar .ab-item:hover, #wpadminbar a.ab-item:hover {
            background-color: #ed9032 !important;
        }
    </style>';
}
add_action('wp_head', 'savour_manager_admin_bar_style');
add_action('admin_head', 'savour_manager_admin_bar_style');

function savour_manager_admin_menu_style() {
    echo '<style>
        #toplevel_page_configurador-sitio {
            background-color: #ea0f0f;
        }
        #toplevel_page_configurador-sitio a {
            color: white !important;
        }
        #toplevel_page_configurador-sitio a:hover,
        #toplevel_page_configurador-sitio a:active {
            background-color: #981e1e !important;
        }
    </style>';
}
add_action('admin_head', 'savour_manager_admin_menu_style');

function savour_manager_icon_color() {
    echo '<style>
        #adminmenu .toplevel_page_configurador-sitio .wp-menu-image:before {
            font-family: "dashicons";
            content: "\f527"; /* dashicon ghost */
            color: white !important;
        }
    </style>';
}
add_action('admin_head', 'savour_manager_icon_color');

function savour_manager_active_menu_color() {
    echo '<style>
        #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head,
        #adminmenu .wp-menu-arrow,
        #adminmenu .wp-menu-arrow div,
        #adminmenu li.current a.menu-top,
        #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu {
            background-color: #ea0f0f !important;
            color: white !important;
            font-size:15px;
        }
    </style>';
}
add_action('admin_head', 'savour_manager_active_menu_color');
