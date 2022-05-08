import $ from "jquery";
import vex from "vex-js";

// Initialize vex
vex.registerPlugin( require( "vex-dialog" ) );
vex.defaultOptions.className = "vex-theme-plain";

import import_init from "./import";
import export_init from "./export";

import "./assets/styles/index.scss";

$( document ).ready( function() {

    import_init();
    export_init();

} );