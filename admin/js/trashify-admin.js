jQuery(document).ready(function($) {
    let currentPage = 1;
    let totalPages = 1;
    let selectedImages = new Set();
    let currentAuthor = 0;
    let totalImages = 0;

    // Initialize the interface
    function init() {
        loadImages();
        setupEventListeners();
    }

    // Load images from the server
    function loadImages() {
        $('.trashify-loading').show();
        $('.trashify-images').empty();

        $.ajax({
            url: trashify_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'trashify_get_media',
                nonce: trashify_ajax.nonce,
                page: currentPage,
                author: currentAuthor
            },
            success: function(response) {
                if (response.success) {
                    displayImages(response.data.images);
                    updatePagination(response.data.total, response.data.pages);
                    totalImages = response.data.total;
                } else {
                    showError('Erro ao carregar imagens');
                }
            },
            error: function() {
                showError('Erro ao carregar imagens');
            },
            complete: function() {
                $('.trashify-loading').hide();
            }
        });
    }

    // Display images in the grid
    function displayImages(images) {
        const $container = $('.trashify-images');
        
        images.forEach(function(image) {
            const $item = $(`
                <div class="trashify-image-item" data-id="${image.id}">
                    <input type="checkbox" class="trashify-image-checkbox" ${selectedImages.has(image.id) ? 'checked' : ''}>
                    <img src="${image.url}" alt="${image.title}" class="trashify-image-preview">
                    <div class="trashify-image-info">
                        <div class="trashify-image-title">${image.title}</div>
                        <div class="trashify-image-meta">
                            ${image.author} - ${image.date}
                        </div>
                    </div>
                </div>
            `);
            
            $container.append($item);
        });
    }

    // Update pagination controls
    function updatePagination(total, pages) {
        totalPages = pages;
        $('#trashify-current-page').text(currentPage);
        $('#trashify-total-pages').text(totalPages);
        
        $('#trashify-prev-page').prop('disabled', currentPage <= 1);
        $('#trashify-next-page').prop('disabled', currentPage >= totalPages);
    }

    // Setup event listeners
    function setupEventListeners() {
        // Author filter change
        $('#trashify-author-filter').on('change', function() {
            currentAuthor = parseInt($(this).val());
            currentPage = 1;
            selectedImages.clear();
            loadImages();
        });

        // Pagination
        $('#trashify-prev-page').on('click', function() {
            if (currentPage > 1) {
                currentPage--;
                loadImages();
            }
        });

        $('#trashify-next-page').on('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                loadImages();
            }
        });

        // Image selection
        $(document).on('change', '.trashify-image-checkbox', function() {
            const imageId = $(this).closest('.trashify-image-item').data('id');
            if (this.checked) {
                selectedImages.add(imageId);
            } else {
                selectedImages.delete(imageId);
            }
            updateDeleteButton();
        });

        // Delete selected images
        $('#trashify-delete-selected').on('click', function() {
            if (selectedImages.size > 0) {
                showConfirmDialog('selected');
            }
        });

        // Delete all images
        $('#trashify-delete-all').on('click', function() {
            if (totalImages > 0) {
                showConfirmDialog('all');
            }
        });

        // Confirm delete
        $('#trashify-confirm-delete').on('click', function() {
            const deleteType = $(this).data('delete-type');
            if (deleteType === 'all') {
                deleteAllImages();
            } else {
                deleteSelectedImages();
            }
        });

        // Cancel delete
        $('#trashify-cancel-delete').on('click', function() {
            $('#trashify-confirm-dialog').hide();
        });
    }

    // Show confirmation dialog
    function showConfirmDialog(type) {
        const $dialog = $('#trashify-confirm-dialog');
        const $message = $dialog.find('.trashify-dialog-message');
        const $confirmButton = $('#trashify-confirm-delete');

        if (type === 'all') {
            $message.text('Tem certeza que deseja excluir todas as imagens? Esta ação não pode ser desfeita.');
            $confirmButton.data('delete-type', 'all');
        } else {
            $message.text('Tem certeza que deseja excluir as imagens selecionadas? Esta ação não pode ser desfeita.');
            $confirmButton.data('delete-type', 'selected');
        }

        $dialog.show();
    }

    // Update delete button state
    function updateDeleteButton() {
        $('#trashify-delete-selected').prop('disabled', selectedImages.size === 0);
        $('#trashify-delete-all').prop('disabled', totalImages === 0);
    }

    // Delete selected images
    function deleteSelectedImages() {
        const imageIds = Array.from(selectedImages);
        
        $.ajax({
            url: trashify_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'trashify_delete_media',
                nonce: trashify_ajax.nonce,
                ids: imageIds
            },
            success: function(response) {
                if (response.success) {
                    selectedImages.clear();
                    $('#trashify-confirm-dialog').hide();
                    loadImages();
                } else {
                    showError('Erro ao excluir imagens');
                }
            },
            error: function() {
                showError('Erro ao excluir imagens');
            }
        });
    }

    // Delete all images
    function deleteAllImages() {
        $.ajax({
            url: trashify_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'trashify_delete_all_media',
                nonce: trashify_ajax.nonce,
                author: currentAuthor
            },
            success: function(response) {
                if (response.success) {
                    selectedImages.clear();
                    $('#trashify-confirm-dialog').hide();
                    loadImages();
                } else {
                    showError('Erro ao excluir imagens');
                }
            },
            error: function() {
                showError('Erro ao excluir imagens');
            }
        });
    }

    // Show error message
    function showError(message) {
        const $error = $(`<div class="notice notice-error"><p>${message}</p></div>`);
        $('.trashify-admin').prepend($error);
        setTimeout(function() {
            $error.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Initialize the plugin
    init();
}); 