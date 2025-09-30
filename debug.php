<?php

/**
 * WP Fast Setup - Debug Tool
 * Use this file to debug issues with the plugin functionality
 */

// SECURITY: Only allow access in debug mode and for administrators
if (!defined('WP_DEBUG') || !WP_DEBUG || !current_user_can('manage_options')) {
    wp_die('Access denied. This debug tool is only available in debug mode for administrators.');
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if WordPress is loaded
if (!defined('ABSPATH')) {
    die('WordPress not loaded');
}

echo "<h1>🔧 WP Fast Setup - Debug Tool</h1>";
echo "<style>body{font-family:monospace;background:#f5f5f5;padding:20px}.debug-section{margin:20px 0;padding:15px;border:1px solid #ddd;background:white;border-radius:5px}.success{color:green}.error{color:red}.warning{color:orange}</style>";

// Section 1: WordPress Environment
echo "<div class='debug-section'>";
echo "<h2>📊 WordPress Environment</h2>";
echo "<strong>WordPress Version:</strong> " . get_bloginfo('version') . "<br>";
echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
echo "<strong>Site URL:</strong> " . get_site_url() . "<br>";
echo "<strong>Home URL:</strong> " . get_home_url() . "<br>";
echo "<strong>Admin Email:</strong> " . get_option('admin_email') . "<br>";
echo "<strong>Current User:</strong> " . (is_user_logged_in() ? wp_get_current_user()->user_login : 'Not logged in') . "<br>";
echo "<strong>User Capabilities:</strong> " . (current_user_can('manage_options') ? '<span class="success">Has manage_options</span>' : '<span class="error">Missing manage_options</span>') . "<br>";
echo "</div>";

// Section 3: AJAX Handlers Status
echo "<div class='debug-section'>";
echo "<h2>� AJAX Handlers Status</h2>";

// Test if WordPress AJAX is properly loaded
echo "<strong>WordPress AJAX Loaded:</strong> " . (defined('DOING_AJAX') ? 'Yes (DOING_AJAX defined)' : 'No') . "<br>";
echo "<strong>Admin Context:</strong> " . (is_admin() ? 'Yes' : 'No') . "<br>";
echo "<strong>User Logged In:</strong> " . (is_user_logged_in() ? 'Yes' : 'No') . "<br>";

// Test AJAX handlers registration
$ajax_actions = [
    'wp_ajax_wp_fast_setup_save_site_settings',
    'wp_ajax_wp_fast_setup_install_plugins',
    'wp_ajax_wp_fast_setup_save_google_drive'
];

echo "<strong>AJAX Actions Registered:</strong><br>";
foreach ($ajax_actions as $action) {
    $has_action = has_action($action);
    echo "&nbsp;&nbsp;- <code>$action</code>: " . ($has_action ? '<span class="success">Registered (priority: ' . $has_action . ')</span>' : '<span class="error">Not registered</span>') . "<br>";
}

// Test nonce creation
echo "<strong>Nonce Creation Test:</strong> ";
$test_nonce = wp_create_nonce('wp_fast_setup_action');
echo '<code>' . esc_html($test_nonce) . '</code><br>';

// Test nonce verification
echo "<strong>Nonce Verification Test:</strong> " . (wp_verify_nonce($test_nonce, 'wp_fast_setup_action') ? '<span class="success">Valid</span>' : '<span class="error">Invalid</span>') . "<br>";

echo "</div>";

// Section 3: Test Site Settings Update
echo "<div class='debug-section'>";
echo "<h2>⚙️ Test Site Settings Update</h2>";

if (isset($_POST['test_site_settings'])) {
    echo "<h3>Test Results:</h3>";

    // Test blogname update
    $test_blogname = 'Test Blog Name ' . time();
    update_option('blogname', $test_blogname);
    $current_blogname = get_option('blogname');
    echo "<strong>Blog Name Test:</strong> " . ($current_blogname === $test_blogname ? '<span class="success">PASSED</span>' : '<span class="error">FAILED</span>') . "<br>";

    // Test admin email update
    $test_email = 'test' . time() . '@example.com';
    update_option('admin_email', $test_email);
    $current_email = get_option('admin_email');
    echo "<strong>Admin Email Test:</strong> " . ($current_email === $test_email ? '<span class="success">PASSED</span>' : '<span class="error">FAILED</span>') . "<br>";

    // Test site URL update
    $test_url = 'https://test' . time() . '.example.com';
    update_option('siteurl', $test_url);
    update_option('home', $test_url);
    $current_siteurl = get_option('siteurl');
    $current_home = get_option('home');
    echo "<strong>Site URL Test:</strong> " . ($current_siteurl === $test_url && $current_home === $test_url ? '<span class="success">PASSED</span>' : '<span class="error">FAILED</span>') . "<br>";

    echo "<hr>";
}

echo "<form method='post'>";
echo "<button type='submit' name='test_site_settings' style='padding:10px 20px;background:#007cba;color:white;border:none;border-radius:5px;cursor:pointer;'>🧪 Test Site Settings Update</button>";
echo "</form>";
echo "</div>";

// Section 4: Google Drive Configuration
echo "<div class='debug-section'>";
echo "<h2>☁️ Google Drive Configuration</h2>";
$api_key = get_option('wp_fast_setup_google_drive_api_key', '');
$folder_id = get_option('wp_fast_setup_google_drive_folder_id', '');
echo "<strong>API Key Configured:</strong> " . (!empty($api_key) ? '<span class="success">Yes</span> (' . substr($api_key, 0, 10) . '...)' : '<span class="error">No</span>') . "<br>";
echo "<strong>Folder ID Configured:</strong> " . (!empty($folder_id) ? '<span class="success">Yes</span> (' . substr($folder_id, 0, 10) . '...)' : '<span class="error">No</span>') . "<br>";
echo "<strong>Constants Available:</strong> " . (defined('WP_FAST_SETUP_DEFAULT_API_KEY') ? '<span class="success">Yes</span>' : '<span class="error">No</span>') . "<br>";
echo "</div>";

// Section 5: Plugin Manager Test
echo "<div class='debug-section'>";
echo "<h2>📦 Plugin Manager Test</h2>";

if (isset($_POST['test_plugin_manager'])) {
    echo "<h3>Test Results:</h3>";

    // Test local ZIP files
    $zip_dir = plugin_dir_path(__FILE__) . 'zip-files/';
    $zip_files = glob($zip_dir . '*.zip');
    echo "<strong>ZIP Files Found:</strong> " . count($zip_files) . "<br>";
    if (count($zip_files) > 0) {
        echo "<strong>ZIP Files:</strong><br>";
        foreach ($zip_files as $file) {
            echo "&nbsp;&nbsp;- " . basename($file) . "<br>";
        }
    }

    // Test plugin installation functions
    echo "<strong>Plugin Functions Available:</strong><br>";
    echo "&nbsp;&nbsp;- plugins_api: " . (function_exists('plugins_api') ? '<span class="success">Yes</span>' : '<span class="error">No</span>') . "<br>";
    echo "&nbsp;&nbsp;- wp_remote_get: " . (function_exists('wp_remote_get') ? '<span class="success">Yes</span>' : '<span class="error">No</span>') . "<br>";
    echo "&nbsp;&nbsp;- activate_plugin: " . (function_exists('activate_plugin') ? '<span class="success">Yes</span>' : '<span class="error">No</span>') . "<br>";

    echo "<hr>";
}

echo "<form method='post'>";
echo "<button type='submit' name='test_plugin_manager' style='padding:10px 20px;background:#007cba;color:white;border:none;border-radius:5px;cursor:pointer;'>🧪 Test Plugin Manager</button>";
echo "</form>";
echo "</div>";

// Section 6: JavaScript Console Logs
echo "<div class='debug-section'>";
echo "<h2>🔍 JavaScript Debug Info</h2>";
echo "<p>Check your browser's developer console (F12) for JavaScript errors when using the plugin.</p>";
echo "<strong>Expected Console Logs:</strong><br>";
echo "&nbsp;&nbsp;- 'WP Fast Setup: Admin_Pages constructor called'<br>";
echo "&nbsp;&nbsp;- 'WP Fast Setup: register_admin_menu called'<br>";
echo "&nbsp;&nbsp;- AJAX request/response logs<br>";
echo "</div>";

// Section 7: Error Logs
echo "<div class='debug-section'>";
echo "<h2>📝 Recent Error Logs</h2>";
$log_file = WP_CONTENT_DIR . '/debug.log';
if (file_exists($log_file) && is_readable($log_file)) {
    $logs = file($log_file);
    $recent_logs = array_slice($logs, -10); // Last 10 lines
    echo "<pre style='background:#f9f9f9;padding:10px;border-radius:3px;max-height:200px;overflow:auto;'>";
    foreach ($recent_logs as $log) {
        if (strpos($log, 'WP Fast Setup') !== false) {
            echo htmlspecialchars($log);
        }
    }
    echo "</pre>";
} else {
    echo "<span class='warning'>Debug log not found or not readable</span><br>";
    echo "<strong>To enable debug logging, add to wp-config.php:</strong><br>";
    echo "<code>define('WP_DEBUG', true);\ndefine('WP_DEBUG_LOG', true);</code>";
}
echo "</div>";

echo "<hr>";
echo "<p><strong>Debug completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
