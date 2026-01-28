# 📧 Sistema di Notifiche Email - FP Forms

## 📋 PANORAMICA

FP Forms invia **3 tipi di email** ad ogni submission:

| # | Tipo Email | Destinatario | Quando | Configurazione |
|---|------------|--------------|--------|----------------|
| 1 | **Notifica Admin** | Webmaster | Sempre | Obbligatoria |
| 2 | **Conferma Cliente** | Utente che compila | Se abilitata | Opzionale |
| 3 | **Notifiche Staff** | Team/Staff multiplo | Se abilitata | Opzionale |

---

## 🔧 CONFIGURAZIONE

### **1. Email Webmaster (Admin)**
**Location:** Form Builder → Impostazioni Form → Notifiche Email

```
Email Destinatario: admin@example.com
Oggetto Email: Nuova submission da {form_title}
```

**Cosa include:**
- ✅ Tutti i campi compilati
- ✅ Data/ora submission
- ✅ IP utente
- ✅ Utente WordPress (se loggato)
- ✅ Reply-To automatico (email cliente)

**Template Email:**
```
Hai ricevuto una nuova submission dal form "Contact Form"

--------------------------------------------------

Nome: Mario Rossi
Email: mario.rossi@example.com
Telefono: +39 333 1234567
Messaggio: Vorrei maggiori informazioni...

--------------------------------------------------

Informazioni aggiuntive:
Data: 2025-11-05 23:45:00
IP: 192.168.1.1
```

---

### **2. Email Conferma Cliente**
**Location:** Form Builder → Impostazioni Form → Email di Conferma

```
☑️ Invia email di conferma all'utente
Oggetto: Conferma ricezione messaggio
Messaggio: Grazie per averci contattato!
```

**Cosa include:**
- ✅ Messaggio personalizzato
- ✅ Tag dinamici ({nome}, {form_title}, etc.)
- ✅ From personalizzato (Settings → Nome Mittente)

**Esempio Email:**
```
Da: Your Company <noreply@example.com>
A: mario.rossi@example.com

Oggetto: Conferma ricezione messaggio

Grazie per averci contattato!

Abbiamo ricevuto il tuo messaggio e ti risponderemo
al più presto.

Cordiali saluti,
Your Company Team
```

**Come funziona:**
1. Sistema cerca automaticamente campo `email` nel form
2. Estrae l'indirizzo
3. Invia conferma se checkbox attivata nelle impostazioni

---

### **3. Notifiche Staff/Team**
**Location:** Form Builder → Impostazioni Form → Notifiche Staff

```
☑️ Invia notifica a membri dello staff/team

Email Staff (una per riga):
sales@example.com
support@example.com
marketing@example.com

Oggetto: [STAFF] Nuova submission: {form_title}
Messaggio: [Template personalizzato o default]
```

**Formati supportati:**
```
# Una per riga
mario.rossi@example.com
giulia.bianchi@example.com

# Separati da virgola
sales@example.com, support@example.com, info@example.com

# Separati da punto e virgola
team1@example.com; team2@example.com; team3@example.com
```

**Cosa include:**
- ✅ Stessi dati della notifica admin
- ✅ Oggetto personalizzabile con tag
- ✅ Messaggio personalizzabile (o template default)
- ✅ Reply-To automatico al cliente
- ✅ Invio separato a ogni email (no CC/BCC)

---

## 🔄 FLUSSO COMPLETO AD OGNI SUBMISSION

```
[Utente compila form] 
        ↓
[Click Submit]
        ↓
[Validazione]
        ↓
[Salva in Database]
        ↓
┌───────────────────────────────────────┐
│ INVIO EMAIL (in parallelo)            │
├───────────────────────────────────────┤
│ 1. → admin@example.com (Webmaster)    │
│ 2. → mario.rossi@... (Cliente)        │
│ 3. → sales@... (Staff #1)             │
│ 4. → support@... (Staff #2)           │
│ 5. → team@... (Staff #3)              │
└───────────────────────────────────────┘
        ↓
[Brevo Sync - se attivo]
        ↓
[GTM/GA4 Tracking - se attivo]
        ↓
[Messaggio Successo]
```

---

## 📊 TAG DINAMICI DISPONIBILI

Puoi usare questi tag in oggetto e messaggio email:

**Tag Campi Form:**
- `{nome}` - Valore campo "nome"
- `{email}` - Valore campo "email"
- `{telefono}` - Valore campo "telefono"
- `{messaggio}` - Valore campo "messaggio"
- ... qualsiasi nome campo del form

