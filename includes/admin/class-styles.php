<?php

/**
 * Class Admin_Styles
 *
 * Handles inline CSS styles for the admin bar and custom admin menu.
 */
class Admin_Styles
{
    /**
     * Constructor.
     * Hooks into admin and frontend heads.
     */
    public function __construct()
    {
        // Show styles on both frontend and admin pages.
        add_action('wp_head', array($this, 'print_styles'));
        add_action('admin_head', array($this, 'print_styles'));
    }

    /**
     * Prints all inline styles.
     */
    public function print_styles()
    {
        $css_url = WP_FAST_SETUP_PLUGIN_URL . 'styles/admin-styles.css';
        echo '<link rel="stylesheet" href="' . esc_url($css_url) . '" type="text/css" media="all" />';
    }

}

// Instantiate to hook the styles.
new Admin_Styles();