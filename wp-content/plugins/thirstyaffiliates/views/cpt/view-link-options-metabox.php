<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wp_nonce_field( 'thirsty_affiliates_cpt_nonce', '_thirstyaffiliates_nonce' ); ?>

<p>
    <label class="info-label block" for="ta_no_follow">
        <?php _e( 'No follow this link? (server side redirects)' , 'thirstyaffiliates' ); ?>
        <span class="tooltip" data-tip="<?php esc_attr_e( 'Adds the rel="nofollow" tag so search engines don\'t pass link juice.' , 'thirstyaffiliates' ); ?>"></span>
    </label>
    <select id="ta_no_follow" name="ta_no_follow">
        <option value="global" <?php selected( $thirstylink->get_prop( 'no_follow' ) , 'global' ); ?>><?php echo sprintf( __( 'Global (%s)' , 'thirstyaffiliates' ) , $global_no_follow ); ?></option>
        <option value="yes" <?php selected( $thirstylink->get_prop( 'no_follow' ) , 'yes' ); ?>><?php _e( 'Yes' , 'thirstyaffiliates' ); ?></option>
        <option value="no" <?php selected( $thirstylink->get_prop( 'no_follow' ) , 'no' ); ?>><?php _e( 'No' , 'thirstyaffiliates' ); ?></option>
    <select>
</p>

<p>
    <label class="info-label block" for="ta_new_window">
        <?php _e( 'Open this link in new window?' , 'thirstyaffiliates' ); ?>
        <span class="tooltip" data-tip="<?php esc_attr_e( 'Opens links in a new window when clicked on.' , 'thirstyaffiliates' ); ?>"></span>
    </label>
    <select id="ta_new_window" name="ta_new_window">
        <option value="global" <?php selected( $thirstylink->get_prop( 'new_window' ) , 'global' ); ?>><?php echo sprintf( __( 'Global (%s)' , 'thirstyaffiliates' ) , $global_new_window ); ?></option>
        <option value="yes" <?php selected( $thirstylink->get_prop( 'new_window' ) , 'yes' ); ?>><?php _e( 'Yes' , 'thirstyaffiliates' ); ?></option>
        <option value="no" <?php selected( $thirstylink->get_prop( 'new_window' ) , 'no' ); ?>><?php _e( 'No' , 'thirstyaffiliates' ); ?></option>
    <select>
</p>

<p>
    <label class="info-label block" for="ta_pass_query_str">
        <?php _e( 'Pass query string to destination url?' , 'thirstyaffiliates' ); ?>
        <span class="tooltip" data-tip="<?php esc_attr_e( 'Passes the query strings present after the cloaked url automatically to the destination url when redirecting.' , 'thirstyaffiliates' ); ?>"></span>
    </label>
    <select id="ta_pass_query_str" name="ta_pass_query_str">
        <option value="global" <?php selected( $thirstylink->get_prop( 'pass_query_str' ) , 'global' ); ?>><?php echo sprintf( __( 'Global (%s)' , 'thirstyaffiliates' ) , $global_pass_query_str ); ?></option>
        <option value="yes" <?php selected( $thirstylink->get_prop( 'pass_query_str' ) , 'yes' ); ?>><?php _e( 'Yes' , 'thirstyaffiliates' ); ?></option>
        <option value="no" <?php selected( $thirstylink->get_prop( 'pass_query_str' ) , 'no' ); ?>><?php _e( 'No' , 'thirstyaffiliates' ); ?></option>
    <select>
</p>

<?php if ( get_option( 'ta_uncloak_link_per_link' ) === 'yes' ) : ?>
<p>
    <label class="info-label block" for="ta_uncloak_link">
        <?php _e( 'Uncloak link?' , 'thirstyaffiliates' ); ?>
        <span class="tooltip" data-tip="<?php esc_attr_e( 'Uncloaks the link when loaded on the frontend.' , 'thirstyaffiliates' ); ?>"></span>
    </label>
    <select id="ta_uncloak_link" name="ta_uncloak_link">
        <option value="global" <?php selected( $thirstylink->get_prop( 'uncloak_link' ) , 'global' ); ?>><?php echo sprintf( __( 'Global (%s)' , 'thirstyaffiliates' ) , $global_uncloak ); ?></option>
        <option value="yes" <?php selected( $thirstylink->get_prop( 'uncloak_link' ) , 'yes' ); ?>><?php _e( 'Yes' , 'thirstyaffiliates' ); ?></option>
        <option value="no" <?php selected( $thirstylink->get_prop( 'uncloak_link' ) , 'no' ); ?>><?php _e( 'No' , 'thirstyaffiliates' ); ?></option>
    <select>
</p>
<?php endif; ?>

<p>
    <label class="info-label block" for="ta_redirect_type">
        <?php _e( 'Redirect type (server side redirects):' , 'thirstyaffiliates' ); ?>
        <span class="tooltip" data-tip="<?php esc_attr_e( 'Override the default redirection type for this link.' , 'thirstyaffiliates' ); ?>"></span>
    </label>
    <select id="ta_redirect_type" name="ta_redirect_type">
        <option value="global" <?php selected( $post_redirect_type , 'global' ); ?>>
            <?php echo sprintf( __( 'Global (%s)' , 'thirstyaffiliates' ) , $default_redirect_type ); ?>
        </option>
        <?php foreach ( $redirect_types as $redirect_type => $redirect_label ) : ?>
            <option value="<?php echo esc_attr( $redirect_type ); ?>" <?php selected( $post_redirect_type , $redirect_type ); ?>>
                <?php echo $redirect_label; ?>
            </option>
        <?php endforeach; ?>
    <select>
</p>

<p>
    <label class="info-label" for="ta_rel_tags">
        <?php _e( 'Additional rel tags:' , 'thirstyaffiliates' ); ?>
    </label>
    <input type="text" class="ta-form-input" id="ta_rel_tags" name="ta_rel_tags" value="<?php echo esc_attr( $rel_tags ); ?>" placeholder="<?php echo esc_attr( $global_rel_tags ); ?>">
</p>

<p>
    <label class="info-label" for="ta_css_classes">
        <?php _e( 'Additional CSS classes:' , 'thirstyaffiliates' ); ?>
    </label>
    <input type="text" class="ta-form-input" id="ta_css_classes" name="ta_css_classes" value="<?php echo esc_attr( $css_classes ); ?>" placeholder="<?php echo esc_attr( $global_css_classes ); ?>">
</p>

<script type="text/javascript">
jQuery( document ).ready( function($) {

    $( "#ta-link-options-metabox label .tooltip" ).tipTip({
        "attribute"       : "data-tip",
        "defaultPosition" : "left",
        "fadeIn"          : 50,
        "fadeOut"         : 50,
        "delay"           : 200
    });

});
</script>
