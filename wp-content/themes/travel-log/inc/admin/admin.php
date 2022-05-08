<?php
/**
 * Admin functions.
 *
 * @package Travel_Log
 */

add_action( 'admin_menu', 'travel_log_admin_menu_page' );

/**
 * Register admin page.
 *
 * @since 1.0.0
 */
function travel_log_admin_menu_page() {

	$theme = wp_get_theme( get_template() );

	add_theme_page(
		$theme->display( 'Name' ) . __( ' Info', 'travel-log' ),
		$theme->display( 'Name' ) . __( ' Info', 'travel-log' ),
		'manage_options',
		'travel-log',
		'travel_log_do_admin_page'
	);

}

if ( ! function_exists( 'travel_log_admin_about_page_simplify_num' ) ) :
	/**
	 * Return Simplified Number.
	 *
	 * @param $number
	 */
	function travel_log_admin_about_page_simplify_num( $number, $precision = 2 ) {
		$suffixes = array( 'k', 'M' );
		$suffix   = '';
		while ( $number >= 1000 && count( $suffixes ) ) {
			$suffix = array_shift($suffixes);
			$number /= 1000;    // 1.289 | 12.345 | 123.456
		}
		$num = $number;
		while ( $precision ) {
			$num /= 10;
			$precision--;
			if ( $num < 1 ) {
				break;
			}
		}

		$number = round( $number, $precision );

		return $number . $suffix;
	}
endif;

if ( ! function_exists( 'travel_log_admin_call_plugins_org_api' ) ) :
	/**
	 * Calls WP Org API travel_log_admin_call_plugins_org_api.
	 *
	 * @param $slug Plugin Slug.
	 */
	function travel_log_admin_call_plugins_org_api( $slug ) {

		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$call_api = get_transient( 'tlog_theme_about_plugin_info_' . $slug );

		if ( false === $call_api ) {
			$call_api = plugins_api(
				'plugin_information', array(
					'slug'   => $slug,
					'fields' => array(
						'active_installs'   => true,
						'rating'            => false,
						'description'       => false,
						'short_description' => true,
						'donate_link'       => false,
						'tags'              => false,
						'sections'          => true,
						'homepage'          => true,
						'added'             => false,
						'last_updated'      =>  true,
						'compatibility'     => false,
						'tested'            => true,
						'requires'          => false,
						'downloadlink'      => false,
						'icons'             => true,
					),
				)
			);
			set_transient( 'tlog_theme_about_plugin_info_' . $slug, $call_api, WEEK_IN_SECONDS );
		}

		return $call_api;

	}

endif;

if ( ! function_exists( 'travel_log_about_page_get_plugin_icon' ) ) :
	/**
	 * Get icon of wordpress.org plugin
	 *
	 * @param array $arr array of image formats.
	 *
	 * @return mixed
	 */
	function travel_log_about_page_get_plugin_icon( $arr ) {

		if ( ! empty( $arr['svg'] ) ) {
			$plugin_icon_url = $arr['svg'];
		} elseif ( ! empty( $arr['2x'] ) ) {
			$plugin_icon_url = $arr['2x'];
		} elseif ( ! empty( $arr['1x'] ) ) {
			$plugin_icon_url = $arr['1x'];
		} else {
			$plugin_icon_url = get_template_directory_uri() . '/images/placeholder_plugin.png';
		}

		return $plugin_icon_url;
	}

endif;

