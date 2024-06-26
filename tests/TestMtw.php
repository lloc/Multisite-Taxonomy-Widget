<?php

namespace lloc\MtwTests;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Mtw\Mtw;

class TestMtw extends MtwUnitTestCase {

	public function setUp(): void {
		parent::setUp();

		$this->test = new Mtw();
	}

	public function test_mtw_get_formatelements() {
		$expected = array(
			'test'            => 'abc',
			'before_mtw_list' => '<ul>',
			'after_mtw_list'  => '</ul>',
			'before_mtw_item' => '<li>',
			'after_mtw_item'  => '</li>',
		);

		Filters\expectApplied( 'mtw_formatelements_output_filter' )->once()->andReturnFirstArg();

		$this->assertEquals( $expected, $this->test->get_formatelements( array( 'test' => 'abc' ) ) );
	}

	public function test_form() {
		$taxonomies = array(
			(object) array(
				'name'   => 'category',
				'labels' => (object) array( 'singular_name' => 'Category' ),
			),
			(object) array(
				'name'   => 'tag',
				'labels' => (object) array( 'singular_name' => 'Tag' ),
			),
		);

		Functions\expect( 'get_taxonomies' )->once()->andReturn( $taxonomies );

		$this->expectOutputString( '<p><label for="title">Title:</label> <input class="widefat" id="title" name="title" type="text" value="" /></p><p><label for="taxonomy">Taxonomy:</label> <select class="widefat" id="taxonomy" name="taxonomy"><option value="category" selected="selected">Category</option><option value="tag">Tag</option></select></p><p><label for="name">Name:</label> <input class="widefat" id="name" name="name" type="text" value="" /></p><p><label for="limit">Limit:</label> <input class="widefat" id="limit" name="limit" type="text" value="10" /></p><p><label for="thumbnail">Thumbnail:</label> <input class="widefat" id="thumbnail" name="thumbnail" type="text" value="0" /></p>' );

		$this->test->form( array( 'taxonomy' => 'category' ) );
	}

	public function test_update() {
		$expected = array(
			'title'     => '',
			'taxonomy'  => '',
			'name'      => '',
			'limit'     => 10,
			'thumbnail' => 0,
		);
		$this->assertEquals( $expected, $this->test->update( array(), array() ) );
	}

	public function test_widget() {
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
			array( 'blog_id' => 1 ),
			array( 'blog_id' => 2 ),
		);

		Functions\expect( 'get_posts' )->times( 2 )->andReturn( array( $a ), array( $b ) );
		Functions\expect( 'get_sites' )->once()->andReturn( $sites );
		Functions\expect( 'get_the_time' )->times( 2 )->andReturn( 1234567890 );
		Functions\expect( 'get_permalink' )->times( 2 )->andReturn( $a->slug, $b->slug );
		Functions\expect( 'get_the_post_thumbnail' )->times( 2 )->andReturn( 'Thumbnail 1', 'Thumbnail 2' );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'esc_url' )->times( 2 )->andReturnFirstArg();

		Filters\expectApplied( 'widget_title' )->once()->andReturnFirstArg();

		$this->expectOutputString( 'TEST<ul><li><a href="test-1">Thumbnail 1</a> <a href="test-1">Test 1</a></li><li><a href="test-2">Thumbnail 2</a> <a href="test-2">Test 2</a></li></ul>' );

		$this->test->widget(
			array(),
			array(
				'title'     => 'TEST',
				'thumbnail' => 1,
			)
		);
	}
}
