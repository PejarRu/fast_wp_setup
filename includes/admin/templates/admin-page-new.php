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

    /* Custom Checkboxes */
    .wpf-checkbox-label {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-weight: 500;
        color: var(--wpfs-text);
        margin-bottom: 10px;
    }

    .wpf-checkbox-label input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .wpf-checkbox-checkmark {
        display: inline-block;
        width: 18px;
        height: 18px;
        background: var(--wpfs-white);
        border: 2px solid var(--wpfs-border);
        border-radius: 3px;
        margin-right: 10px;
        position: relative;
        transition: all 0.2s ease;
    }

    .wpf-checkbox-label input[type="checkbox"]:checked~.wpf-checkbox-checkmark::after {
        content: '✓';
        position: absolute;
        top: -2px;
        left: 1px;
        color: var(--wpfs-primary);
        font-weight: bold;
        font-size: 14px;
    }

    .wpf-checkbox-label:hover .wpf-checkbox-checkmark {
        border-color: var(--wpfs-primary);
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

    /* Plugin Filters */
    .wpf-plugin-filters {
        margin-bottom: 20px;
        padding: 15px;
        background: var(--wpfs-light);
        border-radius: 8px;
        border: 1px solid var(--wpfs-border);
    }

    .wpf-filter-group {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .wpf-filter-input,
    .wpf-filter-select {
        padding: 8px 12px;
        border: 1px solid var(--wpfs-border);
        border-radius: 4px;
        font-size: 14px;
        min-width: 150px;
    }

    .wpf-filter-input:focus,
    .wpf-filter-select:focus {
        border-color: var(--wpfs-primary);
        outline: none;
    }

    /* Plugin List */
    .wpf-plugin-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
        max-height: 400px;
        overflow-y: auto;
        padding: 15px;
        background: var(--wpfs-light);
        border-radius: 8px;
        border: 1px solid var(--wpfs-border);
    }

    .wpf-plugin-item {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        background: var(--wpfs-white);
        border-radius: 6px;
        border: 1px solid var(--wpfs-border);
        transition: all 0.2s ease;
        position: relative;
    }

    .wpf-plugin-item:hover {
        border-color: var(--wpfs-primary);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .wpf-plugin-item input[type="checkbox"] {
        margin-right: 10px;
        transform: scale(1.2);
    }

    .wpf-plugin-item label {
        flex: 1;
        cursor: pointer;
        font-weight: 500;
        color: var(--wpfs-text);
    }

    .fav-star-btn {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        padding: 5px;
        border-radius: 3px;
        transition: all 0.2s ease;
        color: #ddd;
        margin-left: 10px;
    }

    .fav-star-btn:hover {
        background: var(--wpfs-light);
        transform: scale(1.1);
    }

    .fav-star-btn.favorite {
        color: #ffd700;
    }

    .fav-star-btn.favorite:hover {
        color: #ffed4e;
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

    /* Media Management */
    .wpf-media-status {
        background: var(--wpfs-light);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        display: flex;
        justify-content: center;
    }

    .wpf-media-stats {
        display: flex;
        gap: 40px;
    }

    .wpf-stat-item {
        text-align: center;
    }

    .wpf-stat-number {
        display: block;
        font-size: 2em;
        font-weight: 700;
        color: var(--wpfs-primary);
        margin-bottom: 5px;
    }

    .wpf-stat-label {
        font-size: 0.9em;
        color: var(--wpfs-text-light);
        font-weight: 500;
    }

    .wpf-media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .wpf-media-item {
        background: var(--wpfs-white);
        border: 2px solid var(--wpfs-border);
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .wpf-media-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--wpfs-shadow);
    }

    .wpf-media-item.imported {
        border-color: var(--wpfs-accent);
        background: #f0f9f0;
    }

    .wpf-media-item.available {
        border-color: var(--wpfs-warning);
        background: #fff8e1;
    }

    .wpf-media-preview {
        height: 150px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--wpfs-light);
    }

    .wpf-media-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .wpf-media-info {
        padding: 15px;
    }

    .wpf-media-info h4 {
        margin: 0 0 8px 0;
        font-size: 0.9em;
        font-weight: 600;
        color: var(--wpfs-text);
        word-break: break-all;
    }

    .wpf-media-status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75em;
        font-weight: 600;
    }

    .wpf-media-status-badge.imported {
        background: var(--wpfs-accent);
        color: white;
    }

    .wpf-media-status-badge.available {
        background: var(--wpfs-warning);
        color: white;
    }

    .wpf-media-info-box {
        background: var(--wpfs-light);
        border-radius: 8px;
        padding: 20px;
        margin-top: 25px;
        border-left: 4px solid var(--wpfs-primary);
    }

    .wpf-media-info-box h4 {
        margin: 0 0 10px 0;
        color: var(--wpfs-text);
        font-size: 1em;
    }

    .wpf-media-info-box code {
        background: var(--wpfs-white);
        padding: 2px 6px;
        border-radius: 3px;
        font-family: monospace;
        font-size: 0.9em;
    }

    /* Responsive adjustments for media grid */
    @media (max-width: 768px) {
        .wpf-media-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }

        .wpf-media-stats {
            flex-direction: column;
            gap: 20px;
        }
    }
