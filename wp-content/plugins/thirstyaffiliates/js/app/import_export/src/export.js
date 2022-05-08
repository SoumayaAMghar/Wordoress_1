/* global import_export_var */

import $ from "jquery";
import Clipboard from "clipboard";
import vex from "vex-js";

export default function export_init() {

    let clipboard = new Clipboard( "#copy-settings-string" );

    clipboard.on( 'success' , function( e ) {
        
        vex.dialog.alert( import_export_var.settings_string_copied );

    } );

    clipboard.on( 'error' , function( e ) {
        
        vex.dialog.alert( import_export_var.failed_copy_settings_string );

    } );

}