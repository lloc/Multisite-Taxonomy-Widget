<?php

namespace lloc\MtwTests;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Mtw\Posts;
use Mockery\Mock;

class TestPosts extends MtwUnitTestCase {

	public static function get_data(): array {
		return array(
			array( 'a', 'b', -1 ),
			array( 'b', 'a', 1 ),
			array( 'a', 'a', 0 ),
		);
	}

	/**
	 * @dataProvider get_data
	 */
	public function test_compare_posts( $one, $two, $expected ) {
		$test = new Posts();

		$a = \Mockery::mock( \WP_Post::class );
		$b = \Mockery::mock( \WP_Post::class );

		$a->mtw_ts = $one;
		$b->mtw_ts = $two;

		$this->assertEquals( $expected, $test->cmp_posts( $a, $b ) );
	}

	public function test_create_shortcode() {
		global $wpdb;

		$wpdb         = \Mockery::mock( '\WPDB' );
		$wpdb->siteid = 1;
		$wpdb->blogid = 1;

		$a             = \Mockery::mock( '\WP_Post' );
		$a->post_title = 'Test 1';
		$a->slug       = 'test-1';
		$a->ID         = 13;

		$b             = \Mockery::mock( '\WP_Post' );
		$b->post_title = 'Test 2';
		$b->slug       = 'test-2';
		$b->ID         = 42;

		$sites = array(
			(object) array( 'blog_id' => 1 ),
			(object) array( 'blog_id' => 2 ),
		);

		Functions\expect( 'get_posts' )->times( 2 )->andReturn( array( $a ), array( $b ) );
		Functions\expect( 'get_the_time' )->times( 2 )->andReturn( 1234567890 );
		Functions\expect( 'get_permalink' )->times( 2 )->andReturn( $a->slug, $b->slug );
		Functions\expect( 'get_the_post_thumbnail' )->times( 2 )->andReturn( 'Thumbnail 1', 'Thumbnail 2' );
		Functions\expect( 'get_sites' )->once()->andReturn( $sites );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'esc_url' )->times( 2 )->andReturnFirstArg();

		$expected = '<ul><li> <a href="test-1">Test 1</a></li><li> <a href="test-2">Test 2</a></li></ul>';

		$this->assertEquals( $expected, ( new Posts() )->create_shortcode( array() ) );
	}

	public function test_create_shortcode_with_filter() {
		global $wpdb;

		$wpdb         = \Mockery::mock( '\WPDB' );
		$wpdb->siteid = 1;
		$wpdb->blogid = 1;

		$a             = \Mockery::mock( '\WP_Post' );
		$a->post_title = 'Test 1';
		$a->slug       = 'test-1';
		$a->ID         = 13;

		$b             = \Mockery::mock( '\WP_Post' );
		$b->post_title = 'Test 2';
		$b->slug       = 'test-2';
		$b->ID         = 42;

		$sites = array(
			(object) array( 'blog_id' => 1 ),
			(object) array( 'blog_id' => 2 ),
		);

		Functions\expect( 'get_posts' )->times( 2 )->andReturn( array( $a ), array( $b ) );
		Functions\expect( 'get_the_time' )->times( 2 )->andReturn( 1234567890 );
		Functions\expect( 'get_permalink' )->times( 2 )->andReturn( $a->slug, $b->slug );
		Functions\expect( 'get_the_post_thumbnail' )->times( 2 )->andReturn( 'Thumbnail 1', 'Thumbnail 2' );
		Functions\expect( 'get_sites' )->once()->andReturn( $sites );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'has_filter' )->times( 2 )->with( 'mtw_shortcode_output_filter' )->andReturnTrue();

		Filters\expectApplied( 'mtw_shortcode_output_filter' )->times( 2 )->andReturn( 'Test A', 'Test B' );

		$expected = '<ul><li>Test A</li><li>Test B</li></ul>';

		$this->assertEquals( $expected, ( new Posts() )->create_shortcode( array() ) );
	}

	public function test_create_shortcode_no_posts() {
		global $wpdb;

		$wpdb         = \Mockery::mock( '\WPDB' );
		$wpdb->siteid = 1;
		$wpdb->blogid = 1;

		$sites = array(
			(object) array( 'blog_id' => 1 ),
			(object) array( 'blog_id' => 2 ),
		);

		Functions\expect( 'get_posts' )->times( 2 )->andReturn( array() );
		Functions\expect( 'get_sites' )->once()->andReturn( $sites );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();

		$expected = 'No posts found';

		Filters\expectApplied( 'mtw_posts_no_posts_found' )->once()->andReturn( $expected );

		$this->assertEquals( $expected, ( new Posts() )->create_shortcode( array() ) );
	}

	public function test_get_thumbnail_has_filter() {
		$post = \Mockery::mock( '\WP_Post' );

		Functions\expect( 'has_filter' )->once()->with( 'mtw_thumbnail_output_filter' )->andReturnTrue();

		Filters\expectApplied( 'mtw_thumbnail_output_filter' )->once()->andReturn( 'Test' );

		$this->assertEquals( 'Test', ( new Posts() )->get_thumbnail( $post, array() ) );
	}
}
