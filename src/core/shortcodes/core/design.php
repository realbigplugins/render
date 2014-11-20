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
				'link'        => array(
					'validate' => array(
						'url:',
					),
					'sanitize' => array(
						'url:',
					),
				),
				'color'       => array(
					'colorpicker' => array(),
					'default'     => '#bada55',
				),
				'color_hover' => array(
					'colorpicker' => array(),
					'default'     => '#84A347',
				),
				'font_color'  => array(
					'colorpicker' => array(),
					'default'     => '#fff',
				),
				'size'        => array(
					'selectbox' => array(
						'large',
						'medium',
						'small'
					),
				),
				'shape'       => array(
					'selectbox' => array(
						'square',
						'rounded',
						'circle',
					),
				),
				'icon'        => array(),
			),
			'example'     => '[usl_button link="#" size="large" color="blue" shape="round"]Click here[/usl_button]',
			'wrapping'    => true,
			'render'      => array(
				'noStyle' => true,
			),
		),
		// Box
		array(
			'code'        => 'usl_box',
			'function'    => '_usl_sc_box',
			'title'       => 'Box',
			'description' => 'Creates a nice box for your content.',
			'atts'        => array(
				'color'   => array(
					'colorpicker' => '',
				),
				'font_color'   => array(
					'colorpicker' => '',
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
			'render'      => array(
				'displayBlock' => true,
				'noStyle'      => true,
			),
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
			'render'      => array(
				'noStyle'      => true,
				'displayBlock' => true,
			),
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
			$shortcode['category'] = 'design';
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
		'link'        => '#',
		'size'        => 'medium',
		'color'       => '#bada55',
		'color_hover' => '#84A347',
		'font_color'  => '#fff',
		'shape'       => '',
		'icon'        => '',
	), $atts );

	$class = 'usl-button';
	$class .= ! empty( $atts['size'] ) ? "-$atts[size]" : '';
	$class .= ! empty( $atts['shape'] ) ? "-$atts[shape]" : '';

	$output = "<a href='$atts[link]' class='$class'";
	$output .= " style='background: $atts[color]; color: $atts[font_color]'";
	$output .= '>';
	$output .= "<span class='hover' style='background: $atts[color_hover]'></span>";
	$output .= ! empty( $atts['icon'] ) ? "<span class='icon dashicons $atts[icon]'></span>" : '';
	$output .= '<span class="content">';
	$output .= usl_shortcode_content( $content );
	$output .= '</span>';
	$output .= '</a>';

	return $output;
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
		'color'   => '#bada55',
		'shape'   => 'rounded',
		'font_color' => '#222',
		'heading' => ''
	), $atts );

	$class = 'usl-box';
	$class .= ! empty( $atts['shape'] ) ? "-$atts[shape]" : '';

	$output = "<div class='$class'";
	$output .= " style='background: $atts[color]; color: $atts[font_color]'";
	$output .= '>';
	$output .= ! empty( $atts['heading'] ) ? "<h3>$atts[heading]</h3>" : '';
	$output .= usl_shortcode_content( do_shortcode( $content ) );
	$output .= '</div>';

	return $output;
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

	return '<div class="usl-column-2">' . usl_shortcode_content( do_shortcode( $content ) ) . '</div>';
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

	return '<div class="usl-column-3">' . usl_shortcode_content( do_shortcode( $content ) ) . '</div>';
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

	return '<div class="usl-column-4">' . usl_shortcode_content( do_shortcode( $content ) ) . '</div>';
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

	return '<div class="usl-column-5">' . usl_shortcode_content( do_shortcode( $content ) ) . '</div>';
}