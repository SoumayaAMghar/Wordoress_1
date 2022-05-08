<?php
namespace MailerLiteForms\Admin;

use MailerLiteForms\Api\ApiType;
use MailerLiteForms\Modules\Form;

class Status
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
     * Get plugin installation info
     *
     * @access      public
     * @return      array
     * @since       1.5.0
     */
    public static function getInformation()
    {

        global $wpdb;

        $theme        = wp_get_theme();
        $curl_version = '';
        if ( function_exists( 'curl_version' ) )
        {
            $curl_info    = curl_version();
            $curl_version = $curl_info['version'] . ', ' . $curl_info['ssl_version'];
        }

        // Only if loading the plugin succeeded
        $query = "
        SELECT *
        FROM {$wpdb->base_prefix}mailerlite_forms
        ";
        $forms = $wpdb->get_results($query);
        $number_of_custom_forms   = 0;
        $number_of_embedded_forms = 0;

        foreach ( $forms as $form ) {
            if ($form->type == Form::TYPE_CUSTOM ) {
                $number_of_custom_forms ++;
            } elseif ($form->type == Form::TYPE_EMBEDDED ) {
                $number_of_embedded_forms ++;
            }
        }

        $environment_group = __( 'Environment', 'mailerlite' );
        $plugin_group      = __( 'Plugin', 'mailerlite' );

        $fields                                               = [];
        $fields['WordPress']['Version']                       = get_bloginfo( 'version' );
        $fields['WordPress']['Home URL']                      = get_option( 'home' );
        $fields['WordPress']['Site URL']                      = get_option( 'home' );
        $fields['WordPress']['Multisite']                     = is_multisite() ? 'Yes' : 'No';
        $fields['WordPress']['Debug mode']                    = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'Yes' : 'No';
        $fields['WordPress']['Theme name']                    = $theme->get( 'Name' );
        $fields['WordPress']['Theme URI']                     = $theme->get( 'ThemeURI' );
        $fields['WordPress']['Active plugins']                = implode( ', ', get_option( 'active_plugins' ) );
        $fields[ $environment_group ]['Required PHP version'] = MAILERLITE_PHP_VERSION;
        $fields[ $environment_group ]['PHP version']          = phpversion();
        $fields[ $environment_group ]['Server information']   = isset( $_SERVER['SERVER_SOFTWARE'] ) ? wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) : '';
        $fields[ $environment_group ]['cURL version']         = $curl_version;
        $fields[ $plugin_group ]['Version']                   = MAILERLITE_VERSION;
        $fields[ $plugin_group ]['API key provided']          = (bool) get_option( 'mailerlite_api_key' ) ? 'Yes' : 'No';

        switch ( intval(get_option('mailerlite_platform', 0)) ) {
            case ApiType::CURRENT: $fields[$plugin_group]['API type'] = "MailerLite v2"; break;
            case ApiType::REWRITE: $fields[$plugin_group]['API type'] = "MailerLite API (Rewrite)"; break;
            case ApiType::INVALID:
            default              : $fields[$plugin_group]['API type'] = "Not set"; break;
        }

        $fields[ $plugin_group ]['Popups enabled']            = ! get_option( 'mailerlite_popups_disabled' ) ? 'Yes' : 'No';
        $fields[ $plugin_group ]['Double opt-in enabled']     = ! get_option( 'mailerlite_double_optin_disabled' ) ? 'Yes' : 'No';

        if ( class_exists( 'MailerLite_Form' ) ) {
            $fields[ $plugin_group ]['Custom forms']   = $number_of_custom_forms;
            $fields[ $plugin_group ]['Embedded forms'] = $number_of_embedded_forms;
        }

        return $fields;
    }
}