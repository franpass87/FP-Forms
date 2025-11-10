# 🔬 BUGFIX SESSION #6 - FORENSIC ANALYSIS

**Data:** 5 Novembre 2025  
**Focus:** Feature Integration, Data Flow, System Coherence  
**Scope:** Conditional Logic, Templates, Hooks, Data Lifecycle

---

## 🎯 AREE NON ANCORA ANALIZZATE

**Sessioni precedenti:**
- #3: Security, Performance, Basic validation
- #4: Integration, AJAX, A11Y, Mobile  
- #5: Admin validation, Code verification

**Sessione #6 analizza:**
- Conditional Logic con nuovi campi
- Template Library con nuovi settings
- Form duplication/export/import
- Hooks/Filters implementation
- Data lifecycle completo
- Anti-spam honeypot
- Form reset behavior
- reCAPTCHA version switching

---

## 🔍 CATEGORIA 1: CONDITIONAL LOGIC

### **1.1 Conditional su Privacy/Marketing Checkbox**

**Scenario:**
```
Rule: "Show campo B if privacy-checkbox is checked"

Privacy checkbox è SEMPRE checked (forced required GDPR)
→ Condition sempre true?
→ Campo B sempre visibile?
→ Logic inutile?
```

**Status:** ⚠️ **LOGIC QUESTIONABLE**

---

### **1.2 Conditional su reCAPTCHA**

**Scenario:**
```
Rule: "Show campo X if recaptcha is valid"

reCAPTCHA validation è server-side!
→ Frontend non sa se valido
→ Conditional logic non può funzionare
```

**Status:** ⚠️ **IMPOSSIBLE CONDITION**

---

### **1.3 Rules con Nuovi Field Types**

**Check:**
```javascript
// conditional-logic.js - supporta privacy-checkbox?
```

**Potenziale Issue:**
- Conditional logic creato prima di privacy/marketing fields
- Supporta nuovi tipi?
- field.type === 'privacy-checkbox' gestito?

**Status:** ⚠️ **NEW FIELDS SUPPORT DA VERIFICARE**

---

## 🔍 CATEGORIA 2: TEMPLATE LIBRARY

### **2.1 Templates con Nuovi Settings**

**Check:**
```php
// In Library.php - template defaults
'submit_button_text' => 'Invia'
```

**Issue:**
- Templates vecchi non hanno nuovi settings (color, size, style)
- Form importato da template → mancano settings
- Frontend usa ?? defaults → OK
- Ma UX: utente si aspetta settings dal template

**Status:** ⚠️ **TEMPLATES NON AGGIORNATI**

---

### **2.2 Template Import - Settings Validation**

**Scenario:**
```
User importa template da JSON esterno
→ JSON contiene submit_button_color: "red"
→ Non HEX valido
→ Importato senza validazione?
```

**Status:** ⚠️ **IMPORT VALIDATION DA VERIFICARE**

---

## 🔍 CATEGORIA 3: FORM DUPLICATION

### **3.1 Duplicate Form - Settings Copy**

**Check:**
```php
public function ajax_duplicate_form()
```

**Potenziale Issue:**
- Duplica form con tutti i settings ✅
- Ma notifica email usa stessa email admin?
- User duplica form, dimentica di cambiare notification_email
- Riceve notifiche da entrambi i form

**Status:** ⚠️ **UX WARNING NEEDED**

---

### **3.2 Export/Import Settings**

**Scenario:**
```
Export form A con:
- notification_email: admin@siteA.com
- brevo_list_id: 123 (specifico site A)

Import in site B:
- Email admin@siteA.com non esiste
- Brevo list 123 non esiste in account site B
```

**Status:** ⚠️ **CROSS-SITE IMPORT ISSUES**

---

## 🔍 CATEGORIA 4: HOOKS & FILTERS

### **4.1 Hook Execution Order**

**Check:**
```php
do_action( 'fp_forms_after_save_submission', ... );
```

**Listeners:**
- Brevo sync (priority 10)
- Meta CAPI (priority 10)
- Custom hooks (unknown priority)

**Potenziale Issue:**
- Se 2 listener con stessa priority
- Ordine esecuzione non garantito
- Brevo sync prima o dopo Meta?
- Matters?

**Status:** ⚠️ **PRIORITY NON SPECIFICATA**

---

### **4.2 Filter Modification Chain**

**Codice:**
```php
$message = \FPForms\Core\Hooks::filter_email_message( $message, $form_id, $data );
```

**Potenziale Issue:**
- Filter 1: Modifica $message
- Filter 2: Modifica $message (assume format originale)
- Filter 2 crash se format cambiato da Filter 1

**Status:** ⚠️ **FILTER CHAIN FRAGILITY**

---

### **4.3 Hook Error Propagation**

**Check:**
```php
do_action( 'fp_forms_before_send_notification', ... );
// Se listener crasha qui?
// Email comunque inviata dopo?
```

**Status:** ⚠️ **NO ERROR ISOLATION**

---

## 🔍 CATEGORIA 5: DATA FLOW

### **5.1 Sanitized Data vs Raw Data**

**Flow:**
```php
1. $form_data = $_POST (raw)
2. $sanitized_data = sanitize_data( $form_data )
3. Validation usa $sanitized_data ✅
4. Save usa $sanitized_data ✅
5. Email usa $sanitized_data ✅
6. Brevo usa $sanitized_data ✅
7. Meta usa $sanitized_data ✅
```

**Check se in qualche punto usiamo raw invece di sanitized:**

**Status:** ⚠️ **DA VERIFICARE CONSISTENZA**

---

### **5.2 Form Reset - Hidden Fields**

**JavaScript:**
```javascript
$form[0].reset();
```

**Potenziale Issue:**
- Reset cancella anche hidden fields?
- form_id, nonce, action rimangono?
- reCAPTCHA token cancellato?

**Status:** ⚠️ **HIDDEN FIELDS RESET?**

---

### **5.3 File Upload - Data Persistence**

**Scenario:**
```
User upload file → validation fail su altro campo
→ Form mostra errore
→ File ancora uploadato?
→ User deve re-uploadare?
```

**Status:** ⚠️ **FILE LOST ON VALIDATION FAIL**

---

## 🔍 CATEGORIA 6: ANTI-SPAM

### **6.1 Honeypot Field**

**Check:**
```php
$anti_spam = new \FPForms\Security\AntiSpam();
echo $anti_spam->get_honeypot_field( $form['id'] );
```

