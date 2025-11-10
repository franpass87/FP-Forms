# 📧 GUIDA PERSONALIZZAZIONE EMAIL - FP Forms

**Versione:** v1.2.2  
**Location:** Form Builder → Impostazioni Form (sidebar)  
**Status:** ✅ **TUTTE LE EMAIL 100% PERSONALIZZABILI!**

---

## 🎯 PANORAMICA

FP Forms invia **3 tipi di email** ad ogni submission, **tutte completamente personalizzabili**:

| Email | Destinatario | Personalizzabile |
|-------|--------------|------------------|
| 1️⃣ **Webmaster** | Admin/proprietario | ✅ Oggetto + Messaggio |
| 2️⃣ **Cliente** | Chi compila il form | ✅ Oggetto + Messaggio |
| 3️⃣ **Staff** | Team multiplo | ✅ Oggetto + Messaggio |

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** 
1. WordPress Admin → FP Forms
2. Click su "Modifica" accanto al form
3. Scroll nella **sidebar destra**
4. Sezione **"Impostazioni Form"**

---

## 1️⃣ EMAIL WEBMASTER (Admin)

### **Sezione:** Notifiche Email

### **Campi Disponibili:**

#### **Email Destinatario**
```
Input: email
Default: admin_email (da WordPress)
Esempio: admin@tuodominio.com
```

#### **Oggetto Email Webmaster** ⭐ PERSONALIZZABILE
```
Input: text
Default: "Nuova submission da {form_title}"
Placeholder: Nuova submission da {form_title}

Tag Disponibili:
- {form_title} - Nome del form
- {site_name} - Nome del sito
- {date} - Data corrente
- {time} - Ora corrente
```

**Esempi:**
```
"🆕 Nuova richiesta da {form_title}"
"[{site_name}] Submission ricevuta - {date}"
"URGENTE: Nuovo lead dal form {form_title}"
```

#### **Messaggio Email Webmaster** ⭐ NUOVO! PERSONALIZZABILE
```
Textarea: 8 righe
Default: (vuoto = template automatico)
Placeholder: Lascia vuoto per template automatico...

Tag Disponibili:
- Tutti i campi del form: {nome}, {email}, {telefono}, {messaggio}, etc.
- Tag generali: {form_title}, {site_name}, {date}, {time}
```

**Comportamento:**
- **Se VUOTO:** Usa template automatico (tutti i campi + IP + data)
- **Se COMPILATO:** Usa tuo template personalizzato

**Esempio Template Personalizzato:**
```
Ciao Admin,

Hai ricevuto una nuova richiesta dal form "{form_title}".

DATI CLIENTE:
Nome: {nome}
Email: {email}
Telefono: {telefono}

MESSAGGIO:
{messaggio}

---
Ricevuto il {date} alle {time}
Da: {site_name}

Rispondi direttamente a questa email per contattare il cliente.
```

**Esempio Template Auto (default se vuoto):**
```
Hai ricevuto una nuova submission dal form "Contact Form"

--------------------------------------------------

Nome: Mario Rossi
Email: mario.rossi@example.com
Telefono: +39 333 1234567
Messaggio: Vorrei maggiori informazioni sui vostri servizi

--------------------------------------------------

Informazioni aggiuntive:
Data: 2025-11-05 23:45:00
IP: 192.168.1.1
Utente: (guest)
```

---

## 2️⃣ EMAIL CLIENTE (Conferma)

### **Sezione:** Email di Conferma (Cliente)

### **Campi Disponibili:**

#### **Checkbox Abilita**
```
☑️ Invia email di conferma all'utente

Se OFF: Email non inviata
Se ON: Email inviata automaticamente
```

#### **Oggetto Email Conferma** ⭐ PERSONALIZZABILE
```
Input: text
Default: "Conferma ricezione messaggio"

Tag Disponibili:
- Tutti i campi: {nome}, {email}, etc.
- Tag generali: {form_title}, {site_name}, {date}, {time}
```

**Esempi:**
```
"Grazie {nome}, abbiamo ricevuto la tua richiesta!"
"Conferma: il tuo messaggio a {site_name}"
"{nome}, ti risponderemo entro 24 ore"
```

#### **Messaggio Email Conferma** ⭐ PERSONALIZZABILE
```
Textarea: 3 righe
Default: "Grazie per averci contattato!"

Tag Disponibili:
- Tutti i campi del form
- Tag generali
```

**Esempi:**
```
Ciao {nome},

Grazie per averci contattato tramite il nostro sito {site_name}.

Abbiamo ricevuto la tua richiesta e ti risponderemo al più presto all'indirizzo {email}.

Se la tua richiesta è urgente, puoi chiamarci al +39 02 1234567.

Cordiali saluti,
Il Team di {site_name}
```

---

## 3️⃣ EMAIL STAFF (Team)

### **Sezione:** Notifiche Staff

### **Campi Disponibili:**

#### **Checkbox Abilita**
```
☑️ Invia notifica a membri dello staff/team

Se OFF: Email staff non inviate
Se ON: Email inviate a tutti gli indirizzi
```

#### **Email Staff (una per riga)**
```
Textarea: 4 righe
Formati supportati:
- Una per riga
- Separati da virgola
- Separati da punto e virgola

Esempio:
sales@example.com
support@example.com
team@example.com
```

#### **Oggetto Email Staff** ⭐ PERSONALIZZABILE
```
Input: text
Default (se vuoto): "[STAFF] Nuova submission: {form_title}"
Placeholder: [STAFF] Nuova submission: {form_title}

Tag Disponibili:
- Tutti i tag standard
```

**Esempi:**
```
"[VENDITE] Nuovo lead da {form_title}"
"🔔 {site_name}: Nuova richiesta da {nome}"
"[URGENTE] Contatto da {form_title} - {date}"
```

#### **Messaggio Email Staff** ⭐ PERSONALIZZABILE
```
Textarea: 5 righe
Default (se vuoto): Template automatico (come webmaster)

Tag Disponibili:
- Tutti i campi del form
- Tag generali
```

**Esempio Template Staff:**
```
🎯 NUOVO LEAD - AZIONE RICHIESTA

Cliente: {nome}
Email: {email}
Telefono: {telefono}

Richiesta:
{messaggio}

---
AZIONE CONSIGLIATA:
1. Rispondere entro 2 ore
2. Qualificare il lead
3. Schedulare follow-up

Form: {form_title}
Data: {date} {time}
```

---

## 🏷️ TAG DINAMICI COMPLETI

### **Tag Campi Form** (auto-generati)
Qualsiasi campo nel tuo form diventa un tag:

```
Campo "nome"      → Tag: {nome}
Campo "email"     → Tag: {email}
Campo "telefono"  → Tag: {telefono}
Campo "messaggio" → Tag: {messaggio}
Campo "azienda"   → Tag: {azienda}
Campo "budget"    → Tag: {budget}
... qualsiasi nome campo
```

### **Tag Generali** (sempre disponibili)

| Tag | Output Esempio | Descrizione |
|-----|----------------|-------------|
| `{form_title}` | "Contact Form" | Titolo del form |
| `{site_name}` | "Your Company" | Nome sito WordPress |
| `{site_url}` | "https://example.com" | URL sito |
| `{date}` | "05/11/2025" | Data formato locale |
| `{time}` | "23:45" | Ora formato locale |

### **Come Usare i Tag:**

**Esempio Oggetto:**
```
Input: "Nuova richiesta da {nome} - {form_title}"
Output: "Nuova richiesta da Mario Rossi - Contact Form"
```

**Esempio Messaggio:**
```
Input: 
"Ciao {nome},
Grazie per il tuo interesse in {site_name}.
Ti contatteremo all'indirizzo {email} entro 24 ore."

Output:
"Ciao Mario,
Grazie per il tuo interesse in Your Company.
Ti contatteremo all'indirizzo mario@example.com entro 24 ore."
```

---

## 📊 TEMPLATE EXAMPLES

### **Template 1: Webmaster Dettagliato**
```
NUOVA SUBMISSION - {form_title}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📋 DATI CLIENTE
Nome Completo: {nome} {cognome}
Email: {email}
Telefono: {telefono}
Azienda: {azienda}

💬 MESSAGGIO
{messaggio}

🎯 INFORMAZIONI RICHIESTE
Budget: {budget}
Scadenza: {scadenza}
Servizio: {servizio}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Ricevuto: {date} alle {time}
Sito: {site_name}

⚡ AZIONE IMMEDIATA RICHIESTA
```

### **Template 2: Cliente Formale**
```
Gentile {nome},

Grazie per aver contattato {site_name}.

La sua richiesta è stata ricevuta con successo e sarà esaminata dal nostro team entro 24 ore lavorative.

RIEPILOGO RICHIESTA:
- Servizio: {servizio}
- Budget indicativo: {budget}
- Email di contatto: {email}
- Telefono: {telefono}

Le invieremo una risposta dettagliata all'indirizzo email fornito.

Per qualsiasi urgenza, può contattarci al numero +39 02 1234567.

Cordiali saluti,
Il Team di {site_name}
```

### **Template 3: Staff Action-Oriented**
```
🚨 NUOVO LEAD - PRIORITÀ ALTA

Lead Info:
👤 {nome} ({email})
📞 {telefono}
🏢 {azienda}

Richiesta:
"{messaggio}"

💰 Budget: {budget}
⏰ Scadenza: {scadenza}

━━━━━━━━━━━━━━━━━
📋 NEXT STEPS:
1. ✅ Qualifica lead (entro 1h)
2. 📧 Invia proposta (entro 4h)
3. 📞 Follow-up call (entro 24h)

Assegnato a: Sales Team
Form: {form_title}
Data: {date} {time}
```

