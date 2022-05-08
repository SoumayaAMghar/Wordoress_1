<?php
namespace MailerLiteForms\Modules;

use MailerLiteForms\Admin\Views\CreateCustomView;
use MailerLiteForms\Api\PlatformAPI;
use MailerLiteForms\Controllers\AdminController;
use MailerLiteForms\Views\CustomForm;
use MailerLiteForms\Views\EmbeddedForm;
use MailerLiteForms\Views\InvalidForm;

class Form
{

    const TYPE_CUSTOM = 1;
    const TYPE_EMBEDDED = 2;

    public $form_id;
    public $form_type;
    public $form_name;
    public $form_data;

    private $platform;

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct()
    {

        $this->platform = intval(get_option( 'mailerlite_platform', 1));
    }

    /**
     * Sets form data for class
     */
    public static function init() {
        add_action(
            'wp_enqueue_scripts',
            [ '\MailerLiteForms\Modules\Form', 'add_jquery_validation_libraries' ]
        );

        add_action(
            'wp_ajax_nopriv_mailerlite_subscribe_form',
            [ '\MailerLiteForms\Modules\Form', 'save_form_data' ]
        );
        add_action(
            'wp_ajax_mailerlite_subscribe_form',
            [ '\MailerLiteForms\Modules\Form', 'save_form_data' ]
        );

    }

    /**
     * Generates form by type
     *
     * @param $form_id
     * @param $form_type
     * @param $form_name
     * @param $form_data
     */
    public function generate_form( $form_id, $form_type, $form_name, $form_data ) {

        $this->form_id   = $form_id;
        $this->form_type = $form_type;
        $this->form_name = $form_name;
        $this->form_data = $form_data;

        if ( $this->form_type == self::TYPE_CUSTOM ) {
            $this->generate_custom_form();
        } else {
            $this->generate_embedded_form();
        }
    }

    /**
     * Saves form data
     */
    public static function save_form_data() {
        global $wpdb;

        check_admin_referer( 'mailerlite_form', 'ml_nonce' );

        $form_id     = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
        $form_fields = isset( $_POST['form_fields'] ) ? $_POST['form_fields'] : [];

        $api_key = get_option( 'mailerlite_api_key' );

        if ( $form_id > 0 && isset( $form_fields['email'] ) ) {
            $query = $wpdb->prepare(
                "SELECT * FROM
				{$wpdb->base_prefix}mailerlite_forms
				WHERE id = %d",
                $form_id
            );
            $form = $wpdb->get_row($query);

            if ( isset( $form->data ) ) {

                $form->data = unserialize( $form->data );

                $API = new PlatformAPI( $api_key );

                $form_email = $form_fields['email'];
                unset( $form_fields['email'] );

                $fields = [];

                foreach ( $form_fields as $field => $value ) {
                    $fields[ $field ] = $value;
                }

                $subscriber = [
                    'email'  => $form_email,
                    'fields' => $fields,
                ];

                $subbed = 0;

                foreach ( $form->data['lists'] as $list ) {

                    if ( $list != '' ) {

                        if ( $API->addSubscriberToGroup( $subscriber, $list, 1 ) ) {

                            $subbed++;
                        }
                    }else{

                        if ( $API->addSubscriber( $subscriber, 1)) {
                            $subbed++;
                        }
                    }

                }

                if ( count($form->data['lists']) === $subbed ) {

                    echo json_encode(
                        [
                            'status'  => 'success',
                            'message' => __(
                                'Subscriber successfully saved', 'mailerlite'
                            ),
                        ]
                    );
                }else{

                    echo json_encode(
                        [
                            'status'  => 'error',
                            'message' => __( 'There was an error while subscribing to the list.', 'mailerlite' ),
                        ]
                    );
                }
            } else {
                echo json_encode(
                    [
                        'status'  => 'error',
                        'message' => __( 'Form not found', 'mailerlite' ),
                    ]
                );
            }
        } else {
            echo json_encode(
                [
                    'status'  => 'error',
                    'message' => __( 'Wrong data provided', 'mailerlite' ),
                ]
            );
        }

        exit;
    }

    /**
     * Method to generate custom form
     */
    private function generate_custom_form() {

        global $form_id, $form_name, $form_data;

        $form_id   = $this->form_id;
        $form_name = $this->form_name;
        $form_data = $this->form_data;

        new CustomForm($form_id, $form_data);

    }

