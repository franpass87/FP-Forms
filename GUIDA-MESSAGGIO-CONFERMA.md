# ✅ GUIDA: Messaggio di Conferma Post-Invio

**Versione:** v1.2.3  
**Feature:** Success Message Customization  
**Status:** ✅ **MIGLIORATO** (già esistente + tag dinamici + stili)

---

## 🎯 OVERVIEW

Il **messaggio di conferma** appare dopo l'invio del form per **rassicurare il cliente** che la submission è andata a buon fine.

**Personalizzazioni Disponibili:**
1. ✅ **Testo del Messaggio** (con tag dinamici)
2. ✅ **Tipo/Stile Visivo** (Successo, Info, Celebration)
3. ✅ **Durata Visualizzazione** (permanente o auto-hide)

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** WP Admin → FP Forms → Modifica Form → Sidebar

**Sezione:** Impostazioni Form → **Messaggio di Conferma**

---

## 📝 OPZIONI DISPONIBILI

### **1. Messaggio di Successo**
```
Campo: Textarea (4 righe)
Default: "Grazie! Il tuo messaggio è stato inviato con successo."
Placeholder: (same as default)
Tag Disponibili: {nome}, {email}, {form_title}, {site_name}, {date}, {time}
```

**Funzione:**
- Messaggio mostrato dopo submit riuscito
- Può includere tag dinamici sostituiti automaticamente
- Supporta testo multi-riga

**Esempi:**
```
Standard:
"Grazie! Il tuo messaggio è stato inviato con successo."

Personalizzato:
"Grazie {nome}! Abbiamo ricevuto la tua richiesta.
Ti risponderemo entro 24 ore all'indirizzo {email}."

Con brand:
"Grazie per aver contattato {site_name}!
Elaboreremo la tua richiesta il prima possibile."

Specifico:
"Perfetto {nome}! La tua iscrizione a {form_title} è confermata.
Ti abbiamo inviato una email di conferma a {email}."
```

---

### **2. Tipo Messaggio** ⭐ NUOVO!
```
Campo: Select dropdown
Default: ✓ Successo (verde)
Opzioni:
- ✓ Successo (verde) - Classico, professionale
- ℹ️ Info (blu) - Informativo, neutrale
- 🎉 Celebration (festoso) - Celebrativo, enthusiastic
```

**Varianti Visive:**

#### **✓ Successo (verde)** - Default
```
Background: Verde chiaro (#d1fae5)
Testo: Verde scuro (#065f46)
Border: Verde (#10b981)
Icona: ✓ check mark
```
**Quando usare:** Form contatti, richieste info, form standard

#### **ℹ️ Info (blu)**
```
Background: Blu chiaro (#dbeafe)
Testo: Blu scuro (#1e40af)
Border: Blu (#3b82f6)
Icona: ℹ️ info symbol
```
**Quando usare:** Form informativi, newsletter signup, download

#### **🎉 Celebration (festoso)**
```
Background: Gradient giallo (#fef3c7 → #fde68a)
Testo: Marrone scuro (#92400e)
Border: Arancione (#f59e0b)
Icona: 🎉 celebration
Font-weight: 600 (bold)
```
**Quando usare:** Registrazioni, contest, giveaway, milestone events

---

### **3. Durata Visualizzazione** ⭐ NUOVO!
```
Campo: Select dropdown
Default: Sempre visibile
Opzioni:
- Sempre visibile (0)
- 3 secondi (3000ms)
- 5 secondi (5000ms)
- 10 secondi (10000ms)
```

**Comportamento:**
- **Sempre visibile:** Messaggio resta finché utente non ricarica pagina
- **Auto-hide:** Messaggio scompare (fade out) dopo X secondi

**Quando usare:**
- **Sempre visibile:** Form importanti, serve conferma visiva persistente
- **3 secondi:** Quick feedback, form semplici
- **5 secondi:** Standard, equilibrio perfetto
- **10 secondi:** Form complessi, voglio che utente legga bene

---

## 🏷️ TAG DINAMICI

### **Tag Campi Form** (auto-generati)
Qualsiasi campo nel tuo form diventa un tag:

```
Campo "nome"      → Tag: {nome}
Campo "email"     → Tag: {email}
Campo "telefono"  → Tag: {telefono}
Campo "azienda"   → Tag: {azienda}
... qualsiasi nome campo
```

### **Tag Generali** (sempre disponibili)

| Tag | Output Esempio | Descrizione |
|-----|----------------|-------------|
| `{form_title}` | "Contact Form" | Titolo del form |
| `{site_name}` | "Your Company" | Nome sito WordPress |
| `{site_url}` | "https://example.com" | URL sito |
| `{date}` | "05/11/2025" | Data corrente (formato WP) |
| `{time}` | "23:45" | Ora corrente (formato WP) |

### **Come Usare i Tag:**

**Input (configurazione):**
```
"Grazie {nome}! 
Abbiamo ricevuto la tua richiesta il {date} alle {time}.
Ti risponderemo a {email} entro 24 ore.

Cordiali saluti,
Il team di {site_name}"
```

**Output (dopo submit con nome="Mario" email="mario@example.com"):**
```
Grazie Mario! 
Abbiamo ricevuto la tua richiesta il 05/11/2025 alle 23:45.
Ti risponderemo a mario@example.com entro 24 ore.

Cordiali saluti,
Il team di Your Company
```

---

## 🎨 ESEMPI CONFIGURAZIONI COMPLETE

### **Esempio 1: Form Contatti Standard**
```
Messaggio:
"Grazie {nome}! Il tuo messaggio è stato inviato con successo.
Ti risponderemo entro 24 ore all'indirizzo {email}."

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Box verde con check, messaggio personalizzato, resta visibile

---

### **Esempio 2: Newsletter Signup**
```
Messaggio:
"Perfetto! Sei iscritto alla newsletter di {site_name}.
Controlla {email} per confermare l'iscrizione."

Tipo: ℹ️ Info (blu)
Durata: 5 secondi
```
**Risultato:** Box blu informativo, scompare dopo 5 secondi

---

### **Esempio 3: Contest Entry**
```
Messaggio:
"🎉 {nome}, sei ufficialmente iscritto al contest!
Riceverai aggiornamenti a {email}.
In bocca al lupo!"

Tipo: 🎉 Celebration (festoso)
Durata: 10 secondi
```
**Risultato:** Box gradient giallo festoso, bold, celebration emoji

---

### **Esempio 4: Download Lead Magnet**
```
Messaggio:
"Grazie {nome}! 
La tua guida è in arrivo a {email}.
Se non la vedi, controlla lo spam!"

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Successo con istruzioni chiare

---

### **Esempio 5: Appointment Booking**
```
Messaggio:
"Perfetto {nome}!
Il tuo appuntamento per {data_evento} alle {ora_evento} è confermato.
Riceverai un reminder 24h prima a {email}."

Tipo: ℹ️ Info (blu)
Durata: Sempre visibile
```
**Risultato:** Conferma dettagliata con info evento

---

### **Esempio 6: Job Application**
```
Messaggio:
"Candidatura ricevuta!
Grazie {nome} per l'interesse nella posizione di {posizione}.
Il nostro team HR esaminerà il tuo CV e ti contatterà a {email} entro 7 giorni.

In bocca al lupo! 🍀"

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Conferma professionale con timeline

---

## 📱 RENDERING FRONTEND

### **HTML Generato:**
```html
<div class="fp-forms-messages">
    <div class="fp-forms-message fp-forms-success fp-msg-success" 
         style="display:none;">
        <!-- Messaggio inserito qui via JavaScript -->
    </div>
</div>
```

### **JavaScript Behavior:**
```javascript
// Dopo submit success
$('.fp-forms-success')
    .removeClass('fp-msg-success fp-msg-info fp-msg-celebration')
    .addClass('fp-msg-' + messageType)
    .text(response.data.message)
    .fadeIn();

// Auto-hide (se configurato)
if (duration > 0) {
    setTimeout(function() {
        $('.fp-forms-success').fadeOut();
    }, duration);
}
```

### **CSS Classes:**
```css
.fp-forms-success                /* Base */
.fp-msg-success                 /* Verde */
.fp-msg-info                    /* Blu */
.fp-msg-celebration             /* Festoso */
```

---

## 🎯 BEST PRACTICES

### **Messaggi Efficaci:**
```
✅ BUONO:
- "Grazie {nome}! Ti risponderemo entro 24 ore."
- "Iscrizione confermata! Check {email} per il link."
- "Candidatura ricevuta! Ti contatteremo presto."

