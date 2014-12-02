<?php

/**
 * Contains all USL packaged shortcodes within the User category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// Full Name
	array(
		'code'        => 'usl_user_full_name',
		'function'    => '_usl_sc_user_full_name',
		'title'       => __( 'User Full Name', 'USL' ),
		'description' => __( 'Get the full name of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// First Name
	array(
		'code'        => 'usl_user_first_name',
		'function'    => '_usl_sc_user_first_name',
		'title'       => __( 'User First Name', 'USL' ),
		'description' => __( 'Get the first name of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Last Name
	array(
		'code'        => 'usl_user_last_name',
		'function'    => '_usl_sc_user_last_name',
		'title'       => __( 'User Last Name', 'USL' ),
		'description' => __( 'Get the last name of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Username
	array(
		'code'        => 'usl_user_username',
		'function'    => '_usl_sc_user_username',
		'title'       => __( 'User Username', 'USL' ),
		'description' => __( 'Get the username of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Email
	array(
		'code'        => 'usl_user_email',
		'function'    => '_usl_sc_user_email',
		'title'       => __( 'User Email', 'USL' ),
		'description' => __( 'Get the email of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Display Name
	array(
		'code'        => 'usl_user_display_name',
		'function'    => '_usl_sc_user_display_name',
		'title'       => __( 'User Display Name', 'USL' ),
		'description' => __( 'Get the display name of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// ID
	array(
		'code'        => 'usl_user_id',
		'function'    => '_usl_sc_user_id',
		'title'       => __( 'User ID', 'USL' ),
		'description' => __( 'Get the ID of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Author URL
	array(
		'code'        => 'usl_user_url',
		'function'    => '_usl_sc_user_url',
		'title'       => __( 'User URL', 'USL' ),
		'description' => __( 'Get the author URL of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Registered Date
	array(
		'code'        => 'usl_user_registered_date',
		'function'    => '_usl_sc_user_registered_date',
		'title'       => __( 'User Registered Date', 'USL' ),
		'description' => __( 'Get the date the specified user registered.', 'USL' ),
		'atts'        => array(
			'user'   => array(
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
			'format' => array(
				'default' => 'F jS, Y',
			),
		),
		'render'      => true,
	),
	// Description
	array(
		'code'        => 'usl_user_description',
		'function'    => '_usl_sc_user_description',
		'title'       => __( 'User Description', 'USL' ),
		'description' => __( 'Get the description of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Role
	array(
		'code'        => 'usl_user_role',
		'function'    => '_usl_sc_user_role',
		'title'       => __( 'User Role', 'USL' ),
		'description' => __( 'Get the role of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Capabilities
	array(
		'code'        => 'usl_user_capabilities',
		'function'    => '_usl_sc_user_capabilities',
		'title'       => __( 'User Capabilities', 'USL' ),
		'description' => __( 'Get the capabilities of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Admin Theme
	array(
		'code'        => 'usl_user_admin_theme',
		'function'    => '_usl_sc_user_admin_theme',
		'title'       => __( 'User Admin Theme', 'USL' ),
		'description' => __( 'Get the admin theme of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Primary Blog
	array(
		'code'        => 'usl_user_primary_blog',
		'function'    => '_usl_sc_user_primary_blog',
		'title'       => __( 'User Primary Blog', 'USL' ),
		'description' => __( 'Get the primary blog of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Source Domain
	array(
		'code'        => 'usl_user_source_domain',
		'function'    => '_usl_sc_user_source_domain',
		'title'       => __( 'User Source Domain', 'USL' ),
		'description' => __( 'Get the source domain of the specified user.', 'USL' ),
		'atts'        => array(
			'user' => array(
				'label' => __( 'User', 'USL' ),
				'selectbox' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => '_usl_user_dropdown',
				),
			),
		),
		'render'      => true,
	),
	// Custom User Info
	array(
		'code'        => 'usl_user',
		'function'    => '_usl_sc_user',
		'title'       => __( 'Custom User Information', 'USL' ),
		'description' => __( 'Get a custom property of the specified user.', 'USL' ),
		'atts'        => array(
			'user'     => array(
				'required' => true,
			),
			'property' => array(
				'required' => true,
			),
		),
		'render'      => true,
	),
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'user';
	$shortcode['source'] = 'Ultimate Shortcodes Library';
	usl_add_shortcode( $shortcode );
}

/**
 * Gets specified current user property.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The specified current user property.
 */
function _usl_sc_user( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	// Get all of the set properties (if any)
	if ( ! empty( $atts['property'] ) ) {
		$output = $user->{$atts['property']};
	} else {
		$output = 'No property set.';
	}

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user full name.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The current user full name.
 */
function _usl_sc_user_full_name( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = "$user->user_firstname $user->user_lastname";

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user first name.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user first name.
 */
function _usl_sc_user_first_name( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user->user_firstname;

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user last name.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user last name.
 */
function _usl_sc_user_last_name( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user->user_lastname;

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user username.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user username.
 */
function _usl_sc_user_username( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user->user_login;

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user email.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user email.
 */
function _usl_sc_user_email( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user->user_email;

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user display name.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user display name.
 */
function _usl_sc_user_display_name( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user->display_name;

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user ID.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user ID.
 */
function _usl_sc_user_id( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user->ID;

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user author url.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user author url.
 */
function _usl_sc_user_url( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = get_author_posts_url( $user->ID );

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the date the current user registered.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the date the current user registered.
 */
function _usl_sc_user_registered_date( $atts ) {

	$atts = shortcode_atts( array(
		'format' => 'F jS, Y',
	), $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user->user_registered;

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	$output = date( $atts['format'], strtotime( $output ) );

	return $output;
}

/**
 * Gets the current user description.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user description.
 */
function _usl_sc_user_description( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user->description;

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user role.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user role.
 */
function _usl_sc_user_role( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$roles = $user->roles;

	$output = '';
	if ( $roles ) {
		foreach ( $roles as $role ) {
			$output .= $role;
		}
	}

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user capabilities.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user capabilities.
 */
function _usl_sc_user_capabilities( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$caps   = $user->allcaps;
	$output = '';
	if ( $caps ) {
		$i = 0;
		foreach ( $caps as $cap => $val ) {
			$i ++;
			$output .= $cap . ( $i < count( $caps ) ? ', ' : '' );
		}
	}

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user admin theme.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user admin theme.
 */
function _usl_sc_user_admin_theme( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user->admin_color;

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user primary blog.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user primary blog.
 */
function _usl_sc_user_primary_blog( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user->primary_blog;

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Gets the current user domain.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the current user domain.
 */
function _usl_sc_user_source_domain( $atts = array() ) {

	// Escape atts
	usl_esc_atts( $atts );

	if ( ! $user = _usl_sc_user_get_userdata( $atts ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user->source_domain;

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	return $output;
}

/**
 * Helper function for getting the user data.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return bool|WP_User The user object on success and false on failure.
 */
function _usl_sc_user_get_userdata( $atts ) {

	$atts = shortcode_atts( array(
		'user' => get_current_user_id(),
	), $atts );

	$user = get_userdata( $atts['user'] );

	if ( ! ( $user instanceof WP_User ) ) {
		return false;
	}

	return $user;
}

/**
 * Helper function for populating the user selectbox.
 *
 * @since USL 1.0.0
 * @access Private
 *
 * @return bool|array List of registered users.
 */
function _usl_user_dropdown() {

	$users = get_users( array(
		'fields' => array(
			'ID',
			'display_name',
		)
	) );

	$output = array();
	foreach ( $users as $user ) {
		$output[ $user->ID ] = $user->display_name;
	}

	return $output;
}