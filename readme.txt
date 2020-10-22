=== Multisite Taxonomy Widget ===
Contributors: realloc
Donate link: http://www.greenpeace.org/international/en/supportus/
Tags: multisite, recent posts, taxonomy, category, tag, widget
Requires at least: 4.6
Tested up to: 5.5
Stable tag: 1.2
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

List the latest posts of a specific taxonomy from your blog-network.

== Description ==

Display a **recent posts**-widget of all your posts in your blog-network which have a specific tag, category or any other built-in or custom taxonomy.

== Installation ==

1. Download the plugin and uncompress it with your preferred unzip programme 
2. Copy the entire directory in your plugin directory of your WordPress blog (/wp-content/plugins) 
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Place the Multisite Taxonomy Widget in any widgetized area or use the shortcode [mtw_posts]

There is an [introduction](https://github.com/lloc/Multisite-Taxonomy-Widget#multisite-taxonomy-widget) for further information.

== Changelog ==

= 1.2 =
* Tested with latest WordPress version
* Unit test structure added
* Automatic deploy to WordPress plugin dir set

= 1.1 =
* subsitition of wp_get_sites with get_sites
* minimum is now WordPress 4.6
* array notation

= 1.0 =
* tagged as stable
* WordPress Coding Standards

= 0.8 =
* Bugfix: Strict standards and PHPDocs

[...]

= 0.5 =
* Filter added: You can override the tags used for the list output using `mtw_formatelements_output_filter`.

= 0.4 =
* Thumbnails to the output added
* Filter added: You can override the thumbnail-output using `mtw_thumbnail_output_filter`.

= 0.3 =
* Shortcode [mtw_posts] is now available. You can use taxonomy, name and limit as parameters.
* Filters added: You can override the output using `mtw_shortcode_output_filter` or `mtw_widget_output_filter`. 

[...]

= 0.1 =
* Initial version
