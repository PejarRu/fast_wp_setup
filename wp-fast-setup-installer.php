<?php

/**
 * Plugin Name: WP Fast Setup
 * Description: Configura el sitio, instala plugins habituales, crea páginas básicas
 * Version: 3.0.1
 * Author: Alex Parra
 * Text Domain: wp-fast-setup
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_FAST_SETUP_VERSION', '3.0.1');
define('WP_FAST_SETUP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_FAST_SETUP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load environment variables if .env file exists
$env_path = __DIR__ . '/.env';
if (file_exists($env_path)) {
    $env_values = @parse_ini_file($env_path, false, INI_SCANNER_RAW);

    if ($env_values && is_array($env_values)) {
        foreach ($env_values as $key => $value) {
            $clean_key = trim($key);
            if ($clean_key === '') {
                continue;
            }

            $clean_value = is_string($value) ? trim($value) : $value;
            if (is_string($clean_value)) {
                $clean_value = trim($clean_value, "\"' ");
            }

            $_ENV[$clean_key] = $clean_value;
            putenv($clean_key . '=' . $clean_value);
        }
    } else {
        $env_lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($env_lines as $line) {
            if (strpos($line, '=') !== false && strpos(ltrim($line), '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $clean_key = trim($key);
                $clean_value = trim($value);
                $clean_value = trim($clean_value, "\"' ");
                $_ENV[$clean_key] = $clean_value;
                putenv($clean_key . '=' . $clean_value);
            }
        }
    }
}

// Default Google Drive settings - Priority: .env > Empty defaults
$default_drive_api_key = $_ENV['GOOGLE_DRIVE_API_KEY'] ?? getenv('GOOGLE_DRIVE_API_KEY');
if (empty($default_drive_api_key)) {
    $default_drive_api_key = 'AIzaSyAhiAfbbeOo2K6DYH39rIEQnhGdzvJrvTI';
}

$default_drive_folder_id = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? getenv('GOOGLE_DRIVE_FOLDER_ID');
if (empty($default_drive_folder_id)) {
    $default_drive_folder_id = '1UCyT_r27DYShoDTqE_i-YLFUkmL5MeFX';
}

define('WP_FAST_SETUP_DEFAULT_API_KEY', $default_drive_api_key);
define('WP_FAST_SETUP_DEFAULT_FOLDER_ID', $default_drive_folder_id);

/**
 * Main plugin class
 */
class WP_Fast_Setup
{
    private static $instance = null;
    private $admin_pages;
    private $dev_tools;
    private $media_importer;

    /**
     * Get singleton instance
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        add_action('admin_init', function () {
            error_log('WP Fast Setup: Constructor called');
        });
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load plugin dependencies
     */
    private function load_dependencies()
    {
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-admin-pages.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-styles.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-template-manager.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-users.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-media-importer.php';

        // Load development tools if in debug mode
        if (defined('WP_DEBUG') && WP_DEBUG) {
            require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/dev-tools.php';
        }
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks()
    {
        add_action('plugins_loaded', array($this, 'init_plugin'));
        add_action('init', array($this, 'init_admin_components'), 5);
    }

    /**
     * Initialize plugin components
     */
    public function init_plugin()
    {
        // Initialize components that don't require admin context
        // (moved Admin_Pages initialization to init_admin_components)
    }

    /**
     * Initialize admin-specific components
     */
    public function init_admin_components()
    {
        // Always initialize AJAX handlers (they need to be available for AJAX requests)
        error_log('WP Fast Setup: init_admin_components called');

        // Load and initialize modules that handle AJAX
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-site-settings-handler.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-plugin-manager.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-page-creator.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-feature-manager.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-media-importer.php';

        // Initialize AJAX handlers
        new SiteSettingsHandler();
        new PluginManager();
        new PageCreator();
        new FeatureManager();
        WP_Fast_Setup_Media_Importer::get_instance();

        // Initialize admin interface only in admin context
        if (is_admin()) {
            // Initialize admin pages (main interface)
            $this->admin_pages = new Admin_Pages();

            // Initialize development tools if in debug mode
            if (defined('WP_DEBUG') && WP_DEBUG) {
                $this->dev_tools = new WP_Fast_Setup_Dev_Tools();
            }
        }
    }
}

/**
 * Initialize the plugin
 */
function wp_fast_setup_init()
{
    return WP_Fast_Setup::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'wp_fast_setup_init');

// Enable SVG upload if option is set
if (get_option('wp_fast_setup_svg_enabled') === '1') {
    add_filter('upload_mimes', function($mimes) {
        $mimes['svg'] = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
        return $mimes;
    });
    
    add_filter('wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {
        $filetype = wp_check_filetype($filename, $mimes);
        return [
            'ext'             => $filetype['ext'],
            'type'            => $filetype['type'],
            'proper_filename' => $data['proper_filename']
        ];
    }, 10, 4);
}
