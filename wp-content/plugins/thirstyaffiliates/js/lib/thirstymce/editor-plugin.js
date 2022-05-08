( function( tinymce ) {

    // don't run the file if global variable is not defined.
    if( typeof parent.ta_editor_var != 'object' )
        return;

    // add thirstyaffiliates to the tinymce plugin manager.
    tinymce.PluginManager.add( 'thirstyaffiliates', function( editor , url ) {

        var thirstyToolbar,
            $ = window.jQuery,
            linkNode,
            inputInstance,
            thirstylink_apply_key_command     = navigator.platform.match(/Mac/i) ? '⌘⌥K' : 'Ctrl+Alt+K',
            thirstylink_quick_add_key_command = navigator.platform.match(/Mac/i) ? '⌘⇧K' : 'Ctrl+Shift+K';

        // ThirstyLinkInput input type object.
        ThirstyLinkInputObj = {
            renderHtml: function() {
                return (
                    '<div id="' + this._id + '" class="wp-thirstylink-input">' +
                        '<input type="text" value="" placeholder="' + parent.ta_editor_var.simple_search_placeholder + '" data-aff-content="" data-aff-title="" data-aff-class="" data-aff-rel="" data-aff-target="" data-aff-link-insertion-type="" data-aff-link-id="" />' +
                        '<ul class="affiliate-link-list" style="display: none;"></ul>' +
                    '</div>'
                );
            },
            getURL: function() {
                return tinymce.trim( this.getEl().firstChild.value );
            },
            getData: function( attrib ) {
                return tinymce.trim( this.getEl().firstChild.getAttribute( 'data-aff-' + attrib ) );
            },
            reset: function() {
                var input = this.getEl().firstChild;

                input.value = '';
                input.nextSibling.innerHTML = '';
            }
        };

        // register ThirstyLinkInput input type to tinymce.
        if ( tinymce.ui.Factory )
            tinymce.ui.Factory.add( 'ThirstyLinkInput' , tinymce.ui.Control.extend( ThirstyLinkInputObj ) );
        else
            tinymce.ui.ThirstyLinkInput = tinymce.ui.Control.extend( ThirstyLinkInputObj );

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
            
            editor.$( '.temp-ta-node' ).each( function( i, element ) {
				var $element = editor.$( element );
				$element.replaceWith( $element.html() );
            });
        }

        // when undoing a change, trigger remove placeholders and move cursor to end of content to prevent error (Gutenberg)
        editor.on( 'undo' , function() {
            removePlaceholders();
            if ( ThirstyLinkPicker.isGutenberg ) {
                editor.focus();
                editor.selection.select(editor.getBody() , true );
                editor.selection.collapse( false );
            }
        } );

        // Register custom inline toolbar
        editor.on( 'preinit', function() {

            if ( editor.wp && editor.wp._createToolbar ) {

                thirstyToolbar = editor.wp._createToolbar( [
					'thirstyaffiliates_search_input',
                    'thirstyaffiliates_apply_affiliate_link',
                    'thirstyaffiliates_advance_affiliate_link'
				], true );

                thirstyToolbar.on( 'hide', function() {
					if ( ! thirstyToolbar.scrolling && thirstyToolbar.tempHide != true ) {
						editor.execCommand( 'thirstylink_cancel' );
					}
				} );

                thirstyToolbar.on( 'show', function() {

                    var element = thirstyToolbar.$el.find( 'input' );

                    $( element ).focus();
                });

                ThirstyLinkPicker.editorinit = true;
            }
        });

        // assign event nodes on when the toolbar will need to show up
        editor.on( 'wptoolbar', function( event ) {

            var linkNode = editor.dom.getParent( event.element, 'a' ),
                $linkNode, href, edit;

            if ( linkNode ) {

                $linkNode = editor.$( linkNode );
                href      = $linkNode.attr( 'href' );
                edit      = $linkNode.attr( 'data-thirstylink-edit' );

                if ( href === '_wp_thirstylink_placeholder' || edit ) {

                    event.element = linkNode;
                    event.toolbar = thirstyToolbar;
                }
            }
        });

        /*
        |--------------------------------------------------------------------------
        | TinyMCE Custom Commands
        |--------------------------------------------------------------------------
        */

        // insert affiliate link
        editor.addCommand( 'thirstylink_insert' , function() {

            var node     = editor.selection.getNode();
                linkNode = getSelectedLink();

            thirstyToolbar.tempHide = false;

            if ( linkNode ) {

                // TODO: edit inserted link

            } else {

                var style = parent.ta_editor_var.insertion_type === "shortcode" ? 'box-shadow: none; border: 1px solid #999; text-decoration: none; color: inherit;' : '';

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
                editor.nodeChanged();

            }
        } );

        // cancel insert affiliate link
        editor.addCommand( 'thirstylink_cancel', function() {

			if ( ! thirstyToolbar.tempHide ) {
				inputInstance.reset();
				removePlaceholders();
			}
		} );

        editor.addCommand( 'thirstylink_apply' , function() {

            if ( linkNode ) {

                var href                = inputInstance.getURL(),
                    content             = inputInstance.getData( 'content' ),
                    class_name          = inputInstance.getData( 'class' ),
                    title               = inputInstance.getData( 'title' )
                    rel                 = inputInstance.getData( 'rel' ),
                    target              = inputInstance.getData( 'target' ),
                    link_id             = inputInstance.getData( 'link-id' ),
                    link_insertion_type = inputInstance.getData( 'link-insertion-type' ),
                    other_atts          = JSON.parse( inputInstance.getData( 'other-atts' ) );

                if ( link_insertion_type == 'shortcode' ) {

                    var shortcode_text   = tinymce.trim( linkNode.innerHTML ) ? tinymce.trim( linkNode.innerHTML ) : title,
                        shortcode_markup = "[thirstylink ids=\"" + link_id + "\"]" + shortcode_text + "[/thirstylink]";

                    editor.selection.setContent( shortcode_markup );

                    removePlaceholders();

                } else {

                    if ( ! /^(?:[a-z]+:|#|\?|\.|\/)/.test( href ) )
                        return;

                    var link_attributes = {
                        href   : href,
                        class  : class_name,
                        title  : title,
                        rel    : rel,
                        target : target,
                        'data-wplink-edit': null,
                        'data-thirstylink-edit' : null,
                        'data-mce-bogus' : null,
                    };

                    if ( typeof other_atts == 'object' && Object.keys( other_atts ).length > 0 ) {

                        for ( var x in other_atts )
                            link_attributes[ x ] = other_atts[ x ];
                    }

                    editor.dom.setAttribs( linkNode , link_attributes );

                    if ( ! tinymce.trim( linkNode.innerHTML ) )
                        editor.$( linkNode ).text( content );

                    // if inserting link to an image, then collapse selection.
                    if ( $( linkNode ).prop( 'outerHTML' ).indexOf( '<img' ) > -1 )
                        editor.selection.collapse();
                }

            }

            inputInstance.reset();
			editor.nodeChanged();

        } );

        function gutenbergPromise() {

            return new Promise(function( resolve , reject ) {

                ThirstyLinkPicker.replace_shortcodes = replace_shortcodes;
                ThirstyLinkPicker.bookmark           = editor.selection.getBookmark();
                removePlaceholders();

                editor.selection.moveToBookmark( ThirstyLinkPicker.bookmark );
                editor.execCommand( 'mceInsertLink', false, 
                    {
                        class  : 'temp-ta-node',
                        href   : '_temp_ta_node',
                    }
                );

                jQuery( ".wp-link-preview a[href='_temp_ta_node']" ).closest( '.mce-inline-toolbar-grp' ).hide();

                resolve( true );
            });
        }

        editor.addCommand( 'thirstylink_gutenberg_advance' , function() {

            gutenbergPromise().then(function(v){
                editor.execCommand( 'thirstylink_advance' );
            });
            
        } );

        editor.addCommand( 'thirstylink_advance' , function() {
            
            ThirstyLinkPicker.editor        = editor;
            ThirstyLinkPicker.linkNode      = linkNode;
            ThirstyLinkPicker.inputInstance = inputInstance;

            var post_id = $( '#post_ID' ).val();
            thirstyToolbar.tempHide = true;

            if ( ! ThirstyLinkPicker.isGutenberg )
                editor.execCommand( "Unlink" , false , false );

            tb_show( 'Add Affiliate Link' , window.ajaxurl + '?action=ta_advanced_add_affiliate_link&post_id=' + post_id + '&height=640&width=640&TB_iframe=false' );
            ThirstyLinkPicker.resize_thickbox();

            inputInstance.reset();
            thirstyToolbar.tempHide = false;
        } );

        editor.addCommand( 'thirstylink_gutenberg_quick_add' , function() {

            gutenbergPromise().then(function(v){
                editor.execCommand( 'thirstylink_quick_add' );
            });

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
            image : url + '/img/aff.gif',
            icon  : 'test_icon',
            cmd   : ThirstyLinkPicker.isGutenberg ? 'thirstylink_gutenberg_advance' : 'thirstylink_insert',
            onpostrender: function() {
                this.$el.addClass( 'ta-add-link-button' );
                $('body').trigger('ta_reinit_tour_pointer');
            }
        });

        // quick add affiliate link post
        editor.addButton( 'thirstyaffiliates_quickaddlink_button' , {
            title : 'Quick Add Affiliate Link (' + thirstylink_quick_add_key_command + ')',
            image : url + '/img/aff-new.gif',
            cmd   : ThirstyLinkPicker.isGutenberg ? 'thirstylink_gutenberg_quick_add' : 'thirstylink_quick_add'
        });

        // search affiliate link input
        editor.addButton( 'thirstyaffiliates_search_input' , {
            type  : 'ThirstyLinkInput',
            onPostRender: function() {

                var element     = this.getEl(),
					input       = element.firstChild,
                    resultList  = element.getElementsByTagName( 'ul' ),
                    cache, last;

				inputInstance = this;

                // search affiliate link event
                tinymce.$( input ).on( 'keyup' , function() {

                    var $input = $(this),
                        $resultList = $input.next();

                    // clear results list
                    $resultList.html('').hide();

                    if ( $input.val().length < 3 )
                        return;

                    if ( last === $input.val() ) {

                        $resultList.html( cache ).show();
                        return;
                    }

                    last = $input.val();

                    $.post( window.ajaxurl, {
                        action  : 'search_affiliate_links_query',
                        keyword : $input.val(),
                        post_id : $( '#post_ID' ).val()
                    }, function( response ) {

                        if ( response.status == 'success' ) {

                            cache = response.search_query_markup;
                            $resultList.html( response.search_query_markup ).show();

                        } else {
                            // TODO: Handle error here
                        }

                    } , 'json' );
                } );


            }
        });

        editor.addButton( 'thirstyaffiliates_apply_affiliate_link' , {
            title   : 'Apply Affiliate Link',
            icon    : 'dashicon dashicons-editor-break',
            classes : 'widget btn primary',
            cmd     : 'thirstylink_apply'
        });

        editor.addButton( 'thirstyaffiliates_advance_affiliate_link' , {
            title   : 'Advanced Options',
            icon    : 'dashicon dashicons-admin-generic',
            cmd     : 'thirstylink_advance'
        });

        /*
        |--------------------------------------------------------------------------
        | TinyMCE Custom keyboard shortcuts
        |--------------------------------------------------------------------------
        */

        editor.addShortcut( 'meta+alt+k' , 'Add Affiliate Link' , ThirstyLinkPicker.isGutenberg ? 'thirstylink_gutenberg_advance' : 'thirstylink_insert' );
        editor.addShortcut( 'meta+shift+k' , 'Quick Add Affiliate Link' , ThirstyLinkPicker.isGutenberg ? 'thirstylink_gutenberg_quick_add' : 'thirstylink_quick_add' );

        /*
        |--------------------------------------------------------------------------
        | Shortcode: custom display on editor
        |--------------------------------------------------------------------------
        */

        var shortcodeToolbar,
            shortcodeNode;

        // replace shortcodes function
        function replace_shortcodes( content ) {

            if ( ThirstyLinkPicker.isGutenberg ) return content;

            return content.replace( /\[thirstylink(.*?)\[\/thirstylink\]/g , function( match ) {
                
                var text = match.match( /\](.*?)\[\/thirstylink/i ),
                    data = window.encodeURIComponent( match );

                return '<span class="ta-editor-shortcode" data-shortcode="' + data + '">' + text[1] + '</span>';
            });
        }

        // restore shortcodes function
        function restore_shortcodes( content ) {

            return content.replace( /<span class="ta-editor-shortcode(.*?)<\/span>/g , function( match , el ) {

                var attr_name = new RegExp( 'data-shortcode' + '=\"([^\"]+)\"' ).exec( el ),
                    shortcode = attr_name ? window.decodeURIComponent( attr_name[1] ) : '';

                return shortcode ? shortcode : match;
            } );
        }

        // event before wp editor sets content
        editor.on( 'BeforeSetContent', function( e ) {
            e.content = replace_shortcodes( e.content );
        });

        // event on wp editor processes post (change to text tab, save post, etc.)
        editor.on( 'PostProcess', function( event ) {
            if ( event.get ) {
                event.content = restore_shortcodes( event.content );
            }
        });

        // shortcode is clicked event.
        editor.on( 'mouseup', function( event ) {
            
            var dom  = editor.dom,
                node = event.target,
                x;

            // if shortcode is not selected, then we remove the selected class to all shortcodes.
            if ( node.nodeName !== 'SPAN' || ! node.classList.contains( 'ta-editor-shortcode' ) || ! dom.getAttrib( node , 'data-shortcode' ) ) {

                var shortcodes = dom.$( "span.ta-editor-shortcode" );
                
                if ( shortcodes.length >= 1 ) {
                    for ( x = 0; x < shortcodes.length; x++ )
                        shortcodes[ x ].classList.remove( "shortcode-selected" );
                }            
                
                return;
            }
                
            // mark shortcode as selected
            node.classList.add( "shortcode-selected" );
            shortcodeNode = node;
        } );

        /*
        |--------------------------------------------------------------------------
        | Shortcode toolbar
        |--------------------------------------------------------------------------
        */

        // define taShortcodePreview input type object.
        taShortcodePreview = {
            renderHtml: function() {
                return ( '<div id="' + this._id + '" class="shortcode-preview"></div>' );
            }
        };

        // register taShortcodePreview input type to tinymce.
        if ( tinymce.ui.Factory )
            tinymce.ui.Factory.add( 'taShortcodePreview' , tinymce.ui.Control.extend( taShortcodePreview ) );
        else
            tinymce.ui.taShortcodePreview = tinymce.ui.Control.extend( taShortcodePreview );

        // Register custom inline toolbar
        editor.on( 'preinit', function() {

            if ( editor.wp && editor.wp._createToolbar ) {

                shortcodeToolbar = editor.wp._createToolbar( [
                    'taShortcodePreview',
                    'ta_edit_shortcode_btn',
                    'ta_remove_shortcode_btn'
                ], true );
                
                shortcodeToolbar.on( 'show', function() {

                    var $preview      = shortcodeToolbar.$el.find( 'div.shortcode-preview' ),
                        shortcode_txt = window.decodeURIComponent( editor.$( shortcodeNode ).attr( 'data-shortcode' ) );

                    $preview.closest( '.mce-container-body' ).addClass( 'shortcode-preview-toolbar' );

                    if ( shortcode_txt.length > 70 )
                        shortcode_txt = shortcode_txt.substring( 0 , 70 ) + '…';

                    $preview.text( shortcode_txt );
                });
            }
        });

        // assign event nodes on when the toolbar will need to show up
        editor.on( 'wptoolbar', function( event ) {

            var tempnode = editor.dom.getParent( event.element, 'span.ta-editor-shortcode' ),
                $shortcode, data, edit;

            if ( tempnode ) {

                $shortcode = editor.$( tempnode );
                data      = $shortcode.attr( 'data-shortcode' );
                edit      = $shortcode.hasClass( 'shortcode-selected' );

                if ( data && edit ) {

                    event.element = tempnode;
                    event.toolbar = shortcodeToolbar;
                }
            }
        });

        // register shortcode toolbar buttons
        editor.addButton( 'ta_edit_shortcode_btn' , {
            title   : 'Edit affiliate link shortcode',
            icon    : 'dashicon dashicons-edit',
            cmd     : 'ta_edit_shortcode'
        });
        editor.addButton( 'ta_remove_shortcode_btn' , {
            title   : 'Remove affiliate link shortcode',
            icon    : 'dashicon dashicons-editor-unlink',
            cmd     : 'ta_remove_shortcode'
        });

        // register remove shortcode command
        editor.addCommand( 'ta_remove_shortcode' , function() {
            var text = editor.$( shortcodeNode ).text();
            $( shortcodeNode ).replaceWith( text );
        });

        editor.addCommand( 'ta_edit_shortcode' , function() {

            var post_id = $( '#post_ID' ).val();

            ThirstyLinkPicker.editor        = editor;
            ThirstyLinkPicker.shortcodeData = {
                node : shortcodeNode,
                data : window.decodeURIComponent( editor.$( shortcodeNode ).attr( 'data-shortcode' ) )
            };

            tb_show( 'Edit Affiliate Link Shortcode' , window.ajaxurl + '?action=ta_edit_affiliate_link_shortcode&post_id=' + post_id + '&height=640&width=640&TB_iframe=false' );
            ThirstyLinkPicker.resize_thickbox();

        } );

    });

} )( window.tinymce );
