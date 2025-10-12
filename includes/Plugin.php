<?php

namespace lloc\Mtw;

/**
 * Class Plugin
 *
 * @package lloc\mtw
 */
class Plugin {

	/**
	 * @var string
	 */
	public string $plugin_file;

	/**
	 * Plugin constructor.
	 *
	 * @param string $plugin_file
	 */
	public function __construct( string $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	/**
	 * Register hooks
	 */
	public function hooks(): void {
		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		add_shortcode( 'mtw_posts', array( Posts::class, 'create_shortcode' ) );
	}

	/**
	 * Register widget
	 */
	public function register_widget(): void {
		register_widget( Mtw::class );
	}
}
