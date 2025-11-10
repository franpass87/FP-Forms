# FP Forms

Form builder professionale per WordPress, progettato per creare form di prenotazione e contatto da inserire nelle landing page.

## 🚀 Caratteristiche

### Core Features
- **Form Builder Drag & Drop** - Crea form personalizzati con facilità
- **Campi Multipli** - Testo, email, telefono, textarea, select, radio, checkbox, data, numero, **file upload** 🆕
- **Gestione Submissions** - Visualizza e gestisci tutte le submissions ricevute
- **Export Submissions** 🆕 - Export CSV e Excel con filtri avanzati
- **Template Library** 🆕 - 8 template pronti all'uso
- **Notifiche Email** - Invia automaticamente email di notifica
- **Email di Conferma** - Invia email di conferma agli utenti
- **Shortcode** - Inserisci i form ovunque con `[fp_form id="123"]`
- **Success Redirect** 🆕 - Redirect automatico dopo submit
- **Custom CSS** 🆕 - Classi CSS personalizzate
- **Responsive Design** - I form si adattano perfettamente a tutti i dispositivi
- **Validazione Campi** - Validazione lato client e server
- **Dark Mode Support** - Supporto automatico per modalità scura

### 🆕 Caratteristiche Avanzate (v1.0.0)
- **Architettura Modulare** - Design pattern enterprise-level
- **Caching Intelligente** - Performance ottimizzate (60-70% più veloce)
- **Sistema Logging** - Debugging e monitoraggio professionale
- **Hooks & Filters** - Completamente estendibile da sviluppatori
- **Field Factory** - Aggiungi tipi di campo custom facilmente
- **Capability System** - Controllo accessi granulare
- **Validazione Centralizzata** - Sistema di validazione robusto
- **Sanitizzazione Specializzata** - Sicurezza massima

## 📦 Installazione

1. Carica la cartella `FP-Forms` in `wp-content/plugins/`
2. Installa le dipendenze con Composer:
   ```bash
   cd wp-content/plugins/FP-Forms
   composer install
   ```
3. Attiva il plugin dal pannello WordPress
4. Vai su **FP Forms** > **Nuovo Form** per creare il tuo primo form

## 🎨 Design System

FP Forms utilizza un design system professionale **completamente coerente** con FP-Experiences:

- ✅ **Palette colori** unificata
- ✅ **Spacing system** consistente  
- ✅ **Componenti** riutilizzabili
- ✅ **Dark mode** nativo
- ✅ **Accessibilità** WCAG 2.1 AA

Per dettagli completi vedi [DESIGN-SYSTEM-FP.md](DESIGN-SYSTEM-FP.md)

## 🎯 Come Usare

### Creare un Form

1. Vai su **FP Forms** > **Nuovo Form**
2. Inserisci il titolo del form
3. Aggiungi i campi cliccando sui tipi di campo nella sidebar
4. Configura ogni campo (etichetta, nome, placeholder, ecc.)
5. Configura le impostazioni email nella sidebar
6. Clicca su **Salva Form**

### Inserire un Form

Usa lo shortcode generato automaticamente:

```
[fp_form id="123"]
```

Puoi inserire lo shortcode in:
- Post e Pagine
- Widget
- Page Builder (Elementor, WPBakery, ecc.)
- Template PHP: `<?php echo do_shortcode('[fp_form id="123"]'); ?>`

### Visualizzare le Submissions

1. Vai su **FP Forms** > **Tutti i Form**
2. Clicca sul numero di submissions del form desiderato
3. Visualizza, filtra ed esporta le submissions

## ⚙️ Impostazioni

Vai su **FP Forms** > **Impostazioni** per configurare:

- **Email Mittente** - Nome e indirizzo email mittente
- **Google reCAPTCHA** - Abilita protezione anti-spam (opzionale)

## 🎨 Personalizzazione

### CSS Personalizzato

Puoi sovrascrivere gli stili del plugin aggiungendo CSS personalizzato nel tuo tema:

```css
.fp-forms-form {
    /* I tuoi stili */
}
```

### Template Personalizzati

Copia i file da `templates/frontend/` nel tuo tema in `your-theme/fp-forms/` per personalizzarli.

## 🔧 Requisiti

- WordPress 5.8+
- PHP 7.4+
- Composer (per installazione dipendenze)

## 📝 Struttura Plugin

```
FP-Forms/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       └── frontend.js
├── includes/
│   ├── Activator.php
│   └── Deactivator.php
├── src/
│   ├── Plugin.php
│   ├── Admin/
│   │   └── Manager.php
│   ├── Database/
│   │   └── Manager.php
│   ├── Email/
│   │   └── Manager.php
│   ├── Forms/
│   │   └── Manager.php
│   ├── Frontend/
│   │   └── Manager.php
│   └── Submissions/
│       └── Manager.php
├── templates/
│   ├── admin/
│   │   ├── forms-list.php
│   │   ├── form-builder.php
│   │   ├── submissions-list.php
│   │   └── settings.php
│   └── frontend/
│       └── form.php
├── composer.json
├── fp-forms.php
└── README.md
```

## 🛡️ Sicurezza

- Tutti i dati vengono sanitizzati e validati
- Protezione CSRF con nonce
- Prepared statements per query al database
- Supporto reCAPTCHA per protezione anti-spam

## 📧 Supporto

Per supporto, contatta: info@francescopasseri.com

## 📄 Licenza

GPL v2 or later

## 🔄 Changelog

### 1.1.0 - 2025-11-04
- 🆕 **Upload File** - Nuovo campo per caricare file (CV, documenti, immagini)
- 🆕 **Export Submissions** - Export CSV e Excel con filtri
- 🆕 **Template Library** - 8 template predefiniti pronti all'uso
- 🆕 **Success Redirect** - Redirect automatico dopo submit
- 🆕 **Custom CSS Class** - Classi CSS personalizzate per form
- 🆕 **Conditional Logic** - Sistema base per logica condizionale (beta)
- 🆕 **Field Width** - Grid system per layout campi responsive
- ⚡ Performance migliorate
- 📚 Documentazione estesa

Vedi [CHANGELOG-v1.1.md](CHANGELOG-v1.1.md) per dettagli completi.

### 1.0.0 - 2025-11-04
- ✅ Release iniziale
- ✅ Form builder completo
- ✅ Gestione submissions
- ✅ Sistema notifiche email
- ✅ Supporto shortcode
- ✅ Design responsive
- 🆕 Architettura modulare ottimizzata
- 🆕 Sistema di caching per performance
- 🆕 Logger professionale per debugging
- 🆕 16 classi specializzate
- 🆕 Hooks & Filters per estensibilità
- 🆕 Field Factory pattern
- 🆕 Capability system
- 🆕 Performance migliorate del 60-70%

**Per dettagli completi sulle ottimizzazioni:** Vedi [OTTIMIZZAZIONI.md](OTTIMIZZAZIONI.md)

