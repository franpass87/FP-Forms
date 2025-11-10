# 📁 Struttura Plugin FP Forms

## File e Directory Creati

```
FP-Forms/
│
├── 📄 fp-forms.php                          # File principale del plugin
├── 📄 composer.json                         # Configurazione Composer per PSR-4
├── 📄 composer.lock                         # Lock file Composer (auto-generato)
├── 📄 .gitignore                            # File da ignorare in Git
├── 📄 README.md                             # Documentazione completa
├── 📄 QUICK-START.md                        # Guida rapida all'uso
├── 📄 STRUTTURA-PLUGIN.md                   # Questo file
├── 📄 OTTIMIZZAZIONI.md                     # 🆕 Dettaglio ottimizzazioni
├── 📄 DEVELOPER.md                          # 🆕 Guida sviluppatori
├── 📄 RIEPILOGO-FINALE.md                   # 🆕 Riepilogo completo
│
├── 📁 vendor/                               # Dipendenze Composer (auto-generato)
│   └── autoload.php                         # Autoloader PSR-4
│
├── 📁 includes/                             # File di utilità
│   ├── Activator.php                        # Gestisce attivazione plugin (ottimizzato)
│   └── Deactivator.php                      # Gestisce disattivazione plugin (ottimizzato)
│
├── 📁 src/                                  # Classi PSR-4 del plugin
│   ├── Plugin.php                           # Classe principale singleton (ottimizzato)
│   │
│   ├── 📁 Core/                            # 🆕 Componenti core
│   │   ├── Capabilities.php                 # Gestione permessi e ruoli
│   │   ├── Cache.php                        # Object caching manager
│   │   ├── Hooks.php                        # Hooks & Filters system
│   │   └── Logger.php                       # Sistema logging professionale
│   │
│   ├── 📁 Helpers/                         # 🆕 Utility helpers
│   │   └── Helper.php                       # Funzioni comuni riutilizzabili
│   │
│   ├── 📁 Validators/                      # 🆕 Sistema validazione
│   │   └── Validator.php                    # Validatore campi centralizzato
│   │
│   ├── 📁 Sanitizers/                      # 🆕 Sistema sanitizzazione
│   │   └── Sanitizer.php                    # Sanitizzatore dati specializzato
│   │
│   ├── 📁 Fields/                          # 🆕 Gestione campi
│   │   └── FieldFactory.php                 # Factory pattern per rendering
│   │
│   ├── 📁 Admin/                           # Gestione area admin
│   │   └── Manager.php                      # Menu, pagine admin, AJAX handlers
│   │
│   ├── 📁 Database/                        # Gestione database
│   │   └── Manager.php                      # CRUD submissions e fields (con caching)
│   │
│   ├── 📁 Email/                           # Sistema email
│   │   └── Manager.php                      # Notifiche e conferme (con logging)
│   │
│   ├── 📁 Forms/                           # Gestione form
│   │   └── Manager.php                      # CRUD form, duplicazione
│   │
│   ├── 📁 Frontend/                        # Gestione frontend
│   │   └── Manager.php                      # Rendering form (usa FieldFactory)
│   │
│   └── 📁 Submissions/                     # Gestione submissions
│       └── Manager.php                      # Salvataggio (usa Validator/Sanitizer)
│
├── 📁 templates/                           # Template PHP
│   │
│   ├── 📁 admin/                           # Template admin
│   │   ├── forms-list.php                   # Lista tutti i form
│   │   ├── form-builder.php                 # Page builder form
│   │   ├── submissions-list.php             # Lista submissions
│   │   ├── settings.php                     # Pagina impostazioni
│   │   │
│   │   └── 📁 partials/                    # Componenti riutilizzabili
│   │       └── field-item.php               # Item campo nel builder
│   │
│   └── 📁 frontend/                        # Template frontend
│       └── form.php                         # Template form pubblico
│
└── 📁 assets/                              # Risorse statiche
    │
    ├── 📁 css/                             # Fogli di stile
    │   ├── admin.css                        # Stili area admin
    │   └── frontend.css                     # Stili frontend/form pubblici
    │
    └── 📁 js/                              # JavaScript
        ├── admin.js                         # Script area admin
        └── frontend.js                      # Script frontend/form pubblici
```

## 🗄️ Tabelle Database

Il plugin crea 2 tabelle custom:

### `wp_fp_forms_submissions`
Salva tutte le submissions ricevute dai form.

