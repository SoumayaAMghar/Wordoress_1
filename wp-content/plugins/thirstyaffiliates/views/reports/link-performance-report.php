<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="link-performance-report wp-core-ui">
    <div class="stats-range">
        <ul>
            <?php foreach ( $range_nav as $nrange => $label ) : ?>
                <li<?php echo ( $nrange == $current_range ) ? ' class="current"' : ''; ?>>
                    <a href="<?php echo admin_url( 'edit.php?post_type=thirstylink&page=thirsty-reports&range=' . $nrange ); ?>">
                        <?php echo $label; ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <li class="custom-range">
                <span><?php _e( 'Custom' , 'thirstyaffiliates' ); ?></span>
                <form id="custom-date-range" method="GET">
                    <input type="hidden" name="post_type" value="<?php echo $cpt_slug; ?>">
                    <input type="hidden" name="page" value="thirsty-reports">
                    <input type="hidden" name="range" value="custom">
                    <input type="text" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( $start_date ); ?>" name="start_date" class="range_datepicker from" required>
                    <span>&mdash;</span>
                    <input type="text" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( $end_date ); ?>" name="end_date" class="range_datepicker to" required>
                    <button type="submit" class="button"><?php _e( 'Go' , 'thirstyaffiliates' ); ?></button>
                </form>
            </li>

            <?php do_action( 'ta_stats_reporting_menu_items' ); ?>

        </ul>
    </div>
    <div class="report-chart-wrap">

        <div class="chart-sidebar">

            <?php do_action( 'ta_before_stats_reporting_chart_legend' ); ?>

            <ul class="chart-legend">
                <li style="border-color: #3498db">
                    <?php _e( 'General' , 'thirstyaffiliates' ); ?>
                    <em class="count"></em>
                    <span><?php _e( 'All links' , 'thirstyaffiliates' ); ?></span>
                </li>
            </ul>

            <?php do_action( 'ta_after_stats_reporting_chart_legend' ); ?>

            <div class="add-legend">
                <label for="add-report-data"><?php _e( 'Fetch report for specific link:' , 'thirstyaffiliates' ); ?></label>
                <div class="input-wrap">
                    <input type="text" id="add-report-data" placeholder="<?php esc_attr_e( 'Search affiliate link' , 'thirstyaffiliates' ); ?>"
                        data-range="<?php echo esc_attr( $current_range ); ?>"
                        data-start-date="<?php echo esc_attr( $start_date ); ?>"
                        data-end-date="<?php echo esc_attr( $end_date ); ?>"
                        data-linkid="<?php echo esc_attr( $link_id ); ?>">
                    <ul class="link-search-result" style="display: none;"></ul>
                </div>

                <div class="input-wrap link-report-color-field" style="display:none;">
                    <input type="text" class="color-field" id="link-report-color" value="#e74c3c">
                </div>

                <button type="button" class="button-primary" id="fetch-link-report"><?php _e( 'Fetch Report' , 'thirstyaffiliates' ); ?></button>
            </div>

            <?php do_action( 'ta_stats_reporting_chart_sidebar' ); ?>

        </div>

        <div class="report-chart-placeholder"></div>

        <?php do_action( 'ta_stats_reporting_after_chart_placeholder' ); ?>

    </div>
    <div class="overlay"></div>
</div>

<script type="text/javascript">
    var report_data = { 'click_counts' :[] },
        report_details = {
            label       : '<?php echo _e( 'General' , 'thirstyaffiliates' ); ?>',
            label       : '<?php echo _e( 'All links' , 'thirstyaffiliates' ); ?>',
            timeformat  : '<?php echo ( $range[ 'type' ] == 'year' ) ? '%b' : '%d %b'; ?>',
            minTickSize : [ 1 , "<?php echo ( $range[ 'type' ] == 'year' ) ? 'month' : 'day'; ?>" ],
            clicksLabel : '<?php _e( 'Clicks: ' , 'thirstyaffiliates' ); ?>',
            totalClicks : ''
        },
        main_chart;
</script>

<?php do_action( 'ta_after_link_performace_report' , $range ); ?>
