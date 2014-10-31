<?php

/**
 * Contains all USL packaged shortcodes within the Design category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */
class USL_CoreShortcodes_Design {

	private $_shortcodes = array(
		// Button
		array(
			'code'        => 'usl_button',
			'function'    => '_usl_sc_button',
			'title'       => 'Button',
			'description' => 'Creates a sweet button',
			'atts'        => array(
				'link'  => array(),
				'color' => array(
					'colorpicker' => array(),
					'required'        => true,
				),
				'size'  => array(
					'selectbox' => array(
						'large',
						'medium',
						'small'
					),
				),
				'shape' => array(
					'selectbox' => array(
						'square',
						'rounded',
						'round',
					),
				),
			),
			'example'     => '[usl_button link="#" size="large" color="blue" shape="round"]Click here[/usl_button]',
			'wrapping'    => true,
		),
		// Box
		array(
			'code'        => 'usl_box',
			'function'    => '_usl_sc_box',
			'title'       => 'Box',
			'description' => 'Creates a nice box for your content.',
			'atts'        => array(
				'color'   => array(
					'selectbox' => array(
						'red',
						'blue',
						'green',
						'orange',
					)
				),
				'shape'   => array(
					'accepted_values' => array(
						'square',
						'rounded',
						'round',
					)
				),
				'heading' => array(),
			),
			'wrapping'    => true,
			'example'     => '[usl_box color="blue" shape="round" heading="About me"]Lorem ipsum...[/usl_box]',
		),
		// Column 2
		array(
			'code'        => 'usl_column_two',
			'function'    => '_usl_sc_column_two',
			'title'       => 'Column 2',
			'description' => 'Creates a nice column that is half the width of the container.',
			'wrapping'    => true,
			'example'     => '[usl_column_two]Lorem ipsum...[/usl_column_two]',
		),
		// Column 3
		array(
			'code'        => 'usl_column_three',
			'function'    => '_usl_sc_column_three',
			'title'       => 'Column 3',
			'description' => 'Creates a nice column that is a third the width of the container.',
			'wrapping'    => true,
			'example'     => '[usl_column_three]Lorem ipsum...[/usl_column_three]',
		),
		// Column 4
		array(
			'code'        => 'usl_column_four',
			'function'    => '_usl_sc_column_four',
			'title'       => 'Column 4',
			'description' => 'Creates a nice column that is a quarter the width of the container.',
			'wrapping'    => true,
			'example'     => '[usl_column_four]Lorem ipsum...[/usl_column_four]',
		),
		// Column 5
		array(
			'code'        => 'usl_column_five',
			'function'    => '_usl_sc_column_five',
			'title'       => 'Column 5',
			'description' => 'Creates a nice column that is a fifth the width of the container.',
			'wrapping'    => true,
			'example'     => '[usl_column_five]Lorem ipsum...[/usl_column_five]',
		)
	);

	function __construct() {

		foreach ( $this->_shortcodes as $shortcode ) {
			$shortcode['category'] = 'Design';
			usl_add_shortcode( $shortcode );
		}
	}
}

new USL_CoreShortcodes_Design();

/**
 * Wraps the content within a styled button.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The button HTML.
 */
function _usl_sc_button( $atts = array(), $content = null ) {
	$atts = shortcode_atts( array(
		'link'  => '#',
		'size'  => 'small',
		'color' => 'red',
		'shape' => 'rounded'
	), $atts );

	return "<a href='$atts[link]' class='usl-$atts[color] usl-$atts[size] usl-$atts[shape]'>$atts[content]</a>";
}


/**
 * Wraps the content within a styled box.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The box HTML.
 */
function _usl_sc_box( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'color'   => 'red',
		'shape'   => 'rounded',
		'heading' => ''
	), $atts );

	return "<div class='usl-$atts[color] usl-$atts[shape] usl-box'><h3>$atts[heading]</h3>" . do_shortcode( $atts['content'] ) . '</div>';
}


/**
 * Wraps the content within a half-width column.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_two( $atts, $content = null ) {

	return '<div class="usl-column-2">' . do_shortcode( $content ) . '</div>';
}


/**
 * Wraps the content within a third-width column.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_three( $atts, $content = null ) {

	return '<div class="usl-column-3">' . do_shortcode( $content ) . '</div>';
}


/**
 * Wraps the content within a quarter-width column.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_four( $atts, $content = null ) {

	return '<div class="usl-column-4">' . do_shortcode( $content ) . '</div>';
}


/**
 * Wraps the content within a fifth-width column.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_five( $atts, $content = null ) {

	return '<div class="usl-column-5">' . do_shortcode( $content ) . '</div>';
}