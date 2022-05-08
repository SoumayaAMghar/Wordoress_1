<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<html>

<head>
<?php
    do_action('admin_print_styles');
	do_action('admin_print_scripts');
	do_action('admin_head');
?>
</head>

<body>

    <div class="edit-shortcode-wrap">

        <form method="post">

            <div class="field-row">
                <label for="shortcode_text">
                    <?php _e( 'Link text:' , 'thirstyaffiliates' ); ?>
                </label>
                <input type="text" class="form-control" id="shortcode_text" name="shortcode_text" value="" required>
            </div>

            <div class="field-row">
                <label for="shortcode_ids">
                    <?php _e( 'Affiliate link ids:' , 'thirstyaffiliates' ); ?>
                </label>
                <input type="text" class="form-control" id="shortcode_ids" name="shortcode_ids" value="" required>
            </div>

            <div class="field-row one-half first-half">
                <label for="shortcode_class">
                    <?php _e( 'Class link attribute:' , 'thirstyaffiliates' ); ?>
                </label>
                <input type="text" class="form-control" id="shortcode_class" name="shortcode_class" value="">
            </div>

            <div class="field-row one-half">
                <label for="shortcode_title">
                    <?php _e( 'Link title attribute:' , 'thirstyaffiliates' ); ?>
                </label>
                <input type="text" class="form-control" id="shortcode_title" name="shortcode_title" value="">
            </div>

            <div class="field-row one-half first-half">
                <label for="shortcode_rel">
                    <?php _e( 'Link rel attribute:' , 'thirstyaffiliates' ); ?>
                </label>
                <input type="text" class="form-control" id="shortcode_rel" name="shortcode_rel" value="">
            </div>

            <div class="field-row one-half">
                <label for="shortcode_target">
                    <?php _e( 'link target attribute:' , 'thirstyaffiliates' ); ?>
                </label>
                <input type="text" class="form-control" id="shortcode_target" name="shortcode_target" value="">
            </div>

            <div class="field-row submit-row">
                <span class="ta_spinner" style="background-image: url('<?php echo $this->_constants->IMAGES_ROOT_URL() . 'spinner.gif'; ?>');"></span>
                <button class="button button-primary" id="submit" type="submit" name="add_link">
                    <?php _e( 'Edit Shortcode' , 'thirstyaffiliates' ); ?>
                </button>
            </div>

            <input type="hidden" name="action" value="ta_process_edit_affiliate_link_shortcode">
            <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">

            <?php wp_nonce_field( 'ta_process_edit_affiliate_link_shortcode' , '_ta_edit_shortcode_nonce' ); ?>

        </form>

    </div>

</body>

</html>


