<?php
namespace FPForms\I18n;

/**
 * Traduttore automatico per FP Forms.
 *
 * Rileva la lingua dall'URL (/en/, /de/, /fr/, /es/) e traduce
 * tutte le stringhe del textdomain 'fp-forms' tramite il filtro gettext.
 * Default: italiano (nessuna traduzione applicata).
 *
 * @since 1.4.0
 */
class Translator {

    /**
     * Lingua corrente rilevata.
     *
     * @var string|null
     */
    private static $current_lang = null;

    /**
     * Cache delle traduzioni caricate.
     *
     * @var array|null
     */
    private static $translations = null;

    /**
     * Inizializza il traduttore.
     */
    public static function init() {
        // Solo frontend, no admin / CLI / AJAX / REST
        if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
            return;
        }

        $lang = self::detect_lang();

        // Se italiano non serve filtrare
        if ( 'it' === $lang ) {
            return;
        }

        add_filter( 'gettext', [ __CLASS__, 'filter_gettext' ], 10, 3 );
    }

    // =============================================================
    //  RILEVAMENTO LINGUA
    // =============================================================

    /**
     * Rileva la lingua corrente dall'URL.
     *
     * Supporta: en, de, fr, es. Fallback: it.
     *
     * @return string Codice lingua a 2 lettere.
     */
    public static function detect_lang() {
        if ( null !== self::$current_lang ) {
            return self::$current_lang;
        }

        $request_uri = isset( $_SERVER['REQUEST_URI'] )
            ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) )
            : '';

        $supported = [ 'en', 'de', 'fr', 'es' ];
        foreach ( $supported as $code ) {
            if ( preg_match( '#^/' . preg_quote( $code, '#' ) . '(/|$)#i', $request_uri ) ) {
                self::$current_lang = $code;
                return self::$current_lang;
            }
        }

        self::$current_lang = 'it';
        return self::$current_lang;
    }

    // =============================================================
    //  FILTRO GETTEXT
    // =============================================================

    /**
     * Filtra le stringhe del textdomain fp-forms.
     *
     * @param string $translated Stringa tradotta (originale, dato che non ci sono .mo).
     * @param string $original   Stringa originale.
     * @param string $domain     Textdomain.
     * @return string
     */
    public static function filter_gettext( $translated, $original, $domain ) {
        if ( 'fp-forms' !== $domain ) {
            return $translated;
        }

        $lang         = self::detect_lang();
        $translations = self::get_translations();

        if ( isset( $translations[ $original ][ $lang ] ) ) {
            return $translations[ $original ][ $lang ];
        }

        // Fallback: inglese se disponibile
        if ( 'en' !== $lang && isset( $translations[ $original ]['en'] ) ) {
            return $translations[ $original ]['en'];
        }

        return $translated;
    }

    // =============================================================
    //  MAPPA TRADUZIONI
    // =============================================================

    /**
     * Restituisce la mappa completa delle traduzioni.
     *
     * Chiave = stringa originale italiana passata a __().
     * Valore = array associativo [ 'en' => '...', 'de' => '...', ... ].
     *
     * @return array
     */
    public static function get_translations() {
        if ( null !== self::$translations ) {
            return self::$translations;
        }

        self::$translations = [

            // -------------------------------------------------
            //  Frontend JS (wp_localize_script)
            // -------------------------------------------------
            'Invio in corso...' => [
                'en' => 'Submitting...',
                'de' => 'Wird gesendet...',
                'fr' => 'Envoi en cours...',
                'es' => 'Enviando...',
            ],
            'Si è verificato un errore. Riprova.' => [
                'en' => 'An error occurred. Please try again.',
                'de' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.',
                'fr' => 'Une erreur est survenue. Veuillez réessayer.',
                'es' => 'Se produjo un error. Inténtelo de nuevo.',
            ],
            'Si è verificato un errore di connessione. Riprova.' => [
                'en' => 'A connection error occurred. Please try again.',
                'de' => 'Ein Verbindungsfehler ist aufgetreten. Bitte versuchen Sie es erneut.',
                'fr' => 'Une erreur de connexion est survenue. Veuillez réessayer.',
                'es' => 'Se produjo un error de conexión. Inténtelo de nuevo.',
            ],
            'La richiesta ha impiegato troppo tempo. Verifica la connessione e riprova.' => [
                'en' => 'The request took too long. Check your connection and try again.',
                'de' => 'Die Anfrage hat zu lange gedauert. Überprüfen Sie Ihre Verbindung und versuchen Sie es erneut.',
                'fr' => 'La requête a pris trop de temps. Vérifiez votre connexion et réessayez.',
                'es' => 'La solicitud tardó demasiado. Verifique su conexión e inténtelo de nuevo.',
            ],
            'Richiesta annullata. Riprova.' => [
                'en' => 'Request cancelled. Please try again.',
                'de' => 'Anfrage abgebrochen. Bitte versuchen Sie es erneut.',
                'fr' => 'Requête annulée. Veuillez réessayer.',
                'es' => 'Solicitud cancelada. Inténtelo de nuevo.',
            ],
            'Questo campo è obbligatorio.' => [
                'en' => 'This field is required.',
                'de' => 'Dieses Feld ist erforderlich.',
                'fr' => 'Ce champ est obligatoire.',
                'es' => 'Este campo es obligatorio.',
            ],

            // -------------------------------------------------
            //  FieldFactory - Campi
            // -------------------------------------------------
            'Nome' => [
                'en' => 'First name',
                'de' => 'Vorname',
                'fr' => 'Prénom',
                'es' => 'Nombre',
            ],
            'Cognome' => [
                'en' => 'Last name',
                'de' => 'Nachname',
                'fr' => 'Nom',
                'es' => 'Apellido',
            ],
            '-- Seleziona --' => [
                'en' => '-- Select --',
                'de' => '-- Auswählen --',
                'fr' => '-- Sélectionner --',
                'es' => '-- Seleccionar --',
            ],
            'Ho letto e accetto la' => [
                'en' => 'I have read and accept the',
                'de' => 'Ich habe die gelesen und akzeptiere die',
                'fr' => "J'ai lu et j'accepte la",
                'es' => 'He leído y acepto la',
            ],
            'Privacy Policy' => [
                'en' => 'Privacy Policy',
                'de' => 'Datenschutzrichtlinie',
                'fr' => 'Politique de confidentialité',
                'es' => 'Política de privacidad',
            ],
            'Acconsento a ricevere comunicazioni marketing e newsletter' => [
                'en' => 'I consent to receive marketing communications and newsletters',
                'de' => 'Ich stimme dem Erhalt von Marketing-Mitteilungen und Newslettern zu',
                'fr' => "Je consens à recevoir des communications marketing et des newsletters",
                'es' => 'Acepto recibir comunicaciones de marketing y boletines',
            ],

            // -------------------------------------------------
            //  Template form.php - Pulsante submit (default)
            // -------------------------------------------------
            'Invia' => [
                'en' => 'Submit',
                'de' => 'Absenden',
                'fr' => 'Envoyer',
                'es' => 'Enviar',
            ],

            // -------------------------------------------------
            //  Template form.php - Trust badges
            // -------------------------------------------------
            'Risposta Immediata' => [
                'en' => 'Instant Response',
                'de' => 'Sofortige Antwort',
                'fr' => 'Réponse immédiate',
                'es' => 'Respuesta inmediata',
            ],
            'I Tuoi Dati Sono Al Sicuro' => [
                'en' => 'Your Data Is Safe',
                'de' => 'Ihre Daten sind sicher',
                'fr' => 'Vos données sont en sécurité',
                'es' => 'Tus datos están seguros',
            ],
            'No Spam, Mai' => [
                'en' => 'No Spam, Ever',
                'de' => 'Kein Spam, niemals',
                'fr' => 'Pas de spam, jamais',
                'es' => 'Sin spam, nunca',
            ],
            'Connessione Sicura SSL' => [
                'en' => 'Secure SSL Connection',
                'de' => 'Sichere SSL-Verbindung',
                'fr' => 'Connexion SSL sécurisée',
                'es' => 'Conexión segura SSL',
            ],
            'Risposta Entro 24h' => [
                'en' => 'Reply Within 24h',
                'de' => 'Antwort innerhalb von 24 Std.',
                'fr' => 'Réponse sous 24h',
                'es' => 'Respuesta en 24h',
            ],
            'Preventivo Gratuito' => [
                'en' => 'Free Quote',
                'de' => 'Kostenloses Angebot',
                'fr' => 'Devis gratuit',
                'es' => 'Presupuesto gratuito',
            ],
            '1000+ Clienti Soddisfatti' => [
                'en' => '1000+ Happy Clients',
                'de' => '1000+ zufriedene Kunden',
                'fr' => '1000+ clients satisfaits',
                'es' => '1000+ clientes satisfechos',
            ],
            'Supporto Dedicato' => [
                'en' => 'Dedicated Support',
                'de' => 'Persönlicher Support',
                'fr' => 'Support dédié',
                'es' => 'Soporte dedicado',
            ],
            'Privacy Garantita' => [
                'en' => 'Privacy Guaranteed',
                'de' => 'Datenschutz garantiert',
                'fr' => 'Confidentialité garantie',
                'es' => 'Privacidad garantizada',
            ],
            'GDPR Compliant' => [
                'en' => 'GDPR Compliant',
                'de' => 'DSGVO-konform',
                'fr' => 'Conforme au RGPD',
                'es' => 'Cumple el RGPD',
            ],
            '4,9/5 da recensioni verificate' => [
                'en' => '4.9/5 from verified reviews',
                'de' => '4,9/5 aus verifizierten Bewertungen',
                'fr' => '4,9/5 d’avis vérifiés',
                'es' => '4,9/5 en reseñas verificadas',
            ],
            'Richieste gestite ogni giorno' => [
                'en' => 'Requests handled every day',
                'de' => 'Täglich bearbeitete Anfragen',
                'fr' => 'Demandes traitées chaque jour',
                'es' => 'Solicitudes atendidas cada día',
            ],
            'Pagamenti sicuri' => [
                'en' => 'Secure payments',
                'de' => 'Sichere Zahlungen',
                'fr' => 'Paiements sécurisés',
                'es' => 'Pagos seguros',
            ],
            'Guida omaggio in PDF' => [
                'en' => 'Free PDF guide',
                'de' => 'Kostenloser PDF-Leitfaden',
                'fr' => 'Guide PDF offert',
                'es' => 'Guía PDF de regalo',
            ],
            'Disponibilità limitata oggi' => [
                'en' => 'Limited availability today',
                'de' => 'Begrenzte Verfügbarkeit heute',
                'fr' => 'Disponibilité limitée aujourd’hui',
                'es' => 'Disponibilidad limitada hoy',
            ],
            'Garanzia soddisfatti o rimborsati' => [
                'en' => 'Satisfaction or money-back guarantee',
                'de' => 'Zufriedenheits- oder Geld-zurück-Garantie',
                'fr' => 'Satisfait ou remboursé',
                'es' => 'Garantía de satisfacción o reembolso',
            ],
            'Disiscrizione in un clic' => [
                'en' => 'One-click unsubscribe',
                'de' => 'Abmeldung mit einem Klick',
                'fr' => 'Désinscription en un clic',
                'es' => 'Baja en un clic',
            ],
            'Risposta da persone reali, non bot' => [
                'en' => 'Replies from real people, not bots',
                'de' => 'Antworten von echten Menschen, keine Bots',
                'fr' => 'Réponses par de vraies personnes, pas des robots',
                'es' => 'Respuestas de personas reales, no bots',
            ],
            'Compila in meno di 1 minuto' => [
                'en' => 'Complete in under 1 minute',
                'de' => 'In unter 1 Minute ausfüllen',
                'fr' => 'Remplir en moins d’1 minute',
                'es' => 'Completa en menos de 1 minuto',
            ],
            'Nessuna carta richiesta' => [
                'en' => 'No card required',
                'de' => 'Keine Karte erforderlich',
                'fr' => 'Aucune carte requise',
                'es' => 'Sin tarjeta requerida',
            ],
            'Prezzi chiari, zero sorprese' => [
                'en' => 'Clear pricing, no surprises',
                'de' => 'Klare Preise, keine Überraschungen',
                'fr' => 'Prix clairs, zéro surprise',
                'es' => 'Precios claros, sin sorpresas',
            ],
            'Processo trasparente in 3 passi' => [
                'en' => 'Transparent process in 3 steps',
                'de' => 'Transparenter Ablauf in 3 Schritten',
                'fr' => 'Processus transparent en 3 étapes',
                'es' => 'Proceso transparente en 3 pasos',
            ],
            'Oltre 200 richieste questo mese' => [
                'en' => '200+ requests this month',
                'de' => 'Über 200 Anfragen diesen Monat',
                'fr' => 'Plus de 200 demandes ce mois-ci',
                'es' => 'Más de 200 solicitudes este mes',
            ],
            '500+ recensioni a 5 stelle' => [
                'en' => '500+ five-star reviews',
                'de' => '500+ Fünf-Sterne-Bewertungen',
                'fr' => '500+ avis 5 étoiles',
                'es' => '500+ reseñas de 5 estrellas',
            ],
            'Certificazioni di settore riconosciute' => [
                'en' => 'Recognized industry certifications',
                'de' => 'Anerkannte Branchenzertifizierungen',
                'fr' => 'Certifications sectorielles reconnues',
                'es' => 'Certificaciones sectoriales reconocidas',
            ],
            'Partner ufficiale' => [
                'en' => 'Official partner',
                'de' => 'Offizieller Partner',
                'fr' => 'Partenaire officiel',
                'es' => 'Socio oficial',
            ],
            '15 minuti di consulenza gratuita' => [
                'en' => '15 minutes free consultation',
                'de' => '15 Minuten kostenlose Beratung',
                'fr' => '15 minutes de consultation gratuite',
                'es' => '15 minutos de consulta gratuita',
            ],
            'Posti limitati questa settimana' => [
                'en' => 'Limited spots this week',
                'de' => 'Begrenzte Plätze diese Woche',
                'fr' => 'Places limitées cette semaine',
                'es' => 'Plazas limitadas esta semana',
            ],
            '30 giorni soddisfatti o rimborsati' => [
                'en' => '30-day satisfaction or money-back',
                'de' => '30 Tage Zufriedenheit oder Geld zurück',
                'fr' => '30 jours satisfait ou remboursé',
                'es' => '30 días satisfecho o reembolso',
            ],
            'I tuoi dati non vengono rivenduti' => [
                'en' => 'Your data is never resold',
                'de' => 'Ihre Daten werden nicht weiterverkauft',
                'fr' => 'Vos données ne sont jamais revendues',
                'es' => 'Tus datos no se revenden',
            ],
            'Ti richiamiamo entro 2 ore' => [
                'en' => 'We call you back within 2 hours',
                'de' => 'Wir rufen innerhalb von 2 Stunden zurück',
                'fr' => 'Rappel sous 2 heures',
                'es' => 'Te llamamos en 2 horas',
            ],
            'Team di supporto in italiano' => [
                'en' => 'Italian-speaking support team',
                'de' => 'Support-Team auf Italienisch',
                'fr' => 'Équipe support en italien',
                'es' => 'Equipo de soporte en italiano',
            ],
            "Ti basta inserire l'email" => [
                'en' => 'Just enter your email to get started',
                'de' => 'Nur E-Mail eingeben – mehr brauchen Sie nicht',
                'fr' => 'Il suffit de saisir votre e-mail',
                'es' => 'Solo necesitas introducir tu email',
            ],
            'Nessun costo nascosto' => [
                'en' => 'No hidden fees',
                'de' => 'Keine versteckten Kosten',
                'fr' => 'Aucun frais caché',
                'es' => 'Sin costes ocultos',
            ],

            // -------------------------------------------------
            //  Messaggi generici
            // -------------------------------------------------
            'Form non trovato.' => [
                'en' => 'Form not found.',
                'de' => 'Formular nicht gefunden.',
                'fr' => 'Formulaire introuvable.',
                'es' => 'Formulario no encontrado.',
            ],

            // -------------------------------------------------
            //  Email - Notifiche
            // -------------------------------------------------
            'Nuova submission dal form: %s' => [
                'en' => 'New submission from form: %s',
                'de' => 'Neue Einsendung vom Formular: %s',
                'fr' => 'Nouvelle soumission du formulaire : %s',
                'es' => 'Nueva respuesta del formulario: %s',
            ],
            'Hai ricevuto una nuova submission dal form "%s"' => [
                'en' => 'You received a new submission from form "%s"',
                'de' => 'Sie haben eine neue Einsendung vom Formular "%s" erhalten',
                'fr' => 'Vous avez reçu une nouvelle soumission du formulaire « %s »',
                'es' => 'Has recibido una nueva respuesta del formulario "%s"',
            ],
            'Informazioni aggiuntive:' => [
                'en' => 'Additional information:',
                'de' => 'Zusätzliche Informationen:',
                'fr' => 'Informations supplémentaires :',
                'es' => 'Información adicional:',
            ],
            'Data:' => [
                'en' => 'Date:',
                'de' => 'Datum:',
                'fr' => 'Date :',
                'es' => 'Fecha:',
            ],
            'IP:' => [
                'en' => 'IP:',
                'de' => 'IP:',
                'fr' => 'IP :',
                'es' => 'IP:',
            ],
            'Utente:' => [
                'en' => 'User:',
                'de' => 'Benutzer:',
                'fr' => 'Utilisateur :',
                'es' => 'Usuario:',
            ],
            'Conferma ricezione del tuo messaggio' => [
                'en' => 'Your message has been received',
                'de' => 'Ihre Nachricht wurde empfangen',
                'fr' => 'Votre message a bien été reçu',
                'es' => 'Hemos recibido tu mensaje',
            ],
            'Grazie per averci contattato. Abbiamo ricevuto il tuo messaggio e ti risponderemo al più presto.' => [
                'en' => 'Thank you for contacting us. We have received your message and will get back to you as soon as possible.',
                'de' => 'Vielen Dank für Ihre Nachricht. Wir haben sie erhalten und werden uns schnellstmöglich bei Ihnen melden.',
                'fr' => 'Merci de nous avoir contactés. Nous avons bien reçu votre message et vous répondrons dans les plus brefs délais.',
                'es' => 'Gracias por contactarnos. Hemos recibido tu mensaje y te responderemos lo antes posible.',
            ],
            '[STAFF] Nuova submission: %s' => [
                'en' => '[STAFF] New submission: %s',
                'de' => '[STAFF] Neue Einsendung: %s',
                'fr' => '[STAFF] Nouvelle soumission : %s',
                'es' => '[STAFF] Nueva respuesta: %s',
            ],
        ];

        /**
         * Permette di aggiungere/modificare traduzioni da temi o plugin esterni.
         *
         * @since 1.4.0
         *
         * @param array $translations Mappa [ original_it => [ lang => traduzione ] ].
         */
        self::$translations = apply_filters( 'fp_forms_translations', self::$translations );

        return self::$translations;
    }
}
