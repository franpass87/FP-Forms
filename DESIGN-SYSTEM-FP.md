# 🎨 FP Forms - Design System

Design system coerente con l'ecosistema FP (Francesco Passeri)

**Versione:** 1.0.0  
**Data:** 2025-11-04  
**Autore:** Francesco Passeri

---

## 📐 Filosofia del Design

Il design system di FP Forms è stato creato per essere **perfettamente coerente** con FP-Experiences e altri plugin dell'ecosistema FP. 

### Principi Guida

1. **Coerenza Visiva** - Stessi colori, spacing, e componenti
2. **Accessibilità** - WCAG 2.1 AA compliant
3. **Responsive** - Mobile-first approach
4. **Performance** - CSS ottimizzato, no framework pesanti
5. **Modernità** - Design pulito e professionale

---

## 🎨 Sistema Colori

### Palette Principale

```css
/* Colori Brand */
--fp-color-primary: #2563eb;          /* Blu primario */
--fp-color-primary-hover: #1d4ed8;    /* Blu hover */

/* Colori Stato */
--fp-color-danger: #dc2626;           /* Rosso errori */
--fp-color-success: #059669;          /* Verde successo */
--fp-color-warning: #d97706;          /* Arancione warning */

/* Colori Testo */
--fp-color-text: #1f2937;             /* Testo principale */
--fp-color-muted: #6b7280;            /* Testo secondario */

/* Colori Superficie */
--fp-color-surface: #ffffff;          /* Superficie elementi */
--fp-color-background: #f9fafb;       /* Background pagina */
--fp-color-border: rgba(0,0,0,0.08);  /* Bordi */
```

### Esempi Uso

```css
/* Pulsante Primario */
background: var(--fp-color-primary);
color: #fff;

/* Testo Muted */
color: var(--fp-color-muted);

/* Card */
background: var(--fp-color-surface);
border: 1px solid var(--fp-color-border);
```

---

## 📏 Spacing System

Sistema di spacing basato su multipli di 4px (unità base).

```css
--fp-spacing-xs: 8px;    /* 2 × 4px */
--fp-spacing-sm: 12px;   /* 3 × 4px */
--fp-spacing-md: 16px;   /* 4 × 4px - Default */
--fp-spacing-lg: 24px;   /* 6 × 4px */
--fp-spacing-xl: 32px;   /* 8 × 4px */
```

### Esempi

```css
/* Padding Card */
padding: var(--fp-spacing-lg);

/* Gap Grid */
gap: var(--fp-spacing-md);

/* Margin Bottom */
margin-bottom: var(--fp-spacing-xl);
```

---

## 🔲 Border Radius

```css
--fp-radius-sm: 6px;     /* Piccolo (tag, badge) */
--fp-radius-md: 8px;     /* Medio (input, button) */
--fp-radius-lg: 12px;    /* Large (card, modal) */
--fp-radius-xl: 16px;    /* Extra large */
--fp-radius-full: 9999px;/* Circolare completo */
```

### Quando Usare

- **sm (6px)** - Badge, piccoli elementi
- **md (8px)** - Input, button standard
- **lg (12px)** - Card, container principali
- **xl (16px)** - Modal, elementi grandi
- **full** - Pill buttons, tag circolari

---

## 🌑 Shadows

Sistema di ombre progressive per depth perception.

```css
--fp-shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
--fp-shadow-md: 0 1px 3px rgba(0,0,0,0.08);
--fp-shadow-lg: 0 4px 6px rgba(0,0,0,0.1);
--fp-shadow-xl: 0 10px 15px rgba(0,0,0,0.1);
```

### Livelli di Elevazione

- **sm** - Leggero hover state
- **md** - Card, elementi flat
- **lg** - Dropdown, floating elements
- **xl** - Modal, drawer, elementi sovrapposti

---

## 🔤 Typography

### Font Family

```css
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
```

Sistema font nativo per performance ottimali.

### Scale Tipografica

```css
/* Headings */
h1: 28px / font-weight: 700
h2: 20px / font-weight: 600
h3: 16px / font-weight: 700

/* Body */
Base: 15px / line-height: 1.5
Small: 13px
Large: 16px
```

---

## 🎯 Componenti

### 1. Buttons

#### Primary Button

