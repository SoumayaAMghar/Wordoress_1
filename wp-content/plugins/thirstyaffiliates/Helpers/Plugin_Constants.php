<?php
namespace ThirstyAffiliates\Helpers;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Model that houses all the plugin constants.
 * Note as much as possible, we need to make this class succinct as the only purpose of this is to house all the constants that is utilized by the plugin.
 * Therefore we omit class member comments and minimize comments as much as possible.
 * In fact the only verbouse comment here is this comment you are reading right now.
 * And guess what, it just got worse coz now this comment takes 5 lines instead of 3.
 *
 * @since 3.0.0
 */
class Plugin_Constants {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    private static $_instance;

    // Plugin configuration constants
    const TOKEN               = 'ta';
    const INSTALLED_VERSION   = 'ta_installed_version';
    const VERSION             = '3.10.5';
    const TEXT_DOMAIN         = 'thirstyaffiliates';
    const THEME_TEMPLATE_PATH = 'thirstyaffiliates';
    const META_DATA_PREFIX    = '_ta_';

    // CPT Taxonomy constants
    const AFFILIATE_LINKS_CPT   = 'thirstylink';
    const AFFILIATE_LINKS_TAX   = 'thirstylink-category';
    const DEFAULT_LINK_CATEGORY = 'Uncategorized';

    // CRON
    const CRON_REQUEST_REVIEW          = 'ta_cron_request_review';
    const CRON_MIGRATE_OLD_PLUGIN_DATA = 'ta_cron_migrate_old_plugin_data';
    const CRON_STATS_TRIMMER           = 'ta_cron_stats_trimmer';

    // Options
    const SHOW_REQUEST_REVIEW     = 'ta_show_request_review';
    const REVIEW_REQUEST_RESPONSE = 'ta_request_review_response';
    const MIGRATION_COMPLETE_FLAG = 'ta_migration_complete_flag';

    // Settings Constants
    const DEFAULT_BLOCKED_BOTS = 'googlebot,bingbot,Slurp,DuckDuckBot,Baiduspider,YandexBot,Sogou,Exabot,facebo,ia_archiver';

    // DB Tables
    const LINK_CLICK_DB      = 'ta_link_clicks';
    const LINK_CLICK_META_DB = 'ta_link_clicks_meta';

    // Help Section
    const CLEAN_UP_PLUGIN_OPTIONS = 'ta_clean_up_plugin_options';




    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    public function __construct( Abstract_Main_Plugin_Class $main_plugin ) {

        // Path constants
        $this->_MAIN_PLUGIN_FILE_PATH = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'thirstyaffiliates' . DIRECTORY_SEPARATOR . 'thirstyaffiliates.php';
        $this->_PLUGIN_DIR_PATH       = plugin_dir_path( $this->_MAIN_PLUGIN_FILE_PATH );
        $this->_PLUGIN_DIR_URL        = plugin_dir_url( $this->_MAIN_PLUGIN_FILE_PATH );
        $this->_PLUGIN_DIRNAME        = plugin_basename( dirname( $this->_MAIN_PLUGIN_FILE_PATH ) );
        $this->_PLUGIN_BASENAME       = plugin_basename( $this->_MAIN_PLUGIN_FILE_PATH );

        $this->_CSS_ROOT_URL          = $this->_PLUGIN_DIR_URL . 'css/';
        $this->_IMAGES_ROOT_URL       = $this->_PLUGIN_DIR_URL . 'images/';
        $this->_JS_ROOT_URL           = $this->_PLUGIN_DIR_URL . 'js/';

        $this->_VIEWS_ROOT_PATH       = $this->_PLUGIN_DIR_PATH . 'views/';
        $this->_TEMPLATES_ROOT_PATH   = $this->_PLUGIN_DIR_PATH . 'templates/';
        $this->_LOGS_ROOT_PATH        = $this->_PLUGIN_DIR_PATH . 'logs/';

        $main_plugin->add_to_public_helpers( $this );

    }

    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin );

        return self::$_instance;

    }

    public function VERSION() {
        return self::VERSION;
    }

    public function MAIN_PLUGIN_FILE_PATH() {
        return $this->_MAIN_PLUGIN_FILE_PATH;
    }

    public function PLUGIN_DIR_PATH() {
        return $this->_PLUGIN_DIR_PATH;
    }

    public function PLUGIN_DIR_URL() {
        return $this->_PLUGIN_DIR_URL;
    }

    public function PLUGIN_DIRNAME() {
        return $this->_PLUGIN_DIRNAME;
    }

    public function PLUGIN_BASENAME() {
        return $this->_PLUGIN_BASENAME;
    }

    public function CSS_ROOT_URL() {
        return $this->_CSS_ROOT_URL;
    }

    public function IMAGES_ROOT_URL() {
        return $this->_IMAGES_ROOT_URL;
    }

    public function JS_ROOT_URL() {
        return $this->_JS_ROOT_URL;
    }

    public function VIEWS_ROOT_PATH() {
        return $this->_VIEWS_ROOT_PATH;
    }

    public function TEMPLATES_ROOT_PATH() {
        return $this->_TEMPLATES_ROOT_PATH;
    }

    public function LOGS_ROOT_PATH() {
        return $this->_LOGS_ROOT_PATH;
    }

    public function REDIRECT_TYPES() {
        return apply_filters( 'ta_redirect_types' , array(
            '301' => __( '301 Permanent' , 'thirstyaffiliates' ),
            '302' => __( '302 Temporary' , 'thirstyaffiliates' ),
            '307' => __( '307 Temporary (alternative)' , 'thirstyaffiliates' )
        ) );
    }

    // HTAccess Module
    public function HTACCESS_FILE() {
        return untrailingslashit( ABSPATH ) . '/.htaccess';
    }

}
