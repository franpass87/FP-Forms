<?php
namespace FPForms\Core;

/**
 * Hooks Manager per estensibilità del plugin
 */
class Hooks {
    
    /**
     * Registra tutti gli hooks del plugin
     */
    public static function register() {
        // Form hooks
        self::register_form_hooks();
        
        // Submission hooks
        self::register_submission_hooks();
        
        // Email hooks
        self::register_email_hooks();
        
        // Admin hooks
        self::register_admin_hooks();
    }
    
    /**
     * Registra form hooks
     */
    private static function register_form_hooks() {
        /**
         * Fires before a form is created
         * 
         * @param string $title Form title
         * @param array $args Form arguments
         */
        // do_action( 'fp_forms_before_create_form', $title, $args );
        
        /**
         * Fires after a form is created
         * 
         * @param int $form_id Form ID
         * @param array $form_data Form data
         */
        // do_action( 'fp_forms_after_create_form', $form_id, $form_data );
        
        /**
         * Fires before a form is updated
         * 
         * @param int $form_id Form ID
         * @param array $data Update data
         */
        // do_action( 'fp_forms_before_update_form', $form_id, $data );
        
        /**
         * Fires after a form is updated
         * 
         * @param int $form_id Form ID
         * @param array $data Update data
         */
        // do_action( 'fp_forms_after_update_form', $form_id, $data );
        
        /**
         * Fires before a form is deleted
         * 
         * @param int $form_id Form ID
         */
        // do_action( 'fp_forms_before_delete_form', $form_id );
        
        /**
         * Fires after a form is deleted
         * 
         * @param int $form_id Form ID
         */
        // do_action( 'fp_forms_after_delete_form', $form_id );
    }
    
    /**
     * Registra submission hooks
     */
    private static function register_submission_hooks() {
        /**
         * Fires before submission validation
         * 
         * @param int $form_id Form ID
         * @param array $data Submission data
         */
        // do_action( 'fp_forms_before_validate_submission', $form_id, $data );
        
        /**
         * Fires after submission validation
         * 
         * @param int $form_id Form ID
         * @param array $data Submission data
         * @param array $validation Validation result
         */
        // do_action( 'fp_forms_after_validate_submission', $form_id, $data, $validation );
        
        /**
         * Fires before a submission is saved
         * 
         * @param int $form_id Form ID
         * @param array $data Submission data
         */
        // do_action( 'fp_forms_before_save_submission', $form_id, $data );
        
        /**
         * Fires after a submission is saved
         * 
         * @param int $submission_id Submission ID
         * @param int $form_id Form ID
         * @param array $data Submission data
         */
        // do_action( 'fp_forms_after_save_submission', $submission_id, $form_id, $data );
        
        /**
         * Fires before a submission is deleted
         * 
         * @param int $submission_id Submission ID
         */
        // do_action( 'fp_forms_before_delete_submission', $submission_id );
        
        /**
         * Fires after a submission is deleted
         * 
         * @param int $submission_id Submission ID
         */
        // do_action( 'fp_forms_after_delete_submission', $submission_id );
    }
    
    /**
     * Registra email hooks
     */
    private static function register_email_hooks() {
        /**
         * Fires before notification email is sent
         * 
         * @param int $form_id Form ID
         * @param array $data Submission data
         * @param string $to Email recipient
         */
        // do_action( 'fp_forms_before_send_notification', $form_id, $data, $to );
        
        /**
         * Fires after notification email is sent
         * 
         * @param int $form_id Form ID
         * @param array $data Submission data
         * @param bool $success Whether email was sent successfully
         */
        // do_action( 'fp_forms_after_send_notification', $form_id, $data, $success );
        
        /**
         * Fires before confirmation email is sent
         * 
         * @param int $form_id Form ID
         * @param array $data Submission data
         * @param string $to Email recipient
         */
        // do_action( 'fp_forms_before_send_confirmation', $form_id, $data, $to );
        
        /**
         * Fires after confirmation email is sent
         * 
         * @param int $form_id Form ID
         * @param array $data Submission data
         * @param bool $success Whether email was sent successfully
         */
        // do_action( 'fp_forms_after_send_confirmation', $form_id, $data, $success );
    }
    
