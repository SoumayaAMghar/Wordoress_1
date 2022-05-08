/* global jQuery , parent , Options */

const $ = jQuery;
const { editor , isGutenberg , bookmark } = parent.ThirstyLinkPicker;

let $advancedLinkPicker,
    $searchPanel,
    $resultPanel,
    $resultList,
    searchAjaxPromise,
    firstSearch = true,
    paged = 2,
    initialResultCache,
    searchCache,
    lastSearched,
    lastCategory;

/**
 * Initialize affiliate link picker search controls.
 *
 * @since 3.0.0
 * @since 3.4.0 Add support for category into the search query.
 */
export default function advanced_link_picker_search() {

    $advancedLinkPicker = $( "#advanced_add_affiliate_link" ),
    $searchPanel        = $advancedLinkPicker.find( ".search-panel" ),
    $resultPanel        = $advancedLinkPicker.find( ".results-panel" ),
    $resultList         = $resultPanel.find( "ul.results-list" ),
    searchAjaxPromise;

    // create temporary node for Gutenberg editor.
    if ( isGutenberg ) add_temporary_node();

    $searchPanel.on( "keyup thirstysearch" , "#thirstylink-search" , search_affiliate_links );
    $resultPanel.on( "click" , ".load-more-results" , load_more_results );
    $resultList.on( "ta_center_images" , ".images-block" , center_images );

}

/**
 * Search affiliate links.
 * 
 * @since 3.5
 */
function search_affiliate_links() {

    const $input   = $(this),
        $category  = $searchPanel.find( "#thirstylink-category" ),
        $load_more = $resultPanel.find( ".load-more-results" );
    
    let cat_value = $category.val();
    cat_value = cat_value == "all" ? "" : cat_value;

    if ( firstSearch && $input.val().length < 3 && ! cat_value )
        return;

    if ( cat_value && $input.val() && $input.val().length < 3 )
        return;

    if ( searchAjaxPromise && lastSearched !== $input.val() && lastCategory !== cat_value ) {
        searchAjaxPromise.abort();
        searchAjaxPromise = null;
    }

    // save initial results to a cache variable
    if ( ! initialResultCache ) initialResultCache = $resultList.html();

    // clear results list
    $resultList.html(`
        <li class="spinner">
            <i style="background-image: url(${ Options.spinner_image });"></i>
            <span>${ Options.searching_text }</span>
        </li>
    `);

    if ( ( $input.val() == "" || $input.val().length < 3 ) && ! firstSearch && ! cat_value ) {

        paged = 2;
        $resultList.html( initialResultCache ).show();
        $load_more.show();
        return;
    }

    if ( lastSearched === $input.val() && lastCategory === cat_value ) {

        paged = 2;
        $resultList.html( searchCache ).show();
        $load_more.show();
        return;
    }

    paged = 1;
    $load_more.hide();

    searchAjaxPromise = $.post(
        parent.ajaxurl,
        {
            action   : "search_affiliate_links_query",
            keyword  : $input.val(),
            paged    : paged,
            advance  : true,
            category : cat_value,
            post_id  : Options.post_id
        },
        ( response ) => {

            lastSearched = $input.val();
            lastCategory =  cat_value;
            firstSearch  = false;

            if ( response.status == "success" ) {

                searchCache = response.search_query_markup;
                $resultList.html( response.search_query_markup ).show();

                paged++;

                if ( response.count < 1 )
                    $load_more.hide();
                else
                    $load_more.show();

            } else {
                // TODO: Handle error here
            }

        },
        "json"
    );
}

/**
 * Load more search results.
 * 
 * @since 3.5
 */
function load_more_results() {

    const $load_more = $(this),
        $input       = $searchPanel.find( "#thirstylink-search" ),
        $category    = $searchPanel.find( "#thirstylink-category" );
        
    let cat_value = $category.val();
    cat_value = cat_value == "all" ? "" : cat_value;

    if ( $load_more.hasClass( "fetching" ) )
        return;

    if ( ! paged || paged < 2 ) paged = 2;

    $load_more.addClass( "fetching" ).css( "padding-top" , "4px" ).find( ".spinner" ).show();
    $load_more.find( ".button-text" ).hide();

    searchAjaxPromise = $.post( 
        parent.ajaxurl,
        {
            action   : "search_affiliate_links_query",
            keyword  : $input.val(),
            paged    : paged,
            category : cat_value,
            advance  : true
        }, 
        ( response ) => {

            $load_more.removeClass( "fetching" ).find( ".spinner" ).hide();
            $load_more.find( ".button-text" ).show();

            if ( response.status == "success" ) {

                paged++;

                if ( response.count < 1 ) {
                    $load_more.hide();
                    return;
                }

                $resultList.append( response.search_query_markup );

            } else {
                // TODO: Handle error here
            }

        },
        "json"
    );
}

/**
 * Force all listed images to be centered.
 * 
 * @since 3.5
 */
function center_images() {

    const $images = $(this).find( ".images img" );
    let $image, x, marginLeft;

    for ( x = 0; x <= $images.length; x++ ) {

        $image = $( $images[x] );

        if ( ! $image.width() ) continue;

        marginLeft = ( $image.width() - 75 ) / 2;
        $image.css( "margin-left" , -marginLeft );
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
    $( "#advanced_add_affiliate_link" ).data( "linkNode" , $span[0] );
}