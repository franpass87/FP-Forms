<?php
namespace FPForms\Templates;

/**
 * Library di template form predefiniti
 */
class Library {
    
    /**
     * Templates disponibili
     */
    private $templates = [];
    
    /**
     * Costruttore
     */
    public function __construct() {
        $this->register_default_templates();
    }
    
    /**
     * Registra template di default
     */
    private function register_default_templates() {
        $this->templates = [
            'contact-simple' => $this->get_contact_simple_template(),
            'quote-request' => $this->get_quote_request_template(),
            'booking' => $this->get_booking_template(),
            'job-application' => $this->get_job_application_template(),
            'newsletter' => $this->get_newsletter_template(),
            'feedback' => $this->get_feedback_template(),
            'support-ticket' => $this->get_support_ticket_template(),
            'event-registration' => $this->get_event_registration_template(),
        ];
    }
    
    /**
     * Ottiene tutti i template
     */
    public function get_templates( $category = null ) {
        if ( $category ) {
            return array_filter( $this->templates, function( $template ) use ( $category ) {
                return isset( $template['category'] ) && $template['category'] === $category;
            } );
        }
        
        return $this->templates;
    }
    
    /**
     * Ottiene singolo template
     */
    public function get_template( $template_id ) {
        return isset( $this->templates[ $template_id ] ) ? $this->templates[ $template_id ] : null;
    }
    
    /**
     * Importa template e crea form
     */
    public function import_template( $template_id, $custom_title = null ) {
        $template = $this->get_template( $template_id );
        
        if ( ! $template ) {
            return new \WP_Error( 'template_not_found', __( 'Template non trovato.', 'fp-forms' ) );
        }
        
        $title = $custom_title ? $custom_title : $template['name'];
        
        $form_id = \FPForms\Plugin::instance()->forms->create_form( $title, [
            'description' => $template['description'],
            'fields' => $template['fields'],
            'settings' => $template['settings'],
        ] );
        
        if ( $form_id ) {
            \FPForms\Core\Logger::info( 'Template imported successfully', [
                'template_id' => $template_id,
                'form_id' => $form_id,
            ] );
        }
        
        return $form_id;
    }
    
