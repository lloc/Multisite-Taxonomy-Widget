<?php

namespace lloc\Mtw;

/**
 * Class Plugin
 *
 * @package lloc\mtw
 */
class Plugin {

	public string $plugin_file;

	public function __construct( string $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	public function hooks(): void {
		add_action( 'plugins_loaded', array( $this, 'init_i18n_support' ) );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		add_shortcode( 'mtw_posts', array( Posts::class, 'create_shortcode' ) );
	}

	public function init_i18n_support(): void {
		$plugin_rel_path = dirname( plugin_basename( $this->plugin_file ) ) . '/languages';

		load_plugin_textdomain( 'multisite-taxonomy-widget', false, $plugin_rel_path );
	}

	public function register_widget(): void {
		register_widget( Mtw::class );
	}
}
