<?php
/**
 * Plugin Name: ThirstyAffiliates
 * Plugin URI: http://thirstyaffiliates.com/
 * Description: ThirstyAffiliates is a revolution in affiliate link management. Collect, collate and store your affiliate links for use in your posts and pages.
 * Version: 3.10.5
 * Author: Caseproof
 * Author URI: https://caseproof.com/
 * Requires at least: 5.0
 * Tested up to: 5.9
 *
 * Text Domain: thirstyaffiliates
 * Domain Path: /languages/
 *
 * @package ThirstyAffiliates
 * @category Core
 * @author Rymera Web Co
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

use ThirstyAffiliates\Models\Bootstrap;
use ThirstyAffiliates\Models\Migration;
use ThirstyAffiliates\Models\Marketing;
use ThirstyAffiliates\Models\Script_Loader;
use ThirstyAffiliates\Models\Settings;
use ThirstyAffiliates\Models\Stats_Reporting;
use ThirstyAffiliates\Models\Affiliate_Links_CPT;
use ThirstyAffiliates\Models\Affiliate_Link;
use ThirstyAffiliates\Models\Affiliate_Link_Attachment;
use ThirstyAffiliates\Models\Link_Fixer;
use ThirstyAffiliates\Models\Rewrites_Redirection;
use ThirstyAffiliates\Models\Link_Picker;
use ThirstyAffiliates\Models\Shortcodes;
use ThirstyAffiliates\Models\Guided_Tour;
use ThirstyAffiliates\Models\REST_API;

/**
 * Register plugin autoloader.
 *
 * @since 3.0.0
 *
 * @param string $class_name Name of the class to load.
 */
spl_autoload_register( function( $class_name ) {

    if ( strpos( $class_name , 'ThirstyAffiliates\\' ) === 0 ) { // Only do autoload for our plugin files

        $class_file  = str_replace( array( '\\' , 'ThirstyAffiliates' . DIRECTORY_SEPARATOR ) , array( DIRECTORY_SEPARATOR , '' ) , $class_name ) . '.php';

        require_once plugin_dir_path( __FILE__ ) . $class_file;

    }

} );

/**
 * The main plugin class.
 */
class ThirstyAffiliates extends Abstract_Main_Plugin_Class {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Single main instance of Plugin ThirstyAffiliates plugin.
     *
     * @since 3.0.0
     * @access private
     * @var ThirstyAffiliates
     */
    private static $_instance;

    /**
     * Array of missing external plugins that this plugin is depends on.
     *
     * @since 3.0.0
     * @access private
     * @var array
     */
    private $_failed_dependencies;




    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    /**
     * ThirstyAffiliates constructor.
     *
     * @since 3.0.0
     * @since 3.0.0 Added the admin_notices when the free plugins was activated 48 hours ago and the Pro version has not been installed
     * @access public
     */
    public function __construct() {

        register_deactivation_hook( __FILE__ , array( $this , 'general_deactivation_code' ) );

        if ( $this->_check_plugin_dependencies() !== true ) {

            // Display notice that plugin dependency is not present.
            add_action( 'admin_notices' , array( $this , 'missing_plugin_dependencies_notice' ) );

        } elseif ( $this->_check_plugin_dependency_version_requirements() !== true ) {

            // Display notice that some dependent plugin did not meet the required version.
            add_action( 'admin_notices' , array( $this , 'invalid_plugin_dependency_version_notice' ) );

        } else {

            // Lock 'n Load
            $this->_initialize_plugin_components();
            $this->_run_plugin();

        }

    }

    /**
     * Ensure that only one instance of Plugin Boilerplate is loaded or can be loaded (Singleton Pattern).
     *
     * @since 3.0.0
     * @access public
     *
     * @return ThirstyAffiliates
     */
    public static function get_instance() {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self();

        return self::$_instance;

    }

