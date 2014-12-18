<?php

/**
 * Contains all USL packaged shortcodes within the Design category.
 *
 * @since      USL 1.0.0
 *
 * @package    USL
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// Accordion
	// TODO Test and fix up
	array(
		'code'        => 'usl_accordion',
		'function'    => '_usl_sc_accordion',
		'title'       => __( 'Accordion', 'USL' ),
		'description' => __( 'Creates a clickable dropdown for your content', 'USL' ),
		'atts'        => array(
			'sections' => array(
				'label'      => __( 'Sections', 'USL' ),
				'required'   => true,
				'type'       => 'repeater',
				'properties' => array(
					'fields' => array(
						'heading' => array(
							'label'    => __( 'Heading', 'USL' ),
							'required' => true,
						),
						'content' => array(
							'label'        => __( 'Content', 'USL' ),
							'required'     => true,
							'type'         => 'textarea',
							'initCallback' => 'accordionUseContentInit',
						)
					),
				),
			),
		),
		'noDisplay'   => true,
		'render'      => array(
			'allowNesting' => true,
			'noStyle'      => true,
		),
	),
	// Button
	array(
		'code'        => 'usl_button',
		'function'    => '_usl_sc_button',
		'title'       => __( 'Button', 'USL' ),
		'description' => __( 'Creates a sweet button', 'USL' ),
		'atts'        => array(
			array(
				'type'  => 'section_break',
				'label' => __( 'Colors', 'USL' ),
			),
			'color'                      => array(
				'label'   => __( 'Background', 'USL' ),
				'type'    => 'colorpicker',
				'default' => USL_PRIMARY_COLOR,
			),
			'color_hover'                => array(
				'label'   => __( 'Background (Hover)', 'USL' ),
				'type'    => 'colorpicker',
				'default' => USL_PRIMARY_COLOR_DARK,
			),
			'font_color'                 => array(
				'label'   => __( 'Font', 'USL' ),
				'type'    => 'colorpicker',
				'default' => USL_PRIMARY_FONT_COLOR,
			),
			array(
				'type'  => 'section_break',
				'label' => __( 'Visual', 'USL' ),
			),
			'size'                       => array(
				'label'      => __( 'Size', 'USL' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Medium', 'USL' ),
					'options'     => array(
						'large'  => __( 'Large', 'USL' ),
						'medium' => __( 'Medium', 'USL' ),
						'small'  => __( 'Small', 'USL' ),
					),
				),
			),
			'shape'                      => array(
				'label'      => __( 'Shape', 'USL' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Rectangle', 'USL' ),
					'options'     => array(
						'rectangle' => __( 'Rectangle', 'USL' ),
						'rounded'   => __( 'Rounded', 'USL' ),
						'ellipse'   => __( 'Ellipse', 'USL' ),
					),
				),
			),
			'icon'                       => array(
				'label'      => __( 'Icon', 'USL' ),
				'type'       => 'selectbox',
				'properties' => array(
					'callback'    => 'usl_sc_icon_list',
					'allowIcons'  => true,
					'placeholder' => __( 'Select an icon (no icon by default)', 'USL' ),
				)
			),
			array(
				'type'  => 'section_break',
				'label' => __( 'Meta', 'USL' ),
			),
			'link'                       => array(
				'label' => __( 'HREF (link)', 'USL' ),
			),
			array(
				'type'        => 'section_break',
				'description' => __( 'If all border-radius\' are set to 0, none will be used. If at least one is set, all will be used.', 'USL' ),
				'advanced'    => true,
			),
			'border_top_left_radius'     => array(
				'label'      => __( 'Border Top Left Radius', 'USL' ),
				'advanced'   => true,
				'type'       => 'counter',
				'properties' => array(
					'shift_step' => 5,
					'max'        => 200,
					'unit'       => array(
						'default' => 'px',
						'custom'  => true,
						'allowed' => array(
							'px',
							'%',
							'em',
							'rem',
							'pt',
						),
					),
				),
			),
			'border_top_right_radius'    => array(
				'label'      => __( 'Border Top Right Radius', 'USL' ),
				'advanced'   => true,
				'type'       => 'counter',
				'properties' => array(
					'shift_step' => 5,
					'max'        => 200,
					'unit'       => array(
						'default' => 'px',
						'custom'  => true,
						'allowed' => array(
							'px',
							'%',
							'em',
							'rem',
							'pt',
						),
					),
				),
			),
			'border_bottom_left_radius'  => array(
				'label'      => __( 'Border Bottom Left Radius', 'USL' ),
				'advanced'   => true,
				'type'       => 'counter',
				'properties' => array(
					'shift_step' => 5,
					'max'        => 200,
					'unit'       => array(
						'default' => 'px',
						'custom'  => true,
						'allowed' => array(
							'px',
							'%',
							'em',
							'rem',
							'pt',
						),
					),
				),
			),
			'border_bottom_right_radius' => array(
				'label'      => __( 'Border Bottom Right Radius', 'USL' ),
				'advanced'   => true,
				'type'       => 'counter',
				'properties' => array(
					'shift_step' => 5,
					'max'        => 200,
					'unit'       => array(
						'default' => 'px',
						'custom'  => true,
						'allowed' => array(
							'px',
							'%',
							'em',
							'rem',
							'pt',
						),
					),
				),
			),
		),
		'wrapping'    => true,
		'render'      => array(
			'noStyle' => true,
		),
	),
	// Box
	// TODO Test and fix up
	array(
		'code'        => 'usl_box',
		'function'    => '_usl_sc_box',
		'title'       => __( 'Box', 'USL' ),
		'description' => __( 'Creates a nice box for your content.', 'USL' ),
		'atts'        => array(
			array(
				'type'  => 'section_break',
				'label' => __( 'Content', 'USL' ),
			),
			'heading'                    => array(
				'label' => __( 'Heading', 'USL' ),
			),
			array(
				'type'  => 'section_break',
				'label' => __( 'Colors', 'USL' ),
			),
			'color'                      => array(
				'label'   => __( 'Box Background', 'USL' ),
				'type'    => 'colorpicker',
				'default' => USL_PRIMARY_COLOR,
			),
			'font_color'                 => array(
				'label'   => __( 'Body Font', 'USL' ),
				'type'    => 'colorpicker',
				'default' => USL_PRIMARY_FONT_COLOR,
			),
			'heading_font_color'         => array(
				'label'   => __( 'Heading Font', 'USL' ),
				'type'    => 'colorpicker',
				'default' => USL_PRIMARY_FONT_COLOR,
			),
			array(
				'type'  => 'section_break',
				'label' => __( 'Visual', 'USL' ),
			),
			'shape'                      => array(
				'label'      => __( 'Shape', 'USL' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Rectangle', 'USL' ),
					'options'     => array(
						'rectangle' => __( 'Rectangle', 'USL' ),
						'rounded'   => __( 'Rounded', 'USL' ),
						'ellipse'   => __( 'Ellipse', 'USL' ),
					),
				),
			),
			array(
				'type'        => 'section_break',
				'description' => __( 'If all border-radius\' are set to 0, none will be used. If at least one is set, all will be used.', 'USL' ),
				'advanced'    => true,
			),
			'border_top_left_radius'     => array(
				'label'      => __( 'Border Top Left Radius', 'USL' ),
				'advanced'   => true,
				'type'       => 'counter',
				'properties' => array(
					'shift_step' => 5,
					'max'        => 200,
					'unit'       => array(
						'default' => 'px',
						'custom'  => true,
						'allowed' => array(
							'px',
							'%',
							'em',
							'rem',
							'pt',
						),
					),
				),
			),
			'border_top_right_radius'    => array(
				'label'      => __( 'Border Top Right Radius', 'USL' ),
				'advanced'   => true,
				'type'       => 'counter',
				'properties' => array(
					'shift_step' => 5,
					'max'        => 200,
					'unit'       => array(
						'default' => 'px',
						'custom'  => true,
						'allowed' => array(
							'px',
							'%',
							'em',
							'rem',
							'pt',
						),
					),
				),
			),
			'border_bottom_left_radius'  => array(
				'label'      => __( 'Border Bottom Left Radius', 'USL' ),
				'advanced'   => true,
				'type'       => 'counter',
				'properties' => array(
					'shift_step' => 5,
					'max'        => 200,
					'unit'       => array(
						'default' => 'px',
						'custom'  => true,
						'allowed' => array(
							'px',
							'%',
							'em',
							'rem',
							'pt',
						),
					),
				),
			),
			'border_bottom_right_radius' => array(
				'label'      => __( 'Border Bottom Right Radius', 'USL' ),
				'advanced'   => true,
				'type'       => 'counter',
				'properties' => array(
					'shift_step' => 5,
					'max'        => 200,
					'unit'       => array(
						'default' => 'px',
						'custom'  => true,
						'allowed' => array(
							'px',
							'%',
							'em',
							'rem',
							'pt',
						),
					),
				),
			),
		),
		'wrapping'    => true,
		'render'      => array(
			'noStyle' => true,
		),
	),
	// Column 2
	// TODO Test and fix up
	array(
		'code'        => 'usl_column_two',
		'function'    => '_usl_sc_column_two',
		'title'       => __( 'Column 2', 'USL' ),
		'description' => __( 'Creates a nice column that is half the width of the container.', 'USL' ),
		'atts'        => array(
			'padding_left'  => array(
				'label'      => __( 'Padding left', 'USL' ),
				'type'       => 'counter',
				'default'    => 10,
				'properties' => array(
					'unit' => array(
						'default' => 'px',
						'custom'  => true,
						'allowed' => array(
							'px',
							'%',
							'em',
							'rem',
							'pt',
						),
					),
				),
			),
			'padding_right' => array(
				'label'      => __( 'Padding right', 'USL' ),
				'type'       => 'counter',
				'default'    => 10,
				'properties' => array(
					'unit' => array(
						'default' => 'px',
						'custom'  => true,
						'allowed' => array(
							'px',
							'%',
							'em',
							'rem',
							'pt',
						),
					),
				),
			),
		),
		'wrapping'    => true,
		'render'      => array(
			'noStyle'      => true,
			'allowNesting' => true,
		),
	),
	// Column 3
	// TODO Test and fix up
	array(
		'code'        => 'usl_column_three',
		'function'    => '_usl_sc_column_three',
		'title'       => __( 'Column 3', 'USL' ),
		'description' => __( 'Creates a nice column that is a third the width of the container.', 'USL' ),
		'wrapping'    => true,
		'render'      => array(
			'noStyle'      => true,
			'allowNesting' => true,
		),
	),
	// Column 4
	// TODO Test and fix up
	array(
		'code'        => 'usl_column_four',
		'function'    => '_usl_sc_column_four',
		'title'       => __( 'Column 4', 'USL' ),
		'description' => __( 'Creates a nice column that is a quarter the width of the container.', 'USL' ),
		'wrapping'    => true,
		'render'      => array(
			'noStyle'      => true,
			'allowNesting' => true,
		),
	),
	// Column 5
	// TODO Test and fix up
	array(
		'code'        => 'usl_column_five',
		'function'    => '_usl_sc_column_five',
		'title'       => __( 'Column 5', 'USL' ),
		'description' => __( 'Creates a nice column that is a fifth the width of the container.', 'USL' ),
		'wrapping'    => true,
		'render'      => array(
			'noStyle'      => true,
			'allowNesting' => true,
		),
	)
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'design';
	$shortcode['source']   = 'Ultimate Shortcodes Library';
	usl_add_shortcode( $shortcode );
}

/**
 * Outside wrapper for an accordion.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The accordion HTML.
 */
