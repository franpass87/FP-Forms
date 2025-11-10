/**
 * FP Forms - Frontend JavaScript
 */

(function($) {
    'use strict';
    
    var FPFormsFrontend = {
        
        /**
         * Inizializza
         */
        init: function() {
            this.bindEvents();
        },
        
        /**
         * Bind degli eventi
         */
        bindEvents: function() {
            $(document).on('submit', '.fp-forms-form', this.handleSubmit);
        },
        
        /**
         * Gestisce submit form
         */
        handleSubmit: function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var formId = $form.data('form-id');
            
            // BUGFIX #18: Prevent double submit (race condition)
            if ($form.hasClass('is-submitting')) {
                return false;
            }
            $form.addClass('is-submitting');
            
            // Reset messaggi e errori
            $form.find('.fp-forms-success').hide();
            $form.find('.fp-forms-error').hide();
            $form.find('.fp-forms-field').removeClass('has-error');
            $form.find('.fp-forms-error').text('').hide();
            
            // Validazione client-side
            var isValid = FPFormsFrontend.validateForm($form);
            
            if (!isValid) {
                // BUGFIX #18: Remove submitting flag on validation fail
                $form.removeClass('is-submitting');
                return false;
            }
            
            // Raccogli dati - usa FormData per supportare file upload
            var formData = new FormData();
            
            // Aggiungi campi standard
            formData.append('action', 'fp_forms_submit');
            formData.append('form_id', formId);
            formData.append('nonce', $form.find('[name="nonce"]').val());
            
            // Raccogli tutti i campi
            var fieldValues = {};
            
            $form.find('input, textarea, select').each(function() {
                var $field = $(this);
                var name = $field.attr('name');
                
                // Salta campi di sistema
                if (!name || name === 'action' || name === 'form_id' || name === 'nonce') {
                    return;
                }
                
                // Gestisci file upload
                if ($field.is(':file')) {
                    var files = this.files;
                    if (files.length > 0) {
                        for (var i = 0; i < files.length; i++) {
                            formData.append(name + (files.length > 1 ? '[]' : ''), files[i]);
                        }
                    }
                    return;
                }
                
                // Gestisci checkbox multipli
                if ($field.is(':checkbox')) {
                    if (!fieldValues[name]) {
                        fieldValues[name] = [];
                    }
                    if ($field.is(':checked')) {
                        fieldValues[name].push($field.val());
                    }
                } 
                // Gestisci radio
                else if ($field.is(':radio')) {
                    if ($field.is(':checked')) {
                        fieldValues[name] = $field.val();
                    }
                }
                // Altri campi
                else {
                    fieldValues[name] = $field.val();
                }
            });
            
            // Aggiungi field values al FormData
            formData.append('form_data', JSON.stringify(fieldValues));
            
            // Aggiungi reCAPTCHA token se presente (v3: hidden field, v2: auto-inviato dal widget)
            var $recaptchaToken = $form.find('[name="fp_recaptcha_token"]');
            if ($recaptchaToken.length && $recaptchaToken.val()) {
                formData.append('fp_recaptcha_token', $recaptchaToken.val());
            }
            
            // Aggiungi g-recaptcha-response se presente (v2)
            var $grecaptchaResponse = $form.find('[name="g-recaptcha-response"]');
            if ($grecaptchaResponse.length && $grecaptchaResponse.val()) {
                formData.append('g-recaptcha-response', $grecaptchaResponse.val());
            }
            
            // Loading state
            $form.addClass('is-loading');
            var $btn = $form.find('.fp-forms-submit-btn');
            var originalText = $btn.text();
            $btn.prop('disabled', true).text(fpForms.strings.submitting);
            
            // AJAX submit
            $.ajax({
                url: fpForms.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,  // Non processare i dati (per file upload)
                contentType: false,  // Non impostare content-type (per file upload)
                success: function(response) {
                    $form.removeClass('is-loading');
                    $btn.prop('disabled', false).text(originalText);
                    
                    if (response.success) {
                        // Trigger tracking event: SUCCESS
                        $form[0].dispatchEvent(new CustomEvent('fpFormSubmitSuccess', {
                            detail: {
                                formId: formId,
                                message: response.data.message,
                                submissionId: response.data.submission_id
                            }
                        }));
                        
                        // Check per redirect
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                            return;
                        }
                        
                        // Mostra messaggio successo
                        var $successMsg = $form.find('.fp-forms-success');
                        var messageType = response.data.message_type || 'success';
                        var messageDuration = response.data.message_duration || 0;
                        
                        // BUGFIX #19: Validate messageType client-side
                        var allowedTypes = ['success', 'info', 'celebration'];
                        if (allowedTypes.indexOf(messageType) === -1) {
                            messageType = 'success';
                        }
                        
                        // Applica classe tipo
                        $successMsg
                            .removeClass('fp-msg-success fp-msg-info fp-msg-celebration')
                            .addClass('fp-msg-' + messageType)
                            .text(response.data.message)
                            .addClass('is-visible')
                            .attr('role', 'alert') // BUGFIX #20: A11Y - announce to screen readers
                            .attr('aria-live', 'polite')
                            .fadeIn();
                        
                        // Auto-hide se configurato
                        if (messageDuration > 0) {
                            setTimeout(function() {
                                $successMsg.fadeOut();
                            }, messageDuration);
                        }
                        
                        // Reset form
                        $form[0].reset();
                        
                        // Scroll al messaggio (BUGFIX #21: check se elemento visibile)
                        var $successElement = $form.find('.fp-forms-success');
                        if ($successElement.length && $successElement.offset()) {
                            $('html, body').animate({
                                scrollTop: $successElement.offset().top - 100
                            }, 500);
                        }
                        
                        // BUGFIX #18: Remove submitting flag
                        $form.removeClass('is-submitting');
                    } else {
                        // BUGFIX #18: Remove submitting flag on error
                        $form.removeClass('is-submitting');
                        
                        // Trigger tracking event: ERROR
                        $form[0].dispatchEvent(new CustomEvent('fpFormSubmitError', {
                            detail: {
                                formId: formId,
                                message: response.data.message,
                                errors: response.data.errors
                            }
                        }));
                        
                        // Mostra errore
                        $form.find('.fp-forms-error')
                            .text(response.data.message)
                            .fadeIn();
                        
                        // Evidenzia campi con errori
                        if (response.data.errors) {
                            $.each(response.data.errors, function(fieldName, errorMsg) {
                                var $field = $form.find('[name="' + fieldName + '"]').closest('.fp-forms-field');
                                $field.addClass('has-error');
                                $field.find('.fp-forms-error').text(errorMsg).show();
                            });
                            
                            // Scroll al primo errore
                            var $firstError = $form.find('.has-error').first();
                            if ($firstError.length) {
                                $('html, body').animate({
                                    scrollTop: $firstError.offset().top - 100
                                }, 500);
                            }
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // BUGFIX #18: Remove submitting flag on AJAX error
                    $form.removeClass('is-submitting');
                    $form.removeClass('is-loading');
                    $btn.prop('disabled', false).text(originalText);
                    
                    // BUGFIX #22: Better error handling with user feedback (i18n)
                    var errorMessage = fpForms.strings.error_connection;
                    
                    if (textStatus === 'timeout') {
                        errorMessage = fpForms.strings.error_timeout;
                    } else if (textStatus === 'abort') {
                        errorMessage = fpForms.strings.error_abort;
                    }
                    
                    $form.find('.fp-forms-error')
                        .text(errorMessage)
                        .attr('role', 'alert')
                        .attr('aria-live', 'assertive')
                        .fadeIn();
                }
            });
            
            return false;
        },
        
        /**
         * Valida form lato client
         */
        validateForm: function($form) {
            var isValid = true;
            
            // Valida campi required
            $form.find('[required]').each(function() {
                var $field = $(this);
                var $container = $field.closest('.fp-forms-field');
                var value = $field.val();
                
                // Gestione checkbox e radio
                if ($field.is(':checkbox') || $field.is(':radio')) {
                    var name = $field.attr('name');
                    var $group = $form.find('[name="' + name + '"]');
                    
                    if (!$group.is(':checked')) {
                        $container.addClass('has-error');
                        $container.find('.fp-forms-error')
                            .text(fpForms.strings.required)
                            .show();
                        isValid = false;
                    }
                } 
                // Altri campi
                else if (!value || value.trim() === '') {
                    $container.addClass('has-error');
                    $container.find('.fp-forms-error')
                        .text(fpForms.strings.required)
                        .show();
                    isValid = false;
                }
            });
            
            // Valida email
            $form.find('input[type="email"]').each(function() {
                var $field = $(this);
                var $container = $field.closest('.fp-forms-field');
                var value = $field.val();
                
                if (value && !FPFormsFrontend.isValidEmail(value)) {
                    $container.addClass('has-error');
                    $container.find('.fp-forms-error')
                        .text('Inserisci un indirizzo email valido.')
                        .show();
                    isValid = false;
                }
            });
            
            return isValid;
        },
        
        /**
         * Valida email
         */
        isValidEmail: function(email) {
            var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    };
    
    // Inizializza al document ready
    $(document).ready(function() {
        FPFormsFrontend.init();
    });
    
})(jQuery);
