<?php
/**
 * Contains all USL packaged shortcodes within the User category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */
class USL_CoreShortcodes_User {

	private $_shortcodes = array(
		// Custom User Info
		array(
			'code' => 'usl_user',
			'function' => '_usl_sc_user',
			'title' => 'Current User',
			'description' => 'Get a property of the current user.',
			'atts' => array(
				'property' => array(
					'required' => true,
				),
			),
			'example' => '[usl_user property="first_name"]',
		),
		// Full Name
		array(
			'code' => 'usl_user_full_name',
			'function' => '_usl_sc_user_full_name',
			'title' => 'Current User Full Name',
			'description' => 'Get the full name of the current user.',
		),
		// First Name
		array(
			'code' => 'usl_user_first_name',
			'function' => '_usl_sc_user_first_name',
			'title' => 'Current User First Name',
			'description' => 'Get the first name of the current user.',
		),
		// Last Name
		array(
			'code' => 'usl_user_last_name',
			'function' => '_usl_sc_user_last_name',
			'title' => 'Current User Last Name',
			'description' => 'Get the last name of the current user.',
		),
		// Username
		array(
			'code' => 'usl_user_username',
			'function' => '_usl_sc_user_username',
			'title' => 'Current User Username',
			'description' => 'Get the username of the current user.',
		),
		// Email
		array(
			'code' => 'usl_user_email',
			'function' => '_usl_sc_user_email',
			'title' => 'Current User Email',
			'description' => 'Get the email of the current user.',
		),
		// Display Name
		array(
			'code' => 'usl_user_display_name',
			'function' => '_usl_sc_user_display_name',
			'title' => 'Current User Display Name',
			'description' => 'Get the display name of the current user.',
		),
		// ID
		array(
			'code' => 'usl_user_id',
			'function' => '_usl_sc_user_id',
			'title' => 'Current User ID',
			'description' => 'Get the ID of the current user.',
		),
		// Author URL
		array(
			'code' => 'usl_user_url',
			'function' => '_usl_sc_user_url',
			'title' => 'Current User URL',
			'description' => 'Get the author URL of the current user.',
		),
		// Registered Date
		array(
			'code' => 'usl_user_registered_date',
			'function' => '_usl_sc_user_registered_date',
			'title' => 'Current User Registered Date',
			'description' => 'Get the date the current user registered.',
		),
		// Description
		array(
			'code' => 'usl_user_description',
			'function' => '_usl_sc_user_description',
			'title' => 'Current User Description',
			'description' => 'Get the description of the current user.',
		),
		// Role
		array(
			'code' => 'usl_user_role',
			'function' => '_usl_sc_user_role',
			'title' => 'Current User Role',
			'description' => 'Get the role of the current user.',
		),
		// Capabilities
		array(
			'code' => 'usl_user_capabilities',
			'function' => '_usl_sc_user_capabilities',
			'title' => 'Current User Capabilities',
			'description' => 'Get the capabilities of the current user.',
		),
		// Admin Theme
		array(
			'code' => 'usl_user_admin_theme',
			'function' => '_usl_sc_user_admin_theme',
			'title' => 'Current User Admin Theme',
			'description' => 'Get the admin theme of the current user.',
		),
		// Primary Blog
		array(
			'code' => 'usl_user_primary_blog',
			'function' => '_usl_sc_user_primary_blog',
			'title' => 'Current User Primary Blog',
			'description' => 'Get the primary blog of the current user.',
		),
		// Source Domain
		array(
			'code' => 'usl_user_source_domain',
			'function' => '_usl_sc_user_source_domain',
			'title' => 'Current User Source Domain',
			'description' => 'Get the source domain of the current user.',
		),
	);

	function __construct() {

		foreach ( $this->_shortcodes as $shortcode ) {
			$shortcode['category'] = 'User';
			usl_add_shortcode( $shortcode );
		}
	}
}

new USL_CoreShortcodes_User();

/**
 * Gets specified current user property.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The specified current user property.
 */
function _usl_sc_user( $atts = array() ) {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	// Get all of the set properties (if any)
	if ( ! empty( $atts['property'] ) ) {

		$output = $current_user->{$atts['property']};
	} else {
		$output = 'No property set.';
	}

	return $output;
}

/**
 * Gets the current user full name.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The current user full name.
 */
function _usl_sc_user_full_name() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	return "$current_user->user_firstname $current_user->user_lastname";
}

/**
 * Gets the current user first name.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user first name.
 */
function _usl_sc_user_first_name() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	return $current_user->user_firstname;
}

/**
 * Gets the current user last name.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user last name.
 */
function _usl_sc_user_last_name() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	return $current_user->user_lastname;
}

/**
 * Gets the current user username.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user username.
 */
function _usl_sc_user_login() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	return $current_user->user_login;
}

/**
 * Gets the current user email.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user email.
 */
function _usl_sc_user_email() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	return $current_user->user_email;
}

/**
 * Gets the current user display name.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user display name.
 */
function _usl_sc_user_display_name() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	return $current_user->display_name;
}

/**
 * Gets the current user ID.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user ID.
 */
function _usl_sc_user_id() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	return $current_user->ID;
}

/**
 * Gets the current user author url.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user author url.
 */
function _usl_sc_user_url() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	return get_author_posts_url( $current_user->ID );
}

/**
 * Gets the date the current user registered.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the date the current user registered.
 */
function _usl_sc_user_registered_date() {

	// TODO Allow atts to be passed to customize the date format, also use a date format!

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	return $current_user->user_registered;
}

/**
 * Gets the current user description.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user description.
 */
function _usl_sc_user_description() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	$current_user = get_userdata( $current_user->ID );

	return $current_user->description;
}

/**
 * Gets the current user role.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user role.
 */
function _usl_sc_user_role() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	$current_user = get_userdata( $current_user->ID );

	$roles = $current_user->roles;

	$output = '';
	if ( $roles ) {
		foreach ( $roles as $role ) {
			$output .= $role;
		}
	}
	return $output;
}

/**
 * Gets the current user capabilities.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user capabilities.
 */
function _usl_sc_user_caps() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	$current_user = get_userdata( $current_user->ID );

	$caps = $current_user->allcaps;
	$output = '';
	if ( $caps ) {
		foreach ( $caps as $cap => $val ) {
			$output .= $cap. '<br/>';
		}
	}
	return $output;
}

/**
 * Gets the current user admin theme.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user admin theme.
 */
function _usl_sc_user_admin_theme() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	$current_user = get_userdata( $current_user->ID );

	return $current_user->admin_color;
}

/**
 * Gets the current user primary blog.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user primary blog.
 */
function _usl_sc_user_primary_blog() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	$current_user = get_userdata( $current_user->ID );

	return $current_user->primary_blog;
}

/**
 * Gets the current user domain.
 *
 * @since USL 0.1.0
 * @access Private
 *
 * @return string The the current user domain.
 */
function _usl_sc_user_source_domain() {

	$current_user = wp_get_current_user();

	if ( ! ( $current_user instanceof WP_User) ) {
		return 'Cannot get current user';
	}

	$current_user = get_userdata( $current_user->ID );

	return $current_user->source_domain;
}