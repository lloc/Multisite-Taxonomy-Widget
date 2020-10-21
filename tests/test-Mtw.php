<?php

namespace lloc\MtwTests;

class Mtw extends Mtw_UnitTestCase {

	public function get_sut() {
		\Mockery::mock( '\WP_Widget' );

		return \Mockery::mock( \lloc\Mtw\Mtw::class )->makePartial();
	}

	public function test_mtw_get_formatelements() {
		$test = $this->get_sut();

		$expected = [
		    'test' => 'abc',
		    'before_mtw_list' => '<ul>',
		    'after_mtw_list' => '</ul>',
		    'before_mtw_item' => '<li>',
		    'after_mtw_item' => '</li>',
		];

		$this->assertEquals( $expected, $test->get_formatelements( [ 'test' => 'abc' ] ) );
	}

}
