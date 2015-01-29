<?php
/**
 * Provides global helper functions for Render.
 *
 * @since   1.0.0
 *
 * @package Render
 */

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Adds a shortcode to Render.
 *
 * @since 1.0.0
 *
 * @param array $shortcode The shortcode to add.
 */
function render_add_shortcode( $shortcode ) {

	// Add shortcode to Render
	add_filter( 'render_add_shortcodes', function ( $shortcodes ) use ( $shortcode ) {
		$shortcodes[] = $shortcode;

		return $shortcodes;
	} );
}

/**
 * Adds a shortcode category to Render.
 *
 * @since 1.0.0
 *
 * @param array $category The category to add.
 */
function render_add_shortcode_category( $category ) {

	add_filter( 'render_modal_categories', function ( $categories ) use ( $category ) {
		$categories[ $category['id'] ] = array(
			'label' => $category['label'],
			'icon'  => $category['icon'],
		);

		return $categories;
	} );
}

/**
 * Transforms an ID to a name.
 *
 * @since 1.0.0
 *
 * @param string $id The generic ID.
 * @return string The formatted name.
 */
function render_translate_id_to_name( $id ) {
	return ucwords( str_replace( array( ' ', '_', '-' ), ' ', $id ) );
}

/**
 * Escapes shortcode attributes.
 *
 * @since 1.0.0
 *
 * @param array $atts The un-escaped attributes.
 * @return array The escaped attributes.
 */
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

/**
 * Strips <p> tags around shortcodes.
 *
 * @since 1.0.0
 *
 * @param string $content The content.
 * @return string The formatted content.
 */
function render_strip_paragraphs_around_shortcodes( $content ) {

	$array = array(
		'<p>['    => '[',
		']</p>'   => ']',
		']<br />' => ']',
	);

	$content = strtr( $content, $array );

	return $content;
}

/**
 * Translates special Render associative shortcode attributes into a formatted array.
 *
 * Render uses JSON format for certain attributes (namely repeater fields). This function translates those JSON
 * attributes into a formatted, PHP usable associative array.
 *
 * @since 1.0.0
 *
 * @param array $atts    The shortcode attributes.
 * @param array $keyname The name of the key to grab values from.
 * @return array The new associative array.
 */
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
				$array[ $field_name ] = $field_values[ $i ];
			}

			$output[] = $array;
		}
	}

	return $output;
}

/**
 * Outputs an HTML error message formatted by the plugin.
 *
 * @since 1.0.0
 *
 * @param string $message The error message to display.
 * @return string The HTML error message.
 */
function render_sc_error( $message ) {
	return "<span class='render-sc-error'>ERROR: $message</span>";
}

/**
 * Returns all shortcodes disabled through Render.
 *
 * @since 1.0.0
 *
 * @return array Disabled shortcodes, or an empty array
 */
function render_get_disabled_shortcodes() {
	return get_option( 'render_disabled_shortcodes', array() );
}

/**
 * Gets all "registered" Render shortcode categories.
 *
 * @since 1.0.0
 *
 * @return mixed|void Array of Render shortcode categories.
 */
function render_get_shortcode_categories() {

	/**
	 * Shortcode categories in the modal.
	 *
	 * @since 1.0.0
	 */
	return apply_filters( 'render_modal_categories', array(
		'all'   => array(
			'label' => __( 'All', 'Render' ),
			'icon'  => 'dashicons-tagcloud',
		),
		'other' => array(
			'label' => __( 'Other', 'Render' ),
			'icon'  => 'dashicons-admin-generic',
		),
	) );
}

/**
 * Returns all shortcode categories currently in use.
 *
 * @since 1.0.0
 *
 * @return array Categories in use.
 */
function render_get_shortcode_used_categories() {

	global $Render;

	$used_categories = array_values(
		array_unique(
			wp_list_pluck( $Render->shortcodes, 'category' )
		)
	);

	return array_merge(
		array(
			'all' => array(
				'label' => __( 'All', 'Render' ),
				'icon'  => 'dashicons-tagcloud',
			),
		),
		array_intersect_key( render_get_shortcode_categories(), array_flip( $used_categories ) )
	);
}

/**
 * Outputs an attribute template.
 *
 * @since 1.0.0
 *
 * @param string $template Which template to use.
 * @param array  $extra    Extra attribute parameters to use (or override).
 * @return array Attribute.
 */
