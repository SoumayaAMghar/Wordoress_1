<?php
/**
 * Fired during plugin activation and loading.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */

// If this file is called directly, abort.
if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists('Vision_Activator')) :

class Vision_Activator {
	public function activate() {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		global $wpdb;
		
		$table = $wpdb->prefix . VISION_PLUGIN_NAME;
		$sql = 'CREATE TABLE ' . $table . ' (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title text DEFAULT NULL,
			slug varchar(200) DEFAULT NULL,
			active tinyint NOT NULL DEFAULT 1,
			data longtext DEFAULT NULL,
			config longtext DEFAULT NULL,
			author bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			editor bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			created datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
			modified datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
			deleted tinyint NOT NULL DEFAULT 0,
			UNIQUE KEY id (id)
		)
		CHARACTER SET utf8mb4,
		COLLATE utf8mb4_unicode_ci;';
		dbDelta($sql);
		
		update_option(VISION_PLUGIN_NAME . '_db_version', VISION_DB_VERSION, false);
		
		$this->update_data();
		
		if( get_option(VISION_PLUGIN_NAME . '_activated') == false ) {
			$this->install_data();
		}
		
		update_option(VISION_PLUGIN_NAME . '_activated', time(), false);
	}
	
	public function update_data() {
		global $wpdb;
		
		$table = $wpdb->prefix . VISION_PLUGIN_NAME;
		
		$sql = 'UPDATE ' . $table . ' SET created=date  WHERE created=%s';
		$wpdb->query($wpdb->prepare($sql, '0000-00-00 00:00:00'));
		
		$sql = 'UPDATE ' . $table . ' SET editor=author WHERE editor=%d';
		$wpdb->query($wpdb->prepare($sql, 0));
		
		// modify field types
		$wpdb->query('ALTER TABLE ' . $table . ' MODIFY data LONGTEXT');
		$wpdb->query('ALTER TABLE ' . $table . ' MODIFY config LONGTEXT');
		
		// Add support Emoji
		$sql = 'ALTER TABLE ' . $table . ' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
		$wpdb->query($sql);
		
		$sql = 'ALTER TABLE ' . $table . ' MODIFY title text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
		$wpdb->query($sql);
		
		$sql = 'ALTER TABLE ' . $table . ' MODIFY data longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
		$wpdb->query($sql);
		
		$sql = 'ALTER TABLE ' . $table . ' MODIFY config longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
		$wpdb->query($sql);
		
		$sql = 'ALTER TABLE ' . $table . ' MODIFY slug varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
		$wpdb->query($sql);
		
		// Add the new column "deleted"
		$sql = 'ALTER TABLE ' . $table . ' ADD deleted tinyint NOT NULL DEFAULT 0';
		$wpdb->query($sql);
	}
	
	public function install_data() {
	}
	
	public function check_db() {
		if ( get_option(VISION_PLUGIN_NAME . '_db_version') != VISION_DB_VERSION ) {
			$this->activate();
		}
	}
}

endif;