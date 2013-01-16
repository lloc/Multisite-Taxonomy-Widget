<?php
/*
Plugin Name: Multisite Taxonomy Widget
Plugin URI: http://lloc.de/
Description: List the latest posts of a specific taxonomy from your blog-network.
Version: 0.5
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
		$args = mtw_get_formatelements( $args );
		echo $args['before_widget'];
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( $title ) {
			echo $args['before_title'];
			echo $title;
			echo $args['after_title'];
		}
		$posts = mtw_get_posts_from_blogs( $instance );
		if ( $posts ) { 
			echo $args['before_mtw_list'];
			foreach ( $posts as $post ) {
				echo $args['before_mtw_item'];
				if ( has_filter( 'mtw_widget_output_filter' ) ) {
					echo apply_filters(
						'mtw_widget_output_filter',
						$post,
						$instance
					);
				}
				else {
					printf(
						'%s <a href="%s">%s</a>',
						mtw_get_thumbnail( $post, $instance ),
						$post->mtw_href,
						apply_filters( 'the_title', $post->post_title )
					);
				}
				echo $args['after_mtw_item'];
			}
			echo $args['after_mtw_list'];
		}
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['taxonomy']  = strip_tags( $new_instance['taxonomy'] );
		$instance['name']      = strip_tags( $new_instance['name'] );
		$instance['limit']     = (int) $new_instance['limit'];
		$instance['thumbnail'] = (int) $new_instance['thumbnail'];
		return $instance;
	}

	public function form( $instance ) {
		printf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'title' ),
			__( 'Title', 'mtw' ),
			$this->get_field_name( 'title' ),
			( isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '' )
		);
		printf(
			'<p><label for="%1$s">%2$s:</label> <select class="widefat" id="%1$s" name="%3$s">',
			$this->get_field_id( 'taxonomy' ),
			__( 'Taxonomy', 'mtw' ),
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
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'name' ),
			__( 'Name', 'mtw' ),
			$this->get_field_name( 'name' ),
			( isset( $instance['name'] ) ? esc_attr( $instance['name'] ) : '' )
		);
		printf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'limit' ),
			__( 'Limit', 'mtw' ),
			$this->get_field_name( 'limit' ),
			( isset( $instance['limit'] ) ? (int) $instance['limit'] : 10 )
		);
		printf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'thumbnail' ),
			__( 'Thumbnail', 'mtw' ),
			$this->get_field_name( 'thumbnail' ),
			( isset( $instance['thumbnail'] ) ? (int) $instance['thumbnail'] : 0 )
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
				'terms' => sanitize_title( $instance['name'] ),
			),
		),
		'posts_per_page' => $instance['limit'],
	);
	$query   = new WP_Query( $args );
	$ts_size = ( !empty( $instance['thumbnail'] ) ?
		array( $instance['thumbnail'], $instance['thumbnail'] ) :
		'thumbnail'
	);
	while ( $query->have_posts() ) {
		$query->next_post();
		$query->post->mtw_ts    = get_the_time( 'U', $query->post->ID );
		$query->post->mtw_href  = get_permalink( $query->post->ID );
		$query->post->mtw_thumb = get_the_post_thumbnail( $query->post->ID, $ts_size );
		$posts[] = $query->post;
	}
	usort( $posts, 'mtw_cmp_posts' );
	wp_reset_query();
	wp_reset_postdata();
	return( array_slice( $posts, 0, $instance['limit'] ) );
}

function mtw_cmp_posts( $a, $b ) {
	if ( $a->mtw_ts == $b->mtw_ts )
		return 0;
	return( $a->mtw_ts > $b->mtw_ts ? (-1) : 1 );
}

function mtw_get_posts_from_blogs( $instance ) {
	global $wpdb;
	$posts = mtw_get_posts( $instance, array() );
	$blogs = $wpdb->get_col(
		"SELECT blog_id FROM {$wpdb->blogs} WHERE blog_id != {$wpdb->blogid} AND site_id = {$wpdb->siteid} AND spam = 0 AND deleted = 0 AND archived = '0'"
	);
	if ( $blogs ) {
		foreach ( $blogs as $blog_id ) {
			switch_to_blog( $blog_id );
			$posts = mtw_get_posts( $instance, $posts );
			restore_current_blog();
		}
	}
	return $posts;
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
	$posts   = mtw_get_posts_from_blogs( $atts );
	$content = '';
	if ( $posts ) {
		$content = '<ul>';
		foreach ( $posts as $post ) {
			$content .= '<li>';
			if ( has_filter( 'mtw_shortcode_output_filter' ) ) {
				$content .= apply_filters(
					'mtw_shortcode_output_filter',
					$post,
					$atts
				);
			}
			else {
				$content .= sprintf(
					'%s <a href="%s">%s</a>',
					mtw_get_thumbnail( $post, $atts ),
					$post->mtw_href,
					apply_filters( 'the_title', $post->post_title )
				);
			}
			$content .= '</li>';
		}
		$content .= '</ul>';
	}
	return $content;
}
add_shortcode( 'mtw_posts', 'mtw_create_shortcode' );

function mtw_get_thumbnail( $post, array $atts ) {
	if ( !empty( $atts['thumbnail'] ) ) {
		if ( has_filter( 'mtw_thumbnail_output_filter' ) ) {
			return apply_filters(
				'mtw_thumbnail_output_filter',
				$post,
				$atts
			);
		}
		return sprintf(
			'<a href="%s">%s</a>',
			$post->mtw_href,
			$post->mtw_thumb
		);
	}
	return '';
}

function mtw_get_formatelements( array $args ) {
	$args['before_mtw_list'] = '<ul>';
	$args['after_mtw_list']  = '</ul>';
	$args['before_mtw_item'] = '<li>';
	$args['after_mtw_item']  = '</li>';
	return apply_filters( 'mtw_formatelements_output_filter', $args );
}
