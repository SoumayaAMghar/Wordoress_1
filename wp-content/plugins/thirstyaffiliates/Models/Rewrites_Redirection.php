<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Deactivatable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

/**
 * Model that houses the logic for permalink rewrites and affiliate link redirections.
 *
 * @since 3.0.0
 */
class Rewrites_Redirection implements Model_Interface , Deactivatable_Interface {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that holds the single main instance of Rewrites_Redirection.
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

    /**
     * Property that holds the currently loaded thirstylink post.
     *
     * @since 3.0.0
     * @access private
     */
    private $_thirstylink;




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
     * @return Redirection
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }




    /*
    |--------------------------------------------------------------------------
    | Flush Rewrite Rules
    |--------------------------------------------------------------------------
    */

    /**
     * Get thirstylink Affiliate_Link object.
     *
     * @since 3.0.0
     * @access private
     *
     * @param int $post_id Thirstylink post id.
     * @return Affiliate_Link object.
     */
    private function get_thirstylink_post( $post_id ) {

        if ( is_object( $this->_thirstylink ) && $this->_thirstylink->get_id() == $post_id )
            return $this->_thirstylink;

        return $this->_thirstylink = new Affiliate_Link( $post_id );

    }

    /**
     * Set ta_flush_rewrite_rules transient value to true if the link prefix value has changed.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string $new_value Option new value.
     * @param string $old_value Option old value.
     */
    public function set_flush_rewrite_rules_transient( $new_value , $old_value ) {

        if ( $new_value != $old_value )
            set_transient( 'ta_flush_rewrite_rules' , 'true' , 5 * 60 );

        return $new_value;

    }

    /**
     * Set rewrite tags and rules.
     *
     * @since 3.0.0
     * @access private
     *
     * @param string $link_prefix Thirstylink post type slug.
     */
    public function set_rewrites( $link_prefix ) {

        add_rewrite_tag( '%' . $link_prefix . '%' , '([^&]+)' );
        add_rewrite_rule( "$link_prefix/([^/]+)?/?$" , 'index.php?thirstylink=$matches[1]' , 'top' );

        if ( get_option( 'ta_show_cat_in_slug' ) === 'yes' ) {

            add_rewrite_tag( '%thirstylink-category%' , '([^&]+)');
    		add_rewrite_rule( "$link_prefix/([^/]+)?/?([^/]+)?/?" , 'index.php?thirstylink=$matches[2]&thirstylink-category=$matches[1]' , 'top' );
        }
    }

    /**
     * Flush rewrite rules (soft) when the ta_flush_rewrite_rules transient is set to 'true'.
     *
     * @since 3.0.0
     * @access public
     */
    public function flush_rewrite_rules() {

        if ( 'true' !== get_transient( 'ta_flush_rewrite_rules' ) )
            return;

        flush_rewrite_rules( false );
        delete_transient( 'ta_flush_rewrite_rules' );

        // block bots on accessing/indexing affiliate links on htaccess
        $this->block_bots_to_access_affiliate_links_on_htaccess();
    }




    /*
    |--------------------------------------------------------------------------
    | Redirection Handler
    |--------------------------------------------------------------------------
    */

    /**
     * Handles redirect for thirstylink link urls.
     *
     * @since 3.0.0
     * @since 3.2.2 Add implementation for disabling cache for 301 redirects.
     * @since 3.3.2 TA-265 when attachment page is viewed link prefix, set page to 404 via $wp_query object.
     * @access public
     */
    public function redirect_url() {

        global $post , $wp_query;

        if ( is_admin() || ! is_object( $post ) || $post->post_type != Plugin_Constants::AFFILIATE_LINKS_CPT ) {

            if ( is_object( $post ) && $post->post_type === 'attachment' && $this->validate_cloaked_url() )
                $wp_query->set_404();

            return;
        }

        $thirstylink   = $this->get_thirstylink_post( $post->ID );
        $redirect_url  = html_entity_decode( $thirstylink->get_prop( 'destination_url' ) );
        $redirect_type = $thirstylink->get_redirect_type();

        // Apply any filters to the url and redirect type before redirecting
        $redirect_url  = apply_filters( 'ta_filter_redirect_url' , $redirect_url , $thirstylink , '' );
        $redirect_type = apply_filters( 'ta_filter_redirect_type' , $redirect_type , $thirstylink );

        // if cloaked url is invalid, then don't redirect.
        if ( ! $this->validate_cloaked_url( $thirstylink ) ) {

            $wp_query->set_404();
            remove_action( 'template_redirect' , array( $this , 'redirect_url' ) , 1 );
            return;
        }

        // perform actions before redirecting
        do_action( 'ta_before_link_redirect' , $thirstylink , $redirect_url , $redirect_type );

        if ( $redirect_url && $redirect_type ) {

            // tell browser not to cache 301 redirects (if option is enabled)
            if ( $redirect_type == 301 && get_option( 'ta_browser_no_cache_301_redirect' ) == 'yes' ) {
                header( 'Cache-Control: no-store, no-cache, must-revalidate' );
                header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );
            }

            if ( $thirstylink->is( 'no_follow' ) ) {
                header( 'X-Robots-Tag: noindex, nofollow' );
            }

            wp_redirect( $redirect_url , intval( $redirect_type ) );
		    exit;
        }

    }

    /**
     * Validate the cloaked url.
     *
     * @since 3.2.2
     * @since 3.3.2 improved code so it will always check the link prefix and only check category when it is present and eligible.
     * @access private
     *
     * @return boolean True if cloaked url is valid, false otherwise.
     */
    private function validate_cloaked_url( $thirstylink = null ) {

        $cat_slug    = is_object( $thirstylink ) ? $thirstylink->get_category_slug() : '';
        $link_prefix = $this->_helper_functions->get_thirstylink_link_prefix();
        $referrer    = isset( $_SERVER[ 'REQUEST_URI' ] ) ? $_SERVER[ 'REQUEST_URI' ] : '';
        $needle      = '/' . $link_prefix . '/';

        // if setting is disabled or category slug is not defined, then return as validated.
        if ( get_option( 'ta_show_cat_in_slug' ) == 'yes' && $cat_slug )
            $needle .= $cat_slug  . '/';

        return strpos( $referrer , $needle ) !== false;
    }

    /**
     * Pass query strings to destination url when option is enabled on settings.
     *
     * @since 3.0.0
     * @since 3.4.0 Add query string as a parameter to support enhanced JS redirect.
     * @access public
     *
     * @param string         $redirect_url Affiliate link destination url.
     * @param Affiliate_Link $thirstylink  Affiliate link object.
     * @param string         $query_string Query string.
     * @return string Redirect url with query string or not.
     */
    public function pass_query_string_to_destination_url( $redirect_url , $thirstylink , $query_string = '' ) {

        if ( ! $query_string && isset( $_SERVER[ 'QUERY_STRING' ] ) )
            $query_string = $_SERVER[ 'QUERY_STRING' ];

        if ( ! $query_string || ! $thirstylink->is( 'pass_query_str' ) )
            return $redirect_url;

        $connector  = ( strpos( $redirect_url , '?' ) === false ) ? '?' : '&';

        return $redirect_url . $connector . $query_string;
    }

    /**
     * Add/Recreate htaccess rule to block bots access to affiliate links.
     *
     * @since 3.1.0
     * @since 3.3.2 Get blocked bots from setting value.
     * @since 3.3.7 We only add the htaccess bots blocker content when the ta_enable_bot_crawl_blocker_script setting option is set to "yes".
     * @access public
     */
    public function block_bots_to_access_affiliate_links_on_htaccess() {

        $htaccess = $this->remove_block_bots_htaccess_rules();

        if ( get_option( 'ta_enable_bot_crawl_blocker_script' ) == 'yes' ) {

            $link_prefix = $this->_helper_functions->get_thirstylink_link_prefix();
            $bots_list   = apply_filters( 'ta_block_bots_on_htaccess' , $this->_helper_functions->get_blocked_bots() );

            // prepare new TA block bots htaccess content.
            $block_bots  = "\n#BEGIN Block-Bots-ThirstyAffiliates\n";
            $block_bots .= "<IfModule mod_rewrite.c>\n";
            $block_bots .= "RewriteEngine On\n";
            $block_bots .= "RewriteCond %{HTTP_USER_AGENT} (" . $bots_list . ") [NC]\n";
            $block_bots .= "RewriteRule ^" . $link_prefix . "/ - [L,F]\n";
            $block_bots .= "</IfModule>\n";
            $block_bots .= "#END Block-Bots-ThirstyAffiliates\n\n";

            // prepend block bots rules in the htaccess content.
            $htaccess = $block_bots . $htaccess;
        }

        file_put_contents( $this->_constants->HTACCESS_FILE() , $htaccess );
    }

    /**
     * Remove ThirstyAffiliates block bots htaccess rules.
     *
     * @since 3.1.0
     * @access public
     *
     * @param boolean $put_contents Toggle to check if function needs to save htaccess file or not.
     * @return string Htaccess content after removing TA block bots rules.
     */
    public function remove_block_bots_htaccess_rules( $put_contents = false ) {

        $htaccess = file_get_contents( $this->_constants->HTACCESS_FILE() );
        $pattern  = "/[\n]*#[\s]*BEGIN Block-Bots-ThirstyAffiliates.*?#[\s]*END Block-Bots-ThirstyAffiliates[\n][\n]/is";
        $htaccess = preg_replace( $pattern , "" , $htaccess );

        if ( $put_contents )
            file_put_contents( $this->_constants->HTACCESS_FILE() , $htaccess );

        return $htaccess;
    }

    /**
     * Block bots access to affiliate links for non-apache servers.
     *
     * @since 3.3.3
     * @since 3.3.7 We only block bots when the ta_enable_bot_crawl_blocker_script setting option is set to "yes".
     * @access public
     */
    public function block_bots_non_apache_server() {

        global $post;

        $is_apache = strpos( $_SERVER[ 'SERVER_SOFTWARE' ] , 'Apache' ) !== false;

        if ( $is_apache || ! is_object( $post ) || $post->post_type !== Plugin_Constants::AFFILIATE_LINKS_CPT || ! $this->_helper_functions->is_user_agent_bot() )
            return;

        $message = apply_filters( 'ta_blocked_bots_non_apache_message' , sprintf( __( "<h1>Forbidden</h1><p>You don't have permission to access %s on this server.</p>" , 'thirstyaffiliates' ) , $_SERVER[ 'REQUEST_URI' ] ) );
        header( 'HTTP/1.0 403 Forbidden' );
        die( $message );
    }




    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
    */

    /**
     * Execute codes that needs to run plugin deactivation.
     *
     * @since 1.0.0
     * @access public
     * @implements ThirstyAffiliates\Interfaces\Deactivatable_Interface
     */
    public function deactivate() {

        $this->remove_block_bots_htaccess_rules( true );
    }

    /**
     * Execute ajax handler.
     *
     * @since 3.0.0
     * @access public
     * @inherit ThirstyAffiliates\Interfaces\Model_Interface
     */
    public function run() {

        // flush rewrite rules
        add_filter( 'pre_update_option_ta_link_prefix' , array( $this , 'set_flush_rewrite_rules_transient' ) , 10 , 2 );
        add_filter( 'pre_update_option_ta_link_prefix_custom' , array( $this , 'set_flush_rewrite_rules_transient' ) , 10 , 2 );
        add_filter( 'pre_update_option_ta_show_cat_in_slug' , array( $this , 'set_flush_rewrite_rules_transient' ) , 10 , 2 );
        add_filter( 'pre_update_option_ta_blocked_bots' , array( $this , 'set_flush_rewrite_rules_transient' ) , 10 , 2 );
        add_action( 'update_option_ta_enable_bot_crawl_blocker_script' , array( $this , 'set_flush_rewrite_rules_transient' ) , 10 , 2 );
        add_action( 'ta_after_register_thirstylink_post_type' , array( $this , 'set_rewrites' ) , 1 , 1 );
        add_action( 'ta_after_register_thirstylink_post_type' , array( $this , 'flush_rewrite_rules' ) );

        // redirection handler
        add_action( 'template_redirect' , array( $this , 'redirect_url' ) , 1 );

        // filter redirect url before redirecting
        add_filter( 'ta_filter_redirect_url' , array( $this , 'pass_query_string_to_destination_url' ) , 10 , 3 );

        // block bots on redirect (for non-apache servers).
        if ( get_option( 'ta_enable_bot_crawl_blocker_script' ) == 'yes' )
            add_action( 'wp' , array( $this , 'block_bots_non_apache_server' ) );

        // update the ta_enable_bot_crawl_blocker_script setting option if it has no value yet on database.
        if ( get_option( 'ta_enable_bot_crawl_blocker_script' , 'not_on_db' ) === 'not_on_db' )
            update_option( 'ta_enable_bot_crawl_blocker_script' , '' );
    }
}
