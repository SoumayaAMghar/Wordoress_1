<?php
/**
 * Theme widget area functions.
 *
 * @package Travel_Log
 */

/**
 * Register widget area.
 */
function travel_log_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'travel-log' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'travel-log' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Front Page Widget Area', 'travel-log' ),
		'id'            => 'front-page-before-footer',
		'description'   => esc_html__( 'Add widgets here.', 'travel-log' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	$args = array(
		'name'          => esc_html__( 'Footer Widget %s', 'travel-log' ),
		'id'            => 'footer-sidebar',
		'description'   => esc_html__( 'Add widgets here.', 'travel-log' ),
		'before_widget' => '<div class="footer-active"><div class="wrap-col"><section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section></div></div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);
	register_sidebars( 4, $args );
}
add_action( 'widgets_init', 'travel_log_widgets_init' );
