<?php

namespace lloc\MtwTests;

use Brain\Monkey\Functions;
use lloc\Mtw\Plugin;
use lloc\Mtw\Posts;

class TestPlugin extends MtwUnitTestCase {

	/**
	 * @doesNotPerformAssertions
	 */
	public function test_load() {
		Functions\expect( 'add_action' )
			->once()
			->with( 'plugins_loaded', array( Plugin::class, 'init_i18n_support' ) );

		Functions\expect( 'add_action' )
			->once()
			->with( 'widgets_init', array( Plugin::class, 'register_widget' ) );

		Functions\expect( 'add_shortcode' )
			->once()
			->with( 'mtw_posts', array( Posts::class, 'create_shortcode' ) );

		Plugin::init();
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function test_init_i18n_support() {
		Functions\expect( 'mtw_get_path' )->once()->andReturn( '/path/to/plugin' );
		Functions\expect( 'load_plugin_textdomain' )->once();

		Plugin::init_i18n_support();
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function test_register_widget() {
		Functions\expect( 'register_widget' )->once();

		Plugin::register_widget();
	}
}
