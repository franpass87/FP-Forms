/**
 * FP Forms - Admin JavaScript
 */

(function($) {
    'use strict';
    
    var FPFormsAdmin = {
        
        /**
         * Costruisce un notice DOM in modo sicuro (senza concatenazione HTML con dati non trusted).
         * @param {string} type  'success' | 'error'
         * @param {string} message  Testo del messaggio (verrà escaped)
         * @param {jQuery|null} $extra  Nodo jQuery opzionale da appendere dopo il testo
         * @returns {jQuery}
         */
        _safeNotice: function(type, message, $extra) {
            var isSuccess = (type === 'success');
            var $div = $('<div>').addClass('notice inline').css({ margin: 0, padding: '8px 12px' });
            $div.addClass(isSuccess ? 'notice-success' : 'notice-error');
            var $icon = $('<span>').addClass('dashicons').css('color', isSuccess ? '#46b450' : '#dc3232');
            $icon.addClass(isSuccess ? 'dashicons-yes' : 'dashicons-no');
            $div.append($icon);
            $div.append(document.createTextNode(' ' + (message || (isSuccess ? 'OK' : 'Errore durante il test'))));
            if ($extra) { $div.append($extra); }
            return $div;
        },
        
        /**
         * Inizializza
         */
        init: function() {
            this.bindEvents();
            this.initSortable();
            this.initFieldOptionsVisibility();
        },
        
        /**
         * Bind degli eventi
         */
        bindEvents: function() {
            // Forms List
            $(document).on('click', '.fp-copy-shortcode', this.copyShortcode);
            $(document).on('click', '.fp-delete-form', this.deleteForm);
            $(document).on('click', '.fp-duplicate-form', this.duplicateForm);
            
            // Form Builder
            $(document).on('click', '.fp-field-type', this.addField);
            $(document).on('click', '.fp-field-edit', this.toggleFieldEdit);
            $(document).on('click', '.fp-field-delete', this.deleteField);
            $(document).on('submit', '#fp-form-builder', this.saveForm);
            $(document).on('input', '.fp-field-input-label', this.updateFieldPreview);
            $(document).on('change', '.fp-field-type', this.toggleFieldOptions);
            
            // Submissions
            $(document).on('click', '.fp-view-submission', this.viewSubmission);
            $(document).on('click', '.fp-delete-submission', this.deleteSubmission);
            $(document).on('click', '.fp-export-submissions-btn', this.openExportModal);
            $(document).on('submit', '#fp-export-form', this.exportSubmissions);
            $(document).on('click', '.fp-modal-close', this.closeModal);
            
            // Templates
            $(document).on('click', '.fp-import-template-btn', this.openTemplateImport);
            $(document).on('submit', '#fp-template-import-form', this.importTemplate);
            
            // Conditional Logic
            $(document).on('click', '#fp-add-conditional-rule', this.addConditionalRule);
            $(document).on('click', '.fp-rule-delete', this.deleteConditionalRule);
            $(document).on('change', '#fp-fields-container', this.updateConditionalFields);
            
            // Bulk Actions
            $(document).on('change', '#fp-select-all-submissions, #fp-select-all-table', this.toggleSelectAll);
            $(document).on('change', '.fp-submission-checkbox', this.updateSelectedCount);
            $(document).on('click', '#fp-apply-bulk-action', this.applyBulkAction);
            
            // Chiudi modal cliccando fuori
            $(document).on('click', '.fp-modal', function(e) {
                if ($(e.target).hasClass('fp-modal')) {
                    FPFormsAdmin.closeModal();
                }
            });
        },
        
        /**
         * Inizializza sortable per i campi
         */
        initSortable: function() {
            if (!$('#fp-fields-container').length) return;
            try {
                if ($.fn.sortable) {
                    $('#fp-fields-container').sortable({
                        handle: '.fp-field-drag',
                        placeholder: 'fp-field-placeholder',
                        axis: 'y',
                        opacity: 0.8
                    });
                }
            } catch (err) {
                if ( window.fpFormsDebug || ( typeof fpFormsAdmin !== 'undefined' && fpFormsAdmin.debug ) ) {
                    console.warn('FP Forms: sortable init skipped', err);
                }
            }
        },
        
        /**
         * Copia shortcode
         */
        copyShortcode: function(e) {
            e.preventDefault();
            var shortcode = $(this).data('shortcode');
            var $btn = $(this);
            var originalText = $btn.text();
            
            function showCopied() {
                $btn.text('Copiato!').addClass('copied');
                setTimeout(function() {
                    $btn.text(originalText).removeClass('copied');
                }, 2000);
            }
            
            // Usa Clipboard API moderna con fallback a execCommand
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(shortcode).then(showCopied).catch(function() {
                    fallbackCopy();
                });
            } else {
                fallbackCopy();
            }
            
            function fallbackCopy() {
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(shortcode).select();
                try { document.execCommand('copy'); } catch(err) {}
                $temp.remove();
                showCopied();
            }
        },
        
        /**
         * Elimina form
         */
        deleteForm: function(e) {
            e.preventDefault();
            
            var formId = $(this).data('form-id');
            var $row = $(this).closest('tr');
            var self = this;
            
            window.fpConfirm('Sei sicuro di voler eliminare questo form? Questa azione non può essere annullata.', {
                title: 'Elimina Form',
                confirmText: 'Elimina',
                cancelText: 'Annulla',
                confirmClass: 'button-primary',
                onConfirm: function() {
                    $.ajax({
                        url: fpFormsAdmin.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'fp_forms_delete_form',
                            nonce: fpFormsAdmin.nonce,
                            form_id: formId
                        },
                        success: function(response) {
                            if (response.success) {
                                $row.fadeOut(function() {
                                    $(this).remove();
                                });
                                if (typeof window.fpToast !== 'undefined') {
                                    window.fpToast.success('Form eliminato con successo');
                                }
                            } else {
                                if (typeof window.fpToast !== 'undefined') {
                                    window.fpToast.error(response.data.message || fpFormsAdmin.strings.error);
                                }
                            }
                        },
                        error: function() {
                            if (typeof window.fpToast !== 'undefined') {
                                window.fpToast.error(fpFormsAdmin.strings.error);
                            }
                        }
                    });
                }
            });
        },
        
        /**
         * Duplica form
         */
        duplicateForm: function(e) {
            e.preventDefault();
            
            var formId = $(this).data('form-id');
            var $btn = $(this);
            
            $btn.prop('disabled', true).text('Duplicazione...');
            
            $.ajax({
                url: fpFormsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fp_forms_duplicate_form',
                    nonce: fpFormsAdmin.nonce,
                    form_id: formId
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        if (typeof window.fpToast !== 'undefined') {
                            window.fpToast.error(response.data.message || fpFormsAdmin.strings.error);
                        }
                        $btn.prop('disabled', false).text('Duplica');
                    }
                },
                error: function() {
                    if (typeof window.fpToast !== 'undefined') {
                        window.fpToast.error(fpFormsAdmin.strings.error);
                    }
                    $btn.prop('disabled', false).text('Duplica');
                }
            });
        },
        
        /**
         * Aggiunge un campo
         */
        addField: function(e) {
            e.preventDefault();
            e.stopPropagation();

            var type = $(this).data('type');
            if (!type) return;
            var index = $('#fp-fields-container .fp-field-item').length;

            var fieldData = FPFormsAdmin.getFieldDefaults(type);
            var html = FPFormsAdmin.getFieldHtml(fieldData, index);
            if (!html) return;

            $('#fp-fields-container').append(html);

            var $newField = $('#fp-fields-container .fp-field-item').last();
            
            // Mostra subito il form di editing
            $newField.find('.fp-field-body').show();
            
            // Mostra opzioni specifiche in base al tipo
            FPFormsAdmin.showFieldOptions($newField, type);
        },
        
        /**
         * Ottiene defaults per tipo di campo
         */
        getFieldDefaults: function(type) {
            var typeLabels = {
                'text': 'Testo',
                'fullname': 'Nome e cognome',
                'email': 'Email',
                'phone': 'Telefono',
                'number': 'Numero',
                'date': 'Data',
                'textarea': 'Area di Testo',
                'select': 'Select',
                'radio': 'Radio Button',
                'checkbox': 'Checkbox',
                'privacy-checkbox': 'Privacy Policy',
                'marketing-checkbox': 'Marketing',
                'recaptcha': 'reCAPTCHA',
                'file': 'Upload File',
                'calculated': 'Calcolato',
                'step_break': 'Nuovo Step'
            };
            var typeNames = {
                'text': 'testo', 'email': 'email', 'phone': 'telefono', 'number': 'numero',
                'date': 'data', 'textarea': 'messaggio', 'select': 'selezione', 'radio': 'opzione',
                'checkbox': 'checkbox', 'file': 'file', 'calculated': 'calcolato'
            };
            
            // Default specifici per privacy-checkbox
            if (type === 'privacy-checkbox') {
                return {
                    type: type,
                    typeLabel: typeLabels[type],
                    label: 'Privacy Policy',
                    name: 'privacy_consent',
                    placeholder: '',
                    description: '',
                    choices: '',
                    required: true // Sempre obbligatorio per GDPR
                };
            }
            
            // Default specifici per marketing-checkbox
            if (type === 'marketing-checkbox') {
                return {
                    type: type,
                    typeLabel: typeLabels[type],
                    label: 'Consenso Marketing',
                    name: 'marketing_consent',
                    placeholder: '',
                    description: '',
                    choices: '',
                    required: false // Opzionale di default
                };
            }
            
            // Default specifici per fullname (nome e cognome)
            if (type === 'fullname') {
                return {
                    type: type,
                    typeLabel: typeLabels[type],
                    label: 'Nome e cognome',
                    name: 'nome_cognome',
                    placeholder: '',
                    description: '',
                    choices: '',
                    required: true
                };
            }

            // Default specifici per reCAPTCHA
            if (type === 'recaptcha') {
                return {
                    type: type,
                    typeLabel: typeLabels[type],
                    label: 'Protezione Anti-Spam',
                    name: 'recaptcha',
                    placeholder: '',
                    description: '',
                    choices: '',
                    required: false // Non applicabile
                };
            }
            
            // Default specifici per step_break
            if (type === 'step_break') {
                return {
                    type: type,
                    typeLabel: typeLabels[type],
                    label: 'Nuovo Step',
                    name: 'step_break_' + Date.now(),
                    step_title: 'Step ' + (Date.now() % 1000),
                    placeholder: '',
                    description: '',
                    choices: '',
                    required: false
                };
            }
            
            var baseName = typeNames[type] || 'field';
            return {
                type: type,
                typeLabel: typeLabels[type] || type,
                label: typeLabels[type] || 'Nuovo Campo',
                name: baseName + '_' + Date.now(),
                placeholder: '',
                description: '',
                choices: '',
                required: false
            };
        },
        
        /**
         * Ottiene HTML campo
         */
        getFieldHtml: function(field, index) {
            var template = $('#fp-field-template').html();
            if (!template) {
                if ( window.fpFormsDebug || ( typeof fpFormsAdmin !== 'undefined' && fpFormsAdmin.debug ) ) {
                    console.warn('FP Forms: template #fp-field-template non trovato');
                }
                return '';
            }
            template = template.replace(/\{\{index\}\}/g, index);
            template = template.replace(/\{\{type\}\}/g, field.type);
            template = template.replace(/\{\{typeLabel\}\}/g, field.typeLabel);
            template = template.replace(/\{\{label\}\}/g, field.label);
            template = template.replace(/\{\{name\}\}/g, field.name);
            template = template.replace(/\{\{placeholder\}\}/g, field.placeholder || '');
            template = template.replace(/\{\{description\}\}/g, field.description || '');
            template = template.replace(/\{\{choices\}\}/g, field.choices || '');
            template = template.replace(/\{\{required\}\}/g, field.required ? 'checked' : '');
            
            return template;
        },
        
        /**
         * Toggle edit campo
         */
        toggleFieldEdit: function(e) {
            e.preventDefault();
            $(this).closest('.fp-field-item').find('.fp-field-body').slideToggle();
        },
        
        /**
         * Elimina campo
         */
        deleteField: function(e) {
            e.preventDefault();
            
            var $field = $(this).closest('.fp-field-item');
            
            window.fpConfirm('Eliminare questo campo?', {
                title: 'Elimina Campo',
                confirmText: 'Elimina',
                cancelText: 'Annulla',
                onConfirm: function() {
                    $field.fadeOut(function() {
                        $(this).remove();
                    });
                }
            });
        },
        
        /**
         * Aggiorna preview campo
         */
        updateFieldPreview: function() {
            var label = $(this).val();
            $(this).closest('.fp-field-item').find('.fp-field-label-preview').text(label);
        },
        
        /**
         * Toggle opzioni campo in base al tipo
         */
        toggleFieldOptions: function() {
            var $field = $(this).closest('.fp-field-item');
            var type = $(this).val();
            
            FPFormsAdmin.showFieldOptions($field, type);
        },
        
        /**
         * Mostra opzioni specifiche per il tipo di campo
         */
        showFieldOptions: function($field, type) {
            var fieldType = type || $field.find('.fp-field-type').val();
            
            // Opzioni scelte (select/radio/checkbox)
            var $choices = $field.find('.fp-field-choices');
            if ($choices.length) {
                if (['select', 'radio', 'checkbox'].indexOf(fieldType) !== -1) {
                    $choices.show();
                } else {
                    $choices.hide();
                }
            }
            
            // Opzioni upload file
            var $fileOptions = $field.find('.fp-field-file-options');
            if ($fileOptions.length) {
                if (fieldType === 'file') {
                    $fileOptions.show();
                } else {
                    $fileOptions.hide();
                }
            }
        },
        
        /**
         * Inizializza la visibilità delle opzioni per i campi già presenti
         */
        initFieldOptionsVisibility: function() {
            $('#fp-fields-container .fp-field-item').each(function() {
                FPFormsAdmin.showFieldOptions($(this));
            });
        },
        
        /**
         * Valida form prima del salvataggio
         */
        validateForm: function() {
            var toast = typeof window.fpToast !== 'undefined' ? window.fpToast : null;
            var title = $('#form_title').val().trim();

            if (!title) {
                if (toast && toast.error) toast.error('Il titolo del form è obbligatorio');
                $('#form_title').focus();
                return false;
            }

            var fieldsCount = $('#fp-fields-container .fp-field-item').length;

            if (fieldsCount === 0) {
                if (toast && toast.warning) toast.warning('Aggiungi almeno un campo al form');
                return false;
            }

            var hasError = false;
            $('#fp-fields-container .fp-field-item').each(function() {
                var $field = $(this);
                var label = $field.find('.fp-field-input-label').val().trim();
                var name = $field.find('.fp-field-input-name').val().trim();

                if (!label || !name) {
                    hasError = true;
                    $field.addClass('fp-field-error');
                } else {
                    $field.removeClass('fp-field-error');
                }
            });

            if (hasError) {
                if (toast && toast.error) toast.error('Alcuni campi non hanno etichetta o nome');
                return false;
            }

            return true;
        },
        
        /**
         * Salva form
         */
        saveForm: function(e) {
            e.preventDefault();
            
            var $form = $(e.currentTarget);
            if (!$form.length) return;
            
            // Valida prima
            if (!FPFormsAdmin.validateForm()) {
                return;
            }
            
            var formId = $('#form_id').val();
            var title = $('#form_title').val();
            var description = $('#form_description').val();
            
            // Raccogli campi (selettore document-level per compatibilità layout)
            var fields = [];
            $('#fp-fields-container .fp-field-item').each(function(index) {
                var $field = $(this);
                var type = $field.find('.fp-field-type').val();
                
                var fieldData = {
                    type: type,
                    label: $field.find('.fp-field-input-label').val(),
                    name: $field.find('.fp-field-input-name').val(),
                    required: $field.find('.fp-field-input-required').is(':checked'),
                    options: {
                        placeholder: $field.find('.fp-field-input-placeholder').val(),
                        description: $field.find('.fp-field-input-description').val(),
                        error_message: $field.find('.fp-field-input-error-message').val()
                    }
                };
                
                // Aggiungi choices se necessario
                if (['select', 'radio', 'checkbox'].indexOf(type) !== -1) {
                    var choicesText = $field.find('.fp-field-input-choices').val();
                    fieldData.options.choices = choicesText.split('\n').filter(function(c) {
                        return c.trim() !== '';
                    });
                }
                
                // Aggiungi opzioni file se necessario
                if (type === 'file') {
                    fieldData.options.max_size = parseInt($field.find('.fp-field-input-max-size').val()) || 5;
                    var allowedTypes = $field.find('.fp-field-input-allowed-types').val();
                    fieldData.options.allowed_types = allowedTypes ? allowedTypes.split(',').map(function(t) {
                        return t.trim();
                    }) : ['pdf', 'jpg', 'png'];
                    fieldData.options.multiple = $field.find('.fp-field-input-multiple').is(':checked');
                }
                
                // Aggiungi opzioni privacy-checkbox se necessario
                if (type === 'privacy-checkbox') {
                    fieldData.options.privacy_text = $field.find('.fp-field-input-privacy-text').val() || 'Ho letto e accetto la';
                    fieldData.required = true; // Forza sempre required per GDPR
                }
                
                // Aggiungi opzioni marketing-checkbox se necessario
                if (type === 'marketing-checkbox') {
                    fieldData.options.marketing_text = $field.find('.fp-field-input-marketing-text').val() || 'Acconsento a ricevere comunicazioni marketing e newsletter';
                }
                
                // Aggiungi step_title per step_break
                if (type === 'step_break') {
                    fieldData.step_title = $field.find('.fp-field-input-step-title').val() || '';
                }
                
                fields.push(fieldData);
            });
            
            // Raccogli settings
            var settings = {
                // Trust badges
                trust_badges: $('input[name="trust_badges[]"]:checked').map(function() {
                    return $(this).val();
                }).get(),
                submit_button_text: $('input[name="submit_button_text"]').val(),
                submit_button_color: $('input[name="submit_button_color"]').val(),
                submit_button_size: $('select[name="submit_button_size"]').val(),
                submit_button_style: $('select[name="submit_button_style"]').val(),
                submit_button_align: $('select[name="submit_button_align"]').val(),
                submit_button_width: $('select[name="submit_button_width"]').val(),
                submit_button_icon: $('select[name="submit_button_icon"]').val(),
                success_message: $('textarea[name="success_message"]').val(),
                success_message_type: $('select[name="success_message_type"]').val(),
                success_message_duration: $('select[name="success_message_duration"]').val(),
                custom_css_class: $('input[name="custom_css_class"]').val(),
                custom_border_color: $('input[name="custom_border_color"]').val(),
                custom_focus_color: $('input[name="custom_focus_color"]').val(),
                custom_text_color: $('input[name="custom_text_color"]').val(),
                custom_background_color: $('input[name="custom_background_color"]').val(),
                success_redirect_enabled: $('input[name="success_redirect_enabled"]').is(':checked'),
                success_redirect_url: $('input[name="success_redirect_url"]').val(),
                // Email settings
                disable_wordpress_emails: $('input[name="disable_wordpress_emails"]').is(':checked'),
                notification_email: $('input[name="notification_email"]').val(),
                notification_subject: $('input[name="notification_subject"]').val(),
                notification_message: $('textarea[name="notification_message"]').val(),
                confirmation_enabled: $('input[name="confirmation_enabled"]').is(':checked'),
                confirmation_template: $('input[name="confirmation_template"]:checked').val() || '',
                confirmation_accent_color: $('input[name="confirmation_accent_color"]').val() || '',
                confirmation_footer_info: $('textarea[name="confirmation_footer_info"]').val() || '',
                confirmation_subject: $('input[name="confirmation_subject"]').val(),
                confirmation_message: $('textarea[name="confirmation_message"]').val(),
                // Staff notifications (v1.2)
                staff_notifications_enabled: $('input[name="staff_notifications_enabled"]').is(':checked'),
                staff_emails: $('textarea[name="staff_emails"]').val(),
                staff_notification_subject: $('input[name="staff_notification_subject"]').val(),
                staff_notification_message: $('textarea[name="staff_notification_message"]').val(),
                // Brevo integration (v1.2)
                brevo_enabled: $('input[name="brevo_enabled"]').is(':checked'),
                brevo_list_id: $('input[name="brevo_list_id"]').val(),
                brevo_event_name: $('input[name="brevo_event_name"]').val(),
                // Conditional logic
                conditional_rules: FPFormsAdmin.getConditionalRules(),
                conditional_operator_global: $('#fp-conditional-operator-global').val() || 'or',
                // Webhooks
                webhooks: FPFormsAdmin.getWebhooks(),
                // Multi-step
                enable_multistep: $('input[name="enable_multistep"]').is(':checked'),
                // Progressive save
                enable_progressive_save: $('input[name="enable_progressive_save"]').is(':checked'),
                // Payment (Stripe)
                payment_enabled: $('input[name="payment_enabled"]').is(':checked'),
                payment_provider: $('#payment_provider').val() || '',
                payment_amount: parseFloat($('input[name="payment_amount"]').val()) || 0,
                payment_amount_field: $('#payment_amount_field').val() || ''
            };
            
            var $btn = $('#fp-form-builder').find('button[type="submit"]');
            if (typeof window.fpLoadingButton === 'function') window.fpLoadingButton($btn, 'Salvataggio...');
            if (typeof window.fpProgress !== 'undefined' && window.fpProgress.show) window.fpProgress.show(30);

            $.ajax({
                url: fpFormsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fp_forms_save_form',
                    nonce: fpFormsAdmin.nonce,
                    form_id: formId,
                    title: title,
                    description: description,
                    fields: JSON.stringify(fields),
                    settings: JSON.stringify(settings)
                },
                success: function(response) {
                    if (typeof window.fpProgress !== 'undefined' && window.fpProgress.show) window.fpProgress.show(100);
                    if (response.success) {
                        if (typeof window.fpToast !== 'undefined' && window.fpToast.success) window.fpToast.success('Form salvato con successo!');
                        setTimeout(function() {
                            if ((!formId || parseInt(formId, 10) === 0) && response.data && response.data.form_id) {
                                window.location.href = (fpFormsAdmin.editFormUrlBase || (location.pathname + '?page=fp-forms-edit&form_id=')) + response.data.form_id;
                            } else {
                                if (typeof window.fpLoadingButtonReset === 'function') window.fpLoadingButtonReset($btn);
                                if (typeof window.fpProgress !== 'undefined' && window.fpProgress.hide) window.fpProgress.hide();
                            }
                        }, 600);
                    } else {
                        if (typeof window.fpToast !== 'undefined' && window.fpToast.error) window.fpToast.error(response.data && response.data.message ? response.data.message : (fpFormsAdmin.strings && fpFormsAdmin.strings.error));
                        if (typeof window.fpLoadingButtonReset === 'function') window.fpLoadingButtonReset($btn);
                        if (typeof window.fpProgress !== 'undefined' && window.fpProgress.hide) window.fpProgress.hide();
                    }
                },
                error: function() {
                    if (typeof window.fpToast !== 'undefined' && window.fpToast.error) window.fpToast.error(fpFormsAdmin.strings && fpFormsAdmin.strings.error ? fpFormsAdmin.strings.error : 'Errore');
                    if (typeof window.fpLoadingButtonReset === 'function') window.fpLoadingButtonReset($btn);
                    if (typeof window.fpProgress !== 'undefined' && window.fpProgress.hide) window.fpProgress.hide();
                }
            });
        },
        
        /**
         * Visualizza submission
         */
        viewSubmission: function(e) {
            e.preventDefault();
            
            var submissionId = $(this).data('submission-id');
            
            // Mostra modal
            $('#fp-submission-modal').fadeIn();
            
            // Mostra skeleton loader
            var skeleton = '<div class="fp-skeleton-loader">' +
                '<div class="fp-skeleton-line"></div>' +
                '<div class="fp-skeleton-line"></div>' +
                '<div class="fp-skeleton-line fp-skeleton-short"></div>' +
            '</div>';
            $('#fp-submission-details').html(skeleton);
            
            // AJAX per ottenere dettagli
            $.ajax({
                url: fpFormsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fp_forms_get_submission_details',
                    nonce: fpFormsAdmin.nonce,
                    submission_id: submissionId
                },
                success: function(response) {
                    if (response.success) {
                        $('#fp-submission-details').html(response.data.html);
                    } else {
                        $('#fp-submission-details').html('<p>Errore nel caricare i dettagli.</p>');
                    }
                },
                error: function() {
                    $('#fp-submission-details').html('<p>Errore nel caricare i dettagli.</p>');
                }
            });
        },
        
        /**
         * Elimina submission
         */
        deleteSubmission: function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var submissionId = $btn.data('submission-id');
            var $row = $btn.closest('tr');
            
            window.fpConfirm('Eliminare questa submission? Questa azione non può essere annullata.', {
                title: 'Elimina Submission',
                confirmText: 'Elimina',
                cancelText: 'Annulla',
                onConfirm: function() {
                    if (typeof window.fpLoadingButton !== 'undefined') fpLoadingButton($btn, 'Eliminazione...');
                    
                    $.ajax({
                        url: fpFormsAdmin.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'fp_forms_delete_submission',
                            nonce: fpFormsAdmin.nonce,
                            submission_id: submissionId
                        },
                        success: function(response) {
                            if (response.success) {
                                if (typeof window.fpToast !== 'undefined') fpToast.success('Submission eliminata');
                                $row.fadeOut(400, function() {
                                    $(this).remove();
                                });
                    } else {
                        if (typeof window.fpToast !== 'undefined') fpToast.error(response.data.message || fpFormsAdmin.strings.error);
                        if (typeof window.fpLoadingButtonReset !== 'undefined') fpLoadingButtonReset($btn);
                    }
                },
                error: function() {
                    if (typeof window.fpToast !== 'undefined') fpToast.error(fpFormsAdmin.strings.error);
                    if (typeof window.fpLoadingButtonReset !== 'undefined') fpLoadingButtonReset($btn);
                }
                    });
                }
            });
        },
        
        /**
         * Chiudi modal
         */
        closeModal: function() {
            $('.fp-modal').fadeOut();
        },
        
        /**
         * Apri modal export
         */
        openExportModal: function(e) {
            e.preventDefault();
            $('#fp-export-modal').fadeIn();
        },
        
        /**
         * Export submissions
         */
        exportSubmissions: function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var formData = new FormData($form[0]);
            formData.append('action', 'fp_forms_export_submissions');
            formData.append('nonce', fpFormsAdmin.nonce);
            
            // Crea form temporaneo per submit
            var tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = fpFormsAdmin.ajaxurl;
            tempForm.style.display = 'none';
            
            formData.forEach(function(value, key) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                tempForm.appendChild(input);
            });
            
            document.body.appendChild(tempForm);
            tempForm.submit();
            document.body.removeChild(tempForm);
            
            // Chiudi modal
            setTimeout(function() {
                $('#fp-export-modal').fadeOut();
            }, 500);
        },
        
        /**
         * Apri modal import template
         */
        openTemplateImport: function(e) {
            e.preventDefault();
            
            var templateId = $(this).closest('.fp-template-card').data('template-id');
            $('#template-id').val(templateId);
            $('#fp-template-import-modal').fadeIn();
        },
        
        /**
         * Importa template
         */
        importTemplate: function(e) {
            e.preventDefault();
            
            var templateId = $('#template-id').val();
            var customTitle = $('#template-title').val();
            var $btn = $(this).find('button[type="submit"]');
            
            if (typeof window.fpLoadingButton !== 'undefined') fpLoadingButton($btn, 'Importazione...');
            if (typeof window.fpProgress !== 'undefined') fpProgress.show(50);
            
            $.ajax({
                url: fpFormsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fp_forms_import_template',
                    nonce: fpFormsAdmin.nonce,
                    template_id: templateId,
                    custom_title: customTitle
                },
                success: function(response) {
                    if (typeof window.fpProgress !== 'undefined') fpProgress.show(100);
                    if (response.success) {
                        if (typeof window.fpToast !== 'undefined') fpToast.success('Template importato con successo!');
                        setTimeout(function() {
                            var redir = (typeof response.data.redirect === 'string') ? response.data.redirect : '';
                            if (redir && (redir.charAt(0) === '/' || redir.indexOf(window.location.origin) === 0)) {
                                window.location.href = redir;
                            }
                        }, 600);
                    } else {
                        if (typeof window.fpToast !== 'undefined') fpToast.error(response.data.message || fpFormsAdmin.strings.error);
                        if (typeof window.fpLoadingButtonReset !== 'undefined') fpLoadingButtonReset($btn);
                        if (typeof window.fpProgress !== 'undefined') fpProgress.hide();
                    }
                },
                error: function() {
                    if (typeof window.fpToast !== 'undefined') fpToast.error(fpFormsAdmin.strings.error);
                    if (typeof window.fpLoadingButtonReset !== 'undefined') fpLoadingButtonReset($btn);
                    if (typeof window.fpProgress !== 'undefined') fpProgress.hide();
                }
            });
        },
        
        /**
         * Aggiungi regola condizionale
         */
        addConditionalRule: function(e) {
            e.preventDefault();
            
            var index = $('.fp-rule-item').length;
            var template = $('#fp-rule-template').html();
            
            template = template.replace(/\{\{index\}\}/g, index);
            template = template.replace(/\{\{ruleNumber\}\}/g, index + 1);
            template = template.replace(/\{\{ruleId\}\}/g, 'rule_' + Date.now());
            
            $('#fp-conditional-rules-container').append(template);
            
            // Popola opzioni campi
            FPFormsAdmin.updateConditionalFields();
        },
        
        /**
         * Elimina regola condizionale
         */
        deleteConditionalRule: function(e) {
            e.preventDefault();
            
            var $rule = $(this).closest('.fp-rule-item');
            
            window.fpConfirm('Eliminare questa regola?', {
                title: 'Elimina Regola',
                confirmText: 'Elimina',
                cancelText: 'Annulla',
                onConfirm: function() {
                    $rule.fadeOut(function() {
                        $(this).remove();
                        // Rinumera regole
                        $('.fp-rule-item').each(function(idx) {
                            $(this).find('.fp-rule-number').text('Regola #' + (idx + 1));
                        });
                    });
                }
            });
        },
        
        /**
         * Ottiene webhooks dal form
         */
        getWebhooks: function($ctx) {
            var webhooks = [];
            var $scope = ($ctx && $ctx.length) ? $ctx : $(document);
            $scope.find('.fp-webhook-item').each(function() {
                var $webhook = $(this);
                var url = $webhook.find('.fp-webhook-url').val();
                
                // Solo aggiungi se ha URL
                if (url) {
                    webhooks.push({
                        id: $webhook.find('.fp-webhook-id').val() || 'webhook_' + Date.now(),
                        enabled: $webhook.find('.fp-webhook-enabled').is(':checked'),
                        url: url,
                        secret: $webhook.find('.fp-webhook-secret').val() || ''
                    });
                }
            });
            
            return webhooks;
        },
        
        /**
         * Aggiorna campi disponibili nelle regole
         */
        updateConditionalFields: function() {
            var fields = [];
            
            $('#fp-fields-container .fp-field-item').each(function() {
                var name = $(this).find('.fp-field-input-name').val();
                var label = $(this).find('.fp-field-input-label').val();
                
                if (name && label) {
                    fields.push({ name: name, label: label });
                }
            });
            
            // Aggiorna tutti i select nelle regole
            $('.fp-rule-trigger-field, .fp-rule-targets').each(function() {
                var $select = $(this);
                var currentValue = $select.val();
                
                $select.find('option:not(:first)').remove();
                
                fields.forEach(function(field) {
                    $select.append($('<option></option>').val(field.name).text(field.label));
                });
                
                // Ripristina valore
                if (currentValue) {
                    $select.val(currentValue);
                }
            });
        },
        
        /**
         * Ottiene regole condizionali
         */
        getConditionalRules: function($ctx) {
            var rules = [];
            var $scope = ($ctx && $ctx.length) ? $ctx : $(document);
            $scope.find('.fp-rule-item').each(function() {
                var $rule = $(this);
                var targetValues = $rule.find('.fp-rule-targets').val();
                
                if (!targetValues || targetValues.length === 0) {
                    return; // Skip regola incompleta
                }
                
                rules.push({
                    id: $rule.find('.fp-rule-id').val(),
                    trigger_field: $rule.find('.fp-rule-trigger-field').val(),
                    condition: $rule.find('.fp-rule-condition').val(),
                    value: $rule.find('.fp-rule-value').val(),
                    action: $rule.find('.fp-rule-action').val(),
                    target_fields: targetValues
                });
            });
            
            return rules;
        },
        
        /**
         * Toggle select all submissions
         */
        toggleSelectAll: function() {
            var checked = $(this).prop('checked');
            $('.fp-submission-checkbox').prop('checked', checked);
            FPFormsAdmin.updateSelectedCount();
        },
        
        /**
         * Aggiorna contatore selezionate
         */
        updateSelectedCount: function() {
            var count = $('.fp-submission-checkbox:checked').length;
            var $counter = $('.fp-selected-count');
            
            if (count > 0) {
                $counter.find('strong').text(count);
                $counter.show();
            } else {
                $counter.hide();
            }
        },
        
        /**
         * Applica bulk action
         */
        applyBulkAction: function(e) {
            e.preventDefault();
            
            var action = $('#fp-bulk-action').val();
            var submissionIds = [];
            
            $('.fp-submission-checkbox:checked').each(function() {
                submissionIds.push($(this).val());
            });
            
            if (submissionIds.length === 0) {
                if (typeof window.fpToast !== 'undefined') fpToast.warning('Seleziona almeno una submission');
                return;
            }
            
            if (!action) {
                if (typeof window.fpToast !== 'undefined') fpToast.warning('Seleziona un\'azione da applicare');
                return;
            }
            
            var $btn = $(this);
            var self = this;
            
            // Confirm per delete
            if (action === 'delete') {
                window.fpConfirm('Eliminare ' + submissionIds.length + ' submissions? Questa azione non può essere annullata.', {
                    title: 'Elimina Submissions',
                    confirmText: 'Elimina',
                    cancelText: 'Annulla',
                    onConfirm: function() {
                        executeBulkAction();
                    }
                });
                return;
            }
            
            executeBulkAction();
            
            function executeBulkAction() {
                if (typeof window.fpLoadingButton !== 'undefined') fpLoadingButton($btn, 'Elaborazione...');
                if (typeof window.fpProgress !== 'undefined') fpProgress.show(50);
                
                $.ajax({
                url: fpFormsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fp_forms_bulk_action_submissions',
                    nonce: fpFormsAdmin.nonce,
                    bulk_action: action,
                    submission_ids: submissionIds
                },
                success: function(response) {
                    if (typeof window.fpProgress !== 'undefined') fpProgress.show(100);
                    if (response.success) {
                        if (response.data.csv) {
                            var raw = atob(response.data.csv);
                            var bytes = new Uint8Array(raw.length);
                            for (var i = 0; i < raw.length; i++) { bytes[i] = raw.charCodeAt(i); }
                            var blob = new Blob([bytes], {type: 'text/csv;charset=utf-8;'});
                            var link = document.createElement('a');
                            link.href = URL.createObjectURL(blob);
                            link.download = response.data.filename || 'export.csv';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            URL.revokeObjectURL(link.href);
                            if (typeof window.fpToast !== 'undefined') fpToast.success(response.data.message || 'Export completato!');
                            if (typeof window.fpLoadingButtonReset !== 'undefined') fpLoadingButtonReset($btn);
                            if (typeof window.fpProgress !== 'undefined') fpProgress.hide();
                        } else {
                            if (typeof window.fpToast !== 'undefined') fpToast.success(response.data.message || 'Operazione completata!');
                            setTimeout(function() {
                                location.reload();
                            }, 800);
                        }
                    } else {
                        if (typeof window.fpToast !== 'undefined') fpToast.error(response.data.message || fpFormsAdmin.strings.error);
                        if (typeof window.fpLoadingButtonReset !== 'undefined') fpLoadingButtonReset($btn);
                        if (typeof window.fpProgress !== 'undefined') fpProgress.hide();
                    }
                },
                error: function() {
                    if (typeof window.fpToast !== 'undefined') fpToast.error(fpFormsAdmin.strings.error);
                    if (typeof window.fpLoadingButtonReset !== 'undefined') fpLoadingButtonReset($btn);
                    if (typeof window.fpProgress !== 'undefined') fpProgress.hide();
                }
            });
            }
        }
    };

    // Inizializza al document ready
    $(document).ready(function() {
        try {
            FPFormsAdmin.init();
        } catch (err) {
            if ( window.fpFormsDebug || ( typeof fpFormsAdmin !== 'undefined' && fpFormsAdmin.debug ) ) {
                console.error('FP Forms Admin init error:', err);
            }
        }
        try {
            FPFormsSettings.init();
        } catch (err) {
            if ( window.fpFormsDebug || ( typeof fpFormsAdmin !== 'undefined' && fpFormsAdmin.debug ) ) {
                console.error('FP Forms Settings init error:', err);
            }
        }
    });
    
    /**
     * FP Forms - Settings Page Scripts
     */
    var FPFormsSettings = {
        
        init: function() {
            this.initColorPicker();
            this.initRecaptchaToggle();
            this.initRecaptchaTest();
            this.initBrevoHandlers();
            this.initMetaHandlers();
            this.initSimulationRunner();
        },
        
        /**
         * Sincronizza color picker con text input
         * BUGFIX #17: Previeni listener duplicati con .off() prima di .on()
         */
        initColorPicker: function() {
            // Remove existing handlers to prevent memory leaks
            $(document).off('input', 'input[name="submit_button_color"]');
            $(document).off('input', 'input[name="submit_button_color_text"]');
            
            // Sync color picker → text input
            $(document).on('input', 'input[name="submit_button_color"]', function() {
                $(this).next('input[name="submit_button_color_text"]').val(this.value);
            });
            
            // Sync text input → color picker (validato)
            $(document).on('input', 'input[name="submit_button_color_text"]', function() {
                var color = this.value;
                if (/^#[0-9A-F]{6}$/i.test(color)) {
                    $(this).prev('input[name="submit_button_color"]').val(color);
                }
            });
        },
        
        /**
         * Toggle score minimo v2/v3
         */
        initRecaptchaToggle: function() {
            var $versionSelect = $('#recaptcha_version');
            if (!$versionSelect.length) return;
            
            $versionSelect.on('change', function() {
                var version = $(this).val();
                if (version === 'v3') {
                    $('#recaptcha_score_row').show();
                } else {
                    $('#recaptcha_score_row').hide();
                }
            });
        },
        
        /**
         * Test connessione reCAPTCHA
         */
        initRecaptchaTest: function() {
            $('#fp-test-recaptcha').on('click', function(e) {
                e.preventDefault();
                
                var $btn = $(this);
                var $result = $('#fp-recaptcha-test-result');
                var originalHtml = $btn.html();
                
                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Testing...');
                $result.html('');
                
                $.ajax({
                    url: fpFormsAdmin.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'fp_forms_test_recaptcha',
                        nonce: fpFormsAdmin.nonce
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalHtml);
                        
                        if (response.success) {
                            $result.empty().append(FPFormsAdmin._safeNotice('success', response.data.message));
                        } else {
                            $result.empty().append(FPFormsAdmin._safeNotice('error', response.data.message));
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                        $result.empty().append(FPFormsAdmin._safeNotice('error', 'Errore di connessione'));
                    }
                });
            });
        },
        
        /**
         * Brevo handlers
         */
        initBrevoHandlers: function() {
            // Test connessione
            $('#fp-test-brevo').on('click', function(e) {
                e.preventDefault();
                
                var $btn = $(this);
                var $result = $('#fp-brevo-test-result');
                var originalHtml = $btn.html();
                
                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Testing...');
                $result.html('');
                
                $.ajax({
                    url: fpFormsAdmin.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'fp_forms_test_brevo',
                        nonce: fpFormsAdmin.nonce
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalHtml);
                        
                        if (response.success) {
                            var $extra = null;
                            if (response.data.account) {
                                $extra = $('<small>');
                                $extra.append(document.createTextNode(
                                    ' Email: ' + String(response.data.account.email || '') +
                                    ' | Plan: ' + String(response.data.account.plan || '')
                                ));
                            }
                            $result.empty().append(FPFormsAdmin._safeNotice('success', response.data.message, $extra));
                        } else {
                            $result.empty().append(FPFormsAdmin._safeNotice('error', response.data.message));
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                        $result.empty().append(FPFormsAdmin._safeNotice('error', 'Errore di connessione'));
                    }
                });
            });
            
            // Carica liste
            $('#fp-load-brevo-lists').on('click', function(e) {
                e.preventDefault();
                
                var $btn = $(this);
                var $container = $('#fp-brevo-lists-container');
                var originalHtml = $btn.html();
                
                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Caricamento...');
                $container.html('');
                
                $.ajax({
                    url: fpFormsAdmin.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'fp_forms_load_brevo_lists',
                        nonce: fpFormsAdmin.nonce
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalHtml);
                        
                        if (response.success && response.data.lists) {
                            var $notice = $('<div>').addClass('notice notice-info inline').css({'margin': '0', 'padding': '8px 12px'});
                            $notice.append($('<strong>').text('Liste disponibili:'));
                            var $ul = $('<ul>').css({'margin': '8px 0 0 20px'});
                            
                            response.data.lists.forEach(function(list) {
                                var $li = $('<li>');
                                $li.append($('<code>').text(String(list.id)));
                                $li.append(document.createTextNode(' - ' + String(list.name) + ' (' + parseInt(list.total_subscribers || 0) + ' contatti)'));
                                $ul.append($li);
                            });
                            
                            $notice.append($ul);
                            $container.empty().append($notice);
                        } else {
                            var errMsg = (response.data && response.data.message) ? response.data.message : 'Errore caricamento liste';
                            $container.empty().append(FPFormsAdmin._safeNotice('error', errMsg));
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                        $container.empty().append(FPFormsAdmin._safeNotice('error', 'Errore di connessione'));
                    }
                });
            });
        },
        
        /**
         * Meta Pixel handlers
         */
        initMetaHandlers: function() {
            // Test connessione
            $('#fp-test-meta').on('click', function(e) {
                e.preventDefault();
                
                var $btn = $(this);
                var $result = $('#fp-meta-test-result');
                var originalHtml = $btn.html();
                
                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Testing...');
                $result.html('');
                
                $.ajax({
                    url: fpFormsAdmin.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'fp_forms_test_meta',
                        nonce: fpFormsAdmin.nonce
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalHtml);
                        
                        if (response.success) {
                            $result.empty().append(FPFormsAdmin._safeNotice('success', response.data.message));
                        } else {
                            $result.empty().append(FPFormsAdmin._safeNotice('error', response.data.message));
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                        $result.empty().append(FPFormsAdmin._safeNotice('error', 'Errore di connessione'));
                    }
                });
            });
        },

        /**
         * Simulazione flussi (dry-run): email, tracking, integrazioni.
         */
        initSimulationRunner: function() {
            $('#fp-run-simulation').on('click', function(e) {
                e.preventDefault();

                var $btn = $(this);
                var $result = $('#fp-simulation-result');
                var formId = parseInt($('#fp_simulation_form_id').val(), 10) || 0;
                var originalHtml = $btn.html();

                if (!formId) {
                    if (typeof window.fpToast !== 'undefined') {
                        fpToast.warning('Seleziona un form da simulare');
                    }
                    return;
                }

                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Simulazione...');
                $result.html('');

                $.ajax({
                    url: fpFormsAdmin.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'fp_forms_run_simulation',
                        nonce: fpFormsAdmin.nonce,
                        form_id: formId
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalHtml);

                        if (response.success && response.data && response.data.report) {
                            FPFormsSettings.renderSimulationReport(response.data.report);
                            if (typeof window.fpToast !== 'undefined') {
                                fpToast.success(response.data.message || 'Simulazione completata');
                            }
                        } else {
                            var msg = (response.data && response.data.message) ? response.data.message : 'Errore simulazione';
                            $result.empty().append(FPFormsAdmin._safeNotice('error', msg));
                            if (typeof window.fpToast !== 'undefined') {
                                fpToast.error(msg);
                            }
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                        $result.empty().append(FPFormsAdmin._safeNotice('error', 'Errore di connessione durante la simulazione'));
                        if (typeof window.fpToast !== 'undefined') {
                            fpToast.error('Errore di connessione durante la simulazione');
                        }
                    }
                });
            });
        },

        /**
         * Render report simulazione in formato leggibile.
         */
        renderSimulationReport: function(report) {
            var $result = $('#fp-simulation-result');
            var checks = Array.isArray(report.checks) ? report.checks : [];
            var summary = report.summary || {};
            var ok = parseInt(summary.ok || 0, 10);
            var warning = parseInt(summary.warning || 0, 10);
            var disabled = parseInt(summary.disabled || 0, 10);

            var $box = $('<div>').css({
                background: '#fff',
                border: '1px solid #d0d7de',
                borderRadius: '8px',
                padding: '12px'
            });

            var title = 'Report simulazione - #' + String(report.form_id || '') + ' ' + String(report.form_title || '');
            $box.append($('<p>').css({margin: '0 0 10px', fontWeight: '600'}).text(title));
            $box.append($('<p>').css({margin: '0 0 12px', color: '#4b5563'}).text(
                'Esito: ' + ok + ' OK, ' + warning + ' warning, ' + disabled + ' disabilitati.'
            ));

            var $list = $('<ul>').css({margin: '0 0 10px 18px'});
            checks.forEach(function(item) {
                var status = String(item.status || 'warning');
                var label = String(item.label || item.key || 'Check');
                var details = String(item.details || '');
                var badge = status === 'ok' ? 'OK' : (status === 'disabled' ? 'DISABILITATO' : 'WARNING');
                var prefix = status === 'ok' ? '✅ ' : (status === 'disabled' ? '⚪ ' : '⚠️ ');
                $list.append($('<li>').text(prefix + label + ' [' + badge + '] - ' + details));
            });
            $box.append($list);

            if (report.simulated_at) {
                $box.append($('<small>').css({color: '#6b7280'}).text('Eseguita il: ' + String(report.simulated_at)));
            }

            $result.empty().append($box);
        },
        
        /**
         * Aggiungi webhook
         */
        addWebhook: function(e) {
            e.preventDefault();
            
            var index = $('.fp-webhook-item').length;
            var template = $('#fp-webhook-template').html();
            
            template = template.replace(/\{\{index\}\}/g, index);
            template = template.replace(/\{\{webhookNumber\}\}/g, index + 1);
            template = template.replace(/\{\{webhookId\}\}/g, 'webhook_' + Date.now());
            
            $('#fp-webhooks-container').append(template);
        },
        
        /**
         * Elimina webhook
         */
        deleteWebhook: function(e) {
            e.preventDefault();
            
            var $webhook = $(this).closest('.fp-webhook-item');
            
            window.fpConfirm('Eliminare questo webhook?', {
                title: 'Elimina Webhook',
                confirmText: 'Elimina',
                cancelText: 'Annulla',
                onConfirm: function() {
                    $webhook.fadeOut(function() {
                        $(this).remove();
                        // Rinumera webhook
                        $('.fp-webhook-item').each(function(idx) {
                            $(this).find('.fp-webhook-number').text('Webhook #' + (idx + 1));
                        });
                    });
                }
            });
        },
        
        /**
         * Test webhook
         */
        testWebhook: function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var $webhook = $btn.closest('.fp-webhook-item');
            var $result = $webhook.find('.fp-webhook-test-result');
            var url = $webhook.find('.fp-webhook-url').val();
            var secret = $webhook.find('.fp-webhook-secret').val();
            
            if (!url) {
                fpToast.warning('Inserisci un URL webhook prima di testare');
                return;
            }
            
            var originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Testing...');
            $result.html('');
            
            $.ajax({
                url: fpFormsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fp_forms_test_webhook',
                    nonce: fpFormsAdmin.nonce,
                    url: url,
                    secret: secret || ''
                },
                success: function(response) {
                    $btn.prop('disabled', false).html(originalHtml);
                    
                    if (response.success) {
                        $result.removeClass('error').addClass('success').text('✓ ' + response.data.message);
                        fpToast.success('Webhook testato con successo!');
                    } else {
                        $result.removeClass('success').addClass('error').text('✕ ' + (response.data.message || 'Errore'));
                        fpToast.error(response.data.message || 'Errore durante il test webhook');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html(originalHtml);
                    $result.removeClass('success').addClass('error').text('✕ Errore di connessione');
                    fpToast.error('Errore di connessione durante il test');
                }
            });
        }
    };
    
    // Bind eventi webhooks (4 arg espliciti: events, selector, data, handler evita "guid on string")
    $(document).on('click', '#fp-add-webhook', null, FPFormsSettings.addWebhook);
    $(document).on('click', '.fp-webhook-delete', FPFormsSettings.deleteWebhook);
    $(document).on('click', '.fp-test-webhook', FPFormsSettings.testWebhook);
    
    /**
     * Ripristina snapshot
     */
    FPFormsAdmin.restoreSnapshot = function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var snapshotId = $btn.data('snapshot-id');
        var formId = $btn.data('form-id');
        
        window.fpConfirm('Ripristinare questa versione del form? La versione corrente verrà salvata come nuovo snapshot.', {
            title: 'Ripristina Snapshot',
            confirmText: 'Ripristina',
            cancelText: 'Annulla',
            onConfirm: function() {
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Ripristino...');
                
                $.ajax({
                    url: fpFormsAdmin.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'fp_forms_restore_snapshot',
                        nonce: fpFormsAdmin.nonce,
                        form_id: formId,
                        snapshot_id: snapshotId
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof window.fpToast !== 'undefined') fpToast.success(response.data.message);
                            setTimeout(function() {
                                var redir = (typeof response.data.redirect === 'string') ? response.data.redirect : '';
                                if (redir && (redir.charAt(0) === '/' || redir.indexOf(window.location.origin) === 0)) {
                                    window.location.href = redir;
                                }
                            }, 1000);
                        } else {
                            if (typeof window.fpToast !== 'undefined') fpToast.error(response.data.message || 'Errore nel ripristinare lo snapshot');
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function() {
                        if (typeof window.fpToast !== 'undefined') fpToast.error('Errore di connessione');
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    };
    
    // Bind evento restore snapshot
    $(document).on('click', '.fp-restore-snapshot', FPFormsAdmin.restoreSnapshot);

    
})(jQuery);