**Verification:**
- Honeypot validato su submission?
- Se bot compila honeypot?
- Submission bloccata?

**Status:** ⚠️ **HONEYPOT VALIDATION DA VERIFICARE**

---

### **6.2 Rate Limiting**

**Potenziale Issue:**
```
Bot spam: 1000 submissions/minuto
→ Nessun rate limiting?
→ DB flood?
→ Email flood?
```

**Status:** ⚠️ **NO RATE LIMITING**

---

### **6.3 reCAPTCHA Bypass**

**Scenario:**
```
Form con reCAPTCHA
→ User rimuove campo reCAPTCHA dal DOM (DevTools)
→ Submit senza token
→ Validation fallisce? ✅
→ Ma logged come tentativo malevolo?
```

**Status:** ⚠️ **NO SECURITY LOGGING**

---

## 🔍 CATEGORIA 7: EMAIL DELIVERY

### **7.1 wp_mail() Failure Silent**

**Check:**
```php
try {
    $success = wp_mail( $to, $subject, $message, $headers );
    Logger::log_email( $to, $subject, $success );
} catch ( \Exception $e ) {
    Logger::error( 'Email failed' );
}
```

**Issue:**
- wp_mail() ritorna false (not exception)
- catch non viene mai eseguito
- Only log_email() registra

**Status:** ⚠️ **EXCEPTION CATCH INUTILE**

---

### **7.2 Email Queue**

**Scenario:**
```
Form submission con:
- Email webmaster
- Email cliente  
- Email staff (3 persone)
= 5 email totali

Inviate sequenzialmente:
→ 5 × wp_mail() calls
→ Lento?
→ Async queue?
```

**Status:** ⚠️ **NO ASYNC EMAIL QUEUE**

---

### **7.3 Email Delivery Retry**

**Scenario:**
```
wp_mail() fail (SMTP down)
→ Email persa
→ Nessun retry
→ User non sa
```

**Status:** ⚠️ **NO RETRY MECHANISM**

---

## 🔍 CATEGORIA 8: INTEGRATIONS EDGE CASES

### **8.1 Brevo - Email Campo Non Email Type**

**Scenario:**
```
Form senza campo type="email"
→ Solo campo "text" con nome "contact"
→ extract_email() cerca 'email' field
→ Non trova
→ Sync skip? ✅ (già gestito con check)
```

**Status:** ✅ **GIÀ GESTITO**

---

### **8.2 Meta CAPI - User Data Minimal**

**Scenario:**
```
Form solo con "messaggio" (textarea)
→ Nessun email, nome, phone
→ prepare_user_data() ritorna array vuoto
→ Meta API call con user_data vuoto
→ Accettato da Meta?
```

**Status:** ⚠️ **MINIMAL DATA DA VERIFICARE**

---

### **8.3 reCAPTCHA v2 ↔ v3 Switch**

**Scenario:**
```
Form creato con reCAPTCHA v2
→ Admin cambia settings globali a v3
→ Form esistente ha già campo reCAPTCHA
→ Quale versione viene usata?
→ Mismatch token?
```

**Status:** ⚠️ **VERSION SWITCH CONSISTENCY**

---

## 🔍 CATEGORIA 9: FORM LIFECYCLE

### **9.1 Form Deletion - Submissions Orphan**

**Scenario:**
```
Form cancellato
→ Submissions associate rimangono?
→ Orphan posts in DB?
→ Cleanup automatico?
```

**Status:** ⚠️ **ORPHAN SUBMISSIONS?**

---

### **9.2 Plugin Deactivation**

**Check:**
```php
register_deactivation_hook()
```

**Issue:**
- Cosa succede ai form salvati?
- Submissions rimangono?
- Settings rimangono?
- Cleanup?

**Status:** ⚠️ **DEACTIVATION CLEANUP DA VERIFICARE**

---

### **9.3 Plugin Uninstall**

**Check:**
```php
register_uninstall_hook()
```

**GDPR:**
- User disinstalla plugin
- Dati sensibili rimangono in DB?
- Forms, submissions, meta devono essere cancellati

**Status:** ⚠️ **UNINSTALL CLEANUP DA VERIFICARE**

---

## 🔍 CATEGORIA 10: TRACKING CONSISTENCY

### **10.1 GTM vs GA4 Events**

**Check:**
```javascript
// Tracking.php
pushToDataLayer() // GTM
sendToGA4()       // GA4
```

**Potenziale Issue:**
- Eventi inviati a entrambi?
- Duplicazione eventi in GA4 (se GTM → GA4)?
- User configura GTM che già invia a GA4
- + plugin invia direttamente a GA4
- = Eventi doppi

**Status:** ⚠️ **EVENT DUPLICATION RISK**

---

### **10.2 Meta Pixel vs CAPI**

**Check:**
```javascript
// Pixel client-side
fbq('track', 'Lead');

// CAPI server-side
send_conversion_event('Lead');
```

**Potenziale Issue:**
- Stesso evento inviato 2 volte (client + server)
- Meta deduplication ID usato?
- Eventi contati doppi?

**Status:** ⚠️ **DEDUPLICATION DA VERIFICARE**

---

### **10.3 Tracking Form View Multiple**

**Scenario:**
```
Pagina con form caricata
→ fpFormView evento
→ User naviga via
→ Torna indietro (browser back)
→ fpFormView di nuovo?
→ View contato doppio?
```

**Status:** ⚠️ **DUPLICATE VIEW TRACKING**

---

## 📊 RIEPILOGO NUOVI BUG TROVATI

### **🔴 CRITICI (0)**
Nessuno!

### **🟡 MODERATI (12)**

1. Honeypot validation
2. Rate limiting assente
3. Email queue non async
4. Form deletion orphans
5. Uninstall cleanup
6. Templates non aggiornati
7. Import validation
8. Meta CAPI deduplication
9. GTM/GA4 event duplication
10. reCAPTCHA version switch
11. Hook error isolation
12. Filter chain fragility

### **🟢 MINORI (10)**

13. Conditional logic su privacy field
14. Conditional su reCAPTCHA
15. New fields in conditional
16. Duplicate form email warning
17. Cross-site import issues
18. Hook execution order
19. Form reset hidden fields
20. File lost on validation fail
21. wp_mail exception handling
22. Tracking view duplication

---

## 🎯 PRIORITÀ ANALISI

