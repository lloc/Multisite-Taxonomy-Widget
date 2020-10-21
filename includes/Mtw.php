<?php

namespace lloc\Mtw;

/**
 * Widget
 * @package Mtw
 */
class Mtw extends \WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			'mtw',
			'Multisite Taxonomy Widget',
			[ 'description' => __( 'List the latest posts of a specific taxonomy from the whole blog-network', 'multisite-taxonomy-widget' ) ]
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
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$args = $this->get_formatelements( $args );

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
					echo apply_filters( 'mtw_widget_output_filter', $post, $instance );
				} else {
					$thumbnail  = Posts::get_thumbnail( $post, $instance );
					$post_title = apply_filters( 'the_title', $post->post_title );

					printf( '%s <a href="%s">%s</a>', $thumbnail, $post->mtw_href, $post_title );
				}

				echo $args['after_mtw_item'];
			}
			echo $args['after_mtw_list'];
		}
		echo $args['after_widget'];
	}

	/**
	 * Update
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['taxonomy'] = strip_tags( $new_instance['taxonomy'] );
		$instance['name']     = strip_tags( $new_instance['name'] );

		$temp              = (int) $new_instance['limit'];
		$instance['limit'] = ( $temp > 0 || - 1 == $temp ? $temp : 10 );

		$temp                  = (int) $new_instance['thumbnail'];
		$instance['thumbnail'] = ( 0 <= $temp ? $temp : 0 );

		return $instance;
	}

	/**
	 * Form
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		printf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'title' ),
			__( 'Title', 'multisite-taxonomy-widget' ),
			$this->get_field_name( 'title' ),
			( isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '' )
		);

		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
		printf(
			'<p><label for="%1$s">%2$s:</label> <select class="widefat" id="%1$s" name="%3$s">',
			$this->get_field_id( 'taxonomy' ),
			__( 'Taxonomy', 'multisite-taxonomy-widget' ),
			$this->get_field_name( 'taxonomy' )
		);
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
			__( 'Name', 'multisite-taxonomy-widget' ),
			$this->get_field_name( 'name' ),
			( isset( $instance['name'] ) ? esc_attr( $instance['name'] ) : '' )
		);

		printf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'limit' ),
			__( 'Limit', 'multisite-taxonomy-widget' ),
			$this->get_field_name( 'limit' ),
			( isset( $instance['limit'] ) ? (int) $instance['limit'] : 10 )
		);

		printf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'thumbnail' ),
			__( 'Thumbnail', 'multisite-taxonomy-widget' ),
			$this->get_field_name( 'thumbnail' ),
			( isset( $instance['thumbnail'] ) ? (int) $instance['thumbnail'] : 0 )
		);
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
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_formatelements( array $args ) {
		$args['before_mtw_list'] = '<ul>';
		$args['after_mtw_list']  = '</ul>';
		$args['before_mtw_item'] = '<li>';
		$args['after_mtw_item']  = '</li>';

		return apply_filters( 'mtw_formatelements_output_filter', $args );
	}

}