❌ CATTIVO:
- "OK" (troppo corto, non rassicurante)
- "Form inviato" (generico, freddo)
- "Grazie per aver compilato il modulo di contatto del sito example.com nella sezione contatti..." (troppo lungo)
```

### **Personalizzazione:**
```
✅ Usa il nome: "Grazie {nome}!"
✅ Conferma email: "...a {email}"
✅ Specifica timing: "entro 24 ore"
✅ Aggiungi istruzioni: "controlla spam"
✅ Rassicura: "i tuoi dati sono al sicuro"

❌ Non ripetere ovvietà
❌ Non essere troppo formale se brand è casual
❌ Non promettere tempi irrealistici
```

### **Tipo Messaggio:**
```
Successo (verde): Default, professionale, affidabile
→ Form contatti, richieste, ordini

Info (blu): Neutrale, informativo
→ Newsletter, download, info requests

Celebration (festoso): Enthusiastic, emozionale
→ Contest, giveaway, milestone, registrazioni speciali
```

### **Durata:**
```
Sempre visibile: 
→ Form importanti (ordini, appuntamenti, job applications)
→ Messaggi con istruzioni da leggere

3 secondi:
→ Form semplici (newsletter)
→ Quick feedback

5 secondi (recommended):
→ Equilibrio perfetto
→ Abbastanza per leggere, non invasivo

10 secondi:
→ Messaggi lunghi
→ Form complessi
→ Vuoi che utente legga tutto
```

---

## 🔧 IMPLEMENTAZIONE TECNICA

### **Database Storage:**
```json
{
  "settings": {
    "success_message": "Grazie {nome}! Ti risponderemo a {email}.",
    "success_message_type": "success",
    "success_message_duration": "5000"
  }
}
```

### **Backend Processing (PHP):**
```php
// In Manager.php
$success_message = $form['settings']['success_message'] ?? 'Default...';

// Replace tag dinamici
$success_message = $this->replace_success_tags( $success_message, $form, $data );

// Tag replacement
$message = str_replace( '{nome}', $data['nome'], $message );
$message = str_replace( '{email}', $data['email'], $message );
$message = str_replace( '{form_title}', $form['title'], $message );
// ... etc
```

### **Response JSON:**
```json
{
  "success": true,
  "message": "Grazie Mario! Ti risponderemo a mario@example.com.",
  "message_type": "success",
  "message_duration": 5000,
  "submission_id": 123
}
```

### **Frontend Display:**
```javascript
$('.fp-forms-success')
    .addClass('fp-msg-success')
    .text('Grazie Mario! Ti risponderemo a mario@example.com.')
    .fadeIn();

setTimeout(() => {
    $('.fp-forms-success').fadeOut();
}, 5000);
```

---

## 🎨 CSS STYLING

### **Success (verde):**
```css
.fp-forms-success.fp-msg-success {
    background: #d1fae5;
    color: #065f46;
    border: 2px solid #10b981;
}

.fp-forms-success.fp-msg-success::before {
    content: '✓ ';
    font-weight: bold;
}
```

### **Info (blu):**
```css
.fp-forms-success.fp-msg-info {
    background: #dbeafe;
    color: #1e40af;
    border: 2px solid #3b82f6;
}

.fp-forms-success.fp-msg-info::before {
    content: 'ℹ️ ';
}
```

### **Celebration (festoso):**
```css
.fp-forms-success.fp-msg-celebration {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 2px solid #f59e0b;
    font-weight: 600;
}

.fp-forms-success.fp-msg-celebration::before {
    content: '🎉 ';
}
```

---

## 🚀 WORKFLOW COMPLETO

**1. Configurazione (Admin):**
```
→ Form Builder → Sidebar → Messaggio di Conferma
→ Inserisci messaggio con tag
→ Scegli tipo (success/info/celebration)
→ Scegli durata (0/3000/5000/10000)
→ Salva Form
```

**2. Frontend Submit:**
```
Utente compila:
- nome: "Mario"
- email: "mario@example.com"
→ Click "Invia"
```

**3. Backend Processing:**
```
PHP (Manager.php):
→ Valida dati
→ Salva submission
→ Ottieni success_message da settings
→ Replace tag:
  "{nome}" → "Mario"
  "{email}" → "mario@example.com"
  "{form_title}" → "Contact Form"
→ Return JSON response
```

**4. Frontend Display:**
```
JavaScript (frontend.js):
→ Riceve response JSON
→ Estrae message, message_type, message_duration
→ Applica classe CSS (.fp-msg-success)
→ Mostra messaggio (fadeIn animation)
→ Se duration > 0: auto-hide dopo X ms
→ Scroll al messaggio
→ Reset form
```

**5. User Experience:**
```
Utente vede:
✓ "Grazie Mario! Ti risponderemo a mario@example.com entro 24h."

Box verde con check mark
Se durata 5s → scompare dopo 5 secondi
Altrimenti → resta visibile
```

---

## 📊 ESEMPI PER SETTORI

### **E-commerce:**
```
"Grazie {nome}!
Il tuo ordine #{order_id} è stato ricevuto.
Riceverai conferma a {email} entro pochi minuti.

Totale: {total}€"

Tipo: Success
Durata: Sempre visibile
```

### **SaaS/Software:**
```
"Welcome aboard {nome}! 🚀
Il tuo account {site_name} è attivo.
Abbiamo inviato le credenziali a {email}.

Inizia subito!"

Tipo: Celebration
Durata: 10 secondi
```

### **Healthcare:**
```
"Appuntamento confermato.

Paziente: {nome}
Data: {data_appuntamento}
Ora: {ora_appuntamento}
Medico: {medico}

Riceverai un reminder 24h prima a {email}."

Tipo: Info
Durata: Sempre visibile
```

### **Education:**
```
"Iscrizione completata! 📚

Benvenuto {nome} al corso {corso}.
Inizio: {data_inizio}
Aula: {aula}

Ti abbiamo inviato i materiali a {email}.
Ci vediamo in aula!"

Tipo: Celebration
Durata: Sempre visibile
```

### **Real Estate:**
```
"Richiesta informazioni ricevuta.

Immobile: {immobile}
Cliente: {nome}
Email: {email}
Telefono: {telefono}

Un nostro agente ti contatterà entro 2 ore lavorative."

Tipo: Success
Durata: Sempre visibile
```

---

## ⚙️ OPZIONI AVANZATE

### **Redirect dopo Successo:**
```
Se hai abilitato "Redirect dopo invio":
→ Messaggio NON viene mostrato
→ Utente reindirizzato a thank-you page
→ Mostra messaggio sulla thank-you page invece

Best Practice:
- Form semplici: Messaggio inline
- Form complessi: Redirect a thank-you page
```

### **Multi-lingua:**
```
Per siti multilingua:
1. Usa WPML/Polylang
2. Traduci il messaggio
3. Tag vengono sostituiti in tutte le lingue

Oppure:
→ Crea form separati per lingua
```

### **Custom CSS:**
```
Per styling custom:

.fp-forms-success.my-custom-success {
    background: your-color;
    border: your-border;
    font-size: your-size;
}

