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

		// Unset the code first
		$code = $shortcode['code'];
		unset( $shortcodes['code'] );

		$shortcodes[ $code ] = $shortcode;

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
 *
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
 *
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
 * Un-escapes the escaped shortcode attributes in the content.
 *
 * @since {{VERSION}}
 *
 * @param string $content The attribute to be escaped.
 *
 * @return string The escaped attribute.
 */
function render_sc_attr_unescape( $content ) {

	global $Render;

	$escapes = $Render::$sc_attr_escapes;

	foreach ( $escapes as $escape ) {
		$char_code = ord( $escape );
		$content   = preg_replace( "/::{$char_code}::/", $escape, $content );
	}

	return $content;
}

/**
 * Strips <p> tags around shortcodes.
 *
 * @since 1.0.0
 *
 * @param string $content The content.
 *
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
 *
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
 *
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
 * @param array  $args     Optional args that some cases use.
 *
 * @return array Attribute.
 */
function render_sc_attr_template( $template, $extra = array(), $args = array() ) {

	global $post;

	$output = array();

	// Set the timezone accordingly for displaying the output
	$orig_timezone = date_default_timezone_get();
	$timezone      = get_option( 'timezone_string', 'UTC' );
	date_default_timezone_set( ! empty( $timezone ) ? $timezone : 'UTC' );

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

			// Get our default properties set up
			$properties = array(
				'placeholder' => __( 'The current post', 'Render' ),
				'no_options'  => __( 'No posts available.', 'Render' ),
			);

			// Get our options or groups and then set the appropriate one by determining the array depth
			foreach ( $options = render_sc_post_list( $args ) as $group => $_options ) {
				$properties[ is_array( $_options ) ? 'groups' : 'options' ] = $options;
				break;
			}

			$output = array(
				'label'      => __( 'Post', 'Render' ),
				'type'       => 'selectbox',
				'default'    => is_object( $post ) ? $post->ID : null,
				'properties' => $properties,
			);
			break;

		case 'post_type_list':

			$output = array(
				'label'      => __( 'Post Type', 'Render' ),
				'type'       => 'selectbox',
				'default'    => 'any',
				'properties' => array(
					'allowDeselect' => false,
					'options'       => array(
						'any' => __( 'Any', 'Render' ),
					),
					'callback'      => array(
						'function' => 'render_sc_post_type_list',
					),
				),
			);
			break;

		case 'terms_list':

			$output = array(
				'label'      => __( 'Terms', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'callback'    => array(
						'function' => 'render_sc_term_list',
						'args'     => $args,
					),
					'placeholder' => __( 'Select a taxonomy to get terms from.', 'Render' ),
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
				'label'       => __( 'Link', 'Render' ),
				'description' => __( 'Links to a post / page.', 'Render' ),
				'type'        => 'selectbox',
				'properties'  => array(
					'placeholder'      => __( 'Select a post / page, or type a link', 'Render' ),
					'allowCustomInput' => true,
					'callback'         => array(
						'function' => 'render_sc_post_list',
						'args'     => $args,
					),
				),
			);
			break;

		case 'phone':

			$output = array(
				'label'      => __( 'Phone', 'Render' ),
				'properties' => array(
					'prefix'      => '<span class="dashicons dashicons-phone"></span>',
					'prefixWidth' => 15,
					'mask'        => array(
						'template' => 'phone',
					),
				),
			);
			break;

		case 'email':

			$output = array(
				'label'      => __( 'Email', 'Render' ),
				'properties' => array(
					'prefix'      => '<span class="dashicons dashicons-email"></span>',
					'prefixWidth' => 15,
				),
				'validate'   => array(
					'EMAIL' => true,
				),
			);
			break;

		case 'border-radius':

			$orientation = isset( $args['orientation'] ) ? $args['orientation'] : '';

			$output = array(
				'label'      => sprintf( __( 'Border %s Radius', 'Render' ), $orientation ),
				'advanced'   => true,
				'type'       => 'counter',
				'properties' => array(
					'shift_step' => 20,
					'unit'       => array(
						'default' => 'px',
						'allowed' => array(
							'px',
							'%',
							'em',
							'rem',
							'pt',
						),
					),
				),
			);
			break;

		case 'post_order':

			$output = array(
				'label'      => __( 'Order', 'Render' ),
				'type'       => 'toggle',
				'properties' => array(
					'values' => array(
						'DSC' => __( 'Descending', 'Render' ),
						'ASC' => __( 'Ascending', 'Render' ),
					),
				),
			);
			break;

		case 'post_orderby':

			$output = array(
				'label'      => __( 'Order by', 'Render' ),
				'type'       => 'selectbox',
				'default'    => 'date',
				'properties' => array(
					'options' => array(
						'none'          => __( 'None', 'Render' ),
						'ID'            => __( 'Post ID', 'Render' ),
						'author'        => __( 'Author', 'Render' ),
						'title'         => __( 'Title', 'Render' ),
						'name'          => __( 'Name (slug)', 'Render' ),
						'type'          => __( 'Post Type', 'Render' ),
						'date'          => __( 'Date', 'Render' ),
						'modified'      => __( 'Last Modified', 'Render' ),
						'parent'        => __( 'Parent', 'Render' ),
						'rand'          => __( 'Random', 'Render' ),
						'comment_count' => __( 'Comment Count', 'Render' ),
						'menu_order'    => __( 'Menu Order', 'Render' ),
					),
				),
			);
			break;
	}

	/**
	 * Allows more templates to be used.
	 *
	 * Simply call this filter, do your own switch case with various templates and output methods, and return the output.
	 * Just be sure to return the output, unmodified, if your switch case does not match anything.
	 *
	 * @since 1.0.3
	 */
	$output = apply_filters( 'render_sc_attr_templates', $output, $template, $extra, $args );

	// Reset timezone
	date_default_timezone_set( $orig_timezone );

	// Merge in the extra overrides
	if ( ! empty( $extra ) ) {
		$output = array_replace_recursive( $output, $extra );
	}

	return $output;
}

