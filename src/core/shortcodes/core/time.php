<?php
/**
 * Contains all USL packaged shortcodes within the Time category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */
class USL_CoreShortcodes_Time {

	private $_shortcodes = array(
		// Custom Date
		array(
			'code' => 'usl_date',
			'function' => '_usl_sc_custom_date',
			'title' => 'Custom Date',
			'description' => 'Outputs the current date in a custom format.',
		),
		// Month
		array(
			'code' => 'usl_month',
			'function' => '_usl_sc_month',
			'title' => 'Current Month',
			'description' => 'Outputs the current month.',
		),
		// Year
		array(
			'code' => 'usl_year',
			'function' => '_usl_sc_year',
			'title' => 'Current Year',
			'description' => 'Outputs the current year.',
		),
		// Day of Month
		array(
			'code' => 'usl_day_month',
			'function' => '_usl_sc_day_month',
			'title' => 'Current Day of the Month',
			'description' => 'Outputs the current day of the month.',
		),
		// Day of Week
		array(
			'code' => 'usl_day_week',
			'function' => '_usl_sc_day_week',
			'title' => 'Current Day of the Week',
			'description' => 'Outputs the current day of the week.',
		),
		// Day of Year
		array(
			'code' => 'usl_day_year',
			'function' => '_usl_sc_day_year',
			'title' => 'Current Day of the Year',
			'description' => 'Outputs the current day of the year.',
		),
	);

	function __construct() {

		foreach ( $this->_shortcodes as $shortcode ) {
			$shortcode['category'] = 'Time';
			usl_add_shortcode( $shortcode );
		}
	}
}

new USL_CoreShortcodes_Time();

/**
 * Gets the specified date format.
 *
 * @since USL 0.1.0
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

	return date( $atts['format'] );
}

/**
 * Gets the current month.
 *
 * @since USL 0.1.0
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
 * @since USL 0.1.0
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
 * @since USL 0.1.0
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
 * @since USL 0.1.0
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
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The current day of hte year.
 */
function _usl_sc_day_year() {

	return date( 'z' );
}