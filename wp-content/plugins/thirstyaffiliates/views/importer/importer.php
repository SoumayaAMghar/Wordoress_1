<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="ta-blur-wrap">

    <div class="ta-blur">

        <h3><?php _e( 'STEP 1 - select your CSV file:' , 'thirstyaffiliates' ); ?></h3>

        <p>
            <?php _e( 'This tool lets you upload a properly formatted CSV file containing a list of affiliate links. These links will be bulk imported for you directly into ThirstyAffiliates.' , 'thirstyaffiliates' ); ?>
        </p>

        <p>
            <?php _e( 'Any categories you specify in your import file that are not found in the system will be created as the links are imported' , 'thirstyaffiliates' ); ?>
        </p>

        <form class="tap-round-box" id="tap_upload_csv_form" enctype="multipart/form-data" method="post">
            <p>
                <strong>Import from CSV</strong>
            </p>
            <p>
                Maximum file size: 128 MB </p>
            <p>
                <label for="import">
                    <input type="file" id="import" name="import">
                    Select a CSV file (comma delimited .csv) </label>
            </p>
            <p>
                <label for="override_links">
                    <input type="checkbox" id="override_links" name="override_links">
                    Override already existing links with the same slug? </label>
            </p>
            <p>
                <label for="skip_escape">
                    <input type="checkbox" id="skip_escape" name="skip_escape">
                    Skip escaping of URLs? </label>
            </p>
            <p>
                <button class="button-primary" type="submit">
                    Upload file and import </button>
                <span class="tap-spinner"></span>
            </p>
        </form>

    </div>

    <?php
        $section_title = 'ThirstyAffiliates CSV Importer';
        $upgrade_link = 'https://thirstyaffiliates.com/pricing?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=importer';
        include_once $this->_constants->VIEWS_ROOT_PATH() . 'ta-upgrade.php';
    ?>

</div>
