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
			'disabled' => false,
		);

		foreach ( $args['atts'] as $i => $att ) {
			$args['atts'][ $i ] = wp_parse_args( $args['atts'][ $i ], $defaults );
		}
	}

	// Add the wrapping property to the render data
	if ( $args['render'] ) {
		if ( ! is_array( $args['render'] ) ) {
			$args['render'] = array();
		}
		$args['render']['wrapping'] = $args['wrapping'];
	}

	// Create the actual shortcode if it hasn't yet been created
	if ( ! array_key_exists( $args['code'], $shortcode_tags ) ) {
		add_shortcode( $args['code'], $args['function'] );
	}

	// Add the shortcode info to our list if it hasn't yet been added
	if ( empty( $USL->shortcodes ) || ! array_key_exists( $args['code'], $USL->shortcodes ) ) {

		// TODO make this dynamic
		$USL->shortcodes[ $args['code'] ] = array(
			'title'       => $args['title'],
			'atts'        => $args['atts'],
			'source'      => $args['source'],
			'tags'        => $args['tags'],
			'description' => $args['description'],
			'example'     => $args['example'],
			'category'    => $args['category'],
			'wrapping'    => $args['wrapping'],
			'render'      => $args['render'],
			'noDisplay'   => $args['noDisplay'],
		);
	}
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
			'title'    => usl_translate_id_to_name( $code ),
		), USL::$shortcode_defaults );
	}

	// Merge Other shortcodes with USL shortcodes
	$all_shortcodes = array_merge( $_shortcode_tags, $USL->shortcodes );

	// Sort the array alphabetically by shortcode title
	uasort( $all_shortcodes, function ( $a, $b ) {
		return strcmp( $a['title'], $b['title'] );
	} );

	return $all_shortcodes;
}

function _usl_get_categories() {
	return array_unique(
		wp_list_pluck(
			_usl_get_merged_shortcodes(),
			'category'
		)
	);
}

function usl_translate_id_to_name( $id ) {
	return ucwords( str_replace( array( ' ', '_', '-' ), ' ', $id ) );
}

function usl_esc_atts( $atts ) {

	if ( empty( $atts ) ) {
		return $atts;
	}

	foreach ( $atts as $i => $att ) {
		$atts[ $i ] = esc_attr( $att );
	}

	return $atts;
}

function usl_strip_paragraphs_around_shortcodes( $content ) {

	$array = array(
		'<p>['    => '[',
		']</p>'   => ']',
		']<br />' => ']',
	);

	$content = strtr( $content, $array );

	return $content;
}

function usl_associative_atts( $atts, $keyname ) {

	$output = array();

	// Cycle through all atts
	foreach ( $atts as $name => $value ) {

		// Skip if not a keyname
		if ( $name !== $keyname ) {
			continue;
		}

		// Decode our fields into an array
		$fields = json_decode( html_entity_decode( $value ), true );

		// Cycle through each field and get the total count and make them arrays
		$count = 0;
		foreach ( $fields as $field_name => $field_values ) {

			$exploded_values       = explode( '::sep::', $field_values );
			$fields[ $field_name ] = $exploded_values;
			$count                 = count( $exploded_values );
		}

		// Cycle through the total count (number of fields) and build each fields'
		for ( $i = 0; $i < $count; $i ++ ) {

			$array = array();
			foreach ( $fields as $field_name => $field_values ) {
				$array[ $field_name ] = _usl_decode_att( $field_values[ $i ] );
			}

			$output[] = $array;
		}
	}

	return $output;
}

function _usl_decode_att( $att ) {
	return str_replace( array( '::dquot::', '::squot::', '::br::' ), array( '"', '\'', '<br/>' ), $att );
}

/**
 * Outputs an HTML error message formatted by the plugin.
 *
 * @since 1.0.0
 *
 * @param string $message The error message to display.
 * @return string The HTML error message.
 */
function _usl_sc_error( $message ) {
	return "<span class='usl-sc-error'>ERROR: $message</span>";
}

function usl_sc_attr_template( $template ) {

	switch ( $template ) {
		case 'date_format':

			return array(
				'label'       => __( 'Date Format', 'USL' ),
				'type'        => 'selectbox',
				'description' => sprintf(
					__( 'Format to display the date. Either choose one or input a custom date format using %s date format standards.', 'USL' ), '<a href="http://php.net/manual/en/function.date.php" target="_blank">PHP</a>' ),
				'properties'  => array(
					'placeholder'      => __( 'Select a date format or enter a custom format.', 'USL' ),
					'default'          => 'default_date',
					'allowCustomInput' => true,
					'options'          => array(
						'default_date'      => __( 'Date format set in Settings -> General', 'USL' ),
						'l, F jS, Y - g:iA' => date( 'l, F jS, Y - g:iA' ),
						'l, F jS, Y'        => date( 'l, F jS, Y' ),
						'F jS, Y'           => date( 'F jS, Y' ),
						'M jS, Y'           => date( 'M jS, Y' ),
						'm-d-Y'             => date( 'm-d-Y' ),
						'd-m-Y'             => date( 'd-m-Y' ),
						'm-d-y'             => date( 'm-d-y' ),
						'd-m-y'             => date( 'd-m-y' ),
						'j-n-y'             => date( 'j-n-y' ),
						'n-j-y'             => date( 'n-j-y' ),
					),
				),
			);
			break;

		case 'post_list':

			return array(
				'label'      => __( 'Post', 'USL' ),
				'type'       => 'selectbox',
				'properties' => array(
					'callback'    => array(
						'groups'   => true,
						'function' => '_usl_sc_post_list',
					),
					'placeholder' => __( 'Defaults to the current post.', 'USL' ),
				),
			);
			break;
	}
}