if ( ! function_exists( 'travel_log_about_page_check_plugin_active' ) ) :
	/**
	 * Check if plugin is active
	 *
	 * @param plugin-slug $slug the plugin slug.
	 * @return array
	 */
	function travel_log_about_page_check_plugin_active( $slug ) {
		if ( ( $slug == 'intergeo-maps' ) || ( $slug == 'visualizer' ) ) {
			$plugin_root_file = 'index';
		} elseif ( $slug == 'adblock-notify-by-bweb' ) {
			$plugin_root_file = 'adblock-notify';
		} else {
			$plugin_root_file = $slug;
		}

		$path = WPMU_PLUGIN_DIR . '/' . $slug . '/' . $plugin_root_file . '.php';
		if ( ! file_exists( $path ) ) {
			$path = WP_PLUGIN_DIR . '/' . $slug . '/' . $plugin_root_file . '.php';
			if ( ! file_exists( $path ) ) {
				$path = false;
			}
		}

		if ( file_exists( $path ) ) {

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			$needs = is_plugin_active( $slug . '/' . $plugin_root_file . '.php' ) ? 'deactivate' : 'activate';

			return array(
				'status' => is_plugin_active( $slug . '/' . $plugin_root_file . '.php' ),
				'needs'  => $needs,
			);
		}

		return array(
			'status' => false,
			'needs'  => 'install',
		);
	}
endif;

if ( ! function_exists( '' ) ) :
	/**
	 * Check if a slug is from intergeo, visualizer or adblock and returns the correct slug for them.
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return string
	 */
	function travel_log_admin_about_page_check_plugin_slug( $slug ) {
		switch ( $slug ) {
			case 'intergeo-maps':
			case 'visualizer':
				$slug = 'index';
				break;
			case 'adblock-notify-by-bweb':
				$slug = 'adblock-notify';
				break;
		}
		return $slug;
	}
endif;

/**
 * Create the install/activate button link for plugins
 *
 * @param plugin-state $state The plugin state (not installed/inactive/active).
 * @param plugin-slug  $slug The plugin slug.
 */
function travel_log_admin_page_create_action_link( $state, $slug ) {
	switch ( $state ) {
		case 'install':
			return wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'install-plugin',
						'plugin' => $slug,
					),
					network_admin_url( 'update.php' )
				),
				'install-plugin_' . $slug
			);
			break;
		case 'deactivate':
			return add_query_arg(
				array(
					'action'        => 'deactivate',
					'plugin'        => rawurlencode( $slug . '/' . $slug . '.php' ),
					'plugin_status' => 'all',
					'paged'         => '1',
					'_wpnonce'      => wp_create_nonce( 'deactivate-plugin_' . $slug . '/' . $slug . '.php' ),
				), network_admin_url( 'plugins.php' )
			);
			break;
		case 'activate':
			return add_query_arg(
				array(
					'action'        => 'activate',
					'plugin'        => rawurlencode( $slug . '/' . $slug . '.php' ),
					'plugin_status' => 'all',
					'paged'         => '1',
					'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $slug . '/' . $slug . '.php' ),
				), network_admin_url( 'plugins.php' )
			);
			break;
	}// End switch().
}

/**
 * Render admin page.
 *
 * @since 1.0.0
 */