### **Template 4: Cliente Informale**
```
Ciao {nome}! 👋

Grazie mille per averci scritto!

Abbiamo ricevuto il tuo messaggio e siamo super entusiasti di aiutarti.

Ti risponderemo all'indirizzo {email} il prima possibile (di solito entro qualche ora).

Nel frattempo, se vuoi saperne di più, dai un'occhiata al nostro sito:
{site_url}

A prestissimo!
Il Team di {site_name} ✨
```

---

## 🎨 BEST PRACTICES

### **Email Webmaster:**
✅ **Usa template personalizzato se:**
- Vuoi formattazione specifica
- Hai workflow definiti
- Vuoi solo alcuni campi (non tutti)
- Serve format specifico per CRM/ticket system

✅ **Usa template automatico se:**
- Vuoi vedere TUTTI i campi
- Non hai requirement specifici
- Preferisci semplicità

### **Email Cliente:**
✅ **Sempre personalizza!**
- Tono professionale/informale in base al brand
- Aggiungi info utili (orari, contatti)
- Rassicura il cliente (risposta entro X ore)
- Personalizza con {nome} per tocco umano

### **Email Staff:**
✅ **Personalizza per workflow:**
- Aggiungi checklist azioni
- Includi priority/urgency
- Link a CRM/sistema tickets
- Istruzioni chiare next steps

---

## 🔧 CONFIGURAZIONE STEP-BY-STEP

### **Step 1: Apri Form Builder**
```
WP Admin → FP Forms → Click "Modifica" sul form
```

### **Step 2: Vai a Sidebar → Notifiche Email**
```
Scroll sidebar destra fino a "Notifiche Email"
```

### **Step 3: Personalizza Webmaster**
```
[Oggetto Email Webmaster]
Input: "🆕 Nuovo contatto da {form_title}"

[Messaggio Email Webmaster] ⭐ NUOVO!
Textarea: (Inserisci tuo template o lascia vuoto)

Esempio:
"Nuovo contatto ricevuto:
Nome: {nome}
Email: {email}
Messaggio: {messaggio}"
```

### **Step 4: Personalizza Cliente**
```
[✓] Invia email di conferma all'utente

[Oggetto]
Input: "Grazie {nome}, abbiamo ricevuto la tua richiesta"

[Messaggio]
Textarea: "Ciao {nome}, ti risponderemo a {email} entro 24h!"
```

### **Step 5: Personalizza Staff**
```
[✓] Invia notifica a staff

[Email Staff]
Textarea:
sales@example.com
support@example.com

[Oggetto]
Input: "[SALES] Nuovo lead: {nome} da {form_title}"

[Messaggio]
Textarea: "Lead qualificato da seguire. Budget: {budget}"
```

### **Step 6: Salva**
```
Click "Salva Form" in fondo alla sidebar
✅ "Form aggiornato!"
```

---

## 🎯 ESEMPI PER CASO D'USO

### **Contact Form Aziendale**

**Webmaster:**
```
Oggetto: Nuova richiesta informazioni - {form_title}

Messaggio:
NUOVO CONTATTO RICEVUTO

Cliente: {nome}
Email: {email}
Telefono: {telefono}
Azienda: {azienda}

Richiesta:
{messaggio}

Ricevuto: {date} alle {time}
```

**Cliente:**
```
Oggetto: Grazie per averci contattato

Messaggio:
Gentile {nome},

Abbiamo ricevuto la sua richiesta e la contatteremo entro 24 ore lavorative all'indirizzo {email}.

Cordiali saluti,
{site_name}
```

**Staff:**
```
Oggetto: [ACTION REQUIRED] Nuovo lead: {nome}

Messaggio:
Nuovo lead da qualificare:

Nome: {nome}
Email: {email}
Telefono: {telefono}
Budget: {budget}

AZIONI:
1. Rispondere entro 2 ore
2. Qualificare lead (A/B/C)
3. Inserire in CRM
```

---

### **E-commerce Lead Generation**

**Webmaster:**
```
Oggetto: 💰 Nuovo potenziale cliente da {form_title}

Messaggio:
LEAD E-COMMERCE

Cliente: {nome}
Email: {email}
Prodotto interesse: {prodotto}
Budget: {budget}

Note: {note}
```

**Cliente:**
```
Oggetto: {nome}, ecco il tuo sconto del 10%!

Messaggio:
Ciao {nome}!

Grazie per il tuo interesse in {prodotto}.

🎁 REGALO PER TE: Usa il codice WELCOME10 per il 10% di sconto!

Ti invieremo una proposta personalizzata a {email} entro oggi.

Shop now: {site_url}
```

**Staff:**
```
Oggetto: 🛒 Hot lead e-commerce: {nome} ({prodotto})

Messaggio:
LEAD CALDO - PRIORITÀ ALTA

Cliente: {nome}
Email: {email}
Prodotto: {prodotto}
Budget: {budget}

ACTION:
- Invia proposta entro 1h
- Follow up call entro 3h
- Chiudi vendita entro 24h

CRM: [Inserisci in "Hot Leads"]
```

---

### **Event Registration**

**Webmaster:**
```
Oggetto: ✅ Nuova iscrizione evento: {nome}

Messaggio:
Iscrizione confermata per {evento}

Partecipante: {nome}
Email: {email}
Numero partecipanti: {numero_persone}
Esigenze speciali: {note}
```

**Cliente:**
```
Oggetto: 🎉 Iscrizione confermata per {evento}!

Messaggio:
Ciao {nome}!

Sei ufficialmente iscritto a {evento}!

📅 Data: {data_evento}
🕐 Ora: {ora_evento}
📍 Luogo: {luogo_evento}

Ti invieremo un reminder 24 ore prima.

Ci vediamo lì!
Team {site_name}
```

**Staff:**
```
Oggetto: [EVENTO] Nuova iscrizione: {nome}

Messaggio:
Nuovo partecipante registrato:

Nome: {nome}
Email: {email}
N. Persone: {numero_persone}
Esigenze: {note}

TODO:
- Aggiungi a lista partecipanti
- Invia badge/QR code
- Reminder -24h
```

---

## 📝 TAG PERSONALIZZATI PER CAMPO

### **Campi Checkbox Multipli:**
```
Campo: "Interessi" (checkbox)
Values: ['Sport', 'Viaggi', 'Tecnologia']

Tag: {interessi}
Output: "Sport, Viaggi, Tecnologia"
```

### **Campi Select:**
```
Campo: "Servizio" (select)
Value: "Consulenza Premium"

Tag: {servizio}
Output: "Consulenza Premium"
```

### **Campi Privacy/Marketing:**
```
Campo: "privacy_consent" (privacy-checkbox)
Value: "1"

Tag: {privacy_consent}
Output: "1"

Consiglio: Non includere nei template (non utile)
```

---

## ⚙️ OPZIONI AVANZATE

### **HTML Email (Feature Request)**
```
Attualmente: Plain text
Richiesta: HTML templates

Workaround (ora):
Usa hook filter:

add_filter('fp_forms_email_message', function($message, $form_id) {
    // Convert to HTML
    return nl2br($message);
}, 10, 2);
```

### **Allegati Email**
```
File upload già supportati!

Template:
"Hai ricevuto {numero_files} file allegati."

Gli attachment sono gestiti automaticamente da FileField.
```

### **CC/BCC**
```
Attualmente: Invio separato a ogni staff email

Se vuoi CC/BCC:
Usa hook filter:

add_filter('fp_forms_email_headers', function($headers) {
    $headers[] = 'Cc: manager@example.com';
    return $headers;
}, 10, 1);
```

---

## ✅ VERIFICA FUNZIONALITÀ

**Ho aggiunto:**
- ✅ Campo `notification_message` (webmaster template custom)
- ✅ UI nel form builder (textarea 8 righe)
- ✅ Logic in `Email/Manager.php` (usa custom o auto)
- ✅ Save in `admin.js` (campo salvato)
- ✅ Tag replacement (funziona con tutti i tag)

**Ora hai:**
```
Email Webmaster:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile ⭐ NUOVO!
  
Email Cliente:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile
  
Email Staff:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile
```

---

## 🎯 COME TESTARE

**Test Template Custom:**

1. Crea form
2. Sidebar → Notifiche Email
3. **Messaggio Email Webmaster:**
   ```
   Test personalizzazione:
   
   Nome: {nome}
   Email: {email}
   Data: {date}
   ```
4. Salva form
5. Compila e invia in frontend
6. Check email ricevuta → ✅ Template personalizzato usato!

**Test Template Auto:**

1. Lascia campo "Messaggio Email Webmaster" **vuoto**
2. Salva
3. Submit form
4. Check email → ✅ Template automatico con tutti i campi!

---

## 🎉 **TUTTE E 3 LE EMAIL SONO ORA COMPLETAMENTE PERSONALIZZABILI!**

**Campi Aggiunti:**
- ✅ Messaggio Email Webmaster (nuovo campo)
- ✅ Logic usa custom o fallback auto
- ✅ Tag replacement completo
- ✅ Save in JavaScript
- ✅ UI con placeholder e help text

**Personalizzazione Totale:**
- **Webmaster:** Oggetto ✅ + Messaggio ✅
- **Cliente:** Oggetto ✅ + Messaggio ✅
- **Staff:** Oggetto ✅ + Messaggio ✅

