<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */

// If this file is called directly, abort.
if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists('Vision_Deactivator')) :

class Vision_Deactivator {
	public function deactivate() {
		global $wpdb;
		
		$table = $wpdb->prefix . VISION_PLUGIN_NAME;
		$sql = 'SELECT COUNT(*) FROM ' . $table . ';';
		$count = $wpdb->get_var($sql);
		
		if($count > 0) {
			return;
		}
		
		// delete all if our tables are empty
		$table = $wpdb->prefix . VISION_PLUGIN_NAME;
		$sql = 'DROP TABLE IF EXISTS ' . $table . ';';
		$wpdb->query($sql);
		
		delete_option(VISION_PLUGIN_NAME . '_db_version');
		delete_option(VISION_PLUGIN_NAME . '_activated');
		delete_option(VISION_PLUGIN_NAME . '_settings');
		
		$this->delete_files(VISION_PLUGIN_UPLOAD_DIR . '/');
	}
	
	private function delete_files($target) {
		if(is_dir($target)) {
			$files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
			foreach($files as $file) {
				$this->delete_files($file);
			}
			rmdir($target);
		} else if(is_file($target)) {
			unlink($target);
		}
	}
}

endif;