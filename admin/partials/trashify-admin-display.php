<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://prolldevs.com.br
 * @since      1.0.0
 *
 * @package    Trashify
 * @subpackage Trashify/admin/partials
 */
?>

<div class="wrap trashify-admin">
    <h1><?php echo esc_html__('Trashify - Exclusão de Imagens', 'trashify-image-deletion'); ?></h1>
    
    <div class="trashify-filters">
        <select id="author-filter">
            <option value=""><?php echo esc_html__('Todos os autores', 'trashify-image-deletion'); ?></option>
            <?php
            $authors = get_users(array('who' => 'authors'));
            foreach ($authors as $author) {
                echo '<option value="' . esc_attr($author->ID) . '">' . esc_html($author->display_name) . '</option>';
            }
            ?>
        </select>
        
        <div class="trashify-buttons">
            <button id="delete-selected" class="button button-primary">
                <?php echo esc_html__('Excluir Selecionados', 'trashify-image-deletion'); ?>
            </button>
            
            <button id="delete-all" class="button button-danger">
                <?php echo esc_html__('Excluir Todos', 'trashify-image-deletion'); ?>
            </button>
        </div>
    </div>

    <div class="trashify-grid">
        <div class="trashify-images">
            <?php
            $args = array(
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'post_status' => 'inherit',
                'posts_per_page' => -1,
            );
            
            $images = new WP_Query($args);
            
            if ($images->have_posts()) :
                while ($images->have_posts()) : $images->the_post();
                    ?>
                    <div class="trashify-image-item">
                        <input type="checkbox" class="trashify-image-checkbox" value="<?php echo esc_attr(get_the_ID()); ?>">
                        <img src="<?php echo esc_url(wp_get_attachment_image_url(get_the_ID(), 'thumbnail')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="trashify-image-preview">
                        <div class="trashify-image-info">
                            <div class="trashify-image-title"><?php echo esc_html(get_the_title()); ?></div>
                            <div class="trashify-image-meta"><?php echo esc_html(get_the_author()); ?></div>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>' . esc_html__('Nenhuma imagem encontrada.', 'trashify-image-deletion') . '</p>';
            endif;
            ?>
        </div>
    </div>
</div> 