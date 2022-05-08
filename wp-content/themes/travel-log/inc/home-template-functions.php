<?php
/**
 * Home template functions.
 *
 * @package Travel_Log
 */

if ( ! function_exists( 'travel_log_front_page_slider_content' ) ) :

	/**
	 * [travel_log_front_page_slider_content Generater Front Slider Contents]
	 *
	 * @since 1.0.0
	 */
	function travel_log_front_page_slider_content() {

		$defaults = travel_log_default_values();

		// $enabled = travel_log_get_theme_option( 'home_slider_enable' );

		// if ( false === $enabled ) {
		// 	return;
		// }

		$slider_category = travel_log_get_theme_option( 'home_slider_category' );

		$slider_content_type = travel_log_get_theme_option( 'slider_content_type' );

		if ( class_exists( 'WP_Travel' ) ) :

			if ( 'trip-types' === $slider_content_type  ) :

				$slider_category = travel_log_get_theme_option( 'home_slider_category_trip_type' );

			elseif ( 'trip-location' === $slider_content_type  ) :

				$slider_category = travel_log_get_theme_option( 'home_slider_category_trip_location' );

			endif;

			endif;

		$read_more_button_text = travel_log_get_theme_option( 'home_slider_read_more_text' );

		if ( class_exists( 'WP_Travel' ) ) {

			if ( 'trip-types' === $slider_content_type  ) {

				if ( empty( $slider_category ) ) {

					// get all terms in the taxonomy
					$terms = get_terms( 'itinerary_types' );
					// convert array of term objects to array of term IDs
					$slider_category = wp_list_pluck( $terms, 'term_id' );

				}

				$args = array(
				'post_type' => travel_log_wp_travel_support_get_post_type(),
				'tax_query' => array(
					array(
						'taxonomy' => 'itinerary_types',
						'field'    => 'id',
						'terms'    => $slider_category,
					),
				),
				);

			} elseif ( 'trip-location' === $slider_content_type  ) {

				if ( empty( $slider_category ) ) {

					// get all terms in the taxonomy
					$terms = get_terms( 'travel_locations' );
					// convert array of term objects to array of term IDs
					$slider_category = wp_list_pluck( $terms, 'term_id' );

				}

				$args = array(
				'post_type' => travel_log_wp_travel_support_get_post_type(),
				'tax_query' => array(
					array(
						'taxonomy' => 'travel_locations',
						'field'    => 'id',
						'terms'    => $slider_category,
					),
				),
				);

			} else {

				$args['category_name'] = $slider_category;

			}
		} else {

				$args['category_name'] = $slider_category;

		}

		$args['posts_per_page'] = apply_filters( 'travel_log_slider_posts_limit', 5 );

		$slider_posts = new WP_Query( $args );

		if ( $slider_posts->have_posts() ) :
		?>
		<div id="featured-slider" class="featured-slider clearfix">
		<div class="travel-banner slider" <?php echo is_rtl() ? 'dir="rtl"' : '' ; ?> >
			<?php
			while ( $slider_posts->have_posts() ) :
				$slider_posts->the_post();
				?>
				<div>
					<div class="slider-image-wrapper">
						<?php
						if ( has_post_thumbnail() ) :
							the_post_thumbnail( apply_filters( 'travel_log_slider_thumbnail_size', 'full' ) );
						else :
							travel_log_no_slider_thumbnail();
						endif;
						?>
							<div class="featured-slider-contents">
								<h1><?php the_title(); ?></h1>
									<p><?php the_excerpt(); ?></p>
									<?php

									if ( class_exists( 'WP_Travel' ) && 'category' !== $slider_content_type ) : ?>

											<i><?php esc_html_e( 'from', 'travel-log' ); ?></i>

											<?php travel_log_itinerary_trip_price( get_the_ID(), true ); ?>

										<?php endif;
									?>
								<div class="slider-buttons">
								<?php if ( ! empty( $read_more_button_text ) ) : ?>
									<a href="<?php the_permalink(); ?>" class="slider-info"><?php echo esc_html( $read_more_button_text ); ?></a>
								<?php endif; ?>
								<?php echo travel_slider_additional_button(); ?>
								</div>
							</div>
					</div>

				</div>

				<?php

				endwhile;
			wp_reset_postdata();
			?>
			</div>
		</div>

	<?php

	endif;

	}
endif;

