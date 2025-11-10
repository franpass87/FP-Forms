/**
 * FP Forms - File Upload Enhancement
 * Gestisce preview e validazione file upload lato client
 */

(function($) {
    'use strict';
    
    var FPFileUpload = {
        
        /**
         * Inizializza
         */
        init: function() {
            this.bindEvents();
        },
        
        /**
         * Bind eventi
         */
        bindEvents: function() {
            $(document).on('change', '.fp-forms-file-input', this.handleFileSelect);
            $(document).on('click', '.fp-forms-file-remove', this.handleFileRemove);
        },
        
        /**
         * Gestisce selezione file
         */
        handleFileSelect: function(e) {
            var $input = $(this);
            var $container = $input.closest('.fp-forms-field-file');
            var $preview = $container.find('.fp-forms-file-preview');
            var $error = $container.find('.fp-forms-error');
            var files = this.files;
            var maxSize = parseInt($input.data('max-size')) || 5242880; // 5MB default
            
            // Reset error
            $error.hide().text('');
            $container.removeClass('has-error');
            
            // Validazione
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                
                // Check dimensione
                if (file.size > maxSize) {
                    var maxMB = Math.round(maxSize / 1024 / 1024);
                    $error.text('File troppo grande. Max: ' + maxMB + 'MB').show();
                    $container.addClass('has-error');
                    $input.val('');
                    return;
                }
            }
            
            // Mostra preview
            if (files.length > 0) {
                FPFileUpload.showPreview($preview, files);
            }
        },
        
        /**
         * Mostra preview file
         */
        showPreview: function($preview, files) {
            $preview.empty().show();
            
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var icon = FPFileUpload.getFileIcon(file.type);
                var size = FPFileUpload.formatFileSize(file.size);
                
                var $item = $('<div class="fp-forms-file-preview-item">');
                $item.append('<span class="fp-forms-file-preview-icon">' + icon + '</span>');
                $item.append('<span class="fp-forms-file-preview-name">' + file.name + '</span>');
                $item.append('<span class="fp-forms-file-preview-size">' + size + '</span>');
                $item.append('<button type="button" class="fp-forms-file-remove" data-index="' + i + '">×</button>');
                
                $preview.append($item);
            }
        },
        
        /**
         * Gestisce rimozione file
         */
        handleFileRemove: function(e) {
            e.preventDefault();
            var $container = $(this).closest('.fp-forms-field-file');
            var $input = $container.find('.fp-forms-file-input');
            var $preview = $container.find('.fp-forms-file-preview');
            
            // Reset input
            $input.val('');
            $preview.empty().hide();
        },
        
        /**
         * Ottiene icona file in base al tipo
         */
        getFileIcon: function(mimeType) {
            if (mimeType.startsWith('image/')) {
                return '🖼️';
            } else if (mimeType === 'application/pdf') {
                return '📄';
            } else if (mimeType.includes('word') || mimeType.includes('document')) {
                return '📝';
            } else if (mimeType.includes('sheet') || mimeType.includes('excel')) {
                return '📊';
            } else if (mimeType.includes('zip') || mimeType.includes('compressed')) {
                return '📦';
            } else {
                return '📎';
            }
        },
        
        /**
         * Formatta dimensione file
         */
        formatFileSize: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    };
    
    // Inizializza
    $(document).ready(function() {
        FPFileUpload.init();
    });
    
})(jQuery);