**Tutto configurabile dal Form Builder senza toccare codice!** 🎉📧


**Versione:** v1.2.2  
**Location:** Form Builder → Impostazioni Form (sidebar)  
**Status:** ✅ **TUTTE LE EMAIL 100% PERSONALIZZABILI!**

---

## 🎯 PANORAMICA

FP Forms invia **3 tipi di email** ad ogni submission, **tutte completamente personalizzabili**:

| Email | Destinatario | Personalizzabile |
|-------|--------------|------------------|
| 1️⃣ **Webmaster** | Admin/proprietario | ✅ Oggetto + Messaggio |
| 2️⃣ **Cliente** | Chi compila il form | ✅ Oggetto + Messaggio |
| 3️⃣ **Staff** | Team multiplo | ✅ Oggetto + Messaggio |

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** 
1. WordPress Admin → FP Forms
2. Click su "Modifica" accanto al form
3. Scroll nella **sidebar destra**
4. Sezione **"Impostazioni Form"**

---

## 1️⃣ EMAIL WEBMASTER (Admin)

### **Sezione:** Notifiche Email

### **Campi Disponibili:**

#### **Email Destinatario**
```
Input: email
Default: admin_email (da WordPress)
Esempio: admin@tuodominio.com
```

#### **Oggetto Email Webmaster** ⭐ PERSONALIZZABILE
```
Input: text
Default: "Nuova submission da {form_title}"
Placeholder: Nuova submission da {form_title}

Tag Disponibili:
- {form_title} - Nome del form
- {site_name} - Nome del sito
- {date} - Data corrente
- {time} - Ora corrente
```

**Esempi:**
```
"🆕 Nuova richiesta da {form_title}"
"[{site_name}] Submission ricevuta - {date}"
"URGENTE: Nuovo lead dal form {form_title}"
```

#### **Messaggio Email Webmaster** ⭐ NUOVO! PERSONALIZZABILE
```
Textarea: 8 righe
Default: (vuoto = template automatico)
Placeholder: Lascia vuoto per template automatico...

Tag Disponibili:
- Tutti i campi del form: {nome}, {email}, {telefono}, {messaggio}, etc.
- Tag generali: {form_title}, {site_name}, {date}, {time}
```

**Comportamento:**
- **Se VUOTO:** Usa template automatico (tutti i campi + IP + data)
- **Se COMPILATO:** Usa tuo template personalizzato

**Esempio Template Personalizzato:**
```
Ciao Admin,

Hai ricevuto una nuova richiesta dal form "{form_title}".

DATI CLIENTE:
Nome: {nome}
Email: {email}
Telefono: {telefono}

MESSAGGIO:
{messaggio}

---
Ricevuto il {date} alle {time}
Da: {site_name}

Rispondi direttamente a questa email per contattare il cliente.
```

**Esempio Template Auto (default se vuoto):**
```
Hai ricevuto una nuova submission dal form "Contact Form"

--------------------------------------------------

Nome: Mario Rossi
Email: mario.rossi@example.com
Telefono: +39 333 1234567
Messaggio: Vorrei maggiori informazioni sui vostri servizi

--------------------------------------------------

Informazioni aggiuntive:
Data: 2025-11-05 23:45:00
IP: 192.168.1.1
Utente: (guest)
```

---

## 2️⃣ EMAIL CLIENTE (Conferma)

### **Sezione:** Email di Conferma (Cliente)

### **Campi Disponibili:**

#### **Checkbox Abilita**
```
☑️ Invia email di conferma all'utente

Se OFF: Email non inviata
Se ON: Email inviata automaticamente
```

#### **Oggetto Email Conferma** ⭐ PERSONALIZZABILE
```
Input: text
Default: "Conferma ricezione messaggio"

Tag Disponibili:
- Tutti i campi: {nome}, {email}, etc.
- Tag generali: {form_title}, {site_name}, {date}, {time}
```

**Esempi:**
```
"Grazie {nome}, abbiamo ricevuto la tua richiesta!"
"Conferma: il tuo messaggio a {site_name}"
"{nome}, ti risponderemo entro 24 ore"
```

#### **Messaggio Email Conferma** ⭐ PERSONALIZZABILE
```
Textarea: 3 righe
Default: "Grazie per averci contattato!"

Tag Disponibili:
- Tutti i campi del form
- Tag generali
```

**Esempi:**
```
Ciao {nome},

Grazie per averci contattato tramite il nostro sito {site_name}.

Abbiamo ricevuto la tua richiesta e ti risponderemo al più presto all'indirizzo {email}.

Se la tua richiesta è urgente, puoi chiamarci al +39 02 1234567.

Cordiali saluti,
Il Team di {site_name}
```

---

## 3️⃣ EMAIL STAFF (Team)

### **Sezione:** Notifiche Staff

### **Campi Disponibili:**

#### **Checkbox Abilita**
```
☑️ Invia notifica a membri dello staff/team

Se OFF: Email staff non inviate
Se ON: Email inviate a tutti gli indirizzi
```

#### **Email Staff (una per riga)**
```
Textarea: 4 righe
Formati supportati:
- Una per riga
- Separati da virgola
- Separati da punto e virgola

Esempio:
sales@example.com
support@example.com
team@example.com
```

#### **Oggetto Email Staff** ⭐ PERSONALIZZABILE
```
Input: text
Default (se vuoto): "[STAFF] Nuova submission: {form_title}"
Placeholder: [STAFF] Nuova submission: {form_title}

Tag Disponibili:
- Tutti i tag standard
```

**Esempi:**
```
"[VENDITE] Nuovo lead da {form_title}"
"🔔 {site_name}: Nuova richiesta da {nome}"
"[URGENTE] Contatto da {form_title} - {date}"
```

#### **Messaggio Email Staff** ⭐ PERSONALIZZABILE
```
Textarea: 5 righe
Default (se vuoto): Template automatico (come webmaster)

Tag Disponibili:
- Tutti i campi del form
- Tag generali
```

**Esempio Template Staff:**
```
🎯 NUOVO LEAD - AZIONE RICHIESTA

Cliente: {nome}
Email: {email}
Telefono: {telefono}

Richiesta:
{messaggio}

---
AZIONE CONSIGLIATA:
1. Rispondere entro 2 ore
2. Qualificare il lead
3. Schedulare follow-up

Form: {form_title}
Data: {date} {time}
```

---

## 🏷️ TAG DINAMICI COMPLETI

### **Tag Campi Form** (auto-generati)
Qualsiasi campo nel tuo form diventa un tag:

```
Campo "nome"      → Tag: {nome}
Campo "email"     → Tag: {email}
Campo "telefono"  → Tag: {telefono}
Campo "messaggio" → Tag: {messaggio}
Campo "azienda"   → Tag: {azienda}
Campo "budget"    → Tag: {budget}
... qualsiasi nome campo
```

### **Tag Generali** (sempre disponibili)

| Tag | Output Esempio | Descrizione |
|-----|----------------|-------------|
| `{form_title}` | "Contact Form" | Titolo del form |
| `{site_name}` | "Your Company" | Nome sito WordPress |
| `{site_url}` | "https://example.com" | URL sito |
| `{date}` | "05/11/2025" | Data formato locale |
| `{time}` | "23:45" | Ora formato locale |

### **Come Usare i Tag:**

**Esempio Oggetto:**
```
Input: "Nuova richiesta da {nome} - {form_title}"
Output: "Nuova richiesta da Mario Rossi - Contact Form"
```

**Esempio Messaggio:**
```
Input: 
"Ciao {nome},
Grazie per il tuo interesse in {site_name}.
Ti contatteremo all'indirizzo {email} entro 24 ore."

Output:
"Ciao Mario,
Grazie per il tuo interesse in Your Company.
Ti contatteremo all'indirizzo mario@example.com entro 24 ore."
```

---

## 📊 TEMPLATE EXAMPLES

### **Template 1: Webmaster Dettagliato**
```
NUOVA SUBMISSION - {form_title}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📋 DATI CLIENTE
Nome Completo: {nome} {cognome}
Email: {email}
Telefono: {telefono}
Azienda: {azienda}

💬 MESSAGGIO
{messaggio}

🎯 INFORMAZIONI RICHIESTE
Budget: {budget}
Scadenza: {scadenza}
Servizio: {servizio}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Ricevuto: {date} alle {time}
Sito: {site_name}

⚡ AZIONE IMMEDIATA RICHIESTA
```

### **Template 2: Cliente Formale**
```
Gentile {nome},

Grazie per aver contattato {site_name}.

La sua richiesta è stata ricevuta con successo e sarà esaminata dal nostro team entro 24 ore lavorative.

RIEPILOGO RICHIESTA:
- Servizio: {servizio}
- Budget indicativo: {budget}
- Email di contatto: {email}
- Telefono: {telefono}

Le invieremo una risposta dettagliata all'indirizzo email fornito.

Per qualsiasi urgenza, può contattarci al numero +39 02 1234567.

Cordiali saluti,
Il Team di {site_name}
```

### **Template 3: Staff Action-Oriented**
```
🚨 NUOVO LEAD - PRIORITÀ ALTA

Lead Info:
👤 {nome} ({email})
📞 {telefono}
🏢 {azienda}

Richiesta:
"{messaggio}"

💰 Budget: {budget}
⏰ Scadenza: {scadenza}

━━━━━━━━━━━━━━━━━
📋 NEXT STEPS:
1. ✅ Qualifica lead (entro 1h)
2. 📧 Invia proposta (entro 4h)
3. 📞 Follow-up call (entro 24h)

Assegnato a: Sales Team
Form: {form_title}
Data: {date} {time}
```

