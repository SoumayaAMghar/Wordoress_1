<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

// Data Models
use ThirstyAffiliates\Models\Affiliate_Link;

/**
 * Model that houses the logic of registering the 'thirstylink' custom post type.
 *
 * @since 3.0.0
 */
class Affiliate_Links_CPT implements Model_Interface , Initiable_Interface {

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
     * @var Affiliate_Links_CPT
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
     * @return Affiliate_Links_CPT
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }

    /**
     * Register admin interfaces.
     *
     * @since 3.3.3
     * @access public
     *
     * @param array $interfaces List of admin interfaces.
     * @return array Filtered list of admin interfaces.
     */
    public function register_admin_interfaces( $interfaces ) {

        $interfaces[ 'edit-thirstylink' ]          = 'edit_posts';
        $interfaces[ 'thirstylink' ]               = 'edit_posts';
        $interfaces[ 'edit-thirstylink-category' ] = 'manage_categories';

        return $interfaces;
    }

    /**
     * Register admin interfaces.
     *
     * @since 3.3.3
     * @access public
     *
     * @param array $interfaces List of menu items.
     * @return array Filtered list of menu items.
     */
    public function register_admin_menu_items( $menu_items ) {

        $list_slug                    = 'edit.php?post_type=' . Plugin_Constants::AFFILIATE_LINKS_CPT;
        $new_post_slug                = 'post-new.php?post_type=' . Plugin_Constants::AFFILIATE_LINKS_CPT;
        $link_cat_slug                = 'edit-tags.php?taxonomy=' . Plugin_Constants::AFFILIATE_LINKS_TAX . '&post_type=' . Plugin_Constants::AFFILIATE_LINKS_CPT;
        $menu_items[ $list_slug ]     = 'edit_posts';
        $menu_items[ $new_post_slug ] = 'edit_posts';
        $menu_items[ $link_cat_slug ] = 'manage_categories';

        return $menu_items;
    }

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




    /*
    |--------------------------------------------------------------------------
    | Register Post Type and Taxonomy
    |--------------------------------------------------------------------------
    */

    /**
     * Register the 'thirstylink' custom post type.
     *
     * @since 3.0.0
     * @since 3.3.2 Set manage_terms capability to read so we can control visibility natively. see Bootstrap::admin_interface_visibility.
     * @access private
     */
    private function register_thirstylink_custom_post_type() {

        $link_prefix = $this->_helper_functions->get_thirstylink_link_prefix();

        $labels = array(
            'name'                => __( 'Affiliate Links' , 'thirstyaffiliates' ),
            'singular_name'       => __( 'Affiliate Link' , 'thirstyaffiliates' ),
            'menu_name'           => __( 'ThirstyAffiliates' , 'thirstyaffiliates' ),
            'parent_item_colon'   => __( 'Parent Affiliate Link' , 'thirstyaffiliates' ),
            'all_items'           => __( 'Affiliate Links' , 'thirstyaffiliates' ),
            'view_item'           => __( 'View Affiliate Link' , 'thirstyaffiliates' ),
            'add_new_item'        => __( 'Add Affiliate Link' , 'thirstyaffiliates' ),
            'add_new'             => __( 'New Affiliate Link' , 'thirstyaffiliates' ),
            'edit_item'           => __( 'Edit Affiliate Link' , 'thirstyaffiliates' ),
            'update_item'         => __( 'Update Affiliate Link' , 'thirstyaffiliates' ),
            'search_items'        => __( 'Search Affiliate Links' , 'thirstyaffiliates' ),
            'not_found'           => __( 'No Affiliate Link found' , 'thirstyaffiliates' ),
            'not_found_in_trash'  => __( 'No Affiliate Links found in Trash' , 'thirstyaffiliates' )
        );

        $args = array(
            'label'               => __( 'Affiliate Links' , 'thirstyaffiliates' ),
            'description'         => __( 'ThirstyAffiliates affiliate links' , 'thirstyaffiliates' ),
            'labels'              => $labels,
            'supports'            => array( 'title' , 'custom-fields' ),
            'taxonomies'          => array(),
            'hierarchical'        => true,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_json'        => false,
            'query_var'           => true,
            'rewrite'             => array(
                'slug'       => $link_prefix,
				'with_front' => false,
				'pages'      => false
            ),
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => true,
            'menu_position'       => 26,
            'menu_icon'           => $this->get_menu_icon(),
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest'        => true,
        );

        register_post_type( Plugin_Constants::AFFILIATE_LINKS_CPT , apply_filters( 'ta_affiliate_links_cpt_args' , $args , $labels ) );

        do_action( 'ta_after_register_thirstylink_post_type' , $link_prefix );
    }

    /**
     * Get the plugin menu icon.
     *
     * @return string
     */
    private function get_menu_icon() {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16.688" height="9.875" viewBox="0 0 16.688 9.875">
<path id="TA.svg" fill="%s"  class="cls-1" d="M2.115,15.12H4.847L6.836,7.7H9.777l0.63-2.381H1.821L1.177,7.7H4.118Zm4.758,0H9.829l1.177-1.751h3.782l0.238,1.751h2.858L16.357,5.245H13.7Zm5.5-3.866,1.835-2.816,0.35,2.816H12.378Z" transform="translate(-1.188 -5.25)"/>
</svg>';

        $fill = isset( $_GET['post_type'] ) && is_string( $_GET['post_type'] ) && $_GET['post_type'] == 'thirstylink' ? '#ffffff' : '#a0a5aa';

        return 'data:image/svg+xml;base64,' . base64_encode( sprintf( $icon, $fill ) );
    }

    /**
     * Register the 'thirstylink-category' custom taxonomy.
     *
     * @since 3.0.0
     * @since 3.3.2 Set manage_terms capability to read so we can control visibility natively. see Bootstrap::admin_interface_visibility.
     * @access private
     */
    private function register_thirstylink_category_custom_taxonomy() {

        $labels = array(
    		'name'                       => __( 'Link Categories', 'thirstyaffiliates' ),
    		'singular_name'              => __( 'Link Category', 'thirstyaffiliates' ),
    		'menu_name'                  => __( 'Link Categories', 'thirstyaffiliates' ),
    		'all_items'                  => __( 'All Categories', 'thirstyaffiliates' ),
    		'parent_item'                => __( 'Parent Category', 'thirstyaffiliates' ),
    		'parent_item_colon'          => __( 'Parent Category:', 'thirstyaffiliates' ),
    		'new_item_name'              => __( 'New Category Name', 'thirstyaffiliates' ),
    		'add_new_item'               => __( 'Add New Category', 'thirstyaffiliates' ),
    		'edit_item'                  => __( 'Edit Category', 'thirstyaffiliates' ),
    		'update_item'                => __( 'Update Category', 'thirstyaffiliates' ),
    		'view_item'                  => __( 'View Category', 'thirstyaffiliates' ),
    		'separate_items_with_commas' => __( 'Separate items with commas', 'thirstyaffiliates' ),
    		'add_or_remove_items'        => __( 'Add or remove items', 'thirstyaffiliates' ),
    		'choose_from_most_used'      => __( 'Choose from the most used', 'thirstyaffiliates' ),
    		'popular_items'              => __( 'Popular Categories', 'thirstyaffiliates' ),
    		'search_items'               => __( 'Search Categories', 'thirstyaffiliates' ),
    		'not_found'                  => __( 'Not Found', 'thirstyaffiliates' ),
    		'no_terms'                   => __( 'No items', 'thirstyaffiliates' ),
    		'items_list'                 => __( 'Category list', 'thirstyaffiliates' ),
    		'items_list_navigation'      => __( 'Category list navigation', 'thirstyaffiliates' )
    	);

    	$args = array(
    		'labels'                     => $labels,
    		'hierarchical'               => true,
    		'public'                     => false,
    		'show_ui'                    => true,
    		'show_admin_column'          => true,
    		'show_in_nav_menus'          => false,
    		'show_tagcloud'              => false,
            'rewrite'                    => false,
            'capabilities'               => array(
                'manage_terms' => 'read',
            )
    	);

    	register_taxonomy( Plugin_Constants::AFFILIATE_LINKS_TAX , Plugin_Constants::AFFILIATE_LINKS_CPT , apply_filters( 'ta_affiliate_link_taxonomy_args' , $args , $labels ) );

    }




    /*
    |--------------------------------------------------------------------------
    | UI and metabox related functions
    |--------------------------------------------------------------------------
    */

    /**
     * Replace default post type permalink html with affiliate link ID.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string $html    Permalink html.
     * @param int    $post_id Affiliate Link post id.
     * @return string Link ID html.
     */
    public function replace_permalink_with_id( $html , $post_id ) {

        if ( get_post_type( $post_id ) == Plugin_Constants::AFFILIATE_LINKS_CPT )
            return '<span id="link_id">' . __( 'Link ID:' , 'thirstyaffiliates' ) . ' <strong>' . $post_id . '</strong></span>';

        return $html;
    }

    /**
     * Add custom HTML ta tag
     *
     * @since 3.10.4
     * @access public
     *
     * @param array $tags     List of allowed HTML tags
     * @param string $context Context name
     * @return array          Array of allowed HTML tags
     */
    public function add_affiliate_link_tag( $tags, $context ) {
        $tags['ta'] = $tags['a'];

        return $tags;
    }

    /**
     * Register metaboxes
     *
     * @since 3.0.0
     * @access public
     */
    public function register_metaboxes() {

        $normal_metaboxes = apply_filters( 'ta_register_normal_metaboxes' , array(
            array(
                'id'       => 'ta-urls-metabox',
                'title'    => __( 'URLs', 'thirstyaffiliates' ),
                'cb'       => array( $this , 'urls_metabox' ),
                'sort'     => 10,
                'priority' => 'high'
            ),
            array(
                'id'       => 'ta-attach-images-metabox',
                'title'    => __( 'Attach Images', 'thirstyaffiliates' ),
                'cb'       => array( $this , 'attach_images_metabox' ),
                'sort'     => 20,
                'priority' => 'high'
            ),
            array(
                'id'       => 'ta-inserted-link-scanner-metabox',
                'title'    => __( 'Link Inserted Scanner', 'thirstyaffiliates' ),
                'cb'       => array( $this , 'inserted_link_scanner_metabox' ),
                'sort'     => 99,
                'priority' => 'low'
            ),
        ) );

        $side_metaboxes = apply_filters( 'ta_register_side_metaboxes' , array(
            array(
                'id'       => 'ta-save-affiliate-link-metabox-side',
                'title'    => __( 'Save Affiliate Link', 'thirstyaffiliates' ),
                'cb'       => array( $this , 'save_affiliate_link_metabox' ),
                'sort'     => 10,
                'priority' => 'high'
            ),
            array(
                'id'       => 'ta-link-options-metabox',
                'title'    => __( 'Link Options', 'thirstyaffiliates' ),
                'cb'       => array( $this , 'link_options_metabox' ),
                'sort'     => 30,
                'priority' => 'default'
            ),
        ) );

        // sort metaboxes by priority.
        $this->sort_metaboxes( $normal_metaboxes );
        $this->sort_metaboxes( $side_metaboxes );

        // register normal metaboxes.
        foreach ( $normal_metaboxes as $metabox )
            add_meta_box( $metabox[ 'id' ] , $metabox[ 'title' ] , $metabox[ 'cb' ] , Plugin_Constants::AFFILIATE_LINKS_CPT , 'normal' , $metabox[ 'priority' ] );

        // register side metaboxes.
        foreach ( $side_metaboxes as $metabox )
            add_meta_box( $metabox[ 'id' ] , $metabox[ 'title' ] , $metabox[ 'cb' ] , Plugin_Constants::AFFILIATE_LINKS_CPT , 'side' , $metabox[ 'priority' ] );

        // remove unnecessary metaboxes
        remove_meta_box( 'submitdiv', Plugin_Constants::AFFILIATE_LINKS_CPT , 'side' );
        remove_meta_box( 'postcustom' , Plugin_Constants::AFFILIATE_LINKS_CPT , 'normal' );
    }

    /**
     * Function to sort registered metaboxes by priority.
     *
     * @since 3.2.1
     * @access private
     *
     * @param array $metaboxes Metaboxes list to sort.
     */
    private function sort_metaboxes( &$metaboxes ) {

        usort( $metaboxes , function( $a , $b ) {
            if ( $a[ 'sort' ] == $b[ 'sort' ] ) return 0;
            return ( $a[ 'sort' ] > $b[ 'sort' ] ) ? 1 : -1;
        } );
    }

    /**
     * Display "URls" metabox
     *
     * @since 3.0.0
     * @access public
     *
     * @param WP_Post $post Affiliate link WP_Post object.
     */
    public function urls_metabox( $post ) {

        $screen           = get_current_screen();
        $thirstylink      = $this->get_thirstylink_post( $post->ID );
        $home_link_prefix = home_url( user_trailingslashit( $this->_helper_functions->get_thirstylink_link_prefix() ) );
        $default_cat_slug = $this->_helper_functions->get_default_category_slug( $post->ID );

        include_once( $this->_constants->VIEWS_ROOT_PATH() . 'cpt/view-urls-metabox.php' );

    }

    /**
     * Display "Attach Images" metabox
     *
     * @since 3.0.0
     * @since 3.4.0 Add support for external images.
     * @access public
     *
     * @param WP_Post $post Affiliate link WP_Post object.
     */
    public function attach_images_metabox( $post ) {

        $thirstylink     = $this->get_thirstylink_post( $post->ID );
        $legacy_uploader = get_option( 'ta_legacy_uploader', 'no' );
        $attachments     = $thirstylink->get_prop( 'image_ids' );

        include_once( $this->_constants->VIEWS_ROOT_PATH() . 'cpt/view-attach-images-metabox.php' );

    }

    /**
     * Display link options metabox
     *
     * @since 3.0.0
     * @since 3.4.0 Add global option for redirect type. Add additional CSS classes field.
     * @access public
     *
     * @param WP_Post $post Affiliate link WP_Post object.
     */
    public function link_options_metabox( $post ) {

        $thirstylink           = $this->get_thirstylink_post( $post->ID );
        $default_redirect_type = $this->_helper_functions->get_option( 'ta_link_redirect_type' , '302' );
        $post_redirect_type    = $thirstylink->get_prop( 'redirect_type' );
        $redirect_types        = $this->_constants->REDIRECT_TYPES();
        $global_no_follow      = $thirstylink->get_toggle_prop_global_value( 'no_follow' );
        $global_new_window     = $thirstylink->get_toggle_prop_global_value( 'new_window' );
        $global_pass_query_str = $thirstylink->get_toggle_prop_global_value( 'pass_query_str' );
        $global_uncloak        = $thirstylink->get_toggle_prop_global_value( 'uncloak_link' );
        $rel_tags              = get_post_meta( $post->ID , Plugin_Constants::META_DATA_PREFIX . 'rel_tags' , true );
        $css_classes           = get_post_meta( $post->ID , Plugin_Constants::META_DATA_PREFIX . 'css_classes' , true );
        $global_rel_tags       = get_option( 'ta_additional_rel_tags' );
        $global_css_classes    = get_option( 'ta_additional_css_classes' );

        include_once( $this->_constants->VIEWS_ROOT_PATH() . 'cpt/view-link-options-metabox.php' );

    }

    /**
     * Display "Save Affiliate Link" metabox
     *
     * @since 3.0.0
     * @access public
     *
     * @param WP_Post $post Affiliate link WP_Post object.
     */
    public function save_affiliate_link_metabox( $post ) {

        include( $this->_constants->VIEWS_ROOT_PATH() . 'cpt/view-save-affiliate-link-metabox.php' );

    }

    /**
     * Display inserted link scanner metabox
     *
     * @since 3.2.0
     * @access public
     *
     * @param WP_Post $post Affiliate link WP_Post object.
     */
    public function inserted_link_scanner_metabox( $post ) {

        $thirstylink       = $this->get_thirstylink_post( $post->ID );
        $spinner_image_src = $this->_constants->IMAGES_ROOT_URL() . 'spinner-2x.gif';
        $inserted_to       = $thirstylink->get_prop( 'inserted_to' );
        $timezone          = new \DateTimeZone( $this->_helper_functions->get_site_current_timezone() );
        $last_scanned      = \DateTime::createFromFormat( 'Y-m-d G:i:s' , $thirstylink->get_prop( 'scanned_inserted' ) );
        $not_yet_scanned   = __( 'Not yet scanned' , 'thirstyaffiliates' );

        if ( $last_scanned )
            $last_scanned->setTimezone( $timezone );

        $last_scanned_txt        = $last_scanned !== false ? __( 'Last scanned on:' , 'thirstyaffiliates' ) . ' <em>' . $last_scanned->format( 'F j, Y g:ia' ) . '</em>' : $not_yet_scanned;
        $inserted_into_rows_html = $this->get_inserted_into_rows_html( $inserted_to );

        include( $this->_constants->VIEWS_ROOT_PATH() . 'cpt/view-inserted-link-scanner-metabox.php' );
    }

    /**
     * Get the HTML for inserted into table rows.
     *
     * @since 3.2.0
     * @access private
     *
     * @param array          $inserted_to WP_Post IDs where affiliate link is inserte to.
     * @param Affiliate_Link $thirstylink Affiliate link object.
     */
    private function get_inserted_into_rows_html( $inserted_to ) {

        global $wpdb;

        if ( ! is_array( $inserted_to ) || empty( $inserted_to ) )
            return '<tr><td colspan="4">' . __( 'No results found.' , 'thirstyaffiliates' ) . '</td></tr>';

        $inserted_to_str = implode( ',' , $inserted_to );
        $results         = $wpdb->get_results( "SELECT ID , post_title , post_type FROM $wpdb->posts WHERE ID IN ( $inserted_to_str )" );

        ob_start();
        foreach ( $results as $object ) : ?>
            <tr>
                <td class="id"><?php echo esc_html( $object->ID ); ?></td>
                <td class="title"><?php echo mb_strimwidth( esc_html( $object->post_title ) , 0 , 60 , "..." ); ?></td>
                <td class="post-type"><?php echo esc_html( $object->post_type ); ?></td>
                <td class="actions">
                    <a class="view" href="<?php echo get_permalink( $object->ID ); ?>" target="_blank"><span class="dashicons dashicons-admin-links"></span></a>
                    <a class="edit" href="<?php echo get_edit_post_link( $object->ID ); ?>" target="_blank"><span class="dashicons dashicons-edit"></span></a>
                </td>
            </tr>
        <?php endforeach;
        return ob_get_clean();
    }




    /*
    |--------------------------------------------------------------------------
    | Save Functions
    |--------------------------------------------------------------------------
    */

    /**
     * Save thirstylink post.
     *
     * @since 3.0.0
     * @since 3.2.2 Make sure post name (slug) is updated.
     * @since 3.4.0 Add css_classes to the saved properties.
     * @access public
     *
     * @param int $post_id Affiliate link post ID.
     */
    public function save_post( $post_id ) {

        if ( ! isset( $_POST[ '_thirstyaffiliates_nonce' ] ) || ! wp_verify_nonce( $_POST['_thirstyaffiliates_nonce'], 'thirsty_affiliates_cpt_nonce' ) )
            return;

        // remove save_post hooked action to prevent infinite loop
        remove_action( 'save_post' , array( $this , 'save_post' ) );

        $thirstylink = $this->get_thirstylink_post( $post_id );

        // set Properties
        $thirstylink->set_prop( 'destination_url' , esc_url_raw( $_POST[ 'ta_destination_url' ] ) );
        $thirstylink->set_prop( 'no_follow' , sanitize_text_field( $_POST[ 'ta_no_follow' ] ) );
        $thirstylink->set_prop( 'new_window' , sanitize_text_field( $_POST[ 'ta_new_window' ] ) );
        $thirstylink->set_prop( 'pass_query_str' , sanitize_text_field( $_POST[ 'ta_pass_query_str' ] ) );
        $thirstylink->set_prop( 'redirect_type' , sanitize_text_field( $_POST[ 'ta_redirect_type' ] ) );
        $thirstylink->set_prop( 'rel_tags' , sanitize_text_field( $_POST[ 'ta_rel_tags' ] ) );
        $thirstylink->set_prop( 'css_classes' , sanitize_text_field( $_POST[ 'ta_css_classes' ] ) );

        if ( isset( $_POST[ 'post_name' ] ) )
            $thirstylink->set_prop( 'slug' , sanitize_text_field( $_POST[ 'post_name' ] ) );

        if ( isset( $_POST[ 'ta_uncloak_link' ] ) )
            $thirstylink->set_prop( 'uncloak_link' , sanitize_text_field( $_POST[ 'ta_uncloak_link' ] ) );

        if ( isset( $_POST[ 'ta_category_slug' ] ) && $_POST[ 'ta_category_slug' ] ) {

            $category_slug_id = (int) sanitize_text_field( $_POST[ 'ta_category_slug' ] );
            $category_slug    = get_term( $category_slug_id , Plugin_Constants::AFFILIATE_LINKS_TAX );
            $thirstylink->set_prop( 'category_slug_id' , $category_slug_id );
            $thirstylink->set_prop( 'category_slug' , $category_slug->slug );

        } else {

            $thirstylink->set_prop( 'category_slug_id' , 0 );
            $thirstylink->set_prop( 'category_slug' , '' );
        }

        do_action( 'ta_save_affiliate_link_post' , $thirstylink , $post_id );

        // save affiliate link
        $thirstylink->save();

        // set default term
        $this->_helper_functions->save_default_affiliate_link_category( $post_id );

        // add back save_post hooked action after saving
        add_action( 'save_post' , array( $this , 'save_post' ) );

        do_action( 'ta_after_save_affiliate_link_post' , $post_id , $thirstylink );
    }

    /**
     * Set default term when affiliate link is saved.
     *
     * @deprecated 3.2.0 Moved to helper functions
     *
     * @since 3.0.0
     * @access public
     *
     * @param int $post_id Affiliate link post ID.
     */
    public function save_default_affiliate_link_category( $post_id ) {

        $this->_helper_functions->save_default_affiliate_link_category( $post_id );
    }




    /*
    |--------------------------------------------------------------------------
    | Affiliate Link listing related UI functions
    |--------------------------------------------------------------------------
    */

    /**
     * Add custom column to thirsty link listings (Link ID).
     *
     * @since 3.0.0
     * @access public
     *
     * @param array $columns Post type listing columns.
     * @return array Filtered post type listing columns.
     */
    public function custom_post_listing_column( $columns ) {

        $updated_columns = array();

        foreach ( $columns as $key => $column ) {

            // add link_id and link_destination column before link categories column
            if ( $key == 'taxonomy-thirstylink-category' ) {

                $updated_columns[ 'link_id' ]          = __( 'Link ID' , 'thirstyaffiliates' );
                $updated_columns[ 'redirect_type' ]    = __( 'Redirect Type' , 'thirstyaffiliates' );
                $updated_columns[ 'cloaked_url' ]      = __( 'Cloaked URL' , 'thirstyaffiliates' );
                $updated_columns[ 'link_destination' ] = __( 'Link Destination' , 'thirstyaffiliates' );
            }


            $updated_columns[ $key ] = $column;
        }

        return apply_filters( 'ta_post_listing_custom_columns' , $updated_columns );

    }

    /**
     * Add custom column to thirsty link listings (Link ID).
     *
     * @since 3.2.0
     * @access public
     *
     * @param array $columns Post type listing sortable columns.
     * @return array Filtered Post type listing sortable columns.
     */
    public function custom_post_listing_sortable_column( $columns ) {

        $columns[ 'link_id' ]          = 'ID';
        $columns[ 'redirect_type' ]    = '_ta_redirect_type';
        $columns[ 'cloaked_url' ]      = 'name';
        $columns[ 'link_destination' ] = '_ta_destination_url';

        return apply_filters( 'ta_post_listing_sortable_custom_columns' , $columns );
    }

    /**
     * Add custom sorting support for TA custom columns with post meta values.
     *
     * @since 3.2.0
     * @access public
     *
     * @param WP_Query $query Main WP_Query instance of the page.
     */
    public function custom_post_listing_column_custom_sorting( $query ) {

        $post_type = get_post_type();
        if ( !$post_type && isset( $_GET[ 'post_type' ] ) )
            $post_type = $_GET[ 'post_type' ];

        if ( ! is_admin() || $post_type !== Plugin_Constants::AFFILIATE_LINKS_CPT )
            return;

        $meta_key     = $query->get( 'orderby' );
        $default_keys = array( '_ta_redirect_type', '_ta_destination_url' );
        $extend_keys  = apply_filters( 'ta_post_listing_custom_sorting_keys' , array() );
        $meta_keys    = array_unique( array_merge( $default_keys , $extend_keys ) );


        if ( ! in_array( $meta_key , $meta_keys ) )
            return;

        $meta_data = apply_filters( 'ta_post_listing_custom_sorting_metadata' , array(
            'meta_key' => $meta_key,
            'orderby'  => 'meta_value',
        ) , $meta_key , $meta_keys );

        if ( ! $meta_data[ 'meta_key' ] || ! $meta_data[ 'orderby' ] )
            return;

        $query->set( 'meta_key' , $meta_key );
    }

    /**
     * Add custom column to thirsty link listings (Link ID).
     *
     * @since 3.0.0
     * @since 3.4.0 Update display for redirect type value.
     * @access public
     *
     * @param string $column  Current column name.
     * @param int    $post_id Thirstylink ID.
     * @return array
     */
    public function custom_post_listing_column_value( $column , $post_id ) {

        $thirstylink   = $this->get_thirstylink_post( $post_id );
        $edit_link     = get_edit_post_link( $post_id );
        $redirect_type = $thirstylink->get_prop( 'redirect_type' );

        switch ( $column ) {

            case 'link_id' :
                echo '<span>' . $post_id . '</span>';
                break;

            case 'redirect_type' :
                echo $redirect_type;
                echo $redirect_type === 'global' ? ' (' . $this->_helper_functions->get_option( 'ta_link_redirect_type' , '302' ) . ')' : '';
                break;

            case 'cloaked_url' :
                echo '<div class="ta-display-input-wrap">';
                echo '<input style="width:100%;" type="text" value="' . $thirstylink->get_prop( 'permalink' ) . '" readonly>';
                echo '<a href="' . $edit_link . '"><span class="dashicons dashicons-edit"></span></a>';
                echo '</div>';
                break;

            case 'link_destination' :
                echo '<div class="ta-display-input-wrap">';
                echo '<input style="width:100%;" type="text" value="' . $thirstylink->get_prop( 'destination_url' ) . '" readonly>';
                echo '<a href="' . $edit_link . '"><span class="dashicons dashicons-edit"></span></a>';
                echo '</div>';
                break;

        }

        do_action( 'ta_post_listing_custom_columns_value' , $column , $thirstylink );

    }

    /**
     * Setup the filter box for the list page so users can filter links by category
     *
     * @since 3.2.0
     * @access public
     *
     * @global string   $typenow  Current post type context.
     * @global WP_Query $wp_query Object that contains the main query of WP.
     */
    public function restrict_links_by_category() {

        global $typenow , $wp_query;

        if ( $typenow != Plugin_Constants::AFFILIATE_LINKS_CPT )
            return;

        $taxonomy = Plugin_Constants::AFFILIATE_LINKS_TAX;

        wp_dropdown_categories( array(
			'show_option_all' => __( 'Show Link Categories' , 'thirstyaffiliates' ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => ( isset( $wp_query->query[ $taxonomy ] ) ? $wp_query->query[ $taxonomy ] : '' ),
			'hierarchical'    => true,
			'depth'           => 4,
			'show_count'      => true,
			'hide_empty'      => true
		) );
    }

    /**
     * Convert category ID to slug to make the filter by category dropdown work.
     *
     * @since 3.2.0
     * @access public
     *
     * @global string $typenow Current post type context.
     *
     * @param WP_Query $query Current page query.
     * @return WP_Query Filtered current page query.
     */
    public function convert_cat_id_to_slug_in_query( $query ) {

        global $typenow;

        if ( $typenow != Plugin_Constants::AFFILIATE_LINKS_CPT )
            return;

        $qv       = &$query->query_vars;
        $taxonomy = Plugin_Constants::AFFILIATE_LINKS_TAX;

        if ( isset( $qv[ $taxonomy ] ) && is_numeric( $qv[ $taxonomy ] ) ) {

    		$term            = get_term_by( 'id' , $qv[ $taxonomy ] , $taxonomy );
    		$qv[ $taxonomy ] = is_object( $term ) ? $term->slug : '';

    	}

        return $query;
    }




    /*
    |--------------------------------------------------------------------------
    | Affiliate Link Filters
    |--------------------------------------------------------------------------
    */

    /**
     * Add category slug to the permalink.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string  $post_link  Thirstylink permalink.
     * @param WP_Post $post       Thirstylink WP_Post object.
     * @return string Thirstylink permalink.
     */
    public function add_category_slug_to_permalink( $post_link , $post ) {

        $link_prefix = $this->_helper_functions->get_thirstylink_link_prefix();

        if ( get_option( 'ta_show_cat_in_slug' ) !== 'yes' || is_wp_error( $post ) || $post->post_type != 'thirstylink' )
            return $post_link;

        $link_cat_id = get_post_meta( $post->ID , '_ta_category_slug_id' , true );
        $link_cat    = get_post_meta( $post->ID , '_ta_category_slug' , true );

        if ( ! $link_cat && $link_cat_id ) {

            $link_cat_obj = get_term( $link_cat_id , Plugin_Constants::AFFILIATE_LINKS_TAX );
            $link_cat     = $link_cat_obj->slug;

        } elseif ( ! $link_cat && ! $link_cat_id ) {

            $link_cat = $this->_helper_functions->get_default_category_slug( $post->ID );
        }

        if ( ! $link_cat )
            return $post_link;

        return home_url( user_trailingslashit( $link_prefix . '/' . $link_cat . '/' . $post->post_name ) );
    }




    /*
    |--------------------------------------------------------------------------
    | AJAX functions.
    |--------------------------------------------------------------------------
    */

    /**
     * Ajax get category slug.
     *
     * @since 3.0.0
     * @access public
     */
    public function ajax_get_category_slug() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( $this->_helper_functions->get_capability_for_interface('thirstylink_edit', 'publish_posts') ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta_get_category_slug', false, false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_POST[ 'term_id' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Missing required post data' , 'thirstyaffiliates' ) );
        else {

            $link_cat_id = (int) sanitize_text_field( $_POST[ 'term_id' ] );
            $category    = get_term( $link_cat_id , Plugin_Constants::AFFILIATE_LINKS_TAX );

            $response = array( 'status' => 'success' , 'category_slug' => $category->slug );
        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
    }

    /**
     * Ajax link inserted scanner.
     *
     * @since 3.2.0
     * @access public
     */
    public function ajax_link_inserted_scanner() {

        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Invalid AJAX call' , 'thirstyaffiliates' ) );
        elseif ( ! current_user_can( $this->_helper_functions->get_capability_for_interface('thirstylink_edit', 'publish_posts') ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'You do not have permission to do this' , 'thirstyaffiliates' ) );
        elseif ( ! check_ajax_referer( 'ta_link_inserted_scanner', false, false ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Security Check Failed' , 'thirstyaffiliates' ) );
        elseif ( ! isset( $_POST[ 'link_id' ] ) )
            $response = array( 'status' => 'fail' , 'error_msg' => __( 'Missing required post data' , 'thirstyaffiliates' ) );
        else {

            $link_id      = (int) sanitize_text_field( $_POST[ 'link_id' ] );
            $thirstylink  = $this->get_thirstylink_post( $link_id );
            $inserted_to  = $thirstylink->scan_where_links_inserted();
            $timezone     = new \DateTimeZone( $this->_helper_functions->get_site_current_timezone() );
            $last_scanned = \DateTime::createFromFormat( 'Y-m-d G:i:s' , get_post_meta( $link_id , Plugin_Constants::META_DATA_PREFIX . 'scanned_inserted' , true ) );
            $last_scanned->setTimezone($timezone);

            $response = array(
                'status'         => 'success',
                'results_markup' => $this->get_inserted_into_rows_html( $inserted_to ),
                'last_scanned'   => __( 'Last scanned on:' , 'thirstyaffiliates' ) . ' <em>' . $last_scanned->format( 'F j, Y g:ia' ) . '</em>'
            );
        }

        @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        echo wp_json_encode( $response );
        wp_die();
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

        // cpt and taxonomy
        $this->register_thirstylink_custom_post_type();
        $this->register_thirstylink_category_custom_taxonomy();

        add_action( 'wp_ajax_ta_get_category_slug' , array( $this , 'ajax_get_category_slug' ) );
        add_action( 'wp_ajax_ta_link_inserted_scanner' , array( $this , 'ajax_link_inserted_scanner' ) );
    }

    /**
     * Execute 'thirstylink' custom post type code.
     *
     * @since 3.0.0
     * @access public
     * @inherit ThirstyAffiliates\Interfaces\Model_Interface
     */
    public function run() {

        // replace permalink with link ID
        add_filter( 'get_sample_permalink_html', array( $this , 'replace_permalink_with_id' ), 10 , 2 );
        add_filter( 'wp_kses_allowed_html', array( $this, 'add_affiliate_link_tag' ), 10, 2 );

        // metaboxes
        add_action( 'add_meta_boxes' , array( $this , 'register_metaboxes' ) );
        add_action( 'save_post' , array( $this , 'save_post' ) );

        // custom column
        add_filter( 'manage_edit-thirstylink_columns' , array( $this , 'custom_post_listing_column' ) );
        add_filter( 'manage_edit-thirstylink_sortable_columns', array( $this , 'custom_post_listing_sortable_column' ) );
        add_action( 'manage_thirstylink_posts_custom_column', array( $this  , 'custom_post_listing_column_value' ) , 10 , 2 );
        add_action( 'pre_get_posts' , array( $this , 'custom_post_listing_column_custom_sorting' ) );

        // filter by category
        add_action( 'restrict_manage_posts' , array( $this , 'restrict_links_by_category' ) );
        add_filter( 'parse_query' , array( $this , 'convert_cat_id_to_slug_in_query' ) );

        // filter to add category on permalink
        add_filter( 'post_type_link' , array( $this , 'add_category_slug_to_permalink' ) , 10 , 2 );

        // Register admin interface and menus.
        add_filter( 'ta_admin_interfaces' , array( $this , 'register_admin_interfaces' ) );
        add_filter( 'ta_menu_items' , array( $this , 'register_admin_menu_items' ) );
    }

}
