<?php
/**
 * Multisite Taxonomy Widget
 *
 * @copyright Copyright (C) 2011-2022, Dennis Ploetner, re@lloc.de
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 or later
 * @wordpress-plugin
 *
 * Plugin Name: Multisite Taxonomy Widget
 * Plugin URI: https://wordpress.org/plugins/multisite-taxonomy-widget/
 * Description: List the latest posts of a specific taxonomy from your blog-network.
 * Version: 1.3.0
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

( new \lloc\Mtw\Plugin( __FILE__ ) )->hooks();