**Tag Generali:**
- `{form_title}` - Titolo del form
- `{site_name}` - Nome del sito
- `{site_url}` - URL del sito
- `{date}` - Data corrente
- `{time}` - Ora corrente

**Esempio:**
```
Oggetto: Nuova richiesta da {nome} - {form_title}
         ↓
         Nuova richiesta da Mario Rossi - Contact Form

Messaggio: Ciao {nome}, grazie per il tuo interesse!
           ↓
           Ciao Mario, grazie per il tuo interesse!
```

---

## ⚙️ HEADERS EMAIL

**From:**
```
From: Your Company <noreply@example.com>
```
Configurabile in: **FP Forms → Impostazioni** → Email Settings

**Reply-To (automatico):**
```
Reply-To: mario.rossi@example.com
```
Estrae automaticamente l'email dal form per permettere risposta diretta

**Content-Type:**
```
Content-Type: text/plain; charset=UTF-8
```

---

## 🎯 CASI D'USO

### **Contact Form Standard**
```
✅ Admin: admin@company.com (riceve tutto)
✅ Cliente: conferma automatica
❌ Staff: non necessario
```

### **Sales Lead Form**
```
✅ Admin: admin@company.com (backup)
✅ Cliente: conferma ricezione richiesta
✅ Staff: 
   - sales@company.com
   - sales.manager@company.com
```

### **Support Ticket**
```
✅ Admin: admin@company.com
✅ Cliente: conferma apertura ticket
✅ Staff:
   - support@company.com
   - tecnico1@company.com
   - tecnico2@company.com
```

### **Newsletter Signup**
```
✅ Admin: admin@company.com (log)
✅ Cliente: welcome email + double opt-in
❌ Staff: non necessario (usa Brevo automation)
```

---

## 🔒 SICUREZZA & LOGGING

**Error Handling:**
- ✅ Try/catch su ogni invio
- ✅ Logging errori dettagliato
- ✅ Submission NON bloccata se email fallisce

**Logging:**
```
[INFO] Email sent | to: admin@..., subject: Nuova submission..., success: true
[INFO] Confirmation sent | to: mario@..., success: true
[INFO] Staff notifications sent | form_id: 123, count: 3
[ERROR] Staff notification failed | to: invalid-email, error: ...
```

**Anti-Spam:**
- ✅ Nonce verification
- ✅ reCAPTCHA integration
- ✅ Honeypot fields
- ✅ Rate limiting

---

## 🎨 PERSONALIZZAZIONE AVANZATA

### **Via Hooks (per sviluppatori):**

```php
// Modifica recipients notifica admin
add_filter('fp_forms_notification_recipients', function($to, $form_id, $data) {
    if ($form_id === 5) {
        return 'sales@example.com';
    }
    return $to;
}, 10, 3);

// Modifica oggetto email
add_filter('fp_forms_email_subject', function($subject, $form_id, $data) {
    return '[PRIORITY] ' . $subject;
}, 10, 3);

// Modifica messaggio email
add_filter('fp_forms_email_message', function($message, $form_id, $data) {
    return $message . "\n\nCustom Footer";
}, 10, 3);

// Azione prima dell'invio
add_action('fp_forms_before_send_notification', function($form_id, $data, $to) {
    // Custom logic (es: notifica Slack, Discord, etc.)
}, 10, 3);
```

---

## ✅ CHECKLIST CONFIGURAZIONE

**Per ogni form:**
- [ ] **Admin Email** configurata
- [ ] **Oggetto** personalizzato
- [ ] **Conferma Cliente** abilitata (se necessaria)
- [ ] **Email Staff** aggiunte (se necessarie)
- [ ] **From Name/Email** configurati in Settings globali
- [ ] Test invio email (invia form di test)
- [ ] Verifica spam folder

**Test:**
1. Compila form in frontend
2. Check inbox admin (arrivo notifica?)
3. Check inbox cliente (arrivo conferma?)
4. Check inbox staff (tutte ricevute?)
5. Check log errori (se problemi)

---

## 🚀 PROSSIMI STEP CONSIGLIATI

**Template HTML:**
- [ ] Supporto HTML email (oltre a plain text)
- [ ] Template grafici custom
- [ ] Email builder drag & drop

**Automazioni:**
- [ ] Notifica condizionale (se campo X = Y)
- [ ] Delay send (es: 5 min dopo submission)
- [ ] Email drip campaign

**Integrazioni:**
- [✅] Brevo/Sendinblue sync (già implementato!)
- [ ] Mailchimp integration
- [ ] ActiveCampaign integration

