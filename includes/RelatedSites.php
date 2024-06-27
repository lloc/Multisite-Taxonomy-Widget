<?php

namespace lloc\Mtw;

class RelatedSites {

	const PRESET = array(
		'public'   => 1,
		'archived' => 0,
		'spam'     => 0,
		'deleted'  => 0,
	);

	protected int $network_id;

	protected int $current_site;

	public function __construct( int $network_id, int $current_site ) {
		$this->network_id   = $network_id;
		$this->current_site = $current_site;
	}

	public function get( ?string $field = null ): array {
		$args  = array_merge( self::PRESET, array( 'network_id' => $this->network_id ) );
		$sites = array_filter(
			get_sites( $args ),
			function ( $site ) {
				return $this->current_site != $site->blog_id;
			}
		);

		return ! is_null( $field ) ? wp_list_pluck( $sites, $field ) : $sites;
	}
}