</style>

<div class="wpf-setup-wrapper">
    <!-- Header -->
    <div class="wpf-header">
        <h1>🚀 WP Fast Setup</h1>
        <p>Configura tu sitio WordPress en minutos con herramientas profesionales</p>

        <!-- Debug Tools -->
        <div style="text-align: center; margin: 15px 0;">
            <a href="?page=wp-fast-setup&test_drive=1" class="wpf-btn wpf-btn-secondary" style="font-size: 0.9em; padding: 8px 16px;">
                🔧 Probar Conexión Google Drive
            </a>
        </div>

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

    <!-- Debug Section -->
    <?php if (isset($_GET['test_drive']) && $_GET['test_drive'] == '1'): ?>
        <div class="wpf-card" style="margin-bottom: 30px; border: 2px solid #007cba;">
            <div class="wpf-card-header">
                <span class="wpf-card-icon">🔧</span>
                <h2 class="wpf-card-title">Herramientas de Debug</h2>
            </div>
            <?php $this->test_drive_connection(); ?>
        </div>
    <?php endif; ?>

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
        <div class="wpf-tab" data-tab="media">
            <span class="wpf-tab-icon">🖼️</span>
            <h3 class="wpf-tab-title">Medios</h3>
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

            <form id="features-form" method="POST" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_site'); ?>
                <!-- Nonce input for JS (per-form) -->
                <input type="hidden" id="wp_fast_setup_nonce_features" value="<?php echo esc_attr(wp_create_nonce('wp_fast_setup_action')); ?>">

                <div class="wpf-form-group">
                    <label for="site_name">Nombre del Sitio</label>
                    <input type="text" id="site_name" name="nombre_sitio" value="<?php echo esc_attr($current_site_name); ?>" placeholder="Mi Sitio Web">
                </div>

                <div class="wpf-form-group">
                    <label for="admin_email">Correo del Administrador</label>
                    <input type="email" id="admin_email" name="admin_email" value="<?php echo esc_attr($current_admin_email); ?>" placeholder="admin@misitio.com">
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

                <div class="wpf-form-group">
                    <label class="wpf-checkbox-label">
                        <input type="checkbox" id="disable_comments" name="disable_comments" value="1" <?php checked($current_comment_status, 'closed'); ?>>
                        <span class="wpf-checkbox-checkmark"></span>
                        Desactivar comentarios en todo el sitio
                    </label>
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
                    <input type="password" id="wpfs_google_drive_api_key" name="google_drive_api_key" autocomplete="new-password" value="<?php echo esc_attr(WP_FAST_SETUP_DEFAULT_API_KEY); ?>" style="width: 100%; max-width: 600px;">
                </div>
                <div class="wpf-form-group">
                    <label for="wpfs_google_drive_folder_id">ID de la Carpeta de Google Drive</label>
                    <input type="password" id="wpfs_google_drive_folder_id" name="google_drive_folder_id" autocomplete="new-password" value="<?php echo esc_attr(WP_FAST_SETUP_DEFAULT_FOLDER_ID); ?>" style="width: 100%; max-width: 600px;">
                </div>
                <small class="wpf-form-help">Los campos están pre-llenados con la configuración de .env. Puedes modificarlos si es necesario.</small>

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

            <form method="POST" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_features'); ?>
                <!-- Nonce input for JS (features form) -->
                <input type="hidden" id="wp_fast_setup_nonce_features" value="<?php echo esc_attr(wp_create_nonce('wp_fast_setup_action')); ?>">

                <div class="wpf-checkbox-group">
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="permalinks " name="activar_permalinks">
                        <label for="permalinks">🔗 Permalinks Amigables</label>
                    </div>

                <div class="wpf-form-group">
                    <label class="wpf-checkbox-label">
                        <input type="checkbox" id="set_permalinks" name="set_permalinks" value="1" <?php checked($current_permalink_structure, '/index.php/%year%/%monthnum%/%day%/%postname%/'); ?>>
                        <span class="wpf-checkbox-checkmark"></span>
                        Configurar permalinks amigables (/año/mes/dia/nombre/)
                    </label>
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
                    <button type="submit" name="save_features" class="wpf-btn wpf-btn-success" title="Aplicar las características avanzadas seleccionadas (SEO, comentarios, usuario admin, etc.)">
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

            <!-- Plugin Filters -->
            <div class="wpf-plugin-filters">
                <div class="wpf-filter-group">
                    <input type="text" id="plugin-search" placeholder="🔍 Buscar plugin..." class="wpf-filter-input">
                    <select id="plugin-sort" class="wpf-filter-select">
                        <option value="name-asc">📝 Nombre A-Z</option>
                        <option value="name-desc">📝 Nombre Z-A</option>
                        <option value="type">🏷️ Por Tipo</option>
                    </select>
                    <select id="plugin-letter" class="wpf-filter-select">
                        <option value="">Todas las letras</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                        <option value="G">G</option>
                        <option value="H">H</option>
                        <option value="I">I</option>
                        <option value="J">J</option>
                        <option value="K">K</option>
                        <option value="L">L</option>
                        <option value="M">M</option>
                        <option value="N">N</option>
                        <option value="O">O</option>
                        <option value="P">P</option>
                        <option value="Q">Q</option>
                        <option value="R">R</option>
                        <option value="S">S</option>
                        <option value="T">T</option>
                        <option value="U">U</option>
                        <option value="V">V</option>
                        <option value="W">W</option>
                        <option value="X">X</option>
                        <option value="Y">Y</option>
                        <option value="Z">Z</option>
                    </select>
                    <a href="<?php echo esc_url(WP_FAST_SETUP_PLUGIN_URL . 'includes/plugins-list.json'); ?>" download="plugins-list.json" class="wpf-btn wpf-btn-secondary" style="font-size: 0.9em; padding: 8px 12px;">
                        📥 Descargar JSON
                    </a>
                </div>
            </div>

            <form method="POST" action="">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_plugins'); ?>
                <!-- Nonce input for JS (plugin/menu forms) -->
                <input type="hidden" id="wp_fast_setup_nonce_plugins" value="<?php echo esc_attr(wp_create_nonce('wp_fast_setup_action')); ?>">

                <?php
                // Leer lista plugin del JSON
                $json_file = WP_FAST_SETUP_PLUGIN_DIR . 'includes/plugins-list.json';
                $all_plugins = [];
                if (file_exists($json_file)) {
                    $json_data = file_get_contents($json_file);
                    $data = json_decode($json_data, true);

                    // Favoritos primero
                    if (isset($data['favoritos']) && is_array($data['favoritos'])) {
                        foreach ($data['favoritos'] as $fav) {
                            $slug = $fav['slug'];
                            $source = $fav['source'];
                            $icon = '⭐';
                            $all_plugins[] = ['slug' => $slug, 'type' => 'favorito', 'icon' => $icon, 'source' => $source];
                        }
                    }

                    // Plugins del repositorio
                    if (isset($data['plugins']) && is_array($data['plugins'])) {
                        foreach ($data['plugins'] as $slug => $post_key) {
                            $all_plugins[] = ['slug' => $slug, 'type' => 'repo', 'icon' => '📚', 'post_key' => $post_key, 'source' => 'repo'];
                        }
                    }

                    // Plugins locales - obtenidos dinámicamente de la carpeta
                    if (!empty($local_zip_files)) {
                        foreach ($local_zip_files as $zip_file) {
                            $friendly_name = isset($data['locales'][$zip_file]) ? $data['locales'][$zip_file] : pathinfo($zip_file, PATHINFO_FILENAME);
                            $all_plugins[] = ['slug' => $zip_file, 'type' => 'local', 'icon' => '💾', 'zip' => $zip_file, 'plugin_slug' => '', 'source' => 'local', 'label' => $friendly_name];
                        }
                    }
                }

                // Plugins de Drive
                if (!empty($drive_zip_files)) {
                    foreach ($drive_zip_files as $file) {
                        $all_plugins[] = ['slug' => $file['name'], 'type' => 'drive', 'icon' => '☁️', 'id' => $file['id'], 'source' => 'drive'];
                    }
                } elseif (!empty($local_zip_files)) {
                    // Si no hay Drive, usar locales como respaldo, pero ya están incluidos arriba
                }

                // Mostrar lista unificada
                echo '<div class="wpf-plugin-list">';
                foreach ($all_plugins as $plugin) {
                    $id = '';
                    $name = '';
                    $value = '';
                    if ($plugin['type'] === 'repo') {
                        $id = $plugin['post_key'];
                        $name = $plugin['post_key'];
                        $label = $plugin['slug'];
                    } elseif ($plugin['type'] === 'local') {
                        $id = 'local_' . sanitize_title($plugin['zip']);
                        $name = 'install_local_zip_' . sanitize_title($plugin['zip']);
                        $label = isset($plugin['label']) ? $plugin['label'] : $plugin['zip'];
                    } elseif ($plugin['type'] === 'drive') {
                        $id = 'drive_' . sanitize_title($plugin['slug']);
                        $name = 'install_drive_zip_' . sanitize_title($plugin['slug']);
                        $value = $plugin['id'];
                        $label = $plugin['slug'];
                    } elseif ($plugin['type'] === 'favorito') {
                        // Para favoritos, usar el name basado en source
                        if ($plugin['source'] === 'repo') {
                            $post_key = isset($data['plugins'][$plugin['slug']]) ? $data['plugins'][$plugin['slug']] : '';
                            $id = $post_key;
                            $name = $post_key;
                        } elseif ($plugin['source'] === 'local') {
                            $zip = array_search($plugin['slug'], $data['locales']);
                            if ($zip) {
                                $id = 'local_' . sanitize_title($zip);
                                $name = 'install_local_zip_' . sanitize_title($zip);
                            }
                        } elseif ($plugin['source'] === 'drive') {
                            // Para drive, no se puede instalar desde favoritos directamente, pero mostrar
                            $id = 'fav_' . sanitize_title($plugin['slug']);
                            $name = 'install_fav_' . sanitize_title($plugin['slug']);
                        }
                        $label = $plugin['slug'];
                    }

                    echo '<div class="wpf-plugin-item">';
                    echo '<input type="checkbox" id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '">';
                    echo '<label for="' . esc_attr($id) . '">' . $plugin['icon'] . ' ' . esc_html($label) . '</label>';

                    // Verificar si el plugin ya está en favoritos
                    $is_favorite = false;
                    if (isset($data['favoritos']) && is_array($data['favoritos'])) {
                        foreach ($data['favoritos'] as $fav) {
                            if ($fav['slug'] === $plugin['slug'] && $fav['source'] === $plugin['source']) {
                                $is_favorite = true;
                                break;
                            }
                        }
                    }

                    $star_class = $is_favorite ? 'favorite' : '';
                    echo '<button type="button" class="fav-star-btn ' . $star_class . '" data-slug="' . esc_attr($plugin['slug']) . '" data-source="' . esc_attr($plugin['source']) . '" title="' . ($is_favorite ? 'Quitar de favoritos' : 'Añadir a favoritos') . '">';
                    echo $is_favorite ? '⭐' : '☆';
                    echo '</button>';

                    echo '</div>';
                }
                echo '</div>';
                ?>

                <div class="wpf-button-group">
                    <button type="submit" name="install_plugins" class="wpf-btn wpf-btn-primary" title="Instalar todos los plugins seleccionados desde el repositorio de WordPress o archivos ZIP locales">
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
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_content'); ?>

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
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_menus'); ?>

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
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_homepage'); ?>

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
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_header'); ?>
                <button type="submit" name="create_header" class="wpf-btn wpf-btn-primary" title="Crear un header profesional con Elementor usando templates predefinidos">
                    🎨 Crear Header
                </button>
            </form>

            <form method="post" action="" style="display: inline;">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_footer'); ?>
                <button type="submit" name="create_footer" class="wpf-btn wpf-btn-primary" title="Crear un footer profesional con Elementor usando templates predefinidos">
                    🎨 Crear Footer
                </button>
            </form>
        </div>

        <div class="wpf-card wpf-card-warning" style="border-left: 4px solid var(--wpfs-warning); background: #fefefe;">
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

