/**
 * FP Forms - Progressive Form Auto-Save
 * Salva automaticamente i dati del form in LocalStorage
 */

(function($) {
    'use strict';
    
    /**
     * Progressive Save Manager
     */
    window.fpProgressiveSave = {
        
        /**
         * Inizializza auto-save per form
         */
        init: function($form) {
            var formId = $form.data('form-id');
            if (!formId) {
                return;
            }
            
            var self = this;
            var storageKey = 'fp_forms_autosave_' + formId;
            var saveInterval = 30000; // 30 secondi
            var saveTimer;
            
            // Ripristina dati salvati se presenti
            this.restoreFormData($form, storageKey);
            
            // Auto-save periodico
            saveTimer = setInterval(function() {
                self.saveFormData($form, storageKey);
            }, saveInterval);
            
            // Auto-save su change
            $form.on('change input', 'input, textarea, select', function() {
                clearTimeout(self.debounceTimer);
                self.debounceTimer = setTimeout(function() {
                    self.saveFormData($form, storageKey);
                }, 2000); // Debounce 2 secondi
            });
            
            // Salva prima di submit
            $form.on('submit', function() {
                self.saveFormData($form, storageKey);
            });
            
            // Pulisci dopo submit successo
            $form.on('fp_forms_submit_success', function() {
                self.clearFormData(storageKey);
            });
            
            // Salva timer per cleanup
            $form.data('fp-save-timer', saveTimer);
        },
        
        /**
         * Salva dati form in LocalStorage
         */
        saveFormData: function($form, storageKey) {
            try {
                var formData = {};
                var timestamp = Date.now();
                
                $form.find('input, textarea, select').each(function() {
                    var $field = $(this);
                    var name = $field.attr('name');
                    var type = $field.attr('type');
                    
                    // Salta campi di sistema
                    if (!name || name === 'action' || name === 'form_id' || name === 'nonce' || name.indexOf('fp_hp_') === 0) {
                        return;
                    }
                    
                    // Gestisci diversi tipi di campo
                    if (type === 'checkbox' || type === 'radio') {
                        var checked = $form.find('[name="' + name + '"]:checked');
                        if (checked.length > 0) {
                            if (checked.length === 1) {
                                formData[name] = checked.val();
                            } else {
                                formData[name] = checked.map(function() {
                                    return $(this).val();
                                }).get();
                            }
                        }
                    } else if (type === 'file') {
                        // File non salvabili in LocalStorage, salta
                        return;
                    } else {
                        var value = $field.val();
                        if (value) {
                            formData[name] = value;
                        }
                    }
                });
                
                // Salva con timestamp
                var saveData = {
                    data: formData,
                    timestamp: timestamp,
                    formId: $form.data('form-id')
                };
                
                localStorage.setItem(storageKey, JSON.stringify(saveData));
                
            } catch (e) {
                // LocalStorage potrebbe essere disabilitato o pieno
                console.warn('[FP Forms] Auto-save failed:', e);
            }
        },
        
        /**
         * Ripristina dati form da LocalStorage
         */
        restoreFormData: function($form, storageKey) {
            try {
                var saved = localStorage.getItem(storageKey);
                if (!saved) {
                    return;
                }
                
                var saveData = JSON.parse(saved);
                
                // Verifica che sia per lo stesso form
                if (saveData.formId !== $form.data('form-id')) {
                    return;
                }
                
                // Verifica che non sia troppo vecchio (max 7 giorni)
                var maxAge = 7 * 24 * 60 * 60 * 1000; // 7 giorni in ms
                if (Date.now() - saveData.timestamp > maxAge) {
                    localStorage.removeItem(storageKey);
                    return;
                }
                
                // Mostra notifica di ripristino
                var $notice = $('<div class="fp-autosave-notice">' +
                    '<span class="dashicons dashicons-info"></span> ' +
                    'Abbiamo trovato dati salvati. <a href="#" class="fp-restore-data">Ripristina</a> o <a href="#" class="fp-dismiss-restore">Ignora</a>' +
                '</div>');
                
                $form.prepend($notice);
                
                // Ripristina dati
                $notice.find('.fp-restore-data').on('click', function(e) {
                    e.preventDefault();
                    
                    var formData = saveData.data;
                    
                    for (var name in formData) {
                        var $field = $form.find('[name="' + name + '"]');
                        var value = formData[name];
                        
                        if ($field.length) {
                            if ($field.is(':checkbox') || $field.is(':radio')) {
                                if (Array.isArray(value)) {
                                    value.forEach(function(val) {
                                        $form.find('[name="' + name + '"][value="' + val + '"]').prop('checked', true);
                                    });
                                } else {
                                    $form.find('[name="' + name + '"][value="' + value + '"]').prop('checked', true);
                                }
                            } else if ($field.is('select')) {
                                $field.val(value);
                            } else {
                                $field.val(value);
                            }
                        }
                    }
                    
                    $notice.fadeOut(function() {
                        $(this).remove();
                    });
                    
                    if (typeof window.fpToast !== 'undefined') {
                        window.fpToast.success('Dati ripristinati con successo!');
                    }
                });
                
                // Ignora ripristino
                $notice.find('.fp-dismiss-restore').on('click', function(e) {
                    e.preventDefault();
                    $notice.fadeOut(function() {
                        $(this).remove();
                    });
                });
                
            } catch (e) {
                console.warn('[FP Forms] Auto-restore failed:', e);
            }
        },
        
        /**
         * Pulisci dati salvati
         */
        clearFormData: function(storageKey) {
            try {
                localStorage.removeItem(storageKey);
            } catch (e) {
                console.warn('[FP Forms] Clear auto-save failed:', e);
            }
        }
    };
    
    // Auto-inizializza su form esistenti
    $(document).ready(function() {
        $('.fp-forms-form').each(function() {
            fpProgressiveSave.init($(this));
        });
    });
    
    // Inizializza su form dinamici
    $(document).on('fp_forms_form_loaded', function(e, $form) {
        fpProgressiveSave.init($form);
    });
    
})(jQuery);

