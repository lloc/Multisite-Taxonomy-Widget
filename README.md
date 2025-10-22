# Multisite Taxonomy Widget

Display a **recent posts**-widget of all your posts in your blog-network which have a specific tag, category or any other built-in or custom taxonomy.

[![codecov](https://codecov.io/gh/lloc/Multisite-Taxonomy-Widget/graph/badge.svg?token=829HP64ZBZ)](https://codecov.io/gh/lloc/Multisite-Taxonomy-Widget)

This plugin enhances WordPress multisites by displaying related posts across the network. It offers a widget, shortcode, and filters for flexible content surfacing.

_Please keep in mind that the version of the Multisite Taxonomy Widget on GitHub is a work in progress._

**Download the [latest stable version from the WordPress Plugin Directory](http://downloads.wordpress.org/plugin/multisite-taxonomy-widget.zip).**

Need help? Check out the [forum](http://wordpress.org/support/plugin/multisite-taxonomy-widget) first! If you find any bugs then I would very much like to [hear about the issue](https://github.com/lloc/Multisite-Taxonomy-Widget/issues).

## Features

- Query posts across your multisite network by taxonomy (core or custom) with an adjustable limit.
- Expose the content via a widget or shortcode, each supporting thumbnails and custom markup.
- Extend behaviour through WordPress-style filters without touching core plugin code.

## Installation

1. Download the zip from the WordPress directory or clone this repository.
2. Install it into your `wp-content/plugins/` directory and activate it network-wide.
3. Configure the widget or shortcode as described below.

## Usage

### Widget

After activation you will find **Multisite Taxonomy** in _Appearance ▸ Widgets_ (`/wp-admin/widgets.php`).

- Drag the widget into any sidebar.
- Configure the available fields:
  - **Title** — optional heading for the widget output.
  - **Taxonomy** — taxonomy slug (e.g. `category`, `post_tag`, `product_category`).
  - **Name** — the term slug to query against (`cool-post` when the term name is _Cool post_).
  - **Limit** — maximum number of posts; set to `-1` to show all (not recommended for large sites).
  - **Thumbnail** — positive pixel width to include thumbnails, or `0`/empty to hide them.

### Shortcode

Use the `[mtw_posts]` shortcode anywhere shortcodes are supported. Parameters mirror the widget settings.

```text
[mtw_posts taxonomy="category" name="test"]

[mtw_posts taxonomy="post_tag" name="featured" thumbnail="0"]

[mtw_posts taxonomy="product_category" name="test" limit="5"]
```

## Filters and Extensibility

Hook Description | Purpose
---|---
`mtw_formatelements_output_filter` | Override the list wrappers (`<ul>`, `<li>`).
`mtw_thumbnail_output_filter` | Customize thumbnail markup with access to the post object and args.
`mtw_shortcode_output_filter` | Adjust the shortcode list item output.
`mtw_widget_output_filter` | Adjust the widget list item output.

## Development

```bash
composer install        # install dependencies
composer test           # run the PHPUnit suite with Brain Monkey
composer phpstan        # static analysis using WordPress extensions
composer coverage       # generate HTML coverage at tests/coverage/
```

Feel free to open pull requests; see `AGENTS.md` for the full contributor guide.

## Support

Visit the [support forum](http://wordpress.org/support/plugin/multisite-taxonomy-widget) to share questions or ideas. Bug reports are always welcome through [GitHub issues](https://github.com/lloc/Multisite-Taxonomy-Widget/issues).

Additional usage documentation and API references are available on the [project site](http://lloc.github.com/Multisite-Taxonomy-Widget/).
