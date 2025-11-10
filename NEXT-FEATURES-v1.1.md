# 🚀 FP Forms v1.1 - Next Features

**Release Target:** Gennaio 2025  
**Focus:** Features essenziali più richieste  
**Priorità:** Massima ROI/Effort ratio

---

## 📋 Features da Implementare

### 1️⃣ Upload File (Priority: ⭐⭐⭐⭐⭐)

**Effort:** 3-4 giorni  
**Impact:** ALTO

#### Implementazione Tecnica

**Nuovo Field Type:**
```php
// src/Fields/FileField.php
class FileField extends FieldBase {
    public function render( $field, $form_id ) {
        return sprintf(
            '<input type="file" 
                   name="fp_field_%s" 
                   id="fp_field_%d_%s"
                   accept="%s"
                   %s />',
            $field['name'],
            $form_id,
            $field['name'],
            $this->get_accepted_types( $field ),
            $field['required'] ? 'required' : ''
        );
    }
}
```

**Backend Processing:**
```php
// src/Submissions/FileHandler.php
class FileHandler {
    private $upload_dir = 'fp-forms';
    private $max_size = 5242880; // 5MB
    
    public function handle_upload( $file, $field_options ) {
        // 1. Valida tipo file
        if ( ! $this->validate_file_type( $file, $field_options ) ) {
            return new WP_Error( 'invalid_type', __( 'Tipo file non permesso' ) );
        }
        
        // 2. Valida dimensione
        if ( ! $this->validate_file_size( $file, $field_options ) ) {
            return new WP_Error( 'file_too_large', __( 'File troppo grande' ) );
        }
        
        // 3. Upload
        $upload = wp_handle_upload( $file, [
            'test_form' => false,
            'upload_dir' => $this->get_upload_dir(),
        ] );
        
        // 4. Salva riferimento in submission
        return $upload['url'];
    }
}
```

**Database Schema:**
```sql
-- Nuova tabella per file uploads
CREATE TABLE wp_fp_forms_files (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    submission_id bigint(20) NOT NULL,
    field_name varchar(255) NOT NULL,
    file_name varchar(255) NOT NULL,
    file_path varchar(500) NOT NULL,
    file_url varchar(500) NOT NULL,
    file_type varchar(100) NOT NULL,
    file_size bigint(20) NOT NULL,
    uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY submission_id (submission_id)
);
```

**Builder UI:**
```javascript
// Builder options per file field
{
    allowed_types: {
        label: 'Tipi File Permessi',
        type: 'multiselect',
        options: ['pdf', 'doc', 'docx', 'jpg', 'png', 'zip'],
        default: ['pdf', 'jpg', 'png']
    },
    max_size: {
        label: 'Dimensione Massima (MB)',
        type: 'number',
        default: 5,
        min: 1,
        max: 50
    },
    multiple: {
        label: 'Upload Multipli',
        type: 'checkbox',
        default: false
    }
}
```

**Security:**
- Sanitize filename
- Check MIME type
- Scan antivirus (optional with ClamAV)
- Store outside public directory option
- Delete files on submission delete

---

### 2️⃣ Export Submissions (Priority: ⭐⭐⭐⭐⭐)

**Effort:** 2-3 giorni  
**Impact:** ALTO

#### Implementazione Tecnica

**Admin Page:**
```php
// src/Admin/ExportPage.php
class ExportPage {
    public function render_export_modal() {
        ?>
        <div id="fp-export-modal" class="fp-modal">
            <div class="fp-modal-content">
                <h2><?php _e( 'Export Submissions', 'fp-forms' ); ?></h2>
                
                <form id="fp-export-form">
                    <!-- Format -->
                    <select name="format">
                        <option value="csv">CSV</option>
                        <option value="xlsx">Excel (XLSX)</option>
                        <option value="pdf">PDF</option>
                    </select>
                    
                    <!-- Date Range -->
                    <input type="date" name="date_from">
                    <input type="date" name="date_to">
                    
                    <!-- Fields Selection -->
                    <div class="fields-selector">
                        <label>
                            <input type="checkbox" name="all_fields" checked>
                            Tutti i campi
                        </label>
                        <div id="specific-fields" style="display:none;">
                            <!-- Dynamic fields checkboxes -->
                        </div>
                    </div>
                    
                    <button type="submit">Export</button>
                </form>
            </div>
        </div>
        <?php
    }
}
```

