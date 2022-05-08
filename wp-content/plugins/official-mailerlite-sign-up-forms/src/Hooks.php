<?php
namespace MailerLiteForms;

use MailerLiteForms\Api\ApiType;

class Hooks
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

        $this->initHooks();
    }

    public static function addStyle()
    {

        wp_register_style(
            'mailerlite_forms.css',
            MAILERLITE_PLUGIN_URL . '/assets/css/mailerlite_forms.css', [],
            MAILERLITE_VERSION
        );
        wp_enqueue_style( 'mailerlite_forms.css' );
    }

    public static function loadTextDomain()
    {

        load_plugin_textdomain(
            MAILERLITE_TEXT_DOMAIN, false, basename(MAILERLITE_PLUGIN_DIR ) . '/languages/'
        );
    }

    private function initHooks()
    {

        register_activation_hook(
            MAILERLITE_PLUGIN_BASENAME, [
                '\MailerLiteForms\Core',
                'install'
            ]
        );

        if ( is_admin() ) {

            add_action(
                'init', [
                    '\MailerLiteForms\Controllers\AdminController',
                    'init'
                ]
            );
        }

        $mailerlite_api_key         = get_option( 'mailerlite_api_key' );
        $account_id                 = get_option( 'account_id' );
        $account_subdomain          = get_option( 'account_subdomain' );
        $mailerlite_popups_disabled = get_option( 'mailerlite_popups_disabled' );
        $platform                   = intval(get_option( 'mailerlite_platform', 1));

        if ( ! $mailerlite_popups_disabled && $mailerlite_api_key && $account_id && $account_subdomain && $platform == ApiType::CURRENT ) {

            add_action( 'wp_head', [
                '\MailerLiteForms\Helper',
                'mailerlite_universal'
            ] );
        }

        if ( $mailerlite_api_key && $account_id && $platform == ApiType::REWRITE ) {

            add_action( 'wp_head', [
                '\MailerLiteForms\Helper',
                'mailerlite_universal_rw'
            ] );
        }

        add_action(
            'init', [
                '\MailerLiteForms\Hooks',
                'loadTextDomain'
            ]
        );

        add_action(
            'wp_enqueue_scripts', [
                '\MailerLiteForms\Hooks',
                'addStyle'
            ]
        );

        add_action(
            'init', [
                '\MailerLiteForms\Modules\Form', 'init'
            ]
        );

        add_action(
            'init', [
                '\MailerLiteForms\Modules\Shortcode', 'init'
            ]
        );

        add_action(
            'init', [
                '\MailerLiteForms\Modules\Gutenberg', 'init'
            ]
        );

        add_action(
            'widgets_init', [
                '\MailerLiteForms\Modules\Widget', 'register_mailerlite_widget'
            ]
        );
    }
}