**P0:** Verificare esistenti (già implementati?)
**P1:** Fix critici se trovati
**P2:** Documentare edge cases
**P3:** Suggerimenti futuri

---



**Data:** 5 Novembre 2025  
**Focus:** Feature Integration, Data Flow, System Coherence  
**Scope:** Conditional Logic, Templates, Hooks, Data Lifecycle

---

## 🎯 AREE NON ANCORA ANALIZZATE

**Sessioni precedenti:**
- #3: Security, Performance, Basic validation
- #4: Integration, AJAX, A11Y, Mobile  
- #5: Admin validation, Code verification

**Sessione #6 analizza:**
- Conditional Logic con nuovi campi
- Template Library con nuovi settings
- Form duplication/export/import
- Hooks/Filters implementation
- Data lifecycle completo
- Anti-spam honeypot
- Form reset behavior
- reCAPTCHA version switching

---

## 🔍 CATEGORIA 1: CONDITIONAL LOGIC

### **1.1 Conditional su Privacy/Marketing Checkbox**

**Scenario:**
```
Rule: "Show campo B if privacy-checkbox is checked"

Privacy checkbox è SEMPRE checked (forced required GDPR)
→ Condition sempre true?
→ Campo B sempre visibile?
→ Logic inutile?
```

**Status:** ⚠️ **LOGIC QUESTIONABLE**

---

### **1.2 Conditional su reCAPTCHA**

**Scenario:**
```
Rule: "Show campo X if recaptcha is valid"

reCAPTCHA validation è server-side!
→ Frontend non sa se valido
→ Conditional logic non può funzionare
```

**Status:** ⚠️ **IMPOSSIBLE CONDITION**

---

### **1.3 Rules con Nuovi Field Types**

**Check:**
```javascript
// conditional-logic.js - supporta privacy-checkbox?
```

**Potenziale Issue:**
- Conditional logic creato prima di privacy/marketing fields
- Supporta nuovi tipi?
- field.type === 'privacy-checkbox' gestito?

**Status:** ⚠️ **NEW FIELDS SUPPORT DA VERIFICARE**

---

## 🔍 CATEGORIA 2: TEMPLATE LIBRARY

### **2.1 Templates con Nuovi Settings**

**Check:**
```php
// In Library.php - template defaults
'submit_button_text' => 'Invia'
```

**Issue:**
- Templates vecchi non hanno nuovi settings (color, size, style)
- Form importato da template → mancano settings
- Frontend usa ?? defaults → OK
- Ma UX: utente si aspetta settings dal template

**Status:** ⚠️ **TEMPLATES NON AGGIORNATI**

---

### **2.2 Template Import - Settings Validation**

**Scenario:**
```
User importa template da JSON esterno
→ JSON contiene submit_button_color: "red"
→ Non HEX valido
→ Importato senza validazione?
```

**Status:** ⚠️ **IMPORT VALIDATION DA VERIFICARE**

---

## 🔍 CATEGORIA 3: FORM DUPLICATION

### **3.1 Duplicate Form - Settings Copy**

**Check:**
```php
public function ajax_duplicate_form()
```

**Potenziale Issue:**
- Duplica form con tutti i settings ✅
- Ma notifica email usa stessa email admin?
- User duplica form, dimentica di cambiare notification_email
- Riceve notifiche da entrambi i form

**Status:** ⚠️ **UX WARNING NEEDED**

---

### **3.2 Export/Import Settings**

**Scenario:**
```
Export form A con:
- notification_email: admin@siteA.com
- brevo_list_id: 123 (specifico site A)

Import in site B:
- Email admin@siteA.com non esiste
- Brevo list 123 non esiste in account site B
```

**Status:** ⚠️ **CROSS-SITE IMPORT ISSUES**

---

## 🔍 CATEGORIA 4: HOOKS & FILTERS

### **4.1 Hook Execution Order**

**Check:**
```php
do_action( 'fp_forms_after_save_submission', ... );
```

**Listeners:**
- Brevo sync (priority 10)
- Meta CAPI (priority 10)
- Custom hooks (unknown priority)

**Potenziale Issue:**
- Se 2 listener con stessa priority
- Ordine esecuzione non garantito
- Brevo sync prima o dopo Meta?
- Matters?

**Status:** ⚠️ **PRIORITY NON SPECIFICATA**

---

### **4.2 Filter Modification Chain**

**Codice:**
```php
$message = \FPForms\Core\Hooks::filter_email_message( $message, $form_id, $data );
```

**Potenziale Issue:**
- Filter 1: Modifica $message
- Filter 2: Modifica $message (assume format originale)
- Filter 2 crash se format cambiato da Filter 1

**Status:** ⚠️ **FILTER CHAIN FRAGILITY**

---

### **4.3 Hook Error Propagation**

**Check:**
```php
do_action( 'fp_forms_before_send_notification', ... );
// Se listener crasha qui?
// Email comunque inviata dopo?
```

**Status:** ⚠️ **NO ERROR ISOLATION**

---

## 🔍 CATEGORIA 5: DATA FLOW

### **5.1 Sanitized Data vs Raw Data**

**Flow:**
```php
1. $form_data = $_POST (raw)
2. $sanitized_data = sanitize_data( $form_data )
3. Validation usa $sanitized_data ✅
4. Save usa $sanitized_data ✅
5. Email usa $sanitized_data ✅
6. Brevo usa $sanitized_data ✅
7. Meta usa $sanitized_data ✅
```

**Check se in qualche punto usiamo raw invece di sanitized:**

**Status:** ⚠️ **DA VERIFICARE CONSISTENZA**

---

### **5.2 Form Reset - Hidden Fields**

**JavaScript:**
```javascript
$form[0].reset();
```

**Potenziale Issue:**
- Reset cancella anche hidden fields?
- form_id, nonce, action rimangono?
- reCAPTCHA token cancellato?

**Status:** ⚠️ **HIDDEN FIELDS RESET?**

---

### **5.3 File Upload - Data Persistence**

**Scenario:**
```
User upload file → validation fail su altro campo
→ Form mostra errore
→ File ancora uploadato?
→ User deve re-uploadare?
```

**Status:** ⚠️ **FILE LOST ON VALIDATION FAIL**

---

## 🔍 CATEGORIA 6: ANTI-SPAM

### **6.1 Honeypot Field**

**Check:**
```php
$anti_spam = new \FPForms\Security\AntiSpam();
echo $anti_spam->get_honeypot_field( $form['id'] );
```

