<?php

namespace MailerLiteForms\Admin;

class AdminNotice
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
     * Error view for invalid api key
     *
     * @access      public
     * @since       1.5.0
     */
    public static function error_invalid_api_key()
    {

        $class   = 'notice notice-error mailersend-warning-declare-found-notice mailersend-notice is-dismissible';
        $message =  __( 'Wrong MailerLite API key', 'mailerlite' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
    }

    /**
     * Error view for old PHP version
     *
     * @access      public
     * @since       1.5.0
     */
    public static function oldPHPNotice()
    {

        $class   = 'notice notice-error';
        $message = 'The <strong>MailerLite</strong> plugin requires PHP version ' . MAILERLITE_PHP_VERSION . ' or greater. You are currently using PHP version <strong>' . PHP_VERSION . '</strong>';

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
    }

    /**
     * Error view for old WP version
     *
     * @access      public
     * @since       1.5.0
     */
    public static function oldWPNotice()
    {

        $class   = 'notice notice-error';
        $message = 'The <strong>MailerLite</strong> plugin requires WordPress version ' . MAILERLITE_WP_VERSION . ' or greater.';

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
    }

    /**
     * Error view for permission denied
     *
     * @access      public
     * @since       1.5.0
     */
    public static function notAllowedNotice()
    {

        $class   = 'notice notice-error';
        $message = __( 'You are not allowed to do that', 'mailerlite' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
    }
}