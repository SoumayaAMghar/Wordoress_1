<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Activatable_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

/**
 * Model that houses the logic of migration of old versions of TA to version 3.0.0
 *
 * @since 3.0.0
 */
class Migration implements Model_Interface , Activatable_Interface , Initiable_Interface {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that holds the single main instance of Migration.
     *
     * @since 3.0.0
     * @access private
     * @var Migration
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
     * Property that holds the list of all affiliate links.
     *
     * @since 3.0.0
     * @access private
     * @var array
     */
    private $_all_affiliate_links;

    /**
     * Variable that holds the mapping between options from old version of the plugin to the new version of the plugin.
     *
     * @since 3.0.0
     * @access public
     * @var array
     */
    private $_old_new_options_mapping;

    /**
     * Variable that holds the mapping between post meta from old version of the plugin to the new version of the plugin.
     *
     * @since 3.0.0
     * @access public
     * @var array
     */
    private $_old_new_meta_mapping;




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

        $this->_constants           = $constants;
        $this->_helper_functions    = $helper_functions;

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
     * @return Migration
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }

    /**
     * Initialize data key mappings.
     *
     * @since 3.0.0
     * @access public
     */
    public function initialize_data_key_mappings() {

        // Get all affiliate links
        $this->_all_affiliate_links = $this->get_all_affiliate_links();

        $this->_old_new_options_mapping = apply_filters( 'ta_old_new_options_mapping' , array(

            // Core Data
            'linkprefix'                 => 'ta_link_prefix',
            'linkprefixcustom'           => 'ta_link_prefix_custom',
            'showcatinslug'              => 'ta_show_cat_in_slug',
            'disablecatautoselect'       => 'ta_disable_cat_auto_select',
            'linkredirecttype'           => 'ta_link_redirect_type',
            'nofollow'                   => 'ta_no_follow',
            'newwindow'                  => 'ta_new_window',
            // 'legacyuploader'             => '', // Obsolete option, not supported anymore on 3.0.0
            'disabletitleattribute'      => 'ta_disable_title_attribute',
            'disablethirstylinkclass'    => 'ta_disable_thirsty_link_class',
            'disablevisualeditorbuttons' => 'ta_disable_visual_editor_buttons',
            'disabletexteditorbuttons'   => 'ta_disable_text_editor_buttons',
            'additionalreltags'          => 'ta_additional_rel_tags',

            /* autolinker options */
            'autolinkbbpress'            => 'tap_autolink_bbpress',
            'randomplacement'            => 'tap_autolink_random_placement',
            'autolinkheadings'           => 'tap_autolink_inside_heading',
            'disablearchives'            => 'tap_autolink_disable_archives',
            'disablehome'                => 'tap_autolink_disable_homepage',
            'enablefeedreplacement'      => 'tap_autolink_enable_feeds',
            'enabledPostTypes'           => 'tap_autolink_post_types',

            /* geolocations options */
            // 'geolicencekey'               => '', // Obsolete option, not supported anymore on 3.0.0
            // 'geolicenceemail'             => '', // Obsolete option, not supported anymore on 3.0.0
            'disableforwardingproxytest' => 'tap_geolocations_disable_proxy_test',
            // 'enableip2locationdb'         => '', // Obsolete option, not supported anymore on 3.0.0
            // 'ip2locationdbfile'           => '', // Obsolete option, not supported anymore on 3.0.0
            // 'disableip2locationdbcache'   => '', // Obsolete option, not supported anymore on 3.0.0
            // 'enableip2locationwebservice' => '', // Obsolete option, not supported anymore on 3.0.0
            // 'ip2locationwebservicekey'    => '', // Obsolete option, not supported anymore on 3.0.0
            // 'enablemaxminddb'             => '', // Obsolete option, not supported anymore on 3.0.0
            // 'enablemaxmindwebservice'     => '', // Obsolete option, not supported anymore on 3.0.0
            'maxminddbfile'              => 'tap_geolocations_maxmind_mmdb_file',
            'maxmindwebserviceuserid'    => 'tap_geolocations_maxmind_api_userid',
            'maxmindwebservicekey'       => 'tap_geolocations_maxmind_api_key',

            // Azon
            /*
            * Options below are not imported.
            *
            * include_item_stock_on_search_result
            * include_item_rating_on_search_result
            * include_sales_rank_on_search_result
            */
            'azon_aws_access_key_id'       => 'tap_amazon_access_key_id',
            'azon_aws_secret_key'          => 'tap_amazon_secret_key',
            'defaultSearchCountry'         => 'tap_last_used_search_endpoint',
            'azon_geolocation_support'     => 'tap_azon_geolocation_integration',
            'defaultCategories'            => 'tap_azon_imported_link_categories',
            'exclude_zero_priced_products' => 'tap_hide_products_with_empty_price',

            // Google Click Tracking
            'gctactionname'              => 'tap_google_click_tracking_action_name',
            'gctusepost'                 => 'tap_google_click_tracking_use_post',
            'gctuselegacyga'             => 'tap_google_click_tracking_use_legacy_ga',
            // 'gctfilterlegacyga'       => '', // Obsolete option, not supported anymore on 3.0.0

            // Stats
            'statsmanualexclusions'      => 'tap_stats_manual_exclusions'


        ) );

        $this->_old_new_meta_mapping = apply_filters( 'ta_old_new_meta_mapping' , array(

            // Core Data
            'linkredirecttype' => 'redirect_type',
            'linkurl'          => 'destination_url',
            'nofollow'         => 'no_follow',
            'newwindow'        => 'new_window',
            // 'enablewildcard'   => '', // Tentative
            // 'wildcards'        => '', // Tentative
            'linkname'         => 'name',

            // autolinker meta
            'keywordlist'      => 'autolink_keyword_list',
            'autolinklimit'    => 'autolink_keyword_limit',
            'autolinkheadings' => 'autolink_inside_heading',
            'randomplacement'  => 'autolink_random_placement',

            // geolocations meta
            'geolink'          => 'geolocation_links',

            // Azon
            // 'asin'             => '_tap_asin' // asin bugged out, it does not contain asin anymore

            // Stats
            'statsmanualexclusions'      => 'tap_stats_manual_exclusions',


        ) );

    }

    /**
     * Migrate old plugin data.
     * Note: Plugin_Constants::MIGRATION_COMPLETE_FLAG is not used to determine if we should migrate or not
     * The only purpose of this option is to determine if there is a current migration running.
     * If it says 'no' then there is a migration running, but its not yet finished.
     * If it says 'yes' then it says, the current migration is done.
     * We need to allow re-running migration coz there might be some data that have not migrated on first pass ( though this is for worst case scenarios ).
     *
     * @since 3.0.0
     * @access public
     */
    public function migrate_old_plugin_data() {

        update_option( Plugin_Constants::MIGRATION_COMPLETE_FLAG , 'no' );

        $this->initialize_data_key_mappings();
        $this->migrate_plugin_options();
        $this->migrate_link_meta();
        $this->migrate_image_attachments();

        do_action( 'ta_migrate_old_plugin_data' );

        update_option( Plugin_Constants::MIGRATION_COMPLETE_FLAG , 'yes' );

    }

    /**
     * Migrate old plugin data to new data model via ajax.
     *
     * @since 3.0.0
     * @access public
     */
    public function ajax_migrate_old_plugin_data() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX Call.' , 'thirstyaffiliates' ) );
        elseif ( !$this->_helper_functions->current_user_authorized() )
            $response = array( 'status' => 'fail' , 'error_msg' => __(  'Unauthorized operation. Only authorized accounts can do data migration.' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta_migrate_old_plugin_data', false, false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        else {

            $this->migrate_old_plugin_data();
            $response = array( 'status' => 'success' , 'success_msg' => __( 'Data Migration Complete!' , 'thirstyaffiliates' ) );

        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();

    }




    /*
    |--------------------------------------------------------------------------
    | Migrating Settings Options
    |--------------------------------------------------------------------------
    */

    /**
     * Migrate old plugin options to the new plugin options.
     *
     * @since 3.0.0
     * @access public
     */
    public function migrate_plugin_options() {

        $old_options = get_option( 'thirstyOptions' , array() );
        if ( !is_array( $old_options ) )
            $old_options = array();

        $old_options_cache = $old_options;

        $old_options = apply_filters( 'ta_migration_process_old_options' , $old_options );

        foreach ( $old_options as $key => $val ) {

            if ( array_key_exists( $key , $this->_old_new_options_mapping ) ) {

                if ( $val === 'on' )
                    $val = 'yes';

                update_option( $this->_old_new_options_mapping[ $key ] , $val );
                unset( $old_options_cache[ $key ] );

            }

        }

        update_option( 'thirstyOptions' , $old_options_cache );

        do_action( 'ta_migrate_complex_options' ); // Hook for migrating complex options

    }

    /**
     * Migrate complex options.
     *
     * @since 3.0.0
     * @access public
     */
    public function complex_options_migration() {

        $old_options = get_option( 'thirstyOptions' , array() );
        if ( !is_array( $old_options ) )
            $old_options = array();

        // Amazon Associate Tags
        $country_codes        = array( 'us' , 'ca' , 'cn' , 'de' , 'es' , 'fr' , 'in' , 'it' , 'jp' , 'uk' ); // The only supported countries on old azon
        $new_associate_tags   = array();
        $update_associate_tag = false;

        foreach ( $country_codes as $cc ) {

            if ( isset( $old_options[ $cc . "_azon_aws_associate_tag" ] ) && !empty( $old_options[ $cc . "_azon_aws_associate_tag" ] ) ) {

                $update_associate_tag = true;
                $new_associate_tags[ strtoupper( $cc ) ] = $old_options[ $cc . "_azon_aws_associate_tag" ];
                unset( $old_options[ $cc . "_azon_aws_associate_tag" ] );

            }

        }

        if ( $update_associate_tag )
            update_option( 'tap_amazon_associate_tags' , $new_associate_tags );

        // Amazon Import Images
        $imported_images = array();
        $update_images   = false;

        if ( isset( $old_options[ 'importImages' ] ) && !empty( $old_options[ 'importImages' ] ) && is_array( $old_options[ 'importImages' ] ) ) {

            foreach ( $old_options[ 'importImages' ] as $key => $val )
                if ( in_array( $key , array( 'small' , 'medium' , 'large' ) ) && $val === 'on' ) {

                    $update_images     = true;
                    $imported_images[] = $key;

                }

            unset( $old_options[ 'importImages' ] );

        }

        if ( $update_images )
            update_option( 'tap_azon_import_images' , $imported_images );

        // Update the old option so no repeating of imports
        update_option( 'thirstyOptions' , $old_options );

    }




    /*
    |--------------------------------------------------------------------------
    | Migrating Link Meta
    |--------------------------------------------------------------------------
    */

    /**
     * Get all affiliate links.
     *
     * @since 3.0.0
     * @access public
     *
     * @return array Array of all affiliate links as objects.
     */
    public function get_all_affiliate_links() {

        global $wpdb;

        $query = "SELECT *
                  FROM $wpdb->posts
                  WHERE post_type = 'thirstylink'";

        return $wpdb->get_results( $query );

    }

    /**
     * Generate link meta insert sql.
     *
     * @since 3.0.0
     * @access public
     *
     * @global \wpdb $wpdb                Global $wpdb object.
     * @param int   $link_id             Affiliate link id.
     * @param array $old_link_meta       Old thirsty affiliate link meta.
     * @param array $old_link_meta_cache Old thirsty affiliate link meta. Passed by reference. Used to track down the new old meta data.
     * @return string SQL query.
     */
    private function _generate_link_meta_insert_sql( $link_id , $old_link_meta , &$old_link_meta_cache ) {

        global $wpdb;

        $query      = "INSERT INTO $wpdb->postmeta ( post_id , meta_key , meta_value ) VALUES";
        $first_pass = false;

        foreach ( $this->_old_new_meta_mapping as $old_key => $new_key ) {

            if ( isset( $old_link_meta[ $old_key ] ) ) {

                if ( $first_pass )
                    $query .= ",";

                $value = $old_link_meta[ $old_key ] === 'on' ? 'yes' : $old_link_meta[ $old_key ];

                if ( $new_key === "destination_url" ) {

                    // Do not esc the destination_url as its already escaped there on meta
                    // There are cases on TA V2 that & are stored as double amps ( '&amp;amp;' ) we should replace them with single &amp;
                    $value = esc_url_raw( str_replace( array( '&amp;amp;' , '&amp;' ) , '&' , $value ) );

                } else
                    $value = esc_sql( $value );

                $query .= " ( " . $link_id . " , '" . Plugin_Constants::META_DATA_PREFIX . $new_key . "' , '" . $value . "' )";

                /*
                 * We handle asin in a special way, i found out that old azon bugged out, it was not storing asin meta on azon imported links anymore.
                 * That is important for azon to identify which links came from amazon ( hence identifying on search if the product item on search result is already imported or not ).
                 * Because of that, we need to import the asin data when we reached to importing linkurl, linkurl contains asin data, so will extract that from link url and import it.
                 * We only do this tho for azon imported links.
                 */
                if ( array_key_exists( 'asin' , $old_link_meta ) && $old_key === 'linkurl' && isset( $old_link_meta[ $old_key ] ) ) {

                    $parsed_url = parse_url( $old_link_meta[ $old_key ] );

                    if ( isset( $parsed_url[ 'query' ] ) ) {

                        // Yes intentionally doubled coz i see links like this &amp;amp;
                        $parsed_url[ 'query' ] = str_replace( "&amp;" , "&" , $parsed_url[ 'query' ] );
                        $parsed_url[ 'query' ] = str_replace( "&amp;" , "&" , $parsed_url[ 'query' ] );
                        parse_str( $parsed_url[ 'query' ] , $parsed_query_string );

                        if ( isset( $parsed_query_string[ 'creativeASIN' ] ) ) {

                            $asin   = $parsed_query_string[ 'creativeASIN' ];
                            $query .= ", ( " . $link_id . " , '_tap_asin' , '" . $asin . "' )";

                        }

                    }

                }

                unset( $old_link_meta_cache[ $old_key ] );

                if ( !$first_pass )
                    $first_pass = true;

            }

        }

        return $first_pass ? $query : false;

    }

    /**
     * Generate link meta delete sql.
     * This sql query deletes old post meta from links after old data is migrated to the new post meta.
     * This is designed to be reusable, the only dynamic part here is the link id.
     * Therefore users of this function must str_replace the <link_id> with the proper link id.
     * Currently not used, but we might used this later so lets just keep it.
     *
     * @since 3.0.0
     * @access public
     *
     * @global \wpdb $wpdb Global $wpdb object.
     */
    private function _generate_link_meta_delete_sql() {

        global $wpdb;

        return "DELETE FROM $wpdb->postmeta
                WHERE post_id = <link_id>
                AND meta_key IN ( " . implode( "," , array_keys( $this->_old_new_meta_mapping ) ) . " )";

    }

    /**
     * Migrate old link metadata to new link metadata model.
     * Old data comes from 'thirstyData' post meta which contains a serialized array of link meta data.
     *
     * @since 3.0.0
     * @access public
     *
     * @global \wpdb $wpdb Global $wpdb object.
     */
    public function migrate_link_meta() {

        global $wpdb;

        foreach ( $this->_all_affiliate_links as $affiliate_link ) {

            $old_link_meta = maybe_unserialize( get_post_meta( $affiliate_link->ID , 'thirstyData' , true ) );
            if ( !is_array( $old_link_meta ) )
                $old_link_meta = array();

            $old_link_meta_cache = $old_link_meta;

            if ( empty( $old_link_meta ) )
                continue;

            $old_link_meta = apply_filters( 'ta_migration_process_old_link_meta' , $old_link_meta , $affiliate_link );

            $query = $this->_generate_link_meta_insert_sql( $affiliate_link->ID , $old_link_meta , $old_link_meta_cache );
            if ( $query && $wpdb->query( $query ) )
                update_post_meta( $affiliate_link->ID , 'thirstyData' , serialize( $old_link_meta_cache ) );

        }

    }

    /**
     * Migrate autolinker enabled post types field
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $old_options TA2 settings.
     * @return array Filtered TA2 settings.
     */
    public function migrate_autolinker_enabled_post_types_field( $old_options ) {

        if ( isset( $old_options[ 'enabledPostTypes' ] ) && ! empty( $old_options[ 'enabledPostTypes' ] ) ) {

            $support_post_types = array();

            foreach ( $old_options[ 'enabledPostTypes' ] as $post_type => $val )
                $support_post_types[] = $post_type;

            $old_options[ 'enabledPostTypes' ] = $support_post_types;
        }

        return $old_options;
    }

    /**
     * Migrate stats manual exclusion but removing the default value added by the Stats addon.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $old_options TA2 settings.
     * @return array Filtered TA2 settings.
     */
    public function migrate_stats_manual_exclusion( $old_options ) {

        if ( ! isset( $old_options[ 'statsmanualexclusions' ] ) )
            return $old_options;

        $default_exclusions = "64.233.160.0-64.233.191.255\r\n66.102.0.0-66.102.15.255\r\n66.249.64.0-66.249.95.255\r\n72.14.192.0-72.14.255.255\r\n74.125.0.0-74.125.255.255\r\n209.85.128.0-209.85.255.255\r\n216.239.32.0-216.239.63.255\r\n64.4.0.0-64.4.63.255\r\n65.52.0.0-65.55.255.255\r\n131.253.21.0-131.253.47.255\r\n157.54.0.0-157.60.255.255\r\n207.46.0.0-207.46.255.255\r\n207.68.128.0-207.68.207.255\r\n8.12.144.0-8.12.144.255\r\n66.196.64.0-66.196.127.255\r\n66.228.160.0-66.228.191.255\r\n67.195.0.0-67.195.255.255\r\n68.142.192.0-68.142.255.255\r\n72.30.0.0-72.30.255.255\r\n74.6.0.0-74.6.255.255\r\n98.136.0.0-98.139.255.255\r\n202.160.176.0-202.160.191.255\r\n209.191.64.0-209.191.127.255";
        $old_options[ 'statsmanualexclusions' ] = trim( str_replace( $default_exclusions , '' , $old_options[ 'statsmanualexclusions' ] ) );

        return $old_options;
    }

    /**
     * Migrate new GCT tracking script option. (Added on TAP 1.1.0)
     *
     * @since 3.1.1
     * @access public
     *
     * @param array $old_options List of TA2 old options data.
     * @return array Filtered List of TA2 old options data.
     */
    public function migrate_gct_tracking_script( $old_options ) {

        if ( ! is_plugin_active( 'thirstyaffiliates-pro/thirstyaffiliates-pro.php' ) )
            return $old_options;

        $tap_plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/thirstyaffiliates-pro/thirstyaffiliates-pro.php' );

        if ( version_compare( $tap_plugin_data[ 'Version' ] , '1.1.0' , "<" ) )
            return $old_options;

        if ( isset( $old_options[ 'gctuselegacyga' ] ) && $old_options[ 'gctuselegacyga' ] == 'on' ) {

            update_option( 'tap_google_click_tracking_script' , 'legacy_ga' );
            unset( $old_options[ 'gctuselegacyga' ] );
        }

        return $old_options;
    }




    /*
    |--------------------------------------------------------------------------
    | Migrating Image Attachments To An Affiliate Link
    |--------------------------------------------------------------------------
    */

    /**
     * Migrate old image attachments data to new image attachment meta data.
     * The code on how we used to attach image to a link can be found here in this functon "thirstyAttachImageToLink".
     * We are using the post parent of the attachment as the place to hold the affiliate link id that the current attachment is attached to.
     *
     * @since 3.0.0
     * @access public
     *
     * @global WPDB $wpdb Global $wpdb object.
     */
    public function migrate_image_attachments() {

        global $wpdb;

        foreach ( $this->_all_affiliate_links as $affiliate_link ) {

            $old_link_attachments = get_posts( array(
                                        'post_type'      => 'attachment',
                                        'posts_per_page' => -1,
                                        'post_status'    => null,
                                        'post_parent'    => $affiliate_link->ID,
                                        'orderby'        => 'menu_order',
                                        'order'          => 'ASC'
                                    ) );

            if ( is_array( $old_link_attachments ) ) {

                $new_attachment_data = array();
                foreach ( $old_link_attachments as $attachment )
                    $new_attachment_data[] = $attachment->ID;

                if ( !empty( $new_attachment_data ) ) {

                    update_post_meta( $affiliate_link->ID , Plugin_Constants::META_DATA_PREFIX . 'image_ids' , $new_attachment_data );

                    $wpdb->query( "UPDATE $wpdb->posts
                                   SET post_parent = ''
                                   WHERE ID IN ( " . implode( "," , array_map( 'intval' , $new_attachment_data ) ) . " )
                                   AND post_type = 'attachment'" );

                }

            }

        }

    }




    /*
    |--------------------------------------------------------------------------
    | Migrating Geolocations Data
    |--------------------------------------------------------------------------
    */

    /**
     * Migrate geolocations metadata
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $old_link_data Affiliate link old post meta data.
     * @param object $affiliate_link Affiliate link object $wpdb->posts single row result.
     * @return array Filtered affiliate link old post meta data.
     */
    public function migrate_geolocations_meta_data( $old_link_meta , $affiliate_link ) {

        if ( empty( $old_link_meta ) || ! isset( $old_link_meta[ 'geolink' ] ) || ! is_array( $old_link_meta[ 'geolink' ] ) || empty( $old_link_meta[ 'geolink' ] ) ) {

            unset( $old_link_meta[ 'geolink' ] ); // NOTE: We need to unset this so no empty array will be processed in the SQL query.
            return $old_link_meta;
        }

        $temp_geolinks  = array();
        $geolinks       = array();
        $country_clones = array();
        $keys           = array();

        // seperate the geolinks with actual urls from the clone ones.
        foreach ( $old_link_meta[ 'geolink' ] as $country => $destination ) {

            if ( filter_var( $destination , FILTER_VALIDATE_URL ) === FALSE )
                $country_clones[ $country ] = $destination;
            else {

                $temp_geolinks[ $country ] = array(
                    'countries'       => array( $country ),
                    'destination_url' => $destination
                );
            }

        }

        // assign cloned countries to $temp_geolinks country data
        foreach ( $country_clones as $country => $cloned_country )
            $temp_geolinks[ $cloned_country ][ 'countries' ][] = $country;

        // generate key for each geolink and add to $geolinks array list
        foreach ( $temp_geolinks as $country => $data ) {

            $key              = trim( implode( ',' , $data[ 'countries' ] ) );
            $geolinks[ $key ] = esc_url_raw( str_replace( array( '&amp;amp;' , '&amp;' ) , '&' , $data[ 'destination_url' ] ) );
        }

        // Return combined keys with geolinks data
        $old_link_meta[ 'geolink' ] = serialize( $geolinks );

        return $old_link_meta;
    }




    /*
    |--------------------------------------------------------------------------
    | Migration Admin Notice
    |--------------------------------------------------------------------------
    */

    /**
     *  Show admin notice that migration process is currently running.
     *
     * @since 3.0.0
     * @access public
     */
    public function migration_running_admin_notice() {

        if ( get_option( Plugin_Constants::MIGRATION_COMPLETE_FLAG ) === 'no' ) { ?>

            <div class="notice notice-warning">
                <p><?php _e( '<b>ThirstyAffiliates is currently migrating your old affiliate link data to the new data model.<br>Please hold off making changes to your affiliate links. Please refresh the page and if this message has disappeared, the migration is complete.</b>' , 'thirstyaffiliates' ); ?></p>
            </div>

        <?php }

    }




    /*
    |--------------------------------------------------------------------------
    | After migration
    |--------------------------------------------------------------------------
    */

    /**
     * After migration, urls will have double amps, change this to single amps.
     * This is a behavior on TA v2 where it saves & on db as double amps.
     *
     * @since 3.0.1
     * @access public
     */
    public function fix_double_amps_on_destination_url() {

        $affiliate_link_ids = array();
        foreach ( $this->_all_affiliate_links as $affiliate_link )
            $affiliate_link_ids[] = $affiliate_link->ID;

        if ( !empty( $affiliate_link_ids ) && is_array( $affiliate_link_ids ) ) {

            global $wpdb;

            $affiliate_link_ids_str = implode( "," , array_map( 'intval' , $affiliate_link_ids ) );

            $query = "UPDATE $wpdb->postmeta
                        SET meta_value = REPLACE( REPLACE( meta_value , '&amp;amp;' , '&' ) , '&amp;' , '&' )
                        WHERE meta_key = '_ta_destination_url'
                        AND post_id IN ( $affiliate_link_ids_str )";

            $wpdb->query( $query );

        }

    }




    /*
    |--------------------------------------------------------------------------
    | Fulfill Implemented Interface Contracts
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

        // Execute one time cron on plugin activation to migrate old data to new data
        wp_clear_scheduled_hook( Plugin_Constants::CRON_MIGRATE_OLD_PLUGIN_DATA );
        wp_schedule_single_event( time() + 5 , Plugin_Constants::CRON_MIGRATE_OLD_PLUGIN_DATA ); // Delay the migration for 5 sec to be sure everything is set up

    }

    /**
     * Execute codes that needs to run on plugin initialization.
     *
     * @since 3.0.0
     * @access public
     * @implements \ThirstyAffiliates\Interfaces\Initiable_Interface
     */
    public function initialize() {

        add_action( 'wp_ajax_ta_migrate_old_plugin_data' , array( $this , 'ajax_migrate_old_plugin_data' ) );

    }

    /**
     * Execute Migration class.
     *
     * @since 3.0.0
     * @access public
     * @implements \ThirstyAffiliates\Interfaces\Model_Interface
     */
    public function run() {

        add_action( Plugin_Constants::CRON_MIGRATE_OLD_PLUGIN_DATA , array( $this , 'migrate_old_plugin_data' ) );

        add_action( 'admin_notices' , array( $this , 'migration_running_admin_notice' ) );

        add_filter( 'ta_migration_process_old_options' , array( $this , 'migrate_autolinker_enabled_post_types_field' ) , 10 , 1 );
        add_filter( 'ta_migration_process_old_options' , array( $this , 'migrate_stats_manual_exclusion' ) , 10 , 1 );
        add_filter( 'ta_migration_process_old_options' , array( $this , 'migrate_gct_tracking_script' ) , 10 , 1 );
        add_action( 'ta_migrate_complex_options' , array( $this , 'complex_options_migration' ) );
        add_filter( 'ta_migration_process_old_link_meta' , array( $this , 'migrate_geolocations_meta_data' ) , 10 , 2 );

        add_action( 'ta_migrate_old_plugin_data' , array( $this , 'fix_double_amps_on_destination_url' ) );

    }

}