---

**Status:** ✅ Sistema email completo e funzionante!



## 📋 PANORAMICA

FP Forms invia **3 tipi di email** ad ogni submission:

| # | Tipo Email | Destinatario | Quando | Configurazione |
|---|------------|--------------|--------|----------------|
| 1 | **Notifica Admin** | Webmaster | Sempre | Obbligatoria |
| 2 | **Conferma Cliente** | Utente che compila | Se abilitata | Opzionale |
| 3 | **Notifiche Staff** | Team/Staff multiplo | Se abilitata | Opzionale |

---

## 🔧 CONFIGURAZIONE

### **1. Email Webmaster (Admin)**
**Location:** Form Builder → Impostazioni Form → Notifiche Email

```
Email Destinatario: admin@example.com
Oggetto Email: Nuova submission da {form_title}
```

**Cosa include:**
- ✅ Tutti i campi compilati
- ✅ Data/ora submission
- ✅ IP utente
- ✅ Utente WordPress (se loggato)
- ✅ Reply-To automatico (email cliente)

**Template Email:**
```
Hai ricevuto una nuova submission dal form "Contact Form"

--------------------------------------------------

Nome: Mario Rossi
Email: mario.rossi@example.com
Telefono: +39 333 1234567
Messaggio: Vorrei maggiori informazioni...

--------------------------------------------------

Informazioni aggiuntive:
Data: 2025-11-05 23:45:00
IP: 192.168.1.1
```

---

### **2. Email Conferma Cliente**
**Location:** Form Builder → Impostazioni Form → Email di Conferma

```
☑️ Invia email di conferma all'utente
Oggetto: Conferma ricezione messaggio
Messaggio: Grazie per averci contattato!
```

**Cosa include:**
- ✅ Messaggio personalizzato
- ✅ Tag dinamici ({nome}, {form_title}, etc.)
- ✅ From personalizzato (Settings → Nome Mittente)

**Esempio Email:**
```
Da: Your Company <noreply@example.com>
A: mario.rossi@example.com

Oggetto: Conferma ricezione messaggio

Grazie per averci contattato!

Abbiamo ricevuto il tuo messaggio e ti risponderemo
al più presto.

Cordiali saluti,
Your Company Team
```

**Come funziona:**
1. Sistema cerca automaticamente campo `email` nel form
2. Estrae l'indirizzo
3. Invia conferma se checkbox attivata nelle impostazioni

---

### **3. Notifiche Staff/Team**
**Location:** Form Builder → Impostazioni Form → Notifiche Staff

```
☑️ Invia notifica a membri dello staff/team

Email Staff (una per riga):
sales@example.com
support@example.com
marketing@example.com

Oggetto: [STAFF] Nuova submission: {form_title}
Messaggio: [Template personalizzato o default]
```

**Formati supportati:**
```
# Una per riga
mario.rossi@example.com
giulia.bianchi@example.com

# Separati da virgola
sales@example.com, support@example.com, info@example.com

# Separati da punto e virgola
team1@example.com; team2@example.com; team3@example.com
```

**Cosa include:**
- ✅ Stessi dati della notifica admin
- ✅ Oggetto personalizzabile con tag
- ✅ Messaggio personalizzabile (o template default)
- ✅ Reply-To automatico al cliente
- ✅ Invio separato a ogni email (no CC/BCC)

---

## 🔄 FLUSSO COMPLETO AD OGNI SUBMISSION

```
[Utente compila form] 
        ↓
[Click Submit]
        ↓
[Validazione]
        ↓
[Salva in Database]
        ↓
┌───────────────────────────────────────┐
│ INVIO EMAIL (in parallelo)            │
├───────────────────────────────────────┤
│ 1. → admin@example.com (Webmaster)    │
│ 2. → mario.rossi@... (Cliente)        │
│ 3. → sales@... (Staff #1)             │
│ 4. → support@... (Staff #2)           │
│ 5. → team@... (Staff #3)              │
└───────────────────────────────────────┘
        ↓
[Brevo Sync - se attivo]
        ↓
[GTM/GA4 Tracking - se attivo]
        ↓
[Messaggio Successo]
```

---

## 📊 TAG DINAMICI DISPONIBILI

Puoi usare questi tag in oggetto e messaggio email:

**Tag Campi Form:**
- `{nome}` - Valore campo "nome"
- `{email}` - Valore campo "email"
- `{telefono}` - Valore campo "telefono"
- `{messaggio}` - Valore campo "messaggio"
- ... qualsiasi nome campo del form

