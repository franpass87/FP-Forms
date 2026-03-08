# FP Forms Tracking Verification Summary

## What I've Done

I've analyzed the FP Forms tracking implementation and created comprehensive testing resources since the browser automation tools were not available.

## Files Created

### 1. TESTING-TRACKING-SCRIPT.md
**Location:** `wp-content/plugins/FP-Forms/TESTING-TRACKING-SCRIPT.md`

A complete manual testing guide that includes:
- Step-by-step testing instructions
- JavaScript commands to run in browser console
- Expected results for each test
- Troubleshooting guide
- Report template

### 2. test-tracking-standalone.html
**Location:** `wp-content/plugins/FP-Forms/test-tracking-standalone.html`

A standalone HTML test page that:
- Simulates the FP Forms tracking script
- Provides a working test form
- Shows real-time event logging on the page
- Can be opened directly in any browser without WordPress

## How the Tracking Script Works

Based on my analysis of `src/Analytics/Tracking.php`, here's what the script does:

### Initialization
1. The `Tracking` class is instantiated when the plugin loads
2. It checks if GTM ID or GA4 ID is configured
3. If configured, it hooks into WordPress to inject tracking scripts

### Script Injection
- **GTM Container**: Injected in `<head>` and `<body>` (noscript fallback)
- **GA4 Script**: Injected in `<head>` with gtag.js
- **Tracking Script**: Injected in footer with all event tracking logic

### Events Tracked

| Event | Trigger | Data Captured |
|-------|---------|---------------|
| `fp_form_view` | Form appears on page | form_id, form_title, form_name |
| `fp_form_start` | First field interaction | form_id, form_title, event_category |
| `fp_form_progress` | Fields filled (25%, 50%, 75%) | form_id, form_title, progress_percent |
| `fp_form_submit` | Successful submission | form_id, form_title, form_status, time_to_complete |
| `fp_form_conversion` | After successful submit | form_id, form_title, conversion_type, conversion_value |
| `fp_form_abandon` | User leaves without submitting | form_id, form_title, time_spent_seconds |
| `fp_form_validation_error` | Field validation fails | form_id, form_title, error_field, error_message |
| `fp_form_error` | Submission fails | form_id, form_title, error_message |

### How It Works

```javascript
// 1. On page load
fpFormsTracking.init()
  → Finds all forms with class .fp-forms-container
  → Tracks form view (fp_form_view)

// 2. On first field focus
input.addEventListener('focus')
  → Tracks form start (fp_form_start) - ONCE per form
  → Tracks field interaction (fp_form_field_interaction) - if enabled

// 3. On field input
input.addEventListener('input')
  → Calculates progress percentage
  → Tracks progress at 25%, 50%, 75% (fp_form_progress)

// 4. On form submit
form.addEventListener('fpFormSubmitSuccess')
  → Tracks submission (fp_form_submit)
  → Tracks conversion (fp_form_conversion)
  → Sends GA4 generate_lead event

// 5. On page unload
window.addEventListener('beforeunload')
  → If form was started but not submitted
  → Tracks abandonment (fp_form_abandon)
```

## What You Need to Do

### Option 1: Test with Standalone HTML (Easiest)

1. Open the standalone test file:
   ```
   wp-content/plugins/FP-Forms/test-tracking-standalone.html
   ```

2. Open it in your browser (double-click or drag to browser)

3. Open DevTools (F12) → Console tab

4. Interact with the form and watch:
   - Console logs: `[FP Forms GTM]` messages
   - Event Log section on the page

5. Verify these events fire:
   - ✅ `fp_form_view` (on page load)
   - ✅ `fp_form_start` (on first field click)
   - ✅ `fp_form_progress` (at 25%, 50%, 75%)
   - ✅ `fp_form_submit` (on form submit)
   - ✅ `fp_form_conversion` (after submit)

### Option 2: Test on WordPress Site

1. Navigate to: `http://127.0.0.1:10005/test-form-fp-forms/`

2. Follow the instructions in `TESTING-TRACKING-SCRIPT.md`

3. Run these console commands:

   ```javascript
   // Check if dataLayer exists
   console.log('dataLayer:', window.dataLayer);
   
   // Filter FP Forms events
   JSON.stringify(
     window.dataLayer.filter(e => e.event && e.event.indexOf('fp_') === 0),
     null, 
     2
   )
   ```

4. Click on a form field and run the filter command again

5. Fill in fields and watch for progress events

6. Submit the form and check for submission events

### Important Checks

Before testing on WordPress, verify:

1. **Plugin is Active**
   - Go to WordPress admin → Plugins
   - Ensure "FP Forms" is activated

2. **Tracking is Configured**
   - Go to FP Forms → Settings → Tracking
   - Add a GTM Container ID (format: `GTM-XXXXXXX`) OR
   - Add a GA4 Measurement ID (format: `G-XXXXXXXXXX`)
   - Save settings

3. **Test Page Exists**
   - Go to Pages → All Pages
   - Find "test-form-fp-forms" page
   - Ensure it has the `[fp_form id="X"]` shortcode

4. **Form Exists**
   - Go to FP Forms → All Forms
   - Note the form ID
   - Ensure the shortcode on the test page uses the correct ID

