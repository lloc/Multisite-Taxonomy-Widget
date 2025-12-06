<?php

namespace lloc\Mtw;

class Post {

	/**
	 * @var \WP_Post Post object
	 */
	protected \WP_Post $post;

	/**
	 * @var int Timestamp
	 */
	protected int $timestamp;

	/**
	 * @var string Permalink
	 */
	protected string $permalink;

	/**
	 * @var string Thumbnail HTML
	 */
	protected string $thumbnail;

	/**
	 * @param \WP_Post     $post
	 * @param array|string $ts_size
	 */
	public function __construct( \WP_Post $post, $ts_size = 'thumbnail' ) {
		$this->post = $post;

		$this->timestamp = get_the_time( 'U', $post->ID );
		$this->permalink = get_permalink( $post->ID );
		$this->thumbnail = get_the_post_thumbnail( $post->ID, $ts_size );
	}

	/**
	 * Get thumbnail
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function get_thumbnail( array $atts ): string {
		if ( has_filter( 'mtw_thumbnail_output_filter' ) ) {
			return apply_filters( 'mtw_thumbnail_output_filter', $this, $atts );
		}

		if ( empty( $atts['thumbnail'] ) ) {
			return '';
		}

		return sprintf( '<a href="%1$s">%2$s</a>', esc_url( $this->permalink ), $this->thumbnail );
	}

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public function build_link( array $atts ): string {
		return sprintf(
			'%1$s <a href="%2$s">%3$s</a>',
			$this->get_thumbnail( $atts ),
			esc_url( $this->permalink ),
			apply_filters( 'the_title', $this->post->post_title )
		);
	}

	/**
	 * Get timestamp
	 *
	 * @return int
	 */
	public function timestamp(): int {
		return $this->timestamp;
	}
}
