<?php
namespace Mediavine\Grow;

use WP_Screen;

/**
 * Tools to help manage admin notices.
 */
class Admin_Notices {

	/** @var null  */
	private static $instance = null;

	/**
	 *
	 *
	 * @return Admin_Notices
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 *
	 */
	public function init() {
		add_action( 'admin_notices', [ $this, 'dpsp_admin_notices' ] );
		add_action( 'admin_notices', [ $this, 'dpsp_admin_notice_initial_setup_nag' ] );
		add_action( 'admin_notices', [ $this, 'dpsp_admin_notice_facebook_access_token_expired' ] );
		add_action( 'admin_notices', [ $this, 'dpsp_admin_notice_google_plus_removal' ] );
		add_action( 'admin_notices', [ $this, 'dpsp_admin_notice_grow_name_change' ] );
		add_action( 'admin_init', [ $this, 'dpsp_admin_notice_dismiss' ] );
		add_action( 'dpsp_first_activation', [ $this, 'dpsp_setup_activation_notices' ] );
		add_filter( 'removable_query_args', [ $this, 'dpsp_removable_query_args' ] );

		if ( ! \Social_Pug::is_free() ) {
			add_action( 'admin_notices', [ $this, 'notify_license_status' ] );
			add_action( 'admin_notices', [ $this, 'dpsp_admin_notice_jquery_deprecation' ] );
			add_action( 'admin_notices', [ $this, 'dpsp_serial_admin_notification' ] );
		}
	}

	/**
	 * Determines if first activation was before or after a specific date
	 *
	 * @param string $date Date in format: 'h:i m d Y'
	 * @return boolean
	 */
	public function was_first_activation_after( $date ) {
		$first_activation = Settings::get_setting( 'dpsp_first_activation', '' );
		if ( empty( $first_activation ) ) {
			return true;
		}

		$date = strtotime( $date );
		if ( ! empty( $date ) && $first_activation > $date ) {
			return true;
		}

		return false;
	}

	/**
	 * Display admin notices for our pages.
	 */
	function dpsp_admin_notices() {
		// Exit if settings updated is not present
		if ( empty( filter_input( INPUT_GET, 'settings-updated' ) ) ) {
			return;
		}

		$admin_page = ( ! empty( filter_input( INPUT_GET, 'page' ) ) ? filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) : '' );

		// Show these notices only on dpsp pages
		if ( false === strpos( $admin_page, 'dpsp' ) || 'dpsp-register-version' === $admin_page ) {
			return;
		}

		// Get messages
		$message_id = ( ! empty( filter_input( INPUT_GET, 'dpsp_message_id' ) ) ? filter_input( INPUT_GET, 'dpsp_message_id', FILTER_SANITIZE_NUMBER_INT ) : 0 );
		$message    = $this->dpsp_get_admin_notice_message( $message_id );

		$class = ( ! empty( filter_input( INPUT_GET, 'dpsp_message_class' ) ) ? filter_input( INPUT_GET, 'dpsp_message_class', FILTER_SANITIZE_STRING ) : 'updated' );

