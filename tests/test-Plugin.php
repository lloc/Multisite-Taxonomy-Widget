<?php

namespace lloc\MtwTests;

use Brain\Monkey\Functions;

class Plugin extends Mtw_UnitTestCase {

	public function setUp(): void {
		parent::setUp();

		Functions\when( 'add_shortcode' )->justReturn( null );
	}

	public function test_load() {
		$this->assertInstanceOf( \lloc\Mtw\Plugin::class, \lloc\Mtw\Plugin::load() );
	}

}