if ( ! function_exists( 'travel_log_front_page_slider_wrap' ) ) :
	/**
	 * Wrapper for slider section.
	 *
	 * @since 1.0.0
	 */
	function travel_log_front_page_slider_wrap() {

		if ( ! is_front_page() || is_home() ) {
			return;
		}

		$enabled = travel_log_get_theme_option( 'home_slider_enable' );

		if ( ! is_customize_preview() && false === $enabled && ! is_home() ) {

			return;

		}

		echo '<div id="travel-log-front-page-slider-wrap" class="travel-log-show-partial-edit-shortcut">';

		travel_log_front_page_slider_content();

		echo '</div>';
	}

endif;

add_action( 'travel_log_front_page_content', 'travel_log_front_page_slider_wrap' );

if ( ! function_exists( 'travel_log_front_page_search_wrap' ) ) :
	/**
	 * Wrapper for WP Travel Search below slider section.
	 *
	 * @since 1.0.9
	 */
	function travel_log_front_page_itinerary_search_wrap() {

		if ( ! is_front_page() || is_home() ) {
			return;
		}

		$enabled_search = travel_log_get_theme_option( 'home_itinerary_search_enable' );

		if ( ! class_exists( 'WP_Travel' ) ) {
			return;
		}

		if ( true === $enabled_search ) {

			$section_title     = travel_log_get_theme_option( 'itinerary_search_title' );
			$section_sub_title = travel_log_get_theme_option( 'itinerary_search_sub_title' );
		?>
		<div id="travel-log-front-page-itinerary-search-wrap" class="travel-log-show-partial-edit-shortcut">
			<section id="section-itinerary-search" class="section-itinerary-search">
				<div class="container">
				<?php if ( ! empty( $section_title ) || ! empty( $section_sub_title ) ) : ?>
					<div class="row">
						<div class="col-sm-12">
							<h2 class="section-title"><?php echo esc_html( $section_title ); ?></h2>
								<div class="title-tagline">
									<p><?php echo esc_html( $section_sub_title ); ?></p>
								</div>
						</div>
					</div>
				<?php endif; ?>
					<div class="row">
						<div class="col-sm-12">
							<?php travel_log_itinerary_search_form(); ?>
						</div>
					</div>
				</div>
			</section>
		</div>
		<?php
		}
	}

endif;

add_action( 'travel_log_front_page_content', 'travel_log_front_page_itinerary_search_wrap' );

