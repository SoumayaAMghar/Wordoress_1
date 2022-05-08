<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wp_nonce_field( 'thirsty_affiliates_cpt_nonce', '_thirstyaffiliates_nonce' ); ?>

<p class="destination-url-field">
    <label class="info-label" for="ta_destination_url">
        <?php _e( 'Destination URL:' , 'thirstyaffiliates' ); ?>
    </label>
    <input type="url" class="ta-form-input" id="ta_destination_url" name="ta_destination_url" value="<?php echo $thirstylink->get_prop( 'destination_url' ); ?>">
</p>

<?php if ( $screen->action != 'add' ) : ?>
<p class="cloaked-url-field">
    <label class="info-label" for="ta_destination_url">
        <?php _e( 'Cloaked URL:' , 'thirstyaffiliates' ); ?>
    </label>
    <span class="cloaked-fields">
        <input type="url" class="ta-form-input" id="ta_cloaked_url" name="ta_cloaked_url" value="<?php echo esc_attr( $thirstylink->get_prop( 'permalink' ) ); ?>" readonly>
        <button type="button" class="edit-ta-slug button"><?php _e( 'Edit slug' , 'thirstyaffiliates' ); ?></button>
        <a class="button" href="<?php echo esc_attr( $thirstylink->get_prop( 'permalink' ) ); ?>" target="_blank"><?php _e( 'Visit Link' , 'thirstyaffiliates' ); ?></a>
    </span>
    <span class="slug-fields" style="display: none;">
        <input type="text" class="ta-form-input" id="ta_slug" name="post_name" pattern="[a-z0-9-_% ]+" value="<?php echo esc_attr( $thirstylink->get_prop( 'slug' ) ); ?>">
        <span class="edit-slug-warning"><?php _e( '<strong>Warning:</strong> Editing the slug will break already inserted links for this affiliate link.' , 'thirstyaffiliates' ); ?></span>
        <button type="button" class="save-ta-slug button"><?php _e( 'Save' , 'thirstyaffiliates' ); ?></button>
    </span>
</p>
<?php endif; ?>

<?php if ( get_option( 'ta_show_cat_in_slug' ) === 'yes'  ) : ?>
    <p>
        <label class="info-label" for="ta_category_slug">
            <?php _e( 'Category to show in slug:' , 'thirstyaffiliates' ); ?>
        </label>
        <select name="ta_category_slug" data-home-link-prefix="<?php echo esc_attr( $home_link_prefix ); ?>">
            <option value="" data-slug="<?php echo esc_attr( $default_cat_slug ); ?>" <?php selected( $thirstylink->get_prop( 'category_slug' ) , '' ) ?> >
                <?php _e( 'Default' , 'thirstylink' ); ?>
            </option>
            <?php foreach( $thirstylink->get_prop( 'categories' ) as $category ) : ?>
                <option value="<?php echo $category->term_id; ?>" data-slug="<?php echo esc_attr( $category->slug ); ?>" <?php selected( $thirstylink->get_prop( 'category_slug' ) , $category->slug ) ?> >
                    <?php echo $category->name; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
<?php endif; ?>

<?php do_action( 'ta_urls_metabox_urls_fields' , $post ); ?>