    /**
     * Method to generate embedded form
     */
    private function generate_embedded_form() {

        global $form_data;

        $form_data = $this->form_data;

        new EmbeddedForm($form_data, $this->platform);

    }

    /**
     * Register jQuery validation library
     */
    public static function add_jquery_validation_libraries() {

        if ( ! wp_script_is( 'jquery' ) && ! wp_script_is( 'google-hosted-jquery' ) ) {
            wp_register_script(
                'google-hosted-jquery',
                '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js',
                false
            );
            wp_enqueue_script( 'google-hosted-jquery' );
        }

        if ( ! wp_script_is( 'jquery-validation-plugin' )
             && wp_script_is(
                 'jquery'
             )
        ) {
            wp_register_script(
                'jquery-validation-plugin',
                MAILERLITE_PLUGIN_URL . '/assets/js/jquery.validate.min.js',
                [ 'jquery' ], false, true
            );
        } elseif ( ! wp_script_is( 'jquery-validation-plugin' )
                   && wp_script_is(
                       'google-hosted-jquery'
                   )
        ) {
            wp_register_script(
                'jquery-validation-plugin',
                MAILERLITE_PLUGIN_URL . '/assets/js/jquery.validate.min.js',
                [ 'google-hosted-jquery' ], false, true
            );
        }

        wp_enqueue_script( 'jquery-validation-plugin' );

    }

    /**
     * Loads MailerLite form
     *
     * @param $form_id
     */
    public function load_mailerlite_form( $form_id ) {

        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT * FROM
		{$wpdb->base_prefix}mailerlite_forms
		WHERE id = %d",
            $form_id
        );

        $form = $wpdb->get_row($query);

        if( $form ) {
            if (isset($form->data)) {
                $form_data = unserialize($form->data);

                //$MailerLite_form = new Form();
                $this->generate_form(
                    $form_id, $form->type, $form->name, $form_data
                );
            }
        }else{

            new InvalidForm($form_id);
        }
    }

    /**
     * Create new signup form
     *
     * @param $data
     */
    public static function create_new_form( $data ) {

        global $wpdb;

        $form_type = in_array( $data['form_type'], [ Form::TYPE_CUSTOM, Form::TYPE_EMBEDDED ] )
            ? $data['form_type'] : Form::TYPE_CUSTOM;

        if ( $form_type == Form::TYPE_CUSTOM ) {

            $form_name = __( 'New custom signup form', 'mailerlite' );
            $form_data = [
                'title'           => __( 'Newsletter signup', 'mailerlite' ),
                'description'     => __(
                    'Just simple MailerLite form!', 'mailerlite'
                ),
                'success_message' => '<span style="color: rgb(51, 153, 102);">' . __(
                        'Thank you for sign up!', 'mailerlite'
                    ) . '</span>',
                'button'          => __( 'Subscribe', 'mailerlite' ),
                'lists'           => [],
                'fields'          => [ 'email' => __( 'Email', 'mailerlite' ) ],
            ];

            if ( array_key_exists( 'create_signup_form_now', $_POST ) ) {
                $form_name          = $_POST['form_name'];
                $form_data['lists'] = $_POST['form_lists'];
                $selected_groups = explode(';*',$_POST['selected_groups']);

                foreach ($selected_groups as $group) {
                    $group = explode('::', $group);
                    $group_data = [];
                    $group_data['id'] = $group[0];
                    $group_data['name'] = $group[1];
                    $form_data['selected_groups'][] = (object)$group_data;
                }
            } else {
                $API = new PlatformAPI( AdminController::apiKey() );

                $groups = $API->getGroups([
                    'limit' => AdminController::FIRST_GROUP_LOAD
                ]);

                $can_load_more_groups = $API->checkMoreGroups(AdminController::FIRST_GROUP_LOAD);

                require_once( ABSPATH . 'wp-admin/admin-header.php' );

                new CreateCustomView($form_name, $groups, $can_load_more_groups);

                exit;
            }
        } else {
            $form_name = __( 'New embedded signup form', 'mailerlite' );
            $form_data = [
                'id'   => 0,
                'code' => 0,
            ];
        }

        $wpdb->insert( $wpdb->base_prefix . 'mailerlite_forms', [
            'name' => $form_name,
            'time' => date( 'Y-m-d h:i:s' ),
            'type' => $form_type,
            'data' => serialize( $form_data ),
        ] );
    }
}