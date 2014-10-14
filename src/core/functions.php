<?php
// Create the new function for adding shortcodes
function add_usl_shortcode( $args ) {

	global $USL, $shortcode_tags;

	// Establish defaults
	$defaults = array(
		'code' => '',
		'function' => '',
		'title' => '',
		'description' => '',
		'category' => '',
		'atts' => array(),
		'example' => '',
		'wrapping' => false,
	);
	wp_parse_args( $args, $defaults );

	// Establish default attribute properties (if any exist)
	if ( ! empty( $args['atts'] ) ) {

		$defaults = array(
			'required' => '0',
		);

		foreach ( $args['atts'] as $i => $att ) {
			wp_parse_args( $args['atts'][ $i ], $defaults );
		}
	}

	// Create the actual shortcode if it hasn't yet been created
	if ( ! array_key_exists( $args['code'], $shortcode_tags ) ) {
		add_shortcode( $args['code'], $args['function'] );
	}

	// Add the shortcode info to our list if it hasn't yet been added
	if ( ! array_key_exists( $args['code'], $USL->shortcodes ) ) {

		$USL->shortcodes[ $args['code'] ] = array(
			'title'       => $args['title'],
			'atts'        => $args['atts'],
			'description' => $args['desc'],
			'example'     => $args['example'],
			'category'    => $args['category'],
			'wrapping'    => $args['wrapping '] ? '1' : '0',
		);
	}
}

/**
 * @param $code
 *
 * @return string
 */
function usl_core_shortcodes( $code ) {
	$core = array(
		'embed',
		'caption',
		'wp_caption',
		'gallery',
		'playlist',
		'audio',
		'video'
	);
	if ( in_array( $code, $core ) ) {
		$code = 'Media';

		return $code;
	}

	return 'Other';
}

/**
 * @param $a
 * @param $b
 *
 * @return mixed
 */
function usl_sort_title_asc( $a, $b ) {
	return strcasecmp( $b['title'], $a['title'] );
}

function usl_sort_title_desc( $a, $b ) {
	return strcasecmp( $a['title'], $b['title'] );
}