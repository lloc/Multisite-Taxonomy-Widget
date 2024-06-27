<?php

namespace lloc\Mtw;

/**
 * Class Posts
 *
 * @package lloc\mtw
 */
class Posts {

	/**
	 * Get thumbnail
	 *
	 * You can use code like this if you want to override the output of the
	 * function:
	 *
	 * <code>
	 * function my_get_thumbnail( WP_Post $post, array $atts, ?string $default = null ) {
	 *     if ( ! empty( $atts['thumbnail'] ) ) {
	 *         return sprintf(
	 *             '<a href="%s" title="%s">%s</a>',
	 *             $post->mtw_href,
	 *             apply_filters( 'the_title', $post->post_title ),
	 *             $post->mtw_thumb
	 *         );
	 *     }
	 *     return $default
	 * }
	 * add_filter( 'mtw_thumbnail_output_filter', 'my_get_thumbnail', 10, 3 );
	 * </code>
	 *
	 * @package Mtw
	 *
	 * @param \WP_Post $post
	 * @param array    $atts
	 * @param ?string  $default
	 *
	 * @return string
	 */
	public static function get_thumbnail( \WP_Post $post, array $atts ): string {
		if ( has_filter( 'mtw_thumbnail_output_filter' ) ) {
			return apply_filters( 'mtw_thumbnail_output_filter', $post, $atts );
		}

		if ( empty( $atts['thumbnail'] ) ) {
			return '';
		}

		return sprintf( '<a href="%1$s">%2$s</a>', esc_url( $post->mtw_href ), $post->mtw_thumb );
	}

	/**
	 * Create shortcode
	 *
	 * You can use code like this if you want to override the output of the
	 * function:
	 *
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

			$list[] = self::build_link( $post, $atts );
		}

		return sprintf( '<ul><li>%s</li></ul>', implode( '</li><li>', $list ) );
	}

	public static function build_link( \WP_Post $post, array $atts ): string {
		return sprintf(
			'%1$s <a href="%2$s">%3$s</a>',
			self::get_thumbnail( $post, $atts ),
			esc_url( $post->mtw_href ),
			apply_filters( 'the_title', $post->post_title )
		);
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
			$post->mtw_ts    = get_the_time( 'U', $post->ID );
			$post->mtw_href  = get_permalink( $post->ID );
			$post->mtw_thumb = get_the_post_thumbnail( $post->ID, $ts_size );

			$posts[] = $post;
		}

		usort( $posts, array( self::class, 'cmp_posts' ) );

		return array_slice( $posts, 0, $limit );
	}

	/**
	 * Compare posts
	 *
	 * @param \WP_Post $a
	 * @param \WP_Post $b
	 *
	 * @return int
	 */
	public static function cmp_posts( \WP_Post $a, \WP_Post $b ): int {
		return $a->mtw_ts <=> $b->mtw_ts;
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