### **Template 4: Cliente Informale**
```
Ciao {nome}! 👋

Grazie mille per averci scritto!

Abbiamo ricevuto il tuo messaggio e siamo super entusiasti di aiutarti.

Ti risponderemo all'indirizzo {email} il prima possibile (di solito entro qualche ora).

Nel frattempo, se vuoi saperne di più, dai un'occhiata al nostro sito:
{site_url}

A prestissimo!
Il Team di {site_name} ✨
```

---

## 🎨 BEST PRACTICES

### **Email Webmaster:**
✅ **Usa template personalizzato se:**
- Vuoi formattazione specifica
- Hai workflow definiti
- Vuoi solo alcuni campi (non tutti)
- Serve format specifico per CRM/ticket system

✅ **Usa template automatico se:**
- Vuoi vedere TUTTI i campi
- Non hai requirement specifici
- Preferisci semplicità

### **Email Cliente:**
✅ **Sempre personalizza!**
- Tono professionale/informale in base al brand
- Aggiungi info utili (orari, contatti)
- Rassicura il cliente (risposta entro X ore)
- Personalizza con {nome} per tocco umano

### **Email Staff:**
✅ **Personalizza per workflow:**
- Aggiungi checklist azioni
- Includi priority/urgency
- Link a CRM/sistema tickets
- Istruzioni chiare next steps

---

## 🔧 CONFIGURAZIONE STEP-BY-STEP

### **Step 1: Apri Form Builder**
```
WP Admin → FP Forms → Click "Modifica" sul form
```

### **Step 2: Vai a Sidebar → Notifiche Email**
```
Scroll sidebar destra fino a "Notifiche Email"
```

### **Step 3: Personalizza Webmaster**
```
[Oggetto Email Webmaster]
Input: "🆕 Nuovo contatto da {form_title}"

[Messaggio Email Webmaster] ⭐ NUOVO!
Textarea: (Inserisci tuo template o lascia vuoto)

Esempio:
"Nuovo contatto ricevuto:
Nome: {nome}
Email: {email}
Messaggio: {messaggio}"
```

### **Step 4: Personalizza Cliente**
```
[✓] Invia email di conferma all'utente

[Oggetto]
Input: "Grazie {nome}, abbiamo ricevuto la tua richiesta"

[Messaggio]
Textarea: "Ciao {nome}, ti risponderemo a {email} entro 24h!"
```

### **Step 5: Personalizza Staff**
```
[✓] Invia notifica a staff

[Email Staff]
Textarea:
sales@example.com
support@example.com

[Oggetto]
Input: "[SALES] Nuovo lead: {nome} da {form_title}"

[Messaggio]
Textarea: "Lead qualificato da seguire. Budget: {budget}"
```

### **Step 6: Salva**
```
Click "Salva Form" in fondo alla sidebar
✅ "Form aggiornato!"
```

---

## 🎯 ESEMPI PER CASO D'USO

### **Contact Form Aziendale**

**Webmaster:**
```
Oggetto: Nuova richiesta informazioni - {form_title}

Messaggio:
NUOVO CONTATTO RICEVUTO

Cliente: {nome}
Email: {email}
Telefono: {telefono}
Azienda: {azienda}

Richiesta:
{messaggio}

Ricevuto: {date} alle {time}
```

**Cliente:**
```
Oggetto: Grazie per averci contattato

Messaggio:
Gentile {nome},

Abbiamo ricevuto la sua richiesta e la contatteremo entro 24 ore lavorative all'indirizzo {email}.

Cordiali saluti,
{site_name}
```

**Staff:**
```
Oggetto: [ACTION REQUIRED] Nuovo lead: {nome}

Messaggio:
Nuovo lead da qualificare:

Nome: {nome}
Email: {email}
Telefono: {telefono}
Budget: {budget}

AZIONI:
1. Rispondere entro 2 ore
2. Qualificare lead (A/B/C)
3. Inserire in CRM
```

---

### **E-commerce Lead Generation**

**Webmaster:**
```
Oggetto: 💰 Nuovo potenziale cliente da {form_title}

Messaggio:
LEAD E-COMMERCE

Cliente: {nome}
Email: {email}
Prodotto interesse: {prodotto}
Budget: {budget}

Note: {note}
```

**Cliente:**
```
Oggetto: {nome}, ecco il tuo sconto del 10%!

Messaggio:
Ciao {nome}!

Grazie per il tuo interesse in {prodotto}.

🎁 REGALO PER TE: Usa il codice WELCOME10 per il 10% di sconto!

Ti invieremo una proposta personalizzata a {email} entro oggi.

Shop now: {site_url}
```

**Staff:**
```
Oggetto: 🛒 Hot lead e-commerce: {nome} ({prodotto})

Messaggio:
LEAD CALDO - PRIORITÀ ALTA

Cliente: {nome}
Email: {email}
Prodotto: {prodotto}
Budget: {budget}

ACTION:
- Invia proposta entro 1h
- Follow up call entro 3h
- Chiudi vendita entro 24h

CRM: [Inserisci in "Hot Leads"]
```

---

### **Event Registration**

**Webmaster:**
```
Oggetto: ✅ Nuova iscrizione evento: {nome}

Messaggio:
Iscrizione confermata per {evento}

Partecipante: {nome}
Email: {email}
Numero partecipanti: {numero_persone}
Esigenze speciali: {note}
```

**Cliente:**
```
Oggetto: 🎉 Iscrizione confermata per {evento}!

Messaggio:
Ciao {nome}!

Sei ufficialmente iscritto a {evento}!

📅 Data: {data_evento}
🕐 Ora: {ora_evento}
📍 Luogo: {luogo_evento}

Ti invieremo un reminder 24 ore prima.

Ci vediamo lì!
Team {site_name}
```

**Staff:**
```
Oggetto: [EVENTO] Nuova iscrizione: {nome}

Messaggio:
Nuovo partecipante registrato:

Nome: {nome}
Email: {email}
N. Persone: {numero_persone}
Esigenze: {note}

TODO:
- Aggiungi a lista partecipanti
- Invia badge/QR code
- Reminder -24h
```

---

## 📝 TAG PERSONALIZZATI PER CAMPO

### **Campi Checkbox Multipli:**
```
Campo: "Interessi" (checkbox)
Values: ['Sport', 'Viaggi', 'Tecnologia']

Tag: {interessi}
Output: "Sport, Viaggi, Tecnologia"
```

### **Campi Select:**
```
Campo: "Servizio" (select)
Value: "Consulenza Premium"

Tag: {servizio}
Output: "Consulenza Premium"
```

### **Campi Privacy/Marketing:**
```
Campo: "privacy_consent" (privacy-checkbox)
Value: "1"

Tag: {privacy_consent}
Output: "1"

Consiglio: Non includere nei template (non utile)
```

---

## ⚙️ OPZIONI AVANZATE

### **HTML Email (Feature Request)**
```
Attualmente: Plain text
Richiesta: HTML templates

Workaround (ora):
Usa hook filter:

add_filter('fp_forms_email_message', function($message, $form_id) {
    // Convert to HTML
    return nl2br($message);
}, 10, 2);
```

### **Allegati Email**
```
File upload già supportati!

Template:
"Hai ricevuto {numero_files} file allegati."

Gli attachment sono gestiti automaticamente da FileField.
```

### **CC/BCC**
```
Attualmente: Invio separato a ogni staff email

Se vuoi CC/BCC:
Usa hook filter:

add_filter('fp_forms_email_headers', function($headers) {
    $headers[] = 'Cc: manager@example.com';
    return $headers;
}, 10, 1);
```

---

## ✅ VERIFICA FUNZIONALITÀ

**Ho aggiunto:**
- ✅ Campo `notification_message` (webmaster template custom)
- ✅ UI nel form builder (textarea 8 righe)
- ✅ Logic in `Email/Manager.php` (usa custom o auto)
- ✅ Save in `admin.js` (campo salvato)
- ✅ Tag replacement (funziona con tutti i tag)

**Ora hai:**
```
Email Webmaster:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile ⭐ NUOVO!
  
Email Cliente:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile
  
Email Staff:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile
```

---

## 🎯 COME TESTARE

**Test Template Custom:**

1. Crea form
2. Sidebar → Notifiche Email
3. **Messaggio Email Webmaster:**
   ```
   Test personalizzazione:
   
   Nome: {nome}
   Email: {email}
   Data: {date}
   ```
4. Salva form
5. Compila e invia in frontend
6. Check email ricevuta → ✅ Template personalizzato usato!

**Test Template Auto:**

1. Lascia campo "Messaggio Email Webmaster" **vuoto**
2. Salva
3. Submit form
4. Check email → ✅ Template automatico con tutti i campi!

---

## 🎉 **TUTTE E 3 LE EMAIL SONO ORA COMPLETAMENTE PERSONALIZZABILI!**

**Campi Aggiunti:**
- ✅ Messaggio Email Webmaster (nuovo campo)
- ✅ Logic usa custom o fallback auto
- ✅ Tag replacement completo
- ✅ Save in JavaScript
- ✅ UI con placeholder e help text

**Personalizzazione Totale:**
- **Webmaster:** Oggetto ✅ + Messaggio ✅
- **Cliente:** Oggetto ✅ + Messaggio ✅
- **Staff:** Oggetto ✅ + Messaggio ✅

**Tutto configurabile dal Form Builder senza toccare codice!** 🎉📧


