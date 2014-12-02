<?php

/**
 * Contains all USL packaged shortcodes within the Design category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// Button
	array(
		'code'        => 'usl_button',
		'function'    => '_usl_sc_button',
		'title'       => __( 'Button', 'USL' ),
		'description' => __( 'Creates a sweet button', 'USL' ),
		'atts'        => array(
			'color'       => array(
				'label'       => __( 'Color', 'USL' ),
				'colorpicker' => array(),
				'default'     => '#bada55',
			),
			'color_hover' => array(
				'label'       => __( 'Color Hover', 'USL' ),
				'colorpicker' => array(),
				'default'     => '#84A347',
			),
			'font_color'  => array(
				'label'       => __( 'Font Color', 'USL' ),
				'colorpicker' => array(),
				'default'     => '#222',
			),
			'size'        => array(
				'label'     => __( 'Size', 'USL' ),
				'selectbox' => array(
					'options' => array(
						'large'  => __( 'Large', 'USL' ),
						'medium' => __( 'Medium', 'USL' ),
						'small'  => __( 'Small', 'USL' ),
					),
				),
			),
			'shape'       => array(
				'label'     => __( 'Shape', 'USL' ),
				'selectbox' => array(
					'options' => array(
						'square'  => __( 'Square', 'USL' ),
						'rounded' => __( 'Rounded', 'USL' ),
						'circle'  => __( 'Circle', 'USL' ),
					),
				),
			),
			'link'        => array(
				'label'    => __( 'Link', 'USL' ),
				'validate' => array(
					'url:',
				),
				'sanitize' => array(
					'url:',
				),
			),
			'icon'        => array(
				'label'     => __( 'Icon', 'USL' ),
				'selectbox' => array(
					'callback'    => '_usl_sc_icon_list',
					'allowIcons' => true,
					'placeholder' => __( 'Select an icon (no icon by default)', 'USL' ),
				)
			),
		),
		'wrapping'    => true,
		'render'      => array(
			'noStyle'      => true,
			'dummyContent' => __( 'Button', 'USL' ),
		),
	),
	// Box
	array(
		'code'        => 'usl_box',
		'function'    => '_usl_sc_box',
		'title'       => __( 'Box', 'USL' ),
		'description' => __( 'Creates a nice box for your content.', 'USL' ),
		'atts'        => array(
			'color'      => array(
				'label'       => __( 'Color', 'USL' ),
				'colorpicker' => '',
			),
			'font_color' => array(
				'label'       => __( 'Font Color', 'USL' ),
				'colorpicker' => '',
			),
			'shape'      => array(
				'label'     => __( 'Shape', 'USL' ),
				'selectbox' => array(
					'options' => array(
						'square'  => __( 'Square', 'USL' ),
						'rounded' => __( 'Rounded', 'USL' ),
						'circle'  => __( 'Circle', 'USL' ),
					),
				),
			),
			'heading'    => array(
				'label' => __( 'Heading', 'USL' ),
			),
		),
		'wrapping'    => true,
		'render'      => array(
			'noStyle'      => true,
		),
	),
	// Column 2
	array(
		'code'        => 'usl_column_two',
		'function'    => '_usl_sc_column_two',
		'title'       => __( 'Column 2', 'USL' ),
		'description' => __( 'Creates a nice column that is half the width of the container.', 'USL' ),
		'wrapping'    => true,
		'render'      => array(
			'noStyle'      => true,
			'allowNesting' => true,
		),
	),
	// Column 3
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
	$shortcode['source'] = 'Ultimate Shortcodes Library';
	usl_add_shortcode( $shortcode );
}

/**
 * Wraps the content within a styled button.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The button HTML.
 */
function _usl_sc_button( $atts = array(), $content = null ) {

	// TODO Test

	$atts = shortcode_atts( array(
		'link'        => '#',
		'size'        => 'medium',
		'color'       => '#bada55',
		'color_hover' => '#84A347',
		'font_color'  => '#fff',
		'shape'       => '',
		'icon'        => '',
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	$class = 'usl-button';
	$class .= ! empty( $atts['size'] ) ? "-$atts[size]" : '';
	$class .= ! empty( $atts['shape'] ) ? "-$atts[shape]" : '';
	$class .= ! empty( $atts['icon'] ) ? "-icon" : '';

	$output = "<a href='$atts[link]' class='$class'";
	$output .= " style='background: $atts[color]; color: $atts[font_color]'";
	$output .= '>';
	$output .= "<span class='hover' style='background: $atts[color_hover]'></span>";
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
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The box HTML.
 */
function _usl_sc_box( $atts, $content = null ) {

	// TODO Test
	$atts = shortcode_atts( array(
		'color'      => '#bada55',
		'shape'      => 'rounded',
		'font_color' => '#222',
		'heading'    => ''
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	$class = 'usl-box';
	$class .= ! empty( $atts['shape'] ) ? "-$atts[shape]" : '';

	$output = "<div class='$class'";
	$output .= " style='background: $atts[color]; color: $atts[font_color]'";
	$output .= '>';
	$output .= ! empty( $atts['heading'] ) ? "<h3>$atts[heading]</h3>" : '';
	$output .= do_shortcode( $content );
	$output .= '</div>';

	return $output;
}


/**
 * Wraps the content within a half-width column.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_two( $atts, $content = null ) {

	// TODO Test

	return '<div class="usl-column-2">' . do_shortcode( $content ) . '</div>';
}


/**
 * Wraps the content within a third-width column.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_three( $atts, $content = null ) {

	// TODO Test

	return '<div class="usl-column-3">' . do_shortcode( $content ) . '</div>';
}


/**
 * Wraps the content within a quarter-width column.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_four( $atts, $content = null ) {

	// TODO Test

	return '<div class="usl-column-4">' . do_shortcode( $content ) . '</div>';
}


/**
 * Wraps the content within a fifth-width column.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return string The content in a column HTML.
 */
function _usl_sc_column_five( $atts, $content = null ) {

	// TODO Test

	return '<div class="usl-column-5">' . do_shortcode( $content ) . '</div>';
}

/**
 * Helper function for populating the icon list selectbox.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @return bool|array List of icons.
 */
function _usl_sc_icon_list() {

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
		// FIXME Icon not output correct (look on button)
		$output["dashicons-$icon"] = array(
			'label' => usl_translate_id_to_name( str_replace( 'admin-', '', $icon ) ),
			'icon'  => "dashicons dashicons-$icon",
		);
	}

	return $output;
}