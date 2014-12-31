<?php
/**
 * Contains all Render packaged shortcodes within the Query category.
 *
 * @since      Render 1.0.0
 *
 * @package    Render
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// Post meta
	array(
		'code'        => 'render_query',
		'function'    => '_render_query',
		'title'       => __( 'Query', 'Render' ),
		'description' => __( 'Queries the database for posts.', 'Render' ),
		'tags'        => 'query database posts data loop',
		'atts'        => array(
			'author'    => array(
				'label'      => __( 'Author', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Author', 'Render' ),
					'options'     => _render_authors(),
				),
			),
			'post_type' => array(
				'label'       => __( 'Post type', 'Render' ),
				'type'        => 'selectbox',
				'description' => __( 'Available post types.', 'Render' ),
				'properties'  => array(
					'placeholder' => __( 'Post Type', 'Render' ),
					'options'     => get_post_types(),
				),
			),
		),
		'render'      => true,
		'wrapping'    => false
	)
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'query';
	$shortcode['source']   = 'Render';
	render_add_shortcode( $shortcode );
}

/**
 * Get all authors on the site
 *
 * @return array
 */
function _render_authors() {
	$users = get_users( array( 'fields' => array( 'display_name' ) ) );
	foreach ( $users as $user ) {
		$authors[] = esc_html( $user->display_name );
	}

	return $authors;
}

/**
 * Runs a WP_Query.
 *
 * @since  0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string
 */
function _render_query( $atts ) {

	$atts = shortcode_atts( array(
		'post_type' => '',
		'author'    => '',
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	$args = array(
		'post_type' => $atts['post_type'],
		'author'    => $atts['author']
	);

	$output = '';
	$query  = new WP_Query( $args );

	if ( $query->have_posts() ) {
		$output = '<ul>';
		while ( $query->have_posts() ) {
			$query->the_post();
			$output .= '<li>' . get_the_title() . '</li>';
		}
		$output .= '</ul>';
	} else {
		$output = 'No posts found';
	}

	return $output;
}