**Tag Generali:**
- `{form_title}` - Titolo del form
- `{site_name}` - Nome del sito
- `{site_url}` - URL del sito
- `{date}` - Data corrente
- `{time}` - Ora corrente

**Esempio:**
```
Oggetto: Nuova richiesta da {nome} - {form_title}
         ↓
         Nuova richiesta da Mario Rossi - Contact Form

Messaggio: Ciao {nome}, grazie per il tuo interesse!
           ↓
           Ciao Mario, grazie per il tuo interesse!
```

---

## ⚙️ HEADERS EMAIL

**From:**
```
From: Your Company <noreply@example.com>
```
Configurabile in: **FP Forms → Impostazioni** → Email Settings

**Reply-To (automatico):**
```
Reply-To: mario.rossi@example.com
```
Estrae automaticamente l'email dal form per permettere risposta diretta

**Content-Type:**
```
Content-Type: text/plain; charset=UTF-8
```

---

## 🎯 CASI D'USO

### **Contact Form Standard**
```
✅ Admin: admin@company.com (riceve tutto)
✅ Cliente: conferma automatica
❌ Staff: non necessario
```

### **Sales Lead Form**
```
✅ Admin: admin@company.com (backup)
✅ Cliente: conferma ricezione richiesta
✅ Staff: 
   - sales@company.com
   - sales.manager@company.com
```

### **Support Ticket**
```
✅ Admin: admin@company.com
✅ Cliente: conferma apertura ticket
✅ Staff:
   - support@company.com
   - tecnico1@company.com
   - tecnico2@company.com
```

### **Newsletter Signup**
```
✅ Admin: admin@company.com (log)
✅ Cliente: welcome email + double opt-in
❌ Staff: non necessario (usa Brevo automation)
```

---

## 🔒 SICUREZZA & LOGGING

**Error Handling:**
- ✅ Try/catch su ogni invio
- ✅ Logging errori dettagliato
- ✅ Submission NON bloccata se email fallisce

**Logging:**
```
[INFO] Email sent | to: admin@..., subject: Nuova submission..., success: true
[INFO] Confirmation sent | to: mario@..., success: true
[INFO] Staff notifications sent | form_id: 123, count: 3
[ERROR] Staff notification failed | to: invalid-email, error: ...
```

**Anti-Spam:**
- ✅ Nonce verification
- ✅ reCAPTCHA integration
- ✅ Honeypot fields
- ✅ Rate limiting

---

## 🎨 PERSONALIZZAZIONE AVANZATA

### **Via Hooks (per sviluppatori):**

```php
// Modifica recipients notifica admin
add_filter('fp_forms_notification_recipients', function($to, $form_id, $data) {
    if ($form_id === 5) {
        return 'sales@example.com';
    }
    return $to;
}, 10, 3);

// Modifica oggetto email
add_filter('fp_forms_email_subject', function($subject, $form_id, $data) {
    return '[PRIORITY] ' . $subject;
}, 10, 3);

// Modifica messaggio email
add_filter('fp_forms_email_message', function($message, $form_id, $data) {
    return $message . "\n\nCustom Footer";
}, 10, 3);

// Azione prima dell'invio
add_action('fp_forms_before_send_notification', function($form_id, $data, $to) {
    // Custom logic (es: notifica Slack, Discord, etc.)
}, 10, 3);
```

---

## ✅ CHECKLIST CONFIGURAZIONE

**Per ogni form:**
- [ ] **Admin Email** configurata
- [ ] **Oggetto** personalizzato
- [ ] **Conferma Cliente** abilitata (se necessaria)
- [ ] **Email Staff** aggiunte (se necessarie)
- [ ] **From Name/Email** configurati in Settings globali
- [ ] Test invio email (invia form di test)
- [ ] Verifica spam folder

**Test:**
1. Compila form in frontend
2. Check inbox admin (arrivo notifica?)
3. Check inbox cliente (arrivo conferma?)
4. Check inbox staff (tutte ricevute?)
5. Check log errori (se problemi)

---

## 🚀 PROSSIMI STEP CONSIGLIATI

**Template HTML:**
- [ ] Supporto HTML email (oltre a plain text)
- [ ] Template grafici custom
- [ ] Email builder drag & drop

**Automazioni:**
- [ ] Notifica condizionale (se campo X = Y)
- [ ] Delay send (es: 5 min dopo submission)
- [ ] Email drip campaign

**Integrazioni:**
- [✅] Brevo/Sendinblue sync (già implementato!)
- [ ] Mailchimp integration
- [ ] ActiveCampaign integration

---

