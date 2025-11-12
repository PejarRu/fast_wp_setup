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
        width: 100%;
        box-sizing: border-box;
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

    /* Fixed Progress Bar */
    .wpf-fixed-progress {
        display: none;
        margin-top: 20px;
        padding: 20px;
        background: var(--wpfs-white);
        border-radius: 8px;
        box-shadow: var(--wpfs-shadow);
        border-left: 4px solid var(--wpfs-primary);
    }

    .wpf-fixed-progress.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .wpf-fixed-progress-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .wpf-fixed-progress-icon {
        font-size: 24px;
        margin-right: 15px;
    }

    .wpf-fixed-progress-title {
        margin: 0;
        font-size: 1.2em;
        font-weight: 600;
        color: var(--wpfs-text);
    }

    .wpf-fixed-progress-message {
        margin: 0 0 15px 0;
        color: var(--wpfs-text-light);
        font-size: 0.9em;
    }

    .wpf-fixed-progress-bar {
        width: 100%;
        height: 8px;
        background: var(--wpfs-light);
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .wpf-fixed-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--wpfs-primary), var(--wpfs-accent));
        width: 0%;
        transition: width 0.3s ease;
        border-radius: 4px;
    }

    .wpf-fixed-progress-status {
        font-size: 0.85em;
        color: var(--wpfs-text-light);
        text-align: center;
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

    .wpf-plugin-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .wpf-plugin-search {
        flex: 1;
        min-width: 240px;
    }

    .wpf-plugin-search input[type="search"] {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--wpfs-border);
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .wpf-plugin-search input[type="search"]:focus {
        border-color: var(--wpfs-primary);
        box-shadow: 0 0 0 3px rgba(34, 113, 177, 0.1);
        outline: none;
    }

    .wpf-plugin-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .wpf-plugin-actions .wpf-btn {
        padding: 10px 16px;
    }

    .wpf-favorite-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        color: #d97706;
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

        <!-- Fixed Progress Bar -->
        <div id="wpf-fixed-progress" class="wpf-fixed-progress">
            <div class="wpf-fixed-progress-header">
                <div class="wpf-fixed-progress-icon">📦</div>
                <h3 class="wpf-fixed-progress-title">Instalando Plugins...</h3>
            </div>
            <p class="wpf-fixed-progress-message">Por favor espera mientras se instalan los plugins seleccionados.</p>
            <div class="wpf-fixed-progress-bar">
                <div id="wpf-fixed-progress-fill" class="wpf-fixed-progress-fill"></div>
            </div>
            <div class="wpf-fixed-progress-status">Preparando instalación...</div>
        </div>
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

            <form id="site-settings-form" method="POST" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_site'); ?>

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
                    <button type="submit" name="save_site_settings" class="wpf-btn wpf-btn-primary" title="Guardar todos los cambios de configuración del sitio (título, URL, idioma, etc.)">
                        💾 Guardar Configuración
                    </button>
                </div>
            </form>
        </div>

        <div class="wpf-card">
            <div class="wpf-card-header">
                <span class="wpf-card-icon">🔗</span>
                <h2 class="wpf-card-title">Configuración de Google Drive</h2>
            </div>
            <p class="wpf-card-description">Configura tu API Key y Folder ID de Google Drive para acceder a plugins adicionales</p>

            <form id="google-drive-form" method="POST" action="">
                <div class="wpf-form-group">
                    <label for="wpfs_google_drive_api_key">API Key de Google Drive</label>
                    <input type="password" id="wpfs_google_drive_api_key" name="google_drive_api_key" autocomplete="new-password" value="<?php echo esc_attr(get_option('wp_fast_setup_google_drive_api_key', WP_FAST_SETUP_DEFAULT_API_KEY)); ?>" style="width: 100%; max-width: 600px;">
                </div>
                <div class="wpf-form-group">
                    <label for="wpfs_google_drive_folder_id">ID de la Carpeta de Google Drive</label>
                    <input type="password" id="wpfs_google_drive_folder_id" name="google_drive_folder_id" autocomplete="new-password" value="<?php echo esc_attr(get_option('wp_fast_setup_google_drive_folder_id', WP_FAST_SETUP_DEFAULT_FOLDER_ID)); ?>" style="width: 100%; max-width: 600px;">
                </div>
                <small class="wpf-form-help">Por defecto usa la configuración predefinida. Puedes personalizarla con tu propia API Key y Folder ID si lo deseas.</small>

                <div class="wpf-button-group">
                    <button type="submit" class="wpf-btn wpf-btn-secondary" title="Guardar la configuración de API Key y Folder ID de Google Drive">
                        💾 Guardar Configuración de Google Drive
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

            <form id="features-form" method="POST" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_features'); ?>

                <div class="wpf-checkbox-group">
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="feature_permalinks" class="wpf-feature-checkbox" name="features[]" value="set_permalinks">
                        <label for="feature_permalinks">🔗 Permalinks amigables</label>
                    </div>
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="feature_hello_elementor" class="wpf-feature-checkbox" name="features[]" value="hello_elementor">
                        <label for="feature_hello_elementor">🎨 Activar tema Hello Elementor</label>
                    </div>
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="feature_disable_comments" class="wpf-feature-checkbox" name="features[]" value="disable_comments">
                        <label for="feature_disable_comments">🚫 Desactivar comentarios globalmente</label>
                    </div>
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="feature_create_admin" class="wpf-feature-checkbox" name="features[]" value="create_admin">
                        <label for="feature_create_admin">👤 Crear usuario administrador auxiliar</label>
                    </div>
                </div>

                <div class="wpf-button-group">
                    <button type="submit" class="wpf-btn wpf-btn-success" title="Aplicar las características avanzadas seleccionadas (permalinks, tema, comentarios, usuario admin, etc.)">
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

            <form id="plugin-install-form" method="POST" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_plugins'); ?>

                <div class="wpf-plugin-toolbar">
                    <div class="wpf-plugin-search">
                        <label for="plugin-search" class="screen-reader-text">Buscar plugins</label>
                        <input type="search" id="plugin-search" placeholder="Buscar plugins..." aria-label="Buscar plugins">
                    </div>
                    <div class="wpf-plugin-actions">
                        <button type="button" class="wpf-btn wpf-btn-secondary" id="select-all-plugins">Seleccionar todo</button>
                        <button type="button" class="wpf-btn wpf-btn-secondary" id="deselect-all-plugins">Deseleccionar</button>
                        <button type="button" class="wpf-btn wpf-btn-secondary" id="clear-all-plugins">Limpiar filtros</button>
                        <button type="button" class="wpf-btn wpf-btn-secondary" id="sort-asc">Ordenar A-Z</button>
                        <button type="button" class="wpf-btn wpf-btn-secondary" id="sort-desc">Ordenar Z-A</button>
                    </div>
                </div>

                <?php
                $json_file = WP_FAST_SETUP_PLUGIN_DIR . 'includes/plugins-list.json';
                $plugin_entries = array();
                $favorites_repo = $favorites_local = $favorites_drive = array();
                $data = array();

                if (file_exists($json_file)) {
                    $json_data = file_get_contents($json_file);
                    $data = json_decode($json_data, true);

                    if (!empty($data['favoritos']) && is_array($data['favoritos'])) {
                        foreach ($data['favoritos'] as $fav) {
                            $source = isset($fav['source']) ? $fav['source'] : 'repo';
                            $slug = isset($fav['slug']) ? $fav['slug'] : '';
                            if (!$slug) {
                                continue;
                            }
                            switch ($source) {
                                case 'repo':
                                    $favorites_repo[] = $slug;
                                    break;
                                case 'local':
                                    $favorites_local[] = $slug;
                                    break;
                                case 'drive':
                                    $favorites_drive[] = $slug;
                                    break;
                            }
                        }
                    }

                    if (!empty($data['plugins']) && is_array($data['plugins'])) {
                        foreach ($data['plugins'] as $slug => $post_key) {
                            $label = ucwords(str_replace(array('-', '_'), ' ', $slug));
                            $is_favorite = in_array($slug, $favorites_repo, true);
                            $plugin_entries[] = array(
                                'id' => 'repo_' . sanitize_title($slug),
                                'name' => 'plugins[]',
                                'value' => $slug,
                                'label' => $label,
                                'icon' => $is_favorite ? '⭐' : '📚',
                                'source' => 'repo',
                                'effective_type' => 'repo',
                                'slug' => $slug,
                                'zip' => '',
                                'drive_id' => '',
                                'is_favorite' => $is_favorite,
                            );
                        }
                    }
                }

                if (!empty($local_zip_files)) {
                    $local_labels = isset($data['locales']) && is_array($data['locales']) ? $data['locales'] : array();
                    foreach ($local_zip_files as $zip_file) {
                        $friendly_name = isset($local_labels[$zip_file]) ? $local_labels[$zip_file] : pathinfo($zip_file, PATHINFO_FILENAME);
                        $slug = pathinfo($zip_file, PATHINFO_FILENAME);
                        $is_favorite = in_array($zip_file, $favorites_local, true) || in_array($friendly_name, $favorites_local, true);
                        $plugin_entries[] = array(
                            'id' => 'local_' . sanitize_title($zip_file),
                            'name' => 'local_zips[]',
                            'value' => $zip_file,
                            'label' => $friendly_name,
                            'icon' => $is_favorite ? '⭐' : '💾',
                            'source' => 'local',
                            'effective_type' => 'local',
                            'slug' => $slug,
                            'zip' => $zip_file,
                            'drive_id' => '',
                            'is_favorite' => $is_favorite,
                        );
                    }
                }

                if (!empty($drive_zip_files)) {
                    foreach ($drive_zip_files as $file) {
                        $drive_id = isset($file['id']) ? $file['id'] : '';
                        $name = isset($file['name']) ? $file['name'] : $drive_id;
                        if (!$drive_id) {
                            continue;
                        }
                        $is_favorite = in_array($drive_id, $favorites_drive, true) || in_array($name, $favorites_drive, true);
                        $plugin_entries[] = array(
                            'id' => 'drive_' . sanitize_title($drive_id),
                            'name' => 'drive_files[' . $drive_id . ']',
                            'value' => $drive_id,
                            'label' => $name,
                            'icon' => $is_favorite ? '⭐' : '☁️',
                            'source' => 'drive',
                            'effective_type' => 'drive',
                            'slug' => $name,
                            'zip' => '',
                            'drive_id' => $drive_id,
                            'is_favorite' => $is_favorite,
                        );
                    }
                }

                if (!empty($plugin_entries)) {
                    usort($plugin_entries, function ($a, $b) {
                        if (!empty($a['is_favorite']) && empty($b['is_favorite'])) {
                            return -1;
                        }
                        if (empty($a['is_favorite']) && !empty($b['is_favorite'])) {
                            return 1;
                        }
                        return strcasecmp($a['label'], $b['label']);
                    });
                }

                echo '<div class="wpf-plugin-list">';
                foreach ($plugin_entries as $entry) {
                    $data_attributes = '';
                    $attributes_map = array(
                        'data-plugin-source' => $entry['source'],
                        'data-plugin-effective-type' => $entry['effective_type'],
                        'data-plugin-label' => $entry['label'],
                        'data-plugin-slug' => $entry['slug'],
                        'data-plugin-zip' => $entry['zip'],
                        'data-plugin-drive-id' => $entry['drive_id'],
                    );

                    if (!empty($entry['is_favorite'])) {
                        $attributes_map['data-plugin-favorite'] = '1';
                    }

                    foreach ($attributes_map as $attr_key => $attr_value) {
                        if ($attr_value !== '') {
                            $data_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr($attr_value));
                        }
                    }

                    echo '<div class="wpf-plugin-item">';
                    printf(
                        '<input type="checkbox" class="wpf-plugin-checkbox" id="%1$s" name="%2$s" value="%3$s"%4$s>',
                        esc_attr($entry['id']),
                        esc_attr($entry['name']),
                        esc_attr($entry['value']),
                        $data_attributes
                    );
                    printf(
                        '<label for="%1$s">%2$s %3$s</label>',
                        esc_attr($entry['id']),
                        esc_html($entry['icon']),
                        esc_html($entry['label'])
                    );

                    if (empty($entry['is_favorite'])) {
                        echo '<button type="button" class="add-fav-btn" data-slug="' . esc_attr($entry['slug'] ?: $entry['value']) . '" data-source="' . esc_attr($entry['source']) . '">Añadir a Favoritos</button>';
                    } else {
                        echo '<span class="wpf-favorite-badge">⭐ Favorito</span>';
                    }
                    echo '</div>';
                }
                echo '</div>';
                ?>

                <div class="wpf-button-group">
                    <button type="submit" name="install_plugins" class="wpf-btn wpf-btn-primary" title="Instalar todos los plugins seleccionados desde el repositorio de WordPress, archivos locales o Google Drive">
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

            <form id="content-form" method="post" action="">
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
                    <button type="submit" name="create_pages" class="wpf-btn wpf-btn-primary" title="Crear páginas nuevas con la plantilla seleccionada sin afectar las existentes" onclick="setPageAction('create')">
                        📄 Crear Páginas
                    </button>
                    <button type="submit" name="delete_and_create_pages" class="wpf-btn wpf-btn-warning" title="Eliminar todas las páginas existentes y crear nuevas con la plantilla seleccionada" onclick="setPageAction('delete')">
                        🗑️ Borrar y Crear Nuevas
                    </button>
                    <button type="submit" name="create_pages_and_menu" class="wpf-btn wpf-btn-success" title="Crear páginas nuevas y agregarlas automáticamente al menú de navegación" onclick="setPageAction('create_menu')">
                        📄➕ Crear con Menú
                    </button>
                    <button type="submit" name="delete_and_create_pages_with_menu" class="wpf-btn wpf-btn-warning" title="Eliminar páginas existentes, crear nuevas y agregarlas al menú de navegación" onclick="setPageAction('delete_menu')">
                        🗑️➕ Borrar y Crear con Menú
                    </button>
                </div>

                <!-- Hidden fields for page actions -->
                <input type="hidden" name="page_action" id="page_action" value="">
                <input type="hidden" name="delete_existing" id="delete_existing" value="0">
                <input type="hidden" name="create_menu" id="create_menu" value="0">
            </form>
        </div>

        <!-- Descargas de configuración JSON -->
        <div class="wpf-card">
            <div class="wpf-card-header">
                <span class="wpf-card-icon">⬇️</span>
                <h2 class="wpf-card-title">Descargas de Configuración (JSON)</h2>
            </div>
            <p class="wpf-card-description">Haz clic en los enlaces para descargar los archivos JSON de configuración.</p>

            <div class="wpf-form-group">
                <a href="<?php echo esc_url(WP_FAST_SETUP_PLUGIN_URL . 'data/admin-site-enhancements-ase-settings-2025-10-14-0818.json'); ?>" download style="color:var(--wpfs-primary); text-decoration:underline; font-weight:600; margin-right:18px;">Descargar configuración ASE (JSON)</a>

                <a href="<?php echo esc_url(WP_FAST_SETUP_PLUGIN_URL . 'data/wp-rocket-settings-fast-2025-02-07-67a5ca0c3a004.json'); ?>" download style="color:var(--wpfs-primary); text-decoration:underline; font-weight:600;">Descargar configuración WP Rocket (JSON)</a>
            </div>
        </div>

        <!-- Menu Creation Section -->
        <div class="wpf-card">
            <div class="wpf-card-header">
                <span class="wpf-card-icon">🍽️</span>
                <h2 class="wpf-card-title">Crear Menús de Navegación</h2>
            </div>
            <p class="wpf-card-description">Crea menús vacíos para organizar la navegación de tu sitio</p>

            <form id="menu-form" method="post" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>

                <div class="wpf-form-group">
                    <label for="menus_input">Menús a Crear</label>
                    <textarea name="menus_input" id="menus_input" placeholder="Ingrese un menú por línea."></textarea>
                    <small style="color: var(--wpfs-text-light);">Ingrese un menú por línea. Cada línea será un menú separado.</small>
                </div>

                <div class="wpf-button-group">
                    <button type="submit" name="create_menus" class="wpf-btn wpf-btn-primary" title="Crear menús vacíos">
                        🍽️ Crear Menús
                    </button>
                </div>
            </form>
        </div>

        <!-- Homepage Settings Section -->
        <div class="wpf-card">
            <div class="wpf-card-header">
                <span class="wpf-card-icon">🏠</span>
                <h2 class="wpf-card-title">Configurar Página Principal y Blogs</h2>
            </div>
            <p class="wpf-card-description">Establece qué páginas usar como página principal y página de blogs</p>

            <form id="homepage-form" method="post" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>

                <div class="wpf-form-group">
                    <label for="homepage_page">Página Principal</label>
                    <select name="homepage_page" id="homepage_page">
                        <option value="">-- Seleccionar Página --</option>
                        <?php
                        $pages = get_pages();
                        $current_homepage = get_option('page_on_front');
                        foreach ($pages as $page) {
                            $selected = ($page->ID == $current_homepage) ? 'selected' : '';
                            echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
                        }
                        ?>
                    </select>
                    <small style="color: var(--wpfs-text-light);">Selecciona la página que quieres usar como página principal de tu sitio</small>
                </div>

                <div class="wpf-form-group">
                    <label for="blog_page">Página de Blogs</label>
                    <select name="blog_page" id="blog_page">
                        <option value="">-- Seleccionar Página --</option>
                        <?php
                        $current_blogpage = get_option('page_for_posts');
                        foreach ($pages as $page) {
                            $selected = ($page->ID == $current_blogpage) ? 'selected' : '';
                            echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
                        }
                        ?>
                    </select>
                    <small style="color: var(--wpfs-text-light);">Selecciona la página donde se mostrarán tus entradas de blog</small>
                </div>

                <div class="wpf-button-group">
                    <button type="submit" name="set_homepage" class="wpf-btn wpf-btn-primary" title="Establecer las páginas seleccionadas como página principal y página de blogs">
                        🏠 Establecer Páginas
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
                        <button type="submit" name="create_header" class="wpf-btn wpf-btn-primary" title="Crear un header profesional con Elementor usando templates predefinidos">
                            🎨 Crear Header
                        </button>
                    </form>

                    <form method="post" action="" style="display: inline;">
                        <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce'); ?>
                        <button type="submit" name="create_footer" class="wpf-btn wpf-btn-primary" title="Crear un footer profesional con Elementor usando templates predefinidos">
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
                                onclick="return confirm('¿Estás seguro de que quieres eliminar permanentemente el plugin WP Fast Setup? Esta acción no se puede deshacer.');"
                                title="Eliminar completamente WP Fast Setup y todos sus archivos (acción irreversible)">
                                🗑️ Eliminar Permanentemente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php settings_errors('wp_fast_setup_messages'); ?>