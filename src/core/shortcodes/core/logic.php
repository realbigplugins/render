<?php

/**
 * Contains all USL packaged shortcodes within the Logic category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	array(
		'code'        => 'usl_if',
		'function'    => '_usl_sc_if',
		'title'       => __( 'If', 'USL' ),
		'description' => __( 'Allows for the use of conditional statements for showing content.', 'USL' ),
		'atts'        => array(
			'arg1'     => array(
				'label' => __( 'Argument One', 'USL' ),
				'selectbox' => array(
					'allowCustomInput' => true,
					'options'          => array(
						'logged_in' => __( 'User is logged in', 'USL' ),
						'home'      => __( 'Current page is home page', 'USL' ),
						'comments'  => __( 'Comments allowed', 'USL' ),
						'single'    => __( 'Is single post', 'USL' ),
						'page'      => __( 'Is page', 'USL' ),
						'category'  => __( 'Is category page', 'USL' ),
						'tag'       => __( 'Is tag page', 'USL' ),
						'tax'       => __( 'Is taxonomy page', 'USL' ),
						'author'    => __( 'Is archive of specific author', 'USL' ),
						'archive'   => __( 'Current page is an archive page', 'USL' ),
						'search'    => __( 'Current page is a search results page', 'USL' ),
						'404'       => __( 'Current page is a 404', 'USL' ),
					),
				),
			),
			'arg2'     => array(
				'label' => __( 'Argument Two', 'USL' ),
				'selectbox' => array(
					'allowCustomInput' => true,
					'placeholder'      => __( 'True', 'USL' ),
					'options'          => array(
						'true'  => __( 'True', 'USL' ),
						'false' => __( 'False', 'USL' ),
					),
				),
			),
			'operator' => array(
				'label' => __( 'Operator', 'USL' ),
				'selectbox' => array(
					'allowCustomInput' => true,
					'placeholder'      => __( 'Equals', 'USL' ),
					'options'          => array(
						'===' => __( 'Identical', 'USL' ),
						'=='  => __( 'Equals', 'USL' ),
						'!='  => __( 'Does not equal', 'USL' ),
						'!==' => __( 'Not identical', 'USL' ),
						'<'   => __( 'Less than', 'USL' ),
						'>'   => __( 'Greater than', 'USL' ),
						'<='  => __( 'Less than or equal to', 'USL' ),
						'>='  => __( 'Greater than or equal', 'USL' ),
					),
				),
			),
			'param'    => array(
				'label' => __( 'Parameter (optional)', 'USL' ),
				'description' => __( 'Used in some conditions to further specify the condition.', 'USL' ),
			),
		),
		'render'      => array(
			'displayBlock' => true,
			'noStyle'      => true,
		),
		'wrapping'    => true,
	),
);


foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'logic';
	usl_add_shortcode( $shortcode );
}

/**
 * Returns the content if the condition is met, otherwise, returns nothing.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return bool True if statement succeeds, false otherwise. Doy!
 */
function _usl_sc_if( $atts, $content ) {

	$atts = shortcode_atts( array(
		'arg1'     => 'logged_in',
		'arg2'     => 'true',
		'operator' => '==',
		'param'    => '',
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	$param = $atts['param'];

	// Correctly set arg1 to appropriate function
	$arg1 = $atts['arg1'];
	switch ( $arg1 ) {
		case 'logged_in':
			$arg1 = is_user_logged_in();
			break;
		case 'home':
			$arg1 = is_home();
			break;
		case 'single':
			$arg1 = is_single( $param );
			break;
		case 'page':
			$arg1 = is_page( $param );
			break;
		case 'category':
			$arg1 = is_category( $param );
			break;
		case 'tag':
			$arg1 = is_tag( $param );
			break;
		case 'tax':
			$arg1 = is_tax( $param );
			break;
		case 'author':
			$arg1 = is_author( $param );
			break;
		case 'archive':
			$arg1 = is_archive();
			break;
		case 'search':
			$arg1 = is_search();
			break;
		case '404':
			$arg1 = is_404();
			break;
		case 'comments':
			$arg1 = comments_open();
			break;
		default:
			$arg1 = true;
	}

	// Correctly set arg2 to boolean
	$arg2 = $atts['arg2'] === 'false' ? false : true;

	// Checks for operator
	$output   = '';
	$operator = $atts['operator'];
	switch ( $operator ) {
		case '===':
			if ( $arg1 === $arg2 ) {
				$output = $content;
			}
			break;
		case '==':
			if ( $arg1 == $arg2 ) {
				$output = $content;
			}
			break;
		case '!=':
			if ( $arg1 != $arg2 ) {
				$output = $content;
			}
			break;
		case '!==':
			if ( $arg1 !== $arg2 ) {
				$output = $content;
			}
			break;
		case '<':
			if ( $arg1 < $arg2 ) {
				$output = $content;
			}
			break;
		case '>':
			if ( $arg1 > $arg2 ) {
				$output = $content;
			}
			break;
		case '>=':
			if ( $arg1 >= $arg2 ) {
				$output = $content;
			}
			break;
		case '<=':
			if ( $arg1 <= $arg2 ) {
				$output = $content;
			}
			break;
		default:
			$output = $content;
	}

	// Differ for tinymce output
	if ( defined( 'USL_SHORTCODE_RENDERING' ) && USL_SHORTCODE_RENDERING ) {
		return '<div class="' . ( empty( $output ) ? 'hidden' : 'visible' ) . '">' . usl_shortcode_content( $content ) . '</div>';
	}

	return usl_shortcode_content( $output );
}