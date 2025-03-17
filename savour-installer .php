<?php
/**
 * Plugin Name: Savour Manager v.1
 *Description: Configura el sitio, instala plugins habituales, crea páginas básicas. Carga JSON para header, footer, single y abogado desde archivos .json externos, con metadatos extra para reconocer plantillas (header, footer, single-post, etc.). Incluye parche: elimina _elementor_pro_version si solo está Pro Elements.
 *Version: 3.0
 *Author: Alex Parra
 * Text Domain: savour-manager
 * @package Savour_Manager
 * @wordpress-plugin
 */

// Evitar acceso directo
if (!defined('ABSPATH')) exit;

// Load core components
/* require_once plugin_dir_path(__FILE__) . 'includes/admin/class-admin-pages.php'; */
require_once plugin_dir_path(__FILE__) . 'includes/admin/admin-pages.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/styles.php'; // Add this line
require_once plugin_dir_path(__FILE__) . 'includes/admin/plugins.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/templates.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/users.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/pages.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/styles.php';

// Initialize main menu
new Admin_Pages();