**CSV Export:**
```php
// src/Export/CsvExporter.php
class CsvExporter {
    public function export( $form_id, $options ) {
        $submissions = $this->get_submissions( $form_id, $options );
        $fields = $this->get_fields( $form_id );
        
        // Create CSV
        $filename = 'form-' . $form_id . '-' . date( 'Y-m-d' ) . '.csv';
        
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        
        $output = fopen( 'php://output', 'w' );
        
        // Header row
        fputcsv( $output, $this->get_csv_headers( $fields ) );
        
        // Data rows
        foreach ( $submissions as $submission ) {
            fputcsv( $output, $this->format_submission_row( $submission, $fields ) );
        }
        
        fclose( $output );
        exit;
    }
}
```

**Excel Export (usando PHPSpreadsheet):**
```php
// composer require phpoffice/phpspreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExporter {
    public function export( $form_id, $options ) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->fromArray( $this->get_headers( $fields ), NULL, 'A1' );
        
        // Data
        $row = 2;
        foreach ( $submissions as $submission ) {
            $sheet->fromArray( $this->format_row( $submission ), NULL, 'A' . $row );
            $row++;
        }
        
        // Style
        $sheet->getStyle( 'A1:Z1' )->getFont()->setBold( true );
        
        // Download
        $writer = new Xlsx( $spreadsheet );
        header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
        header( 'Content-Disposition: attachment; filename="export.xlsx"' );
        $writer->save( 'php://output' );
        exit;
    }
}
```

---

### 3️⃣ Form Templates (Priority: ⭐⭐⭐⭐)

**Effort:** 2 giorni  
**Impact:** MEDIO-ALTO

#### Template Structure

**Template JSON:**
```json
{
    "id": "contact-basic",
    "name": "Contatto Semplice",
    "description": "Form contatto base con campi essenziali",
    "category": "general",
    "thumbnail": "contact-basic.jpg",
    "fields": [
        {
            "type": "text",
            "label": "Nome Completo",
            "name": "full_name",
            "required": true,
            "options": {
                "placeholder": "Mario Rossi"
            }
        },
        {
            "type": "email",
            "label": "Email",
            "name": "email",
            "required": true,
            "options": {
                "placeholder": "mario@example.com"
            }
        },
        {
            "type": "phone",
            "label": "Telefono",
            "name": "phone",
            "required": false
        },
        {
            "type": "textarea",
            "label": "Messaggio",
            "name": "message",
            "required": true,
            "options": {
                "rows": 5,
                "placeholder": "Come possiamo aiutarti?"
            }
        }
    ],
    "settings": {
        "submit_button_text": "Invia Messaggio",
        "success_message": "Grazie! Ti contatteremo presto.",
        "notification_email": "{admin_email}",
        "notification_subject": "Nuovo messaggio da {field:full_name}"
    }
}
```

**Template Library:**
```php
// src/Templates/Library.php
class Library {
    private $templates = [];
    
    public function __construct() {
        $this->load_templates();
    }
    
    private function load_templates() {
        $template_dir = FP_FORMS_PLUGIN_DIR . 'templates/library/';
        $files = glob( $template_dir . '*.json' );
        
        foreach ( $files as $file ) {
            $template = json_decode( file_get_contents( $file ), true );
            $this->templates[ $template['id'] ] = $template;
        }
    }
    
    public function get_templates( $category = null ) {
        if ( $category ) {
            return array_filter( $this->templates, function( $t ) use ( $category ) {
                return $t['category'] === $category;
            } );
        }
        
        return $this->templates;
    }
    
    public function import_template( $template_id, $form_title = null ) {
        $template = $this->templates[ $template_id ];
        
        if ( ! $template ) {
            return false;
        }
        
        // Create form from template
        $form_id = \FPForms\Plugin::instance()->forms->create_form(
            $form_title ?: $template['name'],
            [
                'fields' => $template['fields'],
                'settings' => $template['settings'],
            ]
        );
        
        return $form_id;
    }
}
```

**UI in Builder:**
```php
// Admin page per template selection
<div class="fp-templates-library">
    <div class="fp-templates-categories">
        <button data-category="all">Tutti</button>
        <button data-category="general">Generali</button>
        <button data-category="business">Business</button>
        <button data-category="booking">Prenotazioni</button>
    </div>
    
    <div class="fp-templates-grid">
        <?php foreach ( $templates as $template ) : ?>
        <div class="fp-template-card" data-id="<?php echo $template['id']; ?>">
            <img src="<?php echo $template['thumbnail']; ?>">
            <h3><?php echo $template['name']; ?></h3>
            <p><?php echo $template['description']; ?></p>
            <button class="fp-import-template">
                Usa Template
            </button>
        </div>
        <?php endforeach; ?>
    </div>
</div>
```

**10 Template da Creare:**

