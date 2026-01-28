# 📝 GUIDA: Personalizzazione Testi Campi Form

**Versione:** v1.2.3  
**Feature:** Custom Field Texts & Validation Messages  
**Status:** ✅ **IMPLEMENTATO**

---

## 🎯 OVERVIEW

Ogni campo del form è **completamente personalizzabile** nei testi! Puoi modificare:

1. ✅ **Etichetta** (Label) - Già disponibile
2. ✅ **Nome Campo** (Name) - Già disponibile
3. ✅ **Placeholder** - Già disponibile
4. ✅ **Descrizione** (Help Text) - Già disponibile
5. ✅ **Messaggio Errore Personalizzato** ⭐ **NUOVO!**

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** WP Admin → FP Forms → Modifica Form → Aggiungi Campo → Click "Modifica" sul campo

**Location:** Form Builder → Campo → Body (espanso)

---

## 📝 CAMPI PERSONALIZZABILI

### **1. Etichetta Campo** (Label)
```
Campo: Input text (required)
Default: Dipende dal tipo campo
Esempio: "Nome Completo", "Il tuo indirizzo email"
Visibile: Sopra il campo nel frontend
```

**Funzione:**
- Label principale del campo
- Usata nei messaggi di errore
- Screen reader accessible

**Esempi:**
```
Text: "Nome e Cognome"
Email: "La tua email"
Phone: "Numero di telefono (anche cellulare)"
Textarea: "Descrivi la tua richiesta"
```

---

### **2. Nome Campo** (Name)
```
Campo: Input text (required)
Default: Auto-generato (lowercase, no spazi)
Esempio: "nome_completo", "email", "telefono"
Visibile: No (solo tecnico)
```

**Funzione:**
- Identificatore univoco del campo
- Usato per salvare nel DB
- Usato in email templates come tag `{nome_campo}`

**Best Practices:**
```
✅ BUONO:
- nome
- email
- telefono
- data_nascita
- messaggio

❌ CATTIVO:
- Nome (maiuscola)
- il mio campo (spazi)
- campo-1 (numeri poco descrittivi)
- cämpö (caratteri speciali)
```

---

### **3. Placeholder**
```
Campo: Input text (opzionale)
Default: Vuoto
Esempio: "Es: Mario Rossi", "nome@example.com"
Visibile: Dentro il campo (grigio) quando vuoto
```

**Funzione:**
- Hint visivo per l'utente
- Scompare quando l'utente inizia a digitare
- Non sostituisce la label (accessibility)

**Esempi:**
```
Nome: "Es: Mario Rossi"
Email: "nome@example.com"
Phone: "+39 333 1234567"
Date: "GG/MM/AAAA"
Number: "Es: 1000"
Textarea: "Scrivi qui il tuo messaggio..."
```

**⚠️ Attenzione:**
```
❌ NON usare placeholder come label
❌ NON mettere informazioni critiche solo nel placeholder

✅ Usa label + placeholder insieme
✅ Placeholder = esempio, Label = descrizione
```

---

### **4. Descrizione** (Help Text)
```
Campo: Input text (opzionale)
Default: Vuoto
Esempio: "Inserisci il tuo numero principale per essere ricontattato"
Visibile: Sotto il campo (grigio, small)
```

**Funzione:**
- Testo di aiuto/chiarimento
- Informazioni aggiuntive
- Istruzioni specifiche

**Quando Usare:**
```
✅ Quando serve chiarire il campo:
- "Useremo questa email solo per inviarti la fattura"
- "Formato accettato: PDF, DOC, JPG (max 5MB)"
- "Seleziona almeno 2 opzioni"

✅ Per rassicurare l'utente:
- "Non condivideremo mai i tuoi dati"
- "Questa informazione è opzionale"
```

**Esempi Pratici:**
```
Email: "Ti invieremo una conferma a questo indirizzo"
Phone: "Formato: +39 oppure senza prefisso"
File: "Tipi accettati: PDF, JPG, PNG. Max 5MB"
Textarea: "Minimo 50 caratteri"
Date: "Inserisci la data dell'evento"
```

---

### **5. Messaggio Errore Personalizzato** ⭐ NUOVO!
```
Campo: Input text (opzionale)
Default: Messaggio automatico
Esempio: "Per favore, inserisci la tua email aziendale"
Visibile: Quando il campo non è valido (rosso)
```

**Funzione:**
- Messaggio mostrato se validazione fallisce
- Sostituisce il messaggio predefinito
- Più specifico e contestuale

**Messaggi Predefiniti (senza personalizzazione):**
```
Required: "Il campo "{label}" è obbligatorio."
Email: "Inserisci un indirizzo email valido per "{label}"."
Phone: "Inserisci un numero di telefono valido per "{label}"."
Number: "Inserisci un numero valido per "{label}"."
```

**Messaggi Personalizzati (esempi):**
```
Email aziendale:
→ "Inserisci la tua email aziendale (non Gmail/Hotmail)"

Telefono urgenza:
→ "Questo numero è obbligatorio per contattarti in caso di emergenza"

Budget minimo:
→ "Il budget deve essere almeno 1000€"

Nome completo:
→ "Inserisci nome e cognome per favore"

Messaggio dettagliato:
→ "Descrivi la tua richiesta in almeno 50 caratteri"
```

**Quando Personalizzare:**
```
✅ Quando il messaggio default è troppo generico
✅ Quando serve un tono più friendly/formale
✅ Quando ci sono requisiti specifici
✅ Quando vuoi guidare l'utente meglio

❌ Non serve se il messaggio default va bene
❌ Non ripetere semplicemente la label
```

---

## 🎨 ESEMPI COMPLETI PER CAMPO

### **Esempio 1: Campo Email (Standard)**
```
Etichetta: Email
Nome: email
Placeholder: nome@example.com
Descrizione: Ti invieremo la conferma a questo indirizzo
Messaggio Errore: (vuoto = usa default)
Required: ✅ Sì
```
**Risultato:** Campo email standard con help text rassicurante

---

### **Esempio 2: Campo Email (Aziendale)**
```
Etichetta: Email Aziendale
Nome: email_aziendale
Placeholder: nome@tuaazienda.com
Descrizione: Solo indirizzi email aziendali, no Gmail/Hotmail
Messaggio Errore: Inserisci una email aziendale valida (non Gmail o Hotmail)
Required: ✅ Sì
```
**Risultato:** Campo email con validazione specifica e messaggio custom

---

### **Esempio 3: Campo Telefono**
```
Etichetta: Numero di Telefono
Nome: telefono
Placeholder: +39 333 1234567
Descrizione: Preferibilmente cellulare per essere ricontattato velocemente
Messaggio Errore: Inserisci un numero valido con prefisso (+39)
Required: ✅ Sì
```
**Risultato:** Campo telefono con formato chiaro

---

### **Esempio 4: Campo Nome Completo**
```
Etichetta: Nome e Cognome
Nome: nome_completo
Placeholder: Es: Mario Rossi
Descrizione: (vuoto)
Messaggio Errore: Per favore inserisci nome e cognome completi
Required: ✅ Sì
```
**Risultato:** Campo nome con messaggio friendly

---

### **Esempio 5: Campo Budget**
```
Etichetta: Budget Disponibile (€)
Nome: budget
Placeholder: Es: 5000
Descrizione: Indicaci il budget indicativo per il progetto
Messaggio Errore: Il budget deve essere almeno 500€
Required: ✅ Sì
Type: Number
```
**Risultato:** Campo numero con requisito minimo chiaro

---

### **Esempio 6: Campo Messaggio**
```
Etichetta: Descrivi la tua Richiesta
Nome: messaggio
Placeholder: Scrivi qui il tuo messaggio...
Descrizione: Più dettagli fornisci, meglio potremo aiutarti (minimo 50 caratteri)
Messaggio Errore: Per favore descrivi la tua richiesta in modo più dettagliato
Required: ✅ Sì
Type: Textarea
```
**Risultato:** Textarea con incoraggiamento a essere dettagliati

---

### **Esempio 7: Campo File Upload**
```
Etichetta: Carica il tuo CV
Nome: cv_file
Placeholder: (N/A per file)
Descrizione: Formati accettati: PDF, DOC, DOCX. Dimensione massima: 5MB
Messaggio Errore: Il file CV è obbligatorio per completare la candidatura
Required: ✅ Sì
Type: File
```
**Risultato:** File upload con specifiche chiare

---

