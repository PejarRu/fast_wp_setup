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

/**
 * Main plugin class
 */
class WP_Fast_Setup
{
    private static $instance = null;
    private $admin_pages;

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
        add_action('admin_init', function() {
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
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks()
    {
        add_action('plugins_loaded', array($this, 'init_plugin'));
    }

    private function init_admin() {
        if (is_admin()) {
            $this->admin_pages = new Admin_Pages();
        }
    }

    /**
     * Initialize plugin components
     */
    public function init_plugin() {
        
        if (is_admin() && current_user_can('manage_options')) {
            error_log('WP Fast Setup: init_plugin called');
            $this->admin_pages = new Admin_Pages();
        }
    }
}

/**
 * Initialize the plugin
 */
function wp_fast_setup_init() {
    return WP_Fast_Setup::get_instance();

}

// Start the plugin
add_action('plugins_loaded', 'wp_fast_setup_init');
