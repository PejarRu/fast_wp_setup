<?php

class Theme_Manager
{
    /**
     * Activate permalinks
     */
    public function activate_permalinks()
    {
        update_option('permalink_structure', '/%postname%/');
        flush_rewrite_rules();
    }

    /**
     * Disable comments globally
     */
    public function disable_comments()
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
