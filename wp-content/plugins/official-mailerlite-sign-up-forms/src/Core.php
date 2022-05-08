<?php
namespace MailerLiteForms;

use MailerLiteForms\Controllers\AdminController;

class Core
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

        if ( ! $this->hasServerRequirements() ) {

            return;
        }


        if ( is_admin() ) {
            new AdminController();
        }

        new Hooks();
    }

    /**
     * Run installation
     *
     * @access      private
     * @since       1.5.0
     */
    public static function install()
    {

        global $wpdb;

        if ( ! self::hasServerRequirements() ) {

            return;
        }

        $table_name = $wpdb->base_prefix . "mailerlite_forms";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $charset_collate = ' CHARACTER SET utf8 COLLATE utf8_bin';

        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              name tinytext NOT NULL,
              type tinyint(1) default '1' NOT NULL,
              data text NOT NULL,
              PRIMARY KEY (id)
           ) DEFAULT " . $charset_collate . ";";
        dbDelta( $sql );
    }

    private static function hasServerRequirements()
    {

        global $wp_version;

        if ( self::checkPHPRequirments() ) {

            add_action(
                'admin_notices', [
                    '\MailerLiteForms\Admin\AdminNotice',
                    'oldPHPNotice'
                ]
            );

            self::deactivatePlugin();
        }

        if ( version_compare( $wp_version, MAILERLITE_WP_VERSION, '<' ) ) {

            add_action(
                'admin_notices', [
                    '\MailerLiteForms\Admin\AdminNotice',
                    'oldWPNotice'
                ]
            );

            self::deactivatePlugin();
        }

        return true;
    }

    private static function deactivatePlugin()
    {

        deactivate_plugins( MAILERLITE_PLUGIN_BASENAME );
    }

    private static function checkPHPRequirments()
    {

        return version_compare( PHP_VERSION, MAILERLITE_PHP_VERSION, '<' );
    }
}