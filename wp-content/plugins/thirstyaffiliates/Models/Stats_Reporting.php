<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;
use ThirstyAffiliates\Interfaces\Activatable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

use ThirstyAffiliates\Models\Affiliate_Link;

/**
 * Model that houses the logic for permalink rewrites and affiliate link redirections.
 *
 * @since 3.0.0
 */
class Stats_Reporting implements Model_Interface , Initiable_Interface , Activatable_Interface {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that holds the single main instance of Stats_Reporting.
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
     * Variable to store local browser's zone string.
     *
     * @since 3.2.2
     * @access private
     * @var string
     */
    private $_browser_zone_str;




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

    /**
     * Update $_browser_zone_str class property value.
     *
     * @since 3.3.3
     * @access public
     *
     * @param string $timezone Timezone set on browser
     */
    public function set_browser_zone_str( $timezone ) {

        if ( in_array( $timezone , timezone_identifiers_list() ) )
            $this->_browser_zone_str = $timezone;
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

        $interfaces[ 'thirstylink_page_thirsty-reports' ] = apply_filters( 'ta_reports_admin_interface' , array(
            'link_performance' => 'manage_options'
        ) );

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

        $menu_items[ 'thirsty-reports' ] = 'manage_options';
        return $menu_items;
    }




    /*
    |--------------------------------------------------------------------------
    | Data saving
    |--------------------------------------------------------------------------
    */

    /**
     * Save link click data to the database.
     *
     * @since 3.0.0
     * @since 3.1.0 Set to save additional information: $cloaked_url , $redirect_url and $redirect_type.
     * @since 3.2.0 Set to save additonal information: keyword.
     * @since 3.4.0 Add tracking for browser/device.
     * @access private
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param Affiliate_Link $thirstylink   Affiliate link object.
     * @param string         $http_referer  HTTP Referrer value.
     * @param string         $cloaked_url   Affiliate link cloaked url.
     * @param string         $redirect_url  Link to where user is redirected to.
     * @param string         $redirect_type Redirect type (301,302, etc.)
     * @param string         $keyword       Affiliate link keyword.
     */
    private function save_click_data( $thirstylink , $http_referer , $cloaked_url , $redirect_url , $redirect_type , $keyword = '' ) {

        global $wpdb;

        if ( apply_filters( 'ta_filter_before_save_click' , false , $thirstylink , $http_referer ) )
            return;

        $link_click_db      = $wpdb->prefix . Plugin_Constants::LINK_CLICK_DB;
        $link_click_meta_db = $wpdb->prefix . Plugin_Constants::LINK_CLICK_META_DB;

        // insert click entry
        $wpdb->insert(
            $link_click_db,
            array(
                'link_id'      => $thirstylink->get_id(),
                'date_clicked' => current_time( 'mysql' , true )
            )
        );

        // save click meta data
        if ( $click_id = $wpdb->insert_id ) {

            $meta_data = apply_filters( 'ta_save_click_data' , array(
                'user_ip_address' => $this->_helper_functions->get_user_ip_address(),
                'http_referer'    => $http_referer,
                'cloaked_url'     => $cloaked_url,
                'redirect_url'    => $redirect_url,
                'redirect_type'   => $redirect_type,
                'keyword'         => $keyword,
                'browser_device'  => $this->_helper_functions->get_visitor_browser_device()
            ), $thirstylink );

            foreach ( $meta_data as $key => $value ) {

                // make sure there is a key and a value before saving.
                if ( ! $key || ! $value )
                    continue;

                $wpdb->insert(
                    $link_click_meta_db,
                    array(
                        'click_id'   => $click_id,
                        'meta_key'   => $key,
                        'meta_value' => $value
                    )
                );
            }

        }
    }

