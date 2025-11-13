<?php

/**
 * Site Settings Handler
 * Handles site configuration, language, permalinks, comments, etc.
 */

class SiteSettingsHandler
{
    public function __construct()
    {
        // AJAX actions for site settings
        add_action('wp_ajax_wp_fast_setup_save_site_settings', array($this, 'ajax_save_site_settings'));
        add_action('wp_ajax_wp_fast_setup_save_elementor_branding', array($this, 'ajax_save_elementor_branding'));
        error_log('WP Fast Setup: SiteSettingsHandler constructor called - AJAX actions registered');
    }

    /**
     * AJAX handler for saving site settings
     */
    public function ajax_save_site_settings()
    {
        error_log('WP Fast Setup: ajax_save_site_settings called - START');
        error_log('WP Fast Setup: POST data: ' . print_r($_POST, true));
        error_log('WP Fast Setup: REQUEST data: ' . print_r($_REQUEST, true));

        // Verify nonce - check all possible field names used in forms
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
            error_log('WP Fast Setup: Nonce valid via $_POST[_wpnonce]');
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
            error_log('WP Fast Setup: Nonce valid via $_POST[nonce]');
        } elseif (isset($_POST['wp_fast_setup_nonce_site']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_site'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
            error_log('WP Fast Setup: Nonce valid via $_POST[wp_fast_setup_nonce_site]');
        } else {
            error_log('WP Fast Setup: No valid nonce found. Available POST keys: ' . implode(', ', array_keys($_POST)));
        }

        if (!$nonce_valid) {
            error_log('WP Fast Setup: Invalid nonce - ending request');
            wp_send_json_error('Invalid nonce');
            return;
        }

        if (!is_user_logged_in()) {
            error_log('WP Fast Setup: User is not logged in');
            wp_send_json_error('User not logged in');
            return;
        }

        // For AJAX requests, be less strict with permissions
        if (!wp_doing_ajax() && !current_user_can('manage_options')) {
            error_log('WP Fast Setup: User does not have manage_options capability');
            wp_send_json_error('Insufficient permissions');
            return;
        }

        try {
            error_log('WP Fast Setup: Processing site settings data');
            $site_name = sanitize_text_field($_POST['nombre_sitio']);
            $site_tagline = isset($_POST['site_tagline']) ? sanitize_text_field($_POST['site_tagline']) : '';
            $admin_email = sanitize_email($_POST['admin_email']);
            $site_language = sanitize_text_field($_POST['idioma_sitio']);
            $site_url = esc_url_raw($_POST['url_sitio']);
            $disable_comments = isset($_POST['disable_comments']) ? intval($_POST['disable_comments']) : 0;
            $set_permalinks = isset($_POST['set_permalinks']) ? intval($_POST['set_permalinks']) : 0;
            $language_available = true;
            $language_changed = false;
            $user_locale_updates = array('updated' => 0, 'skipped' => 0);

            // Update site name
            if (!empty($site_name)) {
                update_option('blogname', $site_name);
            }

            // Update site tagline/description
            if (isset($_POST['site_tagline'])) {
                update_option('blogdescription', $site_tagline);
            }

            // Update admin email in options and admin user
            $admin_email_status = null;
            if (!empty($admin_email) && is_email($admin_email)) {
                $previous_admin_email = get_option('admin_email');
                $admin_email_status = $this->force_update_admin_email_option($admin_email, $previous_admin_email);

                // Update admin user's email (not just current user)
                $this->update_admin_user_email($admin_email, $previous_admin_email);
            }

            // Update site URL
            if (!empty($site_url)) {
                update_option('siteurl', $site_url);
                update_option('home', $site_url);
            }

            // Update language
            if (!empty($site_language)) {
                $previous_language = get_option('WPLANG');
                if (empty($previous_language)) {
                    $previous_language = get_locale();
                }

                $language_changed = ($site_language !== $previous_language);

                error_log('WP Fast Setup: Updating language to: ' . $site_language);

                // Update WPLANG option (this is the primary language setting)
                $old_lang = get_option('WPLANG');
                update_option('WPLANG', $site_language);
                error_log('WP Fast Setup: WPLANG updated from ' . $old_lang . ' to ' . $site_language);

                // For WordPress 4.0+, also update the locale
                update_option('locale', $site_language);

                // Force reload of text domains
                if (function_exists('unload_textdomain')) {
                    unload_textdomain('wp-fast-setup');
                    unload_textdomain('default');
                }

                // Force reload of default text domain
                if (function_exists('load_default_textdomain')) {
                    load_default_textdomain();
                }

                // Try to switch locale for current request
                if (function_exists('switch_to_locale')) {
                    $switched = switch_to_locale($site_language);
                    error_log('WP Fast Setup: switch_to_locale result: ' . ($switched ? 'true' : 'false'));
                }

                // Clear any cached translations
                if (function_exists('wp_cache_flush')) {
                    wp_cache_flush();
                }

                // Force WordPress to re-initialize locale
                global $wp_locale;
                if (isset($wp_locale) && method_exists($wp_locale, 'init')) {
                    $wp_locale->init();
                    error_log('WP Fast Setup: wp_locale->init() called');
                }

                // Try to download language pack if available
                if (!function_exists('wp_download_language_pack') && file_exists(ABSPATH . 'wp-admin/includes/translation-install.php')) {
                    require_once ABSPATH . 'wp-admin/includes/translation-install.php';
                }

                if (function_exists('wp_download_language_pack')) {
                    $download_result = wp_download_language_pack($site_language);
                    error_log('WP Fast Setup: Language pack download result: ' . ($download_result ? 'success' : 'failed'));
                }

                // Check if language files are available
                $language_available = ($site_language === 'es_ES') ? true : $this->is_language_available($site_language);
                error_log('WP Fast Setup: Language ' . $site_language . ' available: ' . ($language_available ? 'yes' : 'no'));

                error_log('WP Fast Setup: Language update completed for ' . $site_language);

                // Verify the language was actually saved
                $saved_lang = get_option('WPLANG');
                error_log('WP Fast Setup: Verification - saved language: ' . $saved_lang);

                // Sync admin/user locales so the dashboard reflects the change immediately
                $user_locale_updates = $this->sync_user_locales_with_site($site_language, $previous_language);
            }

            // Handle comments disabling
            if ($disable_comments) {
                // Close comments on all existing posts
                global $wpdb;
                $wpdb->query("UPDATE $wpdb->posts SET comment_status = 'closed' WHERE post_type = 'post'");

                // Set default comment status to closed for future posts
                update_option('default_comment_status', 'closed');

                // Disable comments on pages too
                update_option('default_page_comments', 0);

                // Close pingbacks/trackbacks
                update_option('default_ping_status', 'closed');
                update_option('default_pingback_flag', 0);
            }

            // Handle permalinks
            if ($set_permalinks) {
                update_option('permalink_structure', '/%postname%/');

                // Flush rewrite rules
                global $wp_rewrite;
                $wp_rewrite->flush_rules();
            }

            wp_send_json_success(array(
                'message' => 'Configuración del sitio guardada correctamente',
                'site_name_updated' => !empty($site_name),
                'admin_email_updated' => !empty($admin_email),
                'admin_email_status' => $admin_email_status,
                'site_url_updated' => !empty($site_url),
                'language_updated' => $language_changed,
                'language_available' => $language_available,
                'user_locale_updates' => $user_locale_updates,
                'comments_disabled' => $disable_comments,
                'permalinks_set' => $set_permalinks
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error al guardar configuración: ' . $e->getMessage());
        }
    }

    /**
     * AJAX handler for syncing Elementor site identity (logo & favicon)
     */
    public function ajax_save_elementor_branding()
    {
        $nonce_valid = false;
        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        } elseif (isset($_POST['wp_fast_setup_nonce_elementor_branding']) && wp_verify_nonce($_POST['wp_fast_setup_nonce_elementor_branding'], 'wp_fast_setup_action')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error('Invalid nonce');
        }

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        if (!did_action('elementor/loaded') || !class_exists('\Elementor\\Plugin')) {
            wp_send_json_error('Elementor no está activo en este sitio.');
            return;
        }

        if (!wp_doing_ajax() && !current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        $logo_id = isset($_POST['elementor_logo_id']) ? absint($_POST['elementor_logo_id']) : 0;
        $favicon_id = isset($_POST['elementor_favicon_id']) ? absint($_POST['elementor_favicon_id']) : 0;

        try {
            $kits_manager = \Elementor\Plugin::$instance->kits_manager ?? null;
            if (!$kits_manager || !method_exists($kits_manager, 'get_active_kit')) {
                wp_send_json_error('No se pudo acceder al kit activo de Elementor.');
                return;
            }

            $kit = $kits_manager->get_active_kit();
            if (!$kit && method_exists($kits_manager, 'get_active_kit_for_frontend')) {
                $kit = $kits_manager->get_active_kit_for_frontend();
            }

            if (!$kit) {
                wp_send_json_error('No se encontró un kit activo de Elementor para actualizar.');
                return;
            }

            if (!method_exists($kit, 'update_settings') || !method_exists($kit, 'get_settings')) {
                wp_send_json_error('El kit de Elementor no permite actualizar ajustes.');
                return;
            }

            $settings_to_update = array();
            $logo_url = '';
            $favicon_url = '';

            if ($logo_id > 0) {
                $logo_url = wp_get_attachment_url($logo_id);
                if (!$logo_url) {
                    wp_send_json_error('No se pudo encontrar el archivo del logo seleccionado.');
                    return;
                }

                $settings_to_update['site_logo'] = array(
                    'id' => $logo_id,
                    'url' => $logo_url,
                );
            } else {
                $settings_to_update['site_logo'] = array();
            }

            if ($favicon_id > 0) {
                $favicon_url = wp_get_attachment_url($favicon_id);
                if (!$favicon_url) {
                    wp_send_json_error('No se pudo encontrar el archivo de favicon seleccionado.');
                    return;
                }

                $settings_to_update['site_favicon'] = array(
                    'id' => $favicon_id,
                    'url' => $favicon_url,
                );
            } else {
                $settings_to_update['site_favicon'] = array();
            }

            if (!empty($settings_to_update)) {
                $kit->update_settings($settings_to_update);
            }

            wp_send_json_success(array(
                'message' => 'Identidad de Elementor actualizada correctamente.',
                'logo_id' => $logo_id,
                'favicon_id' => $favicon_id,
                'logo_url' => $logo_url,
                'favicon_url' => $favicon_url,
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error al actualizar Elementor: ' . $e->getMessage());
        }
    }

    /**
     * Get current site settings values
     */
    public function get_current_settings()
    {
        $stored_language = get_option('WPLANG');
        if (empty($stored_language)) {
            $stored_language = get_locale();
        }

        return array(
            'site_name' => get_option('blogname'),
            'language' => $stored_language,
            'site_url' => get_option('siteurl'),
            'admin_email' => get_option('admin_email'),
            'tagline' => get_option('blogdescription'),
            'logo_id' => get_option('wp_fast_setup_site_logo'),
            'favicon_id' => get_option('site_icon'),
            'blog_public' => get_option('blog_public', 1),
            'comment_status' => get_option('default_comment_status', 'open'),
            'permalink_structure' => get_option('permalink_structure', '')
        );
    }

    /**
     * Update the primary administrator user's email address
     */
    private function update_admin_user_email($new_email, $previous_email = '')
    {
        if (empty($new_email)) {
            return;
        }

        try {
            $user_to_update = false;

            if (!empty($previous_email)) {
                $user_to_update = get_user_by('email', $previous_email);
            }

            if (!$user_to_update) {
                $user_to_update = get_user_by('email', $new_email);
            }

            if (!$user_to_update) {
                $admins = get_users(array(
                    'role' => 'administrator',
                    'orderby' => 'ID',
                    'order' => 'ASC',
                    'number' => 1,
                ));

                if (!empty($admins)) {
                    $user_to_update = $admins[0];
                }
            }

            if ($user_to_update) {
                $existing_user_id = email_exists($new_email);
                if ($existing_user_id && intval($existing_user_id) !== intval($user_to_update->ID)) {
                    error_log('WP Fast Setup: Cannot update admin user email, address already in use by another user.');
                    return;
                }

                $result = wp_update_user(array(
                    'ID' => $user_to_update->ID,
                    'user_email' => $new_email,
                ));

                if (is_wp_error($result)) {
                    error_log('WP Fast Setup: Failed to update admin user email: ' . $result->get_error_message());
                } else {
                    error_log('WP Fast Setup: Admin user email updated to ' . $new_email);
                }
            } else {
                error_log('WP Fast Setup: No administrator user found to update email.');
            }
        } catch (Exception $e) {
            error_log('WP Fast Setup: Exception while updating admin user email: ' . $e->getMessage());
        }
    }

    /**
     * Force updates the admin_email option bypassing WordPress confirmation flow.
     *
     * @param string      $new_email     The email to set.
     * @param string|null $previous_email The previous admin email if already known.
     *
     * @return array Result payload with success flag and contextual info.
     */
    private function force_update_admin_email_option($new_email, $previous_email = null)
    {
        global $wpdb;

        if (empty($new_email) || !is_email($new_email)) {
            return array(
                'success' => false,
                'message' => __('Dirección de correo no válida.', 'fast-wp-setup'),
            );
        }

        if ($previous_email === null) {
            $previous_email = get_option('admin_email');
        }

        if ($previous_email === $new_email) {
            return array(
                'success' => true,
                'message'  => __('El correo ya estaba configurado.', 'fast-wp-setup'),
                'previous' => $previous_email,
                'current'  => $new_email,
            );
        }

        $updated = $wpdb->update(
            $wpdb->options,
            array('option_value' => $new_email),
            array('option_name'  => 'admin_email')
        );

        if ($updated === false) {
            return array(
                'success' => false,
                'message'  => __('No se pudo actualizar el correo de administrador.', 'fast-wp-setup'),
                'previous' => $previous_email,
            );
        }

        delete_option('adminhash');
        delete_option('new_admin_email');
        wp_cache_delete('alloptions', 'options');

        return array(
            'success' => true,
            'message'  => __('Correo de administrador actualizado directamente.', 'fast-wp-setup'),
            'previous' => $previous_email,
            'current'  => $new_email,
        );
    }
    /**
     * Synchronize administrator/user locales with the selected site locale so the dashboard reflects the change immediately
     */
    private function sync_user_locales_with_site($new_locale, $previous_locale = '')
    {
        $result = array(
            'updated' => 0,
            'skipped' => 0,
        );

        if (empty($new_locale)) {
            return $result;
        }

        $user_ids = array();

        $admin_users = get_users(array(
            'role__in' => array('administrator'),
            'fields' => 'ID'
        ));

        if (!empty($admin_users)) {
            foreach ($admin_users as $admin_id) {
                $user_ids[intval($admin_id)] = true;
            }
        }

        $current_user_id = get_current_user_id();
        if ($current_user_id) {
            $user_ids[intval($current_user_id)] = true;
        }

        if (empty($user_ids)) {
            return $result;
        }

        foreach (array_keys($user_ids) as $user_id) {
            $existing_locale = get_user_meta($user_id, 'locale', true);

            if (!empty($existing_locale) && !empty($previous_locale) && $existing_locale !== $previous_locale) {
                $result['skipped']++;
                continue;
            }

            $updated = update_user_meta($user_id, 'locale', $new_locale);
            if ($updated !== false) {
                $result['updated']++;
            }
        }

        return $result;
    }

    /**
     * Get available languages
     */
    public function get_available_languages()
    {
        return array(
            'en_US' => 'English',
            'es_ES' => 'Español',
            'fr_FR' => 'Français',
            'de_DE' => 'Deutsch',
            'it_IT' => 'Italiano',
            'pt_PT' => 'Português',
            'pt_BR' => 'Português (Brasil)',
            'ru_RU' => 'Русский',
            'ja' => '日本語',
            'zh_CN' => '中文 (简体)',
            'zh_TW' => '中文 (繁體)',
            'ar' => 'العربية',
            'hi_IN' => 'हिन्दी'
        );
    }

    /**
     * Get language name by code
     */
    public function get_language_name($lang_code)
    {
        $languages = $this->get_available_languages();
        return isset($languages[$lang_code]) ? $languages[$lang_code] : $lang_code;
    }

    /**
     * Check if language files are available for a given locale
     */
    private function is_language_available($locale)
    {
        try {
            if ($locale === 'en_US') {
                return true;
            }

            // Get WordPress language directory
            $lang_dir = '';

            if (function_exists('wp_lang_dir')) {
                $lang_dir = wp_lang_dir();
            } elseif (defined('WP_CONTENT_DIR')) {
                $lang_dir = trailingslashit(WP_CONTENT_DIR) . 'languages/';
            } elseif (defined('ABSPATH')) {
                $lang_dir = trailingslashit(ABSPATH . 'wp-content') . 'languages/';
            }

            if (empty($lang_dir)) {
                return false;
            }

            $lang_dir = trailingslashit($lang_dir);

            // Check if .mo file exists for this locale
            $mo_file = $lang_dir . 'wordpress-' . $locale . '.mo';
            if (file_exists($mo_file)) {
                return true;
            }

            // Check if admin .mo file exists
            $admin_mo_file = $lang_dir . 'wordpress-admin-' . $locale . '.mo';
            if (file_exists($admin_mo_file)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            error_log('WP Fast Setup: Error checking language availability: ' . $e->getMessage());
            return false;
        }
    }
}
