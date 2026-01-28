# 📊 Tracking Eventi Avanzati - FP Forms

## 🎯 PANORAMICA

FP Forms traccia **15+ eventi** per un funnel completo di conversione su **3 piattaforme**:
- 🔵 **Google Tag Manager** (GTM)
- 🟢 **Google Analytics 4** (GA4)
- 🔴 **Meta Pixel** (Facebook/Instagram)

Tutti gli eventi sono **sincronizzati** e tracciati in parallelo per massima visibilità.

---

## 📋 EVENTI TRACCIATI

### **1️⃣ AWARENESS - Visualizzazione**

#### **Form View** (Caricamento pagina con form)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_view',
  'form_id': 123,
  'form_title': 'Contact Form',
  'form_name': 'FP Form #123'
}
```

**GA4:**
```javascript
gtag('event', 'form_view', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'form_type': 'fp_forms'
});
```

**Meta Pixel:**
```javascript
fbq('track', 'ViewContent', {
  content_name: 'Contact Form',
  content_category: 'form',
  content_ids: [123],
  content_type: 'form_view'
});
```

---

### **2️⃣ INTEREST - Inizio Compilazione**

#### **Form Start** (Primo campo focus)

**Quando:** Utente clicca/fa focus sul primo campo

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_start',
  'form_id': 123,
  'form_title': 'Contact Form',
  'event_category': 'engagement'
}
```

**GA4:**
```javascript
gtag('event', 'form_start', {
  'form_id': 123,
  'form_name': 'Contact Form'
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormStart', {
  form_id: 123,
  form_title: 'Contact Form',
  event_category: 'engagement'
});
```

---

### **3️⃣ CONSIDERATION - Progressione**

#### **Form Progress** (25%, 50%, 75% compilazione)

**Quando:** L'utente compila campi e raggiunge milestone

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_progress',
  'form_id': 123,
  'form_title': 'Contact Form',
  'progress_percent': 50,
  'event_category': 'engagement'
}
```

**GA4:**
```javascript
gtag('event', 'form_progress', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'progress': 50
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormProgress', {
  form_id: 123,
  form_title: 'Contact Form',
  progress_percent: 50,
  event_category: 'engagement'
});
```

**Milestone tracciati:**
- ✅ 25% - Primo quartile compilato
- ✅ 50% - Metà form compilato
- ✅ 75% - Quasi completo

---

### **4️⃣ CONVERSION - Submission Completata**

#### **Form Submit** (Submission con successo)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_submit',
  'form_id': 123,
  'form_title': 'Contact Form',
  'form_status': 'success',
  'time_to_complete': 45 // secondi
}
```

**GA4:**
```javascript
// Event 1: Form Submit
gtag('event', 'form_submit', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'success': true,
  'engagement_time_msec': 45000
});

// Event 2: Generate Lead (standard)
gtag('event', 'generate_lead', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'value': 1.0,
  'currency': 'EUR'
});

// Event 3: Conversion
gtag('event', 'conversion', {
  'send_to': 'AW-CONVERSION_ID',
  'form_id': 123,
  'value': 1.0,
  'currency': 'EUR'
});
```

**Meta Pixel:**
```javascript
// Event 1: Lead (STANDARD - per Ads)
fbq('track', 'Lead', {
  content_name: 'Contact Form',
  content_category: 'form_submission',
  content_ids: [123],
  value: 1.0,
  currency: 'EUR',
  status: 'completed'
});

// Event 2: CompleteRegistration (se signup/registrazione)
fbq('track', 'CompleteRegistration', {
  content_name: 'Contact Form',
  status: 'completed',
  value: 1.0,
  currency: 'EUR'
});

// Event 3: Custom FormSubmission
fbq('trackCustom', 'FormSubmission', {
  form_id: 123,
  form_title: 'Contact Form',
  submission_id: 456,
  time_spent_seconds: 45,
  event_category: 'conversion'
});
```

**Server-Side (Conversions API):**
```php
POST /v18.0/{pixel_id}/events
{
  "event_name": "Lead",
  "event_time": 1699224300,
  "action_source": "website",
  "user_data": {
    "em": "sha256(email)",
    "fn": "sha256(nome)",
    "ln": "sha256(cognome)",
    ...
  },
  "custom_data": {
    "form_id": 123,
    "value": 1.0,
    "currency": "EUR"
  }
}
```

---

### **5️⃣ RETENTION - Conversione Avanzata**

#### **Form Conversion** (GTM-specific)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_conversion',
  'form_id': 123,
  'form_title': 'Contact Form',
  'conversion_type': 'form_submission',
  'conversion_value': 1.0
}
```

Usabile per:
- Google Ads conversion tracking
- Enhanced conversions
- Cross-domain tracking

---

### **6️⃣ DROP-OFF - Abbandono Form**

#### **Form Abandon** (Pagina chiusa senza submit)

**Quando:** Utente inizia compilazione ma esce dalla pagina senza completare

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_abandon',
  'form_id': 123,
  'form_title': 'Contact Form',
  'time_spent_seconds': 15,
  'event_category': 'abandonment'
}
```

**GA4:**
```javascript
gtag('event', 'form_abandon', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'time_spent': 15
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormAbandoned', {
  form_id: 123,
  form_title: 'Contact Form',
  time_spent_seconds: 15,
  event_category: 'abandonment'
});
```

**Utile per:**
- Remarketing utenti che abbandonano
- A/B test ottimizzazione form
- Identificare campi problematici

---

### **7️⃣ ERROR TRACKING - Errori Validazione**

#### **Validation Error** (Campo non valido)

**Quando:** Utente invia form con errori validazione

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_validation_error',
  'form_id': 123,
  'form_title': 'Contact Form',
  'error_field': 'email',
  'error_message': 'Email non valida',
  'event_category': 'error'
}
```

**GA4:**
```javascript
gtag('event', 'form_error', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'error_field': 'email',
  'error_type': 'validation'
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormValidationError', {
  form_id: 123,
  form_title: 'Contact Form',
  field_name: 'email',
  error_message: 'Email non valida',
  event_category: 'error'
});
```

**Errori tracciati:**
- Campo obbligatorio vuoto
- Email non valida
- Telefono non valido
- File troppo grande
- reCAPTCHA fallito
- Errori server

---

### **8️⃣ ENGAGEMENT - Interazioni Campi** (Opzionale)

#### **Field Interaction** (Focus su campo specifico)

**Quando:** Utente fa focus su un campo (se `track_interactions` = true)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_field_interaction',
  'form_id': 123,
  'field_name': 'telefono',
  'field_type': 'tel'
}
```

⚠️ **Attenzione:** Genera molti eventi! Usare solo per analisi dettagliate.

---

## 📈 FUNNEL COMPLETO TRACCIATO

```
👁️  Form View (100 utenti)
        ↓ -20%
✏️  Form Start (80 utenti)      ← Iniziano a compilare
        ↓ -15%
📊  Progress 25% (68 utenti)
        ↓ -10%
📊  Progress 50% (61 utenti)
        ↓ -8%
📊  Progress 75% (56 utenti)
        ↓ -6%
✅  Form Submit (53 utenti)      ← CONVERSIONE!
        ↓
🎯  Lead in CRM
```

**Drop-off Points:**
- 20% abbandona prima di iniziare
- 15% abbandona al 25%
- 10% abbandona al 50%
- 8% abbandona al 75%
- 6% errori validazione

**Conversion Rate:** 53% (53/100)

---

## 🎛️ TIMING METRICS

### **Time to Complete**
Tracciato automaticamente da primo focus a submit:

```javascript
// Salvato in ogni evento submission
time_to_complete: 45 // secondi
time_spent_seconds: 45
engagement_time_msec: 45000
```

**Statistiche utili:**
- Tempo medio compilazione
- Identificare form troppo lunghi
- Ottimizzare UX

---

## 📊 DASHBOARD & REPORTING

### **Google Analytics 4**

**Funnel Exploration:**
```
Step 1: form_view          (100%)
Step 2: form_start         (80%)
Step 3: form_progress (25) (68%)
Step 4: form_progress (50) (61%)
Step 5: form_progress (75) (56%)
Step 6: form_submit        (53%) ← Conversion
```

**Metriche Chiave:**
- Conversion Rate per form
- Tempo medio completamento
- Drop-off rate per step
- Errori validazione più frequenti

### **Google Tag Manager**

**Trigger Utili:**
```
1. Form Start → GA4 Event + Remarketing Tag
2. Form Progress 50% → Remarketing specifico
3. Form Abandon → Email recovery automation
4. Form Submit → Conversione + Thank You pixel
5. Validation Error → Debug + A/B test
```

### **Meta Events Manager**

**Eventi Standard (usabili in Ads):**
- ✅ `PageView` - Tutte le pagine
- ✅ `ViewContent` - Form visto
- ✅ `Lead` - **CONVERSIONE PRINCIPALE**
- ✅ `CompleteRegistration` - Signup forms

