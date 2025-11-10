/**
 * FP Forms - Conditional Logic
 * Sistema per mostrare/nascondere campi in base a condizioni
 */

(function($) {
    'use strict';
    
    class FPConditionalLogic {
        
        constructor( formElement, rules ) {
            this.form = formElement;
            this.formId = $(formElement).data('form-id');
            this.rules = rules || [];
            
            if ( this.rules.length > 0 ) {
                this.init();
            }
        }
        
        init() {
            // Debug: Conditional logic initialized with rules
            if (window.fpFormsDebug) {
                console.log('[FP Forms] Initializing conditional logic with', this.rules.length, 'rules');
            }
            
            // Bind events per ogni regola
            this.rules.forEach( rule => {
                this.bindRule( rule );
            });
            
            // Valuta tutte le regole al caricamento
            this.evaluateAllRules();
        }
        
        bindRule( rule ) {
            const triggerField = this.getFieldElement( rule.trigger_field );
            
            if ( ! triggerField ) {
                console.warn('[FP Forms] Trigger field not found:', rule.trigger_field);
                return;
            }
            
            // Event listener
            $(triggerField).on('change keyup', () => {
                this.evaluateRule( rule );
            });
        }
        
        evaluateRule( rule ) {
            const value = this.getFieldValue( rule.trigger_field );
            const shouldApply = this.checkCondition( value, rule.condition, rule.value );
            
            // Debug: Rule evaluation
            if (window.fpFormsDebug) {
                console.log('[FP Forms] Evaluating rule:', rule, 'Result:', shouldApply);
            }
            
            if ( shouldApply ) {
                this.applyAction( rule.action, rule.target_fields );
            } else {
                this.reverseAction( rule.action, rule.target_fields );
            }
        }
        
        evaluateAllRules() {
            this.rules.forEach( rule => {
                this.evaluateRule( rule );
            });
        }
        
        checkCondition( value, condition, expected ) {
            // Converti in stringa per confronto
            value = String( value ).trim();
            expected = String( expected ).trim();
            
            switch ( condition ) {
                case 'equals':
                    return value === expected;
                    
                case 'not_equals':
                    return value !== expected;
                    
                case 'contains':
                    return value.includes( expected );
                    
                case 'not_contains':
                    return ! value.includes( expected );
                    
                case 'greater_than':
                    return parseFloat( value ) > parseFloat( expected );
                    
                case 'less_than':
                    return parseFloat( value ) < parseFloat( expected );
                    
                case 'is_empty':
                    return value === '';
                    
                case 'is_not_empty':
                    return value !== '';
                    
                default:
                    return false;
            }
        }
        
        applyAction( action, targetFields ) {
            targetFields.forEach( fieldName => {
                const fieldContainer = this.getFieldContainer( fieldName );
                
                if ( ! fieldContainer ) {
                    return;
                }
                
                switch ( action ) {
                    case 'show':
                        $(fieldContainer).slideDown( 200 ).addClass('fp-conditional-visible');
                        break;
                        
                    case 'hide':
                        $(fieldContainer).slideUp( 200 ).removeClass('fp-conditional-visible');
                        // Clear value quando nascosto
                        this.clearFieldValue( fieldName );
                        break;
                        
                    case 'require':
                        this.setRequired( fieldContainer, true );
                        break;
                        
                    case 'unrequire':
                        this.setRequired( fieldContainer, false );
                        break;
                }
            });
        }
        
        reverseAction( action, targetFields ) {
            const reverseActions = {
                'show': 'hide',
                'hide': 'show',
                'require': 'unrequire',
                'unrequire': 'require'
            };
            
            const reversedAction = reverseActions[ action ];
            
            if ( reversedAction ) {
                this.applyAction( reversedAction, targetFields );
            }
        }
        
        getFieldElement( fieldName ) {
            return this.form.querySelector( `[name="fp_field_${fieldName}"], [name="fp_field_${fieldName}[]"]` );
        }
        
        getFieldContainer( fieldName ) {
            const field = this.getFieldElement( fieldName );
            return field ? field.closest( '.fp-forms-field' ) : null;
        }
        
        getFieldValue( fieldName ) {
            const field = this.getFieldElement( fieldName );
            
            if ( ! field ) {
                return '';
            }
            
            // Checkbox multipli
            if ( field.type === 'checkbox' && field.name.includes('[]') ) {
                const checked = this.form.querySelectorAll( `[name="fp_field_${fieldName}[]"]:checked` );
                return Array.from( checked ).map( cb => cb.value ).join( ',' );
            }
            
            // Radio
            if ( field.type === 'radio' ) {
                const checked = this.form.querySelector( `[name="fp_field_${fieldName}"]:checked` );
                return checked ? checked.value : '';
            }
            
            // Altri campi
            return field.value;
        }
        
        clearFieldValue( fieldName ) {
            const field = this.getFieldElement( fieldName );
            
            if ( ! field ) {
                return;
            }
            
            if ( field.type === 'checkbox' || field.type === 'radio' ) {
                const all = this.form.querySelectorAll( `[name="${field.name}"]` );
                all.forEach( f => f.checked = false );
            } else {
                field.value = '';
            }
        }
        
        setRequired( fieldContainer, required ) {
            const field = fieldContainer.querySelector( 'input, select, textarea' );
            
            if ( ! field ) {
                return;
            }
            
            if ( required ) {
                field.setAttribute( 'required', 'required' );
                const label = fieldContainer.querySelector( '.fp-forms-label' );
                if ( label && ! label.querySelector( '.fp-forms-required' ) ) {
                    const asterisk = document.createElement( 'span' );
                    asterisk.className = 'fp-forms-required';
                    asterisk.textContent = '*';
                    label.appendChild( asterisk );
                }
            } else {
                field.removeAttribute( 'required' );
                const asterisk = fieldContainer.querySelector( '.fp-forms-required' );
                if ( asterisk ) {
                    asterisk.remove();
                }
            }
        }
    }
    
    // Auto-inizializza al document ready
    $(document).ready(function() {
        $('.fp-forms-form').each(function() {
            const formElement = this;
            const formId = $(this).data('form-id');
            
            // Cerca regole nel data attribute o global var
            const rules = $(this).data('conditional-rules') || window['fpFormsRules_' + formId] || [];
            
            if ( rules.length > 0 ) {
                new FPConditionalLogic( formElement, rules );
            }
        });
    });
    
    // Esponi globalmente per uso esterno
    window.FPConditionalLogic = FPConditionalLogic;
    
})(jQuery);