```css
.button-primary {
    background: var(--fp-color-primary);
    color: #fff;
    padding: 10px 20px;
    border-radius: var(--fp-radius-md);
    font-weight: 600;
    box-shadow: var(--fp-shadow-sm);
}

.button-primary:hover {
    background: var(--fp-color-primary-hover);
    transform: translateY(-1px);
    box-shadow: var(--fp-shadow-md);
}
```

#### Secondary Button

```css
.button {
    background: #fff;
    border: 1px solid #d1d5db;
    padding: 10px 20px;
    border-radius: var(--fp-radius-md);
    font-weight: 500;
}
```

### 2. Empty State

```html
<div class="fp-forms-empty-state">
    <div class="fp-forms-empty-icon">📋</div>
    <h2>Titolo</h2>
    <p>Descrizione</p>
    <a href="#" class="button button-primary button-hero">
        Call to Action
    </a>
</div>
```

```css
.fp-forms-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: var(--fp-color-background);
    border-radius: var(--fp-radius-lg);
    border: 2px dashed #e5e7eb;
}
```

### 3. Card

```html
<div class="fp-card">
    <h3>Card Title</h3>
    <p>Card content...</p>
</div>
```

```css
.fp-card {
    background: var(--fp-color-surface);
    border-radius: var(--fp-radius-lg);
    box-shadow: var(--fp-shadow-md);
    padding: var(--fp-spacing-lg);
    border: 1px solid var(--fp-color-border);
}
```

### 4. Badge / Chip

```html
<span class="fp-badge">Nuovo</span>
```

```css
.fp-badge {
    display: inline-flex;
    padding: 4px 10px;
    border-radius: var(--fp-radius-full);
    font-size: 12px;
    font-weight: 600;
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
```

### 5. Form Input

```html
<input type="text" class="fp-forms-input">
```

```css
.fp-forms-input {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid var(--fp-forms-border);
    border-radius: var(--fp-radius-md);
    font-size: 15px;
    transition: all 0.2s ease;
}

.fp-forms-input:focus {
    border-color: var(--fp-color-primary);
    box-shadow: 0 0 0 3px var(--fp-focus-ring);
}
```

### 6. Table

```css
.fp-forms-table-container {
    background: var(--fp-color-surface);
    border-radius: var(--fp-radius-lg);
    box-shadow: var(--fp-shadow-md);
    border: 1px solid var(--fp-color-border);
    overflow: hidden;
}

.wp-list-table thead {
    background: var(--fp-color-background);
}

.wp-list-table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}
```

---

## ♿ Accessibilità

### Focus States

Tutti gli elementi interattivi hanno un focus state visibile:

```css
*:focus-visible {
    outline: 3px solid var(--fp-focus-ring);
    outline-offset: 2px;
}

--fp-focus-ring: rgba(37, 99, 235, 0.5);
```

### Contrast Ratio

- Testo normale su bianco: **11:1** (AAA)
- Testo muted su bianco: **4.5:1** (AA)
- Primary button: **4.5:1** (AA)

### Screen Readers

```css
.fp-forms-sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    clip: rect(0,0,0,0);
    white-space: nowrap;
}
```

### ARIA Labels

Tutti i campi form hanno label associati:

```html
<label for="email">Email</label>
<input id="email" type="email" aria-required="true">
```

---

## 📱 Responsive Breakpoints

```css
/* Mobile First */
@media screen and (max-width: 480px) {
    /* Small phones */
}

@media screen and (max-width: 768px) {
    /* Tablets & large phones */
}

@media screen and (max-width: 1024px) {
    /* Small desktops */
}

@media screen and (max-width: 1200px) {
    /* Medium desktops */
}
```

### Esempi

```css
/* Desktop (default) */
.fp-builder-container {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
}

/* Mobile */
@media (max-width: 1200px) {
    .fp-builder-container {
        grid-template-columns: 1fr;
    }
}
```

---

## 🌓 Dark Mode

Supporto completo per dark mode usando `prefers-color-scheme`.

```css
@media (prefers-color-scheme: dark) {
    :root {
        --fp-color-text: #f9fafb;
        --fp-color-muted: #9ca3af;
        --fp-color-border: #374151;
        --fp-color-surface: #1f2937;
        --fp-color-background: #111827;
    }
}
```

### Auto-Switch

I colori si adattano automaticamente alle preferenze di sistema dell'utente.

---

## 🎬 Animazioni

### Transitions

```css
/* Standard */
transition: all 0.2s ease;

/* Hover Transform */
transform: translateY(-2px);

/* Focus */
transition: border-color 0.2s ease, box-shadow 0.2s ease;
```

