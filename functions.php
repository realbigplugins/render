<?php
// Create the new function for adding shortcodes
function add_usl_shortcode( $name, $function, $title, $desc, $category, $atts = '', $example = '' ) {

// Create the actual shortcode
add_shortcode( $name, $function );

// Add the shortcode info to our list
global $usl_codes;
	$usl_codes[] = array(
			'Title' => $title,
			'Code' => $name,
			'Atts' => $atts,
			'Description' => $desc,
			'Example' => $example,
			'Category' => $category
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
}

/**
 * @param $a
 * @param $b
 *
 * @return mixed
 */
function usl_sort_title_asc($a, $b) {
	return strcasecmp( $b['Title'], $a['Title'] );
}
function usl_sort_title_desc($a, $b) {
	return strcasecmp( $a['Title'], $b['Title'] );
}