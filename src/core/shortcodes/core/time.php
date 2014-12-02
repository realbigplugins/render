<?php

/**
 * Contains all USL packaged shortcodes within the Time category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// Custom Date
	array(
		'code'        => 'usl_date',
		'function'    => '_usl_sc_custom_date',
		'title'       => __( 'Custom Date', 'USL' ),
		'description' => __( 'Outputs the current date in a custom format.', 'USL' ),
		'atts'        => array(
			'format' => array(
				'label' => __( 'Format', 'USL' ),
				'default' => 'F jS, Y',
			)
		),
		'render'      => true,
	),
	// Month
	array(
		'code'        => 'usl_month',
		'function'    => '_usl_sc_month',
		'title'       => __( 'Current Month', 'USL' ),
		'description' => __( 'Outputs the current month.', 'USL' ),
		'render'      => true,
	),
	// Year
	array(
		'code'        => 'usl_year',
		'function'    => '_usl_sc_year',
		'title'       => __( 'Current Year', 'USL' ),
		'description' => __( 'Outputs the current year.', 'USL' ),
		'render'      => true,
	),
	// Day of Month
	array(
		'code'        => 'usl_day_month',
		'function'    => '_usl_sc_day_month',
		'title'       => __( 'Current Day of the Month', 'USL' ),
		'description' => __( 'Outputs the current day of the month.', 'USL' ),
		'render'      => true,
	),
	// Day of Week
	array(
		'code'        => 'usl_day_week',
		'function'    => '_usl_sc_day_week',
		'title'       => __( 'Current Day of the Week', 'USL' ),
		'description' => __( 'Outputs the current day of the week.', 'USL' ),
		'render'      => true,
	),
	// Day of Year
	array(
		'code'        => 'usl_day_year',
		'function'    => '_usl_sc_day_year',
		'title'       => __( 'Current Day of the Year', 'USL' ),
		'description' => __( 'Outputs the current day of the year.', 'USL' ),
		'render'      => true,
	),
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'time';
	usl_add_shortcode( $shortcode );
}

/**
 * Gets the specified date format.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The specified date format.
 */
function _usl_sc_custom_date( $atts ) {

	$atts = shortcode_atts( array(
		'format' => 'F jS, Y',
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	return date( $atts['format'] );
}

/**
 * Gets the current month.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @return string The current month.
 */
function _usl_sc_month() {

	return date( 'm' );
}

/**
 * Gets the current Year.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @return string The current year.
 */
function _usl_sc_year() {

	return date( 'Y' );
}

/**
 * Gets the current day of the month.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @return string The current day of the month.
 */
function _usl_sc_day_month() {

	return date( 'j' );
}

/**
 * Gets the current day of the week.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @return string The current day of the week.
 */
function _usl_sc_day_week() {

	return date( 'l' );
}

/**
 * Gets the current day of hte year.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @return string The current day of hte year.
 */
function _usl_sc_day_year() {

	return date( 'z' );
}