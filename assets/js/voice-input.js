/**
 * FP Forms - Voice Input Support
 * Speech-to-text per campi form usando Web Speech API
 */

(function($) {
    'use strict';
    
    /**
     * Voice Input Manager
     */
    window.fpVoiceInput = {
        
        /**
         * Inizializza voice input per form
         */
        init: function($form) {
            var self = this;
            
            // Verifica supporto Web Speech API
            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                return; // Browser non supporta
            }
            
            // Aggiungi pulsante microfono ai campi con data-voice-input o tutti i text/textarea/email
            $form.find('input[type="text"], input[type="email"], input[type="tel"], textarea').each(function() {
                var $field = $(this);
                var $fieldContainer = $field.closest('.fp-forms-field');
                
                // Verifica se voice input è abilitato per questo campo
                var voiceEnabled = $fieldContainer.data('voice-input') === true || 
                                   $fieldContainer.data('voice-input') === 'true';
                
                // Se non abilitato esplicitamente, abilita per default (compatibilità)
                if (voiceEnabled === false) {
                    return;
                }
                
                // Salta se già ha voice button
                if ($field.siblings('.fp-voice-input-btn').length) {
                    return;
                }
                
                // Crea pulsante microfono
                var $btn = $('<button type="button" class="fp-voice-input-btn" aria-label="Inserisci testo con voce" title="Inserisci testo con voce">' +
                    '<span class="dashicons dashicons-microphone"></span>' +
                '</button>');
                
                // Posiziona dopo il campo
                $field.after($btn);
                
                // Wrapper per styling
                if (!$field.parent().hasClass('fp-voice-input-wrapper')) {
                    $field.wrap('<div class="fp-voice-input-wrapper"></div>');
                }
                
                // Bind click
                $btn.on('click', function(e) {
                    e.preventDefault();
                    self.startRecognition($field, $btn);
                });
            });
        },
        
        /**
         * Avvia riconoscimento vocale
         */
        startRecognition: function($field, $btn) {
            var self = this;
            
            // Verifica supporto
            var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) {
                if (typeof window.fpToast !== 'undefined') {
                    window.fpToast.error('Il tuo browser non supporta il riconoscimento vocale');
                }
                return;
            }
            
            var recognition = new SpeechRecognition();
            recognition.lang = 'it-IT'; // Italiano
            recognition.continuous = false;
            recognition.interimResults = false;
            
            // Stato
            var isRecording = false;
            
            // Start
            recognition.onstart = function() {
                isRecording = true;
                $btn.addClass('fp-voice-recording');
                $field.addClass('fp-voice-active');
                
                if (typeof window.fpToast !== 'undefined') {
                    window.fpToast.info('In ascolto... Parla ora', 2000);
                }
            };
            
            // Risultato
            recognition.onresult = function(event) {
                var transcript = event.results[0][0].transcript;
                
                // Aggiungi al campo (non sostituisce, aggiunge)
                var currentValue = $field.val();
                var newValue = currentValue ? currentValue + ' ' + transcript : transcript;
                $field.val(newValue);
                
                // Trigger change event
                $field.trigger('input').trigger('change');
            };
            
            // Fine
            recognition.onend = function() {
                isRecording = false;
                $btn.removeClass('fp-voice-recording');
                $field.removeClass('fp-voice-active');
            };
            
            // Errore
            recognition.onerror = function(event) {
                isRecording = false;
                $btn.removeClass('fp-voice-recording');
                $field.removeClass('fp-voice-active');
                
                var errorMsg = 'Errore riconoscimento vocale';
                
                if (event.error === 'no-speech') {
                    errorMsg = 'Nessun parlato rilevato. Riprova.';
                } else if (event.error === 'not-allowed') {
                    errorMsg = 'Permesso microfono negato. Abilita nelle impostazioni browser.';
                }
                
                if (typeof window.fpToast !== 'undefined') {
                    window.fpToast.error(errorMsg);
                }
            };
            
            // Avvia riconoscimento
            try {
                recognition.start();
            } catch (e) {
                if (typeof window.fpToast !== 'undefined') {
                    window.fpToast.error('Impossibile avviare il riconoscimento vocale');
                }
            }
        }
    };
    
    // Auto-inizializza su form esistenti
    $(document).ready(function() {
        $('.fp-forms-form').each(function() {
            fpVoiceInput.init($(this));
        });
    });
    
    // Inizializza su form dinamici
    $(document).on('fp_forms_form_loaded', function(e, $form) {
        fpVoiceInput.init($form);
    });
    
})(jQuery);

