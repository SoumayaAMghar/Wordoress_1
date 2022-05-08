<?php
/**
 * Itinerary Template : WP Travel
 *
 * @see         http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     Travel_Log
 * @since       1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$trip_id                   = get_the_ID();
$trip_pricing_options_data = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

$min_price_key = travel_log_itinerary_get_min_price_key( $trip_pricing_options_data );

/**
 * @since 1.2.8 Fixes for trip prices for WP Travel 4.0.0 and later.
 *
 * @since 1.2.9 Replaced wp_travel_get_price with travel_log_itinerary_get_price
 */
$regular_price = travel_log_itinerary_get_price( $trip_id, true );
$trip_price    = travel_log_itinerary_get_price( $trip_id );

/**
 * @since 1.2.9 Replaced with wp_travel_is_enable_sale();
 */
$enable_sale = travel_log_itinerary_is_sale_enabled( $trip_id, false, '', '', $min_price_key );

$trip_duration   = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
$settings        = travel_log_itinerary_get_settings();
$currency_code   = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
$currency_symbol = travel_log_itinerary_get_currency_symbol( $currency_code );
$per_person_text = travel_log_itinerary_get_price_per_text( $trip_id, $min_price_key );
?>
<div class="post-item-wrapper trip-details">
		<a href="<?php the_permalink(); ?>">
			<div class="post-thumb">
				<?php
				if ( has_post_thumbnail() ) :

					the_post_thumbnail( apply_filters( 'travel_log_trip_content_thumbnail_size', 'medium' ) );

				else :

					travel_log_no_slider_thumbnail();

				endif;
				?>
			</div>
			<span class="effect"></span>
			<div class="post-content">
				<div class="trip-metas">
					<div class="clearfix title-price-wrapper" >
						<h4 class="post-title"><?php the_title(); ?></h4>
						<?php if ( $trip_price ) : ?>
							<div class="trip-price" >

							<?php if ( $enable_sale ) : ?>
								<del>
									<span><?php echo travel_log_itinerary_get_formated_price_currency( $regular_price, true ); ?></span>
								</del>
							<?php endif; ?>
								<span class="person-count">
									<ins>
										<span><?php echo travel_log_itinerary_get_formated_price_currency( $trip_price ); ?></span>
									</ins>
									<?php if ( ! empty( $per_person_text ) ) : ?>
										/<?php echo esc_html( $per_person_text ); ?>
									<?php endif; ?>
								</span>
							</div>
						<?php endif; ?>
					</div>
					<?php travel_log_itinerary_get_trip_duration( get_the_ID() ); ?>
					<div class="clearfix" >
						<?php
						if ( travel_log_itinerary_tab_show_in_menu( 'reviews' ) ) {
							?>
							<?php $average_rating = travel_log_itinerary_get_average_rating(); ?>
								<div class="wp-travel-average-review" title="<?php printf( esc_attr__( 'Rated %s out of 5', 'travel-log' ), $average_rating ); ?>">
									<span style="width:<?php echo esc_attr( ( $average_rating / 5 ) * 100 ); ?>%">
										<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'travel-log' ), '<span itemprop="bestRating">', '</span>' ); ?>
									</span>
								</div>
							<span class="rating-count"><?php printf( esc_html__( '( %s Reviews )', 'travel-log' ), travel_log_itinerary_get_rating_count() ); ?></span>
							<?php
						}
						?>
						<?php $terms = get_the_terms( get_the_ID(), 'itinerary_types' ); ?>
						<?php
						if ( is_array( $terms ) && count( $terms ) > 0 ) :
							$first_term = array_shift( $terms );
							$term_name  = $first_term->name;
							?>
							<span class="trip-category"><?php echo esc_html( $term_name ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php if ( $enable_sale && $trip_price ) : ?>
			<div class="wp-travel-offer">
				<span><?php esc_html_e( 'Offer', 'travel-log' ); ?></span>
			</div>
			<?php endif; ?>
	</a>
</div>