**Status:** ✅ Sistema email completo e funzionante!



## 📋 PANORAMICA

FP Forms invia **3 tipi di email** ad ogni submission:

| # | Tipo Email | Destinatario | Quando | Configurazione |
|---|------------|--------------|--------|----------------|
| 1 | **Notifica Admin** | Webmaster | Sempre | Obbligatoria |
| 2 | **Conferma Cliente** | Utente che compila | Se abilitata | Opzionale |
| 3 | **Notifiche Staff** | Team/Staff multiplo | Se abilitata | Opzionale |

---

## 🔧 CONFIGURAZIONE

### **1. Email Webmaster (Admin)**
**Location:** Form Builder → Impostazioni Form → Notifiche Email

```
Email Destinatario: admin@example.com
Oggetto Email: Nuova submission da {form_title}
```

**Cosa include:**
- ✅ Tutti i campi compilati
- ✅ Data/ora submission
- ✅ IP utente
- ✅ Utente WordPress (se loggato)
- ✅ Reply-To automatico (email cliente)

**Template Email:**
```
Hai ricevuto una nuova submission dal form "Contact Form"

--------------------------------------------------

Nome: Mario Rossi
Email: mario.rossi@example.com
Telefono: +39 333 1234567
Messaggio: Vorrei maggiori informazioni...

--------------------------------------------------

Informazioni aggiuntive:
Data: 2025-11-05 23:45:00
IP: 192.168.1.1
```

---

### **2. Email Conferma Cliente**
**Location:** Form Builder → Impostazioni Form → Email di Conferma

```
☑️ Invia email di conferma all'utente
Oggetto: Conferma ricezione messaggio
Messaggio: Grazie per averci contattato!
```

**Cosa include:**
- ✅ Messaggio personalizzato
- ✅ Tag dinamici ({nome}, {form_title}, etc.)
- ✅ From personalizzato (Settings → Nome Mittente)

**Esempio Email:**
```
Da: Your Company <noreply@example.com>
A: mario.rossi@example.com

Oggetto: Conferma ricezione messaggio

Grazie per averci contattato!

Abbiamo ricevuto il tuo messaggio e ti risponderemo
al più presto.

Cordiali saluti,
Your Company Team
```

**Come funziona:**
1. Sistema cerca automaticamente campo `email` nel form
2. Estrae l'indirizzo
3. Invia conferma se checkbox attivata nelle impostazioni

---

### **3. Notifiche Staff/Team**
**Location:** Form Builder → Impostazioni Form → Notifiche Staff

```
☑️ Invia notifica a membri dello staff/team

Email Staff (una per riga):
sales@example.com
support@example.com
marketing@example.com

Oggetto: [STAFF] Nuova submission: {form_title}
Messaggio: [Template personalizzato o default]
```

**Formati supportati:**
```
# Una per riga
mario.rossi@example.com
giulia.bianchi@example.com

# Separati da virgola
sales@example.com, support@example.com, info@example.com

# Separati da punto e virgola
team1@example.com; team2@example.com; team3@example.com
```

**Cosa include:**
- ✅ Stessi dati della notifica admin
- ✅ Oggetto personalizzabile con tag
- ✅ Messaggio personalizzabile (o template default)
- ✅ Reply-To automatico al cliente
- ✅ Invio separato a ogni email (no CC/BCC)

---

## 🔄 FLUSSO COMPLETO AD OGNI SUBMISSION

```
[Utente compila form] 
        ↓
[Click Submit]
        ↓
[Validazione]
        ↓
[Salva in Database]
        ↓
┌───────────────────────────────────────┐
│ INVIO EMAIL (in parallelo)            │
├───────────────────────────────────────┤
│ 1. → admin@example.com (Webmaster)    │
│ 2. → mario.rossi@... (Cliente)        │
│ 3. → sales@... (Staff #1)             │
│ 4. → support@... (Staff #2)           │
│ 5. → team@... (Staff #3)              │
└───────────────────────────────────────┘
        ↓
[Brevo Sync - se attivo]
        ↓
[GTM/GA4 Tracking - se attivo]
        ↓
[Messaggio Successo]
```

---

## 📊 TAG DINAMICI DISPONIBILI

Puoi usare questi tag in oggetto e messaggio email:

**Tag Campi Form:**
- `{nome}` - Valore campo "nome"
- `{email}` - Valore campo "email"
- `{telefono}` - Valore campo "telefono"
- `{messaggio}` - Valore campo "messaggio"
- ... qualsiasi nome campo del form

