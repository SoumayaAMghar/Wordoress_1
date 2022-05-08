<?php
// If this file is called directly, abort.
if(!defined('ABSPATH')) {
	exit;
}
?>
<div id="vision-modal-{{ modalData.id }}" class="vision-modal" tabindex="-1">
	<div class="vision-modal-dialog">
		<div class="vision-modal-header">
			<div class="vision-modal-close" al-on.click="modalData.deferred.resolve('close');">&times;</div>
			<div class="vision-modal-title"><?php esc_html_e('Select a show effect', VISION_PLUGIN_NAME); ?></div>
		</div>
		<div class="vision-modal-data">
			<div class="vision-modal-effects">
			<div class="vision-modal-group">
				<div class="vision-modal-title">General</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-bounce">Bounce</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-pulse">Pulse</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-rubberBand">Rubber Band</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-shake">Shake</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-headShake">Head Shake</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-swing">Swing</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-tada">Tada</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-wobble">Wobble</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-jello">Jello</div>
				</div>
			</div>
			
			<div class="vision-modal-group">
				<div class="vision-modal-title">Bounce</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-bounceIn">BounceIn</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-bounceInDown">BounceInDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-bounceInLeft">BounceInLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-bounceInRight">BounceInRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-bounceInUp">BounceInUp</div>
				</div>
			</div>
			
			<div class="vision-modal-group">
				<div class="vision-modal-title">Fade</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-fadeIn">FadeIn</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-fadeInDown">FadeInDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-fadeInLeft">FadeInLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-fadeInRight">FadeInRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-fadeInUp">FadeInUp</div>
				</div>
			</div>
			
			<div class="vision-modal-group">
				<div class="vision-modal-title">Rotate</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-rotateIn">RotateIn</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-rotateInDownLeft">RotateInDownLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-rotateInDownRight">RotateInDownRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-rotateInUpLeft">RotateInUpLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-rotateInUpRight">RotateInUpRight</div>
				</div>
			</div>
			
			<div class="vision-modal-group">
				<div class="vision-modal-title">Zoom</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-zoomIn">ZoomIn</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-zoomInDown">ZoomInDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-zoomInLeft">ZoomInLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-zoomInRight">ZoomInRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-zoomInUp">ZoomInUp</div>
				</div>
			</div>
			
			<div class="vision-modal-group">
				<div class="vision-modal-title">Slide</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-slideInDown">SlideInDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-slideInLeft">SlideInLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-slideInRight">SlideInRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-slideInUp">SlideInUp</div>
				</div>
			</div>
			
			<div class="vision-modal-group">
				<div class="vision-modal-title">Perspective</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-perspectiveInDown">PerspectiveInDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-perspectiveInLeft">PerspectiveInLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-perspectiveInRight">PerspectiveInRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-perspectiveInUp">PerspectiveInUp</div>
				</div>
			</div>
	
			<div class="vision-modal-group">
				<div class="vision-modal-title">Tin</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-tinInDown">TinInDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-tinInLeft">TinInLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-tinInRight">TinInRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-tinInUp">TinInUp</div>
				</div>
			</div>
			
			<div class="vision-modal-group">
				<div class="vision-modal-title">Space</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-spaceInDown">SpaceInDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-spaceInLeft">SpaceInLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-spaceInRight">SpaceInRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-spaceInUp">SpaceInUp</div>
				</div>
			</div>
			
			<div class="vision-modal-group">
				<div class="vision-modal-title">Flip</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-flip">Flip</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-flipInX">FlipInX</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-flipInY">FlipInY</div>
				</div>
			</div>
			
			<div class="vision-modal-group">
				<div class="vision-modal-title">Advanced</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-lightSpeedIn">LightSpeedIn</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-rollIn">RollIn</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-vanishIn">VanishIn</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-swashIn">SwashIn</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-foolishIn">FoolishIn</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-holeIn">HoleIn</div>
				</div>
			</div>
			</div>
		</div>
		<div class="vision-modal-footer">
			<div class="vision-modal-text"><?php esc_html_e('Selected effect:', VISION_PLUGIN_NAME); ?> <b>{{modalData.selectedClass}}</b></div>
			<div class="vision-modal-btn vision-modal-btn-close" al-on.click="modalData.deferred.resolve('close');"><?php esc_html_e('Close', VISION_PLUGIN_NAME); ?></div>
			<div class="vision-modal-btn vision-modal-btn-create" al-on.click="modalData.deferred.resolve(true);"><?php esc_html_e('OK', VISION_PLUGIN_NAME); ?></div>
		</div>
	</div>
</div>