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

    .wpf-form-group input[type="file"] {
        padding: 8px;
        border: 2px dashed var(--wpfs-border);
        border-radius: 6px;
        background: var(--wpfs-light);
        transition: all 0.3s ease;
    }

    .wpf-form-group input[type="file"]:hover {
        border-color: var(--wpfs-primary);
        background: rgba(39, 71, 177, 0.05);
    }

    .wpf-current-image {
        margin-top: 10px;
        padding: 15px;
        background: var(--wpfs-light);
        border-radius: 6px;
        border: 1px solid var(--wpfs-border);
    }

    .wpf-current-image img {
        border-radius: 4px;
        box-shadow: var(--wpfs-shadow);
    }

    .wpf-form-help {
        display: block;
        margin-top: 5px;
        font-size: 12px;
        color: var(--wpfs-text-light);
        font-style: italic;
    }

    /* Indexation Checkbox */
    .wpf-checkbox-label {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
        font-weight: normal;
        margin-bottom: 5px;
    }

    .wpf-checkbox-label input[type="checkbox"] {
        margin-top: 2px;
        width: 16px;
        height: 16px;
        accent-color: var(--wpfs-primary);
    }

    .wpf-checkbox-text {
        font-size: 14px;
        color: var(--wpfs-text);
        line-height: 1.4;
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
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
    .wpf-drive-status {
        background: var(--wpfs-light);
        border: 1px solid var(--wpfs-border);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .wpf-drive-status h4 {
        margin: 0 0 10px 0;
        color: var(--wpfs-text);
    }

    .wpf-status-connected,
    .wpf-status-disconnected {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        border-radius: 6px;
    }

    .wpf-status-connected {
        background: #d1f2d3;
        border: 1px solid #4ade80;
        color: #166534;
    }

    .wpf-status-disconnected {
        background: #fee2e2;
        border: 1px solid #f87171;
        color: #991b1b;
    }

    .wpf-status-connected a,
    .wpf-status-disconnected a {
        color: var(--wpfs-primary);
        text-decoration: none;
        font-weight: 500;
    }

    .wpf-plugin-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
        padding: 15px;
        background: var(--wpfs-light);
        border: 1px solid var(--wpfs-border);
        border-radius: 8px;
    }

    .wpf-plugin-search {
        flex: 1;
        max-width: 300px;
    }

    .wpf-search-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--wpfs-border);
        border-radius: 6px;
        font-size: 14px;
    }

    .wpf-search-input:focus {
        outline: none;
        border-color: var(--wpfs-primary);
        box-shadow: 0 0 0 2px rgba(37, 113, 177, 0.1);
    }

    .wpf-plugin-actions {
        display: flex;
        gap: 10px;
    }

    .wpf-btn-sm {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 4px;
    }

    .wpf-btn-info {
        background: #17a2b8;
        color: white;
    }

    .wpf-btn-info:hover {
        background: #138496;
    }

    .wpf-media-selector {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
    }

    .wpf-current-image {
        margin-top: 10px;
        padding: 10px;
        background: var(--wpfs-light);
        border: 1px solid var(--wpfs-border);
        border-radius: 6px;
    }

    .wpf-current-image img {
        border: 1px solid var(--wpfs-border);
        border-radius: 4px;
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
        <div class="wpf-tab active" data-tab="site" title="Configurar título, idioma, logo, favicon y características avanzadas">
            <span class="wpf-tab-icon">1️⃣</span>
            <h3 class="wpf-tab-title">Configuración del Sitio</h3>
        </div>
        <div class="wpf-tab" data-tab="plugins" title="Instalar y gestionar plugins desde repositorio o archivos ZIP">
            <span class="wpf-tab-icon">2️⃣</span>
            <h3 class="wpf-tab-title">Plugins</h3>
        </div>
        <div class="wpf-tab" data-tab="content" title="Crear páginas automáticamente con diferentes plantillas">
            <span class="wpf-tab-icon">3️⃣</span>
            <h3 class="wpf-tab-title">Contenido</h3>
        </div>
        <div class="wpf-tab" data-tab="templates" title="Crear headers y footers con Elementor">
            <span class="wpf-tab-icon">4️⃣</span>
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

            <form method="POST" action="" enctype="multipart/form-data">
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_site'); ?>

                <div class="wpf-form-group">
                    <label for="site_name">Título del Sitio</label>
                    <input type="text" id="site_name" name="nombre_sitio" value="<?php echo esc_attr($current_site_name); ?>" placeholder="Mi Sitio Web">
                </div>

                <div class="wpf-form-group">
                    <label for="site_tagline">Descripción del Sitio (Tagline)</label>
                    <input type="text" id="site_tagline" name="site_tagline" value="<?php echo esc_attr($current_tagline); ?>" placeholder="Solo otro sitio WordPress">
                </div>

                <div class="wpf-form-group">
                    <label for="site_language">Idioma del Sitio</label>
                    <select id="site_language" name="idioma_sitio">
                        <option value="es_ES" <?php selected($current_language, 'es_ES'); ?>>Español</option>
                        <option value="en_US" <?php selected($current_language, 'en_US'); ?>>English (US)</option>
                        <option value="en_GB" <?php selected($current_language, 'en_GB'); ?>>English (UK)</option>
                        <option value="fr_FR" <?php selected($current_language, 'fr_FR'); ?>>Français</option>
                        <option value="de_DE" <?php selected($current_language, 'de_DE'); ?>>Deutsch</option>
                        <option value="it_IT" <?php selected($current_language, 'it_IT'); ?>>Italiano</option>
                        <option value="pt_PT" <?php selected($current_language, 'pt_PT'); ?>>Português</option>
                        <option value="pt_BR" <?php selected($current_language, 'pt_BR'); ?>>Português (Brasil)</option>
                        <option value="ru_RU" <?php selected($current_language, 'ru_RU'); ?>>Русский</option>
                        <option value="ja" <?php selected($current_language, 'ja'); ?>>日本語</option>
                        <option value="zh_CN" <?php selected($current_language, 'zh_CN'); ?>>中文 (简体)</option>
                        <option value="zh_TW" <?php selected($current_language, 'zh_TW'); ?>>中文 (繁體)</option>
                        <option value="ar" <?php selected($current_language, 'ar'); ?>>العربية</option>
                        <option value="hi_IN" <?php selected($current_language, 'hi_IN'); ?>>हिन्दी</option>
                    </select>
                </div>

                <div class="wpf-form-group">
                    <label for="site_url">URL del Sitio</label>
                    <input type="url" id="site_url" name="url_sitio" value="<?php echo esc_url($current_url); ?>" placeholder="https://misitio.com">
                </div>

                <div class="wpf-form-group">
                    <label for="admin_email">Correo del Administrador</label>
                    <input type="email" id="admin_email" name="admin_email" value="<?php echo esc_attr($current_admin_email); ?>" placeholder="admin@misitio.com">
                    <small class="wpf-form-help">Cambia el correo electrónico del administrador sin confirmación</small>
                </div>

                <div class="wpf-form-group">
                    <label for="site_logo">Logo del Sitio</label>
                    <div class="wpf-media-selector">
                        <input type="hidden" id="site_logo_id" name="site_logo_id" value="<?php echo esc_attr($current_logo_id); ?>">
                        <button type="button" id="select-logo-btn" class="wpf-btn wpf-btn-secondary wpf-btn-sm" title="Seleccionar una imagen desde la biblioteca de medios de WordPress">
                            📁 Seleccionar Logo
                        </button>
                        <button type="button" id="remove-logo-btn" class="wpf-btn wpf-btn-danger wpf-btn-sm" <?php echo $current_logo_id ? '' : 'style="display:none;"'; ?> title="Quitar el logo actual">
                            🗑️ Quitar
                        </button>
                    </div>
                    <?php if ($current_logo_id) : ?>
                        <div class="wpf-current-image">
                            <p><strong>Logo actual:</strong></p>
                            <img id="logo-preview" src="<?php echo wp_get_attachment_image_url($current_logo_id, 'thumbnail'); ?>" alt="Current Logo" style="max-width: 100px; max-height: 100px;">
                        </div>
                    <?php else: ?>
                        <div class="wpf-current-image" id="logo-preview-container" style="display:none;">
                            <p><strong>Logo seleccionado:</strong></p>
                            <img id="logo-preview" src="" alt="Selected Logo" style="max-width: 100px; max-height: 100px;">
                        </div>
                    <?php endif; ?>
                    <small class="wpf-form-help">Compatible con Elementor. Selecciona una imagen desde la biblioteca de medios</small>
                </div>

                <div class="wpf-form-group">
                    <label for="site_favicon">Favicon del Sitio</label>
                    <div class="wpf-media-selector">
                        <input type="hidden" id="site_favicon_id" name="site_favicon_id" value="<?php echo esc_attr($current_favicon_id); ?>">
                        <button type="button" id="select-favicon-btn" class="wpf-btn wpf-btn-secondary wpf-btn-sm" title="Seleccionar una imagen para el favicon desde la biblioteca de medios">
                            📁 Seleccionar Favicon
                        </button>
                        <button type="button" id="remove-favicon-btn" class="wpf-btn wpf-btn-danger wpf-btn-sm" <?php echo $current_favicon_id ? '' : 'style="display:none;"'; ?> title="Quitar el favicon actual">
                            🗑️ Quitar
                        </button>
                    </div>
                    <?php if ($current_favicon_id) : ?>
                        <div class="wpf-current-image">
                            <p><strong>Favicon actual:</strong></p>
                            <img id="favicon-preview" src="<?php echo wp_get_attachment_image_url($current_favicon_id, 'thumbnail'); ?>" alt="Current Favicon" style="max-width: 32px; max-height: 32px;">
                        </div>
                    <?php else: ?>
                        <div class="wpf-current-image" id="favicon-preview-container" style="display:none;">
                            <p><strong>Favicon seleccionado:</strong></p>
                            <img id="favicon-preview" src="" alt="Selected Favicon" style="max-width: 32px; max-height: 32px;">
                        </div>
                    <?php endif; ?>
                    <small class="wpf-form-help">Sube un favicon (recomendado: 32x32px, formato ICO, PNG o JPG)</small>
                </div>

                <div class="wpf-form-group">
                    <label class="wpf-checkbox-label">
                        <input type="checkbox" id="blog_public" name="blog_public" value="1" <?php checked($current_blog_public, 1); ?>>
                        <span class="wpf-checkbox-text" title="Controla si los motores de búsqueda pueden indexar tu sitio web">Permitir que los motores de búsqueda indexen este sitio</span>
                    </label>
                    <small class="wpf-form-help">Desmarca esta opción para evitar que los motores de búsqueda indexen tu sitio web</small>
                </div>

                <div class="wpf-button-group">
                    <button type="submit" name="save_site_settings" class="wpf-btn wpf-btn-primary" title="Guardar todos los cambios de configuración del sitio">
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
                    <button type="submit" class="wpf-btn wpf-btn-secondary" title="Guardar las credenciales de Google Drive para acceder a plugins adicionales">
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
                        <input type="checkbox" id="permalinks" name="activar_permalinks">
                        <label for="permalinks" title="Configurar URLs amigables para SEO (/post-name/ en lugar de ?p=123)">🔗 Permalinks Amigables</label>
                    </div>
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="hello_elementor" name="activar_hello_elementor">
                        <label for="hello_elementor" title="Instalar y activar el tema Hello Elementor, eliminando otros temas">🎨 Tema Hello Elementor</label>
                    </div>
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="disable_comments" name="desactivar_comentarios">
                        <label for="disable_comments" title="Desactivar completamente el sistema de comentarios en todo el sitio">🚫 Desactivar Comentarios</label>
                    </div>
                    <div class="wpf-checkbox-item">
                        <input type="checkbox" id="create_admin" name="activar_usuario">
                        <label for="create_admin" title="Crear un usuario administrador adicional con credenciales seguras">👤 Crear Usuario Admin</label>
                    </div>
                </div>

                <div class="wpf-button-group">
                    <button type="submit" name="save_features" class="wpf-btn wpf-btn-success" title="Aplicar las características seleccionadas (instalar temas, configurar permalinks, etc.)">
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
                <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_plugins'); ?>

                <?php
                // Check Google Drive connection status
                $api_key = get_option('wp_fast_setup_google_drive_api_key', WP_FAST_SETUP_DEFAULT_API_KEY);
                $folder_id = get_option('wp_fast_setup_google_drive_folder_id', WP_FAST_SETUP_DEFAULT_FOLDER_ID);
                $drive_connected = !empty($api_key) && !empty($folder_id);
                ?>

                <div class="wpf-drive-status">
                    <h4>🔗 Estado de Google Drive</h4>
                    <?php if ($drive_connected): ?>
                        <div class="wpf-status-connected">
                            <span class="wpf-status-icon">✅</span>
                            <span>Google Drive conectado y configurado</span>
                        </div>
                    <?php else: ?>
                        <div class="wpf-status-disconnected">
                            <span class="wpf-status-icon">❌</span>
                            <span>Google Drive no configurado. </span>
                            <a href="#site" onclick="document.querySelector('[data-tab=\'site\']').click();">Configurar credenciales aquí</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Plugin Controls -->
                <div class="wpf-plugin-controls">
                    <div class="wpf-plugin-search">
                        <input type="text" id="plugin-search" placeholder="🔍 Buscar plugins por nombre..." class="wpf-search-input">
                    </div>
                    <div class="wpf-plugin-actions">
                        <button type="button" id="sort-asc" class="wpf-btn wpf-btn-info wpf-btn-sm" title="Ordenar plugins alfabéticamente de A a Z">
                            🔤 Ordenar A-Z
                        </button>
                        <button type="button" id="sort-desc" class="wpf-btn wpf-btn-info wpf-btn-sm" title="Ordenar plugins alfabéticamente de Z a A">
                            🔡 Ordenar Z-A
                        </button>
                        <button type="button" id="select-all-plugins" class="wpf-btn wpf-btn-secondary wpf-btn-sm" title="Marcar todos los plugins para instalar">
                            ✅ Seleccionar Todos
                        </button>
                        <button type="button" id="deselect-all-plugins" class="wpf-btn wpf-btn-warning wpf-btn-sm" title="Desmarcar todos los plugins seleccionados">
                            🚫 Deseleccionar Todos
                        </button>
                        <button type="button" id="clear-all-plugins" class="wpf-btn wpf-btn-danger wpf-btn-sm" title="Desmarcar todos los plugins y limpiar la búsqueda">
                            🗑️ Limpiar Todo
                        </button>
                    </div>
                </div>

                <?php
                // Leer lista plugin del JSON
                $json_file = WP_FAST_SETUP_PLUGIN_DIR . 'includes/plugins-list.json';
                if (file_exists($json_file)) {
                    $json_data = file_get_contents($json_file);
                    $data = json_decode($json_data, true);
                    if (isset($data['plugins']) && is_array($data['plugins'])) {
                        echo '<h4>📚 Plugins del Repositorio de WordPress</h4>';
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
                    <h4>☁️ Plugins desde Google Drive (Archivos ZIP)</h4>
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
                            <p><?php echo esc_html($drive_error ?: 'Error de conexión con Google Drive'); ?></p>
                            <div style="margin-top: 10px; font-size: 12px; color: #666;">
                                <strong>🔍 Posibles soluciones:</strong>
                                <ul style="margin: 5px 0; padding-left: 20px;">
                                    <li>Verifica que tu API Key de Google Drive sea correcta</li>
                                    <li>Asegúrate de que el Folder ID corresponda a una carpeta existente</li>
                                    <li>Comprueba que la carpeta de Google Drive tenga permisos de lectura públicos</li>
                                    <li>Revisa que no hayas excedido los límites de la API de Google Drive</li>
                                </ul>
                                <p><strong>🧪 Para diagnosticar:</strong> Ejecuta <code>php test-google-drive.php</code> en la terminal</p>
                            </div>
                        </div>
                    </div>
                    <h4>💾 Plugins Locales (Archivos ZIP del Servidor)</h4>
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
                    <button type="submit" name="install_plugins" class="wpf-btn wpf-btn-primary" title="Instalar y activar automáticamente todos los plugins seleccionados">
                        📦 Instalar y Activar Plugins Seleccionados
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
                    <h4>🎯 Presets Rápidos de Páginas</h4>
                    <select id="preset_pages_select" class="wpf-form-group">
                        <option value="">Seleccione un preset rápido</option>
                        <option value="basico">Básico (Inicio, Servicios, Contacto)</option>
                        <option value="completo">Completo (Inicio, Nosotros, Servicios, Portfolio, Blog, Contacto)</option>
                        <option value="especial">Especial (Home, About Us, Products, FAQ, Support, Contact)</option>
                    </select>
                </div>

                <div class="wpf-form-group">
                    <label for="pages_input">Páginas a Crear (Personalizado)</label>
                    <textarea name="pages_input" id="pages_input" placeholder="Ejemplo:
Inicio
 Servicios
 Contacto

Cada línea = una página nueva
Espacio inicial = subpágina"></textarea>
                    <small style="color: var(--wpfs-text-light);">Escribe una página por línea. Si una línea comienza con espacio, será una subpágina de la línea anterior.</small>
                </div>

                <div class="wpf-template-options">
                    <label title="Crear páginas con ancho completo optimizadas para Elementor">
                        <input type="radio" name="page_template" value="elementor_header_footer" checked>
                        🎨 Elementor Full Width
                    </label>
                    <label title="Crear páginas con el template predeterminado de WordPress">
                        <input type="radio" name="page_template" value="default">
                        📄 Default
                    </label>
                </div>

                <div class="wpf-button-group">
                    <button type="submit" name="create_pages" class="wpf-btn wpf-btn-primary" title="Crear nuevas páginas sin afectar las existentes" onclick="setPageAction('create')">
                        📄 Solo Crear Páginas
                    </button>
                    <button type="submit" name="delete_and_create_pages" class="wpf-btn wpf-btn-warning" title="Eliminar todas las páginas existentes y crear las nuevas" onclick="setPageAction('delete')">
                        🗑️ Eliminar y Crear Páginas
                    </button>
                    <button type="submit" name="create_pages_and_menu" class="wpf-btn wpf-btn-success" title="Crear páginas nuevas y generar un menú de navegación automáticamente" onclick="setPageAction('create_menu')">
                        📄 Crear Páginas con Menú
                    </button>
                    <button type="submit" name="delete_and_create_pages_with_menu" class="wpf-btn wpf-btn-warning" title="Eliminar todo el contenido existente y crear páginas nuevas con menú" onclick="setPageAction('delete_menu')">
                        🗑️ Eliminar Todo y Crear con Menú
                    </button>
                </div>

                <!-- Hidden fields for page actions -->
                <input type="hidden" name="page_action" id="page_action" value="">
                <input type="hidden" name="delete_existing" id="delete_existing" value="0">
                <input type="hidden" name="create_menu" id="create_menu" value="0">
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
                    <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_header'); ?>
                    <button type="submit" name="create_header" class="wpf-btn wpf-btn-primary" title="Crear un header profesional con Elementor usando templates predefinidos">
                        🎨 Crear Header con Elementor
                    </button>
                </form>

                <form method="post" action="" style="display: inline;">
                    <?php wp_nonce_field('wp_fast_setup_action', 'wp_fast_setup_nonce_footer'); ?>
                    <button type="submit" name="create_footer" class="wpf-btn wpf-btn-primary" title="Crear un footer profesional con Elementor usando templates predefinidos">
                        🎨 Crear Footer con Elementor
                    </button>
                </form>
            </div>

            <div class="wpf-card" style="margin-top: 30px; border-left: 4px solid var(--wpfs-warning); background: #fefefe;">
                <div class="wpf-card-header">
                    <span class="wpf-card-icon">⚠️</span>
                    <h2 class="wpf-card-title">Desinstalar Plugin</h2>
                </div>
                <p class="wpf-card-description">Esta acción desinstalará completamente el plugin WP Fast Setup, eliminando todos sus archivos y configuraciones.</p>

                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" onsubmit="return confirm('¿Estás seguro de que quieres desinstalar completamente WP Fast Setup? Se eliminarán todos los archivos y configuraciones. Esta acción no se puede deshacer.');">
                    <?php wp_nonce_field('wp_fast_setup_delete_plugin', 'wp_fast_setup_delete_nonce'); ?>
                    <input type="hidden" name="action" value="wp_fast_setup_delete_plugin">
                    <div class="wpf-button-group">
                        <button type="submit" class="wpf-btn wpf-btn-warning" title="Desinstalar completamente WP Fast Setup y eliminar todos sus archivos">
                            🗑️ Desinstalar WP Fast Setup
                        </button>
                    </div>
                </form>
            </div>
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

                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(tc => tc.classList.remove('active'));

                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });

        // AJAX form submission for content/pages creation
        const contentForm = document.getElementById('content-form');
        if (contentForm) {
            console.log('WP Fast Setup: Content form found and event listener attached');

            contentForm.addEventListener('submit', function(e) {
                e.preventDefault();

                console.log('WP Fast Setup: Content form submitted');

                const formData = new FormData();
                formData.append('action', 'wp_fast_setup_create_pages');
                formData.append('nonce', '<?php echo wp_create_nonce('wp_fast_setup_action'); ?>');
                formData.append('pages_input', this.querySelector('[name="pages_input"]').value);
                formData.append('page_template', this.querySelector('[name="page_template"]:checked').value);
                formData.append('delete_existing', this.querySelector('[name="delete_existing"]').value);
                formData.append('create_menu', this.querySelector('[name="create_menu"]').value);

                console.log('WP Fast Setup: Pages form data:');
                console.log('- pages_input:', this.querySelector('[name="pages_input"]').value);
                console.log('- page_template:', this.querySelector('[name="page_template"]:checked').value);
                console.log('- delete_existing:', this.querySelector('[name="delete_existing"]').value);
                console.log('- create_menu:', this.querySelector('[name="create_menu"]').value);

                // Find the clicked button
                let submitBtn = this.querySelector('button[type="submit"]:focus');
                if (!submitBtn) {
                    // Fallback: find any submit button
                    submitBtn = this.querySelector('button[type="submit"]');
                }

                if (submitBtn) {
                    const originalText = submitBtn.textContent;
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Procesando...';

                    console.log('WP Fast Setup: Sending AJAX request to:', ajaxurl);

                    fetch(ajaxurl, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            console.log('WP Fast Setup: Raw response:', response);
                            return response.json();
                        })
                        .then(data => {
                            console.log('WP Fast Setup: AJAX response data:', data);
                            if (data.success) {
                                submitBtn.textContent = '✅ Completado';
                                setTimeout(() => {
                                    submitBtn.disabled = false;
                                    submitBtn.textContent = originalText;
                                }, 2000);
                            } else {
                                console.error('WP Fast Setup: Error in response:', data);
                                alert('❌ Error: ' + (data.data || data.message || 'Error desconocido'));
                                submitBtn.disabled = false;
                                submitBtn.textContent = originalText;
                            }
                        })
                        .catch(error => {
                            console.error('WP Fast Setup: Fetch error:', error);
                            alert('❌ Error de conexión: ' + error.message);
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        });
                } else {
                    console.error('WP Fast Setup: No submit button found');
                }
            });
        } else {
            console.error('WP Fast Setup: Content form not found');
        }

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
            }
        };
    });
</script>

<script src="<?php echo plugins_url('assets/js/wp-fast-setup.js', WP_FAST_SETUP_PLUGIN_DIR . 'wp-fast-setup-installer.php'); ?>"></script>

<?php settings_errors('wp_fast_setup_messages'); ?>