<?php

/**
 * WP Fast Setup - Development Tools
 * Herramientas para desarrollo y testing sin reinstalar el plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Only load in admin and if user is admin
if (!is_admin() || !current_user_can('manage_options')) {
    return;
}

class WP_Fast_Setup_Dev_Tools
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_dev_menu'));
        add_action('admin_bar_menu', array($this, 'add_dev_toolbar'), 100);
        add_action('wp_ajax_wpfs_dev_reload', array($this, 'reload_plugin'));
        add_action('wp_ajax_wpfs_dev_clear_cache', array($this, 'clear_cache'));
    }

    /**
     * Add development menu
     */
    public function add_dev_menu()
    {
        add_submenu_page(
            'wp-fast-setup',
            'Dev Tools',
            '🛠️ Dev Tools',
            'manage_options',
            'wpfs-dev-tools',
            array($this, 'render_dev_page')
        );
    }

    /**
     * Add development toolbar
     */
    public function add_dev_toolbar($wp_admin_bar)
    {
        $wp_admin_bar->add_node(array(
            'id'    => 'wpfs-dev-tools',
            'title' => '🔄 WPFS Dev',
            'href'  => admin_url('admin.php?page=wpfs-dev-tools'),
            'meta'  => array('class' => 'wpfs-dev-toolbar')
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'wpfs-dev-reload',
            'title' => 'Reload Plugin',
            'parent' => 'wpfs-dev-tools',
            'href'  => '#',
            'meta'  => array('onclick' => 'wpfsDevReload(); return false;')
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'wpfs-dev-cache',
            'title' => 'Clear Cache',
            'parent' => 'wpfs-dev-tools',
            'href'  => '#',
            'meta'  => array('onclick' => 'wpfsDevClearCache(); return false;')
        ));
    }

    /**
     * Render development tools page
     */
    public function render_dev_page()
    {
?>
        <div class="wrap">
            <h1>🛠️ WP Fast Setup - Development Tools</h1>

            <div class="wpfs-dev-grid">
                <div class="wpfs-dev-card">
                    <h3>🔄 Reload Plugin</h3>
                    <p>Recarga el plugin sin reinstalarlo. Útil para cambios en código PHP.</p>
                    <button id="wpfs-reload-btn" class="button button-primary">Reload Plugin</button>
                    <div id="wpfs-reload-status"></div>
                </div>

                <div class="wpfs-dev-card">
                    <h3>🧹 Clear Cache</h3>
                    <p>Limpia caches de WordPress, navegador y plugins.</p>
                    <button id="wpfs-cache-btn" class="button button-secondary">Clear Cache</button>
                    <div id="wpfs-cache-status"></div>
                </div>

                <div class="wpfs-dev-card">
                    <h3>📊 Debug Info</h3>
                    <p>Información útil para debugging.</p>
                    <ul>
                        <li><strong>WP_DEBUG:</strong> <?php echo WP_DEBUG ? '✅ Enabled' : '❌ Disabled'; ?></li>
                        <li><strong>Plugin Version:</strong> <?php echo WP_FAST_SETUP_VERSION; ?></li>
                        <li><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></li>
                        <li><strong>Last Modified:</strong> <?php echo date('Y-m-d H:i:s', filemtime(WP_FAST_SETUP_PLUGIN_DIR . 'wp-fast-setup-installer.php')); ?></li>
                    </ul>
                </div>

                <div class="wpfs-dev-card">
                    <h3>📝 Quick Actions</h3>
                    <p>Acciones rápidas para desarrollo.</p>
                    <a href="<?php echo admin_url('admin.php?page=wp-fast-setup'); ?>" class="button">Go to Plugin</a>
                    <a href="<?php echo admin_url('plugins.php'); ?>" class="button">Plugins Page</a>
                    <a href="<?php echo admin_url('admin.php?page=wpfs-dev-tools&action=export_logs'); ?>" class="button">Export Logs</a>
                </div>
            </div>

            <div class="wpfs-dev-logs">
                <h3>📋 Recent Logs</h3>
                <div id="wpfs-logs-container">
                    <?php $this->show_recent_logs(); ?>
                </div>
                <button id="wpfs-refresh-logs" class="button">Refresh Logs</button>
            </div>
        </div>

        <style>
            .wpfs-dev-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 20px;
                margin: 20px 0;
            }

            .wpfs-dev-card {
                background: #fff;
                border: 1px solid #ccd0d4;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .wpfs-dev-card h3 {
                margin-top: 0;
                color: #2271b1;
            }

            .wpfs-dev-logs {
                background: #f9f9f9;
                border: 1px solid #e5e5e5;
                padding: 20px;
                border-radius: 8px;
                margin-top: 30px;
            }

            .wpfs-dev-logs pre {
                background: #fff;
                padding: 10px;
                border-radius: 4px;
                max-height: 300px;
                overflow-y: auto;
                font-size: 12px;
            }
        </style>

        <script>
            function wpfsDevReload() {
                jQuery.post(ajaxurl, {
                    action: 'wpfs_dev_reload',
                    nonce: '<?php echo wp_create_nonce('wpfs_dev_reload'); ?>'
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                });
            }

            function wpfsDevClearCache() {
                jQuery.post(ajaxurl, {
                    action: 'wpfs_dev_clear_cache',
                    nonce: '<?php echo wp_create_nonce('wpfs_dev_clear_cache'); ?>'
                }, function(response) {
                    jQuery('#wpfs-cache-status').html(response.success ? '✅ Cache cleared!' : '❌ Error: ' + response.data);
                });
            }

            jQuery(document).ready(function($) {
                $('#wpfs-reload-btn').click(function() {
                    wpfsDevReload();
                });

                $('#wpfs-cache-btn').click(function() {
                    wpfsDevClearCache();
                });

                $('#wpfs-refresh-logs').click(function() {
                    location.reload();
                });
            });
        </script>
<?php
    }

    /**
     * Reload plugin (deactivate and reactivate)
     */
    public function reload_plugin()
    {
        // Verify nonce - check both possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wpfs_dev_reload')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wpfs_dev_reload')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error('Invalid nonce');
        }

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        // For AJAX requests, be less strict with permissions
        if (!wp_doing_ajax() && !current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        $plugin_file = plugin_basename(WP_FAST_SETUP_PLUGIN_DIR . 'wp-fast-setup-installer.php');

        // Deactivate
        deactivate_plugins($plugin_file);

        // Reactivate
        $result = activate_plugin($plugin_file);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success('Plugin reloaded successfully');
        }
    }

    /**
     * Clear various caches
     */
    public function clear_cache()
    {
        // Verify nonce - check both possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wpfs_dev_clear_cache')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wpfs_dev_clear_cache')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error('Invalid nonce');
        }

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        // For AJAX requests, be less strict with permissions
        if (!wp_doing_ajax() && !current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        // Clear WordPress cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        // Clear object cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        wp_send_json_success('Cache cleared successfully');
    }

    /**
     * Show recent debug logs
     */
    private function show_recent_logs()
    {
        $log_file = WP_CONTENT_DIR . '/debug.log';

        if (!file_exists($log_file)) {
            echo '<p>No debug.log file found. Enable WP_DEBUG to see logs.</p>';
            return;
        }

        $logs = file($log_file);
        $recent_logs = array_slice($logs, -20); // Last 20 lines

        echo '<pre>';
        foreach ($recent_logs as $log) {
            if (strpos($log, 'WP Fast Setup') !== false) {
                echo htmlspecialchars($log);
            }
        }
        echo '</pre>';
    }
}

// Initialize dev tools
new WP_Fast_Setup_Dev_Tools();
