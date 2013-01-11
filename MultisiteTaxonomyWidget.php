<?php
/*
Plugin Name: Multisite Taxonomy Widget
Plugin URI: http://lloc.de/
Description: List the latest posts of a specific taxonomy from the whole blog-network 
Version: 0.1
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
		extract( $args );
		$posts = mtw_get_posts( $taxonomy, $name, $limit, array() );
		$blogs = $wpdb->get_col(
			"SELECT blog_id FROM {$wpdb->blogs} WHERE WHERE blog_id != {$wpdb->blogid} AND WHERE blog_id != {$wpdb->blogid} AND site_id = {$wpdb->siteid} AND spam = 0 AND deleted = 0 AND archived = '0'"
		);
		if ( $blogs ) {
			foreach ( $blogs as $blog_id ) {
				switch_to_blog( $blog_id );
				$posts = mtw_get_posts( $taxonomy, $name, $limit, $posts );
				restore_current_blog();
			}
		}
		echo $before_widget;
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( $title ) {
			echo $before_title;
			echo $title;
			echo $after_title;
		}
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
		echo $after_widget;
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
		$taxonomies = get_taxonomies( array( 'public' => true ), 'names' ); 
		foreach ( $taxonomies as $key => $value ) {
			printf(
				'<option value="%s"%s>%s</option>',
				$key,
				( isset( $instance['taxonomy'] ) && $key == $instance['taxonomy'] ? ' selected="selected"' : '' ),
				$value
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

function mtw_get_posts( $taxonomy, $name, $limit, array $posts ) {
	$limit = (int) $limit;
	$args  = array(
		'post_type'      => 'any',
		$taxonomy        => $name,
		'posts_per_page' => $limit,
	);
	$query = new WP_Query( $args );
	while ( $query->have_posts() ) {
		$query->next_post();
		$temp        = new StdClass;
		$temp->time  = get_the_time( 'U', $query->post->ID );
		$temp->title = get_the_title( $query->post->ID );
		$temp->href  = get_permalink( $query->post->ID );
		$posts[]     = $temp;
	}
	usort( $posts, 'mta_cmp_posts' );
	wp_reset_query();
	wp_reset_postdata();
	return( array_slice( $posts, 0, $limit ) );
}

function mta_cmp_posts( $a, $b ) {
    if ( $a->time == $b->time )
        return 0;
    return( $a->time > $b->time ) ? (-1) : 1;
}