    /**
     * Template: Contatto Semplice
     */
    private function get_contact_simple_template() {
        return [
            'id' => 'contact-simple',
            'name' => __( 'Contatto Semplice', 'fp-forms' ),
            'description' => __( 'Form di contatto base con campi essenziali', 'fp-forms' ),
            'category' => 'general',
            'icon' => '✉️',
            'fields' => [
                [
                    'type' => 'text',
                    'label' => __( 'Nome Completo', 'fp-forms' ),
                    'name' => 'full_name',
                    'required' => true,
                    'options' => [
                        'placeholder' => __( 'Mario Rossi', 'fp-forms' ),
                    ],
                ],
                [
                    'type' => 'email',
                    'label' => __( 'Email', 'fp-forms' ),
                    'name' => 'email',
                    'required' => true,
                    'options' => [
                        'placeholder' => __( 'mario@example.com', 'fp-forms' ),
                    ],
                ],
                [
                    'type' => 'phone',
                    'label' => __( 'Telefono', 'fp-forms' ),
                    'name' => 'phone',
                    'required' => false,
                    'options' => [
                        'placeholder' => __( '+39 123 456 7890', 'fp-forms' ),
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => __( 'Messaggio', 'fp-forms' ),
                    'name' => 'message',
                    'required' => true,
                    'options' => [
                        'rows' => 5,
                        'placeholder' => __( 'Come possiamo aiutarti?', 'fp-forms' ),
                    ],
                ],
            ],
            'settings' => [
                'submit_button_text' => __( 'Invia Messaggio', 'fp-forms' ),
                'success_message' => __( 'Grazie! Abbiamo ricevuto il tuo messaggio e ti contatteremo presto.', 'fp-forms' ),
                'notification_email' => get_option( 'admin_email' ),
                'notification_subject' => __( 'Nuovo messaggio di contatto', 'fp-forms' ),
            ],
        ];
    }
    
    /**
     * Template: Richiesta Preventivo
     */
    private function get_quote_request_template() {
        return [
            'id' => 'quote-request',
            'name' => __( 'Richiesta Preventivo', 'fp-forms' ),
            'description' => __( 'Form per richiedere preventivi e quotazioni', 'fp-forms' ),
            'category' => 'business',
            'icon' => '💼',
            'fields' => [
                [
                    'type' => 'text',
                    'label' => __( 'Nome Azienda', 'fp-forms' ),
                    'name' => 'company_name',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => __( 'Nome Referente', 'fp-forms' ),
                    'name' => 'contact_name',
                    'required' => true,
                ],
                [
                    'type' => 'email',
                    'label' => __( 'Email', 'fp-forms' ),
                    'name' => 'email',
                    'required' => true,
                ],
                [
                    'type' => 'phone',
                    'label' => __( 'Telefono', 'fp-forms' ),
                    'name' => 'phone',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => __( 'Servizio Richiesto', 'fp-forms' ),
                    'name' => 'service',
                    'required' => true,
                    'options' => [
                        'choices' => [
                            'Consulenza',
                            'Sviluppo Web',
                            'Marketing',
                            'SEO',
                            'Altro',
                        ],
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => __( 'Budget Indicativo', 'fp-forms' ),
                    'name' => 'budget',
                    'required' => false,
                    'options' => [
                        'choices' => [
                            'Meno di 1.000€',
                            '1.000€ - 5.000€',
                            '5.000€ - 10.000€',
                            'Oltre 10.000€',
                        ],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => __( 'Dettagli Progetto', 'fp-forms' ),
                    'name' => 'project_details',
                    'required' => true,
                    'options' => [
                        'rows' => 6,
                        'placeholder' => __( 'Descrivi il tuo progetto...', 'fp-forms' ),
                    ],
                ],
            ],
            'settings' => [
                'submit_button_text' => __( 'Richiedi Preventivo', 'fp-forms' ),
                'success_message' => __( 'Grazie! Ti invieremo un preventivo entro 24 ore.', 'fp-forms' ),
            ],
        ];
    }
    
    /**
     * Template: Prenotazione
     */
    private function get_booking_template() {
        return [
            'id' => 'booking',
            'name' => __( 'Prenotazione', 'fp-forms' ),
            'description' => __( 'Form per prenotazioni di servizi o appuntamenti', 'fp-forms' ),
            'category' => 'booking',
            'icon' => '📅',
            'fields' => [
                [
                    'type' => 'text',
                    'label' => __( 'Nome e Cognome', 'fp-forms' ),
                    'name' => 'full_name',
                    'required' => true,
                ],
                [
                    'type' => 'email',
                    'label' => __( 'Email', 'fp-forms' ),
                    'name' => 'email',
                    'required' => true,
                ],
                [
                    'type' => 'phone',
                    'label' => __( 'Telefono', 'fp-forms' ),
                    'name' => 'phone',
                    'required' => true,
                ],
                [
                    'type' => 'date',
                    'label' => __( 'Data Prenotazione', 'fp-forms' ),
                    'name' => 'booking_date',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => __( 'Orario Preferito', 'fp-forms' ),
                    'name' => 'booking_time',
                    'required' => true,
                    'options' => [
                        'choices' => [
                            '09:00 - 10:00',
                            '10:00 - 11:00',
                            '11:00 - 12:00',
                            '14:00 - 15:00',
                            '15:00 - 16:00',
                            '16:00 - 17:00',
                        ],
                    ],
                ],
                [
                    'type' => 'number',
                    'label' => __( 'Numero Persone', 'fp-forms' ),
                    'name' => 'people_count',
                    'required' => true,
                    'options' => [
                        'placeholder' => '2',
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => __( 'Note Aggiuntive', 'fp-forms' ),
                    'name' => 'notes',
                    'required' => false,
                    'options' => [
                        'rows' => 4,
                    ],
                ],
            ],
            'settings' => [
                'submit_button_text' => __( 'Conferma Prenotazione', 'fp-forms' ),
                'success_message' => __( 'Prenotazione confermata! Riceverai una email di conferma a breve.', 'fp-forms' ),
                'confirmation_enabled' => true,
                'confirmation_subject' => __( 'Conferma Prenotazione', 'fp-forms' ),
            ],
        ];
    }
    
    /**
     * Template: Lavora con Noi
     */
    private function get_job_application_template() {
        return [
            'id' => 'job-application',
            'name' => __( 'Lavora con Noi', 'fp-forms' ),
            'description' => __( 'Form per candidature di lavoro', 'fp-forms' ),
            'category' => 'business',
            'icon' => '💼',
            'fields' => [
                [
                    'type' => 'text',
                    'label' => __( 'Nome e Cognome', 'fp-forms' ),
                    'name' => 'full_name',
                    'required' => true,
                ],
                [
                    'type' => 'email',
                    'label' => __( 'Email', 'fp-forms' ),
                    'name' => 'email',
                    'required' => true,
                ],
                [
                    'type' => 'phone',
                    'label' => __( 'Telefono', 'fp-forms' ),
                    'name' => 'phone',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => __( 'Posizione di Interesse', 'fp-forms' ),
                    'name' => 'position',
                    'required' => true,
                    'options' => [
                        'choices' => [
                            'Sviluppatore Web',
                            'Designer',
                            'Marketing Manager',
                            'Project Manager',
                            'Altro',
                        ],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => __( 'Perché vuoi lavorare con noi?', 'fp-forms' ),
                    'name' => 'motivation',
                    'required' => true,
                    'options' => [
                        'rows' => 5,
                    ],
                ],
            ],
            'settings' => [
                'submit_button_text' => __( 'Invia Candidatura', 'fp-forms' ),
                'success_message' => __( 'Grazie per la tua candidatura! Ti contatteremo se il tuo profilo è in linea con le nostre esigenze.', 'fp-forms' ),
            ],
        ];
    }
    
    /**
     * Template: Newsletter
     */
    private function get_newsletter_template() {
        return [
            'id' => 'newsletter',
            'name' => __( 'Newsletter Signup', 'fp-forms' ),
            'description' => __( 'Form minimal per iscrizione newsletter', 'fp-forms' ),
            'category' => 'general',
            'icon' => '📰',
            'fields' => [
                [
                    'type' => 'email',
                    'label' => __( 'Indirizzo Email', 'fp-forms' ),
                    'name' => 'email',
                    'required' => true,
                    'options' => [
                        'placeholder' => __( 'tua@email.com', 'fp-forms' ),
                    ],
                ],
                [
                    'type' => 'checkbox',
                    'label' => __( 'Privacy', 'fp-forms' ),
                    'name' => 'privacy_consent',
                    'required' => true,
                    'options' => [
                        'choices' => [
                            __( 'Accetto la Privacy Policy', 'fp-forms' ),
                        ],
                    ],
                ],
            ],
            'settings' => [
                'submit_button_text' => __( 'Iscriviti', 'fp-forms' ),
                'success_message' => __( 'Iscrizione completata! Controlla la tua email per confermare.', 'fp-forms' ),
            ],
        ];
    }
    
    /**
     * Template: Feedback
     */
    private function get_feedback_template() {
        return [
            'id' => 'feedback',
            'name' => __( 'Feedback', 'fp-forms' ),
            'description' => __( 'Form per raccogliere feedback e recensioni', 'fp-forms' ),
            'category' => 'general',
            'icon' => '⭐',
            'fields' => [
                [
                    'type' => 'text',
                    'label' => __( 'Nome', 'fp-forms' ),
                    'name' => 'name',
                    'required' => false,
                ],
                [
                    'type' => 'email',
                    'label' => __( 'Email', 'fp-forms' ),
                    'name' => 'email',
                    'required' => false,
                ],
                [
                    'type' => 'select',
                    'label' => __( 'Valutazione', 'fp-forms' ),
                    'name' => 'rating',
                    'required' => true,
                    'options' => [
                        'choices' => [
                            '⭐⭐⭐⭐⭐ Eccellente',
                            '⭐⭐⭐⭐ Molto Buono',
                            '⭐⭐⭐ Buono',
                            '⭐⭐ Sufficiente',
                            '⭐ Scarso',
                        ],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => __( 'Il tuo Feedback', 'fp-forms' ),
                    'name' => 'feedback',
                    'required' => true,
                    'options' => [
                        'rows' => 5,
                        'placeholder' => __( 'Raccontaci la tua esperienza...', 'fp-forms' ),
                    ],
                ],
            ],
            'settings' => [
                'submit_button_text' => __( 'Invia Feedback', 'fp-forms' ),
                'success_message' => __( 'Grazie per il tuo feedback!', 'fp-forms' ),
            ],
        ];
    }
    
    /**
     * Template: Support Ticket
     */
    private function get_support_ticket_template() {
        return [
            'id' => 'support-ticket',
            'name' => __( 'Support Ticket', 'fp-forms' ),
            'description' => __( 'Form per richieste di supporto tecnico', 'fp-forms' ),
            'category' => 'business',
            'icon' => '🆘',
            'fields' => [
                [
                    'type' => 'text',
                    'label' => __( 'Nome', 'fp-forms' ),
                    'name' => 'name',
                    'required' => true,
                ],
                [
                    'type' => 'email',
                    'label' => __( 'Email', 'fp-forms' ),
                    'name' => 'email',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => __( 'Categoria Problema', 'fp-forms' ),
                    'name' => 'category',
                    'required' => true,
                    'options' => [
                        'choices' => [
                            'Tecnico',
                            'Account',
                            'Pagamento',
                            'Funzionalità',
                            'Altro',
                        ],
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => __( 'Priorità', 'fp-forms' ),
                    'name' => 'priority',
                    'required' => true,
                    'options' => [
                        'choices' => [
                            'Bassa',
                            'Media',
                            'Alta',
                            'Urgente',
                        ],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => __( 'Descrizione Problema', 'fp-forms' ),
                    'name' => 'description',
                    'required' => true,
                    'options' => [
                        'rows' => 6,
                        'placeholder' => __( 'Descrivi il problema in dettaglio...', 'fp-forms' ),
                    ],
                ],
            ],
            'settings' => [
                'submit_button_text' => __( 'Invia Ticket', 'fp-forms' ),
                'success_message' => __( 'Ticket creato! Ti risponderemo entro 24 ore.', 'fp-forms' ),
            ],
        ];
    }
    
    /**
     * Template: Event Registration
     */
    private function get_event_registration_template() {
        return [
            'id' => 'event-registration',
            'name' => __( 'Registrazione Evento', 'fp-forms' ),
            'description' => __( 'Form per iscrizione a eventi', 'fp-forms' ),
            'category' => 'general',
            'icon' => '🎫',
            'fields' => [
                [
                    'type' => 'text',
                    'label' => __( 'Nome e Cognome', 'fp-forms' ),
                    'name' => 'full_name',
                    'required' => true,
                ],
                [
                    'type' => 'email',
                    'label' => __( 'Email', 'fp-forms' ),
                    'name' => 'email',
                    'required' => true,
                ],
                [
                    'type' => 'phone',
                    'label' => __( 'Telefono', 'fp-forms' ),
                    'name' => 'phone',
                    'required' => false,
                ],
                [
                    'type' => 'number',
                    'label' => __( 'Numero Partecipanti', 'fp-forms' ),
                    'name' => 'participants',
                    'required' => true,
                    'options' => [
                        'placeholder' => '1',
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => __( 'Note / Richieste Speciali', 'fp-forms' ),
                    'name' => 'notes',
                    'required' => false,
                    'options' => [
                        'rows' => 3,
                    ],
                ],
            ],
            'settings' => [
                'submit_button_text' => __( 'Conferma Iscrizione', 'fp-forms' ),
                'success_message' => __( 'Iscrizione confermata! Ti aspettiamo all\'evento.', 'fp-forms' ),
            ],
        ];
    }
}