		if ( isset( $message ) ) {
			echo '<div class="dpsp-admin-notice notice is-dismissible ' . esc_attr( $class ) . '">';
			echo '<p>' . esc_attr( $message ) . '</p>';
			echo '</div>';
		}
	}

	/**
	 * Returns a human readable message given a message id.
	 *
	 * @param int $message_id
	 * @return mixed
	 */
	function dpsp_get_admin_notice_message( $message_id ) {
		$messages = apply_filters(
			'dpsp_get_admin_notice_message',
			[
				__( 'Settings saved.', 'social-pug' ),
				__( 'Settings imported.', 'social-pug' ),
				__( 'Please select an import file.', 'social-pug' ),
				__( 'Import file is not valid.', 'social-pug' ),
				__( 'Grow Social by Mediavine App authorized successfully.', 'social-pug' ),
			]
		);

		return $messages[ $message_id ];
	}

	/**
	 * Adds admin notifications for entering the license serial key.
	 */
	function dpsp_serial_admin_notification() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$dpsp_settings = Settings::get_setting( 'dpsp_settings' );

		$serial  = ( ! empty( $dpsp_settings['product_serial'] ) ? $dpsp_settings['product_serial'] : '' );
		$license = ( ! empty( $dpsp_settings['mv_grow_license'] ) ? $dpsp_settings['mv_grow_license'] : '' );
		// Check to see if serial is saved in the database
		if ( empty( $serial ) && empty( $license ) ) {

			$notice_classes = 'dpsp-serial-missing';
			// translators: %1$s is replaced by admin url, %2$s is replaced by store url
			$message = sprintf( __( 'Your <strong>Grow Social Pro by Mediavine</strong> license key is empty. Please <a href="%1$s">register your copy</a> to receive automatic updates and support. <br /><br /> Need a license key? <a class="dpsp-get-license button button-primary" target="_blank" href="%2$s">Get your license here</a>', 'social-pug' ), admin_url( 'admin.php?page=dpsp-settings' ), 'https://marketplace.mediavine.com/grow-social-pro/' );

			// Display the notice if notice classes have been added
			echo '<div class="dpsp-admin-notice notice ' . esc_attr( $notice_classes ) . '">';
			echo '<p>' . wp_kses( $message, View_Loader::get_allowed_tags() ) . '</p>';

			echo '</div>';
		}
	}

	/**
	 * Add admin notice to let you know the Facebook access token has expired.
	 */
	function dpsp_admin_notice_facebook_access_token_expired() {
		// Do not display this notice if user cannot activate plugins
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$facebook_access_token = Settings::get_setting( 'dpsp_facebook_access_token' );

		// Do not display the notice if the access token is missing
		if ( empty( $facebook_access_token['access_token'] ) || empty( $facebook_access_token['expires_in'] ) ) {
			return;
		}

		// Do not display the notice if the token isn't expired
		if ( time() < absint( $facebook_access_token['expires_in'] ) ) {
			return;
		}

		$settings = Settings::get_setting( 'dpsp_settings', [] );

		// Do not display the notice if the Facebook share count provider isn't set to Grow Social by Mediavine's app
		if ( ! empty( $settings['facebook_share_counts_provider'] ) && 'authorized_app' !== $settings['facebook_share_counts_provider'] ) {
			return;
		}

		$branding = \Social_Pug::get_branding_name();

		// Echo the admin notice
		echo '<div class="dpsp-admin-notice notice notice-error">';
		// translators: %s Branding name, free or pro version
		echo '<h4>' . sprintf( esc_html__( '%s Important Notification', 'social-pug' ), esc_html( $branding ) ) . '</h4>';
		// translators: %s Branding name, free or pro version
		echo '<p>' . sprintf( esc_html__( 'Your %s Facebook app authorization has expired. Please reauthorize the app for continued Facebook share counts functionality.', 'social-pug' ), esc_html( $branding ) ) . '</p>';
		echo '<p><a class="dpsp-button-primary" href="' . esc_url( add_query_arg( [ 'page' => 'dpsp-settings' ], admin_url( 'admin.php' ) ) ) . '#dpsp-card-misc">' . esc_html__( 'Reauthorize Grow Social by Mediavine App', 'social-pug' ) . '</a></p>';
		echo '</div>';
	}

	/**
	 * Add admin notice to anounce the removal of Google+.
	 */
	function dpsp_admin_notice_google_plus_removal() {
		// Do not display this notice if user cannot activate plugins
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Don't show this if the plugin has been activated after 4th of October 2019
		if ( $this->was_first_activation_after( '2019-04-10 00:00:00' ) ) {
			return;
		}

		// Do not display this notice for users that have dismissed it
		if ( '' !== get_user_meta( get_current_user_id(), 'dpsp_admin_notice_google_plus_removal', true ) ) {
			return;
		}

		$branding = \Social_Pug::get_branding_name();

		// Echo the admin notice
		echo '<div class="dpsp-admin-notice notice notice-error">';
		// translators: %s Branding name, free or pro version
		echo '<h4>' . sprintf( esc_html__( '%s Important Notification', 'social-pug' ), esc_html( $branding ) ) . '</h4>';
		// translators: %s Branding name, free or pro version
		echo '<p>' . sprintf( esc_html__( 'As you may already know, Google+ has shut down on April 2nd. As a result, with this latest update, %s no longer supports Google+ functionality.', 'social-pug' ), esc_html( $branding ) ) . '</p>';
		echo '<p>' . esc_html__( 'Please make sure to verify your settings, your widgets, your shortcodes, and remove any Google+ buttons you may have placed within your website.', 'social-pug' ) . '</p>';
		echo '<p><a href="' . esc_attr( add_query_arg( [ 'dpsp_admin_notice_google_plus_removal' => 1 ] ) ) . '">' . esc_html__( 'Thank you, I understand.', 'social-pug' ) . '</a></p>';
		echo '</div>';
	}

	/**
	 * Add admin notice to announce the name change.
	 */
	function dpsp_admin_notice_grow_name_change() {
		// Do not display this notice if user cannot activate plugins
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Don't show this if the plugin has been activated after 30th of November 2019
		if ( $this->was_first_activation_after( '2019-11-30 00:00:00' ) ) {
			return;
		}

		// Do not display this notice for users that have dismissed it
		if ( '' !== get_user_meta( get_current_user_id(), 'dpsp_admin_notice_grow_name_change', true ) ) {
			return;
		}

		// Echo the admin notice
		echo '<div class="dpsp-admin-notice dpsp-admin-grow-notice notice notice-info">';
		echo '<div class="notice-img-wrap" >';
		echo '<img src="' . esc_url( DPSP_PLUGIN_DIR_URL . 'assets/dist/grow-logo-sq-navy.' . DPSP_VERSION . '.png' ) . '" />';
		echo '</div>';
		echo '<div class="notice-text-wrap">';
		echo '<h4>' . esc_html__( 'Social Pug is now Grow Social by Mediavine!', 'social-pug' ) . '</h4>';
		echo '<p>' . esc_html__( 'You\'re going to notice some new paint and a new name today and we wanted to let you know what that\'s all about. The short version is that Grow Social by Mediavine is the same plugin you know and love but with a new, larger development team!', 'social-pug' ) . '</p>';
		echo '<p><a href="https://www.mediavine.com/social-pug-is-now-grow-mediavines-new-social-sharing-buttons-marketplace-more/" target="_blank">' . esc_html__( 'Check out the blog post', 'social-pug' ) . '</a>' . esc_html__( ' for all the details on this development and our exciting plans to continue Growing an already awesome plugin.', 'social-pug' ) . '</p>';
		echo '<p class="notice-subtext">' . esc_html__( '(Those who are familiar with Mediavine for our full-service ad management, rest assured that this plugin is totally independent of ads and available to anyone and everyone who wants to Grow their social presence.)', 'social-pug' ) . '</p>';
		echo '<p><a href="' . esc_attr( add_query_arg( [ 'dpsp_admin_notice_grow_name_change' => 1 ] ) ) . '">' . esc_html__( 'Thank you, I understand.', 'social-pug' ) . '</a></p>';
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Add admin notice to announce the deprecation of jQuery JS.
	 */
	function dpsp_admin_notice_jquery_deprecation() {
		// Do not display this notice if user cannot activate plugins
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Don't show this if the plugin has been activated after February 28 2021
		if ( $this->was_first_activation_after( '2021-02-28 00:00:00' ) ) {
			return;
		}

		// Do not display this notice for users that have dismissed it
		if ( '' !== get_user_meta( get_current_user_id(), 'dpsp_admin_notice_optimized_js', true ) ) {
			return;
		}

		$notice =
			__( '<strong>Your Grow Social Pro by Mediavine JavaScript has been optimized!</strong>', 'social-pug' ) .
			'</p><p>' .
			__( 'The Legacy jQuery version of our JavaScript is now deprecated, and all users have been switched to the Optimized JavaScipt. ', 'social-pug' ) .
			sprintf(
				// translators: Link to contact Mediavine
				__( 'If you find an issue with the optimized settings where you need to revert to the jQuery JavaScript, %s. ', 'social-pug' ),
				'<a href="mailto:grow@mediavine.com">' . __( 'please contact Mediavine', 'social-pug' ) . '</a>'
			) .
			sprintf(
				// translators: Link to settings
				__( 'You can revert the JavaScript so on the %s.', 'social-pug' ),
				'<a href="' . admin_url( 'admin.php?page=dpsp-settings' ) . '">' . __( 'Grow Settings page under Misc', 'social-pug' ) . '</a>'
			) .
			'</p><p>' .
			__( '<strong>After July 2021, the jQuery JavaScript will be removed completely.</strong>', 'social-pug' ) .
			'</p><p>' .
			'<a href="' . esc_attr( add_query_arg( [ 'dpsp_admin_notice_optimized_js' => 1 ] ) ) . '">' . __( 'Thank you, I understand.', 'social-pug' ) . '</a>';

		mv_grow_admin_error_notice( $notice, 'warning dpsp-admin-notice is-dismissible' );
	}

	/**
	 * Add admin notice for initial setup help documentation
	 */
	function dpsp_admin_notice_initial_setup_nag() {
		// Do not display this notice if user cannot activate plugins
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Do not display this notice after it has been dismissed
		if ( 'yes' !== Settings::get_setting( 'dpsp_run_setup_info_nag', 'no' ) ) {
			return;
		}

		// Echo the admin notice
		echo '<div class="dpsp-admin-notice notice notice-info">';
		echo '<a class="notice-dismiss" href="' . esc_attr( add_query_arg( [ 'dpsp_admin_notice_initial_setup_nag' => 1 ] ) ) . '"></a>';
		echo '<h4>' . esc_html__( 'Grow Social by Mediavine Notification', 'social-pug' ) . '</h4>';
		echo '<p>' . esc_html__( 'Looking to get started with Grow Social? Click the button below for a step by step guide to setting everything up!', 'social-pug' ) . '</p>';
		echo '<p><a class="dpsp-button-primary" target="_blank" href="https://product-help.mediavine.com/en/articles/4868647-getting-started-with-grow-social-and-grow-social-pro">' . esc_html__( 'Learn how to set up Grow Social', 'social-pug' ) . '</a></p>';
		echo '</div>';
	}

	/**
	 * Adds an option on first install so initial admin notice is displayed.
	 */
	function dpsp_setup_activation_notices() {
		update_option( 'dpsp_run_setup_info_nag', 'yes' );
	}

	/**
	 * Handle admin notices dismissals.
	 */
	function dpsp_admin_notice_dismiss() {
		if ( ! empty( filter_input( INPUT_GET, 'dpsp_admin_notice_twitter_counts' ) ) ) {
			add_user_meta( get_current_user_id(), 'dpsp_admin_notice_twitter_counts', 1, true );
		}

		if ( ! empty( filter_input( INPUT_GET, 'dpsp_admin_notice_renew_1' ) ) ) {
			add_user_meta( get_current_user_id(), 'dpsp_admin_notice_renew_1', 1, true );
		}

		if ( ! empty( filter_input( INPUT_GET, 'dpsp_admin_notice_recovery_system' ) ) ) {
			add_user_meta( get_current_user_id(), 'dpsp_admin_notice_recovery_system', 1, true );
		}

		if ( ! empty( filter_input( INPUT_GET, 'dpsp_admin_notice_major_update_2_6_0' ) ) ) {
			add_user_meta( get_current_user_id(), 'dpsp_admin_notice_major_update_2_6_0', 1, true );
		}

		if ( ! empty( filter_input( INPUT_GET, 'dpsp_admin_notice_google_plus_removal' ) ) ) {
			add_user_meta( get_current_user_id(), 'dpsp_admin_notice_google_plus_removal', 1, true );
		}

		if ( ! empty( filter_input( INPUT_GET, 'dpsp_admin_notice_grow_name_change' ) ) ) {
			add_user_meta( get_current_user_id(), 'dpsp_admin_notice_grow_name_change', 1, true );
		}

		if ( ! empty( filter_input( INPUT_GET, 'dpsp_admin_notice_optimized_js' ) ) ) {
			add_user_meta( get_current_user_id(), 'dpsp_admin_notice_optimized_js', 1, true );
		}

		if ( ! empty( filter_input( INPUT_GET, 'dpsp_admin_notice_initial_setup_nag' ) ) ) {
			update_option( 'dpsp_run_setup_info_nag', 'no' );
		}
	}

	/**
	 * Remove dpsp query args from the URL.
	 *
	 * @param array $removable_query_args The args that WP will remove
	 * @return array
	 */
	function dpsp_removable_query_args( $removable_query_args ) {
		$new_args = [ 'dpsp_message_id', 'dpsp_message_class', 'dpsp_admin_notice_dismiss_button_icon_animation', 'dpsp_admin_notice_activate_button_icon_animation', 'dpsp_admin_notice_activate_button_icon_animation_done' ];

		return array_merge( $new_args, $removable_query_args );
	}

	/**
	 * Notify users of their current license status, if available, while on the Grow Social settings page.
	 */
	public function notify_license_status() : void {
		$screen = get_current_screen();
		if ( ! ( $screen instanceof WP_Screen ) || 'grow_page_dpsp-settings' !== $screen->id ) {
			return;
		}

		$license_status      = get_option( Activation::OPTION_LICENSE_STATUS );
		$license_status_date = get_option( Activation::OPTION_LICENSE_STATUS_DATE );

		if ( ! $license_status ) {
			return;
		}

		switch ( $license_status ) {
			case Activation::LICENSE_STATUS_VALID:
				$notice_type    = Admin_Messages::MESSAGE_TYPE_SUCCESS;
				$license_notice = __( 'The Grow Social Pro license is valid.', 'mediavine' );
				break;
			case Activation::LICENSE_STATUS_INVALID:
				$notice_type    = Admin_Messages::MESSAGE_TYPE_ERROR;
				$license_notice = __( 'The Grow Social Pro license is not valid.', 'mediavine' );
				break;
			case Activation::LICENSE_STATUS_EXPIRED:
				$notice_type    = Admin_Messages::MESSAGE_TYPE_WARNING;
				$license_notice = __( 'The Grow Social Pro license has expired.', 'mediavine' );
				break;
			default:
				return;
		}

		if ( $license_status_date && filter_var( $license_status_date, FILTER_VALIDATE_INT ) ) {
			$date_format                   = get_option( 'date_format', 'F j, Y' );
			$license_status_date_formatted = gmdate( $date_format, $license_status_date );
			$license_notice                = sprintf(
				// translators: %1$s is the license status message, %2$s is the last-checked date.
				__( '%1$s Last checked %2$s.', 'mediavine' ),
				$license_notice,
				$license_status_date_formatted
			);
		}

		mv_grow_admin_error_notice( $license_notice, $notice_type );
	}
}
