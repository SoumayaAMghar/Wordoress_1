<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="scanned-inserted-status">
    <button id="inserted-link-scan-trigger" class="button-primary" type="button"><?php _e( 'Start Scan' , 'thirstyaffiliates' ); ?></button>
    <span class="last-scanned"><?php echo $last_scanned_txt; ?></span>
</div>

<div class="inserted-into-table">
    <table>
        <thead>
            <tr>
                <th class="id"><?php _e( 'ID' , 'thirstyaffiliates' ); ?></th>
                <th class="title"><?php _e( 'Title' , 'thirstyaffiliates' ); ?></th>
                <th class="post-type"><?php _e( 'Post Type' , 'thirstyaffiliates' ); ?></th>
                <th class="actions"></th>
            </tr>
        </thead>
        <tbody>
            <?php echo $inserted_into_rows_html; ?>
        </tbody>
    </table>
</div>

<div class="overlay" style="background-image:url(<?php echo esc_url( $spinner_image_src ); ?>);"></div>
