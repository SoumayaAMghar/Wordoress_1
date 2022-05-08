<div class="dpsp-page-wrapper dpsp-page-settings wrap">

	<h1 class="dpsp-page-title"><?php esc_html_e( 'Settings', 'social-pug' ); ?></h1>

	<form method="post" action="options.php">
		<?php
		$dpsp_settings = get_option( 'dpsp_settings', 'not_set' );
		settings_fields( 'dpsp_settings' );
		?>

		<!-- General Settings Tab Content -->
		<div id="dpsp-tab-general-settings">

			<div class="dpsp-card">

				<div class="dpsp-card-header">
					<?php esc_html_e( 'Social Identity', 'social-pug' ); ?>
				</div>

				<div class="dpsp-card-inner">

					<?php dpsp_settings_field( 'text', 'dpsp_settings[twitter_username]', ( isset( $dpsp_settings['twitter_username'] ) ? $dpsp_settings['twitter_username'] : '' ), __( 'Twitter Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'switch', 'dpsp_settings[tweets_have_username]', ( isset( $dpsp_settings['tweets_have_username'] ) ? $dpsp_settings['tweets_have_username'] : '' ), __( 'Add Twitter Username to all tweets', 'social-pug' ), [ 'yes' ] ); ?>

				</div>

			</div>

			<!-- Misc -->
			<div id="dpsp-card-misc" class="dpsp-card">

				<div class="dpsp-card-header">
					<?php esc_html_e( 'Misc', 'social-pug' ); ?>
				</div>

				<div class="dpsp-card-inner">

					<?php
					dpsp_settings_field(
						'select', 'dpsp_settings[facebook_share_counts_provider]', ( isset( $dpsp_settings['facebook_share_counts_provider'] ) ? $dpsp_settings['facebook_share_counts_provider'] : '' ), __( 'Facebook Share Counts Provider', 'social-pug' ), [
							'authorized_app' => __( 'Grow by Mediavine App', 'social-pug' ),
							'own_app'        => __( 'Facebook Graph API', 'social-pug' ),
						]
					);
?>

					<div class="dpsp-setting-field-wrapper dpsp-setting-field-text dpsp-has-field-label dpsp-setting-field-facebook-authorize-app">

						<?php $facebook_access_token = Mediavine\Grow\Settings::get_setting( 'dpsp_facebook_access_token' ); ?>

						<?php if ( ! empty( $facebook_access_token['access_token'] ) && ! empty( $facebook_access_token['expires_in'] ) ) : ?>

							<?php if ( time() < $facebook_access_token['expires_in'] ) : ?>

								<div class="dpsp-setting-field-facebook-app-authorized">
									<span class="dashicons dashicons-yes"></span>
									<strong><?php esc_html_e( 'Authorized', 'social-pug' ); ?></strong>
									<?php
									// translators: %s
									echo wp_kses_post( '- ' . sprintf( __( 'Expires on %s', 'social-pug' ), date( 'F d, Y', absint( $facebook_access_token['expires_in'] ) ) ) );
									?>
								</div>

							<?php else : ?>

								<div class="dpsp-setting-field-facebook-app-authorized-expired">
									<span class="dashicons dashicons-warning"></span>
									<strong><?php esc_html_e( 'Authorization Expired', 'social-pug' ); ?></strong>
									<?php esc_html_e( '- Please reauthorize.', 'social-pug' ); ?>
								</div>
								<?php
								$api_url = add_query_arg(
									[
										'action'     => 'authorize_facebook_app_free',
										'referer'    => home_url(),
										'tkn'        => wp_create_nonce( 'dpsp_authorize_facebook_app' ),
										'client_url' => urlencode( add_query_arg( [ 'page' => 'dpsp-settings' ], admin_url( 'admin.php' ) ) ), // @codingStandardsIgnoreLine
									], 'http://apitest.devpups.com/1.0/'
								);
								?>
								<a class="dpsp-button-primary" href="<?php echo esc_url( $api_url ); ?>"><?php esc_html_e( 'Reauthorize Grow', 'social-pug' ); ?></a>

							<?php endif; ?>

						<?php else : ?>
							<?php
							$api_url =
									add_query_arg(
										[
											'action'     => 'authorize_facebook_app_free',
											'referer'    => home_url(),
											'tkn'        => wp_create_nonce( 'dpsp_authorize_facebook_app' ),
											'client_url' => urlencode( add_query_arg( [ 'page' => 'dpsp-settings' ], admin_url( 'admin.php' ) ) ), // @codingStandardsIgnoreLine
										], 'http://apitest.devpups.com/1.0/'
									);
							?>
							<a class="dpsp-button-primary" href="<?php echo esc_url( $api_url ); ?>"><?php esc_html_e( 'Authorize Grow', 'social-pug' ); ?></a>

						<?php endif; ?>

					</div>

					<?php dpsp_settings_field( 'text', 'dpsp_settings[facebook_app_id]', ( isset( $dpsp_settings['facebook_app_id'] ) ? $dpsp_settings['facebook_app_id'] : '' ), __( 'Facebook App ID', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[facebook_app_secret]', ( isset( $dpsp_settings['facebook_app_secret'] ) ? $dpsp_settings['facebook_app_secret'] : '' ), __( 'Facebook App Secret', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'switch', 'dpsp_settings[disable_meta_tags]', ( isset( $dpsp_settings['disable_meta_tags'] ) ? $dpsp_settings['disable_meta_tags'] : '' ), __( 'Disable Open Graph Meta Tags', 'social-pug' ), [ 'yes' ] ); ?>

				</div>

			</div>

		</div><!-- End of General Settings Tab Content -->

		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="dpsp_settings[always_update]" value="<?php echo ( isset( $dpsp_settings['always_update'] ) && 1 === $dpsp_settings['always_update'] ? 0 : 1 ); ?>" />
		<p class="submit"><input type="submit" class="dpsp-button-primary" value="<?php esc_html_e( 'Save Changes' ); ?>" /></p>
	</form>
</div>

<?php do_action( 'dpsp_submenu_page_bottom' ); ?>
