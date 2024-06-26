<?php
/**
 * Plugin Name: Multisite Taxonomy Widget
 * Plugin URI: https://github.com/lloc/Multisite-Taxonomy-Widget
 * Description: List the latest posts of a specific taxonomy from your blog-network.
 * Version: 1.2
 * Author: Dennis Ploetner
 * Author URI: http://lloc.de/
 * Text Domain: multisite-taxonomy-widget
 * Domain Path: /languages/
 * License: GPLv2 or later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

/**
 * Gets path of the plugin
 *
 * @return string
 */
function mtw_get_path() {
	return plugin_basename( __FILE__ );
}

\lloc\Mtw\Plugin::init();
