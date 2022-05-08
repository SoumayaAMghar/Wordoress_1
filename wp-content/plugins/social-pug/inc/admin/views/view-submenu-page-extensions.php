<div class="dpsp-page-wrapper dpsp-page-extensions wrap">

	<h1 class="dpsp-page-title"><?php esc_html_e( 'All Social Share Tools in One Plugin', 'social-pug' ); ?></h1>

	<p><?php _e( 'Get <a href="https://marketplace.mediavine.com/grow-social-pro/" target="_blank">Grow Social Pro by Mediavine</a> to have access to even more tools and offer your users a beautiful social sharing experience.', 'social-pug' ); // @codingStandardsIgnoreLine - no user-entered content that needs escaping ?></p>

	<p><?php _e( 'To gain immediate access to the tools below, <a href="https://marketplace.mediavine.com/grow-social-pro/" target="_blank">have a look at our pricing.</a>', 'social-pug' ); // @codingStandardsIgnoreLine - no user-entered content that needs escaping ?></p>

	<div class="dpsp-row dpsp-m-padding">
	<?php
		$tools = [];

		$tools['premium_networks'] = [
			'name' => __( 'Social Networks Pack', 'social-pug' ),
			'img'  => 'assets/dist/extension-networks.' . DPSP_VERSION . '.png',
			'desc' => __( 'Take advantage of all the social networks available.', 'social-pug' ),
			'url'  => 'https://marketplace.mediavine.com/grow-social-pro/?utm_source=plugin-extensions&amp;utm_medium=social-networks-pack&amp;utm_campaign=social-pug#social-share-buttons',
		];

		$tools['share_mobile'] = [
			'name' => __( 'Share Mobile Sticky', 'social-pug' ),
			'img'  => 'assets/dist/tool-mobile.' . DPSP_VERSION . '.png',
			'desc' => __( 'Add a mobile sticky share footer to your posts and pages.', 'social-pug' ),
			'url'  => 'https://marketplace.mediavine.com/grow-social-pro/?utm_source=plugin-extensions&amp;utm_medium=share-mobile-sticky&amp;utm_campaign=social-pug#share-mobile-sticky',
		];

		$tools['share_pop_up'] = [
			'name' => __( 'Share Pop-Up', 'social-pug' ),
			'img'  => 'assets/dist/tool-pop-up.' . DPSP_VERSION . '.png',
			'desc' => __( 'Add a simple share pop-up that has custom triggers.', 'social-pug' ),
			'url'  => 'https://marketplace.mediavine.com/grow-social-pro/?utm_source=plugin-extensions&amp;utm_medium=share-pop-up&amp;utm_campaign=social-pug#share-pop-up',
		];

		$tools['share_image'] = [
			'name' => __( 'Image Hover Pinterest Button', 'social-pug' ),
			'img'  => 'assets/dist/tool-image-hover-pinterest.' . DPSP_VERSION . '.png',
			'desc' => __( 'Add a Pinterest button to your single posts images when a user hovers on them.', 'social-pug' ),
			'url'  => 'https://marketplace.mediavine.com/grow-social-pro/?utm_source=plugin-extensions&amp;utm_medium=share-image&amp;utm_campaign=social-pug#share-pinterest-hover',
		];

		$tools['follow_widget'] = [
			'name' => __( 'Follow Buttons Widget', 'social-pug' ),
			'img'  => 'assets/dist/tool-follow-widget.' . DPSP_VERSION . '.png',
			'desc' => __( 'Link your social profiles with the help of the follow buttons.', 'social-pug' ),
			'url'  => 'https://marketplace.mediavine.com/grow-social-pro/?utm_source=plugin-extensions&amp;utm_medium=follow-buttons-widget&amp;utm_campaign=social-pug#social-share-buttons',
		];

		$tools['click_to_tweet'] = [
			'name' => __( 'Click to Tweet', 'social-pug' ),
			'img'  => 'assets/dist/extension-ctt.' . DPSP_VERSION . '.png',
			'desc' => __( 'Add custom tweetable quotes anywhere in your content.', 'social-pug' ),
			'url'  => 'https://marketplace.mediavine.com/grow-social-pro/?utm_source=plugin-extensions&amp;utm_medium=click-to-tweet&amp;utm_campaign=social-pug#sharable-quotes',
		];

		$tools['branch_shortening'] = [
			'name' => __( 'Branch Integration', 'social-pug' ),
			'img'  => 'assets/dist/extension-branch.' . DPSP_VERSION . '.png',
			'desc' => __( 'Shorten share links with the help of Branch.', 'social-pug' ),
			'url'  => 'https://marketplace.mediavine.com/grow-social-pro/?utm_source=plugin-extensions&amp;utm_medium=share-branch&amp;utm_campaign=social-pug#share-bitly-ga',
		];

		$tools['bitly_shortening'] = [
			'name' => __( 'Bitly Integration', 'social-pug' ),
			'img'  => 'assets/dist/extension-bitly.' . DPSP_VERSION . '.png',
			'desc' => __( 'Shorten share links with the help of Bitly.', 'social-pug' ),
			'url'  => 'https://marketplace.mediavine.com/grow-social-pro/?utm_source=plugin-extensions&amp;utm_medium=share-bitly&amp;utm_campaign=social-pug#share-bitly-ga',
		];

		$tools['ga_utm_tracking'] = [
			'name' => __( 'Analytics UTM Tracking', 'social-pug' ),
			'img'  => 'assets/dist/extension-ga-utm-tracking.' . DPSP_VERSION . '.png',
			'desc' => __( 'Track shared links with the help of the UTM parameters.', 'social-pug' ),
			'url'  => 'https://marketplace.mediavine.com/grow-social-pro/?utm_source=plugin-extensions&amp;utm_medium=share-utm-tracking&amp;utm_campaign=social-pug#share-bitly-ga',
		];

		foreach ( $tools as $tool_slug => $tool ) {
			dpsp_output_tool_box( $tool_slug, $tool );
		}
	?>
	</div><!-- End of Share Tools -->


	<?php
	/*
	<h1 class="dpsp-page-title" style="margin-top: 25px;"><?php esc_html_e( 'Recommended Plugins', 'social-pug' ); ?></h1>

	<div class="dpsp-row dpsp-m-padding">
	<?php
		$tools = array();

		$tools['premium_networks'] = array(
			'name' 		 		 => __( 'SkyePress - Auto Post and Schedule to Social Media', 'social-pug' ),
			'img'		 		 => 'assets/dist/skyepress-social-pug-promo.' . DPSP_VERSION . '.png',
			'desc'				 => __( 'Auto Post to your Twitter, Facebook and LinkedIn profiles and much more...', 'social-pug' ),
			'url'				 => admin_url( 'admin.php?page=dpsp-extensions&sub-page=skyepress' )
		);

		foreach( $tools as $tool_slug => $tool )
			dpsp_output_tool_box( $tool_slug, $tool );
	?>
	</div><!-- End of Our Plugins -->
	*/
?>

</div>
