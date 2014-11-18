<?php
// Create the new function for adding shortcodes
function usl_add_shortcode( $args ) {

	global $USL, $shortcode_tags;

	// Merge if it already exists
	if ( isset( $shortcode_tags[ $args['code'] ] ) ) {
		$args['function'] = $args['code'];
	}

	// Establish defaults
	$args = wp_parse_args( $args, USL::$shortcode_defaults );

	// Establish default attribute properties (if any exist)
	if ( ! empty( $args['atts'] ) ) {

		$defaults = array(
			'required' => '0',
		);

		foreach ( $args['atts'] as $i => $att ) {
			$args['atts'][ $i ] = wp_parse_args( $args['atts'][ $i ], $defaults );
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
			'description' => $args['description'],
			'example'     => $args['example'],
			'category'    => $args['category'],
			'wrapping'    => $args['wrapping'],
			'render'      => $args['render'],
		);
	}
}

function usl_shortcode_content( $content ) {
	return "<span class='usl-tinymce-shortcode-content'>$content</span>";
}

/**
 * Merges the WP global shortcode array with the USL array.
 *
 * @since USL 1.0.0
 *
 * @return array The merged shortcode array.
 */
function _usl_get_merged_shortcodes() {

	global $USL, $shortcode_tags;

	// Setup the WP $shortcode_tags to be compatible with USL
	$_shortcode_tags = array();
	foreach ( $shortcode_tags as $code => $shortcode_func ) {

		// Skips (shouldn't be many, mainly for duplicated shortcodes)
		$skips = array(
			'wp_caption',
		);
		if ( in_array( $code, $skips ) ) {
			continue;
		}

		$_shortcode_tags[ $code ] = wp_parse_args( array(
			'function' => $shortcode_func,
			'title' => ucwords( str_replace( '_', ' ', $code ) ),
		), USL::$shortcode_defaults );
	}

	// Merge WP shortcodes with USL shortcodes
	return array_merge( $_shortcode_tags, $USL->shortcodes );
}

function _usl_translate_id_to_name( $id ) {
	return ucwords( str_replace( array( ' ', '_' ), ' ', $id ) );
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