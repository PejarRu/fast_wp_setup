<?php


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