<?php
/*
Plugin Name: Multisite Taxonomy Widget
Plugin URI: https://github.com/lloc/Multisite-Taxonomy-Widget
Description: List the latest posts of a specific taxonomy from your blog-network.
Version: 0.7
Author: Dennis Ploetner 
Author URI: http://lloc.de/
*/

/*
Copyright 2013  Dennis Ploetner  (email : re@lloc.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Widget
 * @package Mtw
 */
class MultisiteTaxonomyWidget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			'mtw',
			'Multisite Taxonomy',
			array( 'description' => __( 'List the latest posts of a specific taxonomy from the whole blog-network', 'mtw' ) )
		);
	}

	/**
	 * Widget
	 * 
	 * You can use code like this if you want to override the output of
	 * the method:
	 * <code>
	 * function my_widget_output( $post, array $atts ) {
	 *     return sprintf(
	 *         '<a href="%1$s" title="%2$s">%2$s</a>',
	 *         $post->mtw_href,
	 *         apply_filters( 'the_title', $post->post_title )
	 *     );
	 * }
	 * add_filter( 'mtw_widget_output_filter', 'my_widget_output' );
	 * </code>
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( array $args, array $instance ) {
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

	/**
	 * Update
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( array $new_instance, array $old_instance ) {
		$instance = $old_instance;

		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['taxonomy'] = strip_tags( $new_instance['taxonomy'] );
		$instance['name']     = strip_tags( $new_instance['name'] );

		$temp              = (int) $new_instance['limit'];
		$instance['limit'] = ( $temp > 0 || $temp == -1 ? $temp : 10 );

		$temp                  = (int) $new_instance['thumbnail'];
		$instance['thumbnail'] = ( $temp >= 0 ? $temp : 0 );
		return $instance;
	}

	/**
	 * Form
	 * @param $instance
	 */
	public function form( array $instance ) {
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

/**
 * Get posts
 * @package Mtw
 * @param array $instance
 * @param array $posts
 * @return array
 */
function mtw_get_posts( array $instance, array $posts ) {
	$args  = array(
		'post_type' => 'any',
		'tax_query' => array(
			array(
				'taxonomy' => sanitize_title( $instance['taxonomy'] ),
				'field' => 'slug',
				'terms' => sanitize_title( $instance['name'] ),
			),
		),
		'posts_per_page' => $instance['limit'],
	);
	$query   = new WP_Query( $args );
	$ts_size = ( !empty( $instance['thumbnail'] ) ?
		array( (int) $instance['thumbnail'], (int) $instance['thumbnail'] ) :
		'thumbnail'
	);
	while ( $query->have_posts() ) {
		$query->next_post();
		$query->post->mtw_ts    = get_the_time( 'U', $query->post->ID );
		$query->post->mtw_href  = get_permalink( $query->post->ID );
		$query->post->mtw_thumb = get_the_post_thumbnail( $query->post->ID, $ts_size );
		$posts[] = mtw_post_faxctory( $query->post );
	}
	usort( $posts, 'mtw_cmp_posts' );
	wp_reset_query();
	wp_reset_postdata();
	return( array_slice( $posts, 0, $instance['limit'] ) );
}

/**
 * Compare posts
 * @package Mtw
 * @param WP_Post $a
 * @param WP_Post $b
 * @return int
 */
function mtw_cmp_posts( WP_Post $a, WP_Post $b ) {
	if ( $a->mtw_ts == $b->mtw_ts )
		return 0;
	return( $a->mtw_ts > $b->mtw_ts ? (-1) : 1 );
}

/**
 * Get posts from blogs
 * @package Mtw
 * @param array $instance
 * @return array
 */
function mtw_get_posts_from_blogs( array $instance ) {
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

/**
 * Plugin init
 * @package Mtw
 */
function mtw_plugin_init() {
	load_plugin_textdomain(
		'mtw',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages/'
	);
}
add_action( 'init', 'mtw_plugin_init' );

/**
 * Create shortcode
 * 
 * You can use code like this if you want to override the output of the
 * function:
 * <code>
 * function my_create_shortcode( WP_Post $post, array $atts ) {
 *     return sprintf(
 *         '<a href="%1$s" title="%2$s">%2$s</a>',
 *         $post->mtw_href,
 *         apply_filters( 'the_title', $post->post_title )
 *     );
 * }
 * add_filter( 'mtw_shortcode_output_filter', 'my_create_shortcode' );
 * </code>
 * @package Mtw
 * @param array $atts
 * @return string
 */
function mtw_create_shortcode( array $atts ) {
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

/**
 * Get thumbnail
 * 
 * You can use code like this if you want to override the output of the
 * function:
 * <code>
 * function my_get_thumbnail( WP_Post $post, array $atts ) {
 *     if ( !empty( $atts['thumbnail'] ) ) {
 *         return sprintf(
 *             '<a href="%s" title="%s">%s</a>',
 *             $post->mtw_href,
 *             apply_filters( 'the_title', $post->post_title ),
 *             $post->mtw_thumb
 *         );
 *     }
 *     return '';
 * }
 * add_filter( 'mtw_thumbnail_output_filter', 'my_get_thumbnail' );
 * </code>
 * @package Mtw
 * @param WP_Post $post
 * @param array $atts
 * @return string
 */
function mtw_get_thumbnail( WP_Post $post, array $atts ) {
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

/**
 * Get formatelements
 * 
 * You can use code like this if you want to override the output of the
 * function:
 * <code>
 * function my_get_formatelements( $args ) {
 *     $args['before_mtw_list'] = '<div>';
 *     $args['after_mtw_list']  = '</div>';
 *     $args['before_mtw_item'] = '<p>';
 *     $args['after_mtw_item']  = '</p>';
 *     return $args;
 * }
 * add_filter( 'mtw_formatelements_output_filter', 'my_get_formatelements' );
 * </code>
 * @package Mtw
 * @param array $args
 * @return array
 */ 
function mtw_get_formatelements( array $args ) {
	$args['before_mtw_list'] = '<ul>';
	$args['after_mtw_list']  = '</ul>';
	$args['before_mtw_item'] = '<li>';
	$args['after_mtw_item']  = '</li>';
	return apply_filters( 'mtw_formatelements_output_filter', $args );
}

/**
 * Post factory
 * 
 * Factory as workaround for the new introduced class WP_Post
 * @package Mtw
 * @param mixed $obj
 * @return mixed
 * @since 0.7
 */
function mtw_post_faxctory( $obj ) {
	if ( is_object( $obj ) && $obj instanceof StdClass ) {
		$new = new WP_Post;
		foreach ( $obj as $key => $value ) {
			$new->$key = $value;
		}
		return $new;
	}
	return $obj;
}

if ( !class_exists( 'WP_Post' ) ) {
	/**
	 * WP_Post
	 * 
	 * There will be no core class WP_Post if the WordPress version is 
	 * not 3.6 and higher.
	 * @package Mtw
	 */
	class WP_Post extends StdClass { }
}
