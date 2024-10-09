<?php

namespace lloc\Mtw;

/**
 * Widget
 *
 * @package Mtw
 */
class Mtw extends \WP_Widget {

	const DEFAULT_LIMIT = 10;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			'mtw',
			'Multisite Taxonomy Widget',
			array(
				'description' => __(
					'List the latest posts of a specific taxonomy from the whole blog-network',
					'multisite-taxonomy-widget'
				),
			)
		);
	}

	/**
	 * Widget
	 *
	 * You can use code like this if you want to override the output of
	 * the method:
	 *
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
		$args = ( new FormatElements( $args ) )->get();

		$content = array( $args['before_widget'] );

		$title = apply_filters( 'widget_title', $instance['title'] ?? '' );
		if ( $title ) {
			$content[] = $args['before_title'];
			$content[] = $title;
			$content[] = $args['after_title'];
		}

		$posts  = Posts::get_posts_from_network( $instance );
		$filter = has_filter( 'mtw_widget_output_filter' );
		if ( $posts ) {
			$content[] = $args['before_mtw_list'];

			foreach ( $posts as $post ) {
				$content[] = $args['before_mtw_item'];

				if ( $filter ) {
					$content[] = apply_filters( 'mtw_widget_output_filter', $post, $instance );
				} else {
					$content[] = Posts::build_link( $post, $instance );
				}

				$content[] = $args['after_mtw_item'];
			}

			$content[] = $args['after_mtw_list'];
		}

		$content[] = $args['after_widget'];

		echo wp_kses_post( implode( '', $content ) );
	}

	/**
	 * Update
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $instance ) {
		$instance['title']    = wp_strip_all_tags( $new_instance['title'] ?? '' );
		$instance['taxonomy'] = wp_strip_all_tags( $new_instance['taxonomy'] ?? '' );
		$instance['name']     = wp_strip_all_tags( $new_instance['name'] ?? '' );

		$temp              = intval( $new_instance['limit'] ?? 0 );
		$instance['limit'] = ( $temp > 0 || - 1 == $temp ? $temp : self::DEFAULT_LIMIT );

		$temp                  = intval( $new_instance['thumbnail'] ?? 0 );
		$instance['thumbnail'] = ( 0 <= $temp ? $temp : 0 );

		return $instance;
	}

	/**
	 * Form
	 *
	 * @param array<string, mixed> $instance
	 *
	 * @return string
	 */
	public function form( $instance ) {
		$params = array(
			'title'     => $instance['title'] ?? '',
			'taxonomy'  => $instance['taxonomy'] ?? '',
			'name'      => $instance['name'] ?? '',
			'limit'     => $instance['limit'] ?? self::DEFAULT_LIMIT,
			'thumbnail' => $instanceq['thumbnail'] ?? 0,
		);

		$content = array(
			sprintf(
				'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
				$this->get_field_id( 'title' ),
				esc_html__( 'Title', 'multisite-taxonomy-widget' ),
				$this->get_field_name( 'title' ),
				esc_attr( $params['title'] )
			),
		);

		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

		$content[] = sprintf(
			'<p><label for="%1$s">%2$s:</label> <select class="widefat" id="%1$s" name="%3$s">',
			$this->get_field_id( 'taxonomy' ),
			esc_html__( 'Taxonomy', 'multisite-taxonomy-widget' ),
			$this->get_field_name( 'taxonomy' )
		);
		foreach ( $taxonomies as $taxonomy ) {
			$content[] = sprintf(
				'<option value="%s" %s>%s</option>',
				$taxonomy->name,
				selected( $taxonomy->name, $params['taxonomy'], false ),
				$taxonomy->labels->singular_name
			);
		}
		$content[] = '</select></p>';

		$content[] = sprintf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'name' ),
			esc_html__( 'Name', 'multisite-taxonomy-widget' ),
			$this->get_field_name( 'name' ),
			esc_attr( $params['name'] )
		);

		$content[] = sprintf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'limit' ),
			esc_html__( 'Limit', 'multisite-taxonomy-widget' ),
			$this->get_field_name( 'limit' ),
			intval( $params['limit'] )
		);

		$content[] = sprintf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'thumbnail' ),
			esc_html__( 'Thumbnail', 'multisite-taxonomy-widget' ),
			$this->get_field_name( 'thumbnail' ),
			intval( $params['thumbnail'] )
		);

		$allowed_html = ( new InputElements( wp_kses_allowed_html( 'post' ) ) )->get();

		echo wp_kses( implode( '', $content ), $allowed_html );

		return 'form';
	}
}
