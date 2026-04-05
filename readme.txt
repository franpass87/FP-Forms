=== FP Forms ===

Contributors: franpass87
Tags: forms, form builder, contact form, landing page, stripe, payments, conditional logic
Requires at least: 5.8
Tested up to: 6.6
Stable tag: 1.6.41
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Form builder professionale per WordPress: crea form di contatto e prenotazione per landing page. Pagamenti Stripe, logica condizionale, coda email, anti-spam.

== Description ==

FP Forms è un form builder per WordPress progettato per landing page, prenotazioni e form di contatto. Include:

* **Form builder** drag & drop con campi multipli (testo, email, telefono, file, data, numero, select, radio, checkbox)
* **Pagamenti Stripe** (v1.6): checkout redirect, webhook, email dopo conferma pagamento
* **Conditional Logic 2.0**: campi mostrati/nascosti e required in base ad altri campi; validazione server-side
* **Coda email**: invio in background, rate limit, fallback SMTP
* **Anti-spam**: reCAPTCHA v3, spam score composito, lock submission per evitare doppi invii
* Notifiche email (webmaster, staff, conferma utente)
* Export submissions in CSV e Excel
* Shortcode `[fp_form id="123"]` per inserire i form ovunque

Richiede PHP 7.4+ e WordPress 5.8+.

== Installation ==

1. Carica la cartella del plugin in `wp-content/plugins/` (o installa tramite zip).
2. Assicurati che la cartella `vendor/` sia presente (il plugin può essere distribuito con dipendenze già incluse).
3. Attiva il plugin da **Plugin** nel menu WordPress.
4. Vai su **FP Forms** > **Nuovo Form** per creare il primo form.
5. Inserisci il form con lo shortcode `[fp_form id="ID"]` in una pagina o un post.

Per i form a pagamento Stripe: configura le chiavi in **FP Forms** > **Impostazioni** e imposta il webhook Stripe su `https://tuosito.com/wp-json/fp-forms/v1/stripe-webhook`.

== Changelog ==

= 1.6.41 = (2026-04-05)
* Menu admin: voce FP Forms raggruppata con gli altri plugin FP (posizione 56.16).

= 1.6.40 = (2026-04-04)
* Form builder: colori personalizzati su due colonne.

= 1.6.39 = (2026-04-04)
* Form builder: sezione Pulsante Submit su due colonne (meno scroll verticale).

= 1.6.38 = (2026-04-04)
* 12 nuovi badge euristici nel builder (prova sociale, autorità, reciprocità, urgenza, rischio, supporto, attrito, trasparenza).

= 1.6.37 = (2026-04-04)
* Form builder: anteprima template automatico direttamente nei campi messaggio email (salvataggio invariato finché non modifichi il testo).

= 1.6.36 = (2026-04-04)
* Form builder: anteprima del template automatico sotto i messaggi email vuoti (webmaster, conferma, staff).

= 1.6.35 = (2026-04-04)
* Form builder: rimosso pulsante «Mostra tipi di campo» (palette già a destra).

= 1.6.34 = (2026-04-04)
* Consensi privacy/marketing collegati al bus tracking (generate_lead) e hook dedicato; Brevo rispetta opt-in marketing; opzione filtro per fp_consent_update.

= 1.6.33 = (2026-04-04)
* Nuovi badge euristici (12) + gruppi Attrito ridotto e Trasparenza.

= 1.6.32 = (2026-04-04)
* Form builder: layout flex workspace + clip orizzontale; niente margin negativi laterali sulla testata palette.

= 1.6.31 = (2026-04-04)
* Form builder: fix allineamento orizzontale colonna «Aggiungi campi» rispetto al contenitore.

= 1.6.30 = (2026-04-04)
* Form builder: badge euristici su più colonne (griglia) per meno scroll verticale.

= 1.6.29 = (2026-04-04)
* Form builder: fix lineetta blu sui titoli h4 delle impostazioni (Badge euristici, ecc.).

= 1.6.28 = (2026-04-04)
* Form builder: design system FP su banner, sezioni (card), workspace, impostazioni e pulsanti.

= 1.6.27 = (2026-04-04)
* Admin: fix contenuto che copriva il menu WP a sinistra.

= 1.6.26 = (2026-04-04)
* Form builder: card campi e palette allineate in alto (workspace + form_id nel meta).

= 1.6.25 = (2026-04-04)
* Form builder: palette senza sticky, tipi campo a 2 colonne, barra salva non fissa.

