<?php

namespace FPForms\Integrations;

/**
 * Meta Pixel & Conversions API — disabled.
 * Tracking is now handled by FP-Marketing-Tracking-Layer.
 * Server-side Lead events are dispatched via Analytics\TrackingBridge.
 */
class MetaPixel {

    public function __construct() {
        // No-op: tracking delegated to FP-Marketing-Tracking-Layer
    }

    public function is_enabled(): bool {
        return false;
    }
}