Aggiungi classe in Custom CSS Class field
```

---

## ✅ CHECKLIST PRE-PUBBLICAZIONE

**Prima di pubblicare il form:**

- [ ] ✅ Messaggio scritto e chiaro
- [ ] ✅ Tag dinamici testati
- [ ] ✅ Nome campi corrispondono ai tag
- [ ] ✅ Tipo messaggio appropriato per contesto
- [ ] ✅ Durata impostata correttamente
- [ ] ✅ Test submit su desktop
- [ ] ✅ Test submit su mobile
- [ ] ✅ Verifica tag replacement
- [ ] ✅ Verifica styling/colori
- [ ] ✅ Verifica timing auto-hide
- [ ] ✅ Verifica scroll al messaggio
- [ ] ✅ Tono di voce coerente con brand

---

## 🎉 CONCLUSIONE

**Messaggio di Conferma: 100% Personalizzabile!**

**3 Opzioni:**
1. ✅ Testo con tag dinamici
2. ✅ Tipo/stile visivo (3 varianti)
3. ✅ Durata visualizzazione (4 opzioni)

**Tag Disponibili:**
- {nome_campo} per ogni campo del form
- {form_title}, {site_name}, {site_url}
- {date}, {time}

**Stili:**
- ✓ Success (verde) - professionale
- ℹ️ Info (blu) - informativo
- 🎉 Celebration (festoso) - celebrativo

**Durata:**
- Sempre visibile
- 3 / 5 / 10 secondi auto-hide

**No Code Required - Tutto dalla UI! ✅🎨**


**Versione:** v1.2.3  
**Feature:** Success Message Customization  
**Status:** ✅ **MIGLIORATO** (già esistente + tag dinamici + stili)

---

## 🎯 OVERVIEW

Il **messaggio di conferma** appare dopo l'invio del form per **rassicurare il cliente** che la submission è andata a buon fine.

**Personalizzazioni Disponibili:**
1. ✅ **Testo del Messaggio** (con tag dinamici)
2. ✅ **Tipo/Stile Visivo** (Successo, Info, Celebration)
3. ✅ **Durata Visualizzazione** (permanente o auto-hide)

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** WP Admin → FP Forms → Modifica Form → Sidebar

**Sezione:** Impostazioni Form → **Messaggio di Conferma**

---

## 📝 OPZIONI DISPONIBILI

### **1. Messaggio di Successo**
```
Campo: Textarea (4 righe)
Default: "Grazie! Il tuo messaggio è stato inviato con successo."
Placeholder: (same as default)
Tag Disponibili: {nome}, {email}, {form_title}, {site_name}, {date}, {time}
```

**Funzione:**
- Messaggio mostrato dopo submit riuscito
- Può includere tag dinamici sostituiti automaticamente
- Supporta testo multi-riga

**Esempi:**
```
Standard:
"Grazie! Il tuo messaggio è stato inviato con successo."

Personalizzato:
"Grazie {nome}! Abbiamo ricevuto la tua richiesta.
Ti risponderemo entro 24 ore all'indirizzo {email}."

Con brand:
"Grazie per aver contattato {site_name}!
Elaboreremo la tua richiesta il prima possibile."