### **Esempio 8: Campo Data Evento**
```
Etichetta: Data dell'Evento
Nome: data_evento
Placeholder: GG/MM/AAAA
Descrizione: Quando vorresti che si svolga l'evento?
Messaggio Errore: Seleziona una data valida per l'evento
Required: ✅ Sì
Type: Date
```
**Risultato:** Date picker con contesto chiaro

---

## 🎯 BEST PRACTICES

### **Labels (Etichette):**
```
✅ BUONO:
- "Nome e Cognome"
- "Il tuo indirizzo email"
- "Numero di telefono principale"
- "Budget disponibile (€)"

❌ CATTIVO:
- "Nome:" (non serve il :)
- "inserisci qui" (non descrittivo)
- "NOME" (non urlare)
- "Il tuo nome per favore grazie" (troppo verboso)
```

### **Placeholders:**
```
✅ BUONO:
- "Es: Mario Rossi"
- "nome@example.com"
- "+39 333 1234567"
- "Scrivi qui..."

❌ CATTIVO:
- "Nome" (ripete la label)
- "Obbligatorio" (usa required flag)
- Placeholder troppo lungo (>50 caratteri)
- Placeholder con istruzioni critiche
```

### **Descrizioni:**
```
✅ BUONO:
- "Useremo questa email solo per la conferma"
- "Formato: PDF, JPG. Max 5MB"
- "Seleziona almeno 2 opzioni"

❌ CATTIVO:
- Ripetere la label
- Informazioni già nel placeholder
- Descrizione troppo lunga (>100 caratteri)
```

### **Messaggi Errore:**
```
✅ BUONO:
- "Inserisci una email aziendale valida"
- "Il budget deve essere almeno 500€"
- "Per favore descrivi la tua richiesta"

❌ CATTIVO:
- "Errore!" (non descrittivo)
- "Hai sbagliato" (colpevolizzante)
- "Il campo è richiesto" (ripete il default)
```

---

## 🌍 MULTILINGUA

**Attualmente:** Testi in italiano

**Per Multilingua:**
1. Usa plugin WPML / Polylang
2. Traduci le stringhe del form
3. Oppure: Crea form separati per lingua

**Tag tradotti automaticamente:**
```
__( 'Etichetta Campo', 'fp-forms' )
__( 'Placeholder', 'fp-forms' )
__( 'Descrizione', 'fp-forms' )
```

---

## 🎨 TONO DI VOCE

### **Formale (B2B, Legal, Finance):**
```
Label: "Ragione Sociale"
Placeholder: "Es: Acme S.p.A."
Descrizione: "Inserire la denominazione esatta come da registro imprese"
Errore: "La ragione sociale è obbligatoria per procedere"
```

### **Friendly (Consumer, E-commerce):**
```
Label: "Il tuo nome"
Placeholder: "Come ti chiami?"
Descrizione: "Solo il nome va benissimo!"
Errore: "Ehi, come possiamo chiamarti? 😊"
```

### **Tecnico (SaaS, Developer):**
```
Label: "API Key"
Placeholder: "sk_live_..."
Descrizione: "Format: 32 alphanumeric chars. Get from dashboard."
Errore: "Invalid API key format"
```

---

## 🔧 IMPLEMENTAZIONE TECNICA

### **Database Storage:**
```json
{
  "fields": [
    {
      "type": "email",
      "label": "Email Aziendale",
      "name": "email_aziendale",
      "required": true,
      "options": {
        "placeholder": "nome@tuaazienda.com",
        "description": "Solo email aziendali",
        "error_message": "Inserisci una email aziendale valida"
      }
    }
  ]
}
```

### **Rendering Frontend:**
```html
<div class="fp-forms-field">
    <label class="fp-forms-label">Email Aziendale</label>
    
    <input type="email" 
           name="email_aziendale"
           placeholder="nome@tuaazienda.com"
           class="fp-forms-input"
           required>
    
    <small class="fp-forms-description">
        Solo email aziendali
    </small>
    
    <!-- Messaggio errore (se validazione fallisce) -->
    <span class="fp-forms-error" style="display:none;">
        Inserisci una email aziendale valida
    </span>
</div>
```

### **Validation Logic:**
```php
// In Validator.php
public function validate_email( $value, $field_name, $field_label, $custom_message = '' ) {
    if ( ! is_email( $value ) ) {
        $error = ! empty( $custom_message ) 
            ? $custom_message 
            : sprintf( 'Email non valida per "%s"', $field_label );
        
        $this->add_error( $field_name, $error );
    }
}

// In Manager.php
$custom_error = $field['options']['error_message'] ?? '';
$validator->validate_email( $value, $name, $label, $custom_error );
```

---

## 📊 ACCESSIBILITY (A11Y)

**Label:**
- ✅ Sempre presente
- ✅ Collegata al campo (for/id)
- ✅ Screen reader legge la label

**Placeholder:**
- ⚠️ Non sostituisce la label
- ⚠️ Non sempre letto da screen reader
- ✅ Usa solo come hint visivo

**Description:**
- ✅ `aria-describedby` collegato
- ✅ Screen reader legge dopo la label

**Error Message:**
- ✅ `aria-invalid="true"` quando errore
- ✅ `role="alert"` per messaggio errore
- ✅ Screen reader annuncia l'errore

**Best Practices A11Y:**
```html
<!-- BUONO (accessible) -->
<label for="email">Email</label>
<input id="email" 
       type="email"
       placeholder="nome@example.com"
       aria-describedby="email-help"
       aria-invalid="false">
<small id="email-help">Ti invieremo la conferma</small>

<!-- CATTIVO (non accessible) -->
<input type="email" placeholder="Email"> <!-- No label! -->
```

---

## 🎯 CHECKLIST PER CAMPO

**Prima di pubblicare un campo:**

- [ ] ✅ Label chiara e descrittiva
- [ ] ✅ Nome campo lowercase, no spazi
- [ ] ✅ Placeholder = esempio, non istruzione
- [ ] ✅ Descrizione solo se necessaria
- [ ] ✅ Messaggio errore custom solo se migliora UX
- [ ] ✅ Required impostato correttamente
- [ ] ✅ Tono di voce coerente con brand
- [ ] ✅ Test su desktop
- [ ] ✅ Test su mobile
- [ ] ✅ Test validazione (invia vuoto/invalido)
- [ ] ✅ Test screen reader

---

## 📚 ESEMPI PER CASI D'USO

### **Form Contatti Aziendale:**
```
Nome Completo:
- Label: "Nome e Cognome"
- Placeholder: "Es: Mario Rossi"
- Errore: "Inserisci nome e cognome completi"

Email:
- Label: "Email"
- Placeholder: "nome@example.com"
- Descrizione: "Per inviarti la conferma"
- Errore: (default)

Telefono:
- Label: "Telefono"
- Placeholder: "+39 333 1234567"
- Descrizione: "Preferibilmente cellulare"
- Errore: (default)

Messaggio:
- Label: "La tua Richiesta"
- Placeholder: "Scrivi qui..."
- Descrizione: "Più dettagli fornisci, meglio potremo aiutarti"
- Errore: "Descrivi la tua richiesta per favore"
```

---

### **Form E-commerce Lead:**
```
Budget:
- Label: "Budget Disponibile (€)"
- Placeholder: "Es: 5000"
- Descrizione: "Indicaci un range approssimativo"
- Errore: "Il budget deve essere almeno 500€"

Scadenza:
- Label: "Quando ti serve?"
- Placeholder: "GG/MM/AAAA"
- Descrizione: "Entro quando vorresti il lavoro completato?"
- Errore: "Seleziona una data valida"
```

---

### **Form Candidatura:**
```
CV Upload:
- Label: "Carica il tuo CV"
- Descrizione: "PDF, DOC, DOCX. Max 5MB"
- Errore: "Il CV è obbligatorio per candidarti"

Lettera Motivazionale:
- Label: "Lettera di Presentazione"
- Placeholder: "Parlaci di te..."
- Descrizione: "Minimo 200 caratteri"
- Errore: "Scrivi almeno 200 caratteri per presentarti"
```

---

## ✅ CONCLUSIONE

**5 Testi Personalizzabili per Campo:**
1. ✅ Etichetta (Label)
2. ✅ Nome (Name - tecnico)
3. ✅ Placeholder
4. ✅ Descrizione (Help Text)
5. ✅ Messaggio Errore ⭐ NUOVO!

**Disponibili per tutti i tipi campo:**
- ✅ Text, Email, Phone, Number, Date, Textarea, Select, Radio, Checkbox, File
- ❌ Privacy/Marketing checkbox (testi specifici propri)
- ❌ reCAPTCHA (configurato globalmente)