**Tag Generali:**
- `{form_title}` - Titolo del form
- `{site_name}` - Nome del sito
- `{site_url}` - URL del sito
- `{date}` - Data corrente
- `{time}` - Ora corrente

**Esempio:**
```
Oggetto: Nuova richiesta da {nome} - {form_title}
         ↓
         Nuova richiesta da Mario Rossi - Contact Form

Messaggio: Ciao {nome}, grazie per il tuo interesse!
           ↓
           Ciao Mario, grazie per il tuo interesse!
```

---

## ⚙️ HEADERS EMAIL

**From:**
```
From: Your Company <noreply@example.com>
```
Configurabile in: **FP Forms → Impostazioni** → Email Settings

**Reply-To (automatico):**
```
Reply-To: mario.rossi@example.com
```
Estrae automaticamente l'email dal form per permettere risposta diretta

**Content-Type:**
```
Content-Type: text/plain; charset=UTF-8
```

---

## 🎯 CASI D'USO

### **Contact Form Standard**
```
✅ Admin: admin@company.com (riceve tutto)
✅ Cliente: conferma automatica
❌ Staff: non necessario
```

### **Sales Lead Form**
```
✅ Admin: admin@company.com (backup)
✅ Cliente: conferma ricezione richiesta
✅ Staff: 
   - sales@company.com
   - sales.manager@company.com
```

### **Support Ticket**
```
✅ Admin: admin@company.com
✅ Cliente: conferma apertura ticket
✅ Staff:
   - support@company.com
   - tecnico1@company.com
   - tecnico2@company.com
```

### **Newsletter Signup**
```
✅ Admin: admin@company.com (log)
✅ Cliente: welcome email + double opt-in
❌ Staff: non necessario (usa Brevo automation)
```

---

## 🔒 SICUREZZA & LOGGING

**Error Handling:**
- ✅ Try/catch su ogni invio
- ✅ Logging errori dettagliato
- ✅ Submission NON bloccata se email fallisce

**Logging:**
```
[INFO] Email sent | to: admin@..., subject: Nuova submission..., success: true
[INFO] Confirmation sent | to: mario@..., success: true
[INFO] Staff notifications sent | form_id: 123, count: 3
[ERROR] Staff notification failed | to: invalid-email, error: ...
```

**Anti-Spam:**
- ✅ Nonce verification
- ✅ reCAPTCHA integration
- ✅ Honeypot fields
- ✅ Rate limiting

---

## 🎨 PERSONALIZZAZIONE AVANZATA

### **Via Hooks (per sviluppatori):**

```php
// Modifica recipients notifica admin
add_filter('fp_forms_notification_recipients', function($to, $form_id, $data) {
    if ($form_id === 5) {
        return 'sales@example.com';
    }
    return $to;
}, 10, 3);

// Modifica oggetto email
add_filter('fp_forms_email_subject', function($subject, $form_id, $data) {
    return '[PRIORITY] ' . $subject;
}, 10, 3);

// Modifica messaggio email
add_filter('fp_forms_email_message', function($message, $form_id, $data) {
    return $message . "\n\nCustom Footer";
}, 10, 3);

// Azione prima dell'invio
add_action('fp_forms_before_send_notification', function($form_id, $data, $to) {
    // Custom logic (es: notifica Slack, Discord, etc.)
}, 10, 3);
```

---

## ✅ CHECKLIST CONFIGURAZIONE

**Per ogni form:**
- [ ] **Admin Email** configurata
- [ ] **Oggetto** personalizzato
- [ ] **Conferma Cliente** abilitata (se necessaria)
- [ ] **Email Staff** aggiunte (se necessarie)
- [ ] **From Name/Email** configurati in Settings globali
- [ ] Test invio email (invia form di test)
- [ ] Verifica spam folder

**Test:**
1. Compila form in frontend
2. Check inbox admin (arrivo notifica?)
3. Check inbox cliente (arrivo conferma?)
4. Check inbox staff (tutte ricevute?)
5. Check log errori (se problemi)

---

## 🚀 PROSSIMI STEP CONSIGLIATI

**Template HTML:**
- [ ] Supporto HTML email (oltre a plain text)
- [ ] Template grafici custom
- [ ] Email builder drag & drop

**Automazioni:**
- [ ] Notifica condizionale (se campo X = Y)
- [ ] Delay send (es: 5 min dopo submission)
- [ ] Email drip campaign

**Integrazioni:**
- [✅] Brevo/Sendinblue sync (già implementato!)
- [ ] Mailchimp integration
- [ ] ActiveCampaign integration

---

**Status:** ✅ Sistema email completo e funzionante!