1. **Contatto Semplice** - Nome, Email, Messaggio
2. **Preventivo** - Info azienda, Servizio, Budget, Timeline
3. **Prenotazione** - Nome, Data, Ora, Persone, Note
4. **Lavora con Noi** - Info personale, CV upload, Lettera motivazionale
5. **Newsletter** - Solo Email (minimal)
6. **Feedback** - Rating, Commento, Email
7. **Support Ticket** - Problema, Categoria, Urgenza, Descrizione
8. **Event Registration** - Nome, Email, Numero partecipanti, Dietary restrictions
9. **Quote Request** - Prodotto/Servizio, Quantità, Delivery date
10. **Survey Base** - Multiple choice, Ratings, Open text

---

### 4️⃣ Conditional Logic (Priority: ⭐⭐⭐⭐⭐)

**Effort:** 5-6 giorni  
**Impact:** ALTISSIMO

#### Implementazione Tecnica

**Data Structure:**
```json
{
    "rules": [
        {
            "id": "rule_1",
            "trigger_field": "tipo_richiesta",
            "condition": "equals",
            "value": "preventivo",
            "action": "show",
            "target_fields": ["budget", "timeline"],
            "logic": "and"
        },
        {
            "id": "rule_2",
            "trigger_field": "budget",
            "condition": "greater_than",
            "value": 10000,
            "action": "require",
            "target_fields": ["partita_iva"]
        }
    ]
}
```

**Frontend JS:**
```javascript
// assets/js/conditional-logic.js
class ConditionalLogic {
    constructor( formId, rules ) {
        this.formId = formId;
        this.rules = rules;
        this.init();
    }
    
    init() {
        this.rules.forEach( rule => {
            const trigger = document.querySelector( 
                `[name="fp_field_${rule.trigger_field}"]` 
            );
            
            trigger.addEventListener( 'change', () => {
                this.evaluateRule( rule );
            } );
            
            // Evaluate on load
            this.evaluateRule( rule );
        } );
    }
    
    evaluateRule( rule ) {
        const value = this.getFieldValue( rule.trigger_field );
        const shouldApply = this.checkCondition( value, rule.condition, rule.value );
        
        if ( shouldApply ) {
            this.applyAction( rule.action, rule.target_fields );
        } else {
            this.reverseAction( rule.action, rule.target_fields );
        }
    }
    
    checkCondition( value, condition, expected ) {
        switch ( condition ) {
            case 'equals':
                return value == expected;
            case 'not_equals':
                return value != expected;
            case 'contains':
                return value.includes( expected );
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
    
    applyAction( action, fields ) {
        fields.forEach( fieldName => {
            const field = this.getFieldElement( fieldName );
            
            switch ( action ) {
                case 'show':
                    field.style.display = 'block';
                    field.classList.add( 'fp-conditional-visible' );
                    break;
                case 'hide':
                    field.style.display = 'none';
                    field.classList.remove( 'fp-conditional-visible' );
                    break;
                case 'require':
                    this.setRequired( field, true );
                    break;
                case 'unrequire':
                    this.setRequired( field, false );
                    break;
            }
        } );
    }
}
```

**Builder UI:**
```html
<div class="fp-conditional-rules-builder">
    <button id="add-rule">+ Aggiungi Regola</button>
    
    <div class="fp-rules-list">
        <!-- Rule template -->
        <div class="fp-rule-item">
            <select name="trigger_field">
                <option>Quando campo...</option>
                <!-- Dynamic fields -->
            </select>
            
            <select name="condition">
                <option value="equals">è uguale a</option>
                <option value="not_equals">è diverso da</option>
                <option value="contains">contiene</option>
                <option value="greater_than">è maggiore di</option>
                <option value="less_than">è minore di</option>
            </select>
            
            <input type="text" name="value" placeholder="valore">
            
            <select name="action">
                <option value="show">Mostra</option>
                <option value="hide">Nascondi</option>
                <option value="require">Rendi obbligatorio</option>
            </select>
            
            <select name="targets" multiple>
                <!-- Dynamic fields -->
            </select>
            
            <button class="delete-rule">×</button>
        </div>
    </div>
</div>
```

---

### 5️⃣ Multi-Notification System (Priority: ⭐⭐⭐⭐)

**Effort:** 2-3 giorni  
**Impact:** MEDIO

#### Implementazione Tecnica

