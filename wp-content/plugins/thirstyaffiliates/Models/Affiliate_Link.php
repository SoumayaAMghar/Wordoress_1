<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;

/**
 * Model that houses the data model of an affiliate link.
 *
 * @since 3.0.0
 */
class Affiliate_Link {


    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

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
     * Stores affiliate link ID.
     *
     * @since 3.0.0
     * @access protected
     * @var array
     */
    protected $id;

    /**
     * Stores affiliate link data.
     *
     * @since 3.0.0
     * @access protected
     * @var array
     */
    protected $data = array();

    /**
     * Stores affiliate link default data.
     *
     * @since 3.0.0
     * @access protected
     * @var array
     */
    protected $default_data = array(
        'name'             => '',
        'slug'             => '',
        'date_created'     => '',
        'date_modified'    => '',
        'status'           => '',
        'permalink'        => '',
        'destination_url'  => '',
        'rel_tags'         => '',
        'css_classes'      => '',
        'redirect_type'    => 'global',
        'no_follow'        => 'global',
        'new_window'       => 'global',
        'uncloak_link'     => 'global',
        'pass_query_str'   => 'global',
        'image_ids'        => array(),
        'categories'       => array(),
        'category_slug'    => '',
        'category_slug_id' => 0,
        'inserted_to'      => array(),
        'scanned_inserted' => ''
    );

    /**
     * Stores affiliate link default data.
     *
     * @since 3.0.0
     * @access protected
     * @var array
     */
    protected $extend_data = array();

    /**
     * Stores affiliate link post data.
     *
     * @since 3.0.0
     * @access private
     * @var object
     */
    protected $post_data;

    /**
     * This is where changes to the $data will be saved.
     *
     * @since 3.0.0
     * @access protected
     * @var object
     */
    protected $changes = array();

    /**
     * Stores boolean if the data has been read from the database or not.
     *
     * @since 3.0.0
     * @access protected
     * @var object
     */
    protected $object_is_read = false;

    /**
     * List of deprecated properties.
     *
     * @since 3.3.1
     * @access protected
     * @var array
     */
    protected $deprecated_props = array(
        'link_health_issue',
        'link_health_issue_ignored'
    );




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
    public function __construct( $id = null ) {

        $this->_constants        = ThirstyAffiliates()->helpers[ 'Plugin_Constants' ];
        $this->_helper_functions = ThirstyAffiliates()->helpers[ 'Helper_Functions' ];
        $this->deprecated_props  = apply_filters( 'ta_affiliate_link_deprecated_props' , $this->deprecated_props );

        if ( filter_var( $id , FILTER_VALIDATE_INT ) && $id ) {

            $this->extend_data = apply_filters( 'ta_affiliate_link_extended_data' , $this->extend_data , $this->default_data );
            $this->data        = $this->get_merged_default_extended_data();
            $this->id          = absint( $id );

            $this->read();

        }

    }

