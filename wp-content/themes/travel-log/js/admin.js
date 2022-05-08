/**
 * Admin JS
 *
 * @package Travel_Log
 *
 */

jQuery(function($) {

    jQuery(function() {
        jQuery("#tabs").tabs();
    });

    $( '#recommended-actions' ).click( function(e) {

        e.preventDefault();
    
        $( "#tabs" ).tabs( "option", "active", 1 );

    });

});