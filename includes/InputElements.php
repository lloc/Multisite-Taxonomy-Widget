<?php

namespace lloc\Mtw;

class InputElements {

	const PRESET = array(
		'label'  => array(),
		'input'  => array(
			'class' => array(),
			'id'    => array(),
			'name'  => array(),
			'type'  => array(),
			'value' => array(),
		),
		'select' => array(
			'class' => array(),
			'id'    => array(),
			'name'  => array(),
		),
		'option' => array(
			'value'    => array(),
			'selected' => array(),
		),
	);

	/**
	 * Args for input elements
	 *
	 * @var array
	 */
	protected array $args;

	public function __construct( array $args = array() ) {
		$this->args = array_merge( self::PRESET, $args );
	}

	/**
	 * @return array
	 */
	public function get(): array {
		return apply_filters( 'mtw_inputelements_output_filter', $this->args );
	}
}