### Keyframes

```css
/* Slide In */
@keyframes fp-slide-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Spin (loading) */
@keyframes fp-spin {
    to { transform: rotate(360deg); }
}
```

### Reduced Motion

Rispetto per le preferenze di accessibilità:

```css
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
```

---

## 🎯 States & Feedback

### Loading State

```css
.fp-forms-form.is-loading {
    opacity: 0.6;
    pointer-events: none;
}

.is-loading .button::after {
    content: '';
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #fff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: fp-spin 0.6s linear infinite;
}
```

### Error State

```css
.fp-forms-field.has-error .fp-forms-input {
    border-color: var(--fp-color-danger);
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}
```

### Success Message

```css
.fp-forms-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
    padding: 16px 20px;
    border-radius: var(--fp-radius-md);
}
```

---

## 📦 Utilities Classes

```css
/* Spacing */
.fp-forms-mt-sm { margin-top: var(--fp-spacing-sm); }
.fp-forms-mt-md { margin-top: var(--fp-spacing-md); }
.fp-forms-mt-lg { margin-top: var(--fp-spacing-lg); }

.fp-forms-mb-sm { margin-bottom: var(--fp-spacing-sm); }
.fp-forms-mb-md { margin-bottom: var(--fp-spacing-md); }
.fp-forms-mb-lg { margin-bottom: var(--fp-spacing-lg); }

/* Text */
.fp-forms-text-center { text-align: center; }

/* Screen Reader Only */
.fp-forms-sr-only { /* ... */ }
```

---

## 🔧 Implementazione

### Admin CSS

```php
wp_enqueue_style(
    'fp-forms-admin',
    FP_FORMS_PLUGIN_URL . 'assets/css/admin.css',
    [],
    FP_FORMS_VERSION
);
```

### Frontend CSS

```php
wp_enqueue_style(
    'fp-forms-frontend',
    FP_FORMS_PLUGIN_URL . 'assets/css/frontend.css',
    [],
    FP_FORMS_VERSION
);
```

### Body Class Admin

```php
add_filter( 'admin_body_class', function( $classes ) {
    return $classes . ' fp-forms-admin-shell';
} );
```

---

## ✅ Checklist Design

Quando crei nuovi componenti, assicurati di:

- [ ] Usare variabili CSS del design system
- [ ] Implementare focus states visibili
- [ ] Supportare dark mode
- [ ] Essere responsive
- [ ] Includere ARIA labels dove necessario
- [ ] Testare con screen reader
- [ ] Verificare contrast ratio
- [ ] Supportare reduced motion
- [ ] Usare spacing system coerente
- [ ] Mantenere consistenza con FP-Experiences

---

## 📚 Risorse

### Palette Colori
- Primary: #2563eb (Blue 600)
- Success: #059669 (Emerald 600)
- Danger: #dc2626 (Red 600)
- Warning: #d97706 (Amber 600)

### Ispirazione Design
- FP-Experiences (plugin principale)
- Tailwind CSS v3 color system
- Material Design 3 principles
- Apple Human Interface Guidelines

### Tools
- [Contrast Checker](https://webaim.org/resources/contrastchecker/)
- [Color.review](https://color.review/) - Verifica accessibilità
- [Can I Use](https://caniuse.com/) - Compatibilità CSS

---

## 🎓 Best Practices

### 1. Mobile First

Scrivi sempre CSS mobile-first:

```css
/* Mobile (default) */
.element {
    flex-direction: column;
}

/* Desktop */
@media (min-width: 768px) {
    .element {
        flex-direction: row;
    }
}
```

### 2. CSS Variables

Usa sempre le variabili CSS:

```css
/* ❌ Bad */
color: #6b7280;

/* ✅ Good */
color: var(--fp-color-muted);
```

### 3. Naming Convention

Usa prefisso `fp-forms-` per evitare conflitti:

```css
.fp-forms-button { }
.fp-forms-input { }
.fp-forms-card { }
```

### 4. Performance

```css
/* Usa transform invece di margin/padding per animazioni */
.button:hover {
    transform: translateY(-2px); /* ✅ GPU accelerated */
}

/* Evita */
.button:hover {
    margin-top: -2px; /* ❌ Trigger reflow */
}
```

---

**Design System v1.0.0**  
**Aggiornato:** 2025-11-04  
**Mantenuto da:** Francesco Passeri