function _usl_sc_accordion( $atts = array() ) {

	$atts = shortcode_atts( array(
		'sections' => false,
	), $atts );

	if ( $atts['sections'] === false ) {
		return 'ERROR: No sections set!';
	}

	$atts = usl_esc_atts( $atts );

	$sections = usl_associative_atts( $atts, 'sections' );

	$output = '<div class="usl-accordion">';

	foreach ( $sections as $section ) {
		$output .= "<h3 class='usl-accordion-title'>$section[heading]</h3>";
		$output .= '<div class="usl-accordion-content">' . wpautop( do_shortcode( $section['content'] ) ) . '</div>';
	}

	$output .= '</div>'; // .usl-accordion

	return $output;
}

/**
 * Wraps the content within a styled button.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param null|array  $atts    The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The button HTML.
 */
function _usl_sc_button( $atts = array(), $content = null ) {

	$atts = shortcode_atts( array(
		'link'                       => '#',
		'size'                       => 'medium',
		'color'                      => USL_PRIMARY_COLOR,
		'color_hover'                => USL_PRIMARY_COLOR_DARK,
		'font_color'                 => USL_PRIMARY_FONT_COLOR,
		'shape'                      => 'rectangle',
		'icon'                       => '',
		'border_top_left_radius'     => 0,
		'border_top_right_radius'    => 0,
		'border_bottom_left_radius'  => 0,
		'border_bottom_right_radius' => 0,
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	$border_radius = usl_sc_parse_border_radius( $atts );

	$class = 'usl-button';
	$class .= ! empty( $atts['size'] ) ? "-$atts[size]" : '';
	$class .= ! empty( $atts['shape'] ) ? "-$atts[shape]" : '';
	$class .= ! empty( $atts['icon'] ) ? "-icon" : '';

	$output = "<a href='$atts[link]' class='$class'";
	$output .= " style='background: $atts[color]; color: $atts[font_color]; $border_radius'";
	$output .= '>';
	$output .= "<span class='hover' style='background: $atts[color_hover]; $border_radius'></span>";
	$output .= ! empty( $atts['icon'] ) ? "<span class='icon dashicons $atts[icon]'></span>" : '';
	$output .= '<span class="content">';
	$output .= do_shortcode( $content );
	$output .= '</span>';
	$output .= '</a>';

	return $output;
}


/**
 * Wraps the content within a styled box.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param null|array  $atts    The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The box HTML.
 */
function _usl_sc_box( $atts = array(), $content = null ) {

	$atts = shortcode_atts( array(
		'color'                      => USL_PRIMARY_COLOR,
		'font_color'                 => USL_PRIMARY_FONT_COLOR,
		'heading_font_color'         => USL_PRIMARY_FONT_COLOR,
		'shape'                      => 'rectangle',
		'heading'                    => '',
		'border_top_left_radius'     => 0,
		'border_top_right_radius'    => 0,
		'border_bottom_left_radius'  => 0,
		'border_bottom_right_radius' => 0,
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	$border_radius = usl_sc_parse_border_radius( $atts );

	$class = 'usl-box';
	$class .= ! empty( $atts['shape'] ) ? "-$atts[shape]" : '';

	$output = "<div class='$class'";
	$output .= " style='background: $atts[color]; color: $atts[font_color]; $border_radius'";
	$output .= '>';
	$output .= ! empty( $atts['heading'] ) ? "<h3 style='color: $atts[heading_font_color]'>$atts[heading]</h3>" : '';
	$output .= do_shortcode( $content );
	$output .= '</div>';

	return $output;
}


/**
 * Wraps the content within a half-width column.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param null|array  $atts    The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_two( $atts = array(), $content = null ) {

	$atts = shortcode_atts( array(
		'padding_left'  => '10px',
		'padding_right' => '10px',
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	$padding = "padding-left: $atts[padding_left]; padding-right: $atts[padding_right];";

	return "<div class='usl-column-two' style='$padding'>" . do_shortcode( $content ) . '</div>';
}


/**
 * Wraps the content within a third-width column.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param null|array  $atts    The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_three( $atts, $content = null ) {

	return '<div class="usl-column-three">' . do_shortcode( $content ) . '</div>';
}


/**
 * Wraps the content within a quarter-width column.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param null|array  $atts    The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_four( $atts, $content = null ) {

	return '<div class="usl-column-four">' . do_shortcode( $content ) . '</div>';
}


/**
 * Wraps the content within a fifth-width column.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param null|array  $atts    The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_five( $atts, $content = null ) {

	return '<div class="usl-column-five">' . do_shortcode( $content ) . '</div>';
}

/**
 * Helper function for populating the icon list selectbox.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @return bool|array List of icons.
 */
function usl_sc_icon_list() {

	$icons = array(
		'menu',
		'dashboard',
		'admin-site',
		'admin-media',
		'admin-page',
		'admin-comments',
		'admin-appearance',
		'admin-plugins',
		'admin-users',
		'admin-tools',
		'admin-settings',
		'admin-network',
		'admin-generic',
		'admin-home',
		'admin-collapse',
		'admin-links',
		'admin-post',
		'format-standard',
		'format-image',
		'format-gallery',
		'format-audio',
		'format-video',
		'format-links',
		'format-chat',
		'format-status',
		'format-aside',
		'format-quote',
		'welcome-write-blog',
		'welcome-edit-page',
		'welcome-add-page',
		'welcome-view-site',
		'welcome-widgets-menus',
		'welcome-comments',
		'welcome-learn-more',
		'image-crop',
		'image-rotate-left',
		'image-rotate-right',
		'image-flip-vertical',
		'image-flip-horizontal',
		'undo',
		'redo',
		'editor-bold',
		'editor-italic',
		'editor-ul',
		'editor-ol',
		'editor-quote',
		'editor-alignleft',
		'editor-aligncenter',
		'editor-alignright',
		'editor-insertmore',
		'editor-spellcheck',
		'editor-distractionfree',
		'editor-expand',
		'editor-contract',
		'editor-kitchensink',
		'editor-underline',
		'editor-justify',
		'editor-textcolor',
		'editor-paste-word',
		'editor-paste-text',
		'editor-removeformatting',
		'editor-video',
		'editor-customchar',
		'editor-outdent',
		'editor-indent',
		'editor-help',
		'editor-strikethrough',
		'editor-unlink',
		'editor-rtl',
		'editor-break',
		'editor-code',
		'editor-paragraph',
		'align-left',
		'align-right',
		'align-center',
		'align-none',
		'lock',
		'calendar',
		'visibility',
		'post-status',
		'edit',
		'post-trash',
		'trash',
		'external',
		'arrow-up',
		'arrow-down',
		'arrow-left',
		'arrow-right',
		'arrow-up-alt',
		'arrow-down-alt',
		'arrow-left-alt',
		'arrow-right-alt',
		'arrow-up-alt2',
		'arrow-down-alt2',
		'arrow-left-alt2',
		'arrow-right-alt2',
		'leftright',
		'sort',
		'randomize',
		'list-view',
		'exerpt-view',
		'hammer',
		'art',
		'migrate',
		'performance',
		'universal-access',
		'universal-access-alt',
		'tickets',
		'nametag',
		'clipboard',
		'heart',
		'megaphone',
		'schedule',
		'wordpress',
		'wordpress-alt',
		'pressthis',
		'update',
		'screenoptions',
		'info',
		'cart',
		'feedback',
		'cloud',
		'translation',
		'tag',
		'category',
		'archive',
		'tagcloud',
		'text',
		'media-archive',
		'media-audio',
		'media-code',
		'media-default',
		'media-document',
		'media-interactive',
		'media-spreadsheet',
		'media-text',
		'media-video',
		'playlist-audio',
		'playlist-video',
		'yes',
		'no',
		'no-alt',
		'plus',
		'plus-alt',
		'minus',
		'dismiss',
		'marker',
		'star-filled',
		'star-half',
		'star-empty',
		'flag',
		'share',
		'share1',
		'share-alt',
		'share-alt2',
		'twitter',
		'rss',
		'email',
		'email-alt',
		'facebook',
		'facebook-alt',
		'networking',
		'googleplus',
		'location',
		'location-alt',
		'camera',
		'images-alt',
		'images-alt2',
		'video-alt',
		'video-alt2',
		'video-alt3',
		'vault',
		'shield',
		'shield-alt',
		'sos',
		'search',
		'slides',
		'analytics',
		'chart-pie',
		'chart-bar',
		'chart-line',
		'chart-area',
		'groups',
		'businessman',
		'id',
		'id-alt',
		'products',
		'awards',
		'forms',
		'testimonial',
		'portfolio',
		'book',
		'book-alt',
		'download',
		'upload',
		'backup',
		'clock',
		'lightbulb',
		'microphone',
		'desktop',
		'tablet',
		'smartphone',
		'smiley'
	);

	$output = array();
	foreach ( $icons as $icon ) {
		$output["dashicons-$icon"] = array(
			'label' => usl_translate_id_to_name( str_replace( 'admin-', '', $icon ) ),
			'icon'  => "dashicons dashicons-$icon",
		);
	}

	return $output;
}

function usl_sc_parse_border_radius( $atts ) {

	// Prepare border radius'
	$_border_radius = array(
		'border-top-left-radius'     => $atts['border_top_left_radius'],
		'border-top-right-radius'    => $atts['border_top_right_radius'],
		'border-bottom-left-radius'  => $atts['border_bottom_left_radius'],
		'border-bottom-right-radius' => $atts['border_bottom_right_radius'],
	);

	// intval() each radius so we can later add them
	$_border_radius_sum = $_border_radius;
	array_walk( $_border_radius_sum, function( &$value ) {
		$value = intval( $value );
	});

	// Use border radius if at least one is not 0
	$border_radius = '';
	if ( array_sum( $_border_radius_sum ) !== 0 ) {
		foreach ( $_border_radius as $property => $value ) {
			$border_radius .= "$property: $value;";
		}
	} else {
		$border_radius = '';
	}

	return $border_radius;
}