    /**
     * Read data from DB and save on instance.
     *
     * @since 3.0.0
     * @since 3.2.2 Optimize method by lessening the metadata fetch into one single function call.
     * @access public
     */
    private function read() {

        $this->post_data = get_post( $this->id );

        if ( ! is_a( $this->post_data , 'WP_Post' ) || $this->object_is_read )
            return;

        $this->id     = $this->post_data->ID; // set the affiliate link ID
        $meta_data    = get_metadata( 'post' , $this->id );
        $default_data = $this->get_merged_default_extended_data();

        foreach ( $default_data as $prop => $value ) {

            // fetch raw meta data if present.
            $raw_data = isset( $meta_data[ Plugin_Constants::META_DATA_PREFIX . $prop ] ) ? maybe_unserialize( $meta_data[ Plugin_Constants::META_DATA_PREFIX . $prop ][0] ) : '';

            switch ( $prop ) {

                case 'name' :
                case 'slug' :
                case 'status' :
                case 'date_created' :
                case 'permalink' :
                    $this->data[ $prop ] = $this->get_post_data_equivalent( $prop );
                    break;

                case 'no_follow' :
                case 'new_window' :
                case 'pass_query_str' :
                case 'uncloak_link' :
                case 'redirect_type' :
                    $this->data[ $prop ] = ! empty( $raw_data ) ? $raw_data : $default_data[ $prop ];
                    break;

                case 'rel_tags' :
                case 'css_classes' :
                    $this->data[ $prop ] = ! empty( $raw_data ) ? $raw_data : $this->get_prop_global_option_value( $prop );
                    break;

                case 'image_ids' :
                case 'inserted_to' :
                    $this->data[ $prop ] = ( is_array( $raw_data ) && ! empty( $raw_data ) ) ? $raw_data : $default_data[ $prop ];
                    break;

                case 'categories' :
                    $categories          = wp_get_post_terms( $this->id , Plugin_Constants::AFFILIATE_LINKS_TAX );
                    $this->data[ $prop ] = ! empty( $categories ) ? $categories : $default_data[ $prop ];
                    break;

                default :
                    $this->data[ $prop ] = apply_filters( 'ta_read_thirstylink_property' , $raw_data , $prop , $default_data , $this->id , $meta_data );
                    break;

            }

        }

        $this->object_is_read = true;

    }




    /*
    |--------------------------------------------------------------------------
    | Data getters
    |--------------------------------------------------------------------------
    */

    /**
     * Get merged $default_data and $extended_data class properties.
     *
     * @since 3.0.0
     * @access public
     *
     * @return array Data properties.
     */
    private function get_merged_default_extended_data() {

        return array_merge( $this->default_data , $this->extend_data );

    }

    /**
     * Return's the post data equivalent of a certain affiliate link data property.
     *
     * @since 3.0.0
     * @access private
     *
     * @param string $prop Affiliate link property name.
     * @return string WP Post property equivalent.
     */
    private function get_post_data_equivalent( $prop ) {

        $equivalents = apply_filters( 'ta_affiliate_link_post_data_equivalent' , array(
            'name'          => $this->post_data->post_title,
            'slug'          => $this->post_data->post_name,
            'permalink'     => get_permalink( $this->post_data->ID ),
            'status'        => $this->post_data->post_status,
            'date_created'  => $this->post_data->post_date,
            'date_modified' => $this->post_data->post_modified,
        ) , $this->post_data );

        if ( array_key_exists( $prop , $equivalents ) )
            return $equivalents[ $prop ];
        else
            return;

    }

    /**
     * Return data property.
     *
     * @since 3.0.0
     * @since 3.3.1 Make sure deprecated props are ignored.
     * @access public
     *
     * @param string $prop    Data property slug.
     * @param mixed  $default Set property default value (optional).
     * @return mixed Property data.
     */
    public function get_prop( $prop , $default = '' ) {

        $default_data = $this->get_merged_default_extended_data();

        if ( in_array( $prop , $this->deprecated_props ) )
            return $default;

        if ( array_key_exists( $prop , $this->data ) && $this->data[ $prop ] )
            $return_value = $this->data[ $prop ];
        else
            $return_value = ( $default ) ? $default : $default_data[ $prop ];

        return $return_value;

    }

    /**
     * Get redirect type
     *
     * @since 3.4.0
     * @access public
     *
     * @param string $default Default redirect type.
     * @return string Redirect type.
     */
    public function get_redirect_type( $default = '' ) {

        $redirect_type = $this->data[ 'redirect_type' ];

        if ( $redirect_type === 'global' )
            $redirect_type = get_option( 'ta_link_redirect_type' , $default );
        elseif ( ! $redirect_type )
            $redirect_type = $default ? $default : get_option( 'ta_link_redirect_type' );

        return $redirect_type;
    }

    /**
     * Return Affiliate_Link ID.
     *
     * @since 3.0.0
     * @access public
     *
     * @return int Affiliate_Link ID.
     */
    public function get_id() {

        return absint( $this->id );

    }

