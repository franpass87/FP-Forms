/**
 * FP Forms Modal Confirm Dialog
 * Sostituisce alert() e confirm() con modal moderni
 */

(function($) {
    'use strict';
    
    /**
     * Mostra modal di conferma
     * @param {string} message - Messaggio da mostrare
     * @param {Object} options - Opzioni (title, confirmText, cancelText, onConfirm, onCancel)
     * @returns {jQuery} - Modal jQuery element
     */
    window.fpConfirm = function(message, options) {
        options = options || {};
        
        var defaults = {
            title: 'Conferma',
            message: message,
            confirmText: 'Conferma',
            cancelText: 'Annulla',
            confirmClass: 'button-primary',
            cancelClass: 'button-secondary',
            onConfirm: null,
            onCancel: null,
            showCancel: true
        };
        
        var settings = $.extend({}, defaults, options);
        
        // Crea modal HTML
        var modalHtml = '<div class="fp-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="fp-modal-title">' +
            '<div class="fp-modal">' +
                '<div class="fp-modal-header">' +
                    '<h3 id="fp-modal-title" class="fp-modal-title">' + settings.title + '</h3>' +
                    '<button class="fp-modal-close" aria-label="Chiudi">&times;</button>' +
                '</div>' +
                '<div class="fp-modal-body">' +
                    '<p class="fp-modal-message"></p>' +
                '</div>' +
                '<div class="fp-modal-footer">';
        
        if (settings.showCancel) {
            modalHtml += '<button class="fp-modal-btn fp-modal-cancel ' + settings.cancelClass + '">' + 
                settings.cancelText + '</button>';
        }
        
        modalHtml += '<button class="fp-modal-btn fp-modal-confirm ' + settings.confirmClass + '">' + 
            settings.confirmText + '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        var $modal = $(modalHtml);
        $('body').append($modal);
        
        // Sanitizza e inserisci messaggio (prevenzione XSS)
        var $messageEl = $modal.find('.fp-modal-message');
        $messageEl.text(settings.message); // .text() invece di .html() previene XSS
        
        // Animazione entrata
        setTimeout(function() {
            $modal.addClass('fp-modal-show');
        }, 10);
        
        // Focus trap
        var $focusable = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        var $firstFocusable = $focusable.first();
        var $lastFocusable = $focusable.last();
        
        // Focus su primo elemento
        $firstFocusable.focus();
        
        // Keyboard navigation
        $modal.on('keydown', function(e) {
            // ESC per chiudere
            if (e.key === 'Escape' || e.keyCode === 27) {
                closeModal();
                return;
            }
            
            // Tab trap
            if (e.key === 'Tab' || e.keyCode === 9) {
                if (e.shiftKey) {
                    if (document.activeElement === $firstFocusable[0]) {
                        e.preventDefault();
                        $lastFocusable.focus();
                    }
                } else {
                    if (document.activeElement === $lastFocusable[0]) {
                        e.preventDefault();
                        $firstFocusable.focus();
                    }
                }
            }
        });
        
        // Click overlay per chiudere
        $modal.on('click', '.fp-modal-overlay', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Click close button
        $modal.on('click', '.fp-modal-close, .fp-modal-cancel', function() {
            closeModal();
            if (settings.onCancel && typeof settings.onCancel === 'function') {
                settings.onCancel();
            }
        });
        
        // Click confirm
        $modal.on('click', '.fp-modal-confirm', function() {
            closeModal();
            if (settings.onConfirm && typeof settings.onConfirm === 'function') {
                settings.onConfirm();
            }
        });
        
        function closeModal() {
            $modal.removeClass('fp-modal-show');
            setTimeout(function() {
                $modal.remove();
            }, 300);
        }
        
        return $modal;
    };
    
    /**
     * Shorthand per confirm semplice (compatibilità con confirm())
     * @param {string} message - Messaggio
     * @returns {Promise} - Promise che risolve con true/false
     */
    window.fpConfirmPromise = function(message) {
        return new Promise(function(resolve) {
            window.fpConfirm(message, {
                onConfirm: function() {
                    resolve(true);
                },
                onCancel: function() {
                    resolve(false);
                }
            });
        });
    };
    
})(jQuery);

