<?php

class USL_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'usl_widget',
			'Shortcode',
			array(
				'description' => 'Adds a shortcode to your sidebar.'
			)
		);
	}

	public function widget( $args, $instance ) {

	}

	public function form( $instance ) {


	}

	public function update( $new_instance, $old_instance ) {

	}
}

function _usl_register_widget() {
	register_widget( 'USL_Widget' );
}

add_action( 'widgets_init', '_usl_register_widget' );