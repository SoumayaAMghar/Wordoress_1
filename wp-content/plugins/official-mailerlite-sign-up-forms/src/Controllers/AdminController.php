<?php
namespace MailerLiteForms\Controllers;

use MailerLiteForms\Admin\Actions;
use MailerLiteForms\Admin\Hooks;
use MailerLiteForms\Admin\Status;
use MailerLiteForms\Admin\Views\CreateView;
use MailerLiteForms\Admin\Views\EditCustomView;
use MailerLiteForms\Admin\Views\EditEmbeddedView;
use MailerLiteForms\Admin\Views\GroupsView;
use MailerLiteForms\Admin\Views\MainView;
use MailerLiteForms\Admin\Views\SettingsView;
use MailerLiteForms\Admin\Views\StatusView;
use MailerLiteForms\Api\ApiType;
use MailerLiteForms\Api\PlatformAPI;
use MailerLiteForms\Helper;
use MailerLiteForms\Modules\Form;

class AdminController
{

    const FIRST_GROUP_LOAD = 100;

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
     * Create, edit, list pages method
     */
    public static function forms() {

        global $wpdb;

        $api_key = self::apiKey();
        $result  = '';

        // show settings if API key is not set
        if ( $api_key == "" ) {

            wp_redirect( admin_url( '/admin.php?page=mailerlite_settings' ) );
            exit;
        }

        // Create new signup form view
        if ( isset( $_GET['view'] ) && $_GET['view'] == 'create' ) {

            if ( isset( $_POST['create_signup_form'] ) ) {

                ( new Form() )->create_new_form( $_POST );

                wp_redirect(
                    'admin.php?page=mailerlite_main&view=edit&id='
                    . $wpdb->insert_id
                );
            } else {
                if ( isset( $_GET['noheader'] ) ) {

                    require_once( ABSPATH . 'wp-admin/admin-header.php' );
                }
            }

            $API = new PlatformAPI($api_key);

            $webforms = $API->getEmbeddedForms([
                'limit' => 1000,
                'type' => 'embedded'
            ]);

            if ( ! empty( $webforms->error ) && ! empty( $webforms->error->message ) ) {

                $msg = '<u>' . __( 'Error happened', 'mailerlite' ) . '</u>: ' . $webforms->error->message;
                add_action( 'admin_notices', function() use ($msg) {

                    $class   = 'notice notice-error';
                    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $msg );
                });
            }


            new CreateView($webforms);

        } // Edit signup form view
        elseif ( isset( $_GET['view'] ) && isset( $_GET['id'] )
                 && $_GET['view'] == 'edit'
                 && absint( $_GET['id'] )
        ) {
            $_POST = array_map( 'stripslashes_deep', $_POST );

            $form_id = absint( $_GET['id'] );

            $query = $wpdb->prepare(
                "SELECT *
				FROM {$wpdb->base_prefix}mailerlite_forms
				WHERE id=%d",
                $form_id
            );
            $form = $wpdb->get_row($query);

            if ( isset( $form->data ) ) {
                $form->data = unserialize( $form->data );

                if ( $form->type == Form::TYPE_CUSTOM ) {
                    add_filter(
                        'wp_default_editor',
                        function() {
                            return 'tinymce';
                        }
                    );

                    $API = new PlatformAPI( AdminController::apiKey() );

                    $groups_from_ml = $API->getGroups([
                        'limit' => AdminController::FIRST_GROUP_LOAD
                    ]);

                    $lists = $form->data['lists'];
                    if (! isset($form->data['selected_groups'])) {
                        $groups_selected = array_filter($groups_from_ml, function($group) use ($lists) {
                            return in_array($group->id, $lists);
                        });

                        if (count($groups_selected) != count($lists)) {

                            $groups_from_ml_extended = $API->getGroups([
                                'limit' => AdminController::FIRST_GROUP_LOAD
                            ]);

                            $groups_selected = array_filter($groups_from_ml_extended, function($group) use ($lists) {
                                return in_array($group->id, $lists);
                            });
                        }
                    } else {
                        $groups_selected = $form->data['selected_groups'];
                    }

                    $groups_not_selected = array_filter($groups_from_ml, function($group) use ($lists) {
                        return ! in_array($group->id, $lists);
                    });
                    $groups = array_merge($groups_selected, $groups_not_selected);

                    $can_load_more_groups = $API->checkMoreGroups(AdminController::FIRST_GROUP_LOAD);

                    $fields    = $API->getFields();

                    if ( isset( $_POST['save_custom_signup_form'] ) ) {
                        $form_name        = Helper::issetWithDefault( 'form_name',
                            __( 'Subscribe for newsletter!', 'mailerlite' ) );
                        $form_title       = Helper::issetWithDefault( 'form_title',
                            __( 'Newsletter signup', 'mailerlite' ) );
                        $form_description = Helper::issetWithDefault( 'form_description',
                            __( 'Just simple MailerLite form!', 'mailerlite' ), false );
                        $success_message  = Helper::issetWithDefault( 'success_message',
                            '<span style="color: rgb(51, 153, 102);">' . __( 'Thank you for sign up!',
                                'mailerlite' ) . '</span>', false );
                        $button_name      = Helper::issetWithDefault( 'button_name', __( 'Subscribe', 'mailerlite' ) );
                        $please_wait      = Helper::issetWithDefault( 'please_wait' );
                        $language         = Helper::issetWithDefault( 'language' );

                        $selected_fields = isset( $_POST['form_selected_field'] )
                                           && is_array(
                                               $_POST['form_selected_field']
                                           ) ? $_POST['form_selected_field'] : [];
                        $field_titles    = isset( $_POST['form_field'] )
                                           && is_array(
                                               $_POST['form_field']
                                           ) ? $_POST['form_field'] : [];

                        if ( ! isset( $field_titles['email'] ) || $field_titles['email'] == '' ) {
                            $field_titles['email'] = __( 'Email', 'mailerlite' );
                        }

                        $form_lists = isset( $_POST['form_lists'] ) && is_array( $_POST['form_lists'] ) ? $_POST['form_lists'] : [];

                        $form_selected_groups =[];
                        $selected_groups = explode(';*',$_POST['selected_groups']);

                        foreach ($selected_groups as $group) {
                            $group = explode('::', $group);
                            $group_data = [];
                            $group_data['id'] = $group[0];
                            $group_data['name'] = $group[1];
                            $form_selected_groups[] = (object)$group_data;
                        }

                        $groups_not_included = array_filter($form_selected_groups, function($group) use ($groups) {
                            return ! in_array($group->id, $groups);
                        });
                        $groups = array_merge($groups_not_included, $groups_not_selected);

                        $prepared_fields = [];

                        // Force to use email
                        $prepared_fields['email'] = $field_titles['email'];

                        foreach ( $selected_fields as $field ) {
                            if ( isset( $field_titles[ $field ] ) ) {
                                $prepared_fields[ $field ] = $field_titles[ $field ];
                            }
                        }

                        $form_data = [
                            'title'           => $form_title,
                            'description'     => wpautop( $form_description, true ),
                            'success_message' => wpautop( $success_message, true ),
                            'button'          => $button_name,
                            'please_wait'     => $please_wait,
                            'language'        => $language,
                            'lists'           => $form_lists,
                            'fields'          => $prepared_fields,
                            'selected_groups' => $form_selected_groups
                        ];

                        $wpdb->update(
                            $wpdb->base_prefix . 'mailerlite_forms',
                            [
                                'name' => $form_name,
                                'data' => serialize( $form_data ),
                            ],
                            [ 'id' => $form_id ],
                            [],
                            [ '%d' ]
                        );

                        $form->data = $form_data;
                        $form->name = $form_name;

                        $result = 'success';
                    }

                    new EditCustomView($result, $form, $fields, $groups, $can_load_more_groups);

                } elseif ( $form->type == Form::TYPE_EMBEDDED ) {

                    $API = new PlatformAPI( $api_key );

                    $webforms = $API->getEmbeddedForms([
                        'limit' => 1000,
                        'type' => 'embedded'
                    ]);

                    if ( ! empty( $webforms->error ) && ! empty( $webforms->error->message ) ) {

                        $msg = '<u>' . __( 'Error happened', 'mailerlite' ) . '</u>: ' . $webforms->error->message;
                        add_action( 'admin_notices', function() use ($msg) {

                            $class   = 'notice notice-error';
                            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $msg );
                        });
                    }

                    $parsed_webforms = [];

                    foreach ( $webforms as $webform ) {
                        $parsed_webforms[ $webform->id ] = $webform->code;
                    }

                    if ( isset( $_POST['save_embedded_signup_form'] ) ) {
                        $form_name = Helper::issetWithDefault( 'form_name', __( 'Embedded webform', 'mailerlite' ) );

                        $form_webform_id = isset( $_POST['form_webform_id'] )
                                           && isset( $parsed_webforms[ $_POST['form_webform_id'] ] )
                            ? $_POST['form_webform_id'] : 0;

                        $form_data = [
                            'id'   => $form_webform_id,
                            'code' => $parsed_webforms[ $form_webform_id ],
                        ];

                        $wpdb->update(
                            $wpdb->base_prefix . 'mailerlite_forms',
                            [
                                'name' => $form_name,
                                'data' => serialize( $form_data ),
                            ],
                            [ 'id' => $form_id ],
                            [],
                            [ '%d' ]
                        );

                        $form->data = $form_data;
                        $form->name = $form_name;

                        $result = 'success';
                    }

                    new EditEmbeddedView($result, $form, $webforms, $API->getApiType());
                }
            } else {
                $query = "
					SELECT * FROM
					{$wpdb->base_prefix}mailerlite_forms
					ORDER BY time DESC
				";
                $forms_data = $wpdb->get_results($query);

                new MainView( $forms_data );
            }
        } // Delete signup form view
        elseif ( isset( $_GET['view'] ) && isset( $_GET['id'] )
                 && $_GET['view'] == 'delete'
                 && absint( $_GET['id'] ) ) {
            $wpdb->delete(
                $wpdb->base_prefix . 'mailerlite_forms', [ 'id' => $_GET['id'] ]
            );
            wp_redirect( 'admin.php?page=mailerlite_main' );
        } // Signup forms list
        else {
            $query = "
				SELECT * FROM
				{$wpdb->base_prefix}mailerlite_forms
				ORDER BY time DESC
			";
            $forms_data = $wpdb->get_results($query);

            new MainView( $forms_data );
        }
    }

    /**
     * Show Settings view
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public static function settings()
    {

        $api_key = self::apiKey();

        $ML_Settings_Double_OptIn   = new PlatformAPI( $api_key );

        if( $ML_Settings_Double_OptIn->getApiType() !== ApiType::INVALID ) {

            $double_optin_enabled = $ML_Settings_Double_OptIn->getDoubleOptin();
            $double_optin_enabled_local = ! get_option('mailerlite_double_optin_disabled');

            // Make sure they option is up-to-date
            if ($double_optin_enabled != $double_optin_enabled_local) {

                update_option('mailerlite_double_optin_disabled', ! $double_optin_enabled);
            }
        }

        new SettingsView( $api_key );
    }

    /**
     * Show Status page
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public static function status()
    {

        $information = ( new Status() )->getInformation();

        new StatusView($information);
    }

    /**
     * Register Actions and Hooks
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public static function init()
    {

        if ( is_admin() ) {

            new Actions();

            new Hooks();
        }
    }

    /**
     * Show more groups
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public static function getMoreGroups()
    {

        global $wpdb;

        check_admin_referer( 'mailerlite_load_more_groups', 'ml_nonce' );

        $form_id = intval( $_POST['form_id'] );
        $offset  = intval( $_POST['offset'] );

        $query = $wpdb->prepare(
            "SELECT *
        FROM {$wpdb->base_prefix}mailerlite_forms
        WHERE id=%d",
            $form_id
        );

        $form = $wpdb->get_row($query);

        $form->data = unserialize( $form->data );

        $ML_Groups = new PlatformAPI( self::apiKey() );

        $lists = $form->data['lists'];

        $groups_from_ml_extended = $ML_Groups->getMoreGroups(self::FIRST_GROUP_LOAD, $offset);

        $groups = array_filter($groups_from_ml_extended, function($group) use ($lists) {
            return ! in_array($group->id, $lists);
        });

        $can_load_more_groups = $ML_Groups->checkMoreGroups( self::FIRST_GROUP_LOAD, $offset + 1);

        new GroupsView( $groups, $form, $can_load_more_groups);

        exit;
    }

    /**
     * Get API key
     *
     * @access      public
     * @return      string
     * @since       1.5.0
     */
    public static function apiKey()
    {

        return get_option( 'mailerlite_api_key' );;
    }
}