**Eventi Custom (analytics):**
- `FormStart` - Engagement
- `FormProgress` - Micro-conversioni
- `FormAbandoned` - Remarketing
- `FormValidationError` - Ottimizzazione
- `FormSubmission` - Tracking dettagliato

---

## 🎯 CASI D'USO

### **1. Remarketing Intelligente**

**Google Ads:**
```
Audience: Utenti che hanno fatto "form_start" MA NON "form_submit"
Messaggio: "Hai iniziato la tua richiesta, completa ora!"
```

**Meta Ads:**
```
Audience: FormStart E NON Lead (ultimi 7 giorni)
Placement: Facebook Feed + Instagram Stories
Objective: Conversioni
```

### **2. Ottimizzazione Form**

**Identifica colli di bottiglia:**
```sql
-- GA4 Query
SELECT 
  error_field,
  COUNT(*) as error_count
FROM form_validation_error
GROUP BY error_field
ORDER BY error_count DESC
```

**Risultato esempio:**
```
email: 45 errori      → Migliora label/placeholder
telefono: 32 errori   → Aggiungi format helper
privacy: 18 errori    → Rendi più visibile
```

### **3. A/B Testing**

**Testa varianti form:**
- Form A (5 campi) vs Form B (3 campi)
- Confronta: conversion rate, tempo medio, abandon rate
- Ottimizza basandoti su dati reali

### **4. Lead Scoring**

**Assegna punteggi:**
```
Form View only:          +10 punti
Form Start:              +25 punti
Progress 50%:            +40 punti
Progress 75%:            +60 punti
Form Submit (Lead):      +100 punti ✅
```

---

## 🔧 CONFIGURAZIONE

### **Settings Globali**
**FP Forms → Impostazioni**

**Google Tag Manager:**
```
GTM ID: GTM-XXXXXXX
☑️ Track Form Views
☐ Track Field Interactions (genera molti eventi)
```

**Google Analytics 4:**
```
GA4 ID: G-XXXXXXXXXX
☑️ Track Form Views
☐ Track Field Interactions
```

**Meta Pixel:**
```
Pixel ID: 1234567890123456
Access Token: EAAG... (Conversions API)
☑️ Track Form Views
```

---

## 📊 EVENTI PER PIATTAFORMA

### **GTM - Tutti gli Eventi**

| Evento | Trigger | Category | Parametri |
|--------|---------|----------|-----------|
| `fp_form_view` | Page load | impression | form_id, form_title |
| `fp_form_start` | First focus | engagement | form_id, form_title |
| `fp_form_progress` | Input change | engagement | form_id, progress_percent |
| `fp_form_abandon` | Page unload | abandonment | form_id, time_spent |
| `fp_form_validation_error` | Submit error | error | form_id, error_field |
| `fp_form_submit` | Submit success | conversion | form_id, time_to_complete |
| `fp_form_conversion` | Submit success | conversion | form_id, conversion_value |
| `fp_form_error` | Submit fail | error | form_id, error_message |
| `fp_form_field_interaction` | Field focus | engagement | form_id, field_name |

**Totale:** 9 eventi GTM

### **GA4 - Eventi Standard + Custom**

| Evento | Tipo | Enhanced Measurement | Parametri |
|--------|------|---------------------|-----------|
| `form_view` | Custom | No | form_id, form_name |
| `form_start` | Standard | Sì | form_id, form_name |
| `form_progress` | Custom | No | form_id, progress |
| `form_abandon` | Custom | No | form_id, time_spent |
| `form_error` | Custom | No | form_id, error_type |
| `form_submit` | Standard | Sì | form_id, success |
| `generate_lead` | Standard | Sì | form_id, value, currency |
| `conversion` | Standard | Sì | send_to, value, currency |

**Totale:** 8 eventi GA4 (5 standard + 3 custom)

### **Meta Pixel - Eventi Standard + Custom**

| Evento | Tipo | Ads Compatible | Parametri |
|--------|------|----------------|-----------|
| `PageView` | Standard | Sì | - |
| `ViewContent` | Standard | Sì | content_name, content_ids |
| `Lead` | Standard | **Sì** | content_name, value, currency |
| `CompleteRegistration` | Standard | Sì | content_name, value |
| `FormStart` | Custom | No | form_id, form_title |
| `FormProgress` | Custom | No | form_id, progress_percent |
| `FormAbandoned` | Custom | No | form_id, time_spent |
| `FormValidationError` | Custom | No | form_id, field_name |
| `FormSubmission` | Custom | No | form_id, submission_id |

**Totale:** 9 eventi Meta (4 standard + 5 custom)

---

## 🎨 COME USARE IN META ADS

### **Campagne Lead Generation**

**Optimization Event:** `Lead` ✅ (già tracciato!)

**Custom Audiences:**
```
1. Form Viewers (remarketing)
   → ViewContent E NON Lead (ultimi 7 giorni)

2. Form Starters (warm)
   → FormStart E NON Lead (ultimi 3 giorni)

3. Form Progressers (hot)
   → FormProgress 75% E NON Lead (ultimi 24 ore)

4. Form Abandoners
   → FormAbandoned (ultimi 7 giorni)
```

**Lookalike Audiences:**
```
Source: Lead events (ultimi 180 giorni)
Size: 1% - 3%
Location: Italia
```

### **Campagne Retargeting**

**Sequence:**
```
Day 1-3:  Ad to Form Viewers (ViewContent)
Day 4-7:  Ad to Form Starters (FormStart)
Day 8-14: Ad to Form Abandoners (FormAbandoned)
```

---

## 🔬 ANALISI AVANZATE

### **Conversion Funnel (GA4)**

```
100 Form Views
 ↓ 80% start
 80 Form Starts
 ↓ 85% progress 25%
 68 Progress 25%
 ↓ 90% progress 50%
 61 Progress 50%
 ↓ 92% progress 75%
 56 Progress 75%
 ↓ 95% submit
 53 Submissions ✅

Conversion Rate: 53%
Average Time: 45s
Drop-off Points: Start (-20%), 25% (-15%)
```

### **Error Analysis**

**Top Errors:**
```
1. email (45 volte) - 32% errori
2. telefono (32 volte) - 23% errori
3. privacy (18 volte) - 13% errori
```

**Action Items:**
- Migliora validazione email in real-time
- Aggiungi format mask per telefono
- Rendi checkbox privacy più evidente

### **Timing Metrics**

**Distribuzione tempo compilazione:**
```
0-15s:   12% (troppo veloce - possibile spam)
15-30s:  25% (veloce)
30-60s:  45% (normale) ✅
60-120s: 15% (lento)
120s+:   3% (molto lento - possibile issue)
```

---

## 🚀 BEST PRACTICES

### **Setup Essenziale:**
✅ GTM Container installato
✅ GA4 Property configurata
✅ Meta Pixel installato
✅ Conversions API configurata (Meta)
✅ Eventi testati in preview mode

### **Privacy & GDPR:**
✅ Cookie consent banner
✅ Privacy policy aggiornata
✅ Dati PII hashed (Meta CAPI)
✅ Opt-out mechanism
✅ Data retention configurata

### **Testing:**
1. **GTM Preview Mode** → Verifica dataLayer events
2. **GA4 DebugView** → Verifica eventi real-time
3. **Meta Pixel Helper** → Verifica pixel events
4. **Console Browser** → Check log `[FP Forms ...]`

### **Monitoring:**
- Dashboard settimanale conversioni
- Alert se conversion rate < soglia
- Report mensile funnel analysis
- Quarterly optimization review

---

## 📱 EVENTI MOBILI vs DESKTOP

Tutti gli eventi funzionano identicamente su:
- ✅ Desktop
- ✅ Mobile browser
- ✅ Tablet
- ✅ Progressive Web App (PWA)

**Touch events** gestiti automaticamente (focus su tap).

---

## 🎯 SUMMARY EVENTI

**Totale Eventi Implementati:** 15+

**Per Funnel Stage:**
- Awareness: 1 evento (View)
- Interest: 1 evento (Start)
- Consideration: 3 eventi (Progress 25/50/75)
- Conversion: 4 eventi (Submit, Lead, Registration, Conversion)
- Retention: 1 evento (Conversion GTM)
- Drop-off: 1 evento (Abandon)
- Error: 2 eventi (Validation, Submit error)
- Engagement: 1 evento (Field interactions)

**Piattaforme:**
- 🔵 GTM: 9 eventi
- 🟢 GA4: 8 eventi
- 🔴 Meta: 9 eventi

**Totale unique events cross-platform:** 26 combinazioni

---

**Status:** ✅ Tracking eventi avanzati completo e production-ready!

**Last Update:** 5 Novembre 2025, 23:59 CET