**No Code Required:** Tutto configurabile dalla UI del Form Builder! 📝✨


**Versione:** v1.2.3  
**Feature:** Custom Field Texts & Validation Messages  
**Status:** ✅ **IMPLEMENTATO**

---

## 🎯 OVERVIEW

Ogni campo del form è **completamente personalizzabile** nei testi! Puoi modificare:

1. ✅ **Etichetta** (Label) - Già disponibile
2. ✅ **Nome Campo** (Name) - Già disponibile
3. ✅ **Placeholder** - Già disponibile
4. ✅ **Descrizione** (Help Text) - Già disponibile
5. ✅ **Messaggio Errore Personalizzato** ⭐ **NUOVO!**

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** WP Admin → FP Forms → Modifica Form → Aggiungi Campo → Click "Modifica" sul campo

**Location:** Form Builder → Campo → Body (espanso)

---

## 📝 CAMPI PERSONALIZZABILI

### **1. Etichetta Campo** (Label)
```
Campo: Input text (required)
Default: Dipende dal tipo campo
Esempio: "Nome Completo", "Il tuo indirizzo email"
Visibile: Sopra il campo nel frontend
```

**Funzione:**
- Label principale del campo
- Usata nei messaggi di errore
- Screen reader accessible

**Esempi:**
```
Text: "Nome e Cognome"
Email: "La tua email"
Phone: "Numero di telefono (anche cellulare)"
Textarea: "Descrivi la tua richiesta"
```

---

### **2. Nome Campo** (Name)
```
Campo: Input text (required)
Default: Auto-generato (lowercase, no spazi)
Esempio: "nome_completo", "email", "telefono"
Visibile: No (solo tecnico)
```

**Funzione:**
- Identificatore univoco del campo
- Usato per salvare nel DB
- Usato in email templates come tag `{nome_campo}`

**Best Practices:**
```
✅ BUONO:
- nome
- email
- telefono
- data_nascita
- messaggio

❌ CATTIVO:
- Nome (maiuscola)
- il mio campo (spazi)
- campo-1 (numeri poco descrittivi)
- cämpö (caratteri speciali)
```

---

### **3. Placeholder**
```
Campo: Input text (opzionale)
Default: Vuoto
Esempio: "Es: Mario Rossi", "nome@example.com"
Visibile: Dentro il campo (grigio) quando vuoto
```

**Funzione:**
- Hint visivo per l'utente
- Scompare quando l'utente inizia a digitare
- Non sostituisce la label (accessibility)

**Esempi:**
```
Nome: "Es: Mario Rossi"
Email: "nome@example.com"
Phone: "+39 333 1234567"
Date: "GG/MM/AAAA"
Number: "Es: 1000"
Textarea: "Scrivi qui il tuo messaggio..."
```

**⚠️ Attenzione:**
```
❌ NON usare placeholder come label
❌ NON mettere informazioni critiche solo nel placeholder

✅ Usa label + placeholder insieme
✅ Placeholder = esempio, Label = descrizione
```

---

### **4. Descrizione** (Help Text)
```
Campo: Input text (opzionale)
Default: Vuoto
Esempio: "Inserisci il tuo numero principale per essere ricontattato"
Visibile: Sotto il campo (grigio, small)
```

**Funzione:**
- Testo di aiuto/chiarimento
- Informazioni aggiuntive
- Istruzioni specifiche

**Quando Usare:**
```
✅ Quando serve chiarire il campo:
- "Useremo questa email solo per inviarti la fattura"
- "Formato accettato: PDF, DOC, JPG (max 5MB)"
- "Seleziona almeno 2 opzioni"

✅ Per rassicurare l'utente:
- "Non condivideremo mai i tuoi dati"
- "Questa informazione è opzionale"
```

**Esempi Pratici:**
```
Email: "Ti invieremo una conferma a questo indirizzo"
Phone: "Formato: +39 oppure senza prefisso"
File: "Tipi accettati: PDF, JPG, PNG. Max 5MB"
Textarea: "Minimo 50 caratteri"
Date: "Inserisci la data dell'evento"
```

---

### **5. Messaggio Errore Personalizzato** ⭐ NUOVO!
```
Campo: Input text (opzionale)
Default: Messaggio automatico
Esempio: "Per favore, inserisci la tua email aziendale"
Visibile: Quando il campo non è valido (rosso)
```

**Funzione:**
- Messaggio mostrato se validazione fallisce
- Sostituisce il messaggio predefinito
- Più specifico e contestuale

**Messaggi Predefiniti (senza personalizzazione):**
```
Required: "Il campo "{label}" è obbligatorio."
Email: "Inserisci un indirizzo email valido per "{label}"."
Phone: "Inserisci un numero di telefono valido per "{label}"."
Number: "Inserisci un numero valido per "{label}"."
```

**Messaggi Personalizzati (esempi):**
```
Email aziendale:
→ "Inserisci la tua email aziendale (non Gmail/Hotmail)"

Telefono urgenza:
→ "Questo numero è obbligatorio per contattarti in caso di emergenza"

Budget minimo:
→ "Il budget deve essere almeno 1000€"

Nome completo:
→ "Inserisci nome e cognome per favore"

Messaggio dettagliato:
→ "Descrivi la tua richiesta in almeno 50 caratteri"
```

**Quando Personalizzare:**
```
✅ Quando il messaggio default è troppo generico
✅ Quando serve un tono più friendly/formale
✅ Quando ci sono requisiti specifici
✅ Quando vuoi guidare l'utente meglio

❌ Non serve se il messaggio default va bene
❌ Non ripetere semplicemente la label
```

---

## 🎨 ESEMPI COMPLETI PER CAMPO

### **Esempio 1: Campo Email (Standard)**
```
Etichetta: Email
Nome: email
Placeholder: nome@example.com
Descrizione: Ti invieremo la conferma a questo indirizzo
Messaggio Errore: (vuoto = usa default)
Required: ✅ Sì
```
**Risultato:** Campo email standard con help text rassicurante

---

### **Esempio 2: Campo Email (Aziendale)**
```
Etichetta: Email Aziendale
Nome: email_aziendale
Placeholder: nome@tuaazienda.com
Descrizione: Solo indirizzi email aziendali, no Gmail/Hotmail
Messaggio Errore: Inserisci una email aziendale valida (non Gmail o Hotmail)
Required: ✅ Sì
```
**Risultato:** Campo email con validazione specifica e messaggio custom

---

### **Esempio 3: Campo Telefono**
```
Etichetta: Numero di Telefono
Nome: telefono
Placeholder: +39 333 1234567
Descrizione: Preferibilmente cellulare per essere ricontattato velocemente
Messaggio Errore: Inserisci un numero valido con prefisso (+39)
Required: ✅ Sì
```
**Risultato:** Campo telefono con formato chiaro

---

### **Esempio 4: Campo Nome Completo**
```
Etichetta: Nome e Cognome
Nome: nome_completo
Placeholder: Es: Mario Rossi
Descrizione: (vuoto)
Messaggio Errore: Per favore inserisci nome e cognome completi
Required: ✅ Sì
```
**Risultato:** Campo nome con messaggio friendly

---

### **Esempio 5: Campo Budget**
```
Etichetta: Budget Disponibile (€)
Nome: budget
Placeholder: Es: 5000
Descrizione: Indicaci il budget indicativo per il progetto
Messaggio Errore: Il budget deve essere almeno 500€
Required: ✅ Sì
Type: Number
```
**Risultato:** Campo numero con requisito minimo chiaro

---

### **Esempio 6: Campo Messaggio**
```
Etichetta: Descrivi la tua Richiesta
Nome: messaggio
Placeholder: Scrivi qui il tuo messaggio...
Descrizione: Più dettagli fornisci, meglio potremo aiutarti (minimo 50 caratteri)
Messaggio Errore: Per favore descrivi la tua richiesta in modo più dettagliato
Required: ✅ Sì
Type: Textarea
```
**Risultato:** Textarea con incoraggiamento a essere dettagliati

---

### **Esempio 7: Campo File Upload**
```
Etichetta: Carica il tuo CV
Nome: cv_file
Placeholder: (N/A per file)
Descrizione: Formati accettati: PDF, DOC, DOCX. Dimensione massima: 5MB
Messaggio Errore: Il file CV è obbligatorio per completare la candidatura
Required: ✅ Sì
Type: File
```
**Risultato:** File upload con specifiche chiare

---

### **Esempio 8: Campo Data Evento**
```
Etichetta: Data dell'Evento
Nome: data_evento
Placeholder: GG/MM/AAAA
Descrizione: Quando vorresti che si svolga l'evento?
Messaggio Errore: Seleziona una data valida per l'evento
Required: ✅ Sì
Type: Date
```
**Risultato:** Date picker con contesto chiaro