**Verification:**
- Honeypot validato su submission?
- Se bot compila honeypot?
- Submission bloccata?

**Status:** ⚠️ **HONEYPOT VALIDATION DA VERIFICARE**

---

### **6.2 Rate Limiting**

**Potenziale Issue:**
```
Bot spam: 1000 submissions/minuto
→ Nessun rate limiting?
→ DB flood?
→ Email flood?
```

**Status:** ⚠️ **NO RATE LIMITING**

---

### **6.3 reCAPTCHA Bypass**

**Scenario:**
```
Form con reCAPTCHA
→ User rimuove campo reCAPTCHA dal DOM (DevTools)
→ Submit senza token
→ Validation fallisce? ✅
→ Ma logged come tentativo malevolo?
```

**Status:** ⚠️ **NO SECURITY LOGGING**

---

## 🔍 CATEGORIA 7: EMAIL DELIVERY

### **7.1 wp_mail() Failure Silent**

**Check:**
```php
try {
    $success = wp_mail( $to, $subject, $message, $headers );
    Logger::log_email( $to, $subject, $success );
} catch ( \Exception $e ) {
    Logger::error( 'Email failed' );
}
```

**Issue:**
- wp_mail() ritorna false (not exception)
- catch non viene mai eseguito
- Only log_email() registra

**Status:** ⚠️ **EXCEPTION CATCH INUTILE**

---

### **7.2 Email Queue**

**Scenario:**
```
Form submission con:
- Email webmaster
- Email cliente  
- Email staff (3 persone)
= 5 email totali

Inviate sequenzialmente:
→ 5 × wp_mail() calls
→ Lento?
→ Async queue?
```

**Status:** ⚠️ **NO ASYNC EMAIL QUEUE**

---

### **7.3 Email Delivery Retry**

**Scenario:**
```
wp_mail() fail (SMTP down)
→ Email persa
→ Nessun retry
→ User non sa
```

**Status:** ⚠️ **NO RETRY MECHANISM**

---

## 🔍 CATEGORIA 8: INTEGRATIONS EDGE CASES

### **8.1 Brevo - Email Campo Non Email Type**

**Scenario:**
```
Form senza campo type="email"
→ Solo campo "text" con nome "contact"
→ extract_email() cerca 'email' field
→ Non trova
→ Sync skip? ✅ (già gestito con check)
```

**Status:** ✅ **GIÀ GESTITO**

---

### **8.2 Meta CAPI - User Data Minimal**

**Scenario:**
```
Form solo con "messaggio" (textarea)
→ Nessun email, nome, phone
→ prepare_user_data() ritorna array vuoto
→ Meta API call con user_data vuoto
→ Accettato da Meta?
```

**Status:** ⚠️ **MINIMAL DATA DA VERIFICARE**

---

### **8.3 reCAPTCHA v2 ↔ v3 Switch**

**Scenario:**
```
Form creato con reCAPTCHA v2
→ Admin cambia settings globali a v3
→ Form esistente ha già campo reCAPTCHA
→ Quale versione viene usata?
→ Mismatch token?
```

**Status:** ⚠️ **VERSION SWITCH CONSISTENCY**

---

## 🔍 CATEGORIA 9: FORM LIFECYCLE

### **9.1 Form Deletion - Submissions Orphan**

**Scenario:**
```
Form cancellato
→ Submissions associate rimangono?
→ Orphan posts in DB?
→ Cleanup automatico?
```

**Status:** ⚠️ **ORPHAN SUBMISSIONS?**

---

### **9.2 Plugin Deactivation**

**Check:**
```php
register_deactivation_hook()
```

**Issue:**
- Cosa succede ai form salvati?
- Submissions rimangono?
- Settings rimangono?
- Cleanup?

**Status:** ⚠️ **DEACTIVATION CLEANUP DA VERIFICARE**

---

### **9.3 Plugin Uninstall**

**Check:**
```php
register_uninstall_hook()
```

**GDPR:**
- User disinstalla plugin
- Dati sensibili rimangono in DB?
- Forms, submissions, meta devono essere cancellati

**Status:** ⚠️ **UNINSTALL CLEANUP DA VERIFICARE**

---

## 🔍 CATEGORIA 10: TRACKING CONSISTENCY

### **10.1 GTM vs GA4 Events**

**Check:**
```javascript
// Tracking.php
pushToDataLayer() // GTM
sendToGA4()       // GA4
```

**Potenziale Issue:**
- Eventi inviati a entrambi?
- Duplicazione eventi in GA4 (se GTM → GA4)?
- User configura GTM che già invia a GA4
- + plugin invia direttamente a GA4
- = Eventi doppi

**Status:** ⚠️ **EVENT DUPLICATION RISK**

---

### **10.2 Meta Pixel vs CAPI**

**Check:**
```javascript
// Pixel client-side
fbq('track', 'Lead');

// CAPI server-side
send_conversion_event('Lead');
```

**Potenziale Issue:**
- Stesso evento inviato 2 volte (client + server)
- Meta deduplication ID usato?
- Eventi contati doppi?

**Status:** ⚠️ **DEDUPLICATION DA VERIFICARE**

---

### **10.3 Tracking Form View Multiple**

**Scenario:**
```
Pagina con form caricata
→ fpFormView evento
→ User naviga via
→ Torna indietro (browser back)
→ fpFormView di nuovo?
→ View contato doppio?
```

**Status:** ⚠️ **DUPLICATE VIEW TRACKING**

---

## 📊 RIEPILOGO NUOVI BUG TROVATI

### **🔴 CRITICI (0)**
Nessuno!

### **🟡 MODERATI (12)**

1. Honeypot validation
2. Rate limiting assente
3. Email queue non async
4. Form deletion orphans
5. Uninstall cleanup
6. Templates non aggiornati
7. Import validation
8. Meta CAPI deduplication
9. GTM/GA4 event duplication
10. reCAPTCHA version switch
11. Hook error isolation
12. Filter chain fragility

### **🟢 MINORI (10)**

13. Conditional logic su privacy field
14. Conditional su reCAPTCHA
15. New fields in conditional
16. Duplicate form email warning
17. Cross-site import issues
18. Hook execution order
19. Form reset hidden fields
20. File lost on validation fail
21. wp_mail exception handling
22. Tracking view duplication

---

## 🎯 PRIORITÀ ANALISI

**P0:** Verificare esistenti (già implementati?)
**P1:** Fix critici se trovati
**P2:** Documentare edge cases
**P3:** Suggerimenti futuri

