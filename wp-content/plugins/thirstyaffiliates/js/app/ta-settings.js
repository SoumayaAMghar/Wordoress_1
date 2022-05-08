jQuery( document ).ready( function($){

    // Functions
    thirstySettings = {

        /**
         * Event function that toggles if custom link prefix option is to be shown or not.
         *
         * @since 3.0.0
         */
        customLinkPrefix : function() {

            $settingsBlock.on( 'change' , '#ta_link_prefix' , function() {

                var linkPrefix           = $(this).val(),
                    $customLinkPrefixRow = $settingsBlock.find( '.ta_link_prefix_custom-row' );

                if ( linkPrefix == 'custom' ) {

                    $customLinkPrefixRow.show();
                    $customLinkPrefixRow.find( '#ta_link_prefix_custom' ).prop( 'disabled' , false );

                } else {

                    $customLinkPrefixRow.hide();
                    $customLinkPrefixRow.find( '#ta_link_prefix_custom' ).prop( 'disabled' , true );

                }

            } );

            $settingsBlock.find( '#ta_link_prefix' ).trigger( 'change' );

        },

        /**
         * Event function that checks on submit if the selected link prefix is valid or not.
         *
         * @since 3.0.0
         */
        validLinkPrefix : function() {

            if ( $settingsBlock.find( '#ta_link_prefix' ).length > 0 ) {

                $settingsBlock.on( 'click' , '#submit' , function() {

                    var linkPrefix           = $settingsBlock.find( '#ta_link_prefix' ).val(),
                        $customLinkPrefixRow = $settingsBlock.find( '.ta_link_prefix_custom-row' );

                    if ( linkPrefix == 'custom' && $.trim( $customLinkPrefixRow.find( '#ta_link_prefix_custom' ).val() ) === '' ) {

                            alert( ta_settings_var.i18n_custom_link_prefix_valid_val );

                            $( 'html, body' ).animate( {
                                scrollTop : $customLinkPrefixRow.find( '#ta_link_prefix_custom' ).offset().top - 50
                            } , 500 );

                            $customLinkPrefixRow.find( '#ta_link_prefix_custom' ).focus();

                            return false;

                    }

                } );

            }

        },

        /**
         * Event function that show/hides the category select field for toggle options that have the 'category' option.
         *
         * @since 3.2.0
         */
        toggleCat : function() {

            $settingsBlock.on( 'change' , 'select.toggle-cat' , function() {

                var $cat_select     = $settingsBlock.find( 'select.toggle-cat-' + $(this).prop( 'id' ) ),
                    $cat_select_row = $cat_select.closest( 'tr' ),
                    option_value    = $(this).val();

                if ( $cat_select.length < 1 || $cat_select_row.length < 1 )
                    return;

                if ( option_value == 'category' ) {

                    $cat_select.prop( 'disabled' , false );
                    $cat_select.prop( 'required' , true );
                    $cat_select.selectize({
                        plugins   : [ 'remove_button' , 'drag_drop' ]
                    });
                    $cat_select_row.show();

                } else {

                    $cat_select.prop( 'disabled' , true );

                    if ( $cat_select[0].selectize ) {
                        $cat_select[0].selectize.destroy();
                    }

                    $cat_select.prop( 'required' , false );
                    $cat_select_row.hide();
                }

            } );

            $settingsBlock.find( 'select.toggle-cat' ).trigger( 'change' );
        },

        /**
         * Initialize block bots settings as a selectized textarea.
         *
         * @since 3.3.2
         */
        blockBotsSettings : function() {

            $settingsBlock.find( '#ta_blocked_bots' ).selectize({
                plugins   : [ 'restore_on_backspace' , 'remove_button' , 'drag_drop' ],
                delimeter : ',',
                persist   : false,
                create    : function(input) {
                    return {
                        value : input,
                        text  : input
                    }
                }
            });
        }
    };

    var $settingsBlock = $( '.ta-settings.wrap' );

    // initialize custom link prefix settings display
    thirstySettings.customLinkPrefix();
    thirstySettings.validLinkPrefix();
    thirstySettings.toggleCat();
    thirstySettings.blockBotsSettings();

});