if ( ! function_exists( 'travel_log_front_page_post_filter_content' ) ) :

	/**
	 * Front page post filter section content.
	 *
	 * @since 1.0.0
	 */
	function travel_log_front_page_post_filter_content() {

		// $enabled = travel_log_get_theme_option( 'post_filter_enable' );

		$title = travel_log_get_theme_option( 'post_filter_title' );

		$sub_title = travel_log_get_theme_option( 'post_filter_sub_title' );

		$categories = travel_log_get_theme_option( 'post_filter_category' );

		$content_type = travel_log_get_theme_option( 'post_filter_content_type' );

		if ( class_exists( 'WP_Travel' ) ) :
			if ( 'trip-types' === $content_type  ) :
				$categories = travel_log_get_theme_option( 'post_filter_category_trip_type' );
			elseif ( 'trip-location' === $content_type  ) :
				$categories = travel_log_get_theme_option( 'post_filter_category_trip_location' );
			endif;
		endif;

		// if ( ! $enabled ) {
		// 	return;
		// }
		?>
		<section id="tab-tours" class="tab-tours">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
						<div class="title-tagline">
						<p><?php echo esc_html( $sub_title ); ?></p>
						</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<ul id="filters" class="post-filter-controls">
							<!-- For filtering controls add -->
							<li class="filter" data-filter=".filtr-item"> <span class="active"><?php esc_html_e( 'All', 'travel-log' ); ?> </span> </li>
						<?php
						if ( ! empty( $categories ) ) :

							foreach ( $categories as $category ) :

								if ( 'trip-types' === $content_type && class_exists( 'WP_Travel' ) ) {

									$term = get_term( (int) $category, 'itinerary_types' );

									$term_id = $term->term_taxonomy_id;

								} elseif ( 'trip-location' === $content_type && class_exists( 'WP_Travel' ) ) {

									$term = get_term( (int) $category, 'travel_locations' );

									$term_id = $term->term_taxonomy_id;
								} else {

									$term = get_term( (int) $category, 'category' );

									$term_id = $term->term_id;

								}

								$term_id = apply_filters( 'travel_log_post_filter_term_id', $term_id, $content_type, $category );

							?>
								<li class="filter" data-filter=".<?php echo absint( $term_id ); ?>"><span><?php echo esc_html( $term->name ); ?></span></li>
							<?php
							endforeach;
							endif;
						?>
						</ul>
						<div class="filtr-container" id="tourlist">
						<ul class="wp-travel-itinerary-list" >
						<?php
						if ( ! empty( $categories ) ) {

							if ( 'trip-types' === $content_type && class_exists( 'WP_Travel' ) ) {

								$args = array(
								'post_type' => travel_log_wp_travel_support_get_post_type(),
								'tax_query' => array(
									array(
										'taxonomy' => 'itinerary_types',
										'field'    => 'id',
										'terms'    => $categories,
									),
								),
								);

							} elseif ( 'trip-location' === $content_type && class_exists( 'WP_Travel' ) ) {

								$args = array(
                                    'post_type' => travel_log_wp_travel_support_get_post_type(),
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'travel_locations',
                                            'field'    => 'id',
                                            'terms'    => $categories,
                                        ),
                                    ),
								);
							} else {

								$args['cat'] = $categories;

							}
						} else {

							if ( 'trip-types' === $content_type || 'trip-location' === $content_type && class_exists( 'WP_Travel' ) ) {
								$args['post_type'] = travel_log_wp_travel_support_get_post_type();
							}
						}

						$args['posts_per_page'] = apply_filters( 'travel_log_posts_filter_posts_limit', 9 );

						$slider_posts = new WP_Query( $args );

						if ( $slider_posts->have_posts() ) :

							while ( $slider_posts->have_posts() ) :

								$slider_posts->the_post();

								$category_detail = get_the_category( get_the_ID() );

								if ( class_exists( 'WP_Travel' ) ) :

									if ( 'trip-types' == $content_type  ) :

										$category_detail = get_the_terms( get_the_ID(), 'itinerary_types' );

										elseif ( 'trip-location' == $content_type  ) :

											$category_detail = get_the_terms( get_the_ID(), 'travel_locations' );

										endif;

									endif;

								$category_ids = !empty( $category_detail ) ? wp_list_pluck( $category_detail, 'term_id' ) : '';

								$category_ids = ! empty( $category_ids ) ? implode( ' ', $category_ids ) : '';

								echo '<div class="col-md-4 col-sm-6 col-xs-12 filtr-item ' . $category_ids . '">';

									$args = array(
										'thumbnail_size' => 'medium',
									);

								if ( class_exists( 'WP_Travel' ) ) :

									if ( 'trip-types' === $content_type || 'trip-location' === $content_type ) :

										get_template_part( 'template-parts/wp-travel/itinerary', 'item' );

									else :
										// Load Content Template.
										travel_log_trip_content( $args );

									endif;
								else :
									// Load Content Template.
									travel_log_trip_content( $args );

								endif;

								echo '</div>';

							endwhile;
							wp_reset_postdata();
							endif;
						?>
						</ul>
						</div>
						</div>
					</div>
				</div>
			</section>
		<?php
	}

endif;

if ( ! function_exists( 'travel_log_front_page_post_filter_wrap' ) ) :

	/**
	 * Wrapper for post filter.
	 *
	 * @since 1.0.0
	 */
	function travel_log_front_page_post_filter_wrap() {

		if ( ! is_front_page() || is_home() ) {
			return;
		}

		$enable = travel_log_get_theme_option( 'post_filter_enable' );

		if ( ! is_customize_preview() && false === $enable ) {
			return;
		}

		echo '<div id="travel-log-front-page-post-filter-wrap" class="travel-log-show-partial-edit-shortcut">';
		travel_log_front_page_post_filter_content();
		echo '</div>';
	}

endif;

add_action( 'travel_log_front_page_content', 'travel_log_front_page_post_filter_wrap', 20 );

/* Call to action section. */

