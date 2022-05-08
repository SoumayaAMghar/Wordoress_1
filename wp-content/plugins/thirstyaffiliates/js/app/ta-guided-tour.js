(function(){
    window.TA = window.TA ||{ Admin: {} };
}());

(function($){

    function Tour() {

        if ( !ta_guided_tour_params.screen.elem )
            return;
        this.initPointer();

    }

    Tour.prototype.initPointer = function(){

        var self = this;
        self.$elem = $( ta_guided_tour_params.screen.elem ).pointer({
            content: ta_guided_tour_params.screen.html,
            width: 1000,
            position: {
                align: ta_guided_tour_params.screen.align,
                edge: ta_guided_tour_params.screen.edge,
            },
            buttons: function( event, t ){
                return self.createButtons( t );
            },
        }).pointer( 'open' );

        var pointer_width = ( typeof ta_guided_tour_params.screen.width !== 'undefined' ) ?  ta_guided_tour_params.screen.width : 320,
            $wp_pointer   = $( '.wp-pointer' );

        $wp_pointer.css( 'width' , pointer_width );

        // adjust the arrow pointer on the settings screen
        if ( ta_guided_tour_params.screenid == 'thirstylink_page_thirsty-settings' ) {

            var tab_width    = self.$elem.width() + 20,
                panel_width  = $wp_pointer.width(),
                arrow_offset;

            if ( ta_guided_tour_params.screen.align == 'left' && ta_guided_tour_params.screen.edge == 'top' )
                arrow_offset = ( tab_width / 2 ) - 13;
            else if ( ta_guided_tour_params.screen.align == 'right' && ta_guided_tour_params.screen.edge == 'top' )
                arrow_offset = panel_width - ( tab_width / 2 ) - 13;

            if ( arrow_offset )
                $wp_pointer.find( '.wp-pointer-arrow' ).css( 'left' , arrow_offset );

        } else if ( ta_guided_tour_params.screenid === 'post' )
            $wp_pointer.css( 'display' , 'none' );
    };

    Tour.prototype.createButtons = function( t ) {

        this.$buttons = $( '<div></div>', {
            'class': 'ta-tour-buttons'
        });

        if ( ta_guided_tour_params.screen.btn_tour_done )
            this.createTourCompleteButton( t );

        this.createCloseButton( t );
        this.createPrevButton( t );
        this.createNextButton( t );

        return this.$buttons;

    };

    Tour.prototype.createCloseButton = function( t ) {

        var $btnClose = $( '<button></button>', {
            'class': 'button button-large',
            'type': 'button'
        }).html( ta_guided_tour_params.texts.btn_close_tour );

        $btnClose.click(function() {

            var data = {
                action : ta_guided_tour_params.actions.close_tour,
                nonce  : ta_guided_tour_params.nonces.close_tour,
            };

            $.post( ta_guided_tour_params.urls.ajax, data, function( response ) {

                if ( response.status == 'success' )
                    t.element.pointer( 'close' );

            } , 'json' );

        });

        this.$buttons.append($btnClose);

    };

    Tour.prototype.createPrevButton = function( t ) {

        if ( !ta_guided_tour_params.screen.prev )
            return;

        var $btnPrev = $( '<button></button>' , {
            'class': 'button button-large',
            'type': 'button'
        } ).html( ta_guided_tour_params.texts.btn_prev_tour );

        $btnPrev.click( function(){
            window.location.href = ta_guided_tour_params.screen.prev;
        });

        this.$buttons.append($btnPrev);

    };

    Tour.prototype.createNextButton = function( t ) {

        if ( !ta_guided_tour_params.screen.next )
            return;

        // Check if this is the first screen of the tour.
        var text = ( !ta_guided_tour_params.screen.prev ) ? ta_guided_tour_params.texts.btn_start_tour : ta_guided_tour_params.texts.btn_next_tour;

        // Check if this is the last screen of the tour.
        text = ( ta_guided_tour_params.screen.btn_tour_done ) ? ta_guided_tour_params.screen.btn_tour_done : text;

        var $btnStart = $( '<button></button>', {
            'class' : 'button button-large button-primary',
            'type'  : 'button'
        }).html( text );

        $btnStart.click( function() {
            window.location.href = ta_guided_tour_params.screen.next;
        } );

        this.$buttons.append( $btnStart );

    };

    Tour.prototype.createTourCompleteButton = function( t ) {

        var $btnTourComplete = $( '<button></button>', {
            'class': 'button button-large button-primary',
            'type': 'button'
        }).html( ta_guided_tour_params.screen.btn_tour_done );

        $btnTourComplete.click(function() {

            var data = {
                action : ta_guided_tour_params.actions.close_tour,
                nonce  : ta_guided_tour_params.nonces.close_tour,
            };

            // open link to TA Pro on new tab
            window.open( ta_guided_tour_params.screen.btn_tour_done_url );

            $.post( ta_guided_tour_params.urls.ajax, data, function( response ) {

                if ( response.status == 'success' )
                    t.element.pointer( 'close' );

            } , 'json' );

        });

        this.$buttons.append( $btnTourComplete );

    };

    TA.Admin.Tour = Tour;

    // DOM ready
    $( function() {
        new TA.Admin.Tour();
    });

    if ( ta_guided_tour_params.screenid === 'post' ) {

        $( 'body' ).on( 'ta_reinit_tour_pointer' , function() {

            Tour.prototype.initPointer();

            setTimeout(function(){

                var $ta_button  = ThirstyLinkPicker.editorinit ? $( '.ta-add-link-button' ) : $( '#qt_content_thirstyaffiliates_aff_link' ),
                    offset_top  = $ta_button.offset().top + 28,
                    offset_left = ThirstyLinkPicker.editorinit ? $ta_button.offset().left - 48 : $ta_button.offset().left - 23;

                $( '.wp-pointer' ).css( {
                    top     : offset_top,
                    left    : offset_left,
                    display : 'block'
                } );

            }, 500 );
        } );

        $( '.wp-editor-tabs' ).on( 'click' , 'button' , function() {

            ThirstyLinkPicker.editorinit = $(this).hasClass( 'switch-tmce' ) ? true : false;
            setTimeout(function(){ $( 'body' ).trigger( 'ta_reinit_tour_pointer' ); } , 500 );
        } );

        setTimeout( function() {
            if ( $( '#wp-content-wrap' ).hasClass( 'html-active' ) )
                $( '.wp-editor-tabs .switch-html' ).trigger( 'click' ); 
        } , 500 );
    }

}(jQuery));
