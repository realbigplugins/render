<?php

/**
 * Contains all Render packaged shortcodes within the Time category.
 *
 * @since      Render 1.0.0
 *
 * @package    Render
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// Custom Date
	array(
		'code'        => 'render_date',
		'function'    => '_render_sc_custom_date',
		'title'       => __( 'Custom Date', 'Render' ),
		'description' => __( 'Outputs the current date in a custom format.', 'Render' ),
		'atts'        => array(
			'format' => array(
				'label'      => __( 'Format', 'Render' ),
				'default'    => 'F jS, Y',
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder'      => __( 'Select a date format or enter a custom format.', 'Render' ),
					'default'          => 'default_date',
					'allowCustomInput' => true,
					'groups'           => array(
						array(
							'label'   => __( 'Full Date', 'Render' ),
							'options' => array(
								'default_date'      => __( 'Date format set in Settings -> General', 'Render' ),
								'l, F jS, Y - g:iA' => date( 'l, F jS, Y - g:iA' ),
								'l, F jS, Y'        => date( 'l, F jS, Y' ),
								'F jS, Y'           => date( 'F jS, Y' ),
								'M jS, Y'           => date( 'M jS, Y' ),
								'm-d-Y'             => date( 'm-d-Y' ),
								'd-m-Y'             => date( 'd-m-Y' ),
								'm-d-y'             => date( 'm-d-y' ),
								'd-m-y'             => date( 'd-m-y' ),
								'j-n-y'             => date( 'j-n-y' ),
								'n-j-y'             => date( 'n-j-y' ),
							),
						),
						array(
							'label'   => __( 'Day', 'Render' ),
							'options' => array(
								'l'  => date( 'l' ),
								'D'  => date( 'D' ),
								'jS' => date( 'jS' ),
								'd'  => date( 'd' ),
								'j'  => date( 'j' ),
							),
						),
						array(
							'label'   => __( 'Week of Year', 'Render' ),
							'options' => array(
								'W' => date( 'W' ),
							),
						),
						array(
							'label'   => __( 'Month', 'Render' ),
							'options' => array(
								'W' => date( 'W' ),
								'M' => date( 'M' ),
								'm' => date( 'm' ),
								'n' => date( 'n' ),
							),
						),
						array(
							'label'   => __( 'Year', 'Render' ),
							'options' => array(
								'Y' => date( 'Y' ),
								'y' => date( 'y' ),
							),
						),
						array(
							'label'   => __( 'Time', 'Render' ),
							'options' => array(
								'default_time' => __( 'Time format set in Settings -> General', 'Render' ),
								'g:i A'        => date( 'g:i A' ),
								'g:i a'        => date( 'g:i a' ),
								'h:i A'        => date( 'h:i A' ),
								'h:i a'        => date( 'h:i a' ),
								'H:i'          => date( 'H:i' ),
							),
						),
					),
				),
			)
		),
		'render'      => true,
	),
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'time';
	$shortcode['source']   = 'Render';
	render_add_shortcode( $shortcode );
}

/**
 * Gets the specified date format.
 *
 * @since  Render 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The specified date format.
 */
function _render_sc_custom_date( $atts ) {

	$atts = shortcode_atts( array(
		'format' => 'default_date',
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	// Output in the specified format
	switch ( $atts['format'] ) {
		case 'default_date':
			return date( get_option( 'date_format', 'F j, Y' ) );
			break;
		case 'default_time':
			return date( get_option( 'time_format', 'g:i a' ) );
			break;
		default:
			return date( $atts['format'] );
	}
}