<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

use ThirstyAffiliates\Models\Affiliate_Link;

/**
 * Model that houses the link picker logic.
 *
 * @since 3.0.0
 */
class Link_Picker implements Model_Interface , Initiable_Interface {

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
     * @var Link_Picker
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
     * @return Link_Picker
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }



    /*
    |--------------------------------------------------------------------------
    | Register tinymce buttons and scripts
    |--------------------------------------------------------------------------
    */

    /**
     * Initialize thirsty editor buttons.
     *
     * @since 3.0.0
     * @access public
     */
    public function init_thirsty_editor_buttons() {

        if ( ! is_admin() || ! current_user_can( 'edit_posts' ) )
            return;

        if ( get_option( 'ta_disable_visual_editor_buttons' ) == 'yes' || get_user_option( 'rich_editing' ) != 'true' )
            return;

        add_filter( 'mce_external_plugins' , array( $this , 'load_thirsty_mce_plugin' ) );
		add_filter( 'mce_buttons' , array( $this , 'register_mce_buttons' ) , 5 );

    }

    /**
     * Initialize thirsty editor buttons on page builders.
     *
     * @since 3.4.0
     * @access public
     */
    public function init_thirsty_editor_buttons_for_page_builders() {

        if ( get_option( 'ta_disable_visual_editor_buttons' ) == 'yes' || get_user_option( 'rich_editing' ) != 'true' )
            return;

        if ( ! $this->_helper_functions->is_page_builder_active() || ! current_user_can( 'edit_posts' ) )
            return;

        add_filter( 'mce_external_plugins' , array( $this , 'load_thirsty_mce_plugin' ) , 99999 );
        add_filter( 'mce_buttons' , array( $this , 'register_mce_buttons' ) , 99 );
    }

    /**
     * Load Thirsty Affiliate MCE plugin to TinyMCE.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $mce_plugins Array of all MCE plugins.
     * @return array
     */
    public function load_thirsty_mce_plugin( $mce_plugins ) {

        $mce_plugins[ 'thirstyaffiliates' ] = $this->_constants->JS_ROOT_URL() . 'lib/thirstymce/editor-plugin.js';

	    return $mce_plugins;
    }

    /**
     * Register Thirsty Affiliate MCE buttons.
     *
     * @since 3.0.0
     * @since 3.4.0 Make sure only one instance of the buttons are loaded.
     * @access public
     *
     * @param array $buttons Array of all MCE buttons.
     * @return array
     */
    public function register_mce_buttons( $buttons ) {

        // if buttons are already present, then skip to prevent duplicate.
        if ( in_array( 'thirstyaffiliates_button' , $buttons ) )
            return $buttons;

        $enable_link_picker = current_user_can( apply_filters( 'ta_enable_advance_link_picker' , 'edit_posts' ) );
        $enable_quick_add   = current_user_can( apply_filters( 'ta_enable_quick_add_affiliate_link' , 'publish_posts' ) );

        if ( $enable_link_picker ) array_push( $buttons , 'separator' , 'thirstyaffiliates_button' );
        if ( $enable_quick_add ) array_push( $buttons , 'separator' , 'thirstyaffiliates_quickaddlink_button' );

    	return $buttons;
    }

    /**
     * Register WP Editor style.
     *
     * @since 3.4.0
     * @access public
     */
    public function register_wp_editor_style() {

        add_editor_style( $this->_constants->CSS_ROOT_URL() . 'admin/tinymce/editor.css' );
    }

    /**
     * Register WP Editor style via mce_css filter (for frontend editors).
     *
     * @since 3.4.0
     * @access public
     */
    public function register_wp_editor_style_mce_css( $mce_css ) {

        if ( ! empty( $mce_css ) ) $mce_css .= ',';
            $mce_css .= $this->_constants->CSS_ROOT_URL() . 'admin/tinymce/editor.css';

            return $mce_css;
    }




    /*
    |--------------------------------------------------------------------------
    | Link Picker methods
    |--------------------------------------------------------------------------
    */

    /**
     * Return
     *
     * @since 3.0.0
     * @since 3.4.0 Add support for additional CSS classes field. Add support for external images.
     * @access public
     *
     * @param array  $affiliate_links List of affiliate link IDs.
     * @param bool   $advanced        Boolean check if its advanced or not.
     * @param int    $post_id         ID of the post currently being edited.
     * @param string $result_markup   Search Affiliate Links result markup.
     * @return string Search Affiliate Links result markup
     */
    public function search_affiliate_links_result_markup( $affiliate_links , $advance = false , $post_id = 0 ,  $result_markup = '' ) {

        if ( is_array( $affiliate_links ) && ! empty( $affiliate_links ) ) {

            foreach( $affiliate_links as $link_id ) {

                $thirstylink = new Affiliate_Link( $link_id );
                $rel         = $thirstylink->is( 'no_follow' ) ? 'nofollow' : '';
                $target      = $thirstylink->is( 'new_window' ) ? '_blank' : '';
                $class       = ( get_option( 'ta_disable_thirsty_link_class' ) !== "yes" ) ? 'thirstylink' : '';
                $title       = ( get_option( 'ta_disable_title_attribute' ) !== "yes" ) ? $thirstylink->get_prop( 'name' ) : '';
                $other_atts  = esc_attr( json_encode( apply_filters( 'ta_link_insert_extend_data_attributes' , array() , $thirstylink , $post_id ) ) );

                if ( $thirstylink->get_prop( 'rel_tags' ) )
                    $rel = trim( $rel . ' ' . $thirstylink->get_prop( 'rel_tags' ) );

                if ( $thirstylink->get_prop( 'css_classes' ) )
                    $class = trim( $class . ' ' . $thirstylink->get_prop( 'css_classes' ) );

                if ( $advance ) {

                    $images        = $thirstylink->get_prop( 'image_ids' );
                    $images_markup = '<span class="images-block">';

                    if ( is_array( $images ) && ! empty( $images ) ) {

                        $images_markup .= '<span class="label">' . __( 'Select image:' , 'thirstyaffiliates' ) . '</span>';
                        $images_markup .= '<span class="images">';

                        foreach( $images as $image ) {

                            if ( filter_var( $image , FILTER_VALIDATE_URL ) )
                                $images_markup .= '<span class="image"><img src="'. $image .'" width="75" height="75" data-imgid="' . $image . '" data-type="image"></span>';
                            else
                                $images_markup .= '<span class="image">' . wp_get_attachment_image( $image , array( 75 , 75 ) , false , array( 'data-imgid' => $image , 'data-type' => 'image' ) ) . '</span>';

                        }

                        $images_markup .= '</span>';
                    } else {

                        $images_markup .= '<span class="no-images">' . __( 'No images found' , 'thirstyaffiliates' ) . '</span>';
                    }

                    $images_markup .= '</span>';

                    $result_markup .= '<li class="thirstylink"
                                            data-linkid="' . $thirstylink->get_id() . '"
                                            data-class="' . esc_attr( $class ) . '"
                                            data-title="' . esc_attr( str_replace( '"' , '' , $title ) ) . '"
                                            data-href="' . esc_url( $thirstylink->get_prop( 'permalink' ) ) . '"
                                            data-rel="' . trim( esc_attr( $rel ) ) . '"
                                            data-target="' . esc_attr( $target ) . '"
                                            data-other-atts="' . $other_atts . '">
                                            <span class="name">' . esc_html( mb_strimwidth( $thirstylink->get_prop( 'name' ) , 0 , 44 , "..." ) ) . '</span>
                                            <span class="slug">[' . esc_html ( mb_strimwidth( $thirstylink->get_prop( 'slug' ) , 0 , 35 , "..." ) ) . ']</span>
                                            <span class="actions">
                                                <button type="button" data-type="normal" class="button insert-link-button dashicons dashicons-admin-links" data-tip="' . esc_attr__( 'Insert link' , 'thirstyaffiliates' ) . '"></button>
                                                <button type="button" data-type="shortcode" class="button insert-shortcode-button dashicons dashicons-editor-code" data-tip="' . esc_attr__( 'Insert shortcode' , 'thirstyaffiliates' ) . '"></button>
                                                <button type="button" data-type="image" class="button insert-image-button dashicons dashicons-format-image" data-tip="' . esc_attr__( 'Insert image' , 'thirstyaffiliates' ) . '"></button>
                                            </span>
                                            ' . $images_markup . '
                                        </li>';
                } else {

                    $result_markup .= '<li data-class="' . esc_attr( $class ) . '"
                                           data-title="' . esc_attr( str_replace( '"' , '' , $title ) ) . '"
                                           data-href="' . esc_attr( $thirstylink->get_prop( 'permalink' ) ) . '"
                                           data-rel="' . esc_attr( $rel ) . '"
                                           data-target="' . esc_attr( $target ) . '"
                                           data-link-id="' . esc_attr( $thirstylink->get_id() ) . '"
                                           data-link-insertion-type="' . esc_attr( $this->_helper_functions->get_option( 'ta_link_insertion_type' , 'link' ) ) . '"
                                           data-other-atts="' . $other_atts . '">';
                    $result_markup .= '<strong>' . $link_id . '</strong> : <span>' . esc_html( $thirstylink->get_prop( 'name' ) ) . '</span></li>';

                }

            }

        } else
            $result_markup .= '<li class="no-links-found">' . __( 'No affiliate links found' , 'thirstyaffiliates' ) . '</li>';

        return $result_markup;
    }

    /**
     * Search Affiliate Links Query AJAX function
     *
     * @since 3.0.0
     * @since 3.4.0 Add support for category in the search query.
     * @access public
     */
    public function ajax_search_affiliate_links_query() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( $this->_helper_functions->get_capability_for_interface('thirstylink_list', 'edit_posts') ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_POST[ 'keyword' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Missing required post data' , 'thirstyaffiliates' ) );
        else {

            $paged           = ( isset( $_POST[ 'paged' ] ) && $_POST[ 'paged' ] ) ? $_POST[ 'paged' ] : 1;
            $category        = ( isset( $_POST[ 'category' ] ) && $_POST[ 'category' ] ) ? esc_attr( $_POST[ 'category' ] ) : '';
            $exclude         = ( isset( $_POST[ 'exclude' ] ) && is_array( $_POST[ 'exclude' ] ) && ! empty( $_POST[ 'exclude' ] ) ) ? $_POST[ 'exclude' ] : array();
            $is_gutenberg    = isset( $_POST[ 'gutenberg' ] ) && $_POST[ 'gutenberg' ];
            $with_images     = isset( $_POST[ 'with_images' ] ) && $_POST[ 'with_images' ];
            $affiliate_links = $this->_helper_functions->search_affiliate_links_query( $_POST[ 'keyword' ] , $paged , $category , $exclude , $is_gutenberg , $with_images );
            $advance         = ( isset( $_POST[ 'advance' ] ) && $_POST[ 'advance' ] ) ? true : false;
            $post_id         = isset( $_POST[ 'post_id' ] ) ? intval( $_POST[ 'post_id' ] ) : 0;

            if ( $is_gutenberg ) {
                $response = array( 'status' => 'success' , 'affiliate_links' => $affiliate_links , 'count' => count( $affiliate_links ) );
            } else {
                $result_markup = $this->search_affiliate_links_result_markup( $affiliate_links , $advance , $post_id );
                $response      = array( 'status' => 'success' , 'search_query_markup' => $result_markup , 'count' => count( $affiliate_links ) );
            }

        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
    }

    /**
     * AJAX function to display the advance add affiliate link thickbox content.
     *
     * @since 3.0.0
     * @since 3.4.0 Add category field (selectized) in the search form.
     * @access public
     */
    public function ajax_display_advanced_add_affiliate_link() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! current_user_can( apply_filters( 'ta_enable_advance_link_picker' , 'edit_posts' ) ) )
            wp_die( "You're not allowed to do this." );

        $post_id         = isset( $_REQUEST[ 'post_id' ] ) ? intval( $_REQUEST[ 'post_id' ] ) : 0;
        $affiliate_links = $this->_helper_functions->search_affiliate_links_query();
        $result_markup   = $this->search_affiliate_links_result_markup( $affiliate_links , true , $post_id );
        $html_editor     = isset( $_REQUEST[ 'html_editor' ] ) ? sanitize_text_field( $_REQUEST[ 'html_editor' ] ) : false;

        wp_enqueue_script('editor');
		wp_dequeue_script('jquery-ui-core');
		wp_dequeue_script('jquery-ui-sortable');
		wp_dequeue_script('admin-scripts');
        wp_enqueue_style( 'jquery_tiptip' , $this->_constants->CSS_ROOT_URL() . 'lib/jquery-tiptip/jquery-tiptip.css' , array() , $this->_constants->VERSION() , 'all' );
        wp_enqueue_style( 'ta_advance_link_picker_css' , $this->_constants->JS_ROOT_URL() . 'app/advance_link_picker/dist/advance-link-picker.css' , array( 'dashicons' ) , $this->_constants->VERSION() , 'all' );
        wp_enqueue_style( 'selectize' , $this->_constants->JS_ROOT_URL() . 'lib/selectize/selectize.default.css' , array() , Plugin_Constants::VERSION , 'all' );
        wp_enqueue_script( 'jquery_tiptip' , $this->_constants->JS_ROOT_URL() . 'lib/jquery-tiptip/jquery.tipTip.min.js' , array() , $this->_constants->VERSION() );
        wp_enqueue_script( 'ta_advance_link_picker_js' , $this->_constants->JS_ROOT_URL() . 'app/advance_link_picker/dist/advance-link-picker.js' , array( 'jquery_tiptip' ) , $this->_constants->VERSION() );
        wp_enqueue_script( 'selectize' , $this->_constants->JS_ROOT_URL() . 'lib/selectize/selectize.min.js' , array() , Plugin_Constants::VERSION );

        wp_localize_script( 'ta_advance_link_picker_js', 'ta_advance_link_picker_js_params', array(
            'get_image_markup_nonce' => wp_create_nonce( 'ta_get_image_markup' ),
        ));

        include( $this->_constants->VIEWS_ROOT_PATH() . 'linkpicker/advance-link-picker.php' );

        wp_die();
    }

    /**
     * Get image markup by ID.
     *
     * @since 3.0.0
     * @since 3.4.0 Add support for external images.
     * @access public
     */
    public function ajax_get_image_markup_by_id() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( apply_filters( 'ta_enable_advance_link_picker' , 'edit_posts' ) ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta_get_image_markup', false, false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_REQUEST[ 'imgid' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Missing required post data' , 'thirstyaffiliates' ) );
        else {

            if ( filter_var( $_REQUEST[ 'imgid' ] , FILTER_VALIDATE_URL ) ) {

                $image_url = esc_url( $_REQUEST[ 'imgid' ] );
                $image_markup = '<img src="' . $image_url . '">';

            } else {

                $image_id     = (int) sanitize_text_field( $_REQUEST[ 'imgid' ] );
                $image_markup = wp_get_attachment_image( $image_id , 'full' );
            }

            $response = array( 'status' => 'success' , 'image_markup' => $image_markup );
        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
    }





    /*
    |--------------------------------------------------------------------------
    | Quick Add Affiliate Link methods
    |--------------------------------------------------------------------------
    */

    /**
     * Display the quick add affiliate link content on the thickbox popup.
     *
     * @since 3.0.0
     * @since 3.4.0 Add global option for redirect type.
     * @access public
     */
    public function ajax_display_quick_add_affiliate_link_thickbox() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! current_user_can( apply_filters( 'ta_enable_quick_add_affiliate_link' , 'publish_posts' ) ) )
            wp_die( "You're not allowed to do this." );

        $post_id               = isset( $_REQUEST[ 'post_id' ] ) ? intval( $_REQUEST[ 'post_id' ] ) : 0;
        $redirect_types        = $this->_constants->REDIRECT_TYPES();
        $selection             = sanitize_text_field( $_REQUEST[ 'selection' ] );
        $default_redirect_type = get_option( 'ta_link_redirect_type' , '301' );
        $global_no_follow      = get_option( 'ta_no_follow' ) == 'yes' ? 'yes' : 'no';
        $global_new_window     = get_option( 'ta_new_window' ) == 'yes' ? 'yes' : 'no';
        $html_editor           = isset( $_REQUEST[ 'html_editor' ] ) ? sanitize_text_field( $_REQUEST[ 'html_editor' ] ) : false;
        $categories            = get_terms( Plugin_Constants::AFFILIATE_LINKS_TAX , array(
            'hide_empty' => false,
        ) );

        wp_enqueue_style( 'ta_quick_add_affiliate_link_css' , $this->_constants->JS_ROOT_URL() . 'app/quick_add_affiliate_link/dist/quick-add-affiliate-link.css' , array( 'dashicons' ) , $this->_constants->VERSION() , 'all' );
        wp_enqueue_style( 'selectize' , $this->_constants->JS_ROOT_URL() . 'lib/selectize/selectize.default.css' , array() , Plugin_Constants::VERSION , 'all' );

        wp_enqueue_script('editor');
        wp_dequeue_script('jquery-ui-core');
		wp_dequeue_script('jquery-ui-sortable');
		wp_dequeue_script('admin-scripts');
        wp_enqueue_script( 'ta_quick_add_affiliate_link_js' , $this->_constants->JS_ROOT_URL() . 'app/quick_add_affiliate_link/dist/quick-add-affiliate-link.js' , array() , $this->_constants->VERSION() );
        wp_enqueue_script( 'selectize', $this->_constants->JS_ROOT_URL() . 'lib/selectize/selectize.min.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-sortable' ) , Plugin_Constants::VERSION );

        include( $this->_constants->VIEWS_ROOT_PATH() . 'linkpicker/quick-add-affiliate-link.php' );

        wp_die();

    }

    /**
     * Process quick add affiliate link. Create Affiliate link post.
     *
     * @since 3.0.0
     * @access public
     */
    public function process_quick_add_affiliate_link() {

        $thirstylink = new Affiliate_Link();

        // set Properties
        $thirstylink->set_prop( 'name' , sanitize_text_field( $_POST[ 'ta_link_name' ] ) );
        $thirstylink->set_prop( 'destination_url' , esc_url_raw( $_POST[ 'ta_destination_url' ] ) );
        $thirstylink->set_prop( 'no_follow' , sanitize_text_field( $_POST[ 'ta_no_follow' ] ) );
        $thirstylink->set_prop( 'new_window' , sanitize_text_field( $_POST[ 'ta_new_window' ] ) );
        $thirstylink->set_prop( 'redirect_type' , sanitize_text_field( $_POST[ 'ta_redirect_type' ] ) );

        do_action( 'ta_save_quick_add_affiliate_link' , $thirstylink );

        // save affiliate link
        $thirstylink->save();

        // save categories
        $this->quick_add_save_categories( $thirstylink );
        $this->_helper_functions->save_default_affiliate_link_category( $thirstylink->get_id() );

        return $thirstylink;
    }

    /**
     * Quick add: Save categories for affiliate link on.
     *
     * @since 3.2.0
     * @access private
     *
     * @param Affiliate_Link $thirstylink Affiliate link object.
     */
    private function quick_add_save_categories( $thirstylink ) {

        $categories = array();
        if ( isset( $_POST[ 'ta_link_categories' ] ) )
            $categories = array_map( 'intval' , $_POST[ 'ta_link_categories' ] );

        if ( ! empty( $categories ) )
            wp_set_post_terms( $thirstylink->get_id() , $categories , Plugin_Constants::AFFILIATE_LINKS_TAX );
    }

    /**
     * AJAX function to process quick add affiliate link.
     *
     * @since 3.0.0
     * @access public
     */
    public function ajax_process_quick_add_affiliate_link() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( apply_filters( 'ta_enable_quick_add_affiliate_link' , 'publish_posts' ) ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta_process_quick_add_affiliate_link', false, false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_REQUEST[ 'ta_link_name' ] ) || ! isset( $_REQUEST[ 'ta_destination_url' ] ) || ! isset( $_REQUEST[ 'ta_redirect_type' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Missing required post data' , 'thirstyaffiliates' ) );
        else {

            $thirstylink = $this->process_quick_add_affiliate_link();
            $post_id     = isset( $_POST[ 'post_id' ] ) ? intval( sanitize_text_field( $_POST[ 'post_id' ] ) ) : 0;
            $rel         = $thirstylink->is( 'no_follow' ) ? 'nofollow' : '';
            $target      = $thirstylink->is( 'new_window' ) ? '_blank' : '';
            $class       = ( get_option( 'ta_disable_thirsty_link_class' ) !== "yes" ) ? 'thirstylink' : '';
            $title       = ( get_option( 'ta_disable_title_attribute' ) !== "yes" ) ? $thirstylink->get_prop( 'name' ) : '';

            if ( $thirstylink->get_prop( 'rel_tags' ) )
                $rel = trim( $rel . ' ' . $thirstylink->get_prop( 'rel_tags' ) );

            if ( $thirstylink->get_prop( 'css_classes' ) )
                $class = trim( $class . ' ' . $thirstylink->get_prop( 'css_classes' ) );

            $response = array(
                'status'              => 'success',
                'link_id'             => $thirstylink->get_id(),
                'content'             => $thirstylink->get_prop( 'name' ),
                'href'                => $thirstylink->get_prop( 'permalink' ),
                'class'               => $class,
                'title'               => str_replace( '"' , '' , $title ),
                'rel'                 => $rel,
                'target'              => $target,
                'link_insertion_type' => $this->_helper_functions->get_option( 'ta_link_insertion_type' , 'link' ),
                'other_atts'          => apply_filters( 'ta_link_insert_extend_data_attributes' , array() , $thirstylink , $post_id )
            );

        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
    }



    /*
    |--------------------------------------------------------------------------
    | Shortcode editor modal interface
    |--------------------------------------------------------------------------
    */

    /**
     * AJAX display shortcode editor form.
     *
     * @since 3.4.0
     * @access public
     */
    public function ajax_display_shortcode_editor_form() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! current_user_can( apply_filters( 'ta_edit_affiliate_link_shortcode' , 'publish_posts' ) ) )
            wp_die( "You're not allowed to do this." );

        $post_id = isset( $_REQUEST[ 'post_id' ] ) ? intval( $_REQUEST[ 'post_id' ] ) : 0;

        wp_enqueue_style( 'edit-shortcode' , $this->_constants->JS_ROOT_URL() . 'app/edit_shortcode/dist/edit-shortcode.css' , array( 'dashicons' ) , $this->_constants->VERSION() , 'all' );
        wp_enqueue_style( 'selectize' , $this->_constants->JS_ROOT_URL() . 'lib/selectize/selectize.default.css' , array() , Plugin_Constants::VERSION , 'all' );

        wp_enqueue_script('editor');
        wp_dequeue_script('jquery-ui-core');
		wp_dequeue_script('jquery-ui-sortable');
		wp_dequeue_script('admin-scripts');
        wp_enqueue_script( 'edit-shortcode' , $this->_constants->JS_ROOT_URL() . 'app/edit_shortcode/dist/edit-shortcode.js' , array() , $this->_constants->VERSION() );
        wp_enqueue_script( 'selectize', $this->_constants->JS_ROOT_URL() . 'lib/selectize/selectize.min.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-sortable' ) , Plugin_Constants::VERSION );

        include( $this->_constants->VIEWS_ROOT_PATH() . 'linkpicker/edit-shortcode.php' );

        wp_die();
    }

    /**
     * Transform Gutenberg affiliate link <ta> tags to <a> tags.
     *
     * @since 3.6
     * @access public
     *
     * @global \WP_Post $post WP_Post object of currently loaded post.
     *
     * @param string $content WP_Post content.
     * @return string Filtered WP_Post content.
     */
    public function gutenberg_transform_ta_tags_to_link( $content ) {

        preg_match_all( '/<ta [^>]*>(.*?)<\/ta>/i' , $content , $matches , PREG_OFFSET_CAPTURE );

        if ( isset( $matches[0] ) && ! empty( $matches[0] ) ) {

            $diff = 0;

            foreach ( $matches[0] as $match ) {

                $link_id = $this->_helper_functions->get_string_between( $match[0] , 'linkid="' , '"' );
                $href    = $this->_helper_functions->get_string_between( $match[0] , 'href="' , '"' );
                $text    = $this->_helper_functions->get_string_between( $match[0] , '>' , '</ta' );

                if ( $link_id )
                    $replacement = do_shortcode( '[thirstylink ids="' . $link_id . '"]' . $text . '[/thirstylink]' );
                else
                    $replacement = '<a href="' . $href . '">' . $text . '</a>';

                $match[1] = $match[1] + $diff; // Fix the offset of the match after the last replacement
                $content  = substr_replace( $content , $replacement , $match[1] , strlen( $match[0] ) );

                // Add the string length difference to the next match's offset
                // because all the string positions have moved since the first
                // replacement.
                $diff = $diff + strlen( $replacement ) - strlen( $match[0] );
            }
        }

        return $content;
    }




    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
    */

    /**
     * Method that houses codes to be executed on init hook.
     *
     * @since 3.0.0
     * @access public
     * @inherit ThirstyAffiliates\Interfaces\Initiable_Interface
     */
    public function initialize() {

        // // TinyMCE buttons
        $this->init_thirsty_editor_buttons();
        $this->init_thirsty_editor_buttons_for_page_builders();

        // Advanced Link Picker hooks
        add_action( 'wp_ajax_search_affiliate_links_query' , array( $this , 'ajax_search_affiliate_links_query' ) );
        add_action( 'wp_ajax_ta_advanced_add_affiliate_link' , array( $this , 'ajax_display_advanced_add_affiliate_link' ) );
        add_action( 'wp_ajax_ta_get_image_markup_by_id' , array( $this , 'ajax_get_image_markup_by_id' ) );

        // Quick Add Affiliate Link hooks
        add_action( 'wp_ajax_ta_quick_add_affiliate_link_thickbox' , array( $this , 'ajax_display_quick_add_affiliate_link_thickbox' ) );
        add_action( 'wp_ajax_ta_process_quick_add_affiliate_link' , array( $this , 'ajax_process_quick_add_affiliate_link' ) );

        // edit shortcode
        add_action( 'wp_ajax_ta_edit_affiliate_link_shortcode' , array( $this , 'ajax_display_shortcode_editor_form' ) );

    }

    /**
     * Execute link picker.
     *
     * @since 3.0.0
     * @access public
     */
    public function run() {

        // TinyMCE buttons
        add_action( 'wp' , array( $this , 'init_thirsty_editor_buttons_for_page_builders' ) );

        // WP editor style
        add_action( 'admin_init' , array( $this , 'register_wp_editor_style' ) );
        add_filter( 'mce_css' , array( $this , 'register_wp_editor_style_mce_css' ) );

        add_filter( 'the_content' , array( $this , 'gutenberg_transform_ta_tags_to_link' ) );
    }
}
