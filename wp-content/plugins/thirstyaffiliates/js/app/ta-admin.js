jQuery(document).ready(function($) {
  // Flyout Menu Elements.
  var $flyoutMenu    = $( '#caseproof-flyout' );

  if ( $flyoutMenu.length === 0 ) {
    return;
  }

  var $head   = $flyoutMenu.find( '.caseproof-flyout-head' ),
    $sullie = $head.find( 'img' ),
    menu    = {
      state: 'inactive',
      srcInactive: $sullie.attr( 'src' ),
      srcActive: $sullie.data( 'active' ),
    };

  // Click on the menu head icon.
  $head.on( 'click', function( e ) {

    e.preventDefault();

    if ( menu.state === 'active' ) {
      $flyoutMenu.removeClass( 'opened' );
      $sullie.attr( 'src', menu.srcInactive );
      menu.state = 'inactive';
    } else {
      $flyoutMenu.addClass( 'opened' );
      $sullie.attr( 'src', menu.srcActive );
      menu.state = 'active';
    }
  } );

  // Page elements and other values.
  var $wpfooter = $( '#wpfooter' );

  if ( $wpfooter.length === 0 ) {
    return;
  }

  var $overlap       = $( '#caseproof-overview, #caseproof-entries-list, #caseproof-tools.caseproof-tools-tab-action-scheduler' ),
    wpfooterTop    = $wpfooter.offset().top,
    wpfooterBottom = wpfooterTop + $wpfooter.height(),
    overlapBottom  = $overlap.length > 0 ? $overlap.offset().top + $overlap.height() + 85 : 0;

  // Hide menu if scrolled down to the bottom of the page.
  $( window ).on( 'resize scroll', function( e ) {

    var viewTop = $( window ).scrollTop(),
      viewBottom = viewTop + $( window ).height();

    if ( wpfooterBottom <= viewBottom && wpfooterTop >= viewTop && overlapBottom > viewBottom ) {
      $flyoutMenu.addClass( 'out' );
    } else {
      $flyoutMenu.removeClass( 'out' );
    }
  } );

  // $( window ).trigger( 'scroll' );
});