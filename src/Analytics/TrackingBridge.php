<?php

declare(strict_types=1);

namespace FPForms\Analytics;

use function absint;
use function add_action;
use function apply_filters;
use function array_filter;
use function count;
use function delete_option;
use function do_action;
use function esc_url_raw;
use function get_bloginfo;
use function get_option;
use function get_post;
use function in_array;
use function is_admin;
use function is_ssl;
use function sanitize_email;
use function sanitize_text_field;
use function strtolower;
use function time;
use function update_option;
use function wp_doing_ajax;
use function wp_doing_cron;
use function wp_json_encode;
use function wp_unslash;

/**
 * Bridges FP-Forms submission events to the centralized
 * FP-Marketing-Tracking-Layer via do_action('fp_tracking_event').
 *
 * Replaces Analytics\Tracking (GTM/GA4) and Integrations\MetaPixel.
 * The fp-tracking.js file handles client-side form interaction events
 * (form_view, form_start, form_progress, form_abandon) via DOM events.
 *
 * Pagamenti (webhook): `form_payment_completed` viene accodato lato server senza browser;
 * alla landing `?fp_forms_success=1` viene ripetuto solo il push dataLayer (stesso `event_id`,
 * `fp_skip_server_dispatch`) se la transazione risulta `completed`.
 */
class TrackingBridge {

    private const REPLAY_PAYLOAD_OPTION_PREFIX = 'fp_forms_pcc_r_';

    private const REPLAY_TY_SENT_OPTION_PREFIX = 'fp_forms_pcc_ty_';

    public function __construct() {
        // Submission completata
        add_action('fp_forms_after_save_submission', [$this, 'on_submission'], 10, 3);

        // Form visualizzato (hook già sparato da Frontend\Manager::render_form)
        add_action('fp_forms_form_rendered', [$this, 'on_form_view'], 10, 1);

        // Tentativo di submit (prima della validazione)
        add_action('fp_forms_before_validate_submission', [$this, 'on_submit_attempt'], 10, 2);

        // Pagamento avviato (form con integrazione pagamento)
        add_action('fp_forms_payment_started', [$this, 'on_payment_started'], 10, 4);

        // Pagamento completato (webhook provider)
        add_action('fp_forms_payment_completed', [$this, 'on_payment_completed'], 10, 3);

        add_action('template_redirect', [$this, 'maybeReplayPaymentCompletedForDatalayer'], 5);
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

        $params = [
            'form_id'    => $form_id,
            'form_title' => $title,
            'page_url'   => $this->getRequestPageUrl(),
        ];
        do_action('fp_tracking_event', 'form_view', $this->enrichEventParams($params, 'form_view', $form_id));
    }

    /**
     * Fires form_submit_attempt when the user submits the form
     * (before validation — so we track even failed attempts).
     */
    public function on_submit_attempt(int $form_id, array $form_data): void {
        $form  = get_post($form_id);
        $title = $form instanceof \WP_Post ? $form->post_title : '';

        $params = [
            'form_id'      => $form_id,
            'form_title'   => $title,
            'fields_count' => count($form_data),
            'page_url'     => $this->getRequestPageUrl(),
        ];
        do_action('fp_tracking_event', 'form_submit_attempt', $this->enrichEventParams($params, 'form_submit_attempt', $form_id));
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
            'page_url'      => $this->getRequestPageUrl(),
        ];

        do_action('fp_tracking_event', 'generate_lead', $this->enrichEventParams($params, 'generate_lead', $form_id));
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