**Versione:** v1.2.2  
**Location:** Form Builder → Impostazioni Form (sidebar)  
**Status:** ✅ **TUTTE LE EMAIL 100% PERSONALIZZABILI!**

---

## 🎯 PANORAMICA

FP Forms invia **3 tipi di email** ad ogni submission, **tutte completamente personalizzabili**:

| Email | Destinatario | Personalizzabile |
|-------|--------------|------------------|
| 1️⃣ **Webmaster** | Admin/proprietario | ✅ Oggetto + Messaggio |
| 2️⃣ **Cliente** | Chi compila il form | ✅ Oggetto + Messaggio |
| 3️⃣ **Staff** | Team multiplo | ✅ Oggetto + Messaggio |

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** 
1. WordPress Admin → FP Forms
2. Click su "Modifica" accanto al form
3. Scroll nella **sidebar destra**
4. Sezione **"Impostazioni Form"**

---

## 1️⃣ EMAIL WEBMASTER (Admin)

### **Sezione:** Notifiche Email

### **Campi Disponibili:**

#### **Email Destinatario**
```
Input: email
Default: admin_email (da WordPress)
Esempio: admin@tuodominio.com
```

#### **Oggetto Email Webmaster** ⭐ PERSONALIZZABILE
```
Input: text
Default: "Nuova submission da {form_title}"
Placeholder: Nuova submission da {form_title}

Tag Disponibili:
- {form_title} - Nome del form
- {site_name} - Nome del sito
- {date} - Data corrente
- {time} - Ora corrente
```

**Esempi:**
```
"🆕 Nuova richiesta da {form_title}"
"[{site_name}] Submission ricevuta - {date}"
"URGENTE: Nuovo lead dal form {form_title}"
```

#### **Messaggio Email Webmaster** ⭐ NUOVO! PERSONALIZZABILE
```
Textarea: 8 righe
Default: (vuoto = template automatico)
Placeholder: Lascia vuoto per template automatico...

Tag Disponibili:
- Tutti i campi del form: {nome}, {email}, {telefono}, {messaggio}, etc.
- Tag generali: {form_title}, {site_name}, {date}, {time}
```

**Comportamento:**
- **Se VUOTO:** Usa template automatico (tutti i campi + IP + data)
- **Se COMPILATO:** Usa tuo template personalizzato

**Esempio Template Personalizzato:**
```
Ciao Admin,

Hai ricevuto una nuova richiesta dal form "{form_title}".

DATI CLIENTE:
Nome: {nome}
Email: {email}
Telefono: {telefono}

MESSAGGIO:
{messaggio}

---
Ricevuto il {date} alle {time}
Da: {site_name}

Rispondi direttamente a questa email per contattare il cliente.
```

**Esempio Template Auto (default se vuoto):**
```
Hai ricevuto una nuova submission dal form "Contact Form"

--------------------------------------------------

Nome: Mario Rossi
Email: mario.rossi@example.com
Telefono: +39 333 1234567
Messaggio: Vorrei maggiori informazioni sui vostri servizi

--------------------------------------------------

Informazioni aggiuntive:
Data: 2025-11-05 23:45:00
IP: 192.168.1.1
Utente: (guest)
```

---

## 2️⃣ EMAIL CLIENTE (Conferma)

### **Sezione:** Email di Conferma (Cliente)

### **Campi Disponibili:**

#### **Checkbox Abilita**
```
☑️ Invia email di conferma all'utente

Se OFF: Email non inviata
Se ON: Email inviata automaticamente
```

#### **Oggetto Email Conferma** ⭐ PERSONALIZZABILE
```
Input: text
Default: "Conferma ricezione messaggio"

Tag Disponibili:
- Tutti i campi: {nome}, {email}, etc.
- Tag generali: {form_title}, {site_name}, {date}, {time}
```

**Esempi:**
```
"Grazie {nome}, abbiamo ricevuto la tua richiesta!"
"Conferma: il tuo messaggio a {site_name}"
"{nome}, ti risponderemo entro 24 ore"
```

#### **Messaggio Email Conferma** ⭐ PERSONALIZZABILE
```
Textarea: 3 righe
Default: "Grazie per averci contattato!"

Tag Disponibili:
- Tutti i campi del form
- Tag generali
```

**Esempi:**
```
Ciao {nome},

Grazie per averci contattato tramite il nostro sito {site_name}.

Abbiamo ricevuto la tua richiesta e ti risponderemo al più presto all'indirizzo {email}.

Se la tua richiesta è urgente, puoi chiamarci al +39 02 1234567.

Cordiali saluti,
Il Team di {site_name}
```

---

## 3️⃣ EMAIL STAFF (Team)

### **Sezione:** Notifiche Staff

### **Campi Disponibili:**

#### **Checkbox Abilita**
```
☑️ Invia notifica a membri dello staff/team

Se OFF: Email staff non inviate
Se ON: Email inviate a tutti gli indirizzi
```

#### **Email Staff (una per riga)**
```
Textarea: 4 righe
Formati supportati:
- Una per riga
- Separati da virgola
- Separati da punto e virgola

Esempio:
sales@example.com
support@example.com
team@example.com
```

#### **Oggetto Email Staff** ⭐ PERSONALIZZABILE
```
Input: text
Default (se vuoto): "[STAFF] Nuova submission: {form_title}"
Placeholder: [STAFF] Nuova submission: {form_title}

Tag Disponibili:
- Tutti i tag standard
```

**Esempi:**
```
"[VENDITE] Nuovo lead da {form_title}"
"🔔 {site_name}: Nuova richiesta da {nome}"
"[URGENTE] Contatto da {form_title} - {date}"
```

#### **Messaggio Email Staff** ⭐ PERSONALIZZABILE
```
Textarea: 5 righe
Default (se vuoto): Template automatico (come webmaster)

Tag Disponibili:
- Tutti i campi del form
- Tag generali
```

**Esempio Template Staff:**
```
🎯 NUOVO LEAD - AZIONE RICHIESTA

Cliente: {nome}
Email: {email}
Telefono: {telefono}

Richiesta:
{messaggio}

---
AZIONE CONSIGLIATA:
1. Rispondere entro 2 ore
2. Qualificare il lead
3. Schedulare follow-up

Form: {form_title}
Data: {date} {time}
```

---

## 🏷️ TAG DINAMICI COMPLETI

### **Tag Campi Form** (auto-generati)
Qualsiasi campo nel tuo form diventa un tag:

```
Campo "nome"      → Tag: {nome}
Campo "email"     → Tag: {email}
Campo "telefono"  → Tag: {telefono}
Campo "messaggio" → Tag: {messaggio}
Campo "azienda"   → Tag: {azienda}
Campo "budget"    → Tag: {budget}
... qualsiasi nome campo
```

### **Tag Generali** (sempre disponibili)

| Tag | Output Esempio | Descrizione |
|-----|----------------|-------------|
| `{form_title}` | "Contact Form" | Titolo del form |
| `{site_name}` | "Your Company" | Nome sito WordPress |
| `{site_url}` | "https://example.com" | URL sito |
| `{date}` | "05/11/2025" | Data formato locale |
| `{time}` | "23:45" | Ora formato locale |

### **Come Usare i Tag:**

**Esempio Oggetto:**
```
Input: "Nuova richiesta da {nome} - {form_title}"
Output: "Nuova richiesta da Mario Rossi - Contact Form"
```

**Esempio Messaggio:**
```
Input: 
"Ciao {nome},
Grazie per il tuo interesse in {site_name}.
Ti contatteremo all'indirizzo {email} entro 24 ore."

Output:
"Ciao Mario,
Grazie per il tuo interesse in Your Company.
Ti contatteremo all'indirizzo mario@example.com entro 24 ore."
```

---

## 📊 TEMPLATE EXAMPLES

### **Template 1: Webmaster Dettagliato**
```
NUOVA SUBMISSION - {form_title}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📋 DATI CLIENTE
Nome Completo: {nome} {cognome}
Email: {email}
Telefono: {telefono}
Azienda: {azienda}

💬 MESSAGGIO
{messaggio}

🎯 INFORMAZIONI RICHIESTE
Budget: {budget}
Scadenza: {scadenza}
Servizio: {servizio}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Ricevuto: {date} alle {time}
Sito: {site_name}

⚡ AZIONE IMMEDIATA RICHIESTA
```

### **Template 2: Cliente Formale**
```
Gentile {nome},

Grazie per aver contattato {site_name}.

La sua richiesta è stata ricevuta con successo e sarà esaminata dal nostro team entro 24 ore lavorative.

RIEPILOGO RICHIESTA:
- Servizio: {servizio}
- Budget indicativo: {budget}
- Email di contatto: {email}
- Telefono: {telefono}

Le invieremo una risposta dettagliata all'indirizzo email fornito.

Per qualsiasi urgenza, può contattarci al numero +39 02 1234567.

Cordiali saluti,
Il Team di {site_name}
```

### **Template 3: Staff Action-Oriented**
```
🚨 NUOVO LEAD - PRIORITÀ ALTA

Lead Info:
👤 {nome} ({email})
📞 {telefono}
🏢 {azienda}

Richiesta:
"{messaggio}"

💰 Budget: {budget}
⏰ Scadenza: {scadenza}

━━━━━━━━━━━━━━━━━
📋 NEXT STEPS:
1. ✅ Qualifica lead (entro 1h)
2. 📧 Invia proposta (entro 4h)
3. 📞 Follow-up call (entro 24h)

Assegnato a: Sales Team
Form: {form_title}
Data: {date} {time}
```