## 🎯 PANORAMICA

FP Forms traccia **15+ eventi** per un funnel completo di conversione su **3 piattaforme**:
- 🔵 **Google Tag Manager** (GTM)
- 🟢 **Google Analytics 4** (GA4)
- 🔴 **Meta Pixel** (Facebook/Instagram)

Tutti gli eventi sono **sincronizzati** e tracciati in parallelo per massima visibilità.

---

## 📋 EVENTI TRACCIATI

### **1️⃣ AWARENESS - Visualizzazione**

#### **Form View** (Caricamento pagina con form)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_view',
  'form_id': 123,
  'form_title': 'Contact Form',
  'form_name': 'FP Form #123'
}
```

**GA4:**
```javascript
gtag('event', 'form_view', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'form_type': 'fp_forms'
});
```

**Meta Pixel:**
```javascript
fbq('track', 'ViewContent', {
  content_name: 'Contact Form',
  content_category: 'form',
  content_ids: [123],
  content_type: 'form_view'
});
```

---

### **2️⃣ INTEREST - Inizio Compilazione**

#### **Form Start** (Primo campo focus)

**Quando:** Utente clicca/fa focus sul primo campo

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_start',
  'form_id': 123,
  'form_title': 'Contact Form',
  'event_category': 'engagement'
}
```

**GA4:**
```javascript
gtag('event', 'form_start', {
  'form_id': 123,
  'form_name': 'Contact Form'
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormStart', {
  form_id: 123,
  form_title: 'Contact Form',
  event_category: 'engagement'
});
```

---

### **3️⃣ CONSIDERATION - Progressione**

#### **Form Progress** (25%, 50%, 75% compilazione)

**Quando:** L'utente compila campi e raggiunge milestone

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_progress',
  'form_id': 123,
  'form_title': 'Contact Form',
  'progress_percent': 50,
  'event_category': 'engagement'
}
```

**GA4:**
```javascript
gtag('event', 'form_progress', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'progress': 50
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormProgress', {
  form_id: 123,
  form_title: 'Contact Form',
  progress_percent: 50,
  event_category: 'engagement'
});
```

**Milestone tracciati:**
- ✅ 25% - Primo quartile compilato
- ✅ 50% - Metà form compilato
- ✅ 75% - Quasi completo

---

### **4️⃣ CONVERSION - Submission Completata**

#### **Form Submit** (Submission con successo)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_submit',
  'form_id': 123,
  'form_title': 'Contact Form',
  'form_status': 'success',
  'time_to_complete': 45 // secondi
}
```

**GA4:**
```javascript
// Event 1: Form Submit
gtag('event', 'form_submit', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'success': true,
  'engagement_time_msec': 45000
});

// Event 2: Generate Lead (standard)
gtag('event', 'generate_lead', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'value': 1.0,
  'currency': 'EUR'
});

// Event 3: Conversion
gtag('event', 'conversion', {
  'send_to': 'AW-CONVERSION_ID',
  'form_id': 123,
  'value': 1.0,
  'currency': 'EUR'
});
```

**Meta Pixel:**
```javascript
// Event 1: Lead (STANDARD - per Ads)
fbq('track', 'Lead', {
  content_name: 'Contact Form',
  content_category: 'form_submission',
  content_ids: [123],
  value: 1.0,
  currency: 'EUR',
  status: 'completed'
});

// Event 2: CompleteRegistration (se signup/registrazione)
fbq('track', 'CompleteRegistration', {
  content_name: 'Contact Form',
  status: 'completed',
  value: 1.0,
  currency: 'EUR'
});

// Event 3: Custom FormSubmission
fbq('trackCustom', 'FormSubmission', {
  form_id: 123,
  form_title: 'Contact Form',
  submission_id: 456,
  time_spent_seconds: 45,
  event_category: 'conversion'
});
```

**Server-Side (Conversions API):**
```php
POST /v18.0/{pixel_id}/events
{
  "event_name": "Lead",
  "event_time": 1699224300,
  "action_source": "website",
  "user_data": {
    "em": "sha256(email)",
    "fn": "sha256(nome)",
    "ln": "sha256(cognome)",
    ...
  },
  "custom_data": {
    "form_id": 123,
    "value": 1.0,
    "currency": "EUR"
  }
}
```

---

### **5️⃣ RETENTION - Conversione Avanzata**

#### **Form Conversion** (GTM-specific)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_conversion',
  'form_id': 123,
  'form_title': 'Contact Form',
  'conversion_type': 'form_submission',
  'conversion_value': 1.0
}
```

Usabile per:
- Google Ads conversion tracking
- Enhanced conversions
- Cross-domain tracking

---

### **6️⃣ DROP-OFF - Abbandono Form**

#### **Form Abandon** (Pagina chiusa senza submit)

**Quando:** Utente inizia compilazione ma esce dalla pagina senza completare

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_abandon',
  'form_id': 123,
  'form_title': 'Contact Form',
  'time_spent_seconds': 15,
  'event_category': 'abandonment'
}
```

**GA4:**
```javascript
gtag('event', 'form_abandon', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'time_spent': 15
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormAbandoned', {
  form_id: 123,
  form_title: 'Contact Form',
  time_spent_seconds: 15,
  event_category: 'abandonment'
});
```

**Utile per:**
- Remarketing utenti che abbandonano
- A/B test ottimizzazione form
- Identificare campi problematici

---

### **7️⃣ ERROR TRACKING - Errori Validazione**

#### **Validation Error** (Campo non valido)

**Quando:** Utente invia form con errori validazione

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_validation_error',
  'form_id': 123,
  'form_title': 'Contact Form',
  'error_field': 'email',
  'error_message': 'Email non valida',
  'event_category': 'error'
}
```

**GA4:**
```javascript
gtag('event', 'form_error', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'error_field': 'email',
  'error_type': 'validation'
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormValidationError', {
  form_id: 123,
  form_title: 'Contact Form',
  field_name: 'email',
  error_message: 'Email non valida',
  event_category: 'error'
});
```

**Errori tracciati:**
- Campo obbligatorio vuoto
- Email non valida
- Telefono non valido
- File troppo grande
- reCAPTCHA fallito
- Errori server

---

### **8️⃣ ENGAGEMENT - Interazioni Campi** (Opzionale)

#### **Field Interaction** (Focus su campo specifico)

**Quando:** Utente fa focus su un campo (se `track_interactions` = true)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_field_interaction',
  'form_id': 123,
  'field_name': 'telefono',
  'field_type': 'tel'
}
```

⚠️ **Attenzione:** Genera molti eventi! Usare solo per analisi dettagliate.

---

## 📈 FUNNEL COMPLETO TRACCIATO

```
👁️  Form View (100 utenti)
        ↓ -20%
✏️  Form Start (80 utenti)      ← Iniziano a compilare
        ↓ -15%
📊  Progress 25% (68 utenti)
        ↓ -10%
📊  Progress 50% (61 utenti)
        ↓ -8%
📊  Progress 75% (56 utenti)
        ↓ -6%
✅  Form Submit (53 utenti)      ← CONVERSIONE!
        ↓
🎯  Lead in CRM
```

**Drop-off Points:**
- 20% abbandona prima di iniziare
- 15% abbandona al 25%
- 10% abbandona al 50%
- 8% abbandona al 75%
- 6% errori validazione

**Conversion Rate:** 53% (53/100)

---

## 🎛️ TIMING METRICS

### **Time to Complete**
Tracciato automaticamente da primo focus a submit:

```javascript
// Salvato in ogni evento submission
time_to_complete: 45 // secondi
time_spent_seconds: 45
engagement_time_msec: 45000
```

**Statistiche utili:**
- Tempo medio compilazione
- Identificare form troppo lunghi
- Ottimizzare UX

---

## 📊 DASHBOARD & REPORTING

### **Google Analytics 4**

**Funnel Exploration:**
```
Step 1: form_view          (100%)
Step 2: form_start         (80%)
Step 3: form_progress (25) (68%)
Step 4: form_progress (50) (61%)
Step 5: form_progress (75) (56%)
Step 6: form_submit        (53%) ← Conversion
```

**Metriche Chiave:**
- Conversion Rate per form
- Tempo medio completamento
- Drop-off rate per step
- Errori validazione più frequenti

### **Google Tag Manager**

**Trigger Utili:**
```
1. Form Start → GA4 Event + Remarketing Tag
2. Form Progress 50% → Remarketing specifico
3. Form Abandon → Email recovery automation
4. Form Submit → Conversione + Thank You pixel
5. Validation Error → Debug + A/B test
```

### **Meta Events Manager**

**Eventi Standard (usabili in Ads):**
- ✅ `PageView` - Tutte le pagine
- ✅ `ViewContent` - Form visto
- ✅ `Lead` - **CONVERSIONE PRINCIPALE**
- ✅ `CompleteRegistration` - Signup forms

