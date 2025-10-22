<?php

/**
 * WP Fast Setup - Media Importer Class
 * Handles automatic import of images from assets/images/ to WordPress media library
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Fast_Setup_Media_Importer
{

    private static $instance = null;
    private $images_dir;
    private $imported_images = array();

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
        $this->images_dir = WP_FAST_SETUP_PLUGIN_DIR . 'assets/images/';

        // Hook into plugin activation
        register_activation_hook(WP_FAST_SETUP_PLUGIN_DIR . 'wp-fast-setup-installer.php', array($this, 'import_media_on_activation'));

        // Add admin action for manual import
        add_action('admin_post_wp_fast_setup_import_media', array($this, 'import_media'));
        add_action('admin_post_wp_fast_setup_delete_imported_media', array($this, 'delete_imported_media'));

        // Add AJAX handler for media import
        add_action('wp_ajax_wp_fast_setup_import_media', array($this, 'ajax_import_media'));
    }

    /**
     * Import media on plugin activation
     */
    public function import_media_on_activation()
    {
        $this->import_media();
    }

    /**
     * Import all images from assets/images/ directory to WordPress media library
     */
    public function import_media()
    {
        if (!current_user_can('upload_files')) {
            wp_die(__('You do not have permission to upload files.', 'wp-fast-setup'));
        }

        $imported_count = 0;
        $errors = array();

        // Get all PNG, JPG, JPEG, GIF, WEBP files from the images directory
        $image_files = glob($this->images_dir . '*.{png,jpg,jpeg,gif,webp}', GLOB_BRACE);

        if (empty($image_files)) {
            $this->log_message('No image files found in ' . $this->images_dir);
            return;
        }

        foreach ($image_files as $image_file) {
            $result = $this->import_single_image($image_file);
            if ($result['success']) {
                $imported_count++;
                $this->imported_images[] = $result['attachment_id'];
            } else {
                $errors[] = $result['error'];
            }
        }

        // Store imported image IDs for potential cleanup
        update_option('wp_fast_setup_imported_media', $this->imported_images);

        // Log results
        $this->log_message("Imported $imported_count images. Errors: " . count($errors));

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->log_message("Import error: $error");
            }
        }

        // Redirect back to admin page with success message
        if (wp_get_referer()) {
            wp_safe_redirect(add_query_arg(array(
                'imported' => $imported_count,
                'errors' => count($errors)
            ), wp_get_referer()));
            exit;
        }
    }

    /**
     * Import a single image file to WordPress media library
     */
    private function import_single_image($file_path)
    {
        if (!file_exists($file_path)) {
            return array('success' => false, 'error' => 'File does not exist: ' . $file_path);
        }

        $filename = basename($file_path);
        $filetype = wp_check_filetype($filename);

        if (!$filetype['type']) {
            return array('success' => false, 'error' => 'Invalid file type for: ' . $filename);
        }

        // Check if image already exists in media library
        $existing_attachment = $this->get_attachment_by_filename($filename);
        if ($existing_attachment) {
            return array('success' => true, 'attachment_id' => $existing_attachment->ID, 'message' => 'Image already exists');
        }

        // Read file contents
        $file_contents = file_get_contents($file_path);
        if (!$file_contents) {
            return array('success' => false, 'error' => 'Could not read file: ' . $filename);
        }

        // Upload the file to WordPress
        $upload = wp_upload_bits($filename, null, $file_contents);

        if ($upload['error']) {
            return array('success' => false, 'error' => $upload['error']);
        }

        // Create attachment post
        $attachment = array(
            'post_mime_type' => $filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attachment_id = wp_insert_attachment($attachment, $upload['file']);

        if (is_wp_error($attachment_id)) {
            return array('success' => false, 'error' => $attachment_id->get_error_message());
        }

        // Generate metadata for the attachment
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        return array('success' => true, 'attachment_id' => $attachment_id);
    }

    /**
     * Check if an attachment with the given filename already exists
     */
    private function get_attachment_by_filename($filename)
    {
        global $wpdb;

        $attachment = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $wpdb->posts WHERE post_type = 'attachment' AND post_title = %s",
            preg_replace('/\.[^.]+$/', '', $filename)
        ));

        return $attachment;
    }

    /**
     * Delete all imported media (for cleanup/testing)
     */
    public function delete_imported_media()
    {
        if (!current_user_can('delete_posts')) {
            wp_die(__('You do not have permission to delete posts.', 'wp-fast-setup'));
        }

        $imported_media = get_option('wp_fast_setup_imported_media', array());
        $deleted_count = 0;

        foreach ($imported_media as $attachment_id) {
            if (wp_delete_attachment($attachment_id, true)) {
                $deleted_count++;
            }
        }

        // Clear the stored IDs
        delete_option('wp_fast_setup_imported_media');

        $this->log_message("Deleted $deleted_count imported images");

        // Redirect back
        if (wp_get_referer()) {
            wp_safe_redirect(add_query_arg('deleted', $deleted_count, wp_get_referer()));
            exit;
        }
    }

    /**
     * AJAX handler for media import
     */
    public function ajax_import_media()
    {
        // Verify nonce - check both possible field names
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_import_media')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_import_media')) {
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
        if (!wp_doing_ajax() && !current_user_can('upload_files')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        try {
            $this->import_media();
            wp_send_json_success(array(
                'message' => 'Media imported successfully',
                'imported_count' => count($this->imported_images)
            ));
        } catch (Exception $e) {
            wp_send_json_error('Import failed: ' . $e->get_message());
        }
    }

    /**
     * Get list of available images in the assets/images directory
     */
    public function get_available_images()
    {
        $images = array();
        $image_files = glob($this->images_dir . '*.{png,jpg,jpeg,gif,webp}', GLOB_BRACE);

        foreach ($image_files as $file) {
            $filename = basename($file);
            $images[] = array(
                'filename' => $filename,
                'path' => $file,
                'url' => WP_FAST_SETUP_PLUGIN_URL . 'assets/images/' . $filename,
                'exists_in_media' => $this->get_attachment_by_filename($filename) ? true : false
            );
        }

        return $images;
    }

    /**
     * Log messages for debugging
     */
    private function log_message($message)
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WP Fast Setup Media Importer: ' . $message);
        }
    }

    /**
     * Get imported media IDs
     */
    public function get_imported_media_ids()
    {
        return get_option('wp_fast_setup_imported_media', array());
    }
}
