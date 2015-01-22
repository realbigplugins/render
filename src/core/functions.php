<?php
/**
 * Provides global helper functions for Render.
 *
 * @since 1.0.0
 *
 * @package Render
 */

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

function render_translate_id_to_name( $id ) {
	return ucwords( str_replace( array( ' ', '_', '-' ), ' ', $id ) );
}

function render_esc_atts( $atts ) {

	if ( empty( $atts ) ) {
		return $atts;
	}

	foreach ( $atts as $i => $att ) {
		$atts[ $i ] = esc_attr( $att );

		// Turn bool strings into actual bool
		$atts[ $i ] = $att == 'true' ? true : $att;
		$atts[ $i ] = $att == 'false' ? false : $att;
	}

	return $atts;
}

function render_strip_paragraphs_around_shortcodes( $content ) {

	$array = array(
		'<p>['    => '[',
		']</p>'   => ']',
		']<br />' => ']',
	);

	$content = strtr( $content, $array );

	return $content;
}

function render_associative_atts( $atts, $keyname ) {

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
				$array[ $field_name ] = _render_decode_att( $field_values[ $i ] );
			}

			$output[] = $array;
		}
	}

	return $output;
}

function _render_decode_att( $att ) {
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
function _render_sc_error( $message ) {
	return "<span class='render-sc-error'>ERROR: $message</span>";
}

function render_sc_attr_template( $template, $extra = array() ) {

	$output = array();

	switch ( $template ) {
		case 'date_format':

			$output = array(
				'label'       => __( 'Date Format', 'Render' ),
				'type'        => 'selectbox',
				'description' => sprintf(
					__( 'Format to display the date. Either choose one or input a custom date format using %s date format standards.', 'Render' ), '<a href="http://php.net/manual/en/function.date.php" target="_blank">PHP</a>' ),
				'properties'  => array(
					'placeholder'      => __( 'Select a date format or enter a custom format.', 'Render' ),
					'default'          => 'default_date',
					'allowCustomInput' => true,
					'options'          => array(
						'default_date'      => __( 'Date format set in Settings -> General', 'Render' ),
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

			$output = array(
				'label'      => __( 'Post', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'groups' => array(),
					'callback'    => array(
						'groups'   => true,
						'function' => '_render_sc_post_list',
					),
					'placeholder' => __( 'Defaults to the current post.', 'Render' ),
				),
			);
			break;
	}

	if ( ! empty( $extra ) ) {
		$output = array_merge( $output, $extra );
	}

	return $output;
}