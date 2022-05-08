/* global jQuery parent */

/**
 * Initialize JS app.
 * 
 * @since 3.4.0
 */
export default function edit_shortcode_init() {

    const edit_shortcode = document.querySelector( ".edit-shortcode-wrap" );

    jQuery( edit_shortcode ).on( "init_shortcode_data" , init_shortcode_data );
    jQuery( edit_shortcode ).on( "click" , "#submit" , process_edit_shortcode );


    jQuery( edit_shortcode ).trigger( "init_shortcode_data" );
}

/**
 * Initialize shortcode data.
 * 
 * @since 3.4.0
 */
function init_shortcode_data() {

    const form     = document.querySelector( ".edit-shortcode-wrap form" ),
        raw_data   = parent.ThirstyLinkPicker.shortcodeData.data,
        processed  = {
            text   : raw_data.match( /\](.*?)\[\/thirstylink/i ),
            ids    : raw_data.match( /ids=\"(.*?)\"/i ),
            class  : raw_data.match( /class=\"(.*?)\"/i ),
            title  : raw_data.match( /title=\"(.*?)\"/i ),
            rel    : raw_data.match( /rel=\"(.*?)\"/i ),
            target : raw_data.match( /target=\"(.*?)\"/i ),
        };

    for ( let attr in processed ) {
        if ( attr in processed && processed[ attr ] != null )
            jQuery( form ).find( "#shortcode_" + attr ).val( processed[ attr ][1] );
    }
}

/**
 * Process edit shortcode.
 * 
 * @since 3.4.0
 */
function process_edit_shortcode( e ) {

    e.preventDefault();

    const form  = document.querySelector( ".edit-shortcode-wrap form" ),
        node    = parent.ThirstyLinkPicker.shortcodeData.node;

    if ( ! form.checkValidity() )
        return;
    
    let data = {
        text   : jQuery( form ).find( "#shortcode_text" ).val(),
        ids    : jQuery( form ).find( "#shortcode_ids" ).val(),
        class  : jQuery( form ).find( "#shortcode_class" ).val(),
        title  : jQuery( form ).find( "#shortcode_title" ).val(),
        rel    : jQuery( form ).find( "#shortcode_rel" ).val(),
        target : jQuery( form ).find( "#shortcode_target" ).val(),
    };
    let shortcode = "[thirstylink ids=\"" + data.ids + "\"";

    for ( let attr in data ) {

        if ( attr == "text" || attr == "ids" ) continue;

        if ( attr in data && data[ attr ] != null && data[ attr ] )
            shortcode += " " + attr + "=\"" + data[ attr ] + "\"";
    }
    
    // text and end.
    shortcode += "]" + data.text + "[/thirstylink]";

    // replace content in editor to newly formed shortcode
    parent.ThirstyLinkPicker.editor.$( node )
        .replaceWith( "<span class=\"ta-editor-shortcode\" data-shortcode=\"" + window.encodeURIComponent( shortcode ) + "\">" + data.text + "</span>" );

    parent.ThirstyLinkPicker.shortcodeData = null;
    parent.ThirstyLinkPicker.close_thickbox();
}