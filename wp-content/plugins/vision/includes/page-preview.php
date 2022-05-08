<?php

// If this file is called directly, abort.
if(!defined('ABSPATH')) {
	exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<?php $this->do_preview_head(); ?>
</head>
<body>
<div class="vision-preview-wrap">
	<div class="vision-preview-header">
		<div class="vision-preview-btn vision-preview-image" data-device="image" title="original image size"></div>
		<div class="vision-preview-btn vision-preview-desktop" data-device="desktop" title="desktop"></div>
		<div class="vision-preview-btn vision-preview-tablet" data-device="tablet" title="tablet"></div>
		<div class="vision-preview-btn vision-preview-mobile" data-device="mobile" title="mobile"></div>
	</div>
	<div class="vision-preview-workspace">
		<div id="vision-preview-canvas" class="vision-preview-canvas">
			<?php $this->do_preview(); ?>
		</div>
	</div>
</div>
<?php $this->do_preview_footer(); ?>
</body>
</html>