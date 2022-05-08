<html>

<head>
<?php
    do_action('admin_print_styles');
	do_action('admin_print_scripts');
	do_action('admin_head');
?>
</head>

<body>

    <div id="quick_add_affiliate_link" data-htmleditor="<?php echo $html_editor; ?>">

        <form method="post" onsubmit="dummySubmitFunc(event)">

            <div class="field-row">
                <label for="ta_link_name">
                    <?php _e( 'Link Name:' , 'thirstyaffiliates' ); ?>
                </label>
                <input type="text" class="form-control" id="ta_link_name" name="ta_link_name" value="<?php echo esc_attr( $selection ); ?>" required>
            </div>

            <div class="field-row">
                <label for="ta_destination_url">
                    <?php _e( 'Destination URL:' , 'thirstyaffiliates' ); ?>
                </label>
                <span class="guide">
                    <?php _e( 'http:// or https:// is required' , 'thirstyaffiliates' ); ?>
                </span>
                <input type="url" class="form-control" id="ta_destination_url" name="ta_destination_url" value="" required>
            </div>

            <div class="field-row link-option">
                <label for="ta_redirect_type">
                    <?php _e( 'Redirect Type:' , 'thirstyaffiliates' ); ?>
                </label>
                <select id="ta_redirect_type" name="ta_redirect_type">
                    <option value="global">
                        <?php echo sprintf( __( 'Global (%s)' , 'thirstyaffiliates' ) , $default_redirect_type ); ?>
                    </option>
                    <?php foreach ( $redirect_types as $redirect_type => $redirect_label ) : ?>
                        <option value="<?php echo esc_attr( $redirect_type ); ?>">
                            <?php echo esc_html( $redirect_label ); ?>
                        </option>
                    <?php endforeach; ?>
                <select>
            </div>

            <div class="field-row link-option">
                <label for="ta_no_follow">
                    <?php _e( 'No follow this link?' , 'thirstyaffiliates' ); ?>
                </label>
                <select id="ta_no_follow" name="ta_no_follow">
                    <option value="global"><?php echo sprintf( __( 'Global (%s)' , 'thirstyaffiliates' ) , $global_no_follow ); ?></option>
                    <option value="yes"><?php _e( 'Yes' , 'thirstyaffiliates' ); ?></option>
                    <option value="no"><?php _e( 'No' , 'thirstyaffiliates' ); ?></option>
                <select>
            </div>

            <div class="field-row link-option">
                <label for="ta_new_window">
                    <?php _e( 'Open this link in new window?' , 'thirstyaffiliates' ); ?>
                </label>
                <select id="ta_new_window" name="ta_new_window">
                    <option value="global"><?php echo sprintf( __( 'Global (%s)' , 'thirstyaffiliates' ) , $global_new_window ); ?></option>
                    <option value="yes"><?php _e( 'Yes' , 'thirstyaffiliates' ); ?></option>
                    <option value="no"><?php _e( 'No' , 'thirstyaffiliates' ); ?></option>
                <select>
            </div>

            <div class="field-row link-categories">
                <label for="ta_link_categories">
                    <?php _e( 'Link Categories' , 'thirstyaffiliates' ); ?>
                </label>
                <select multiple id="link_categories" name="ta_link_categories[]" data-placeholder="<?php esc_attr_e( 'Select categories...' , 'thirstyaffiliates' ); ?>">
                    <?php foreach ( $categories as $category ) : ?>
                        <option value="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php do_action( 'ta_quick_add_affiliate_link_form' ); ?>

            <div class="field-row submit-row">
                <span class="ta_spinner" style="background-image: url('<?php echo $this->_constants->IMAGES_ROOT_URL() . 'spinner.gif'; ?>');"></span>
                <button class="button" type="submit" name="add_link">
                    <?php _e( 'Add Link' , 'thirstyaffiliates' ); ?>
                </button>
                <button class="button button-primary" type="submit" name="add_link_and_insert">
                    <?php _e( 'Add Link & Insert Into Post' , 'thirstyaffiliates' ); ?>
                </button>
            </div>

            <input type="hidden" name="action" value="ta_process_quick_add_affiliate_link">
            <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">

            <?php wp_nonce_field( 'ta_process_quick_add_affiliate_link' ); ?>
        </form>
    </div>

<script>
jQuery( document ).ready( function( $ ) {
    $( 'select#link_categories' ).selectize({
        plugins   : [ 'remove_button' , 'drag_drop' ]
    });
});

function dummySubmitFunc( event ) { event.preventDefault(); }
</script>
</body>

</html>
