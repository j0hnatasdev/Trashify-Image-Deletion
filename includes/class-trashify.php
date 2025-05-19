<?php

class Trashify {
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = TRASHIFY_VERSION;
        $this->plugin_name = 'trashify-exclusao-de-imagens';
        
        $this->load_dependencies();
        $this->define_admin_hooks();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trashify-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-trashify-admin.php';
        
        $this->loader = new Trashify_Loader();
    }

    private function define_admin_hooks() {
        $plugin_admin = new Trashify_Admin($this->get_plugin_name(), $this->get_version());

        // Add menu items
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        
        // Add admin scripts and styles
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        // Add AJAX handlers
        $this->loader->add_action('wp_ajax_trashify_load_media', $plugin_admin, 'ajax_load_media');
        $this->loader->add_action('wp_ajax_trashify_delete_media', $plugin_admin, 'ajax_delete_media');
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