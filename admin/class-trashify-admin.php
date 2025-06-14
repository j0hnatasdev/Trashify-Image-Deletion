<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Trashify_Admin {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        $css_file = TRASHIFY_PLUGIN_DIR . 'admin/css/trashify-admin.css';
        if (file_exists($css_file)) {
            wp_enqueue_style(
                $this->plugin_name,
                TRASHIFY_PLUGIN_URL . 'admin/css/trashify-admin.css',
                array(),
                $this->version,
                'all'
            );
        }
    }

    public function enqueue_scripts() {
        $js_file = TRASHIFY_PLUGIN_DIR . 'admin/js/trashify-admin.js';
        if (file_exists($js_file)) {
            wp_enqueue_script(
                $this->plugin_name,
                TRASHIFY_PLUGIN_URL . 'admin/js/trashify-admin.js',
                array('jquery'),
                $this->version,
                false
            );

            wp_localize_script($this->plugin_name, 'trashify_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('trashify_nonce'),
                'confirm_delete' => esc_html__('Tem certeza que deseja excluir as imagens selecionadas?', 'trashify-image-deletion')
            ));
        }
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            esc_html__('Trashify', 'trashify-image-deletion'),
            esc_html__('Trashify', 'trashify-image-deletion'),
            'manage_options',
            'trashify',
            array($this, 'display_plugin_admin_page'),
            'dashicons-trash',
            81
        );
    }

    public function display_plugin_admin_page() {
        if (!current_user_can('edit_posts')) {
            wp_die(esc_html__('Você não tem permissão para acessar esta página.', 'trashify-image-deletion'));
        }

        $template_file = TRASHIFY_PLUGIN_DIR . 'admin/partials/trashify-admin-display.php';
        if (file_exists($template_file)) {
            include_once $template_file;
        } else {
            wp_die(esc_html__('Erro: Template não encontrado.', 'trashify-image-deletion'));
        }
    }

    public function ajax_get_media() {
        check_ajax_referer('trashify_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Permissão negada');
        }

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $author = isset($_POST['author']) ? intval($_POST['author']) : 0;
        $per_page = 20;

        $args = array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => $per_page,
            'paged' => $page,
            'post_mime_type' => 'image'
        );

        if ($author > 0) {
            $args['author'] = $author;
        }

        $query = new WP_Query($args);
        $images = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $attachment_id = get_the_ID();
                $images[] = array(
                    'id' => $attachment_id,
                    'title' => get_the_title(),
                    'url' => wp_get_attachment_thumb_url($attachment_id),
                    'author' => get_the_author(),
                    'date' => get_the_date()
                );
            }
        }

        wp_reset_postdata();

        wp_send_json_success(array(
            'images' => $images,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages
        ));
    }

    public function ajax_delete_media() {
        check_ajax_referer('trashify_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Permissão negada');
        }

        $attachment_ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : array();

        if (empty($attachment_ids)) {
            wp_send_json_error('Nenhuma imagem selecionada');
        }

        $deleted = array();
        $failed = array();

        foreach ($attachment_ids as $attachment_id) {
            if (wp_delete_attachment($attachment_id, true)) {
                $deleted[] = $attachment_id;
            } else {
                $failed[] = $attachment_id;
            }
        }

        wp_send_json_success(array(
            'deleted' => $deleted,
            'failed' => $failed
        ));
    }

    public function ajax_delete_all_media() {
        check_ajax_referer('trashify_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Permissão negada');
        }

        $author = isset($_POST['author']) ? intval($_POST['author']) : 0;

        $args = array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => -1,
            'post_mime_type' => 'image'
        );

        if ($author > 0) {
            $args['author'] = $author;
        }

        $query = new WP_Query($args);
        $deleted = array();
        $failed = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $attachment_id = get_the_ID();
                if (wp_delete_attachment($attachment_id, true)) {
                    $deleted[] = $attachment_id;
                } else {
                    $failed[] = $attachment_id;
                }
            }
        }

        wp_reset_postdata();

        wp_send_json_success(array(
            'deleted' => $deleted,
            'failed' => $failed
        ));
    }
} 