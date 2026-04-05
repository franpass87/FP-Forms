# FP Forms

Form builder professionale per WordPress, progettato per creare form di prenotazione e contatto da inserire nelle landing page.

## 🚀 Caratteristiche

### Core Features
- **Form Builder Drag & Drop** - Crea form personalizzati con facilità; interfaccia admin con titolo in alto, elenco campi a sinistra (passo 1), palette tipi a destra in griglia a due colonne (passo 2), aspetto e avanzate sotto, flusso pagina senza sticky sulla palette
- **Campi Multipli** - Testo, email, telefono, textarea, select, radio, checkbox, data, numero, **file upload** 🆕
- **Gestione Submissions** - Visualizza e gestisci tutte le submissions ricevute
- **Export Submissions** 🆕 - Export CSV e Excel con filtri avanzati
- **Notifiche Webmaster/Staff più chiare** 🆕 - Messaggi email automatici semplificati e orientati all'azione
- **Simulazione Flussi (Dry-Run)** 🆕 - Verifica email/tracking/integrazioni senza credenziali e senza invii reali
- **Pagina Impostazioni ripulita** 🆕 - Correzione testi con caratteri corrotti per una lettura più chiara
- **Pagamenti Stripe (v1.6)** 🆕 - Checkout redirect, webhook, email dopo conferma pagamento
- **Conditional Logic 2.0 (v1.6)** 🆕 - Validazione server-side e operatore AND/OR tra regole
- **Coda email e anti-spam (v1.6)** 🆕 - Invio in background, rate limit, lock submission, spam score composito
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
3. Aggiungi i campi cliccando sui tipi nella colonna **Aggiungi campi** a destra
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

## 🌐 Traduzioni

Text domain: `fp-forms`. Per generare il file `.pot` (dalla root del sito WordPress):

```bash
wp i18n make-pot wp-content/plugins/FP-Forms wp-content/plugins/FP-Forms/languages/fp-forms.pot --domain=fp-forms
```

Le traduzioni vanno in `languages/` (es. `fp-forms-it_IT.po`).

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

### 1.6.41 - 2026-04-05
- Menu admin: posizione voce allineata allo schema FP (56.x), vicino agli altri plugin FP.

### 1.6.40 - 2026-04-04
- Form builder: griglia a due colonne anche per i quattro colori personalizzati del form.

### 1.6.39 - 2026-04-04
- Form builder: layout a griglia su due colonne per la sezione Pulsante Submit.

### 1.6.38 - 2026-04-04
- Aggiunti 12 badge euristici opzionali nel form builder (stessi gruppi bias cognitivi), con traduzioni en/de/fr/es.

### 1.6.37 - 2026-04-04
- Form builder: anteprima template automatico **dentro** i textarea messaggio; salvataggio come vuoto (template automatico) finché non modifichi il contenuto.

### 1.6.36 - 2026-04-04
- Form builder: anteprima read-only del template email automatico quando i campi messaggio (webmaster, conferma plain text, staff) sono vuoti.

### 1.6.17 - 2026-04-04
- Tracking: sulla pagina di ritorno da Stripe (`fp_forms_success`), evento `form_payment_completed` anche nel dataLayer (GTM) con stesso `event_id` del webhook, senza doppio invio server-side (richiede FP Marketing Tracking Layer con supporto `fp_skip_server_dispatch`).

### 1.6.13 - 2026-03-24
- Brevo: chiarito che le email del form restano su WordPress; evento Track opzionale con checklist allineata agli altri plugin FP.

### 1.6.5 - 2026-03-21
- Changed: Badge fiducia rinominati in "Badge euristici", raggruppati per bias cognitivo con label e tooltip; layout admin migliorato.

### 1.6.4 - 2026-03-19
- Fix: template admin con `h1` screen reader nel `.wrap` e titolo visibile in `h2` (compat notice WordPress).

### 1.6.3 - 2025-03-19
- Fix: admin CSS — niente flex/order su `#wpbody-content`; margine sul `.wrap` per le notice.

### 1.6.2 - 2025-03-18
- Docs: readme Tested up to 6.6; sezione Traduzioni in README.

### 1.6.1 - 2025-03-18
- Fix: pulizia coda email in Deactivator con `wp_unschedule_hook()`.

### 1.6.0 - 2026-03-18
- Pagamenti Stripe (Checkout redirect, webhook, email dopo pagamento)
- Conditional Logic 2.0 (validazione server-side, AND/OR)
- Coda email, rate limit, lock submission, spam score composito, fallback SMTP

Vedi [CHANGELOG.md](CHANGELOG.md) per la cronologia completa.

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
---

## Autore

**Francesco Passeri**
- Sito: [francescopasseri.com](https://francescopasseri.com)
- Email: [info@francescopasseri.com](mailto:info@francescopasseri.com)
- GitHub: [github.com/franpass87](https://github.com/franpass87)
