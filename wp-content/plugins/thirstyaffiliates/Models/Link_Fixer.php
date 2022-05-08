<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

use ThirstyAffiliates\Models\Affiliate_Link;

/**
 * Model that houses the link fixer logic.
 *
 * @since 3.0.0
 */
class Link_Fixer implements Model_Interface , Initiable_Interface {

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
     * @var Link_Fixer
     */
    private static $_instance;

    /**
     * Model that houses the main plugin object.
     *
     * @since 3.0.0
     * @access private
     * @var Abstract_Main_Plugin_Class
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
     * @return Link_Fixer
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }

    /**
     * Get data of links to be fixed.
     *
     * @since 3.0.0
     * @since 3.2.4 Make sure that link fixer runs using the default language set on WPML when it is active.
     * @since 3.3.0 Add data-nojs attribute support.
     * @since 3.4.0 Add support for additional classes field. Remove query string on href before processing.
     * @access public
     *
     * @global SitePress $sitepress WPML main plugin object.
     *
     * @param array $links   List of affiliate links to fix.
     * @param int   $post_id ID of the post currently being viewed.
     * @param array $data    Affiliate Links data.
     * @return array Affiliate Links data.
     */
    public function get_link_fixer_data( $links , $post_id = 0 , $data = array() ) {

        global $sitepress;

        if ( empty( $links ) )
            return $data;

        if ( is_object( $sitepress ) )
            $sitepress->switch_lang( $sitepress->get_default_language() );

        $global_rel   = get_option( 'ta_additional_rel_tags' );
        $global_class = get_option( 'ta_additional_css_classes' );

        foreach( $links as $link ) {

            $href    = strtok( esc_url_raw( $link[ 'href' ] ) , '?' );
            $class   = isset( $link[ 'class' ] ) ? sanitize_text_field( $link[ 'class' ] ) : '';
            $key     = (int) sanitize_text_field( $link[ 'key' ] );
            $link_id = $this->url_to_affiliate_link_id( $href );

            $thirstylink = new Affiliate_Link( $link_id );

            if ( ! $thirstylink->get_id() || get_post_type( $link_id ) !== Plugin_Constants::AFFILIATE_LINKS_CPT )
                continue;

            $class   = str_replace( 'thirstylinkimg' , 'thirstylink' , $class );
            $class  .= ( get_option( 'ta_disable_thirsty_link_class' ) !== "yes" && strpos( $class , 'thirstylink' ) === false ) ? ' thirstylink' : '';
            $href    = ( $thirstylink->is( 'uncloak_link' ) ) ? apply_filters( 'ta_uncloak_link_url' , $thirstylink->get_prop( 'destination_url' ) , $thirstylink ) : $thirstylink->get_prop( 'permalink' );
            $rel     = $thirstylink->is( 'no_follow' ) ? 'nofollow' : '';
            $target  = $thirstylink->is( 'new_window' ) ? '_blank' : '';
            $title   = get_option( 'ta_disable_title_attribute' ) != 'yes' ? esc_attr( str_replace( '"' , '' , $thirstylink->get_prop( 'name' ) ) ) : '';
            $title   = str_replace( '&#039;' , '\'' , $title );

            // append custom rel tags.
            if ( $thirstylink->get_prop( 'rel_tags' ) )
                $rel = trim( $rel . ' ' . $thirstylink->get_prop( 'rel_tags' ) );

            // make sure rel attribute entries are unique
            $rel_array = explode( ' ' , $rel );
            $rel       = implode( ' ' , array_unique( $rel_array ) );

            // append custom classes.
            if ( $thirstylink->get_prop( 'css_classes' ) )
                $class = trim( $class . ' ' . $thirstylink->get_prop( 'css_classes' ) );

            // make sure class attribute entries are unique
            $class_array = explode( ' ' , $class );
            $class       = implode( ' ' , array_unique( $class_array ) );

            // if link is image, then change default class to 'thirstylinkimg'
            if ( $link[ 'is_image' ] )
                $class = str_replace( 'thirstylink' , 'thirstylinkimg' , $class );

            // if thirstylink class is not the same with global class, then we remove global class.
            if ( $thirstylink->get_prop( 'css_classes' ) != $global_class )
                $class = str_replace( $global_class , '' , $class ) ;

            $data[] = array(
                'key'     => $key,
                'link_id' => $link_id,
                'class'   => trim( esc_attr( preg_replace( '!\s+!' , ' ',  $class ) ) ),
                'href'    => esc_url_raw( $href ),
                'rel'     => trim( esc_attr( preg_replace( '!\s+!' , ' ',  $rel ) ) ),
                'target'  => esc_attr( $target ),
                'title'   => $title,
                'nojs'    => apply_filters( 'ta_nojs_redirect_attribute' , false , $thirstylink ),
                'pass_qs' => $thirstylink->is( 'pass_query_str' )
            );
        }

        return $data;
    }

    /**
     * Get the ID from a given affiliate link URL (replaces url_to_postid function).
     *
     * @since 3.3.0
     * @access private
     *
     * @param string $href Affilaite link cloaked URL.
     * @return int Affiliate link ID.
     */
    private function url_to_affiliate_link_id( $href ) {

        global $wpdb;

        $link_parts  = explode( "/" , $href );
        $link_prefix = $this->_helper_functions->get_thirstylink_link_prefix();
        $cpt_slug    = esc_sql( Plugin_Constants::AFFILIATE_LINKS_CPT );

        // get the key of the link prefix in the url from the $link_parts variable.
        $key = (int) array_search( $link_prefix , $link_parts );

        // get the slug from the $link_parts variable based on the position of the link prefix's key.
        // if $key + 2 exists, this means that the link has a category slug in it, so we fetch $key + 2, otherwise we fetch $key + 1 (no category slug).
        $slug = esc_sql( isset( $link_parts[ $key + 2 ] ) && $link_parts[ $key + 2 ] ? $link_parts[ $key + 2 ] : $link_parts[ $key + 1 ] );

        // fetch the ID based on the post type and slug.
        $id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_type = '$cpt_slug' AND post_name = '$slug'" );

        return $id ? absint( $id ) : 0;
    }

    /**
     * Ajax link fixer.
     *
     * @since 3.0.0
     * @access public
     */
    public function ajax_link_fixer() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_POST[ 'hrefs' ] ) || empty( $_POST[ 'hrefs' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        else {

            $links    = $_POST[ 'hrefs' ];
            $post_id  = isset( $_POST[ 'post_id' ] ) ? intval( $_POST[ 'post_id' ] ) : 0;
            $response = array(
                'status' => 'success',
                'data' => $this->get_link_fixer_data( $links , $post_id )
            );
        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
    }




    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
    */

    /**
     * Execute codes that needs to run on plugin initialization.
     *
     * @since 3.0.0
     * @access public
     * @implements ThirstyAffiliates\Interfaces\Initiable_Interface
     */
    public function initialize() {

        add_action( 'wp_ajax_ta_link_fixer' , array( $this , 'ajax_link_fixer' ) );
        add_action( 'wp_ajax_nopriv_ta_link_fixer' , array( $this , 'ajax_link_fixer' ) );
    }

    /**
     * Execute link picker.
     *
     * @since 3.0.0
     * @access public
     */
    public function run() {
    }
}