<!-- Tab Content: Media -->
<div id="media" class="wpf-tab-content">
    <div class="wpf-card">
        <div class="wpf-card-header">
            <span class="wpf-card-icon">🖼️</span>
            <h2 class="wpf-card-title">Importación de Imágenes</h2>
        </div>
        <p class="wpf-card-description">Importa automáticamente las imágenes de la carpeta assets/images/ a la galería de medios de WordPress</p>

        <?php
        $media_importer = WP_Fast_Setup_Media_Importer::get_instance();
        $available_images = $media_importer->get_available_images();
        $imported_media_ids = $media_importer->get_imported_media_ids();
        ?>

        <div class="wpf-media-status">
            <div class="wpf-media-stats">
                <div class="wpf-stat-item">
                    <span class="wpf-stat-number"><?php echo count($available_images); ?></span>
                    <span class="wpf-stat-label">Imágenes disponibles</span>
                </div>
                <div class="wpf-stat-item">
                    <span class="wpf-stat-number"><?php echo count($imported_media_ids); ?></span>
                    <span class="wpf-stat-label">Imágenes importadas</span>
                </div>
            </div>
        </div>

        <?php if (!empty($available_images)): ?>
            <div class="wpf-media-grid">
                <?php foreach ($available_images as $image): ?>
                    <div class="wpf-media-item <?php echo $image['exists_in_media'] ? 'imported' : 'available'; ?>">
                        <div class="wpf-media-preview">
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['filename']); ?>" loading="lazy">
                        </div>
                        <div class="wpf-media-info">
                            <h4><?php echo esc_html($image['filename']); ?></h4>
                            <span class="wpf-media-status-badge <?php echo $image['exists_in_media'] ? 'imported' : 'available'; ?>">
                                <?php echo $image['exists_in_media'] ? '✅ Importada' : '⏳ Pendiente'; ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="wpf-notice wpf-notice-warning">
                <div class="wpf-notice-icon">⚠️</div>
                <div>
                    <strong>No se encontraron imágenes</strong>
                    <p>Coloca archivos PNG, JPG, JPEG, GIF o WEBP en la carpeta <code>assets/images/</code> del plugin.</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="wpf-button-group">
            <button type="button" id="wpf-import-media-btn" class="wpf-btn wpf-btn-primary" <?php echo empty($available_images) ? 'disabled' : ''; ?>>
                🖼️ Importar Imágenes a Galería
            </button>

            <?php if (!empty($imported_media_ids)): ?>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display: inline;">
                    <input type="hidden" name="action" value="wp_fast_setup_delete_imported_media">
                    <?php wp_nonce_field('wp_fast_setup_delete_media', 'wp_fast_setup_delete_media_nonce'); ?>
                    <button type="submit" class="wpf-btn wpf-btn-warning"
                        onclick="return confirm('¿Estás seguro de que quieres eliminar todas las imágenes importadas de la galería de medios?');">
                        🗑️ Eliminar Imágenes Importadas
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <div class="wpf-media-info-box">
            <h4>📁 Ubicación de imágenes</h4>
            <p>Las imágenes se buscan automáticamente en: <code><?php echo esc_html(WP_FAST_SETUP_PLUGIN_DIR . 'assets/images/'); ?></code></p>
            <p><strong>Formatos soportados:</strong> PNG, JPG, JPEG, GIF, WEBP</p>
            <p><strong>Nota:</strong> Las imágenes ya existentes en la galería no se duplicarán.</p>
        </div>
    </div>
