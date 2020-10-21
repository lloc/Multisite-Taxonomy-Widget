<?php

namespace lloc\Mtw;

/**
 * Class Actions
 * @package lloc\mtw
 */
class Plugin {

	/**
	 * Loader
	 *
	 * @return self
	 */
	public static function load(): self {
		$obj = new self();

		add_action( 'plugins_loaded', [ $obj, 'init_i18n_support' ] );
		add_action( 'widgets_init', [ $obj, 'register_widget' ] );

		add_shortcode( 'mtw_posts', [ Posts::class, 'create_shortcode' ] );

		return $obj;
	}

	function init_i18n_support() {
		load_plugin_textdomain( 'multisite-taxonomy-widget', false, dirname( mtw_get_path() ) . '/languages/' );
	}

	public function register_widget() {
		register_widget( Mtw::class );
	}

}