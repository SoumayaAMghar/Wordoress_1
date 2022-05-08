<?php

namespace MailerLiteForms\Admin;

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

        $this->adminHooks();
        $this->adminScripts();
    }

    /**
     * Add admin hooks
     */
    private function adminHooks()
    {

        add_action(
            'admin_menu', [
                '\MailerLiteForms\Admin\Menu',
                'generateLinks',
            ]
        );

        add_action(
            'wp_ajax_mailerlite_get_more_groups', [
                '\MailerLiteForms\Controllers\AdminController',
                'getMoreGroups'
            ]
        );
    }

    /**
     * Add admin hooks to load
     */
    private function adminScripts()
    {

        add_action(
            'admin_enqueue_scripts', [
                '\MailerLiteForms\Admin\Hooks',
                'load_mailerlite_admin_css'
            ]
        );
    }

    /**
     * Register plugin styling
     */
    public static function load_mailerlite_admin_css( $hook ) {

        $allowed_hooks = [
            'toplevel_page_mailerlite_main',
            'mailerlite_page_mailerlite_settings',
            'mailerlite_page_mailerlite_status',
        ];

        if ( ! in_array( $hook, $allowed_hooks ) ) {
            return;
        }

        wp_register_style(
            'mailerlite.css',
            MAILERLITE_PLUGIN_URL . '/assets/css/mailerlite.css', [],
            MAILERLITE_VERSION
        );
        wp_enqueue_style( 'mailerlite.css' );
    }
}