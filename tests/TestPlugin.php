<?php

namespace lloc\MtwTests;

use Brain\Monkey\Functions;
use lloc\Mtw\Plugin;
use lloc\Mtw\Posts;

class TestPlugin extends MtwUnitTestCase {

	protected function setUp(): void {
		$this->test = new Plugin( '/path/to/plugin' );
	}

	public function test_load(): void {
		Functions\expect( 'add_action' )
			->once()
			->with( 'widgets_init', array( $this->test, 'register_widget' ) );

		Functions\expect( 'add_shortcode' )
			->once()
			->with( 'mtw_posts', array( Posts::class, 'create_shortcode' ) );

		$this->expectNotToPerformAssertions();

		$this->test->hooks();
	}

	public function test_register_widget(): void {
		Functions\expect( 'register_widget' )->once();

		$this->expectNotToPerformAssertions();

		$this->test->register_widget();
	}
}
