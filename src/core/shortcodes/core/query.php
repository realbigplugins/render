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
foreach (
	array(
		/*
		 * Query
		 *
		 * Queries information from the database to output a post list.
		 *
		 * @since 1.0.0
		 *
		 * @att {selectbox} author      The author to grab posts from.
		 * @att {selectbox} cateegory   The category to grab posts from.
		 * @att {selectbox} tag         The tag to grab posts from.
		 * @att {selectbox} post_type   The post type to grab posts from.
		 * @att {selectbox} include     Specific posts to include.
		 * @att {selectbox} exclude     Specific posts to exclude.
		 * @att {counter}   numberposts How many posts to grab.
		 * @att {toggle}    order       Which direction to order.
		 * @att {selectbox} orderby     What to order the posts by.
		 * @att {selectbox} post_status What post status to filter grabbed posts through.
		 * @att {counter}   offset      How many posts into the results to start the list at.
		 * @att {text}      s           Search parameter for filtering posts.
		 * @att {text}      meta_key    The meta key for which to filter posts for.
		 * @att {text}      meta_value  The value of the meta key to filter posts through.
		 */
		array(
			'code'        => 'render_query',
			'function'    => '_render_query',
			'title'       => __( 'Query', 'Render' ),
			'description' => __( 'Outputs a list of posts.', 'Render' ),
			'tags'        => 'data loop',
			'atts'        => array(
				array(
					'type'  => 'section_break',
					'label' => __( 'Refine Search', 'Render' ),
				),
				'author'           => array(
					'label'      => __( 'Author', 'Render' ),
					'type'       => 'selectbox',
					'properties' => array(
						'placeholder' => __( 'Any author', 'Render' ),
						'options'     => render_sc_user_list( 'edit_posts' ),
					),
				),
				'category'         => array(
					'label'      => __( 'Category', 'Render' ),
					'type'       => 'selectbox',
					'properties' => array(
						'no_options'  => __( 'No categories available.', 'Render' ),
						'placeholder' => __( 'Any category', 'Render' ),
						'callback'    => array(
							'function' => 'render_categories_dropdown'
						),
					),
				),
				'tag'              => array(
					'label'      => __( 'Tag', 'Render' ),
					'type'       => 'selectbox',
					'properties' => array(
						'no_options'  => __( 'No tags available.', 'Render' ),
						'placeholder' => __( '-- None --', 'Render' ),
						'callback'    => array(
							'function' => 'render_tags_dropdown'
						),
					),
				),
				'post_type'        => render_sc_attr_template( 'post_type_list', array() ),
				'include'          => render_sc_attr_template( 'post_list', array(
					'label'       => __( 'Include', 'Render' ),
					'properties'  => array(
						'multi' => true,
						'placeholder' => 'Select an option',
					),
					'conditional' => array(
						'populate' => array(
							'atts'     => array(
								'post_type',
							),
							'callback' => 'render_sc_populate_post_type',
						),
					),
				) ),
				'exclude'          => render_sc_attr_template( 'post_list', array(
					'label'       => __( 'Exclude', 'Render' ),
					'properties'  => array(
						'multi' => true,
						'placeholder' => 'Select an option',
					),
					'conditional' => array(
						'populate' => array(
							'atts'     => array(
								'post_type',
							),
							'callback' => 'render_sc_populate_post_type',
						),
					),
				) ),
				'numberposts'      => array(
					'label'       => __( 'Count', 'Render' ),
					'description' => __( 'Max number of posts to show.', 'Render' ),
					'type'        => 'counter',
					'default'     => 5,
					'properties'  => array(
						'max' => 50,
					),
				),
				array(
					'type'  => 'section_break',
					'label' => __( 'Order', 'Render' ),
				),
				'order'            => render_sc_attr_template( 'post_order' ),
				'orderby'          => render_sc_attr_template( 'post_orderby' ),
				'post_status'      => array(
					'label'      => __( 'Post Status', 'Render' ),
					'type'       => 'selectbox',
					'properties' => array(
						'placeholder' => __( 'Any status', 'Render' ),
						'callback'    => array(
							'function' => 'get_post_stati'
						),
					),
					'advanced'   => true,
				),
				'offset'           => array(
					'label'       => __( 'Offset', 'Render' ),
					'description' => __( 'Start at this many posts in from found posts.', 'Render' ),
					'type'        => 'counter',
					'default'     => 0,
					'properties'  => array(
						'max' => 60,
					),
					'advanced'    => true,
				),
				's'                => array(
					'label'    => __( 'Search', 'Render' ),
					'advanced' => true,
				),
				'meta_key'         => array(
					'label'       => __( 'Meta Key', 'Render' ),
					'description' => __( 'The name of the meta key to use the value to search for.', 'Render' ),
					'advanced'    => true,
				),
				'meta_value'       => array(
					'label'       => __( 'Meta Key', 'Render' ),
					'description' => __( 'Only shows posts containing a meta key with this value.', 'Render' ),
					'advanced'    => true,
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'meta_key' => array(
									'type' => 'NOT EMPTY',
								),
							),
						),
					),
				),
				'suppress_filters' => array(
					'label'       => __( 'Suppress Filters', 'Render' ),
					'description' => __( 'Suppresses any filters applied to get_posts.', 'Render' ),
					'type'        => 'checkbox',
					'advanced'    => true,
				),
			),
			'render'      => true,
			'wrapping'    => false
		)
	) as $shortcode
) {

	$shortcode['category'] = 'query';
	$shortcode['source']   = 'Render';

	render_add_shortcode( $shortcode );
	render_add_shortcode_category( array(
		'id'    => 'query',
		'label' => __( 'Query', 'Render' ),
		'icon'  => 'dashicons-download',
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
		'post_type'        => 'any',
		'author'           => '',
		'cat'              => '',
		'tag'              => '',
		's'                => '',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_status'      => '',
		'order'            => '',
		'orderby'          => '',
		'suppress_filters' => '1',
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	// Convert to boolean
	$atts['suppress_filters'] = $atts['suppress_filters'] == '1';

	$output = '';
	$posts  = get_posts( $atts );

	if ( ! empty( $posts ) ) {
		$output .= '<ul>';
		foreach ( $posts as $post ) {
			$output .= '<li><a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a></li>';
		}
		$output .= '</ul>';
	} else {
		$output = __( 'No posts found.', 'Render' );
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
 * @param array $args Optional arguments to use
 * @return array List of post types.
 */
function render_sc_post_type_list( $args = array() ) {

	$exclude_media = isset( $args['exclude_media'] ) ? $args['exclude_media'] : true;
	unset( $args['exclude_media'] );

	$post_types = get_post_types( wp_parse_args( $args, array(
		'public' => true,
	) ), 'objects' );

	$output = array();
	foreach ( $post_types as $post_type ) {

		// Skip media if set
		if ( $exclude_media && $post_type->name == 'attachment' ) {
			continue;
		}

		$output[ $post_type->name ] = $post_type->label;
	}

	return $output;
}

/**
 * Returns a dynamic list of posts based on the supplied post_type.
 *
 * @since {{VERSION}}
 *
 * @param array|null $atts The attributes this output depends on.
 * @return array The new options list
 */
function render_sc_populate_post_type( $atts ) {

	$options = render_sc_post_list( array(
		'post_type' => isset( $atts['post_type'] ) ? $atts['post_type'] : 'any',
		'public'    => true,
	) );

	return array(
		'options'         => render_build_options_html( $options ),
		'no_options_text' => __( 'No posts available.', 'Render' ),
	);
}