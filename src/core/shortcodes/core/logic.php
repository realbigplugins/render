<?php

/**
 * Contains all USL packaged shortcodes within the Logic category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */
class USL_CoreShortcodes_Logic {

	private $_shortcodes = array(
		array(
			'code'        => 'usl_if',
			'function'    => '_usl_sc_if',
			'title'       => 'If',
			'description' => 'Allows for the use of conditional statements for showing content.',
			'atts'        => array(
				'arg1'     => array(
					'selectbox' => array(
						'allowCustomInput' => true,
						'options'          => array(
							'logged_in' => 'User is logged in',
							'home'      => 'Current page is home page',
							'comments'  => 'Comments allowed',
							'single'    => 'Is single post',
							'page'      => 'Is page',
							'category'  => 'Is category page',
							'tag'       => 'Is tag page',
							'tax'       => 'Is taxonomy page',
							'author'    => 'Is archive of specific author',
							'archive'   => 'Current page is an archive page',
							'search'    => 'Current page is a search results page',
							'404'       => 'Current page is a 404',
						),
					),
				),
				'arg2'     => array(
					'selectbox' => array(
						'allowCustomInput' => true,
						'placeholder' => 'True',
						'options'          => array(
							'true'  => 'True',
							'false' => 'False',
						),
					),
				),
				'operator' => array(
					'selectbox' => array(
						'allowCustomInput' => true,
						'placeholder' => 'Equals',
						'options'          => array(
							'===' => 'Identical',
							'=='  => 'Equals',
							'!='  => 'Does not equal',
							'!==' => 'Not identical',
							'<'   => 'Less than',
							'>'   => 'Greater than',
							'<='  => 'Less than or equal to',
							'>='  => 'Greater than or equal',
						),
					),
				),
				'param'    => array(
					'description' => 'Used in some conditions to further specify the condition.',
				),
			),
			'render'      => array(
				'displayBlock' => true,
				'noStyle'      => true,
			),
			'wrapping'    => true,
		),
	);

	function __construct() {

		foreach ( $this->_shortcodes as $shortcode ) {
			$shortcode['category'] = 'logic';
			usl_add_shortcode( $shortcode );
		}
	}
}

new USL_CoreShortcodes_Logic();

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
	$output = '';
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