    /**
     * Save click data on redirect
     *
     * @since 3.0.0
     * @since 3.1.0 Passed additional 2 parameters: $redirect_url and $redirect type. Updated save_click_data function call to include new required arguments.
     * @since 3.3.0 If enhanced javascript redirect in frontend is enabled, then all server redirects are allowed (with this, stats links clicked via "open new tab" will be saved).
     * @access public
     *
     * @param Affiliate_Link $thirstylink   Affiliate link object.
     * @param string         $redirect_url  Link to where user is redirected to.
     * @param string         $redirect_type Redirect type (301,302, etc.)
     */
    public function save_click_data_on_redirect( $thirstylink , $redirect_url , $redirect_type ) {

        // make sure that this is only runs on the frontend and not on the backend.
        if ( is_admin() ) return;

        $link_id      = $thirstylink->get_id();
        $http_referer = isset( $_SERVER[ 'HTTP_REFERER' ] ) ? $_SERVER[ 'HTTP_REFERER' ] : '';
        $query_string = isset( $_SERVER[ 'QUERY_STRING' ] ) ? $_SERVER[ 'QUERY_STRING' ] : '';
        $cloaked_url  = $query_string ? $thirstylink->get_prop( 'permalink' ) . '?' . $query_string : $thirstylink->get_prop( 'permalink' );

        $same_site           = $http_referer && strrpos( 'x' . $http_referer , home_url() );
        $admin_referrer      = $http_referer && strrpos( 'x' . $http_referer , admin_url() );
        $js_redirect_enabled = get_option( 'ta_enable_javascript_frontend_redirect' ) == 'yes';

        // NOTE: this fixes a bug reported on TA-250
        if ( $http_referer == $cloaked_url || $admin_referrer ) return;

        // if the refferer is from an external site, then record stat.
        if ( ( $same_site && $js_redirect_enabled ) || ! $same_site )
            $this->save_click_data( $thirstylink , $http_referer , $cloaked_url , $redirect_url , $redirect_type );
    }

    /**
     * AJAX save click data on redirect
     *
     * @since 3.0.0
     * @since 3.1.0 Updated save_click_data function call to include new required arguments.
     * @since 3.2.0 Updated save_click_data function call to include keyword argument.
     * @since 3.3.0 print actual affiliate link redirect url for ehanced javascript redirect support.
     * @since 3.4.0 Add query string as parameter to support passing query strings in enhanced JS redirect.
     * @access public
     */
    public function ajax_save_click_data_on_redirect() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            wp_die();

        $link_id      = isset( $_REQUEST[ 'link_id' ] ) ? (int) sanitize_text_field( $_REQUEST[ 'link_id' ] ) : 0;
        $http_referer = isset( $_REQUEST[ 'page' ] ) ? esc_url_raw( $_REQUEST[ 'page' ] ) : '';
        $cloaked_url  = isset( $_REQUEST[ 'href' ] ) ? esc_url_raw( $_REQUEST[ 'href' ] ) : '';
        $keyword      = isset( $_REQUEST[ 'keyword' ] ) ? sanitize_text_field( $_REQUEST[ 'keyword' ] ) : '';
        $query_string = isset( $_REQUEST[ 'qs' ] ) ? sanitize_text_field( $_REQUEST[ 'qs' ] ) : '';

        if ( ! $link_id )
            $link_id = url_to_postid( $cloaked_url );

        if ( $link_id ) {

            $thirstylink   = new Affiliate_Link( $link_id );
            $redirect_url  = apply_filters( 'ta_filter_redirect_url' , $thirstylink->get_prop( 'destination_url' ) , $thirstylink , $query_string );
            $redirect_type = $thirstylink->get_redirect_type();

            $this->save_click_data( $thirstylink , $http_referer , $cloaked_url , $redirect_url , $redirect_type , $keyword );

            do_action( 'ta_before_link_redirect_ajax', $thirstylink, $redirect_url, $redirect_type );

            // print actual affiliate link redirect url for enhanced javascript redirect support.
            if ( get_option( 'ta_enable_javascript_frontend_redirect' ) == 'yes' )
                echo $redirect_url;
        }

