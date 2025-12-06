<?php

namespace lloc\Mtw;

/**
 * Class Posts
 *
 * @package lloc\mtw
 */
class Posts {

	/**
	 * Create shortcode
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function create_shortcode( array $atts ): string {
		$posts = self::get_posts_from_network( $atts );

		if ( empty( $posts ) ) {
			return apply_filters( 'mtw_posts_no_posts_found', '' );
		}

		$list = array();
		foreach ( $posts as $post ) {
			if ( has_filter( 'mtw_shortcode_output_filter' ) ) {
				$list[] = apply_filters( 'mtw_shortcode_output_filter', $post, $atts );
				continue;
			}

			$list[] = $post->build_link( $atts );
		}

		return sprintf( '<ul><li>%s</li></ul>', implode( '</li><li>', $list ) );
	}

	/**
	 * Get posts
	 *
	 * @param array $instance
	 * @param array $posts
	 *
	 * @return array
	 */
	public static function get_posts( array $instance, array $posts ) {
		$limit = $instance['limit'] ?? Mtw::DEFAULT_LIMIT;
		$args  = array(
			'post_type'      => 'any',
			'posts_per_page' => $limit,
			'tax_query'      => array(
				array(
					'taxonomy' => sanitize_title( $instance['taxonomy'] ?? '' ),
					'field'    => 'slug',
					'terms'    => sanitize_title( $instance['name'] ?? '' ),
				),
			),
		);

		$ts_size = ( ! empty( $instance['thumbnail'] ) ? array( (int) $instance['thumbnail'], (int) $instance['thumbnail'] ) : 'thumbnail' );

		foreach ( get_posts( $args ) as $post ) {
			$posts[] = new Post( $post, $ts_size );
		}

		usort( $posts, array( self::class, 'cmp_posts' ) );

		return array_slice( $posts, 0, $limit );
	}

	/**
	 * Compare posts
	 *
	 * @param Post $a
	 * @param Post $b
	 *
	 * @return int
	 */
	public static function cmp_posts( Post $a, Post $b ): int {
		return $a->timestamp() <=> $b->timestamp();
	}

	/**
	 * Get posts from blogs
	 *
	 * @package Mtw
	 *
	 * @param array $instance
	 *
	 * @return array
	 */
	public static function get_posts_from_network( array $instance ) {
		global $wpdb;

		$posts = self::get_posts( $instance, array() );
		$sites = ( new RelatedSites( $wpdb->siteid, $wpdb->blogid ) )->get( 'blog_id' );
		foreach ( $sites as $blog_id ) {
			switch_to_blog( $blog_id );
			$posts = self::get_posts( $instance, $posts );
			restore_current_blog();
		}

		return $posts;
	}
}
