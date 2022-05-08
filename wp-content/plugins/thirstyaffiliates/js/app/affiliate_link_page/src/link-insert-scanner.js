/* global ajaxurl, ta_affiliate_link_page_params */
import $ from "jquery";

/**
 * Initialize link insert scanner event script.
 *
 * @since 3.2.0
 */
export default function link_insert_scanner() {

    const $link_insert_scanner_div = $( "#ta-inserted-link-scanner-metabox" );

    // EVENT: trigger scan of where link is inserted (ajax).
    $link_insert_scanner_div.on( "click" , "#inserted-link-scan-trigger" , function() {

        let $button  = $( this ),
            $inside  = $link_insert_scanner_div.find( ".inside" ),
            $tbody   = $inside.find( ".inserted-into-table table tbody" ),
            $overlay = $inside.find( ".overlay" ),
            $scanned = $inside.find( ".scanned-inserted-status .last-scanned" ),
            link_id  = $( "input#post_ID" ).val();

        // disable button.
        $button.prop( "disabled" , true );

        // show overlay.
        $overlay.css( "height" , $inside.height() ).show();

        $.post( ajaxurl , {
            action  : "ta_link_inserted_scanner",
            _ajax_nonce: ta_affiliate_link_page_params.link_inserted_scanner_nonce,
            link_id : link_id
        }, function( data ) {

            if ( data.status == "success" ) {

                $tbody.html( data.results_markup );
                $scanned.html( data.last_scanned );

            } else {

                // TODO: Make this as vex dialog
                alert( data.error_msg );
                console.log( data );
            }

            $button.prop( "disabled" , false );
            $overlay.hide();

        } , "json" );
    } );
}
