/**
 * FP Forms - Conditional Logic
 * Sistema per mostrare/nascondere campi in base a condizioni
 */

(function($) {
    'use strict';
    
    class FPConditionalLogic {
        
        constructor( formElement, rulesOrData ) {
            this.form = formElement;
            this.formId = $(formElement).data('form-id');
            if ( Array.isArray( rulesOrData ) ) {
                this.rules = rulesOrData;
                this.operator = 'or';
            } else if ( rulesOrData && Array.isArray( rulesOrData.rules ) ) {
                this.rules = rulesOrData.rules;
                this.operator = ( rulesOrData.operator === 'and' ) ? 'and' : 'or';
            } else {
                this.rules = [];
                this.operator = 'or';
            }
            this._originalRequired = new Map();

            if ( this.rules.length > 0 ) {
                this.init();
            }
        }
        
        init() {
            if (window.fpFormsDebug) {
                console.log('[FP Forms] Initializing conditional logic with', this.rules.length, 'rules');
            }
            
            // Salva lo stato required originale di ogni campo target
            this.rules.forEach( rule => {
                (rule.target_fields || []).forEach( fieldName => {
                    if ( ! this._originalRequired.has( fieldName ) ) {
                        const fieldContainer = this.getFieldContainer( fieldName );
                        if ( fieldContainer ) {
                            const field = fieldContainer.querySelector( 'input, select, textarea' );
                            this._originalRequired.set( fieldName, field ? field.hasAttribute('required') : false );
                        }
                    }
                });
            });
            
            // Bind events per ogni regola con namespace .fpCL per cleanup
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
            
            // Namespace .fpCL per permettere rimozione con destroy()
            $(triggerField).on('change.fpCL keyup.fpCL', () => {
                this.evaluateRule( rule );
            });
        }
        
        /**
         * Rimuove tutti i listener registrati da questa istanza
         */
        destroy() {
            this.rules.forEach( rule => {
                const triggerField = this.getFieldElement( rule.trigger_field );
                if ( triggerField ) {
                    $(triggerField).off('.fpCL');
                }
            });
        }
        
        evaluateRule( rule ) {
            const value = this.getFieldValue( rule.trigger_field );
            const shouldApply = this.checkCondition( value, rule.condition, rule.value );
            if ( window.fpFormsDebug ) {
                console.log( '[FP Forms] Evaluating rule:', rule, 'Result:', shouldApply );
            }
            if ( shouldApply ) {
                this.applyAction( rule.action, rule.target_fields );
            } else {
                this.reverseAction( rule.action, rule.target_fields );
            }
        }

        evaluateAllRules() {
            if ( this.operator === 'or' && this.rules.length <= 1 ) {
                this.rules.forEach( rule => this.evaluateRule( rule ) );
                return;
            }
            var self = this;
            var ruleMatches = this.rules.map( function( rule ) {
                var value = self.getFieldValue( rule.trigger_field );
                return self.checkCondition( value, rule.condition, rule.value );
            } );
            var targetFields = {};
            this.rules.forEach( function( rule, idx ) {
                ( rule.target_fields || [] ).forEach( function( fieldName ) {
                    if ( ! targetFields[ fieldName ] ) targetFields[ fieldName ] = { show: [], hide: [], require: [], unrequire: [] };
                    if ( rule.action === 'show' ) targetFields[ fieldName ].show.push( idx );
                    else if ( rule.action === 'hide' ) targetFields[ fieldName ].hide.push( idx );
                    else if ( rule.action === 'require' ) targetFields[ fieldName ].require.push( idx );
                    else if ( rule.action === 'unrequire' ) targetFields[ fieldName ].unrequire.push( idx );
                } );
            } );
            Object.keys( targetFields ).forEach( function( fieldName ) {
                var container = self.getFieldContainer( fieldName );
                if ( ! container ) return;
                var t = targetFields[ fieldName ];
                var anyHide = t.hide.some( function( idx ) { return ruleMatches[ idx ]; } );
                var showMatchCount = t.show.filter( function( idx ) { return ruleMatches[ idx ]; } ).length;
                var requireMatchCount = t.require.filter( function( idx ) { return ruleMatches[ idx ]; } ).length;
                var unrequireMatchCount = t.unrequire.filter( function( idx ) { return ruleMatches[ idx ]; } ).length;
                var visible = ! anyHide && ( t.show.length === 0 || ( self.operator === 'or' ? showMatchCount > 0 : showMatchCount === t.show.length ) );
                var required = t.require.length > 0 && unrequireMatchCount === 0 && ( self.operator === 'or' ? requireMatchCount > 0 : requireMatchCount === t.require.length );
                if ( ! required && t.require.length === 0 ) {
                    required = self._originalRequired.get( fieldName ) || false;
                }
                if ( visible ) {
                    $( container ).slideDown( 200 ).addClass( 'fp-conditional-visible' );
                } else {
                    $( container ).slideUp( 200 ).removeClass( 'fp-conditional-visible' );
                    self.clearFieldValue( fieldName );
                }
                self.setRequired( container, required );
            } );
        }
        
        checkCondition( value, condition, expected ) {
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
            targetFields.forEach( fieldName => {
                const fieldContainer = this.getFieldContainer( fieldName );
                if ( ! fieldContainer ) return;
                
                switch ( action ) {
                    case 'show':
                        // Inverso di show → nascondi
                        $(fieldContainer).slideUp( 200 ).removeClass('fp-conditional-visible');
                        this.clearFieldValue( fieldName );
                        break;
                        
                    case 'hide':
                        // Inverso di hide → mostra
                        $(fieldContainer).slideDown( 200 ).addClass('fp-conditional-visible');
                        break;
                        
                    case 'require': {
                        // Inverso di require → ripristina lo stato required originale
                        const wasRequired = this._originalRequired.get( fieldName ) || false;
                        this.setRequired( fieldContainer, wasRequired );
                        break;
                    }
                    case 'unrequire': {
                        // Inverso di unrequire → ripristina lo stato required originale
                        const wasReq = this._originalRequired.get( fieldName ) || false;
                        this.setRequired( fieldContainer, wasReq );
                        break;
                    }
                }
            });
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
            
            if ( field.type === 'checkbox' && field.name.includes('[]') ) {
                const checked = this.form.querySelectorAll( `[name="fp_field_${fieldName}[]"]:checked` );
                return Array.from( checked ).map( cb => cb.value ).join( ',' );
            }
            
            if ( field.type === 'radio' ) {
                const checked = this.form.querySelector( `[name="fp_field_${fieldName}"]:checked` );
                return checked ? checked.value : '';
            }
            
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
            const data = $(this).data('conditional-rules') || window['fpFormsRules_' + formId] || [];
            const rules = Array.isArray( data ) ? data : ( data && data.rules ) || [];
            if ( rules.length > 0 ) {
                new FPConditionalLogic( formElement, data );
            }
        });
    });
    
    // Esponi globalmente per uso esterno
    window.FPConditionalLogic = FPConditionalLogic;
    
})(jQuery);
