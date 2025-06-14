<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Trashify {
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = TRASHIFY_VERSION;
        $this->plugin_name = 'trashify';
        
        $this->load_dependencies();
        $this->define_admin_hooks();
    }

    private function load_dependencies() {
        $loader_file = TRASHIFY_PLUGIN_DIR . 'includes/class-trashify-loader.php';
        $admin_file = TRASHIFY_PLUGIN_DIR . 'admin/class-trashify-admin.php';

        if (!file_exists($loader_file)) {
            wp_die(esc_html__('Erro: Arquivo de loader não encontrado.', 'trashify-image-deletion'));
        }

        if (!file_exists($admin_file)) {
            wp_die(esc_html__('Erro: Arquivo de admin não encontrado.', 'trashify-image-deletion'));
        }

        require_once $loader_file;
        require_once $admin_file;
        
        $this->loader = new Trashify_Loader();
    }

    private function define_admin_hooks() {
        $plugin_admin = new Trashify_Admin($this->get_plugin_name(), $this->get_version());

        // Add menu items
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        
        // Add admin scripts and styles
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        // Add AJAX handlers
        $this->loader->add_action('wp_ajax_trashify_delete_media', $plugin_admin, 'ajax_delete_media');
        $this->loader->add_action('wp_ajax_trashify_get_media', $plugin_admin, 'ajax_get_media');
        $this->loader->add_action('wp_ajax_trashify_delete_all_media', $plugin_admin, 'ajax_delete_all_media');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }
} 