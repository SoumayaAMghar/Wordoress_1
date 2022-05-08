/* global wp , ajaxurl , tb_remove, ta_affiliate_link_page_params */
import $ from "jquery";

/**
 * Initialize affiliate link attachment controls.
 *
 * @since 3.0.0
 * @since 3.2.0 Add methods to show/hide images on media uploader when removed/added on attachments list.
 */
export default function affiliate_link_attachment_init() {

    let $attach_images_metabox = $( "#ta-attach-images-metabox" ),
        $thirsty_image_holder  = $attach_images_metabox.find( "#thirsty_image_holder" );

    // Add attachments to affiliate link
    $attach_images_metabox.on( "click" , "#ta_upload_media_manager" , function( event ) {

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( mediaFrame ) {

            mediaFrame.open();
            return;

        }

        // Create the media frame.
        let mediaFrame = wp.media( {
            title    : $( this ).data( "uploader-title" ),        // Set the title of the modal.
            library  : { type : "image" },                        // Tell the modal to show only images.
            button   : {                                          // Customize the submit button.
                text  : $( this ).data( "uploader-button-text" ), // Set the text of the button.
                close : true                                      // Tell the button not to close the modal, since we're going to refresh the page when the image is selected.
            },
            multiple : true                                       // 2.4.7: Allow selection of multiple images
        } );

        // When an image is selected, run a callback.
        mediaFrame.on( "select" , function() {

            // Grab the selected attachment.
            let selection           = mediaFrame.state().get( "selection" ),
                affiliate_link_id   = $( "input[name=post_ID]" ).val(),
                attachment_ids      = [],
                $image_place_holder = $( "#thirsty_image_holder" );

            if ( $attach_images_metabox.find( "#thirsty_image_holder" ).length <= 0 )
                $attach_images_metabox.find( ".inside" ).append( "<div id='thirsty_image_holder'></div>" );

            selection.map( function( attachment ) {

                // Attach this image to the affiliate link
                attachment = attachment.toJSON();
                attachment_ids.push( attachment.id );

            } );

            if ( attachment_ids.length > 0 ) {

                $.ajax( {
                    url      : ajaxurl,
                    type     : "POST",
                    data     : {
                        action : "ta_add_attachments_to_affiliate_link",
                        _ajax_nonce: ta_affiliate_link_page_params.add_attachments_nonce,
                        attachment_ids : attachment_ids,
                        affiliate_link_id : affiliate_link_id
                    },
                    dataType : "json"
                } )
                .done( function( data ) {

                    if ( data.status === "success"  ) {

                        let attachments = $thirsty_image_holder.data( "attachments" ),
                            imgs;

                        if ( typeof attachments == "object" ) {

                            imgs = $thirsty_image_holder.data( "attachments" ).map( function( value ) {
                                return parseInt( value , 10 );
                            } );

                            imgs = $.unique( imgs.concat( attachment_ids ) );
                            $thirsty_image_holder.data( "attachments" , imgs );
                        }

                        $attach_images_metabox.find( "#thirsty_image_holder" ).append( data.added_attachments_markup );
                        $thirsty_image_holder.trigger( "ta_center_images" );

                    } else {

                        // TODO: Make this as vex dialog
                        alert( data.error_msg );
                        console.log( data );

                    }

                } )
                .fail( function( jqxhr ) {

                    // TODO: Make this as vex dialog
                    alert( jqxhr );
                    console.log( "Failed to add attachments to affiliate link" ); // TODO: Internationalize

                } )
                .always( function() {

                    if ( $image_place_holder.find( ".thirsty-attached-image" ).length > 0 )
                        $image_place_holder.show();

                    tb_remove();

                } );

            }

        } );

        mediaFrame.open();

    } );

    // Remove attachment from affiliate link
    $attach_images_metabox.on( "click" , ".thirsty-remove-img" , function() {

        let $this = $( this );

        if ( $this.hasClass( "removing" ) )
            return false;

        $this.addClass( "removing" );

        let attachment_id       = parseInt( $this.attr( "id" ) ),
            affiliate_link_id   = $( "input[name=post_ID]" ).val(),
            $image_wrap         = $this.closest( ".thirsty-attached-image" ),
            $image_place_holder = $( "#thirsty_image_holder" ),
            $external_image;

        if ( $this.hasClass( "remove-external" ) ) {
            $external_image = $this.closest( ".external-image" ).find( "img" );
            attachment_id  = $external_image[0].getAttribute( "src" );
        }

        $.ajax( {
            url      : ajaxurl,
            type     : "POST",
            data     : {
                action : "ta_remove_attachment_to_affiliate_link",
                _ajax_nonce: ta_affiliate_link_page_params.remove_attachments_nonce,
                attachment_id : attachment_id,
                affiliate_link_id : affiliate_link_id
            },
            dataType : "json"
        } )
        .done( function( data ) {

            if ( data.status === "success" ) {

                let attachments = $thirsty_image_holder.data( "attachments" ),
                    imgs;

                if ( typeof attachments == "object" ) {

                    imgs = $thirsty_image_holder.data( "attachments" ).map( function( value ) {
                        return parseInt( value , 10 );
                    } );

                    imgs = new Set( imgs );
                    imgs.delete( attachment_id );
                    $thirsty_image_holder.data( "attachments" , Array.from( imgs ) );
                }

                $image_wrap.fadeOut( 300 ).delay( 300 ).remove();

                if ( $image_place_holder.find( ".thirsty-attached-image" ).length <= 0 )
                    $image_place_holder.hide();

            } else {

                $this.removeClass( "removing" );

                // TODO: Make vex dialog
                alert( data.error_msg );
                console.log( data );

            }

        } )
        .fail( function( jqxhr ) {

            $this.removeClass( "removing" );

            // TODO: Make vex dialog
            alert( "Failed to remove attachment from affiliate link" ); // TODO: Internationalize
            console.log( jqxhr );

        } );

    } );

    // on reload of media uploader images list (from browser cache) hide images that are already on selected attachments.
    let reload_media_uploader_images_list = function() {

        let $media_list = $( ".media-modal-content ul.attachments li.attachment" ),
            attachments = $thirsty_image_holder.data( "attachments" ),
            imgs;

        if ( typeof attachments != "object" )
            return;

        imgs = attachments.map( function( value ) {
            return parseInt( value , 10 );
        } );

        $media_list.each( function() {

            let $el   = $(this),
                imgid = $(this).data( "id" );

            $el.show();

            if ( $.inArray( imgid , imgs ) > -1 )
                $el.hide();
        } );
    };

    $attach_images_metabox.on( "click" , "#ta_upload_media_manager" , reload_media_uploader_images_list );
    $( "body" ).on( "click" , ".media-modal-content .media-menu-item" , reload_media_uploader_images_list );

    // On per image load (fresh load of media uploader) hide image if already added on attachments.
    $( document ).on( "DOMNodeInserted" , function(e) {

        let $el  = $( e.target ),
            imgs = $thirsty_image_holder.data( "attachments" ),
            imgid;

        if ( ! $el.hasClass( "attachment" ) || typeof imgs != "object" )
            return;

        imgid = $el.data( "id" );
        imgs  = imgs.map( function( value ) {
            return parseInt( value , 10 );
        } );

        if ( $.inArray( imgid , imgs ) > -1 )
            $el.remove();
    } );

    /**
     * Show add external image form.
     */
    $attach_images_metabox.on( "click" , "#add-external-image" , function() {
        $(this).hide();
        $attach_images_metabox.find( ".external-image-form" ).show();
        $attach_images_metabox.find( ".external-image-form input" ).focus();
    } );

    /**
     * Implement add external image form.
     */
    $attach_images_metabox.on( "click" , ".external-image-form button.add-external" , function() {

        const $input = $attach_images_metabox.find( ".external-image-form input" ),
            data     = {
                action  : "ta_insert_external_image",
                _ajax_nonce : ta_affiliate_link_page_params.insert_external_image_nonce,
                url     : $input.val(),
                link_id : $( "input[name=post_ID]" ).val()
            };

        $.post( ajaxurl , data , ( data ) => {

            if ( data.status == "success" ) {

                if ( $attach_images_metabox.find( "#thirsty_image_holder" ).length <= 0 )
                    $attach_images_metabox.find( ".inside" ).append( "<div id='thirsty_image_holder'></div>" );

                $attach_images_metabox.find( "#thirsty_image_holder" ).append( data.markup ).show();
                $thirsty_image_holder.trigger( "ta_center_images" );

            } else {

                // TODO: Make this as vex dialog
                alert( data.error_msg );
                console.log( data );

            }

            $attach_images_metabox.find( ".external-image-form input" ).val( "" );
            $attach_images_metabox.find( "#add-external-image" ).show();
            $attach_images_metabox.find( ".external-image-form" ).hide();

        } , "json" );
    } );

    /**
     * Implement cancel add external image form.
     */
    $attach_images_metabox.on( "click" , ".external-image-form button.cancel" , function() {

        $attach_images_metabox.find( ".external-image-form input" ).val( "" );
        $attach_images_metabox.find( "#add-external-image" ).show();
        $attach_images_metabox.find( ".external-image-form" ).hide();
    } );

    /**
     * Force all attached images to be centered.
     */
    $attach_images_metabox.on( "ta_center_images" , "#thirsty_image_holder" , function() {

        const $images = $(this).find( ".thirsty-img img" );
        let $image, x, marginLeft;

        setTimeout( () => {
            for ( x = 0; x <= $images.length; x++ ) {

                $image = $( $images[x] );

                if ( ! $image.width() ) continue;

                marginLeft = ( $image.width() - 100 ) / 2;
                $image.css( "margin-left" , -marginLeft );
            }
        } , 500 );

    } );
    $thirsty_image_holder.trigger( "ta_center_images" );
}
