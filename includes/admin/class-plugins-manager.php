
<?php
// Plugin installation and management
/**
 * Instalar/activar plugins desde el repositorio
 */
class Plugin_Manager
{

    // Optional constructor if you want to hook actions/filters
    public function __construct() {
        // For example, you could initialize something here
    }
    

    /**
     * Instalar y activar plugins desde el repositorio.
     * @param string $slug Plugin slug to install.
     * @param string $plugin_file Optional relative plugin file path (default: "$slug/$slug.php")
     * @return bool|string True on success, or error message on failure.
     */
    function install_plugin($slug)
    {
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $plugin_info = plugins_api('plugin_information', ['slug' => $slug]);
        if (!is_wp_error($plugin_info)) {
            $upgrader = new Plugin_Upgrader();
            $upgrader->install($plugin_info->download_link);
            activate_plugin($slug . '/' . $slug . '.php');
        }
    }

    /**
     * Instalar y activar plugin desde un ZIP local.
     * @param string $zip_file_path Ruta al archivo ZIP.
     * @param string $plugin_file Relative plugin file path for activation (default: 'pro-elements/pro-elements.php').
     * @return bool|string True on success, or error message on failure.
     */
    function install_plugin_from_zip($zip_file_path)
    {
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
     * Chequea si Elementor Pro (o Pro Elements) está activo
     */
    function has_theme_builder()
    {
        if (did_action('elementor_pro/init')) {
            return true;
        }
        if (class_exists('ProElements\Plugin')) {
            return true;
        }
        return false;
    }
}
