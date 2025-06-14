<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://developers.prollabe.com/
 * @since      1.0.0
 *
 * @package    Trashify
 * @subpackage Trashify/admin/partials
 * @charset    UTF-8
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Sair se acessado diretamente
}
?>

<div class="wrap trashify-admin">
    <h1><?php echo esc_html__('Trashify - Image Deletion', 'trashify-image-deletion'); ?></h1>
    
    <div class="trashify-filters">
        <select id="trashify-author-filter">
            <option value=""><?php echo esc_html__('Todos os autores', 'trashify-image-deletion'); ?></option>
            <?php
            $authors = get_users(array('who' => 'authors'));
            foreach ($authors as $author) {
                printf(
                    '<option value="%s">%s</option>',
                    esc_attr($author->ID),
                    esc_html($author->display_name)
                );
            }
            ?>
        </select>
        
        <div class="trashify-buttons">
            <button id="trashify-delete-selected" class="button button-primary" disabled>
                <?php echo esc_html__('Excluir Selecionados', 'trashify-image-deletion'); ?>
            </button>
            
            <button id="trashify-delete-all" class="button button-danger">
                <?php echo esc_html__('Excluir Todos', 'trashify-image-deletion'); ?>
            </button>
        </div>
    </div>

    <div class="trashify-loading" style="display: none;">
        <?php echo esc_html__('Carregando...', 'trashify-image-deletion'); ?>
    </div>

    <div class="trashify-grid">
        <div class="trashify-images">
            <!-- As imagens serão carregadas via AJAX -->
        </div>

        <div class="trashify-pagination">
            <button class="button" id="trashify-prev-page" disabled>
                <?php echo esc_html__('Anterior', 'trashify-image-deletion'); ?>
            </button>
            <span class="trashify-page-info">
                <?php echo esc_html__('Página', 'trashify-image-deletion'); ?> <span id="trashify-current-page">1</span>
                <?php echo esc_html__('de', 'trashify-image-deletion'); ?> <span id="trashify-total-pages">1</span>
            </span>
            <button class="button" id="trashify-next-page" disabled>
                <?php echo esc_html__('Próxima', 'trashify-image-deletion'); ?>
            </button>
        </div>
    </div>

    <!-- Diálogo de confirmação -->
    <div id="trashify-confirm-dialog" style="display: none;" class="trashify-dialog">
        <div class="trashify-dialog-content">
            <h3><?php echo esc_html__('Confirmar Exclusão', 'trashify-image-deletion'); ?></h3>
            <p class="trashify-dialog-message"></p>
            <div class="trashify-dialog-buttons">
                <button id="trashify-confirm-delete" class="button button-primary">
                    <?php echo esc_html__('Confirmar', 'trashify-image-deletion'); ?>
                </button>
                <button id="trashify-cancel-delete" class="button">
                    <?php echo esc_html__('Cancelar', 'trashify-image-deletion'); ?>
                </button>
            </div>
        </div>
    </div>
</div> 