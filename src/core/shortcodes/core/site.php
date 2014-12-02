<?php
/**
 * Contains all USL packaged shortcodes within the Site category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// Site Info
	array(
		'code'        => 'usl_site',
		'function'    => '_usl_sc_site',
		'title'       => __( 'Site Information', 'USL' ),
		'description' => __( 'Gets specified info about the current site.', 'USL' ),
		'atts'        => array(
			'info' => array(
				'label' => __( 'Info', 'USL' ),
			),
		),
		'render'      => true,
	),
	// Site Title
	array(
		'code'        => 'usl_site_title',
		'function'    => '_usl_sc_site_title',
		'title'       => __( 'Site Title', 'USL' ),
		'description' => __( 'Gets the title of the current site.', 'USL' ),
		'render'      => true,
	),
	// Site Tagline
	array(
		'code'        => 'usl_site_tagline',
		'function'    => '_usl_sc_site_tagline',
		'title'       => __( 'Site Tagline', 'USL' ),
		'description' => __( 'Gets the tagline of the current site.', 'USL' ),
		'render'      => true,
	),
	// Site Admin Email
	array(
		'code'        => 'usl_site_admin_email',
		'function'    => '_usl_sc_site_admin_email',
		'title'       => __( 'Site Admin Email', 'USL' ),
		'description' => __( 'Gets the admin email the current site.', 'USL' ),
		'render'      => true,
	),
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'site';
	usl_add_shortcode( $shortcode );
}

/**
 * Gets info about the current site.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The desired blog info.
 */
function _usl_sc_site( $atts ) {

	$atts = shortcode_atts( array(
		'info' => 'name'
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	return get_bloginfo( $atts['info'] );
}

/**
 * Gets the current site title.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @return string The texturized site title.
 */
function _usl_sc_site_title() {

	return get_bloginfo( 'name', 'display' );
}

/**
 * Gets the site tag-line.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @return string The texturized site tag-line.
 */
function _usl_sc_site_tagline() {

	return get_bloginfo( 'description', 'display' );
}

/**
 * Gets the site's admin email.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @return string The site's admin email.
 */
function _usl_sc_site_admin_email() {

	return get_bloginfo( 'admin_email' );
}