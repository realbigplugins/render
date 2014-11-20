<?php
/**
 * Contains all USL packaged shortcodes within the Site category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */

// IDEA Site time zone
// IDEA Site URL
// IDEA Site login page
// IDEA Site home page
// IDEA Site posts page
class USL_CoreShortcodes_Site {

	private $_shortcodes = array(
		// Site Info
		array(
			'code' => 'usl_site',
			'function' => '_usl_sc_site',
			'title' => 'Site Information',
			'description' => 'Gets specified info about the current site.',
			'atts' => array(
				'info' => array(),
			),
			'example' => '[usl_site info="description"]',
			'render' => true,
		),
		// Site Title
		array(
			'code' => 'usl_site_title',
			'function' => '_usl_sc_site_title',
			'title' => 'Site Title',
			'description' => 'Gets the title of the current site.',
			'render' => true,
		),
		// Site Tagline
		array(
			'code' => 'usl_site_tagline',
			'function' => '_usl_sc_site_tagline',
			'title' => 'Site Tagline',
			'description' => 'Gets the tagline of the current site.',
			'render' => true,
		),
		// Site Admin Email
		array(
			'code' => 'usl_site_admin_email',
			'function' => '_usl_sc_site_admin_email',
			'title' => 'Site Admin Email',
			'description' => 'Gets the admin email the current site.',
			'render' => true,
		),
	);

	function __construct() {

		foreach ( $this->_shortcodes as $shortcode ) {
			$shortcode['category'] = 'site';
			usl_add_shortcode( $shortcode );
		}
	}
}

new USL_CoreShortcodes_Site();

/**
 * Gets info about the current site.
 *
 * @since USL 0.1.0
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

	return get_bloginfo( $atts['info'] );
}

/**
 * Gets the current site title.
 *
 * @since USL 0.1.0
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
 * @since USL 0.1.0
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
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The site's admin email.
 */
function _usl_sc_site_admin_email() {

	return get_bloginfo( 'admin_email' );
}