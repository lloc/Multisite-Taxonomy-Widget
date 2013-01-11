<?php
/*
Plugin Name: Multisite Taxonomy Widget
Plugin URI: http://lloc.de/multisite-taxonomy-widgets
Description: List the latest posts of a specific taxonomy in the whole blog-network 
Version: 0.1
Author: Dennis Ploetner 
Author URI: http://lloc.de/
*/

/**
 * The Multisite Taxonomy Widget
 * @package mtw
 */
class MultisiteTaxonomyWidget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 
			'multisitetaxonomywidget',
			'MultisiteTaxonomyWidget',
			'Multisite Taxonomy'
		);
	}

	/**
	 * Output of the widget in the frontend
	 * @param array $args
	 * @param array $instance
	 * @uses MslsOutput
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( $title )
			echo $before_title . $title . $after_title;
		echo '<!-- content -->';
		echo $after_widget;
	}

	/**
	 * Update widget in the backend
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['taxonomy'] = strip_tags( $new_instance['taxonomy'] );
		$instance['name']     = strip_tags( $new_instance['name'] );
		return $instance;
	}

	/**
	 * Display an input-form in the backend
	 * @param array $instance
	 */
	public function form( $instance ) {
		printf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'title' ),
			__( 'Title:' ),
			$this->get_field_name( 'title' ),
			( isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '' )
		);
		printf(
			'<p><label for="%1$s">%2$s:</label> <select id="%1$s" name="%3$s">',
			$this->get_field_id( 'taxonomy' ),
			__( 'Taxonomy:' ),
			$this->get_field_name( 'taxonomy' )
		);
		$taxonomies = get_taxonomies( array( 'public' => true ), 'names' ); 
		foreach ( $taxonomies as $taxonomy ) {
			printf(
				'<option%s>%s</option>',
				( isset( $instance['taxonomy'] ) && $taxonomy == $instance['taxonomy'] ? 'selected="selected"' : '' ),
				$taxonomy
			);
		}
		echo '</select></p>' );
		printf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'name' ),
			__( 'Name:' ),
			$this->get_field_name( 'name' ),
			( isset( $instance['name'] ) ? esc_attr( $instance['name'] ) : '' )
		);
	}

}