if ( ! function_exists( 'travel_log_front_page_call_to_action_content' ) ) :

	/**
	 * Call to action content generate.

	 * @since 1.0.0
	 */
	function travel_log_front_page_call_to_action_content() {

		// $enabled = travel_log_get_theme_option( 'call_to_action_enable' );

		$call_to_action_page = travel_log_get_theme_option( 'call_to_action_content_page' );

		// if ( ! $enabled ) {
		// 	return;
		// }

		if ( empty( $call_to_action_page ) ) :

			return;

			endif;

		$post = get_post( $call_to_action_page );

		if ( ! empty( $post ) ) {

			$page_id = $post->ID;

			$title = get_the_title( $page_id );

			$bg_url = get_the_post_thumbnail_url( $page_id, 'full' );

			$sub_title = $post->post_content;

			$button_url = get_permalink( $page_id );

			wp_reset_postdata();
		}

		$button_text = travel_log_get_theme_option( 'call_to_action_button_text' );

		$inline_style = ( '' !== $bg_url ) ? 'background-image: url( ' . esc_url( $bg_url ) . ' )' : '';
		?>
		<section id="highlight-tag" class="highlight-tag" style="<?php echo esc_attr( $inline_style ); ?>">
		<div class="theme-overlay"></div>
			<div class="container">
				<div class="row">
					<div class="highlight-wrapper clearfix">
						<div class="col-sm-9">
							<?php if ( '' !== $title ) : ?>
								<h2><?php echo esc_html( $title ); ?></h2>
							<?php endif; ?>
							<?php if ( '' !== $sub_title ) : ?>
								<p><?php

									$excerpt_length = travel_log_get_theme_option( 'call_to_action_excerpt_length' );

									$num_words = apply_filters( 'travel_log_cta_sub_title_length', $excerpt_length );

									$more = apply_filters( 'travel_log_cta_more_text', '...' );

									echo esc_html( wp_trim_words( $sub_title , $num_words , $more ) ); ?>

								</p>
							<?php endif; ?>
								</div>
								<div class="col-sm-3">
									<?php if ( '' !== $button_text && '' !== $button_url ) : ?>
								<a id="cta-button" href="<?php echo esc_url( $button_url ); ?>" class="highlight-book theme-btn"><?php echo esc_html( $button_text ); ?></a>
							<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
			</section><!-- end of highlight tag booking -->
			<?php
	}

endif;

if ( ! function_exists( 'travel_log_front_page_call_to_action_wrap' ) ) :

	/**
	 * Wrap for front page call to action section.
	 *
	 * @since 1.0.0
	 */
	function travel_log_front_page_call_to_action_wrap() {

		$enabled = travel_log_get_theme_option( 'call_to_action_enable' );

		if ( ! is_customize_preview() && false === $enabled ) {
			return;
		}

		echo '<div id="travel-log-front-page-call-to-action-wrap" class="travel-log-show-partial-edit-shortcut">';
		travel_log_front_page_call_to_action_content();
		echo '</div>';
	}

endif;

add_action( 'travel_log_front_page_content', 'travel_log_front_page_call_to_action_wrap', 30 );

if ( ! function_exists( 'travel_log_front_page_recommended_posts_content' ) ) :

	/**
	 * Recommended posts content.
	 *
	 * @since 1.0.0
	 */
	function travel_log_front_page_recommended_posts_content() {

		// $enabled = travel_log_get_theme_option( 'recommended_posts_enable' );
		// if ( false === $enabled ) {
		// 	return;
		// }
		$title = travel_log_get_theme_option( 'recommended_posts_title' );

		$sub_title = travel_log_get_theme_option( 'recommended_posts_sub_title' );

		$category = travel_log_get_theme_option( 'recommended_posts_category' );

		$recommended_content_type = travel_log_get_theme_option( 'home_recommended_content_type' );

		?>
		<section id="recomended-travel" class="recmended-travel clearfix">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<?php if ( '' !== $title ) : ?>
						<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $sub_title ) : ?>
						<div class="title-tagline">
							<p><?php echo esc_html( $sub_title ); ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="recomended-lists clearfix" <?php echo is_rtl() ? 'dir="rtl"' : '' ; ?>>
						<?php

						if ( class_exists( 'WP_Travel' ) && 'recommended-trips' === $recommended_content_type  ) :

							$args = array(
								'post_type' => travel_log_wp_travel_support_get_post_type(),
								'meta_query' => array(
									array(
										'key' => 'wp_travel_featured',
										'value' => 'yes',
										'compare' => '=',
									),
								),
							);

							else :

								$args['category_name'] = $category;

								endif;

							$args['posts_per_page'] = apply_filters( 'travel_log_recommended_posts_limit', 10 );

							$recommended_posts = new WP_Query( $args );

							if ( $recommended_posts->have_posts() ) :

								while ( $recommended_posts->have_posts() ) :

									$recommended_posts->the_post();

									$args = array(
										'thumbnail_size' => 'medium',
									);

									if ( class_exists( 'WP_Travel' ) && 'recommended-trips' === $recommended_content_type ) :

										get_template_part( 'template-parts/wp-travel/itinerary', 'item' );

									else :
										// Load Content Template.
										travel_log_trip_content( $args );

									endif;

								endwhile;

								wp_reset_postdata();

								endif;
							?>
							</div>
						</div>
					</div>
				</div>
			</section>
			<?php
	}

