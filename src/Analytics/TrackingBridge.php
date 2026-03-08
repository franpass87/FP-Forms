<?php

declare(strict_types=1);

namespace FPForms\Analytics;

use function add_action;
use function array_filter;
use function count;
use function do_action;
use function get_post;
use function in_array;
use function sanitize_email;
use function sanitize_text_field;
use function strtolower;
use function time;
use function wp_unslash;

/**
 * Bridges FP-Forms submission events to the centralized
 * FP-Marketing-Tracking-Layer via do_action('fp_tracking_event').
 *
 * Replaces Analytics\Tracking (GTM/GA4) and Integrations\MetaPixel.
 * The fp-tracking.js file handles client-side form interaction events
 * (form_view, form_start, form_progress, form_abandon) via DOM events.
 */
class TrackingBridge {

    public function __construct() {
        // Submission completata
        add_action('fp_forms_after_save_submission', [$this, 'on_submission'], 10, 3);

        // Form visualizzato (hook già sparato da Frontend\Manager::render_form)
        add_action('fp_forms_form_rendered', [$this, 'on_form_view'], 10, 1);

        // Tentativo di submit (prima della validazione)
        add_action('fp_forms_before_validate_submission', [$this, 'on_submit_attempt'], 10, 2);

        // Pagamento avviato (form con integrazione pagamento)
        add_action('fp_forms_payment_started', [$this, 'on_payment_started'], 10, 4);
    }

    /**
     * Fires form_view when a form is rendered on the page (server-side).
     * Deduplicates per request using a static set.
     */
    public function on_form_view(int $form_id): void {
        static $fired = [];
        if (isset($fired[$form_id])) {
            return;
        }
        $fired[$form_id] = true;

        $form  = get_post($form_id);
        $title = $form instanceof \WP_Post ? $form->post_title : '';

        do_action('fp_tracking_event', 'form_view', [
            'form_id'    => $form_id,
            'form_title' => $title,
            'page_url'   => (isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : ''),
        ]);
    }

    /**
     * Fires form_submit_attempt when the user submits the form
     * (before validation — so we track even failed attempts).
     */
    public function on_submit_attempt(int $form_id, array $form_data): void {
        $form  = get_post($form_id);
        $title = $form instanceof \WP_Post ? $form->post_title : '';

        do_action('fp_tracking_event', 'form_submit_attempt', [
            'form_id'     => $form_id,
            'form_title'  => $title,
            'fields_count' => count($form_data),
        ]);
    }

    /**
     * Fires the generate_lead tracking event when a form is submitted.
     *
     * @param int   $submission_id
     * @param int   $form_id
     * @param array $data          Sanitized form data
     */
    public function on_submission(int $submission_id, int $form_id, array $data): void {
        $form    = get_post($form_id);
        $title   = $form instanceof \WP_Post ? $form->post_title : '';
        $event_id = 'fp_forms_' . $submission_id . '_' . time();

        // Extract user data for server-side (Meta CAPI / GA4 MP)
        $user_data = $this->extract_user_data($data);

        $params = [
            'form_id'       => $form_id,
            'form_title'    => $title,
            'submission_id' => $submission_id,
            'value'         => 1.0,
            'currency'      => 'EUR',
            'event_id'      => $event_id,
            'user_data'     => $user_data,
        ];

        do_action('fp_tracking_event', 'generate_lead', $params);
    }

    /**
     * Extracts PII fields from form data for server-side hashing.
     * Looks for common field names used in FP-Forms.
     */
    private function extract_user_data(array $data): array {
        $user_data = [];

        $email_keys = ['email', 'mail', 'e-mail', 'email_address'];
        $fn_keys    = ['first_name', 'nome', 'firstname', 'name', 'nome_cognome'];
        $ln_keys    = ['last_name', 'cognome', 'lastname', 'surname'];
        $phone_keys = ['phone', 'telefono', 'tel', 'mobile', 'cellulare'];

        foreach ($data as $key => $value) {
            $key_lower = strtolower((string) $key);

            if (empty($user_data['em']) && in_array($key_lower, $email_keys, true)) {
                $user_data['em'] = sanitize_email((string) $value);
            }
            if (empty($user_data['fn']) && in_array($key_lower, $fn_keys, true)) {
                $user_data['fn'] = sanitize_text_field((string) $value);
            }
            if (empty($user_data['ln']) && in_array($key_lower, $ln_keys, true)) {
                $user_data['ln'] = sanitize_text_field((string) $value);
            }
            if (empty($user_data['ph']) && in_array($key_lower, $phone_keys, true)) {
                $user_data['ph'] = sanitize_text_field((string) $value);
            }
        }

        return array_filter($user_data);
    }

    /**
     * Fires form_payment_started when a payment process is initiated.
     *
     * @param int    $submission_id
     * @param int    $form_id
     * @param string $provider       Payment provider slug (e.g. stripe, paypal)
     * @param float  $amount
     */
    public function on_payment_started(int $submission_id, int $form_id, string $provider, float $amount): void {
        $form  = get_post($form_id);
        $title = $form instanceof \WP_Post ? $form->post_title : '';

        do_action('fp_tracking_event', 'form_payment_started', [
            'form_id'       => $form_id,
            'form_title'    => $title,
            'submission_id' => $submission_id,
            'payment_provider' => sanitize_text_field($provider),
            'value'         => $amount,
            'currency'      => 'EUR',
            'event_id'      => 'fp_forms_pay_' . $submission_id . '_' . time(),
        ]);
    }
}
