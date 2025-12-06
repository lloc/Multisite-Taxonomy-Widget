<?php

namespace lloc\MtwTests;

use Brain\Monkey\Filters;
use lloc\Mtw\FormatElements;

class TestFormatElements extends MtwUnitTestCase {

	public function test_get() {
		$expected = array(
			'test'            => 'abc',
			'before_mtw_list' => '<ul>',
			'after_mtw_list'  => '</ul>',
			'before_mtw_item' => '<li>',
			'after_mtw_item'  => '</li>',
			'before_widget'   => '',
			'after_widget'    => '',
			'before_title'    => '',
			'after_title'     => '',
		);

		Filters\expectApplied( FormatElements::MTW_FORMATELEMENTS_OUTPUT_FILTER )->once()->andReturnFirstArg();

		$test = new FormatElements( array( 'test' => 'abc' ) );

		$this->assertEquals( $expected, $test->get() );
	}
}
