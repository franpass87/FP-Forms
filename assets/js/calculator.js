/**
 * FP Forms - Form Calculator
 * Calcola valori in base a formula
 */

(function($) {
    'use strict';
    
    /**
     * Inizializza calculator per form
     */
    window.fpFormsCalculator = {
        
        init: function($form) {
            var self = this;
            
            $form.find('.fp-calculated-field').each(function() {
                var $calc = $(this);
                var formula = $calc.data('formula');
                
                if (!formula) {
                    return;
                }
                
                // Trova campi referenziati nella formula
                var fieldNames = self.extractFieldNames(formula);
                
                // Bind change events per ogni campo referenziato
                fieldNames.forEach(function(fieldName) {
                    var $field = $form.find('[name="fp_field_' + fieldName + '"]');
                    
                    if ($field.length) {
                        $field.on('change keyup input', function() {
                            self.calculate($form, $calc, formula);
                        });
                    }
                });
                
                // Calcolo iniziale
                self.calculate($form, $calc, formula);
            });
        },
        
        /**
         * Estrae nomi campi dalla formula
         */
        extractFieldNames: function(formula) {
            var matches = formula.match(/\{([^}]+)\}/g);
            if (!matches) {
                return [];
            }
            
            return matches.map(function(match) {
                return match.replace(/[{}]/g, '');
            });
        },
        
        /**
         * Calcola valore
         */
        calculate: function($form, $calc, formula) {
            var format = $calc.data('format') || 'number';
            var decimals = parseInt($calc.data('decimals')) || 2;
            
            // Ottieni valori campi
            var values = {};
            var fieldNames = this.extractFieldNames(formula);
            
            fieldNames.forEach(function(fieldName) {
                var $field = $form.find('[name="fp_field_' + fieldName + '"]');
                var value = 0;
                
                if ($field.length) {
                    if ($field.is(':checkbox') || $field.is(':radio')) {
                        // Per checkbox/radio, somma valori selezionati
                        $form.find('[name="fp_field_' + fieldName + '"]:checked').each(function() {
                            value += parseFloat($(this).val()) || 0;
                        });
                    } else {
                        value = parseFloat($field.val()) || 0;
                    }
                }
                
                values[fieldName] = value;
            });
            
            // Sostituisci variabili nella formula
            var expression = formula;
            for (var key in values) {
                var regex = new RegExp('\\{' + key + '\\}', 'g');
                expression = expression.replace(regex, values[key]);
            }
            
            // Calcola risultato con validazione sicurezza migliorata
            try {
                var result;
                
                // Validazione sicurezza: solo numeri, operatori matematici e parentesi
                // Rimuovi spazi per validazione più precisa
                var cleanExpression = expression.replace(/\s/g, '');
                
                // Whitelist: solo caratteri matematici sicuri
                if (!/^[0-9+\-*/().]+$/.test(cleanExpression)) {
                    $calc.val('Error');
                    return;
                }
                
                // Verifica parentesi bilanciate
                var openParens = (cleanExpression.match(/\(/g) || []).length;
                var closeParens = (cleanExpression.match(/\)/g) || []).length;
                if (openParens !== closeParens) {
                    $calc.val('Error');
                    return;
                }
                
                // Verifica che non ci siano operatori consecutivi (eccetto +- per numeri negativi)
                if (/[+\-*/]{2,}/.test(cleanExpression.replace(/[+\-][0-9]/g, ''))) {
                    $calc.val('Error');
                    return;
                }
                
                // Usa Function constructor con validazione aggiuntiva
                // Rimuovi qualsiasi carattere non valido prima di eseguire
                var safeExpression = cleanExpression.replace(/[^0-9+\-*/().]/g, '');
                
                if (safeExpression.length === 0) {
                    $calc.val('0');
                    return;
                }
                
                result = Function('"use strict"; return (' + safeExpression + ')')();
                
                // Verifica che il risultato sia un numero valido
                if (!isFinite(result) || isNaN(result)) {
                    $calc.val('Error');
                    return;
                }
                
                // Formatta risultato
                if (format === 'currency') {
                    $calc.val('€ ' + result.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
                } else if (format === 'percentage') {
                    $calc.val(result.toFixed(decimals) + '%');
                } else {
                    $calc.val(result.toFixed(decimals));
                }
            } catch (e) {
                console.warn('[FP Forms Calculator] Error:', e);
                $calc.val('Error');
            }
        }
    };
    
    // Auto-inizializza su form esistenti
    $(document).ready(function() {
        $('.fp-forms-form').each(function() {
            fpFormsCalculator.init($(this));
        });
    });
    
    // Inizializza su form dinamici
    $(document).on('fp_forms_form_loaded', function(e, $form) {
        fpFormsCalculator.init($form);
    });
    
})(jQuery);

