<?php
/**
 * Contains all Render packaged shortcodes within the Site category.
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
	// Site Info
	array(
		'code'        => 'render_site_info',
		'function'    => '_render_sc_site_info',
		'title'       => __( 'Site Information', 'Render' ),
		'description' => __( 'Gets specified info about the current site.', 'Render' ),
		'tags'        => 'title tag line admin email url version language description name',
		'atts'        => array(
			'info' => array(
				'label'       => __( 'Info', 'Render' ),
				'description' => sprintf(
					__( 'Which information to get about the site. Either choose an option or input your own from this list: %s.', 'Render' ),
					'<a href="http://codex.wordpress.org/Function_Reference/get_bloginfo">codex</a>'
				),
				'type'        => 'selectbox',
				'properties'  => array(
					'allowCustomInput' => true,
					'default'          => 'title',
					'options'          => array(
						'name'       => __( 'Title', 'Render' ),
						'description'     => __( 'Tag Line', 'Render' ),
						'admin_email' => __( 'Admin Email', 'Render' ),
						'url'     => __( 'Site URL', 'Render' ),
						'version'     => __( 'WordPress Version', 'Render' ),
						'language'     => __( 'Language', 'Render' ),
					),
				),
			),
		),
		'render'      => true,
	),
) as $shortcode ) {

	$shortcode['category'] = 'site';
	$shortcode['source']   = 'Render';

	// Adds shortcode to Render
	add_filter( 'render_add_shortcodes', function( $shortcodes ) use ( $shortcode ) {
		$shortcodes[] = $shortcode;
		return $shortcodes;
	});

	// Add shortcode category
	add_filter( 'render_modal_categories', function( $categories ) {
		$categories['site'] = array(
			'label' => __( 'Site', 'Render' ),
			'icon' => 'dashicons-admin-home',
		);
		return $categories;
	});
}

/**
 * Gets info about the current site.
 *
 * @since  Render 1.0.0
 * @access Private
 *
 * @param array $atts The attributes sent to the shortcode.
 *
 * @return string The desired blog info.
 */
function _render_sc_site_info( $atts = array() ) {

	$atts = shortcode_atts( array(
		'info' => 'name'
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	$output = get_bloginfo( $atts['info'] );

	// Default bloginfo is the name, so if it returns name, but we didn't ask for name, it was an invalid option
	if ( $output == get_bloginfo( 'name' ) && $atts['info'] !== 'name' ) {
		$output = render_sc_error( 'Not a valid option.' );
	}

	return $output;
}