Specifico:
"Perfetto {nome}! La tua iscrizione a {form_title} è confermata.
Ti abbiamo inviato una email di conferma a {email}."
```

---

### **2. Tipo Messaggio** ⭐ NUOVO!
```
Campo: Select dropdown
Default: ✓ Successo (verde)
Opzioni:
- ✓ Successo (verde) - Classico, professionale
- ℹ️ Info (blu) - Informativo, neutrale
- 🎉 Celebration (festoso) - Celebrativo, enthusiastic
```

**Varianti Visive:**

#### **✓ Successo (verde)** - Default
```
Background: Verde chiaro (#d1fae5)
Testo: Verde scuro (#065f46)
Border: Verde (#10b981)
Icona: ✓ check mark
```
**Quando usare:** Form contatti, richieste info, form standard

#### **ℹ️ Info (blu)**
```
Background: Blu chiaro (#dbeafe)
Testo: Blu scuro (#1e40af)
Border: Blu (#3b82f6)
Icona: ℹ️ info symbol
```
**Quando usare:** Form informativi, newsletter signup, download

#### **🎉 Celebration (festoso)**
```
Background: Gradient giallo (#fef3c7 → #fde68a)
Testo: Marrone scuro (#92400e)
Border: Arancione (#f59e0b)
Icona: 🎉 celebration
Font-weight: 600 (bold)
```
**Quando usare:** Registrazioni, contest, giveaway, milestone events

---

### **3. Durata Visualizzazione** ⭐ NUOVO!
```
Campo: Select dropdown
Default: Sempre visibile
Opzioni:
- Sempre visibile (0)
- 3 secondi (3000ms)
- 5 secondi (5000ms)
- 10 secondi (10000ms)
```

**Comportamento:**
- **Sempre visibile:** Messaggio resta finché utente non ricarica pagina
- **Auto-hide:** Messaggio scompare (fade out) dopo X secondi

**Quando usare:**
- **Sempre visibile:** Form importanti, serve conferma visiva persistente
- **3 secondi:** Quick feedback, form semplici
- **5 secondi:** Standard, equilibrio perfetto
- **10 secondi:** Form complessi, voglio che utente legga bene

---

## 🏷️ TAG DINAMICI

### **Tag Campi Form** (auto-generati)
Qualsiasi campo nel tuo form diventa un tag:

```
Campo "nome"      → Tag: {nome}
Campo "email"     → Tag: {email}
Campo "telefono"  → Tag: {telefono}
Campo "azienda"   → Tag: {azienda}
... qualsiasi nome campo
```

### **Tag Generali** (sempre disponibili)

| Tag | Output Esempio | Descrizione |
|-----|----------------|-------------|
| `{form_title}` | "Contact Form" | Titolo del form |
| `{site_name}` | "Your Company" | Nome sito WordPress |
| `{site_url}` | "https://example.com" | URL sito |
| `{date}` | "05/11/2025" | Data corrente (formato WP) |
| `{time}` | "23:45" | Ora corrente (formato WP) |

### **Come Usare i Tag:**

**Input (configurazione):**
```
"Grazie {nome}! 
Abbiamo ricevuto la tua richiesta il {date} alle {time}.
Ti risponderemo a {email} entro 24 ore.

Cordiali saluti,
Il team di {site_name}"
```

**Output (dopo submit con nome="Mario" email="mario@example.com"):**
```
Grazie Mario! 
Abbiamo ricevuto la tua richiesta il 05/11/2025 alle 23:45.
Ti risponderemo a mario@example.com entro 24 ore.

Cordiali saluti,
Il team di Your Company
```

---

## 🎨 ESEMPI CONFIGURAZIONI COMPLETE

### **Esempio 1: Form Contatti Standard**
```
Messaggio:
"Grazie {nome}! Il tuo messaggio è stato inviato con successo.
Ti risponderemo entro 24 ore all'indirizzo {email}."

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Box verde con check, messaggio personalizzato, resta visibile

---

### **Esempio 2: Newsletter Signup**
```
Messaggio:
"Perfetto! Sei iscritto alla newsletter di {site_name}.
Controlla {email} per confermare l'iscrizione."

Tipo: ℹ️ Info (blu)
Durata: 5 secondi
```
**Risultato:** Box blu informativo, scompare dopo 5 secondi

---

### **Esempio 3: Contest Entry**
```
Messaggio:
"🎉 {nome}, sei ufficialmente iscritto al contest!
Riceverai aggiornamenti a {email}.
In bocca al lupo!"

Tipo: 🎉 Celebration (festoso)
Durata: 10 secondi
```
**Risultato:** Box gradient giallo festoso, bold, celebration emoji

---

### **Esempio 4: Download Lead Magnet**
```
Messaggio:
"Grazie {nome}! 
La tua guida è in arrivo a {email}.
Se non la vedi, controlla lo spam!"

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Successo con istruzioni chiare

---

### **Esempio 5: Appointment Booking**
```
Messaggio:
"Perfetto {nome}!
Il tuo appuntamento per {data_evento} alle {ora_evento} è confermato.
Riceverai un reminder 24h prima a {email}."

Tipo: ℹ️ Info (blu)
Durata: Sempre visibile
```
**Risultato:** Conferma dettagliata con info evento

---

### **Esempio 6: Job Application**
```
Messaggio:
"Candidatura ricevuta!
Grazie {nome} per l'interesse nella posizione di {posizione}.
Il nostro team HR esaminerà il tuo CV e ti contatterà a {email} entro 7 giorni.

In bocca al lupo! 🍀"

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Conferma professionale con timeline

---

## 📱 RENDERING FRONTEND

### **HTML Generato:**
```html
<div class="fp-forms-messages">
    <div class="fp-forms-message fp-forms-success fp-msg-success" 
         style="display:none;">
        <!-- Messaggio inserito qui via JavaScript -->
    </div>
</div>
```

### **JavaScript Behavior:**
```javascript
// Dopo submit success
$('.fp-forms-success')
    .removeClass('fp-msg-success fp-msg-info fp-msg-celebration')
    .addClass('fp-msg-' + messageType)
    .text(response.data.message)
    .fadeIn();

// Auto-hide (se configurato)
if (duration > 0) {
    setTimeout(function() {
        $('.fp-forms-success').fadeOut();
    }, duration);
}
```

### **CSS Classes:**
```css
.fp-forms-success                /* Base */
.fp-msg-success                 /* Verde */
.fp-msg-info                    /* Blu */
.fp-msg-celebration             /* Festoso */
```

---

## 🎯 BEST PRACTICES

### **Messaggi Efficaci:**
```
✅ BUONO:
- "Grazie {nome}! Ti risponderemo entro 24 ore."
- "Iscrizione confermata! Check {email} per il link."
- "Candidatura ricevuta! Ti contatteremo presto."

❌ CATTIVO:
- "OK" (troppo corto, non rassicurante)
- "Form inviato" (generico, freddo)
- "Grazie per aver compilato il modulo di contatto del sito example.com nella sezione contatti..." (troppo lungo)
```

### **Personalizzazione:**
```
✅ Usa il nome: "Grazie {nome}!"
✅ Conferma email: "...a {email}"
✅ Specifica timing: "entro 24 ore"
✅ Aggiungi istruzioni: "controlla spam"
✅ Rassicura: "i tuoi dati sono al sicuro"

❌ Non ripetere ovvietà
❌ Non essere troppo formale se brand è casual
❌ Non promettere tempi irrealistici
```

### **Tipo Messaggio:**
```
Successo (verde): Default, professionale, affidabile
→ Form contatti, richieste, ordini

Info (blu): Neutrale, informativo
→ Newsletter, download, info requests

Celebration (festoso): Enthusiastic, emozionale
→ Contest, giveaway, milestone, registrazioni speciali
```

### **Durata:**
```
Sempre visibile: 
→ Form importanti (ordini, appuntamenti, job applications)
→ Messaggi con istruzioni da leggere

3 secondi:
→ Form semplici (newsletter)
→ Quick feedback

5 secondi (recommended):
→ Equilibrio perfetto
→ Abbastanza per leggere, non invasivo

10 secondi:
→ Messaggi lunghi
→ Form complessi
→ Vuoi che utente legga tutto
```

---

## 🔧 IMPLEMENTAZIONE TECNICA

### **Database Storage:**
```json
{
  "settings": {
    "success_message": "Grazie {nome}! Ti risponderemo a {email}.",
    "success_message_type": "success",
    "success_message_duration": "5000"
  }
}
```

### **Backend Processing (PHP):**
```php
// In Manager.php
$success_message = $form['settings']['success_message'] ?? 'Default...';

// Replace tag dinamici
$success_message = $this->replace_success_tags( $success_message, $form, $data );

// Tag replacement
$message = str_replace( '{nome}', $data['nome'], $message );
$message = str_replace( '{email}', $data['email'], $message );
$message = str_replace( '{form_title}', $form['title'], $message );
// ... etc
```

### **Response JSON:**
```json
{
  "success": true,
  "message": "Grazie Mario! Ti risponderemo a mario@example.com.",
  "message_type": "success",
  "message_duration": 5000,
  "submission_id": 123
}
```

### **Frontend Display:**
```javascript
$('.fp-forms-success')
    .addClass('fp-msg-success')
    .text('Grazie Mario! Ti risponderemo a mario@example.com.')
    .fadeIn();

setTimeout(() => {
    $('.fp-forms-success').fadeOut();
}, 5000);
```

---

## 🎨 CSS STYLING

### **Success (verde):**
```css
.fp-forms-success.fp-msg-success {
    background: #d1fae5;
    color: #065f46;
    border: 2px solid #10b981;
}

.fp-forms-success.fp-msg-success::before {
    content: '✓ ';
    font-weight: bold;
}
```

### **Info (blu):**
```css
.fp-forms-success.fp-msg-info {
    background: #dbeafe;
    color: #1e40af;
    border: 2px solid #3b82f6;
}

.fp-forms-success.fp-msg-info::before {
    content: 'ℹ️ ';
}
```

### **Celebration (festoso):**
```css
.fp-forms-success.fp-msg-celebration {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 2px solid #f59e0b;
    font-weight: 600;
}

.fp-forms-success.fp-msg-celebration::before {
    content: '🎉 ';
}
```

---

## 🚀 WORKFLOW COMPLETO

**1. Configurazione (Admin):**
```
→ Form Builder → Sidebar → Messaggio di Conferma
→ Inserisci messaggio con tag
→ Scegli tipo (success/info/celebration)
→ Scegli durata (0/3000/5000/10000)
→ Salva Form
```

**2. Frontend Submit:**
```
Utente compila:
- nome: "Mario"
- email: "mario@example.com"
→ Click "Invia"
```

**3. Backend Processing:**
```
PHP (Manager.php):
→ Valida dati
→ Salva submission
→ Ottieni success_message da settings
→ Replace tag:
  "{nome}" → "Mario"
  "{email}" → "mario@example.com"
  "{form_title}" → "Contact Form"
→ Return JSON response
```

**4. Frontend Display:**
```
JavaScript (frontend.js):
→ Riceve response JSON
→ Estrae message, message_type, message_duration
→ Applica classe CSS (.fp-msg-success)
→ Mostra messaggio (fadeIn animation)
→ Se duration > 0: auto-hide dopo X ms
→ Scroll al messaggio
→ Reset form
```

**5. User Experience:**
```
Utente vede:
✓ "Grazie Mario! Ti risponderemo a mario@example.com entro 24h."

Box verde con check mark
Se durata 5s → scompare dopo 5 secondi
Altrimenti → resta visibile
```

---

## 📊 ESEMPI PER SETTORI

### **E-commerce:**
```
"Grazie {nome}!
Il tuo ordine #{order_id} è stato ricevuto.
Riceverai conferma a {email} entro pochi minuti.

Totale: {total}€"

Tipo: Success
Durata: Sempre visibile
```

### **SaaS/Software:**
```
"Welcome aboard {nome}! 🚀
Il tuo account {site_name} è attivo.
Abbiamo inviato le credenziali a {email}.

Inizia subito!"

Tipo: Celebration
Durata: 10 secondi
```

### **Healthcare:**
```
"Appuntamento confermato.

Paziente: {nome}
Data: {data_appuntamento}
Ora: {ora_appuntamento}
Medico: {medico}

Riceverai un reminder 24h prima a {email}."

Tipo: Info
Durata: Sempre visibile
```

### **Education:**
```
"Iscrizione completata! 📚

Benvenuto {nome} al corso {corso}.
Inizio: {data_inizio}
Aula: {aula}

Ti abbiamo inviato i materiali a {email}.
Ci vediamo in aula!"

Tipo: Celebration
Durata: Sempre visibile
```

### **Real Estate:**
```
"Richiesta informazioni ricevuta.

Immobile: {immobile}
Cliente: {nome}
Email: {email}
Telefono: {telefono}

Un nostro agente ti contatterà entro 2 ore lavorative."

Tipo: Success
Durata: Sempre visibile
```

---

## ⚙️ OPZIONI AVANZATE

### **Redirect dopo Successo:**
```
Se hai abilitato "Redirect dopo invio":
→ Messaggio NON viene mostrato
→ Utente reindirizzato a thank-you page
→ Mostra messaggio sulla thank-you page invece

Best Practice:
- Form semplici: Messaggio inline
- Form complessi: Redirect a thank-you page
```

### **Multi-lingua:**
```
Per siti multilingua:
1. Usa WPML/Polylang
2. Traduci il messaggio
3. Tag vengono sostituiti in tutte le lingue

Oppure:
→ Crea form separati per lingua
```

### **Custom CSS:**
```
Per styling custom:

.fp-forms-success.my-custom-success {
    background: your-color;
    border: your-border;
    font-size: your-size;
}

Aggiungi classe in Custom CSS Class field
```

---

## ✅ CHECKLIST PRE-PUBBLICAZIONE

**Prima di pubblicare il form:**

- [ ] ✅ Messaggio scritto e chiaro
- [ ] ✅ Tag dinamici testati
- [ ] ✅ Nome campi corrispondono ai tag
- [ ] ✅ Tipo messaggio appropriato per contesto
- [ ] ✅ Durata impostata correttamente
- [ ] ✅ Test submit su desktop
- [ ] ✅ Test submit su mobile
- [ ] ✅ Verifica tag replacement
- [ ] ✅ Verifica styling/colori
- [ ] ✅ Verifica timing auto-hide
- [ ] ✅ Verifica scroll al messaggio
- [ ] ✅ Tono di voce coerente con brand

---

## 🎉 CONCLUSIONE

**Messaggio di Conferma: 100% Personalizzabile!**

**3 Opzioni:**
1. ✅ Testo con tag dinamici
2. ✅ Tipo/stile visivo (3 varianti)
3. ✅ Durata visualizzazione (4 opzioni)

**Tag Disponibili:**
- {nome_campo} per ogni campo del form
- {form_title}, {site_name}, {site_url}
- {date}, {time}

**Stili:**
- ✓ Success (verde) - professionale
- ℹ️ Info (blu) - informativo
- 🎉 Celebration (festoso) - celebrativo

**Durata:**
- Sempre visibile
- 3 / 5 / 10 secondi auto-hide

**No Code Required - Tutto dalla UI! ✅🎨**


**Versione:** v1.2.3  
**Feature:** Success Message Customization  
**Status:** ✅ **MIGLIORATO** (già esistente + tag dinamici + stili)

---

## 🎯 OVERVIEW

Il **messaggio di conferma** appare dopo l'invio del form per **rassicurare il cliente** che la submission è andata a buon fine.

**Personalizzazioni Disponibili:**
1. ✅ **Testo del Messaggio** (con tag dinamici)
2. ✅ **Tipo/Stile Visivo** (Successo, Info, Celebration)
3. ✅ **Durata Visualizzazione** (permanente o auto-hide)

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** WP Admin → FP Forms → Modifica Form → Sidebar

**Sezione:** Impostazioni Form → **Messaggio di Conferma**

---

## 📝 OPZIONI DISPONIBILI

### **1. Messaggio di Successo**
```
Campo: Textarea (4 righe)
Default: "Grazie! Il tuo messaggio è stato inviato con successo."
Placeholder: (same as default)
Tag Disponibili: {nome}, {email}, {form_title}, {site_name}, {date}, {time}
```

**Funzione:**
- Messaggio mostrato dopo submit riuscito
- Può includere tag dinamici sostituiti automaticamente
- Supporta testo multi-riga

**Esempi:**
```
Standard:
"Grazie! Il tuo messaggio è stato inviato con successo."

Personalizzato:
"Grazie {nome}! Abbiamo ricevuto la tua richiesta.
Ti risponderemo entro 24 ore all'indirizzo {email}."

Con brand:
"Grazie per aver contattato {site_name}!
Elaboreremo la tua richiesta il prima possibile."

Specifico:
"Perfetto {nome}! La tua iscrizione a {form_title} è confermata.
Ti abbiamo inviato una email di conferma a {email}."
```

---

### **2. Tipo Messaggio** ⭐ NUOVO!
```
Campo: Select dropdown
Default: ✓ Successo (verde)
Opzioni:
- ✓ Successo (verde) - Classico, professionale
- ℹ️ Info (blu) - Informativo, neutrale
- 🎉 Celebration (festoso) - Celebrativo, enthusiastic
```

**Varianti Visive:**

#### **✓ Successo (verde)** - Default
```
Background: Verde chiaro (#d1fae5)
Testo: Verde scuro (#065f46)
Border: Verde (#10b981)
Icona: ✓ check mark
```
**Quando usare:** Form contatti, richieste info, form standard

#### **ℹ️ Info (blu)**
```
Background: Blu chiaro (#dbeafe)
Testo: Blu scuro (#1e40af)
Border: Blu (#3b82f6)
Icona: ℹ️ info symbol
```
**Quando usare:** Form informativi, newsletter signup, download

#### **🎉 Celebration (festoso)**
```
Background: Gradient giallo (#fef3c7 → #fde68a)
Testo: Marrone scuro (#92400e)
Border: Arancione (#f59e0b)
Icona: 🎉 celebration
Font-weight: 600 (bold)
```
**Quando usare:** Registrazioni, contest, giveaway, milestone events

---

### **3. Durata Visualizzazione** ⭐ NUOVO!
```
Campo: Select dropdown
Default: Sempre visibile
Opzioni:
- Sempre visibile (0)
- 3 secondi (3000ms)
- 5 secondi (5000ms)
- 10 secondi (10000ms)
```

**Comportamento:**
- **Sempre visibile:** Messaggio resta finché utente non ricarica pagina
- **Auto-hide:** Messaggio scompare (fade out) dopo X secondi

**Quando usare:**
- **Sempre visibile:** Form importanti, serve conferma visiva persistente
- **3 secondi:** Quick feedback, form semplici
- **5 secondi:** Standard, equilibrio perfetto
- **10 secondi:** Form complessi, voglio che utente legga bene

---

## 🏷️ TAG DINAMICI

### **Tag Campi Form** (auto-generati)
Qualsiasi campo nel tuo form diventa un tag:

```
Campo "nome"      → Tag: {nome}
Campo "email"     → Tag: {email}
Campo "telefono"  → Tag: {telefono}
Campo "azienda"   → Tag: {azienda}
... qualsiasi nome campo
```

### **Tag Generali** (sempre disponibili)

| Tag | Output Esempio | Descrizione |
|-----|----------------|-------------|
| `{form_title}` | "Contact Form" | Titolo del form |
| `{site_name}` | "Your Company" | Nome sito WordPress |
| `{site_url}` | "https://example.com" | URL sito |
| `{date}` | "05/11/2025" | Data corrente (formato WP) |
| `{time}` | "23:45" | Ora corrente (formato WP) |

### **Come Usare i Tag:**

**Input (configurazione):**
```
"Grazie {nome}! 
Abbiamo ricevuto la tua richiesta il {date} alle {time}.
Ti risponderemo a {email} entro 24 ore.

Cordiali saluti,
Il team di {site_name}"
```

**Output (dopo submit con nome="Mario" email="mario@example.com"):**
```
Grazie Mario! 
Abbiamo ricevuto la tua richiesta il 05/11/2025 alle 23:45.
Ti risponderemo a mario@example.com entro 24 ore.

Cordiali saluti,
Il team di Your Company
```

---

## 🎨 ESEMPI CONFIGURAZIONI COMPLETE

### **Esempio 1: Form Contatti Standard**
```
Messaggio:
"Grazie {nome}! Il tuo messaggio è stato inviato con successo.
Ti risponderemo entro 24 ore all'indirizzo {email}."

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Box verde con check, messaggio personalizzato, resta visibile

---

### **Esempio 2: Newsletter Signup**
```
Messaggio:
"Perfetto! Sei iscritto alla newsletter di {site_name}.
Controlla {email} per confermare l'iscrizione."

Tipo: ℹ️ Info (blu)
Durata: 5 secondi
```
**Risultato:** Box blu informativo, scompare dopo 5 secondi

---

### **Esempio 3: Contest Entry**
```
Messaggio:
"🎉 {nome}, sei ufficialmente iscritto al contest!
Riceverai aggiornamenti a {email}.
In bocca al lupo!"

Tipo: 🎉 Celebration (festoso)
Durata: 10 secondi
```
**Risultato:** Box gradient giallo festoso, bold, celebration emoji

---

### **Esempio 4: Download Lead Magnet**
```
Messaggio:
"Grazie {nome}! 
La tua guida è in arrivo a {email}.
Se non la vedi, controlla lo spam!"

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Successo con istruzioni chiare

---

### **Esempio 5: Appointment Booking**
```
Messaggio:
"Perfetto {nome}!
Il tuo appuntamento per {data_evento} alle {ora_evento} è confermato.
Riceverai un reminder 24h prima a {email}."

Tipo: ℹ️ Info (blu)
Durata: Sempre visibile
```
**Risultato:** Conferma dettagliata con info evento

---

### **Esempio 6: Job Application**
```
Messaggio:
"Candidatura ricevuta!
Grazie {nome} per l'interesse nella posizione di {posizione}.
Il nostro team HR esaminerà il tuo CV e ti contatterà a {email} entro 7 giorni.

In bocca al lupo! 🍀"

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Conferma professionale con timeline

---

## 📱 RENDERING FRONTEND

### **HTML Generato:**
```html
<div class="fp-forms-messages">
    <div class="fp-forms-message fp-forms-success fp-msg-success" 
         style="display:none;">
        <!-- Messaggio inserito qui via JavaScript -->
    </div>
</div>
```

### **JavaScript Behavior:**
```javascript
// Dopo submit success
$('.fp-forms-success')
    .removeClass('fp-msg-success fp-msg-info fp-msg-celebration')
    .addClass('fp-msg-' + messageType)
    .text(response.data.message)
    .fadeIn();

// Auto-hide (se configurato)
if (duration > 0) {
    setTimeout(function() {
        $('.fp-forms-success').fadeOut();
    }, duration);
}
```

### **CSS Classes:**
```css
.fp-forms-success                /* Base */
.fp-msg-success                 /* Verde */
.fp-msg-info                    /* Blu */
.fp-msg-celebration             /* Festoso */
```

---

## 🎯 BEST PRACTICES

### **Messaggi Efficaci:**
```
✅ BUONO:
- "Grazie {nome}! Ti risponderemo entro 24 ore."
- "Iscrizione confermata! Check {email} per il link."
- "Candidatura ricevuta! Ti contatteremo presto."

❌ CATTIVO:
- "OK" (troppo corto, non rassicurante)
- "Form inviato" (generico, freddo)
- "Grazie per aver compilato il modulo di contatto del sito example.com nella sezione contatti..." (troppo lungo)
```

### **Personalizzazione:**
```
✅ Usa il nome: "Grazie {nome}!"
✅ Conferma email: "...a {email}"
✅ Specifica timing: "entro 24 ore"
✅ Aggiungi istruzioni: "controlla spam"
✅ Rassicura: "i tuoi dati sono al sicuro"

❌ Non ripetere ovvietà
❌ Non essere troppo formale se brand è casual
❌ Non promettere tempi irrealistici
```

### **Tipo Messaggio:**
```
Successo (verde): Default, professionale, affidabile
→ Form contatti, richieste, ordini

Info (blu): Neutrale, informativo
→ Newsletter, download, info requests

Celebration (festoso): Enthusiastic, emozionale
→ Contest, giveaway, milestone, registrazioni speciali
```

### **Durata:**
```
Sempre visibile: 
→ Form importanti (ordini, appuntamenti, job applications)
→ Messaggi con istruzioni da leggere

3 secondi:
→ Form semplici (newsletter)
→ Quick feedback

5 secondi (recommended):
→ Equilibrio perfetto
→ Abbastanza per leggere, non invasivo

10 secondi:
→ Messaggi lunghi
→ Form complessi
→ Vuoi che utente legga tutto
```

---

## 🔧 IMPLEMENTAZIONE TECNICA

### **Database Storage:**
```json
{
  "settings": {
    "success_message": "Grazie {nome}! Ti risponderemo a {email}.",
    "success_message_type": "success",
    "success_message_duration": "5000"
  }
}
```

### **Backend Processing (PHP):**
```php
// In Manager.php
$success_message = $form['settings']['success_message'] ?? 'Default...';

// Replace tag dinamici
$success_message = $this->replace_success_tags( $success_message, $form, $data );

// Tag replacement
$message = str_replace( '{nome}', $data['nome'], $message );
$message = str_replace( '{email}', $data['email'], $message );
$message = str_replace( '{form_title}', $form['title'], $message );
// ... etc
```

### **Response JSON:**
```json
{
  "success": true,
  "message": "Grazie Mario! Ti risponderemo a mario@example.com.",
  "message_type": "success",
  "message_duration": 5000,
  "submission_id": 123
}
```

### **Frontend Display:**
```javascript
$('.fp-forms-success')
    .addClass('fp-msg-success')
    .text('Grazie Mario! Ti risponderemo a mario@example.com.')
    .fadeIn();

setTimeout(() => {
    $('.fp-forms-success').fadeOut();
}, 5000);
```

---

## 🎨 CSS STYLING

### **Success (verde):**
```css
.fp-forms-success.fp-msg-success {
    background: #d1fae5;
    color: #065f46;
    border: 2px solid #10b981;
}

.fp-forms-success.fp-msg-success::before {
    content: '✓ ';
    font-weight: bold;
}
```

### **Info (blu):**
```css
.fp-forms-success.fp-msg-info {
    background: #dbeafe;
    color: #1e40af;
    border: 2px solid #3b82f6;
}

.fp-forms-success.fp-msg-info::before {
    content: 'ℹ️ ';
}
```

### **Celebration (festoso):**
```css
.fp-forms-success.fp-msg-celebration {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 2px solid #f59e0b;
    font-weight: 600;
}

.fp-forms-success.fp-msg-celebration::before {
    content: '🎉 ';
}
```

---

## 🚀 WORKFLOW COMPLETO

**1. Configurazione (Admin):**
```
→ Form Builder → Sidebar → Messaggio di Conferma
→ Inserisci messaggio con tag
→ Scegli tipo (success/info/celebration)
→ Scegli durata (0/3000/5000/10000)
→ Salva Form
```

**2. Frontend Submit:**
```
Utente compila:
- nome: "Mario"
- email: "mario@example.com"
→ Click "Invia"
```

**3. Backend Processing:**
```
PHP (Manager.php):
→ Valida dati
→ Salva submission
→ Ottieni success_message da settings
→ Replace tag:
  "{nome}" → "Mario"
  "{email}" → "mario@example.com"
  "{form_title}" → "Contact Form"
→ Return JSON response
```

**4. Frontend Display:**
```
JavaScript (frontend.js):
→ Riceve response JSON
→ Estrae message, message_type, message_duration
→ Applica classe CSS (.fp-msg-success)
→ Mostra messaggio (fadeIn animation)
→ Se duration > 0: auto-hide dopo X ms
→ Scroll al messaggio
→ Reset form
```

**5. User Experience:**
```
Utente vede:
✓ "Grazie Mario! Ti risponderemo a mario@example.com entro 24h."

Box verde con check mark
Se durata 5s → scompare dopo 5 secondi
Altrimenti → resta visibile
```

---

## 📊 ESEMPI PER SETTORI

### **E-commerce:**
```
"Grazie {nome}!
Il tuo ordine #{order_id} è stato ricevuto.
Riceverai conferma a {email} entro pochi minuti.

Totale: {total}€"

Tipo: Success
Durata: Sempre visibile
```

### **SaaS/Software:**
```
"Welcome aboard {nome}! 🚀
Il tuo account {site_name} è attivo.
Abbiamo inviato le credenziali a {email}.

Inizia subito!"

Tipo: Celebration
Durata: 10 secondi
```

### **Healthcare:**
```
"Appuntamento confermato.

Paziente: {nome}
Data: {data_appuntamento}
Ora: {ora_appuntamento}
Medico: {medico}

Riceverai un reminder 24h prima a {email}."

Tipo: Info
Durata: Sempre visibile
```

### **Education:**
```
"Iscrizione completata! 📚

Benvenuto {nome} al corso {corso}.
Inizio: {data_inizio}
Aula: {aula}

Ti abbiamo inviato i materiali a {email}.
Ci vediamo in aula!"

Tipo: Celebration
Durata: Sempre visibile
```

### **Real Estate:**
```
"Richiesta informazioni ricevuta.

Immobile: {immobile}
Cliente: {nome}
Email: {email}
Telefono: {telefono}

Un nostro agente ti contatterà entro 2 ore lavorative."

Tipo: Success
Durata: Sempre visibile
```

---

## ⚙️ OPZIONI AVANZATE

### **Redirect dopo Successo:**
```
Se hai abilitato "Redirect dopo invio":
→ Messaggio NON viene mostrato
→ Utente reindirizzato a thank-you page
→ Mostra messaggio sulla thank-you page invece

Best Practice:
- Form semplici: Messaggio inline
- Form complessi: Redirect a thank-you page
```

### **Multi-lingua:**
```
Per siti multilingua:
1. Usa WPML/Polylang
2. Traduci il messaggio
3. Tag vengono sostituiti in tutte le lingue

Oppure:
→ Crea form separati per lingua
```

### **Custom CSS:**
```
Per styling custom:

.fp-forms-success.my-custom-success {
    background: your-color;
    border: your-border;
    font-size: your-size;
}

Aggiungi classe in Custom CSS Class field
```

---

## ✅ CHECKLIST PRE-PUBBLICAZIONE

**Prima di pubblicare il form:**

- [ ] ✅ Messaggio scritto e chiaro
- [ ] ✅ Tag dinamici testati
- [ ] ✅ Nome campi corrispondono ai tag
- [ ] ✅ Tipo messaggio appropriato per contesto
- [ ] ✅ Durata impostata correttamente
- [ ] ✅ Test submit su desktop
- [ ] ✅ Test submit su mobile
- [ ] ✅ Verifica tag replacement
- [ ] ✅ Verifica styling/colori
- [ ] ✅ Verifica timing auto-hide
- [ ] ✅ Verifica scroll al messaggio
- [ ] ✅ Tono di voce coerente con brand

---

## 🎉 CONCLUSIONE

**Messaggio di Conferma: 100% Personalizzabile!**

**3 Opzioni:**
1. ✅ Testo con tag dinamici
2. ✅ Tipo/stile visivo (3 varianti)
3. ✅ Durata visualizzazione (4 opzioni)

**Tag Disponibili:**
- {nome_campo} per ogni campo del form
- {form_title}, {site_name}, {site_url}
- {date}, {time}

**Stili:**
- ✓ Success (verde) - professionale
- ℹ️ Info (blu) - informativo
- 🎉 Celebration (festoso) - celebrativo

**Durata:**
- Sempre visibile
- 3 / 5 / 10 secondi auto-hide

**No Code Required - Tutto dalla UI! ✅🎨**


**Versione:** v1.2.3  
**Feature:** Success Message Customization  
**Status:** ✅ **MIGLIORATO** (già esistente + tag dinamici + stili)

---

## 🎯 OVERVIEW

Il **messaggio di conferma** appare dopo l'invio del form per **rassicurare il cliente** che la submission è andata a buon fine.

**Personalizzazioni Disponibili:**
1. ✅ **Testo del Messaggio** (con tag dinamici)
2. ✅ **Tipo/Stile Visivo** (Successo, Info, Celebration)
3. ✅ **Durata Visualizzazione** (permanente o auto-hide)

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** WP Admin → FP Forms → Modifica Form → Sidebar

**Sezione:** Impostazioni Form → **Messaggio di Conferma**

---

## 📝 OPZIONI DISPONIBILI

### **1. Messaggio di Successo**
```
Campo: Textarea (4 righe)
Default: "Grazie! Il tuo messaggio è stato inviato con successo."
Placeholder: (same as default)
Tag Disponibili: {nome}, {email}, {form_title}, {site_name}, {date}, {time}
```

**Funzione:**
- Messaggio mostrato dopo submit riuscito
- Può includere tag dinamici sostituiti automaticamente
- Supporta testo multi-riga

**Esempi:**
```
Standard:
"Grazie! Il tuo messaggio è stato inviato con successo."

Personalizzato:
"Grazie {nome}! Abbiamo ricevuto la tua richiesta.
Ti risponderemo entro 24 ore all'indirizzo {email}."

Con brand:
"Grazie per aver contattato {site_name}!
Elaboreremo la tua richiesta il prima possibile."

Specifico:
"Perfetto {nome}! La tua iscrizione a {form_title} è confermata.
Ti abbiamo inviato una email di conferma a {email}."
```

---

### **2. Tipo Messaggio** ⭐ NUOVO!
```
Campo: Select dropdown
Default: ✓ Successo (verde)
Opzioni:
- ✓ Successo (verde) - Classico, professionale
- ℹ️ Info (blu) - Informativo, neutrale
- 🎉 Celebration (festoso) - Celebrativo, enthusiastic
```

**Varianti Visive:**

#### **✓ Successo (verde)** - Default
```
Background: Verde chiaro (#d1fae5)
Testo: Verde scuro (#065f46)
Border: Verde (#10b981)
Icona: ✓ check mark
```
**Quando usare:** Form contatti, richieste info, form standard

#### **ℹ️ Info (blu)**
```
Background: Blu chiaro (#dbeafe)
Testo: Blu scuro (#1e40af)
Border: Blu (#3b82f6)
Icona: ℹ️ info symbol
```
**Quando usare:** Form informativi, newsletter signup, download

#### **🎉 Celebration (festoso)**
```
Background: Gradient giallo (#fef3c7 → #fde68a)
Testo: Marrone scuro (#92400e)
Border: Arancione (#f59e0b)
Icona: 🎉 celebration
Font-weight: 600 (bold)
```
**Quando usare:** Registrazioni, contest, giveaway, milestone events

---

### **3. Durata Visualizzazione** ⭐ NUOVO!
```
Campo: Select dropdown
Default: Sempre visibile
Opzioni:
- Sempre visibile (0)
- 3 secondi (3000ms)
- 5 secondi (5000ms)
- 10 secondi (10000ms)
```

**Comportamento:**
- **Sempre visibile:** Messaggio resta finché utente non ricarica pagina
- **Auto-hide:** Messaggio scompare (fade out) dopo X secondi

**Quando usare:**
- **Sempre visibile:** Form importanti, serve conferma visiva persistente
- **3 secondi:** Quick feedback, form semplici
- **5 secondi:** Standard, equilibrio perfetto
- **10 secondi:** Form complessi, voglio che utente legga bene

---

## 🏷️ TAG DINAMICI

### **Tag Campi Form** (auto-generati)
Qualsiasi campo nel tuo form diventa un tag:

```
Campo "nome"      → Tag: {nome}
Campo "email"     → Tag: {email}
Campo "telefono"  → Tag: {telefono}
Campo "azienda"   → Tag: {azienda}
... qualsiasi nome campo
```

### **Tag Generali** (sempre disponibili)

| Tag | Output Esempio | Descrizione |
|-----|----------------|-------------|
| `{form_title}` | "Contact Form" | Titolo del form |
| `{site_name}` | "Your Company" | Nome sito WordPress |
| `{site_url}` | "https://example.com" | URL sito |
| `{date}` | "05/11/2025" | Data corrente (formato WP) |
| `{time}` | "23:45" | Ora corrente (formato WP) |

### **Come Usare i Tag:**

**Input (configurazione):**
```
"Grazie {nome}! 
Abbiamo ricevuto la tua richiesta il {date} alle {time}.
Ti risponderemo a {email} entro 24 ore.

Cordiali saluti,
Il team di {site_name}"
```

**Output (dopo submit con nome="Mario" email="mario@example.com"):**
```
Grazie Mario! 
Abbiamo ricevuto la tua richiesta il 05/11/2025 alle 23:45.
Ti risponderemo a mario@example.com entro 24 ore.

Cordiali saluti,
Il team di Your Company
```

---

## 🎨 ESEMPI CONFIGURAZIONI COMPLETE

### **Esempio 1: Form Contatti Standard**
```
Messaggio:
"Grazie {nome}! Il tuo messaggio è stato inviato con successo.
Ti risponderemo entro 24 ore all'indirizzo {email}."

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Box verde con check, messaggio personalizzato, resta visibile

---

### **Esempio 2: Newsletter Signup**
```
Messaggio:
"Perfetto! Sei iscritto alla newsletter di {site_name}.
Controlla {email} per confermare l'iscrizione."

Tipo: ℹ️ Info (blu)
Durata: 5 secondi
```
**Risultato:** Box blu informativo, scompare dopo 5 secondi

---

### **Esempio 3: Contest Entry**
```
Messaggio:
"🎉 {nome}, sei ufficialmente iscritto al contest!
Riceverai aggiornamenti a {email}.
In bocca al lupo!"

Tipo: 🎉 Celebration (festoso)
Durata: 10 secondi
```
**Risultato:** Box gradient giallo festoso, bold, celebration emoji

---

### **Esempio 4: Download Lead Magnet**
```
Messaggio:
"Grazie {nome}! 
La tua guida è in arrivo a {email}.
Se non la vedi, controlla lo spam!"

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Successo con istruzioni chiare

---

### **Esempio 5: Appointment Booking**
```
Messaggio:
"Perfetto {nome}!
Il tuo appuntamento per {data_evento} alle {ora_evento} è confermato.
Riceverai un reminder 24h prima a {email}."

Tipo: ℹ️ Info (blu)
Durata: Sempre visibile
```
**Risultato:** Conferma dettagliata con info evento

---

### **Esempio 6: Job Application**
```
Messaggio:
"Candidatura ricevuta!
Grazie {nome} per l'interesse nella posizione di {posizione}.
Il nostro team HR esaminerà il tuo CV e ti contatterà a {email} entro 7 giorni.

In bocca al lupo! 🍀"

Tipo: ✓ Successo (verde)
Durata: Sempre visibile
```
**Risultato:** Conferma professionale con timeline

---

## 📱 RENDERING FRONTEND

### **HTML Generato:**
```html
<div class="fp-forms-messages">
    <div class="fp-forms-message fp-forms-success fp-msg-success" 
         style="display:none;">
        <!-- Messaggio inserito qui via JavaScript -->
    </div>
</div>
```

### **JavaScript Behavior:**
```javascript
// Dopo submit success
$('.fp-forms-success')
    .removeClass('fp-msg-success fp-msg-info fp-msg-celebration')
    .addClass('fp-msg-' + messageType)
    .text(response.data.message)
    .fadeIn();

// Auto-hide (se configurato)
if (duration > 0) {
    setTimeout(function() {
        $('.fp-forms-success').fadeOut();
    }, duration);
}
```

### **CSS Classes:**
```css
.fp-forms-success                /* Base */
.fp-msg-success                 /* Verde */
.fp-msg-info                    /* Blu */
.fp-msg-celebration             /* Festoso */
```

---

## 🎯 BEST PRACTICES

### **Messaggi Efficaci:**
```
✅ BUONO:
- "Grazie {nome}! Ti risponderemo entro 24 ore."
- "Iscrizione confermata! Check {email} per il link."
- "Candidatura ricevuta! Ti contatteremo presto."

❌ CATTIVO:
- "OK" (troppo corto, non rassicurante)
- "Form inviato" (generico, freddo)
- "Grazie per aver compilato il modulo di contatto del sito example.com nella sezione contatti..." (troppo lungo)
```

### **Personalizzazione:**
```
✅ Usa il nome: "Grazie {nome}!"
✅ Conferma email: "...a {email}"
✅ Specifica timing: "entro 24 ore"
✅ Aggiungi istruzioni: "controlla spam"
✅ Rassicura: "i tuoi dati sono al sicuro"

❌ Non ripetere ovvietà
❌ Non essere troppo formale se brand è casual
❌ Non promettere tempi irrealistici
```

### **Tipo Messaggio:**
```
Successo (verde): Default, professionale, affidabile
→ Form contatti, richieste, ordini

Info (blu): Neutrale, informativo
→ Newsletter, download, info requests

Celebration (festoso): Enthusiastic, emozionale
→ Contest, giveaway, milestone, registrazioni speciali
```

### **Durata:**
```
Sempre visibile: 
→ Form importanti (ordini, appuntamenti, job applications)
→ Messaggi con istruzioni da leggere

3 secondi:
→ Form semplici (newsletter)
→ Quick feedback

5 secondi (recommended):
→ Equilibrio perfetto
→ Abbastanza per leggere, non invasivo

10 secondi:
→ Messaggi lunghi
→ Form complessi
→ Vuoi che utente legga tutto
```

---

## 🔧 IMPLEMENTAZIONE TECNICA

### **Database Storage:**
```json
{
  "settings": {
    "success_message": "Grazie {nome}! Ti risponderemo a {email}.",
    "success_message_type": "success",
    "success_message_duration": "5000"
  }
}
```

### **Backend Processing (PHP):**
```php
// In Manager.php
$success_message = $form['settings']['success_message'] ?? 'Default...';

// Replace tag dinamici
$success_message = $this->replace_success_tags( $success_message, $form, $data );

// Tag replacement
$message = str_replace( '{nome}', $data['nome'], $message );
$message = str_replace( '{email}', $data['email'], $message );
$message = str_replace( '{form_title}', $form['title'], $message );
// ... etc
```

### **Response JSON:**
```json
{
  "success": true,
  "message": "Grazie Mario! Ti risponderemo a mario@example.com.",
  "message_type": "success",
  "message_duration": 5000,
  "submission_id": 123
}
```

### **Frontend Display:**
```javascript
$('.fp-forms-success')
    .addClass('fp-msg-success')
    .text('Grazie Mario! Ti risponderemo a mario@example.com.')
    .fadeIn();

setTimeout(() => {
    $('.fp-forms-success').fadeOut();
}, 5000);
```

---

## 🎨 CSS STYLING

### **Success (verde):**
```css
.fp-forms-success.fp-msg-success {
    background: #d1fae5;
    color: #065f46;
    border: 2px solid #10b981;
}

.fp-forms-success.fp-msg-success::before {
    content: '✓ ';
    font-weight: bold;
}
```

### **Info (blu):**
```css
.fp-forms-success.fp-msg-info {
    background: #dbeafe;
    color: #1e40af;
    border: 2px solid #3b82f6;
}

.fp-forms-success.fp-msg-info::before {
    content: 'ℹ️ ';
}
```

### **Celebration (festoso):**
```css
.fp-forms-success.fp-msg-celebration {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 2px solid #f59e0b;
    font-weight: 600;
}

.fp-forms-success.fp-msg-celebration::before {
    content: '🎉 ';
}
```

---

## 🚀 WORKFLOW COMPLETO

**1. Configurazione (Admin):**
```
→ Form Builder → Sidebar → Messaggio di Conferma
→ Inserisci messaggio con tag
→ Scegli tipo (success/info/celebration)
→ Scegli durata (0/3000/5000/10000)
→ Salva Form
```

**2. Frontend Submit:**
```
Utente compila:
- nome: "Mario"
- email: "mario@example.com"
→ Click "Invia"
```

**3. Backend Processing:**
```
PHP (Manager.php):
→ Valida dati
→ Salva submission
→ Ottieni success_message da settings
→ Replace tag:
  "{nome}" → "Mario"
  "{email}" → "mario@example.com"
  "{form_title}" → "Contact Form"
→ Return JSON response
```

**4. Frontend Display:**
```
JavaScript (frontend.js):
→ Riceve response JSON
→ Estrae message, message_type, message_duration
→ Applica classe CSS (.fp-msg-success)
→ Mostra messaggio (fadeIn animation)
→ Se duration > 0: auto-hide dopo X ms
→ Scroll al messaggio
→ Reset form
```

**5. User Experience:**
```
Utente vede:
✓ "Grazie Mario! Ti risponderemo a mario@example.com entro 24h."

Box verde con check mark
Se durata 5s → scompare dopo 5 secondi
Altrimenti → resta visibile
```

---

## 📊 ESEMPI PER SETTORI

### **E-commerce:**
```
"Grazie {nome}!
Il tuo ordine #{order_id} è stato ricevuto.
Riceverai conferma a {email} entro pochi minuti.

Totale: {total}€"

Tipo: Success
Durata: Sempre visibile
```

### **SaaS/Software:**
```
"Welcome aboard {nome}! 🚀
Il tuo account {site_name} è attivo.
Abbiamo inviato le credenziali a {email}.

Inizia subito!"

Tipo: Celebration
Durata: 10 secondi
```

### **Healthcare:**
```
"Appuntamento confermato.

Paziente: {nome}
Data: {data_appuntamento}
Ora: {ora_appuntamento}
Medico: {medico}

Riceverai un reminder 24h prima a {email}."

Tipo: Info
Durata: Sempre visibile
```

### **Education:**
```
"Iscrizione completata! 📚

Benvenuto {nome} al corso {corso}.
Inizio: {data_inizio}
Aula: {aula}

Ti abbiamo inviato i materiali a {email}.
Ci vediamo in aula!"

Tipo: Celebration
Durata: Sempre visibile
```

### **Real Estate:**
```
"Richiesta informazioni ricevuta.

Immobile: {immobile}
Cliente: {nome}
Email: {email}
Telefono: {telefono}

Un nostro agente ti contatterà entro 2 ore lavorative."

Tipo: Success
Durata: Sempre visibile
```

---

## ⚙️ OPZIONI AVANZATE

### **Redirect dopo Successo:**
```
Se hai abilitato "Redirect dopo invio":
→ Messaggio NON viene mostrato
→ Utente reindirizzato a thank-you page
→ Mostra messaggio sulla thank-you page invece

Best Practice:
- Form semplici: Messaggio inline
- Form complessi: Redirect a thank-you page
```

### **Multi-lingua:**
```
Per siti multilingua:
1. Usa WPML/Polylang
2. Traduci il messaggio
3. Tag vengono sostituiti in tutte le lingue

Oppure:
→ Crea form separati per lingua
```

### **Custom CSS:**
```
Per styling custom:

.fp-forms-success.my-custom-success {
    background: your-color;
    border: your-border;
    font-size: your-size;
}

Aggiungi classe in Custom CSS Class field
```

---

## ✅ CHECKLIST PRE-PUBBLICAZIONE

**Prima di pubblicare il form:**

- [ ] ✅ Messaggio scritto e chiaro
- [ ] ✅ Tag dinamici testati
- [ ] ✅ Nome campi corrispondono ai tag
- [ ] ✅ Tipo messaggio appropriato per contesto
- [ ] ✅ Durata impostata correttamente
- [ ] ✅ Test submit su desktop
- [ ] ✅ Test submit su mobile
- [ ] ✅ Verifica tag replacement
- [ ] ✅ Verifica styling/colori
- [ ] ✅ Verifica timing auto-hide
- [ ] ✅ Verifica scroll al messaggio
- [ ] ✅ Tono di voce coerente con brand

---

## 🎉 CONCLUSIONE

**Messaggio di Conferma: 100% Personalizzabile!**

**3 Opzioni:**
1. ✅ Testo con tag dinamici
2. ✅ Tipo/stile visivo (3 varianti)
3. ✅ Durata visualizzazione (4 opzioni)

**Tag Disponibili:**
- {nome_campo} per ogni campo del form
- {form_title}, {site_name}, {site_url}
- {date}, {time}

**Stili:**
- ✓ Success (verde) - professionale
- ℹ️ Info (blu) - informativo
- 🎉 Celebration (festoso) - celebrativo

**Durata:**
- Sempre visibile
- 3 / 5 / 10 secondi auto-hide

**No Code Required - Tutto dalla UI! ✅🎨**









