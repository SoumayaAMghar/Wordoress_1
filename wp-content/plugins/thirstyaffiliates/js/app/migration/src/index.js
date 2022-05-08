/* global migration_var , ajaxurl */

import $ from "jquery";
import vex from "vex-js";

// Initialize vex
vex.registerPlugin( require( "vex-dialog" ) );
vex.defaultOptions.className = "vex-theme-plain";

import "./assets/styles/index.scss";

$( document ).ready( function() {

    $( "#ta_migrate_old_data" ).on( "click" , function() {

        let $this = $( this );

        vex.dialog.confirm( {
            message  : migration_var.i18n_confirm_migration ,
            callback : function( value ) {

                if ( value ) {

                    $this.attr( "disabled" , "disabled" );
                    $this.closest( ".forminp-migration_controls" ).addClass( "-processing" );

                    $.ajax( {
                        url      : ajaxurl,
                        type     : "POST",
                        data     : {
                            action : "ta_migrate_old_plugin_data",
                            _ajax_nonce: migration_var.migration_nonce
                        },
                        dataType : "json"
                    } )
                    .done( function( data , text_status , jqxhr ) {

                        if ( data.status === "success" )
                            vex.dialog.alert( data.success_msg );
                        else {

                            vex.dialog.alert( data.error_msg );
                            console.log( data );

                        }

                    } )
                    .fail( function( jqxhr , text_status , error_thrown ) {

                        vex.dialog.alert( migration_var.i18n_migration_failed );
                        console.log( jqxhr );

                    } )
                    .always( function() {

                        $this.removeAttr( "disabled" );
                        $this.closest( ".forminp-migration_controls" ).removeClass( "-processing" );

                    } );

                }

            }

        } );

    } );

} );