    /**
     * Registra admin hooks
     */
    private static function register_admin_hooks() {
        /**
         * Fires before admin assets are enqueued
         * 
         * @param string $hook Current admin page hook
         */
        // do_action( 'fp_forms_before_admin_enqueue_scripts', $hook );
        
        /**
         * Fires after admin assets are enqueued
         * 
         * @param string $hook Current admin page hook
         */
        // do_action( 'fp_forms_after_admin_enqueue_scripts', $hook );
    }
    
    /**
     * Filters disponibili
     */
    
    /**
     * Filter form data before save
     * 
     * @param array $data Form data
     * @param int $form_id Form ID
     * @return array Modified form data
     */
    public static function filter_form_data( $data, $form_id = 0 ) {
        return apply_filters( 'fp_forms_form_data', $data, $form_id );
    }
    
    /**
     * Filter submission data before save
     * 
     * @param array $data Submission data
     * @param int $form_id Form ID
     * @return array Modified submission data
     */
    public static function filter_submission_data( $data, $form_id ) {
        return apply_filters( 'fp_forms_submission_data', $data, $form_id );
    }
    
    /**
     * Filter validation errors
     * 
     * @param array $errors Validation errors
     * @param int $form_id Form ID
     * @param array $data Submission data
     * @return array Modified errors
     */
    public static function filter_validation_errors( $errors, $form_id, $data ) {
        return apply_filters( 'fp_forms_validation_errors', $errors, $form_id, $data );
    }
    
    /**
     * Filter email notification recipients
     * 
     * @param string|array $to Email recipients
     * @param int $form_id Form ID
     * @param array $data Submission data
     * @return string|array Modified recipients
     */
    public static function filter_notification_recipients( $to, $form_id, $data ) {
        return apply_filters( 'fp_forms_notification_recipients', $to, $form_id, $data );
    }
    
    /**
     * Filter email subject
     * 
     * @param string $subject Email subject
     * @param int $form_id Form ID
     * @param array $data Submission data
     * @return string Modified subject
     */
    public static function filter_email_subject( $subject, $form_id, $data ) {
        return apply_filters( 'fp_forms_email_subject', $subject, $form_id, $data );
    }
    
    /**
     * Filter email message
     * 
     * @param string $message Email message
     * @param int $form_id Form ID
     * @param array $data Submission data
     * @return string Modified message
     */
    public static function filter_email_message( $message, $form_id, $data ) {
        return apply_filters( 'fp_forms_email_message', $message, $form_id, $data );
    }
    
    /**
     * Filter email headers
     * 
     * @param array $headers Email headers
     * @param int $form_id Form ID
     * @param array $data Submission data
     * @return array Modified headers
     */
    public static function filter_email_headers( $headers, $form_id, $data ) {
        return apply_filters( 'fp_forms_email_headers', $headers, $form_id, $data );
    }
    
    /**
     * Filter field HTML
     * 
     * @param string $html Field HTML
     * @param array $field Field data
     * @param int $form_id Form ID
     * @return string Modified HTML
     */
    public static function filter_field_html( $html, $field, $form_id ) {
        return apply_filters( 'fp_forms_field_html', $html, $field, $form_id );
    }
    
    /**
     * Filter form HTML
     * 
     * @param string $html Form HTML
     * @param int $form_id Form ID
     * @param array $form Form data
     * @return string Modified HTML
     */
    public static function filter_form_html( $html, $form_id, $form ) {
        return apply_filters( 'fp_forms_form_html', $html, $form_id, $form );
    }
    
    /**
     * Filter success message
     * 
     * @param string $message Success message
     * @param int $form_id Form ID
     * @param array $data Submission data
     * @return string Modified message
     */
    public static function filter_success_message( $message, $form_id, $data ) {
        return apply_filters( 'fp_forms_success_message', $message, $form_id, $data );
    }
}

