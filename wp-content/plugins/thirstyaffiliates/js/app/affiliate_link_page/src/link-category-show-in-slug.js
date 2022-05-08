/* global ajaxurl, ta_affiliate_link_page_params */
import $ from "jquery";

/**
 * Initialize link category show in slug controls.
 *
 * @since 3.0.0
 */
export default function link_category_show_in_slug() {

    const $link_category_checklist = $( "#thirstylink-categorychecklist" ),
        $category_show_on_slug     = $( "select[name='ta_category_slug']" );

    // EVENT: append checked (remove unchecked) categories to the category to show on slug selection.
    $link_category_checklist.on( "change" , "input[type='checkbox']" , function() {

        let catID          = $(this).val(),
            catName        = $(this).parent().text(),
            isChecked      = $(this).prop( "checked" );

        update_default_category();

        if ( isChecked )
            append_category_option( catID , catName );
        else {
            $category_show_on_slug.find( "option[value='" + catID + "']" ).remove();
            $category_show_on_slug.trigger( "change" );
        }

    });

    // EVENT: append newly added category to the category to show on slug selection
    $(document).on( "DOMNodeInserted" , "#thirstylink-categorychecklist" , function() {

        let $newCat    = $( "#thirstylink-categorychecklist li" ).first(),
            newCatID   = $newCat.find( "input[type='checkbox']" ).val(),
            newCatName = $newCat.find( "label" ).text(),
            isChecked  = $newCat.find( "input[type='checkbox']" ).prop( "checked" );

        update_default_category();

        if ( isChecked )
            append_category_option( newCatID , newCatName );
        else
            $category_show_on_slug.find( "option[value='" + newCatID + "']" ).remove();
    } );

    // EVENT: update cloaked url input value when category to show on slug selection is changed.
    $(document).on( "change" , "select[name='ta_category_slug']" , function() {

        let select            = $(this),
            new_cat_slug      = select.find( "option:selected" ).data( "slug" ),
            home_link_prefix  = select.data( "home-link-prefix" ),
            post_name         = $( "#ta_slug" ).val(),
            cloaked_url_input = $( "#ta_cloaked_url" );

        if ( $(this).find( "option" ).length <= 1 )
            cloaked_url_input.val( home_link_prefix + post_name + "/" );
        else
            cloaked_url_input.val( home_link_prefix + new_cat_slug + "/" + post_name + "/" );
    } );

    // function to append the the checked category to the category to show on slug selection.
    function append_category_option( cat_id , cat_name ) {

        $.post( ajaxurl , {
            action  : "ta_get_category_slug",
            _ajax_nonce: ta_affiliate_link_page_params.get_category_slug_nonce,
            term_id : cat_id
        }, function( data ) {

            if ( data.status == "success" ) {

                $category_show_on_slug.append( "<option value='" + cat_id + "' data-slug='" + data.category_slug + "'>" + cat_name + "</option>" )
                  .trigger( "change" );
            } else {

                // TODO: Make this as vex dialog
                alert( data.error_msg );
                console.log( data );
            }

        } , "json" );
    }

    // function to update the default category slug, with the first checked in the category list.
    function update_default_category() {

        let all_checked_cats = $link_category_checklist.find( "input[type='checkbox']:checked" ),
            all_labels       = [],
            all_labels_keys  = [],
            first_category,
            first_category_key,
            current_cat_label,
            key;

        if ( all_checked_cats.length < 1 )
            return;

        for ( key = 0; key < all_checked_cats.length; key++ ) {

            current_cat_label = $( all_checked_cats[ key ] ).parent().text().trim();
            all_labels.push( current_cat_label );
            all_labels_keys[ current_cat_label ] = key;
        }

        // sort all labels insensitive of case.
        all_labels = all_labels.sort( function(a, b) {
            a = a.toLowerCase();
            b = b.toLowerCase();
            return a > b ? 1 : ( a < b ? -1 : 0 );
        } );

        first_category_key = all_labels_keys[ all_labels[0] ];
        first_category     = $( all_checked_cats[ first_category_key ] );

        $.post( ajaxurl , {
            action  : "ta_get_category_slug",
            _ajax_nonce: ta_affiliate_link_page_params.get_category_slug_nonce,
            term_id : first_category.val()
        }, function( data ) {

            if ( data.status == "success" ) {

                $category_show_on_slug.find( "option:first-child" ).data( "slug" , data.category_slug )
                  .attr( "data-slug" , data.category_slug );
                $category_show_on_slug.trigger( "change" );
            } else {

                // TODO: Make this as vex dialog
                alert( data.error_msg );
                console.log( data );
            }

        } , "json" );
    }
}
