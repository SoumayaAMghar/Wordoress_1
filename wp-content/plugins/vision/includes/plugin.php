<?php
/**
 * Main class and entry point
 */

// If this file is called directly, abort.
if(!defined('ABSPATH')) {
	die;
}

class Vision_Builder {
	private $pluginBasename = NULL;
	
	private $ajax_action_item_update = NULL;
	private $ajax_action_item_update_status = NULL;
	private $ajax_action_settings_update = NULL;
	private $ajax_action_settings_get = NULL;
	private $ajax_action_delete_data = NULL;
	private $ajax_action_modal = NULL;
	
	private $vision_map_id = null;
	private $vision_map_version = null;
	private $shortcodes = array();
	
	function __construct($pluginBasename) {
		$this->pluginBasename = $pluginBasename;
	}
	
	function run() {
		$upload_dir = wp_upload_dir();
		$plugin_url = plugin_dir_url(dirname(__FILE__));
		
		define('VISION_PLUGIN_UPLOAD_DIR', wp_normalize_path($upload_dir['basedir'] . '/' . VISION_PLUGIN_NAME));
		define('VISION_PLUGIN_UPLOAD_URL', set_url_scheme($upload_dir['baseurl'] . '/' . VISION_PLUGIN_NAME . '/'));
		
		define('VISION_PLUGIN_PLAN', 'lite');
		
		$user = wp_get_current_user(); //is_super_admin()
		$allowed_roles = $this->getAllowedRoles();
		if((array_intersect($allowed_roles, $user->roles) || current_user_can('manage_options')) && is_admin()) {
			$this->ajax_action_item_update = VISION_PLUGIN_NAME . '_ajax_item_update';
			$this->ajax_action_item_update_status = VISION_PLUGIN_NAME . '_ajax_item_update_status';
			$this->ajax_action_settings_update = VISION_PLUGIN_NAME . '_ajax_settings_update';
			$this->ajax_action_settings_get = VISION_PLUGIN_NAME . '_ajax_settings_get';
			$this->ajax_action_delete_data = VISION_PLUGIN_NAME . '_ajax_delete_data';
			$this->ajax_action_modal = VISION_PLUGIN_NAME . '_ajax_modal';
			
			load_plugin_textdomain(VISION_PLUGIN_NAME, false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/');
			
			add_action('admin_menu', array($this, 'admin_menu'));
			add_action('admin_notices', array($this, 'admin_notices'));
			add_action('in_admin_header', array($this, 'in_admin_header'));
			add_action('wp_loaded', array($this, 'page_redirects'));
			
			// important, because ajax has another url
			add_action('wp_ajax_' . $this->ajax_action_item_update, array($this, 'ajax_item_update'));
			add_action('wp_ajax_' . $this->ajax_action_item_update_status, array($this, 'ajax_item_update_status'));
			add_action('wp_ajax_' . $this->ajax_action_settings_update, array($this, 'ajax_settings_update'));
			add_action('wp_ajax_' . $this->ajax_action_settings_get, array($this, 'ajax_settings_get'));
			add_action('wp_ajax_' . $this->ajax_action_delete_data, array($this, 'ajax_delete_data'));
			add_action('wp_ajax_' . $this->ajax_action_modal, array($this, 'ajax_modal'));
		} else {
			add_shortcode(VISION_SHORTCODE_NAME, array($this, 'shortcode'));
		}
		
		// only logged users with right roles can preview a vision map
		if(array_intersect($allowed_roles, $user->roles) || current_user_can('manage_options')) {
			add_filter('do_parse_request', array($this, 'do_parse_request'));
		}
	}
	
	function joinPaths() {
		$paths = array();
		
		foreach(func_get_args() as $arg) {
			if($arg !== '') {
				$paths[] = $arg;
			}
		}
		
		return preg_replace('#/+#','/',join('/', $paths));
	}
	
	function joinUrls() {
		$urls = array();
		
		foreach(func_get_args() as $arg) {
			if($arg !== '') {
				$urls[] = $arg;
			}
		}
		
		return preg_replace('/([^:])(\/{2,})/','$1/',join('/', $urls));
	}
	
	function IsNullOrEmptyString($str) {
		return(!isset($str) || trim($str)==='');
	}
	
	function getAllowedRoles() {
		$allowed_roles = array('administrator');
		
		$settings_key = VISION_PLUGIN_NAME . '_settings';
		$settings_value = get_option($settings_key);
		if($settings_value) {
			$settings = unserialize($settings_value);
			if(is_array($settings->roles)) $allowed_roles = array_merge($allowed_roles, $settings->roles);
		}
		
		return $allowed_roles;
	}
	
	function getCurrentUrl() {
		$home_path = rtrim(parse_url(home_url(), PHP_URL_PATH ), '/');
		$path = rtrim(substr(add_query_arg(array()), strlen( $home_path ) ), '/');
		return ($path === '') ? '/' : $path;
	}
	
	function getLoaderGlobalsName() {
		return VISION_PLUGIN_NAME . '_globals';
	}
	
	function getLoaderGlobals($timestamp) {
		$plugin_url = plugin_dir_url(dirname(__FILE__));
		
		$globals = array(
			'plan' => VISION_PLUGIN_PLAN,
			'version' => $timestamp,
			'effects_url' => $plugin_url . 'assets/css/vision-effects.min.css',
			'theme_base_url' => $plugin_url . 'assets/themes/',
			'plugin_base_url' => $plugin_url . 'assets/js/lib/vision/',
			'plugin_upload_base_url' => VISION_PLUGIN_UPLOAD_URL,
			'plugin_version' => VISION_PLUGIN_VERSION,
			'ssl' => is_ssl()
		);
		
		return $globals;
	}
	
	function embedLoader($in_footer, $timestamp) {
		$plugin_url = plugin_dir_url(dirname(__FILE__));
		wp_enqueue_script(VISION_PLUGIN_NAME . '-loader-js', $plugin_url . 'assets/js/loader.min.js', array('jquery'), VISION_PLUGIN_VERSION, $in_footer);
		wp_localize_script(VISION_PLUGIN_NAME . '-loader-js', $this->getLoaderGlobalsName(), $this->getLoaderGlobals($timestamp));
	}
	
	/**
	 * generate main css text
	 */
	function getMainCss($itemData, $itemId) {
		$upload_dir = wp_upload_dir();
		
		// create main css
		$main_css = '';
		$main_css .= '.vision-map-' . $itemId . ' {' . PHP_EOL;
		
		$main_css .= (!$this->IsNullOrEmptyString($itemData->background->color) ? 'background-color:' . $itemData->background->color . ';' . PHP_EOL : '');
		if(!$this->IsNullOrEmptyString($itemData->background->image->url)) {
			$imageUrl = ($itemData->background->image->relative ? $upload_dir['baseurl'] : '') . $itemData->background->image->url;
			$main_css .= 'background-image:url(' . $imageUrl . ');' . PHP_EOL;
		}
		$main_css .= ($itemData->background->size ? 'background-size:' . $itemData->background->size . ';' . PHP_EOL : '');
		$main_css .= ($itemData->background->repeat ? 'background-repeat:' . $itemData->background->repeat . ';' . PHP_EOL : '');
		$main_css .= ($itemData->background->position ? 'background-position:' . $itemData->background->position . ';' . PHP_EOL : '');
		
		$main_css .= '}' . PHP_EOL;
		
		$layerId = 0;
		foreach($itemData->layers as $layerKey => $layer) {
			if(!$layer->visible) {
				continue;
			}
			
			$layerId++;
			$layerSelector = '.vision-map-' . $itemId . ' .vision-layers [data-layer-id="' . $layer->id . '"] .vision-body';
			
			// main
			$main_css .= $layerSelector . ' {' . PHP_EOL;
			switch($layer->type) {
				case 'link': {
					$main_css .= ($layer->link->normalColor ? 'background-color:' . $layer->link->normalColor . ';' . PHP_EOL : '');
					$main_css .= ($layer->link->radius != NULL ? 'border-radius:' . $layer->link->radius . ';' . PHP_EOL : '');
				} break;
				case 'image': {
					$main_css .= (!$this->IsNullOrEmptyString($layer->image->background->color) ? 'background-color:' . $layer->image->background->color . ';' . PHP_EOL : '');
					if(!$this->IsNullOrEmptyString($layer->image->background->file->url)) {
						$imageUrl = ($layer->image->background->file->relative ? $upload_dir['baseurl'] : '') . $layer->image->background->file->url;
						$main_css .= 'background-image:url(' . $imageUrl . ');' . PHP_EOL;
					}
					$main_css .= ($layer->image->background->size ? 'background-size:' . $layer->image->background->size . ';' . PHP_EOL : '');
					$main_css .= ($layer->image->background->repeat ? 'background-repeat:' . $layer->image->background->repeat . ';' . PHP_EOL : '');
					$main_css .= ($layer->image->background->position ? 'background-position:' . $layer->image->background->position . ';' . PHP_EOL : '');
				} break;
				case 'text': {
					$main_css .= (!$this->IsNullOrEmptyString($layer->text->background->color) ? 'background-color:' . $layer->text->background->color . ';' . PHP_EOL : '');
					if(!$this->IsNullOrEmptyString($layer->text->background->file->url)) {
						$imageUrl = ($layer->text->background->file->relative ? $upload_dir['baseurl'] : '') . $layer->text->background->file->url;
						$main_css .= 'background-image:url(' . $imageUrl . ');' . PHP_EOL;
					}
					$main_css .= ($layer->text->background->size ? 'background-size:' . $layer->text->background->size . ';' . PHP_EOL : '');
					$main_css .= ($layer->text->background->repeat ? 'background-repeat:' . $layer->text->background->repeat . ';' . PHP_EOL : '');
					$main_css .= ($layer->text->background->position ? 'background-position:' . $layer->text->background->position . ';' . PHP_EOL : '');
					
					$main_css .= ($layer->text->font ? 'font-family:"' . str_replace('+', ' ', $layer->text->font) . '",sans-serif;' . PHP_EOL : '');
					$main_css .= ($layer->text->color ? 'color:' . $layer->text->color . ';' . PHP_EOL : '');
					$main_css .= ($layer->text->size != NULL ? 'font-size:' . $layer->text->size . 'px;' . PHP_EOL : '');
					$main_css .= ($layer->text->lineHeight != NULL ? 'line-height:' . $layer->text->lineHeight . 'px;' . PHP_EOL : '');
					$main_css .= ($layer->text->align ? 'text-align:' . $layer->text->align . ';' . PHP_EOL : '');
					$main_css .= ($layer->text->letterSpacing != NULL ? 'letter-spacing:' . $layer->text->letterSpacing . 'px;' . PHP_EOL : '');
				} break;
			}
			$main_css .= '}' . PHP_EOL;
			
			if($layer->type == 'link') {
				$main_css .= $layerSelector . ':hover {' . PHP_EOL;
				$main_css .= ($layer->link->hoverColor ? 'background-color:' . $layer->link->hoverColor . ';' . PHP_EOL : '');
				$main_css .= '}' . PHP_EOL;
			}
		}
		
		return $main_css;
	}
	
	/**
	 * Shortcode output for the plugin
	 */
	function shortcode($atts) {
		extract(shortcode_atts(array('id'=>0, 'slug'=>NULL, 'class'=>NULL), $atts));
		
		if(!$id && !$slug) {
			return '<p>' . esc_html__('Error: invalid vision identifier attribute', VISION_PLUGIN_NAME) . '</p>';
		}
		
		global $wpdb;
		$table = $wpdb->prefix . VISION_PLUGIN_NAME;
		$upload_dir = wp_upload_dir();
		
		$sql = ($id ? sprintf('SELECT * FROM %1$s WHERE id=%2$d AND NOT deleted', $table, $id) : sprintf('SELECT * FROM %1$s WHERE slug="%2$s" AND NOT deleted LIMIT 0, 1', $table, $slug));
		$item = $wpdb->get_row($sql, OBJECT);
		$preview = filter_input(INPUT_GET, 'preview', FILTER_SANITIZE_NUMBER_INT);
		
		if($item && ($item->active || (!$item->active && $preview == 1))) {
			$version = strtotime(mysql2date('d M Y H:i:s', $item->modified));
			$itemData = unserialize($item->data);
			$id = $item->id;
			$id_postfix = strtolower(wp_generate_password(5, false)); // generate unique postfix for $id to avoid clashes with multiple same shortcode use
			$id_element = 'vision-' . $id . '-' . $id_postfix;
			
			array_push($this->shortcodes, array(
				'id'            => $item->id,
				'version'       => $version
			));
			
			if(sizeof($this->shortcodes) == 1) {
				$this->embedLoader(true, $version);
			}
			
			// debug
			//ob_start();
			//var_dump($value);
			//$data = ob_get_clean();
			//$fp = fopen("m:\debug.txt", "w");
			//fwrite($fp, $data);
			//fclose($fp);
			
			ob_start(); // turn on buffering
			
			echo '<!-- vision begin -->' . PHP_EOL;
			
			echo '<div ';
			//echo 'id="' . $id_element . '" ';
			echo (property_exists($itemData, 'containerId') && $itemData->containerId ? 'id="' . $itemData->containerId . '" ':'');
			echo 'class="vision-map vision-map-' . $id . ($class ? ' ' . $class : '') . '"';
			echo 'data-json-src="'. VISION_PLUGIN_UPLOAD_URL . $item->id . '/config.json?ver=' . $version . '" ';
			echo 'data-item-id="' . $item->id . '" ';
			echo 'tabindex="1" ';
			echo 'style="display:none;" ';
			echo '>' . PHP_EOL;
				
				//=============================================
				// STORE BEGIN
				echo '<div class="vision-store">' . PHP_EOL;
				
				echo '<div class="vision-layers-data">' . PHP_EOL;
				foreach($itemData->layers as $layerKey => $layer) {
					if(!$layer->visible) {
						continue;
					}
					
					//=============================================
					// LAYER BEGIN
					echo '<div class="vision-layer" data-layer-id="' . $layer->id . '">';
					
					if($layer->contentData) {
						echo do_shortcode($layer->contentData);
					}
					
					if($layer->type == 'text') {
						echo $layer->text->data;
					}
					
					if($layer->type == 'svg') {
						if(!$this->IsNullOrEmptyString($layer->svg->file->url)) {
							$svgUrl = ($layer->svg->file->url ? ($layer->svg->file->relative ? $upload_dir['baseurl'] : '') . $layer->svg->file->url : '');
							echo file_get_contents($svgUrl, FILE_USE_INCLUDE_PATH);
							
							/*
							$url = 'http://example.com/path-to/fb_logo.svg';
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
							$svg = curl_exec($ch);
							curl_close($ch);
							echo $svg;
							*/
						}
					}
					
					echo '</div>' . PHP_EOL;
					// LAYER END
					//=============================================
				}
				echo '</div>' . PHP_EOL;
				
				echo '<div class="vision-tooltips-data">' . PHP_EOL;
				foreach($itemData->layers as $layerKey => $layer) {
					if(!$layer->visible) {
						continue;
					}
					
					//=============================================
					// TOOLTIP BEGIN
					echo '<div class="vision-data" data-layer-id="' . $layer->id . '">';
					echo do_shortcode($layer->tooltip->data);
					echo '</div>' . PHP_EOL;
					// TOOLTIP END
					//=============================================
				}
				echo '</div>' . PHP_EOL;
				
				echo '<div class="vision-popovers-data">' . PHP_EOL;
				foreach($itemData->layers as $layerKey => $layer) {
					if(!$layer->visible) {
						continue;
					}
					
					//=============================================
					// POPOVER BEGIN
					echo '<div class="vision-data" data-layer-id="' . $layer->id . '">';
					echo do_shortcode($layer->popover->data);
					echo '</div>' . PHP_EOL;
					// POPOVER END
					//=============================================
				}
				echo '</div>' . PHP_EOL;
				
				echo '</div>' . PHP_EOL;
				// STORE END
				//=============================================
				
			echo '</div>' . PHP_EOL;
			echo '<!-- vision end -->' . PHP_EOL;
			
			$output = ob_get_contents(); // get the buffered content into a var
			ob_end_clean(); // clean buffer
			
			return $output;
		}
		
		return '<p>' . esc_html__('Error: the vision item can’t be found', VISION_PLUGIN_NAME) . '</p>';
	}
	
	/**
	* output preview header data
	*/
	function do_preview_head() {
		$plugin_url = plugin_dir_url(dirname(__FILE__));
		
		$preview_css_src = $plugin_url . 'assets/css/preview.min.css?ver=' . VISION_PLUGIN_VERSION;
		$preview_js_src = $plugin_url . 'assets/js/preview.min.js?ver=' . VISION_PLUGIN_VERSION;
		$loader_js_src = $plugin_url . 'assets/js/loader.min.js?ver=' . VISION_PLUGIN_VERSION;
		$jquery_js_src = null;
		
		global $wp_scripts;
		foreach($wp_scripts->registered as $dependency) :
			if($dependency->handle == 'jquery-core' && $dependency->src) {
				$jquery_js_src = (substr($dependency->src,0,1)=='/' ? get_site_url() . $dependency->src : $dependency->src);
				break;
			};
		endforeach;
		
		$head = '';
		$head .= wp_sprintf('<link rel="stylesheet" type="text/css" href="%s">', $preview_css_src) . PHP_EOL;
		$head .= wp_sprintf('<script type="text/javascript" src="%s"></script>', ($jquery_js_src ? $jquery_js_src : 'http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js')) . PHP_EOL;
		$head .= wp_sprintf('<script type="text/javascript" src="%s"></script>', $preview_js_src) . PHP_EOL;
		$head .= wp_sprintf('<script type="text/javascript">') . PHP_EOL;
		$head .= wp_sprintf('/* <![CDATA[ */') . PHP_EOL;
		$head .= wp_sprintf('var %s=%s;',$this->getLoaderGlobalsName(), json_encode($this->getLoaderGlobals($this->vision_map_version))) . PHP_EOL;
		$head .= wp_sprintf('/* ]]> */') . PHP_EOL;
		$head .= wp_sprintf('</script>') . PHP_EOL;
		$head .= wp_sprintf('<script type="text/javascript" src="%s"></script>', $loader_js_src) . PHP_EOL;
		
		echo $head;
	}
	
	/**
	* output preview content
	*/
	function do_preview() {
		$atts = array('id'=>$this->vision_map_id);
		echo $this->shortcode($atts);
	}
	
	/**
	* output preview footer data
	*/
	function do_preview_footer() {
	}
	
	/**
	* Run a filter to obtain some custom url settings, compare them to the current url
	* and if a match is found the custom callback is fired, the custom view is loaded
	* and request is stopped.
	*/
	function do_parse_request($result) {
		if(current_filter() !== 'do_parse_request') {
			return $result;
		}
		
		$current = $this->getCurrentUrl();
		if(preg_match('/vision\/map\/([a-z0-9_-]+)/', $current, $matches)) {
			$preview = filter_input(INPUT_GET, 'preview', FILTER_SANITIZE_NUMBER_INT);
			
			if(is_numeric($matches[1])) {
				$vision_map_id = $matches[1];
				$shortcode = false;
				
				if($vision_map_id != null) {
					global $wpdb;
					$table = $wpdb->prefix . VISION_PLUGIN_NAME;
					
					$sql = sprintf('SELECT * FROM %1$s WHERE id=%2$d AND NOT deleted', $table, $vision_map_id);
					$item = $wpdb->get_row($sql, OBJECT);
					
					if($item && ($item->active || (!$item->active && $preview == 1))) {
						$this->vision_map_id = $item->id;
						$this->vision_map_version = strtotime(mysql2date('Y-m-d H:i:s', $item->modified));
						
						$shortcode = true;
					}
				}
			} else {
				$vision_map_slug = $matches[1];
				$shortcode = false;
				
				if($vision_map_slug != null) {
					global $wpdb;
					$table = $wpdb->prefix . VISION_PLUGIN_NAME;
					
					$sql = sprintf('SELECT * FROM %1$s WHERE slug="%2$s" AND NOT deleted', $table, $vision_map_slug);
					$item = $wpdb->get_row($sql, OBJECT);
					
					if($item && ($item->active || (!$item->active && $preview == 1))) {
						$this->vision_map_id = $item->id;
						$this->vision_map_version = strtotime(mysql2date('Y-m-d H:i:s', $item->modified));
						
						$shortcode = true;
					}
				}
			}
			
			if($shortcode) {
				require_once(plugin_dir_path(dirname(__FILE__)) . 'includes/page-preview.php');
				exit();
			}
		}
		
		return $result;
	}
	
	/**
	 * Prepare upload directory
	 */
	function admin_notices() {
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		
		if(!($page===VISION_PLUGIN_NAME || 
			 $page===VISION_PLUGIN_NAME . '_item')) {
			return;
		}
		
		if(!file_exists(VISION_PLUGIN_UPLOAD_DIR)) {
			wp_mkdir_p(VISION_PLUGIN_UPLOAD_DIR);
		}
		
		if(!file_exists(VISION_PLUGIN_UPLOAD_DIR)) {
			echo '<div class="notice notice-error is-dismissible">';
			echo '<p>' . sprintf(esc_html__('The "%s" directory could not be created', VISION_PLUGIN_NAME), '<b>' . VISION_PLUGIN_NAME . '</b>') . '</p>';
			echo '<p>' . esc_html__('Please run the following commands in order to make the directory', VISION_PLUGIN_NAME) . '<br>';
			echo '<b>mkdir ' . VISION_PLUGIN_UPLOAD_DIR . '</b><br>';
			echo '<b>chmod 777 ' . VISION_PLUGIN_UPLOAD_DIR . '</b></p>';
			echo '</div>';
			return;
		}
		
		if(!wp_is_writable(VISION_PLUGIN_UPLOAD_DIR)) {
			echo '<div class="notice notice-error is-dismissible">';
			echo '<p>' . sprintf(esc_html__('The "%s" directory is not writable, therefore the css and js files cannot be saved.', VISION_PLUGIN_NAME), '<b>' . VISION_PLUGIN_NAME . '</b>') . '</p>';
			echo '<p>' . esc_html__('Please run the following commands in order to make the directory', VISION_PLUGIN_NAME) . '<br>';
			echo '<b>chmod 777 ' . VISION_PLUGIN_UPLOAD_DIR . '</b></p>';
			echo '</div>';
			return;
		}
		
		if(!file_exists(VISION_PLUGIN_UPLOAD_DIR . '/' . 'index.php')) {
			$data = '<?php' . PHP_EOL . '// silence is golden' . PHP_EOL . '?>';
			@file_put_contents(VISION_PLUGIN_UPLOAD_DIR . '/' . 'index.php', $data);
		}
	}
	
	/**
	 * Fires at the beginning of the content section in an admin page
	 */
	function in_admin_header() {
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		
		if(!(($page===VISION_PLUGIN_NAME) ||
			 ($page===VISION_PLUGIN_NAME . '_item') ||
			 ($page===VISION_PLUGIN_NAME . '_settings'))) {
			return;
		}
		
		remove_all_actions('admin_notices');
		remove_all_actions('all_admin_notices');
		add_action('admin_notices', array($this, 'admin_notices'));
	}
	
	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	function admin_menu() {
		// add "edit_posts" if we want to give access to author, editor and contributor roles
		add_menu_page(esc_html__('Vision', VISION_PLUGIN_NAME), esc_html__('Vision', VISION_PLUGIN_NAME), 'read', VISION_PLUGIN_NAME, array( $this, 'admin_menu_page_items' ), 'dashicons-format-image');
		add_submenu_page(VISION_PLUGIN_NAME, esc_html__('Vision', VISION_PLUGIN_NAME), esc_html__('All Items', VISION_PLUGIN_NAME), 'read', VISION_PLUGIN_NAME, array( $this, 'admin_menu_page_items' ));
		add_submenu_page(VISION_PLUGIN_NAME, esc_html__('Vision', VISION_PLUGIN_NAME), esc_html__('Add New', VISION_PLUGIN_NAME), 'read', VISION_PLUGIN_NAME . '_item', array( $this, 'admin_menu_page_item' ));
		add_submenu_page(VISION_PLUGIN_NAME, esc_html__('Vision', VISION_PLUGIN_NAME), esc_html__('Settings', VISION_PLUGIN_NAME), 'manage_options', VISION_PLUGIN_NAME . '_settings', array( $this, 'admin_menu_page_settings' ));
	}
	
	/**
	 * Custom redirects
	 */
	function page_redirects() {
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		
		if($page===VISION_PLUGIN_NAME) {
			$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
			if($action) {
				$url = remove_query_arg(array('action', 'id', '_wpnonce'), $_SERVER['REQUEST_URI']);
				header('Refresh:0; url="' . $url . '"', true, 303);
				//wp_redirect($url); // does not work delete and dublicate operations on XAMPP
			}
		}
	}
	
	/**
	 * Show admin menu items page
	 */
	function admin_menu_page_items() {
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		
		if($page===VISION_PLUGIN_NAME) {
			$plugin_url = plugin_dir_url( dirname(__FILE__) );
			$upload_dir = wp_upload_dir();
			
			// styles
			wp_enqueue_style(VISION_PLUGIN_NAME . '_admin_css', $plugin_url . 'assets/css/admin.min.css', array(), VISION_PLUGIN_VERSION, 'all' );
			wp_enqueue_style(VISION_PLUGIN_NAME . '_fontawesome', $plugin_url . 'assets/css/font-awesome.min.css', array(), VISION_PLUGIN_NAME, 'all' );
			
			// scripts
			wp_enqueue_script(VISION_PLUGIN_NAME . '_admin_js', $plugin_url . 'assets/js/admin.min.js', array('jquery'), VISION_PLUGIN_VERSION, false );
			
			// global settings to help ajax work
			$globals = array(
				'plan' => VISION_PLUGIN_PLAN,
				'msg_pro_title' => esc_html__('Available only in Pro version', VISION_PLUGIN_NAME),
				'upload_url' => $upload_dir['baseurl'],
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( VISION_PLUGIN_NAME . '_ajax' ),
				'ajax_msg_error' => esc_html__('Uncaught Error', VISION_PLUGIN_NAME) //Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information
			);
			
			$globals['ajax_action_update'] = $this->ajax_action_item_update_status;
			
			require_once( plugin_dir_path( dirname(__FILE__) ) . 'includes/list-table-items.php' );
			require_once( plugin_dir_path( dirname(__FILE__) ) . 'includes/page-items.php' );
			
			// set global settings
			wp_localize_script(VISION_PLUGIN_NAME . '_admin_js', VISION_PLUGIN_NAME . '_globals', $globals);
		}
	}
	
	/**
	 * Show admin menu item page
	 */
	function admin_menu_page_item() {
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		
		if($page===VISION_PLUGIN_NAME . '_item') {
			$plugin_url = plugin_dir_url(dirname(__FILE__));
			$upload_dir = wp_upload_dir();
			
			// styles
			wp_enqueue_style(VISION_PLUGIN_NAME . '_admin_css', $plugin_url . 'assets/css/admin.min.css', array(), VISION_PLUGIN_VERSION, 'all' );
			wp_enqueue_style(VISION_PLUGIN_NAME . '_fontawesome', $plugin_url . 'assets/css/font-awesome.min.css', array(), VISION_PLUGIN_NAME, 'all' );
			wp_enqueue_style(VISION_PLUGIN_NAME . '_vision_effects_css', $plugin_url . 'assets/css/vision-effects.min.css', array(), VISION_PLUGIN_VERSION, 'all' );
			
			// scripts
			wp_enqueue_script(VISION_PLUGIN_NAME . '_ace', $plugin_url . 'assets/js/lib/ace/ace.js', array(), VISION_PLUGIN_VERSION, false );
			wp_enqueue_script(VISION_PLUGIN_NAME . '-url-js', $plugin_url . 'assets/js/lib/url/url.min.js', array(), VISION_PLUGIN_VERSION, false );
			wp_enqueue_script(VISION_PLUGIN_NAME . '_admin_js', $plugin_url . 'assets/js/admin.min.js', array('jquery'), VISION_PLUGIN_VERSION, false );
			wp_enqueue_media();
			
			// global settings to help ajax work
			$globals = array(
				'plan' => VISION_PLUGIN_PLAN,
				'msg_pro_title' => esc_html__('Available only in Pro version', VISION_PLUGIN_NAME),
				'msg_edit_text' => esc_html__('Edit your text here', VISION_PLUGIN_NAME),
				'msg_custom_js_error' => esc_html__('Custom js code error', VISION_PLUGIN_NAME),
				'msg_layer_id_error' => esc_html__('The layer ID should be unique', VISION_PLUGIN_NAME),
				'wp_base_url' => get_site_url(),
				'upload_base_url' => $upload_dir['baseurl'],
				'plugin_base_url' => $plugin_url,
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( VISION_PLUGIN_NAME . '_ajax' ),
				'ajax_msg_error' => esc_html__('Uncaught Error', VISION_PLUGIN_NAME) //Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information
			);
			
			$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
			
			$globals['ajax_action_get'] = $this->ajax_action_settings_get;
			$globals['ajax_action_update'] = $this->ajax_action_item_update;
			$globals['ajax_action_modal'] = $this->ajax_action_modal;
			$globals['ajax_item_id'] = $id;
			$globals['settings'] = NULL;
			$globals['config'] = NULL;
			
			$settings_key = VISION_PLUGIN_NAME . '_settings';
			$settings_value = get_option($settings_key);
			if($settings_value) {
				$globals['settings'] = unserialize($settings_value); // json_encode(unserialize($settings_value)) problem with double quotes
			}
			
			// get item data from DB
			if($id) {
				global $wpdb;
				$table = $wpdb->prefix . VISION_PLUGIN_NAME;
				
				$query = $wpdb->prepare('SELECT * FROM ' . $table . ' WHERE id=%s', $id);
				$item = $wpdb->get_row($query, OBJECT);
				if($item) {
					//{
					//id: null,
					//title: null,
					//active: true,
					//config: {...}
					//}
					$globals['config'] = unserialize($item->data); // json_encode(unserialize($item->data)) problem with double quotes
				}
			} else {
				// new item
				$item = (object) array(
					'author' => get_current_user_id(),
					'editor' => get_current_user_id(),
					'created' => current_time('mysql', 1),
					'modified' => current_time('mysql', 1)
				);
			}
			
			require_once( plugin_dir_path( dirname(__FILE__) ) . 'includes/page-item.php' );
			
			// set global settings
			wp_localize_script(VISION_PLUGIN_NAME . '_admin_js', VISION_PLUGIN_NAME . '_globals', $globals);
		}
	}
	
	/**
	 * Show admin menu settings page
	 */
	function admin_menu_page_settings() {
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		
		if($page===VISION_PLUGIN_NAME . '_settings') {
			$plugin_url = plugin_dir_url(dirname(__FILE__));
			
			// styles
			wp_enqueue_style(VISION_PLUGIN_NAME . '_admin_css', $plugin_url . 'assets/css/admin.min.css', array(), VISION_PLUGIN_VERSION, 'all' );
			wp_enqueue_style(VISION_PLUGIN_NAME . '_fontawesome', $plugin_url . 'assets/css/font-awesome.min.css', array(), VISION_PLUGIN_NAME, 'all' );
			
			// scripts
			wp_enqueue_script(VISION_PLUGIN_NAME . '_admin_js', $plugin_url . 'assets/js/admin.min.js', array('jquery'), VISION_PLUGIN_VERSION, false );
			
			// global settings to help ajax work
			$globals = array(
				'plan' => VISION_PLUGIN_PLAN,
				'msg_pro_title' => esc_html__('Available only in Pro version', VISION_PLUGIN_NAME),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( VISION_PLUGIN_NAME . '_ajax' ),
				'ajax_msg_error' => esc_html__('Uncaught Error', VISION_PLUGIN_NAME) //Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information
			);
			
			$globals['ajax_action_update'] = $this->ajax_action_settings_update;
			$globals['ajax_action_get'] = $this->ajax_action_settings_get;
			$globals['ajax_action_modal'] = $this->ajax_action_modal;
			$globals['ajax_action_delete_data'] = $this->ajax_action_delete_data;
			$globals['config'] = NULL;
			
			// read settings
			$settings_key = VISION_PLUGIN_NAME . '_settings';
			$settings_value = get_option($settings_key);
			if($settings_value) {
				$globals['config'] = json_encode(unserialize($settings_value));
			}
			
			require_once(plugin_dir_path( dirname(__FILE__) ) . 'includes/page-settings.php' );
			
			// set global settings
			wp_localize_script(VISION_PLUGIN_NAME . '_admin_js', VISION_PLUGIN_NAME . '_globals', $globals);
		}
	}
	
	/**
	 * Ajax update item state
	 */
	function ajax_item_update_status() {
		$error = false;
		$data = array();
		$config = filter_input(INPUT_POST, 'config', FILTER_UNSAFE_RAW);
		
		if(check_ajax_referer(VISION_PLUGIN_NAME . '_ajax', 'nonce', false)) {
			global $wpdb;
			
			$table = $wpdb->prefix . VISION_PLUGIN_NAME;
			$config = json_decode($config);
			$result = false;
			
			if(isset($config->id) && isset($config->active)) {
				$query = $wpdb->prepare('SELECT * FROM ' . $table . ' WHERE id=%s', $config->id);
				$item = $wpdb->get_row($query, OBJECT );
				
				if($item && (current_user_can('manage_options') || get_current_user_id()==$item->author) ) {
					$itemData = unserialize($item->data);
					$itemData->active = $config->active;
					
					$result = $wpdb->update(
						$table,
						array(
							'active'=> $itemData->active,
							'data' => serialize($itemData)
						),
						array('id'=>$config->id));
				}
			}
			
			if($result) {
				$data['id'] = $config->id;
				$data['msg'] = esc_html__('The item was successfully updated', VISION_PLUGIN_NAME);
			} else {
				$error = true;
				$data['msg'] = esc_html__('The operation failed, can\'t update item', VISION_PLUGIN_NAME);
			}
		} else {
			$error = true;
			$data['msg'] = esc_html__('The operation failed', VISION_PLUGIN_NAME);
		}
		
		if($error) {
			wp_send_json_error($data);
		} else {
			wp_send_json_success($data);
		}
		
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	
	/**
	 * Ajax update item data
	 */
	function ajax_item_update() {
		$error = false;
		$data = array();
		
		if(check_ajax_referer(VISION_PLUGIN_NAME . '_ajax', 'nonce', false)) {
			global $wpdb;
			$table = $wpdb->prefix . VISION_PLUGIN_NAME;
			
			$inputId = filter_input(INPUT_POST, 'id', FILTER_UNSAFE_RAW);
			$inputData = filter_input(INPUT_POST, 'data', FILTER_UNSAFE_RAW);
			$inputConfig = filter_input(INPUT_POST, 'config', FILTER_UNSAFE_RAW);
			$itemData = json_decode($inputData);
			$itemConfig = json_decode($inputConfig);
			$flag = true;
			
			if(VISION_PLUGIN_PLAN == 'lite') {
				$rowcount = $wpdb->get_var('SELECT COUNT(*) FROM ' . $table );
				
				if(!($rowcount == 0 || ($rowcount == 1 && $inputId))) {
					$flag = false;
					$error = true;
					$data['msg'] = esc_html__('The operation failed, you can work only with one item. To create more, buy the pro version.', VISION_PLUGIN_NAME);
				}
			}
			
			if($flag) {
				$itemConfig->modified = current_time('mysql', 1);
				
				if($inputId) {
					$result = false;
					
					$query = $wpdb->prepare('SELECT * FROM ' . $table . ' WHERE id=%s', $inputId);
					$item = $wpdb->get_row($query, OBJECT);
					if($item && (current_user_can('manage_options') || get_current_user_id()==$item->author) ) {
						$itemData->slug = sanitize_title(($itemData->slug ? $itemData->slug : $itemData->title));
						
						$result = $wpdb->update(
							$table,
							array(
								'title' => $itemData->title,
								'slug' => $itemData->slug,
								'active' => $itemData->active,
								'data' => serialize($itemData),
								'config' => serialize($itemConfig),
								//'author' => get_current_user_id(),
								'editor' => get_current_user_id(),
								//'date' => NULL,
								'modified' => current_time('mysql', 1)
							),
							array('id'=>$inputId));
					}
					
					if($result) {
						$data['id'] = $inputId;
						$data['msg'] = esc_html__('The item was successfully updated', VISION_PLUGIN_NAME);
					} else {
						$error = true;
						$data['msg'] = esc_html__('The operation failed, can\'t update item', VISION_PLUGIN_NAME);
					}
				} else {
					$itemData->slug = sanitize_title(($itemData->slug ? $itemData->slug : $itemData->title));
					
					$result = $wpdb->insert(
						$table,
						array(
							'title' => $itemData->title,
							'slug' => $itemData->slug,
							'active' => $itemData->active,
							'data' => serialize($itemData),
							'config' => serialize($itemConfig),
							'author' => get_current_user_id(),
							'editor' => get_current_user_id(),
							'created' => current_time('mysql', 1),
							'modified' => current_time('mysql', 1)
						));
					
					if($result) {
						$data['id'] = $inputId = $wpdb->insert_id;
						$data['msg'] = esc_html__('The item was successfully created', VISION_PLUGIN_NAME);
					} else {
						$error = true;
						$data['msg'] = esc_html__('The operation failed, can\'t create item', VISION_PLUGIN_NAME);
					}
				}
			}
			
			//======================================
			// [filemanager] create an external file
			if(!$error && wp_is_writable(VISION_PLUGIN_UPLOAD_DIR)) {
				$file_json = 'config.json';
				$file_main_css = 'main.css';
				$file_custom_css = 'custom.css';
				$file_root_path = VISION_PLUGIN_UPLOAD_DIR . '/' . $inputId . '/';
				
				if(!is_dir($file_root_path)) {
					mkdir($file_root_path);
				}
				
				@file_put_contents($file_root_path . $file_json, json_encode($itemConfig));
				@file_put_contents($file_root_path . $file_main_css, $this->getMainCss($itemData, $inputId));
				@file_put_contents($file_root_path . $file_custom_css, $itemData->customCSS->data);
			}
			//======================================
		} else {
			$error = true;
			$data['msg'] = esc_html__('The operation failed', VISION_PLUGIN_NAME);
		}
		
		if($error) {
			wp_send_json_error($data);
		} else {
			wp_send_json_success($data);
		}
		
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	
	/**
	 * Ajax update settings data
	 */
	function ajax_settings_update() {
		$error = false;
		$data = array();
		$config = filter_input(INPUT_POST, 'config', FILTER_UNSAFE_RAW);
		
		if(check_ajax_referer(VISION_PLUGIN_NAME . '_ajax', 'nonce', false)) {
			$settings_key = VISION_PLUGIN_NAME . '_settings';
			$settings_value = serialize(json_decode($config));
			$result = false;
			
			if(get_option($settings_key) == false) {
				$deprecated = null;
				$autoload = 'no';
				$result = add_option($settings_key, $settings_value, $deprecated, $autoload);
			} else {
				$old_settings_value = get_option($settings_key);
				if($old_settings_value === $settings_value) {
					$result = true;
				} else {
					$result = update_option($settings_key, $settings_value);
				}
			}
			
			if($result) {
				$data['msg'] = esc_html__('The settings were successfully updated', VISION_PLUGIN_NAME);
			} else {
				$error = true;
				$data['msg'] = esc_html__('The operation failed, can\'t update settings', VISION_PLUGIN_NAME);
			}
		}
		
		if($error) {
			wp_send_json_error($data);
		} else {
			wp_send_json_success($data);
		}
		
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	
	/**
	 * Ajax settings get data
	 */
	function ajax_settings_get() {
		$error = false;
		$data = array();
		$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
		
		if(check_ajax_referer(VISION_PLUGIN_NAME . '_ajax', 'nonce', false)) {
			switch($type) {
				case 'roles': {
					$data['list'] = array();
					
					$roles = wp_roles()->roles;
					foreach($roles as $key => $role) {
						if(array_key_exists('read', $role['capabilities'])) {
							array_push($data['list'], array('id' => $key, 'name' =>  translate_user_role($role['name'])));
						}
					}
				}
				break;
				case 'themes': {
					$data['list'] = array();
					
					$files = glob(plugin_dir_path( dirname(__FILE__) ) . 'assets/themes/*.min.css');
					foreach($files as $file) {
						$filename = basename($file, '.min.css');
						array_push($data['list'], array('id' => $filename, 'title' => str_replace('-', ' ', $filename)));
					}
				}
				break;
				case 'editor-themes': {
					$data['list'] = array();
					
					$files = glob(plugin_dir_path( dirname(__FILE__) ) . 'assets/js/lib/ace/theme-*.js');
					foreach($files as $file) {
						$filename = str_replace('theme-','',basename($file, '.js'));
						array_push($data['list'], array('id' => $filename, 'title' => str_replace('_', ' ', $filename)));
					}
				}
				break;
				case 'fonts': {
					$data['list'] = array(
						array('fontname' => 'none'),
						array('fontname' => 'Aclonica'),
						array('fontname' => 'Allan'),
						array('fontname' => 'Annie+Use+Your+Telescope'),
						array('fontname' => 'Anonymous+Pro'),
						array('fontname' => 'Allerta+Stencil'),
						array('fontname' => 'Allerta'),
						array('fontname' => 'Amaranth'),
						array('fontname' => 'Anton'),
						array('fontname' => 'Architects+Daughter'),
						array('fontname' => 'Arimo'),
						array('fontname' => 'Artifika'),
						array('fontname' => 'Arvo'),
						array('fontname' => 'Asset'),
						array('fontname' => 'Astloch'),
						array('fontname' => 'Bangers'),
						array('fontname' => 'Bentham'),
						array('fontname' => 'Bevan'),
						array('fontname' => 'Bigshot+One'),
						array('fontname' => 'Bowlby+One'),
						array('fontname' => 'Bowlby+One+SC'),
						array('fontname' => 'Brawler'),
						//array('fontname' => 'Buda:300'),
						array('fontname' => 'Cabin'),
						array('fontname' => 'Calligraffitti'),
						array('fontname' => 'Candal'),
						array('fontname' => 'Cantarell'),
						array('fontname' => 'Cardo'),
						array('fontname' => 'Carter One'),
						array('fontname' => 'Caudex'),
						array('fontname' => 'Cedarville+Cursive'),
						array('fontname' => 'Cherry+Cream+Soda'),
						array('fontname' => 'Chewy'),
						array('fontname' => 'Coda'),
						array('fontname' => 'Coming+Soon'),
						array('fontname' => 'Copse'),
						//array('fontname' => 'Corben:700'),
						array('fontname' => 'Cousine'),
						array('fontname' => 'Covered+By+Your+Grace'),
						array('fontname' => 'Crafty+Girls'),
						array('fontname' => 'Crimson+Text'),
						array('fontname' => 'Crushed'),
						array('fontname' => 'Cuprum'),
						array('fontname' => 'Damion'),
						array('fontname' => 'Dancing+Script'),
						array('fontname' => 'Dawning+of+a+New+Day'),
						array('fontname' => 'Didact+Gothic'),
						array('fontname' => 'Droid+Sans'),
						array('fontname' => 'Droid+Sans+Mono'),
						array('fontname' => 'Droid+Serif'),
						array('fontname' => 'EB+Garamond'),
						array('fontname' => 'Expletus+Sans'),
						array('fontname' => 'Fontdiner+Swanky'),
						array('fontname' => 'Forum'),
						array('fontname' => 'Francois+One'),
						array('fontname' => 'Geo'),
						array('fontname' => 'Give+You+Glory'),
						array('fontname' => 'Goblin+One'),
						array('fontname' => 'Goudy+Bookletter+1911'),
						array('fontname' => 'Gravitas+One'),
						array('fontname' => 'Gruppo'),
						array('fontname' => 'Hammersmith+One'),
						array('fontname' => 'Holtwood+One+SC'),
						array('fontname' => 'Homemade+Apple'),
						array('fontname' => 'Inconsolata'),
						array('fontname' => 'Indie+Flower'),
						array('fontname' => 'IM+Fell+DW+Pica'),
						array('fontname' => 'IM+Fell+DW+Pica+SC'),
						array('fontname' => 'IM+Fell+Double+Pica'),
						array('fontname' => 'IM+Fell+Double+Pica+SC'),
						array('fontname' => 'IM+Fell+English'),
						array('fontname' => 'IM+Fell+English+SC'),
						array('fontname' => 'IM+Fell+French+Canon'),
						array('fontname' => 'IM+Fell+French+Canon+SC'),
						array('fontname' => 'IM+Fell+Great+Primer'),
						array('fontname' => 'IM+Fell+Great+Primer+SC'),
						array('fontname' => 'Irish+Grover'),
						array('fontname' => 'Irish+Growler'),
						array('fontname' => 'Istok+Web'),
						array('fontname' => 'Josefin+Sans'),
						array('fontname' => 'Josefin+Slab'),
						array('fontname' => 'Judson'),
						array('fontname' => 'Jura'),
						//array('fontname' => 'Jura:500'),
						//array('fontname' => 'Jura:600'),
						array('fontname' => 'Just+Another+Hand'),
						array('fontname' => 'Just+Me+Again+Down+Here'),
						array('fontname' => 'Kameron'),
						array('fontname' => 'Kenia'),
						array('fontname' => 'Kranky'),
						array('fontname' => 'Kreon'),
						array('fontname' => 'Kristi'),
						array('fontname' => 'La+Belle+Aurore'),
						//array('fontname' => 'Lato:100'),
						//array('fontname' => 'Lato:300'), 
						array('fontname' => 'Lato'),
						//array('fontname' => 'Lato:bold'),
						//array('fontname' => 'Lato:900'),
						array('fontname' => 'League+Script'),
						array('fontname' => 'Lekton'),  
						array('fontname' => 'Limelight'),  
						array('fontname' => 'Lobster'),
						array('fontname' => 'Lobster Two'),
						array('fontname' => 'Lora'),
						array('fontname' => 'Love+Ya+Like+A+Sister'),
						array('fontname' => 'Loved+by+the+King'),
						array('fontname' => 'Luckiest+Guy'),
						array('fontname' => 'Maiden+Orange'),
						array('fontname' => 'Mako'),
						array('fontname' => 'Maven+Pro'),
						//array('fontname' => 'Maven+Pro:500'),
						//array('fontname' => 'Maven+Pro:700'),
						//array('fontname' => 'Maven+Pro:900'),
						array('fontname' => 'Meddon'),
						array('fontname' => 'MedievalSharp'),
						array('fontname' => 'Megrim'),
						array('fontname' => 'Merriweather'),
						array('fontname' => 'Metrophobic'),
						array('fontname' => 'Michroma'),
						array('fontname' => 'Miltonian+Tattoo'),
						array('fontname' => 'Miltonian'),
						array('fontname' => 'Modern Antiqua'),
						array('fontname' => 'Monofett'),
						array('fontname' => 'Molengo'),
						array('fontname' => 'Mountains of Christmas'),
						//array('fontname' => 'Muli:300'),
						array('fontname' => 'Muli'), 
						array('fontname' => 'Neucha'),
						array('fontname' => 'Neuton'),
						array('fontname' => 'News+Cycle'),
						array('fontname' => 'Nixie+One'),
						array('fontname' => 'Nobile'),
						array('fontname' => 'Nova+Cut'),
						array('fontname' => 'Nova+Flat'),
						array('fontname' => 'Nova+Mono'),
						array('fontname' => 'Nova+Oval'),
						array('fontname' => 'Nova+Round'),
						array('fontname' => 'Nova+Script'),
						array('fontname' => 'Nova+Slim'),
						array('fontname' => 'Nova+Square'),
						//array('fontname' => 'Nunito:light'),
						array('fontname' => 'Nunito'),
						array('fontname' => 'OFL+Sorts+Mill+Goudy+TT'),
						array('fontname' => 'Old+Standard+TT'),
						//array('fontname' => 'Open+Sans:300'),
						array('fontname' => 'Open+Sans'),
						//array('fontname' => 'Open+Sans:600'),
						//array('fontname' => 'Open+Sans:800'),
						//array('fontname' => 'Open+Sans+Condensed:300'),
						array('fontname' => 'Orbitron'),
						//array('fontname' => 'Orbitron:500'),
						//array('fontname' => 'Orbitron:700'),
						//array('fontname' => 'Orbitron:900'),
						array('fontname' => 'Oswald'),
						array('fontname' => 'Over+the+Rainbow'),
						array('fontname' => 'Reenie+Beanie'),
						array('fontname' => 'Pacifico'),
						array('fontname' => 'Patrick+Hand'),
						array('fontname' => 'Paytone+One'), 
						array('fontname' => 'Permanent+Marker'),
						array('fontname' => 'Philosopher'),
						array('fontname' => 'Play'),
						array('fontname' => 'Playfair+Display'),
						array('fontname' => 'Podkova'),
						array('fontname' => 'PT+Sans'),
						array('fontname' => 'PT+Sans+Narrow'),
						//array('fontname' => 'PT+Sans+Narrow:regular,bold'),
						array('fontname' => 'PT+Serif'),
						array('fontname' => 'PT+Serif Caption'),
						array('fontname' => 'Puritan'),
						array('fontname' => 'Quattrocento'),
						array('fontname' => 'Quattrocento+Sans'),
						array('fontname' => 'Radley'),
						//array('fontname' => 'Raleway:100'),
						array('fontname' => 'Redressed'),
						array('fontname' => 'Rock+Salt'),
						array('fontname' => 'Rokkitt'),
						array('fontname' => 'Ruslan+Display'),
						array('fontname' => 'Schoolbell'),
						array('fontname' => 'Shadows+Into+Light'),
						array('fontname' => 'Shanti'),
						array('fontname' => 'Sigmar+One'),
						array('fontname' => 'Six+Caps'),
						array('fontname' => 'Slackey'),
						array('fontname' => 'Smythe'),
						//array('fontname' => 'Sniglet:800'),
						array('fontname' => 'Special+Elite'),
						array('fontname' => 'Stardos+Stencil'),
						array('fontname' => 'Sue+Ellen+Francisco'),
						array('fontname' => 'Sunshiney'),
						array('fontname' => 'Swanky+and+Moo+Moo'),
						array('fontname' => 'Syncopate'),
						array('fontname' => 'Tangerine'),
						array('fontname' => 'Tenor+Sans'),
						array('fontname' => 'Terminal+Dosis+Light'),
						array('fontname' => 'The+Girl+Next+Door'),
						array('fontname' => 'Tinos'),
						array('fontname' => 'Ubuntu'),
						array('fontname' => 'Ultra'),
						array('fontname' => 'Unkempt'),
						//array('fontname' => 'UnifrakturCook:bold'),
						array('fontname' => 'UnifrakturMaguntia'),
						array('fontname' => 'Varela'),
						array('fontname' => 'Varela Round'),
						array('fontname' => 'Vibur'),
						array('fontname' => 'Vollkorn'),
						array('fontname' => 'VT323'),
						array('fontname' => 'Waiting+for+the+Sunrise'),
						array('fontname' => 'Wallpoet'),
						array('fontname' => 'Walter+Turncoat'),
						array('fontname' => 'Wire+One'),
						array('fontname' => 'Yanone+Kaffeesatz'),
						//array('fontname' => 'Yanone+Kaffeesatz:300'),
						//array('fontname' => 'Yanone+Kaffeesatz:400'),
						//array('fontname' => 'Yanone+Kaffeesatz:700'),
						array('fontname' => 'Yeseva+One'),
						array('fontname' => 'Zeyada')
					);
				}
				break;
				default: {
					$error = true;
					$data['msg'] = esc_html__('The operation failed', VISION_PLUGIN_NAME);
				}
				break;
			}
		} else {
			$error = true;
			$data['msg'] = esc_html__('The operation failed', VISION_PLUGIN_NAME);
		}
		
		if($error) {
			wp_send_json_error($data);
		} else {
			wp_send_json_success($data);
		}
		
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	
	/**
	 * Ajax delete all data from tables
	 */
	function ajax_delete_data() {
		$error = true;
		$data = array();
		$data['msg'] = esc_html__('The operation failed, can\'t delete data', VISION_PLUGIN_NAME);
		
		if(check_ajax_referer(VISION_PLUGIN_NAME . '_ajax', 'nonce', false)) {
			global $wpdb;
			$table = $wpdb->prefix . VISION_PLUGIN_NAME;
			
			foreach($wpdb->get_results('SELECT id FROM ' . $table) as $key=>$item) {
				//======================================
				// [filemanager] delete file
				if(wp_is_writable(VISION_PLUGIN_UPLOAD_DIR)) {
					$file_json = 'config.json';
					$file_main_css = 'main.css';
					$file_custom_css = 'custom.css';
					$file_root_path = VISION_PLUGIN_UPLOAD_DIR . '/' . $item->id . '/';
					
					wp_delete_file($file_root_path . $file_json);
					wp_delete_file($file_root_path . $file_main_css);
					wp_delete_file($file_root_path . $file_custom_css);
					
					if(is_dir($file_root_path)) {
						rmdir($file_root_path);
					}
				}
				//======================================
			}
			
			$query = 'TRUNCATE TABLE ' . $table;
			$result = $wpdb->query($query);
			
			if($result) {
				$error = false;
				$data['msg'] = esc_html__('All data deleted', VISION_PLUGIN_NAME);
			}
		}
		
		if($error) {
			wp_send_json_error($data);
		} else {
			wp_send_json_success($data);
		}
		
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	
	/**
	 * Ajax settings get data
	 */
	function ajax_modal() {
		if(check_ajax_referer(VISION_PLUGIN_NAME . '_ajax', 'nonce', false)) {
			$modalName = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
			$modalPath = plugin_dir_path( dirname(__FILE__) ) . 'includes/modal-' . $modalName . '.php';
			
			if(file_exists($modalPath)) {
				require_once( $modalPath );
			}
		}
		
		wp_die(); // this is required to terminate immediately and return a proper response
	}
}

?>