**Eventi Custom (analytics):**
- `FormStart` - Engagement
- `FormProgress` - Micro-conversioni
- `FormAbandoned` - Remarketing
- `FormValidationError` - Ottimizzazione
- `FormSubmission` - Tracking dettagliato

---

## 🎯 CASI D'USO

### **1. Remarketing Intelligente**

**Google Ads:**
```
Audience: Utenti che hanno fatto "form_start" MA NON "form_submit"
Messaggio: "Hai iniziato la tua richiesta, completa ora!"
```

**Meta Ads:**
```
Audience: FormStart E NON Lead (ultimi 7 giorni)
Placement: Facebook Feed + Instagram Stories
Objective: Conversioni
```

### **2. Ottimizzazione Form**

**Identifica colli di bottiglia:**
```sql
-- GA4 Query
SELECT 
  error_field,
  COUNT(*) as error_count
FROM form_validation_error
GROUP BY error_field
ORDER BY error_count DESC
```

**Risultato esempio:**
```
email: 45 errori      → Migliora label/placeholder
telefono: 32 errori   → Aggiungi format helper
privacy: 18 errori    → Rendi più visibile
```

### **3. A/B Testing**

**Testa varianti form:**
- Form A (5 campi) vs Form B (3 campi)
- Confronta: conversion rate, tempo medio, abandon rate
- Ottimizza basandoti su dati reali

### **4. Lead Scoring**

**Assegna punteggi:**
```
Form View only:          +10 punti
Form Start:              +25 punti
Progress 50%:            +40 punti
Progress 75%:            +60 punti
Form Submit (Lead):      +100 punti ✅
```

---

## 🔧 CONFIGURAZIONE

### **Settings Globali**
**FP Forms → Impostazioni**

**Google Tag Manager:**
```
GTM ID: GTM-XXXXXXX
☑️ Track Form Views
☐ Track Field Interactions (genera molti eventi)
```

**Google Analytics 4:**
```
GA4 ID: G-XXXXXXXXXX
☑️ Track Form Views
☐ Track Field Interactions
```

**Meta Pixel:**
```
Pixel ID: 1234567890123456
Access Token: EAAG... (Conversions API)
☑️ Track Form Views
```

---

## 📊 EVENTI PER PIATTAFORMA

### **GTM - Tutti gli Eventi**

| Evento | Trigger | Category | Parametri |
|--------|---------|----------|-----------|
| `fp_form_view` | Page load | impression | form_id, form_title |
| `fp_form_start` | First focus | engagement | form_id, form_title |
| `fp_form_progress` | Input change | engagement | form_id, progress_percent |
| `fp_form_abandon` | Page unload | abandonment | form_id, time_spent |
| `fp_form_validation_error` | Submit error | error | form_id, error_field |
| `fp_form_submit` | Submit success | conversion | form_id, time_to_complete |
| `fp_form_conversion` | Submit success | conversion | form_id, conversion_value |
| `fp_form_error` | Submit fail | error | form_id, error_message |
| `fp_form_field_interaction` | Field focus | engagement | form_id, field_name |

**Totale:** 9 eventi GTM

### **GA4 - Eventi Standard + Custom**

| Evento | Tipo | Enhanced Measurement | Parametri |
|--------|------|---------------------|-----------|
| `form_view` | Custom | No | form_id, form_name |
| `form_start` | Standard | Sì | form_id, form_name |
| `form_progress` | Custom | No | form_id, progress |
| `form_abandon` | Custom | No | form_id, time_spent |
| `form_error` | Custom | No | form_id, error_type |
| `form_submit` | Standard | Sì | form_id, success |
| `generate_lead` | Standard | Sì | form_id, value, currency |
| `conversion` | Standard | Sì | send_to, value, currency |

**Totale:** 8 eventi GA4 (5 standard + 3 custom)

### **Meta Pixel - Eventi Standard + Custom**

| Evento | Tipo | Ads Compatible | Parametri |
|--------|------|----------------|-----------|
| `PageView` | Standard | Sì | - |
| `ViewContent` | Standard | Sì | content_name, content_ids |
| `Lead` | Standard | **Sì** | content_name, value, currency |
| `CompleteRegistration` | Standard | Sì | content_name, value |
| `FormStart` | Custom | No | form_id, form_title |
| `FormProgress` | Custom | No | form_id, progress_percent |
| `FormAbandoned` | Custom | No | form_id, time_spent |
| `FormValidationError` | Custom | No | form_id, field_name |
| `FormSubmission` | Custom | No | form_id, submission_id |

**Totale:** 9 eventi Meta (4 standard + 5 custom)

---

## 🎨 COME USARE IN META ADS

### **Campagne Lead Generation**

**Optimization Event:** `Lead` ✅ (già tracciato!)

**Custom Audiences:**
```
1. Form Viewers (remarketing)
   → ViewContent E NON Lead (ultimi 7 giorni)

2. Form Starters (warm)
   → FormStart E NON Lead (ultimi 3 giorni)

3. Form Progressers (hot)
   → FormProgress 75% E NON Lead (ultimi 24 ore)

4. Form Abandoners
   → FormAbandoned (ultimi 7 giorni)
```

**Lookalike Audiences:**
```
Source: Lead events (ultimi 180 giorni)
Size: 1% - 3%
Location: Italia
```

### **Campagne Retargeting**

**Sequence:**
```
Day 1-3:  Ad to Form Viewers (ViewContent)
Day 4-7:  Ad to Form Starters (FormStart)
Day 8-14: Ad to Form Abandoners (FormAbandoned)
```

---

## 🔬 ANALISI AVANZATE

### **Conversion Funnel (GA4)**

```
100 Form Views
 ↓ 80% start
 80 Form Starts
 ↓ 85% progress 25%
 68 Progress 25%
 ↓ 90% progress 50%
 61 Progress 50%
 ↓ 92% progress 75%
 56 Progress 75%
 ↓ 95% submit
 53 Submissions ✅

Conversion Rate: 53%
Average Time: 45s
Drop-off Points: Start (-20%), 25% (-15%)
```

### **Error Analysis**

**Top Errors:**
```
1. email (45 volte) - 32% errori
2. telefono (32 volte) - 23% errori
3. privacy (18 volte) - 13% errori
```

**Action Items:**
- Migliora validazione email in real-time
- Aggiungi format mask per telefono
- Rendi checkbox privacy più evidente

### **Timing Metrics**

**Distribuzione tempo compilazione:**
```
0-15s:   12% (troppo veloce - possibile spam)
15-30s:  25% (veloce)
30-60s:  45% (normale) ✅
60-120s: 15% (lento)
120s+:   3% (molto lento - possibile issue)
```

---

## 🚀 BEST PRACTICES

### **Setup Essenziale:**
✅ GTM Container installato
✅ GA4 Property configurata
✅ Meta Pixel installato
✅ Conversions API configurata (Meta)
✅ Eventi testati in preview mode

### **Privacy & GDPR:**
✅ Cookie consent banner
✅ Privacy policy aggiornata
✅ Dati PII hashed (Meta CAPI)
✅ Opt-out mechanism
✅ Data retention configurata

### **Testing:**
1. **GTM Preview Mode** → Verifica dataLayer events
2. **GA4 DebugView** → Verifica eventi real-time
3. **Meta Pixel Helper** → Verifica pixel events
4. **Console Browser** → Check log `[FP Forms ...]`

### **Monitoring:**
- Dashboard settimanale conversioni
- Alert se conversion rate < soglia
- Report mensile funnel analysis
- Quarterly optimization review

---

## 📱 EVENTI MOBILI vs DESKTOP

Tutti gli eventi funzionano identicamente su:
- ✅ Desktop
- ✅ Mobile browser
- ✅ Tablet
- ✅ Progressive Web App (PWA)

**Touch events** gestiti automaticamente (focus su tap).

---

## 🎯 SUMMARY EVENTI

**Totale Eventi Implementati:** 15+

**Per Funnel Stage:**
- Awareness: 1 evento (View)
- Interest: 1 evento (Start)
- Consideration: 3 eventi (Progress 25/50/75)
- Conversion: 4 eventi (Submit, Lead, Registration, Conversion)
- Retention: 1 evento (Conversion GTM)
- Drop-off: 1 evento (Abandon)
- Error: 2 eventi (Validation, Submit error)
- Engagement: 1 evento (Field interactions)

**Piattaforme:**
- 🔵 GTM: 9 eventi
- 🟢 GA4: 8 eventi
- 🔴 Meta: 9 eventi

**Totale unique events cross-platform:** 26 combinazioni

---

**Status:** ✅ Tracking eventi avanzati completo e production-ready!

**Last Update:** 5 Novembre 2025, 23:59 CET



## 🎯 PANORAMICA

