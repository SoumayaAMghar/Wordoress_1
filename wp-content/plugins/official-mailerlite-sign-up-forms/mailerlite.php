<?php
/**
 * Plugin Name: MailerLite - Signup forms (official)
 * Description: Official MailerLite Signup forms plugin for WordPress. Ability to embed MailerLite webforms and create custom ones just with few clicks.
 * Version: 1.5.3
 * Author: MailerLite
 * Author URI: https://www.mailerlite.com
 * License: GPLv2 or later
 * Text Domain: mailerlite
 * Domain Path: /languages/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301,
 * USA.
 */

namespace MailerLiteForms;

define( 'MAILERLITE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MAILERLITE_PLUGIN_URL', plugins_url( '', __FILE__ ) );

// Plugin basename
define( 'MAILERLITE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( 'MAILERLITE_VERSION', '1.5.3' );

define( 'MAILERLITE_PHP_VERSION', '7.2.5' );
define( 'MAILERLITE_WP_VERSION', '3.0.1' );

define( 'MAILERLITE_TEXT_DOMAIN', 'mailerlite' );

// autoload
require_once( MAILERLITE_PLUGIN_DIR . 'autoload.php' );

// load plugin
new Core();