## 📋 PANORAMICA

FP Forms invia **3 tipi di email** ad ogni submission:

| # | Tipo Email | Destinatario | Quando | Configurazione |
|---|------------|--------------|--------|----------------|
| 1 | **Notifica Admin** | Webmaster | Sempre | Obbligatoria |
| 2 | **Conferma Cliente** | Utente che compila | Se abilitata | Opzionale |
| 3 | **Notifiche Staff** | Team/Staff multiplo | Se abilitata | Opzionale |

---

## 🔧 CONFIGURAZIONE

### **1. Email Webmaster (Admin)**
**Location:** Form Builder → Impostazioni Form → Notifiche Email

```
Email Destinatario: admin@example.com
Oggetto Email: Nuova submission da {form_title}
```

**Cosa include:**
- ✅ Tutti i campi compilati
- ✅ Data/ora submission
- ✅ IP utente
- ✅ Utente WordPress (se loggato)
- ✅ Reply-To automatico (email cliente)

**Template Email:**
```
Hai ricevuto una nuova submission dal form "Contact Form"

--------------------------------------------------

Nome: Mario Rossi
Email: mario.rossi@example.com
Telefono: +39 333 1234567
Messaggio: Vorrei maggiori informazioni...

--------------------------------------------------

Informazioni aggiuntive:
Data: 2025-11-05 23:45:00
IP: 192.168.1.1
```

---

### **2. Email Conferma Cliente**
**Location:** Form Builder → Impostazioni Form → Email di Conferma

```
☑️ Invia email di conferma all'utente
Oggetto: Conferma ricezione messaggio
Messaggio: Grazie per averci contattato!
```

**Cosa include:**
- ✅ Messaggio personalizzato
- ✅ Tag dinamici ({nome}, {form_title}, etc.)
- ✅ From personalizzato (Settings → Nome Mittente)

**Esempio Email:**
```
Da: Your Company <noreply@example.com>
A: mario.rossi@example.com

Oggetto: Conferma ricezione messaggio

Grazie per averci contattato!

Abbiamo ricevuto il tuo messaggio e ti risponderemo
al più presto.

Cordiali saluti,
Your Company Team
```

**Come funziona:**
1. Sistema cerca automaticamente campo `email` nel form
2. Estrae l'indirizzo
3. Invia conferma se checkbox attivata nelle impostazioni

---

### **3. Notifiche Staff/Team**
**Location:** Form Builder → Impostazioni Form → Notifiche Staff

```
☑️ Invia notifica a membri dello staff/team

Email Staff (una per riga):
sales@example.com
support@example.com
marketing@example.com

Oggetto: [STAFF] Nuova submission: {form_title}
Messaggio: [Template personalizzato o default]
```

**Formati supportati:**
```
# Una per riga
mario.rossi@example.com
giulia.bianchi@example.com

# Separati da virgola
sales@example.com, support@example.com, info@example.com

# Separati da punto e virgola
team1@example.com; team2@example.com; team3@example.com
```

**Cosa include:**
- ✅ Stessi dati della notifica admin
- ✅ Oggetto personalizzabile con tag
- ✅ Messaggio personalizzabile (o template default)
- ✅ Reply-To automatico al cliente
- ✅ Invio separato a ogni email (no CC/BCC)

---

## 🔄 FLUSSO COMPLETO AD OGNI SUBMISSION

```
[Utente compila form] 
        ↓
[Click Submit]
        ↓
[Validazione]
        ↓
[Salva in Database]
        ↓
┌───────────────────────────────────────┐
│ INVIO EMAIL (in parallelo)            │
├───────────────────────────────────────┤
│ 1. → admin@example.com (Webmaster)    │
│ 2. → mario.rossi@... (Cliente)        │
│ 3. → sales@... (Staff #1)             │
│ 4. → support@... (Staff #2)           │
│ 5. → team@... (Staff #3)              │
└───────────────────────────────────────┘
        ↓
[Brevo Sync - se attivo]
        ↓
[GTM/GA4 Tracking - se attivo]
        ↓
[Messaggio Successo]
```

---

## 📊 TAG DINAMICI DISPONIBILI

Puoi usare questi tag in oggetto e messaggio email:

**Tag Campi Form:**
- `{nome}` - Valore campo "nome"
- `{email}` - Valore campo "email"
- `{telefono}` - Valore campo "telefono"
- `{messaggio}` - Valore campo "messaggio"
- ... qualsiasi nome campo del form