**Campi:**
- `id` - ID submission
- `form_id` - ID del form
- `data` - Dati submission (JSON)
- `user_id` - ID utente (se loggato)
- `user_ip` - IP dell'utente
- `user_agent` - User agent browser
- `status` - Stato (unread/read)
- `created_at` - Data/ora submission

### `wp_fp_forms_fields`
Salva la configurazione dei campi di ogni form.

**Campi:**
- `id` - ID campo
- `form_id` - ID del form
- `field_type` - Tipo campo (text, email, ecc.)
- `field_label` - Etichetta visualizzata
- `field_name` - Nome del campo
- `field_options` - Opzioni campo (JSON)
- `field_order` - Ordine visualizzazione
- `is_required` - Se obbligatorio
- `created_at` - Data/ora creazione

## 🎯 Custom Post Type

### `fp_form`
I form sono salvati come custom post type (non pubblico).

**Utilizzo:**
- `post_title` - Titolo del form
- `post_content` - Descrizione del form
- `post_meta['_fp_form_settings']` - Impostazioni form (array serializzato)

## 🔌 Hooks e Filtri Disponibili

### Actions
- `plugins_loaded` - Inizializza plugin
- `admin_menu` - Registra menu admin
- `admin_enqueue_scripts` - Carica assets admin
- `wp_enqueue_scripts` - Carica assets frontend
- `wp_ajax_fp_forms_submit` - Gestisce submit form (utenti loggati)
- `wp_ajax_nopriv_fp_forms_submit` - Gestisce submit form (utenti non loggati)

### Shortcodes
- `[fp_form id="123"]` - Visualizza form con ID specificato

## 🛠️ Funzioni Utili

### Ottenere un form
```php
$form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
```

### Ottenere submissions
```php
$submissions = \FPForms\Plugin::instance()->submissions->get_submissions( $form_id );
```

### Creare un form programmaticamente
```php
$form_id = \FPForms\Plugin::instance()->forms->create_form( 'Titolo Form', [
    'description' => 'Descrizione',
    'fields' => [
        [
            'type' => 'text',
            'label' => 'Nome',
            'name' => 'nome',
            'required' => true,
        ],
        [
            'type' => 'email',
            'label' => 'Email',
            'name' => 'email',
            'required' => true,
        ],
    ],
] );
```

## 📊 Flusso di Lavoro

### Creazione Form
1. Admin crea form via **FP Forms > Nuovo Form**
2. Form salvato come `fp_form` post type
3. Campi salvati in tabella `wp_fp_forms_fields`
4. Shortcode generato automaticamente

### Submission Form
1. Utente compila form in frontend
2. JavaScript valida campi lato client
3. AJAX invia dati al server
4. PHP valida e sanitizza dati
5. Dati salvati in `wp_fp_forms_submissions`
6. Email notifica inviata
7. Email conferma inviata (se abilitata)
8. Messaggio successo mostrato all'utente

### Visualizzazione Submissions
1. Admin va su **FP Forms > Submissions**
2. Lista caricata dalla tabella submissions
3. Dettagli visualizzabili in modal
4. Possibilità di eliminare submissions

## 🎨 Personalizzazione

### Override Template
Copia template da:
```
wp-content/plugins/FP-Forms/templates/frontend/form.php
```

Nel tuo tema:
```
wp-content/themes/tuo-tema/fp-forms/form.php
```

### CSS Personalizzato
Usa classi CSS disponibili:
- `.fp-forms-container` - Container principale
- `.fp-forms-form` - Form
- `.fp-forms-field` - Singolo campo
- `.fp-forms-submit-btn` - Pulsante submit
- `.fp-forms-success` - Messaggio successo
- `.fp-forms-error` - Messaggio errore

## 🔐 Sicurezza Implementata

✅ Nonce verification su tutte le richieste AJAX
✅ Capability check per azioni admin
✅ Sanitizzazione di tutti gli input utente
✅ Prepared statements per query database
✅ Validazione lato server di tutti i dati
✅ Escape di tutti gli output
✅ CSRF protection
✅ XSS protection

## 📈 Performance

- Autoload ottimizzato con Composer
- Query database indicizzate
- Assets caricati solo quando necessario
- Minimize richieste AJAX
- Cache-friendly (compatibile con plugin di cache)

---

**Versione:** 1.0.0  
**Autore:** Francesco Passeri  
**Data Creazione:** 2025-11-04

