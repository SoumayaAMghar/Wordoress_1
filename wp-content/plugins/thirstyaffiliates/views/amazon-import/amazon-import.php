<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="ta-blur-wrap">
    <div class="ta-blur">
        <div id="main-controls">
            <div id="search-controls">
                <h3 class="title">Search Amazon For Products</h3>
                <p class="desc">Please <a href="#">input these required amazon credentials</a> in order to successfully query the Amazon Product Advertisement API:<br><b>Amazon Access Key ID</b> , <b>Amazon Secret Key</b> , <b>Amazon Associate Tags</b></p>
            </div>
            <div id="legend">
                <h3 class="title">Legend</h3>
                <ul>
                    <li class="import-link"><span class="icon"></span><span class="text">Import Link</span></li>
                    <li class="quick-import"><span class="icon"></span><span class="text">1-Click Import Link</span></li>
                </ul>
            </div>
            <div style="float: none; display: block; clear: both;"></div>
        </div>
        <div id="search-results">
            <div id="search-results-table_wrapper" class="dataTables_wrapper">
                <div class="bulkactions" style="display: inline-block; margin-right: 10px;"><select id="bulk-action-selector" autocomplete="off">
                        <option value="">Bulk Actions</option>
                        <option value="import">Import</option>
                        <option value="delete">Delete Imported Link</option>
                    </select><input type="button" id="do-bulk-action" class="button action" value="Apply"></div>
                <div style="display: inline-block;" id="search-results-table_filter" class="dataTables_filter"><label>Filter Results:<input type="search" class="" placeholder="" aria-controls="search-results-table"></label></div>
                <table style="margin: 20px 0;" id="search-results-table" class="wp-list-table widefat fixed striped posts dataTable" cellspacing="0" width="100%" role="grid" aria-describedby="search-results-table_info" style="width: 100%;">
                    <thead>
                        <tr role="row">
                            <th class="manage-column check-column sorting_disabled" style="padding: 10px 3px 6px; width: 30px;" rowspan="1" colspan="1" aria-label=""><input type="checkbox" id="select-all-search-results-top" class="select-all-search-results"></th>
                            <th class="image sorting_disabled" rowspan="1" colspan="1" style="width: 380px;" aria-label="Image">Image</th>
                            <th class="title sorting" tabindex="0" aria-controls="search-results-table" rowspan="1" colspan="1" style="width: 381px;" aria-label="Title: activate to sort column ascending">Title</th>
                            <th class="controls-column sorting_disabled" rowspan="1" colspan="1" style="width: 381px;" aria-label="Import Link">Import Link</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="manage-column check-column sorting_disabled" style="padding: 10px 3px 6px 3px;" rowspan="1" colspan="1"><input type="checkbox" id="select-all-search-results-top" class="select-all-search-results"></th>
                            <th class="image" rowspan="1" colspan="1">Image</th>
                            <th class="title" rowspan="1" colspan="1">Title</th>
                            <th class="controls-column" rowspan="1" colspan="1">Import Link</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <tr class="odd">
                            <td valign="top" colspan="7" class="dataTables_empty">No Data Available</td>
                        </tr>
                    </tbody>
                </table>
                <div class="dataTables_info" id="search-results-table_info" role="status" aria-live="polite"><p>Showing 0 to 0 of 0 entries</p></div>
                <div class="tap-amazon-load-more"><button type="button" class="button"><span class="tap-amazon-button-text">Load More</span><span class="spinner" style="display: none;"></span></button></div>
            </div><!-- #search-results-table -->
        </div>
    </div>
    <?php
        $section_title = 'Import Affiliate Links from Amazon';
        $upgrade_link = 'https://thirstyaffiliates.com/pricing?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=amazon_import';
        include_once $this->_constants->VIEWS_ROOT_PATH() . 'ta-upgrade.php';
    ?>
</div>