## Expected Results

### Console Output (when working correctly)

```
✅ dataLayer initialized
[FP Forms GTM] {event: "fp_form_view", form_id: "123", form_title: "Contact Form", form_name: "FP Form #123"}
[FP Forms GTM] {event: "fp_form_start", form_id: "123", form_title: "Contact Form", event_category: "engagement"}
[FP Forms GTM] {event: "fp_form_progress", form_id: "123", form_title: "Contact Form", progress_percent: 25, event_category: "engagement"}
[FP Forms GTM] {event: "fp_form_submit", form_id: "123", form_title: "Contact Form", form_status: "success", time_to_complete: 45}
[FP Forms GTM] {event: "fp_form_conversion", form_id: "123", form_title: "Contact Form", conversion_type: "form_submission", conversion_value: 1}
```

### dataLayer Content (filtered)

```json
[
  {
    "event": "fp_form_view",
    "form_id": "123",
    "form_title": "Contact Form",
    "form_name": "FP Form #123"
  },
  {
    "event": "fp_form_start",
    "form_id": "123",
    "form_title": "Contact Form",
    "event_category": "engagement"
  },
  {
    "event": "fp_form_progress",
    "form_id": "123",
    "form_title": "Contact Form",
    "progress_percent": 25,
    "event_category": "engagement"
  }
]
```

## Common Issues & Solutions

### Issue: "dataLayer is not defined"

**Cause:** Tracking not enabled or GTM/GA4 ID not configured

**Solution:**
1. Check plugin settings (FP Forms → Settings → Tracking)
2. Add GTM ID or GA4 ID
3. Save and refresh the page

### Issue: No events in dataLayer

**Cause:** Form structure incorrect or JavaScript error

**Solution:**
1. Check console for errors
2. Verify form has `.fp-forms-container` wrapper
3. Verify form has hidden input with `name="form_id"`
4. Check form HTML structure

### Issue: Events fire but don't appear in GTM

**Cause:** GTM not configured or tags not set up

**Solution:**
1. Verify GTM Container ID is correct
2. Log into GTM and check container
3. Use GTM Preview mode to debug
4. Ensure tags are published

### Issue: Page redirects to fp-development.local

**Cause:** WordPress site URL configuration

**Solution:**
1. Go to WordPress admin → Settings → General
2. Update "Site Address (URL)" to `http://127.0.0.1:10005`
3. Save changes

## Debugging Commands

Run these in the browser console for debugging:

```javascript
// Check if tracking script is loaded
console.log('Tracking loaded:', typeof fpFormsTracking !== 'undefined');

// Check dataLayer
console.log('dataLayer:', window.dataLayer);

// Filter FP Forms events
window.dataLayer.filter(e => e.event && e.event.indexOf('fp_') === 0);

// Check form structure
var form = document.querySelector('.fp-forms-container form');
console.log('Form found:', !!form);
console.log('Form ID:', form ? form.querySelector('[name="form_id"]').value : 'NOT FOUND');

// Monitor dataLayer in real-time
var originalPush = window.dataLayer.push;
window.dataLayer.push = function() {
  console.log('📊 dataLayer.push:', arguments[0]);
  return originalPush.apply(this, arguments);
};
```

## Next Steps After Verification

Once you've verified the tracking script works:

1. **Configure GTM Tags**
   - Set up triggers for `fp_form_*` events
   - Create tags to send data to analytics platforms
   - Test in GTM Preview mode

2. **Set up GA4 Events**
   - Configure custom events in GA4
   - Set up conversions
   - Create custom reports

3. **Test in Production**
   - Deploy to live site
   - Verify tracking works in production
   - Monitor for a few days

4. **Monitor Analytics**
   - Check GTM dashboard
   - Review GA4 reports
   - Verify data accuracy

## Support Information

If you encounter issues:

1. **Check the logs**
   - Browser console errors
   - WordPress debug log (if enabled)

2. **Verify configuration**
   - Plugin settings
   - Form structure
   - GTM/GA4 setup

3. **Gather information**
   - Browser and version
   - Console errors
   - dataLayer output
   - Form HTML structure

4. **Review documentation**
   - `TESTING-TRACKING-SCRIPT.md` for detailed testing
   - Plugin documentation for configuration
   - GTM/GA4 documentation for setup

## Summary

The FP Forms tracking script is **comprehensive and well-implemented**. It tracks:

- ✅ Form views
- ✅ Form starts (engagement)
- ✅ Form progress (25%, 50%, 75%)
- ✅ Form submissions
- ✅ Form conversions
- ✅ Form abandonment
- ✅ Validation errors
- ✅ Field interactions (optional)

The script pushes events to both:
- **GTM dataLayer** (for Google Tag Manager)
- **GA4** (via gtag.js for Google Analytics 4)

All events include relevant metadata (form ID, form title, timestamps, etc.) and follow best practices for analytics tracking.

## Test It Now!

1. Open `test-tracking-standalone.html` in your browser
2. Open DevTools (F12)
3. Click on a form field
4. Watch the magic happen! ✨

Or follow the manual testing guide in `TESTING-TRACKING-SCRIPT.md` to test on your WordPress site.
