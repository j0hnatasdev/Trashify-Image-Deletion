<?php

class Trashify_Admin {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/trashify-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/trashify-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        wp_localize_script($this->plugin_name, 'trashify_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('trashify_nonce'),
            'confirm_delete' => __('Tem certeza que deseja excluir as imagens selecionadas?', 'trashify-exclusao-de-imagens'),
            'confirm_delete_all' => __('Tem certeza que deseja excluir todas as imagens? Esta ação não pode ser desfeita.', 'trashify-exclusao-de-imagens'),
            'error_message' => __('Ocorreu um erro ao processar sua solicitação.', 'trashify-exclusao-de-imagens'),
            'success_message' => __('Imagens excluídas com sucesso!', 'trashify-exclusao-de-imagens')
        ));
    }

    public function add_admin_menu() {
        add_media_page(
            __('Trashify - Exclusão de Imagens', 'trashify-exclusao-de-imagens'),
            __('Trashify', 'trashify-exclusao-de-imagens'),
            'upload_files',
            'trashify',
            array($this, 'display_admin_page')
        );
    }

    public function display_admin_page() {
        require_once plugin_dir_path(__FILE__) . 'partials/trashify-admin-display.php';
    }

    public function ajax_load_media() {
        check_ajax_referer('trashify_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(__('Você não tem permissão para acessar esta página.', 'trashify-exclusao-de-imagens'));
        }

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $author = isset($_POST['author']) ? intval($_POST['author']) : 0;
        $per_page = 20;

        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'post_status' => 'inherit',
            'posts_per_page' => $per_page,
            'paged' => $page,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        if ($author > 0) {
            $args['author'] = $author;
        }

        $query = new WP_Query($args);
        $images = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $image = wp_get_attachment_image_src(get_the_ID(), 'thumbnail');
                if ($image) {
                    $images[] = array(
                        'id' => get_the_ID(),
                        'url' => $image[0],
                        'title' => get_the_title(),
                        'author' => get_the_author()
                    );
                }
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

        if (!current_user_can('upload_files')) {
            wp_send_json_error(__('Você não tem permissão para excluir mídias.', 'trashify-exclusao-de-imagens'));
        }

        $image_ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : array();
        $deleted = array();
        $failed = array();

        foreach ($image_ids as $id) {
            if (wp_delete_attachment($id, true)) {
                $deleted[] = $id;
            } else {
                $failed[] = $id;
            }
        }

        wp_send_json_success(array(
            'deleted' => $deleted,
            'failed' => $failed
        ));
    }

    public function ajax_delete_all_media() {
        check_ajax_referer('trashify_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(__('Você não tem permissão para excluir mídias.', 'trashify-exclusao-de-imagens'));
        }

        $author = isset($_POST['author']) ? intval($_POST['author']) : 0;

        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'post_status' => 'inherit',
            'posts_per_page' => -1
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
                $id = get_the_ID();
                if (wp_delete_attachment($id, true)) {
                    $deleted[] = $id;
                } else {
                    $failed[] = $id;
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