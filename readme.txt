=== FP Forms ===

Contributors: franpass87
Tags: forms, form builder, contact form, landing page, stripe, payments, conditional logic
Requires at least: 5.8
Tested up to: 6.6
Stable tag: 1.6.3
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
