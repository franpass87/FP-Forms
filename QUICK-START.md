# 🚀 FP Forms - Guida Rapida

## ✅ Attivazione Plugin

1. **Vai nella Dashboard WordPress**
   - Accedi al pannello di amministrazione
   - Vai su **Plugin** > **Plugin Installati**

2. **Attiva FP Forms**
   - Cerca "FP Forms" nella lista
   - Clicca su **Attiva**

3. **Il plugin creerà automaticamente:**
   - Tabelle nel database per submissions e campi
   - Menu "FP Forms" nella sidebar admin
   - Impostazioni di default

## 📝 Creare il Primo Form

1. **Vai su FP Forms > Nuovo Form**

2. **Inserisci i dati base:**
   - Titolo: es. "Contattaci"
   - Descrizione: (opzionale)

3. **Aggiungi i campi:**
   - Clicca sui tipi di campo nella sidebar destra
   - Configura ogni campo (etichetta, nome, obbligatorio)
   - Riordina trascinando i campi

4. **Configura le impostazioni:**
   - Testo pulsante invio
   - Messaggio di successo
   - Email destinatario notifiche
   - Email di conferma (opzionale)

5. **Salva il form**

## 🎯 Inserire il Form in una Pagina

1. **Copia lo shortcode**
   - Vai su **FP Forms** > **Tutti i Form**
   - Clicca su "Copia" accanto allo shortcode del form
   - Es: `[fp_form id="1"]`

2. **Incolla in una pagina/post:**
   - Crea o modifica una pagina
   - Incolla lo shortcode dove vuoi visualizzare il form
   - Pubblica

3. **Il form è online!**
   - Visita la pagina per vedere il form in azione

## 📬 Visualizzare le Submissions

1. **Vai su FP Forms > Tutti i Form**
2. Clicca sul numero di submissions del form
3. Visualizza tutte le richieste ricevute

## ⚙️ Impostazioni Globali

**FP Forms > Impostazioni**

- Email mittente predefinita
- Google reCAPTCHA (opzionale)

## 💡 Tips Utili

### Campi Comuni per Form di Prenotazione

```
- Nome (text, obbligatorio)
- Email (email, obbligatorio)
- Telefono (phone, obbligatorio)
- Data Prenotazione (date, obbligatorio)
- Numero Persone (number)
- Note/Richieste (textarea)
```

### Shortcode in Template PHP

```php
<?php echo do_shortcode('[fp_form id="1"]'); ?>
```

### Personalizzare gli Stili

Aggiungi CSS personalizzato in **Aspetto > Personalizza > CSS Aggiuntivo**

```css
.fp-forms-submit-btn {
    background: #ff6b6b !important;
}
```

## 🆘 Troubleshooting

### Il form non si vede
- Verifica che lo shortcode sia corretto
- Controlla che il form sia pubblicato
- Pulisci la cache del sito

### Le email non arrivano
- Verifica l'indirizzo email nelle impostazioni
- Controlla la cartella SPAM
- Configura un plugin SMTP (es. WP Mail SMTP)

### Le submissions non si salvano
- Verifica i permessi del database
- Controlla la console JavaScript per errori
- Disattiva temporaneamente altri plugin

## 📞 Supporto

info@francescopasseri.com

---

**Buon lavoro con FP Forms! 🎉**