</div>


</div>

<script>
    // Localize ajaxurl for AJAX requests (use a namespaced global to avoid redeclaration errors)
    window.wpfs_ajaxurl = window.wpfs_ajaxurl || '<?php echo admin_url('admin-ajax.php'); ?>';

    (function() {
        // Helper: activate a tab by id (e.g., 'plugins')
        function activateTab(tabId, pushState = false) {
            if (!tabId) return;

            const tabs = document.querySelectorAll('.wpf-tab');
            const tabContents = document.querySelectorAll('.wpf-tab-content');

            tabs.forEach(t => t.classList.toggle('active', t.getAttribute('data-tab') === tabId));
            tabContents.forEach(tc => tc.classList.toggle('active', tc.id === tabId));

            // Update hash without scrolling if requested
            if (pushState) {
                try {
                    history.replaceState(null, '', '#' + tabId);
                } catch (e) {
                    // ignore
                }
            }
        }

        // On DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            // Delegated click handler for tabs (attach to document to be robust)
            document.addEventListener('click', function(e) {
                const tabEl = e.target.closest('.wpf-tab');
                if (!tabEl) return;
                const targetTab = tabEl.getAttribute('data-tab');
                if (!targetTab) return;
                // Only proceed if corresponding content exists
                const targetContent = document.getElementById(targetTab);
                if (!targetContent) return;
                activateTab(targetTab, true);
            });

            // Activate initial tab from hash if present
            const initialHash = (window.location.hash || '').replace('#', '');
            if (initialHash) {
                // Only activate if a matching tab/content exists
                if (document.getElementById(initialHash)) {
                    activateTab(initialHash, false);
                }
            }

            // Handle back/forward / manual hash change
            window.addEventListener('hashchange', function() {
                const hash = (window.location.hash || '').replace('#', '');
                if (hash && document.getElementById(hash)) {
                    activateTab(hash, false);
                }
            });

            // Preset pages selector
            const presetSelect = document.getElementById('preset_pages_select');
            if (presetSelect) {
                presetSelect.addEventListener('change', function() {
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

                    if (textarea) textarea.value = presetText;
                });
            }

            // Features form (advanced settings)
            const featuresForm = document.querySelector('#site form[action=""]');
            if (featuresForm && featuresForm.querySelector('[name="save_features"]')) {
                featuresForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(featuresForm);
                    formData.append('action', 'wp_fast_setup_activate_features');
                    formData.append('nonce', '<?php echo wp_create_nonce('wp_fast_setup_action'); ?>');

                    const submitBtn = featuresForm.querySelector('[name="save_features"]');
                    const originalText = submitBtn.textContent;
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Procesando...';

                    fetch(window.wpfs_ajaxurl, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                submitBtn.textContent = '✅ Aplicado';
                                setTimeout(() => {
                                    submitBtn.disabled = false;
                                    submitBtn.textContent = originalText;
                                }, 2000);
                            } else {
                                alert('❌ Error: ' + (data.data?.message || data.message || 'Error desconocido'));
                                submitBtn.disabled = false;
                                submitBtn.textContent = originalText;
                            }
                        })
                        .catch(error => {
                            console.error('WP Fast Setup: Features activation error:', error);
                            alert('❌ Error de conexión: ' + error.message);
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        });
                });
            }

            // --- Keep existing AJAX handlers but guard element selection ---

            // Plugins form + progress
            const pluginForm = document.querySelector('#plugins form');
            const fixedProgress = document.getElementById('wpf-fixed-progress');
            const fixedProgressFill = document.getElementById('wpf-fixed-progress-fill');
            const fixedProgressStatus = document.querySelector('.wpf-fixed-progress-status');

            if (pluginForm) {
                pluginForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (fixedProgress) fixedProgress.classList.add('show');
                    if (fixedProgressFill) fixedProgressFill.style.width = '0%';
                    if (fixedProgressStatus) fixedProgressStatus.textContent = 'Preparando instalación...';

                    if (fixedProgress) {
                        fixedProgress.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }

                    const formData = new FormData(pluginForm);
                    formData.append('action', 'wp_fast_setup_install_plugins');
                    formData.append('nonce', '<?php echo wp_create_nonce('wp_fast_setup_action'); ?>');

                    fetch(window.wpfs_ajaxurl, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (fixedProgressFill) fixedProgressFill.style.width = '100%';
                                if (fixedProgressStatus) fixedProgressStatus.textContent = 'Instalación completada exitosamente';
                                setTimeout(() => {
                                    if (fixedProgress) fixedProgress.classList.remove('show');
                                    location.reload();
                                }, 1500);
                            } else {
                                if (fixedProgressStatus) fixedProgressStatus.textContent = 'Error: ' + (data.message || '');
                                setTimeout(() => {
                                    if (fixedProgress) fixedProgress.classList.remove('show');
                                }, 3000);
                            }
                        })
                        .catch(error => {
                            console.error('WP Fast Setup: AJAX error:', error);
                            if (fixedProgressStatus) fixedProgressStatus.textContent = 'Error de conexión';
                            setTimeout(() => {
                                if (fixedProgress) fixedProgress.classList.remove('show');
                            }, 3000);
                        });
                });
            }

            // Site settings
            const siteSettingsForm = document.querySelector('#site form');
            if (siteSettingsForm) {
                siteSettingsForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(siteSettingsForm);
                    formData.append('action', 'wp_fast_setup_save_site_settings');
                    formData.append('nonce', '<?php echo wp_create_nonce('wp_fast_setup_action'); ?>');

                    fetch(window.wpfs_ajaxurl, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('✅ Configuración del sitio guardada correctamente');
                                location.reload();
                            } else {
                                alert('❌ Error: ' + (data.message || ''));
                            }
                        })
                        .catch(error => {
                            console.error('WP Fast Setup: Site settings AJAX error:', error);
                            alert('❌ Error de conexión al guardar configuración');
                        });
                });
            }

            // Google Drive settings
            const googleDriveForm = document.querySelector('#google-drive-form');
            if (googleDriveForm) {
                googleDriveForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(googleDriveForm);
                    formData.append('action', 'wp_fast_setup_save_google_drive');
                    formData.append('nonce', '<?php echo wp_create_nonce('wp_fast_setup_action'); ?>');

                    fetch(window.wpfs_ajaxurl, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('✅ Configuración de Google Drive guardada correctamente');
                                location.reload();
                            } else {
                                alert('❌ Error: ' + (data.message || ''));
                            }
                        })
                        .catch(error => {
                            console.error('WP Fast Setup: Google Drive AJAX error:', error);
                            alert('❌ Error de conexión al guardar configuración de Google Drive');
                        });
                });
            }

            // Content/pages creation
            const contentForm = document.getElementById('content-form');
            if (contentForm) {
                contentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData();
                    formData.append('action', 'wp_fast_setup_create_pages');
                    formData.append('nonce', '<?php echo wp_create_nonce('wp_fast_setup_action'); ?>');
                    const pagesInputEl = this.querySelector('[name="pages_input"]');
                    const pageTemplateEl = this.querySelector('[name="page_template"]:checked');
                    const deleteExistingEl = this.querySelector('[name="delete_existing"]');
                    const createMenuEl = this.querySelector('[name="create_menu"]');

                    if (pagesInputEl) formData.append('pages_input', pagesInputEl.value);
                    if (pageTemplateEl) formData.append('page_template', pageTemplateEl.value);
                    if (deleteExistingEl) formData.append('delete_existing', deleteExistingEl.value);
                    if (createMenuEl) formData.append('create_menu', createMenuEl.value);

                    // Find focused submit button or fallback
                    let submitBtn = this.querySelector('button[type="submit"]:focus');
                    if (!submitBtn) submitBtn = this.querySelector('button[type="submit"]');
                    let originalText = '';
                    if (submitBtn) {
                        originalText = submitBtn.textContent;
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Procesando...';
                    }

                    fetch(window.wpfs_ajaxurl, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (submitBtn) {
                                if (data.success) submitBtn.textContent = '✅ Completado';
                                else alert('❌ Error: ' + (data.data || data.message || 'Error desconocido'));
                            }
                            if (submitBtn) setTimeout(() => {
                                submitBtn.disabled = false;
                                submitBtn.textContent = originalText;
                            }, 2000);
                        })
                        .catch(error => {
                            console.error('WP Fast Setup: Fetch error:', error);
                            alert('❌ Error de conexión: ' + (error.message || ''));
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.textContent = originalText;
                            }
                        });
                });
            }

            // Add to favorites (delegated)
            document.addEventListener('click', function(e) {
                const favBtn = e.target.closest('.add-fav-btn');
                if (favBtn) {
                    const slug = favBtn.getAttribute('data-slug');
                    const source = favBtn.getAttribute('data-source');
                    const formData = new FormData();
                    formData.append('action', 'wp_fast_setup_add_favorite');
                    formData.append('slug', slug);
                    formData.append('source', source);
                    formData.append('nonce', '<?php echo wp_create_nonce('wp_fast_setup_action'); ?>');

                    fetch(window.wpfs_ajaxurl, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Plugin añadido a favoritos');
                                location.reload();
                            } else {
                                alert('Error: ' + (data.data || data.message || ''));
                            }
                        })
                        .catch(error => {
                            alert('Error al añadir a favoritos');
                        });
                }
            });
        });

        // Function to set page creation action
        window.setPageAction = function(action) {
            console.log('WP Fast Setup: Setting page action:', action);

            const deleteExistingField = document.getElementById('delete_existing');
            const createMenuField = document.getElementById('create_menu');
            const pageActionField = document.getElementById('page_action');

            if (deleteExistingField && createMenuField && pageActionField) {
                pageActionField.value = action;

                switch (action) {
                    case 'create':
                        deleteExistingField.value = '0';
                        createMenuField.value = '0';
                        break;
                    case 'delete':
                        deleteExistingField.value = '1';
                        createMenuField.value = '0';
                        break;
                    case 'create_menu':
                        deleteExistingField.value = '0';
                        createMenuField.value = '1';
                        break;
                    case 'delete_menu':
                        deleteExistingField.value = '1';
                        createMenuField.value = '1';
                        break;
                }

                console.log('WP Fast Setup: Action set - delete_existing:', deleteExistingField.value, 'create_menu:', createMenuField.value);
            } else {
                console.error('WP Fast Setup: Required hidden fields not found');
            }
        };

        // Media import functionality
        const importMediaBtn = document.getElementById('wpf-import-media-btn');
        if (importMediaBtn) {
            importMediaBtn.addEventListener('click', function() {
                if (this.disabled) return;

                const originalText = this.textContent;
                this.disabled = true;
                this.textContent = '⏳ Importando...';

                fetch(window.wpfs_ajaxurl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            'action': 'wp_fast_setup_import_media',
                            'nonce': '<?php echo wp_create_nonce('wp_fast_setup_import_media'); ?>'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.textContent = '✅ Importado';
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            alert('❌ Error: ' + (data.data || 'Error desconocido'));
                            this.disabled = false;
                            this.textContent = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('WP Fast Setup: Media import error:', error);
                        alert('❌ Error de conexión: ' + error.message);
                        this.disabled = false;
                        this.textContent = originalText;
                    });
            });
        }

        // Plugin filtering and favorites functionality
        const pluginSearch = document.getElementById('plugin-search');
        const pluginSort = document.getElementById('plugin-sort');
        const pluginLetter = document.getElementById('plugin-letter');
        const pluginList = document.querySelector('.wpf-plugin-list');
        const pluginItems = pluginList ? pluginList.querySelectorAll('.wpf-plugin-item') : [];

        // Filter plugins function
        function filterPlugins() {
            if (!pluginList) return;

            const searchTerm = pluginSearch ? pluginSearch.value.toLowerCase() : '';
            const sortValue = pluginSort ? pluginSort.value : 'name-asc';
            const letterFilter = pluginLetter ? pluginLetter.value : '';

            const itemsArray = Array.from(pluginItems);

            // Filter by search and letter
            const filteredItems = itemsArray.filter(item => {
                const label = item.querySelector('label').textContent.toLowerCase();
                const matchesSearch = label.includes(searchTerm);
                const matchesLetter = !letterFilter || label.startsWith(letterFilter.toLowerCase());
                return matchesSearch && matchesLetter;
            });

            // Sort items
            filteredItems.sort((a, b) => {
                const labelA = a.querySelector('label').textContent.toLowerCase();
                const labelB = b.querySelector('label').textContent.toLowerCase();

                if (sortValue === 'name-desc') {
                    return labelB.localeCompare(labelA);
                } else if (sortValue === 'type') {
                    // Sort by type (favorites first, then by icon/type)
                    const isFavA = a.querySelector('.fav-star-btn.favorite');
                    const isFavB = b.querySelector('.fav-star-btn.favorite');
                    if (isFavA && !isFavB) return -1;
                    if (!isFavA && isFavB) return 1;
                    return labelA.localeCompare(labelB);
                } else {
                    return labelA.localeCompare(labelB);
                }
            });

            // Hide all items first
            itemsArray.forEach(item => item.style.display = 'none');

            // Show filtered and sorted items
            filteredItems.forEach(item => item.style.display = 'flex');
        }

        // Add event listeners for filters
        if (pluginSearch) pluginSearch.addEventListener('input', filterPlugins);
        if (pluginSort) pluginSort.addEventListener('change', filterPlugins);
        if (pluginLetter) pluginLetter.addEventListener('change', filterPlugins);

        // Favorites functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fav-star-btn')) {
                const btn = e.target;
                const slug = btn.dataset.slug;
                const source = btn.dataset.source;
                const isFavorite = btn.classList.contains('favorite');

                // Update button appearance immediately
                btn.classList.toggle('favorite');
                btn.textContent = btn.classList.contains('favorite') ? '⭐' : '☆';
                btn.title = btn.classList.contains('favorite') ? 'Quitar de favoritos' : 'Añadir a favoritos';

                // Send AJAX request to update favorites
                fetch(window.wpfs_ajaxurl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            'action': 'wp_fast_setup_toggle_favorite',
                            'nonce': document.getElementById('wp_fast_setup_nonce_plugins').value,
                            'slug': slug,
                            'source': source,
                            'is_favorite': isFavorite ? '0' : '1'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('WP Fast Setup: Error updating favorite:', data.data);
                            // Revert button state on error
                            btn.classList.toggle('favorite');
                            btn.textContent = btn.classList.contains('favorite') ? '⭐' : '☆';
                            btn.title = btn.classList.contains('favorite') ? 'Quitar de favoritos' : 'Añadir a favoritos';
                            alert('❌ Error al actualizar favorito: ' + (data.data || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        console.error('WP Fast Setup: Favorite update error:', error);
                        // Revert button state on error
                        btn.classList.toggle('favorite');
                        btn.textContent = btn.classList.contains('favorite') ? '⭐' : '☆';
                        btn.title = btn.classList.contains('favorite') ? 'Quitar de favoritos' : 'Añadir a favoritos';
                        alert('❌ Error de conexión: ' + error.message);
                    });
            }
        });

        // Initial filter application
        filterPlugins();
    })();
</script>

<?php settings_errors('wp_fast_setup_messages'); ?>