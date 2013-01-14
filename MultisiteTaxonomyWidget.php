<?php
/*
Plugin Name: Multisite Taxonomy Widget
Plugin URI: http://lloc.de/
Description: List the latest posts of a specific taxonomy from the whole blog-network 
Version: 0.3
Author: Dennis Ploetner 
Author URI: http://lloc.de/
*/

class MultisiteTaxonomyWidget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'mtw',
			'Multisite Taxonomy',
			array( 'description' => __( 'List the latest posts of a specific taxonomy from the whole blog-network', 'mtw' ) )
		);
	}

	public function widget( $args, $instance ) {
		global $wpdb;
		$posts = mtw_get_posts( $instance, array() );
		$blogs = $wpdb->get_col(
			"SELECT blog_id FROM {$wpdb->blogs} WHERE WHERE blog_id != {$wpdb->blogid} AND site_id = {$wpdb->siteid} AND spam = 0 AND deleted = 0 AND archived = '0'"
		);
		if ( $blogs ) {
			foreach ( $blogs as $blog_id ) {
				switch_to_blog( $blog_id );
				$posts = mtw_get_posts( $instance, $posts );
				restore_current_blog();
			}
		}
		echo $args['before_widget'];
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( $title ) {
			echo $args['before_title'];
			echo $title;
			echo $args['after_title'];
		}
		print_r( $posts );
		if ( $posts ) { 
			echo '<ul>';
			foreach ( $posts as $post ) {
				printf(
					'<li><a href="%s">%s</a></li>',
					$post->href,
					$post->title
				);
			}
			echo '</ul>';
		}
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['taxonomy'] = strip_tags( $new_instance['taxonomy'] );
		$instance['name']     = strip_tags( $new_instance['name'] );
		$instance['limit']    = (int) $new_instance['limit'];
		return $instance;
	}

	public function form( $instance ) {
		printf(
			'<p><label for="%1$s">%2$s</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'title' ),
			__( 'Title:', 'mtw' ),
			$this->get_field_name( 'title' ),
			( isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '' )
		);
		printf(
			'<p><label for="%1$s">%2$s</label> <select id="%1$s" name="%3$s">',
			$this->get_field_id( 'taxonomy' ),
			__( 'Taxonomy:', 'mtw' ),
			$this->get_field_name( 'taxonomy' )
		);
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' ); 
		foreach ( $taxonomies as $taxonomy ) {
			printf(
				'<option value="%s"%s>%s</option>',
				$taxonomy->name,
				( isset( $instance['taxonomy'] ) && $taxonomy->name == $instance['taxonomy'] ? ' selected="selected"' : '' ),
				$taxonomy->labels->singular_name
			);
		}
		echo '</select></p>';
		printf(
			'<p><label for="%1$s">%2$s</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'name' ),
			__( 'Name:', 'mtw' ),
			$this->get_field_name( 'name' ),
			( isset( $instance['name'] ) ? esc_attr( $instance['name'] ) : '' )
		);
		printf(
			'<p><label for="%1$s">%2$s</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'limit' ),
			__( 'Limit:', 'mtw' ),
			$this->get_field_name( 'limit' ),
			( isset( $instance['limit'] ) ? (int) $instance['limit'] : 10 )
		);
	}

}
add_action( 'widgets_init', create_function( '', 'register_widget( "MultisiteTaxonomyWidget" );' ) );

function mtw_get_posts( $instance, array $posts ) {
	$args  = array(
		'post_type' => 'any',
		'tax_query' => array(
			array(
				'taxonomy' => $instance['taxonomy'],
				'field' => 'slug',
				'terms' => sanitize title( $instance['name'] ),
			),
		),
		'posts_per_page' => $instance['limit'],
	);
	$query = new WP_Query( $args );
	while ( $query->have_posts() ) {
		$query->next_post();
		$query->timestamp = get_the_time( 'U', $query->post->ID );
		$query->title     = get_the_title( $query->post->ID );
		$query->href      = get_permalink( $query->post->ID );
		$posts[]          = $query;
	}
	usort( $posts, 'mtw_cmp_posts' );
	wp_reset_query();
	wp_reset_postdata();
	return( array_slice( $posts, 0, $instance['limit'] ) );
}

function mtw_cmp_posts( $a, $b ) {
	if ( $a->time == $b->time )
		return 0;
	return( $a->time > $b->time ) ? (-1) : 1;
}

function mtw_plugin_init() {
	load_plugin_textdomain(
		'mtw',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages/'
	);
}
add_action( 'init', 'mtw_plugin_init' );

function mtw_create_shortcode( $atts ) {
	
}