---

## 🎯 BEST PRACTICES

### **Labels (Etichette):**
```
✅ BUONO:
- "Nome e Cognome"
- "Il tuo indirizzo email"
- "Numero di telefono principale"
- "Budget disponibile (€)"

❌ CATTIVO:
- "Nome:" (non serve il :)
- "inserisci qui" (non descrittivo)
- "NOME" (non urlare)
- "Il tuo nome per favore grazie" (troppo verboso)
```

### **Placeholders:**
```
✅ BUONO:
- "Es: Mario Rossi"
- "nome@example.com"
- "+39 333 1234567"
- "Scrivi qui..."

❌ CATTIVO:
- "Nome" (ripete la label)
- "Obbligatorio" (usa required flag)
- Placeholder troppo lungo (>50 caratteri)
- Placeholder con istruzioni critiche
```

### **Descrizioni:**
```
✅ BUONO:
- "Useremo questa email solo per la conferma"
- "Formato: PDF, JPG. Max 5MB"
- "Seleziona almeno 2 opzioni"

❌ CATTIVO:
- Ripetere la label
- Informazioni già nel placeholder
- Descrizione troppo lunga (>100 caratteri)
```

### **Messaggi Errore:**
```
✅ BUONO:
- "Inserisci una email aziendale valida"
- "Il budget deve essere almeno 500€"
- "Per favore descrivi la tua richiesta"

❌ CATTIVO:
- "Errore!" (non descrittivo)
- "Hai sbagliato" (colpevolizzante)
- "Il campo è richiesto" (ripete il default)
```

---

## 🌍 MULTILINGUA

**Attualmente:** Testi in italiano

**Per Multilingua:**
1. Usa plugin WPML / Polylang
2. Traduci le stringhe del form
3. Oppure: Crea form separati per lingua

**Tag tradotti automaticamente:**
```
__( 'Etichetta Campo', 'fp-forms' )
__( 'Placeholder', 'fp-forms' )
__( 'Descrizione', 'fp-forms' )
```

---

## 🎨 TONO DI VOCE

### **Formale (B2B, Legal, Finance):**
```
Label: "Ragione Sociale"
Placeholder: "Es: Acme S.p.A."
Descrizione: "Inserire la denominazione esatta come da registro imprese"
Errore: "La ragione sociale è obbligatoria per procedere"
```

### **Friendly (Consumer, E-commerce):**
```
Label: "Il tuo nome"
Placeholder: "Come ti chiami?"
Descrizione: "Solo il nome va benissimo!"
Errore: "Ehi, come possiamo chiamarti? 😊"
```

### **Tecnico (SaaS, Developer):**
```
Label: "API Key"
Placeholder: "sk_live_..."
Descrizione: "Format: 32 alphanumeric chars. Get from dashboard."
Errore: "Invalid API key format"
```

---

## 🔧 IMPLEMENTAZIONE TECNICA

### **Database Storage:**
```json
{
  "fields": [
    {
      "type": "email",
      "label": "Email Aziendale",
      "name": "email_aziendale",
      "required": true,
      "options": {
        "placeholder": "nome@tuaazienda.com",
        "description": "Solo email aziendali",
        "error_message": "Inserisci una email aziendale valida"
      }
    }
  ]
}
```

### **Rendering Frontend:**
```html
<div class="fp-forms-field">
    <label class="fp-forms-label">Email Aziendale</label>
    
    <input type="email" 
           name="email_aziendale"
           placeholder="nome@tuaazienda.com"
           class="fp-forms-input"
           required>
    
    <small class="fp-forms-description">
        Solo email aziendali
    </small>
    
    <!-- Messaggio errore (se validazione fallisce) -->
    <span class="fp-forms-error" style="display:none;">
        Inserisci una email aziendale valida
    </span>
</div>
```

### **Validation Logic:**
```php
// In Validator.php
public function validate_email( $value, $field_name, $field_label, $custom_message = '' ) {
    if ( ! is_email( $value ) ) {
        $error = ! empty( $custom_message ) 
            ? $custom_message 
            : sprintf( 'Email non valida per "%s"', $field_label );
        
        $this->add_error( $field_name, $error );
    }
}

// In Manager.php
$custom_error = $field['options']['error_message'] ?? '';
$validator->validate_email( $value, $name, $label, $custom_error );
```

---

## 📊 ACCESSIBILITY (A11Y)

**Label:**
- ✅ Sempre presente
- ✅ Collegata al campo (for/id)
- ✅ Screen reader legge la label

**Placeholder:**
- ⚠️ Non sostituisce la label
- ⚠️ Non sempre letto da screen reader
- ✅ Usa solo come hint visivo

**Description:**
- ✅ `aria-describedby` collegato
- ✅ Screen reader legge dopo la label

**Error Message:**
- ✅ `aria-invalid="true"` quando errore
- ✅ `role="alert"` per messaggio errore
- ✅ Screen reader annuncia l'errore

**Best Practices A11Y:**
```html
<!-- BUONO (accessible) -->
<label for="email">Email</label>
<input id="email" 
       type="email"
       placeholder="nome@example.com"
       aria-describedby="email-help"
       aria-invalid="false">
<small id="email-help">Ti invieremo la conferma</small>

<!-- CATTIVO (non accessible) -->
<input type="email" placeholder="Email"> <!-- No label! -->
```

---

## 🎯 CHECKLIST PER CAMPO

**Prima di pubblicare un campo:**

- [ ] ✅ Label chiara e descrittiva
- [ ] ✅ Nome campo lowercase, no spazi
- [ ] ✅ Placeholder = esempio, non istruzione
- [ ] ✅ Descrizione solo se necessaria
- [ ] ✅ Messaggio errore custom solo se migliora UX
- [ ] ✅ Required impostato correttamente
- [ ] ✅ Tono di voce coerente con brand
- [ ] ✅ Test su desktop
- [ ] ✅ Test su mobile
- [ ] ✅ Test validazione (invia vuoto/invalido)
- [ ] ✅ Test screen reader

---

## 📚 ESEMPI PER CASI D'USO

### **Form Contatti Aziendale:**
```
Nome Completo:
- Label: "Nome e Cognome"
- Placeholder: "Es: Mario Rossi"
- Errore: "Inserisci nome e cognome completi"

Email:
- Label: "Email"
- Placeholder: "nome@example.com"
- Descrizione: "Per inviarti la conferma"
- Errore: (default)

Telefono:
- Label: "Telefono"
- Placeholder: "+39 333 1234567"
- Descrizione: "Preferibilmente cellulare"
- Errore: (default)

Messaggio:
- Label: "La tua Richiesta"
- Placeholder: "Scrivi qui..."
- Descrizione: "Più dettagli fornisci, meglio potremo aiutarti"
- Errore: "Descrivi la tua richiesta per favore"
```

---

### **Form E-commerce Lead:**
```
Budget:
- Label: "Budget Disponibile (€)"
- Placeholder: "Es: 5000"
- Descrizione: "Indicaci un range approssimativo"
- Errore: "Il budget deve essere almeno 500€"

Scadenza:
- Label: "Quando ti serve?"
- Placeholder: "GG/MM/AAAA"
- Descrizione: "Entro quando vorresti il lavoro completato?"
- Errore: "Seleziona una data valida"
```

---

### **Form Candidatura:**
```
CV Upload:
- Label: "Carica il tuo CV"
- Descrizione: "PDF, DOC, DOCX. Max 5MB"
- Errore: "Il CV è obbligatorio per candidarti"

Lettera Motivazionale:
- Label: "Lettera di Presentazione"
- Placeholder: "Parlaci di te..."
- Descrizione: "Minimo 200 caratteri"
- Errore: "Scrivi almeno 200 caratteri per presentarti"
```

---

## ✅ CONCLUSIONE

**5 Testi Personalizzabili per Campo:**
1. ✅ Etichetta (Label)
2. ✅ Nome (Name - tecnico)
3. ✅ Placeholder
4. ✅ Descrizione (Help Text)
5. ✅ Messaggio Errore ⭐ NUOVO!

**Disponibili per tutti i tipi campo:**
- ✅ Text, Email, Phone, Number, Date, Textarea, Select, Radio, Checkbox, File
- ❌ Privacy/Marketing checkbox (testi specifici propri)
- ❌ reCAPTCHA (configurato globalmente)

**No Code Required:** Tutto configurabile dalla UI del Form Builder! 📝✨


