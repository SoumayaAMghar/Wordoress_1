<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="thirsty-attached-image">
    <span class="thirsty-remove-img" title="<?php esc_attr_e( 'Remove This Image' , 'thirstyaffiliates' ); ?>" id="<?php echo esc_attr( $attachment_id ); ?>">&times;</span>
    <a class="thirsty-img thickbox" href="<?php echo esc_url( $img[0] ); ?>" rel="gallery-linkimgs" title="<?php echo esc_attr( sanitize_text_field( get_the_title( $attachment_id ) ) ); ?>">
        <?php echo wp_get_attachment_image( $attachment_id , array( 100 , 100 ) ); ?>
    </a>
</div>
