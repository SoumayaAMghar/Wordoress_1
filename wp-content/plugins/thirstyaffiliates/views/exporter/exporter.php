<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="ta-blur-wrap">

    <div class="ta-blur">

        <h3><?php _e( 'STEP 1 - Export your links:' , 'thirstyaffiliates' ); ?></h3>

        <p>
            <?php _e( 'This tool lets you export a properly formatted CSV file containing a list of all the affiliate links on this website. This CSV file will be compatible with the ThirstyAffiliates CSV Importer.' , 'thirstyaffiliates' ); ?>
        </p>

        <p>
            <?php _e( 'You can choose to only export the links from a specific category.' , 'thirstyaffiliates' ); ?>
        </p>

        <form class="tap-round-box" id="tap_upload_csv_form" enctype="multipart/form-data" method="post">

            <p>
            <label for="export_category">
                <?php _e( 'Category to export from:' , 'thirstyaffiliates' ); ?>
            </label>
            <select id="export_category">
                <option value="all" selected="selected"><?php _e( '-- All Categories --' , 'thirstyaffiliates' ); ?></option>
            </select>
        </p>

        <p>
            <input type="submit" class="button-primary" value="<?php esc_attr_e( 'Export affiliate links' , 'thirstyaffiliates' ); ?>" />
            <span class="spinner"></span>
        </p>

        </form>

    </div>

    <?php
        $section_title = 'ThirstyAffiliates CSV Exporter';
        $upgrade_link = 'https://thirstyaffiliates.com/pricing?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=exporter';
        include_once $this->_constants->VIEWS_ROOT_PATH() . 'ta-upgrade.php';
    ?>

</div>