    /**
     * Return changed data property.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string $prop    Data property slug.
     * @param mixed  $default Set property default value (optional).
     * @return mixed Property data.
     */
    public function get_changed_prop( $prop , $default = '' ) {

        return isset( $this->changes[ $prop ] ) ? $this->changes[ $prop ] : $this->get_prop( $prop , $default );

    }

    /**
     * Return affiliate link's WP_Post data.
     *
     * @since 3.0.0
     * @access public
     *
     * @return object Post data object.
     */
    public function get_post_data() {

        return $this->post_data;

    }

    /**
     * Get the properties global option value.
     *
     * @since 3.0.0
     * @since 3.2.2 Remove 'uncloak' option.
     * @since 3.4.0 Remove 'redirect_type' option. Add additional CSS classes option.
     * @access public
     *
     * @param string $prop Name of property.
     * @return string Global option value.
     */
    public function get_prop_global_option_value( $prop , $default = '' ) {

        switch( $prop ) {

            case 'rel_tags' :
                $option = 'ta_additional_rel_tags';
                break;

            case 'css_classes' :
                $option = 'ta_additional_css_classes';
                break;
        }

        return $option ? get_option( $option , $default ) : $default;
    }

    /**
     * Get the global value for the uncloak property.
     *
     * @deprecated 3.2.0
     *
     * @since 3.0.0
     * @access public
     *
     * @return string Global option value.
     */
    public function get_global_uncloak_value() {

        return $this->get_toggle_prop_global_value( 'uncloak_link' );
    }

    /**
     * Get toggle property global value. This function also checks for "category" valued options.
     *
     * @since 3.2.0
     * @access public
     *
     * @param string $toggle_prop Affiliate linkg toggle property name.
     * @return string 'yes' if true, otherwise 'no'.
     */
    public function get_toggle_prop_global_value( $toggle_prop ) {

        $global_props = apply_filters( 'ta_props_with_global_option' , array(
            'no_follow'      => 'ta_no_follow',
            'new_window'     => 'ta_new_window',
            'uncloak_link'   => '',
            'pass_query_str' => 'ta_pass_query_str'
        ) );
        $global_props_cat = apply_filters( 'ta_prop_selected_categories_option' , array(
            'no_follow'      => 'ta_no_follow_category',
            'new_window'     => 'ta_new_window_category',
            'uncloak_link'   => 'ta_category_to_uncloak',
            'pass_query_str' => 'ta_pass_query_str_category'
        ) );

        if ( ! array_key_exists( $toggle_prop , $global_props ) )
            return 'no';

        // get equivalent global value.
        $prop_value = $toggle_prop == 'uncloak_link' ? 'category' : get_option( $global_props[ $toggle_prop ] );

        if ( ! $prop_value )
            return 'no';

        // if prop value is not set to 'category', then return option value.
        if ( $prop_value != 'category' || ! array_key_exists( $toggle_prop , $global_props_cat ) )
            return $prop_value;

        $prop_cats = maybe_unserialize( get_option( $global_props_cat[ $toggle_prop ] , array() ) );
        $link_cats = $this->get_prop( 'categories' );

        // skip when there are no categories to check (false)
        if ( ! is_array( $prop_cats ) || empty( $prop_cats ) || empty( $link_cats ) )
            return 'no';

        foreach ( $link_cats as $category ) {

            if ( in_array( $category->term_id , $prop_cats ) )
                return 'yes';
        }

        return 'no';
    }

    /**
     * Gets the category slug used for affiliate link (if present).
     *
     * @since 3.2.2
     * @access public
     *
     * @return string Affiliate link ategory slug.
     */
    public function get_category_slug() {

        $cat_slug = $this->get_prop( 'category_slug' );
        return $cat_slug ? $cat_slug : $this->_helper_functions->get_default_category_slug( $this->get_id() , $this->get_prop( 'categories' ) );
    }




    /*
    |--------------------------------------------------------------------------
    | Data setters
    |--------------------------------------------------------------------------
    */

