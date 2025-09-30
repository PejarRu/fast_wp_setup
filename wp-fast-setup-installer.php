<?php

/**
 * Plugin Name: WP Fast Setup
 * Description: Configura el sitio, instala plugins habituales, crea páginas básicas
 * Version: 3.0
 * Author: Alex Parra
 * Text Domain: wp-fast-setup
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_FAST_SETUP_VERSION', '3.0');
define('WP_FAST_SETUP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_FAST_SETUP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load environment variables if .env file exists
if (file_exists(__DIR__ . '/.env')) {
    $env_lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Default Google Drive settings - Priority: .env > Empty defaults
define('WP_FAST_SETUP_DEFAULT_API_KEY', $_ENV['GOOGLE_DRIVE_API_KEY'] ?? '');
define('WP_FAST_SETUP_DEFAULT_FOLDER_ID', $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? '');

/**
 * Main plugin class
 */
class WP_Fast_Setup
{
    private static $instance = null;
    private $admin_pages;
    private $dev_tools;

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
        $this->init_admin();
    }

    /**
     * Load required files
     */
    private function load_dependencies()
    {
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-admin-pages.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-plugins-manager.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-styles.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-template-manager.php';
        require_once WP_FAST_SETUP_PLUGIN_DIR . 'includes/admin/class-users.php';

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
    }

    private function init_admin()
    {
        if (is_admin()) {
            $this->admin_pages = new Admin_Pages();
        }
    }

    /**
     * Initialize plugin components
     */
    public function init_plugin()
    {

        if (is_admin() && current_user_can('manage_options')) {
            error_log('WP Fast Setup: init_plugin called');
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
