<?php

namespace lloc\MtwTests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

class MtwUnitTestCase extends TestCase {

	protected $test;

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'sanitize_title' )->returnArg();
		Functions\when( 'esc_html__' )->returnArg();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}
