<?php
// Create the new function for adding shortcodes
function add_usl_shortcode( $name, $function, $title, $desc, $category, $atts = array(), $example = '', $wrapping = false ) {

	global $usl_codes;

	// Create the actual shortcode
	add_shortcode( $name, $function );

	// Add the shortcode info to our list
	$usl_codes[] = array(
		'title'       => $title,
		'code'        => $name,
		'atts'        => $atts,
		'description' => $desc,
		'example'     => $example,
		'category'    => $category,
		'wrapping'    => $wrapping ? '1' : '0',
	);
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
	return strcasecmp( $b['Title'], $a['Title'] );
}

function usl_sort_title_desc( $a, $b ) {
	return strcasecmp( $a['Title'], $b['Title'] );
}