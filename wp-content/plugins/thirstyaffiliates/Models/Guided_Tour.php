<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Activatable_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;
use ThirstyAffiliates\Interfaces\Model_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

/**
 * Model that houses the logic for permalink rewrites and affiliate link redirections.
 *
 * @since 3.0.0
 */
class Guided_Tour implements Model_Interface , Activatable_Interface , Initiable_Interface {

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
     * @var Guided_Tour
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

    /**
     * Property that urls of the guided tour screens.
     *
     * @since 3.0.0
     * @access private
     * @var array
     */
    private $_urls = array();

    /**
     * Property that houses the screens of the guided tour.
     *
     * @since 3.0.0
     * @access private
     * @var array
     */
    private $_screens = array();




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
     * @return Guided_Tour
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }

    /**
     * Define guided tour pages.
     *
     * @since 3.0.0
     * @access private
     */
    private function define_guided_tour_pages() {

        $this->_urls = apply_filters( 'ta_guided_tour_pages' , array(
            'plugin-listing'          => admin_url( 'plugins.php' ),
            'affiliate-links-listing' => admin_url( 'edit.php?post_type=thirstylink' ),
            'new-wp-post'             => admin_url( 'post-new.php' ),
            'general-settings'        => admin_url( 'edit.php?post_type=thirstylink&page=thirsty-settings' ),
            'link-apperance-settings' => admin_url( 'edit.php?post_type=thirstylink&page=thirsty-settings&tab=ta_links_settings' ),
            'modules-settings'        => admin_url( 'edit.php?post_type=thirstylink&page=thirsty-settings&tab=ta_modules_settings' ),
            'import-export-settings'  => admin_url( 'edit.php?post_type=thirstylink&page=thirsty-settings&tab=ta_import_export_settings' ),
            'help-settings'           => admin_url( 'edit.php?post_type=thirstylink&page=thirsty-settings&tab=ta_help_settings' ),
            'new-affiliate-link'      => admin_url( 'post-new.php?post_type=thirstylink' ),
        ) );

        $this->_screens = apply_filters( 'ta_guided_tours' , array(
            'plugins' => array(
                'elem'  => '#menu-posts-thirstylink .menu-top',
                'html'  => __( '<h3>Congratulations, you just activated ThirstyAffiliates!</h3>
                               <p>Would you like to take a tour of the plugin features? It takes less than a minute and you\'ll then know exactly how to use the plugin.</p>', 'thirstyaffiliates' ),
                'prev'  => null,
                'next'  => $this->_urls[ 'affiliate-links-listing' ],
                'edge'  => 'left',
                'align' => 'left',
            ),
            'edit-thirstylink' => array(
                'elem'  => '#wpbody-content > .wrap > .wp-heading-inline',
                'html'  => __( '<h3>ThirstyAffiliates helps you manage affiliate links that you are given from the various affiliate programs you are a member of.</h3>
                               <p>It lets you hide long, and often confusing looking, affiliate link URLs behind another URL called a redirect. When your visitors click on that new URL they are automatically redirected to your affiliate link.</p>
                               <p>This is helpful for five reasons:</p>
                               <ol><li>AESTHETICS: Affiliate links are often long and ugly as mentioned and this can look off putting to visitors. ThirstyAffiliates redirects make them shorter and more attractive to click on like "http://example.com/recommends/some-product-name"</li>
                               <li>PROTECTION: It hides the affiliate code, so malicious software (malware) cannot sniff out the affiliate code for common affiliate programs and replace it with their own code instead. In this way, it protects your commissions.</li>
                               <li>CONVENIENCE: If the affiliate program ever changes the link code you’ll only have ONE place to change it on your blog, rather than going through all your content and changing every instance of the link.</li>
                               <li>CATEGORIZATION: You categorize your affiliate links into logical groups which can make managing them much simpler.</li>
                               <li>STATISTICS: Finally, because your visitors are travelling via a ThirstyAffiliates redirect, the plugin can provide you with statistics on what is being clicked.</li></ol>
                               <p>You can now create an “Affiliate Link” in this new section in your dashboard. This view shows you all the affiliate links you are managing with ThirstyAffiliates.</p>', 'thirstyaffiliates' ),
                'prev'  => $this->_urls[ 'plugin-listing' ],
                'next'  => $this->_urls[ 'new-wp-post' ],
                'edge'  => 'top',
                'align' => 'left',
                'width' => 600
            ),
            'post' => array(
                'elem'  => '#wpbody #insert-media-button',
                'html'  => __( '<h3>Affiliate links can be added to your posts easily by clicking on the “TA” button on your editor.</h3>
                               <p>This works identically to the WordPress link tool, but only searches for affiliate links.</p>
                               <p>Give it a try by typing in some text, highlighting it, then clicking the “TA” button.</p>
                               <p>If you need to you can click the cog icon for an advanced search view which is handy for doing more advanced searches and for inserting images pre-wrapped with your affiliate link or via a shortcode instead.</p>', 'thirstyaffiliates' ),
                'prev'  => $this->_urls[ 'affiliate-links-listing' ],
                'next'  => $this->_urls[ 'general-settings' ],
                'edge'  => 'top',
                'align' => 'left',
                'width' => 400
            ),
            'thirstylink_page_thirsty-settings' => array(
                'ta_general_settings' => array(
                    'elem'  => '.nav-tab-wrapper .ta_general_settings',
                    'html'  => __( '<h3>ThirstyAffiliates has a number of settings that change the way it works, behaves and how your links appear.</h3>
                                   <p>Here are the General settings which are for changing the way you work with ThirstyAffiliates in the backend.</p>', 'thirstyaffiliates' ),
                    'prev'  => $this->_urls[ 'new-wp-post' ],
                    'next'  => $this->_urls[ 'link-apperance-settings' ],
                    'edge'  => 'top',
                    'align' => 'left',
                ),
                'ta_links_settings' => array(
                    'elem'  => '.nav-tab-wrapper .ta_links_settings',
                    'html'  => __( '<h3>One of the most important parts of the settings area is Link Appearance which changes the way your links look to your visitors.</h3>
                                   <p>This includes the Link Prefix setting which is important to decide on before you start using ThirstyAffiliates.</p>
                                   <p>By default, this is set to “recommends” so your links will look like “http://example.com/recommends/some-product-name”.</p>
                                   <p>You can also choose to include the category slug in the URL, change the way ThirstyAffiliates redirects links, add no follow, make links open in a new window and more.</p>', 'thirstyaffiliates' ),
                    'prev'  => $this->_urls[ 'general-settings' ],
                    'next'  => $this->_urls[ 'modules-settings' ],
                    'edge'  => 'top',
                    'align' => 'left',
                    'width' => 450
                ),
                'ta_modules_settings' => array(
                    'elem'  => '.nav-tab-wrapper .ta_modules_settings',
                    'html'  => __( '<h3>We built ThirstyAffiliates to be flexible and as such, you can shut down the parts of ThirstyAffiliates that aren’t being used. This can make the plugin faster.</h3>
                                   <p>If you have the Pro add-on each Pro module will show in here as well.</p>', 'thirstyaffiliates' ),
                    'prev'  => $this->_urls[ 'link-apperance-settings' ],
                    'next'  => $this->_urls[ 'import-export-settings' ],
                    'edge'  => 'top',
                    'align' => 'left',
                ),
                'ta_import_export_settings' => array(
                    'elem'  => '.nav-tab-wrapper .ta_import_export_settings',
                    'html'  => __( '<h3>Setting up multiple sites with all running ThirstyAffiliates?</h3>
                                   <p>We’ve made it super simple to configure your additional sites by being able to import and export ThirstyAffiliate settings.</p>
                                   <p>Just copy the Export section and paste it into the Import section on your other site and ThirstyAffiliates will be automatically configured the same way.</p>', 'thirstyaffiliates' ),
                    'prev'  => $this->_urls[ 'modules-settings' ],
                    'next'  => $this->_urls[ 'help-settings' ],
                    'edge'  => 'top',
                    'align' => 'right',
                ),
                'ta_help_settings' => array(
                    'elem'  => '.nav-tab-wrapper .ta_help_settings',
                    'html'  => __( '<h3>Need some help with ThirstyAffiliates?</h3>
                                   <p>We have a growing knowledge base filled with guides, troubleshooting and FAQ.</p>
                                   <p>Our blog is also very active with lots of interesting affiliate marketing topics to help you grow your affiliate marketing empire.</p>', 'thirstyaffiliates' ),
                    'prev'  => $this->_urls[ 'import-export-settings' ],
                    'next'  => $this->_urls[ 'new-affiliate-link' ],
                    'edge'  => 'top',
                    'align' => 'right',
                )
            ),
            'thirstylink' => array(
                'elem'  => '#menu-posts-thirstylink',
                'html'  => __( '<h3>This concludes the guide. You are now ready to set up your first affiliate link!</h3>
                               <p>We also have a Pro add-on for ThirstyAffiliates which contains lots of interesting features for affiliates like:</p>
                               <ul><li>Automatically link up your affiliate links to keywords in your blog</li>
                               <li>Get more detailed and advanced reports</li>
                               <li>Geolocated affiliate links</li>
                               <li>Google Analytics integration</li>
                               <li>CSV importing/exporting, Amazon importing, and more</li>
                               <li>Link event notification admin emails</li>
                               <li>Automatic link health checker</li></ul>
                               <p>Want to unlock all of the extra features you see here? The Pro add-on is for you. And we’re adding new features all the time!</p>', 'thirstyaffiliates' ),
                'prev'  => $this->_urls[ 'help-settings' ],
                'next'  => null,
                'edge'  => 'left',
                'align' => 'left',
                'width' => 620,
                'btn_tour_done' => __( 'Check out the current Pro add-on features' , 'thirstyaffiliates' ),
                'btn_tour_done_url' => 'https://thirstyaffiliates.com/pricing?utm_source=Free%20Plugin&utm_medium=Tour&utm_campaign=Pro%20Link'
            ),
        ) );
    }

    /**
     * Get current screen.
     *
     * @since 3.0.0
     * @access public
     *
     * @return array|void Current guide tour screen.
     */
    public function get_current_screen() {

        $screen    = get_current_screen();
        $tab       = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : '';

        if ( ! isset( $this->_screens[ $screen->id ] ) || empty( $this->_screens[ $screen->id ] ) )
            return;

        if ( $screen->id == 'thirstylink_page_thirsty-settings' ) {

            if( $tab && isset( $this->_screens[ $screen->id ][ $tab ] ) )
                return $this->_screens[ $screen->id ][ $tab ];
            elseif ( ! isset( $_GET[ 'tab' ] ) )
                return $this->_screens[ $screen->id ][ 'ta_general_settings' ];
            else
                return array();
        }

        return $this->_screens[ $screen->id ];
    }

    /**
     * Get all guide tour screens.
     *
     * @since 3.0.0
     * @access public
     *
     * @return array List of all guide tour screens.
     */
    public function get_screens() {

        return $this->_screens;
    }

    /**
     * AJAX close guided tour.
     *
     * @since 3.0.0
     * @access public
     */
    public function ajax_close_guided_tour() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( 'activate_plugins' ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta-close-guided-tour' , 'nonce' , false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        else {

            update_option( 'ta_guided_tour_status' , 'close' );
            $response = array( 'status' => 'success' );
        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
    }

    /**
     * Set the guided tour status option as 'open' on activation.
     *
     * @since 3.0.0
     * @access private
     */
    private function set_guided_tour_status_open() {

        if ( ! current_user_can('activate_plugins') || get_option( 'ta_guided_tour_status' ) )
            return;

        update_option( 'ta_guided_tour_status' , 'open' );
    }




    /*
    |--------------------------------------------------------------------------
    | Implemented Interface Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 3.0.0
     * @access public
     * @implements \ThirstyAffiliates\Interfaces\Activatable_Interface
     */
    public function activate() {

        $this->set_guided_tour_status_open();
    }

    /**
     * Method that houses codes to be executed on init hook.
     *
     * @since 3.0.0
     * @access public
     * @inherit ThirstyAffiliates\Interfaces\Initiable_Interface
     */
    public function initialize() {

        add_action( 'wp_ajax_ta_close_guided_tour' , array( $this , 'ajax_close_guided_tour' ) );
    }

    /**
     * Execute model.
     *
     * @implements \ThirstyAffiliates\Interfaces\Model_Interface
     *
     * @since 3.0.0
     * @access public
     */
    public function run() {

        if ( get_option( 'ta_guided_tour_status' ) !== 'open' )
            return;

        $this->define_guided_tour_pages();
    }
}
