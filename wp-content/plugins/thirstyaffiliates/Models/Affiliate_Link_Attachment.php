<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

/**
 * Model that houses the logic of media attachments of an affiliate link.
 *
 * @since 3.0.0
 */
class Affiliate_Link_Attachment implements Model_Interface , Initiable_Interface {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that holds the single main instance of Bootstrap.
     *
     * @since 3.0.0
     * @access private
     * @var Affiliate_Link_Attachment
     */
    private static $_instance;

    /**
     * Model that houses the main plugin object.
     *
     * @since 3.0.0
     * @access private
     * @var Affiliate_Link_Attachment
     */
    private $_main_plugin;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 3.0.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 3.0.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;




    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Class constructor.
     *
     * @since 3.0.0
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
        $main_plugin->add_to_public_models( $this );

    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 3.0.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Affiliate_Link_Attachment
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }





    /*
    |--------------------------------------------------------------------------
    | Attachments
    |--------------------------------------------------------------------------
    */

    /**
     * Add attachments to affiliate link via ajax.
     *
     * @since 3.0.0
     * @access public
     */
    public function ajax_add_attachments_to_affiliate_link() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( $this->_helper_functions->get_capability_for_interface('thirstylink_edit', 'publish_posts') ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta_add_attachments_to_affiliate_link', false, false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_POST[ 'attachment_ids' ] ) || ! isset( $_POST[ 'affiliate_link_id' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Missing required post data' , 'thirstyaffiliates' ) );
        else {

            $result = $this->add_attachments_to_affiliate_link( $_POST[ 'attachment_ids' ] , $_POST[ 'affiliate_link_id' ] );

            if ( is_wp_error( $result ) )
                $response = array( 'status' => 'fail' , 'error_msg' => $result->get_error_message() );
            else {

                ob_start();

                foreach ( $result as $attachment_id ) {

                    $img = wp_get_attachment_image_src( $attachment_id , 'full' );
                    include( $this->_constants->VIEWS_ROOT_PATH() . 'cpt/view-attach-images-metabox-single-image.php' );

                }

                $added_attachments_markup = ob_get_clean();

                $response = array( 'status' => 'success' , 'success_msg' => __( 'Attachments successfully added to the affiliate link' , 'thirstyaffiliates' ) , 'added_attachments_markup' => $added_attachments_markup );

            }

        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();

    }

    /**
     * Add attachments to affiliate link.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $attachment_ids    Array of attachment ids.
     * @param int   $affiliate_link_id Id of the current affiliate link.
     * @return \WP_Error|array WP_Error instance on failure, array of attachment IDs otherwise.
     */
    public function add_attachments_to_affiliate_link( $attachment_ids , $affiliate_link_id ) {

        if ( !is_array( $attachment_ids ) )
            return new \WP_Error( 'ta_invalid_attachment_ids' , __( 'Invalid attachment ids to attach to an affiliate link' , 'thirstyaffiliates' ) , array( 'attachment_ids' => $attachment_ids , 'affiliate_link_id' => $affiliate_link_id ) );

        $attachments = get_post_meta( $affiliate_link_id , Plugin_Constants::META_DATA_PREFIX . 'image_ids' , true );
        if ( !is_array( $attachments ) )
            $attachments = array();

        $new_attachment_ids = array_diff( $attachment_ids , $attachments );

        update_post_meta( $affiliate_link_id , Plugin_Constants::META_DATA_PREFIX . 'image_ids' , array_unique( array_merge( $attachments , $attachment_ids ) ) );

        return $new_attachment_ids;

    }

    /**
     * Add attachments to affiliate link via ajax.
     *
     * @since 3.0.0
     * @access public
     */
    public function ajax_remove_attachment_to_affiliate_link() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( $this->_helper_functions->get_capability_for_interface('thirstylink_edit', 'publish_posts') ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta_remove_attachments_from_affiliate_link', false, false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_POST[ 'attachment_id' ] ) || ! isset( $_POST[ 'affiliate_link_id' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Missing required post data' , 'thirstyaffiliates' ) );
        else {

            $result = $this->remove_attachment_to_affiliate_link( $_POST[ 'attachment_id' ] , $_POST[ 'affiliate_link_id' ] );

            if ( is_wp_error( $result ) )
                $response = array( 'status' => 'fail' , 'error_msg' => $result->get_error_message() );
            else
                $response = array( 'status' => 'success' , 'success_msg' => __( 'Attachment successfully removed from attachment' , 'thirstyaffiliates' ) );

        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();

    }

    /**
     * Remove an attachment from an affiliate link.
     *
     * @since 3.0.0
     * @access public
     *
     * @param int $attachment_id     Attachment id.
     * @param int $affiliate_link_id Affiliate link id.
     * @return \WP_Error | boolean WP_Error instance on failure, boolean true otherwise.
     */
    public function remove_attachment_to_affiliate_link( $attachment_id , $affiliate_link_id ) {

        $attachments = get_post_meta( $affiliate_link_id , Plugin_Constants::META_DATA_PREFIX . 'image_ids' , true );
        if ( !is_array( $attachments ) )
            $attachments = array();

        if ( !in_array( $attachment_id , $attachments ) )
            return new \WP_Error( 'ta_invalid_attachment_id' , __( 'Invalid attachment id to remove from an affiliate link' , 'thirstyaffiliates' ) , array( 'attachment_id' => $attachment_id , 'affiliate_link_id' => 'affiliate_link_id' ) );

        $key = array_search( $attachment_id , $attachments );
        unset( $attachments[ $key ] );

        update_post_meta( $affiliate_link_id , Plugin_Constants::META_DATA_PREFIX . 'image_ids' , $attachments );

        return true;

    }

    /**
     * This is the place where we decide whether if it is an affiliate link page and if it is, well apply custom action modifications that is exclusive only to the affiliate link page.
     *
     * @since 3.0.0
     * @access public
     */
    public function current_screen_filter() {

        $current_screen = get_current_screen();

        if ( $current_screen->base === 'post' && $current_screen->post_type === 'thirstylink' ) {

            add_filter( 'upload_mimes' , array( $this , 'restrict_file_upload_to_images_only' ) , 10 , 1 );

        }

    }

    /**
     * Restrict the media library uploader to accept image files only.
     * The effects of this filter is global, thats why we only apply the filter when we are inside an affiliate link edit page.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $mime_types Array of accepted mime types.
     * @return array Filtered array of accepted mime types.
     */
    public function restrict_file_upload_to_images_only( $mime_types ) {

        return array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'gif'          => 'image/gif',
            'png'          => 'image/png',
            'bmp'          => 'image/bmp',
            'tiff|tif'     => 'image/tiff',
            'ico'          => 'image/x-icon'
        );

    }

    /**
     * AJAX insert external image.
     *
     * @since 3.4.0
     * @access public
     */
    public function ajax_insert_external_image() {

        $allowed_extensions = apply_filters( 'ta_allowed_external_image_ext' , array( 'jpg' , 'jpeg' , 'png' , 'gif' , 'svg' ) );

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( $this->_helper_functions->get_capability_for_interface('thirstylink_edit', 'publish_posts') ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta_insert_external_image', false, false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_POST[ 'url' ] ) || ! isset( $_POST[ 'link_id' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Missing required post data' , 'thirstyaffiliates' ) );
        elseif ( ! filter_var( $_POST[ 'url' ] , FILTER_VALIDATE_URL ) || ! in_array( strtolower( pathinfo( $_POST[ 'url' ] , PATHINFO_EXTENSION ) ) , $allowed_extensions ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'The external image source is not a valid url.' , 'thirstyaffiliates' ) );
        else {

            $img_url = esc_url_raw( $_POST[ 'url' ] );
            $link_id = absint( $_POST[ 'link_id' ] );
            $result  = $this->add_attachments_to_affiliate_link( array( $img_url ) , $link_id );

            if ( empty( $result ) )
                $response = array( 'status' => 'fail' , 'error_msg' => __( 'The external image is already added.' , 'thirstyaffiliates' ) );
            else {

                ob_start();
                include( $this->_constants->VIEWS_ROOT_PATH() . 'cpt/view-attach-images-metabox-external-image.php' );
                $image_markup = ob_get_clean();

                $response = array(
                    'status' => 'success',
                    'url'    => $img_url,
                    'markup' => $image_markup
                );
            }

        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
    }




    /*
    |--------------------------------------------------------------------------
    | Implemented Interface Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Execute codes that needs to run on plugin initialization.
     *
     * @since 3.0.0
     * @access public
     * @implements \ThirstyAffiliates\Interfaces\Initiable_Interface
     */
    public function initialize() {

        add_action( 'wp_ajax_ta_add_attachments_to_affiliate_link' , array( $this , 'ajax_add_attachments_to_affiliate_link' ) );
        add_action( 'wp_ajax_ta_remove_attachment_to_affiliate_link' , array( $this , 'ajax_remove_attachment_to_affiliate_link' ) );
        add_action( 'wp_ajax_ta_insert_external_image' , array( $this , 'ajax_insert_external_image' ) );

    }

    /**
     * Execute model core logic.
     *
     * @since 3.0.0
     * @access public
     * @implements \ThirstyAffiliates\Interfaces\Model_Interface
     */
    public function run() {

        add_action( 'current_screen' , array( $this , 'current_screen_filter' ) );

    }

}
