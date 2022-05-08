<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package Travel_Log
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses travel_log_header_style()
 */
function travel_log_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'travel_log_custom_header_args', array(
		'default-image'          => '',
		'default-text-color'     => '000000',
		'width'                  => 1000,
		'height'                 => 250,
		'flex-height'            => true,
		'wp-head-callback'       => 'travel_log_header_style',
	) ) );
}
add_action( 'after_setup_theme', 'travel_log_custom_header_setup' );

if ( ! function_exists( 'travel_log_header_style' ) ) :
	/**
	 * Styles the header image and text displayed on the blog.
	 *
	 * @see travel_log_custom_header_setup().
	 */
	function travel_log_header_style() {

		$header_text_color = get_header_textcolor();

			/*
			* If no custom options for text are set, let's bail.
			* get_header_textcolor() options: Any hex value, 'blank' to hide text. Default: add_theme_support( 'custom-header' ).
			*/
		if ( get_theme_support( 'custom-header', 'default-text-color' ) === $header_text_color ) {
			return;
		}

		if ( ! display_header_text() ) :
				$custom_css = '.site-title,
				.site-description {
				position: absolute;
				clip: rect(1px, 1px, 1px, 1px);
				}';
			else :
				$custom_css = '.site-title a,
				.site-description {
				color:#' . esc_attr( $header_text_color ) . '
				}';
		endif;
			wp_add_inline_style( 'travel-log-custom-colors-style', $custom_css );
	}
	add_action( 'wp_enqueue_scripts', 'travel_log_header_style', 30 );

endif;


/**
 * Add color styling from theme
 */
