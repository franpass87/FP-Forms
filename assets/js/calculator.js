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
                
                // Evita doppia inizializzazione sullo stesso campo (previene memory leak)
                if ($calc.data('fp-calc-init')) { return; }
                $calc.data('fp-calc-init', true);
                
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
            
            // Calcola risultato con parser matematico ricorsivo sicuro (no eval/Function)
            try {
                var result;
                
                // Normalizza segni doppi prima del parsing
                var cleanExpression = expression.replace(/\s/g, '')
                    .replace(/--/g, '+').replace(/-\+/g, '-')
                    .replace(/\+-/g, '-').replace(/\+\+/g, '+');
                
                // Whitelist: solo caratteri matematici sicuri
                if (!/^[0-9+\-*/().]+$/.test(cleanExpression)) {
                    $calc.val('Error');
                    return;
                }
                
                if (cleanExpression.length === 0) {
                    $calc.val('0');
                    return;
                }
                
                // Parser matematico ricorsivo: addizione/sottrazione → moltiplicazione/divisione → primario
                result = mathEval(cleanExpression);
                
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

    /**
     * Parser matematico ricorsivo sicuro — sostituisce eval/Function().
     * Supporta: +, -, *, /, parentesi, numeri decimali e negativi.
     * Non usa eval né Function constructor.
     */
    function mathEval(expr) {
        var pos = 0;

        function parseAdditive() {
            var left = parseMultiplicative();
            while (pos < expr.length && (expr[pos] === '+' || expr[pos] === '-')) {
                var op = expr[pos++];
                var right = parseMultiplicative();
                left = op === '+' ? left + right : left - right;
            }
            return left;
        }

        function parseMultiplicative() {
            var left = parsePrimary();
            while (pos < expr.length && (expr[pos] === '*' || expr[pos] === '/')) {
                var op = expr[pos++];
                var right = parsePrimary();
                if (op === '/') {
                    if (right === 0) { return NaN; }
                    left = left / right;
                } else {
                    left = left * right;
                }
            }
            return left;
        }

        function parsePrimary() {
            // Numero negativo unario
            if (expr[pos] === '-') {
                pos++;
                return -parsePrimary();
            }
            // Parentesi
            if (expr[pos] === '(') {
                pos++; // consuma '('
                var val = parseAdditive();
                if (expr[pos] === ')') { pos++; } // consuma ')'
                return val;
            }
            // Numero
            var start = pos;
            while (pos < expr.length && /[0-9.]/.test(expr[pos])) { pos++; }
            var numStr = expr.slice(start, pos);
            return numStr.length > 0 ? parseFloat(numStr) : NaN;
        }

        var result = parseAdditive();
        return (pos === expr.length) ? result : NaN;
    }

})(jQuery);