/**
 * Sets up the Render Modal.
 *
 * @since 1.0.0
 */
function render_enqueue_modal() {
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
	return '(<\/?(address|figcaption|ol|article|figure|output|aside|footer|p|audio|form|pre|blockquote|h[1-6]|section|canvas|header|table|dd|hgroup|tfoot|div|hr|ul|dl|video|fieldset|noscript)([^>]*)>)';
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

/**
 * Integrates licensing with Render.
 *
 * @since 1.0.3
 *
 * @param string $extension The unique extension ID.
 * @param string $name      The readable name of the extension.
 * @param string $version   The extension version number.
 * @param string $file_path Absolute server path to the extension base file.
 * @param string $author    The author of the extension (optional).
 */
function render_setup_license( $extension, $name, $version, $file_path, $author = 'Joel Worsham & Kyle Maurer' ) {

	// Setup name
	$name = empty( $name ) ? 'Render' : 'Render ' . $name;

	/**
	 * Initializes the EDD plugin updater.
	 *
	 * @since 1.0.3
	 */
	add_action( 'admin_init', function () use ( $extension, $name, $version, $file_path, $author ) {

		// retrieve our license key from the DB
		$license_key = trim( get_option( "{$extension}_license_key" ) );

		// setup the updater
		new EDD_SL_Plugin_Updater( REALBIGPLUGINS_STORE_URL, $file_path, array(
				//
				// current version number
				'version'   => $version,
				//
				// license key (used get_option above to retrieve from DB)
				'license'   => $license_key,
				//
				// the name of our product in EDD
				'item_name' => urlencode( $name ),
				//
				// author of this plugin
				'author'    => $author
			)
		);
	}, 0 );

	/**
	 * Integrates into Render for settings setup.
	 *
	 * @since 1.0.3
	 */
	add_filter( 'render_licensing_extensions', function ( $extensions ) use ( $extension, $name ) {

		$extensions[ $extension ] = $name;

		return $extensions;
	} );

	/**
	 * Adds a licensing nag if the license is not valid.
	 *
	 * @since {{VERSION}}
	 */
	add_action( 'after_plugin_row_' . plugin_basename( $file_path ), function () use ( $extension, $name ) {

		if ( render_check_license( $extension, $name ) != 'valid' ) {

			$html = '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">';

			$html .= sprintf( __(
				'%sActivate your license for %s%s to receive access to support and updates.',
				'Render'
			),
				'<a href="' . admin_url() . 'admin.php?page=render-settings">',
				$name,
				'</a>'
			);

			$html .= '</div></td>';

			echo $html;
		}
	} );
}

/**
 * Allows extensions to add settings to the Render Settings page to disable TinyMCE buttons.
 *
 * @since 1.0.3
 *
 * @param string $button_ID    The ID of the TinyMCE button, or the hook for the media button
 * @param string $button_label The readable label that describes the button.
 */
function render_disable_tinymce_button( $button_ID, $button_label ) {

	add_filter( 'render_disabled_tinymce_buttons', function ( $buttons ) use ( $button_ID, $button_label ) {
		$buttons[ $button_ID ] = $button_label;

		return $buttons;
	} );
}

/**
 * Similar to render_disable_tinymce_button(), but for media buttons instead (sits above the TinyMCE toolbar).
 *
 * @since 1.0.3
 *
 * @param string $hook_name The name of the hook that adds the media button.
 * @param string $label     The readable label that describes the button.
 * @param int    $priority  The priority of the hook.
 */
function render_disable_tinymce_media_button( $hook_name, $label, $priority = 10 ) {

	add_filter( 'render_disabled_tinymce_media_buttons', function ( $buttons ) use ( $hook_name, $priority ) {
		$buttons[ $hook_name ] = $priority;

		return $buttons;
	} );

	if ( get_option( "render_enable_tinymce_button_$hook_name" ) != 'enabled' ) {
		remove_action( 'media_buttons', $hook_name, $priority );
	}

	render_disable_tinymce_button( $hook_name, $label );
}

/**
 * Builds the HTML output for selectbox options.
 *
 * Loosely based on how the Modal outputs the selectbox.
 *
 * @since {{VERSION}}
 * @see   Render_Modal::att_type_selectbox()
 *
 * @param array $options The options.
 *
 * @return string The options HTML.
 */
function render_build_options_html( $options ) {

	$no_options = empty( $options );

	$groups = $options;
	if ( ! $no_options ) {

		// Optgroup support
		$opt_groups = false;
		foreach ( $options as $option ) {
			if ( $opt_groups = is_array( $option ) ) {
				break;
			}
		}

		if ( ! $opt_groups ) {
			$groups = array(
				array(
					'options' => $options,
				),
			);
		}
	}

	$output = '<option></option>';
	if ( ! $no_options ) {
		foreach ( $groups as $opt_group ) {

			if ( isset( $opt_group['label'] ) ) {
				$output .= "<optgroup label=\"$opt_group[label]\" >";
			}

			foreach ( (array) $opt_group['options'] as $option_value => $option ) {
				// Simple format support
				if ( ! is_array( $option ) ) {
					$option_label = $option;
					$option       = array(
						'label' => $option_label,
					);
				}

				$output .= "<option value=\"$option_value\">";
				$output .= $option['label'];
				$output .= '</option>';
			}
			if ( isset( $opt_group['label'] ) ) {
				$output .= '</optgroup>';
			}
		}
	}

	return $output;
}