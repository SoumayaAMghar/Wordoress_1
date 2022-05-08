<?php

namespace MailerLiteForms\Admin;

use MailerLiteForms\Api\PlatformAPI;
use MailerLiteForms\Controllers\AdminController;

class Settings
{

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct()
    {

    }

    /**
     * Checks and sets API key
     */
    public static function setAPIKey()
    {

        if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {

            add_action( 'admin_notices', [
                '\MailerLiteForms\Admin\AdminNotice',
                'notAllowedNotice'
            ] );

            return;
        }

        $key = $_POST['mailerlite_key'];

        if ( $key == '' ) {

            // Allow to the remove the key
            update_option( 'mailerlite_api_key', $key );
            update_option( 'mailerlite_enabled', false );
            update_option( 'account_id', '' );
            update_option( 'account_subdomain', '' );
            update_option( 'mailerlite_popups_disabled', false );
        } else {

            $ML_Lists = new PlatformAPI( $key );

            $response = $ML_Lists->validateKey();

            if ( $ML_Lists->responseCode() == 401 ) {

                add_action( 'admin_notices', [
                    '\MailerLiteForms\Admin\AdminNotice',
                    'error_invalid_api_key'
                ] );

            }elseif ($response === false) {

                $msg = $ML_Lists->getResponseBody();

                add_action( 'admin_notices', function() use ($msg) {

                    $class   = 'notice notice-error';
                    $message = '<u>' . __( 'Send this error to info@mailerlite.com or our chat',
                            'mailerlite' ) . '</u>: ' . $msg;

                    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
                });
            }else{

                if (intval( get_option( 'mailerlite_platform', 1) ) !== $ML_Lists->getApiType())
                    self::clearForms();

                update_option( 'mailerlite_api_key', $key );
                update_option( 'mailerlite_enabled', true );
                update_option( 'mailerlite_platform', $ML_Lists->getApiType() );

                // save account information
                update_option( 'account_id', $response->id );
                update_option( 'account_subdomain', $response->subdomain );
                update_option( 'mailerlite_popups_disabled', false );
            }
        }
    }

    /**
     * Checks and sets popup tracker setting
     */
    public static function setPopups()
    {

        if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {

            add_action( 'admin_notices', [
                '\MailerLiteForms\Admin\AdminNotice',
                'notAllowedNotice'
            ] );

            return;
        }

        update_option( 'mailerlite_popups_disabled', ! get_option( 'mailerlite_popups_disabled' ) );
    }

    /**
     * Checks and sets the double opt-in
     */
    public static function toggleDoubleOptIn()
    {

        if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {

            add_action( 'admin_notices', [
                '\MailerLiteForms\Admin\AdminNotice',
                'notAllowedNotice'
            ] );

            return;
        }

        $ML_Settings_Double_OptIn = new PlatformAPI( AdminController::apiKey() );
        $ML_Settings_Double_OptIn->setDoubleOptin( get_option( 'mailerlite_double_optin_disabled' ) );

        update_option( 'mailerlite_double_optin_disabled', ! get_option( 'mailerlite_double_optin_disabled' ) );
    }

    /**
     * Clear forms on platform change
     */
    private static function clearForms()
    {

        global $wpdb;

        if ( is_admin() ) {

            $wpdb->query('TRUNCATE TABLE '.$wpdb->base_prefix.'mailerlite_forms');
        }
    }
}