---



**Data:** 5 Novembre 2025  
**Focus:** Feature Integration, Data Flow, System Coherence  
**Scope:** Conditional Logic, Templates, Hooks, Data Lifecycle

---

## 🎯 AREE NON ANCORA ANALIZZATE

**Sessioni precedenti:**
- #3: Security, Performance, Basic validation
- #4: Integration, AJAX, A11Y, Mobile  
- #5: Admin validation, Code verification

**Sessione #6 analizza:**
- Conditional Logic con nuovi campi
- Template Library con nuovi settings
- Form duplication/export/import
- Hooks/Filters implementation
- Data lifecycle completo
- Anti-spam honeypot
- Form reset behavior
- reCAPTCHA version switching

---

## 🔍 CATEGORIA 1: CONDITIONAL LOGIC

### **1.1 Conditional su Privacy/Marketing Checkbox**

**Scenario:**
```
Rule: "Show campo B if privacy-checkbox is checked"

Privacy checkbox è SEMPRE checked (forced required GDPR)
→ Condition sempre true?
→ Campo B sempre visibile?
→ Logic inutile?
```

**Status:** ⚠️ **LOGIC QUESTIONABLE**

---

### **1.2 Conditional su reCAPTCHA**

**Scenario:**
```
Rule: "Show campo X if recaptcha is valid"

reCAPTCHA validation è server-side!
→ Frontend non sa se valido
→ Conditional logic non può funzionare
```

**Status:** ⚠️ **IMPOSSIBLE CONDITION**

---

### **1.3 Rules con Nuovi Field Types**

**Check:**
```javascript
// conditional-logic.js - supporta privacy-checkbox?
```

**Potenziale Issue:**
- Conditional logic creato prima di privacy/marketing fields
- Supporta nuovi tipi?
- field.type === 'privacy-checkbox' gestito?

**Status:** ⚠️ **NEW FIELDS SUPPORT DA VERIFICARE**

---

## 🔍 CATEGORIA 2: TEMPLATE LIBRARY

### **2.1 Templates con Nuovi Settings**

**Check:**
```php
// In Library.php - template defaults
'submit_button_text' => 'Invia'
```

**Issue:**
- Templates vecchi non hanno nuovi settings (color, size, style)
- Form importato da template → mancano settings
- Frontend usa ?? defaults → OK
- Ma UX: utente si aspetta settings dal template

**Status:** ⚠️ **TEMPLATES NON AGGIORNATI**

---

### **2.2 Template Import - Settings Validation**

**Scenario:**
```
User importa template da JSON esterno
→ JSON contiene submit_button_color: "red"
→ Non HEX valido
→ Importato senza validazione?
```

**Status:** ⚠️ **IMPORT VALIDATION DA VERIFICARE**

---

## 🔍 CATEGORIA 3: FORM DUPLICATION

### **3.1 Duplicate Form - Settings Copy**

**Check:**
```php
public function ajax_duplicate_form()
```

**Potenziale Issue:**
- Duplica form con tutti i settings ✅
- Ma notifica email usa stessa email admin?
- User duplica form, dimentica di cambiare notification_email
- Riceve notifiche da entrambi i form

**Status:** ⚠️ **UX WARNING NEEDED**

---

### **3.2 Export/Import Settings**

**Scenario:**
```
Export form A con:
- notification_email: admin@siteA.com
- brevo_list_id: 123 (specifico site A)

Import in site B:
- Email admin@siteA.com non esiste
- Brevo list 123 non esiste in account site B
```

**Status:** ⚠️ **CROSS-SITE IMPORT ISSUES**

---

## 🔍 CATEGORIA 4: HOOKS & FILTERS

### **4.1 Hook Execution Order**

**Check:**
```php
do_action( 'fp_forms_after_save_submission', ... );
```

**Listeners:**
- Brevo sync (priority 10)
- Meta CAPI (priority 10)
- Custom hooks (unknown priority)

**Potenziale Issue:**
- Se 2 listener con stessa priority
- Ordine esecuzione non garantito
- Brevo sync prima o dopo Meta?
- Matters?

**Status:** ⚠️ **PRIORITY NON SPECIFICATA**

---

### **4.2 Filter Modification Chain**

**Codice:**
```php
$message = \FPForms\Core\Hooks::filter_email_message( $message, $form_id, $data );
```

**Potenziale Issue:**
- Filter 1: Modifica $message
- Filter 2: Modifica $message (assume format originale)
- Filter 2 crash se format cambiato da Filter 1

**Status:** ⚠️ **FILTER CHAIN FRAGILITY**

---

### **4.3 Hook Error Propagation**

**Check:**
```php
do_action( 'fp_forms_before_send_notification', ... );
// Se listener crasha qui?
// Email comunque inviata dopo?
```

**Status:** ⚠️ **NO ERROR ISOLATION**

---

## 🔍 CATEGORIA 5: DATA FLOW

### **5.1 Sanitized Data vs Raw Data**

**Flow:**
```php
1. $form_data = $_POST (raw)
2. $sanitized_data = sanitize_data( $form_data )
3. Validation usa $sanitized_data ✅
4. Save usa $sanitized_data ✅
5. Email usa $sanitized_data ✅
6. Brevo usa $sanitized_data ✅
7. Meta usa $sanitized_data ✅
```

**Check se in qualche punto usiamo raw invece di sanitized:**

**Status:** ⚠️ **DA VERIFICARE CONSISTENZA**

---

### **5.2 Form Reset - Hidden Fields**

**JavaScript:**
```javascript
$form[0].reset();
```

**Potenziale Issue:**
- Reset cancella anche hidden fields?
- form_id, nonce, action rimangono?
- reCAPTCHA token cancellato?

**Status:** ⚠️ **HIDDEN FIELDS RESET?**

---

### **5.3 File Upload - Data Persistence**

**Scenario:**
```
User upload file → validation fail su altro campo
→ Form mostra errore
→ File ancora uploadato?
→ User deve re-uploadare?
```

**Status:** ⚠️ **FILE LOST ON VALIDATION FAIL**

---

## 🔍 CATEGORIA 6: ANTI-SPAM

### **6.1 Honeypot Field**

**Check:**
```php
$anti_spam = new \FPForms\Security\AntiSpam();
echo $anti_spam->get_honeypot_field( $form['id'] );
```

