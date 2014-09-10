<?php
/**
 * Created by PhpStorm.
 * User: kylemaurer
 * Date: 9/8/14
 * Time: 11:36 PM
 */

/**
 * SHORTCODE IDEAS
 *
 * Site title
 * Site tagline
 * Site time zone
 * Site admin e-mail
 * Site URL
 * Site login page
 * Site home page
 * Site posts page
 */

/**
 * Site
 *
 * @return string|void
 */
function usl_site( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'par' => 'name'
	), $atts));
	return get_bloginfo( $par );
}
add_usl_shortcode(
	'usl_site',
	'usl_site',
	'Site',
	'Gets specified info about the current site.',
	'Site',
	'par',
	'[usl_site par="description"]'
);
/**
 * Site title
 *
 * @return string|void
 */
function usl_site_title( $atts ) {
	$atts = shortcode_atts( array( 'par' => 'name' ), $atts );
	return usl_site( $atts );
}
add_usl_shortcode(
	'usl_site_title',
	'usl_site_title',
	'Site Title',
	'Gets the title of the current site.',
	'Site'
);
/**
 * Site tagline
 *
 * @return string|void
 */
function usl_site_tagline( $atts ) {
	$atts = shortcode_atts( array( 'par' => 'description' ), $atts );
	return usl_site( $atts );
}
add_usl_shortcode(
	'usl_site_tagline',
	'usl_site_tagline',
	'Site Tagline',
	'Gets the tagline of the current site.',
	'Site'
);
/**
 * Site admin email
 *
 * @return string|void
 */
function usl_site_admin_email( $atts ) {
	$atts = shortcode_atts( array( 'par' => 'admin_email' ), $atts );
	return usl_site( $atts );
}
add_usl_shortcode(
	'usl_site_admin_email',
	'usl_site_admin_email',
	'Site Admin Email',
	'Gets the admin email of the current site.',
	'Site'
);
