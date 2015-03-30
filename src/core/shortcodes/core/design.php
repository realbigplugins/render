<?php
/**
 * Contains all Render packaged shortcodes within the Design category.
 *
 * @since      1.0.0
 *
 * @package    Render
 * @subpackage Shortcodes
 */

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Loops through each shortcode and adds it to Render
foreach (
	array(
		/*
		 * Accordion
		 *
		 * The parent wrapper for an accordion.
		 *
		 * @since {{VERSION}}
		 * @nestedChild render_accordion_section
		 *
		 * @att {checkbox}    start_closed             Whether the first section should start open or not.
		 * @att {colorpicker} heading_background       The color of section headings.
		 * @att {colorpicker} heading_background_hover The color of section headings when hovered.
		 * @att {colorpicker} heading_font_color       The color of section headings' font.
		 * @nestedatt {textbox} heading The text for each section heading.
		 */
		array(
			'code'        => 'render_accordion_wrapper',
			'function'    => '_render_sc_accordion_wrapper',
			'title'       => __( 'Accordion', 'Render' ),
			'description' => __( 'An accordion style drop-down for hiding and revealing content.', 'Render' ),
			'atts'        => array(
				'start_closed'             => array(
					'label'      => __( 'Load Closed', 'Render' ),
					'type'       => 'checkbox',
					'properties' => array(
						'value' => 'true',
						'label' => __( 'Load the accordion with all sections collapsed', 'Render' ),
					),
				),
				array(
					'label' => __( 'Colors', 'Render' ),
					'type'  => 'section_break',
				),
				'heading_background'       => array(
					'label'   => __( 'Heading', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_COLOR,
				),
				'heading_background_hover' => array(
					'label'   => __( 'Heading Hover', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_COLOR_DARK,
				),
				'heading_font_color'       => array(
					'label'   => __( 'Heading Font', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_FONT_COLOR,
				),
				array(
					'label' => __( 'Accordion Sections', 'Render' ),
					'type'  => 'section_break',
				),
				'nested_children'          => array(
					'label'      => false,
					'type'       => 'repeater',
					'properties' => array(
						'fields' => array(
							array(
								'label' => __( 'Accordion Section', 'Render' ),
								'type'  => 'placeholder',
							),
							'heading' => array(
								'label'    => __( 'Heading', 'Render' ),
								'required' => true,
							),
						),
					),
				),
			),
			'wrapping'    => true,
			'render'      => array(
				'nested'       => array(
					'child'      => 'render_accordion_section',
					'globalAtts' => array(
						'heading_background',
						'heading_background_hover',
						'heading_font_color',
					),
				),
				'displayBlock' => true,
			),
		),
		/*
		 * Accordion Section
		 *
		 * The child for adding sections to an accordion wrapper.
		 *
		 * @since {{VERSION}}
		 * @nestedParent render_accordion_wrapper
		 */
		array(
			'code'      => 'render_accordion_section',
			'function'  => '_render_sc_accordion_section',
			'title'     => __( 'Accordion', 'Render' ),
			'noDisplay' => true,
			'wrapping'  => true,
			'render'    => array(
				'nested'       => array(
					'parent' => 'render_accordion_wrapper',
				),
				'dummyContent' => 'Enter accordion section content here',
				'noStyle'      => true,
				'displayBlock' => true,
			),
		),
		/*
		 * Button
		 *
		 * @since 1.0.0
		 *
		 * @att {colorpicker} color                      The background color of the button.
		 * @att {colorpicker} color_hover                The background color when hovered.
		 * @att {colorpicker} font_color                 The font color of the button.
		 * @att {selectbox}   size                       The size of the button.
		 * @att {selectbox}   shape                      The shape of the button.
		 * @att {selectbox}   icon                       The icon to show before the button.
		 * @att {text}        link                       Where the button links to.
		 * @att {checkbox}    link_new_window            Whether or not the link opens a new tab / window.
		 * @att {counter}     border_top_left_radius     The button top left border radius.
		 * @att {counter}     border_top_right_radius    The button top right border radius.
		 * @att {counter}     border_bottom_left_radius  The button bottom left border radius.
		 * @att {counter}     border_bottom_right_radius The button bottom right border radius.
		 */
		array(
			'code'        => 'render_button',
			'function'    => '_render_sc_button',
			'title'       => __( 'Button', 'Render' ),
			'description' => __( 'Creates a sweet button.', 'Render' ),
			'atts'        => array(
				array(
					'type'  => 'section_break',
					'label' => __( 'Colors', 'Render' ),
				),
				'color'                      => array(
					'label'   => __( 'Background', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_COLOR,
				),
				'color_hover'                => array(
					'label'   => __( 'Background (Hover)', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_COLOR_DARK,
				),
				'font_color'                 => array(
					'label'   => __( 'Font', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_FONT_COLOR,
				),
				array(
					'type'  => 'section_break',
					'label' => __( 'Visual', 'Render' ),
				),
				'size'                       => array(
					'label'      => __( 'Size', 'Render' ),
					'type'       => 'selectbox',
					'properties' => array(
						'placeholder' => __( 'Medium', 'Render' ),
						'options'     => array(
							'large'  => __( 'Large', 'Render' ),
							'medium' => __( 'Medium', 'Render' ),
							'small'  => __( 'Small', 'Render' ),
						),
					),
				),
				'shape'                      => array(
					'label'      => __( 'Shape', 'Render' ),
					'type'       => 'selectbox',
					'properties' => array(
						'placeholder' => __( 'Rectangle', 'Render' ),
						'options'     => array(
							'rectangle' => __( 'Rectangle', 'Render' ),
							'rounded'   => __( 'Rounded', 'Render' ),
							'ellipse'   => __( 'Ellipse', 'Render' ),
						),
					),
				),
				'icon'                       => array(
					'label'      => __( 'Icon', 'Render' ),
					'type'       => 'selectbox',
					'properties' => array(
						'callback'    => array(
							'function' => 'render_sc_icon_list',
						),
						'allowIcons'  => true,
						'placeholder' => __( 'Select an icon (no icon by default)', 'Render' ),
					)
				),
				array(
					'type'  => 'section_break',
					'label' => __( 'Link', 'Render' ),
				),
				'link_type'                  => array(
					'label'      => __( 'Link Type', 'Render' ),
					'type'       => 'selectbox',
					'properties' => array(
						'placeholder' => __( 'None', 'Render' ),
						'options'     => array(
							'link'  => __( 'Link', 'Render' ),
							'email' => __( 'Email', 'Render' ),
							'phone' => __( 'Phone', 'Render' ),
						),
					),
				),
				'link_url'                   => render_sc_attr_template( 'link', array(
					'required'    => true,
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'link_type' => array(
									'type'  => '==',
									'value' => 'link',
								),
							),
						),
					),
				) ),
				'link_email'                 => render_sc_attr_template( 'email', array(
					'required'    => true,
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'link_type' => array(
									'type'  => '==',
									'value' => 'email',
								),
							),
						),
					),
				) ),
				'link_phone'                 => render_sc_attr_template( 'phone', array(
					'required'    => true,
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'link_type' => array(
									'type'  => '==',
									'value' => 'phone',
								),
							),
						),
					),
				) ),
				'link_new_window'            => array(
					'label'       => __( 'Link Window', 'Render' ),
					'type'        => 'checkbox',
					'properties'  => array(
						'label' => __( 'Open link in new tab', 'Render' ),
					),
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'link_url' => array(
									'type' => 'NOT EMPTY'
								),
							),
						),
					),
				),
				array(
					'type'        => 'section_break',
					'description' => __( 'If all border-radius\' are set to 0, none will be used. If at least one is set, all will be used.', 'Render' ),
					'advanced'    => true,
				),
				'border_top_left_radius'     => render_sc_attr_template( 'border-radius', array(), array(
						'orientation' => __( 'Top Left' )
					)
				),
				'border_top_right_radius'    => render_sc_attr_template( 'border-radius', array(), array(
						'orientation' => __( 'Top Right' )
					)
				),
				'border_bottom_left_radius'  => render_sc_attr_template( 'border-radius', array(), array(
						'orientation' => __( 'Bottom Left' )
					)
				),
				'border_bottom_right_radius' => render_sc_attr_template( 'border-radius', array(), array(
						'orientation' => __( 'Bottom Right' )
					)
				),
			),
			'wrapping'    => true,
			'render'      => array(
				'noStyle'       => true,
				'displayInline' => true,
			),
		),
		/*
		 * Box
		 *
		 * Wraps the selected content in a styled box (with optional heading).
		 *
		 * @since 1.0.0
		 *
		 * @att {textbox}      heading                    The optional heading to go above the content.
		 * @att {colorpicker}  color                      The background color of the box.
		 * @att {colorpicker}  font_color                 The color of the content font.
		 * @att {colorpickeer} heading_font_color         The color of the heading font.
		 * @att {selectbox}    shape                      The shape of the box.
		 * @att {selectbox}    heading_tag                The HTML tag to use for the heading.
		 * @att {counter}      border_top_left_radius     The button top left border radius.
		 * @att {counter}      border_top_right_radius    The button top right border radius.
		 * @att {counter}      border_bottom_left_radius  The button bottom left border radius.
		 * @att {counter}      border_bottom_right_radius The button bottom right border radius.
		 */
		array(
			'code'        => 'render_box',
			'function'    => '_render_sc_box',
			'title'       => __( 'Box', 'Render' ),
			'description' => __( 'Creates a nice box for your content.', 'Render' ),
			'atts'        => array(
				array(
					'type'  => 'section_break',
					'label' => __( 'Content', 'Render' ),
				),
				'heading'                    => array(
					'label' => __( 'Heading', 'Render' ),
				),
				array(
					'type'  => 'section_break',
					'label' => __( 'Colors', 'Render' ),
				),
				'color'                      => array(
					'label'   => __( 'Box Background', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_COLOR,
				),
				'font_color'                 => array(
					'label'   => __( 'Body Font', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_FONT_COLOR,
				),
				'heading_font_color'         => array(
					'label'       => __( 'Heading Font', 'Render' ),
					'type'        => 'colorpicker',
					'default'     => RENDER_PRIMARY_FONT_COLOR,
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'heading' => array(
									'type' => 'NOT EMPTY'
								),
							),
						),
					),
				),
				array(
					'type'  => 'section_break',
					'label' => __( 'Visual', 'Render' ),
				),
				'shape'                      => array(
					'label'      => __( 'Shape', 'Render' ),
					'type'       => 'selectbox',
					'properties' => array(
						'placeholder' => __( 'Rectangle', 'Render' ),
						'options'     => array(
							'rectangle' => __( 'Rectangle', 'Render' ),
							'rounded'   => __( 'Rounded', 'Render' ),
							'ellipse'   => __( 'Ellipse', 'Render' ),
						),
					),
				),
				'heading_tag'                => array(
					'label'       => __( 'Heading Tag', 'Render' ),
					'type'        => 'selectbox',
					'advanced'    => true,
					'properties'  => array(
						'allowCustomInput' => true,
						'default'          => 'h3',
						'options'          => array(
							'h1'   => 'Header 1',
							'h2'   => 'Header 2',
							'h3'   => 'Header 3',
							'h4'   => 'Header 4',
							'h5'   => 'Header 5',
							'h6'   => 'Header 6',
							'p'    => 'Paragraph',
							'span' => 'Span',
							'div'  => 'DIV',
						),
					),
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'heading' => array(
									'type' => 'NOT EMPTY',
								),
							),
						),
					),
				),
				array(
					'type'        => 'section_break',
					'description' => __( 'If all border-radius\' are set to 0, none will be used. If at least one is set, all will be used. Note that this will override the "Shape" defined above.', 'Render' ),
					'advanced'    => true,
				),
				'border_top_left_radius'     => render_sc_attr_template( 'border-radius', array(), array(
						'orientation' => __( 'Top Left' )
					)
				),
				'border_top_right_radius'    => render_sc_attr_template( 'border-radius', array(), array(
						'orientation' => __( 'Top Right' )
					)
				),
				'border_bottom_left_radius'  => render_sc_attr_template( 'border-radius', array(), array(
						'orientation' => __( 'Bottom Left' )
					)
				),
				'border_bottom_right_radius' => render_sc_attr_template( 'border-radius', array(), array(
						'orientation' => __( 'Bottom Right' )
					)
				),
			),
			'wrapping'    => true,
			'render'      => array(
				'noStyle' => true,
			),
		),
		/*
		 * Columns Wrapper
		 *
		 * Create a grid of sections divided into columns.
		 *
		 * @since {{VERSION}}
		 * @nestedChild render_column_section
		 */
		array(
			'code'        => 'render_columns_wrapper',
			'function'    => '_render_sc_columns_wrapper',
			'title'       => __( 'Columns', 'Render' ),
			'description' => __( 'Creates symmetrical columns for grouping content.', 'Render' ),
			'atts'        => array(
				'nested_children' => array(
					'label'       => __( 'Columns', 'Render' ),
					'description' => __( 'Maximum six columns', 'Render' ),
					'type'        => 'repeater',
					'properties'  => array(
						'max'    => 6,
						'fields' => array(
							array(
								'label' => __( 'Column', 'Render' ),
								'type'  => 'placeholder',
							),
						),
					),
				),
			),
			'wrapping'    => true,
			'render'      => array(
				'noStyle'      => true,
				'nested'       => array(
					'child' => 'render_column_section',
				),
				'displayBlock' => true,
			),
		),
		/*
		 * Column Section
		 *
		 * An individual column section.
		 *
		 * @since {{VERSION}}
		 * @nestedParent render_columns_wrapper
		 */
		array(
			'code'      => 'render_column_section',
			'function'  => '_render_sc_column_section',
			'title'     => __( 'Column', 'Render' ),
			'noDisplay' => true,
			'wrapping'  => true,
			'render'    => array(
				'noStyle'      => true,
				'displayBlock' => true,
				'nested'       => array(
					'parent' => 'render_columns_wrapper',
				),
				'dummyContent' => 'Enter column content here',
			),
		),
		/*
		 * Tabs Wrapper
		 *
		 * Creates a tabbed layout for the content.
		 *
		 * @since {{VERSION}}
		 * @nestedChild render_tab_section
		 *
		 * @att {toggle}      content_border             Whether or not the tab content should have border.
		 * @att {colorpicker} border_color               The color for all borders.
		 * @att {colorpicker} navigation_tab_color       The color for the navigation tabs' background.
		 * @att {colorpicker} navigation_tab_hover_color The color for the navigation tabs' background when hovered.
		 * @att {colorpicker} navigation_tab_font_color  The color for the navigation tabs' font.
		 * @nestedatt {textbox} navigation_label The text for the navigation tabs.
		 */
		array(
			'code'        => 'render_tabs_wrapper',
			'function'    => '_render_sc_tabs_wrapper',
			'title'       => __( 'Tabs', 'Render' ),
			'description' => __( 'Creates a tabbed layout for the content.', 'Render' ),
			'atts'        => array(
				'content_border'             => array(
					'label'      => __( 'Tab Content Border', 'Render' ),
					'type'       => 'toggle',
					'properties' => array(
						'deselectStyle' => true,
						'flip'          => true,
						'values'        => array(
							'hide' => __( 'Hide', 'Render' ),
							'show' => __( 'Show', 'Render' ),
						),
					),
				),
				array(
					'label' => __( 'Colors', 'Render' ),
					'type'  => 'section_break',
				),
				'border_color'               => array(
					'label'   => __( 'Borders', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_COLOR_DARK,
				),
				'navigation_tab_color'       => array(
					'label'   => __( 'Navigation Tab', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_COLOR,
				),
				'navigation_tab_hover_color' => array(
					'label'   => __( 'Navigation Tab Hover', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_COLOR_LIGHT,
				),
				'navigation_tab_font_color'  => array(
					'label'   => __( 'Navigation Tab Font', 'Render' ),
					'type'    => 'colorpicker',
					'default' => RENDER_PRIMARY_FONT_COLOR,
				),
				array(
					'label' => __( 'Tab Sections', 'Render' ),
					'type'  => 'section_break',
				),
				'nested_children'            => array(
					'label'      => false,
					'type'       => 'repeater',
					'properties' => array(
						'fields' => array(
							array(
								'label' => __( 'Tab Section', 'Render' ),
								'type'  => 'placeholder',
							),
							'navigation_label' => array(
								'label'    => __( 'Navigation Label', 'Render' ),
								'required' => true,
							),
						),
					),
				),
			),
			'wrapping'    => true,
			'render'      => array(
				'nested'       => array(
					'child'             => 'render_tab_section',
					'ignoreForChildren' => array(
						'navigation_label',
					),
				),
				'displayBlock' => true,
				'noStyle'      => true,
			),
		),
		/*
		 * Tab Section
		 *
		 * An individual tab section.
		 *
		 * @since {{VERSION}}
		 * @nestedParent render_tabs_wrapper
		 */
		array(
			'code'      => 'render_tab_section',
			'function'  => '_render_sc_tab_section',
			'title'     => __( 'Tab', 'Render' ),
			'noDisplay' => true,
			'wrapping'  => true,
			'render'    => array(
				'nested'       => array(
					'parent' => 'render_tabs_wrapper',
				),
				'dummyContent' => 'Enter tab content here',
				'noStyle'      => true,
				'displayBlock' => true,
			),
		),
	) as $shortcode
) {

	$shortcode['category'] = 'design';
	$shortcode['source']   = 'Render';

	render_add_shortcode( $shortcode );
	render_add_shortcode_category( array(
		'id'    => 'design',
		'label' => __( 'Design', 'Render' ),
		'icon'  => 'dashicons-admin-appearance',
	) );
}

/**
 * The main wrapper for an accordion.
 *
 * @since  {{VERSION}}
 * @access private
 *
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 *
 * @return string The accordion wrapper HTML.
 */
function _render_sc_accordion_wrapper( $atts = array(), $content = '' ) {

	$atts = shortcode_atts( array(
		'start_closed' => 'false',
	), $atts );

	// Establish classes
	$classes = array(
		'render-accordion-wrapper',
		$atts['start_closed'] == 'true' ? 'render-accordion-start-closed' : '',
	);
	$classes = array_filter( $classes );

	$output = '<div class="' . implode( ' ', $classes ) . '">';

	$output .= do_shortcode( $content );

	$output .= '</div>'; // .render-accordion-wrapper

	return $output;
}

/**
 * Sections for inside the accordion wrapper.
 *
 * @since  {{VERSION}}
 * @access private
 *
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 *
 * @return string The accordion section HTML.
 */
function _render_sc_accordion_section( $atts = array(), $content = '' ) {

	$atts = shortcode_atts( array(
		'heading'                  => 'Heading',
		'heading_background'       => RENDER_PRIMARY_COLOR,
		'heading_background_hover' => RENDER_PRIMARY_COLOR_DARK,
		'heading_font_color'       => RENDER_PRIMARY_FONT_COLOR,
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	$output = "<div class='render-accordion-section'>";

	$output .= "<h3 class='render-accordion-section-heading' style='color: $atts[heading_font_color]; background: $atts[heading_background];'>";
	$output .= "<span class='render-accordion-hover' style='background: $atts[heading_background_hover];'></span>";
	$output .= "<span class='render-accordion-heading-content'>$atts[heading]</span>";
	$output .= '</h3>';

	$output .= '<div class="render-accordion-section-content">' . wpautop( do_shortcode( $content ) ) . '</div>';

	$output .= '</div>'; // .render-accordion-section

	return do_shortcode( $output );
}

/**
 * Wraps the content within a styled button.
 *
 * @since  1.0.0
 * @access private
 *
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 *
 * @return string The button HTML.
 */
function _render_sc_button( $atts = array(), $content = '' ) {

	$atts = shortcode_atts( array(
		'link_new_window'            => false,
		'link_url'                   => null,
		'link_email'                 => null,
		'link_phone'                 => null,
		'size'                       => 'medium',
		'color'                      => RENDER_PRIMARY_COLOR,
		'color_hover'                => RENDER_PRIMARY_COLOR_DARK,
		'font_color'                 => RENDER_PRIMARY_FONT_COLOR,
		'shape'                      => 'rectangle',
		'icon'                       => '',
		'border_top_left_radius'     => 0,
		'border_top_right_radius'    => 0,
		'border_bottom_left_radius'  => 0,
		'border_bottom_right_radius' => 0,
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	$border_radius = render_sc_parse_border_radius( $atts );

	$link = '';

	// If link is a post link
	$link = $atts['link_url'] !== null ? get_permalink( (int) $atts['link_url'] ) : $link;

	// If link is an email
	$link = $atts['link_email'] !== null ? "mailto:$atts[link_email]" : $link;

	// If link is a phone number
	$link = $atts['link_phone'] !== null ? "tel:$atts[link_phone]" : $link;

	// Sanity check
	$link = ! empty( $link ) ? $link : '#';

	// Get the button content
	$content = do_shortcode( $content );

	$class = 'render-button';
	$class .= ! empty( $atts['size'] ) ? "-$atts[size]" : '';
	$class .= ! empty( $atts['shape'] ) ? "-$atts[shape]" : '';
	$class .= ! empty( $atts['icon'] ) ? "-icon" : '';

	$output = "<a href='$link' class='$class'";
	$output .= " style='background: $atts[color]; color: $atts[font_color]; $border_radius'";
	$output .= $atts['link_new_window'] !== false ? " target='_blank'" : '';
	$output .= '>';
	$output .= "<span class='render-button-hover' style='background: $atts[color_hover]; $border_radius'></span>";
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
 * @since  1.0.0
 * @access private
 *
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 *
 * @return string The box HTML.
 */
function _render_sc_box( $atts = array(), $content = '' ) {

	$atts = shortcode_atts( array(
		'color'                      => RENDER_PRIMARY_COLOR,
		'font_color'                 => RENDER_PRIMARY_FONT_COLOR,
		'heading_font_color'         => RENDER_PRIMARY_FONT_COLOR,
		'shape'                      => 'rectangle',
		'heading'                    => '',
		'heading_tag'                => 'h3',
		'border_top_left_radius'     => 0,
		'border_top_right_radius'    => 0,
		'border_bottom_left_radius'  => 0,
		'border_bottom_right_radius' => 0,
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	$border_radius = render_sc_parse_border_radius( $atts );

	$class = 'render-box';
	$class .= ! empty( $atts['shape'] ) ? "-$atts[shape]" : '';

	$output = "<div class='$class'";
	$output .= " style='background: $atts[color]; color: $atts[font_color]; $border_radius'";
	$output .= '>';
	$output .= ! empty( $atts['heading'] ) ?
		"<$atts[heading_tag] style='color: $atts[heading_font_color]'>$atts[heading]</$atts[heading_tag]>" : '';
	$output .= wpautop( do_shortcode( $content ) );
	$output .= '</div>';

	return $output;
}

/**
 * Wrapper for a column section.
 *
 * @since  {{VERSION}}
 * @access private
 *
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 *
 * @return string The content in a column wrapper HTML.
 */
function _render_sc_columns_wrapper( $atts = array(), $content = '' ) {

	$atts = shortcode_atts( array(
		'nested_children_count' => '1',
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	$output = "<div class='render-columns-wrapper columns-$atts[nested_children_count]'>";

	$output .= do_shortcode( $content );

	$output .= '</div>'; // .render-columns-wrapper

	return $output;
}

/**
 * Sections for inside the columns wrapper.
 *
 * @since  {{VERSION}}
 * @access private
 *
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 *
 * @return string The column section HTML.
 */
function _render_sc_column_section( $atts = array(), $content = '' ) {

	$output = "<div class='render-column-section'>";

	$output .= wpautop( do_shortcode( $content ) );

	$output .= '</div>'; // .render-column-section

	return do_shortcode( $output );
}

/**
 * The TinyMCE callback for the columns wrapper shortcode.
 *
 * This adds a filter that will add the proper columns class onto the TinyMCE shortcode wrapper.
 *
 * @since  {{VERSION}}
 * @access private
 *
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 *
 * @return string The columns wrapper HTML.
 */
function _render_sc_columns_wrapper_tinymce( $atts = array(), $content = '' ) {

	$atts = shortcode_atts( array(
		'nested_children_count' => '1',
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	/**
	 * Adds the proper columns class onto the shortcode wrapper.
	 *
	 * @since {{VERSION}}
	 */
	add_filter( 'render_tinymce_shortcode_wrapper_classes_render_columns_wrapper', function ( $classes ) use ( $atts ) {

		if ( ! in_array( "columns-$atts[nested_children_count]", $classes ) ) {
			$classes[] = "columns-$atts[nested_children_count]";
		}

		return $classes;
	} );

	// Return the normal HTML
	return _render_sc_columns_wrapper( $atts, $content );
}

/**
 * Wraps content in a tabbed layout.
 *
 * @since  {{VERSION}}
 * @access private
 *
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 *
 * @return string The tabs wrapper HTML.
 */
function _render_sc_tabs_wrapper( $atts = array(), $content = '' ) {

	$atts = shortcode_atts( array(
		'nested_children'            => false,
		'navigation_tab_color'       => RENDER_PRIMARY_COLOR,
		'navigation_tab_hover_color' => RENDER_PRIMARY_COLOR_LIGHT,
		'navigation_tab_font_color'  => RENDER_PRIMARY_FONT_COLOR,
		'border_color'               => RENDER_PRIMARY_COLOR_DARK,
		'content_border'             => 'show',
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	// This shouldn't be possible, but just in case it somehow happens
	if ( $atts['nested_children'] === false ) {
		return 'Error in shortcode';
	}

	$tabs = render_associative_atts( $atts, 'nested_children' );

	// Open wrapper
	$output = '<div class="render-tabs-wrapper">';

	// Navigation tabs
	$output .= '<ul class="render-tabs-navigation">';
	$i = 0;
	foreach ( $tabs as $tab ) {
		$i ++;

		// Classes
		$classes   = array();
		$classes[] = 'render-tabs-navigation-tab';
		$classes[] = $i === 1 ? 'render-tabs-navigation-tab-active' : '';

		// Styles
		$styles = array(
			"background: $atts[navigation_tab_color];",
			"color: $atts[navigation_tab_font_color];",
			"border-color: $atts[border_color];",
		);

		$output .= "<li class='" . implode( ' ', array_filter( $classes ) ) . "'";
		$output .= " style='" . implode( ' ', $styles ) . "'>";
		$output .= "<span class='render-tabs-navigation-tab-hover' style='background: $atts[navigation_tab_hover_color];'></span>";
		$output .= "<span class='render-tabs-navigation-tab-content'>$tab[navigation_label]</span>";
		$output .= '</li>';

	}
	$output .= '</ul>';

	// Content
	$styles = array(
		'border-width: ' . ( $atts['content_border'] !== 'hide' ? '1px' : '0' ) . ';',
		"border-color: $atts[border_color];",
	);
	$output .= '<div class="render-tabs-section-wrapper" style="' . implode( ' ', $styles ) . '"">';
	$output .= do_shortcode( $content );
	$output .= '</div>'; // .render-tabs-section-wrapper

	// Close wrapper
	$output .= '</div>'; // .render-tabs-wrapper

	return $output;
}

/**
 * Wraps content in a tab section.
 *
 * @since  {{VERSION}}
 * @access private
 *
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 *
 * @return string The tab section HTML.
 */
function _render_sc_tab_section( $atts = array(), $content = '' ) {

	return '<div class="render-tab-section">' . wpautop( do_shortcode( $content ) ) . '</div>';
}

/**
 * Helper function for populating the icon list selectbox.
 *
 * @since  1.0.0
 * @return bool|array List of icons.
 */
function render_sc_icon_list() {

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
			'label' => render_translate_id_to_name( str_replace( 'admin-', '', $icon ) ),
			'icon'  => "dashicons dashicons-$icon",
		);
	}

	return $output;
}

/**
 * Provides back the CSS formatted border radius based off of attributes.
 *
 * @since 1.0.0
 *
 * @param array $atts The shortcode atts.
 *
 * @return string The parsed border radius'.
 */
function render_sc_parse_border_radius( $atts ) {

	// Prepare border radius'
	$_border_radius = array(
		'border-top-left-radius'     => $atts['border_top_left_radius'],
		'border-top-right-radius'    => $atts['border_top_right_radius'],
		'border-bottom-left-radius'  => $atts['border_bottom_left_radius'],
		'border-bottom-right-radius' => $atts['border_bottom_right_radius'],
	);

	// intval() each radius so we can later add them
	$_border_radius_sum = $_border_radius;
	array_walk( $_border_radius_sum, function ( &$value ) {
		$value = intval( $value );
	} );

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

function _test_callback( $atts ) {

	$options = false;

	if ( $atts['testing'] == 'test1' ) {
		$options = array(
			'min' => 1,
			'max' => 5,
		);
	}

	if ( $atts['testing'] == 'test2' ) {
		$options = array(
			'min' => 6,
			'max' => 10,
		);
	}

	return $options;
}