function travel_log_custom_color_options() {
	wp_enqueue_style(
		'travel-log-custom-colors-style',
		get_template_directory_uri() . '/css/custom-colors.css'
	);
		// Color Values.
		$link_color              = travel_log_get_theme_option( 'travel_log_link_color' );
		$link_hover_color        = travel_log_get_theme_option( 'travel_log_link_hover_color' );
		$button_color            = travel_log_get_theme_option( 'travel_log_button_color' );
		$button_hover_color      = travel_log_get_theme_option( 'travel_log_button_hover_color' );
		$button_text_color       = travel_log_get_theme_option( 'travel_log_button_text_color' );
		$button_text_hover_color = travel_log_get_theme_option( 'travel_log_button_text_hover_color' );
		$footer_bg_color         = travel_log_get_theme_option( 'travel_log_footer_bg_color' );

		$customizer_colors_css = '#highlight-tag .theme-btn,
		.travel-testimonial .client-content .client-bio .name-id:before,
		#return-to-top,
		#simple-menu i,
		#section-itinerary-search div.wp-travel-search p.wp-travel-search input,
		#section-itinerary-search div.wp-travel-search p.wp-travel-search select,
		#section-itinerary-search div.wp-travel-search p select[type=button], 
		#section-itinerary-search div.wp-travel-search p select[type=reset], 
		#section-itinerary-search div.wp-travel-search p select[type=submit], 
		button, 
		input[type=button], 
		input[type=reset], 
		input[type=submit],
		.wp-travel-offer span,
		.post-navigation .nav-links .nav-next a, .post-navigation .nav-links .nav-previous a, 
		.posts-navigation .nav-links .nav-next a, .posts-navigation .nav-links .nav-previous a,
		#highlight-tag .highlight-wrapper .highlight-book,
		.fountainG,
		.widget.widget_calendar tbody a,
		#wp-travel-booking .wp-travel-radio-group input[type="radio"]:checked:before{
			background-color: ' . esc_attr( $button_color ) . ';
			color: ' . esc_attr( $button_text_color ) . ';
		}
		
		.main-navigation ul li.menu-btn a:hover,
		#highlight-tag .theme-btn:hover,
		#return-to-top:hover,
		.posts-navigation .nav-links .nav-previous a:hover, 
		.posts-navigation .nav-links .nav-next a:hover, 
		.post-navigation .nav-links .nav-previous a:hover, 
		.post-navigation .nav-links .nav-next a:hover,
		#simple-menu i:hover,
		#section-itinerary-search div.wp-travel-search p.wp-travel-search:hover input, 
		#section-itinerary-search div.wp-travel-search p.wp-travel-search:hover select,
		#section-itinerary-search div.wp-travel-search p select[type=button]:hover, 
		#section-itinerary-search div.wp-travel-search p select[type=reset]:hover, 
		#section-itinerary-search div.wp-travel-search p select[type=submit]:hover, 
		button:hover, 
		input[type=button]:hover, 
		input[type=reset]:hover, 
		input[type=submit]:hover,
		#highlight-tag .highlight-wrapper .highlight-book:hover,
		.widget.widget_calendar tbody a:hover{
			background-color: ' . esc_attr( $button_hover_color ) . ';
			color: ' . esc_attr( $button_text_hover_color ) . ';
		}
		
		.featured-slider .slick-prev:before, .featured-slider .slick-next:before {
			background: ' . esc_attr( $button_color ) . ';
			color: ' . esc_attr( $button_text_color ) . ';
		}

		.featured-slider .slick-prev:hover::before, 
		.featured-slider .slick-next:hover::before {
		    background: ' . esc_attr( $button_hover_color ) . ';
		    color: ' . esc_attr( $button_text_hover_color ) . ';
		}

		.bar{
			background: ' . esc_attr( $button_hover_color ) . ';
		}

		.featured-slider .travel-banner.slick-slider .slick-dots li button:before,
		.featured-slider .travel-banner.slick-slider .slick-dots li.slick-active button:before{
			color:' . esc_attr( $button_color ) . ';
		}

		@keyframes bounce_fountainG{
		  0%{
		  transform:scale(1);
		    background-color:' . esc_attr( $button_color ) . ';
		  }
		  100%{
		  transform:scale(.3);
		    background-color:' . esc_attr( $button_hover_color ) . ';
		  }
		}
		@-o-keyframes bounce_fountainG{
		  0%{
		  -o-transform:scale(1);
		    background-color:' . esc_attr( $button_color ) . ';
		  }
		  100%{
		  -o-transform:scale(.3);
		    background-color:' . esc_attr( $button_hover_color ) . ';
		  }
		}
		@-ms-keyframes bounce_fountainG{
		  0%{
		  -ms-transform:scale(1);
		    background-color:' . esc_attr( $button_color ) . ';
		  }
		  100%{
		  -ms-transform:scale(.3);
		    background-color:' . esc_attr( $button_hover_color ) . ';
		  }
		}
		@-webkit-keyframes bounce_fountainG{
		  0%{
		  -webkit-transform:scale(1);
		    background-color:' . esc_attr( $button_color ) . ';
		  }
		  100%{
		  -webkit-transform:scale(.3);
		    background-color:' . esc_attr( $button_hover_color ) . ';
		  }
		}
		@-moz-keyframes bounce_fountainG{
		  0%{
		  -moz-transform:scale(1);
		    background-color:' . esc_attr( $button_color ) . ';
		  }
		  100%{
		  -moz-transform:scale(.3);
		    background-color:' . esc_attr( $button_hover_color ) . ';
		  }
		}

	
		.main-navigation li:not(.menu-btn) > a:hover, 
		.main-navigation li.current-menu-item > a, 
		.main-navigation li.current_page_item > a, 
		.main-navigation li:not(.menu-btn):hover > a, 
		.main-navigation li.current_page_ancestor > a, 
		.main-navigation li.current-page-parent > a,
		.main-navigation licurrent-menu-ancestor > a,
		.status-publish .entry-content .theme-read-more, 
		.status-public .entry-content .theme-read-more,
		#filters li span.active,
		.wp-travel-toolbar .wp-travel-view-mode-lists li.active-mode i,
		.wp-travel-navigation.wp-paging-navigation a.current, 
		.wp-travel-navigation.wp-paging-navigation a:hover,
		.status-public .entry-content a, .status-publish .entry-content a,
		.wp-travel-tab-wrapper .tab-list.resp-tabs-list li.resp-tab-active,
		.widget_wp_travel_filter_search_widget #amount,
		.ui-slider .ui-widget-header,
		.ui-state-default, .ui-widget-content .ui-state-default, 
		.ui-widget-header .ui-state-default, 
		.ui-button, 
		html .ui-button.ui-state-disabled:hover, 
		html .ui-button.ui-state-disabled:active,
		.wp-travel-itinerary-items ul.wp-travel-itinerary-list .wp-travel-post-wrap-bg .recent-post-bottom-meta .trip-price ins,
		.wp-travel-trip-time i,
		.entry-meta .travel-info i,
		footer#footer .copy-right-footer .travel-copyright a,
		.checkout-page-wrap .checkout-block h3,
		.comments-area ol .reply a,
		.comments-area ol .edit-link a,
		.widget table tbody a  {
			color: ' . esc_attr( $link_color ) . ';
		}
		
		
		#filters li span:hover,
		.post-item-wrapper .post-content h4:hover, 
		.post-item-wrapper .post-content .read-more-link:hover,
		.post-item-wrapper:hover .post-content h4,
		.post-item-wrapper .post-content h4:hover, 
		.post-item-wrapper .post-content .read-more-link:hover,
		footer#footer a:hover, 
		footer#footer a:visited:hover,
		.travel-blog .featured-blog .blog-latest-post .post-item-wrapper .post-content h4:hover, 
		.travel-blog .featured-blog .blog-latest-post .post-item-wrapper .post-content a:hover,
		.travel-blog .featured-blog .blog-latest-post .post-item-wrapper .post-content h4:hover a,
		.top-header ul a:hover,
		.widget.widget_archive ul li:before, 
		.widget.widget_categories ul li:before, 
		.widget.widget_meta ul li:before, 
		.widget.widget_nav_menu ul li:before, 
		.widget.widget_pages ul li:before, 
		.widget.widget_recent_comments ul li:before, 
		.widget.widget_recent_entries ul li:before, 
		.widget.widget_rss ul li:before, 
		.widget.widget_text ul li:before,
		.status-publish .entry-content a:hover, 
		.status-public .entry-content a:hover,
		.share-handle .btn-floating:hover,
		#breadcrumb .trail-items li a:hover,
		.sidr ul li a:hover,
		.wp-travel.trip-headline-wrapper .wp-travel-booking-enquiry:hover,
		footer#footer .copy-right-footer .travel-copyright a:hover,
		.entry-meta a:hover,
		.comments-area ol .edit-link a,
		.comments-area ol .reply a,
		a:hover, a:focus, a:active,
		.widget table tbody a:hover,
		#header-search #search-form .close:hover{
			color: ' . esc_attr( $link_hover_color ) . ';
		}

		.ui-state-default, .ui-widget-content .ui-state-default, 
		.ui-widget-header .ui-state-default, 
		.ui-button, html .ui-button.ui-state-disabled:hover,
		html .ui-button.ui-state-disabled:active,
		.ui-slider .ui-widget-header{
			background-color: ' . esc_attr( $link_color ) . ';
		}

		input, select{
			outline-color:' . esc_attr( $button_hover_color ) . ';
		}
		
		
		footer#footer,
		.wp-travel-navigation.wp-paging-navigation a.current, 
		.wp-travel-navigation.wp-paging-navigation a:hover,
		.ui-state-default, .ui-widget-content .ui-state-default, 
		.ui-widget-header .ui-state-default, .ui-button, 
		html .ui-button.ui-state-disabled:hover, 
		html .ui-button.ui-state-disabled:active{
			border-color:' . esc_attr( $link_color ) . ';
		}


		#simple-menu i,
		#section-itinerary-search div.wp-travel-search p.wp-travel-search input, 
		#section-itinerary-search div.wp-travel-search p.wp-travel-search select{
			border-color:' . esc_attr( $button_color ) . ';
		}

		#simple-menu i:hover,
		#section-itinerary-search div.wp-travel-search p.wp-travel-search:hover input{
			border-color:' . esc_attr( $button_hover_color ) . ';
		}
		
		.widget .widget-title{
			border-left-color: ' . esc_attr( $link_color ) . ';
		}

		.wp-travel-navigation.wp-paging-navigation a:hover,
		.wp-travel-default-article .wp-travel-explore a:hover,
		ul.availabily-list .availabily-content .btn:hover,
		#faq .wp-collapse-open a:hover{
			border-color:' . esc_attr( $link_hover_color ) . ';
		}
		
		.posts-navigation .nav-links .nav-previous a:before,
		.post-navigation .nav-links .nav-previous a:before{
			border-bottom-color: ' . esc_attr( $button_color ) . ';
		}

		.posts-navigation .nav-links .nav-next a:after,
		.post-navigation .nav-links .nav-next a:after,
		.wp-travel-offer span:before{
			border-right-color: ' . esc_attr( $button_color ) . ';
		}

		.wp-travel-offer span:before{
			border-top-color:' . esc_attr( $button_color ) . ';
		}
		.posts-navigation .nav-links .nav-next a:hover::after,
		.post-navigation .nav-links .nav-next a:hover::after{
			    border-right-color: ' . esc_attr( $button_hover_color ) . ';
		}
		

		.main-navigation ul li.menu-btn a{
			background-color: ' . esc_attr( $button_color ) . ';
			color:' . esc_attr( $button_text_color ) . ';
		}
		.main-navigation ul ul a:hover{
			background-color: ' . esc_attr( $link_hover_color ) . ';
		}
		
		.main-navigation ul li ul li:hover a:hover{
			color:#fff;
		}

		.posts-navigation .nav-links .nav-previous a:hover::before,
		.post-navigation .nav-links .nav-previous a:hover::before{
			    border-bottom-color: ' . esc_attr( $button_hover_color ) . ';
		}
		footer#footer .travel-site-bottom-footer{
			background: ' . esc_attr( $footer_bg_color  ) . ';
		}


		';
		wp_add_inline_style( 'travel-log-custom-colors-style', $customizer_colors_css );
}
add_action( 'wp_enqueue_scripts', 'travel_log_custom_color_options' );