        $params = [
            'form_id'            => $form_id,
            'form_title'         => $title,
            'submission_id'      => $submission_id,
            'payment_provider'   => sanitize_text_field($provider),
            'value'              => $amount,
            'currency'           => 'EUR',
            'event_id'           => 'fp_forms_pay_' . $submission_id . '_' . time(),
            'page_url'           => $this->getRequestPageUrl(),
        ];
        do_action('fp_tracking_event', 'form_payment_started', $this->enrichEventParams($params, 'form_payment_started', $form_id));
    }

    /**
     * Fires form_payment_completed after provider confirmation (webhook/callback).
     *
     * @param int   $submission_id
     * @param int   $form_id
     * @param array $payment_data
     */
    public function on_payment_completed(int $submission_id, int $form_id, array $payment_data): void {
        $form  = get_post($form_id);
        $title = $form instanceof \WP_Post ? $form->post_title : '';

        $currency = isset($payment_data['currency']) ? strtoupper(sanitize_text_field((string) $payment_data['currency'])) : 'EUR';
        $value    = 0.0;
        if (isset($payment_data['amount_total']) && is_numeric($payment_data['amount_total'])) {
            $raw = (float) $payment_data['amount_total'];
            $value = ! empty($payment_data['amount_in_cents']) ? round($raw / 100, 2) : $raw;
        } elseif (isset($payment_data['value']) && is_numeric($payment_data['value'])) {
            $value = (float) $payment_data['value'];
        }

        $params = [
            'form_id'          => $form_id,
            'form_title'       => $title,
            'submission_id'    => $submission_id,
            'transaction_id'   => isset($payment_data['transaction_id']) ? sanitize_text_field((string) $payment_data['transaction_id']) : '',
            'payment_status'   => isset($payment_data['payment_status']) ? sanitize_text_field((string) $payment_data['payment_status']) : '',
            'payment_provider' => isset($payment_data['payment_provider']) ? sanitize_text_field((string) $payment_data['payment_provider']) : '',
            'value'            => $value,
            'currency'         => $currency !== '' ? $currency : 'EUR',
            'event_id'         => 'fp_forms_pay_ok_' . $submission_id . '_' . time(),
            'page_url'         => $this->getRequestPageUrl(),
        ];
        $enriched = $this->enrichEventParams($params, 'form_payment_completed', $form_id);
        $this->persistPaymentCompletedReplayPayload($submission_id, $form_id, $enriched);
        do_action('fp_tracking_event', 'form_payment_completed', $enriched);
    }

    /**
     * Landing post-Stripe (`?fp_forms_success=1&submission_id=&form_id=`): replica `form_payment_completed`
     * nel dataLayer per GTM con lo stesso `event_id` del webhook, senza secondo invio server-side (FP Tracking).
     */
    public function maybeReplayPaymentCompletedForDatalayer(): void {
        if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
            return;
        }
        if (defined('REST_REQUEST') && REST_REQUEST) {
            return;
        }
        if (! isset($_GET['fp_forms_success'], $_GET['submission_id'], $_GET['form_id'])) {
            return;
        }
        if ((string) wp_unslash((string) $_GET['fp_forms_success']) !== '1') {
            return;
        }

        $submission_id = absint((string) $_GET['submission_id']);
        $form_id       = absint((string) $_GET['form_id']);
        if ($submission_id <= 0 || $form_id <= 0) {
            return;
        }

        if (! apply_filters('fp_forms_allow_payment_completed_datalayer_replay', true, $submission_id, $form_id)) {
            return;
        }

        if ((string) get_option(self::replayTySentOptionKey($submission_id), '') === '1') {
            return;
        }

        $db = \FPForms\Plugin::instance()->database;
        $row = $db->get_submission($submission_id);
        if (! $row || (int) $row->form_id !== $form_id) {
            return;
        }

        if (! $this->submissionHasCompletedTransaction($submission_id)) {
            return;
        }

        $payload_key = self::replayPayloadOptionKey($submission_id);
        $raw         = get_option($payload_key, '');
        if (! is_string($raw) || $raw === '') {
            return;
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded) || empty($decoded['params']) || ! is_array($decoded['params'])) {
            return;
        }

        /** @var array<string, mixed> $params */
        $params = $decoded['params'];
        if (! empty($decoded['event_id'])) {
            $params['event_id'] = (string) $decoded['event_id'];
        }

        $params['page_url']                = $this->getRequestPageUrl();
        $params['fp_skip_server_dispatch'] = true;

        do_action('fp_tracking_event', 'form_payment_completed', $params);

        update_option(self::replayTySentOptionKey($submission_id), '1', false);
        delete_option($payload_key);
    }

    /**
     * Salva payload arricchito per replay dataLayer sulla landing di successo pagamento.
     *
     * @param array<string, mixed> $enriched
     */
    private function persistPaymentCompletedReplayPayload(int $submission_id, int $form_id, array $enriched): void {
        if ($submission_id <= 0 || $form_id <= 0) {
            return;
        }

        $db = \FPForms\Plugin::instance()->database;
        $row = $db->get_submission($submission_id);
        if (! $row || (int) $row->form_id !== $form_id) {
            return;
        }

        $json = wp_json_encode(
            [
                'event_id' => (string) ($enriched['event_id'] ?? ''),
                'params'   => $enriched,
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        if ($json === false) {
            return;
        }

        update_option(self::replayPayloadOptionKey($submission_id), $json, false);
    }

    private function submissionHasCompletedTransaction(int $submission_id): bool {
        $payments = \FPForms\Plugin::instance()->payments;
        if (! $payments instanceof \FPForms\Integrations\PaymentManager) {
            return false;
        }

        foreach ($payments->get_transactions_by_submission($submission_id) as $tx) {
            if (isset($tx->status) && (string) $tx->status === 'completed') {
                return true;
            }
        }

        return false;
    }

    private static function replayPayloadOptionKey(int $submission_id): string
    {
        return self::REPLAY_PAYLOAD_OPTION_PREFIX . $submission_id;
    }

    private static function replayTySentOptionKey(int $submission_id): string
    {
        return self::REPLAY_TY_SENT_OPTION_PREFIX . $submission_id;
    }

    /**
     * Parametri comuni per GA4/GTM/Meta (server-side) + filtro estensibile.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function enrichEventParams(array $params, string $context, int $form_id): array {
        $params['fp_source']  = 'fp_forms';
        $params['form_type'] = isset($params['form_type']) ? (string) $params['form_type'] : 'fp_forms';
        $params['affiliation'] = (string) get_bloginfo('name');

        $utmKeys = [
            'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
            'gclid', 'fbclid', 'msclkid', 'ttclid',
        ];
        foreach ($utmKeys as $key) {
            if (! isset($params[ $key ]) && isset($_GET[ $key ])) {
                $params[ $key ] = sanitize_text_field(wp_unslash((string) $_GET[ $key ]));
            }
        }

        /** @var array<string, mixed> $out */
        $out = apply_filters('fp_forms_tracking_event_params', $params, $context, $form_id);

        return $out;
    }

    /**
     * URL completo della richiesta corrente (REST/AJAX inclusi).
     */
    private function getRequestPageUrl(): string {
        if (! isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'])) {
            return '';
        }

        $scheme = is_ssl() ? 'https' : 'http';
        $path   = wp_unslash((string) $_SERVER['REQUEST_URI']);

        return esc_url_raw($scheme . '://' . sanitize_text_field((string) $_SERVER['HTTP_HOST']) . $path);
    }
}
