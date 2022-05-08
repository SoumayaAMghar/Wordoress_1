<?php
namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Activatable_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Model that houses the logic of plugin Settings.
 * General Information:
 * The Ultimate Settings API ( Of course there will always be room for improvements ).
 * Basically we are using parts of the WordPress Settings API ( Only the backend processes )
 * But we are using our own custom render codebase.
 * The issue with WordPress Settings API is that we need to supply callbacks for each option field we add, so its not really extensible.
 * The data supplied on those callbacks are not ideal or not complete too to make a very extensible Settings API.
 * So what we did is, Register the settings and settings options in a way that we can utilize WordPress Settings API to handle them on the backend.
 * But use our own render codebase so we can make the Settings API very easy to extend.
 *
 * Important Note:
 * Be careful with default values. Default values only take effect if you haven't set the option yet. Meaning the option is not yet registered yet to the options db. ( get_option ).
 * Now if you hit save on a settings section with a field that have a default value, and you haven't changed that default value, Alas! it will still not register that option to the options db.
 * The reason is the default value and the current value of the options is the same.
 * Bug if you modify the value of the option, and hit save, this time, that option will be registered to the options db.
 * Then if you set back the value of that option, same as its default, it will still updated that option that is registered to the options db with that value.
 * Again remember, default value only kicks in if the option you are trying to get via get_option function is not yet registered to the options db.
 *
 * Important Note:
 * If the option can contain multiple values ( in array form , ex. checkbox and multiselect option types ), the default value must be in array form even if it only contains one value.
 *
 * Private Model.
 *
 * @since 3.0.0
 */
class Settings implements Model_Interface , Activatable_Interface , Initiable_Interface {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that holds the single main instance of Settings.
     *
     * @since 3.0.0
     * @access private
     * @var Settings
     */
    private static $_instance;

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
     * Property that houses all the supported option field types.
     *
     * @since 3.0.0
     * @access private
     * @var array
     */
    private $_supported_field_types;

    /**
     * Property that houses all the supported option field types that do not needed to be registered to the WP Settings API.
     * Ex. of this are field types that are for decorative purposes only and has no underlying option data to save.
     * Another is type of option fields that perform specialized task and does not need any underlying data to be saved.
     *
     * @since 3.0.0
     * @access public
     */
    private $_skip_wp_settings_registration;

    /**
     * Property that houses all the registered settings sections.
     *
     *
     * @since 3.0.0
     * @access private
     * @var array
     */
    private $_settings_sections;

    /**
     * Property that houses all the registered options of the registered settings sections.
     *
     * @since 3.0.0
     * @access private
     * @var array
     */
    private $_settings_section_options;

    /**
     * Property that holds all plugin options that can be exported.
     *
     * @since 3.0.0
     * @access private
     * @var array
     */
    private $_exportable_options;