    /**
     * Check for external plugin dependencies.
     *
     * @since 3.0.0
     * @access private
     *
     * @return mixed Array if there are missing plugin dependencies, True if all plugin dependencies are present.
     */
    private function _check_plugin_dependencies() {

        // Makes sure the plugin is defined before trying to use it
        if ( !function_exists( 'is_plugin_active' ) )
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $this->failed_dependencies = array();


        // Sample below
        /*
        if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

            $this->failed_dependencies[] = array(
                'plugin-key'       => 'woocommerce',
                'plugin-name'      => 'WooCommerce', // We don't translate this coz this is the plugin name
                'plugin-base-name' => 'woocommerce/woocommerce.php'
            );

        }
        */

        return !empty( $this->failed_dependencies ) ? $this->failed_dependencies : true;

    }

    /**
     * Check plugin dependency version requirements.
     *
     * @since 3.0.0
     * @access private
     *
     * @return boolean True if plugin dependency version requirement is meet, False otherwise.
     */
    private function _check_plugin_dependency_version_requirements() {

        // Sample below
        /*
        $teo_plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/timed-email-offers/timed-email-offers.php' );

        // TEOP 3.0.0 requires TEO 1.1.0
        return version_compare( $teo_plugin_data[ 'Version' ] , '1.1.0' , ">=" );
        */

        return true;

    }

    /**
     * Add notice to notify users that some plugin dependencies of this plugin is missing.
     *
     * @since 3.0.0
     * @access public
     */
    public function missing_plugin_dependencies_notice() {

        if ( !empty( $this->failed_dependencies ) ) {

            $admin_notice_msg = '';

            foreach ( $this->failed_dependencies as $failed_dependency ) {

                $failed_dep_plugin_file = trailingslashit( WP_PLUGIN_DIR ) . plugin_basename( $failed_dependency[ 'plugin-base-name' ] );

                if ( file_exists( $failed_dep_plugin_file ) )
                    $failed_dep_install_text = '<a href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $failed_dependency[ 'plugin-base-name' ] . '&amp;plugin_status=all&amp;s' , 'activate-plugin_' . $failed_dependency[ 'plugin-base-name' ] ) . '" title="' . __( 'Activate this plugin' , 'thirstyaffiliates' ) . '" class="edit">' . __( 'Click here to activate &rarr;' , 'thirstyaffiliates' ) . '</a>';
                else
                    $failed_dep_install_text = '<a href="' . wp_nonce_url( 'update.php?action=install-plugin&amp;plugin=' . $failed_dependency[ 'plugin-key' ] , 'install-plugin_' . $failed_dependency[ 'plugin-key' ] ) . '" title="' . __( 'Install this plugin' , 'thirstyaffiliates' ) . '">' . __( 'Click here to install from WordPress.org repo &rarr;' , 'thirstyaffiliates' ) . '</a>';

                $admin_notice_msg .= sprintf( __( '<br/>Please ensure you have the <a href="%1$s" target="_blank">%2$s</a> plugin installed and activated.<br/>' , 'thirstyaffiliates' ) , 'http://wordpress.org/plugins/' . $failed_dependency[ 'plugin-key' ] . '/' , $failed_dependency[ 'plugin-name' ] );
                $admin_notice_msg .= $failed_dep_install_text . '<br/>';

            } ?>

            <div class="error">
                <p>
                    <?php _e( '<b>ThirstyAffiliates</b> plugin missing dependency.<br/>' , 'thirstyaffiliates' ); ?>
                    <?php echo $admin_notice_msg; ?>
                </p>
            </div>

        <?php }

    }