**Verification:**
- Honeypot validato su submission?
- Se bot compila honeypot?
- Submission bloccata?

**Status:** ⚠️ **HONEYPOT VALIDATION DA VERIFICARE**

---

### **6.2 Rate Limiting**

**Potenziale Issue:**
```
Bot spam: 1000 submissions/minuto
→ Nessun rate limiting?
→ DB flood?
→ Email flood?
```

**Status:** ⚠️ **NO RATE LIMITING**

---

### **6.3 reCAPTCHA Bypass**

**Scenario:**
```
Form con reCAPTCHA
→ User rimuove campo reCAPTCHA dal DOM (DevTools)
→ Submit senza token
→ Validation fallisce? ✅
→ Ma logged come tentativo malevolo?
```

**Status:** ⚠️ **NO SECURITY LOGGING**

---

## 🔍 CATEGORIA 7: EMAIL DELIVERY

### **7.1 wp_mail() Failure Silent**

**Check:**
```php
try {
    $success = wp_mail( $to, $subject, $message, $headers );
    Logger::log_email( $to, $subject, $success );
} catch ( \Exception $e ) {
    Logger::error( 'Email failed' );
}
```

**Issue:**
- wp_mail() ritorna false (not exception)
- catch non viene mai eseguito
- Only log_email() registra

**Status:** ⚠️ **EXCEPTION CATCH INUTILE**

---

### **7.2 Email Queue**

**Scenario:**
```
Form submission con:
- Email webmaster
- Email cliente  
- Email staff (3 persone)
= 5 email totali

Inviate sequenzialmente:
→ 5 × wp_mail() calls
→ Lento?
→ Async queue?
```

**Status:** ⚠️ **NO ASYNC EMAIL QUEUE**

---

### **7.3 Email Delivery Retry**

**Scenario:**
```
wp_mail() fail (SMTP down)
→ Email persa
→ Nessun retry
→ User non sa
```

**Status:** ⚠️ **NO RETRY MECHANISM**

---

## 🔍 CATEGORIA 8: INTEGRATIONS EDGE CASES

### **8.1 Brevo - Email Campo Non Email Type**

**Scenario:**
```
Form senza campo type="email"
→ Solo campo "text" con nome "contact"
→ extract_email() cerca 'email' field
→ Non trova
→ Sync skip? ✅ (già gestito con check)
```

**Status:** ✅ **GIÀ GESTITO**

---

### **8.2 Meta CAPI - User Data Minimal**

**Scenario:**
```
Form solo con "messaggio" (textarea)
→ Nessun email, nome, phone
→ prepare_user_data() ritorna array vuoto
→ Meta API call con user_data vuoto
→ Accettato da Meta?
```

**Status:** ⚠️ **MINIMAL DATA DA VERIFICARE**

---

### **8.3 reCAPTCHA v2 ↔ v3 Switch**

**Scenario:**
```
Form creato con reCAPTCHA v2
→ Admin cambia settings globali a v3
→ Form esistente ha già campo reCAPTCHA
→ Quale versione viene usata?
→ Mismatch token?
```

**Status:** ⚠️ **VERSION SWITCH CONSISTENCY**

---

## 🔍 CATEGORIA 9: FORM LIFECYCLE

### **9.1 Form Deletion - Submissions Orphan**

**Scenario:**
```
Form cancellato
→ Submissions associate rimangono?
→ Orphan posts in DB?
→ Cleanup automatico?
```

**Status:** ⚠️ **ORPHAN SUBMISSIONS?**

---

### **9.2 Plugin Deactivation**

**Check:**
```php
register_deactivation_hook()
```

**Issue:**
- Cosa succede ai form salvati?
- Submissions rimangono?
- Settings rimangono?
- Cleanup?

**Status:** ⚠️ **DEACTIVATION CLEANUP DA VERIFICARE**

---

### **9.3 Plugin Uninstall**

**Check:**
```php
register_uninstall_hook()
```

**GDPR:**
- User disinstalla plugin
- Dati sensibili rimangono in DB?
- Forms, submissions, meta devono essere cancellati

**Status:** ⚠️ **UNINSTALL CLEANUP DA VERIFICARE**

---

## 🔍 CATEGORIA 10: TRACKING CONSISTENCY

### **10.1 GTM vs GA4 Events**

**Check:**
```javascript
// Tracking.php
pushToDataLayer() // GTM
sendToGA4()       // GA4
```

**Potenziale Issue:**
- Eventi inviati a entrambi?
- Duplicazione eventi in GA4 (se GTM → GA4)?
- User configura GTM che già invia a GA4
- + plugin invia direttamente a GA4
- = Eventi doppi

**Status:** ⚠️ **EVENT DUPLICATION RISK**

---

### **10.2 Meta Pixel vs CAPI**

**Check:**
```javascript
// Pixel client-side
fbq('track', 'Lead');

// CAPI server-side
send_conversion_event('Lead');
```

**Potenziale Issue:**
- Stesso evento inviato 2 volte (client + server)
- Meta deduplication ID usato?
- Eventi contati doppi?

**Status:** ⚠️ **DEDUPLICATION DA VERIFICARE**

---

### **10.3 Tracking Form View Multiple**

**Scenario:**
```
Pagina con form caricata
→ fpFormView evento
→ User naviga via
→ Torna indietro (browser back)
→ fpFormView di nuovo?
→ View contato doppio?
```

**Status:** ⚠️ **DUPLICATE VIEW TRACKING**

---

## 📊 RIEPILOGO NUOVI BUG TROVATI

### **🔴 CRITICI (0)**
Nessuno!

### **🟡 MODERATI (12)**

1. Honeypot validation
2. Rate limiting assente
3. Email queue non async
4. Form deletion orphans
5. Uninstall cleanup
6. Templates non aggiornati
7. Import validation
8. Meta CAPI deduplication
9. GTM/GA4 event duplication
10. reCAPTCHA version switch
11. Hook error isolation
12. Filter chain fragility

### **🟢 MINORI (10)**

13. Conditional logic su privacy field
14. Conditional su reCAPTCHA
15. New fields in conditional
16. Duplicate form email warning
17. Cross-site import issues
18. Hook execution order
19. Form reset hidden fields
20. File lost on validation fail
21. wp_mail exception handling
22. Tracking view duplication

---

## 🎯 PRIORITÀ ANALISI

**P0:** Verificare esistenti (già implementati?)
**P1:** Fix critici se trovati
**P2:** Documentare edge cases
**P3:** Suggerimenti futuri