endif;

if ( ! function_exists( 'travel_log_front_page_recommended_posts_wrap' ) ) :

	/**
	 * Wrap for front page recommended posts section.
	 *
	 * @since 1.0.0
	 */
	function travel_log_front_page_recommended_posts_wrap() {

		$enabled = travel_log_get_theme_option( 'recommended_posts_enable' );
		if ( ! is_customize_preview() && false === $enabled ) {
			return;
		}
		echo '<div id="travel-log-front-page-recommended-posts-wrap" class="travel-log-show-partial-edit-shortcut">';

		travel_log_front_page_recommended_posts_content();

		echo '</div>';
	}

endif;

add_action( 'travel_log_front_page_content', 'travel_log_front_page_recommended_posts_wrap', 40 );

if ( ! function_exists( 'travel_log_front_page_testimonials_content' ) ) :

	/**
	 * [travel_log_front_page_testimonials_content Create the testimonial Section Contents]
	 *
	 * @since 1.0.0
	 */
	function travel_log_front_page_testimonials_content() {

		// $enabled = travel_log_get_theme_option( 'testimonials_enable' );
		// if ( false === $enabled ) {
		// 	return;
		// }

		$title = travel_log_get_theme_option( 'testimonials_title' );

		$category = travel_log_get_theme_option( 'testimonials_category' );

		$testimonials_content_type = travel_log_get_theme_option( 'home_testimonials_content_type' );

		$bg_url = travel_log_get_theme_option( 'testimonials_bg_url' );

		$inline_style = ( '' !== $bg_url ) ? 'background-image: url( ' . esc_url( $bg_url ) . ' )' : '';
		?>
		<section id="travel-testimonial" class="travel-testimonial" style="<?php echo esc_attr( $inline_style ); ?>">
		<div class="theme-overlay"></div>
			<div class="container">
				<div class="testimonial-wrapper">
					<div class="row">
						<div class="col-sm-10 col-xs-12">
							<?php if ( '' !== $title ) : ?>
								<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
							<?php endif; ?>
						</div>
					</div>
					<div class="row">
						<div class="container">
							<div class="col-sm-12">
							<!--travel featured slider-->
								<div id="testimonial-slider" class="testimonial-slider clearfix">
								<div class="testimonial slider" <?php echo is_rtl() ? 'dir="rtl"' : '' ; ?> >
									<?php

									if (  class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'custom-content-types' ) && 'jetpack-testimonials' === $testimonials_content_type  ) :

										$args['post_type'] = 'jetpack-testimonial';

										else :

											$args['category_name'] = $category;

											endif;

										$args['posts_per_page'] = apply_filters( 'travel_log_testimonials_posts_limit', 6 );
										$testimonials = new WP_Query( $args );
										if ( $testimonials->have_posts() ) :
											while ( $testimonials->have_posts() ) :
												$testimonials->the_post();
											?>
											<div class="testimonial-content">
													<div class="client-img wrap">
													<?php
													if ( has_post_thumbnail() ) :

														the_post_thumbnail( 'thumbnail' );

														else :
															echo get_avatar( '' ); // Default.
														endif;
														?>
														</div>
													<div class="client-content">
														<div class="testimonial-quote-left clearfix">
	                                                    <i class="wt-icon wt-icon-quote-left" aria-hidden="true"></i>
														</div>
														<?php the_excerpt(); ?>
														<div class="client-bio"><div class="name-id"><?php the_title(); ?></div></div>
													<div class="testimonial-quote-right clearfix">
														<i class="wt-icon wt-icon-quote-right" aria-hidden="true"></i>
													</div>
													</div>
												</div>
											<?php
											endwhile;
											wp_reset_postdata();

											elseif ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'custom-content-types' ) && 'jetpack-testimonials' == $testimonials_content_type ) :
												if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) :?>

													<?php esc_html_e( 'Please add testimonial posts in the jetpack testimonials post type', 'travel-log' ); ?>

												<?php endif;
											endif; ?>
										</div>
									</div>
									</div>
								</div>
							</div>
						</div>
					</div>
			</section>
		<?php
	}

