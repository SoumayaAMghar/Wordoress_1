<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="ta-blur-wrap">
    <div class="ta-blur">
        <div id="col-container" class="wp-clearfix">
            <div id="col-left">
                <div class="col-wrap">
                    <div class="form-wrap">
                        <h2>Add New Event Notification</h2>
                        <form id="addtag" method="post" class="validate">
                            <div class="event-notification-description">
                                <p>Event notifications allow you to send a notification to someone when some significant event happens with your affiliate links.</p>
                                <p>You can setup different event notifications here, then assign one or more to an affiliate link and ThirstyAffiliates will monitor that link for those events.</p>
                            </div>
                            <div class="form-field form-required term-xname-wrap">
                                <label for="tag-name">
                                    Name: </label>
                                <inputname" id="tag-name" type="text" value="" size="40" aria-required="true">
                                <p>Give your event notification a name for internal reference.</p>
                            </div>
                            <div class="form-field term-recipient-wrap">
                                <label for="tag-name">
                                    Recipient Email: </label>
                                <input id="recipient_email" type="email" value="" size="40" aria-required="true" placeholder="admin@test.com">
                            </div>
                            <div class="form-field form-required event-nofication-type-wrap">
                                <label for="event_notification_type">
                                    Notification Type: </label>
                                <div class="radio-list" id="tap_event_notification_type">
                                    <label>
                                        <input type="radio" value="click_count_email" checked="" required="">
                                        Send an email when the link reaches a defined number of clicks. </label>
                                    <label>
                                        <input type="radio" value="click_count_email_24hours" checked="" required="">
                                        Send an email when the link reaches a defined number of clicks within 24 hours. </label>
                                </div>
                            </div>
                            <div class="form-field form-required event-notification-trigger-value-wrap">
                                <label>
                                    Trigger Value: </label>
                                <input type="number" id="tap_event_notification_trigger_value" min="1" required="">
                            </div>
                            <p class="submit">
                                <input type="submit" id="submit" class="button button-primary" value="Add New Event Notification"> <span class="spinner"></span>
                            </p>
                        </form>
                    </div>
                </div>
            </div><!-- /col-left -->
            <div id="col-right">
                <div class="col-wrap">
                    <form id="posts-filter" method="post">
                        <div class="tablenav top">
                            <div class="alignleft actions bulkactions">
                                <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select id="bulk-action-selector-top">
                                    <option value="-1">Bulk Actions</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <input type="submit" id="doaction" class="button action" value="Apply">
                            </div>
                            <div class="tablenav-pages no-pages"><span class="displaying-num">0 items</span>
                                <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                                    <span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input class="current-page" id="current-page-selector" type="text" value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text"> of <span class="total-pages">0</span></span></span>
                                    <a class="next-page button" href="#"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                                    <a class="last-page button" href="#"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a></span></div>
                            <br class="clear">
                        </div>
                        <h2 class="screen-reader-text">Event notification list</h2>
                        <table class="wp-list-table widefat fixed striped tags">
                            <thead>
                                <tr>
                                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                                    <th scope="col" id="name" class="manage-column column-name column-primary sortable desc"><a href="#"><span>Name</span><span class="sorting-indicator"></span></a></th>
                                    <th scope="col" id="event_notification_type" class="manage-column column-event_notification_type">Notification Type</th>
                                    <th scope="col" id="event_trigger_value" class="manage-column column-event_trigger_value">Trigger Value</th>
                                    <th scope="col" id="posts" class="manage-column column-posts num sortable desc"><a href="#"><span>Count</span><span class="sorting-indicator"></span></a></th>
                                </tr>
                            </thead>
                            <tbody id="the-list" data-wp-lists="list:tag">
                                <tr class="no-items">
                                    <td class="colspanchange" colspan="5">Not Found</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></td>
                                    <th scope="col" class="manage-column column-name column-primary sortable desc"><a href="#"><span>Name</span><span class="sorting-indicator"></span></a></th>
                                    <th scope="col" class="manage-column column-event_notification_type">Notification Type</th>
                                    <th scope="col" class="manage-column column-event_trigger_value">Trigger Value</th>
                                    <th scope="col" class="manage-column column-posts num sortable desc"><a href="#"><span>Count</span><span class="sorting-indicator"></span></a></th>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="tablenav bottom">
                            <div class="alignleft actions bulkactions">
                                <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label><select id="bulk-action-selector-bottom">
                                    <option value="-1">Bulk Actions</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <input type="submit" id="doaction2" class="button action" value="Apply">
                            </div>
                            <div class="tablenav-pages no-pages"><span class="displaying-num">0 items</span>
                                <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                                    <span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">1 of <span class="total-pages">0</span></span></span>
                                    <a class="next-page button" href="#"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                                    <a class="last-page button" href="#"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a></span></div>
                            <br class="clear">
                        </div>
                    </form>
                </div>
            </div><!-- /col-right -->
        </div>
    </div>
    <?php
        $section_title = 'Event Notifications from Amazon';
        $upgrade_link = 'https://thirstyaffiliates.com/pricing?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=event_notifications';
        include_once $this->_constants->VIEWS_ROOT_PATH() . 'ta-upgrade.php';
    ?>
</div>
