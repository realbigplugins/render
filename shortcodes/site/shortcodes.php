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
