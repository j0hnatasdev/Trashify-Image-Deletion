<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get all users with upload capabilities
$users = get_users(array(
    'who' => 'authors',
    'orderby' => 'display_name'
));
?>

<div class="wrap trashify-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="trashify-filters">
        <select id="trashify-author-filter">
            <option value=""><?php esc_html_e('Todos os autores', 'trashify-exclusao-de-imagens'); ?></option>
            <?php foreach ($users as $user) : ?>
                <option value="<?php echo esc_attr($user->ID); ?>">
                    <?php echo esc_html($user->display_name); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="trashify-buttons">
            <button id="trashify-delete-selected" class="button button-danger" disabled>
                <?php esc_html_e('Excluir Selecionadas', 'trashify-exclusao-de-imagens'); ?>
            </button>
            <button id="trashify-delete-all" class="button button-danger">
                <?php esc_html_e('Excluir Todas', 'trashify-exclusao-de-imagens'); ?>
            </button>
        </div>
    </div>

    <div class="trashify-grid">
        <div class="trashify-loading">
            <span class="spinner is-active"></span>
            <?php esc_html_e('Carregando imagens...', 'trashify-exclusao-de-imagens'); ?>
        </div>
        <div class="trashify-images"></div>
    </div>

    <div class="trashify-pagination">
        <button class="button" id="trashify-prev-page" disabled>
            <?php esc_html_e('Anterior', 'trashify-exclusao-de-imagens'); ?>
        </button>
        <span class="trashify-page-info">
            <?php esc_html_e('Página', 'trashify-exclusao-de-imagens'); ?> <span id="trashify-current-page">1</span>
            <?php esc_html_e('de', 'trashify-exclusao-de-imagens'); ?> <span id="trashify-total-pages">1</span>
        </span>
        <button class="button" id="trashify-next-page" disabled>
            <?php esc_html_e('Próxima', 'trashify-exclusao-de-imagens'); ?>
        </button>
    </div>

    <div id="trashify-confirm-dialog" class="trashify-dialog" style="display: none;">
        <div class="trashify-dialog-content">
            <h2><?php esc_html_e('Confirmar Exclusão', 'trashify-exclusao-de-imagens'); ?></h2>
            <p class="trashify-dialog-message"></p>
            <div class="trashify-dialog-buttons">
                <button class="button" id="trashify-cancel-delete">
                    <?php esc_html_e('Cancelar', 'trashify-exclusao-de-imagens'); ?>
                </button>
                <button class="button button-primary" id="trashify-confirm-delete">
                    <?php esc_html_e('Excluir', 'trashify-exclusao-de-imagens'); ?>
                </button>
            </div>
        </div>
    </div>
</div> 