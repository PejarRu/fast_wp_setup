<?php

/* =========================
   FUNCIONES PARA CARGAR Y CREAR PLANTILLAS DESDE .json
   (Permitiendo múltiples 'single' con export_type)
   ========================= */

/**
 * Lee un archivo .json (en la misma carpeta del plugin) y devuelve su contenido.
 */
function savour_manager_load_json_file( $filename ) {
    $filepath = plugin_dir_path(__FILE__) .'/includes/data/'. $filename;
    if ( ! file_exists($filepath) ) {
        echo '<div class="notice notice-error"><p>No se encontró el archivo JSON: ' . esc_html($filename) . '</p></div>';
        return '';
    }
    return file_get_contents($filepath);
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
