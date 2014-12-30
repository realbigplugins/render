<?php
/**
 * Contains all Render packaged shortcodes within the Site category.
 *
 * @since      Render 1.0.0
 *
 * @package    Render
 * @subpackage Shortcodes
 */

$_shortcodes = array(
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
				'description' => __( 'Which information to get about the site. Either choose an option or input your own.', 'Render' ),
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
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'site';
	$shortcode['source']   = 'Render';
	render_add_shortcode( $shortcode );
}

/**
 * Gets info about the current site.
 *
 * @since  Render 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The desired blog info.
 */
function _render_sc_site_info( $atts ) {

	$atts = shortcode_atts( array(
		'info' => 'name'
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	return get_bloginfo( $atts['info'] );
}