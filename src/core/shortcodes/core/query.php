<?php
/**
 * Contains all Render packaged shortcodes within the Query category.
 *
 * @since      Render 1.0.0
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
	// Post meta
	// TODO Test and fix up
	array(
		'code'        => 'render_query',
		'function'    => '_render_query',
		'title'       => __( 'Query', 'Render' ),
		'description' => __( 'Queries the database for posts.', 'Render' ),
		'tags'        => 'query database posts data loop',
		'atts'        => array(
			'author'      => array(
				'label'      => __( 'Author', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Author', 'Render' ),
					'options'     => render_user_dropdown( false ),
				),
			),
			'post_type'   => array(
				'label'       => __( 'Post type', 'Render' ),
				'type'        => 'selectbox',
				'description' => __( 'Available post types.', 'Render' ),
				'properties'  => array(
					'placeholder' => __( 'Post Type', 'Render' ),
					'callback'    => array(
						'function' => 'get_post_types',
					),
				),
			),
			'cat'         => array(
				'label'       => __( 'Category', 'Render' ),
				'type'        => 'selectbox',
				'description' => __( 'Available categories.', 'Render' ),
				'properties'  => array(
					'placeholder' => __( '-- None --', 'Render' ),
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
					'placeholder' => __( '-- None --', 'Render' ),
					'callback'    => array(
						'function' => 'render_tags_dropdown'
					),
				),
			),
			's'           => array(
				'label' => __( 'Search', 'Render' ),
			),
			'post_status' => array(
				'label'      => __( 'Post Status', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( '-- N/A --', 'Render' ),
					'callback'    => array(
						'function' => 'get_post_stati'
					),
				),
			),
			'order'       => array(
				'label'      => __( 'Order', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'options' => array(
						'DESC' => __( 'Descending', 'Render' ),
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
		),
		'render'      => true,
		'wrapping'    => false
	)
) as $shortcode ) {

	$shortcode['category'] = 'query';
	$shortcode['source']   = 'Render';

	// Adds shortcode to Render
	add_filter( 'render_add_shortcodes', function( $shortcodes ) use ( $shortcode ) {
		$shortcodes[] = $shortcode;
		return $shortcodes;
	});

	// Add shortcode category
	add_filter( 'render_modal_categories', function( $categories ) {
		$categories['query'] = array(
			'label' => __( 'Query', 'Render' ),
			// TODO Choose icon
			'icon' => 'dashicons-admin-generic',
		);
		return $categories;
	});
}

/**
 * Runs a WP_Query.
 *
 * @since  0.3.0
 * @access Private
 *
 * @param array $atts The attributes sent to the shortcode.
 *
 * @return string
 */
function _render_query( $atts ) {

	$atts = shortcode_atts( array(
		'post_type'   => '',
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

	$args = array(
		'post_type'   => $atts['post_type'],
		'author'      => $atts['author'],
		'cat'         => $atts['cat'],
		'tag'         => $atts['tag'],
		's'           => $atts['s'],
		'post_status' => $atts['post_status'],
		'order'       => $atts['order'],
		'orderby'     => $atts['orderby'],
	);

	$output = '';
	$query  = new WP_Query( $args );

	if ( $query->have_posts() ) {
		$output = '<ul>';
		while ( $query->have_posts() ) {
			$query->the_post();
			$output .= '<a href="' . get_permalink() . '"><li>' . get_the_title() . '</li></a>';
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
 * @since  Render 1.0.0
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
 * @since  Render 1.0.0
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