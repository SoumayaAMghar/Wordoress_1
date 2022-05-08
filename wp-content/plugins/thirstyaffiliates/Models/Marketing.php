<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Activatable_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

/**
 * Model that houses the logic of Marketing of old versions of TA to version 3.0.0
 *
 * @since 3.0.0
 */
class Marketing implements Model_Interface , Activatable_Interface , Initiable_Interface {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that holds the single main instance of Marketing.
     *
     * @since 3.0.0
     * @access private
     * @var Marketing
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
     * @return Marketing
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }

    /**
     * Flag to show review request.
     *
     * @since 3.0.0
     * @access public
     */
    public function flag_show_review_request() {

        update_option( Plugin_Constants::SHOW_REQUEST_REVIEW , 'yes' );

    }

    /**
     * Display the review request admin notice.
     *
     * @since 3.0.0
     * @access public
     */
    public function show_review_request_notice() {

        // Only show to admins
        if ( ! current_user_can( 'manage_options' ) ) {
          return;
        }

        // Check for the constant to disable the prompt
        if ( defined( 'TA_DISABLE_REVIEW_PROMPT' ) && true == TA_DISABLE_REVIEW_PROMPT ) {
          return;
        }

        // Notice has been delayed
        $delayed_option = get_option( 'ta_review_prompt_delay' );
        if ( ! empty( $delayed_option['delayed_until'] ) && time() < $delayed_option['delayed_until'] ) {
          return;
        }

        // Notice has been removed
        if ( get_option( 'ta_review_prompt_removed' ) ) {
          return;
        }

        // Backwards compat
        if ( get_transient( 'ta_review_prompt_delay' ) ) {
          return;
        }

        $review_request_response = get_option( Plugin_Constants::REVIEW_REQUEST_RESPONSE );

        if ( ! is_admin() || get_option( Plugin_Constants::SHOW_REQUEST_REVIEW ) !== 'yes' || ( $review_request_response !== 'review-later' && ! empty( $review_request_response ) ) )
            return;

        ?>
        <div class="notice notice-info is-dismissible ta-review-notice" id="ta_review_notice">
            <div id="ta_review_intro">
                <p><?php _e( 'Are you enjoying using ThirstyAffiliates?', 'thirstyaffiliates' ); ?></p>
                <p><a data-review-selection="yes" class="ta-review-selection" href="#">Yes, I love it</a> 🙂 | <a data-review-selection="no" class="ta-review-selection" href="#">Not really...</a></p>
            </div>
            <div id="ta_review_yes" style="display: none;">
                <p><?php _e( 'That\'s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?', 'thirstyaffiliates' ); ?></p>
                <p style="font-weight: bold;">~ Blair Williams<br>CEO of ThirstyAffiliates</p>
                <p>
                    <a style="display: inline-block; margin-right: 10px;" href="https://wordpress.org/support/plugin/thirstyaffiliates/reviews/?filter=5#new-post" onclick="delayReviewPrompt(event, 'remove', true, true)" target="_blank"><?php esc_html_e( 'Okay, you deserve it', 'thirstyaffiliates' ); ?></a>
                    <a style="display: inline-block; margin-right: 10px;" href="#" onclick="delayReviewPrompt(event, 'delay', true, false)"><?php esc_html_e( 'Nope, maybe later', 'thirstyaffiliates' ); ?></a>
                    <a href="#" onclick="delayReviewPrompt(event, 'remove', true, false)"><?php esc_html_e( 'I already did', 'thirstyaffiliates' ); ?></a>
                </p>
            </div>
            <div id="ta_review_no" style="display: none;">
                <p><?php _e( 'We\'re sorry to hear you aren\'t enjoying ThirstyAffiliates. We would love a chance to improve. Could you take a minute and let us know what we can do better?', 'thirstyaffiliates' ); ?></p>
                <p>
                    <a style="display: inline-block; margin-right: 10px;" href="https://thirstyaffiliates.com/plugin-feedback/?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=request_review" onclick="delayReviewPrompt(event, 'remove', true, true)" target="_blank"><?php esc_html_e( 'Give Feedback', 'thirstyaffiliates' ); ?></a>
                    <a href="#" onclick="delayReviewPrompt(event, 'remove', true, false)"><?php esc_html_e( 'No thanks', 'thirstyaffiliates' ); ?></a>
                </p>
            </div>
        </div>
        <script>

            function delayReviewPrompt(event, type, triggerClick = true, openLink = false) {
              event.preventDefault();
              if ( triggerClick ) {
                jQuery('#ta_review_notice').fadeOut();
              }
              if ( openLink ) {
                var href = event.target.href;
                window.open(href, '_blank');
              }
              jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                  action: 'ta_dismiss_review_prompt',
                  _ajax_nonce: "<?php echo wp_create_nonce( 'ta_dismiss_review_prompt' ) ?>",
                  type: type
                },
              })
              .done(function(data) {

              });
            }

            jQuery(document).ready(function($) {
                $('.ta-review-selection').click(function(event) {
                    event.preventDefault();
                    var $this = $(this);
                    var selection = $this.data('review-selection');
                    $('#ta_review_intro').hide();
                    $('#ta_review_' + selection).show();
                });
                $('body').on('click', '#ta_review_notice .notice-dismiss', function(event) {
                    delayReviewPrompt(event, 'delay', false);
                });
            });
        </script>
        <?php
    }

    /**
     * AJAX dismiss marketing notice.
     *
     * @since 3.2.5
     * @access public
     */
    public function ajax_dismiss_marketing_notice() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( 'manage_options' ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta_dismiss_marketing_notice', false, false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_POST[ 'notice' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Missing required post data' , 'thirstyaffiliates' ) );
        else {

            $notice = sanitize_text_field( $_POST[ 'notice' ] );

            switch( $notice ) {
                case 'enable_js_redirect_notice' :
                    $option = 'ta_show_enable_js_redirect_notice';
                    break;
                default :
                    $option = apply_filters( 'ta_dismiss_marketing_notice_option' , null );
                    break;
            }

            if ( $option ) update_option( $option , 'no' );
            $response = array( 'status' => 'success' );
        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
    }

    /**
     * Add the Pro Features menu link
     *
     * @since 3.0.0
     * @access public
     */
    public function add_pro_features_menu_link() {

        if ( !is_plugin_active( 'thirstyaffiliates-pro/thirstyaffiliates-pro.php' ) && current_user_can( 'manage_options' ) ) {

            global $submenu;

            array_push( $submenu['edit.php?post_type=thirstylink'] , array( '<div id="spfmlt">Pro Features →</div>' , 'manage_options', 'https://thirstyaffiliates.com/pricing/?utm_source=Free%20Plugin&utm_medium=Pro&utm_campaign=Admin%20Menu' ) );

        }

    }

    /**
     * Add the Pro Features menu link target
     *
     * @since 3.0.0
     * @access public
     */
    public function add_pro_features_menu_link_target() {

        ?>
        <script type="text/javascript">
        jQuery(document).ready( function($) {
            $( '#spfmlt' ).parent().attr( 'target' , '_blank' );
        });
        </script>
        <?php

    }

    /**
     * Display enable js redirect notice.
     *
     * @since 3.3.0
     * @since 3.3.5 Only show notice when statistics module is enabled.
     * @access public
     */
    public function display_enable_js_redirect_notice() {

        if ( get_option( 'ta_enable_stats_reporting_module' , 'yes' ) !== 'yes' ) return;

        $post_type = get_post_type();
        if ( !$post_type && isset( $_GET[ 'post_type' ] ) )
            $post_type = $_GET[ 'post_type' ];

        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || $post_type !== Plugin_Constants::AFFILIATE_LINKS_CPT || get_option( 'ta_show_enable_js_redirect_notice' , 'yes' ) !== 'yes' || get_option( 'ta_enable_javascript_frontend_redirect' ) === 'yes' )
            return;

        ?>
        <div class="notice notice-error is-dismissible ta_enable_javascript_redirect_notice">
            <?php
                _e( "<h4>Enable Enhanced Javascript Redirect</h4>
                     <p>ThirstyAffiliates version 3.2.5 introduces a new method of redirecting via javascript which will only run on your website's frontend.
                     We've added this so the plugin can provide more accurate tracking data of your affiliate link clicks.
                     This feature is turned on automatically for <strong>new installs</strong>, but for this install we would like to give you the choice of enabling the feature or not.</p>" , 'thirstyaffiliates' );
            ?>
            <p>
                <button type="button" class="button-primary" id="ta_enable_js_redirect_trigger">
                    <?php _e( 'Enable enhanced javascript redirect feature' , 'thirstyaffiliates' ); ?>
                </button>
            </p>
        </div>
        <script type="text/javascript">
            ( function( $ ) {

                // dismiss notice.
                $( '.ta_enable_javascript_redirect_notice' ).on( 'click' , '.notice-dismiss' , function() {
                    $.ajax( ajaxurl , {
                        type: 'POST',
                        data: {
                            action: 'ta_dismiss_marketing_notice',
                            _ajax_nonce: '<?php echo esc_js( wp_create_nonce( 'ta_dismiss_marketing_notice' ) ); ?>',
                            notice: 'enable_js_redirect_notice'
                        }
                    } );
                } );

                // trigger enable enhanced javascript redirect feature
                $( '.ta_enable_javascript_redirect_notice' ).on( 'click' , '#ta_enable_js_redirect_trigger' , function() {
                    $( '.ta_enable_javascript_redirect_notice .notice-dismiss' ).trigger( 'click' );
                    $.ajax( ajaxurl , {
                        type: 'POST',
                        data: {
                            action: 'ta_enable_js_redirect',
                            _ajax_nonce: '<?php echo esc_js( wp_create_nonce( 'ta_enable_js_redirect' ) ); ?>',
                        }
                    } );
                } );

            } )( jQuery );
        </script>
        <?php
    }

    /**
     * Hide Enable JS redirect setting notice when setting value is changed.
     *
     * @since 3.3.0
     * @access public
     *
     * @param string $value Option value.
     * @return string Filtered option value.
     */
    public function hide_notice_on_enable_js_redirect_setting_change( $value ) {

        update_option( 'ta_show_enable_js_redirect_notice' , 'no' );
        return $value;
    }

    /**
     * AJAX enable JS redirect setting.
     *
     * @since 3.2.5
     * @access public
     */
    public function ajax_enable_js_redirect() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( 'manage_options' ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta_enable_js_redirect', false, false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        else {

            update_option( 'ta_enable_javascript_frontend_redirect' , 'yes' );
            $response = array( 'status' => 'success' );
        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
    }

    /**
     * Add advanced feautures marketing metabox in the sidebar.
     *
     * @since 3.3.0
     * @access public
     *
     * @param array $metabox TA registered metaboxes.
     * @return array Filtered TA registered metaboxes.
     */
    public function add_advanced_features_marketing_metabox( $metaboxes ) {

        if ( ! $this->_helper_functions->is_plugin_active( 'thirstyaffiliates-pro/thirstyaffiliates-pro.php' ) ) {

            $metaboxes[] = array(
                'id'       => 'ta-advanced-features-metabox',
                'title'    => __( 'Advanced Features', 'thirstyaffiliates' ),
                'cb'       => array( $this , 'advanced_features_marketing_metabox_cb' ),
                'sort'     => 40,
                'priority' => 'default'
            );
        }

        return $metaboxes;
    }

    /**
     * Display "Advanced Features" metabox
     *
     * @since 3.3.0
     * @access public
     *
     * @param \WP_Post $post Affiliate link WP_Post object.
     */
    public function advanced_features_marketing_metabox_cb( $post ) {

        $url = esc_url( 'https://thirstyaffiliates.com/pricing/?utm_source=Free%20Plugin&utm_medium=Pro&utm_campaign=Sidebar' );
        $img = esc_url( $this->_constants->IMAGES_ROOT_URL() . 'sidebar.jpg' );
        echo '<a href="' . $url . '" target="_blank"><img src="' . $img . '"></a>';
    }

    public function dismiss_review_prompt() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
            wp_send_json_error( __( 'Invalid AJAX call', 'thirstyaffiliates' ) );
        } elseif ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to do this', 'thirstyaffiliates' ) );
        } elseif ( ! check_ajax_referer( 'ta_dismiss_review_prompt', false, false ) ) {
            wp_send_json_error( __( 'Security Check Failed', 'thirstyaffiliates' ) );
        } elseif ( ! isset( $_POST['type'] ) ) {
            wp_send_json_error( __( 'Missing required post data', 'thirstyaffiliates' ) );
        }

        $type = sanitize_key( wp_unslash( $_POST['type'] ) );

        if ( 'remove' === $type ) {
            update_option( 'ta_review_prompt_removed', true );
            wp_send_json_success( array(
                'status' => 'removed'
            ) );
        } else if ( 'delay' === $type ) {
            update_option( 'ta_review_prompt_delay', array(
              'delayed_until' => time() + WEEK_IN_SECONDS
            ) );
            wp_send_json_success( array(
                'status' => 'delayed'
            ) );
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

        wp_clear_scheduled_hook( Plugin_Constants::CRON_REQUEST_REVIEW );
        wp_schedule_single_event( time() + 1209600 , Plugin_Constants::CRON_REQUEST_REVIEW );

    }

    /**
     * Execute codes that needs to run on plugin initialization.
     *
     * @since 3.0.0
     * @access public
     * @implements \ThirstyAffiliates\Interfaces\Initiable_Interface
     */
    public function initialize() {

        add_action( 'wp_ajax_ta_dismiss_marketing_notice' , array( $this , 'ajax_dismiss_marketing_notice' ) );
        add_action( 'wp_ajax_ta_enable_js_redirect' , array( $this , 'ajax_enable_js_redirect' ) );
        add_action( 'wp_ajax_ta_dismiss_review_prompt' , array( $this, 'dismiss_review_prompt' ) );

    }

    /**
     * Execute Marketing class.
     *
     * @since 3.0.0
     * @access public
     * @implements \ThirstyAffiliates\Interfaces\Model_Interface
     */
    public function run() {

        add_action( Plugin_Constants::CRON_REQUEST_REVIEW , array( $this , 'flag_show_review_request' ) );
        add_action( 'admin_notices' , array( $this , 'show_review_request_notice' ) );
        add_action( 'admin_notices' , array( $this , 'display_enable_js_redirect_notice' ) );
        add_action( 'admin_menu' , array( $this , 'add_pro_features_menu_link' ) , 20 );
        add_action( 'admin_head', array( $this , 'add_pro_features_menu_link_target' ) );
        add_filter( 'option_ta_enable_javascript_frontend_redirect' , array( $this , 'hide_notice_on_enable_js_redirect_setting_change' ) );
        add_filter( 'ta_register_side_metaboxes' , array( $this , 'add_advanced_features_marketing_metabox' ) );
    }

}
