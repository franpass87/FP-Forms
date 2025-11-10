/**
 * FP Forms Loading States
 * Gestione stati di caricamento con spinner e feedback visivo
 */

(function($) {
    'use strict';
    
    /**
     * Mostra loading su bottone
     * @param {jQuery} $btn - Bottone jQuery
     * @param {string} text - Testo loading (opzionale)
     */
    window.fpLoadingButton = function($btn, text) {
        if (!$btn || !$btn.length) return;
        
        text = text || 'Caricamento...';
        
        // Salva stato originale
        if (!$btn.data('fp-original-html')) {
            $btn.data('fp-original-html', $btn.html());
            $btn.data('fp-original-disabled', $btn.prop('disabled'));
        }
        
        // Applica loading
        $btn.prop('disabled', true)
            .addClass('fp-btn-loading')
            .html('<span class="fp-spinner"></span> ' + text);
    };
    
    /**
     * Ripristina bottone
     * @param {jQuery} $btn - Bottone jQuery
     */
    window.fpLoadingButtonReset = function($btn) {
        if (!$btn || !$btn.length) return;
        
        var originalHtml = $btn.data('fp-original-html');
        var originalDisabled = $btn.data('fp-original-disabled');
        
        if (originalHtml) {
            $btn.html(originalHtml)
                .removeClass('fp-btn-loading')
                .prop('disabled', originalDisabled || false);
            
            // Cleanup
            $btn.removeData('fp-original-html');
            $btn.removeData('fp-original-disabled');
        }
    };
    
    /**
     * Mostra skeleton loader
     * @param {jQuery} $container - Container
     */
    window.fpShowSkeleton = function($container) {
        if (!$container || !$container.length) return;
        
        $container.data('fp-original-content', $container.html());
        
        var skeleton = '<div class="fp-skeleton-loader">' +
            '<div class="fp-skeleton-line"></div>' +
            '<div class="fp-skeleton-line"></div>' +
            '<div class="fp-skeleton-line fp-skeleton-short"></div>' +
        '</div>';
        
        $container.html(skeleton).addClass('fp-loading');
    };
    
    /**
     * Nasconde skeleton loader
     * @param {jQuery} $container - Container
     */
    window.fpHideSkeleton = function($container) {
        if (!$container || !$container.length) return;
        
        var originalContent = $container.data('fp-original-content');
        
        if (originalContent) {
            $container.html(originalContent).removeClass('fp-loading');
            $container.removeData('fp-original-content');
        }
    };
    
    /**
     * Progress bar
     */
    window.fpProgress = {
        show: function(percent) {
            if (!$('#fp-global-progress').length) {
                $('body').append('<div id="fp-global-progress" class="fp-progress-bar"><div class="fp-progress-fill"></div></div>');
            }
            
            percent = Math.min(100, Math.max(0, percent || 0));
            
            $('#fp-global-progress').addClass('fp-progress-visible')
                .find('.fp-progress-fill').css('width', percent + '%');
        },
        
        hide: function() {
            $('#fp-global-progress').removeClass('fp-progress-visible');
            setTimeout(function() {
                $('#fp-global-progress .fp-progress-fill').css('width', '0%');
            }, 300);
        }
    };
    
})(jQuery);

