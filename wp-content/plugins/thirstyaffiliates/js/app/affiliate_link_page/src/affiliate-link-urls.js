import $ from "jquery";

/**
 * Initialize affiliate link url controls.
 *
 * @since 3.0.0
 */
export default function affiliate_link_urls_init() {

    const $thirsty_urls_metabox = $( "#ta-urls-metabox" ),
        $slugdiv = $( "#slugdiv" );

    // remove the default #post_name input field.
    $slugdiv.remove();

    // Edit cloaked url slug
    $thirsty_urls_metabox.on( "click" , "button.edit-ta-slug" , function() {

        $thirsty_urls_metabox.find( ".cloaked-fields" ).hide();
        $thirsty_urls_metabox.find( ".slug-fields" ).fadeIn( 200 );

    } );

    // Save cloaked url slug
    $thirsty_urls_metabox.on( "click" , "button.save-ta-slug" , function() {

        var new_slug  = $thirsty_urls_metabox.find( "input#ta_slug" ).val(),
            old_link  = $thirsty_urls_metabox.find( "input#ta_cloaked_url" ).val(),
            link_base = old_link.replace( /[^\/]+\/?$/g , "" );

        if ( /^([a-z0-9-_% ]+)$/.test( new_slug ) )  {

            new_slug = ( new_slug == "" ? old_link.match( /[^\/]+$/ ) : new_slug );
            $thirsty_urls_metabox.find( "input#ta_cloaked_url" ).val( link_base + new_slug + "/" );

            $thirsty_urls_metabox.find( ".slug-fields" ).hide();
            $thirsty_urls_metabox.find( ".cloaked-fields" ).fadeIn( 200 );

        } else
            $( "input#publish" ).click();
    } );

}
