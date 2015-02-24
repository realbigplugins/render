<?php
/**
 * Contains all Render packaged shortcodes within the Query category.
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
foreach ( array(
	// Query
	array(
		'code'        => 'render_query',
		'function'    => '_render_query',
		'title'       => __( 'Query', 'Render' ),
		'description' => __( 'Outputs a list of posts.', 'Render' ),
		'tags'        => 'data loop',
		'atts'        => array(
			array(
				'type' => 'section_break',
				'label' => __('Refine Search', 'Render' ),
			),
			'author'      => array(
				'label'      => __( 'Author', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Any author', 'Render' ),
					'options'     => render_sc_user_list( 'edit_posts' ),
				),
			),
			'post_type'   => array(
				'label'       => __( 'Post type', 'Render' ),
				'type'        => 'selectbox',
				'description' => __( 'Available post types.', 'Render' ),
				'properties'  => array(
					'placeholder' => __( 'Any post type', 'Render' ),
					'callback'    => array(
						'function' => 'render_post_types_dropdown',
					),
				),
			),
			'cat'         => array(
				'label'       => __( 'Category', 'Render' ),
				'type'        => 'selectbox',
				'description' => __( 'Available categories.', 'Render' ),
				'properties'  => array(
					'no_options' => __( 'No categories available.', 'Render' ),
					'placeholder' => __( 'Any category', 'Render' ),
					'callback'    => array(
						'function' => 'render_categories_dropdown'
					),
				),
			),
			'tag'         => array(
				'label'       => __( 'Tag', 'Render' ),
				'type'        => 'selectbox',
				'description' => __( 'Available tags.', 'Render' ),
				'properties'  => array(
					'no_options' => __( 'No tags available.', 'Render' ),
					'placeholder' => __( '-- None --', 'Render' ),
					'callback'    => array(
						'function' => 'render_tags_dropdown'
					),
				),
			),
			array(
				'type' => 'section_break',
				'label' => __( 'Order', 'Render' ),
			),
			'order'       => array(
				'label'      => __( 'Order', 'Render' ),
				'type'       => 'toggle',
				'properties' => array(
					'values' => array(
						'DSC' => __( 'Descending', 'Render' ),
						'ASC'  => __( 'Ascending', 'Render' ),
					),
				),
			),
			'orderby'     => array(
				'label'      => __( 'Order by', 'Render' ),
				'type'       => 'selectbox',
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
			),
			'post_status' => array(
				'label'      => __( 'Post Status', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Any status', 'Render' ),
					'callback'    => array(
						'function' => 'get_post_stati'
					),
				),
				'advanced' => true,
			),
			's'           => array(
				'label' => __( 'Search', 'Render' ),
				'advanced' => true,
			),
		),
		'render'      => true,
		'wrapping'    => false
	)
) as $shortcode ) {

	$shortcode['category'] = 'query';
	$shortcode['source']   = 'Render';

	render_add_shortcode( $shortcode );
	render_add_shortcode_category( array(
		'id' => 'query',
		'label' => __( 'Query', 'Render'),
		'icon' => 'dashicons-download',
	) );
}

/**
 * Runs a WP_Query to provide a drop-down of posts.
 *
 * @since  0.3.0
 * @access private
 *
 * @param array $atts The attributes sent to the shortcode.
 *
 * @return string The drop-down HTML.
 */
function _render_query( $atts = array() ) {

	$atts = shortcode_atts( array(
		'post_type'   => 'any',
		'author'      => '',
		'cat'         => '',
		'tag'         => '',
		's'           => '',
		'post_status' => '',
		'order'       => '',
		'orderby'     => ''
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	$output = '';
	$posts  = get_posts( $atts );

	if ( ! empty( $posts ) ) {
		$output .= '<ul>';
		foreach ( $posts as $post ) {
			$output .= '<li><a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post-> ID ) . '</a></li>';
		}
		$output .= '</ul>';
	} else {
		$output = 'No posts found';
	}

	return $output;
}

/**
 * Helper function for populating the category selectbox.
 *
 * @since  1.0.0
 *
 * @return bool|array List of category terms.
 */
function render_categories_dropdown() {

	$cats = get_categories();

	$output = array();
	foreach ( $cats as $cat ) {
		$output[ $cat->term_id ] = $cat->name;
	}

	return $output;
}

/**
 * Helper function for populating the tags selectbox.
 *
 * @since 1.0.0
 *
 * @return bool|array List of tags.
 */
function render_tags_dropdown() {

	$tags = get_tags();

	$output = array();
	if ( $tags ) {
		foreach ( $tags as $tag ) {
			$output[ $tag->term_id ] = $tag->name;
		}
	}

	return $output;
}

/**
 * Provides selectbox options with all the public post types.
 *
 * @since 1.0.0
 *
 * @return array List of post types.
 */
function render_post_types_dropdown() {

	$post_types = get_post_types( array(
		'public' => true,
	), 'objects' );


	$output = array();
	foreach ( $post_types as $post_type ) {
		$output[ $post_type->name ] = $post_type->label;
	}

	return $output;
}