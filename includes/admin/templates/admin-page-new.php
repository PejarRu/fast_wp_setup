<?php defined('ABSPATH') || exit; ?>

<style>
    /* Modern WP Fast Setup Styles */
    :root {
        --wpfs-primary: #2271b1;
        --wpfs-secondary: #135e96;
        --wpfs-accent: #00a32a;
        --wpfs-warning: #d63638;
        --wpfs-light: #f6f7f7;
        --wpfs-border: #c3c4c7;
        --wpfs-text: #1d2327;
        --wpfs-text-light: #646970;
        --wpfs-white: #ffffff;
        --wpfs-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        --wpfs-shadow-hover: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .wpf-setup-wrapper {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Header */
    .wpf-header {
        background: linear-gradient(135deg, var(--wpfs-primary), var(--wpfs-secondary));
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: var(--wpfs-shadow);
        text-align: center;
    }

    .wpf-header h1 {
        margin: 0 0 10px 0;
        font-size: 2.5em;
        font-weight: 700;
    }

    .wpf-header p {
        margin: 0;
        font-size: 1.1em;
        opacity: 0.9;
    }

    /* Navigation Tabs */
    .wpf-tabs {
        display: flex;
        background: var(--wpfs-white);
        border-radius: 12px 12px 0 0;
        box-shadow: var(--wpfs-shadow);
        margin-bottom: 0;
        overflow: hidden;
    }

    .wpf-tab {
        flex: 1;
        padding: 20px 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
        position: relative;
    }

    .wpf-tab:hover {
        background: var(--wpfs-light);
    }

    .wpf-tab.active {
        background: var(--wpfs-primary);
        color: white;
        border-bottom-color: var(--wpfs-accent);
    }

    .wpf-tab-icon {
        display: block;
        font-size: 24px;
        margin-bottom: 8px;
    }

    .wpf-tab-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
    }

    /* Tab Content */
    .wpf-tab-content {
        background: var(--wpfs-white);
        border-radius: 0 0 12px 12px;
        box-shadow: var(--wpfs-shadow);
        padding: 30px;
        display: none;
    }

    .wpf-tab-content.active {
        display: block;
    }

    /* Cards */
    .wpf-card {
        background: var(--wpfs-white);
        border: 1px solid var(--wpfs-border);
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: var(--wpfs-shadow);
        transition: all 0.3s ease;
    }

    .wpf-card:hover {
        box-shadow: var(--wpfs-shadow-hover);
        transform: translateY(-2px);
    }

    .wpf-card-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--wpfs-light);
    }

    .wpf-card-icon {
        font-size: 28px;
        color: var(--wpfs-primary);
        margin-right: 15px;
        background: var(--wpfs-light);
        padding: 10px;
        border-radius: 8px;
    }

    .wpf-card-title {
        margin: 0;
        font-size: 1.4em;
        font-weight: 600;
        color: var(--wpfs-text);
    }

    .wpf-card-description {
        margin: 0 0 20px 0;
        color: var(--wpfs-text-light);
        font-size: 14px;
    }

    /* Form Elements */
    .wpf-form-group {
        margin-bottom: 20px;
    }

    .wpf-form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--wpfs-text);
    }

    .wpf-form-group input[type="text"],
    .wpf-form-group input[type="url"],
    .wpf-form-group select,
    .wpf-form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--wpfs-border);
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .wpf-form-group input[type="text"]:focus,
    .wpf-form-group input[type="url"]:focus,
    .wpf-form-group select:focus,
    .wpf-form-group textarea:focus {
        border-color: var(--wpfs-primary);
        box-shadow: 0 0 0 3px rgba(34, 113, 177, 0.1);
        outline: none;
    }

    .wpf-form-group textarea {
        min-height: 100px;
        resize: vertical;
    }

    /* Checkboxes */
    .wpf-checkbox-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .wpf-checkbox-item {
        display: flex;
        align-items: center;
        padding: 15px;
        background: var(--wpfs-light);
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .wpf-checkbox-item:hover {
        background: #e9ecef;
    }

    .wpf-checkbox-item input[type="checkbox"] {
        margin-right: 12px;
        transform: scale(1.2);
    }

    .wpf-checkbox-item label {
        margin: 0;
        font-weight: 500;
        cursor: pointer;
        flex: 1;
    }

    /* Buttons */
    .wpf-button-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 25px;
    }

    .wpf-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .wpf-btn-primary {
        background: var(--wpfs-primary);
        color: white;
    }

    .wpf-btn-primary:hover {
        background: var(--wpfs-secondary);
        transform: translateY(-1px);
        box-shadow: var(--wpfs-shadow);
    }

    .wpf-btn-secondary {
        background: var(--wpfs-light);
        color: var(--wpfs-text);
        border: 2px solid var(--wpfs-border);
    }

    .wpf-btn-secondary:hover {
        background: var(--wpfs-white);
        border-color: var(--wpfs-primary);
    }

    .wpf-btn-success {
        background: var(--wpfs-accent);
        color: white;
    }

    .wpf-btn-success:hover {
        background: #028a1e;
    }

    .wpf-btn-warning {
        background: var(--wpfs-warning);
        color: white;
    }

    .wpf-btn-warning:hover {
        background: #b32d2e;
    }

    /* Progress Modal */
    .wpf-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 10000;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .wpf-modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: var(--wpfs-white);
        padding: 40px;
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .wpf-modal-icon {
        font-size: 48px;
        margin-bottom: 20px;
    }

    .wpf-modal-title {
        margin: 0 0 15px 0;
        font-size: 1.5em;
        font-weight: 600;
    }

    .wpf-modal-message {
        margin: 0 0 25px 0;
        color: var(--wpfs-text-light);
    }

    .wpf-progress-bar {
        width: 100%;
        height: 8px;
        background: var(--wpfs-light);
        border-radius: 4px;
        overflow: hidden;
        margin: 20px 0;
    }

    .wpf-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--wpfs-primary), var(--wpfs-accent));
        width: 0%;
        transition: width 0.3s ease;
        border-radius: 4px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .wpf-setup-wrapper {
            padding: 10px;
        }

        .wpf-header {
            padding: 20px;
        }

        .wpf-header h1 {
            font-size: 2em;
        }

        .wpf-tabs {
            flex-direction: column;
        }

        .wpf-tab {
            padding: 15px;
        }

        .wpf-tab-content {
            padding: 20px;
        }

        .wpf-checkbox-group {
            grid-template-columns: 1fr;
        }

        .wpf-button-group {
            flex-direction: column;
        }

        .wpf-btn {
            justify-content: center;
        }
    }

    /* Animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .wpf-card {
        animation: slideIn 0.5s ease;
    }

    /* Status Messages */
    .wpf-notice {
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .wpf-notice-success {
        background: #d1f2eb;
        border-left: 4px solid var(--wpfs-accent);
        color: #0a5d3a;
    }

    .wpf-notice-error {
        background: #f8d7da;
        border-left: 4px solid var(--wpfs-warning);
        color: #721c24;
    }

    .wpf-notice-warning {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        color: #856404;
    }

    .wpf-notice-icon {
        font-size: 20px;
    }

    /* Plugin List */
    .wpf-plugin-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .wpf-plugin-item {
        display: flex;
        align-items: center;
        padding: 15px;
        background: var(--wpfs-light);
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .wpf-plugin-item:hover {
        background: #e9ecef;
    }

    .wpf-plugin-item input[type="checkbox"] {
        margin-right: 12px;
        transform: scale(1.2);
    }

    .wpf-plugin-item label {
        margin: 0;
        font-weight: 500;
        cursor: pointer;
        flex: 1;
    }

    /* Drive Settings */
    .wpf-drive-settings {
        background: #f0f8ff;
        border: 1px solid #b3d9ff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .wpf-drive-settings h4 {
        margin: 0 0 15px 0;
        color: var(--wpfs-primary);
        font-size: 1.1em;
    }

    .wpf-drive-settings small {
        display: block;
        color: var(--wpfs-text-light);
        margin-top: 8px;
        font-size: 12px;
    }

    /* Page Creator */
    .wpf-page-creator {
        background: var(--wpfs-light);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .wpf-page-creator h4 {
        margin: 0 0 15px 0;
        color: var(--wpfs-text);
    }

    .wpf-template-options {
        display: flex;
        gap: 20px;
        margin: 15px 0;
        flex-wrap: wrap;
    }

    .wpf-template-options label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .wpf-template-options input[type="radio"] {
        margin: 0;
    }
</style>

<div class="wpf-setup-wrapper">
    <!-- Header -->
    <div class="wpf-header">
        <h1>🚀 WP Fast Setup</h1>
        <p>Configura tu sitio WordPress en minutos con herramientas profesionales</p>
    </div>

    <!-- Navigation Tabs -->
    <div class="wpf-tabs">
        <div class="wpf-tab active" data-tab="site">
            <span class="wpf-tab-icon">⚙️</span>
            <h3 class="wpf-tab-title">Configuración del Sitio</h3>
        </div>
        <div class="wpf-tab" data-tab="plugins">
            <span class="wpf-tab-icon">📦</span>
            <h3 class="wpf-tab-title">Plugins</h3>
        </div>
        <div class="wpf-tab" data-tab="content">
            <span class="wpf-tab-icon">📄</span>
            <h3 class="wpf-tab-title">Contenido</h3>
        </div>
        <div class="wpf-tab" data-tab="templates">
            <span class="wpf-tab-icon">🎨</span>
            <h3 class="wpf-tab-title">Templates</h3>
        </div>
    </div>

    <!-- Tab Content: Site Configuration -->
    <div id="site" class="wpf-tab-content active">
        <div class="wpf-card">
            <div class="wpf-card-header">
                <span class="wpf-card-icon">🌐</span>
                <h2 class="wpf-card-title">Configuración Básica del Sitio</h2>
            </div>
            <p class="wpf-card-description">Configura los ajustes principales de tu sitio WordPress</p>

            <form method="POST" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>

                <div class="wpf-form-group">
                    <label for="site_name">Nombre del Sitio</label>
                    <input type="text" id="site_name" name="nombre_sitio" value="<?php echo esc_attr($current_site_name); ?>" placeholder="Mi Sitio Web">
                </div>

                <div class="wpf-form-group">
                    <label for="site_language">Idioma del Sitio</label>
                    <select id="site_language" name="idioma_sitio">
                        <option value="es_ES" <?php selected($current_language, 'es_ES'); ?>>Español</option>
                        <option value="en_US" <?php selected($current_language, 'en_US'); ?>>English</option>
                        <option value="fr_FR" <?php selected($current_language, 'fr_FR'); ?>>Français</option>
                        <option value="de_DE" <?php selected($current_language, 'de_DE'); ?>>Deutsch</option>
                    </select>
                </div>

                <div class="wpf-form-group">
                    <label for="site_url">URL del Sitio</label>
                    <input type="url" id="site_url" name="url_sitio" value="<?php echo esc_url($current_url); ?>" placeholder="https://misitio.com">
                </div>

                <div class="wpf-button-group">
                    <button type="submit" name="save_site_settings" class="wpf-btn wpf-btn-primary">
                        💾 Guardar Configuración
                    </button>
                </div>
            </form>
        </div>

        <div class="wpf-card">
            <div class="wpf-card-header">
                <span class="wpf-card-icon">🔧</span>
                <h2 class="wpf-card-title">Características Avanzadas</h2>
            </div>
            <p class="wpf-card-description">Activa o desactiva características avanzadas de WordPress</p>

            <form method="POST" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>

                <div class="wpf-checkbox-group">
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="permalinks" name="activar_permalinks">
                        <label for="permalinks">🔗 Permalinks Amigables</label>
                    </div>
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="hello_elementor" name="activar_hello_elementor">
                        <label for="hello_elementor">🎨 Tema Hello Elementor</label>
                    </div>
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="disable_comments" name="desactivar_comentarios">
                        <label for="disable_comments">🚫 Desactivar Comentarios</label>
                    </div>
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="create_admin" name="activar_usuario">
                        <label for="create_admin">👤 Crear Usuario Admin</label>
                    </div>
                </div>

                <div class="wpf-button-group">
                    <button type="submit" name="save_features" class="wpf-btn wpf-btn-success">
                        ⚡ Aplicar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab Content: Plugins -->
    <div id="plugins" class="wpf-tab-content">
        <div class="wpf-card">
            <div class="wpf-card-header">
                <span class="wpf-card-icon">📦</span>
                <h2 class="wpf-card-title">Instalación de Plugins</h2>
            </div>
            <p class="wpf-card-description">Instala plugins desde el repositorio oficial de WordPress o desde archivos ZIP</p>

            <form method="POST" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>

                <div class="wpf-drive-settings">
                    <h4>🔗 Configuración de Google Drive</h4>
                    <div class="wpf-form-group">
                        <label for="google_drive_api_key">API Key de Google Drive</label>
                        <input type="text" id="google_drive_api_key" name="google_drive_api_key" value="<?php echo esc_attr(get_option('wp_fast_setup_google_drive_api_key', WP_FAST_SETUP_DEFAULT_API_KEY)); ?>">
                    </div>
                    <div class="wpf-form-group">
                        <label for="google_drive_folder_id">ID de la Carpeta de Google Drive</label>
                        <input type="text" id="google_drive_folder_id" name="google_drive_folder_id" value="<?php echo esc_attr(get_option('wp_fast_setup_google_drive_folder_id', WP_FAST_SETUP_DEFAULT_FOLDER_ID)); ?>">
                    </div>
                    <small>Por defecto usa la configuración predefinida. Puedes personalizarla con tu propia API Key y Folder ID si lo deseas.</small>
                </div>

                <?php
                // Leer lista plugin del JSON
                $json_file = WP_FAST_SETUP_PLUGIN_DIR . 'includes/plugins-list.json';
                if (file_exists($json_file)) {
                    $json_data = file_get_contents($json_file);
                    $data = json_decode($json_data, true);
                    if (isset($data['plugins']) && is_array($data['plugins'])) {
                        echo '<h4>📚 Plugins del Repositorio</h4>';
                        echo '<div class="wpf-plugin-list">';
                        foreach ($data['plugins'] as $slug => $post_key) {
                            echo '<div class="wpf-plugin-item">';
                            echo '<input type="checkbox" id="' . esc_attr($post_key) . '" name="' . esc_attr($post_key) . '">';
                            echo '<label for="' . esc_attr($post_key) . '">' . esc_html($slug) . '</label>';
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                }
                ?>

                <?php if (!empty($drive_zip_files)): ?>
                    <h4>☁️ Plugins desde Google Drive</h4>
                    <div class="wpf-plugin-list">
                        <?php foreach ($drive_zip_files as $file): ?>
                            <div class="wpf-plugin-item">
                                <input type="checkbox" id="drive_<?php echo sanitize_title($file['name']); ?>" name="install_drive_zip_<?php echo sanitize_title($file['name']); ?>" value="<?php echo esc_attr($file['id']); ?>">
                                <label for="drive_<?php echo sanitize_title($file['name']); ?>"><?php echo esc_html($file['name']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (!empty($local_zip_files)): ?>
                    <div class="wpf-notice wpf-notice-warning">
                        <span class="wpf-notice-icon">⚠️</span>
                        <div>
                            <strong>Google Drive no disponible</strong>
                            <p>Usando respaldo local de archivos ZIP</p>
                        </div>
                    </div>
                    <h4>💾 Plugins Locales (Respaldo)</h4>
                    <div class="wpf-plugin-list">
                        <?php foreach ($local_zip_files as $zip): ?>
                            <div class="wpf-plugin-item">
                                <input type="checkbox" id="local_<?php echo sanitize_title($zip); ?>" name="install_local_zip_<?php echo sanitize_title($zip); ?>">
                                <label for="local_<?php echo sanitize_title($zip); ?>"><?php echo esc_html($zip); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="wpf-button-group">
                    <button type="submit" name="install_plugins" class="wpf-btn wpf-btn-primary">
                        📦 Instalar Plugins Seleccionados
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab Content: Content -->
    <div id="content" class="wpf-tab-content">
        <div class="wpf-card">
            <div class="wpf-card-header">
                <span class="wpf-card-icon">📄</span>
                <h2 class="wpf-card-title">Crear Páginas Personalizadas</h2>
            </div>
            <p class="wpf-card-description">Crea páginas automáticamente con diferentes plantillas y estructuras</p>

            <form method="post" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>

                <div class="wpf-page-creator">
                    <h4>🎯 Presets de Páginas</h4>
                    <select id="preset_pages_select" class="wpf-form-group">
                        <option value="">Seleccione un preset</option>
                        <option value="base">Base (Inicio, Servicios, Contacto)</option>
                        <option value="completo">Completo (Inicio, Nosotros, Servicios, Portfolio, Blog, Contacto)</option>
                        <option value="especial">Especial (Home, About Us, Products, FAQ, Support, Contact)</option>
                    </select>
                </div>

                <div class="wpf-form-group">
                    <label for="pages_input">Páginas a Crear</label>
                    <textarea name="pages_input" id="pages_input" placeholder="Ingrese una página por línea. Si la línea inicia con un espacio, se creará como subpágina de la línea anterior."></textarea>
                    <small style="color: var(--wpfs-text-light);">Ingrese una página por línea. Si la línea inicia con un espacio, se creará como subpágina de la línea anterior.</small>
                </div>

                <div class="wpf-template-options">
                    <label>
                        <input type="radio" name="page_template" value="elementor_header_footer" checked>
                        🎨 Elementor Full Width
                    </label>
                    <label>
                        <input type="radio" name="page_template" value="default">
                        📄 Default
                    </label>
                </div>

                <div class="wpf-button-group">
                    <button type="submit" name="create_pages" class="wpf-btn wpf-btn-primary">
                        📄 Crear Páginas
                    </button>
                    <button type="submit" name="delete_and_create_pages" class="wpf-btn wpf-btn-warning">
                        🗑️ Borrar y Crear Nuevas
                    </button>
                    <button type="submit" name="create_pages_and_menu" class="wpf-btn wpf-btn-success">
                        📄➕ Crear con Menú
                    </button>
                    <button type="submit" name="delete_and_create_pages_with_menu" class="wpf-btn wpf-btn-warning">
                        🗑️➕ Borrar y Crear con Menú
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab Content: Templates -->
    <div id="templates" class="wpf-tab-content">
        <div class="wpf-card">
            <div class="wpf-card-header">
                <span class="wpf-card-icon">🎨</span>
                <h2 class="wpf-card-title">Templates de Elementor</h2>
            </div>
            <p class="wpf-card-description">Crea headers, footers y páginas usando templates predefinidos de Elementor</p>

            <div class="wpf-button-group">
                <form method="post" action="" style="display: inline;">
                    <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>
                    <button type="submit" name="create_header" class="wpf-btn wpf-btn-primary">
                        🎨 Crear Header
                    </button>
                </form>

                <form method="post" action="" style="display: inline;">
                    <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>
                    <button type="submit" name="create_footer" class="wpf-btn wpf-btn-primary">
                        🎨 Crear Footer
                    </button>
                </form>
            </div>

            <div class="wpf-card" style="margin-top: 30px; border-left: 4px solid var(--wpfs-warning); background: #fefefe;">
                <div class="wpf-card-header">
                    <span class="wpf-card-icon">⚠️</span>
                    <h2 class="wpf-card-title">Eliminar Plugin</h2>
                </div>
                <p class="wpf-card-description">Esta acción eliminará permanentemente el plugin WP Fast Setup de tu instalación de WordPress.</p>

                <form method="post" action="">
                    <?php wp_nonce_field('wp_fast_setup_delete_action', 'wp_fast_setup_delete_nonce'); ?>
                    <div class="wpf-button-group">
                        <button type="submit" name="wp_fast_setup_delete_plugin" class="wpf-btn wpf-btn-warning"
                            onclick="return confirm('¿Estás seguro de que quieres eliminar permanentemente el plugin WP Fast Setup? Esta acción no se puede deshacer.');">
                            🗑️ Eliminar Permanentemente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Progress Modal -->
    <div id="wpf-progress-modal" class="wpf-modal">
        <div class="wpf-modal-content">
            <div class="wpf-modal-icon">📦</div>
            <h3 class="wpf-modal-title">Instalando Plugins...</h3>
            <p class="wpf-modal-message">Por favor espera mientras se instalan los plugins seleccionados.</p>
            <div class="wpf-progress-bar">
                <div id="wpf-progress-fill" class="wpf-progress-fill"></div>
            </div>
            <button id="wpf-close-modal" class="wpf-btn wpf-btn-secondary">Cerrar</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching
        const tabs = document.querySelectorAll('.wpf-tab');
        const tabContents = document.querySelectorAll('.wpf-tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');

                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(tc => tc.classList.remove('active'));

                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });

        // Preset pages selector
        document.getElementById('preset_pages_select').addEventListener('change', function() {
            const preset = this.value;
            const textarea = document.getElementById('pages_input');
            let presetText = '';

            switch (preset) {
                case 'base':
                    presetText = "Inicio\nServicios\nContacto";
                    break;
                case 'completo':
                    presetText = "Inicio\nNosotros\nServicios\nPortfolio\nBlog\nContacto";
                    break;
                case 'especial':
                    presetText = "Home\nAbout Us\nProducts\nFAQ\nSupport\nContact";
                    break;
            }

            textarea.value = presetText;
        });

        // AJAX form submission for plugins
        const pluginForm = document.querySelector('#plugins form');
        const modal = document.getElementById('wpf-progress-modal');
        const progressFill = document.getElementById('wpf-progress-fill');
        const closeModal = document.getElementById('wpf-close-modal');

        if (pluginForm) {
            pluginForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Show modal
                modal.style.display = 'block';
                progressFill.style.width = '0%';

                // Collect form data
                const formData = new FormData(pluginForm);
                formData.append('action', 'wp_fast_setup_install_plugins');
                formData.append('nonce', '<?php echo wp_create_nonce('wp_fast_setup_ajax'); ?>');

                // Send AJAX request
                fetch(ajaxurl, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            progressFill.style.width = '100%';
                            setTimeout(() => {
                                modal.style.display = 'none';
                                location.reload();
                            }, 2000);
                        } else {
                            alert('Error: ' + data.message);
                            modal.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        alert('Error de conexión');
                        modal.style.display = 'none';
                    });
            });
        }

        // Close modal
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
            location.reload();
        });
    });
</script>

<?php settings_errors('wp_fast_setup_messages'); ?>