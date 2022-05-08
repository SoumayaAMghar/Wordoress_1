<?php
namespace MailerLiteForms\Admin;

class Actions
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

        if ( isset( $_POST['action'] )
             && $_POST['action'] == 'enter-mailerlite-key'
             &&  (isset($_POST['mailerlite_key']))
        ) {

            Settings::setAPIKey();
        }

        if ( isset( $_POST['action'] )
             && $_POST['action'] == 'enter-popup-forms'
        ) {

            Settings::setPopups();
        }

        if ( isset( $_POST['action'] )
             && $_POST['action'] == 'toggle-double-opt-in'
        ) {

            Settings::toggleDoubleOptIn();
        }
    }
}