FP Forms traccia **15+ eventi** per un funnel completo di conversione su **3 piattaforme**:
- 🔵 **Google Tag Manager** (GTM)
- 🟢 **Google Analytics 4** (GA4)
- 🔴 **Meta Pixel** (Facebook/Instagram)

Tutti gli eventi sono **sincronizzati** e tracciati in parallelo per massima visibilità.

---

## 📋 EVENTI TRACCIATI

### **1️⃣ AWARENESS - Visualizzazione**

#### **Form View** (Caricamento pagina con form)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_view',
  'form_id': 123,
  'form_title': 'Contact Form',
  'form_name': 'FP Form #123'
}
```

**GA4:**
```javascript
gtag('event', 'form_view', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'form_type': 'fp_forms'
});
```

**Meta Pixel:**
```javascript
fbq('track', 'ViewContent', {
  content_name: 'Contact Form',
  content_category: 'form',
  content_ids: [123],
  content_type: 'form_view'
});
```

---

### **2️⃣ INTEREST - Inizio Compilazione**

#### **Form Start** (Primo campo focus)

**Quando:** Utente clicca/fa focus sul primo campo

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_start',
  'form_id': 123,
  'form_title': 'Contact Form',
  'event_category': 'engagement'
}
```

**GA4:**
```javascript
gtag('event', 'form_start', {
  'form_id': 123,
  'form_name': 'Contact Form'
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormStart', {
  form_id: 123,
  form_title: 'Contact Form',
  event_category: 'engagement'
});
```

---

### **3️⃣ CONSIDERATION - Progressione**

#### **Form Progress** (25%, 50%, 75% compilazione)

**Quando:** L'utente compila campi e raggiunge milestone

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_progress',
  'form_id': 123,
  'form_title': 'Contact Form',
  'progress_percent': 50,
  'event_category': 'engagement'
}
```

**GA4:**
```javascript
gtag('event', 'form_progress', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'progress': 50
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormProgress', {
  form_id: 123,
  form_title: 'Contact Form',
  progress_percent: 50,
  event_category: 'engagement'
});
```

**Milestone tracciati:**
- ✅ 25% - Primo quartile compilato
- ✅ 50% - Metà form compilato
- ✅ 75% - Quasi completo

---

### **4️⃣ CONVERSION - Submission Completata**

#### **Form Submit** (Submission con successo)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_submit',
  'form_id': 123,
  'form_title': 'Contact Form',
  'form_status': 'success',
  'time_to_complete': 45 // secondi
}
```

**GA4:**
```javascript
// Event 1: Form Submit
gtag('event', 'form_submit', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'success': true,
  'engagement_time_msec': 45000
});

// Event 2: Generate Lead (standard)
gtag('event', 'generate_lead', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'value': 1.0,
  'currency': 'EUR'
});

// Event 3: Conversion
gtag('event', 'conversion', {
  'send_to': 'AW-CONVERSION_ID',
  'form_id': 123,
  'value': 1.0,
  'currency': 'EUR'
});
```

**Meta Pixel:**
```javascript
// Event 1: Lead (STANDARD - per Ads)
fbq('track', 'Lead', {
  content_name: 'Contact Form',
  content_category: 'form_submission',
  content_ids: [123],
  value: 1.0,
  currency: 'EUR',
  status: 'completed'
});

// Event 2: CompleteRegistration (se signup/registrazione)
fbq('track', 'CompleteRegistration', {
  content_name: 'Contact Form',
  status: 'completed',
  value: 1.0,
  currency: 'EUR'
});

// Event 3: Custom FormSubmission
fbq('trackCustom', 'FormSubmission', {
  form_id: 123,
  form_title: 'Contact Form',
  submission_id: 456,
  time_spent_seconds: 45,
  event_category: 'conversion'
});
```

**Server-Side (Conversions API):**
```php
POST /v18.0/{pixel_id}/events
{
  "event_name": "Lead",
  "event_time": 1699224300,
  "action_source": "website",
  "user_data": {
    "em": "sha256(email)",
    "fn": "sha256(nome)",
    "ln": "sha256(cognome)",
    ...
  },
  "custom_data": {
    "form_id": 123,
    "value": 1.0,
    "currency": "EUR"
  }
}
```

---

### **5️⃣ RETENTION - Conversione Avanzata**

#### **Form Conversion** (GTM-specific)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_conversion',
  'form_id': 123,
  'form_title': 'Contact Form',
  'conversion_type': 'form_submission',
  'conversion_value': 1.0
}
```

Usabile per:
- Google Ads conversion tracking
- Enhanced conversions
- Cross-domain tracking

---

### **6️⃣ DROP-OFF - Abbandono Form**

#### **Form Abandon** (Pagina chiusa senza submit)

**Quando:** Utente inizia compilazione ma esce dalla pagina senza completare

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_abandon',
  'form_id': 123,
  'form_title': 'Contact Form',
  'time_spent_seconds': 15,
  'event_category': 'abandonment'
}
```

**GA4:**
```javascript
gtag('event', 'form_abandon', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'time_spent': 15
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormAbandoned', {
  form_id: 123,
  form_title: 'Contact Form',
  time_spent_seconds: 15,
  event_category: 'abandonment'
});
```

**Utile per:**
- Remarketing utenti che abbandonano
- A/B test ottimizzazione form
- Identificare campi problematici

---

### **7️⃣ ERROR TRACKING - Errori Validazione**

#### **Validation Error** (Campo non valido)

**Quando:** Utente invia form con errori validazione

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_validation_error',
  'form_id': 123,
  'form_title': 'Contact Form',
  'error_field': 'email',
  'error_message': 'Email non valida',
  'event_category': 'error'
}
```

**GA4:**
```javascript
gtag('event', 'form_error', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'error_field': 'email',
  'error_type': 'validation'
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormValidationError', {
  form_id: 123,
  form_title: 'Contact Form',
  field_name: 'email',
  error_message: 'Email non valida',
  event_category: 'error'
});
```

**Errori tracciati:**
- Campo obbligatorio vuoto
- Email non valida
- Telefono non valido
- File troppo grande
- reCAPTCHA fallito
- Errori server

---

### **8️⃣ ENGAGEMENT - Interazioni Campi** (Opzionale)

#### **Field Interaction** (Focus su campo specifico)

**Quando:** Utente fa focus su un campo (se `track_interactions` = true)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_field_interaction',
  'form_id': 123,
  'field_name': 'telefono',
  'field_type': 'tel'
}
```

⚠️ **Attenzione:** Genera molti eventi! Usare solo per analisi dettagliate.

---

## 📈 FUNNEL COMPLETO TRACCIATO

```
👁️  Form View (100 utenti)
        ↓ -20%
✏️  Form Start (80 utenti)      ← Iniziano a compilare
        ↓ -15%
📊  Progress 25% (68 utenti)
        ↓ -10%
📊  Progress 50% (61 utenti)
        ↓ -8%
📊  Progress 75% (56 utenti)
        ↓ -6%
✅  Form Submit (53 utenti)      ← CONVERSIONE!
        ↓
🎯  Lead in CRM
```

**Drop-off Points:**
- 20% abbandona prima di iniziare
- 15% abbandona al 25%
- 10% abbandona al 50%
- 8% abbandona al 75%
- 6% errori validazione

**Conversion Rate:** 53% (53/100)

---

## 🎛️ TIMING METRICS

### **Time to Complete**
Tracciato automaticamente da primo focus a submit:

```javascript
// Salvato in ogni evento submission
time_to_complete: 45 // secondi
time_spent_seconds: 45
engagement_time_msec: 45000
```

**Statistiche utili:**
- Tempo medio compilazione
- Identificare form troppo lunghi
- Ottimizzare UX

---

## 📊 DASHBOARD & REPORTING

### **Google Analytics 4**

**Funnel Exploration:**
```
Step 1: form_view          (100%)
Step 2: form_start         (80%)
Step 3: form_progress (25) (68%)
Step 4: form_progress (50) (61%)
Step 5: form_progress (75) (56%)
Step 6: form_submit        (53%) ← Conversion
```

**Metriche Chiave:**
- Conversion Rate per form
- Tempo medio completamento
- Drop-off rate per step
- Errori validazione più frequenti

### **Google Tag Manager**

**Trigger Utili:**
```
1. Form Start → GA4 Event + Remarketing Tag
2. Form Progress 50% → Remarketing specifico
3. Form Abandon → Email recovery automation
4. Form Submit → Conversione + Thank You pixel
5. Validation Error → Debug + A/B test
```

### **Meta Events Manager**

**Eventi Standard (usabili in Ads):**
- ✅ `PageView` - Tutte le pagine
- ✅ `ViewContent` - Form visto
- ✅ `Lead` - **CONVERSIONE PRINCIPALE**
- ✅ `CompleteRegistration` - Signup forms

