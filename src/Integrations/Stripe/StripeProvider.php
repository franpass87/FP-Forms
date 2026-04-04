<?php
declare(strict_types=1);

namespace FPForms\Integrations\Stripe;

use FPForms\Integrations\PaymentManager;

/**
 * Provider pagamenti Stripe (Checkout Session redirect).
 */
class StripeProvider {

    /**
     * Costruttore: registra l'hook per il pagamento Stripe e il webhook REST.
     */
    public function __construct() {
        if ( ! class_exists( \Stripe\Stripe::class ) ) {
            return;
        }
        add_action( 'fp_forms_process_payment_stripe', [ $this, 'process' ], 10, 4 );
        add_action( 'rest_api_init', [ $this, 'register_webhook_route' ] );
    }

    /**
     * Registra la route REST per il webhook Stripe.
     */
    public function register_webhook_route(): void {
        register_rest_route( 'fp-forms/v1', '/stripe-webhook', [
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [ $this, 'handle_webhook' ],
            'permission_callback' => '__return_true',
            'args'                => [],
        ] );
    }

    /**
     * Gestisce il webhook Stripe (eventi checkout.session.completed, payment_intent.succeeded).
     */
    public function handle_webhook( \WP_REST_Request $request ): \WP_REST_Response {
        $settings = get_option( 'fp_forms_stripe_settings', [] );
        $webhook_secret = $settings['webhook_secret'] ?? '';
        if ( $webhook_secret === '' ) {
            return new \WP_REST_Response( [ 'error' => 'webhook not configured' ], 400 );
        }
        $payload = $request->get_body();
        $sig_header = $request->get_header( 'stripe_signature' );
        if ( ! $sig_header || $payload === '' ) {
            return new \WP_REST_Response( [ 'error' => 'missing signature or body' ], 400 );
        }
        try {
            \Stripe\Stripe::setApiKey( $settings['secret_key'] ?? '' );
            $event = \Stripe\Webhook::constructEvent( $payload, $sig_header, $webhook_secret );
        } catch ( \UnexpectedValueException $e ) {
            return new \WP_REST_Response( [ 'error' => 'invalid payload' ], 400 );
        } catch ( \Stripe\Exception\SignatureVerificationException $e ) {
            return new \WP_REST_Response( [ 'error' => 'invalid signature' ], 400 );
        }
        if ( $event->type === 'checkout.session.completed' ) {
            $session = $event->data->object;
            $submission_id = isset( $session->client_reference_id ) ? (int) $session->client_reference_id : 0;
            if ( $submission_id > 0 ) {
                $this->mark_transaction_completed( $submission_id, $session->id ?? '', $session->payment_status ?? 'paid' );
                $db = \FPForms\Plugin::instance()->database;
                if ( method_exists( $db, 'update_submission_status' ) ) {
                    $db->update_submission_status( $submission_id, 'unread' );
                }
                do_action( 'fp_forms_payment_completed', $submission_id, (int) ( $session->metadata->form_id ?? 0 ), [
                    'transaction_id'   => $session->id,
                    'payment_status'   => $session->payment_status ?? 'paid',
                    'amount_total'     => isset( $session->amount_total ) ? (int) $session->amount_total : 0,
                    'amount_in_cents'  => true,
                    'currency'         => isset( $session->currency ) ? (string) $session->currency : 'EUR',
                    'payment_provider' => 'stripe',
                ] );
            }
        }
        return new \WP_REST_Response( [ 'received' => true ], 200 );
    }

    /**
     * Aggiorna lo status della transazione a completed.
     */
    private function mark_transaction_completed( int $submission_id, string $transaction_id, string $status ): void {
        global $wpdb;
        $table = $wpdb->prefix . 'fp_forms_transactions';
        $wpdb->update(
            $table,
            [ 'status' => 'completed' ],
            [ 'submission_id' => $submission_id, 'transaction_id' => $transaction_id ],
            [ '%s' ],
            [ '%d', '%s' ]
        );
    }

    /**
     * Processa il pagamento creando una Stripe Checkout Session.
     *
     * @param int   $submission_id ID submission.
     * @param int   $form_id      ID form.
     * @param array $data         Dati submission.
     * @param float $amount       Importo in unità (es. euro).
     */
    public function process( int $submission_id, int $form_id, array $data, float $amount ): void {
        $settings = get_option( 'fp_forms_stripe_settings', [] );
        $secret_key = $settings['secret_key'] ?? '';
        if ( $secret_key === '' ) {
            \FPForms\Core\Logger::warning( 'Stripe: secret key non configurata', [ 'form_id' => $form_id ] );
            return;
        }

        $payments = \FPForms\Plugin::instance()->payments;
        if ( ! $payments instanceof PaymentManager ) {
            return;
        }
        $existing = $payments->get_transactions_by_submission( $submission_id );
        foreach ( $existing as $tx ) {
            if ( in_array( $tx->status, [ 'pending', 'completed' ], true ) ) {
                \FPForms\Core\Logger::info( 'Stripe: idempotenza, transazione già esistente', [
                    'submission_id' => $submission_id,
                    'tx_id' => $tx->id,
                ] );
                return;
            }
        }

        try {
            \Stripe\Stripe::setApiKey( $secret_key );
        } catch ( \Throwable $e ) {
            \FPForms\Core\Logger::error( 'Stripe setApiKey failed', [ 'error' => $e->getMessage() ] );
            return;
        }

        $amount_cents = (int) round( $amount * 100 );
        if ( $amount_cents < 50 ) {
            \FPForms\Core\Logger::warning( 'Stripe: importo minimo 0.50', [ 'amount' => $amount ] );
            return;
        }

        $success_url = add_query_arg( [
            'fp_forms_success' => 1,
            'submission_id'    => $submission_id,
            'form_id'          => $form_id,
        ], home_url( '/' ) );
        $cancel_url = add_query_arg( [
            'fp_forms_cancel' => 1,
            'form_id'        => $form_id,
        ], home_url( '/' ) );

        try {
            $session = \Stripe\Checkout\Session::create( [
                'payment_method_types' => [ 'card' ],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => sprintf( __( 'Pagamento form #%d', 'fp-forms' ), $form_id ),
                            ],
                            'unit_amount' => $amount_cents,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => $success_url,
                'cancel_url' => $cancel_url,
                'client_reference_id' => (string) $submission_id,
                'metadata' => [
                    'form_id'       => (string) $form_id,
                    'submission_id' => (string) $submission_id,
                ],
            ] );
        } catch ( \Throwable $e ) {
            \FPForms\Core\Logger::error( 'Stripe Session create failed', [
                'submission_id' => $submission_id,
                'error' => $e->getMessage(),
            ] );
            return;
        }

        $session_id = $session->id ?? '';
        $checkout_url = $session->url ?? '';
        if ( $session_id !== '' ) {
            $payments->log_transaction(
                $submission_id,
                $form_id,
                'stripe',
                $amount,
                'pending',
                $session_id,
                [ 'checkout_session_id' => $session_id ]
            );
        }

        if ( $checkout_url !== '' ) {
            add_filter( 'fp_forms_checkout_url', function( $url, $sub_id, $fid, $amt ) use ( $checkout_url, $submission_id ) {
                return (int) $sub_id === (int) $submission_id ? $checkout_url : $url;
            }, 10, 4 );
        }
    }
}
