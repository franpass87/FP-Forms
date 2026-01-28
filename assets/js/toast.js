/**
 * FP Forms Toast Notifications
 * Sostituisce gli alert() con toast moderni
 */

(function($) {
    'use strict';
    
    // Crea container toast se non esiste
    function ensureToastContainer() {
        if (!$('#fp-toast-container').length) {
            // Aggiungi ARIA live region per screen readers
            $('body').append('<div id="fp-toast-container" class="fp-toast-container" role="status" aria-live="polite" aria-atomic="true"></div>');
        }
    }
    
    /**
     * Mostra toast notification
     * @param {string} message - Messaggio da mostrare
     * @param {string} type - success|error|warning|info
     * @param {number} duration - Durata in ms (0 = permanente)
     */
    window.fpToast = function(message, type, duration) {
        type = type || 'info';
        duration = duration || 3000;
        
        ensureToastContainer();
        
        var icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        
        // Determina aria-live priority in base al tipo
        var ariaLive = (type === 'error') ? 'assertive' : 'polite';
        
        var $toast = $('<div class="fp-toast fp-toast-' + type + '" role="alert" aria-live="' + ariaLive + '" aria-atomic="true">' +
            '<span class="fp-toast-icon" aria-hidden="true">' + icons[type] + '</span>' +
            '<span class="fp-toast-message">' + message + '</span>' +
            '<button class="fp-toast-close" aria-label="Chiudi notifica">×</button>' +
        '</div>');
        
        $('#fp-toast-container').append($toast);
        
        // Animazione entrata
        setTimeout(function() {
            $toast.addClass('fp-toast-show');
        }, 10);
        
        // Auto-chiusura
        if (duration > 0) {
            setTimeout(function() {
                closeToast($toast);
            }, duration);
        }
        
        // Click per chiudere
        $toast.find('.fp-toast-close').on('click', function() {
            closeToast($toast);
        });
        
        return $toast;
    };
    
    function closeToast($toast) {
        $toast.removeClass('fp-toast-show');
        setTimeout(function() {
            $toast.remove();
        }, 300);
    }
    
    // Shorthand methods
    window.fpToast.success = function(message, duration) {
        return fpToast(message, 'success', duration);
    };
    
    window.fpToast.error = function(message, duration) {
        return fpToast(message, 'error', duration);
    };
    
    window.fpToast.warning = function(message, duration) {
        return fpToast(message, 'warning', duration);
    };
    
    window.fpToast.info = function(message, duration) {
        return fpToast(message, 'info', duration);
    };
    
})(jQuery);

