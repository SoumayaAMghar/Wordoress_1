<?php

// If this file is called directly, abort.
if(!defined('ABSPATH')) {
	exit;
}

$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED );
$author = get_the_author_meta('display_name', $item->author);
$editor = get_the_author_meta('display_name', $item->editor);
$created = mysql2date(get_option('date_format'), $item->created) . ' at ' . mysql2date(get_option('time_format'), $item->created);
$modified = mysql2date(get_option('date_format'), $item->modified) . ' at ' . mysql2date(get_option('time_format'), $item->modified);

?>
<!-- /begin vision app -->
<div class="vision-root" id="vision-app-item" style="display:none;">
	<?php require 'page-info.php'; ?>
	<input id="vision-load-config-from-file" type="file" style="display:none;" />
	<div class="vision-page-header">
		<div class="vision-title"><?php esc_html_e('Vision Item', VISION_PLUGIN_NAME); ?></div>
		<div class="vision-actions">
			<a class="vision-blue" href="?page=<?php echo VISION_PLUGIN_NAME . '_item'; ?>" title="<?php esc_html_e('Create a new item', VISION_PLUGIN_NAME); ?>"><?php esc_html_e('Add Item', VISION_PLUGIN_NAME); ?></a>
			<a class="vision-indigo" href="#" al-on.click="appData.fn.saveConfigToFile(appData)" title="<?php esc_html_e('Save config to a JSON file', VISION_PLUGIN_NAME); ?>"><?php esc_html_e('Save As...', VISION_PLUGIN_NAME); ?></a>
			<a class="vision-green" href="#" al-on.click="appData.fn.loadConfigFromFile(appData)" title="<?php esc_html_e('Load config from a JSON file', VISION_PLUGIN_NAME); ?>"><?php esc_html_e('Load As...', VISION_PLUGIN_NAME); ?></a>
		</div>
	</div>
	<div class="vision-messages" id="vision-messages">
	</div>
	<div class="vision-app" id="vision-app">
		<div class="vision-loader-wrap">
			<div class="vision-loader">
				<div class="vision-loader-bar"></div>
				<div class="vision-loader-bar"></div>
				<div class="vision-loader-bar"></div>
				<div class="vision-loader-bar"></div>
			</div>
		</div>
		<div class="vision-wrap">
			<div class="vision-main-header">
				<input class="vision-title" type="text" al-text="appData.config.title" placeholder="<?php esc_html_e('Title', VISION_PLUGIN_NAME); ?>">
			</div>
			<div class="vision-workplace">
				<div class="vision-main-menu">
					<div class="vision-left-panel">
						<div class="vision-list">
							<a class="vision-item vision-small vision-lite" href="https://1.envato.market/LXAjj" target="_blank" al-if="appData.plan=='lite'"><?php esc_html_e('Buy pro version', VISION_PLUGIN_NAME); ?></a>
							<a class="vision-item vision-small vision-pro" href="https://1.envato.market/LXAjj" target="_blank" al-if="appData.plan=='pro'"><?php esc_html_e('Pro Version', VISION_PLUGIN_NAME); ?></a>
						</div>
					</div>
					<div class="vision-right-panel">
						<div class="vision-list">
							<div class="vision-item vision-green" al-on.click="appData.fn.preview(appData);" title="<?php esc_html_e('The item should be saved before preview', VISION_PLUGIN_NAME); ?>" al-if="appData.wp_item_id != null"><?php esc_html_e('Preview', VISION_PLUGIN_NAME); ?></div>
							<div class="vision-item vision-blue" al-on.click="appData.fn.saveConfig(appData);" title="<?php esc_html_e('Save config to database', VISION_PLUGIN_NAME); ?>"><?php esc_html_e('Save', VISION_PLUGIN_NAME); ?></div>
						</div>
					</div>
				</div>
				<div class="vision-main-tabs vision-clear-fix">
					<div class="vision-tab" al-attr.class.vision-active="appData.ui.tabs.general" al-on.click="appData.fn.onTab(appData, 'general')"><?php esc_html_e('General', VISION_PLUGIN_NAME); ?><div class="vision-status" al-if="appData.config.active"></div></div>
					<div class="vision-tab" al-attr.class.vision-active="appData.ui.tabs.layers" al-on.click="appData.fn.onTab(appData, 'layers')"><?php esc_html_e('Layers', VISION_PLUGIN_NAME); ?></div>
					<div class="vision-tab" al-attr.class.vision-active="appData.ui.tabs.customCSS" al-on.click="appData.fn.onTab(appData, 'customCSS')"><?php esc_html_e('Custom CSS', VISION_PLUGIN_NAME); ?><div class="vision-status" al-if="appData.config.customCSS.active"></div></div>
					<div class="vision-tab" al-attr.class.vision-active="appData.ui.tabs.customJS" al-on.click="appData.fn.onTab(appData, 'customJS')"><?php esc_html_e('Custom JS', VISION_PLUGIN_NAME); ?><div class="vision-status" al-if="appData.config.customJS.active"></div></div>
					<div class="vision-tab" al-attr.class.vision-active="appData.ui.tabs.shortcode" al-on.click="appData.fn.onTab(appData, 'shortcode')" al-if="appData.wp_item_id"><?php esc_html_e('Shortcode', VISION_PLUGIN_NAME); ?></div>
				</div>
				<div class="vision-main-data">
					<div class="vision-section" al-attr.class.vision-active="appData.ui.tabs.general">
						<div class="vision-stage">
							<div class="vision-main-panel vision-main-panel-general">
								<div class="vision-data vision-active">
									<div class="vision-block" al-attr.class.vision-block-folded="appData.ui.generalTab.main">
										<div class="vision-block-header" al-on.click="appData.fn.onGeneralTab(appData,'main')">
											<div class="vision-block-title"><?php esc_html_e('Main settings', VISION_PLUGIN_NAME); ?></div>
											<div class="vision-block-state"></div>
										</div>
										<div class="vision-block-data">
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('Enable/disable item', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Enable item', VISION_PLUGIN_NAME); ?></div>
												<div al-toggle="appData.config.active"></div>
											</div>
											
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('Sets a main map image (jpeg or png format)', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Map image', VISION_PLUGIN_NAME); ?></div>
												<div class="vision-input-group">
													<div class="vision-input-group-cell">
														<input class="vision-text vision-long vision-no-brr" type="text" al-text="appData.config.image.url" placeholder="<?php esc_html_e('Select an image', VISION_PLUGIN_NAME); ?>">
													</div>
													<div class="vision-input-group-cell vision-pinch">
														<div class="vision-btn vision-default vision-no-bl" al-on.click="appData.fn.selectImage(appData, appData.rootScope, appData.config.image)" title="<?php esc_html_e('Select an image', VISION_PLUGIN_NAME); ?>"><span><i class="fa fa-folder"></i></span></div>
													</div>
												</div>
											</div>
											
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('Specifies a theme of elements', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Theme', VISION_PLUGIN_NAME); ?></div>
												<select class="vision-select vision-capitalize" al-select="appData.config.theme">
													<option al-option="null"><?php esc_html_e('none', VISION_PLUGIN_NAME); ?></option>
													<option al-repeat="theme in appData.themes" al-option="theme.id">{{theme.title}}</option>
												</select>
											</div>
										</div>
									</div>
									<div class="vision-block" al-attr.class.vision-block-folded="appData.ui.generalTab.container">
										<div class="vision-block-header" al-on.click="appData.fn.onGeneralTab(appData,'container')">
											<div class="vision-block-title"><?php esc_html_e('Container', VISION_PLUGIN_NAME); ?></div>
											<div class="vision-block-state"></div>
										</div>
										<div class="vision-block-data">
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('The container width will be auto calculated', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Auto width', VISION_PLUGIN_NAME); ?></div>
												<div al-toggle="appData.config.autoWidth"></div>
											</div>
											
											<div class="vision-control" al-if="!appData.config.autoWidth">
												<div class="vision-helper" title="<?php esc_html_e('Sets the container width, can be any valid CSS units, not just pixels', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Custom width', VISION_PLUGIN_NAME); ?></div>
												<input class="vision-text" type="text" al-text="appData.config.containerWidth" placeholder="<?php esc_html_e('Default: auto', VISION_PLUGIN_NAME); ?>">
											</div>
											
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('The container height will be auto calculated', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Auto height', VISION_PLUGIN_NAME); ?></div>
												<div al-toggle="appData.config.autoHeight"></div>
											</div>
											
											<div class="vision-control" al-if="!appData.config.autoHeight">
												<div class="vision-helper" title="<?php esc_html_e('Sets the container height, can be any valid CSS units, not just pixels', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Custom height', VISION_PLUGIN_NAME); ?></div>
												<input class="vision-text" type="text" al-text="appData.config.containerHeight" placeholder="<?php esc_html_e('Default: auto', VISION_PLUGIN_NAME); ?>">
											</div>
											
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('Background color in hexadecimal format (#fff or #555555)', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Background color', VISION_PLUGIN_NAME); ?></div>
												<div class="vision-color" al-color="appData.config.background.color"></div>
											</div>
											
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('Sets a background image (jpeg or png format)', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Background image', VISION_PLUGIN_NAME); ?></div>
												<div class="vision-input-group">
													<div class="vision-input-group-cell">
														<input class="vision-text vision-long vision-no-brr" type="text" al-text="appData.config.background.image.url" placeholder="<?php esc_html_e('Select an image', VISION_PLUGIN_NAME); ?>">
													</div>
													<div class="vision-input-group-cell vision-pinch">
														<div class="vision-btn vision-default vision-no-bl" al-on.click="appData.fn.selectImage(appData, appData.rootScope, appData.config.background.image)" title="<?php esc_html_e('Select a background image', VISION_PLUGIN_NAME); ?>"><span><i class="fa fa-folder"></i></span></div>
													</div>
												</div>
											</div>
											
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('Specifies a size of the background image', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Background size', VISION_PLUGIN_NAME); ?></div>
												<div class="vision-select" al-backgroundsize="appData.config.background.size"></div>
											</div>
											
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('How the background image will be repeated', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Background repeat', VISION_PLUGIN_NAME); ?></div>
												<div class="vision-select" al-backgroundrepeat="appData.config.background.repeat"></div>
											</div>
											
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('Sets a starting position of the background image', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Background position', VISION_PLUGIN_NAME); ?></div>
												<input class="vision-text" type="text" al-text="appData.config.background.position" placeholder="<?php esc_html_e('Example: 50% 50%', VISION_PLUGIN_NAME); ?>">
											</div>
											
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('Sets additional css classes to the container', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Extra CSS classes', VISION_PLUGIN_NAME); ?></div>
												<input class="vision-text" type="text" al-text="appData.config.class">
											</div>
											
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('Sets ID to the container', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Container ID', VISION_PLUGIN_NAME); ?></div>
												<input class="vision-text" type="text" al-text="appData.config.containerId">
											</div>
											
											<div class="vision-control">
												<div class="vision-helper" title="<?php esc_html_e('Sets the slug for the vision item', VISION_PLUGIN_NAME); ?>"></div>
												<div class="vision-label"><?php esc_html_e('Slug', VISION_PLUGIN_NAME); ?></div>
												<input class="vision-text" type="text" al-text="appData.config.slug" data-regex="^([a-z0-9_-]+)$">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="vision-section" al-attr.class.vision-active="appData.ui.tabs.layers">
						<div class="vision-stage">
							<div class="vision-main-panel">
								<div class="vision-edit-layers">
									<div class="vision-layers-toolbar-navigation" al-if="appData.config.layers.length > 0">
										<i class="fa fa-chevron-left" al-on.click="appData.fn.prevLayer(appData)" title="<?php esc_html_e('Prev layer', VISION_PLUGIN_NAME); ?>"></i>
										<i class="fa fa-chevron-right" al-on.click="appData.fn.nextLayer(appData)" title="<?php esc_html_e('Next layer', VISION_PLUGIN_NAME); ?>"></i>
									</div>
									<div class="vision-layers-toolbar-view">
										<i class="fa fa-search-plus" al-on.click="appData.fn.canvasZoomIn(appData)" title="<?php esc_html_e('Zoom in', VISION_PLUGIN_NAME); ?>"></i>
										<span class="vision-zoom-value">{{appData.fn.getCanvasZoom(appData)}}%</span>
										<i class="fa fa-search-minus" al-on.click="appData.fn.canvasZoomOut(appData)" title="<?php esc_html_e('Zoom out', VISION_PLUGIN_NAME); ?>"></i>
										<i class="fa fa-search" al-on.click="appData.fn.canvasZoomDefault(appData)" title="<?php esc_html_e('Zoom default', VISION_PLUGIN_NAME); ?>"></i>
										<i class="fa fa-arrows" al-on.click="appData.fn.canvasZoomFit(appData)" title="<?php esc_html_e('Zoom fit', VISION_PLUGIN_NAME); ?>"></i>
										<i class="fa fa-dot-circle-o" al-on.click="appData.fn.canvasMoveDefault(appData)" title="<?php esc_html_e('Move default', VISION_PLUGIN_NAME); ?>"></i>
									</div>
									<div id="vision-layers-canvas-wrap" class="vision-layers-canvas-wrap" al-on.mousedown="appData.fn.onMoveCanvasStart(appData, $event)">
										<div id="vision-layers-canvas" class="vision-layers-canvas">
											<div id="vision-layers-image" class="vision-layers-image"></div>
											<div class="vision-layers-stage">
												<div class="vision-layer"
												 tabindex="1"
												 al-on.click="appData.fn.onLayerClick(appData, layer)"
												 al-on.mousedown="appData.fn.onEditLayerStart(appData, layer, 'drag', $event)"
												 al-on.keydown="appData.fn.onEditLayerKeyDown(appData, layer, $event)"
												 al-attr.class.vision-active="appData.fn.isLayerActive(appData, layer)"
												 al-attr.class.vision-hidden="!layer.visible"
												 al-attr.class.vision-lock="layer.lock"
												 al-attr.class.vision-layer-link="layer.type == 'link'"
												 al-attr.class.vision-layer-text="layer.type == 'text'"
												 al-attr.class.vision-layer-image="layer.type == 'image'"
												 al-attr.class.vision-layer-svg="layer.type == 'svg'"
												 al-style.top="appData.fn.getLayerStyle(appData, layer, 'y')"
												 al-style.left="appData.fn.getLayerStyle(appData, layer, 'x')"
												 al-style.width="appData.fn.getLayerStyle(appData, layer, 'width')"
												 al-style.height="appData.fn.getLayerStyle(appData, layer, 'height')"
												 al-style.transform="appData.fn.getLayerStyle(appData, layer, 'angle')"
												 al-repeat="layer in appData.config.layers"
												 al-init="appData.fn.initLayer(appData, layer, $element)"
												>
													<div class="vision-layer-inner"
														 al-on.dblclick="appData.fn.onEditLabelText(appData, layer, $element, $event)"
														 spellcheck="false"
														 al-style.border-radius="appData.fn.getLayerStyle(appData, layer, 'border-radius')"
														 al-style.background-color="appData.fn.getLayerStyle(appData, layer, 'background-color')"
														 al-style.background-image="appData.fn.getLayerStyle(appData, layer, 'background-image')"
														 al-style.background-size="appData.fn.getLayerStyle(appData, layer, 'background-size')"
														 al-style.background-repeat="appData.fn.getLayerStyle(appData, layer, 'background-repeat')"
														 al-style.background-position="appData.fn.getLayerStyle(appData, layer, 'background-position')"
														 al-style.color="appData.fn.getLayerStyle(appData, layer, 'color')"
														 al-style.font-family="appData.fn.getLayerStyle(appData, layer, 'font-family')"
														 al-style.font-size="appData.fn.getLayerStyle(appData, layer, 'font-size')"
														 al-style.line-height="appData.fn.getLayerStyle(appData, layer, 'line-height')"
														 al-style.text-align="appData.fn.getLayerStyle(appData, layer, 'text-align')"
														 al-style.letter-spacing="appData.fn.getLayerStyle(appData, layer, 'letter-spacing')"
														 al-init="appData.fn.initLayerInner(appData, layer, $element)"
													>
													</div>
													<div class="vision-layer-resizer">
														<div class="vision-layer-coord">X: {{appData.fn.getLayerCoord(appData, layer, 'x')}} <br>Y: {{appData.fn.getLayerCoord(appData, layer, 'y')}} <br>L: {{appData.fn.getLayerCoord(appData, layer, 'angle')}}°</div>
														<div class="vision-layer-rotator" al-on.mousedown="appData.fn.onEditLayerStart(appData, layer, 'rotate', $event)">
															<div class="vision-layer-line"></div>
														</div>
														<div class="vision-layer-dragger-tl" al-on.mousedown="appData.fn.onEditLayerStart(appData, layer, 'tl', $event)"></div>
														<div class="vision-layer-dragger-tm" al-on.mousedown="appData.fn.onEditLayerStart(appData, layer, 'tm', $event)"></div>
														<div class="vision-layer-dragger-tr" al-on.mousedown="appData.fn.onEditLayerStart(appData, layer, 'tr', $event)"></div>
														<div class="vision-layer-dragger-rm" al-on.mousedown="appData.fn.onEditLayerStart(appData, layer, 'rm', $event)"></div>
														<div class="vision-layer-dragger-br" al-on.mousedown="appData.fn.onEditLayerStart(appData, layer, 'br', $event)"></div>
														<div class="vision-layer-dragger-bm" al-on.mousedown="appData.fn.onEditLayerStart(appData, layer, 'bm', $event)"></div>
														<div class="vision-layer-dragger-bl" al-on.mousedown="appData.fn.onEditLayerStart(appData, layer, 'bl', $event)"></div>
														<div class="vision-layer-dragger-lm" al-on.mousedown="appData.fn.onEditLayerStart(appData, layer, 'lm', $event)"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="vision-sidebar-panel" al-attr.class.vision-hidden="!appData.ui.sidebar" al-style.width="appData.ui.sidebarWidth">
								<div class="vision-sidebar-resizer" al-on.mousedown="appData.fn.onSidebarResizeStart(appData, $event)">
									<div class="vision-sidebar-hide" al-on.click="appData.fn.toggleSidebarPanel(appData)">
										<i class="fa fa-chevron-right" al-if="appData.ui.sidebar"></i>
										<i class="fa fa-chevron-left" al-if="!appData.ui.sidebar"></i>
									</div>
								</div>
								<div class="vision-tabs vision-clear-fix">
									<div class="vision-tab" al-attr.class.vision-active="appData.ui.layersTabs.layers" al-on.click="appData.fn.onLayersTab(appData, 'layers')"><?php esc_html_e('Layers', VISION_PLUGIN_NAME); ?></div>
									<div class="vision-tab" al-attr.class.vision-active="appData.ui.layersTabs.layer" al-on.click="appData.fn.onLayersTab(appData, 'layer')"><?php esc_html_e('Layer', VISION_PLUGIN_NAME); ?></div>
									<div class="vision-tab" al-attr.class.vision-active="appData.ui.layersTabs.tooltip" al-on.click="appData.fn.onLayersTab(appData, 'tooltip')"><?php esc_html_e('Tooltip', VISION_PLUGIN_NAME); ?><div class="vision-status" al-if="appData.ui.activeLayer != null && appData.ui.activeLayer.tooltip.active"></div></div>
									<div class="vision-tab" al-attr.class.vision-active="appData.ui.layersTabs.popover" al-on.click="appData.fn.onLayersTab(appData, 'popover')"><?php esc_html_e('Popover', VISION_PLUGIN_NAME); ?><div class="vision-status" al-if="appData.ui.activeLayer != null && appData.ui.activeLayer.popover.active"></div></div>
								</div>
								<div class="vision-data" al-attr.class.vision-active="appData.ui.layersTabs.layers">
									<div class="vision-layers-wrap">
										<div class="vision-layers-toolbar">
											<div class="vision-left-panel">
												<i class="fa fa-external-link" al-on.click="appData.fn.addLayerLink(appData)" title="<?php esc_html_e('add link', VISION_PLUGIN_NAME); ?>"></i>
												<i class="fa fa-font" al-on.click="appData.fn.addLayerText(appData)" title="<?php esc_html_e('add text', VISION_PLUGIN_NAME); ?>"></i>
												<i class="fa fa-picture-o" al-on.click="appData.fn.addLayerImage(appData)" title="<?php esc_html_e('add image', VISION_PLUGIN_NAME); ?>"></i>
												<!--<i class="fa fa-object-ungroup" al-on.click="appData.fn.addLayerSVG(appData)" title="<?php esc_html_e('add svg', VISION_PLUGIN_NAME); ?>"></i>-->
												<span al-if="appData.ui.activeLayer != null">
												<i class="vision-separator"></i>
												<i class="fa fa-clone" al-on.click="appData.fn.copyLayer(appData)" title="<?php esc_html_e('copy', VISION_PLUGIN_NAME); ?>"></i>
												<i class="fa fa-arrow-up" al-on.click="appData.fn.updownLayer(appData, 'up')" title="<?php esc_html_e('move up', VISION_PLUGIN_NAME); ?>"></i>
												<i class="fa fa-arrow-down" al-on.click="appData.fn.updownLayer(appData, 'down')" title="<?php esc_html_e('move down', VISION_PLUGIN_NAME); ?>"></i>
												</span>
											</div>
											<div class="vision-right-panel">
												<i class="fa fa-trash-o fa-color-red" al-if="appData.ui.activeLayer != null" al-on.click="appData.fn.deleteLayer(appData)" title="<?php esc_html_e('delete', VISION_PLUGIN_NAME); ?>"></i>
											</div>
										</div>
										<div class="vision-layers-list">
											<div class="vision-layer"
											 al-attr.class.vision-active="appData.fn.isLayerActive(appData, layer)"
											 al-on.click="appData.fn.onLayerItemClick(appData, layer)"
											 al-repeat="layer in appData.config.layers"
											 >
												<i class="fa fa-external-link" al-if="layer.type == 'link'"></i>
												<i class="fa fa-font" al-if="layer.type == 'text'"></i>
												<i class="fa fa-picture-o" al-if="layer.type == 'image'"></i>
												<i class="fa fa-object-ungroup" al-if="layer.type == 'svg'"></i>
												<div class="vision-label">{{layer.title ? layer.title : layer.type}}</div>
												<div class="vision-actions">
													<i class="fa fa-commenting-o" al-attr.class.vision-inactive="!layer.tooltip.active" al-on.click="appData.fn.toggleLayerTooltip(appData, layer)" title="<?php esc_html_e('enable/disable tooltip', VISION_PLUGIN_NAME); ?>"></i>
													<i class="fa fa-address-card-o" al-attr.class.vision-inactive="!layer.popover.active" al-on.click="appData.fn.toggleLayerPopover(appData, layer)" title="<?php esc_html_e('enable/disable popover', VISION_PLUGIN_NAME); ?>"></i>
													<i class="fa" al-attr.class.fa-toggle-on="layer.visible" al-attr.class.fa-toggle-off="!layer.visible" al-on.click="appData.fn.toggleLayerVisible(appData, layer)" title="<?php esc_html_e('show/hide', VISION_PLUGIN_NAME); ?>"></i>
													<i class="fa" al-attr.class.fa-unlock-alt="!layer.lock" al-attr.class.fa-lock="layer.lock" al-on.click="appData.fn.toggleLayerLock(appData, layer)" title="<?php esc_html_e('lock/unlock', VISION_PLUGIN_NAME); ?>"></i>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="vision-data" al-attr.class.vision-active="appData.ui.layersTabs.layer">
									<div al-if="appData.ui.activeLayer == null">
										<div class="vision-info"><?php esc_html_e('Please, select a layer to view settings', VISION_PLUGIN_NAME); ?></div>
									</div>
									<div al-if="appData.ui.activeLayer != null">
										<div class="vision-block-list">
											<div class="vision-block" al-attr.class.vision-block-folded="appData.ui.layerTab.general">
												<div class="vision-block-header" al-on.click="appData.fn.onLayerTab(appData,'general')">
													<div class="vision-block-title"><?php esc_html_e('General', VISION_PLUGIN_NAME); ?></div>
													<div class="vision-block-state"></div>
												</div>
												<div class="vision-block-data">
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Set layer title', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Title', VISION_PLUGIN_NAME); ?></div>
														<input class="vision-text vision-long" type="text" al-text="appData.ui.activeLayer.title">
													</div>
													
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Sets a layer id (allow numbers, chars & specials: "_","-"). Should be unique and not empty.', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Id', VISION_PLUGIN_NAME); ?></div>
														<div class="vision-input-group vision-long">
															<div class="vision-input-group-cell">
																<input class="vision-text vision-long vision-no-brr" type="text" al-uuid="appData.ui.activeLayer.id">
															</div>
															<div class="vision-input-group-cell vision-pinch">
																<div class="vision-btn vision-default vision-no-bl" al-on.click="appData.fn.generateLayerId(appData, appData.rootScope, appData.ui.activeLayer)" title="<?php esc_html_e('Generate a new ID', VISION_PLUGIN_NAME); ?>"><span><i class="fa fa-refresh"></i></span></div>
															</div>
														</div>
													</div>
													
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Set layer position', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-input-group vision-long">
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-label"><?php esc_html_e('X [px]', VISION_PLUGIN_NAME); ?></div>
																<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.x">
															</div>
															<div class="vision-input-group-cell vision-lgap">
																<div class="vision-label"><?php esc_html_e('Y [px]', VISION_PLUGIN_NAME); ?></div>
																<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.y">
															</div>
														</div>
													</div>
													
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Set layer size', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-input-group vision-long">
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-label"><?php esc_html_e('Width [px]', VISION_PLUGIN_NAME); ?></div>
																<div class="vision-input-group vision-long">
																	<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.width">
																</div>
																<div class="vision-input-group vision-long">
																	<div class="vision-input-group-cell vision-pinch">
																		<div al-checkbox="appData.ui.activeLayer.autoWidth"></div>
																	</div>
																	<div class="vision-input-group-cell">
																		<?php esc_html_e('Auto width', VISION_PLUGIN_NAME); ?>
																	</div>
																</div>
															</div>
															<div class="vision-input-group-cell vision-lgap">
																<div class="vision-label"><?php esc_html_e('Height [px]', VISION_PLUGIN_NAME); ?></div>
																<div class="vision-input-group vision-long">
																	<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.height">
																</div>
																<div class="vision-input-group vision-long">
																	<div class="vision-input-group-cell vision-pinch">
																		<div al-checkbox="appData.ui.activeLayer.autoHeight"></div>
																	</div>
																	<div class="vision-input-group-cell">
																		<?php esc_html_e('Auto height', VISION_PLUGIN_NAME); ?>
																	</div>
																</div>
															</div>
														</div>
													</div>
													
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Set layer angle', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Angle [deg]', VISION_PLUGIN_NAME); ?></div>
														<input class="vision-number vision-long" al-float="appData.ui.activeLayer.angle">
													</div>
												</div>
											</div>
											
											<div class="vision-block" al-attr.class.vision-block-folded="appData.ui.layerTab.data">
												<div class="vision-block-header" al-on.click="appData.fn.onLayerTab(appData,'data')">
													<div class="vision-block-title"><?php esc_html_e('Data', VISION_PLUGIN_NAME); ?></div>
													<div class="vision-block-state"></div>
												</div>
												<div class="vision-block-data">
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Adds a specific url to the layer', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('URL', VISION_PLUGIN_NAME); ?></div>
														<div class="vision-input-group vision-long">
                                                            <div class="vision-input-group-cell">
															    <input class="vision-text vision-long vision-no-brr" type="text" al-text="appData.ui.activeLayer.url" placeholder="<?php esc_html_e('URL', VISION_PLUGIN_NAME); ?>">
                                                            </div>
                                                            <div class="vision-input-group-cell vision-pinch">
                                                                <div class="vision-btn vision-default vision-no-bl" al-on.click="appData.fn.getPostUrl(appData, appData.rootScope, appData.ui.activeLayer)" title="<?php esc_html_e('Get a post url', VISION_PLUGIN_NAME); ?>"><span><i class="fa fa-file"></i></span></div>
                                                            </div>
														</div>
														<div class="vision-input-group vision-long">
															<div class="vision-input-group-cell vision-pinch">
																<div al-checkbox="appData.ui.activeLayer.urlNewWindow"></div>
															</div>
															<div class="vision-input-group-cell">
																<?php esc_html_e('Open url in a new window', VISION_PLUGIN_NAME); ?>
															</div>
														</div>
                                                        <div class="vision-input-group vision-long">
                                                            <div class="vision-input-group-cell vision-pinch">
                                                                <div al-checkbox="appData.ui.activeLayer.urlNoFollow"></div>
                                                            </div>
                                                            <div class="vision-input-group-cell">
                                                                <?php esc_html_e('Set the "nofollow" tag', VISION_PLUGIN_NAME); ?>
                                                            </div>
                                                        </div>
													</div>
													
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Adds the inner content data to the layer (shortcodes can be used too)', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Content data', VISION_PLUGIN_NAME); ?></div>
														<textarea class="vision-long" al-textarea="appData.ui.activeLayer.contentData"></textarea>
													</div>
													
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Adds a specific string data to the layer, if we want to use it in custom code later', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('User data', VISION_PLUGIN_NAME); ?></div>
														<textarea class="vision-long" al-textarea="appData.ui.activeLayer.userData"></textarea>
													</div>
												</div>
											</div>
											
											<div class="vision-block" al-attr.class.vision-block-folded="appData.ui.layerTab.appearance">
												<div class="vision-block-header" al-on.click="appData.fn.onLayerTab(appData,'appearance')">
													<div class="vision-block-title"><?php esc_html_e('Appearance', VISION_PLUGIN_NAME); ?></div>
													<div class="vision-block-state"></div>
												</div>
												<div class="vision-block-data">
													<div class="vision-control">
														<div class="vision-input-group vision-long">
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-helper" title="<?php esc_html_e('The layer size depends on the image size, it scales with the image', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Zoom with map', VISION_PLUGIN_NAME); ?></div>
																<div al-toggle="appData.ui.activeLayer.scaling"></div>
															</div>
															<div class="vision-input-group-cell vision-lgap">
																<div class="vision-helper" title="<?php esc_html_e('The layer is never the target of mouse events', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('No events', VISION_PLUGIN_NAME); ?></div>
																<div al-toggle="appData.ui.activeLayer.noevents"></div>
															</div>
														</div>
													</div>
													
													<div al-if="appData.ui.activeLayer.type == 'link'">
														<div class="vision-control">
															<div class="vision-helper" title="<?php esc_html_e('Normal color in hexadecimal format (#fff or #555555)', VISION_PLUGIN_NAME); ?>"></div>
															<div class="vision-label"><?php esc_html_e('Normal color', VISION_PLUGIN_NAME); ?></div>
															<div class="vision-color vision-long" al-color="appData.ui.activeLayer.link.normalColor"></div>
														</div>
														
														<div class="vision-control">
															<div class="vision-helper" title="<?php esc_html_e('Hover color in hexadecimal format (#fff or #555555)', VISION_PLUGIN_NAME); ?>"></div>
															<div class="vision-label"><?php esc_html_e('Hover color', VISION_PLUGIN_NAME); ?></div>
															<div class="vision-color vision-long" al-color="appData.ui.activeLayer.link.hoverColor"></div>
														</div>
														
														<div class="vision-control">
															<div class="vision-helper" title="<?php esc_html_e('Sets a radius (5px or 50%)', VISION_PLUGIN_NAME); ?>"></div>
															<div class="vision-label"><?php esc_html_e('Radius', VISION_PLUGIN_NAME); ?></div>
															<input class="vision-number vision-long" type="text" al-text="appData.ui.activeLayer.link.radius" placeholder="<?php esc_html_e('Example: 10px', VISION_PLUGIN_NAME); ?>">
														</div>
													</div>
													
													<div al-if="appData.ui.activeLayer.type == 'text'">
														<div class="vision-control">
															<div class="vision-helper" title="<?php esc_html_e('Specifies a font of the text', VISION_PLUGIN_NAME); ?>"></div>
															<div class="vision-label"><?php esc_html_e('Font', VISION_PLUGIN_NAME); ?></div>
															<div class="vision-select vision-capitalize vision-long" al-textfont="appData.ui.activeLayer.text.font" data-fonts="appData.fonts"></div>
														</div>
														
														<div class="vision-control">
															<div class="vision-helper" title="<?php esc_html_e('Text color in hexadecimal format (#fff or #555555)', VISION_PLUGIN_NAME); ?>"></div>
															<div class="vision-label"><?php esc_html_e('Text color', VISION_PLUGIN_NAME); ?></div>
															<div class="vision-color vision-long" al-color="appData.ui.activeLayer.text.color"></div>
														</div>
														
														<div class="vision-control">
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell vision-rgap">
																	<div class="vision-helper" title="<?php esc_html_e('Sets the text size in px', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Text size [px]', VISION_PLUGIN_NAME); ?></div>
																	<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.text.size" placeholder="<?php esc_html_e('Example: 18', VISION_PLUGIN_NAME); ?>">
																</div>
																<div class="vision-input-group-cell vision-lgap">
																	<div class="vision-helper" title="<?php esc_html_e('Sets the text line height in px', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Line height [px]', VISION_PLUGIN_NAME); ?></div>
																	<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.text.lineHeight" placeholder="<?php esc_html_e('Example: 18', VISION_PLUGIN_NAME); ?>">
																</div>
															</div>
														</div>
														
														<div class="vision-control">
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell vision-rgap">
																	<div class="vision-helper" title="<?php esc_html_e('Specifies the horizontal alignment of the text', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Text align', VISION_PLUGIN_NAME); ?></div>
																	<div class="vision-select vision-long" al-textalign="appData.ui.activeLayer.text.align"></div>
																</div>
																<div class="vision-input-group-cell vision-lgap">
																	<div class="vision-helper" title="<?php esc_html_e('Specifies the spacing behavior between text characters', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Letter spacing [px]', VISION_PLUGIN_NAME); ?></div>
																	<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.text.letterSpacing" placeholder="<?php esc_html_e('Example: 1', VISION_PLUGIN_NAME); ?>">
																</div>
															</div>
														</div>
														
														<div class="vision-control">
															<div class="vision-helper" title="<?php esc_html_e('Sets a background image (jpeg or png format)', VISION_PLUGIN_NAME); ?>"></div>
															<div class="vision-label"><?php esc_html_e('Background image', VISION_PLUGIN_NAME); ?></div>
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell">
																	<input class="vision-text vision-long vision-no-brr" type="text" al-text="appData.ui.activeLayer.text.background.file.url" placeholder="<?php esc_html_e('Select an image', VISION_PLUGIN_NAME); ?>">
																</div>
																<div class="vision-input-group-cell vision-pinch">
																	<div class="vision-btn vision-default vision-no-bl" al-on.click="appData.fn.selectImage(appData, appData.rootScope, appData.ui.activeLayer.text.background.file)"><span><i class="fa fa-folder"></i></span></div>
																</div>
															</div>
														</div>
														
														<!-- background color & repeat -->
														<div class="vision-control">
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell vision-rgap">
																	<div class="vision-helper" title="<?php esc_html_e('Sets a background color', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Background color', VISION_PLUGIN_NAME); ?></div>
																	<div class="vision-color vision-long" al-color="appData.ui.activeLayer.text.background.color"></div>
																</div>
																<div class="vision-input-group-cell vision-lgap">
																	<div class="vision-helper" title="<?php esc_html_e('How the background image will be repeated', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Background repeat', VISION_PLUGIN_NAME); ?></div>
																	<div class="vision-select vision-long" al-backgroundrepeat="appData.ui.activeLayer.text.background.repeat"></div>
																</div>
															</div>
														</div>
														
														<div class="vision-control">
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell vision-rgap">
																	<div class="vision-helper" title="<?php esc_html_e('Specifies a size of the background image', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Background size', VISION_PLUGIN_NAME); ?></div>
																	<div class="vision-select vision-long" al-backgroundsize="appData.ui.activeLayer.text.background.size"></div>
																</div>
																<div class="vision-input-group-cell vision-lgap">
																	<div class="vision-helper" title="<?php esc_html_e('Sets a starting position of the background image', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Background position', VISION_PLUGIN_NAME); ?></div>
																	<input class="vision-text vision-long" type="text" al-text="appData.ui.activeLayer.text.background.position" placeholder="<?php esc_html_e('Example: 50% 50%', VISION_PLUGIN_NAME); ?>">
																</div>
															</div>
														</div>
													</div>
													
													<div al-if="appData.ui.activeLayer.type == 'image'">
														<div class="vision-control">
															<div class="vision-helper" title="<?php esc_html_e('Sets a background image (jpeg or png format)', VISION_PLUGIN_NAME); ?>"></div>
															<div class="vision-label"><?php esc_html_e('Background image', VISION_PLUGIN_NAME); ?></div>
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell">
																	<input class="vision-text vision-long vision-no-brr" type="text" al-text="appData.ui.activeLayer.image.background.file.url" placeholder="<?php esc_html_e('Select an image', VISION_PLUGIN_NAME); ?>">
																</div>
																<div class="vision-input-group-cell vision-pinch">
																	<div class="vision-btn vision-default vision-no-bl" al-on.click="appData.fn.selectImage(appData, appData.rootScope, appData.ui.activeLayer.image.background.file)"><span><i class="fa fa-folder"></i></span></div>
																</div>
															</div>
														</div>
														
														<!-- background color & repeat -->
														<div class="vision-control">
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell vision-rgap">
																	<div class="vision-helper" title="<?php esc_html_e('Sets a background color', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Background color', VISION_PLUGIN_NAME); ?></div>
																	<div class="vision-color vision-long" al-color="appData.ui.activeLayer.image.background.color"></div>
																</div>
																<div class="vision-input-group-cell vision-lgap">
																	<div class="vision-helper" title="<?php esc_html_e('How the background image will be repeated', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Background repeat', VISION_PLUGIN_NAME); ?></div>
																	<div class="vision-select vision-long" al-backgroundrepeat="appData.ui.activeLayer.image.background.repeat"></div>
																</div>
															</div>
														</div>
														
														<div class="vision-control">
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell vision-rgap">
																	<div class="vision-helper" title="<?php esc_html_e('Specifies a size of the background image', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Background size', VISION_PLUGIN_NAME); ?></div>
																	<div class="vision-select vision-long" al-backgroundsize="appData.ui.activeLayer.image.background.size"></div>
																</div>
																<div class="vision-input-group-cell vision-lgap">
																	<div class="vision-helper" title="<?php esc_html_e('Sets a starting position of the background image', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Background position', VISION_PLUGIN_NAME); ?></div>
																	<input class="vision-text vision-long" type="text" al-text="appData.ui.activeLayer.image.background.position" placeholder="<?php esc_html_e('Example: 50% 50%', VISION_PLUGIN_NAME); ?>">
																</div>
															</div>
														</div>
													</div>
													
													<div al-if="appData.ui.activeLayer.type == 'svg'">
														<div class="vision-control">
															<div class="vision-helper" title="<?php esc_html_e('Set svg file', VISION_PLUGIN_NAME); ?>"></div>
															<div class="vision-label"><?php esc_html_e('File', VISION_PLUGIN_NAME); ?></div>
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell">
																	<input class="vision-text vision-long" type="text" al-text="appData.ui.activeLayer.svg.file.url" placeholder="<?php esc_html_e('Select a file', VISION_PLUGIN_NAME); ?>">
																</div>
																<div class="vision-input-group-cell vision-pinch">
																	<div class="vision-btn vision-default vision-no-bl" al-on.click="appData.fn.selectImage(appData, appData.rootScope, appData.ui.activeLayer.svg.file)"><span><i class="fa fa-folder"></i></span></div>
																</div>
															</div>
														</div>
													</div>
													
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Set additional css classes to the layer', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Extra CSS classes', VISION_PLUGIN_NAME); ?></div>
														<input class="vision-number vision-long" type="text" al-text="appData.ui.activeLayer.className">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="vision-data" al-attr.class.vision-active="appData.ui.layersTabs.tooltip">
									<div class="vision-data-block" al-attr.class.vision-active="appData.ui.activeLayer == null">
										<div class="vision-info"><?php esc_html_e('Please, select a layer to view settings', VISION_PLUGIN_NAME); ?></div>
									</div>
									<div class="vision-data-block" al-attr.class.vision-active="appData.ui.activeLayer != null">
										<div class="vision-block-list">
										<div class="vision-block" al-attr.class.vision-block-folded="appData.ui.tooltipTab.data">
											<div class="vision-block-header" al-on.click="appData.fn.onTooltipTab(appData,'data')">
												<div class="vision-block-title"><?php esc_html_e('Data', VISION_PLUGIN_NAME); ?></div>
												<div class="vision-block-state"></div>
											</div>
											<div class="vision-block-data">
												<div al-if="appData.ui.activeLayer != null">
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Enable/disable tooltip for the selected layer', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Enable tooltip', VISION_PLUGIN_NAME); ?></div>
														<div al-toggle="appData.ui.activeLayer.tooltip.active"></div>
													</div>
												</div>
												
												<div class="vision-control">
													<?php
														$settings = array(
															'tinymce' => true,
															'textarea_name' => 'vision-tooltip-text',
															'wpautop' => false,
															'editor_height' => 200, // In pixels, takes precedence and has no default value
															'drag_drop_upload' => true,
															'media_buttons' => true,
															'teeny' => true,
															'quicktags' => true
														);
														wp_editor('','vision-tooltip-editor', $settings);
													?>
												</div>
											</div>
										</div>
										
										<div class="vision-block" al-attr.class.vision-block-folded="appData.ui.tooltipTab.appearance">
											<div class="vision-block-header" al-on.click="appData.fn.onTooltipTab(appData,'appearance')">
												<div class="vision-block-title"><?php esc_html_e('Appearance', VISION_PLUGIN_NAME); ?></div>
												<div class="vision-block-state"></div>
											</div>
											<div class="vision-block-data">
												<div al-if="appData.ui.activeLayer != null">
													<div class="vision-control">
														<div class="vision-input-group vision-long">
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-helper" title="<?php esc_html_e('Specifies a tooltip event trigger', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Trigger', VISION_PLUGIN_NAME); ?></div>
																<div class="vision-select vision-long" al-tooltiptrigger="appData.ui.activeLayer.tooltip.trigger"></div>
															</div>
															<div class="vision-input-group-cell vision-lgap">
																<div class="vision-helper" title="<?php esc_html_e('Specifies a tooltip placement', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Placement', VISION_PLUGIN_NAME); ?></div>
																<div class="vision-select vision-long" al-tooltipplacement="appData.ui.activeLayer.tooltip.placement"></div>
															</div>
														</div>
													</div>
													
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Set tooltip offset', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-input-group vision-long">
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-label"><?php esc_html_e('Offset top [px]', VISION_PLUGIN_NAME); ?></div>
																<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.tooltip.offset.top">
															</div>
															<div class="vision-input-group-cell vision-lgap">
																<div class="vision-label"><?php esc_html_e('Offset left [px]', VISION_PLUGIN_NAME); ?></div>
																<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.tooltip.offset.left">
															</div>
														</div>
													</div>
													
													<div class="vision-control">
														<div class="vision-input-group vision-long">
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-helper" title="<?php esc_html_e('The tooltip size depends on the image size', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Zoom with map', VISION_PLUGIN_NAME); ?></div>
																<div al-toggle="appData.ui.activeLayer.tooltip.scaling"></div>
															</div>
															<div class="vision-input-group-cell vision-lgap">
																<div class="vision-helper" title="<?php esc_html_e('Determines if the tooltip is placed within the viewport as best it can be if there is not enough space', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Smart', VISION_PLUGIN_NAME); ?></div>
																<div al-toggle="appData.ui.activeLayer.tooltip.smart"></div>
															</div>
														</div>
													</div>
													
													<div class="vision-control">
														<div class="vision-input-group vision-long">
															<div class="vision-input-group-cell vision-rgap" al-attr.class.vision-nogap="appData.ui.activeLayer.tooltip.widthFromCSS">
																<div class="vision-helper" title="<?php esc_html_e('If true, the tooltip width will be taken from CSS rules, dont forget to define them', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Width from CSS', VISION_PLUGIN_NAME); ?></div>
																<div al-toggle="appData.ui.activeLayer.tooltip.widthFromCSS"></div>
															</div>
															<div class="vision-input-group-cell vision-lgap" al-if="!appData.ui.activeLayer.tooltip.widthFromCSS">
																<div class="vision-helper" title="<?php esc_html_e('Specifies a tooltip width', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Width [px]', VISION_PLUGIN_NAME); ?></div>
																<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.tooltip.width" placeholder="<?php esc_html_e('auto', VISION_PLUGIN_NAME); ?>">
															</div>
														</div>
													</div>
													
													<div class="vision-control" al-if="appData.ui.activeLayer.tooltip.trigger != 'hover'">
														<div class="vision-helper" title="<?php esc_html_e('The tooltip will be shown immediately once the instance is created', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Show on init', VISION_PLUGIN_NAME); ?></div>
														<div al-toggle="appData.ui.activeLayer.tooltip.showOnInit"></div>
													</div>
													
													<div class="vision-control" al-if="appData.ui.activeLayer.tooltip.trigger == 'hover'">
														<div class="vision-input-group vision-long">
															<!--
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-helper" title="<?php esc_html_e('Enable/disable tooltip follow the cursor as you hover over the layer', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Follow the cursor', VISION_PLUGIN_NAME); ?></div>
																<div al-toggle="appData.ui.activeLayer.tooltip.followCursor"></div>
															</div>
															-->
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-helper" title="<?php esc_html_e('The tooltip will be shown immediately once the instance is created', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Show on init', VISION_PLUGIN_NAME); ?></div>
																<div al-toggle="appData.ui.activeLayer.tooltip.showOnInit"></div>
															</div>
															<div class="vision-input-group-cell vision-lgap">
																<div class="vision-helper" title="<?php esc_html_e('The tooltip won\'t hide when you hover over or click on them', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Interactive', VISION_PLUGIN_NAME); ?></div>
																<div al-toggle="appData.ui.activeLayer.tooltip.interactive"></div>
															</div>
														</div>
													</div>
													
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Set additional css classes to the tooltip', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Extra CSS classes', VISION_PLUGIN_NAME); ?></div>
														<input class="vision-number vision-long" type="text" al-text="appData.ui.activeLayer.tooltip.className">
													</div>
													
													<div class="vision-control">
														<div class="vision-input-group vision-long">
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-helper" title="<?php esc_html_e('Select a show animation effect for the tooltip from the list or write your own', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Show animation', VISION_PLUGIN_NAME); ?></div>
																<div class="vision-input-group vision-long">
																	<div class="vision-input-group-cell">
																		<input class="vision-text vision-long" type="text" al-text="appData.ui.activeLayer.tooltip.showAnimation">
																	</div>
																	<div class="vision-input-group-cell vision-pinch">
																		<div class="vision-btn vision-default vision-no-bl" al-on.click="appData.fn.selectShowAnimation(appData, appData.ui.activeLayer.tooltip)" title="<?php esc_html_e('Select an effect', VISION_PLUGIN_NAME); ?>"><span><i class="fa fa-folder"></i></span></div>
																	</div>
																</div>
																</div>
															<div class="vision-input-group-cell vision-lgap">
																<div class="vision-helper" title="<?php esc_html_e('Select a hide animation effect for the tooltip from the list or write your own', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Hide animation', VISION_PLUGIN_NAME); ?></div>
																<div class="vision-input-group vision-long">
																	<div class="vision-input-group-cell">
																		<input class="vision-text vision-long" type="text" al-text="appData.ui.activeLayer.tooltip.hideAnimation">
																	</div>
																	<div class="vision-input-group-cell vision-pinch">
																		<div class="vision-btn vision-default vision-no-bl" al-on.click="appData.fn.selectHideAnimation(appData, appData.ui.activeLayer.tooltip)" title="<?php esc_html_e('Select an effect', VISION_PLUGIN_NAME); ?>"><span><i class="fa fa-folder"></i></span></div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Set animation duration for show and hide effects', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Duration [ms]', VISION_PLUGIN_NAME); ?></div>
														<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.tooltip.duration">
													</div>
												</div>
											</div>
										</div>
										</div>
									</div>
								</div>
								<div class="vision-data" al-attr.class.vision-active="appData.ui.layersTabs.popover">
									<div class="vision-data-block" al-attr.class.vision-active="appData.ui.activeLayer == null">
										<div class="vision-info"><?php esc_html_e('Please, select a layer to view settings', VISION_PLUGIN_NAME); ?></div>
									</div>
									<div class="vision-data-block" al-attr.class.vision-active="appData.ui.activeLayer != null">
										<div class="vision-block-list">
										<div class="vision-block" al-attr.class.vision-block-folded="appData.ui.popoverTab.data">
											<div class="vision-block-header" al-on.click="appData.fn.onPopoverTab(appData,'data')">
												<div class="vision-block-title"><?php esc_html_e('Data', VISION_PLUGIN_NAME); ?></div>
												<div class="vision-block-state"></div>
											</div>
											<div class="vision-block-data">
												<div al-if="appData.ui.activeLayer != null">
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Enable/disable popover for the selected layer', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Enable popover', VISION_PLUGIN_NAME); ?></div>
														<div al-toggle="appData.ui.activeLayer.popover.active"></div>
													</div>
												</div>
												
												<div class="vision-control">
													<?php
														$settings = array(
															'tinymce' => true,
															'textarea_name' => 'vision-popover-text',
															'wpautop' => false,
															'editor_height' => 200, // In pixels, takes precedence and has no default value
															'drag_drop_upload' => true,
															'media_buttons' => true,
															'teeny' => true,
															'quicktags' => true
														);
														wp_editor('','vision-popover-editor', $settings);
													?>
												</div>
											</div>
										</div>
										
										<div class="vision-block" al-attr.class.vision-block-folded="appData.ui.popoverTab.appearance">
											<div class="vision-block-header" al-on.click="appData.fn.onPopoverTab(appData,'appearance')">
												<div class="vision-block-title"><?php esc_html_e('Appearance', VISION_PLUGIN_NAME); ?></div>
												<div class="vision-block-state"></div>
											</div>
											<div class="vision-block-data">
												<div al-if="appData.ui.activeLayer != null">
													<div class="vision-control">
														<div class="vision-input-group vision-long">
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-helper" title="<?php esc_html_e('Specifies a popover desktop type', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Desktop type', VISION_PLUGIN_NAME); ?></div>
																<div class="vision-select vision-long" al-popovertype="appData.ui.activeLayer.popover.type"></div>
															</div>
															<div class="vision-input-group-cell vision-lgap">
																<div class="vision-helper" title="<?php esc_html_e('Specifies a popover mobile type', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Mobile type', VISION_PLUGIN_NAME); ?></div>
																<div class="vision-select vision-long" al-popovertype="appData.ui.activeLayer.popover.mobileType"></div>
															</div>
														</div>
													</div>
													
													<div al-if="!(appData.ui.activeLayer.popover.type == 'tooltip' || appData.ui.activeLayer.popover.mobileType == 'tooltip')">
														<div class="vision-control">
															<div class="vision-helper" title="<?php esc_html_e('Specifies the event trigger of the popover', VISION_PLUGIN_NAME); ?>"></div>
															<div class="vision-label"><?php esc_html_e('Trigger', VISION_PLUGIN_NAME); ?></div>
															<div class="vision-select vision-long" al-popovertrigger="appData.ui.activeLayer.popover.trigger"></div>
														</div>
													</div>
													
													<div al-if="appData.ui.activeLayer.popover.type == 'tooltip' || appData.ui.activeLayer.popover.mobileType == 'tooltip'">
														<div class="vision-control">
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell vision-rgap">
																	<div class="vision-helper" title="<?php esc_html_e('Specifies a popover event trigger', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Trigger', VISION_PLUGIN_NAME); ?></div>
																	<div class="vision-select vision-long" al-tooltiptrigger="appData.ui.activeLayer.popover.trigger"></div>
																</div>
																<div class="vision-input-group-cell vision-lgap">
																	<div class="vision-helper" title="<?php esc_html_e('Specifies a popover placement', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Placement', VISION_PLUGIN_NAME); ?></div>
																	<div class="vision-select vision-long" al-tooltipplacement="appData.ui.activeLayer.popover.placement"></div>
																</div>
															</div>
														</div>
														
														<div class="vision-control">
															<div class="vision-helper" title="<?php esc_html_e('Set popover offset', VISION_PLUGIN_NAME); ?>"></div>
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell vision-rgap">
																	<div class="vision-label"><?php esc_html_e('Offset top [px]', VISION_PLUGIN_NAME); ?></div>
																	<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.popover.offset.top">
																</div>
																<div class="vision-input-group-cell vision-lgap">
																	<div class="vision-label"><?php esc_html_e('Offset left [px]', VISION_PLUGIN_NAME); ?></div>
																	<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.popover.offset.left">
																</div>
															</div>
														</div>
														
														<div class="vision-control">
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell vision-rgap">
																	<div class="vision-helper" title="<?php esc_html_e('The popover size depends on the image size', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Zoom with map', VISION_PLUGIN_NAME); ?></div>
																	<div al-toggle="appData.ui.activeLayer.popover.scaling"></div>
																</div>
																<div class="vision-input-group-cell vision-lgap">
																	<div class="vision-helper" title="<?php esc_html_e('Determines if the popover is placed within the viewport as best it can be if there is not enough space', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Smart', VISION_PLUGIN_NAME); ?></div>
																	<div al-toggle="appData.ui.activeLayer.popover.smart"></div>
																</div>
															</div>
														</div>
														
														<div class="vision-control">
															<div class="vision-input-group vision-long">
																<div class="vision-input-group-cell vision-rgap" al-attr.class.vision-nogap="appData.ui.activeLayer.popover.widthFromCSS">
																	<div class="vision-helper" title="<?php esc_html_e('If true, the tooltip width will be taken from CSS rules, dont forget to define them', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Width from CSS', VISION_PLUGIN_NAME); ?></div>
																	<div al-toggle="appData.ui.activeLayer.popover.widthFromCSS"></div>
																</div>
																<div class="vision-input-group-cell vision-lgap" al-if="!appData.ui.activeLayer.popover.widthFromCSS">
																	<div class="vision-helper" title="<?php esc_html_e('Specifies the width of the popover', VISION_PLUGIN_NAME); ?>"></div>
																	<div class="vision-label"><?php esc_html_e('Width [px]', VISION_PLUGIN_NAME); ?></div>
																	<input class="vision-number vision-long" al-integer="appData.ui.activeLayer.popover.width" placeholder="<?php esc_html_e('auto', VISION_PLUGIN_NAME); ?>">
																</div>
															</div>
														</div>
													</div>
													
													<div class="vision-control" al-if="appData.ui.activeLayer.popover.trigger != 'hover'">
														<div class="vision-helper" title="<?php esc_html_e('The popover will be shown immediately once the instance is created', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Show on init', VISION_PLUGIN_NAME); ?></div>
														<div al-toggle="appData.ui.activeLayer.popover.showOnInit"></div>
													</div>
													
													<div class="vision-control" al-if="appData.ui.activeLayer.popover.trigger == 'hover'">
														<div class="vision-input-group vision-long">
															<!--
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-helper" title="<?php esc_html_e('Enable/disable popover follow the cursor as you hover over the layer', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Follow the cursor', VISION_PLUGIN_NAME); ?></div>
																<div al-toggle="appData.ui.activeLayer.popover.followCursor"></div>
															</div>
															-->
															<div class="vision-input-group-cell vision-rgap">
																<div class="vision-helper" title="<?php esc_html_e('The popover will be shown immediately once the instance is created', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Show on init', VISION_PLUGIN_NAME); ?></div>
																<div al-toggle="appData.ui.activeLayer.popover.showOnInit"></div>
															</div>
															<div class="vision-input-group-cell vision-lgap">
																<div class="vision-helper" title="<?php esc_html_e('The popover won\'t hide when you hover over or click on them', VISION_PLUGIN_NAME); ?>"></div>
																<div class="vision-label"><?php esc_html_e('Interactive', VISION_PLUGIN_NAME); ?></div>
																<div al-toggle="appData.ui.activeLayer.popover.interactive"></div>
															</div>
														</div>
													</div>
													
													<div class="vision-control">
														<div class="vision-helper" title="<?php esc_html_e('Set additional css classes to the popover', VISION_PLUGIN_NAME); ?>"></div>
														<div class="vision-label"><?php esc_html_e('Extra CSS classes', VISION_PLUGIN_NAME); ?></div>
														<input class="vision-number vision-long" type="text" al-text="appData.ui.activeLayer.popover.className">
													</div>
												</div>
											</div>
										</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="vision-section" al-attr.class.vision-active="appData.ui.tabs.customCSS" al-if="appData.ui.tabs.customCSS">
						<div class="vision-stage">
							<div class="vision-main-panel vision-main-panel-general">
								<div class="vision-data vision-active">
									<div class="vision-control">
										<div class="vision-helper" title="<?php esc_html_e('Enable/disable custom styles', VISION_PLUGIN_NAME); ?>"></div>
										<div class="vision-input-group">
											<div class="vision-input-group-cell vision-pinch">
												<div al-toggle="appData.config.customCSS.active"></div>
											</div>
											<div class="vision-input-group-cell">
												<div class="vision-label vision-offset-top"><?php esc_html_e('Enable styles', VISION_PLUGIN_NAME); ?></div>
											</div>
										</div>
									</div>
									<div class="vision-control">
										<pre id="vision-notepad-css" class="vision-notepad"></pre>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="vision-section" al-attr.class.vision-active="appData.ui.tabs.customJS" al-if="appData.ui.tabs.customJS">
						<div class="vision-stage">
							<div class="vision-main-panel vision-main-panel-general">
								<div class="vision-data vision-active">
									<div class="vision-control">
										<div class="vision-helper" title="<?php esc_html_e('Enable/disable custom javascript code', VISION_PLUGIN_NAME); ?>"></div>
										<div class="vision-input-group">
											<div class="vision-input-group-cell vision-pinch">
												<div al-toggle="appData.config.customJS.active"></div>
											</div>
											<div class="vision-input-group-cell">
												<div class="vision-label vision-offset-top"><?php esc_html_e('Enable javascript code', VISION_PLUGIN_NAME); ?></div>
											</div>
										</div>
									</div>
									<div class="vision-control">
										<pre id="vision-notepad-js" class="vision-notepad"></pre>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="vision-section" al-attr.class.vision-active="appData.ui.tabs.shortcode" al-if="appData.wp_item_id">
						<div class="vision-main-panel vision-main-panel-general">
							<div class="vision-data vision-active">
								<div class="vision-control">
									<div class="vision-info"><?php esc_html_e('Use a shortcode like the one below, copy and paste it into a post or page.', VISION_PLUGIN_NAME); ?></div>
								</div>
								
								<div class="vision-control">
									<div class="vision-label"><?php esc_html_e('Standard shortcode', VISION_PLUGIN_NAME); ?></div>
									<div class="vision-input-group">
										<div class="vision-input-group-cell">
											<input id="vision-shortcode-1" class="vision-text vision-long" type="text" value='[vision id="{{appData.wp_item_id}}"]' readonly="readonly">
										</div>
										<div class="vision-input-group-cell vision-pinch">
											<div class="vision-btn vision-default vision-no-bl" al-on.click="appData.fn.copyToClipboard(appData, '#vision-shortcode-1')" title="<?php esc_html_e('Copy to clipboard', VISION_PLUGIN_NAME); ?>"><span><i class="fa fa-clipboard"></i></span></div>
										</div>
									</div>
								</div>
								
								<p><?php esc_html_e('Next to that you can also add a few optional arguments to your shortcode:', VISION_PLUGIN_NAME); ?></p>
								<table class="vision-table">
									<tbody>
										<tr>
											<th><?php esc_html_e('Variable', VISION_PLUGIN_NAME); ?></th>
											<th><?php esc_html_e('Value', VISION_PLUGIN_NAME); ?></th>
										</tr>
										<tr>
											<td><code>id</code></td>
											<td><?php esc_html_e('item ID', VISION_PLUGIN_NAME); ?></td>
										</tr>
										<tr>
											<td><code>slug</code></td>
											<td><?php esc_html_e('slug identifier', VISION_PLUGIN_NAME); ?></td>
										</tr>
										<tr>
											<td><code>class</code></td>
											<td><?php esc_html_e('custom CSS class', VISION_PLUGIN_NAME); ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="vision-modals" class="vision-modals">
		</div>
	</div>
</div>
<!-- /end vision app -->