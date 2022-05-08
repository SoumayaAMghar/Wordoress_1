<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

/**
 * Model that houses the logic for permalink rewrites and affiliate link redirections.
 *
 * @since 3.0.0
 */
class Shortcodes implements Model_Interface {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that holds the single main instance of Shortcodes.
     *
     * @since 3.0.0
     * @access private
     * @var Redirection
     */
    private static $_instance;

    /**
     * Model that houses the main plugin object.
     *
     * @since 3.0.0
     * @access private
     * @var Redirection
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
     * @return Redirection
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }

    /**
     * Checks if the given ID needs to be uncloaked
     *
     * @since 3.0.0
     * @access public
     *
     * @param int $link_id Affiliate Link post ID.
     * @return boolean.
     */
    public function is_link_to_be_uncloaked( $link_id ) {

        if ( get_option( 'ta_uncloak_link_per_link' ) == 'yes' ) {

            $links_to_uncloak = maybe_unserialize( get_option( 'ta_links_to_uncloak' , array() ) );

            if ( in_array( $link_id , $links_to_uncloak ) )
                return true;

        }

        if ( get_option( 'ta_uncloak_link_per_category' ) == 'yes' && $category_to_uncloak = get_option( 'ta_category_to_uncloak' ) ) {

            if ( has_term( $category_to_uncloak , Plugin_Constants::AFFILIATE_LINKS_TAX , $link_id ) )
                return true;
        }

        return false;
    }

