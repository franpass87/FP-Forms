<?php

declare(strict_types=1);

namespace FPForms\Support;

/**
 * Estrae consensi dai campi dedicati privacy / marketing rispetto alla definizione del form.
 */
final class FormConsentExtractor {

    /**
     * Analizza la submission rispetto ai campi `privacy-checkbox` e `marketing-checkbox`.
     *
     * @param int   $form_id ID form.
     * @param array $data    Dati sanitizzati (chiavi = nome campo senza prefisso `fp_field_`).
     * @return array{
     *     has_privacy_field: bool,
     *     has_marketing_field: bool,
     *     privacy_accepted: ?bool,
     *     marketing_opt_in: ?bool
     * }
     */
    public static function analyze( int $form_id, array $data ): array {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        if ( ! is_array( $form ) || empty( $form['fields'] ) || ! is_array( $form['fields'] ) ) {
            return [
                'has_privacy_field'   => false,
                'has_marketing_field' => false,
                'privacy_accepted'    => null,
                'marketing_opt_in'    => null,
            ];
        }

        $privacy_names   = [];
        $marketing_names = [];

        foreach ( $form['fields'] as $field ) {
            if ( ! is_array( $field ) ) {
                continue;
            }
            $type = isset( $field['type'] ) ? (string) $field['type'] : '';
            $name = isset( $field['name'] ) ? (string) $field['name'] : '';
            if ( $name === '' ) {
                continue;
            }
            if ( $type === 'privacy-checkbox' ) {
                $privacy_names[] = $name;
            } elseif ( $type === 'marketing-checkbox' ) {
                $marketing_names[] = $name;
            }
        }

        $has_privacy   = $privacy_names !== [];
        $has_marketing = $marketing_names !== [];

        $privacy_accepted = null;
        if ( $has_privacy ) {
            $privacy_accepted = true;
            foreach ( $privacy_names as $n ) {
                if ( ! self::is_truthy( $data[ $n ] ?? null ) ) {
                    $privacy_accepted = false;
                    break;
                }
            }
        }

        $marketing_opt_in = null;
        if ( $has_marketing ) {
            $marketing_opt_in = false;
            foreach ( $marketing_names as $n ) {
                if ( self::is_truthy( $data[ $n ] ?? null ) ) {
                    $marketing_opt_in = true;
                    break;
                }
            }
        }

        return [
            'has_privacy_field'   => $has_privacy,
            'has_marketing_field' => $has_marketing,
            'privacy_accepted'    => $privacy_accepted,
            'marketing_opt_in'    => $marketing_opt_in,
        ];
    }

    /**
     * @param mixed $value Valore inviato da checkbox / POST.
     */
    private static function is_truthy( $value ): bool {
        if ( is_bool( $value ) ) {
            return $value;
        }
        if ( is_numeric( $value ) ) {
            return (int) $value !== 0;
        }
        $s = strtolower( trim( (string) $value ) );

        return in_array( $s, [ '1', 'true', 'yes', 'on' ], true );
    }
}
