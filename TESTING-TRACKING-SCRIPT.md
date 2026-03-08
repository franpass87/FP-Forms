# FP Forms Tracking Script - Testing Guide

## Overview
This guide will help you verify that the FP Forms tracking script is working correctly on your WordPress site.

## Prerequisites
- WordPress site running at: `http://127.0.0.1:10005/`
- FP Forms plugin installed and activated
- A test page with an FP Form: `http://127.0.0.1:10005/test-form-fp-forms/`

## What Gets Tracked

The FP Forms tracking script monitors these events:

1. **fp_form_view** - When a form is displayed on the page
2. **fp_form_start** - When a user first interacts with any field
3. **fp_form_progress** - Progress tracking at 25%, 50%, 75% completion
4. **fp_form_submit** - When form is successfully submitted
5. **fp_form_conversion** - Conversion event after successful submission
6. **fp_form_abandon** - When user leaves page without submitting
7. **fp_form_validation_error** - When field validation fails
8. **fp_form_error** - When submission fails

## Manual Testing Steps

### Step 1: Check Tracking Configuration

1. Log into WordPress admin
2. Go to **FP Forms → Settings → Tracking**
3. Verify that either:
   - GTM Container ID is configured (e.g., `GTM-XXXXXXX`), OR
   - GA4 Measurement ID is configured (e.g., `G-XXXXXXXXXX`)
4. Note: If both are empty, tracking will be disabled

### Step 2: Navigate to Test Page

1. Open your browser
2. Navigate to: `http://127.0.0.1:10005/test-form-fp-forms/`
3. If the page redirects to `fp-development.local`, note this behavior

### Step 3: Open Browser DevTools

1. Press **F12** (or right-click → Inspect)
2. Go to the **Console** tab
3. Clear the console (click the 🚫 icon or press Ctrl+L)

### Step 4: Verify dataLayer Exists

Run this command in the console:

```javascript
console.log('dataLayer exists:', typeof window.dataLayer !== 'undefined');
console.log('dataLayer content:', window.dataLayer);
```

**Expected Result:**
```
dataLayer exists: true
dataLayer content: Array(1) [...]
```

### Step 5: Filter FP Forms Events

Run this command to see only FP Forms tracking events:

```javascript
JSON.stringify(
  window.dataLayer ? 
    window.dataLayer.filter(function(e) { 
      return e.event && e.event.indexOf('fp_') === 0; 
    }) : 
    'dataLayer not found',
  null, 
  2
)
```

**Expected Result (on page load):**
```json
[
  {
    "event": "fp_form_view",
    "form_id": "123",
    "form_title": "Contact Form",
    "form_name": "FP Form #123"
  }
]
```

### Step 6: Test Form Start Event

1. Click on any input field in the form (name, email, message, etc.)
2. Check the console for: `[FP Forms GTM] {event: 'fp_form_start', ...}`
3. Run the filter command again (from Step 5)