    /**
     * Add notice to notify user that some plugin dependencies did not meet the required version for the current version of this plugin.
     *
     * @since 3.0.0
     * @access public
     */
    public function invalid_plugin_dependency_version_notice() {

        // Sample below
        /*
        $update_text = sprintf( __( '<a href="%1$s">Click here to update Timed Email Offers &rarr;</a>' , 'timed-email-offers-premium' ) , wp_nonce_url( 'update.php?action=upgrade-plugin&plugin=timed-email-offers' , 'upgrade-plugin_timed-email-offers' ) ); ?>

        <div class="error">
            <p><?php echo sprintf( __( 'Please ensure you have the latest version of <a href="%1$s" target="_blank">Timed Email Offers</a> plugin installed and activated.' , 'timed-email-offers-premium' ) , 'http://wordpress.org/plugins/timed-email-offers/' ); ?></p>
            <p><?php echo $update_text; ?></p>
        </div>

        <?php
        */

    }

    /**
     * Function that get's executed always whether dependecy are present/valid or not.
     *
     * @since 3.0.0
     * @access public
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param boolean $network_wide Flag that determines whether the plugin has been activated network wid ( on multi site environment ) or not.
     */
    public function general_deactivation_code( $network_wide ) {

        // Delete the flag that determines if plugin activation code is triggered
        global $wpdb;

        // check if it is a multisite network
        if ( is_multisite() ) {

            // check if the plugin has been activated on the network or on a single site
            if ( $network_wide ) {

                // get ids of all sites
                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    delete_option( 'ta_activation_code_triggered' );

                }

                restore_current_blog();

            } else
                delete_option( 'ta_activation_code_triggered' ); // activated on a single site, in a multi-site

        } else
            delete_option( 'ta_activation_code_triggered' ); // activated on a single site

    }

    /**
     * Initialize plugin components.
     *
     * @since 3.0.0
     * @access private
     */
    private function _initialize_plugin_components() {

        $plugin_constants = Plugin_Constants::get_instance( $this );
        $helper_functions = Helper_Functions::get_instance( $this , $plugin_constants );

        $settings    = Settings::get_instance( $this , $plugin_constants , $helper_functions );
        $migration   = Migration::get_instance( $this , $plugin_constants , $helper_functions );
        $marketing   = Marketing::get_instance( $this , $plugin_constants , $helper_functions );
        $guided_tour = Guided_Tour::get_instance( $this , $plugin_constants , $helper_functions );
        $stats       = Stats_Reporting::get_instance( $this , $plugin_constants , $helper_functions );
        $rest_api    = REST_API::get_instance( $this , $plugin_constants , $helper_functions );
        $rewrites    = Rewrites_Redirection::get_instance( $this , $plugin_constants , $helper_functions );
        $link_picker = Link_Picker::get_instance( $this , $plugin_constants , $helper_functions );

        $activatables   = array( $settings , $stats , $migration , $marketing , $guided_tour );
        $deactivatables = array( $rewrites );

        $initiables   = array(
            $settings,
            Affiliate_Links_CPT::get_instance( $this , $plugin_constants , $helper_functions ),
            Affiliate_Link_Attachment::get_instance( $this , $plugin_constants , $helper_functions ),
            Link_Fixer::get_instance( $this , $plugin_constants , $helper_functions ),
            $link_picker,
            $stats,
            $migration,
            $marketing,
            $guided_tour,
            $rest_api
        );

        Bootstrap::get_instance( $this , $plugin_constants , $helper_functions , $activatables , $initiables , $deactivatables );
        Script_Loader::get_instance( $this , $plugin_constants , $helper_functions , $guided_tour );

        Shortcodes::get_instance( $this , $plugin_constants , $helper_functions );

    }

    /**
     * Run the plugin. ( Runs the various plugin components ).
     *
     * @since 3.0.0
     * @access private
     */
    private function _run_plugin() {

        foreach ( $this->__all_models as $model )
            if ( $model instanceof Model_Interface )
                $model->run();

    }

}

/**
 * Returns the main instance of ThirstyAffiliates to prevent the need to use globals.
 *
 * @since  3.0.0
 * @return ThirstyAffiliates Main instance of the plugin.
 */
function ThirstyAffiliates() {

    return ThirstyAffiliates::get_instance();

}

// Let's Roll!
$GLOBALS[ 'thirstyaffiliates' ] = ThirstyAffiliates();
