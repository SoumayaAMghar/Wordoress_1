<?php

namespace MailerLiteForms\Modules;

use MailerLiteForms\Api\ApiType;
use MailerLiteForms\Views\Common\TinyMCE;

class Shortcode
{

    /**
     * WordPress' init() hook
     */
    public static function init() {

        add_shortcode(
            'mailerlite_form', [
                '\MailerLiteForms\Modules\Shortcode',
                'mailerlite_generate_shortcode',
            ]
        );

        add_action(
            'wp_ajax_mailerlite_tinymce_window',
            [ '\MailerLiteForms\Modules\Shortcode', 'mailerlite_tinymce_window' ]
        );

        add_action(
            'wp_ajax_mailerlite_redirect_to_form_edit',
            [ '\MailerLiteForms\Modules\Shortcode', 'redirect_to_form_edit' ]
        );

        if ( get_user_option( 'rich_editing' ) ) {
            add_filter(
                'mce_buttons', [
                    '\MailerLiteForms\Modules\Shortcode',
                    'mailerlite_register_button',
                ]
            );
            add_filter(
                'mce_external_plugins', [
                    '\MailerLiteForms\Modules\Shortcode',
                    'mailerlite_add_tinymce_plugin',
                ]
            );
        }

    }

    /**
     * Add tinymce button to toolbar
     *
     * @param $buttons
     *
     * @return mixed
     */
    public static function mailerlite_register_button( $buttons ) {
        array_push( $buttons, "mailerlite_shortcode" );

        return $buttons;
    }

    /**
     * Register tinymce plugin
     *
     * @param $plugin_array
     *
     * @return mixed
     */
    public static function mailerlite_add_tinymce_plugin( $plugin_array ) {
        $plugin_array['mailerlite_shortcode'] = MAILERLITE_PLUGIN_URL . '/assets/js/mailerlite_shortcode.js';

        return $plugin_array;
    }

    /**
     * Returns selection of forms
     */
    public static function mailerlite_tinymce_window() {
        global $wpdb, $forms;

        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        $query = "
			SELECT *
			FROM {$wpdb->base_prefix}mailerlite_forms
		";
        $forms = $wpdb->get_results($query);

        new TinyMCE($forms);

        exit;
    }

    /**
     *
     * Converts shortcode into html
     *
     * @param $attributes
     *
     * @return string
     */
    public static function mailerlite_generate_shortcode( $attributes ) {
        $form_attributes = shortcode_atts( [
            'form_id' => '1',
        ], $attributes );

        ob_start();

        ( new Form() )->load_mailerlite_form( $form_attributes['form_id'] );

        return ob_get_clean();
    }

    /**
     *
     * Redirect to the Mailerlite app form editor page
     *
     * @param $attributes
     *
     * @return string
     */
    public function redirect_to_form_edit() {
        global $wpdb;

        check_admin_referer( 'mailerlite_redirect', 'ml_nonce' );

        $form_id = intval( $_GET['form_id'] );
        $apiType = intval( $_GET['platform'] );

        $query = $wpdb->prepare(
            "SELECT * FROM
			{$wpdb->base_prefix}mailerlite_forms
			WHERE id = %d
			ORDER BY time DESC",
            $form_id
        );

        $form = $wpdb->get_row($query);

        if ( $form != null ) {
            if ( $form->type == Form::TYPE_CUSTOM ) {

                wp_redirect( admin_url( 'admin.php?page=mailerlite_main&view=edit&id=' . $form->id ) );
                exit;
            } elseif ( $form->type == Form::TYPE_EMBEDDED ) {

                $form_data = unserialize( $form->data );

                switch ( $apiType) {
                    case ApiType::CURRENT:
                        wp_redirect( 'https://app.mailerlite.com/webforms/new/content/' . ( $form_data['id'] ) );
                        break;
                    case ApiType::REWRITE:
                        wp_redirect( 'https://dashboard.mailerlite.com/forms/' . ( $form_data['id'] ) . '/content' );
                        break;
                    default:
                        break;
                }

                exit;
            }
        } else {

            echo 'Form not found.';
            exit;
        }
    }
}