**Expected Result:**
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
  }
]
```

### Step 7: Test Form Progress Events

1. Fill in some fields (e.g., name and email)
2. Watch the console for progress events
3. Run the filter command again

**Expected Result:**
You should see `fp_form_progress` events at 25%, 50%, and 75% completion:

```json
{
  "event": "fp_form_progress",
  "form_id": "123",
  "form_title": "Contact Form",
  "progress_percent": 25,
  "event_category": "engagement"
}
```

### Step 8: Test Form Submission

1. Fill out all required fields
2. Click the Submit button
3. Check console for submission events

**Expected Result (on success):**
```json
[
  {
    "event": "fp_form_submit",
    "form_id": "123",
    "form_title": "Contact Form",
    "form_status": "success",
    "time_to_complete": 45
  },
  {
    "event": "fp_form_conversion",
    "form_id": "123",
    "form_title": "Contact Form",
    "conversion_type": "form_submission",
    "conversion_value": 1.0
  }
]
```

### Step 9: Test Form Abandonment

1. Refresh the page
2. Click on a field to start the form
3. Navigate away or close the tab
4. Check if `fp_form_abandon` event was fired (you may need to check GTM/GA4 dashboard)

## Verification Checklist

- [ ] `window.dataLayer` exists
- [ ] `fp_form_view` event fires on page load
- [ ] `fp_form_start` event fires on first field interaction
- [ ] `fp_form_progress` events fire at 25%, 50%, 75%
- [ ] `fp_form_submit` event fires on successful submission
- [ ] `fp_form_conversion` event fires after submission
- [ ] Console shows `[FP Forms GTM]` log messages
- [ ] No JavaScript errors in console

## Troubleshooting

### Issue: dataLayer is undefined

**Possible Causes:**
- GTM/GA4 IDs not configured in plugin settings
- Tracking script not loaded
- JavaScript error preventing script execution

**Solution:**
1. Check plugin settings (FP Forms → Settings → Tracking)
2. View page source (Ctrl+U) and search for "fpFormsTracking"
3. Check console for JavaScript errors

### Issue: No events are being tracked

**Possible Causes:**
- Form doesn't have required structure
- Form ID missing
- JavaScript error

**Solution:**
1. Inspect the form HTML and verify:
   - Form is inside `.fp-forms-container` element
   - Hidden input with `name="form_id"` exists
   - Form has the correct structure

2. Check console for errors

### Issue: Events fire but don't appear in GTM/GA4

**Possible Causes:**
- GTM/GA4 not properly configured
- Wrong container/measurement ID
- GTM tags not published

**Solution:**
1. Verify GTM Container ID format: `GTM-XXXXXXX`
2. Verify GA4 Measurement ID format: `G-XXXXXXXXXX`
3. Check GTM workspace is published
4. Use GTM Preview mode to debug

### Issue: Form redirect to fp-development.local

**Possible Causes:**
- WordPress site URL setting
- Local development environment configuration

**Solution:**
1. Check WordPress admin → Settings → General
2. Verify Site Address (URL) matches your local IP
3. Update if necessary

## Advanced Testing

### Monitor All dataLayer Events

Run this in console to monitor all events in real-time:

```javascript
window.dataLayer = window.dataLayer || [];
var originalPush = window.dataLayer.push;
window.dataLayer.push = function() {
  console.log('dataLayer.push:', arguments[0]);
  return originalPush.apply(this, arguments);
};
```

### Check Tracking Script Configuration

Run this to see the tracking configuration:

```javascript
// This will show if tracking is enabled and what features are active
console.log('Tracking script loaded:', typeof fpFormsTracking !== 'undefined');
```

### Inspect Form Structure

Run this to verify form structure:

```javascript
var form = document.querySelector('.fp-forms-container form');
var formId = form ? form.querySelector('[name="form_id"]') : null;
console.log('Form found:', !!form);
console.log('Form ID:', formId ? formId.value : 'NOT FOUND');
console.log('Form container:', form ? form.closest('.fp-forms-container') : 'NOT FOUND');
```

## Expected Console Output

When everything is working correctly, you should see console messages like:

```
[FP Forms GTM] {event: "fp_form_view", form_id: "123", form_title: "Contact Form", form_name: "FP Form #123"}
[FP Forms GTM] {event: "fp_form_start", form_id: "123", form_title: "Contact Form", event_category: "engagement"}
[FP Forms GA4] form_start {form_id: "123", form_name: "Contact Form"}
[FP Forms GTM] {event: "fp_form_progress", form_id: "123", form_title: "Contact Form", progress_percent: 25, event_category: "engagement"}
[FP Forms GA4] form_progress {form_id: "123", form_name: "Contact Form", progress: 25}
```

## Report Template

After testing, report your findings using this template:

```
## FP Forms Tracking Test Results

**Test Date:** [Date]
**Test URL:** http://127.0.0.1:10005/test-form-fp-forms/
**Browser:** [Chrome/Firefox/Safari/Edge]

### Configuration
- [ ] GTM ID configured: [GTM-XXXXXXX or N/A]
- [ ] GA4 ID configured: [G-XXXXXXXXXX or N/A]
- [ ] Track Views enabled: [Yes/No]
- [ ] Track Interactions enabled: [Yes/No]

### Test Results
- [ ] Page loads successfully
- [ ] dataLayer exists: [Yes/No]
- [ ] fp_form_view event fires: [Yes/No]
- [ ] fp_form_start event fires: [Yes/No]
- [ ] fp_form_progress events fire: [Yes/No]
- [ ] fp_form_submit event fires: [Yes/No]
- [ ] Console shows tracking logs: [Yes/No]

### Issues Found
[Describe any issues or errors]

### dataLayer Events Captured
[Paste the output of the filter command]
```

## Next Steps

After verifying the tracking script works:

1. **Configure GTM Tags** - Set up triggers and tags in GTM to handle the events
2. **Test in Production** - Verify tracking works on live site
3. **Set up GA4 Events** - Configure custom events in GA4 dashboard
4. **Monitor Analytics** - Check that data is flowing correctly

## Support

If you encounter issues not covered in this guide:

1. Check the plugin logs (if logging is enabled)
2. Review the plugin documentation
3. Contact plugin support with:
   - Browser console errors
   - dataLayer output
   - Form HTML structure
   - Plugin version and settings