**Eventi Custom (analytics):**
- `FormStart` - Engagement
- `FormProgress` - Micro-conversioni
- `FormAbandoned` - Remarketing
- `FormValidationError` - Ottimizzazione
- `FormSubmission` - Tracking dettagliato

---

## 🎯 CASI D'USO

### **1. Remarketing Intelligente**

**Google Ads:**
```
Audience: Utenti che hanno fatto "form_start" MA NON "form_submit"
Messaggio: "Hai iniziato la tua richiesta, completa ora!"
```

**Meta Ads:**
```
Audience: FormStart E NON Lead (ultimi 7 giorni)
Placement: Facebook Feed + Instagram Stories
Objective: Conversioni
```

### **2. Ottimizzazione Form**

**Identifica colli di bottiglia:**
```sql
-- GA4 Query
SELECT 
  error_field,
  COUNT(*) as error_count
FROM form_validation_error
GROUP BY error_field
ORDER BY error_count DESC
```

**Risultato esempio:**
```
email: 45 errori      → Migliora label/placeholder
telefono: 32 errori   → Aggiungi format helper
privacy: 18 errori    → Rendi più visibile
```

### **3. A/B Testing**

**Testa varianti form:**
- Form A (5 campi) vs Form B (3 campi)
- Confronta: conversion rate, tempo medio, abandon rate
- Ottimizza basandoti su dati reali

### **4. Lead Scoring**

**Assegna punteggi:**
```
Form View only:          +10 punti
Form Start:              +25 punti
Progress 50%:            +40 punti
Progress 75%:            +60 punti
Form Submit (Lead):      +100 punti ✅
```

---

## 🔧 CONFIGURAZIONE

### **Settings Globali**
**FP Forms → Impostazioni**

**Google Tag Manager:**
```
GTM ID: GTM-XXXXXXX
☑️ Track Form Views
☐ Track Field Interactions (genera molti eventi)
```

**Google Analytics 4:**
```
GA4 ID: G-XXXXXXXXXX
☑️ Track Form Views
☐ Track Field Interactions
```

**Meta Pixel:**
```
Pixel ID: 1234567890123456
Access Token: EAAG... (Conversions API)
☑️ Track Form Views
```

---

## 📊 EVENTI PER PIATTAFORMA

### **GTM - Tutti gli Eventi**

| Evento | Trigger | Category | Parametri |
|--------|---------|----------|-----------|
| `fp_form_view` | Page load | impression | form_id, form_title |
| `fp_form_start` | First focus | engagement | form_id, form_title |
| `fp_form_progress` | Input change | engagement | form_id, progress_percent |
| `fp_form_abandon` | Page unload | abandonment | form_id, time_spent |
| `fp_form_validation_error` | Submit error | error | form_id, error_field |
| `fp_form_submit` | Submit success | conversion | form_id, time_to_complete |
| `fp_form_conversion` | Submit success | conversion | form_id, conversion_value |
| `fp_form_error` | Submit fail | error | form_id, error_message |
| `fp_form_field_interaction` | Field focus | engagement | form_id, field_name |

**Totale:** 9 eventi GTM

### **GA4 - Eventi Standard + Custom**

| Evento | Tipo | Enhanced Measurement | Parametri |
|--------|------|---------------------|-----------|
| `form_view` | Custom | No | form_id, form_name |
| `form_start` | Standard | Sì | form_id, form_name |
| `form_progress` | Custom | No | form_id, progress |
| `form_abandon` | Custom | No | form_id, time_spent |
| `form_error` | Custom | No | form_id, error_type |
| `form_submit` | Standard | Sì | form_id, success |
| `generate_lead` | Standard | Sì | form_id, value, currency |
| `conversion` | Standard | Sì | send_to, value, currency |

**Totale:** 8 eventi GA4 (5 standard + 3 custom)

### **Meta Pixel - Eventi Standard + Custom**

| Evento | Tipo | Ads Compatible | Parametri |
|--------|------|----------------|-----------|
| `PageView` | Standard | Sì | - |
| `ViewContent` | Standard | Sì | content_name, content_ids |
| `Lead` | Standard | **Sì** | content_name, value, currency |
| `CompleteRegistration` | Standard | Sì | content_name, value |
| `FormStart` | Custom | No | form_id, form_title |
| `FormProgress` | Custom | No | form_id, progress_percent |
| `FormAbandoned` | Custom | No | form_id, time_spent |
| `FormValidationError` | Custom | No | form_id, field_name |
| `FormSubmission` | Custom | No | form_id, submission_id |

**Totale:** 9 eventi Meta (4 standard + 5 custom)

---

## 🎨 COME USARE IN META ADS

### **Campagne Lead Generation**

**Optimization Event:** `Lead` ✅ (già tracciato!)

**Custom Audiences:**
```
1. Form Viewers (remarketing)
   → ViewContent E NON Lead (ultimi 7 giorni)

2. Form Starters (warm)
   → FormStart E NON Lead (ultimi 3 giorni)

3. Form Progressers (hot)
   → FormProgress 75% E NON Lead (ultimi 24 ore)

4. Form Abandoners
   → FormAbandoned (ultimi 7 giorni)
```

**Lookalike Audiences:**
```
Source: Lead events (ultimi 180 giorni)
Size: 1% - 3%
Location: Italia
```

### **Campagne Retargeting**

**Sequence:**
```
Day 1-3:  Ad to Form Viewers (ViewContent)
Day 4-7:  Ad to Form Starters (FormStart)
Day 8-14: Ad to Form Abandoners (FormAbandoned)
```

---

## 🔬 ANALISI AVANZATE

### **Conversion Funnel (GA4)**

```
100 Form Views
 ↓ 80% start
 80 Form Starts
 ↓ 85% progress 25%
 68 Progress 25%
 ↓ 90% progress 50%
 61 Progress 50%
 ↓ 92% progress 75%
 56 Progress 75%
 ↓ 95% submit
 53 Submissions ✅

Conversion Rate: 53%
Average Time: 45s
Drop-off Points: Start (-20%), 25% (-15%)
```

### **Error Analysis**

**Top Errors:**
```
1. email (45 volte) - 32% errori
2. telefono (32 volte) - 23% errori
3. privacy (18 volte) - 13% errori
```

**Action Items:**
- Migliora validazione email in real-time
- Aggiungi format mask per telefono
- Rendi checkbox privacy più evidente

### **Timing Metrics**

**Distribuzione tempo compilazione:**
```
0-15s:   12% (troppo veloce - possibile spam)
15-30s:  25% (veloce)
30-60s:  45% (normale) ✅
60-120s: 15% (lento)
120s+:   3% (molto lento - possibile issue)
```

---

## 🚀 BEST PRACTICES

### **Setup Essenziale:**
✅ GTM Container installato
✅ GA4 Property configurata
✅ Meta Pixel installato
✅ Conversions API configurata (Meta)
✅ Eventi testati in preview mode

### **Privacy & GDPR:**
✅ Cookie consent banner
✅ Privacy policy aggiornata
✅ Dati PII hashed (Meta CAPI)
✅ Opt-out mechanism
✅ Data retention configurata

### **Testing:**
1. **GTM Preview Mode** → Verifica dataLayer events
2. **GA4 DebugView** → Verifica eventi real-time
3. **Meta Pixel Helper** → Verifica pixel events
4. **Console Browser** → Check log `[FP Forms ...]`

### **Monitoring:**
- Dashboard settimanale conversioni
- Alert se conversion rate < soglia
- Report mensile funnel analysis
- Quarterly optimization review

---

## 📱 EVENTI MOBILI vs DESKTOP

Tutti gli eventi funzionano identicamente su:
- ✅ Desktop
- ✅ Mobile browser
- ✅ Tablet
- ✅ Progressive Web App (PWA)

**Touch events** gestiti automaticamente (focus su tap).

---

## 🎯 SUMMARY EVENTI

**Totale Eventi Implementati:** 15+

**Per Funnel Stage:**
- Awareness: 1 evento (View)
- Interest: 1 evento (Start)
- Consideration: 3 eventi (Progress 25/50/75)
- Conversion: 4 eventi (Submit, Lead, Registration, Conversion)
- Retention: 1 evento (Conversion GTM)
- Drop-off: 1 evento (Abandon)
- Error: 2 eventi (Validation, Submit error)
- Engagement: 1 evento (Field interactions)

**Piattaforme:**
- 🔵 GTM: 9 eventi
- 🟢 GA4: 8 eventi
- 🔴 Meta: 9 eventi

**Totale unique events cross-platform:** 26 combinazioni

---

**Status:** ✅ Tracking eventi avanzati completo e production-ready!

**Last Update:** 5 Novembre 2025, 23:59 CET



## 🎯 PANORAMICA

