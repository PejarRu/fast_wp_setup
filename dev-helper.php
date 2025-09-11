<?php
/**
 * Development Helper Script for WP Fast Setup
 *
 * Run this script from command line to perform common development tasks
 * Usage: php dev-helper.php [command]
 *
 * Commands:
 * - reload: Reload plugin files
 * - clear-cache: Clear WordPress caches
 * - debug-info: Show debug information
 * - logs: Show recent error logs
 * - setup: Initial development setup
 */

// Prevent direct web access
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/../../../');
}

require_once ABSPATH . 'wp-load.php';

class Dev_Helper
{
    private $plugin_dir;
    private $plugin_file;

    public function __construct()
    {
        $this->plugin_dir = plugin_dir_path(__FILE__);
        $this->plugin_file = $this->plugin_dir . 'wp-fast-setup-installer.php';
    }

    public function reload_plugin()
    {
        echo "🔄 Reloading WP Fast Setup plugin...\n";

        // Deactivate and reactivate plugin
        if (is_plugin_active('wp-fast-setup/wp-fast-setup-installer.php')) {
            deactivate_plugins('wp-fast-setup/wp-fast-setup-installer.php');
            activate_plugin('wp-fast-setup/wp-fast-setup-installer.php');
            echo "✅ Plugin reloaded successfully\n";
        } else {
            activate_plugin('wp-fast-setup/wp-fast-setup-installer.php');
            echo "✅ Plugin activated\n";
        }
    }

    public function clear_cache()
    {
        echo "🗑️ Clearing WordPress caches...\n";

        // Clear object cache
        wp_cache_flush();
        echo "✅ Object cache cleared\n";

        // Clear transients
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'");
        echo "✅ Transients cleared\n";

        // Flush rewrite rules
        flush_rewrite_rules();
        echo "✅ Rewrite rules flushed\n";

        echo "🎉 All caches cleared!\n";
    }

    public function show_debug_info()
    {
        echo "🐛 WP Fast Setup Debug Information\n";
        echo "==================================\n\n";

        echo "Plugin Version: " . WP_FAST_SETUP_VERSION . "\n";
        echo "Plugin Directory: " . WP_FAST_SETUP_PLUGIN_DIR . "\n";
        echo "WordPress Version: " . get_bloginfo('version') . "\n";
        echo "PHP Version: " . PHP_VERSION . "\n";
        echo "Debug Mode: " . (WP_DEBUG ? 'Enabled' : 'Disabled') . "\n";
        echo "Debug Log: " . (WP_DEBUG_LOG ? 'Enabled' : 'Disabled') . "\n";

        echo "\nActive Plugins:\n";
        $active_plugins = get_option('active_plugins');
        foreach ($active_plugins as $plugin) {
            echo "- " . $plugin . "\n";
        }

        echo "\nPHP Memory Limit: " . ini_get('memory_limit') . "\n";
        echo "Max Execution Time: " . ini_get('max_execution_time') . " seconds\n";
    }

    public function show_logs()
    {
        echo "📋 Recent Error Logs\n";
        echo "===================\n\n";

        $log_file = WP_CONTENT_DIR . '/debug.log';

        if (file_exists($log_file)) {
            $logs = file($log_file);
            $recent_logs = array_slice($logs, -20); // Last 20 lines

            foreach ($recent_logs as $log) {
                echo $log;
            }
        } else {
            echo "No debug.log file found. Make sure WP_DEBUG_LOG is enabled.\n";
        }
    }

    public function setup_development()
    {
        echo "🚀 Setting up development environment...\n";

        // Check if wp-config.php exists
        $wp_config = ABSPATH . 'wp-config.php';
        if (!file_exists($wp_config)) {
            echo "❌ wp-config.php not found\n";
            return;
        }

        // Read wp-config.php
        $config_content = file_get_contents($wp_config);

        // Check if debug constants are already set
        if (strpos($config_content, "define('WP_DEBUG', true);") === false) {
            echo "📝 Adding debug constants to wp-config.php...\n";

            // Add debug constants before the /* That's all, stop editing! */ line
            $debug_constants = "\n// Development settings\ndefine('WP_DEBUG', true);\ndefine('WP_DEBUG_LOG', true);\ndefine('WP_DEBUG_DISPLAY', false);\ndefine('SCRIPT_DEBUG', true);\n";

            $config_content = str_replace("/* That's all, stop editing! Happy publishing. */", $debug_constants . "/* That's all, stop editing! Happy publishing. */", $config_content);

            file_put_contents($wp_config, $config_content);
            echo "✅ Debug constants added\n";
        } else {
            echo "ℹ️ Debug constants already configured\n";
        }

        echo "🎉 Development environment setup complete!\n";
        echo "🔄 Please refresh your WordPress admin to see the development tools.\n";
    }
}

// Main execution
$helper = new Dev_Helper();
$command = $argv[1] ?? 'help';

switch ($command) {
    case 'reload':
        $helper->reload_plugin();
        break;

    case 'clear-cache':
        $helper->clear_cache();
        break;

    case 'debug-info':
        $helper->show_debug_info();
        break;

    case 'logs':
        $helper->show_logs();
        break;

    case 'setup':
        $helper->setup_development();
        break;

    case 'help':
    default:
        echo "WP Fast Setup Development Helper\n";
        echo "=================================\n\n";
        echo "Usage: php dev-helper.php [command]\n\n";
        echo "Commands:\n";
        echo "  reload      - Reload the plugin\n";
        echo "  clear-cache - Clear WordPress caches\n";
        echo "  debug-info  - Show debug information\n";
        echo "  logs        - Show recent error logs\n";
        echo "  setup       - Initial development setup\n";
        echo "  help        - Show this help message\n\n";
        echo "Examples:\n";
        echo "  php dev-helper.php setup\n";
        echo "  php dev-helper.php reload\n";
        echo "  php dev-helper.php clear-cache\n";
        break;
}