**Data Structure:**
```php
'notifications' => [
    [
        'id' => 'notif_1',
        'enabled' => true,
        'name' => 'Admin Notification',
        'to' => '{admin_email}',
        'cc' => '',
        'bcc' => '',
        'subject' => 'New submission: {form_title}',
        'message' => '...',
        'from_name' => '{site_name}',
        'from_email' => '{admin_email}',
        'reply_to' => '{field:email}',
        'condition' => [
            'field' => 'tipo',
            'operator' => 'equals',
            'value' => 'urgente'
        ],
        'attachments' => ['{field:cv}'], // Upload field
    ],
    [
        'id' => 'notif_2',
        'enabled' => true,
        'name' => 'User Confirmation',
        'to' => '{field:email}',
        'subject' => 'Thanks for your message',
        'message' => '...',
    ],
]
```

**Email Manager Update:**
```php
// src/Email/Manager.php - Update
public function send_notifications( $form_id, $submission_id, $data ) {
    $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
    $notifications = $form['settings']['notifications'] ?? [];
    
    foreach ( $notifications as $notification ) {
        // Skip if disabled
        if ( ! $notification['enabled'] ) {
            continue;
        }
        
        // Check condition
        if ( ! $this->check_notification_condition( $notification, $data ) ) {
            continue;
        }
        
        // Send email
        $this->send_notification_email( $notification, $form_id, $submission_id, $data );
    }
}

private function check_notification_condition( $notification, $data ) {
    if ( empty( $notification['condition'] ) ) {
        return true;
    }
    
    $condition = $notification['condition'];
    $field_value = $data[ $condition['field'] ] ?? '';
    
    switch ( $condition['operator'] ) {
        case 'equals':
            return $field_value == $condition['value'];
        case 'not_equals':
            return $field_value != $condition['value'];
        case 'contains':
            return strpos( $field_value, $condition['value'] ) !== false;
        default:
            return true;
    }
}
```

---

## 📊 Implementation Timeline

```
Week 1-2: Upload File
├── Field type creation
├── Upload handler
├── Security validation
├── Admin UI for downloads
└── Testing

Week 3: Export Submissions
├── CSV exporter
├── Excel exporter (PHPSpreadsheet)
├── Admin UI modal
└── Testing

Week 4: Form Templates
├── 10 template JSON files
├── Template library system
├── Import functionality
├── UI gallery
└── Testing

Week 5-6: Conditional Logic
├── Data structure
├── Frontend JS engine
├── Builder UI
├── Testing edge cases
└── Documentation

Week 7: Multi-Notification
├── Data structure update
├── Email manager refactor
├── Condition checking
├── Admin UI
└── Testing
```

---

## 🧪 Testing Checklist

### Upload File
- [ ] Validazione tipo file funziona
- [ ] Limite dimensione rispettato
- [ ] Upload multipli (se abilitato)
- [ ] File salvati correttamente
- [ ] Download funziona in admin
- [ ] File eliminati con submission
- [ ] Security: filename sanitizzato
- [ ] Security: MIME type verificato

### Export
- [ ] CSV export corretto
- [ ] Excel export corretto
- [ ] Date range filtering
- [ ] Field selection works
- [ ] Large dataset handling
- [ ] UTF-8 encoding
- [ ] Empty submissions handling

### Templates
- [ ] Import crea form corretto
- [ ] Campi preservati
- [ ] Settings preservate
- [ ] UI responsive
- [ ] Thumbnail loading
- [ ] Category filtering

### Conditional Logic
- [ ] All conditions work
- [ ] All actions work
- [ ] Multiple rules interaction
- [ ] Performance con molte regole
- [ ] Mobile compatibility
- [ ] Edge cases handling

### Multi-Notification
- [ ] Multiple emails inviate
- [ ] Conditions rispettate
- [ ] Attachments inclusi
- [ ] Reply-to corretto
- [ ] CC/BCC funzionanti
- [ ] Tag sostituiti correttamente

---

## 📚 Documentation Updates

Per ogni feature aggiungere:

1. **README.md** - Sezione nuova feature
2. **QUICK-START.md** - How-to rapido
3. **DEVELOPER.md** - Hooks disponibili
4. **FAQ** - Domande comuni

---

## 💡 Marketing

### Annunci Features

**Blog Post:** "5 Nuove Potenti Features in FP Forms v1.1"

**Social Media:**
- Screenshot upload file UI
- GIF conditional logic in azione
- Video template selection

**Email Newsletter:**
- Feature highlights
- Use cases pratici
- Early access per beta testers

---

## 🎯 Success Metrics

**KPIs da Tracciare:**
- Downloads v1.1
- Feature adoption rate
- Support tickets per feature
- User satisfaction survey
- Performance metrics

---

**Document Version:** 1.0  
**Created:** 2025-11-04  
**By:** Francesco Passeri

