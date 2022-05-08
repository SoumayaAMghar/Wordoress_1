/**
 * Custom Js for Layout image select in customizer
 *
 * @package Travel_Log
 */
jQuery(document).ready(function($) {
    $('#input_travel_log_optionstravel_log_layout label img').click(function() {
        $('#input_travel_log_optionstravel_log_layout label').each(function() {
            $(this).find('img').removeClass('travel-log-radio-img-selected');
        });
        $(this).addClass('travel-log-radio-img-selected');
    });
});