**Versione:** v1.2.3  
**Feature:** Custom Field Texts & Validation Messages  
**Status:** ✅ **IMPLEMENTATO**

---

## 🎯 OVERVIEW

Ogni campo del form è **completamente personalizzabile** nei testi! Puoi modificare:

1. ✅ **Etichetta** (Label) - Già disponibile
2. ✅ **Nome Campo** (Name) - Già disponibile
3. ✅ **Placeholder** - Già disponibile
4. ✅ **Descrizione** (Help Text) - Già disponibile
5. ✅ **Messaggio Errore Personalizzato** ⭐ **NUOVO!**

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** WP Admin → FP Forms → Modifica Form → Aggiungi Campo → Click "Modifica" sul campo

**Location:** Form Builder → Campo → Body (espanso)

---

## 📝 CAMPI PERSONALIZZABILI

### **1. Etichetta Campo** (Label)
```
Campo: Input text (required)
Default: Dipende dal tipo campo
Esempio: "Nome Completo", "Il tuo indirizzo email"
Visibile: Sopra il campo nel frontend
```

**Funzione:**
- Label principale del campo
- Usata nei messaggi di errore
- Screen reader accessible

**Esempi:**
```
Text: "Nome e Cognome"
Email: "La tua email"
Phone: "Numero di telefono (anche cellulare)"
Textarea: "Descrivi la tua richiesta"
```

---

### **2. Nome Campo** (Name)
```
Campo: Input text (required)
Default: Auto-generato (lowercase, no spazi)
Esempio: "nome_completo", "email", "telefono"
Visibile: No (solo tecnico)
```

**Funzione:**
- Identificatore univoco del campo
- Usato per salvare nel DB
- Usato in email templates come tag `{nome_campo}`

**Best Practices:**
```
✅ BUONO:
- nome
- email
- telefono
- data_nascita
- messaggio

❌ CATTIVO:
- Nome (maiuscola)
- il mio campo (spazi)
- campo-1 (numeri poco descrittivi)
- cämpö (caratteri speciali)
```

---

### **3. Placeholder**
```
Campo: Input text (opzionale)
Default: Vuoto
Esempio: "Es: Mario Rossi", "nome@example.com"
Visibile: Dentro il campo (grigio) quando vuoto
```

**Funzione:**
- Hint visivo per l'utente
- Scompare quando l'utente inizia a digitare
- Non sostituisce la label (accessibility)

**Esempi:**
```
Nome: "Es: Mario Rossi"
Email: "nome@example.com"
Phone: "+39 333 1234567"
Date: "GG/MM/AAAA"
Number: "Es: 1000"
Textarea: "Scrivi qui il tuo messaggio..."
```

**⚠️ Attenzione:**
```
❌ NON usare placeholder come label
❌ NON mettere informazioni critiche solo nel placeholder

✅ Usa label + placeholder insieme
✅ Placeholder = esempio, Label = descrizione
```

---

### **4. Descrizione** (Help Text)
```
Campo: Input text (opzionale)
Default: Vuoto
Esempio: "Inserisci il tuo numero principale per essere ricontattato"
Visibile: Sotto il campo (grigio, small)
```

**Funzione:**
- Testo di aiuto/chiarimento
- Informazioni aggiuntive
- Istruzioni specifiche

**Quando Usare:**
```
✅ Quando serve chiarire il campo:
- "Useremo questa email solo per inviarti la fattura"
- "Formato accettato: PDF, DOC, JPG (max 5MB)"
- "Seleziona almeno 2 opzioni"

✅ Per rassicurare l'utente:
- "Non condivideremo mai i tuoi dati"
- "Questa informazione è opzionale"
```

**Esempi Pratici:**
```
Email: "Ti invieremo una conferma a questo indirizzo"
Phone: "Formato: +39 oppure senza prefisso"
File: "Tipi accettati: PDF, JPG, PNG. Max 5MB"
Textarea: "Minimo 50 caratteri"
Date: "Inserisci la data dell'evento"
```

---

### **5. Messaggio Errore Personalizzato** ⭐ NUOVO!
```
Campo: Input text (opzionale)
Default: Messaggio automatico
Esempio: "Per favore, inserisci la tua email aziendale"
Visibile: Quando il campo non è valido (rosso)
```

**Funzione:**
- Messaggio mostrato se validazione fallisce
- Sostituisce il messaggio predefinito
- Più specifico e contestuale

**Messaggi Predefiniti (senza personalizzazione):**
```
Required: "Il campo "{label}" è obbligatorio."
Email: "Inserisci un indirizzo email valido per "{label}"."
Phone: "Inserisci un numero di telefono valido per "{label}"."
Number: "Inserisci un numero valido per "{label}"."
```

**Messaggi Personalizzati (esempi):**
```
Email aziendale:
→ "Inserisci la tua email aziendale (non Gmail/Hotmail)"

Telefono urgenza:
→ "Questo numero è obbligatorio per contattarti in caso di emergenza"

Budget minimo:
→ "Il budget deve essere almeno 1000€"

Nome completo:
→ "Inserisci nome e cognome per favore"

Messaggio dettagliato:
→ "Descrivi la tua richiesta in almeno 50 caratteri"
```

**Quando Personalizzare:**
```
✅ Quando il messaggio default è troppo generico
✅ Quando serve un tono più friendly/formale
✅ Quando ci sono requisiti specifici
✅ Quando vuoi guidare l'utente meglio

❌ Non serve se il messaggio default va bene
❌ Non ripetere semplicemente la label
```

---

## 🎨 ESEMPI COMPLETI PER CAMPO

### **Esempio 1: Campo Email (Standard)**
```
Etichetta: Email
Nome: email
Placeholder: nome@example.com
Descrizione: Ti invieremo la conferma a questo indirizzo
Messaggio Errore: (vuoto = usa default)
Required: ✅ Sì
```
**Risultato:** Campo email standard con help text rassicurante

---

### **Esempio 2: Campo Email (Aziendale)**
```
Etichetta: Email Aziendale
Nome: email_aziendale
Placeholder: nome@tuaazienda.com
Descrizione: Solo indirizzi email aziendali, no Gmail/Hotmail
Messaggio Errore: Inserisci una email aziendale valida (non Gmail o Hotmail)
Required: ✅ Sì
```
**Risultato:** Campo email con validazione specifica e messaggio custom

---

### **Esempio 3: Campo Telefono**
```
Etichetta: Numero di Telefono
Nome: telefono
Placeholder: +39 333 1234567
Descrizione: Preferibilmente cellulare per essere ricontattato velocemente
Messaggio Errore: Inserisci un numero valido con prefisso (+39)
Required: ✅ Sì
```
**Risultato:** Campo telefono con formato chiaro

---

### **Esempio 4: Campo Nome Completo**
```
Etichetta: Nome e Cognome
Nome: nome_completo
Placeholder: Es: Mario Rossi
Descrizione: (vuoto)
Messaggio Errore: Per favore inserisci nome e cognome completi
Required: ✅ Sì
```
**Risultato:** Campo nome con messaggio friendly

---

### **Esempio 5: Campo Budget**
```
Etichetta: Budget Disponibile (€)
Nome: budget
Placeholder: Es: 5000
Descrizione: Indicaci il budget indicativo per il progetto
Messaggio Errore: Il budget deve essere almeno 500€
Required: ✅ Sì
Type: Number
```
**Risultato:** Campo numero con requisito minimo chiaro

---

### **Esempio 6: Campo Messaggio**
```
Etichetta: Descrivi la tua Richiesta
Nome: messaggio
Placeholder: Scrivi qui il tuo messaggio...
Descrizione: Più dettagli fornisci, meglio potremo aiutarti (minimo 50 caratteri)
Messaggio Errore: Per favore descrivi la tua richiesta in modo più dettagliato
Required: ✅ Sì
Type: Textarea
```
**Risultato:** Textarea con incoraggiamento a essere dettagliati

---

### **Esempio 7: Campo File Upload**
```
Etichetta: Carica il tuo CV
Nome: cv_file
Placeholder: (N/A per file)
Descrizione: Formati accettati: PDF, DOC, DOCX. Dimensione massima: 5MB
Messaggio Errore: Il file CV è obbligatorio per completare la candidatura
Required: ✅ Sì
Type: File
```
**Risultato:** File upload con specifiche chiare

---

### **Esempio 8: Campo Data Evento**
```
Etichetta: Data dell'Evento
Nome: data_evento
Placeholder: GG/MM/AAAA
Descrizione: Quando vorresti che si svolga l'evento?
Messaggio Errore: Seleziona una data valida per l'evento
Required: ✅ Sì
Type: Date
```
**Risultato:** Date picker con contesto chiaro

