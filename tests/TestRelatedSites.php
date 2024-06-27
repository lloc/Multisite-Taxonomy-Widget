<?php

namespace lloc\MtwTests;

use lloc\Mtw\RelatedSites;

use Brain\Monkey\Functions;
class TestRelatedSites extends MtwUnitTestCase {

	public function setUp(): void {
		parent::setUp();

		$sites = array(
			(object) array( 'blog_id' => 1 ),
			(object) array( 'blog_id' => 2 ),
			(object) array( 'blog_id' => 3 ),
			(object) array( 'blog_id' => 4 ),
		);

		Functions\expect( 'get_sites' )->once()->andReturn( $sites );

		$this->test = new RelatedSites( 1, 3 );
	}

	public function test_get_all() {
		$expected = array(
			0 => (object) array( 'blog_id' => 1 ),
			1 => (object) array( 'blog_id' => 2 ),
			3 => (object) array( 'blog_id' => 4 ),
		);

		$this->assertEquals( $expected, $this->test->get() );
	}

	public function test_get_blog_id() {
		$expected = array(
			0 => 1,
			1 => 2,
			3 => 4,
		);

		Functions\expect( 'wp_list_pluck' )->once()->andReturn( $expected );

		$this->assertEquals( $expected, $this->test->get( 'blog_id' ) );
	}
}
