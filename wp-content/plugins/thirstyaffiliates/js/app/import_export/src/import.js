/* global ajaxurl , import_export_var */

import $ from "jquery";
import vex from "vex-js";

export default function import_init() {

    let import_button  = $( "#import-setting-button" ),
        import_textbox = $( "#ta_import_settings" ),
        spinner        = import_button.siblings( ".spinner" );

    import_button.on( "click" , function() {

        import_button.attr( "disabled" , "disabled" );
        spinner.css( "visibility" , "visible" );

        let settings_string = $.trim( import_textbox.val() );

        if ( settings_string === "" ) {

            vex.dialog.alert( import_export_var.please_input_settings_string );
            import_button.removeAttr( "disabled" );
            spinner.css( "visibility" , "hidden" );

        } else {

            $.ajax( {
                url      : ajaxurl,
                type     : "POST",
                data     : {
                    action: "ta_import_settings",
                    _ajax_nonce: import_export_var.import_settings_nonce,
                    ta_settings_string: settings_string
                },
                dataType : "json"
            } )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status === "success" ) {

                    vex.dialog.alert( data.success_msg );
                    import_textbox.val( "" );

                } else {

                    vex.dialog.alert( data.error_msg );
                    console.log( data );

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                vex.dialog.alert( jqxhr );
                console.log( jqxhr );

            } )
            .always( function() {

                import_button.removeAttr( "disabled" );
                spinner.css( "visibility" , "hidden" );

            } );

        }

    } );

};
