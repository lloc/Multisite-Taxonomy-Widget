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
	 * <code>
	 * function my_get_thumbnail( WP_Post $post, array $atts ) {
	 *     if ( ! empty( $atts['thumbnail'] ) ) {
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
	 *
	 * @param \WP_Post $post
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function get_thumbnail( \WP_Post $post, array $atts ): string {
		if ( has_filter( 'mtw_thumbnail_output_filter' ) ) {
			return apply_filters( 'mtw_thumbnail_output_filter', $post, $atts );
		}

		return(
			! empty( $atts['thumbnail'] ) ?
			sprintf( '<a href="%s">%s</a>', $post->mtw_href, $post->mtw_thumb ) :
			''
		);
	}

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
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function mtw_posts( array $atts ): string {
		$posts   = self::get_posts_from_blogs( $atts );
		$content = '';

		if ( $posts ) {
			$content = '<ul>';

			foreach ( $posts as $post ) {
				$content .= '<li>';

				if ( has_filter( 'mtw_shortcode_output_filter' ) ) {
					$content .= apply_filters('mtw_shortcode_output_filter', $post, $atts );
				} else {
					$content .= sprintf(
						'%s <a href="%s">%s</a>',
						self::get_thumbnail( $post, $atts ),
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


	/**
	 * Get posts
	 *
	 * @param array $instance
	 * @param array $posts
	 *
	 * @return array
	 */
	public static function get_posts( array $instance, array $posts ) {
		$args  = [
			'post_type'      => 'any',
			'posts_per_page' => $instance['limit'],
			'tax_query'      => [
				[
					'taxonomy' => sanitize_title( $instance['taxonomy'] ),
					'field'    => 'slug',
					'terms'    => sanitize_title( $instance['name'] ),
				],
			],
		];

		$query   = new \WP_Query( $args );
		$ts_size = ( ! empty( $instance['thumbnail'] ) ? [ (int) $instance['thumbnail'], (int) $instance['thumbnail'] ] : 'thumbnail' );

		while ( $query->have_posts() ) {
			$query->next_post();

			$query->post->mtw_ts    = get_the_time( 'U', $query->post->ID );
			$query->post->mtw_href  = get_permalink( $query->post->ID );
			$query->post->mtw_thumb = get_the_post_thumbnail( $query->post->ID, $ts_size );

			$posts[]                = $query->post;
		}

		usort( $posts,  [ Posts::class, 'cmp_posts' ] );

		wp_reset_query();
		wp_reset_postdata();

		return array_slice( $posts, 0, $instance['limit'] );
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
	 * @package Mtw
	 *
	 * @param array $instance
	 *
	 * @return array
	 */
	public static function get_posts_from_blogs( array $instance ) {
		global $wpdb;

		$posts   = self::get_posts( $instance, [] );
		$args    = [
			'network_id' => $wpdb->siteid,
			'public'     => 1,
			'archived'   => 0,
			'spam'       => 0,
			'deleted'    => 0,
		];

		$blog_id = $wpdb->blogid;
		$blogs   = get_sites( $args );
		if ( $blogs ) {
			foreach ( $blogs as $blog ) {
				if ( $blog_id != $blog['blog_id'] ) {
					switch_to_blog( $blog['blog_id'] );
					$posts = self::get_posts( $instance, $posts );
					restore_current_blog();
				}
			}
		}

		return $posts;
	}

}