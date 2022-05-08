/* global jQuery parent ta_advance_link_picker_js_params */

const $ = jQuery;
const { editor,
        linkNode,
        isGutenberg,
        inputInstance,
        get_html_editor_selection,
        replace_html_editor_selected_text,
        close_thickbox,
        replace_shortcodes
    } = parent.ThirstyLinkPicker;

let $advancedLinkPicker, $resultList , gbNode;

/**
 * Initialize insert normal affiliate link controls.
 *
 * @since 3.0.0
 */
export default function insert_affiliate_link_normal() {

    $advancedLinkPicker = $( "#advanced_add_affiliate_link" );
    $resultList         = $advancedLinkPicker.find( ".results-panel ul.results-list" );

    $resultList.on( "click" , ".actions .insert-link-button" , insertAffiliateLink );
    $resultList.on( "click" , ".actions .insert-shortcode-button" , insertAffiliateLink );
    $resultList.on( "click" , ".images-block .images img" , insertAffiliateLink );

    // toggle images block
    $resultList.on( "click" , ".actions .insert-image-button" , toggle_images_block );
}

/**
 * Insert affiliate link callback.
 *
 * @since 3.5
 */
function insertAffiliateLink() {

    const $this      = $(this),
        $resultRow   = $this.closest( "li.thirstylink" ),
        type         = $this.data( "type" ),
        html_editor  = $resultList.data( "htmleditor" ),
        linkData     = getLinkData( $resultRow , html_editor );

    gbNode = isGutenberg ? $advancedLinkPicker.data( "linkNode" ) : null;

    if ( linkNode || gbNode || html_editor ) {

        if ( ! /^(?:[a-z]+:|#|\?|\.|\/)/.test( linkData.href ) )
            return;

        switch ( type ) {

        case "shortcode" :
            insert_as_shortcode( linkData );
            break;

        case "image" :
            insert_as_image( $this , linkData );
            break;

        case "normal" :
        default :
            insert_as_link( linkData );
            break;

        }

    }
}

/**
 * Insert affiliate link as shortcode.
 *
 * @since 3.5
 *
 * @param {object} linkData Desctructured object.
 */
function insert_as_shortcode({ html_editor , linkText , linkID , content }) {

    if ( isGutenberg ) content = gbNode.textContent.trim() ? gbNode.textContent : content;

    const shortcode = `[thirstylink ids="${ linkID }"]${ linkText.trim() ? linkText : content }[/thirstylink]`;

    if ( html_editor )
        replace_html_editor_selected_text( shortcode );
    else {

        editor.execCommand( "Unlink" , false , false );

        if ( isGutenberg ) {

            let $tempNode = editor.$( "span.temp-ta-node" );
            $tempNode.replaceWith( replace_shortcodes( shortcode ) );

            editor.selection.collapse();

        } else {

            editor.selection.setContent( shortcode );
            inputInstance.reset();
        }

    }

    close_thickbox();
}

/**
 * Insert affiliate link as image.
 *
 * @since 3.5
 *
 * @param {*} $el
 * @param {*} param1
 */
function insert_as_image( $el , { html_editor , className , classHtml , titleHtml , href , rel , target, other_atts_string } ) {

    if ( className != "" )
        classHtml = classHtml.replace( "thirstylink" , "thirstylinkimg" );

    const imgID = $el.data( "imgid" );

    $.post( parent.ajaxurl, {
        action  : "ta_get_image_markup_by_id",
        _ajax_nonce : ta_advance_link_picker_js_params.get_image_markup_nonce,
        imgid   : imgID,
    }, ( response ) => {

        if ( response.status == "success" ) {

            const linkHtml = `<a ${ classHtml + titleHtml } href="${ href }" rel="${ rel }" target="${ target }" ${ other_atts_string }>${ response.image_markup }</a>`;

            if ( html_editor )
                replace_html_editor_selected_text( linkHtml );
            else {

                close_thickbox();

                if ( isGutenberg ) {

                    let $tempNode = editor.$( "span.temp-ta-node" );
                    $tempNode.replaceWith( $tempNode.html() + linkHtml );

                    editor.selection.collapse();

                } else {

                    editor.execCommand( "mceInsertContent" , false , "" );
                    editor.execCommand( "mceInsertContent" , false , linkHtml );
                    inputInstance.reset();
                }

            }
        }

        close_thickbox();

    } , "json" );

    if ( ! html_editor ) editor.selection.collapse();
}

/**
 * Insert affiliate link as link.
 *
 * @since 3.5
 *
 * @param {*} param0
 */
function insert_as_link({ html_editor , linkText , content , className , classHtml , title , titleHtml , href , rel , target , other_atts , other_atts_string }) {

    if ( isGutenberg )
        content = gbNode.textContent.trim() ? gbNode.textContent : content;
    else
        content = linkText.trim() ? linkText : content;

    const linkHtml = `<a ${ classHtml + titleHtml } href="${ href }" rel="${ rel }" target="${ target }" ${ other_atts_string }>${ content }</a>`;


    if ( html_editor )
        replace_html_editor_selected_text( linkHtml );
    else {

        const link_attributes = {
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

        if ( isGutenberg ) {

            let $tempNode = editor.$( "span.temp-ta-node" );
            $tempNode.replaceWith( linkHtml );

            let $tempLink = editor.$( "a.temp-ta-link" );
            editor.selection.select( $tempLink[0] );
            $tempLink.removeClass( "temp-ta-link" );

            editor.selection.collapse();

        } else {

            editor.execCommand( "mceInsertLink" , false , link_attributes );
            if ( ! linkText.trim() )
                editor.selection.setContent( content );
        }

        inputInstance.reset();
    }

    close_thickbox();
}


/**
 * Get link data.
 *
 * @since 3.5
 *
 * @param {jQuery object} $resultRow
 */
function getLinkData( $resultRow , html_editor ) {

    const other_atts = $resultRow.data( "other-atts" ),
        title        = $resultRow.data( "title" );

    let className         = $resultRow.data( "class" ),
        other_atts_string = "";

    if ( isGutenberg ) className += " temp-ta-link";

    if ( typeof other_atts == "object" && Object.keys( other_atts ).length > 0 ) {
        for ( var x in other_atts )
            other_atts_string += x + "=\"" + other_atts[ x ] + "\" ";
    }

    return {
        html_editor       : html_editor,
        linkText          : ( html_editor ) ? get_html_editor_selection().text : editor.selection.getContent(),
        linkID            : parseInt( $resultRow.data( "linkid" ) ),
        className         : className,
        classHtml         : className ? ` class="${ className }"` : "",
        href              : $resultRow.data( "href" ),
        title             : title,
        titleHtml         : title ? ` title="${ title }"` : "",
        content           : $resultRow.find( "span.name" ).text(),
        rel               : $resultRow.data( "rel" ),
        target            : $resultRow.data( "target" ),
        other_atts        : other_atts,
        other_atts_string : other_atts_string,
    };
}

/**
 * Toggle images block.
 *
 * @since 3.5
 */
function toggle_images_block() {

    let $currentRow  = $(this).closest( ".thirstylink" ),
        $imagesBlock = $currentRow.find( ".images-block" );

    var isShown = $currentRow.hasClass( "show" );

    // hide all shown images block
    $( ".results-panel" ).find( ".images-block" ).removeClass( "show" ).hide();

    if ( ! isShown )
        $imagesBlock.slideDown( "fast" ).addClass( "show" ).trigger( "ta_center_images" );
}