### **Template 4: Cliente Informale**
```
Ciao {nome}! 👋

Grazie mille per averci scritto!

Abbiamo ricevuto il tuo messaggio e siamo super entusiasti di aiutarti.

Ti risponderemo all'indirizzo {email} il prima possibile (di solito entro qualche ora).

Nel frattempo, se vuoi saperne di più, dai un'occhiata al nostro sito:
{site_url}

A prestissimo!
Il Team di {site_name} ✨
```

---

## 🎨 BEST PRACTICES

### **Email Webmaster:**
✅ **Usa template personalizzato se:**
- Vuoi formattazione specifica
- Hai workflow definiti
- Vuoi solo alcuni campi (non tutti)
- Serve format specifico per CRM/ticket system

✅ **Usa template automatico se:**
- Vuoi vedere TUTTI i campi
- Non hai requirement specifici
- Preferisci semplicità

### **Email Cliente:**
✅ **Sempre personalizza!**
- Tono professionale/informale in base al brand
- Aggiungi info utili (orari, contatti)
- Rassicura il cliente (risposta entro X ore)
- Personalizza con {nome} per tocco umano

### **Email Staff:**
✅ **Personalizza per workflow:**
- Aggiungi checklist azioni
- Includi priority/urgency
- Link a CRM/sistema tickets
- Istruzioni chiare next steps

---

## 🔧 CONFIGURAZIONE STEP-BY-STEP

### **Step 1: Apri Form Builder**
```
WP Admin → FP Forms → Click "Modifica" sul form
```

### **Step 2: Vai a Sidebar → Notifiche Email**
```
Scroll sidebar destra fino a "Notifiche Email"
```

### **Step 3: Personalizza Webmaster**
```
[Oggetto Email Webmaster]
Input: "🆕 Nuovo contatto da {form_title}"

[Messaggio Email Webmaster] ⭐ NUOVO!
Textarea: (Inserisci tuo template o lascia vuoto)

Esempio:
"Nuovo contatto ricevuto:
Nome: {nome}
Email: {email}
Messaggio: {messaggio}"
```

### **Step 4: Personalizza Cliente**
```
[✓] Invia email di conferma all'utente

[Oggetto]
Input: "Grazie {nome}, abbiamo ricevuto la tua richiesta"

[Messaggio]
Textarea: "Ciao {nome}, ti risponderemo a {email} entro 24h!"
```

### **Step 5: Personalizza Staff**
```
[✓] Invia notifica a staff

[Email Staff]
Textarea:
sales@example.com
support@example.com

[Oggetto]
Input: "[SALES] Nuovo lead: {nome} da {form_title}"

[Messaggio]
Textarea: "Lead qualificato da seguire. Budget: {budget}"
```

### **Step 6: Salva**
```
Click "Salva Form" in fondo alla sidebar
✅ "Form aggiornato!"
```

---

## 🎯 ESEMPI PER CASO D'USO

### **Contact Form Aziendale**

**Webmaster:**
```
Oggetto: Nuova richiesta informazioni - {form_title}

Messaggio:
NUOVO CONTATTO RICEVUTO

Cliente: {nome}
Email: {email}
Telefono: {telefono}
Azienda: {azienda}

Richiesta:
{messaggio}

Ricevuto: {date} alle {time}
```

**Cliente:**
```
Oggetto: Grazie per averci contattato

Messaggio:
Gentile {nome},

Abbiamo ricevuto la sua richiesta e la contatteremo entro 24 ore lavorative all'indirizzo {email}.

Cordiali saluti,
{site_name}
```

**Staff:**
```
Oggetto: [ACTION REQUIRED] Nuovo lead: {nome}

Messaggio:
Nuovo lead da qualificare:

Nome: {nome}
Email: {email}
Telefono: {telefono}
Budget: {budget}

AZIONI:
1. Rispondere entro 2 ore
2. Qualificare lead (A/B/C)
3. Inserire in CRM
```

---

### **E-commerce Lead Generation**

**Webmaster:**
```
Oggetto: 💰 Nuovo potenziale cliente da {form_title}

Messaggio:
LEAD E-COMMERCE

Cliente: {nome}
Email: {email}
Prodotto interesse: {prodotto}
Budget: {budget}

Note: {note}
```

**Cliente:**
```
Oggetto: {nome}, ecco il tuo sconto del 10%!

Messaggio:
Ciao {nome}!

Grazie per il tuo interesse in {prodotto}.

🎁 REGALO PER TE: Usa il codice WELCOME10 per il 10% di sconto!

Ti invieremo una proposta personalizzata a {email} entro oggi.

Shop now: {site_url}
```

**Staff:**
```
Oggetto: 🛒 Hot lead e-commerce: {nome} ({prodotto})

Messaggio:
LEAD CALDO - PRIORITÀ ALTA

Cliente: {nome}
Email: {email}
Prodotto: {prodotto}
Budget: {budget}

ACTION:
- Invia proposta entro 1h
- Follow up call entro 3h
- Chiudi vendita entro 24h

CRM: [Inserisci in "Hot Leads"]
```

---

### **Event Registration**

**Webmaster:**
```
Oggetto: ✅ Nuova iscrizione evento: {nome}

Messaggio:
Iscrizione confermata per {evento}

Partecipante: {nome}
Email: {email}
Numero partecipanti: {numero_persone}
Esigenze speciali: {note}
```

**Cliente:**
```
Oggetto: 🎉 Iscrizione confermata per {evento}!

Messaggio:
Ciao {nome}!

Sei ufficialmente iscritto a {evento}!

📅 Data: {data_evento}
🕐 Ora: {ora_evento}
📍 Luogo: {luogo_evento}

Ti invieremo un reminder 24 ore prima.

Ci vediamo lì!
Team {site_name}
```

**Staff:**
```
Oggetto: [EVENTO] Nuova iscrizione: {nome}

Messaggio:
Nuovo partecipante registrato:

Nome: {nome}
Email: {email}
N. Persone: {numero_persone}
Esigenze: {note}

TODO:
- Aggiungi a lista partecipanti
- Invia badge/QR code
- Reminder -24h
```

---

## 📝 TAG PERSONALIZZATI PER CAMPO

### **Campi Checkbox Multipli:**
```
Campo: "Interessi" (checkbox)
Values: ['Sport', 'Viaggi', 'Tecnologia']

Tag: {interessi}
Output: "Sport, Viaggi, Tecnologia"
```

### **Campi Select:**
```
Campo: "Servizio" (select)
Value: "Consulenza Premium"

Tag: {servizio}
Output: "Consulenza Premium"
```

### **Campi Privacy/Marketing:**
```
Campo: "privacy_consent" (privacy-checkbox)
Value: "1"

Tag: {privacy_consent}
Output: "1"

Consiglio: Non includere nei template (non utile)
```

---

## ⚙️ OPZIONI AVANZATE

### **HTML Email (Feature Request)**
```
Attualmente: Plain text
Richiesta: HTML templates

Workaround (ora):
Usa hook filter:

add_filter('fp_forms_email_message', function($message, $form_id) {
    // Convert to HTML
    return nl2br($message);
}, 10, 2);
```

### **Allegati Email**
```
File upload già supportati!

Template:
"Hai ricevuto {numero_files} file allegati."

Gli attachment sono gestiti automaticamente da FileField.
```

### **CC/BCC**
```
Attualmente: Invio separato a ogni staff email

Se vuoi CC/BCC:
Usa hook filter:

add_filter('fp_forms_email_headers', function($headers) {
    $headers[] = 'Cc: manager@example.com';
    return $headers;
}, 10, 1);
```

---

## ✅ VERIFICA FUNZIONALITÀ

**Ho aggiunto:**
- ✅ Campo `notification_message` (webmaster template custom)
- ✅ UI nel form builder (textarea 8 righe)
- ✅ Logic in `Email/Manager.php` (usa custom o auto)
- ✅ Save in `admin.js` (campo salvato)
- ✅ Tag replacement (funziona con tutti i tag)

**Ora hai:**
```
Email Webmaster:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile ⭐ NUOVO!
  
Email Cliente:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile
  
Email Staff:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile
```

---

## 🎯 COME TESTARE

**Test Template Custom:**

1. Crea form
2. Sidebar → Notifiche Email
3. **Messaggio Email Webmaster:**
   ```
   Test personalizzazione:
   
   Nome: {nome}
   Email: {email}
   Data: {date}
   ```
4. Salva form
5. Compila e invia in frontend
6. Check email ricevuta → ✅ Template personalizzato usato!

**Test Template Auto:**

1. Lascia campo "Messaggio Email Webmaster" **vuoto**
2. Salva
3. Submit form
4. Check email → ✅ Template automatico con tutti i campi!

---

## 🎉 **TUTTE E 3 LE EMAIL SONO ORA COMPLETAMENTE PERSONALIZZABILI!**

**Campi Aggiunti:**
- ✅ Messaggio Email Webmaster (nuovo campo)
- ✅ Logic usa custom o fallback auto
- ✅ Tag replacement completo
- ✅ Save in JavaScript
- ✅ UI con placeholder e help text

**Personalizzazione Totale:**
- **Webmaster:** Oggetto ✅ + Messaggio ✅
- **Cliente:** Oggetto ✅ + Messaggio ✅
- **Staff:** Oggetto ✅ + Messaggio ✅

**Tutto configurabile dal Form Builder senza toccare codice!** 🎉📧