---

## 🎯 BEST PRACTICES

### **Labels (Etichette):**
```
✅ BUONO:
- "Nome e Cognome"
- "Il tuo indirizzo email"
- "Numero di telefono principale"
- "Budget disponibile (€)"

❌ CATTIVO:
- "Nome:" (non serve il :)
- "inserisci qui" (non descrittivo)
- "NOME" (non urlare)
- "Il tuo nome per favore grazie" (troppo verboso)
```

### **Placeholders:**
```
✅ BUONO:
- "Es: Mario Rossi"
- "nome@example.com"
- "+39 333 1234567"
- "Scrivi qui..."

❌ CATTIVO:
- "Nome" (ripete la label)
- "Obbligatorio" (usa required flag)
- Placeholder troppo lungo (>50 caratteri)
- Placeholder con istruzioni critiche
```

### **Descrizioni:**
```
✅ BUONO:
- "Useremo questa email solo per la conferma"
- "Formato: PDF, JPG. Max 5MB"
- "Seleziona almeno 2 opzioni"

❌ CATTIVO:
- Ripetere la label
- Informazioni già nel placeholder
- Descrizione troppo lunga (>100 caratteri)
```

### **Messaggi Errore:**
```
✅ BUONO:
- "Inserisci una email aziendale valida"
- "Il budget deve essere almeno 500€"
- "Per favore descrivi la tua richiesta"

❌ CATTIVO:
- "Errore!" (non descrittivo)
- "Hai sbagliato" (colpevolizzante)
- "Il campo è richiesto" (ripete il default)
```

---

## 🌍 MULTILINGUA

**Attualmente:** Testi in italiano

**Per Multilingua:**
1. Usa plugin WPML / Polylang
2. Traduci le stringhe del form
3. Oppure: Crea form separati per lingua

**Tag tradotti automaticamente:**
```
__( 'Etichetta Campo', 'fp-forms' )
__( 'Placeholder', 'fp-forms' )
__( 'Descrizione', 'fp-forms' )
```

---

## 🎨 TONO DI VOCE

### **Formale (B2B, Legal, Finance):**
```
Label: "Ragione Sociale"
Placeholder: "Es: Acme S.p.A."
Descrizione: "Inserire la denominazione esatta come da registro imprese"
Errore: "La ragione sociale è obbligatoria per procedere"
```

### **Friendly (Consumer, E-commerce):**
```
Label: "Il tuo nome"
Placeholder: "Come ti chiami?"
Descrizione: "Solo il nome va benissimo!"
Errore: "Ehi, come possiamo chiamarti? 😊"
```

### **Tecnico (SaaS, Developer):**
```
Label: "API Key"
Placeholder: "sk_live_..."
Descrizione: "Format: 32 alphanumeric chars. Get from dashboard."
Errore: "Invalid API key format"
```

---

## 🔧 IMPLEMENTAZIONE TECNICA

### **Database Storage:**
```json
{
  "fields": [
    {
      "type": "email",
      "label": "Email Aziendale",
      "name": "email_aziendale",
      "required": true,
      "options": {
        "placeholder": "nome@tuaazienda.com",
        "description": "Solo email aziendali",
        "error_message": "Inserisci una email aziendale valida"
      }
    }
  ]
}
```

### **Rendering Frontend:**
```html
<div class="fp-forms-field">
    <label class="fp-forms-label">Email Aziendale</label>
    
    <input type="email" 
           name="email_aziendale"
           placeholder="nome@tuaazienda.com"
           class="fp-forms-input"
           required>
    
    <small class="fp-forms-description">
        Solo email aziendali
    </small>
    
    <!-- Messaggio errore (se validazione fallisce) -->
    <span class="fp-forms-error" style="display:none;">
        Inserisci una email aziendale valida
    </span>
</div>
```

### **Validation Logic:**
```php
// In Validator.php
public function validate_email( $value, $field_name, $field_label, $custom_message = '' ) {
    if ( ! is_email( $value ) ) {
        $error = ! empty( $custom_message ) 
            ? $custom_message 
            : sprintf( 'Email non valida per "%s"', $field_label );
        
        $this->add_error( $field_name, $error );
    }
}

// In Manager.php
$custom_error = $field['options']['error_message'] ?? '';
$validator->validate_email( $value, $name, $label, $custom_error );
```

---

## 📊 ACCESSIBILITY (A11Y)

**Label:**
- ✅ Sempre presente
- ✅ Collegata al campo (for/id)
- ✅ Screen reader legge la label

**Placeholder:**
- ⚠️ Non sostituisce la label
- ⚠️ Non sempre letto da screen reader
- ✅ Usa solo come hint visivo

**Description:**
- ✅ `aria-describedby` collegato
- ✅ Screen reader legge dopo la label

**Error Message:**
- ✅ `aria-invalid="true"` quando errore
- ✅ `role="alert"` per messaggio errore
- ✅ Screen reader annuncia l'errore

**Best Practices A11Y:**
```html
<!-- BUONO (accessible) -->
<label for="email">Email</label>
<input id="email" 
       type="email"
       placeholder="nome@example.com"
       aria-describedby="email-help"
       aria-invalid="false">
<small id="email-help">Ti invieremo la conferma</small>

<!-- CATTIVO (non accessible) -->
<input type="email" placeholder="Email"> <!-- No label! -->
```

---

## 🎯 CHECKLIST PER CAMPO

**Prima di pubblicare un campo:**

- [ ] ✅ Label chiara e descrittiva
- [ ] ✅ Nome campo lowercase, no spazi
- [ ] ✅ Placeholder = esempio, non istruzione
- [ ] ✅ Descrizione solo se necessaria
- [ ] ✅ Messaggio errore custom solo se migliora UX
- [ ] ✅ Required impostato correttamente
- [ ] ✅ Tono di voce coerente con brand
- [ ] ✅ Test su desktop
- [ ] ✅ Test su mobile
- [ ] ✅ Test validazione (invia vuoto/invalido)
- [ ] ✅ Test screen reader

---

## 📚 ESEMPI PER CASI D'USO

### **Form Contatti Aziendale:**
```
Nome Completo:
- Label: "Nome e Cognome"
- Placeholder: "Es: Mario Rossi"
- Errore: "Inserisci nome e cognome completi"

Email:
- Label: "Email"
- Placeholder: "nome@example.com"
- Descrizione: "Per inviarti la conferma"
- Errore: (default)

Telefono:
- Label: "Telefono"
- Placeholder: "+39 333 1234567"
- Descrizione: "Preferibilmente cellulare"
- Errore: (default)

Messaggio:
- Label: "La tua Richiesta"
- Placeholder: "Scrivi qui..."
- Descrizione: "Più dettagli fornisci, meglio potremo aiutarti"
- Errore: "Descrivi la tua richiesta per favore"
```

---

### **Form E-commerce Lead:**
```
Budget:
- Label: "Budget Disponibile (€)"
- Placeholder: "Es: 5000"
- Descrizione: "Indicaci un range approssimativo"
- Errore: "Il budget deve essere almeno 500€"

Scadenza:
- Label: "Quando ti serve?"
- Placeholder: "GG/MM/AAAA"
- Descrizione: "Entro quando vorresti il lavoro completato?"
- Errore: "Seleziona una data valida"
```

---

### **Form Candidatura:**
```
CV Upload:
- Label: "Carica il tuo CV"
- Descrizione: "PDF, DOC, DOCX. Max 5MB"
- Errore: "Il CV è obbligatorio per candidarti"

Lettera Motivazionale:
- Label: "Lettera di Presentazione"
- Placeholder: "Parlaci di te..."
- Descrizione: "Minimo 200 caratteri"
- Errore: "Scrivi almeno 200 caratteri per presentarti"
```

---

## ✅ CONCLUSIONE

**5 Testi Personalizzabili per Campo:**
1. ✅ Etichetta (Label)
2. ✅ Nome (Name - tecnico)
3. ✅ Placeholder
4. ✅ Descrizione (Help Text)
5. ✅ Messaggio Errore ⭐ NUOVO!

**Disponibili per tutti i tipi campo:**
- ✅ Text, Email, Phone, Number, Date, Textarea, Select, Radio, Checkbox, File
- ❌ Privacy/Marketing checkbox (testi specifici propri)
- ❌ reCAPTCHA (configurato globalmente)

**No Code Required:** Tutto configurabile dalla UI del Form Builder! 📝✨


