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
			<div class="vision-modal-title"><?php esc_html_e('Select a hide effect', VISION_PLUGIN_NAME); ?></div>
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
					<div class="vision-modal-effect" data-fx-name="vision-fx-hinge">Hinge</div>
				</div>
			</div>
			
			<div class="vision-modal-group">
				<div class="vision-modal-title">Bounce</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-bounceOut">BounceOut</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-bounceOutDown">BounceOutDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-bounceOutLeft">BounceOutLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-bounceOutRight">BounceOutRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-bounceOutUp">BounceOutUp</div>
				</div>
			</div>

			<div class="vision-modal-group">
				<div class="vision-modal-title">Fade</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-fadeOut">fadeOut</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-fadeOutDown">fadeOutDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-fadeOutLeft">fadeOutLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-fadeOutRight">fadeOutRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-fadeOutUp">fadeOutUp</div>
				</div>
			</div>

			<div class="vision-modal-group">
				<div class="vision-modal-title">Rotate</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-rotateOut">rotateOut</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-rotateOutDownLeft">rotateOutDownLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-rotateOutDownRight">rotateOutDownRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-rotateOutUpLeft">rotateOutUpLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-rotateOutUpRight">rotateOutUpRight</div>
				</div>
			</div>

			<div class="vision-modal-group">
				<div class="vision-modal-title">Zoom</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-zoomOut">zoomOut</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-zoomOutDown">zoomOutDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-zoomOutLeft">zoomOutLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-zoomOutRight">zoomOutRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-zoomOutUp">zoomOutUp</div>
				</div>
			</div>

			<div class="vision-modal-group">
				<div class="vision-modal-title">Slide</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-slideOutDown">slideOutDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-slideOutLeft">slideOutLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-slideOutRight">slideOutRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-slideOutUp">slideOutUp</div>
				</div>
			</div>

			<div class="vision-modal-group">
				<div class="vision-modal-title">Perspective</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-perspectiveOutDown">perspectiveOutDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-perspectiveOutLeft">perspectiveOutLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-perspectiveOutRight">perspectiveOutRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-perspectiveOutUp">perspectiveOutUp</div>
				</div>
			</div>

			<div class="vision-modal-group">
				<div class="vision-modal-title">Tin</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-tinOutDown">tinOutDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-tinOutLeft">tinOutLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-tinOutRight">tinOutRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-tinOutUp">tinOutUp</div>
				</div>
			</div>

			<div class="vision-modal-group">
				<div class="vision-modal-title">Space</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-spaceOutDown">spaceOutDown</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-spaceOutLeft">spaceOutLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-spaceOutRight">spaceOutRight</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-spaceOutUp">spaceOutUp</div>
				</div>
			</div>

			<div class="vision-modal-group">
				<div class="vision-modal-title">Flip</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-flip">Flip</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-flipOutX">flipOutX</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-flipOutY">flipOutY</div>
				</div>
			</div>

			<div class="vision-modal-group">
				<div class="vision-modal-title">Advanced</div>
				<div class="vision-modal-btn-group">
					<div class="vision-modal-effect" data-fx-name="vision-fx-lightSpeedOut">LightSpeedOut</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-rollOut">RollOut</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-vanishOut">VanishOut</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-swashOut">SwashOut</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-foolishOut">FoolishOut</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-holeOut">HoleOut</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-bombOutLeft">BombOutLeft</div>
					<div class="vision-modal-effect" data-fx-name="vision-fx-bombOutRight">BombOutRight</div>
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