function render_sc_attr_template( $template, $extra = array() ) {

	$output = array();

	// Set the timezone accordingly for displaying the output
	$orig_timezone = date_default_timezone_get();
	date_default_timezone_set( get_option( 'timezone_string', 'UTC' ) );

	switch ( $template ) {
		case 'date_format':

			$output = array(
				'label'       => __( 'Date Format', 'Render' ),
				'type'        => 'selectbox',
				'description' => sprintf(
					__( 'Format to display the date. Either choose one or input a custom date format using %s date format standards.', 'Render' ),
					'<a href="http://php.net/manual/en/function.date.php" target="_blank">PHP</a>'
				),
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

		case 'full_date_format':

			$output = array(
				'label'       => __( 'Format', 'Render' ),
				'type'        => 'selectbox',
				'description' => sprintf(
					__( 'Format to display the date. Either choose one or input a custom date format using %s date format standards.', 'Render' ), '<a href="http://php.net/manual/en/function.date.php" target="_blank">PHP</a>' ),
				'properties'  => array(
					'placeholder'      => __( 'Select a date format or enter a custom format.', 'Render' ),
					'default'          => 'default_date',
					'allowCustomInput' => true,
					'groups'           => array(
						array(
							'label'   => __( 'Full Date', 'Render' ),
							'options' => array(
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
						array(
							'label'   => __( 'Day', 'Render' ),
							'options' => array(
								'l'  => date( 'l' ),
								'D'  => date( 'D' ),
								'jS' => date( 'jS' ),
								'd'  => date( 'd' ),
								'j'  => date( 'j' ),
							),
						),
						array(
							'label'   => __( 'Week of Year', 'Render' ),
							'options' => array(
								'W' => date( 'W' ),
							),
						),
						array(
							'label'   => __( 'Month', 'Render' ),
							'options' => array(
								'F' => date( 'F' ),
								'M' => date( 'M' ),
								'm' => date( 'm' ),
								'n' => date( 'n' ),
							),
						),
						array(
							'label'   => __( 'Year', 'Render' ),
							'options' => array(
								'Y' => date( 'Y' ),
								'y' => date( 'y' ),
							),
						),
						array(
							'label'   => __( 'Time', 'Render' ),
							'options' => array(
								'default_time' => __( 'Time format set in Settings -> General', 'Render' ),
								'g:i A'        => date( 'g:i A' ),
								'g:i a'        => date( 'g:i a' ),
								'h:i A'        => date( 'h:i A' ),
								'h:i a'        => date( 'h:i a' ),
								'H:i'          => date( 'H:i' ),
							),
						),
					),
				),
			);
			break;

		case 'post_list':

			$output = array(
				'label'      => __( 'Post', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'groups'      => array(),
					'callback'    => array(
						'function' => 'render_sc_post_list',
					),
					'placeholder' => __( 'The current post', 'Render' ),
				),
			);
			break;

		case 'timezone':

			$output = array(
				'label'      => __( 'Timezone', 'Render' ),
				'type'       => 'selectbox',
				'advanced'   => true,
				'properties' => array(
					'placeholder' => __( 'Defaults to timezone set in Settings -> General', 'Render' ),
					'callback'    => array(
						'function' => 'render_sc_timezone_dropdown',
					),
				),
			);
			break;

		case 'link':
			$output = array(
				'label' => __( 'Link', 'Render' ),
				'description' => __( 'Links to a post / page. Also accepts custom input.', 'Render' ),
				'type' => 'selectbox',
				'properties' => array(
					'allowCustomInput' => true,
					'groups' => array(),
					'callback' => array(
						'function' => 'render_sc_post_list',
					),
				),
			);
			break;
	}

	// Reset timezone
	date_default_timezone_set( $orig_timezone );

	if ( ! empty( $extra ) ) {
		$output = array_merge( $output, $extra );
	}

	return $output;
}

/**
 * Sets up the Render Modal.
 *
 * @since 1.0.0
 */
function render_enqueue_modal() {
	include_once( RENDER_PATH . 'core/modal.php' );
	new Render_Modal();
}

/**
 * Match any of all block level elements.
 *
 * https://developer.mozilla.org/en-US/docs/Web/HTML/Block-level_elements
 *
 * @since 1.0.0
 *
 * @return string The regex.
 */
function render_block_regex() {
	return '/<(address|figcaption|ol|article|figure|output|aside|footer|p|audio|form|pre|blockquote|h[1-6]|section|canvas|header|table|dd|hgroup|tfoot|div|hr|ul|dl|video|fieldset|noscript)/';
}

/**
 * Logs out the user for display in the TinyMCE rendering.
 *
 * @since 1.0.0
 */
function render_tinyme_log_out() {

	global $current_user;

	$current_user = null;
	add_filter( 'determine_current_user', '__return_false', 9999 );
}