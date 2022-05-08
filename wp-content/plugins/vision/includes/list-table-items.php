<?php

// If this file is called directly, abort.
if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Vision_List_Table_Items extends WP_List_Table {
	private $count_all = 0;
	private $count_mine = 0;
	private $count_trash = 0;
	private $url_preview_base = '';
	
	function __construct() {
		parent::__construct(array(
			'singular'=> VISION_PLUGIN_NAME . '_item',
			'plural' => VISION_PLUGIN_NAME . '_items',
			'ajax' => false
		));
		
		$this->url_preview_base = rtrim(parse_url(home_url(), PHP_URL_PATH), '/') . '/vision/map/';
	}
	
	function handle_row_actions( $post, $column_name, $primary ) {
		return '';
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
	
	function filesystem_method() {
		return 'direct';
	}
	
	function request_filesystem_credentials() {
		return true;
	}
	
	function getFileSystem() {
		global $wp_filesystem;
		$result = true;
		
		if(!$wp_filesystem) {
			require_once(ABSPATH . '/wp-admin/includes/file.php');
			
			add_filter('filesystem_method', array( $this, 'filesystem_method'));
			add_filter('request_filesystem_credentials', array( $this, 'request_filesystem_credentials'));
			
			$credentials = request_filesystem_credentials(site_url(), '', true, false, null );
			
			$result = WP_Filesystem($credentials);
			
			remove_filter('filesystem_method', array( $this, 'filesystem_method'));
			remove_filter('request_filesystem_credentials', array( $this, 'request_filesystem_credentials'));
		}
		
		if($result)
			return $wp_filesystem;
		return null;
	}
	
	function column_default($item, $column_name){
		switch($column_name){
			case 'title':
			case 'active':
			case 'shortcode':
			case 'author':
			case 'editor':
			case 'created':
			case 'modified':
			case 'id':
				return $item[$column_name];
			default:
				return print_r($item, true);
		}
	}
	
	function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s">',
			$this->_args['singular'], // let's simply repurpose the table's singular label
			$item['id'] // the value of the checkbox should be the record's ID
		);
	}
	
	function column_title($item) {
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		$item_status = filter_input(INPUT_GET, 'item_status', FILTER_SANITIZE_STRING);
		
		if(current_user_can('manage_options') || get_current_user_id()==$item['author']) {
			$actions = array();
			
			switch($item_status) {
				case 'trash': {
					$args = array(
						'page'   => $page,
						'item_status' => $item_status,
						'action' => 'restore',
						'id'  => $item['id']
					);
					$url = add_query_arg($args, 'admin.php');
					
					$actions['restore'] = sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url(wp_nonce_url($url, 'restore_' . $item['id'])),
						esc_html__('Restore', VISION_PLUGIN_NAME)
					);
					
					$args = array(
						'page'   => $page,
						'item_status' => $item_status,
						'action' => 'delete',
						'id'  => $item['id']
					);
					$url = add_query_arg($args, 'admin.php');
					
					$actions['delete'] = sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url(wp_nonce_url($url, 'delete_' . $item['id'])),
						esc_html__('Delete Permanently', VISION_PLUGIN_NAME)
					);
				} break;
				default: {
					$args = array(
						'page'   => VISION_PLUGIN_NAME . '_item',
						'item_status' => $item_status,
						'id'  => $item['id']
					);
					$url = add_query_arg($args, 'admin.php');
					
					$actions['edit'] = sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url($url),
						esc_html__('Edit', VISION_PLUGIN_NAME)
					);
					
					$args = array(
						'page'   => $page,
						'item_status' => $item_status,
						'action' => 'copy',
						'id'  => $item['id']
					);
					$url = add_query_arg($args, 'admin.php');
					
					$actions['copy'] = sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url(wp_nonce_url($url, 'copy_' . $item['id'])),
						esc_html__('Duplicate', VISION_PLUGIN_NAME)
					);
					
					$args = array(
						'page'   => $page,
						'item_status' => $item_status,
						'action' => 'trash',
						'id'  => $item['id']
					);
					$url = add_query_arg($args, 'admin.php');
					
					$actions['trash'] = sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url(wp_nonce_url($url, 'trash_' . $item['id'])),
						esc_html__('Move to Trash', VISION_PLUGIN_NAME)
					);
					
					$args = array(
						'preview' => true
					);
					$url = add_query_arg($args, $this->url_preview_base . $item['id']);
					
					$actions['view'] = sprintf(
						'<a href="%1$s" target="_blank">%2$s</a>',
						esc_url($url),
						esc_html__('Preview', VISION_PLUGIN_NAME)
					);
				} break;
			}
			
			return sprintf('<a href="?page=%1$s&id=%2$s" class="row-title">%3$s</a> %4$s',
				VISION_PLUGIN_NAME . '_item',
				$item['id'],
				$item['title'],
				$this->row_actions($actions)
			);
		}
		
		return sprintf('<strong>%1$s</strong>', $item['title']);
	}
	
	function column_active($item) {
		if(current_user_can('manage_options') || get_current_user_id()==$item['author']) {
			return sprintf(
				'<div class="vision-toggle vision-%1$s" data-id="%2$s">&nbsp;</div>',
				($item['active'] ? 'checked' : 'unchecked'),
				$item['id']
			);
		} else {
			return sprintf(
				'<div class="vision-toggle vision-readonly vision-%1$s" data-id="%2$s">&nbsp;</div>',
				($item['active'] ? 'checked' : 'unchecked'),
				$item['id']
			);
		}
	}
	
	function column_shortcode($item) {
		return sprintf('<code>[vision id="%1$s"]</code>', $item['id']);
	}
	
	function column_author($item) {
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		$args = array(
			'page'   => $page,
			'author' => $item['author']
		);
		$url = add_query_arg($args, 'admin.php');
		
		return sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $url ),
			get_the_author_meta('display_name', $item['author'])
		);
	}
	
	function column_editor($item) {
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		$args = array(
			'page'   => $page,
			'editor' => $item['editor']
		);
		$url = add_query_arg($args, 'admin.php');
		
		return sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $url ),
			get_the_author_meta('display_name', $item['editor'])
		);
	}
	
	function column_created( $item ) {
		$m_time = mysql2date( esc_html__( 'Y/m/d g:i:s a' ), $item['created'] );
		$h_time = mysql2date( esc_html__( 'Y/m/d' ), $item['created'] );
		
		return sprintf('<abbr title="%1$s">%2$s</abbr>', $m_time, $h_time);
	}
	
	function column_modified( $item ) {
		$m_time = mysql2date( esc_html__( 'Y/m/d g:i:s a' ), $item['modified'] );
		$h_time = mysql2date( esc_html__( 'Y/m/d' ), $item['modified'] );
		
		return sprintf('<abbr title="%1$s">%2$s</abbr>', $m_time, $h_time);
	}
	
	function get_views() {
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		$item_status = filter_input(INPUT_GET, 'item_status', FILTER_SANITIZE_STRING);
		
		$args_all   = array('page'   => $page);
		$args_mine  = array('page'   => $page, 'item_status' => 'mine');
		$args_trash = array('page'   => $page, 'item_status' => 'trash');
		
		$url_all   = add_query_arg($args_all, 'admin.php');
		$url_mine  = add_query_arg($args_mine, 'admin.php');
		$url_trash = add_query_arg($args_trash, 'admin.php');
		
		$status_links = array(
			'all'   => sprintf('<a href="%1$s" %2$s>%3$s <span class="count">(%4$d)</span></a>', esc_url($url_all),   ($item_status            ? '' : 'class="current"'), esc_html__('All',VISION_PLUGIN_NAME), $this->count_all),
			'mine'  => sprintf('<a href="%1$s" %2$s>%3$s <span class="count">(%4$d)</span></a>', esc_url($url_mine),  ($item_status == 'mine'  ? 'class="current"' : ''), esc_html__('Mine',VISION_PLUGIN_NAME), $this->count_mine),
			'trash' => sprintf('<a href="%1$s" %2$s>%3$s <span class="count">(%4$d)</span></a>', esc_url($url_trash), ($item_status == 'trash' ? 'class="current"' : ''), esc_html__('Trash',VISION_PLUGIN_NAME), $this->count_trash)
		);
		
		return $status_links;
	}
	
	function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox">',
			'title'     => esc_html__('Title', VISION_PLUGIN_NAME),
			'active'    => esc_html__('Active', VISION_PLUGIN_NAME),
			'shortcode' => esc_html__('Shortcode', VISION_PLUGIN_NAME),
			'author'    => esc_html__('Author', VISION_PLUGIN_NAME),
			'editor'    => esc_html__('Editor', VISION_PLUGIN_NAME),
			'created'   => esc_html__('Created', VISION_PLUGIN_NAME),
			'modified'  => esc_html__('Modified', VISION_PLUGIN_NAME)
		);
		return $columns;
	}
	
	function get_sortable_columns() {
		$columns = array(
			'title'     => array('title',false),
			'active'    => array('active',false),
			'author'    => array('author',false),
			'editor'    => array('editor',false),
			'created'   => array('created',false),
			'modified'  => array('modified',false)
		);
		return $columns;
	}
	
	function get_bulk_actions() {
		$item_status = filter_input(INPUT_GET, 'item_status', FILTER_SANITIZE_STRING);
		$actions = array();
		
		switch($item_status) {
			case 'trash': {
				$actions = array(
					'restore' => esc_html__('Restore', VISION_PLUGIN_NAME),
					'delete'  => esc_html__('Delete Permanently', VISION_PLUGIN_NAME)
				);
			} break;
			default: {
				$actions = array(
					'copy'  => esc_html__('Duplicate', VISION_PLUGIN_NAME),
					'trash' => esc_html__('Move to Trash', VISION_PLUGIN_NAME)
				);
			} break;
		}
		
		return $actions;
	}
	
	function process_trash_action($id, $flag) {
		global $wpdb;
		$table = $wpdb->prefix . VISION_PLUGIN_NAME;
		
		$author = get_current_user_id();
		
		$sql = sprintf('SELECT * FROM %1$s WHERE id=%2$d', $table, $id);
		$item = $wpdb->get_row($sql, OBJECT);
		
		if($item && (current_user_can('manage_options') || $author==$item->author)) {
			$wpdb->update(
				$table,
				array(
					'deleted'=> $flag
				),
				array('id'=>$id)
			);
		}
	}
	
	function process_copy_action($id) {
		global $wpdb;
		$table = $wpdb->prefix . VISION_PLUGIN_NAME;
		
		$author = get_current_user_id();
		
		$sql = sprintf('SELECT * FROM %1$s WHERE id=%2$d AND NOT deleted', $table, $id);
		$item = $wpdb->get_row($sql, OBJECT);
		
		if($item && (current_user_can('manage_options') || $author==$item->author)) {
			$itemData = unserialize($item->data);
			$itemData->slug = sanitize_title(($itemData->slug ? $itemData->slug : $itemData->title));
			$itemData->title = esc_html__('[Duplicate] ', VISION_PLUGIN_NAME) . $itemData->title;
			$itemConfig = unserialize($item->config);
			
			$result = $wpdb->insert(
				$table,
				array(
					'title' => $itemData->title,
					'slug' => $itemData->slug,
					'active' => $itemData->active,
					'data' => serialize($itemData),
					'config' => serialize($itemConfig),
					'author' => $author,
					'editor' => $author,
					'created' => current_time('mysql', 1),
					'modified' => current_time('mysql', 1)
				)
			);
			
			//======================================
			// [filemanager] create an external file
			if($result) {
				if(wp_is_writable(VISION_PLUGIN_UPLOAD_DIR)) {
					$dir_src_root = $this->joinPaths(VISION_PLUGIN_UPLOAD_DIR, $id);
					$dir_dst_root = $this->joinPaths(VISION_PLUGIN_UPLOAD_DIR, $wpdb->insert_id);
					
					$wp_filesystem = $this->getFileSystem();
					
					if($wp_filesystem) {
						if(!$wp_filesystem->is_dir($dir_dst_root)) {
							$wp_filesystem->mkdir($dir_dst_root);
						}
						
						copy_dir($dir_src_root, $dir_dst_root);
					}
				}
			}
			//======================================
		}
	}
	
	function process_delete_action($id) {
		global $wpdb;
		$table = $wpdb->prefix . VISION_PLUGIN_NAME;
		
		$author = get_current_user_id();
		
		$sql = sprintf('SELECT * FROM %1$s WHERE id=%2$d AND deleted', $table, $id);
		$item = $wpdb->get_row($sql, OBJECT);
		
		if($item && (current_user_can('manage_options') || $author==$item->author) ) {
			$result = $wpdb->delete($table, ['id'=>$id], ['%d']);
			
			//======================================
			// [filemanager] delete file
			if($result) {
				if(wp_is_writable(VISION_PLUGIN_UPLOAD_DIR)) {
					$dir_root = $this->joinPaths(VISION_PLUGIN_UPLOAD_DIR, $id);
					
					$wp_filesystem = $this->getFileSystem();
					
					if($wp_filesystem) {
						if($wp_filesystem->is_dir($dir_root)) {
							$wp_filesystem->rmdir($dir_root, true);
						}
					}
				}
			}
			//======================================
		}
	}
	
	function process_actions() {
		switch($this->current_action()) {
			case 'trash': {
				if(isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
					$nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
					$nonce_key = 'bulk-' . $this->_args['plural'];
					
					if(wp_verify_nonce($nonce, $nonce_key)) {
						$items = filter_input(INPUT_POST, $this->_args['singular'], FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
						
						foreach($items as $id) {
							$this->process_trash_action($id, true);
						}
					}
				} else if(isset($_GET['_wpnonce']) && !empty($_GET['_wpnonce'])) {
					$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
					$nonce = filter_input(INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING);
					$nonce_key = 'trash_' . $id;
					
					if(wp_verify_nonce($nonce, $nonce_key)) {
						$this->process_trash_action($id, true);
					}
				}
			} break;
			case 'restore': {
				if(isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
					$nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
					$nonce_key = 'bulk-' . $this->_args['plural'];
					if(wp_verify_nonce($nonce, $nonce_key)) {
						$items = filter_input(INPUT_POST, $this->_args['singular'], FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
						
						foreach($items as $id) {
							$this->process_trash_action($id, false);
						}
					}
				} else if(isset($_GET['_wpnonce']) && !empty($_GET['_wpnonce'])) {
					$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
					$nonce = filter_input(INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING);
					$nonce_key = 'restore_' . $id;
					
					if(wp_verify_nonce($nonce, $nonce_key)) {
						$this->process_trash_action($id, false);
					}
				}
			} break;
			case 'copy': {
				if(isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
					$nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
					$nonce_key = 'bulk-' . $this->_args['plural'];
					if(wp_verify_nonce($nonce, $nonce_key)) {
						$items = filter_input(INPUT_POST, $this->_args['singular'], FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
						
						foreach($items as $id) {
							$this->process_copy_action($id);
						}
					}
				} else if(isset($_GET['_wpnonce']) && !empty($_GET['_wpnonce'])) {
					$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
					$nonce = filter_input(INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING);
					$nonce_key = 'copy_' . $id;
					
					if(wp_verify_nonce($nonce, $nonce_key)) {
						$this->process_copy_action($id);
					}
				}
			} break;
			case 'delete': {
				if(isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
					$nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
					$nonce_key = 'bulk-' . $this->_args['plural'];
					if(wp_verify_nonce($nonce, $nonce_key)) {
						$items = filter_input(INPUT_POST, $this->_args['singular'], FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
						
						foreach($items as $id) {
							$this->process_delete_action($id);
						}
					}
				} else if(isset($_GET['_wpnonce']) && !empty($_GET['_wpnonce'])) {
					$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
					$nonce = filter_input(INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING);
					$nonce_key = 'delete_' . $id;
					
					if(wp_verify_nonce($nonce, $nonce_key)) {
						$this->process_delete_action($id);
					}
				}
			} break;
		}
	}
	
	function prepare_items() {
		$this->process_actions();
		
		$columns = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$hidden = array();
		
		$itemsPerPage = 25;
		$currentPage = ($this->get_pagenum()-1) * $itemsPerPage;
		
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		// make sql query
		global $wpdb;
		
		$table = $wpdb->prefix . VISION_PLUGIN_NAME;
		$item_status = filter_input(INPUT_GET, 'item_status', FILTER_SANITIZE_STRING);
		$orderby = (isset($_GET['orderby']) ? filter_input(INPUT_GET, 'orderby', FILTER_SANITIZE_STRING ) : 'id');
		$order = (isset($_GET['order']) ? filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING ) : 'desc');
		$author = (isset($_GET['author']) ? filter_input(INPUT_GET, 'author', FILTER_SANITIZE_NUMBER_INT ) : NULL);
		$editor = (isset($_GET['editor']) ? filter_input(INPUT_GET, 'editor', FILTER_SANITIZE_NUMBER_INT ) : NULL);
		$search = (isset($_POST['s']) ? filter_input(INPUT_POST, 's', FILTER_SANITIZE_STRING) : NULL);
		$current_user = get_current_user_id();
		
		$sql = '';
		$total_items = 0;
		$sql_search = ($search ? sprintf(' AND title LIKE "%%%s%%"', $search) : '');
		
		// database operations
		if(current_user_can('manage_options')) { // by default, the manage_options permission is only given to 'Super Users' and 'Administrators'
			$this->count_all   = $wpdb->query(sprintf('SELECT id FROM %1$s WHERE NOT deleted', $table));
			$this->count_mine  = $wpdb->query(sprintf('SELECT id FROM %1$s WHERE NOT deleted AND author=%2$s', $table, $current_user));
			$this->count_trash = $wpdb->query(sprintf('SELECT id FROM %1$s WHERE deleted', $table));
			
			switch($item_status) {
				case 'trash': {
					if($author) {
						$sql = sprintf('SELECT * FROM %1$s WHERE author=%2$s AND deleted %3$s ORDER BY %4$s %5$s LIMIT %6$d, %7$d', $table, $author, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
						$sql_total_items = sprintf('SELECT id FROM %1$s WHERE author=%2$s AND deleted %3$s', $table, $author, $sql_search);
					} else if($editor) {
						$sql = sprintf('SELECT * FROM %1$s WHERE editor=%2$s AND deleted %3$s ORDER BY %4$s %5$s LIMIT %6$d, %7$d', $table, $editor, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
						$sql_total_items = sprintf('SELECT id FROM %1$s WHERE editor=%2$s AND deleted %3$s', $table, $editor, $sql_search);
					} else {
						$sql = sprintf('SELECT * FROM %1$s WHERE deleted %2$s ORDER BY %3$s %4$s LIMIT %5$d, %6$d', $table, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
						$sql_total_items = sprintf('SELECT id FROM %1$s WHERE deleted %2$s', $table, $sql_search);
					}
				} break;
				case 'mine': {
					if($author) {
						$sql = sprintf('SELECT * FROM %1$s WHERE author=%2$s AND NOT deleted %3$s ORDER BY %4$s %5$s LIMIT %6$d, %7$d', $table, $current_user, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
						$sql_total_items = sprintf('SELECT id FROM %1$s WHERE author=%2$s AND NOT deleted %3$s', $table, $current_user, $sql_search);
					} else if($editor) {
						$sql = sprintf('SELECT * FROM %1$s WHERE author=%2$s AND editor=%3$s AND NOT deleted %4$s ORDER BY %5$s %6$s LIMIT %7$d, %8$d', $table, $current_user, $editor, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
						$sql_total_items = sprintf('SELECT id FROM %1$s WHERE author=%2$s AND editor=%3$s AND NOT deleted %4$s', $table, $current_user, $editor, $sql_search);
					} else {
						$sql = sprintf('SELECT * FROM %1$s WHERE author=%2$s AND NOT deleted %3$s ORDER BY %4$s %5$s LIMIT %6$d, %7$d', $table, $current_user, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
						$sql_total_items = sprintf('SELECT id FROM %1$s WHERE author=%2$s AND NOT deleted %3$s', $table, $current_user, $sql_search);
					}
				} break;
				default: {
					if($author) {
						$sql = sprintf('SELECT * FROM %1$s WHERE author=%2$s AND NOT deleted %3$s ORDER BY %4$s %5$s LIMIT %6$d, %7$d', $table, $author, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
						$sql_total_items = sprintf('SELECT id FROM %1$s WHERE author=%2$s AND NOT deleted %3$s', $table, $author, $sql_search);
					} else if($editor) {
						$sql = sprintf('SELECT * FROM %1$s WHERE editor=%2$s AND NOT deleted %3$s ORDER BY %4$s %5$s LIMIT %6$d, %7$d', $table, $editor, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
						$sql_total_items = sprintf('SELECT id FROM %1$s WHERE editor=%2$s AND NOT deleted %3$s', $table, $editor, $sql_search);
					} else {
						$sql = sprintf('SELECT * FROM %1$s WHERE NOT deleted %2$s ORDER BY %3$s %4$s LIMIT %5$d, %6$d', $table, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
						$sql_total_items = sprintf('SELECT id FROM %1$s WHERE NOT deleted %2$s', $table, $sql_search);
					}
				} break;
			}
		} else { // the current user view
			$this->count_all   = $wpdb->query(sprintf('SELECT id FROM %1$s WHERE author=%2$s AND NOT deleted', $table, $current_user));
			$this->count_mine  = $wpdb->query(sprintf('SELECT id FROM %1$s WHERE author=%2$s AND NOT deleted', $table, $current_user));
			$this->count_trash = $wpdb->query(sprintf('SELECT id FROM %1$s WHERE author=%2$s AND deleted', $table, $current_user));
			
			switch($item_status) {
				case 'trash': {
					$sql = sprintf('SELECT * FROM %1$s WHERE author=%2$s AND deleted %3$s ORDER BY %4$s %5$s LIMIT %6$d, %7$d', $table, $current_user, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
					$sql_total_items = sprintf('SELECT id FROM %1$s WHERE author=%2$s AND deleted %3$s', $table, $current_user, $sql_search);
				} break;
				case 'mine': {
					$sql = sprintf('SELECT * FROM %1$s WHERE author=%2$s AND NOT deleted %3$s ORDER BY %4$s %5$s LIMIT %6$d, %7$d', $table, $current_user, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
					$sql_total_items = sprintf('SELECT id FROM %1$s WHERE author=%2$s AND NOT deleted %3$s', $table, $current_user, $sql_search);
				} break;
				default: {
					$sql = sprintf('SELECT * FROM %1$s WHERE author=%2$s AND NOT deleted %3$s ORDER BY %4$s %5$s LIMIT %6$d, %7$d', $table, $current_user, $sql_search, $orderby, $order, $currentPage, $itemsPerPage);
					$sql_total_items = sprintf('SELECT id FROM %1$s WHERE author=%2$s AND NOT deleted %3$s', $table, $current_user, $sql_search);
				} break;
			}
		}
		
		$this->items = $wpdb->get_results($sql, 'ARRAY_A');
		$total_items = $wpdb->query($sql_total_items);
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => ceil($total_items / $itemsPerPage)
		));
	}
}
?>