    /**
     * Set new value to properties and save it to $changes property.
     * This stores changes in a special array so we can track what needs to be saved on the DB later.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string $prop Data property slug.
     * @param string $value New property value.
     */
    public function set_prop( $prop , $value ) {

        $default_data = $this->get_merged_default_extended_data();

        if ( array_key_exists( $prop , $this->data ) ) {

            // permalink property must not be changed
            if ( $prop == 'permalink' )
                return;

            if ( gettype( $value ) == gettype( $default_data[ $prop ] ) )
                $this->changes[ $prop ] = $value;
            else {

                // TODO: handle error here.

            }

        } else {

            $this->data[ $prop ]    = $value;
            $this->changes[ $prop ] = $value;

        }

    }




    /*
    |--------------------------------------------------------------------------
    | Save (Create / Update) data to DB
    |--------------------------------------------------------------------------
    */

    /**
     * Save data in $changes to the database.
     *
     * @since 3.0.0
     * @access public
     *
     * @return WP_Error | int On success will return the post ID, otherwise it will return a WP_Error object.
     */
    public function save() {

        if ( ! empty( $this->changes ) ) {

            $post_metas = array();
            $post_data  = array(
                'post_title'    => $this->get_changed_prop( 'name' ),
                'post_name'     => $this->get_changed_prop( 'slug' ),
                'post_status'   => $this->get_changed_prop( 'status' , 'publish' ),
                'post_date'     => $this->get_changed_prop( 'date_created' , current_time( 'mysql' ) ),
                'post_modified' => $this->get_changed_prop( 'date_modified' , current_time( 'mysql' ) )
            );

            foreach ( $this->changes as $prop => $value ) {

                // make sure that property is registered in default data
                if ( ! array_key_exists( $prop , $this->get_merged_default_extended_data() ) )
                    continue;

                if ( in_array( $prop , array( 'permalink' , 'name' , 'slug' , 'status' , 'date_created' , 'date_modified' ) ) )
                    continue;

                $post_metas[ $prop ] = $value;
            }

            // create or update post
            if ( $this->id )
                $post_id = $this->update( $post_data );
            else
                $post_id = $this->create( $post_data );

            if ( ! is_wp_error( $post_id ) )
                $this->update_metas( $post_id , $post_metas );
            else
                return $post_id; // Return WP_Error object on error

            do_action( 'ta_save_affiliate_link' , $this->changes , $this );

            // update instance with new changes.
            $this->object_is_read = false;
            $this->read();

        } else
            return new \WP_Error( 'ta_affiliate_link_no_changes' , __( 'Unable to save affiliate link as there are no changes registered on the object yet.' , 'thirstyaffiliates' ) , array( 'changes' => $this->changes , 'affiliate_link' => $this ) );

        return $post_id;
    }

    /**
     * Create the affiliate link post.
     *
     * @since 3.0.0
     * @access private
     *
     * @param array $post_data Affiliate link post data.
     * @param WP_Error|int WP_Error on error, ID of newly created post otherwise.
     */
    private function create( $post_data ) {

        $post_data = array_merge( array( 'post_type' => Plugin_Constants::AFFILIATE_LINKS_CPT ) , $post_data );
        $this->id  = wp_insert_post( $post_data );

        return $this->id;

    }

    /**
     * Update the affiliate link post.
     *
     * @since 3.0.0
     * @access private
     *
     * @param array $post_data Affiliate link post data.
     * @return int ID of the updated post upon success. 0 on failure.
     */
    private function update( $post_data ) {

        $post_data = array_merge( array( 'ID' => $this->id ) , $post_data );
        return wp_update_post( $post_data , true );

    }

    /**
     * Update/add the affiliate link meta data.
     *
     * @since 3.0.0
     * @access private
     *
     * @param int $post_id Affiliate link post ID.
     * @param array $post_metas Affiliate link meta data.
     */
    private function update_metas( $post_id , $post_metas ) {

        foreach ( $post_metas as $key => $value )
            update_post_meta( $post_id , Plugin_Constants::META_DATA_PREFIX . $key , $value );

    }




