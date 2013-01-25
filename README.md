Multisite Taxonomy Widget
=========================

Display a **recent posts**-widget of all your posts in your blog-network which have a specific tag, category or any other built-in or custom taxonomy.

_Please keep in mind that the version of the Multisite Taxonomy Widget at GitHub is a work in progress._

**Download the [latest stable from the WordPress Plugin Directory](http://downloads.wordpress.org/plugin/multisite-taxonomy-widget.zip).**

Need help? Check out the [forum](http://wordpress.org/support/plugin/multisite-taxonomy-widget) first! If you find any bugs then I would very much like to [hear about the issue](https://github.com/lloc/Multisite-Taxonomy-Widget/issues).

How to use the widget
---------------------

After the activation of the plugin you'll find a new widget called Multisite Taxonomy in _Appearance > Widgets_ (/wp-admin/widgets.php).

When you dragged the widget in one of your sidebars you can fill in various parameters for customizing the output of the widget:

1.  **Title**

	This is the widget title. Let it empty if you don't need to show a title above the widget.

2.  **Taxonomy**

	This is the type of the taxonomy like category, tag and so on.

3.  **Name**

	This is the most important parameter. If you want the widget to look for all posts with the tag _Cool post_ for example put _cool-post_ in here because the plugin uses the slug-form of the taxonomies and the slug of the _Cool post_-taxonomy is probably saved as _cool-post_. But you should check that anyway.

4.  **Limit**

	You can limit the output with this parameter. This should be a number > 0. If you want to show all posts of the the specific taxonomy you can set -1 but it is not recommended. 

5.  **Thumbnail**

	You can set any positive number here if you want to show thumbnails too. If you don’t like them let this field empty.
