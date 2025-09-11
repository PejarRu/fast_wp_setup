<?php
/**
 * Test file for WP Fast Setup plugin deletion functionality
 * This file can be used to test the plugin deletion feature
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Only run if user is admin and we're in debug mode
if (!current_user_can('manage_options') || !WP_DEBUG) {
    wp_die('Access denied');
}

echo '<h1>WP Fast Setup - Test Plugin Deletion</h1>';
echo '<p>This is a test page to verify the plugin deletion functionality.</p>';

// Test if the action is registered
global $wp_filter;
$has_action = isset($wp_filter['admin_post_wp_fast_setup_delete_plugin']);

echo '<h2>Test Results:</h2>';
echo '<ul>';
echo '<li>Action registered: ' . ($has_action ? '<span style="color: green;">YES</span>' : '<span style="color: red;">NO</span>') . '</li>';
echo '<li>Plugin directory: ' . WP_FAST_SETUP_PLUGIN_DIR . '</li>';
echo '<li>Plugin file: ' . plugin_basename(WP_FAST_SETUP_PLUGIN_DIR . 'wp-fast-setup-installer.php') . '</li>';
echo '<li>Current user can delete plugins: ' . (current_user_can('delete_plugins') ? 'YES' : 'NO') . '</li>';
echo '<li>Current user can manage options: ' . (current_user_can('manage_options') ? 'YES' : 'NO') . '</li>';
echo '</ul>';

// Show test form
echo '<h2>Test Form:</h2>';
echo '<form method="post" action="' . admin_url('admin-post.php') . '" onsubmit="return confirm(\'This is a test. The plugin will NOT be deleted.\');">';
wp_nonce_field('wp_fast_setup_delete_plugin', 'wp_fast_setup_delete_nonce');
echo '<input type="hidden" name="action" value="wp_fast_setup_delete_plugin">';
echo '<input type="hidden" name="test_mode" value="1">';
echo '<button type="submit" style="background: #ff6b6b; color: white; padding: 10px 20px; border: none; border-radius: 5px;">Test Plugin Deletion (Safe Mode)</button>';
echo '</form>';

echo '<p><strong>Note:</strong> This test will not actually delete the plugin. It will only test the handler.</p>';
