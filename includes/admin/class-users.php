<?php

/**
 * Crear usuario con rol de administrador
 */

class User_manager
{
    public function __construct()
    {
        add_action('admin_notices', array($this, 'crear_usuario'));
    }

    public function crear_usuario($username, $password, $email)
    {

        if (username_exists($username) || email_exists($email)) {
            echo '<div class="notice notice-error"><p>El usuario o el correo ya existen.</p></div>';
            return;
        }

        $user_id = wp_create_user($username, $password, $email);
        if (!is_wp_error($user_id)) {
            $user = new WP_User($user_id);
            $user->set_role('administrator');
            echo '<div class="notice notice-success"><p>Usuario creado como administrador.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Error al crear el usuario: ' . $user_id->get_error_message() . '</p></div>';
        }
    }
}