**Versione:** v1.2.2  
**Location:** Form Builder → Impostazioni Form (sidebar)  
**Status:** ✅ **TUTTE LE EMAIL 100% PERSONALIZZABILI!**

---

## 🎯 PANORAMICA

FP Forms invia **3 tipi di email** ad ogni submission, **tutte completamente personalizzabili**:

| Email | Destinatario | Personalizzabile |
|-------|--------------|------------------|
| 1️⃣ **Webmaster** | Admin/proprietario | ✅ Oggetto + Messaggio |
| 2️⃣ **Cliente** | Chi compila il form | ✅ Oggetto + Messaggio |
| 3️⃣ **Staff** | Team multiplo | ✅ Oggetto + Messaggio |

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** 
1. WordPress Admin → FP Forms
2. Click su "Modifica" accanto al form
3. Scroll nella **sidebar destra**
4. Sezione **"Impostazioni Form"**

---

## 1️⃣ EMAIL WEBMASTER (Admin)

### **Sezione:** Notifiche Email

### **Campi Disponibili:**

#### **Email Destinatario**
```
Input: email
Default: admin_email (da WordPress)
Esempio: admin@tuodominio.com
```

#### **Oggetto Email Webmaster** ⭐ PERSONALIZZABILE
```
Input: text
Default: "Nuova submission da {form_title}"
Placeholder: Nuova submission da {form_title}

Tag Disponibili:
- {form_title} - Nome del form
- {site_name} - Nome del sito
- {date} - Data corrente
- {time} - Ora corrente
```

**Esempi:**
```
"🆕 Nuova richiesta da {form_title}"
"[{site_name}] Submission ricevuta - {date}"
"URGENTE: Nuovo lead dal form {form_title}"
```

#### **Messaggio Email Webmaster** ⭐ NUOVO! PERSONALIZZABILE
```
Textarea: 8 righe
Default: (vuoto = template automatico)
Placeholder: Lascia vuoto per template automatico...

Tag Disponibili:
- Tutti i campi del form: {nome}, {email}, {telefono}, {messaggio}, etc.
- Tag generali: {form_title}, {site_name}, {date}, {time}
```

**Comportamento:**
- **Se VUOTO:** Usa template automatico (tutti i campi + IP + data)
- **Se COMPILATO:** Usa tuo template personalizzato

**Esempio Template Personalizzato:**
```
Ciao Admin,

Hai ricevuto una nuova richiesta dal form "{form_title}".

DATI CLIENTE:
Nome: {nome}
Email: {email}
Telefono: {telefono}

MESSAGGIO:
{messaggio}

---
Ricevuto il {date} alle {time}
Da: {site_name}

Rispondi direttamente a questa email per contattare il cliente.
```

**Esempio Template Auto (default se vuoto):**
```
Hai ricevuto una nuova submission dal form "Contact Form"

--------------------------------------------------

Nome: Mario Rossi
Email: mario.rossi@example.com
Telefono: +39 333 1234567
Messaggio: Vorrei maggiori informazioni sui vostri servizi

--------------------------------------------------

Informazioni aggiuntive:
Data: 2025-11-05 23:45:00
IP: 192.168.1.1
Utente: (guest)
```

---

## 2️⃣ EMAIL CLIENTE (Conferma)

### **Sezione:** Email di Conferma (Cliente)

### **Campi Disponibili:**

#### **Checkbox Abilita**
```
☑️ Invia email di conferma all'utente

Se OFF: Email non inviata
Se ON: Email inviata automaticamente
```

#### **Oggetto Email Conferma** ⭐ PERSONALIZZABILE
```
Input: text
Default: "Conferma ricezione messaggio"

Tag Disponibili:
- Tutti i campi: {nome}, {email}, etc.
- Tag generali: {form_title}, {site_name}, {date}, {time}
```

**Esempi:**
```
"Grazie {nome}, abbiamo ricevuto la tua richiesta!"
"Conferma: il tuo messaggio a {site_name}"
"{nome}, ti risponderemo entro 24 ore"
```

#### **Messaggio Email Conferma** ⭐ PERSONALIZZABILE
```
Textarea: 3 righe
Default: "Grazie per averci contattato!"

Tag Disponibili:
- Tutti i campi del form
- Tag generali
```

**Esempi:**
```
Ciao {nome},

Grazie per averci contattato tramite il nostro sito {site_name}.

Abbiamo ricevuto la tua richiesta e ti risponderemo al più presto all'indirizzo {email}.

Se la tua richiesta è urgente, puoi chiamarci al +39 02 1234567.

Cordiali saluti,
Il Team di {site_name}
```

---

## 3️⃣ EMAIL STAFF (Team)

### **Sezione:** Notifiche Staff

### **Campi Disponibili:**

#### **Checkbox Abilita**
```
☑️ Invia notifica a membri dello staff/team

Se OFF: Email staff non inviate
Se ON: Email inviate a tutti gli indirizzi
```

#### **Email Staff (una per riga)**
```
Textarea: 4 righe
Formati supportati:
- Una per riga
- Separati da virgola
- Separati da punto e virgola

Esempio:
sales@example.com
support@example.com
team@example.com
```

#### **Oggetto Email Staff** ⭐ PERSONALIZZABILE
```
Input: text
Default (se vuoto): "[STAFF] Nuova submission: {form_title}"
Placeholder: [STAFF] Nuova submission: {form_title}

Tag Disponibili:
- Tutti i tag standard
```

**Esempi:**
```
"[VENDITE] Nuovo lead da {form_title}"
"🔔 {site_name}: Nuova richiesta da {nome}"
"[URGENTE] Contatto da {form_title} - {date}"
```

#### **Messaggio Email Staff** ⭐ PERSONALIZZABILE
```
Textarea: 5 righe
Default (se vuoto): Template automatico (come webmaster)

Tag Disponibili:
- Tutti i campi del form
- Tag generali
```

**Esempio Template Staff:**
```
🎯 NUOVO LEAD - AZIONE RICHIESTA

Cliente: {nome}
Email: {email}
Telefono: {telefono}

Richiesta:
{messaggio}

---
AZIONE CONSIGLIATA:
1. Rispondere entro 2 ore
2. Qualificare il lead
3. Schedulare follow-up

Form: {form_title}
Data: {date} {time}
```

---

## 🏷️ TAG DINAMICI COMPLETI

### **Tag Campi Form** (auto-generati)
Qualsiasi campo nel tuo form diventa un tag:

```
Campo "nome"      → Tag: {nome}
Campo "email"     → Tag: {email}
Campo "telefono"  → Tag: {telefono}
Campo "messaggio" → Tag: {messaggio}
Campo "azienda"   → Tag: {azienda}
Campo "budget"    → Tag: {budget}
... qualsiasi nome campo
```

### **Tag Generali** (sempre disponibili)

| Tag | Output Esempio | Descrizione |
|-----|----------------|-------------|
| `{form_title}` | "Contact Form" | Titolo del form |
| `{site_name}` | "Your Company" | Nome sito WordPress |
| `{site_url}` | "https://example.com" | URL sito |
| `{date}` | "05/11/2025" | Data formato locale |
| `{time}` | "23:45" | Ora formato locale |

### **Come Usare i Tag:**

**Esempio Oggetto:**
```
Input: "Nuova richiesta da {nome} - {form_title}"
Output: "Nuova richiesta da Mario Rossi - Contact Form"
```

**Esempio Messaggio:**
```
Input: 
"Ciao {nome},
Grazie per il tuo interesse in {site_name}.
Ti contatteremo all'indirizzo {email} entro 24 ore."

Output:
"Ciao Mario,
Grazie per il tuo interesse in Your Company.
Ti contatteremo all'indirizzo mario@example.com entro 24 ore."
```

---

## 📊 TEMPLATE EXAMPLES

### **Template 1: Webmaster Dettagliato**
```
NUOVA SUBMISSION - {form_title}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📋 DATI CLIENTE
Nome Completo: {nome} {cognome}
Email: {email}
Telefono: {telefono}
Azienda: {azienda}

💬 MESSAGGIO
{messaggio}

🎯 INFORMAZIONI RICHIESTE
Budget: {budget}
Scadenza: {scadenza}
Servizio: {servizio}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Ricevuto: {date} alle {time}
Sito: {site_name}

⚡ AZIONE IMMEDIATA RICHIESTA
```

### **Template 2: Cliente Formale**
```
Gentile {nome},

Grazie per aver contattato {site_name}.

La sua richiesta è stata ricevuta con successo e sarà esaminata dal nostro team entro 24 ore lavorative.

RIEPILOGO RICHIESTA:
- Servizio: {servizio}
- Budget indicativo: {budget}
- Email di contatto: {email}
- Telefono: {telefono}

Le invieremo una risposta dettagliata all'indirizzo email fornito.

Per qualsiasi urgenza, può contattarci al numero +39 02 1234567.

Cordiali saluti,
Il Team di {site_name}
```

### **Template 3: Staff Action-Oriented**
```
🚨 NUOVO LEAD - PRIORITÀ ALTA

Lead Info:
👤 {nome} ({email})
📞 {telefono}
🏢 {azienda}

Richiesta:
"{messaggio}"

💰 Budget: {budget}
⏰ Scadenza: {scadenza}

━━━━━━━━━━━━━━━━━
📋 NEXT STEPS:
1. ✅ Qualifica lead (entro 1h)
2. 📧 Invia proposta (entro 4h)
3. 📞 Follow-up call (entro 24h)

Assegnato a: Sales Team
Form: {form_title}
Data: {date} {time}
```