**Versione:** v1.2.3  
**Feature:** Custom Field Texts & Validation Messages  
**Status:** ✅ **IMPLEMENTATO**

---

## 🎯 OVERVIEW

Ogni campo del form è **completamente personalizzabile** nei testi! Puoi modificare:

1. ✅ **Etichetta** (Label) - Già disponibile
2. ✅ **Nome Campo** (Name) - Già disponibile
3. ✅ **Placeholder** - Già disponibile
4. ✅ **Descrizione** (Help Text) - Già disponibile
5. ✅ **Messaggio Errore Personalizzato** ⭐ **NUOVO!**

---

## 📍 DOVE PERSONALIZZARE

**Percorso:** WP Admin → FP Forms → Modifica Form → Aggiungi Campo → Click "Modifica" sul campo

**Location:** Form Builder → Campo → Body (espanso)

---

## 📝 CAMPI PERSONALIZZABILI

### **1. Etichetta Campo** (Label)
```
Campo: Input text (required)
Default: Dipende dal tipo campo
Esempio: "Nome Completo", "Il tuo indirizzo email"
Visibile: Sopra il campo nel frontend
```

**Funzione:**
- Label principale del campo
- Usata nei messaggi di errore
- Screen reader accessible

**Esempi:**
```
Text: "Nome e Cognome"
Email: "La tua email"
Phone: "Numero di telefono (anche cellulare)"
Textarea: "Descrivi la tua richiesta"
```

---

### **2. Nome Campo** (Name)
```
Campo: Input text (required)
Default: Auto-generato (lowercase, no spazi)
Esempio: "nome_completo", "email", "telefono"
Visibile: No (solo tecnico)
```

**Funzione:**
- Identificatore univoco del campo
- Usato per salvare nel DB
- Usato in email templates come tag `{nome_campo}`

**Best Practices:**
```
✅ BUONO:
- nome
- email
- telefono
- data_nascita
- messaggio

❌ CATTIVO:
- Nome (maiuscola)
- il mio campo (spazi)
- campo-1 (numeri poco descrittivi)
- cämpö (caratteri speciali)
```

---

### **3. Placeholder**
```
Campo: Input text (opzionale)
Default: Vuoto
Esempio: "Es: Mario Rossi", "nome@example.com"
Visibile: Dentro il campo (grigio) quando vuoto
```

**Funzione:**
- Hint visivo per l'utente
- Scompare quando l'utente inizia a digitare
- Non sostituisce la label (accessibility)

**Esempi:**
```
Nome: "Es: Mario Rossi"
Email: "nome@example.com"
Phone: "+39 333 1234567"
Date: "GG/MM/AAAA"
Number: "Es: 1000"
Textarea: "Scrivi qui il tuo messaggio..."
```

**⚠️ Attenzione:**
```
❌ NON usare placeholder come label
❌ NON mettere informazioni critiche solo nel placeholder

✅ Usa label + placeholder insieme
✅ Placeholder = esempio, Label = descrizione
```

---

### **4. Descrizione** (Help Text)
```
Campo: Input text (opzionale)
Default: Vuoto
Esempio: "Inserisci il tuo numero principale per essere ricontattato"
Visibile: Sotto il campo (grigio, small)
```

**Funzione:**
- Testo di aiuto/chiarimento
- Informazioni aggiuntive
- Istruzioni specifiche

**Quando Usare:**
```
✅ Quando serve chiarire il campo:
- "Useremo questa email solo per inviarti la fattura"
- "Formato accettato: PDF, DOC, JPG (max 5MB)"
- "Seleziona almeno 2 opzioni"

✅ Per rassicurare l'utente:
- "Non condivideremo mai i tuoi dati"
- "Questa informazione è opzionale"
```

**Esempi Pratici:**
```
Email: "Ti invieremo una conferma a questo indirizzo"
Phone: "Formato: +39 oppure senza prefisso"
File: "Tipi accettati: PDF, JPG, PNG. Max 5MB"
Textarea: "Minimo 50 caratteri"
Date: "Inserisci la data dell'evento"
```

---

### **5. Messaggio Errore Personalizzato** ⭐ NUOVO!
```
Campo: Input text (opzionale)
Default: Messaggio automatico
Esempio: "Per favore, inserisci la tua email aziendale"
Visibile: Quando il campo non è valido (rosso)
```

**Funzione:**
- Messaggio mostrato se validazione fallisce
- Sostituisce il messaggio predefinito
- Più specifico e contestuale

**Messaggi Predefiniti (senza personalizzazione):**
```
Required: "Il campo "{label}" è obbligatorio."
Email: "Inserisci un indirizzo email valido per "{label}"."
Phone: "Inserisci un numero di telefono valido per "{label}"."
Number: "Inserisci un numero valido per "{label}"."
```

**Messaggi Personalizzati (esempi):**
```
Email aziendale:
→ "Inserisci la tua email aziendale (non Gmail/Hotmail)"

Telefono urgenza:
→ "Questo numero è obbligatorio per contattarti in caso di emergenza"

Budget minimo:
→ "Il budget deve essere almeno 1000€"

Nome completo:
→ "Inserisci nome e cognome per favore"

Messaggio dettagliato:
→ "Descrivi la tua richiesta in almeno 50 caratteri"
```

**Quando Personalizzare:**
```
✅ Quando il messaggio default è troppo generico
✅ Quando serve un tono più friendly/formale
✅ Quando ci sono requisiti specifici
✅ Quando vuoi guidare l'utente meglio

❌ Non serve se il messaggio default va bene
❌ Non ripetere semplicemente la label
```

---

## 🎨 ESEMPI COMPLETI PER CAMPO

### **Esempio 1: Campo Email (Standard)**
```
Etichetta: Email
Nome: email
Placeholder: nome@example.com
Descrizione: Ti invieremo la conferma a questo indirizzo
Messaggio Errore: (vuoto = usa default)
Required: ✅ Sì
```
**Risultato:** Campo email standard con help text rassicurante

---

### **Esempio 2: Campo Email (Aziendale)**
```
Etichetta: Email Aziendale
Nome: email_aziendale
Placeholder: nome@tuaazienda.com
Descrizione: Solo indirizzi email aziendali, no Gmail/Hotmail
Messaggio Errore: Inserisci una email aziendale valida (non Gmail o Hotmail)
Required: ✅ Sì
```
**Risultato:** Campo email con validazione specifica e messaggio custom

---

### **Esempio 3: Campo Telefono**
```
Etichetta: Numero di Telefono
Nome: telefono
Placeholder: +39 333 1234567
Descrizione: Preferibilmente cellulare per essere ricontattato velocemente
Messaggio Errore: Inserisci un numero valido con prefisso (+39)
Required: ✅ Sì
```
**Risultato:** Campo telefono con formato chiaro

---

### **Esempio 4: Campo Nome Completo**
```
Etichetta: Nome e Cognome
Nome: nome_completo
Placeholder: Es: Mario Rossi
Descrizione: (vuoto)
Messaggio Errore: Per favore inserisci nome e cognome completi
Required: ✅ Sì
```
**Risultato:** Campo nome con messaggio friendly

---

### **Esempio 5: Campo Budget**
```
Etichetta: Budget Disponibile (€)
Nome: budget
Placeholder: Es: 5000
Descrizione: Indicaci il budget indicativo per il progetto
Messaggio Errore: Il budget deve essere almeno 500€
Required: ✅ Sì
Type: Number
```
**Risultato:** Campo numero con requisito minimo chiaro

---

### **Esempio 6: Campo Messaggio**
```
Etichetta: Descrivi la tua Richiesta
Nome: messaggio
Placeholder: Scrivi qui il tuo messaggio...
Descrizione: Più dettagli fornisci, meglio potremo aiutarti (minimo 50 caratteri)
Messaggio Errore: Per favore descrivi la tua richiesta in modo più dettagliato
Required: ✅ Sì
Type: Textarea
```
**Risultato:** Textarea con incoraggiamento a essere dettagliati

---

### **Esempio 7: Campo File Upload**
```
Etichetta: Carica il tuo CV
Nome: cv_file
Placeholder: (N/A per file)
Descrizione: Formati accettati: PDF, DOC, DOCX. Dimensione massima: 5MB
Messaggio Errore: Il file CV è obbligatorio per completare la candidatura
Required: ✅ Sì
Type: File
```
**Risultato:** File upload con specifiche chiare

---

### **Esempio 8: Campo Data Evento**
```
Etichetta: Data dell'Evento
Nome: data_evento
Placeholder: GG/MM/AAAA
Descrizione: Quando vorresti che si svolga l'evento?
Messaggio Errore: Seleziona una data valida per l'evento
Required: ✅ Sì
Type: Date
```
**Risultato:** Date picker con contesto chiaro

