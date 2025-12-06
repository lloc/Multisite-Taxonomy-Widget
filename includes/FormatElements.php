<?php

namespace lloc\Mtw;

class FormatElements {

	const MTW_FORMATELEMENTS_OUTPUT_FILTER = 'mtw_formatelements_output_filter';

	const PRESET = array(
		'before_widget'   => '',
		'after_widget'    => '',
		'before_title'    => '',
		'after_title'     => '',
		'before_mtw_list' => '<ul>',
		'after_mtw_list'  => '</ul>',
		'before_mtw_item' => '<li>',
		'after_mtw_item'  => '</li>',
	);

	/**
	 * @var array
	 */
	protected array $args;

	public function __construct( array $args ) {
		$this->args = array_merge( self::PRESET, $args );
	}

	/**
	 * Get formatelements
	 *
	 * @return array
	 */
	public function get(): array {
		return apply_filters( self::MTW_FORMATELEMENTS_OUTPUT_FILTER, $this->args );
	}
}
