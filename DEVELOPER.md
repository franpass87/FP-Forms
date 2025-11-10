# 👨‍💻 FP Forms - Guida Developer

Guida completa per sviluppatori che vogliono estendere o personalizzare FP Forms.

---

## 📋 Indice

1. [Architettura](#architettura)
2. [Hooks & Filters](#hooks--filters)
3. [Estendere il Plugin](#estendere-il-plugin)
4. [Best Practices](#best-practices)
5. [API Reference](#api-reference)
6. [Testing](#testing)

---

## 🏗️ Architettura

### Struttura Classi

```
FPForms\
├── Core\
│   ├── Capabilities    - Gestione permessi
│   ├── Cache          - Object caching
│   ├── Hooks          - Hooks manager
│   └── Logger         - Logging system
├── Helpers\
│   └── Helper         - Utility functions
├── Validators\
│   └── Validator      - Field validation
├── Sanitizers\
│   └── Sanitizer      - Data sanitization
├── Fields\
│   └── FieldFactory   - Field rendering
├── Admin\
│   └── Manager        - Admin interface
├── Frontend\
│   └── Manager        - Frontend rendering
├── Forms\
│   └── Manager        - Form CRUD
├── Submissions\
│   └── Manager        - Submission handling
├── Email\
│   └── Manager        - Email notifications
└── Database\
    └── Manager        - Database operations
```

### Design Patterns

- **Singleton:** Plugin class
- **Factory:** FieldFactory
- **Strategy:** Validators, Sanitizers
- **Observer:** Hooks system

---

## 🪝 Hooks & Filters

### Actions

#### Form Actions

```php
// Prima della creazione form
do_action( 'fp_forms_before_create_form', $title, $args );

// Dopo la creazione form
do_action( 'fp_forms_after_create_form', $form_id, $form_data );

// Prima dell'update form
do_action( 'fp_forms_before_update_form', $form_id, $data );

// Dopo l'update form
do_action( 'fp_forms_after_update_form', $form_id, $data );

// Prima dell'eliminazione form
do_action( 'fp_forms_before_delete_form', $form_id );

// Dopo l'eliminazione form
do_action( 'fp_forms_after_delete_form', $form_id );
```

#### Submission Actions

```php
// Prima della validazione
do_action( 'fp_forms_before_validate_submission', $form_id, $data );

// Dopo la validazione
do_action( 'fp_forms_after_validate_submission', $form_id, $data, $validation );

// Prima del salvataggio submission
do_action( 'fp_forms_before_save_submission', $form_id, $data );

// Dopo il salvataggio submission
do_action( 'fp_forms_after_save_submission', $submission_id, $form_id, $data );

// Prima dell'eliminazione submission
do_action( 'fp_forms_before_delete_submission', $submission_id );

// Dopo l'eliminazione submission
do_action( 'fp_forms_after_delete_submission', $submission_id );
```

#### Email Actions

```php
// Prima dell'invio notifica
do_action( 'fp_forms_before_send_notification', $form_id, $data, $to );

// Dopo l'invio notifica
do_action( 'fp_forms_after_send_notification', $form_id, $data, $success );

// Prima dell'invio conferma
do_action( 'fp_forms_before_send_confirmation', $form_id, $data, $to );

// Dopo l'invio conferma
do_action( 'fp_forms_after_send_confirmation', $form_id, $data, $success );
```

### Filters

```php
// Modifica dati form
apply_filters( 'fp_forms_form_data', $data, $form_id );

// Modifica dati submission
apply_filters( 'fp_forms_submission_data', $data, $form_id );

// Modifica errori di validazione
apply_filters( 'fp_forms_validation_errors', $errors, $form_id, $data );

// Modifica destinatari email
apply_filters( 'fp_forms_notification_recipients', $to, $form_id, $data );

// Modifica oggetto email
apply_filters( 'fp_forms_email_subject', $subject, $form_id, $data );

// Modifica messaggio email
apply_filters( 'fp_forms_email_message', $message, $form_id, $data );

// Modifica headers email
apply_filters( 'fp_forms_email_headers', $headers, $form_id, $data );

// Modifica HTML campo
apply_filters( 'fp_forms_field_html', $html, $field, $form_id );

// Modifica HTML form
apply_filters( 'fp_forms_form_html', $html, $form_id, $form );

// Modifica messaggio successo
apply_filters( 'fp_forms_success_message', $message, $form_id, $data );
```

---

## 🔧 Estendere il Plugin

### 1. Aggiungere Campo Custom

```php
add_action( 'init', function() {
    \FPForms\Fields\FieldFactory::register( 'rating', 'my_rating_field_renderer' );
});

function my_rating_field_renderer( $field, $form_id ) {
    $field_name = 'fp_field_' . $field['name'];
    $field_id = 'fp_field_' . $form_id . '_' . $field['name'];
    
    $html = '<div class="rating-field">';
    for ( $i = 1; $i <= 5; $i++ ) {
        $html .= sprintf(
            '<input type="radio" id="%s_%d" name="%s" value="%d" />',
            $field_id, $i, $field_name, $i
        );
        $html .= '<label for="' . $field_id . '_' . $i . '">★</label>';
    }
    $html .= '</div>';
    
    return $html;
}
```

### 2. Validazione Custom

```php
add_filter( 'fp_forms_validation_errors', function( $errors, $form_id, $data ) {
    // Valida solo per form ID 123
    if ( $form_id !== 123 ) {
        return $errors;
    }
    
    // Validazione custom
    if ( isset( $data['fp_field_age'] ) && $data['fp_field_age'] < 18 ) {
        $errors['fp_field_age'] = 'Devi avere almeno 18 anni.';
    }
    
    return $errors;
}, 10, 3 );
```

### 3. Modificare Email Dinamicamente

```php
add_filter( 'fp_forms_notification_recipients', function( $to, $form_id, $data ) {
    // Invia a email diversa in base al tipo di richiesta
    if ( isset( $data['fp_field_department'] ) ) {
        $dept = $data['fp_field_department'];
        
        $emails = [
            'sales' => 'sales@example.com',
            'support' => 'support@example.com',
            'info' => 'info@example.com',
        ];
        
        if ( isset( $emails[ $dept ] ) ) {
            return $emails[ $dept ];
        }
    }
    
    return $to;
}, 10, 3 );
```

### 4. Integrare con CRM

```php
add_action( 'fp_forms_after_save_submission', function( $submission_id, $form_id, $data ) {
    // Solo per form ID 456
    if ( $form_id !== 456 ) {
        return;
    }
    
    // Invia a CRM (es. Salesforce, HubSpot)
    $crm_data = [
        'email' => $data['fp_field_email'] ?? '',
        'name' => $data['fp_field_name'] ?? '',
        'phone' => $data['fp_field_phone'] ?? '',
    ];
    
    wp_remote_post( 'https://crm-api.example.com/leads', [
        'body' => json_encode( $crm_data ),
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer YOUR_API_KEY',
        ],
    ] );
}, 10, 3 );
```

### 5. Aggiungere Campo al Form Builder

```php
add_filter( 'fp_forms_builder_field_types', function( $types ) {
    $types['signature'] = [
        'label' => __( 'Firma Digitale', 'myplugin' ),
        'icon' => 'dashicons-edit',
    ];
    
    return $types;
});
```

### 6. Template Custom

Copia template dal plugin:
```
wp-content/plugins/FP-Forms/templates/frontend/form.php
```

Nel tuo tema:
```
wp-content/themes/tuo-tema/fp-forms/form.php
```

Personalizza il template come preferisci.

---

## 💡 Best Practices

### 1. Sempre usare Nonce

```php
if ( ! \FPForms\Helpers\Helper::verify_nonce( $_POST['nonce'], 'my_action' ) ) {
    wp_die( 'Security check failed' );
}
```

### 2. Sanitizzare sempre gli input

```php
$sanitizer = new \FPForms\Sanitizers\Sanitizer();
$clean_data = $sanitizer->sanitize_field( $_POST['field'], 'email' );
```

### 3. Validare sempre

```php
$validator = new \FPForms\Validators\Validator();
$validator->validate_email( $value, 'email', 'Email' );

if ( $validator->has_errors() ) {
    $errors = $validator->get_errors();
}
```

### 4. Usare Cache per query pesanti

```php
$data = \FPForms\Core\Cache::remember( 'my_key', function() {
    // Query pesante
    return get_heavy_data();
}, 3600 );
```

### 5. Logging per debugging

```php
\FPForms\Core\Logger::info( 'Custom action executed', [
    'form_id' => $form_id,
    'user_id' => get_current_user_id(),
]);
```

---

## 📚 API Reference

### Helper Class

```php
// IP utente
$ip = \FPForms\Helpers\Helper::get_user_ip();

// User agent
$ua = \FPForms\Helpers\Helper::get_user_agent();

// Formattare data
$date = \FPForms\Helpers\Helper::format_date( '2025-01-01' );

// Troncare testo
$short = \FPForms\Helpers\Helper::truncate( $long_text, 50 );

// JSON sicuro
$json = \FPForms\Helpers\Helper::safe_json_encode( $data );

// Admin URL
$url = \FPForms\Helpers\Helper::get_admin_url( 'fp-forms', [ 'action' => 'edit' ] );

// Debug log
\FPForms\Helpers\Helper::log( 'Message', $context_data );
```

### Cache Class

```php
// Get
$value = \FPForms\Core\Cache::get( 'key', 'default' );

// Set (TTL in secondi)
\FPForms\Core\Cache::set( 'key', $value, 3600 );

// Delete
\FPForms\Core\Cache::delete( 'key' );

// Remember pattern
$data = \FPForms\Core\Cache::remember( 'key', function() {
    return expensive_operation();
}, 3600 );

// Flush
\FPForms\Core\Cache::flush();
```

### Logger Class

```php
// Log generici
\FPForms\Core\Logger::info( 'Message' );
\FPForms\Core\Logger::error( 'Error message', $context );
\FPForms\Core\Logger::warning( 'Warning' );
\FPForms\Core\Logger::debug( 'Debug info' );

// Log specifici
\FPForms\Core\Logger::log_submission( $form_id, $submission_id, true );
\FPForms\Core\Logger::log_email( $to, $subject, true );

// Ottenere log
$logs = \FPForms\Core\Logger::get_logs( '2025-11-04' );

// Pulire vecchi log
\FPForms\Core\Logger::clean_old_logs( 30 );
```

### Capabilities Class

```php
// Verifica permessi
if ( \FPForms\Core\Capabilities::can_manage_forms() ) {
    // Utente può gestire form
}

if ( \FPForms\Core\Capabilities::can_view_submissions() ) {
    // Utente può vedere submissions
}

// Check or die
\FPForms\Core\Capabilities::check_or_die( 'manage_options' );
```

---

## 🧪 Testing

### Unit Testing

Il plugin è strutturato per essere facilmente testabile.

```php
class ValidatorTest extends WP_UnitTestCase {
    
    public function test_validate_email() {
        $validator = new \FPForms\Validators\Validator();
        
        $validator->validate_email( 'invalid', 'email', 'Email' );
        $this->assertTrue( $validator->has_errors() );
        
        $validator->reset_errors();
        $validator->validate_email( 'valid@email.com', 'email', 'Email' );
        $this->assertFalse( $validator->has_errors() );
    }
}
```

### Integration Testing

```php
// Test submission completa
$form_id = \FPForms\Plugin::instance()->forms->create_form( 'Test Form', [
    'fields' => [
        [
            'type' => 'email',
            'label' => 'Email',
            'name' => 'email',
            'required' => true,
        ],
    ],
] );

$data = [ 'email' => 'test@example.com' ];
$submission_id = \FPForms\Plugin::instance()->database->save_submission( $form_id, $data );

$this->assertGreaterThan( 0, $submission_id );
```

---

## 🔍 Debugging

### Abilitare Debug Mode

In `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

### Visualizzare Log

```php
// In codice
$logs = \FPForms\Core\Logger::get_logs();
echo '<pre>' . esc_html( $logs ) . '</pre>';

// File log
// wp-content/uploads/fp-forms-logs/fp-forms-2025-11-04.log
```

### Query Monitor

Installa Query Monitor plugin per vedere:
- Query database
- Hook chiamati
- Performance

---

## 📞 Supporto

Per domande tecniche: info@francescopasseri.com

**Repository:** Disponibile su richiesta  
**Licenza:** GPL v2 or later

---

**Versione:** 1.0.0  
**Autore:** Francesco Passeri  
**Ultimo Aggiornamento:** 2025-11-04