= 1.6.24 = (2026-04-04)
* Form builder: campi a sinistra, tipi campo a destra.

= 1.6.23 = (2026-04-04)
* Form builder: centraggio elenco campi, Salva centrato, fix click tipi campo (stacking + overflow).

= 1.6.22 = (2026-04-04)
* Form builder: sinistra tipi campo (2), centro elenco campi (1); titolo sopra.

= 1.6.21 = (2026-04-04)
* Form builder: destra solo elenco campi drag; palette tipi campo a tutta larghezza sotto, prima dell’aspetto.

= 1.6.20 = (2026-04-04)
* Form builder: copy header e stile palette sidebar (layout già in v1.6.19).

= 1.6.19 = (2026-04-04)
* Form builder: sidebar solo tipi campo; «Aspetto sul sito» a tutta larghezza sopra email/integrazioni.

= 1.6.15 = (2026-03-24)
* Brevo: upsert contatti via FP Tracking quando Brevo è abilitato nel layer; parametro lingua passato dal sync post-submission.

= 1.6.14 = (2026-03-24)
* Email HTML: con FP Mail SMTP attivo, wrapper branding centralizzato (`fp_fpmail_brand_html`) prima di wp_mail; plain text invariato.

= 1.6.13 = (2026-03-24)
* Brevo: nota su notifiche sempre wp_mail; checklist evento Track dopo sync (retrocompatibile con track_events).

= 1.6.12 = (2026-03-24)
* TrackingBridge: payload arricchito (affiliation, fp_source, page_url, UTM) e filtro fp_forms_tracking_event_params.

= 1.6.11 = (2026-03-23)
* Added: notice in sezione SMTP se FP Mail SMTP installato (centralizza configurazione).

= 1.6.10 = (2026-03-23)
* Fix: compatibilità FP Mail SMTP — FP Forms cede la configurazione SMTP quando FP Mail SMTP è attivo.

= 1.6.9 = (2026-03-23)
* Changed: Menu admin - Accrediti (add-on FP-Forms-Accrediti) spostato sotto Operatività; Accrediti Settings sotto Sistema.

= 1.6.8 = (2026-03-22)
* Fix: Logger - error_log su fallimento creazione .htaccess condizionato a WP_DEBUG.

= 1.6.7 = (2026-03-22)
* Fix: console log/warn/error condizionati a WP_DEBUG (conditional-logic, admin, progressive-save, calculator).

= 1.6.6 = (2026-03-22)
* Changed: rimossi i campi ID marketing locali (GTM/GA4/Meta) dalla pagina impostazioni; configurazione centralizzata in FP Marketing Tracking Layer per prevenire tracciamenti duplicati.

= 1.6.5 = (2026-03-21)
* Changed: Badge fiducia rinominati in "Badge euristici", raggruppati per bias cognitivo con label e tooltip; layout admin migliorato.

= 1.6.4 = (2026-03-19)
* Fix: template admin con h1 screen reader nel `.wrap` e titolo visibile in h2 (compat notice WordPress).

= 1.6.3 = (2025-03-19)
* Fix: admin CSS — niente flex/order su `#wpbody-content`; margine sul `.wrap` per le notice.

= 1.6.2 = (2025-03-18)
* Docs: readme Tested up to 6.6; sezione Traduzioni in README.

= 1.6.1 = (2025-03-18)
* Fix: pulizia coda email in Deactivator con wp_unschedule_hook().

= 1.6.0 =
* Added: Pagamenti Stripe (Checkout redirect, webhook, email dopo pagamento).
* Added: Conditional Logic 2.0 (validazione server-side, operatore AND/OR).
* Added: Coda email, rate limit, lock submission, spam score composito, fallback SMTP.
* Changed: Email per form a pagamento inviate solo dopo fp_forms_payment_completed.

= 1.5.1 =
* Fix: stringhe con encoding errato nelle impostazioni.

= 1.5.0 =
* Added: Simulazione Flussi (Dry-Run) nelle impostazioni.

= 1.4.12 =
* Changed: Migliorata area Submissions e testi notifiche email.

== Upgrade Notice ==

= 1.6.3 =
Fix layout admin: notice WordPress visualizzate nell'ordine corretto.

= 1.6.2 =
Aggiornamento documentazione: Tested up to 6.6, istruzioni traduzioni in README.

= 1.6.1 =
Fix pulizia eventi cron della coda email alla disattivazione del plugin.

= 1.6.0 =
Nuove funzionalità: pagamenti Stripe, Conditional Logic 2.0, coda email e anti-spam avanzato.
