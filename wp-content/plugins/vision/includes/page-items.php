<?php
// If this file is called directly, abort.
if(!defined('ABSPATH')) {
	exit;
}

$list_table = new Vision_List_Table_Items();
$list_table->prepare_items();

?>
<!-- vision app -->
<div class="vision-root" id="vision-app-items">
	<?php require 'page-info.php'; ?>
	<div class="vision-page-header">
		<div class="vision-title"><?php esc_html_e('Vision Items', VISION_PLUGIN_NAME); ?></div>
		<div class="vision-actions">
			<a class="vision-blue" href="?page=<?php echo VISION_PLUGIN_NAME . '_item'; ?>" title="<?php esc_html_e('Create a new item', VISION_PLUGIN_NAME); ?>"><?php _e('Add Item', VISION_PLUGIN_NAME); ?></a>
		</div>
	</div>
	<div class="vision-app">
		<?php $list_table->views(); ?>
		<form method="post">
			<?php $list_table->search_box(esc_html__('Search Items', VISION_PLUGIN_NAME),'item'); ?>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>">
			<?php $list_table->display() ?>
		</form>
	</div>
</div>
<!-- /end vision app -->