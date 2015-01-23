<?php
/**
 * Contains all Render packaged shortcodes within the Time category.
 *
 * @since      Render 1.0.0
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
	// Custom Date
	array(
		'code'        => 'render_date',
		'function'    => '_render_sc_custom_date',
		'title'       => __( 'Custom Date', 'Render' ),
		'description' => __( 'Outputs the current date in a custom format.', 'Render' ),
		'atts'        => array(
			'format' => render_sc_attr_template( 'full_date_format' ),
		),
		'render'      => true,
	),
) as $shortcode ) {

	$shortcode['category'] = 'time';
	$shortcode['source']   = 'Render';

	// Adds shortcode to Render
	add_filter( 'render_add_shortcodes', function( $shortcodes ) use ( $shortcode ) {
		$shortcodes[] = $shortcode;
		return $shortcodes;
	});

	// Add shortcode category
	add_filter( 'render_modal_categories', function( $categories ) {
		$categories['time'] = array(
			'label' => __( 'Time', 'Render' ),
			'icon' => 'dashicons-clock',
		);
		return $categories;
	});
}

/**
 * Gets the specified date format.
 *
 * @since  Render 1.0.0
 * @access Private
 *
 * @param array $atts The attributes sent to the shortcode.
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