<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Initiable_Interface;
use ThirstyAffiliates\Interfaces\Model_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

/**
 * Model that houses the logic for permalink rewrites and affiliate link redirections.
 *
 * @since 3.1.0
 */
class REST_API implements Model_Interface {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that holds the single main instance of Shortcodes.
     *
     * @since 3.1.0
     * @access private
     * @var Redirection
     */
    private static $_instance;

    /**
     * Model that houses the main plugin object.
     *
     * @since 3.1.0
     * @access private
     * @var Redirection
     */
    private $_main_plugin;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 3.1.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 3.1.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Rest field prefix.
     *
     * @since 3.1.0
     * @access private
     * @var string
     */
    private $_rest_prefix = '_ta_';

    /**
     * TA custom fields default data.
     *
     * @since 3.1.0
     * @access private
     * @var array
     */
    private $_ta_fields = array(
        'destination_url' => '',
        'rel_tags'        => '',
        'redirect_type'   => 'global',
        'no_follow'       => 'global',
        'new_window'      => 'global',
        'uncloak_link'    => 'global',
        'pass_query_str'  => 'global',
        'image_ids'       => array(),
        'categories'      => array(),
        'category_slug'   => '',
        'category_slug_id' => 0,
    );




    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Class constructor.
     *
     * @since 3.1.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     */
    public function __construct( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;

        $main_plugin->add_to_all_plugin_models( $this );
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 3.1.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Redirection
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }

    /**
     * Register ThirstyAffiliates custom fields so they can be accessible on REST.
     *
     * @since 3.1.0
     * @access public
     */
    public function register_ta_custom_fields_on_rest() {

        $fields = apply_filters( 'ta_register_rest_api_fields' , $this->_ta_fields );

        foreach ( $fields as $meta_key => $default_value )
            $this->register_rest_field( $meta_key , $default_value );


    }

    /**
     * Register single field in the REST API.
     *
     * @since 3.1.0
     * @access private
     *
     * @param string $meta_key      Custom field meta key.
     * @param mixed  $default_value Custom field default value.
     */
    private function register_rest_field( $meta_key , $default_value ) {

        $field_type = gettype( $default_value );

        register_rest_field( Plugin_Constants::AFFILIATE_LINKS_CPT , $this->_rest_prefix . $meta_key , array(

            // REST field get callback.
            'get_callback' => function( $post_data ) use ( $meta_key , $field_type , $default_value ) {

                    if ( $meta_key == 'categories' )
                        return wp_get_post_terms( $post_data[ 'id' ] , Plugin_Constants::AFFILIATE_LINKS_TAX , array( 'fields' => 'ids' ) );
                    else {

                        $meta_value = get_post_meta( $post_data[ 'id' ] , Plugin_Constants::META_DATA_PREFIX . $meta_key , true );
                        $meta_value = $this->esc_meta_value( $meta_value , $meta_key , $field_type );
                        return ( $meta_value && $field_type === gettype( $meta_value ) ) ? $meta_value : $default_value;
                    }
                },

            // REST field update callback.
            'update_callback' => function( $new_value , $post_obj ) use ( $meta_key , $field_type , $default_value ) {

                    // Filter to determine if field allows to be updated or not.
                    if ( apply_filters( 'ta_restapi_field_update_cb' , false , $meta_key , $new_value , $field_type , $default_value ) )
                        return;

                    if ( $meta_key == 'categories' ) {

                        if ( ! is_array( $new_value ) || empty( $new_value ) )
                            return;

                        $categories = array_unique( array_map( 'intval' , $new_value ) );
                        wp_set_post_terms( $post_obj->ID , $categories , Plugin_Constants::AFFILIATE_LINKS_TAX );

                    } else
                        update_post_meta( $post_obj->ID , Plugin_Constants::META_DATA_PREFIX . $meta_key , $this->sanitize_field( $new_value , $meta_key , $field_type , $default_value ) );
                },

            // REST field schema.
            'schema' => array(
                'type'        => $field_type,
                'context'     => array( 'view' , 'edit' )
            )

        ) );
    }