    /*
    |--------------------------------------------------------------------------
    | Utility Functions.
    |--------------------------------------------------------------------------
    */

    /**
     * Conditional function that checks if a property is true for the affiliate link. This function also checks global and category set values.
     *
     * @since 3.2.0
     * @access public
     *
     * @param string $toggle_prop Affiliate link toggle property to check.
     * @return boolean true | false.
     */
    public function is( $toggle_prop ) {

        // check if global setting for uncloak link is enabled.
        if ( $toggle_prop == 'uncloak_link' && get_option( 'ta_uncloak_link_per_link' ) !== 'yes' )
            return;

        $prop_value = $this->get_prop( $toggle_prop );

        // if prop value is not set to 'global', then return with the boolean equivalent of its value.
        if ( $prop_value != 'global' )
            return $prop_value == 'yes' ? true : false;

        // get property global value. Also checks for category selected options.
        $prop_value = $this->get_toggle_prop_global_value( $toggle_prop );

        return $prop_value == 'yes' ? true : false;
    }

    /**
     * Count affiliate link clicks.
     *
     * @since 3.0.0
     * @since 3.1.0 Add $date_offset parameter to count links only to a certain point.
     * @access public
     *
     * @param string $date_offset Date before limit to check
     * @return int Total number of clicks.
     */
    public function count_clicks( $date_offset = '' ) {

        global $wpdb;

        $table_name = $wpdb->prefix . Plugin_Constants::LINK_CLICK_DB;
        $link_id    = $this->get_id();
        $query      = "SELECT count(*) from $table_name WHERE link_id = $link_id";
        $query     .= ( $date_offset && \DateTime::createFromFormat('Y-m-d H:i:s', $date_offset ) !== false ) ? " AND date_clicked > '$date_offset'" : '';
        $clicks     = $wpdb->get_var( $query );

        return (int) $clicks;
    }

    /**
     * Scan where links are inserted.
     *
     * @since 3.2.0
     * @since 3.3.3 Improve the query to specify the results by searching using the permalink value, and alternating between the used link prefixes.
     * @access public
     *
     * @return array List of WP_Post IDs where affiliate link is inserted in content.
     */
    public function scan_where_links_inserted() {

        global $wpdb;

        // prepare the query.
        $post_ids      = array();
        $link_id       = $this->get_id();
        $cpt_slug      = Plugin_Constants::AFFILIATE_LINKS_CPT;
        $types         = get_post_types( array( 'public' => true ) , 'names' , 'and' );
        $types_str     = implode( "','" , $types );
        $permalink     = $this->get_prop( 'permalink' );
        $link_prefix   = $this->_helper_functions->get_thirstylink_link_prefix();
        $link_prefixes = $this->_helper_functions->get_option( 'ta_used_link_prefixes' , array() );
        $like_query    = array();

        foreach ( $link_prefixes as $prefix )
            $like_query[] = str_replace( $link_prefix , $prefix , "post_content LIKE '%$permalink\"%'" );

        $like_query_str = implode( ' OR ' , $like_query );
        $query          = "SELECT ID FROM $wpdb->posts WHERE ( $like_query_str OR post_content LIKE '%[thirstylink%ids=\"$link_id%' ) AND post_type IN ( '$types_str' ) AND post_status = 'publish'";

        // fetch WP_Post IDs where link is inserted to.
        $raw_ids = $wpdb->get_col( $query );

        // save last scanned
        update_post_meta( $this->get_id() , Plugin_Constants::META_DATA_PREFIX . 'scanned_inserted' , current_time( 'mysql' , true ) );

        // save to custom meta.
        $post_ids = array_map( 'intval' , $raw_ids );
        update_post_meta( $this->get_id() , Plugin_Constants::META_DATA_PREFIX . 'inserted_to' , $post_ids );

        return $post_ids;
    }

}