### **Template 4: Cliente Informale**
```
Ciao {nome}! 👋

Grazie mille per averci scritto!

Abbiamo ricevuto il tuo messaggio e siamo super entusiasti di aiutarti.

Ti risponderemo all'indirizzo {email} il prima possibile (di solito entro qualche ora).

Nel frattempo, se vuoi saperne di più, dai un'occhiata al nostro sito:
{site_url}

A prestissimo!
Il Team di {site_name} ✨
```

---

## 🎨 BEST PRACTICES

### **Email Webmaster:**
✅ **Usa template personalizzato se:**
- Vuoi formattazione specifica
- Hai workflow definiti
- Vuoi solo alcuni campi (non tutti)
- Serve format specifico per CRM/ticket system

✅ **Usa template automatico se:**
- Vuoi vedere TUTTI i campi
- Non hai requirement specifici
- Preferisci semplicità

### **Email Cliente:**
✅ **Sempre personalizza!**
- Tono professionale/informale in base al brand
- Aggiungi info utili (orari, contatti)
- Rassicura il cliente (risposta entro X ore)
- Personalizza con {nome} per tocco umano

### **Email Staff:**
✅ **Personalizza per workflow:**
- Aggiungi checklist azioni
- Includi priority/urgency
- Link a CRM/sistema tickets
- Istruzioni chiare next steps

---

## 🔧 CONFIGURAZIONE STEP-BY-STEP

### **Step 1: Apri Form Builder**
```
WP Admin → FP Forms → Click "Modifica" sul form
```

### **Step 2: Vai a Sidebar → Notifiche Email**
```
Scroll sidebar destra fino a "Notifiche Email"
```

### **Step 3: Personalizza Webmaster**
```
[Oggetto Email Webmaster]
Input: "🆕 Nuovo contatto da {form_title}"

[Messaggio Email Webmaster] ⭐ NUOVO!
Textarea: (Inserisci tuo template o lascia vuoto)

Esempio:
"Nuovo contatto ricevuto:
Nome: {nome}
Email: {email}
Messaggio: {messaggio}"
```

### **Step 4: Personalizza Cliente**
```
[✓] Invia email di conferma all'utente

[Oggetto]
Input: "Grazie {nome}, abbiamo ricevuto la tua richiesta"

[Messaggio]
Textarea: "Ciao {nome}, ti risponderemo a {email} entro 24h!"
```

### **Step 5: Personalizza Staff**
```
[✓] Invia notifica a staff

[Email Staff]
Textarea:
sales@example.com
support@example.com

[Oggetto]
Input: "[SALES] Nuovo lead: {nome} da {form_title}"

[Messaggio]
Textarea: "Lead qualificato da seguire. Budget: {budget}"
```

### **Step 6: Salva**
```
Click "Salva Form" in fondo alla sidebar
✅ "Form aggiornato!"
```

---

## 🎯 ESEMPI PER CASO D'USO

### **Contact Form Aziendale**

**Webmaster:**
```
Oggetto: Nuova richiesta informazioni - {form_title}

Messaggio:
NUOVO CONTATTO RICEVUTO

Cliente: {nome}
Email: {email}
Telefono: {telefono}
Azienda: {azienda}

Richiesta:
{messaggio}

Ricevuto: {date} alle {time}
```

**Cliente:**
```
Oggetto: Grazie per averci contattato

Messaggio:
Gentile {nome},

Abbiamo ricevuto la sua richiesta e la contatteremo entro 24 ore lavorative all'indirizzo {email}.

Cordiali saluti,
{site_name}
```

**Staff:**
```
Oggetto: [ACTION REQUIRED] Nuovo lead: {nome}

Messaggio:
Nuovo lead da qualificare:

Nome: {nome}
Email: {email}
Telefono: {telefono}
Budget: {budget}

AZIONI:
1. Rispondere entro 2 ore
2. Qualificare lead (A/B/C)
3. Inserire in CRM
```

---

### **E-commerce Lead Generation**

**Webmaster:**
```
Oggetto: 💰 Nuovo potenziale cliente da {form_title}

Messaggio:
LEAD E-COMMERCE

Cliente: {nome}
Email: {email}
Prodotto interesse: {prodotto}
Budget: {budget}

Note: {note}
```

**Cliente:**
```
Oggetto: {nome}, ecco il tuo sconto del 10%!

Messaggio:
Ciao {nome}!

Grazie per il tuo interesse in {prodotto}.

🎁 REGALO PER TE: Usa il codice WELCOME10 per il 10% di sconto!

Ti invieremo una proposta personalizzata a {email} entro oggi.

Shop now: {site_url}
```

**Staff:**
```
Oggetto: 🛒 Hot lead e-commerce: {nome} ({prodotto})

Messaggio:
LEAD CALDO - PRIORITÀ ALTA

Cliente: {nome}
Email: {email}
Prodotto: {prodotto}
Budget: {budget}

ACTION:
- Invia proposta entro 1h
- Follow up call entro 3h
- Chiudi vendita entro 24h

CRM: [Inserisci in "Hot Leads"]
```

---

### **Event Registration**

**Webmaster:**
```
Oggetto: ✅ Nuova iscrizione evento: {nome}

Messaggio:
Iscrizione confermata per {evento}

Partecipante: {nome}
Email: {email}
Numero partecipanti: {numero_persone}
Esigenze speciali: {note}
```

**Cliente:**
```
Oggetto: 🎉 Iscrizione confermata per {evento}!

Messaggio:
Ciao {nome}!

Sei ufficialmente iscritto a {evento}!

📅 Data: {data_evento}
🕐 Ora: {ora_evento}
📍 Luogo: {luogo_evento}

Ti invieremo un reminder 24 ore prima.

Ci vediamo lì!
Team {site_name}
```

**Staff:**
```
Oggetto: [EVENTO] Nuova iscrizione: {nome}

Messaggio:
Nuovo partecipante registrato:

Nome: {nome}
Email: {email}
N. Persone: {numero_persone}
Esigenze: {note}

TODO:
- Aggiungi a lista partecipanti
- Invia badge/QR code
- Reminder -24h
```

---

## 📝 TAG PERSONALIZZATI PER CAMPO

### **Campi Checkbox Multipli:**
```
Campo: "Interessi" (checkbox)
Values: ['Sport', 'Viaggi', 'Tecnologia']

Tag: {interessi}
Output: "Sport, Viaggi, Tecnologia"
```

### **Campi Select:**
```
Campo: "Servizio" (select)
Value: "Consulenza Premium"

Tag: {servizio}
Output: "Consulenza Premium"
```

### **Campi Privacy/Marketing:**
```
Campo: "privacy_consent" (privacy-checkbox)
Value: "1"

Tag: {privacy_consent}
Output: "1"

Consiglio: Non includere nei template (non utile)
```

---

## ⚙️ OPZIONI AVANZATE

### **HTML Email (Feature Request)**
```
Attualmente: Plain text
Richiesta: HTML templates

Workaround (ora):
Usa hook filter:

add_filter('fp_forms_email_message', function($message, $form_id) {
    // Convert to HTML
    return nl2br($message);
}, 10, 2);
```

### **Allegati Email**
```
File upload già supportati!

Template:
"Hai ricevuto {numero_files} file allegati."

Gli attachment sono gestiti automaticamente da FileField.
```

### **CC/BCC**
```
Attualmente: Invio separato a ogni staff email

Se vuoi CC/BCC:
Usa hook filter:

add_filter('fp_forms_email_headers', function($headers) {
    $headers[] = 'Cc: manager@example.com';
    return $headers;
}, 10, 1);
```

---

## ✅ VERIFICA FUNZIONALITÀ

**Ho aggiunto:**
- ✅ Campo `notification_message` (webmaster template custom)
- ✅ UI nel form builder (textarea 8 righe)
- ✅ Logic in `Email/Manager.php` (usa custom o auto)
- ✅ Save in `admin.js` (campo salvato)
- ✅ Tag replacement (funziona con tutti i tag)

**Ora hai:**
```
Email Webmaster:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile ⭐ NUOVO!
  
Email Cliente:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile
  
Email Staff:
  ✅ Oggetto personalizzabile
  ✅ Messaggio personalizzabile
```

---

## 🎯 COME TESTARE

**Test Template Custom:**

1. Crea form
2. Sidebar → Notifiche Email
3. **Messaggio Email Webmaster:**
   ```
   Test personalizzazione:
   
   Nome: {nome}
   Email: {email}
   Data: {date}
   ```
4. Salva form
5. Compila e invia in frontend
6. Check email ricevuta → ✅ Template personalizzato usato!

**Test Template Auto:**

1. Lascia campo "Messaggio Email Webmaster" **vuoto**
2. Salva
3. Submit form
4. Check email → ✅ Template automatico con tutti i campi!

---

## 🎉 **TUTTE E 3 LE EMAIL SONO ORA COMPLETAMENTE PERSONALIZZABILI!**

**Campi Aggiunti:**
- ✅ Messaggio Email Webmaster (nuovo campo)
- ✅ Logic usa custom o fallback auto
- ✅ Tag replacement completo
- ✅ Save in JavaScript
- ✅ UI con placeholder e help text

**Personalizzazione Totale:**
- **Webmaster:** Oggetto ✅ + Messaggio ✅
- **Cliente:** Oggetto ✅ + Messaggio ✅
- **Staff:** Oggetto ✅ + Messaggio ✅

**Tutto configurabile dal Form Builder senza toccare codice!** 🎉📧