    /**
     * thirstylink shortcode.
     * example: [thirstylink ids="10,15,18,20"]Affiliate Link[/thirstylink]
     *
     * @since 3.0.0
     * @since 3.3.0 Add data-nojs attribute support.
     * @since 3.4.0 Add additional classes field support.
     * @access public
     *
     * @param array  $atts    Shortcode attributes.
     * @param string $content Shortcode content.
     * @return string Processed shortcode output.
     */
    public function thirstylink_shortcode( $atts , $content = '' ) {

        global $post;

        $post_id = is_a( $post , 'WP_Post' ) ? $post->ID : 0;

        $atts = shortcode_atts( array(
            'ids'       => '',
            'linkid'    => '',
            'linktext'  => '',
            'class'     => '',
            'rel'       => '',
            'target'    => '',
            'title'     => ''
        ), $atts , 'thirstylink' );

        // get all link attributes from $atts
        $link_attributes = array_diff_assoc(
            $atts,
            array(
                'ids'       => $atts[ 'ids' ],
                'linkid'    => $atts[ 'linkid' ],
                'linktext'  => $atts[ 'linktext' ],
            )
        );

        // get the link ID
        if ( ! $atts[ 'linkid' ] ) {

            $ids     = isset( $atts[ 'ids' ] ) ? array_map( 'intval' , explode( ',' , $atts[ 'ids' ] ) ) : array();
            $key     = rand( 0 , count( $ids ) - 1 );
            $link_id = $ids[ $key ];
        } else
            $link_id = (int) $atts[ 'linkid' ];

        $output = '';

        if ( $link_id && get_post_type( $link_id ) == Plugin_Constants::AFFILIATE_LINKS_CPT ) {

            // load thirstylink
            $thirstylink  = new Affiliate_Link( $link_id );
            $uncloak_link = $thirstylink->is( 'uncloak_link' );

            // get the link URL
            $link_attributes[ 'href' ] = ( $uncloak_link ) ? apply_filters( 'ta_uncloak_link_url' , $thirstylink->get_prop( 'destination_url' ) , $thirstylink ) : $thirstylink->get_prop( 'permalink' );

            if ( ! $uncloak_link )
                $link_attributes[ 'href' ] = $this->clean_url( $link_attributes[ 'href' ] );

            // get link text content default if no value is set
            if ( empty( $content ) && $atts[ 'linktext' ] )
                $content = $atts[ 'linktext' ]; // backward compatibility to get the link text content.
            else if ( empty( $content ) )
                $content = $thirstylink->get_prop( 'name' );

            // check for nofollow defaults if no value is set
            if ( empty( $link_attributes[ 'rel' ] ) ) {

                $link_attributes[ 'rel' ] = $thirstylink->is( 'no_follow' ) ? 'nofollow' : '';

                if ( $thirstylink->get_prop( 'rel_tags' ) )
                    $link_attributes[ 'rel' ] = trim( $link_attributes[ 'rel' ] . ' ' . $thirstylink->get_prop( 'rel_tags' ) );
            }

            // check for new window defaults if no value is set
            if ( empty( $link_attributes[ 'target' ] ) ) {

                $link_attributes[ 'target' ] = $thirstylink->is( 'new_window' ) ? '_blank' : '';
            }

            // provide default class value if it is not set
            if ( empty( $link_attributes[ 'class' ] ) ){
                $thirsty_link_class = strpos( $content , '<img ' ) !== false ? 'thirstylinkimg' : 'thirstylink';
                $link_attributes[ 'class' ] = get_option( 'ta_disable_thirsty_link_class' ) !== 'yes' ? $thirsty_link_class : '';

                if ( $thirstylink->get_prop( 'css_classes' ) ) 
                    $link_attributes[ 'class' ] = trim( $link_attributes[ 'class' ] . ' ' . $thirstylink->get_prop( 'css_classes' ) );
            }
                

            // provide default class value if it is not set
            if ( empty( $link_attributes[ 'title' ] ) && get_option( 'ta_disable_title_attribute' ) !== 'yes' )
                $link_attributes[ 'title' ] = $thirstylink->get_prop( 'name' );

            // remove double quote character on title attribute.
            $link_attributes[ 'title' ] = esc_attr( str_replace( '"' , '' , $link_attributes[ 'title' ] ) );

            // add data-link_id attribute if affiliate link is uncloaked.
            if ( $uncloak_link )
                $link_attributes[ 'data-linkid' ] = $link_id;

            // tag links as "nojs" to disable JS redirect for them.
            if ( get_option( 'ta_enable_javascript_frontend_redirect' ) == 'yes' )
                $link_attributes[ 'data-nojs' ] = apply_filters( 'ta_nojs_redirect_attribute' , false , $thirstylink );

            // allow the ability to add custom link attributes
            $link_attributes = apply_filters( 'ta_link_insert_extend_data_attributes' , $link_attributes , $thirstylink , $post_id );

            // Build the link ready for output
            $output .= '<a';

            foreach ( $link_attributes as $name => $value ) {
				// Handle square bracket escaping (used for some addons, eg. Google Analytics click tracking)
				$value   = html_entity_decode( $value );
				$value   = preg_replace( '/&#91;/' , '[' , $value );
				$value   = preg_replace( '/&#93;/' , ']' , $value );
				$output .= ! empty($value) ? ' ' . $name . '="' . trim( esc_attr( $value ) ) . '"' : '';
			}

			$output .= ' data-shortcode="true">' . do_shortcode( $content ) . '</a>';


        } elseif ( current_user_can( 'edit_published_posts' ) )
            $output .= '<span style="color: #0000ff;">' . __( 'SHORTCODE ERROR: ThirstyAffiliates did not detect a valid link id, please check your short code!' , 'thirstyaffiliates' ) . '</span>';
        else
            $output = $content;     

        return $output;
    }

    /**
     * Remove parameters in the URL added via the get_permalink hook and escape it.
     * Note: This is needed for WPML support.
     *
     * @since 3.2.1
     * @access private
     *
     * @param string $raw_url Raw URL.
     * @param string Cleaned URL.
     */
    private function clean_url( $raw_url ) {

        $parse = parse_url( $raw_url );
        extract( $parse );

        // rebuild url excluding parameters.
        $url  = $scheme . '://' . $host;
        $url .= isset( $path ) ? $path : '';

        return esc_url( $url );
    }

    /**
     * Execute shortcodes class.
     *
     * @since 3.0.0
     * @access public
     */
    public function run() {

        add_shortcode( 'thirstylink' , array( $this , 'thirstylink_shortcode' ) );
    }
}