    /**
     * Property that holds list of post update function callbacks per option if there are any.
     *
     * @since 3.0.0
     * @access private
     * @var array
     */
    private $_post_update_option_cbs = array();




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
     * @return Settings
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }

    /**
     * Register admin interfaces.
     *
     * @since 3.3.2
     * @access public
     *
     * @param array $interfaces List of admin interfaces.
     * @return array Filtered list of admin interfaces.
     */
    public function register_admin_interfaces( $interfaces ) {

        $interfaces[ 'thirstylink_page_thirsty-settings' ] = apply_filters( 'ta_settings_admin_interface' , array(
            'ta_general_settings' => 'manage_options',
            'ta_links_settings'   => 'manage_options',
            'ta_modules_settings' => 'manage_options',
            'ta_help_settings'    => 'manage_options',
        ) );

        $interfaces[ 'thirstylink_page_thirsty-settings' ][ 'ta_import_export_settings' ] = 'manage_options';

        return $interfaces;
    }

    /**
     * Register admin interfaces.
     *
     * @since 3.3.2
     * @access public
     *
     * @param array $interfaces List of menu items.
     * @return array Filtered list of menu items.
     */
    public function register_admin_menu_items( $menu_items ) {

        $menu_items[ 'thirsty-settings' ] = 'manage_options';
        return $menu_items;
    }




    /*
    |--------------------------------------------------------------------------
    | Initialize Settings
    |--------------------------------------------------------------------------
    */

    /**
     * Initialize the list of plugin built-in settings sections and its corresponding options.
     *
     * @since 3.0.0
     * @since 3.3.1 Add support for URL input type.
     * @since 3.3.5 Hide enhanced javascript redirect setting when statistics module is disabled.
     * @access public
     */
    public function init_settings_sections_and_options() {

        $this->_supported_field_types = apply_filters( 'ta_supported_field_types' , array(
            'text'                   => array( $this , 'render_text_option_field' ),
            'url'                    => array( $this , 'render_url_option_field' ),
            'number'                 => array( $this , 'render_number_option_field' ),
            'textarea'               => array( $this , 'render_textarea_option_field' ),
            'checkbox'               => array( $this , 'render_checkbox_option_field' ),
            'radio'                  => array( $this , 'render_radio_option_field' ),
            'select'                 => array( $this , 'render_select_option_field' ),
            'multiselect'            => array( $this , 'render_multiselect_option_field' ),
            'toggle'                 => array( $this , 'render_toggle_option_field' ),
            'editor'                 => array( $this , 'render_editor_option_field' ),
            'csv'                    => array( $this , 'render_csv_option_field' ),
            'key_value'              => array( $this , 'render_key_value_option_field' ),
            'link'                   => array( $this , 'render_link_option_field' ),
            'option_divider'         => array( $this , 'render_option_divider_option_field' ),
            'migration_controls'     => array( $this , 'render_migration_controls_option_field' ),
            'social_links'           => array( $this , 'render_social_links_option_field' ),
            'export_global_settings' => array( $this , 'render_export_global_settings_option_field' ),
            'import_global_settings' => array( $this , 'render_import_global_settings_option_field' )
        ) );

        $this->_skip_wp_settings_registration = apply_filters( 'ta_skip_wp_settings_registration' , array(
            'link',
            'option_divider',
            'migration_controls',
            'export_global_settings',
            'import_global_settings'
        ) );

        // Toggle options for settings with 'category' as an option.
        $toggle_cat_options = array(
            'yes'      => __( 'Yes' , 'thirstyaffiliates' ),
            'no'       => __( 'No' , 'thirstyaffiliates' ),
            'category' => __( 'Per category' , 'thirstyaffiliates' )
        );
        $all_categories = $this->_helper_functions->get_all_category_as_options();

        $this->_settings_sections = apply_filters( 'ta_settings_option_sections' , array(
            'ta_general_settings'       => array(
                                                'title' => __( 'General' , 'thirstyaffiliates' ) ,
                                                'desc'  => __( 'Settings that change the general behaviour of ThirstyAffiliates.' , 'thirstyaffiliates' )
                                            ),
            'ta_links_settings'         => array(
                                                'title' => __( 'Link Appearance' , 'thirstyaffiliates' ) ,
                                                'desc'  => __( 'Settings that specifically affect the behaviour & appearance of your affiliate links.' , 'thirstyaffiliates' )
                                            ),
            'ta_modules_settings'       => array(
                                                'title' => __( 'Modules' , 'thirstyaffiliates' ),
                                                'desc'  => __( 'This section allows you to turn certain parts of ThirstyAffiliates on or off. Below are the individual modules and features of the plugin that can be controlled.' , 'thirstyaffiliates' )
                                            ),
            'ta_import_export_settings' => array(
                                                'title' => __( 'Import/Export' , 'thirstyaffiliates' ),
                                                'desc'  => __( 'Import and Export global ThirstyAffiliates plugin settings from one site to another.' , 'thirstyaffiliates' )
                                            ),
            'ta_help_settings'          => array(
                                                'title' => __( 'Help' , 'thirstyaffiliates' ),
                                                'desc'  => __( 'Links to knowledge base and other utilities.' , 'thirstyaffiliates' )
                                            )
        ) );

        $this->_settings_section_options = apply_filters( 'ta_settings_section_options' , array(
            'ta_general_settings'       => apply_filters( 'ta_general_settings_options' , array(

                                                    array(
                                                        'id'        => 'ta_link_insertion_type',
                                                        'title'     => __( 'Default Link Insertion Type' , 'thirstyaffiliates' ),
                                                        'desc'      => __( "Determines the default link type when inserting a link using the quick search." , 'thirstyaffiliates' ),
                                                        'type'      => 'select',
                                                        'default'   => 'link',
                                                        'options'   => array(
                                                            'link'      => __( 'Link' , 'thirstyaffiliates' ),
                                                            'shortcode' => __( 'Shortcode' , 'thirstyaffiliates' ),
                                                        )
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_disable_cat_auto_select',
                                                        'title'     =>  __( 'Disable "uncategorized" category on save?' , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( 'If links are including categories in the URL then by default ThirstyAffiliates will add an "uncategorized" category to apply to non-categorised links during save. If you disable this, it allows you to have some links with categories in the URL and some without.' , 'thirstyaffiliates' ),
                                                        'type'      =>  'toggle'
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_disable_visual_editor_buttons',
                                                        'title'     =>  __( 'Disable buttons on the Visual editor?' , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( "Hide the ThirstyAffiliates buttons on the Visual editor." , 'thirstyaffiliates' ),
                                                        'type'      =>  'toggle'
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_disable_text_editor_buttons',
                                                        'title'     =>  __( 'Disable buttons on the Text/Quicktags editor?' , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( "Hide the ThirstyAffiliates buttons on the Text editor." , 'thirstyaffiliates' ),
                                                        'type'      =>  'toggle'
                                                    ),

                                                    array(
                                                        'id'           => 'ta_stats_trimer_set_point',
                                                        'title'        => __( 'Trim stats older than:' , 'thirstyaffiliates' ),
                                                        'desc'         => __( "months (Automatically clean the statistics database records older than a set point. Setting this to 0 will disable it)." , 'thirstyaffiliates' ),
                                                        'type'         => 'number',
                                                        'min'          => 0,
                                                        'default'      => 0,
                                                        'condition_cb' => function() { return get_option( 'ta_enable_stats_reporting_module' ) === 'yes'; }
                                                    ),

                                                    array(
                                                        'id'        => 'ta_browser_no_cache_301_redirect',
                                                        'title'     =>  __( "Don't cache 301 redirects? (server side redirects)" , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( "By default, browsers caches the 301 redirects. Enabling this option will tell the browser not to cache 301 redirects. Be aware that it is still up to the browser if it will cache it or not." , 'thirstyaffiliates' ),
                                                        'type'      =>  'toggle'
                                                    ),

                                                    array(
                                                        'id'        => 'ta_disable_ip_address_collection',
                                                        'title'     => __( "Disable IP address collection" , 'thirstyaffiliates' ),
                                                        'desc'      => __( "By default ThirstyAffiliates plugin collects visitor's IP address everytime they click an affiliate link as part of the statistics information. By checking this the IP address collection will be disabled, but other information will still be saved." , 'thirstyaffiliates' ),
                                                        'type'      => 'toggle'
                                                    ),

                                                    array(
                                                        'id'        => 'ta_disable_browser_device_collection',
                                                        'title'     => __( "Disable browser/device data collection" , 'thirstyaffiliates' ),
                                                        'desc'      => __( "As of version 3.4, by default ThirstyAffiliates plugin collects visitor's browser and device used everytime they click an affiliate link as part of the statistics information. By checking this the browser/device collection will be disabled, but other information will still be saved." , 'thirstyaffiliates' ),
                                                        'type'      => 'toggle'
                                                    ),

                                                    array(
                                                        'id'        => 'ta_blocked_bots',
                                                        'title'     => __( "Blocked bots" , 'thirstyaffiliates' ),
                                                        'desc'      => __( "By default ThirstyAffiliates blocks bots accessing your affiliate links to give you a more appropriate data in the report. Select bots, or enter new ones to block." , 'thirstyaffiliates' ),
                                                        'type'      => 'textarea',
                                                        'default'   => Plugin_Constants::DEFAULT_BLOCKED_BOTS,

                                                    ),

                                                    array(
                                                        'id'        => 'ta_enable_bot_crawl_blocker_script',
                                                        'title'     => __( "Enable Bot Crawl Blocker Script" , 'thirstyaffiliates' ),
                                                        'desc'      => __( "By enabling this setting, your affiliate links won't redirect for all the <em>blocked bots</em> set above and will send out a 403 forbidden error." , 'thirstyaffiliates' ),
                                                        'type'      => 'toggle'
                                                    )

                                              ) ),
            'ta_links_settings'         => apply_filters( 'ta_links_settings_options' , array(

                                                    array(
                                                        'id'      => 'ta_link_prefix',
                                                        'title'   => __( 'Link Prefix' , 'thirstyaffiliates' ),
                                                        'desc'    => sprintf( __( "The prefix that comes before your cloaked link's slug. <br>eg. %s/<strong>recommends</strong>/your-affiliate-link-name.<br><br><b>Warning :</b> Changing this setting after you've used links in a post could break those links. Be careful!" , 'thirstyaffiliates' ) , home_url() ),
                                                        'type'    => 'select',
                                                        'default' => 'recommends',
                                                        'options' => array(
                                                            'custom'     => '-- custom --',
                                                            'recommends' => 'recommends',
                                                            'link'       => 'link',
                                                            'go'         => 'go',
                                                            'review'     => 'review',
                                                            'product'    => 'product',
                                                            'suggests'   => 'suggests',
                                                            'follow'     => 'follow',
                                                            'endorses'   => 'endorses',
                                                            'proceed'    => 'proceed',
                                                            'fly'        => 'fly',
                                                            'goto'       => 'goto',
                                                            'get'        => 'get',
                                                            'find'       => 'find',
                                                            'act'        => 'act',
                                                            'click'      => 'click',
                                                            'move'       => 'move',
                                                            'offer'      => 'offer',
                                                            'run'        => 'run'
                                                        )
                                                    ),

                                                    array(
                                                        'id'             => 'ta_link_prefix_custom',
                                                        'title'          => __( 'Custom Link Prefix' , 'thirstyaffiliates' ),
                                                        'desc'           => __( 'Enter your preferred link prefix.' , 'thirstyaffiliates' ),
                                                        'type'           => 'text'
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_show_cat_in_slug',
                                                        'title'     =>  __( 'Link Category in URL?' , 'thirstyaffiliates' ),
                                                        'desc'      =>  sprintf( __( "Shows the primary selected category in the url. eg. %s/recommends/<strong>link-category</strong>/your-affiliate-link-name.<br><br><b>Warning :</b> Changing this setting after you've used links in a post could break those links. Be careful!"  , 'thirstyaffiliates' ) , home_url() ),
                                                        'type'      =>  'toggle'
                                                    ),

                                                    array(
                                                        'id'           => 'ta_enable_javascript_frontend_redirect',
                                                        'title'        => __( "Enable Enhanced Javascript Redirect on Frontend" , 'thirstyaffiliates' ),
                                                        'desc'         => __( "By default affiliate links are redirected on the server side. Enabling this will set all affiliate links to be redirected via javascript on your website's frontend. This will then improve the accuracy of the link performance report (This will only work when <strong>Statistics</strong> module is enabled)." , 'thirstyaffiliates' ),
                                                        'type'         => 'toggle',
                                                        'condition_cb' => function() { return get_option( 'ta_enable_stats_reporting_module' ) === 'yes'; }
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_link_redirect_type',
                                                        'title'     =>  __( 'Link Redirect Type (server side redirects)' , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( "This is the type of redirect ThirstyAffiliates will use to redirect the user to your affiliate link." , 'thirstyaffiliates' ),
                                                        'type'      =>  'radio',
                                                        'options'   =>  $this->_constants->REDIRECT_TYPES(),
                                                        'default'   =>  '302'
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_no_follow',
                                                        'title'     =>  __( 'Use no follow on links? (server side redirects)' , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( "Add the nofollow attribute to links so search engines don't index them." , 'thirstyaffiliates' ),
                                                        'type'      =>  'select',
                                                        'options'   =>  $toggle_cat_options,
                                                        'default'   =>  'no',
                                                        'class'     =>  'toggle-cat'
                                                    ),

                                                    array(
                                                        'id'           =>  'ta_no_follow_category',
                                                        'title'        =>  __( 'No follow categories (server side redirects)' , 'thirstyaffiliates' ),
                                                        'desc'         =>  __( "The links assigned to the selected category will be set as \"no follow\"." , 'thirstyaffiliates' ),
                                                        'type'         =>  'multiselect',
                                                        'options'      =>  $all_categories,
                                                        'default'      =>  array(),
                                                        'placeholder'  => __( 'Select category...' , 'thirstyaffiliates' ),
                                                        'class'        => 'toggle-cat-select toggle-cat-ta_no_follow',
                                                        'required'     => true,
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_new_window',
                                                        'title'     =>  __( 'Open links in new window?' , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( "Make links open in a new browser tab by default." , 'thirstyaffiliates' ),
                                                        'type'      =>  'select',
                                                        'options'   =>  $toggle_cat_options,
                                                        'default'   =>  'no',
                                                        'class'     =>  'toggle-cat'
                                                    ),

                                                    array(
                                                        'id'           =>  'ta_new_window_category',
                                                        'title'        =>  __( 'New window categories' , 'thirstyaffiliates' ),
                                                        'desc'         =>  __( "The links assigned to the selected category will be set as \"new window\"." , 'thirstyaffiliates' ),
                                                        'type'         =>  'multiselect',
                                                        'options'      =>  $all_categories,
                                                        'default'      =>  array(),
                                                        'placeholder'  => __( 'Select category...' , 'thirstyaffiliates' ),
                                                        'class'        => 'toggle-cat-select toggle-cat-ta_new_window',
                                                        'required'     => true,
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_pass_query_str',
                                                        'title'     =>  __( 'Pass query strings to destination url?' , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( "Enabling this option will pass all of the query strings present after the cloaked url to the destination url automatically when redirecting." , 'thirstyaffiliates' ),
                                                        'type'      =>  'select',
                                                        'options'   =>  $toggle_cat_options,
                                                        'default'   =>  'no',
                                                        'class'     =>  'toggle-cat',
                                                    ),

                                                    array(
                                                        'id'           =>  'ta_pass_query_str_category',
                                                        'title'        =>  __( 'Pass query strings categories' , 'thirstyaffiliates' ),
                                                        'desc'         =>  __( "The links assigned to the selected category will be set as \"pass query strings\"." , 'thirstyaffiliates' ),
                                                        'type'         =>  'multiselect',
                                                        'options'      =>  $all_categories,
                                                        'default'      =>  array(),
                                                        'placeholder'  => __( 'Select category...' , 'thirstyaffiliates' ),
                                                        'class'        => 'toggle-cat-select toggle-cat-ta_pass_query_str',
                                                        'required'     => true,
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_additional_rel_tags',
                                                        'title'     =>  __( 'Additional rel attribute tags' , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( "Allows you to add extra tags into the rel= attribute when links are inserted." , 'thirstyaffiliates' ),
                                                        'type'      =>  'text'
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_additional_css_classes',
                                                        'title'     =>  __( 'Additional CSS classes' , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( "Allows you to add extra CSS classes when links are inserted." , 'thirstyaffiliates' ),
                                                        'type'      =>  'text'
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_disable_thirsty_link_class',
                                                        'title'     =>  __( 'Disable ThirstyAffiliates CSS classes?' , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( 'To help with styling a CSS class called "thirstylink" is added links on insertion.<br>Likewise the "thirstylinkimg" class is added to images when using the image insertion type. This option disables the addition these CSS classes.' , 'thirstyaffiliates' ),
                                                        'type'      =>  'toggle'
                                                    ),

                                                    array(
                                                        'id'        =>  'ta_disable_title_attribute',
                                                        'title'     =>  __( 'Disable title attribute on link insertion?' , 'thirstyaffiliates' ),
                                                        'desc'      =>  __( "Links are automatically output with a title html attribute (by default this shows the title of the affiliate link).<br>This option disables the output of the title attribute on your links." , 'thirstyaffiliates' ),
                                                        'type'      =>  'toggle'
                                                    ),

                                                    array(
                                                        'id'           =>  'ta_category_to_uncloak',
                                                        'title'        =>  __( 'Select Category to Uncloak' , 'thirstyaffiliates' ),
                                                        'desc'         =>  __( "The links assigned to the selected category will be uncloaked." , 'thirstyaffiliates' ),
                                                        'type'         =>  'multiselect',
                                                        'options'      =>  $all_categories,
                                                        'default'      =>  array(),
                                                        'condition_cb' => function() { return get_option( 'ta_uncloak_link_per_link' ) === 'yes'; },
                                                        'placeholder'  => __( 'Select category...' , 'thirstyaffiliates' )
                                                    )

                                              ) ),

            'ta_modules_settings'       => apply_filters( 'ta_modules_settings_options' , array(

                                        array(
                                            'id'      => 'ta_enable_stats_reporting_module',
                                            'title'   => __( 'Statistics' , 'thirstyaffiliates' ),
                                            'desc'    => __( "When enabled, ThirstyAffiliates will collect click statistics information about visitors that click on your affiliate links. Also adds a new Reports section." , 'thirstyaffiliates' ),
                                            'type'    => 'toggle',
                                            'default' => 'yes'
                                        ),

                                        array(
                                            'id'      => 'ta_enable_link_fixer',
                                            'title'   => __( 'Link Fixer' , 'thirstyaffiliates' ),
                                            'desc'    => __( "Link Fixer is a tiny piece of javascript code that runs on the frontend of your site to fix any outdated/broken affiliate links it detects. It's cache-friendly and runs after page load so it doesn't affect the rendering of content. Changed the settings on your site recently? Enabling Link Fixer means you don't need to update all your previously inserted affiliate links one by one – your visitors will never see an out of date affiliate link again." , 'thirstyaffiliates' ),
                                            'type'    => 'toggle',
                                            'default' => 'yes',
                                        ),

                                        array(
                                            'id'      => 'ta_uncloak_link_per_link',
                                            'title'   => __( 'Uncloak Links' , 'thirstyaffiliates' ),
                                            'desc'    => __( "Uncloak Links is a feature to allow uncloaking of specific links on your site. It replaces the cloaked url with the actual destination url which is important for compatibility with some affiliate program with stricter terms (such as Amazon Associates). Once enabled, you will see a new Uncloak Link checkbox on the affiliate link edit screen. It also introduces a new setting under the Links tab for uncloaking whole categories.<br><br><b>Warning : </b>For this feature to work, the <strong>Link Fixer</strong> module needs to be turned on." , 'thirstyaffiliates' ),
                                            'type'    => 'toggle',
                                            'default' => 'no',
                                        )

                                    ) ),
            'ta_import_export_settings' => apply_filters( 'ta_import_export_settings_options' , array(

                                                array(
                                                    'id'          => 'ta_import_settings',
                                                    'title'       => __( 'Import Global Settings' , 'thirstyaffiliates' ),
                                                    'type'        => 'import_global_settings',
                                                    'placeholder' => __( 'Paste settings string here...' , 'thirstyaffiliates' )
                                                ),

                                                array(
                                                    'id'    => 'ta_export_settings',
                                                    'title' => __( 'Export Global Settings' , 'thirstyaffiliates' ),
                                                    'type'  => 'export_global_settings'
                                                )

                                            ) ),
            'ta_help_settings'          => apply_filters( 'ta_help_settings_options' , array(

                                                array(
                                                    'id'    => 'ta_knowledge_base_divider', // Even though no option is really saved, we still add id, for the purpose of later when extending this section options, they can search for this specific section divider during array loop
                                                    'title' => __( 'Knowledge Base' , 'thirstyaffiliates' ),
                                                    'type'  => 'option_divider'
                                                ),

                                                array(
                                                    'title'     => __( 'Documentation' , 'thirstyaffiliates' ),
                                                    'type'      => 'link',
                                                    'link_url'  => 'https://thirstyaffiliates.com/knowledge-base/?utm_source=Free%20Plugin&utm_medium=Help&utm_campaign=Knowledge%20Base%20Link',
                                                    'link_text' => __( 'Knowledge Base' , 'thirstyaffiliates' ),
                                                    'desc'      => __( 'Guides, troubleshooting, FAQ and more.' , 'thirstyaffiliates' ),
                                                    'id'        => 'ta_kb_link',
                                                ),

                                                array(
                                                    'title'     => __( 'Our Blog' , 'thirstyaffiliates' ),
                                                    'type'      => 'link',
                                                    'link_url'  => 'https://thirstyaffiliates.com/blog?utm_source=Free%20Plugin&utm_medium=Help&utm_campaign=Blog%20Link',
                                                    'link_text' => __( 'ThirstyAffiliates Blog' , 'thirstyaffiliates' ),
                                                    'desc'      => __( 'Learn & grow your affiliate marketing – covering increasing sales, generating traffic, optimising your affiliate marketing, interviews & case studies.' , 'thirstyaffiliates' ),
                                                    'id'        => 'ta_blog_link',
                                                ),

                                                array(
                                                    'title' => __( 'Join the Community' , 'thirstyaffiliates' ),
                                                    'type'  => 'social_links',
                                                    'id'    => 'ta_social_links'
                                                ),

                                                array(
                                                    'id'    => 'ta_other_utilities_divider', // Even though no option is really saved, we still add id, for the purpose of later when extending this section options, they can search for this specific section divider during array loop
                                                    'title' => __( 'Other Utilities' , 'thirstyaffiliates' ),
                                                    'type'  => 'option_divider'
                                                ),

                                                array(
                                                    'title' => __( 'Migrate Old Data' , 'thirstyaffiliates' ),
                                                    'type'  => 'migration_controls',
                                                    'desc'  => __( 'Migrate old ThirstyAffiliates version 2 data to new version 3 data model.' , 'thirstyaffiliates' ),
                                                    'id'    => 'ta_migrate_old_data'
                                                )

                                            ) )

        ) );

        // Get all the exportable options
        foreach ( $this->_settings_section_options as $section_id => $section_options ) {

            foreach ( $section_options as $option ) {

                if ( in_array( $option[ 'type' ] , $this->_skip_wp_settings_registration )  )
                    continue;

                $this->_exportable_options[ $option[ 'id' ] ] = isset( $option[ 'default' ] ) ? $option[ 'default' ] : '';

                if ( isset( $option[ 'post_update_cb' ] ) && is_callable( $option[ 'post_update_cb' ] ) )
                    add_action( 'update_option_' . $option[ 'id' ] , $option[ 'post_update_cb' ] , 10 , 3 );

            }

        }

    }

    /**
     * Register Settings Section and Options Group to WordPress Settings API.
     *
     * @since 3.0.0
     * @access public
     */
    public function register_settings_section_and_options_group() {

        foreach ( $this->_settings_sections as $section_id => $section_data ) {

            add_settings_section(
                $section_id,                   // Settings Section ID
                $section_data[ 'title' ],      // Settings Section Title
                function() {},                 // Callback. Intentionally Left Empty. We Will Handle UI Rendering.
                $section_id . '_options_group' // Options Group
            );

        }

    }

    /**
     * Register Settings Section Options to WordPress Settings API.
     *
     * @since 3.0.0
     * @access public
     */
    public function register_settings_section_options() {

        foreach ( $this->_settings_section_options as $section_id => $section_options ) {

            foreach ( $section_options as $option ) {

                if ( !array_key_exists( $option[ 'type' ] , $this->_supported_field_types ) || in_array( $option[ 'type' ] , $this->_skip_wp_settings_registration ) )
                    continue;

                // Register The Option To The Options Group It Is Scoped With
                add_settings_field(
                    $option[ 'id' ],                // Option ID
                    $option[ 'title' ],             // Option Title
                    function() {},                  // Render Callback. Intentionally Left Empty. We Will Handle UI Rendering.
                    $section_id . '_options_group', // Options Group
                    $section_id                     // Settings Section ID
                );

                // Register The Actual Settings Option
                $args = array();

                if ( isset( $option[ 'data_type' ] ) )
                    $args[ 'type' ] = $option[ 'data_type' ];

                if ( isset( $option[ 'desc' ] ) )
                    $args[ 'description' ] = $option[ 'desc' ];

                if ( isset( $option[ 'sanitation_cb' ] ) && is_callable( $option[ 'sanitation_cb' ] ) )
                    $args[ 'sanitize_callback' ] = $option[ 'sanitation_cb' ];

                if ( isset( $option[ 'show_in_rest' ] ) )
                    $args[ 'show_in_rest' ] = $option[ 'show_in_rest' ];

                if ( isset( $option[ 'default' ] ) )
                    $args[ 'default' ] = $option[ 'default' ]; // Important Note: This will be used on "get_option" function automatically if the current option is not registered yet to the options db.

                register_setting( $section_id . '_options_group' , $option[ 'id' ] , $args );

            }

        }

    }

    /**
     * Initialize Plugin Settings API.
     * We register the plugin settings section and settings section options to WordPress Settings API.
     * We let WordPress Settings API handle the backend stuff.
     * We will handle the UI rendering.
     *
     * @since 3.0.0
     * @access public
     */
    public function init_plugin_settings() {

        $this->init_settings_sections_and_options();
        $this->register_settings_section_and_options_group();
        $this->register_settings_section_options();

    }

    /**
     * Add settings page.
     *
     * @since 3.0.0
     * @since 3.2.2 Access to the settings page will now be controlled by the plugin. see Bootstrap::admin_interface_visibility.
     * @access public
     */
    public function add_settings_page() {

        if ( ! current_user_can( 'edit_posts' ) ) return;

        add_submenu_page(
            'edit.php?post_type=thirstylink',
            __( 'ThirstyAffiliates Settings' , 'thirstyaffiliates' ),
            __( 'Settings' , 'thirstyaffiliates' ),
            'read',
            'thirsty-settings',
            array( $this, 'view_settings_page' )
        );

        if ( ! is_plugin_active( 'thirstyaffiliates-pro/thirstyaffiliates-pro.php' ) ) {
            add_submenu_page(
                'edit.php?post_type=thirstylink',
                __( 'Import CSV' , 'thirstyaffiliates' ),
                __( 'Import CSV' , 'thirstyaffiliates' ),
                'manage_options',
                'thirsty_import',
                array( $this , 'import_page' )
            );

            add_submenu_page(
                'edit.php?post_type=thirstylink',
                __( 'Export CSV' , 'thirstyaffiliates' ),
                __( 'Export CSV' , 'thirstyaffiliates' ),
                'manage_options',
                'thirsty_export',
                array( $this , 'export_page' )
            );

            add_submenu_page(
                'edit.php?post_type=thirstylink',
                __( 'Amazon Import' , 'thirstyaffiliates' ),
                __( 'Amazon Import' , 'thirstyaffiliates' ),
                'manage_options',
                'amazon_import',
                array( $this , 'amazon_import_page' )
            );

            add_submenu_page(
                'edit.php?post_type=thirstylink',
                __( 'Event Notifications' , 'thirstyaffiliates' ),
                __( 'Event Notifications' , 'thirstyaffiliates' ),
                'manage_options',
                'thirsty_event_notifications',
                array( $this , 'event_notifications_page' )
            );
        }

    }

    public function import_page() {
        include_once $this->_constants->VIEWS_ROOT_PATH() . 'importer/importer.php';
    }

    public function export_page() {
        include_once $this->_constants->VIEWS_ROOT_PATH() . 'exporter/exporter.php';
    }

    public function amazon_import_page() {
        include_once $this->_constants->VIEWS_ROOT_PATH() . 'amazon-import/amazon-import.php';
    }

    public function event_notifications_page() {
        include_once $this->_constants->VIEWS_ROOT_PATH() . 'event-notifications/event-notifications.php';
    }

    /**
     * Settings page view.
     *
     * @since 3.0.0
     * @since 3.3.1 Add a new <div> to wrap the side navigation and form. Add additional action hooks.
     * @access public
     */
    public function view_settings_page() {
        ?>

        <div class="wrap ta-settings">

            <h2><?php _e( 'ThirstyAffiliates Settings' , 'thirstyaffiliates' ); ?></h2>

            <?php
            settings_errors(); // Show notices based on the outcome of the settings save action
            $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'ta_general_settings';
            ?>

            <div class="ta-settings-wrapper">

                <?php do_action( 'ta_before_settings_sidenav' ); ?>

                <h2 class="nav-tab-wrapper">
                    <?php foreach ( $this->_settings_sections as $section_key => $section_data ) { ?>
                        <a href="?post_type=thirstylink&page=thirsty-settings&tab=<?php echo $section_key; ?>" class="nav-tab <?php echo $active_tab == $section_key ? 'nav-tab-active' : ''; ?> <?php echo $section_key; ?>"><?php echo $section_data[ 'title' ]; ?></a>
                    <?php }  ?>

                    <?php if ( ! $this->_helper_functions->is_plugin_active( 'thirstyaffiliates-pro/thirstyaffiliates-pro.php' ) ) : ?>
                        <a class="tapro-upgrade nav-tab" href="https://thirstyaffiliates.com/pricing/?utm_source=Free%20Plugin&utm_medium=Pro&utm_campaign=Admin%20Settings" target="_blank"><?php _e( 'Pro Features →' , 'thirstyaffiliates' ); ?></a>
                    <?php endif; ?>
                </h2>

                <?php do_action( 'ta_before_settings_form' ); ?>

                <div class="ta-settings-form">

                    <form method="post" action="options.php" enctype="multipart/form-data">

                        <?php
                        $this->render_settings_section_nonces( $active_tab );
                        $this->render_settings_section_header( $active_tab );
                        $this->render_settings_section_fields( $active_tab );
                        ?>

                    </form>
                </div>

                <?php do_action( 'ta_after_settings_form' ); ?>

            </div>

        </div><!--wrap-->

        <?php
    }

    /**
     * Render all necessary nonces for the current settings section.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string $active_tab Currently active settings section.
     */
    public function render_settings_section_nonces( $active_tab ) {

        settings_fields( $active_tab . '_options_group' );

    }

    /**
     * Render the current settings section header markup.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string $active_tab Currently active settings section.
     */
    public function render_settings_section_header( $active_tab ) {

        if ( ! isset( $this->_settings_sections[ $active_tab ] ) )
            return;

        ?>

        <h2><?php echo $this->_settings_sections[ $active_tab ][ 'title' ]; ?></h2>
        <p class="desc"><?php echo $this->_settings_sections[ $active_tab ][ 'desc' ]; ?></p>

        <?php

    }

    /**
     * Render an option as a hidden field.
     * We do this if that option's condition callback failed.
     * We don't show it for the end user, but we still need to pass on the form the current data so we don't lost it.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_option_as_hidden_field( $option ) {

        if ( in_array( $option[ 'type' ] , $this->_skip_wp_settings_registration ) )
            return; // This is a decorative option type, no need to render this as a hidden field

        ?>

        <input type="hidden" name="<?php echo esc_attr( $option[ 'id' ] ); ?>" value="<?php echo get_option( $option[ 'id' ] , '' ); ?>">

        <?php

    }

    /**
     * Render settings section option fields.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string $active_tab Currently active settings section.
     */
    public function render_settings_section_fields( $active_tab ) {

        $no_save_sections = apply_filters( 'ta_render_settings_no_save_section' , array(
            'ta_import_export_settings'
        ) );
        ?>

        <table class="form-table">
            <tbody>
                <?php
                foreach ( $this->_settings_section_options as $section_id => $section_options ) {

                    if ( $section_id !== $active_tab )
                        continue;

                    foreach ( $section_options as $option ) {

                        if ( isset( $option[ 'condition_cb' ] ) && is_callable( $option[ 'condition_cb' ] ) && !$option[ 'condition_cb' ]() )
                            $this->render_option_as_hidden_field( $option ); // Option condition failed. Render it as a hidden field so its value is preserved
                        else
                            $this->_supported_field_types[ $option[ 'type' ] ]( $option );

                    }

                }
                ?>
            </tbody>
        </table>

        <?php if ( ! in_array( $active_tab , $no_save_sections ) ) : ?>
            <p class="submit">
                <input name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' , 'thirstyaffiliates' ); ?>" type="submit">
            </p>
        <?php endif;

    }




    /*
    |--------------------------------------------------------------------------
    | Option Field Views
    |--------------------------------------------------------------------------
    */

    /**
     * Render 'text' type option field.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_text_option_field( $option ) {

        ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">

            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <input
                    type  = "text"
                    name  = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    id    = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    class = "option-field <?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>"
                    style = "<?php echo isset( $option[ 'style' ] ) ? $option[ 'style' ] : 'width: 360px;'; ?>"
                    value = "<?php echo get_option( $option[ 'id' ] ); ?>" >
                <br>
                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>
            </td>

        </tr>

        <?php

    }

    /**
     * Render 'url' type option field.
     *
     * @since 3.3.1
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_url_option_field( $option ) {

        ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">

            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <input
                    type  = "url"
                    name  = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    id    = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    class = "option-field <?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>"
                    style = "<?php echo isset( $option[ 'style' ] ) ? $option[ 'style' ] : 'width: 360px;'; ?>"
                    value = "<?php echo get_option( $option[ 'id' ] ); ?>" >
                <br>
                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>
            </td>

        </tr>

        <?php

    }

    /**
     * Render 'text' type option field.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_number_option_field( $option ) {

        ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">

            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <input
                    type  = "number"
                    name  = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    id    = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    class = "option-field <?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>"
                    style = "<?php echo isset( $option[ 'style' ] ) ? $option[ 'style' ] : 'width: 100px;'; ?>"
                    value = "<?php echo get_option( $option[ 'id' ] ); ?>"
                    min   = "<?php echo isset( $option[ 'min' ] )  ? $option[ 'min' ] : 0; ?>"
                    max   = "<?php echo isset( $option[ 'max' ] )  ? $option[ 'max' ] : ''; ?>" >
                <span><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></span>
            </td>

        </tr>

        <?php

    }

    /**
     * Render 'textarea' type option field.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_textarea_option_field( $option ) {

        ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">

            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <textarea
                    name  = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    id    = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    class = "option-field <?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>"
                    cols  = "60"
                    rows  = "8"
                    style = "<?php echo isset( $option[ 'style' ] ) ? $option[ 'style' ] : 'width: 360px;'; ?>"><?php echo get_option( $option[ 'id' ] ); ?></textarea>
                <br />
                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>
            </td>

        </tr>

        <?php

    }

    /**
     * Render 'checkbox' type option field.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_checkbox_option_field( $option ) {

        $option_val = get_option( $option[ 'id' ] );
        if ( !is_array( $option_val ) )
            $option_val = array(); ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <?php foreach ( $option[ 'options' ] as $opt_key => $opt_text  ) {

                    $opt_key_class = str_replace( " " , "-" , $opt_key ); ?>

                    <input
                        type  = "checkbox"
                        name  = "<?php echo esc_attr( $option[ 'id' ] ); ?>[]"
                        id    = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                        class = "option-field <?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>"
                        style = "<?php echo isset( $option[ 'style' ] ) ? $option[ 'style' ] : ''; ?>"
                        value = "<?php echo $opt_key; ?>"
                        <?php echo in_array( $opt_key , $option_val ) ? 'checked' : ''; ?>>

                    <label class="<?php echo esc_attr( $option[ 'id' ] ); ?>"><?php echo $opt_text; ?></label>
                    <br>

                <?php } ?>

                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>
            </td>

            <script>
                jQuery( document ).ready( function( $ ) {

                    $( "label.<?php echo esc_attr( $option[ 'id' ] ); ?>" ).on( "click" , function() {

                        $( this ).prev( "input[type='checkbox']" ).trigger( "click" );

                    } );

                } );
            </script>
        </tr>

        <?php

    }

    /**
     * Render 'radio' type option field.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_radio_option_field( $option ) {

        $option_val = get_option( $option[ 'id' ] ); ?>


        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <?php foreach ( $option[ 'options' ] as $opt_key => $opt_text ) { ?>

                    <input
                        type  = "radio"
                        name  = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                        id    = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                        class = "option-field <?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>"
                        style = "<?php echo isset( $option[ 'style' ] ) ? $option[ 'style' ] : ''; ?>"
                        value = "<?php echo $opt_key; ?>"
                        <?php echo $opt_key == $option_val ? 'checked' : ''; ?>>

                    <label class="<?php echo esc_attr( $option[ 'id' ] ); ?>"><?php echo $opt_text; ?></label>
                    <br>

                <?php } ?>

                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>
            </td>

            <script>
                jQuery( document ).ready( function( $ ) {

                    $( "label.<?php echo esc_attr( $option[ 'id' ] ); ?>" ).on( "click" , function() {

                        $( this ).prev( "input[type='radio']" ).trigger( "click" );

                    } );

                } );
            </script>
        </tr>

        <?php

    }

    /**
     * Render 'select' type option field.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_select_option_field( $option ) {

        $option_value = $this->_helper_functions->get_option( $option[ 'id' ] , $option[ 'default' ] ); ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?> <?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>">
                <select
                    data-placeholder = "<?php echo isset( $option[ 'placeholder' ] ) ? $option[ 'placeholder' ] : 'Choose an option...' ; ?>"
                    name  = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    id    = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    class = "option-field selectize-select <?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>"
                    style = "<?php echo isset( $option[ 'style' ] ) ? $option[ 'style' ] : 'width:360px;'; ?>">

                    <?php foreach ( $option[ 'options' ] as $opt_key => $opt_text ) { ?>

                        <option value="<?php echo $opt_key; ?>" <?php selected( $option_value , $opt_key ); ?>><?php echo $opt_text; ?></option>

                    <?php } ?>
                </select>
                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>
            </td>

            <script>
                jQuery( document ).ready( function( $ ) {

                    $( '#<?php echo esc_attr( $option[ 'id' ] ); ?>' ).selectize({
                        searchField : 'text'
                    });

                } );
            </script>
        </tr>

        <?php

    }

    /**
     * Render 'multiselect' type option field.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_multiselect_option_field( $option ) {

        $option_val = get_option( $option[ 'id' ] );
        if ( !is_array( $option_val ) )
            $option_val = array(); ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <select
                    multiple
                    data-placeholder = "<?php echo isset( $option[ 'placeholder' ] ) ? $option[ 'placeholder' ] : 'Choose an option...' ; ?>"
                    name  = "<?php echo esc_attr( $option[ 'id' ] ); ?>[]"
                    id    = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    class = "option-field selectize-select <?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>"
                    style = "<?php echo isset( $option[ 'style' ] ) ? $option[ 'style' ] : 'width:360px;'; ?>"
                    <?php echo isset( $option[ 'required' ] ) && $option[ 'required' ] ? 'required' : '' ?>>

                    <?php foreach ( $option[ 'options' ] as $opt_key => $opt_text ) { ?>

                        <option value="<?php echo $opt_key; ?>" <?php echo in_array( $opt_key , $option_val ) ? 'selected="selected"' : ''; ?>><?php echo $opt_text; ?></option>

                    <?php } ?>
                </select>
                <br>
                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>
            </td>

            <script>
                jQuery( document ).ready( function( $ ) {

                    $( '#<?php echo esc_attr( $option[ 'id' ] ); ?>' ).selectize({
                        plugins   : [ 'remove_button' , 'drag_drop' ]
                    });

                } );
            </script>
        </tr>

        <?php

    }

    /**
     * Render 'toggle' type option field.
     * Basically a single check box style option field.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_toggle_option_field( $option ) {

        ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <input
                    type  = "checkbox"
                    name  = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    id    = "<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    class = "option-field <?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>"
                    style = "<?php echo isset( $option[ 'style' ] ) ? $option[ 'style' ] : ''; ?>"
                    value = "yes"
                    <?php echo get_option( $option[ 'id' ] ) === "yes" ? 'checked' : ''; ?>>
                <label class="<?php echo esc_attr( $option[ 'id' ] ); ?>"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></label>
            </td>

            <script>
                jQuery( document ).ready( function( $ ) {

                    $( "label.<?php echo esc_attr( $option[ 'id' ] ); ?>" ).on( "click" , function() {

                        $( this ).prev( "input[type='checkbox']" ).trigger( "click" );

                    } );

                } );
            </script>
        </tr>

        <?php

    }

    /**
     * Render 'editor' type option field.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_editor_option_field( $option ) {

        $editor_value = html_entity_decode( get_option( $option[ 'id' ] ) ); ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <style type="text/css"><?php echo "div#wp-" . $option[ 'id' ] . "-wrap{ width: 70% !important; }"; ?></style>

                <?php wp_editor( $editor_value , $option[ 'id' ] , array(
                    'wpautop' 		=> true,
                    'textarea_name'	=> $option[ 'id' ],
                    'editor_height' => '300'
                ) ); ?>
                <br>
                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>
            </td>
        </tr>

        <?php

    }

    /**
     * Render 'csv' type option field.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_csv_option_field( $option ) {

        ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row"><?php echo $option[ 'title' ]; ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <input
                    type  = "text"
                    name  = "<?php echo $option[ 'id' ]; ?>"
                    id    = "<?php echo $option[ 'id' ]; ?>"
                    class = "option-field <?php echo isset( $option[ 'class' ] ) ? $option[ 'class' ] : ''; ?>"
                    style = "<?php echo isset( $option[ 'style' ] ) ? $option[ 'style' ] : 'width: 360px;'; ?>"
                    value = "<?php echo get_option( $option[ 'id' ] ); ?>" >
                <br>
                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>
            </td>

            <script>
                jQuery( document ).ready( function( $ ) {

                    $( '#<?php echo $option[ 'id' ]; ?>' ).selectize( {
                        plugins   : [ 'restore_on_backspace' , 'remove_button' , 'drag_drop' ],
                        delimiter : ',',
                        persist   : false,
                        create    : function( input ) {
                            return {
                                value: input,
                                text: input
                            }
                        }
                    } );

                } );
            </script>
        </tr>

        <?php

    }

    /**
     * Render 'key_value' type option field. Do not need to be registered to WP Settings API.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_key_value_option_field( $option ) {

        $option_value = get_option( $option[ 'id' ] );
        if ( !is_array( $option_value ) )
            $option_value =  array(); ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row"><?php echo $option[ 'title' ]; ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">

                <div class="key-value-fields-container" data-field-id="<?php echo $option[ 'id' ]; ?>">

                    <header>
                        <span class="key"><?php _e( 'Key' , 'thirstyaffiliates' ); ?></span>
                        <span class="value"><?php _e( 'Value' , 'thirstyaffiliates' ); ?></span>
                    </header>

                    <div class="fields">

                        <?php if ( empty( $option_value ) ) { ?>

                            <div class="data-set">
                                <input type="text" class="field key-field">
                                <input type="text" class="field value-field">
                                <div class="controls">
                                    <span class="control add dashicons dashicons-plus-alt" autocomplete="off"></span>
                                    <span class="control delete dashicons dashicons-dismiss" autocomplete="off"></span>
                                </div>
                            </div>

                        <?php } else {

                            foreach ( $option_value as $key => $val ) { ?>

                                <div class="data-set">
                                    <input type="text" class="field key-field" value="<?php echo $key; ?>">
                                    <input type="text" class="field value-field" value="<?php echo $val; ?>">
                                    <div class="controls">
                                        <span class="control add dashicons dashicons-plus-alt" autocomplete="off"></span>
                                        <span class="control delete dashicons dashicons-dismiss" autocomplete="off"></span>
                                    </div>
                                </div>

                            <?php }

                        } ?>

                    </div>

                </div>

                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>

            </td>
        </tr>

        <?php

    }

    /**
     * Render 'link' type option field. Do not need to be registered to WP Settings API.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_link_option_field( $option ) {

        ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>
            <td>
                <a id="<?php echo esc_attr( $option[ 'id' ] ); ?>" href="<?php echo $option[ 'link_url' ]; ?>" target="_blank"><?php echo $option[ 'link_text' ]; ?></a>
                <br>
                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>
            </td>
        </tr>

        <?php

    }

    /**
     * Render option divider. Decorative field. Do not need to be registered to WP Settings API.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_option_divider_option_field( $option ) {

        ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row" colspan="2">
                <h3><?php echo sanitize_text_field( $option[ 'title' ] ); ?></h3>
                <?php echo isset( $option[ 'markup' ] ) ? $option[ 'markup' ] : ''; ?>
            </th>
        </tr>

        <?php

    }

    /**
     * Render custom "migration_controls" field. Do not need to be registered to WP Settings API.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_migration_controls_option_field( $option ) {

        $database_processing = apply_filters( 'ta_database_processing' , true ); // Flag to determine if another application is processing the db. ex. data downgrade.
        $processing          = "";
        $disabled            = false;

        if ( get_option( Plugin_Constants::MIGRATION_COMPLETE_FLAG ) === 'no' ) {

            $processing = "-processing";
            $disabled   = true;

        } ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">

            <th scope="row" class="title_desc"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>

            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?> <?php echo $processing; ?>">

                <?php if ( !$database_processing ) { ?>

                    <p><?php _e( 'Another application is currently processing the database. Please wait for this to complete.' , 'thirstyaffiliates' ); ?></p>

                <?php } else { ?>

                    <input
                        <?php echo $disabled ? "disabled" : ""; ?>
                        type="button"
                        id="<?php echo esc_attr( $option[ 'id' ] ); ?>"
                        class="button button-primary"
                        style="<?php echo isset( $option[ 'style' ] ) ? esc_attr( $option[ 'style' ] ) : ''; ?>"
                        value="<?php _e( 'Migrate' , 'thirstyaffiliates' ); ?>">

                    <span class="spinner"></span>
                    <p class="status"><?php _e( 'Migrating data. Please wait...' , 'thirstyaffiliates' ); ?></p>

                <?php } ?>

                <br /><br />
                <p class="desc"><?php echo isset( $option[ 'desc' ] ) ? $option[ 'desc' ] : ''; ?></p>

            </td>

        </tr>

        <?php

    }

    /**
     * Render custom "social_links" field. Do not need to be registered to WP Settings API.
     *
     * @since 3.1.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_social_links_option_field( $option ) {

        ?>
        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>
            <td>
                <ul>
                    <li>
                        <a href="https://www.facebook.com/thirstyaffiliates/"><?php _e( 'Like us on Facebook' , 'thirstyaffiliates' ); ?></a>
                        <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fthirstyaffiliates&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;font=arial&amp;colorscheme=light&amp;action=like&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:21px; vertical-align: bottom;" allowTransparency="true"></iframe>
                    </li>
                    <li>
                        <a href="http://twitter.com/thirstyaff"><?php _e( 'Follow us on Twitter' , 'thirstyaffiliates' ); ?></a>
                        <a href="https://twitter.com/thirstyaff" class="twitter-follow-button" data-show-count="true" style="vertical-align: bottom;">Follow @thirstyaff</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs");</script>
                    </li>
                    <li>
                        <a href="https://www.linkedin.com/company-beta/2928598/"><?php _e( 'Follow us on Linkedin' , 'thirstyaffiliates' ); ?></a>
                    </li>
                    <li>
                        <a href="https://thirstyaffiliates.com/affiliates?utm_source=Free%20Plugin&utm_medium=Help&utm_campaign=Affiliates%20Link" target="_blank"><?php _e( 'Join Our Affiliate Program' , 'thirstyaffiliates' ); ?></a>
                        <?php _e( '(up to 30% commisions)' , 'thirstyaffiliates' ); ?>
                    </li>
                </ul>
            </td>
        </tr>
        <?php
    }

    /**
     * Render custom "export_global_settings" field. Do not need to be registered to WP Settings API.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_export_global_settings_option_field( $option ) {

        $global_settings_string = $this->get_global_settings_string(); ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row" class="title_desc"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></th>
            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <textarea
                    name="<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    id="<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    style="<?php echo isset( $option[ 'style' ] ) ? esc_attr( $option[ 'style' ] ) : ''; ?>"
                    class="<?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>"
                    placeholder="<?php echo isset( $option[ 'placeholder' ] ) ? esc_attr( $option[ 'placeholder' ] ) : ''; ?>"
                    autocomplete="off"
                    readonly
                    rows="10"><?php echo $global_settings_string; ?></textarea>
                <div class="controls">
                    <a id="copy-settings-string" data-clipboard-target="#<?php echo esc_attr( $option[ 'id' ] ); ?>"><?php _e( 'Copy' , 'thirstyaffiliates' ); ?></a>
                </div>
            </td>
        </tr>

        <?php

    }

    /**
     * Render custom "import_global_settings" field. Do not need to be registered to WP Settings API.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $option Array of options data. May vary depending on option type.
     */
    public function render_import_global_settings_option_field( $option ) {

        ?>

        <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">
            <th scope="row" class="title_desc">
                <label for="<?php echo esc_attr( $option[ 'id' ] ); ?>"><?php echo sanitize_text_field( $option[ 'title' ] ); ?></label>
            </th>
            <td class="forminp forminp-<?php echo sanitize_title( $option[ 'type' ] ) ?>">
                <textarea
                    name="<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    id="<?php echo esc_attr( $option[ 'id' ] ); ?>"
                    style="<?php echo isset( $option[ 'style' ] ) ? esc_attr( $option[ 'style' ] ) : ''; ?>"
                    class="<?php echo isset( $option[ 'class' ] ) ? esc_attr( $option[ 'class' ] ) : ''; ?>"
                    placeholder="<?php echo esc_attr( $option[ 'placeholder' ] ); ?>"
                    autocomplete="off"
                    rows="10"></textarea>
                <p class="desc"><?php echo isset( $option[ 'description' ] ) ? $option[ 'description' ] : ''; ?></p>
                <div class="controls">
                    <span class="spinner"></span>
                    <input type="button" id="import-setting-button" class="button button-primary" value="<?php _e( 'Import Settings' , 'thirstyaffiliates' ); ?>">
                </div>
            </td>
        </tr>

        <?php

    }




    /*
    |--------------------------------------------------------------------------
    | "key_value" option field type helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Load styling relating to 'key_value' field type.
     *
     * @since 3.0.0
     * @access public
     */
    public function load_key_value_option_field_type_styling() {

        ?>

        <style>
            .key-value-fields-container header span {
                display: inline-block;
                font-weight: 600;
                margin-bottom: 8px;
            }
            .key-value-fields-container header .key {
                width: 144px;
            }
            .key-value-fields-container header .value {
                width: 214px;
            }
            .key-value-fields-container .fields .data-set {
                margin-bottom: 8px;
            }
            .key-value-fields-container .fields .data-set:last-child {
                margin-bottom: 0;
            }
            .key-value-fields-container .fields .data-set .key-field {
                width: 140px;
                margin-left: 0;
            }
            .key-value-fields-container .fields .data-set .value-field {
                width: 215px;
            }
            .key-value-fields-container .fields .data-set .controls {
                display: none;
            }
            .key-value-fields-container .fields .data-set:hover .controls {
                display: inline-block;
            }
            .key-value-fields-container .fields .data-set .controls .control {
                cursor: pointer;
            }
            .key-value-fields-container .fields .data-set .controls .add {
                color: green;
            }
            .key-value-fields-container .fields .data-set .controls .delete {
                color: red;
            }
        </style>

        <?php

    }

    /**
     * Load scripts relating to 'key_value' field type.
     *
     * @since 3.0.0
     * @access public
     */
    public function load_key_value_option_field_type_script() {

        ?>

        <script>

            jQuery( document ).ready( function( $ ) {

                // Hide the delete button if only 1 data set is available
                function init_data_set_controls() {

                    $( ".key-value-fields-container" ).each( function() {

                        if ( $( this ).find( ".data-set" ).length === 1 )
                            $( this ).find( ".data-set .controls .delete" ).css( "display" , "none" );
                        else
                            $( this ).find( ".data-set .controls .delete" ).removeAttr( "style" );

                    } );

                }

                init_data_set_controls();


                // Attach "add" and "delete" events
                $( ".key-value-fields-container" ).on( "click" , ".controls .add" , function() {

                    let $data_set = $( this ).closest( '.data-set' );

                    $data_set.after( "<div class='data-set'>" +
                                        "<input type='text' class='field key-field' autocomplete='off'> " +
                                        "<input type='text' class='field value-field' autocomplete='off'>" +
                                        "<div class='controls'>" +
                                            "<span class='control add dashicons dashicons-plus-alt'></span>" +
                                            "<span class='control delete dashicons dashicons-dismiss'></span>" +
                                        "</div>" +
                                    "</div>" );

                    init_data_set_controls();

                } );

                $( ".key-value-fields-container" ).on( "click" , ".controls .delete" , function() {

                    let $data_set = $( this ).closest( '.data-set' );

                    $data_set.remove();

                    init_data_set_controls();

                } );


                // Construct hidden fields for each of "key_value" option field types upon form submission
                $( "form" ).submit( function() {

                    $( ".key-value-fields-container" ).each( function() {

                        var $this        = $( this ),
                            field_id     = $this.attr( "data-field-id" ),
                            field_inputs = "";

                        $this.find( ".data-set" ).each( function() {

                            var $this       = $( this ),
                                key_field   = $.trim( $this.find( ".key-field" ).val() ),
                                value_field = $.trim( $this.find( ".value-field" ).val() );

                            if ( key_field !== "" && value_field !== "" )
                                field_inputs += "<input type='hidden' name='" + field_id + "[" + key_field + "]' value='" + value_field + "'>";

                        } );

                        $this.append( field_inputs );

                    } );

                } );

            } );

        </script>

        <?php

    }




    /*
    |--------------------------------------------------------------------------
    | Settings helper
    |--------------------------------------------------------------------------
    */

    /**
     * Get global settings string.
     *
     * @since 3.0.0
     * @access public
     *
     * @return \WP_Error|string WP_Error on error, Base 64 encoded serialized global plugin settings otherwise.
     */
    public function get_global_settings_string() {

        if ( !$this->_helper_functions->current_user_authorized() )
            return new \WP_Error( 'ta_unauthorized_operation_export_settings' , __( 'Unauthorized operation. Only authorized accounts can access global plugin settings string' , 'thirstyaffiliates' )  );

        $global_settings_arr = array();
        foreach ( $this->_exportable_options as $key => $default )
            $global_settings_arr[ $key ] = get_option( $key , $default );

        return base64_encode( serialize( $global_settings_arr ) );

    }

    /**
     * Import settings via ajax.
     *
     * @access public
     * @since 3.0.0
     */
    public function ajax_import_settings() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! $this->_helper_functions->current_user_authorized() )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta_import_settings', false, false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_POST[ 'ta_settings_string' ] ) || ! is_string( $_POST[ 'ta_settings_string' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Required parameter not passed' , 'thirstyaffiliates' ) );
        else {

            $result = $this->import_settings( filter_var( $_POST[ 'ta_settings_string' ] , FILTER_SANITIZE_STRING ) );

            if ( is_wp_error( $result ) )
                $response = array( 'status' => 'fail' , 'error_msg' => $result->get_error_message() );
            else
                $response = array( 'status' => 'success' , 'success_msg' => __( 'Settings successfully imported' , 'thirstyaffiliates' ) );

        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();

    }

    /**
     * Import settings from external global settings string.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string $global_settings_string Settings string.
     * @return \WP_Error | boolean WP_Error instance on failure, boolean true otherwise.
     */
    public function import_settings( $global_settings_string ) {

        $settings_arr = @unserialize( base64_decode( $global_settings_string ) );

        if ( !is_array( $settings_arr ) )
            return new \WP_Error( 'ta_invalid_global_settings_string' , __( 'Invalid global settings string' , 'thirstyaffiliates' ) , array( 'global_settings_string' => $global_settings_string ) );
        else {

            foreach ( $settings_arr as $key => $val ) {

                if ( !array_key_exists( $key , $this->_exportable_options ) )
                    continue;

                update_option( $key , $val );

            }

            return true;

        }

    }

    /**
     * Post update option callback for link prefix options.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string $old_value Old option value before the update.
     * @param string $value     New value saved.
     * @param string $option    Option id.
     */
    public function link_prefix_post_update_callback( $value , $old_value , $option ) {

        if ( $option === 'ta_link_prefix' && $value === 'custom' )
            return $value;

        if ( $option === 'ta_link_prefix_custom' && get_option( 'ta_link_prefix' ) !== 'custom' )
            return $value;

        $used_link_prefixes = maybe_unserialize( get_option( 'ta_used_link_prefixes' , array() ) );
        $check_duplicate    = array_search( $value , $used_link_prefixes );

        if ( $check_duplicate !== false )
            unset( $used_link_prefixes[ $check_duplicate ] );

        $used_link_prefixes[] = sanitize_text_field( $value );
        $count                = count( $used_link_prefixes );

        if ( $count > 10 )
            $used_link_prefixes = array_slice( $used_link_prefixes , $count - 10 , 10 , false );

        update_option( 'ta_used_link_prefixes' , array_unique( $used_link_prefixes ) );

        return $value;
    }

    /**
     * Restrict modules settings.
     *
     * @since 3.6
     * @access public
     */
    public function restrict_module_settings() {

        $screen = get_current_screen();
        $tab    = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : null;

        if ( $screen->id !== 'thirstylink_page_thirsty-settings' || ! $tab ) return;

        $default_values = array();
        $registered     = apply_filters( 'ta_modules_settings_options' , array(
            array( 'id' => 'ta_enable_stats_reporting_module' , 'default' => 'yes' ),
            array( 'id' => 'ta_enable_link_fixer' , 'default' => 'yes' ),
            array( 'id' => 'ta_uncloak_link_per_link' , 'default' => 'no' )
        ) );

        if ( function_exists( 'array_column' ) )
            $default_values = array_column( $registered , 'default' , 'id' );
        else {

            foreach ( $registered as $module )
                $default_values[ $module[ 'id' ] ] = isset( $module[ 'default' ] ) ? $module[ 'default' ] : 'yes';
        }

        $modules        = array_keys( $default_values );
        $current_module = str_replace( array( 'tap_' , 'amazon_settings_section' , '_settings' ) , array( 'tap_enable_' , 'azon' , '' ) , $tab );
        $current_module = apply_filters( 'ta_restrict_current_module' , $current_module , $tab , $modules );
        $default        = in_array( $current_module , $modules ) ? $default_values[ $current_module ] : null;

        if ( ! is_null( $default ) && get_option( $current_module , $default ) !== 'yes' )
            wp_die( __( "Sorry, you are not allowed to access this page." , 'thirstyaffiliates' ) );

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

        if ( get_option( 'ta_settings_initialized' ) !== 'yes' ) {

            update_option( 'ta_link_prefix' , 'recommends' );
            update_option( 'ta_link_prefix_custom' , '' );
            update_option( 'ta_used_link_prefixes' , array( 'recommends' ) );
            update_option( 'ta_enable_javascript_frontend_redirect' , 'yes' );
            update_option( 'ta_show_enable_js_redirect_notice' , 'no' );
            update_option( 'ta_dismiss_marketing_notice_option' , 'no' );
            update_option( 'ta_settings_initialized' , 'yes' );
        }
    }

    /**
     * Execute codes that needs to run on plugin initialization.
     *
     * @since 3.0.0
     * @access public
     * @implements \ThirstyAffiliates\Interfaces\Initiable_Interface
     */
    public function initialize() {

        add_action( 'wp_ajax_ta_import_settings' , array( $this , 'ajax_import_settings' ) );
        add_action( 'wp_ajax_ta_dismiss_upgrade_header', array( $this, 'dismiss_upgrade_header' ) );
    }

    /**
     * Dismisses the TA upgrade header bar.
     *
     * @return void
     */
    public function dismiss_upgrade_header() {

        // Security check
        if ( ! wp_doing_ajax() ) {
            wp_send_json_error( __( 'Invalid AJAX call', 'thirstyaffiliates' ) );
        } elseif ( ! check_ajax_referer( 'ta_dismiss_upgrade_header', false, false ) ) {
            wp_send_json_error( __( 'Security Check Failed', 'thirstyaffiliates' ) );
        }

        update_option( 'ta_dismiss_upgrade_header', true );
    }

    public function ta_admin_header() {

        if ( is_plugin_active( 'thirstyaffiliates-pro/thirstyaffiliates-pro.php' ) || empty( $_GET['post_type'] ) || 'thirstylink' !== $_GET['post_type'] ) {
            return;
        }

        $dismissed = get_option( 'ta_dismiss_upgrade_header', false );

        if ( ! empty( $dismissed ) ) {
            return;
        }

        ?>

        <div class="ta-upgrade-header" id="ta-upgrade-header">
            <span id="close-ta-upgrade-header">X</span>
            <?php _e( 'You\'re using ThirstyAffiliates Lite. To unlock more features, consider <a href="https://thirstyaffiliates.com/pricing?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=upgrade_header">upgrading to Pro.</a>' ); ?>
        </div>

        <div id="ta-admin-header"><img class="ta-logo" src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'TA.svg'; ?>" /></div>

        <script>
            jQuery(document).ready(function($) {
                $('#close-ta-upgrade-header').click(function(event) {
                    var upgradeHeader = $('#ta-upgrade-header');
                    upgradeHeader.fadeOut();
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'ta_dismiss_upgrade_header',
                            _ajax_nonce: "<?php echo wp_create_nonce( 'ta_dismiss_upgrade_header' ); ?>"
                        },
                    })
                    .done(function() {
                        console.log("success");
                    })
                    .fail(function() {
                        console.log("error");
                    })
                    .always(function() {
                        console.log("complete");
                    });
                });
            });
        </script>

        <?php
    }

    public function pro_meta_boxes() {

        if ( empty( $_GET['post_type'] ) || 'thirstylink' !== $_GET['post_type'] || is_plugin_active( 'thirstyaffiliates-pro/thirstyaffiliates-pro.php' ) ) {
            return;
        }

        if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php' ) ) ) {
            return;
        }

        ?>

        <div id="ta-pro-settings" class="ta-blur-wrap">
            <div class="ta-blur">
                <div id="tap-autolink-keywords-metabox" class="postbox ">
                    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Autolink Keywords</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                    <h2 class="hndle ui-sortable-handle"><span>Autolink Keywords</span></h2>
                    <div class="inside">
                        <p>
                            <label class="info-label block" for="tap_autolink_keyword_list-selectized">Enter a list of keywords you wish to automatically link with this affiliate link (case-insensitive)</label>
                            <div class="selectize-control multi plugin-restore_on_backspace plugin-remove_button plugin-drag_drop" style="width: 100%;">
                                <div class="selectize-input items not-full ui-sortable"><input type="text" autocomplete="off" tabindex="" id="tap_autolink_keyword_list-selectized" style="width: 4px;"></div>
                            </div>
                            <span class="ta-input-description">Note: Place your keywords in order of precedence. eg. If "web design" is mentioned first and "web design course" second, it will link "web design" as first preference. Also, please type your entries rather than copy/pasting from the front end of your site. This will eliminate weird character encoding issues, especially relating to apostrophes. </span>
                        </p>
                        <p>
                            <label class="info-label" for="tap_autolink_keyword_limit">
                                Limit (per keyword): <span class="tooltip"></span>
                            </label>
                            <input type="number" class="ta-form-input" id="tap_autolink_keyword_limit" min="-1" step="1" value="0" placeholder="3" style="width: 100px;">
                        </p>
                    </div>
                </div>
                <div id="tap-geolocation-urls-metabox" class="postbox ">
                    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Geolocation URLs</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                    <h2 class="hndle ui-sortable-handle"><span>Geolocation URLs</span></h2>
                    <div class="inside">
                        <p>Enter your geolocation URLs, these will override the destination URL above for visitors from these countries:</p>
                        <div class="add-geo-link-form">
                            <div class="field-wrap select-countries">
                                <div class="selectize-control multi">
                                    <div class="selectize-input items not-full has-options"><input type="text" autocomplete="off" tabindex="" id="geolink_countries-selectized" placeholder="Select countries" style="width: 102px;"></div>
                                </div>
                            </div>
                            <div class="field-wrap destination-input">
                                <input type="text" id="geolink_destination" placeholder="Destination URL">
                            </div>
                            <div class="field-wrap geolink-button">
                                <button type="button" id="add_geolink_btn" class="button-primary">Add Geolink</button>
                            </div>
                        </div>
                        <table class="tap-geo-links-table">
                            <thead>
                                <tr>
                                    <th>Countries</th>
                                    <th>Destination URL</th>
                                    <th class="actions"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" style="text-align: center;">No geolocation links recorded.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="tap-link-scheduler-metabox" class="postbox ">
                    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Link Scheduler</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                    <h2 class="hndle ui-sortable-handle"><span>Link Scheduler</span></h2>
                    <div class="inside">
                        <p class="link-start-date-field date-field">
                            <label class="info-label" for="ta_link_start_date">Start date:</label>
                            <input type="text" class="ta-form-input range_datepicker start hasDatepicker" id="ta_link_start_date" value="" placeholder="yyyy-mm-dd" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))">
                            <span class="dashicons dashicons-calendar-alt"></span>
                        </p>
                        <p class="link-before-start-redirect-field url-field">
                            <label class="info-label" for="ta_before_start_redirect">Before start redirect URL:</label>
                            <input type="url" class="ta-form-input" id="ta_before_start_redirect" value="" placeholder="https://thirstyaffiliates.com/">
                        </p>
                        <p class="link-expire-date-field date-field">
                            <label class="info-label" for="ta_link_expire_date">Expire date:</label>
                            <span class="dashicons dashicons-calendar-alt"></span>
                            <input type="text" class="ta-form-input range_datepicker expire hasDatepicker" id="ta_link_expire_date" value="" placeholder="yyyy-mm-dd" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))">
                        </p>
                        <p class="link-after-expire-redirect-field url-field">
                            <label class="info-label" for="ta_after_expire_redirect">Link expiration redirect URL:</label>
                            <input type="url" class="ta-form-input" id="ta_after_expire_redirect" value="" placeholder="https://thirstyaffiliates.com/">
                        </p>
                    </div>
                </div>
                <div id="tap-preset-click-data-parameters-metabox" class="postbox ">
                    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Preset Click Data Parameters</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                    <h2 class="hndle ui-sortable-handle"><span>Preset Click Data Parameters</span></h2>
                    <div class="inside">
                        <div class="add-preset-click-data-form">
                            <div class="form-input ip-address-input">
                                <label for="preset_ip_address">IP address</label>
                                <input type="text" id="preset_ip_address" value="">
                            </div>
                            <div class="form-input referrer-address-input">
                                <label for="preset_http_referrer">HTTP Referrer</label>
                                <input type="url" id="preset_http_referrer" value="">
                            </div>
                            <div class="form-input keyword-input">
                                <label for="preset_keyword">Keyword</label>
                                <input type="text" id="preset_keyword" value="">
                            </div>
                            <div class="form-input form-submit">
                                <div class="save">
                                    <button type="button" class="button-primary save-preset-click-data">Save</button>
                                </div>
                            </div>
                        </div>
                        <div class="preset-click-data-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="qcode">Cloaked URL with code</th>
                                        <th class="ip-address">IP Address</th>
                                        <th class="referrer">HTTP Referrer</th>
                                        <th class="keyword">Keyword</th>
                                        <th class="actions"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="no-result">
                                        <td colspan="5">No preset click data parameters saved yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $section_title = 'Add Affiliate Link Pro features';
            $upgrade_link = 'https://thirstyaffiliates.com/pricing?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=pro_features';
            include_once $this->_constants->VIEWS_ROOT_PATH() . 'ta-upgrade.php';
            ?>
        </div>

        <script>
            jQuery(document).ready(function($) {
                $('#ta-pro-settings').appendTo('#normal-sortables');
            });
        </script>

        <?php
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

        add_action( 'admin_init' , array( $this , 'init_plugin_settings' ) );
        add_action( 'admin_menu' , array( $this , 'add_settings_page' ) );
        add_action( 'current_screen' , array( $this , 'restrict_module_settings' ) );

        add_action( 'ta_before_settings_form' , array( $this , 'load_key_value_option_field_type_styling' ) );
        add_action( 'ta_before_settings_form' , array( $this , 'load_key_value_option_field_type_script' ) );
        add_action( 'pre_update_option_ta_link_prefix' , array( $this , 'link_prefix_post_update_callback' ) , 10 , 3 );
        add_action( 'pre_update_option_ta_link_prefix_custom' , array( $this , 'link_prefix_post_update_callback' ) , 10 , 3 );

        add_filter( 'ta_admin_interfaces' , array( $this , 'register_admin_interfaces' ) );
        add_filter( 'ta_menu_items' , array( $this , 'register_admin_menu_items' ) );

        add_action( 'in_admin_header', array( $this, 'ta_admin_header' ), 0 );
        add_action( 'admin_footer', array( $this, 'pro_meta_boxes' ) );
    }

}