---



**Data:** 5 Novembre 2025  
**Focus:** Feature Integration, Data Flow, System Coherence  
**Scope:** Conditional Logic, Templates, Hooks, Data Lifecycle

---

## 🎯 AREE NON ANCORA ANALIZZATE

**Sessioni precedenti:**
- #3: Security, Performance, Basic validation
- #4: Integration, AJAX, A11Y, Mobile  
- #5: Admin validation, Code verification

**Sessione #6 analizza:**
- Conditional Logic con nuovi campi
- Template Library con nuovi settings
- Form duplication/export/import
- Hooks/Filters implementation
- Data lifecycle completo
- Anti-spam honeypot
- Form reset behavior
- reCAPTCHA version switching

---

## 🔍 CATEGORIA 1: CONDITIONAL LOGIC

### **1.1 Conditional su Privacy/Marketing Checkbox**

**Scenario:**
```
Rule: "Show campo B if privacy-checkbox is checked"

Privacy checkbox è SEMPRE checked (forced required GDPR)
→ Condition sempre true?
→ Campo B sempre visibile?
→ Logic inutile?
```

**Status:** ⚠️ **LOGIC QUESTIONABLE**

---

### **1.2 Conditional su reCAPTCHA**

**Scenario:**
```
Rule: "Show campo X if recaptcha is valid"

reCAPTCHA validation è server-side!
→ Frontend non sa se valido
→ Conditional logic non può funzionare
```

**Status:** ⚠️ **IMPOSSIBLE CONDITION**

---

### **1.3 Rules con Nuovi Field Types**

**Check:**
```javascript
// conditional-logic.js - supporta privacy-checkbox?
```

**Potenziale Issue:**
- Conditional logic creato prima di privacy/marketing fields
- Supporta nuovi tipi?
- field.type === 'privacy-checkbox' gestito?

**Status:** ⚠️ **NEW FIELDS SUPPORT DA VERIFICARE**

---

## 🔍 CATEGORIA 2: TEMPLATE LIBRARY

### **2.1 Templates con Nuovi Settings**

**Check:**
```php
// In Library.php - template defaults
'submit_button_text' => 'Invia'
```

**Issue:**
- Templates vecchi non hanno nuovi settings (color, size, style)
- Form importato da template → mancano settings
- Frontend usa ?? defaults → OK
- Ma UX: utente si aspetta settings dal template

**Status:** ⚠️ **TEMPLATES NON AGGIORNATI**

---

### **2.2 Template Import - Settings Validation**

**Scenario:**
```
User importa template da JSON esterno
→ JSON contiene submit_button_color: "red"
→ Non HEX valido
→ Importato senza validazione?
```

**Status:** ⚠️ **IMPORT VALIDATION DA VERIFICARE**

---

## 🔍 CATEGORIA 3: FORM DUPLICATION

### **3.1 Duplicate Form - Settings Copy**

**Check:**
```php
public function ajax_duplicate_form()
```

**Potenziale Issue:**
- Duplica form con tutti i settings ✅
- Ma notifica email usa stessa email admin?
- User duplica form, dimentica di cambiare notification_email
- Riceve notifiche da entrambi i form

**Status:** ⚠️ **UX WARNING NEEDED**

---

### **3.2 Export/Import Settings**

**Scenario:**
```
Export form A con:
- notification_email: admin@siteA.com
- brevo_list_id: 123 (specifico site A)

Import in site B:
- Email admin@siteA.com non esiste
- Brevo list 123 non esiste in account site B
```

**Status:** ⚠️ **CROSS-SITE IMPORT ISSUES**

---

## 🔍 CATEGORIA 4: HOOKS & FILTERS

### **4.1 Hook Execution Order**

**Check:**
```php
do_action( 'fp_forms_after_save_submission', ... );
```

**Listeners:**
- Brevo sync (priority 10)
- Meta CAPI (priority 10)
- Custom hooks (unknown priority)

**Potenziale Issue:**
- Se 2 listener con stessa priority
- Ordine esecuzione non garantito
- Brevo sync prima o dopo Meta?
- Matters?

**Status:** ⚠️ **PRIORITY NON SPECIFICATA**

---

### **4.2 Filter Modification Chain**

**Codice:**
```php
$message = \FPForms\Core\Hooks::filter_email_message( $message, $form_id, $data );
```

**Potenziale Issue:**
- Filter 1: Modifica $message
- Filter 2: Modifica $message (assume format originale)
- Filter 2 crash se format cambiato da Filter 1

**Status:** ⚠️ **FILTER CHAIN FRAGILITY**

---

### **4.3 Hook Error Propagation**

**Check:**
```php
do_action( 'fp_forms_before_send_notification', ... );
// Se listener crasha qui?
// Email comunque inviata dopo?
```

**Status:** ⚠️ **NO ERROR ISOLATION**

---

## 🔍 CATEGORIA 5: DATA FLOW

### **5.1 Sanitized Data vs Raw Data**

**Flow:**
```php
1. $form_data = $_POST (raw)
2. $sanitized_data = sanitize_data( $form_data )
3. Validation usa $sanitized_data ✅
4. Save usa $sanitized_data ✅
5. Email usa $sanitized_data ✅
6. Brevo usa $sanitized_data ✅
7. Meta usa $sanitized_data ✅
```

**Check se in qualche punto usiamo raw invece di sanitized:**

**Status:** ⚠️ **DA VERIFICARE CONSISTENZA**

---

### **5.2 Form Reset - Hidden Fields**

**JavaScript:**
```javascript
$form[0].reset();
```

**Potenziale Issue:**
- Reset cancella anche hidden fields?
- form_id, nonce, action rimangono?
- reCAPTCHA token cancellato?

**Status:** ⚠️ **HIDDEN FIELDS RESET?**

---

### **5.3 File Upload - Data Persistence**

**Scenario:**
```
User upload file → validation fail su altro campo
→ Form mostra errore
→ File ancora uploadato?
→ User deve re-uploadare?
```

**Status:** ⚠️ **FILE LOST ON VALIDATION FAIL**

---

## 🔍 CATEGORIA 6: ANTI-SPAM

### **6.1 Honeypot Field**

**Check:**
```php
$anti_spam = new \FPForms\Security\AntiSpam();
echo $anti_spam->get_honeypot_field( $form['id'] );
```

**Verification:**
- Honeypot validato su submission?
- Se bot compila honeypot?
- Submission bloccata?

