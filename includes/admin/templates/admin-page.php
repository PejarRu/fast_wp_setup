<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
    <h1>WP Fast Setup</h1>
    
    <?php settings_errors('wp_fast_setup_messages'); ?>

    <form method="POST" action="">
        <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>

        <div class="wp-fast-setup-manager-container">
            <!-- Site Settings Section -->
            <div class="card w-70">
                <h2>Ajustes del Sitio</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="nombre_sitio">Nombre del sitio:</label></th>
                        <td>
                            <input type="text" id="nombre_sitio" name="nombre_sitio" 
                                   value="<?php echo esc_attr($current_site_name); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="idioma_sitio">Idioma del sitio:</label></th>
                        <td>
                            <input type="text" id="idioma_sitio" name="idioma_sitio" 
                                   value="<?php echo esc_attr($current_language); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="url_sitio">URL del sitio:</label></th>
                        <td>
                            <input type="url" id="url_sitio" name="url_sitio" 
                                   value="<?php echo esc_url($current_url); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Features Section -->
            <div class="card w-70">
                <h2>Características</h2>
                <table class="form-table">
                    <tr>
                        <th>Configuración básica:</th>
                        <td>
                            <label>
                                <input type="checkbox" name="activar_permalinks">
                                Activar Permalinks (/%postname%/)
                            </label><br>
                            <label>
                                <input type="checkbox" name="activar_hello_elementor">
                                Activar tema Hello Elementor
                            </label><br>
                            <label>
                                <input type="checkbox" name="desactivar_comentarios">
                                Desactivar comentarios
                            </label><br>
                            <label>
                                <input type="checkbox" name="activar_usuario">
                                Crear usuario administrador
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Plugins Section -->
            <div class="card w-70">
                <h2>Plugins</h2>
                <table class="form-table">
                    <tr>
                        <th><label>Instalación rápida de plugins:</label></th>
                        <td>
                        <?php
                            // Read the plugin list from the external JSON file.
                            $json_file = WP_FAST_SETUP_PLUGIN_DIR . 'includes/plugins-list.json';
                            if ( file_exists($json_file) ) {
                                $json_data = file_get_contents($json_file);
                                $data = json_decode($json_data, true);
                                if ( isset($data['plugins']) && is_array($data['plugins']) ) {
                                    foreach ($data['plugins'] as $slug => $post_key) {
                                        echo '<label><input type="checkbox" name="' . esc_attr($post_key) . '"> ' . esc_html($slug) . '</label><br>';
                                    }
                                } else {
                                    echo 'No plugins found in JSON.';
                                }
                            } else {
                                echo 'JSON file not found.';
                            }
                        ?>
                         </td>
                    </tr>
                    <tr>
                        <th><label>Instalaciones locales (ZIP):</label></th>
                        <td>
                            <?php
                                $zip_dir = WP_FAST_SETUP_PLUGIN_DIR . 'zip-files/';
                                if ( is_dir($zip_dir) ) {
                                    $zips = glob($zip_dir . '*.zip');
                                    if ( !empty($zips) ) {
                                        foreach ( $zips as $zip ) {
                                            $basename = basename($zip);
                                            $input_name = 'install_zip_' . sanitize_title($basename);
                                            echo '<label><input type="checkbox" name="' . esc_attr($input_name) . '"> ' . esc_html($basename) . '</label><br>';
                                        }
                                    } else {
                                        echo 'No local plugins found.';
                                    }
                                } else {
                                    echo 'No local plugins found.';
                                }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Crear paginas card 
            <div class="card w-70">  
                <div class="wrap">
                    <h1>Crear Páginas</h1>
                    <form method="post">
                        <label for="pages_input">Ingrese una página por línea. Si la línea inicia con un espacio, se creará como subpágina de la línea anterior.</label><br>
                        <textarea name="pages_input" id="pages_input" rows="10" cols="50" style="width:100%;"></textarea><br>
                        <?php /* submit_button('Crear Páginas'); */?>
                    </form>
                </div>
            </div>
            -->
        </div>


        <?php submit_button('Guardar Cambios'); ?>
    </form>

  <!-- Separado: Formulario para Crear Páginas -->
  <div class="card w-70" style="margin-top:20px;">
        <h2>Crear Páginas Personalizadas</h2>
        <form method="post" action="">
            <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>
            <p>Seleccione un preset para precargar las páginas o ingrese las páginas manualmente.</p>
            <select id="preset_pages_select">
                <option value="">Seleccione un preset</option>
                <option value="base">Base (Inicio, Servicios, Contacto)</option>
                <option value="completo">Completo (Inicio, Nosotros, Servicios, Portfolio, Blog, Contacto)</option>
                <option value="especial">Especial (Home, About Us, Products, FAQ, Support, Contact)</option>
            </select>
            <br><br>
            <p>Ingrese una página por línea. Si la línea inicia con un espacio, se creará como subpágina de la línea anterior.</p>
            <textarea name="pages_input" id="pages_input" rows="10" cols="50" style="width:100%;"></textarea><br>
            
            <!-- Radio for page template selection -->
            <p>Selecciona la plantilla de página:</p>
            <label>
                <input type="radio" name="page_template" value="elementor_header_footer" checked>
                Elementor Full Width
            </label>
            <label style="margin-left:20px;">
                <input type="radio" name="page_template" value="default">
                Default
            </label>
            <br><br>
            
            <input type="submit" name="create_pages" class="button button-primary" value="Crear Páginas">
            <input type="submit" name="delete_and_create_pages" class="button button-secondary" value="Borrar Todas las Páginas y Crear Nuevas">
        </form>
    </div>
</div>

<script>
document.getElementById('preset_pages_select').addEventListener('change', function(){
    var preset = this.value;
    var textarea = document.getElementById('pages_input');
    var presetText = '';
    if(preset === 'base'){
        presetText = "Inicio\nServicios\nContacto";
    } else if (preset === 'completo'){
        presetText = "Inicio\nNosotros\nServicios\nPortfolio\nBlog\nContacto";
    } else if (preset === 'especial'){
        presetText = "Home\nAbout Us\nProducts\nFAQ\nSupport\nContact";
    }
    textarea.value = presetText;
});
</script>