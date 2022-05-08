// Register mce buttons on the widget WP editor.
jQuery( document ).on( 'wp-before-tinymce-init' , function( event , init ) {
    init.toolbar1 += ',thirstyaffiliates_button,thirstyaffiliates_quickaddlink_button';
} );

jQuery( document ).on( 'tinymce-editor-setup', function( event, editor ) {

    // don't run the file if global variable is not defined.
    if( typeof parent.ta_editor_var != 'object' )
        return;

    var $ = window.jQuery,
        linkNode,
        inputInstance = {
            reset : function() {}
        },
        thirstylink_apply_key_command     = navigator.platform.match(/Mac/i) ? '⌘⌥K' : 'Ctrl+Alt+K',
        thirstylink_quick_add_key_command = navigator.platform.match(/Mac/i) ? '⌘⇧K' : 'Ctrl+Shift+K';

    // get the selected link
    function getSelectedLink() {
        var href, html,
            node = editor.selection.getNode(),
            link = editor.dom.getParent( node, 'a[href]' );

        if ( ! link ) {
            html = editor.selection.getContent({ format: 'raw' });

            if ( html && html.indexOf( '</a>' ) !== -1 ) {
                href = html.match( /href="([^">]+)"/ );

                if ( href && href[1] ) {
                    link = editor.$( 'a[href="' + href[1] + '"]', node )[0];
                }

                if ( link ) {
                    editor.selection.select( link );
                }
            }
        }

        return link;
    }

    // remove affiliate link placeholder
    function removePlaceholders() {
        editor.$( 'a' ).each( function( i, element ) {
            var $element = editor.$( element );

            if ( $element.attr( 'href' ) === '_wp_thirstylink_placeholder' ) {
                editor.dom.remove( element, true );
            } else if ( $element.attr( 'data-thirstylink-edit' ) ) {
                $element.attr( 'data-thirstylink-edit', null );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | TinyMCE Custom Commands
    |--------------------------------------------------------------------------
    */

    editor.addCommand( 'thirstylink_advance' , function() {

        var post_id = $( '#post_ID' ).val(),
            style = parent.ta_editor_var.insertion_type === "shortcode" ? 'box-shadow: none; border: 1px solid #999; text-decoration: none; color: inherit;' : '';

        removePlaceholders();
        editor.execCommand( 'mceInsertLink', false, {
            class  : 'thirstylink',
            title  : '_title_placeholder',
            style  : style,
            href   : '_wp_thirstylink_placeholder',
            rel    : '_rel_placeholder',
            target : '_target_placeholder' }
        );

        linkNode = editor.$( 'a[href="_wp_thirstylink_placeholder"]' )[0];

        ThirstyLinkPicker.editor        = editor;
        ThirstyLinkPicker.linkNode      = linkNode;
        ThirstyLinkPicker.inputInstance = inputInstance;

        editor.execCommand( "Unlink" , false , false );

        tb_show( 'Add Affiliate Link' , window.ajaxurl + '?action=ta_advanced_add_affiliate_link&post_id=' + post_id + '&height=640&width=640&TB_iframe=false' );
        ThirstyLinkPicker.resize_thickbox();

        inputInstance.reset();
    } );

    editor.addCommand( 'thirstylink_quick_add' , function() {

        var selection = editor.selection.getContent(),
            post_id   = $( '#post_ID' ).val();

        ThirstyLinkPicker.editor = editor;

        tb_show( 'Quick Add Affiliate Link' , window.ajaxurl + '?action=ta_quick_add_affiliate_link_thickbox&post_id=' + post_id + '&height=500&width=500&selection=' + selection + '&TB_iframe=false' );
        ThirstyLinkPicker.resize_thickbox();
    } );

    /*
    |--------------------------------------------------------------------------
    | TinyMCE Custom Buttons
    |--------------------------------------------------------------------------
    */

    // add affiliate link button
    editor.addButton( 'thirstyaffiliates_button' , {
        title : 'Add Affiliate Link (' + thirstylink_apply_key_command + ')',
        image : ta_widget_editor_url + '/img/aff.gif',
        icon  : 'test_icon',
        cmd   : 'thirstylink_advance'
    });

    // quick add affiliate link post
    editor.addButton( 'thirstyaffiliates_quickaddlink_button' , {
        title : 'Quick Add Affiliate Link (' + thirstylink_quick_add_key_command + ')',
        image : ta_widget_editor_url + '/img/aff-new.gif',
        cmd   : 'thirstylink_quick_add'
    });

    /*
    |--------------------------------------------------------------------------
    | TinyMCE Custom keyboard shortcuts
    |--------------------------------------------------------------------------
    */

    editor.addShortcut( 'meta+alt+k' , 'Add Affiliate Link' , 'thirstylink_advance' );
    editor.addShortcut( 'meta+shift+k' , 'Quick Add Affiliate Link' , 'thirstylink_quick_add' );

});
