<?php
/**
 * Contains all Render packaged shortcodes within the Time category.
 *
 * @since      1.0.0
 *
 * @package    Render
 * @subpackage Shortcodes
 */

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Loops through each shortcode and adds it to Render
foreach ( array(
	/*
	 * Custom Date
	 *
	 * Outputs the current date in a custom format.
	 *
	 * @since 1.0.0
	 *
	 * @att {selectbox} format   The format to display the date.
	 * @att {selectbox} timezone The timezone to use.
	 */
	array(
		'code'        => 'render_date',
		'function'    => '_render_sc_custom_date',
		'title'       => __( 'Custom Date', 'Render' ),
		'description' => __( 'Outputs the current date in a custom format.', 'Render' ),
		'atts'        => array(
			'format' => render_sc_attr_template( 'full_date_format' ),
			'timezone'   => render_sc_attr_template( 'timezone' ),
		),
		'render'      => array(
			'displayInline' => true,
		),
	),
) as $shortcode ) {

	$shortcode['category'] = 'time';
	$shortcode['source']   = 'Render';

	render_add_shortcode( $shortcode );
	render_add_shortcode_category( array(
		'id' => 'time',
		'label' => __( 'Time', 'Render'),
		'icon' => 'dashicons-clock',
	) );
}

/**
 * Gets the specified date format.
 *
 * @since  1.0.0
 * @access private
 *
 * @param array $atts The attributes sent to the shortcode.
 *
 * @return string The specified date format.
 */
function _render_sc_custom_date( $atts ) {

	$atts = shortcode_atts( array(
		'format' => 'default_date',
		'timezone'   => get_option( 'timezone_string', 'UTC' ),
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	$orig_timezone = date_default_timezone_get();
	date_default_timezone_set( $atts['timezone'] );

	// Output in the specified format
	switch ( $atts['format'] ) {
		case 'default_date':
			$output = date( get_option( 'date_format', 'F j, Y' ) );
			break;
		case 'default_time':
			$output = date( get_option( 'time_format', 'g:i a' ) );
			break;
		default:
			$output = date( $atts['format'] );
	}

	date_default_timezone_set( $orig_timezone );

	return $output;
}