**Status:** ⚠️ **HONEYPOT VALIDATION DA VERIFICARE**

---

### **6.2 Rate Limiting**

**Potenziale Issue:**
```
Bot spam: 1000 submissions/minuto
→ Nessun rate limiting?
→ DB flood?
→ Email flood?
```

**Status:** ⚠️ **NO RATE LIMITING**

---

### **6.3 reCAPTCHA Bypass**

**Scenario:**
```
Form con reCAPTCHA
→ User rimuove campo reCAPTCHA dal DOM (DevTools)
→ Submit senza token
→ Validation fallisce? ✅
→ Ma logged come tentativo malevolo?
```

**Status:** ⚠️ **NO SECURITY LOGGING**

---

## 🔍 CATEGORIA 7: EMAIL DELIVERY

### **7.1 wp_mail() Failure Silent**

**Check:**
```php
try {
    $success = wp_mail( $to, $subject, $message, $headers );
    Logger::log_email( $to, $subject, $success );
} catch ( \Exception $e ) {
    Logger::error( 'Email failed' );
}
```

**Issue:**
- wp_mail() ritorna false (not exception)
- catch non viene mai eseguito
- Only log_email() registra

**Status:** ⚠️ **EXCEPTION CATCH INUTILE**

---

### **7.2 Email Queue**

**Scenario:**
```
Form submission con:
- Email webmaster
- Email cliente  
- Email staff (3 persone)
= 5 email totali

Inviate sequenzialmente:
→ 5 × wp_mail() calls
→ Lento?
→ Async queue?
```

**Status:** ⚠️ **NO ASYNC EMAIL QUEUE**

---

### **7.3 Email Delivery Retry**

**Scenario:**
```
wp_mail() fail (SMTP down)
→ Email persa
→ Nessun retry
→ User non sa
```

**Status:** ⚠️ **NO RETRY MECHANISM**

---

## 🔍 CATEGORIA 8: INTEGRATIONS EDGE CASES

### **8.1 Brevo - Email Campo Non Email Type**

**Scenario:**
```
Form senza campo type="email"
→ Solo campo "text" con nome "contact"
→ extract_email() cerca 'email' field
→ Non trova
→ Sync skip? ✅ (già gestito con check)
```

**Status:** ✅ **GIÀ GESTITO**

---

### **8.2 Meta CAPI - User Data Minimal**

**Scenario:**
```
Form solo con "messaggio" (textarea)
→ Nessun email, nome, phone
→ prepare_user_data() ritorna array vuoto
→ Meta API call con user_data vuoto
→ Accettato da Meta?
```

**Status:** ⚠️ **MINIMAL DATA DA VERIFICARE**

---

### **8.3 reCAPTCHA v2 ↔ v3 Switch**

**Scenario:**
```
Form creato con reCAPTCHA v2
→ Admin cambia settings globali a v3
→ Form esistente ha già campo reCAPTCHA
→ Quale versione viene usata?
→ Mismatch token?
```

**Status:** ⚠️ **VERSION SWITCH CONSISTENCY**

---

## 🔍 CATEGORIA 9: FORM LIFECYCLE

### **9.1 Form Deletion - Submissions Orphan**

**Scenario:**
```
Form cancellato
→ Submissions associate rimangono?
→ Orphan posts in DB?
→ Cleanup automatico?
```

**Status:** ⚠️ **ORPHAN SUBMISSIONS?**

---

### **9.2 Plugin Deactivation**

**Check:**
```php
register_deactivation_hook()
```

**Issue:**
- Cosa succede ai form salvati?
- Submissions rimangono?
- Settings rimangono?
- Cleanup?

**Status:** ⚠️ **DEACTIVATION CLEANUP DA VERIFICARE**

---

### **9.3 Plugin Uninstall**

**Check:**
```php
register_uninstall_hook()
```

**GDPR:**
- User disinstalla plugin
- Dati sensibili rimangono in DB?
- Forms, submissions, meta devono essere cancellati

**Status:** ⚠️ **UNINSTALL CLEANUP DA VERIFICARE**

---

## 🔍 CATEGORIA 10: TRACKING CONSISTENCY

### **10.1 GTM vs GA4 Events**

**Check:**
```javascript
// Tracking.php
pushToDataLayer() // GTM
sendToGA4()       // GA4
```

**Potenziale Issue:**
- Eventi inviati a entrambi?
- Duplicazione eventi in GA4 (se GTM → GA4)?
- User configura GTM che già invia a GA4
- + plugin invia direttamente a GA4
- = Eventi doppi

**Status:** ⚠️ **EVENT DUPLICATION RISK**

---

### **10.2 Meta Pixel vs CAPI**

**Check:**
```javascript
// Pixel client-side
fbq('track', 'Lead');

// CAPI server-side
send_conversion_event('Lead');
```

**Potenziale Issue:**
- Stesso evento inviato 2 volte (client + server)
- Meta deduplication ID usato?
- Eventi contati doppi?

**Status:** ⚠️ **DEDUPLICATION DA VERIFICARE**

---

### **10.3 Tracking Form View Multiple**

**Scenario:**
```
Pagina con form caricata
→ fpFormView evento
→ User naviga via
→ Torna indietro (browser back)
→ fpFormView di nuovo?
→ View contato doppio?
```

**Status:** ⚠️ **DUPLICATE VIEW TRACKING**

---

## 📊 RIEPILOGO NUOVI BUG TROVATI

### **🔴 CRITICI (0)**
Nessuno!

### **🟡 MODERATI (12)**

1. Honeypot validation
2. Rate limiting assente
3. Email queue non async
4. Form deletion orphans
5. Uninstall cleanup
6. Templates non aggiornati
7. Import validation
8. Meta CAPI deduplication
9. GTM/GA4 event duplication
10. reCAPTCHA version switch
11. Hook error isolation
12. Filter chain fragility

### **🟢 MINORI (10)**

13. Conditional logic su privacy field
14. Conditional su reCAPTCHA
15. New fields in conditional
16. Duplicate form email warning
17. Cross-site import issues
18. Hook execution order
19. Form reset hidden fields
20. File lost on validation fail
21. wp_mail exception handling
22. Tracking view duplication

---

## 🎯 PRIORITÀ ANALISI

**P0:** Verificare esistenti (già implementati?)
**P1:** Fix critici se trovati
**P2:** Documentare edge cases
**P3:** Suggerimenti futuri

---