FP Forms traccia **15+ eventi** per un funnel completo di conversione su **3 piattaforme**:
- 🔵 **Google Tag Manager** (GTM)
- 🟢 **Google Analytics 4** (GA4)
- 🔴 **Meta Pixel** (Facebook/Instagram)

Tutti gli eventi sono **sincronizzati** e tracciati in parallelo per massima visibilità.

---

## 📋 EVENTI TRACCIATI

### **1️⃣ AWARENESS - Visualizzazione**

#### **Form View** (Caricamento pagina con form)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_view',
  'form_id': 123,
  'form_title': 'Contact Form',
  'form_name': 'FP Form #123'
}
```

**GA4:**
```javascript
gtag('event', 'form_view', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'form_type': 'fp_forms'
});
```

**Meta Pixel:**
```javascript
fbq('track', 'ViewContent', {
  content_name: 'Contact Form',
  content_category: 'form',
  content_ids: [123],
  content_type: 'form_view'
});
```

---

### **2️⃣ INTEREST - Inizio Compilazione**

#### **Form Start** (Primo campo focus)

**Quando:** Utente clicca/fa focus sul primo campo

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_start',
  'form_id': 123,
  'form_title': 'Contact Form',
  'event_category': 'engagement'
}
```

**GA4:**
```javascript
gtag('event', 'form_start', {
  'form_id': 123,
  'form_name': 'Contact Form'
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormStart', {
  form_id: 123,
  form_title: 'Contact Form',
  event_category: 'engagement'
});
```

---

### **3️⃣ CONSIDERATION - Progressione**

#### **Form Progress** (25%, 50%, 75% compilazione)

**Quando:** L'utente compila campi e raggiunge milestone

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_progress',
  'form_id': 123,
  'form_title': 'Contact Form',
  'progress_percent': 50,
  'event_category': 'engagement'
}
```

**GA4:**
```javascript
gtag('event', 'form_progress', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'progress': 50
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormProgress', {
  form_id: 123,
  form_title: 'Contact Form',
  progress_percent: 50,
  event_category: 'engagement'
});
```

**Milestone tracciati:**
- ✅ 25% - Primo quartile compilato
- ✅ 50% - Metà form compilato
- ✅ 75% - Quasi completo

---

### **4️⃣ CONVERSION - Submission Completata**

#### **Form Submit** (Submission con successo)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_submit',
  'form_id': 123,
  'form_title': 'Contact Form',
  'form_status': 'success',
  'time_to_complete': 45 // secondi
}
```

**GA4:**
```javascript
// Event 1: Form Submit
gtag('event', 'form_submit', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'success': true,
  'engagement_time_msec': 45000
});

// Event 2: Generate Lead (standard)
gtag('event', 'generate_lead', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'value': 1.0,
  'currency': 'EUR'
});

// Event 3: Conversion
gtag('event', 'conversion', {
  'send_to': 'AW-CONVERSION_ID',
  'form_id': 123,
  'value': 1.0,
  'currency': 'EUR'
});
```

**Meta Pixel:**
```javascript
// Event 1: Lead (STANDARD - per Ads)
fbq('track', 'Lead', {
  content_name: 'Contact Form',
  content_category: 'form_submission',
  content_ids: [123],
  value: 1.0,
  currency: 'EUR',
  status: 'completed'
});

// Event 2: CompleteRegistration (se signup/registrazione)
fbq('track', 'CompleteRegistration', {
  content_name: 'Contact Form',
  status: 'completed',
  value: 1.0,
  currency: 'EUR'
});

// Event 3: Custom FormSubmission
fbq('trackCustom', 'FormSubmission', {
  form_id: 123,
  form_title: 'Contact Form',
  submission_id: 456,
  time_spent_seconds: 45,
  event_category: 'conversion'
});
```

**Server-Side (Conversions API):**
```php
POST /v18.0/{pixel_id}/events
{
  "event_name": "Lead",
  "event_time": 1699224300,
  "action_source": "website",
  "user_data": {
    "em": "sha256(email)",
    "fn": "sha256(nome)",
    "ln": "sha256(cognome)",
    ...
  },
  "custom_data": {
    "form_id": 123,
    "value": 1.0,
    "currency": "EUR"
  }
}
```

---

### **5️⃣ RETENTION - Conversione Avanzata**

#### **Form Conversion** (GTM-specific)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_conversion',
  'form_id': 123,
  'form_title': 'Contact Form',
  'conversion_type': 'form_submission',
  'conversion_value': 1.0
}
```

Usabile per:
- Google Ads conversion tracking
- Enhanced conversions
- Cross-domain tracking

---

### **6️⃣ DROP-OFF - Abbandono Form**

#### **Form Abandon** (Pagina chiusa senza submit)

**Quando:** Utente inizia compilazione ma esce dalla pagina senza completare

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_abandon',
  'form_id': 123,
  'form_title': 'Contact Form',
  'time_spent_seconds': 15,
  'event_category': 'abandonment'
}
```

**GA4:**
```javascript
gtag('event', 'form_abandon', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'time_spent': 15
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormAbandoned', {
  form_id: 123,
  form_title: 'Contact Form',
  time_spent_seconds: 15,
  event_category: 'abandonment'
});
```

**Utile per:**
- Remarketing utenti che abbandonano
- A/B test ottimizzazione form
- Identificare campi problematici

---

### **7️⃣ ERROR TRACKING - Errori Validazione**

#### **Validation Error** (Campo non valido)

**Quando:** Utente invia form con errori validazione

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_validation_error',
  'form_id': 123,
  'form_title': 'Contact Form',
  'error_field': 'email',
  'error_message': 'Email non valida',
  'event_category': 'error'
}
```

**GA4:**
```javascript
gtag('event', 'form_error', {
  'form_id': 123,
  'form_name': 'Contact Form',
  'error_field': 'email',
  'error_type': 'validation'
});
```

**Meta Pixel:**
```javascript
fbq('trackCustom', 'FormValidationError', {
  form_id: 123,
  form_title: 'Contact Form',
  field_name: 'email',
  error_message: 'Email non valida',
  event_category: 'error'
});
```

**Errori tracciati:**
- Campo obbligatorio vuoto
- Email non valida
- Telefono non valido
- File troppo grande
- reCAPTCHA fallito
- Errori server

---

### **8️⃣ ENGAGEMENT - Interazioni Campi** (Opzionale)

#### **Field Interaction** (Focus su campo specifico)

**Quando:** Utente fa focus su un campo (se `track_interactions` = true)

**GTM dataLayer:**
```javascript
{
  'event': 'fp_form_field_interaction',
  'form_id': 123,
  'field_name': 'telefono',
  'field_type': 'tel'
}
```

⚠️ **Attenzione:** Genera molti eventi! Usare solo per analisi dettagliate.

---

## 📈 FUNNEL COMPLETO TRACCIATO

```
👁️  Form View (100 utenti)
        ↓ -20%
✏️  Form Start (80 utenti)      ← Iniziano a compilare
        ↓ -15%
📊  Progress 25% (68 utenti)
        ↓ -10%
📊  Progress 50% (61 utenti)
        ↓ -8%
📊  Progress 75% (56 utenti)
        ↓ -6%
✅  Form Submit (53 utenti)      ← CONVERSIONE!
        ↓
🎯  Lead in CRM
```

**Drop-off Points:**
- 20% abbandona prima di iniziare
- 15% abbandona al 25%
- 10% abbandona al 50%
- 8% abbandona al 75%
- 6% errori validazione

**Conversion Rate:** 53% (53/100)

---

## 🎛️ TIMING METRICS

### **Time to Complete**
Tracciato automaticamente da primo focus a submit:

```javascript
// Salvato in ogni evento submission
time_to_complete: 45 // secondi
time_spent_seconds: 45
engagement_time_msec: 45000
```

**Statistiche utili:**
- Tempo medio compilazione
- Identificare form troppo lunghi
- Ottimizzare UX

---

## 📊 DASHBOARD & REPORTING

### **Google Analytics 4**

**Funnel Exploration:**
```
Step 1: form_view          (100%)
Step 2: form_start         (80%)
Step 3: form_progress (25) (68%)
Step 4: form_progress (50) (61%)
Step 5: form_progress (75) (56%)
Step 6: form_submit        (53%) ← Conversion
```

**Metriche Chiave:**
- Conversion Rate per form
- Tempo medio completamento
- Drop-off rate per step
- Errori validazione più frequenti

### **Google Tag Manager**

**Trigger Utili:**
```
1. Form Start → GA4 Event + Remarketing Tag
2. Form Progress 50% → Remarketing specifico
3. Form Abandon → Email recovery automation
4. Form Submit → Conversione + Thank You pixel
5. Validation Error → Debug + A/B test
```

### **Meta Events Manager**

**Eventi Standard (usabili in Ads):**
- ✅ `PageView` - Tutte le pagine
- ✅ `ViewContent` - Form visto
- ✅ `Lead` - **CONVERSIONE PRINCIPALE**
- ✅ `CompleteRegistration` - Signup forms

**Eventi Custom (analytics):**
- `FormStart` - Engagement
- `FormProgress` - Micro-conversioni
- `FormAbandoned` - Remarketing
- `FormValidationError` - Ottimizzazione
- `FormSubmission` - Tracking dettagliato

---

## 🎯 CASI D'USO

### **1. Remarketing Intelligente**

**Google Ads:**
```
Audience: Utenti che hanno fatto "form_start" MA NON "form_submit"
Messaggio: "Hai iniziato la tua richiesta, completa ora!"
```

**Meta Ads:**
```
Audience: FormStart E NON Lead (ultimi 7 giorni)
Placement: Facebook Feed + Instagram Stories
Objective: Conversioni
```

### **2. Ottimizzazione Form**

**Identifica colli di bottiglia:**
```sql
-- GA4 Query
SELECT 
  error_field,
  COUNT(*) as error_count
