<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="thirsty-attached-image external-image">
    <span class="thirsty-remove-img remove-external" title="<?php esc_attr_e( 'Remove This Image' , 'thirstyaffiliates' ); ?>" id="<?php echo md5( $img_url ); ?>">&times;</span>
    <a class="thirsty-img thickbox" href="<?php echo esc_url( $img_url ); ?>" rel="gallery-linkimgs" title="<?php esc_attr_e( 'external image' , 'thirstyaffiliates' ); ?>">
        <img src="<?php echo esc_url( $img_url ); ?>" width="100" height="100">
    </a>
</div>