        wp_die();
    }




    /*
    |--------------------------------------------------------------------------
    | Fetch Report Data
    |--------------------------------------------------------------------------
    */

    /**
     * Fetch link performance data by date range.
     *
     * @since 3.0.0
     * @access public
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param string $start_date Report start date. Format: YYYY-MM-DD hh:mm:ss
     * @param string $end_date   Report end date. Format: YYYY-MM-DD hh:mm:ss
     * @param array  $link_ids    Affiliate Link post ID
     * @return string/array Link click meta data value.
     */
    public function get_link_performance_data( $start_date , $end_date , $link_ids ) {

        global $wpdb;

        if ( ! is_array( $link_ids ) || empty( $link_ids ) )
            return array();

        $link_clicks_db = $wpdb->prefix . Plugin_Constants::LINK_CLICK_DB;
        $link_ids_str   = implode( ', ' , $link_ids );
        $query          = "SELECT * FROM $link_clicks_db WHERE date_clicked between '$start_date' and '$end_date' and link_id IN ( $link_ids_str )";

        return $wpdb->get_results( $query );
    }

    /**
     * Get link click meta by id and key.
     *
     * @since 3.0.0
     * @access public
     *
     * @param int     $click_id Link click ID.
     * @param string  $meta_key Meta key column value.
     * @param boolean $single   Return single result or array.
     * @return array Link performance data.
     */
    private function get_click_meta( $click_id , $meta_key , $single = false ) {

        global $wpdb;

        $links_click_meta_db = $wpdb->prefix . Plugin_Constants::LINK_CLICK_META_DB;

        if ( $single ){

            $meta = $wpdb->get_row( "SELECT meta_value FROM $links_click_meta_db WHERE click_id = '$click_id' and meta_key = '$meta_key'" , ARRAY_A );
            return array_shift( $meta );

        } else {

            $meta     = array();
            $raw_data = $wpdb->get_results( "SELECT meta_value FROM $links_click_meta_db WHERE click_id = '$click_id' and meta_key = '$meta_key'" , ARRAY_N );

            foreach ( $raw_data as $data )
                $meta[] = array_shift( $data );

            return $meta;
        }
    }

    /**
     * AJAX fetch report by linkid.
     *
     * @since 3.0.0
     * @since 3.1.2 Add total clicks count on the response.
     * @access public
     */
    public function ajax_fetch_report_by_linkid() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( $this->_helper_functions->get_capability_for_interface( 'reports', 'manage_options' ) ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_POST[ 'link_id' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Missing required post data' , 'thirstyaffiliates' ) );
        else {

            // save timezone to use
            $timezone = isset( $_POST[ 'timezone' ] ) ? sanitize_text_field( $_POST[ 'timezone' ] ) : '';
            $this->set_browser_zone_str( $timezone );

            $link_id     = isset( $_POST[ 'link_id' ] ) ? (int) sanitize_text_field( $_POST[ 'link_id' ] ) : 0;
            $thirstylink = new Affiliate_Link( $link_id );
            $range_txt   = isset( $_POST[ 'range' ] ) ? sanitize_text_field( $_POST[ 'range' ] ) : '';
            $start_date  = isset( $_POST[ 'start_date' ] ) ? sanitize_text_field( $_POST[ 'start_date' ] ) : '';
            $end_date    = isset( $_POST[ 'end_date' ] ) ? sanitize_text_field( $_POST[ 'end_date' ] ) : '';

            if ( ! $thirstylink->get_id() )
                $response = array( 'status' => 'fail' , 'error_msg' => __( 'Selected affiliate link is invalid' , 'thirstyaffiliates' ) );
            else {

                $range   = $this->get_report_range_details( $range_txt , $start_date , $end_date );
                $data    = $this->prepare_data_for_flot( $range , array( $link_id ) );

                $response = array(
                    'status'       => 'success',
                    'label'        => $thirstylink->get_prop( 'name' ),
                    'slug'         => $thirstylink->get_prop( 'slug' ),
                    'report_data'  => $data,
                    'total_clicks' => $this->count_total_clicks_from_flot_data( $data )
                );
            }
        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
    }

    /**
     * AJAX init first report.
     *
     * @since 3.2.2
     * @access public
     */
    public function ajax_init_first_report() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( $this->_helper_functions->get_capability_for_interface( 'reports', 'manage_options' ) ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_POST[ 'timezone' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Missing required post data' , 'thirstyaffiliates' ) );
        else {

            // save timezone to use
            $timezone = isset( $_POST[ 'timezone' ] ) ? sanitize_text_field( $_POST[ 'timezone' ] ) : '';
            $this->set_browser_zone_str( $timezone );

            $cpt_slug      = Plugin_Constants::AFFILIATE_LINKS_CPT;
            $current_range = isset( $_POST[ 'range' ] ) ? sanitize_text_field( $_POST[ 'range' ] ) : '7day';
            $start_date    = isset( $_POST[ 'start_date' ] ) ? sanitize_text_field( $_POST[ 'start_date' ] ) : '';
            $end_date      = isset( $_POST[ 'end_date' ] ) ? sanitize_text_field( $_POST[ 'end_date' ] ) : '';
            $range         = $this->get_report_range_details( $current_range , $start_date , $end_date );

            // get all published affiliate link ids
            $query = new \WP_Query( array(
                'post_type'      => $cpt_slug,
                'post_status'    => 'publish',
                'fields'         => 'ids',
                'posts_per_page' => -1
            ) );

            $data         = $this->prepare_data_for_flot( $range , $query->posts );
            $total_clicks = $this->count_total_clicks_from_flot_data( $data );

            $response = array(
                'status'       => 'success',
                'flot_data'    => $data,
                'total_clicks' => $total_clicks,
            );

        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
    }




    /*
    |--------------------------------------------------------------------------
    | Reports Structure
    |--------------------------------------------------------------------------
    */

    /**
     * Get all registered reports.
     *
     * @since 3.0.0
     * @access public
     *
     * @return array Settings sections.
     */
    public function get_all_reports() {

        return apply_filters( 'ta_register_reports' , array() );
    }

    /**
     * Get current loaded report.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string $tab Current report tab.
     * @return array Current loaded report.
     */
    public function get_current_report( $tab = '' ) {

        if ( ! $tab )
            $tab = isset( $_GET[ 'tab' ] ) ? esc_attr( $_GET[ 'tab' ] ) : 'link_performance';

        // get all registered sections and fields
        $reports = $this->get_all_reports();

        return isset( $reports[ $tab ] ) ? $reports[ $tab ] : array();
    }

    /**
     * Register link performance report.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $reports Array list of all registered reports.
     * @return array Array list of all registered reports.
     */
    public function register_link_performance_report( $reports ) {

        $reports[ 'link_performance' ] = array(
            'id'      => 'ta_link_performance_report',
            'tab'     => 'link_performance',
            'name'    => __( 'Link Overview' , 'thirstyaffiliates' ),
            'title'   => __( 'Link Overview Report' , 'thirstyaffiliates' ),
            'desc'    => __( 'Total clicks on affiliate links over a given period.' , 'thirstyaffiliates' ),
            'content' => function() { return $this->get_link_performance_report_content(); }
        );

        return $reports;
    }




    /*
    |--------------------------------------------------------------------------
    | Display Report
    |--------------------------------------------------------------------------
    */

    /**
     * Register reports menu page.
     *
     * @since 3.0.0
     * @since 3.2.2 Access to the settings page will now be controlled by the plugin. see Bootstrap::admin_interface_visibility.
     *
     * @access public
     */
    public function add_reports_submenu() {

        if ( ! current_user_can( 'edit_posts' ) ) return;

        add_submenu_page(
            'edit.php?post_type=thirstylink',
            __( 'ThirstyAffiliates Reports' , 'thirstyaffiliates' ),
            __( 'Reports' , 'thirstyaffiliates' ),
            'read',
            'thirsty-reports',
            array( $this, 'render_reports' )
        );
    }

    /**
     * Render reports page.
     *
     * @since 3.0.0
     * @access public
     */
    public function render_reports() {

        // fetch current section
        $current_report = $this->get_current_report();
        $report_content = is_callable( $current_report[ 'content' ] ) ? $current_report[ 'content' ]() : $current_report[ 'content' ];

        // skip if section data is empty
        if ( empty( $current_report ) ) return; ?>

        <div class="ta-settings ta-settings-<?php echo $current_report[ 'tab' ]; ?> wrap">

            <?php $this->render_reports_nav(); ?>

            <h1><?php echo $current_report[ 'title' ]; ?></h1>
            <p class="desc"><?php echo $current_report[ 'desc' ]; ?></p>

            <?php echo $report_content; ?>
        </div>
        <?php
    }

    /**
     * Render the settings navigation.
     *
     * @since 3.0.0
     * @access public
     */
    public function render_reports_nav() {

        $reports  = $this->get_all_reports();
        $current  = $this->get_current_report();
        $base_url = admin_url( 'edit.php?post_type=thirstylink&page=thirsty-reports' );

        if ( empty( $reports ) ) return; ?>

        <nav class="thirsty-nav-tab">
            <?php foreach ( $reports as $report ) : ?>

                <a href="<?php echo $base_url . '&tab=' . $report[ 'tab' ]; ?>" class="tab <?php echo ( $current[ 'tab' ] === $report[ 'tab' ] ) ? 'tab-active' : ''; ?>">
                    <?php echo $report[ 'name' ]; ?>
                </a>

            <?php endforeach; ?>
        </nav>

        <?php
    }

    /**
     * Get Link performance report content.
     *
     * @since 3.0.0
     * @since 3.3.2 Remove report data query on page first load.
     * @access public
     *
     * @return string Link performance report content.
     */
    public function get_link_performance_report_content() {

        $cpt_slug      = Plugin_Constants::AFFILIATE_LINKS_CPT;
        $current_range = isset( $_GET[ 'range' ] ) ? sanitize_text_field( $_GET[ 'range' ] ) : '7day';
        $start_date    = isset( $_GET[ 'start_date' ] ) ? sanitize_text_field( $_GET[ 'start_date' ] ) : '';
        $end_date      = isset( $_GET[ 'end_date' ] ) ? sanitize_text_field( $_GET[ 'end_date' ] ) : '';
        $link_id       = isset( $_GET[ 'link_id' ] ) ? sanitize_text_field( $_GET[ 'link_id' ] ) : '';
        $range         = $this->get_report_range_details( $current_range , $start_date , $end_date );
        $range_nav     = apply_filters( 'ta_link_performances_report_nav' , array(
            'year'       => __( 'Year' , 'thirstyaffiliates' ),
            'last_month' => __( 'Last Month' , 'thirstyaffiliates' ),
            'month'      => __( 'This Month' , 'thirstyaffiliates' ),
            '7day'       => __( 'Last 7 Days' , 'thirstyaffiliates' )
        ) );

        // make sure link_id is an affiliate link (published).
        // NOTE: when false, this needs to return an empty string as it is used for display.
        if ( $link_id ) $link_id = ( get_post_type( $link_id ) == $cpt_slug && get_post_status( $link_id ) == 'publish' ) ? $link_id : '';

        ob_start();
        include( $this->_constants->VIEWS_ROOT_PATH() . 'reports/link-performance-report.php' );

        return ob_get_clean();
    }




    /*
    |--------------------------------------------------------------------------
    | Helper methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get report range details.
     *
     * @since 3.0.0
     * @since 3.2.2 Change method of getting timezone sting name.
     * @access public
     *
     * @param string $range      Report range type.
     * @param string $start_date Starting date of range.
     * @param string $end_date   Ending date of range.
     * @return array Report range details.
     */
    public function get_report_range_details( $range = '7day' , $start_date = 'now -6 days' , $end_date = 'now' ) {

        $data       = array();
        $zone_str   = $this->get_report_timezone_string();
        $timezone   = new \DateTimeZone( $zone_str );
        $now        = new \DateTime( 'now' , $timezone );

        switch ( $range ) {

            case 'year' :
                $data[ 'type' ]       = 'year';
                $data[ 'start_date' ] = new \DateTime( 'first day of January' . date( 'Y' ) , $timezone );
                $data[ 'end_date' ]   = $now;
                break;

            case 'last_month' :
                $data[ 'type' ]       = 'last_month';
                $data[ 'start_date' ] = new \DateTime( 'first day of last month' , $timezone );
                $data[ 'end_date' ]   = new \DateTime( 'last day of last month' , $timezone );
                break;

            case 'month' :
                $data[ 'type' ]       = 'month';
                $data[ 'start_date' ] = new \DateTime( 'first day of this month' , $timezone );
                $data[ 'end_date' ]   = $now;
                $data[ 'start_date' ]->setTime( 0 , 0 , 0 );
                break;

            case 'custom' :
                $data[ 'type' ] = 'custom';

                try {
                    $data[ 'start_date' ] = new \DateTime( $start_date , $timezone );
                    $data[ 'end_date' ]   = new \DateTime( $end_date . ' 23:59:59' , $timezone );
                } catch ( \Exception $e ) {
                    $start_date = new \DateTime( 'now -6 days' , $timezone );

                    // set hours, minutes and seconds to zero
                    $start_date->setTime( 0 , 0 , 0 );
                    $now->setTime( 23 , 59 , 59 );

                    $data[ 'type' ]       = '7day';
                    $data[ 'start_date' ] = $start_date;
                    $data[ 'end_date' ]   = $now;
                }

                break;

            case '7day' :
            default :
                $start_date = new \DateTime( 'now -6 days' , $timezone );

                // set hours, minutes and seconds to zero
                $start_date->setTime( 0 , 0 , 0 );
                $now->setTime( 23 , 59 , 59 );

                $data[ 'type' ]       = '7day';
                $data[ 'start_date' ] = $start_date;
                $data[ 'end_date' ]   = $now;
                break;
        }

        return apply_filters( 'ta_report_range_data' , $data , $range );
    }

    /**
     * Prepare data to feed for jQuery flot.
     *
     * @since 3.0.0
     * @since 3.2.2 Change method of getting timezone sting name.
     * @since 3.3.3 Set range timezone to UTC before fetching raw data.
     * @since 3.3.4 Set date range timezone back to local browser before setting start time to zero.
     * @access public
     *
     * @param array $range    Report range details
     * @param array $link_ids Affiliate Link post ID
     * @return array Processed data for jQuery flot.
     */
    public function prepare_data_for_flot( $range , $link_ids ) {

        $start_date = $range[ 'start_date' ];
        $end_date   = $range[ 'end_date' ];
        $zone_str   = $this->get_report_timezone_string();
        $timezone   = new \DateTimeZone( $zone_str );
        $utc        = new \DateTimeZone( 'UTC' );
        $flot_data  = array();

        $start_date->setTimezone( $timezone );
        $end_date->setTimezone( $timezone );

        if ( apply_filters( 'ta_report_set_start_date_time_to_zero' , true , $range ) )
            $start_date->setTime( 0 , 0 );

        $start_date->setTimezone( $utc );
        $end_date->setTimezone( $utc );

        $raw_data = $this->get_link_performance_data( $start_date->format( 'Y-m-d H:i:s' ) , $end_date->format( 'Y-m-d H:i:s' ) , $link_ids );

        // get number of days difference between start and end
        $incrementor    = apply_filters( 'ta_report_flot_data_incrementor' , ( 60 * 60 * 24 ) , $range );
        $timestamp_diff = ( $start_date->getTimestamp() - $end_date->getTimestamp() );
        $days_diff      = abs( floor( $timestamp_diff / $incrementor ) );

        // save the timestamp for first day
        $timestamp      = $start_date->format( 'U' );
        $month_time     = $this->get_month_first_day_datetime_obj( 'February' );
        $next_timestamp = ( $range[ 'type' ] == 'year' ) ? $month_time->format( 'U' ) : $timestamp + $incrementor;
        $flot_data[]    = array(
            'timestamp'      => (int) $timestamp,
            'count'          => 0,
            'next_timestamp' => $next_timestamp
        );

        if ( $range[ 'type' ] == 'year' ) {

            $months = array( 'February' , 'March' , 'April' , 'May' , 'June' , 'July' , 'August' , 'September' , 'October' , 'November' , 'December' );

            foreach ( $months as $key => $month ) {

                $month_time = $this->get_month_first_day_datetime_obj( $month );
                $next_month = isset( $months[ $key + 1 ] ) ? $this->get_month_first_day_datetime_obj( $months[ $key + 1 ] ) : new \DateTime( 'now' , $timezone );

                $flot_data[] = array(
                    'timestamp'      => $month_time->format( 'U' ),
                    'count'          => 0,
                    'next_timestamp' => $next_month->format( 'U' )
                );

                if ( $end_date->format( 'F' ) == $month )
                    break;
            }

        } else {

            // determine timestamps for succeeding days
            for ( $x = 1; $x < $days_diff; $x++ ) {

                $timestamp      = $next_timestamp;
                $next_timestamp = $timestamp + $incrementor;

                $flot_data[] = array(
                    'timestamp'      => (int) $timestamp,
                    'count'          => 0,
                    'next_timestamp' => $next_timestamp
                );

            }
        }

        // count each click data and assign to appropriate day.
        foreach ( $raw_data as $click_entry ) {

            $click_date = new \DateTime( $click_entry->date_clicked , $utc );
            $click_date->setTimezone( $timezone );

            $click_timestamp = (int) $click_date->format( 'U' );

            foreach ( $flot_data as $key => $day_data ) {

                if ( $click_timestamp >= $day_data[ 'timestamp' ] && $click_timestamp < $day_data[ 'next_timestamp' ] ) {
                    $flot_data[ $key ][ 'count' ] += 1;
                    continue;
                }
            }
        }

        // convert $flot_data array into non-associative array
        foreach ( $flot_data as $key => $day_data ) {

            unset( $day_data[ 'next_timestamp' ] );

            $day_data[ 'timestamp' ] = $day_data[ 'timestamp' ] * 1000;
            $flot_data[ $key ] = array_values( $day_data );
        }

        return $flot_data;
    }

    /**
     * Count total clicks from flot data.
     *
     * @since 3.2.1
     * @access public
     *
     * @param array $data  Flot data.
     * @param int   $total Total click counts (offset).
     * @return int Total click clounts.
     */
    public function count_total_clicks_from_flot_data( $data , $total = 0 ) {

        if ( ! is_array( $data ) || empty( $data ) )
            return;

        foreach ( $data as $flot )
            $total += intval( $flot[1] );

        return $total;
    }

    /**
     * Get the DateTime object of the first day of a given month.
     *
     * @since 3.0.0
     * @since 3.2.2 Change method of getting timezone sting name.
     * @access public
     *
     * @param string $month Month full textual representation.
     * @return DateTime First day of the given month DateTime object.
     */
    public function get_month_first_day_datetime_obj( $month ) {

        $zone_str  = $this->get_report_timezone_string();
        $timezone  = new \DateTimeZone( $zone_str );

        return new \DateTime( 'First day of ' . $month . ' ' . date( 'Y' ) , $timezone );
    }

    /**
     * Schedule stats trimmer cron job.
     *
     * @since 3.1.0
     * @access private
     */
    private function schedule_stats_trimmer_cron() {

        $zone_str = $this->_helper_functions->get_site_current_timezone();
        $timezone = new \DateTimeZone( $zone_str );
        $time     = new \DateTime( 'first day of next month' , $timezone );

        // clear all scheduled crons so there will always only be one.
        wp_clear_scheduled_hook( Plugin_Constants::CRON_STATS_TRIMMER );

        // schedule cron job
        wp_schedule_single_event( $time->format( 'U' ) , Plugin_Constants::CRON_STATS_TRIMMER );
    }

    /**
     * Implement stats trimmer
     *
     * @since 3.1.0
     * @access public
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     */
    public function implement_stats_trimmer() {

        global $wpdb;

        $trim_point = (int) get_option( 'ta_stats_trimer_set_point' , 0 );

        if ( $trim_point > 0 ) {

            $clicks_db      = $wpdb->prefix . Plugin_Constants::LINK_CLICK_DB;
            $clicks_meta_db = $wpdb->prefix . Plugin_Constants::LINK_CLICK_META_DB;

            // get click ids based on set range.
            $query      = "SELECT id FROM $clicks_db WHERE date_clicked < DATE_ADD( NOW() , INTERVAL -" . $trim_point . " MONTH )";
            $click_ids  = $wpdb->get_col( $query );

            // Proceed on deleting data when $click_ids are present
            if ( is_array( $click_ids ) && ! empty( $click_ids ) ) {

                $click_ids_string = implode( ',', $click_ids );

                // delete click data
                $wpdb->query( "DELETE FROM $clicks_meta_db WHERE click_id IN ( $click_ids_string )" );
                $wpdb->query( "DELETE FROM $clicks_db WHERE id IN ( $click_ids_string )" );
            }
        }

        // reschedule the cron job
        $this->schedule_stats_trimmer_cron();
    }

    /**
     * Prevent saving click data if useragent is a bot (for non-apache servers).
     *
     * @since 3.1.0
     * @since 3.3.3 Moved code to a helper function (DRY).
     * @access public
     *
     * @param  boolean $skip Whether to skip click tracking.
     * @return boolean       True if needs to be prevented, false otherwise.
     */
    public function prevent_save_click_if_useragent_is_bot( $skip ) {

        if ( $this->_helper_functions->is_user_agent_bot() ) {
            $skip = true;
        }

        return $skip;
    }

    /**
     * Delete stats data when an affiliate link is deleted permanently.
     *
     * @since 3.0.1
     * @access public
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param int $link_id Affiliate link ID.
     */
    public function delete_stats_data_on_affiliate_link_deletion( $link_id ) {

        global $wpdb;

        if ( Plugin_Constants::AFFILIATE_LINKS_CPT !== get_post_type( $link_id ) )
            return;

        $link_click_db      = $wpdb->prefix . Plugin_Constants::LINK_CLICK_DB;
        $link_click_meta_db = $wpdb->prefix . Plugin_Constants::LINK_CLICK_META_DB;
        $click_ids          = $wpdb->get_col( "SELECT id FROM $link_click_db WHERE link_id = $link_id" );

        if ( ! is_array( $click_ids ) || empty( $click_ids ) )
            return;

        $click_ids_str = implode( ',' , $click_ids );

        // delete click meta records.
        $wpdb->query( "DELETE FROM $link_click_meta_db WHERE click_id IN ( $click_ids_str )" );

        // delete click records.
        $wpdb->query( "DELETE FROM $link_click_db WHERE id IN ( $click_ids_str )" );
    }

    /**
     * Get timezone to use for the report.
     *
     * @since 3.2.2
     * @since 3.3.3 Made the method public so TAP can utilize it.
     * @access public
     *
     * @return string Timezone string name.
     */
    public function get_report_timezone_string() {

        return $this->_browser_zone_str ? $this->_browser_zone_str : $this->_helper_functions->get_site_current_timezone();
    }




    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
    */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 3.0.0
     * @access public
     * @implements ThirstyAffiliates\Interfaces\Activatable_Interface
     */
    public function activate() {

        $this->schedule_stats_trimmer_cron();
    }

    /**
     * Method that houses codes to be executed on init hook.
     *
     * @since 3.0.0
     * @access public
     * @implements \ThirstyAffiliates\Interfaces\Initiable_Interface
     */
    public function initialize() {

        // When module is disabled in the settings, then it shouldn't run the whole class.
        if ( get_option( 'ta_enable_stats_reporting_module' , 'yes' ) !== 'yes' )
            return;

        add_action( 'wp_ajax_ta_click_data_redirect' , array( $this , 'ajax_save_click_data_on_redirect' ) );
        add_action( 'wp_ajax_nopriv_ta_click_data_redirect' , array( $this , 'ajax_save_click_data_on_redirect' ) );
        add_action( 'wp_ajax_ta_fetch_report_by_linkid' , array( $this , 'ajax_fetch_report_by_linkid' ) );
        add_action( 'wp_ajax_ta_init_first_report' , array( $this , 'ajax_init_first_report' ) );
    }

    /**
     * Execute ajax handler.
     *
     * @since 3.0.0
     * @access public
     * @implements \ThirstyAffiliates\Interfaces\Model_Interface
     */
    public function run() {

        // When module is disabled in the settings, then it shouldn't run the whole class.
        if ( get_option( 'ta_enable_stats_reporting_module' , 'yes' ) !== 'yes' )
            return;

        add_filter( 'ta_filter_before_save_click' , array( $this , 'prevent_save_click_if_useragent_is_bot' ) , 10 , 1 );
        add_action( 'ta_before_link_redirect' , array( $this , 'save_click_data_on_redirect' ) , 10 , 3 );
        add_action( 'admin_menu' , array( $this , 'add_reports_submenu' ) , 10 );
        add_action( 'ta_register_reports' , array( $this , 'register_link_performance_report' ) , 10 );
        add_action( Plugin_Constants::CRON_STATS_TRIMMER , array( $this , 'implement_stats_trimmer' ) );
        add_action( 'before_delete_post' , array( $this , 'delete_stats_data_on_affiliate_link_deletion' ) , 10 );

        add_filter( 'ta_admin_interfaces' , array( $this , 'register_admin_interfaces' ) );
        add_filter( 'ta_menu_items' , array( $this , 'register_admin_menu_items' ) );
    }
}