endif;

if ( ! function_exists( 'travel_log_front_page_testimonials_wrap' ) ) :

	/**
	 * [travel_log_front_page_testimonials_wrap Wrapper For the testimonial Section]
	 *
	 * @since 1.0.0
	 */
	function travel_log_front_page_testimonials_wrap() {

		$enabled = travel_log_get_theme_option( 'testimonials_enable' );
		if ( ! is_customize_preview() && false === $enabled ) {
			return;
		}
		echo '<div id="travel-log-front-page-testimonials-wrap" class="travel-log-show-partial-edit-shortcut">';

		travel_log_front_page_testimonials_content();

		echo '</div>';
	}

endif;

add_action( 'travel_log_front_page_content', 'travel_log_front_page_testimonials_wrap', 50 );

if ( ! function_exists( 'travel_log_front_page_latest_posts_content' ) ) :

	/**
	 * Latest posts content.
	 *
	 * @since 1.0.0
	 */
	function travel_log_front_page_latest_posts_content() {

		// $enabled = travel_log_get_theme_option( 'latest_posts_enable' );

		// if ( false === $enabled ) {
		// 	return;
		// }

		$title = travel_log_get_theme_option( 'latest_posts_title' );

		$sub_title = travel_log_get_theme_option( 'latest_posts_sub_title' );

		$excluded_cats = travel_log_get_theme_option( 'latest_posts_excluded_cats' );

		?>
		<section id="travel-blog" class="travel-blog clearfix">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
				<?php if ( '' !== $title ) : ?>
				<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $sub_title ) : ?>
				<div class="title-tagline">
				<p><?php echo esc_html( $sub_title ); ?></p>
				</div>
				<?php endif; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="featured-blog clearfix" id="featured-blog">
						<div class="blog-slide slider" <?php echo is_rtl() ? 'dir="rtl"' : '' ; ?> >
							<?php

								$args['posts_per_page'] = apply_filters( 'travel_log_posts_filter_posts_limit', 6 );

								if ( isset( $excluded_cats ) ) :

									$cat_ids = explode( ',' , $excluded_cats );

									if ( is_array( $cat_ids ) ) :

										$args['category__not_in'] = $cat_ids;

									endif;

								endif;

								$latest_blog_posts = new WP_Query( $args );

							if ( $latest_blog_posts->have_posts() ) :
								?>
								<?php
								while ( $latest_blog_posts->have_posts() ) :
									$latest_blog_posts->the_post();

									$args = array(
										'thumbnail_size' => 'medium',
									);

										travel_log_post_content( $args );

									endwhile;
								wp_reset_postdata();
								endif;
							?>
							</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		<?php
	}

endif;

if ( ! function_exists( 'travel_log_front_page_latest_posts_wrap' ) ) :

	/**
	 * Wrap for front page latest posts section.
	 *
	 * @since 1.0.0
	 */
	function travel_log_front_page_latest_posts_wrap() {

		$enabled = travel_log_get_theme_option( 'latest_posts_enable' );

		if ( ! is_customize_preview() && false === $enabled ) {
			return;
		}
		echo '<div id="travel-log-front-page-latest-posts-wrap" class="travel-log-show-partial-edit-shortcut">';

		travel_log_front_page_latest_posts_content();

		echo '</div>';
	}

endif;

add_action( 'travel_log_front_page_content', 'travel_log_front_page_latest_posts_wrap', 60 );

if ( ! function_exists( 'travel_log_front_page_widget_area' ) ) :

	/**
	 * Front page widget area.
	 *
	 * @since 2.0.0
	 */
	function travel_log_front_page_widget_area() {
		if ( ! is_active_sidebar( 'front-page-before-footer' ) ) {
			return;
		}
		echo '<div class="travel-log-front-page-full-width-widget clearfix">';
			echo '<div class="before-footer-wrap">';
			dynamic_sidebar( 'front-page-before-footer' );
			echo '</div>';
		echo '</div>';
	}

endif;

add_action( 'travel_log_front_page_content', 'travel_log_front_page_widget_area', 70 );