**Tag Generali:**
- `{form_title}` - Titolo del form
- `{site_name}` - Nome del sito
- `{site_url}` - URL del sito
- `{date}` - Data corrente
- `{time}` - Ora corrente

**Esempio:**
```
Oggetto: Nuova richiesta da {nome} - {form_title}
         ↓
         Nuova richiesta da Mario Rossi - Contact Form

Messaggio: Ciao {nome}, grazie per il tuo interesse!
           ↓
           Ciao Mario, grazie per il tuo interesse!
```

---

## ⚙️ HEADERS EMAIL

**From:**
```
From: Your Company <noreply@example.com>
```
Configurabile in: **FP Forms → Impostazioni** → Email Settings

**Reply-To (automatico):**
```
Reply-To: mario.rossi@example.com
```
Estrae automaticamente l'email dal form per permettere risposta diretta

**Content-Type:**
```
Content-Type: text/plain; charset=UTF-8
```

---

## 🎯 CASI D'USO

### **Contact Form Standard**
```
✅ Admin: admin@company.com (riceve tutto)
✅ Cliente: conferma automatica
❌ Staff: non necessario
```

### **Sales Lead Form**
```
✅ Admin: admin@company.com (backup)
✅ Cliente: conferma ricezione richiesta
✅ Staff: 
   - sales@company.com
   - sales.manager@company.com
```

### **Support Ticket**
```
✅ Admin: admin@company.com
✅ Cliente: conferma apertura ticket
✅ Staff:
   - support@company.com
   - tecnico1@company.com
   - tecnico2@company.com
```

### **Newsletter Signup**
```
✅ Admin: admin@company.com (log)
✅ Cliente: welcome email + double opt-in
❌ Staff: non necessario (usa Brevo automation)
```

---

## 🔒 SICUREZZA & LOGGING

**Error Handling:**
- ✅ Try/catch su ogni invio
- ✅ Logging errori dettagliato
- ✅ Submission NON bloccata se email fallisce

**Logging:**
```
[INFO] Email sent | to: admin@..., subject: Nuova submission..., success: true
[INFO] Confirmation sent | to: mario@..., success: true
[INFO] Staff notifications sent | form_id: 123, count: 3
[ERROR] Staff notification failed | to: invalid-email, error: ...
```

**Anti-Spam:**
- ✅ Nonce verification
- ✅ reCAPTCHA integration
- ✅ Honeypot fields
- ✅ Rate limiting

---

## 🎨 PERSONALIZZAZIONE AVANZATA

### **Via Hooks (per sviluppatori):**

```php
// Modifica recipients notifica admin
add_filter('fp_forms_notification_recipients', function($to, $form_id, $data) {
    if ($form_id === 5) {
        return 'sales@example.com';
    }
    return $to;
}, 10, 3);

// Modifica oggetto email
add_filter('fp_forms_email_subject', function($subject, $form_id, $data) {
    return '[PRIORITY] ' . $subject;
}, 10, 3);

// Modifica messaggio email
add_filter('fp_forms_email_message', function($message, $form_id, $data) {
    return $message . "\n\nCustom Footer";
}, 10, 3);

// Azione prima dell'invio
add_action('fp_forms_before_send_notification', function($form_id, $data, $to) {
    // Custom logic (es: notifica Slack, Discord, etc.)
}, 10, 3);
```

---

## ✅ CHECKLIST CONFIGURAZIONE

**Per ogni form:**
- [ ] **Admin Email** configurata
- [ ] **Oggetto** personalizzato
- [ ] **Conferma Cliente** abilitata (se necessaria)
- [ ] **Email Staff** aggiunte (se necessarie)
- [ ] **From Name/Email** configurati in Settings globali
- [ ] Test invio email (invia form di test)
- [ ] Verifica spam folder

**Test:**
1. Compila form in frontend
2. Check inbox admin (arrivo notifica?)
3. Check inbox cliente (arrivo conferma?)
4. Check inbox staff (tutte ricevute?)
5. Check log errori (se problemi)

---

## 🚀 PROSSIMI STEP CONSIGLIATI

**Template HTML:**
- [ ] Supporto HTML email (oltre a plain text)
- [ ] Template grafici custom
- [ ] Email builder drag & drop

**Automazioni:**
- [ ] Notifica condizionale (se campo X = Y)
- [ ] Delay send (es: 5 min dopo submission)
- [ ] Email drip campaign

**Integrazioni:**
- [✅] Brevo/Sendinblue sync (già implementato!)
- [ ] Mailchimp integration
- [ ] ActiveCampaign integration

---

**Status:** ✅ Sistema email completo e funzionante!