FROM form_validation_error
GROUP BY error_field
ORDER BY error_count DESC
```

**Risultato esempio:**
```
email: 45 errori      → Migliora label/placeholder
telefono: 32 errori   → Aggiungi format helper
privacy: 18 errori    → Rendi più visibile
```

### **3. A/B Testing**

**Testa varianti form:**
- Form A (5 campi) vs Form B (3 campi)
- Confronta: conversion rate, tempo medio, abandon rate
- Ottimizza basandoti su dati reali

### **4. Lead Scoring**

**Assegna punteggi:**
```
Form View only:          +10 punti
Form Start:              +25 punti
Progress 50%:            +40 punti
Progress 75%:            +60 punti
Form Submit (Lead):      +100 punti ✅
```

---

## 🔧 CONFIGURAZIONE

### **Settings Globali**
**FP Forms → Impostazioni**

**Google Tag Manager:**
```
GTM ID: GTM-XXXXXXX
☑️ Track Form Views
☐ Track Field Interactions (genera molti eventi)
```

**Google Analytics 4:**
```
GA4 ID: G-XXXXXXXXXX
☑️ Track Form Views
☐ Track Field Interactions
```

**Meta Pixel:**
```
Pixel ID: 1234567890123456
Access Token: EAAG... (Conversions API)
☑️ Track Form Views
```

---

## 📊 EVENTI PER PIATTAFORMA

### **GTM - Tutti gli Eventi**

| Evento | Trigger | Category | Parametri |
|--------|---------|----------|-----------|
| `fp_form_view` | Page load | impression | form_id, form_title |
| `fp_form_start` | First focus | engagement | form_id, form_title |
| `fp_form_progress` | Input change | engagement | form_id, progress_percent |
| `fp_form_abandon` | Page unload | abandonment | form_id, time_spent |
| `fp_form_validation_error` | Submit error | error | form_id, error_field |
| `fp_form_submit` | Submit success | conversion | form_id, time_to_complete |
| `fp_form_conversion` | Submit success | conversion | form_id, conversion_value |
| `fp_form_error` | Submit fail | error | form_id, error_message |
| `fp_form_field_interaction` | Field focus | engagement | form_id, field_name |

**Totale:** 9 eventi GTM

### **GA4 - Eventi Standard + Custom**

| Evento | Tipo | Enhanced Measurement | Parametri |
|--------|------|---------------------|-----------|
| `form_view` | Custom | No | form_id, form_name |
| `form_start` | Standard | Sì | form_id, form_name |
| `form_progress` | Custom | No | form_id, progress |
| `form_abandon` | Custom | No | form_id, time_spent |
| `form_error` | Custom | No | form_id, error_type |
| `form_submit` | Standard | Sì | form_id, success |
| `generate_lead` | Standard | Sì | form_id, value, currency |
| `conversion` | Standard | Sì | send_to, value, currency |

**Totale:** 8 eventi GA4 (5 standard + 3 custom)

### **Meta Pixel - Eventi Standard + Custom**

| Evento | Tipo | Ads Compatible | Parametri |
|--------|------|----------------|-----------|
| `PageView` | Standard | Sì | - |
| `ViewContent` | Standard | Sì | content_name, content_ids |
| `Lead` | Standard | **Sì** | content_name, value, currency |
| `CompleteRegistration` | Standard | Sì | content_name, value |
| `FormStart` | Custom | No | form_id, form_title |
| `FormProgress` | Custom | No | form_id, progress_percent |
| `FormAbandoned` | Custom | No | form_id, time_spent |
| `FormValidationError` | Custom | No | form_id, field_name |
| `FormSubmission` | Custom | No | form_id, submission_id |

**Totale:** 9 eventi Meta (4 standard + 5 custom)

---

## 🎨 COME USARE IN META ADS

### **Campagne Lead Generation**

**Optimization Event:** `Lead` ✅ (già tracciato!)

**Custom Audiences:**
```
1. Form Viewers (remarketing)
   → ViewContent E NON Lead (ultimi 7 giorni)

2. Form Starters (warm)
   → FormStart E NON Lead (ultimi 3 giorni)

3. Form Progressers (hot)
   → FormProgress 75% E NON Lead (ultimi 24 ore)

4. Form Abandoners
   → FormAbandoned (ultimi 7 giorni)
```

**Lookalike Audiences:**
```
Source: Lead events (ultimi 180 giorni)
Size: 1% - 3%
Location: Italia
```

### **Campagne Retargeting**

**Sequence:**
```
Day 1-3:  Ad to Form Viewers (ViewContent)
Day 4-7:  Ad to Form Starters (FormStart)
Day 8-14: Ad to Form Abandoners (FormAbandoned)
```

---

## 🔬 ANALISI AVANZATE

### **Conversion Funnel (GA4)**

```
100 Form Views
 ↓ 80% start
 80 Form Starts
 ↓ 85% progress 25%
 68 Progress 25%
 ↓ 90% progress 50%
 61 Progress 50%
 ↓ 92% progress 75%
 56 Progress 75%
 ↓ 95% submit
 53 Submissions ✅

Conversion Rate: 53%
Average Time: 45s
Drop-off Points: Start (-20%), 25% (-15%)
```

### **Error Analysis**

**Top Errors:**
```
1. email (45 volte) - 32% errori
2. telefono (32 volte) - 23% errori
3. privacy (18 volte) - 13% errori
```

**Action Items:**
- Migliora validazione email in real-time
- Aggiungi format mask per telefono
- Rendi checkbox privacy più evidente

### **Timing Metrics**

**Distribuzione tempo compilazione:**
```
0-15s:   12% (troppo veloce - possibile spam)
15-30s:  25% (veloce)
30-60s:  45% (normale) ✅
60-120s: 15% (lento)
120s+:   3% (molto lento - possibile issue)
```

---

## 🚀 BEST PRACTICES

### **Setup Essenziale:**
✅ GTM Container installato
✅ GA4 Property configurata
✅ Meta Pixel installato
✅ Conversions API configurata (Meta)
✅ Eventi testati in preview mode

### **Privacy & GDPR:**
✅ Cookie consent banner
✅ Privacy policy aggiornata
✅ Dati PII hashed (Meta CAPI)
✅ Opt-out mechanism
✅ Data retention configurata

### **Testing:**
1. **GTM Preview Mode** → Verifica dataLayer events
2. **GA4 DebugView** → Verifica eventi real-time
3. **Meta Pixel Helper** → Verifica pixel events
4. **Console Browser** → Check log `[FP Forms ...]`

### **Monitoring:**
- Dashboard settimanale conversioni
- Alert se conversion rate < soglia
- Report mensile funnel analysis
- Quarterly optimization review

---

## 📱 EVENTI MOBILI vs DESKTOP

Tutti gli eventi funzionano identicamente su:
- ✅ Desktop
- ✅ Mobile browser
- ✅ Tablet
- ✅ Progressive Web App (PWA)

**Touch events** gestiti automaticamente (focus su tap).

---

## 🎯 SUMMARY EVENTI

**Totale Eventi Implementati:** 15+

**Per Funnel Stage:**
- Awareness: 1 evento (View)
- Interest: 1 evento (Start)
- Consideration: 3 eventi (Progress 25/50/75)
- Conversion: 4 eventi (Submit, Lead, Registration, Conversion)
- Retention: 1 evento (Conversion GTM)
- Drop-off: 1 evento (Abandon)
- Error: 2 eventi (Validation, Submit error)
- Engagement: 1 evento (Field interactions)

**Piattaforme:**
- 🔵 GTM: 9 eventi
- 🟢 GA4: 8 eventi
- 🔴 Meta: 9 eventi

**Totale unique events cross-platform:** 26 combinazioni

---

**Status:** ✅ Tracking eventi avanzati completo e production-ready!

**Last Update:** 5 Novembre 2025, 23:59 CET































