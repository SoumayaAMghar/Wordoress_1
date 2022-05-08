/* global parent */
import $ from "jquery";

const { editor,
        bookmark,
        linkNode,
        isGutenberg,
        get_html_editor_selection,
        replace_html_editor_selected_text,
        close_thickbox,
        replace_shortcodes
    } = parent.ThirstyLinkPicker;

let $add_affiliate_link_wrap , $add_affiliate_link_form , gbNode;

/**
 * Initialize process add affiliate link controls.
 *
 * @since 3.0.0
 */
export default function process_add_affiliate_link() {

    $add_affiliate_link_wrap = $( "#quick_add_affiliate_link" ),
    $add_affiliate_link_form = $add_affiliate_link_wrap.find( "form" );

    if ( isGutenberg ) {

        add_temporary_node();
        gbNode = $add_affiliate_link_wrap.data( "linkNode" );
    }

    $add_affiliate_link_wrap.on( "click" , "button[type='submit']" , create_affiliate_link );
}

/**
 * Create affiliate link.
 * 
 * @since 3.5
 */
function create_affiliate_link() {

    if ( ! $add_affiliate_link_form[0].checkValidity() )
        return;

    let buttonName  = $( this ).prop( "name" ),
        formData    = $add_affiliate_link_form.serializeArray(),
        html_editor = $add_affiliate_link_wrap.data( "htmleditor" ),
        link_text   = ( html_editor ) ? get_html_editor_selection().text : editor.selection.getContent();

    const props = {
        html_editor : $add_affiliate_link_wrap.data( "htmleditor" ),
        link_text   : ( html_editor ) ? get_html_editor_selection().text : editor.selection.getContent()
    };

    $add_affiliate_link_form.find( ".submit-row .ta_spinner" ).css( "display" , "inline-block" );

    $.post( parent.ajaxurl , formData , ( response ) => {

        if ( response.status == "success" && buttonName == "add_link_and_insert" ) {

            $add_affiliate_link_form[0].reset();
            $add_affiliate_link_form.find( ".submit-row .ta_spinner" ).hide();

            if ( response.link_insertion_type === "shortcode" )
                insert_as_shortcode( response , props );
            else
                insert_as_link( response , props );

            // if inserting link to an image, then collapse selection.
            if ( ! html_editor && link_text.indexOf( "<img" ) > -1 )
                editor.selection.collapse();

        } else if ( response.status != "success" ) {
            // TODO: Handle error.
        }

        close_thickbox();

    } , "json" );
}

/**
 * Insert affiliate link as shortcode.
 * 
 * @since 3.5
 * 
 * @param {*} response Desctructured object.  
 * @param {*} props    Desctructured object.  
 */
function insert_as_shortcode( { content , link_id } , { link_text , html_editor } ) {

    let anchor    = link_text ? link_text : content,
        shortcode = "[thirstylink ids=\"" + link_id + "\"]" + anchor + "[/thirstylink]";

    if ( html_editor )
        replace_html_editor_selected_text( shortcode );
    else {

        if ( isGutenberg ) {

            let $tempNode = editor.$( "span.temp-ta-node" );
            $tempNode.replaceWith( replace_shortcodes( shortcode ) );
            editor.selection.collapse();

        } else {

            editor.execCommand( "Unlink" , false , false );
            editor.selection.setContent( shortcode );
        }
        
    }
}

/**
 * Insert affiliate link as link.
 * 
 * @since 3.5
 * 
 * @param {*} response Desctructured object.  
 * @param {*} props    Desctructured object.  
 */
function insert_as_link( { content , href , rel , className , title , target , other_atts } , { link_text , html_editor } ) {

    let other_atts_string = " ";

    for ( let x in other_atts )
        other_atts_string += x + "=\"" + other_atts[ x ] + "\" ";

    if ( isGutenberg ) {
        className += " temp-ta-link";
        link_text  = gbNode.textContent.trim() ? gbNode.textContent : content;
    } else
        link_text = link_text.trim() ? link_text : content;

    const classHtml = className ? ` class="${ className }"` : "";
    const titleHtml = title ? ` title="${ title }"` : "";
    const linkHtml  = `<a ${ classHtml + titleHtml } href="${ href }" rel="${ rel }" target="${ target }" ${ other_atts_string }>${ link_text }</a>`;

    if ( html_editor )
        replace_html_editor_selected_text( linkHtml );
    else if ( isGutenberg ) {
            
        let $tempNode = editor.$( "span.temp-ta-node" );
        $tempNode.replaceWith( linkHtml );
        
        let $tempLink = editor.$( "a.temp-ta-link" );
        editor.selection.select( $tempLink[0] );
        $tempLink.removeClass( "temp-ta-link" );
        
        editor.selection.collapse();

    } else {

        let link_attributes = {
            class  : className,
            title  : title,
            href   : href,
            rel    : rel,
            target : target,
            "data-wplink-edit" : null,
            "data-thirstylink-edit" : null
        };

        if ( typeof other_atts == "object" && Object.keys( other_atts ).length > 0 ) {

            for ( let x in other_atts )
                link_attributes[ x ] = other_atts[ x ];
        }

        editor.execCommand( "Unlink" , false , false );
        editor.execCommand( "mceInsertLink" , false , link_attributes );

        if ( ! link_text.trim() )
            editor.selection.setContent( content );
    }
}

/**
 * Add temporary node (for Gutenberg editor classic block).
 * 
 * @since 3.5
 */
function add_temporary_node() {

    if ( bookmark ) editor.selection.moveToBookmark( bookmark );

    if ( editor.$( "a.temp-ta-node" ).length < 1 ) {
        editor.execCommand( "mceInsertLink", false, 
            {
                class  : "temp-ta-node",
                href   : "_temp_ta_node",
            }
        );
        $( ".wp-link-preview a[href='_temp_ta_node']" ).closest( ".mce-inline-toolbar-grp" ).hide();
    }

    const $tempLink = editor.$( "a.temp-ta-node" );
    $tempLink.replaceWith( `<span class="temp-ta-node">${ $tempLink.html() }</span>` );
        
    // set linkNode value.
    const $span = editor.$( "span.temp-ta-node" );
    editor.selection.setCursorLocation( $span[0] );
    parent.ThirstyLinkPicker.linkNode = $span[0];
    $add_affiliate_link_wrap.data( "linkNode" , $span[0] );
}