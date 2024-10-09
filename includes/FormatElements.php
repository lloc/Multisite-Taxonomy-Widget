<?php

namespace lloc\Mtw;

class FormatElements {

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

	protected array $args;

	public function __construct( array $args ) {
		$this->args = array_merge( self::PRESET, $args );
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
	 *
	 * @package Mtw
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get() {
		return apply_filters( 'mtw_formatelements_output_filter', $this->args );
	}
}
