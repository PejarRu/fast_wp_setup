<?php defined('ABSPATH') || exit; ?>

<style>

    .wp-fast-setup-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .column-left {
        flex: 0 0 30%;
        display: flex;
        flex-direction: column;
    }
    
    .column-right {
        flex: 0 0 65%;
        display: flex;
        flex-direction: column;
    }
    
    @media screen and (max-width: 782px) {
        .column-left, .column-right {
            flex: 0 0 100%;
        }
    }
    
    .compact-card {
        background-color: #fff;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        padding: 15px;
        margin-bottom: 15px;
        max-width: 100%;
    }   
    
    .site-settings-card {
        border-left: 4px solid #2271b1;
        background-color: #fff;
    }
    
    .features-card {
        border-left: 4px solid #2271b1;
        background-color: #fff;
    }
    
    .header-card {
        border-left: 4px solid #2271b1;
        background-color: #fff;
    }
    
    .footer-card {
        border-left: 4px solid #2271b1;
        background-color: #fff;
    }
    
    .standard-card {
        background: #fff;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        padding: 14px;
        margin-bottom: 15px;
    }
    
    .compact-card h2 {
        margin-top: 0;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
        font-size: 16px;
    }
    
    .compact-card .form-table {
        margin-top: 0;
    }
    
    .compact-card .form-table th {
        width: 70px;
        padding: 5px 5px 5px 0;
    }
    
    .compact-card .form-table td {
        padding: 5px 5px;
    }
    
    .compact-card input[type="text"],
    .compact-card input[type="url"] {
        width: 100%;
    }
    
    .compact-card label {
        display: block;
        margin-bottom: 5px;
    }
    
    .wp-fast-setup-manager-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .card {
        background: #fff;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        padding: 15px;
        margin-bottom: 0;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
    }
    
    .w-70 {
        width: 100%;
    }
    
    .form-table th {
        width: 150px;
        padding: 10px 10px 10px 0;
    }
    
    .form-table td {
        padding: 10px 10px;
    }
    
    input[type="text"], 
    input[type="url"], 
    select, 
    textarea {
        width: 100%;
        max-width: 400px;
    }
    
    textarea#pages_input {
        min-height: 120px; 
    }
    
    .button-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
        margin-bottom: 0;
    }
    
    .button-container .button {
        margin: 0 !important;
    }
    
    
    h2 {
        margin-top: 0;
        padding-bottom: 10px;
        margin-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    p {
        margin-top: 0;
        margin-bottom: 8px;
    }
    
    br {
        display: none;
    }
    
    
    .form-table {
        margin-top: 0;
    }
    
    @media screen and (min-width: 783px) {
        .card.w-70 {
            width: calc(50% - 8px);
            margin-bottom: 15px;
        }
        
        .full-width {
            width: 100% !important;
        }
    }
    
    @media screen and (min-width: 1200px) {
        .card.w-70 {
            width: calc(33.33% - 10px);
            min-height: 220px;
        }
        
        .full-width {
            width: 100% !important;
        }
        
        .card.w-70.pages-card {
            min-height: 400px;
            order: 4;
        }
        
        .header-card {
            order: 1;
        }
        
        .footer-card {
            order: 2;
        }
        
        .uninstall-card {
            order: 3;
        }
    }
    
    .template-options {
        display: flex;
        align-items: center;
        margin: 5px 0;
    }
    
    .template-options label {
        margin-right: 15px;
    }
    
    .wrap p.submit {
        margin-top: 0;
        padding-top: 0;
    }
    
    select {
        margin-bottom: 5px;
    }
    
    .card form {
        display: flex;
        flex-direction: column;
        flex: 1;
        justify-content: space-between;
    }

    /* Fix for the page creator card to fill available height */
    .column-right .standard-card:nth-child(2) {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .column-right .standard-card:nth-child(2) form {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    /* Make textarea grow to fill available space */
    .column-right .standard-card:nth-child(2) textarea {
        flex-grow: 1;
        min-height: 240px;
    }
    
    /* Fix for the delete plugin container */
    .delete-plugin-card {
        border-left: 4px solid #dc3545 !important;
        background-color: #f8d7da !important;
        color: #721c24 !important;
        order: 3; /* Ensure it appears after footer (which has order: 2) */
    }
</style>

<div class="wrap">
    <h1>WP Fast Setup</h1>
    
    <?php settings_errors('wp_fast_setup_messages'); ?>

    <div class="wp-fast-setup-layout">
        <div class="column-left">
            <div class="compact-card site-settings-card">
                <h2>Ajustes del Sitio</h2>
                <form method="POST" action="">
                    <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th><label for="nombre_sitio">Titulo:</label></th>
                            <td>
                                <input type="text" id="nombre_sitio" name="nombre_sitio" 
                                    value="<?php echo esc_attr($current_site_name); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th><label for="idioma_sitio">Idioma:</label></th>
                            <td>
                                <input type="text" id="idioma_sitio" name="idioma_sitio" 
                                    value="<?php echo esc_attr($current_language); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th><label for="url_sitio">URL:</label></th>
                            <td>
                                <input type="url" id="url_sitio" name="url_sitio" 
                                    value="<?php echo esc_url($current_url); ?>">
                            </td>
                        </tr>
                    </table>
                    <p>
                        <input type="submit" name="save_site_settings" class="button button-primary" value="Guardar">
                    </p>
                </form>
            </div>

            <div class="compact-card features-card">
                <h2>Características</h2>
                <form method="POST" action="">
                    <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>
                    <label>
                        <input type="checkbox" name="activar_permalinks">
                        Activar Permalinks
                    </label>
                    <label>
                        <input type="checkbox" name="activar_hello_elementor">
                        Activar Hello Elementor
                    </label>
                    <label>
                        <input type="checkbox" name="desactivar_comentarios">
                        Desactivar comentarios
                    </label>
                    <label>
                        <input type="checkbox" name="activar_usuario">
                        Crear admin
                    </label>
                    <p>
                        <input type="submit" name="save_features" class="button button-primary" value="Aplicar">
                    </p>
                </form>
            </div>

            <!-- Header Creator -->
            <div class="compact-card header-card">
                <h2>Crear Header</h2>
                <form method="post" action="">
                    <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>
                    <p>Crea el header basado en header.json</p>
                    <p>
                        <input type="submit" name="create_header" class="button button-primary" value="Crear Header">
                    </p>
                </form>
            </div>

            <!-- Footer Creator -->
            <div class="compact-card footer-card">
                <h2>Crear Footer</h2>
                <form method="post" action="">
                    <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>
                    <p>Crea el footer basado en footer.json</p>
                    <p>
                        <input type="submit" name="create_footer" class="button button-primary" value="Crear Footer">
                    </p>
                </form>
            </div>

            <!-- Delete Plugin Section - fixed width issue -->
            <div class="compact-card delete-plugin-card">
                <h2>Eliminación del Plugin</h2>
                <form method="post" action="">
                    <?php wp_nonce_field('wp_fast_setup_delete_action', 'wp_fast_setup_delete_nonce'); ?>
                    <p>Esta acción eliminará permanentemente el plugin WP Fast Setup de tu instalación de WordPress.</p>
                    <p>
                        <input type="submit" name="wp_fast_setup_delete_plugin" class="button button-primary" value="Eliminar Permanentemente" 
                        style="background-color: #dc3545; border-color: #dc3545; color: white;"
                        onclick="return confirm('¿Estás seguro de que quieres eliminar permanentemente el plugin WP Fast Setup? Esta acción no se puede deshacer.');" />
                    </p>
                </form>
            </div>
        </div>

        <!-- Right Column - Larger cards with fixed height -->
        <div class="column-right">
            <!-- Plugins Section -->
            <div class="standard-card">
                <h2>Plugins</h2>
                <form method="POST" action="">
                    <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th><label>Instalación rápida de plugins:</label></th>
                            <td>
                            <?php
                                // Leer lista plugin del JSON
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
                    <?php submit_button('Guardar Cambios'); ?>
                </form>
            </div>
            
            <!-- Crear paginas card - with height fix -->
            <div class="standard-card">
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
                    <p>Ingrese una página por línea. Si la línea inicia con un espacio, se creará como subpágina de la línea anterior.</p>
                    <textarea name="pages_input" id="pages_input" rows="10"></textarea>
                    
                    <!-- Radio for page template selection -->
                    <div class="template-options">
                        <p>Selecciona la plantilla de página:</p>
                        <label>
                            <input type="radio" name="page_template" value="elementor_header_footer" checked>
                            Elementor Full Width
                        </label>
                        <label>
                            <input type="radio" name="page_template" value="default">
                            Default
                        </label>
                    </div>
                    
                    <div class="button-container">
                        <input type="submit" name="create_pages" class="button button-primary" value="Crear Páginas">
                        <input type="submit" name="delete_and_create_pages" class="button button-secondary" value="Borrar Todas las Páginas y Crear Nuevas">
                        <input type="submit" name="create_pages_and_menu" class="button button-primary" value="Crear Páginas y Menú">
                        <input type="submit" name="delete_and_create_pages_with_menu" class="button button-secondary" value="Borrar Todas las Páginas y Crear Nuevas con Menú">
                    </div>
                </form>
            </div>
        </div>
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
