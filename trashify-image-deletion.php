<?php
/**
 * Plugin Name: Trashify - Image Deletion
 * Plugin URI: https://github.com/j0hnatasdev/Trashify-Image-Deletion
 * Description: Facilita a exclusão de mídias diretamente da biblioteca do WordPress, de forma segura, organizada e seletiva.
 * Version: 1.0.2
 * Author: Prollabe Developers
 * Author URI: https://developers.prollabe.com/
 * Text Domain: trashify-image-deletion
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Charset: UTF-8
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TRASHIFY_VERSION', '1.0.2');
define('TRASHIFY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRASHIFY_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once TRASHIFY_PLUGIN_DIR . 'includes/class-trashify.php';

// Initialize the plugin
function trashify_init() {
    $plugin = new Trashify();
    $plugin->run();
}
add_action('plugins_loaded', 'trashify_init');

// Activation hook
register_activation_hook(__FILE__, 'trashify_activate');
function trashify_activate() {
    // Activation tasks if needed
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'trashify_deactivate');
function trashify_deactivate() {
    // Deactivation tasks if needed
} 