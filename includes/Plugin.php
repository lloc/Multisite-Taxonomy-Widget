<?php

namespace lloc\Mtw;

/**
 * Class Plugin
 *
 * @package lloc\mtw
 */
class Plugin {

	public static function init(): void {
		add_action( 'plugins_loaded', array( __CLASS__, 'init_i18n_support' ) );
		add_action( 'widgets_init', array( __CLASS__, 'register_widget' ) );

		add_shortcode( 'mtw_posts', array( Posts::class, 'create_shortcode' ) );
	}

	public static function init_i18n_support(): void {
		load_plugin_textdomain( 'multisite-taxonomy-widget', false, dirname( mtw_get_path() ) . '/languages/' );
	}

	public static function register_widget(): void {
		register_widget( Mtw::class );
	}
}
