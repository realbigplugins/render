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
 * Site title
 *
 * @return string|void
 */
function usl_site_title() {
	return get_bloginfo( 'name' );
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
function usl_site_tagline() {
	return get_bloginfo( 'description' );
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
function usl_site_admin_email() {
	return get_bloginfo( 'admin_email' );
}
add_usl_shortcode(
	'usl_site_admin_email',
	'usl_site_admin_email',
	'Site Admin Email',
	'Gets the admin email of the current site.',
	'Site'
);
