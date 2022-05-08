<?php

namespace MailerLiteForms\Admin;

class Menu
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
     * Register plugin menu
     *
     * @access      public
     * @since       1.5.0
     */
    public static function generateLinks()
    {

        add_menu_page(
            'MailerLite', 'MailerLite', 'manage_options', 'mailerlite_main',
            null, MAILERLITE_PLUGIN_URL . '/assets/image/icon.png'
        );

        add_submenu_page(
            'mailerlite_main',
            __( 'Forms', 'mailerlite' ),
            __( 'Signup forms', 'mailerlite' ),
            'manage_options',
            'mailerlite_main',
            [ '\MailerLiteForms\Controllers\AdminController', 'forms' ]
        );
        add_submenu_page(
            'mailerlite_main',
            __( 'Settings', 'mailerlite' ),
            __( 'Settings', 'mailerlite' ),
            'manage_options',
            'mailerlite_settings',
            [ '\MailerLiteForms\Controllers\AdminController', 'settings' ]
        );
        add_submenu_page(
            'mailerlite_main',
            __( 'Status', 'mailerlite' ),
            __( 'Status', 'mailerlite' ),
            'manage_options',
            'mailerlite_status',
            [ '\MailerLiteForms\Controllers\AdminController', 'status' ]
        );
    }
}