    /**
     * Escape meta value before displaying.
     *
     * @since 3.1.0
     * @access private
     *
     * @param mixed  $meta_value Custom field value.
     * @param string $meta_key   Custom field meta key.
     * @param string $field_type Custom field type.
     * @return mixed Escaped custom field value.
     */
    private function esc_meta_value( $meta_value , $meta_key , $field_type ) {

        // allow TA pro and other third party plugins to escape value while not running the default function.
        $escaped_value = apply_filters( 'ta_rest_api_esc_meta_value' , false , $meta_value , $meta_key , $field_type );
        if ( $escaped_value !== false ) return $escaped_value;

        switch ( $field_type ) {

            case 'array' :
                $sanitize_func = ( $meta_key == 'image_ids' ) ? 'intval' : 'esc_attr';
                $escaped_value = is_array( $meta_value ) ? array_map( $sanitize_func , $meta_value ) : $meta_value;
                break;

            case 'integer' :
                $escaped_value = intval( $meta_value );
                break;

            case 'string' :
            default :
                $escaped_value = ( $meta_key == 'destination_url' ) ? esc_url_raw( $meta_value ) : esc_attr( $meta_value );
                break;
        }

        return $escaped_value;
    }

    /**
     * Sanitize updated meta value before saving.
     *
     * @since 3.1.0
     * @access private
     *
     * @param mixed  $field_value   Custom field value.
     * @param string $meta_key   Custom field meta key.
     * @param string $field_type    Custom field type.
     * @param mixed  $default_value Custom field default value.
     * @return mixed Sanitized custom field value.
     */
    private function sanitize_field( $field_value , $meta_key , $field_type , $default_value ) {

        // allow TA pro and other third party plugins to sanitize value while not running the default function.
        $sanitized_value = apply_filters( 'ta_rest_api_sanitize_field' , false , $field_value , $meta_key , $field_type , $default_value );
        if ( $sanitized_value !== false ) return $sanitized_value;

        switch ( $field_type ) {

            case 'array' :
                $sanitize_func   = ( $meta_key == 'image_ids' ) ? 'intval' : 'sanitize_text_field';
                $sanitized_value = ( is_array( $field_value ) && ! empty( $field_value ) ) ? array_unique( array_map( $sanitize_func , $field_value ) ) : $default_value;

                // validate if provided ids are already saved attachments for image_ids field.
                if ( $meta_key == 'image_ids' ) {

                    $valid_images = array();
                    foreach ( $sanitized_value as $image_id )
                        if ( get_post_type( $image_id ) === 'attachment' ) $valid_images[] = $image_id;

                    $sanitized_value = $valid_images;
                }

                break;

            case 'integer' :
                $sanitized_value = ( $field_value ) ? intval( $field_value ) : $default_value;
                break;

            case 'string' :
            default :
                $sanitized_value = ( $field_value ) ? $field_value : $default_value;
                $sanitized_value = ( $meta_key == 'destination_url' ) ? esc_url_raw( $field_value ) : sanitize_text_field( $field_value );
                break;
        }

        return $sanitized_value;
    }

    /**
     * Sanitize special TA fields.
     *
     * @since 3.1.0
     * @since 3.4.0 Allow global value for redirect type.
     * @access public
     *
     * @param mixed  $sanitized_value Value after sanitized. Defaults to boolean false.
     * @param mixed  $field_value     Raw field value.
     * @param string $meta_key        TA field meta key.
     * @param string $field_type      TA field variable type.
     * @param mixed  $default_value    TA field default value.
     * @return mixed Filtered sanitized field value.
     */
    public function sanitize_special_fields( $sanitized_value , $field_value , $meta_key , $field_type , $default_value ) {

        $toggle_fields = apply_filters( 'ta_rest_api_sanitize_toggle_fields' , array(
            'no_follow',
            'new_window',
            'uncloak_link',
            'pass_query_str',
        ) );

        if ( in_array( $meta_key , $toggle_fields ) )
            $allowed_values = array( 'global' , 'yes' , 'no' );
        elseif( $meta_key == 'redirect_type' )
            $allowed_values = array( 'global' , '301' , '302' , '307' );

        if ( isset( $allowed_values ) && is_array( $allowed_values ) )
            $sanitized_value = in_array( $field_value , $allowed_values ) ? sanitize_text_field( $field_value ) : $default_value;

        return $sanitized_value;
    }




    /*
    |--------------------------------------------------------------------------
    | Implemented Interface Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Execute model.
     *
     * @implements ThirstyAffiliates\Interfaces\Model_Interface
     *
     * @since 3.1.0
     * @access public
     */
    public function run() {

        add_action( 'rest_api_init' , array( $this , 'register_ta_custom_fields_on_rest' ) , 10 );
        add_filter( 'ta_rest_api_sanitize_field' , array( $this , 'sanitize_special_fields' ) , 10 , 5 );
    }
}
