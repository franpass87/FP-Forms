<?php

namespace FPForms\Analytics;

/**
 * GTM & GA4 Tracking — disabled.
 * Tracking is now handled by FP-Marketing-Tracking-Layer.
 * Client-side events are fired via fp-tracking.js DOM events.
 * Server-side events are fired via TrackingBridge -> do_action('fp_tracking_event').
 */
class Tracking {

    public function __construct() {
        // No-op: tracking delegated to FP-Marketing-Tracking-Layer
    }

    public function is_enabled(): bool {
        return false;
    }

    public function track_submission(int $form_id, int $submission_id, bool $success): void {
        // No-op
    }
}