function travel_log_do_admin_page() {

	$theme = wp_get_theme( get_template() );
	?>
	<div class="wrap about-wrap travel-theme-wrap">

		<h1><?php esc_html_e( 'Welcome to', 'travel-log' ); ?> <?php echo $theme->display( 'Name' ); ?> - <?php esc_html_e( 'Version', 'travel-log' ); ?>:&nbsp;<?php echo esc_html( $theme->display( 'Version' ) ); ?></h1>

	<div class="about-text">
		<?php
			$description_raw  = $theme->display( 'Description' );
			$main_description = explode( 'Official', $description_raw );
			echo wp_kses_post( make_clickable( $main_description[0] ) );
		?>
	</div>
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php esc_html_e( 'Getting Started', 'travel-log' ); ?></a></li>
			<li><a href="#tabs-2"><?php esc_html_e( 'Recommended Actions', 'travel-log' ); ?></a></li>
			<li><a href="#tabs-3"><?php esc_html_e( 'Compatible Plugins', 'travel-log' ); ?></a></li>
			<li><a href="#tabs-4"><?php esc_html_e( 'Support', 'travel-log' ); ?></a></li>
		</ul>
	<div id="tabs-1" class="tabs-column">

		<div class="three-col">

			<div class="col">
				<h3><i class="dashicons dashicons-sos"></i><?php esc_html_e( 'Recommended actions', 'travel-log' ); ?></h3>
				<p>
					<?php esc_html_e( 'We have compiled a list of steps for you to take so we can ensure that the experience you have using one of our products is very easy to follow.', 'travel-log' ); ?>
				</p>
				<p>
					<a id="recommended-actions" class="button button-primary" href="#"><?php esc_html_e( 'Recommended actions', 'travel-log' ); ?></a>
				</p>

			</div><!-- .col -->

			<div class="col">

				<h3><i class="dashicons dashicons-admin-customizer"></i><?php esc_html_e( 'Theme Options', 'travel-log' ); ?></h3>

				<p>
					<?php esc_html_e( 'We have used Customizer API for theme options which will help you preview your changes live and fast.', 'travel-log' ); ?>
				</p>

				<p>
					<a class="button button-primary" href="<?php echo wp_customize_url(); ?>"><?php esc_html_e( 'Customize', 'travel-log' ); ?></a>
				</p>

			</div><!-- .col -->

			<div class="col">

				<h3><i class="dashicons dashicons-book-alt"></i><?php esc_html_e( 'Theme Instructions', 'travel-log' ); ?></h3>
				<p>
					<?php esc_html_e( 'We have prepared detailed theme instructions which will help you to customize theme as you prefer.', 'travel-log' ); ?>
				</p>

				<p>
					<a class="button button-primary" href="<?php echo esc_url( 'http://wensolutions.com/documentations/travel-log' ); ?>" target="_blank"><?php esc_html_e( 'Documentation', 'travel-log' ); ?></a>
				</p>

			</div><!-- .col -->

		</div><!-- .three-col -->
	</div>
 
	<div id="tabs-2" class="tabs-column">

	<?php 

		$addon_slug = 'ws-theme-addons';

		$addon = travel_log_admin_call_plugins_org_api( $addon_slug ); 

		$addon_active = travel_log_about_page_check_plugin_active( $addon_slug );

		if ( ! empty( $addon_active['needs'] ) ) {
			$addon_slug = travel_log_admin_about_page_check_plugin_slug( $addon_slug );
			$addon_url  = travel_log_admin_page_create_action_link( $addon_active['needs'], $addon_slug );
		}
		if ( ! empty( $addon->name ) && ! empty( $addon_active ) ) {

			$add_btn_label = '';
			$add_btn_class = '';

			switch ( $addon_active['needs'] ) {
				case 'install':
					$add_btn_class = 'install-now button button-primary';

						$add_btn_label = __( 'Install and Activate', 'travel-log' );
					break;
				case 'activate':
					$add_btn_class = 'activate-now button button-primary';
					$add_btn_label = __( 'Activate Now', 'travel-log' );
					break;
				case 'deactivate':
					$add_btn_class = 'deactivate-now button';
					$add_btn_label = __( 'Deactivate Now', 'travel-log' );
					break;
			}
		}
		?>


		<?php if ( 'install' == $addon_active['needs'] || 'activate' == $addon_active['needs'] ) : ?>
		<div class="about-page-action-required-box">

			<h3><?php echo esc_html( 'WS Theme Addon', 'travel-log' ); ?></h3>

			<p><?php echo esc_html( 'WS Theme addons is a professional looking, easy to use yet highly functional extensions that enhances the travel log theme with its amazing additional features. This plugin is an amzing collection of additional feature which is for free.
			This plugin basically import the demo content and add widget "WS Instagram Widget" which is best to enhance features of themes.', 'travel-log' ); ?></p>		

			<p class="action_button ">
			<?php
				echo '<a data-slug="' . esc_attr( $addon_slug ) . '" class="' . esc_attr( $add_btn_class ) . '" href="' . esc_url( $addon_url ) . '">' . esc_html( $add_btn_label ) . '</a>';
				echo '</span>'; ?>
			</p>
		</div>
		<?php else : ?>
		<div class="isa_success">
        	<p><i class="dashicons dashicons-yes"></i><?php echo esc_html__( 'All Set! All the recommended actions are setup in your site. Enjoy using Travel Log theme.', 'travel-log' ); ?></p>
    	</div>
		<?php endif; ?>

	</div>
	<div id="tabs-3" class="tabs-column">
	<?php
		$recommended_plugins = array(

			array(
				'slug' => 'wp-travel',
			),
			array(
				'slug' => 'contact-form-7',
			),
			array(
				'slug' => 'elementor',
			),
			array(
				'slug' => 'loco-translate',
			),

		);

	foreach ( $recommended_plugins as $recommended_plugins_item ) {

		if ( ! empty( $recommended_plugins_item['slug'] ) ) {
			$info = travel_log_admin_call_plugins_org_api( $recommended_plugins_item['slug'] );

			if ( ! empty( $info->icons ) ) {
				$icon = travel_log_about_page_get_plugin_icon( $info->icons );
			}

			$active = travel_log_about_page_check_plugin_active( $recommended_plugins_item['slug'] );

			if ( ! empty( $active['needs'] ) ) {
				$slug = travel_log_admin_about_page_check_plugin_slug( $recommended_plugins_item['slug'] );
				$url  = travel_log_admin_page_create_action_link( $active['needs'], $slug );
			}
			if ( ! empty( $info->name ) && ! empty( $active ) ) {

				$label = '';

				switch ( $active['needs'] ) {
					case 'install':
						$class = 'install-now button button-primary';

							$label = __( 'Install and Activate', 'travel-log' );
						break;
					case 'activate':
						$class = 'activate-now button button-primary';
						$label = __( 'Activate Now', 'travel-log' );
						break;
					case 'deactivate':
						$class = 'deactivate-now button';
						$label = __( 'Deactivate Now', 'travel-log' );
						break;
				}
			}
			?>
				<div class="plugin-card">
						<div class="entry-thumbnail">
							<?php 
								if ( ! empty( $icon ) ) {
									echo '<img src="' . esc_url( $icon ) . '" alt="plugin box image">';
								}
							?>
						</div>
						<div class="entry">
						<header class="entry-header">
							<h2 class="entry-title"><a target="_blank" href="https://wordpress.org/plugins/<?php echo esc_attr( $info->slug ); ?>/" rel="bookmark"><?php echo esc_html( $info->name ); ?></a></h2>
						</header>
						<!-- .entry-header -->
						<div class="plugin-rating">
								<div class="wporg-ratings">
									<span class="dashicons dashicons-star-filled"></span>
									<span class="dashicons dashicons-star-filled"></span>
									<span class="dashicons dashicons-star-filled"></span>
									<span class="dashicons dashicons-star-filled"></span>
									<span class="dashicons dashicons-star-filled"></span>
								</div>
								<span class="rating-count">(<a target="_blank" href="https://wordpress.org/support/plugin/<?php echo esc_attr( $info->slug ); ?>/reviews/"><?php echo esc_html( $info->ratings['5'] ); ?><span class="screen-reader-text"> <?php esc_html_e( '5 Star ratings', 'travel-log' ); ?></span></a>)</span>
						</div>
						<div class="entry-excerpt">
							<p><?php echo esc_html( $info->short_description ); ?></p>
							<?php 
							echo '<span class="plugin-card-' . esc_attr( $recommended_plugins_item['slug'] ) . ' action_button ' . ( ( $active['needs'] !== 'install' && $active['status'] ) ? 'active' : '' ) . '">';
								echo '<a data-slug="' . esc_attr( $recommended_plugins_item['slug'] ) . '" class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
								echo '</span>';
							?>
						</div>
						<!-- .entry-excerpt -->
						</div>
						<hr>
						<footer>
							<?php if ( ! empty( $info->author ) ) { ?>
								<span class="plugin-author">
									<i class="dashicons dashicons-admin-users"></i> 
									<?php echo $info->author; ?>
								</span>
							<?php } ?>
							<?php if ( ! empty( $info->active_installs ) ) { ?>
								<span class="active-installs">
									<i class="dashicons dashicons-chart-area"></i>
									<?php
										printf( esc_html__( '%s Active Installs', 'travel-log' ), travel_log_admin_about_page_simplify_num( $info->active_installs ) );
									?>	
								</span>
							<?php } ?>
							<?php if ( ! empty( $info->tested ) ) { ?>
								<span class="tested-with">
									<i class="dashicons dashicons-wordpress-alt"></i>
									<?php
										printf( esc_html__( 'Tested Upto %s', 'travel-log' ), $info->tested );
									?>
								</span>
							<?php } ?>
							<?php if ( ! empty( $info->last_updated ) ) { ?>
								<span class="last-updated">
									<i class="dashicons dashicons-calendar"></i> 
									<?php 
										printf( esc_html__( 'Last Updated : %s', 'travel-log' ), $info->last_updated );
									?>	
								</span>
							<?php } ?>
						</footer>
					</div>
			<?php 
		}// End if().
	}// End foreach().

?>
	</div>

	<div id="tabs-4" class="tabs-column">

		<div class="three-col">


			<div class="col">

				<h3><i class="dashicons dashicons-sos"></i><?php esc_html_e( 'Help &amp; Support', 'travel-log' ); ?></h3>

				<p>
					<?php esc_html_e( 'If you have any question/feedback regarding theme, please post in our official support forum.', 'travel-log' ); ?>
				</p>

				<p>
					<a class="button button-primary" href="<?php echo esc_url( 'http://wptravel.io/support-forum/forum/travel-log' ); ?>" target="_blank"><?php esc_html_e( 'Get Support', 'travel-log' ); ?></a>
				</p>

			</div><!-- .col -->

			<div class="col">

				<h3><i class="dashicons dashicons-book-alt"></i><?php esc_html_e( 'Documentation', 'travel-log' ); ?></h3>
				<p>
					<?php esc_html_e( 'Need more details? Please check our full documentation for detailed information on how to use Travel Log.', 'travel-log' ); ?>
				</p>

				<p>
					<a class="button button-primary" href="<?php echo esc_url( 'http://wensolutions.com/documentations/travel-log' ); ?>" target="_blank"><?php esc_html_e( 'Read full documentation', 'travel-log' ); ?></a>
				</p>

			</div><!-- .col -->

			<div class="col">

				<h3><i class="dashicons dashicons-admin-customizer"></i><?php esc_html_e( 'Create a child theme', 'travel-log' ); ?></h3>

				<p>
					<?php esc_html_e( "If you want to make changes to the theme's files, those changes are likely to be overwritten when you next update the theme. In order to prevent that from happening, you need to create a child theme. For this, please follow the documentation below.", "travel-log" ); ?>
				</p>

				<p>
					<a target="_blank" class="button button-primary" href="http://wptravel.io/how-to-create-a-child-theme/"><?php esc_html_e( 'View how to do this', 'travel-log' ); ?></a>
				</p>

			</div><!-- .col -->

		</div><!-- .three-col -->

	</div>
	</div>
</div>
<?php

}

/**
 * Load admin scripts.
 *
 * @since 1.0.0
 *
 * @param string $hook Current page hook.
 */
function travel_log_load_admin_scripts( $hook ) {

	if ( 'appearance_page_travel-log' === $hook ) {

		wp_enqueue_style( 'travel-log-admin', get_template_directory_uri() . '/css/admin.css', false, '1.0.0' );

		wp_enqueue_script( 'travel-log-admin-js', get_template_directory_uri() . '/js/admin.js', array( 'jquery' ), '1.0.0', true );

		wp_enqueue_script( 'jquery-ui-tabs' );

	}

}
add_action( 'admin_enqueue_scripts', 'travel_log_load_admin_scripts' );
