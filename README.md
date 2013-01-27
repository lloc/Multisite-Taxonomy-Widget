Multisite Taxonomy Widget
=========================

Display a **recent posts**-widget of all your posts in your blog-network which have a specific tag, category or any other built-in or custom taxonomy.

_Please keep in mind that the version of the Multisite Taxonomy Widget at GitHub is a work in progress._

**Download the [latest stable version from the WordPress Plugin Directory](http://downloads.wordpress.org/plugin/multisite-taxonomy-widget.zip).**

Need help? Check out the [forum](http://wordpress.org/support/plugin/multisite-taxonomy-widget) first! If you find any bugs then I would very much like to [hear about the issue](https://github.com/lloc/Multisite-Taxonomy-Widget/issues).

How to use the widget
---------------------

After the activation of the plugin you'll find a new widget called Multisite Taxonomy in _Appearance > Widgets_ (/wp-admin/widgets.php).

Once you dragged the widget in one of your sidebars you can fill in various parameters for customizing the output of the widget:

1.  **Title**

	This is the widget title. Leave it empty if you don't need to show a title above the widget.

2.  **Taxonomy**

	This is the type of the taxonomy such as category, tag and so on.

3.  **Name**

	This is the most important parameter. If you want the widget to search for all posts with the tag _Cool post_ for example put _cool-post_ in here because the plugin uses the slug-form of the taxonomies and the slug of the _Cool post_-taxonomy is probably saved as _cool-post_. But you should check that anyway.

4.  **Limit**

	You can limit the output with this parameter. This should be a number > 0. If you want to show all posts of the specific taxonomy you can set -1 but it is not recommended. 

5.  **Thumbnail**

	You can set any positive number here if you want to show thumbnails. If you don't like them leave this field empty or fill in a 0.

How to use the shortcode
------------------------

Use the shortcode _[mtw_posts]_ if you'd like to show a list of posts of your network in the content where usually a normal widget cannot be placed. The parameters are similar to those for the widget. You can use taxonomy, name, limit and thumbnail as arguments.

With this in mind you can write

	[mtw_posts taxonomy="category" name="test"]

if you want to show the last 10 (because this is standard when limit is not set) posts in the category test. If you prefer tags and you don't want to see thumbnails use:

	[mtw_posts taxonomy="post_tag" name="test" thumbnail="0"]

And if you want to show the last 5 posts of a post type which has a custom taxonomy called product_category, you could use something like:

	[mtw_posts taxonomy="product_category" name="test" limit="5"]

How to use the filter hooks
---------------------------

You can use filters if you want to override the output of the functions. The are 4 filters available:

1.  **mtw_formatelements_output_filter**

	There is a function which calls this filter after adding 4 elements to an array of format-elements: before_mtw_list/after_mtw_list (`<ul>/</ul>`) and before_mtw_item/after_mtw_item (`<li>/</li>`) so you can override this.
	[See also](http://lloc.github.com/Multisite-Taxonomy-Widget/function-mtw_get_formatelements.html)

2.  **mtw_thumbnail_output_filter**

	You can create your customized output of the thumbnail. This filter gives you access to a post-object and an array of parameters and returns the string from your function if you define one.
	[See also](http://lloc.github.com/Multisite-Taxonomy-Widget/function-mtw_get_formatelements.html)

3.  **mtw_shortcode_output_filter**

	You can create your customized output of the list-item when a shortcode is used. This filter gives you access to a post-object and an array of parameters and returns the string from your function if you define one.
	[See also](loc.github.com/Multisite-Taxonomy-Widget/function-mtw_create_shortcode.html)

4.  **mtw_widget_output_filter**

	You can create your customized output of the list-item when a widget is used. This filter gives you access to a post-object and an array of parameters and returns the string from your function if you define one.
	[Read more](http://lloc.github.com/Multisite-Taxonomy-Widget/class-MultisiteTaxonomyWidget.html)
