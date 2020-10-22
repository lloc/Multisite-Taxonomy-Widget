<?php

namespace lloc\MtwTests;

class Posts extends Mtw_UnitTestCase {

	public function get_data() {
		return [
			[ 'a', 'b', -1 ],
			[ 'b', 'a', 1 ],
			[ 'a', 'a', 0 ],
		];
	}

	/**
	 * @dataProvider get_data
	 */
	public function test_compare_posts( $one, $two, $expected ) {
		$test = new \lloc\Mtw\Posts();

		$a = \Mockery::mock( \WP_Post::class );
		$b = \Mockery::mock( \WP_Post::class );

		$a->mtw_ts = $one;
		$b->mtw_ts = $two;

		$this->assertEquals( $expected, $test->cmp_posts( $a, $b ) );
	}

}