---

## 🎯 BEST PRACTICES

### **Labels (Etichette):**
```
✅ BUONO:
- "Nome e Cognome"
- "Il tuo indirizzo email"
- "Numero di telefono principale"
- "Budget disponibile (€)"

❌ CATTIVO:
- "Nome:" (non serve il :)
- "inserisci qui" (non descrittivo)
- "NOME" (non urlare)
- "Il tuo nome per favore grazie" (troppo verboso)
```

### **Placeholders:**
```
✅ BUONO:
- "Es: Mario Rossi"
- "nome@example.com"
- "+39 333 1234567"
- "Scrivi qui..."

❌ CATTIVO:
- "Nome" (ripete la label)
- "Obbligatorio" (usa required flag)
- Placeholder troppo lungo (>50 caratteri)
- Placeholder con istruzioni critiche
```

### **Descrizioni:**
```
✅ BUONO:
- "Useremo questa email solo per la conferma"
- "Formato: PDF, JPG. Max 5MB"
- "Seleziona almeno 2 opzioni"

❌ CATTIVO:
- Ripetere la label
- Informazioni già nel placeholder
- Descrizione troppo lunga (>100 caratteri)
```

### **Messaggi Errore:**
```
✅ BUONO:
- "Inserisci una email aziendale valida"
- "Il budget deve essere almeno 500€"
- "Per favore descrivi la tua richiesta"

❌ CATTIVO:
- "Errore!" (non descrittivo)
- "Hai sbagliato" (colpevolizzante)
- "Il campo è richiesto" (ripete il default)
```

---

## 🌍 MULTILINGUA

**Attualmente:** Testi in italiano

**Per Multilingua:**
1. Usa plugin WPML / Polylang
2. Traduci le stringhe del form
3. Oppure: Crea form separati per lingua

**Tag tradotti automaticamente:**
```
__( 'Etichetta Campo', 'fp-forms' )
__( 'Placeholder', 'fp-forms' )
__( 'Descrizione', 'fp-forms' )
```

---

## 🎨 TONO DI VOCE

### **Formale (B2B, Legal, Finance):**
```
Label: "Ragione Sociale"
Placeholder: "Es: Acme S.p.A."
Descrizione: "Inserire la denominazione esatta come da registro imprese"
Errore: "La ragione sociale è obbligatoria per procedere"
```

### **Friendly (Consumer, E-commerce):**
```
Label: "Il tuo nome"
Placeholder: "Come ti chiami?"
Descrizione: "Solo il nome va benissimo!"
Errore: "Ehi, come possiamo chiamarti? 😊"
```

### **Tecnico (SaaS, Developer):**
```
Label: "API Key"
Placeholder: "sk_live_..."
Descrizione: "Format: 32 alphanumeric chars. Get from dashboard."
Errore: "Invalid API key format"
```

---

## 🔧 IMPLEMENTAZIONE TECNICA

### **Database Storage:**
```json
{
  "fields": [
    {
      "type": "email",
      "label": "Email Aziendale",
      "name": "email_aziendale",
      "required": true,
      "options": {
        "placeholder": "nome@tuaazienda.com",
        "description": "Solo email aziendali",
        "error_message": "Inserisci una email aziendale valida"
      }
    }
  ]
}
```

### **Rendering Frontend:**
```html
<div class="fp-forms-field">
    <label class="fp-forms-label">Email Aziendale</label>
    
    <input type="email" 
           name="email_aziendale"
           placeholder="nome@tuaazienda.com"
           class="fp-forms-input"
           required>
    
    <small class="fp-forms-description">
        Solo email aziendali
    </small>
    
    <!-- Messaggio errore (se validazione fallisce) -->
    <span class="fp-forms-error" style="display:none;">
        Inserisci una email aziendale valida
    </span>
</div>
```

### **Validation Logic:**
```php
// In Validator.php
public function validate_email( $value, $field_name, $field_label, $custom_message = '' ) {
    if ( ! is_email( $value ) ) {
        $error = ! empty( $custom_message ) 
            ? $custom_message 
            : sprintf( 'Email non valida per "%s"', $field_label );
        
        $this->add_error( $field_name, $error );
    }
}

// In Manager.php
$custom_error = $field['options']['error_message'] ?? '';
$validator->validate_email( $value, $name, $label, $custom_error );
```

---

## 📊 ACCESSIBILITY (A11Y)

**Label:**
- ✅ Sempre presente
- ✅ Collegata al campo (for/id)
- ✅ Screen reader legge la label

**Placeholder:**
- ⚠️ Non sostituisce la label
- ⚠️ Non sempre letto da screen reader
- ✅ Usa solo come hint visivo

**Description:**
- ✅ `aria-describedby` collegato
- ✅ Screen reader legge dopo la label

**Error Message:**
- ✅ `aria-invalid="true"` quando errore
- ✅ `role="alert"` per messaggio errore
- ✅ Screen reader annuncia l'errore

**Best Practices A11Y:**
```html
<!-- BUONO (accessible) -->
<label for="email">Email</label>
<input id="email" 
       type="email"
       placeholder="nome@example.com"
       aria-describedby="email-help"
       aria-invalid="false">
<small id="email-help">Ti invieremo la conferma</small>

<!-- CATTIVO (non accessible) -->
<input type="email" placeholder="Email"> <!-- No label! -->
```

---

## 🎯 CHECKLIST PER CAMPO

**Prima di pubblicare un campo:**

- [ ] ✅ Label chiara e descrittiva
- [ ] ✅ Nome campo lowercase, no spazi
- [ ] ✅ Placeholder = esempio, non istruzione
- [ ] ✅ Descrizione solo se necessaria
- [ ] ✅ Messaggio errore custom solo se migliora UX
- [ ] ✅ Required impostato correttamente
- [ ] ✅ Tono di voce coerente con brand
- [ ] ✅ Test su desktop
- [ ] ✅ Test su mobile
- [ ] ✅ Test validazione (invia vuoto/invalido)
- [ ] ✅ Test screen reader

---

## 📚 ESEMPI PER CASI D'USO

### **Form Contatti Aziendale:**
```
Nome Completo:
- Label: "Nome e Cognome"
- Placeholder: "Es: Mario Rossi"
- Errore: "Inserisci nome e cognome completi"

Email:
- Label: "Email"
- Placeholder: "nome@example.com"
- Descrizione: "Per inviarti la conferma"
- Errore: (default)

Telefono:
- Label: "Telefono"
- Placeholder: "+39 333 1234567"
- Descrizione: "Preferibilmente cellulare"
- Errore: (default)

Messaggio:
- Label: "La tua Richiesta"
- Placeholder: "Scrivi qui..."
- Descrizione: "Più dettagli fornisci, meglio potremo aiutarti"
- Errore: "Descrivi la tua richiesta per favore"
```

---

### **Form E-commerce Lead:**
```
Budget:
- Label: "Budget Disponibile (€)"
- Placeholder: "Es: 5000"
- Descrizione: "Indicaci un range approssimativo"
- Errore: "Il budget deve essere almeno 500€"

Scadenza:
- Label: "Quando ti serve?"
- Placeholder: "GG/MM/AAAA"
- Descrizione: "Entro quando vorresti il lavoro completato?"
- Errore: "Seleziona una data valida"
```

---

### **Form Candidatura:**
```
CV Upload:
- Label: "Carica il tuo CV"
- Descrizione: "PDF, DOC, DOCX. Max 5MB"
- Errore: "Il CV è obbligatorio per candidarti"

Lettera Motivazionale:
- Label: "Lettera di Presentazione"
- Placeholder: "Parlaci di te..."
- Descrizione: "Minimo 200 caratteri"
- Errore: "Scrivi almeno 200 caratteri per presentarti"
```

---

## ✅ CONCLUSIONE

**5 Testi Personalizzabili per Campo:**
1. ✅ Etichetta (Label)
2. ✅ Nome (Name - tecnico)
3. ✅ Placeholder
4. ✅ Descrizione (Help Text)
5. ✅ Messaggio Errore ⭐ NUOVO!

**Disponibili per tutti i tipi campo:**
- ✅ Text, Email, Phone, Number, Date, Textarea, Select, Radio, Checkbox, File
- ❌ Privacy/Marketing checkbox (testi specifici propri)
- ❌ reCAPTCHA (configurato globalmente)

**No Code Required:** Tutto configurabile dalla UI del Form Builder! 📝✨






























