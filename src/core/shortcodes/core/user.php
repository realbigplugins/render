<?php

/**
 * Contains all USL packaged shortcodes within the User category.
 *
 * @since      USL 1.0.0
 *
 * @package    USL
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// User Info
	array(
		'code'        => 'usl_user_info',
		'function'    => '_usl_sc_user_info',
		'title'       => __( 'User Information', 'USL' ),
		'description' => __( 'Get a property of the specified user.', 'USL' ),
		'tags'        => 'display name full first last user email id author url description role',
		'atts'        => array(
			'user'     => array(
				'label'      => __( 'User', 'USL' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => array(
						'function' => 'usl_user_dropdown',
					),
				),
			),
			'property' => array(
				'label'       => __( 'Property', 'USL' ),
				'description' => __( 'Select what information to get or input custom information to get.', 'USL' ),
				'required'    => true,
				'type'        => 'selectbox',
				'properties'  => array(
					'groups' => array(
						array(
							'label'   => __( 'Name', 'USL' ),
							'options' => array(
								'display_name' => __( 'Display Name', 'USL' ),
								'full_name'    => __( 'Full Name', 'USL' ),
								'first_name'   => __( 'First Name', 'USL' ),
								'last_name'    => __( 'Last Name', 'USL' ),
								'username'     => __( 'User Name', 'USL' ),
							),
						),
						array(
							'label'   => __( 'Meta', 'USL' ),
							'options' => array(
								'email'       => __( 'Email Address', 'USL' ),
								'id'          => __( 'ID', 'USL' ),
								'author_url'  => __( 'Author Link', 'USL' ),
								'description' => __( 'Description', 'USL' ),
								'role'        => __( 'Role', 'USL' ),
							),
						),
					),
				),
			),
		),
		'render'      => true,
	),
	// Registered Date
	// TODO Test and fix up
	array(
		'code'        => 'usl_user_registered_date',
		'function'    => '_usl_sc_user_registered_date',
		'title'       => __( 'User Registered Date', 'USL' ),
		'description' => __( 'Get the date the specified user registered.', 'USL' ),
		'atts'        => array(
			'user'   => array(
				'label'      => __( 'User', 'USL' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Defaults to the current user', 'USL' ),
					'callback'    => array(
						'function' => 'usl_user_dropdown',
					),
				),
			),
			'date_format' => usl_sc_attr_template( 'date_format' ),
		),
		'render'      => true,
	),
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'user';
	$shortcode['source']   = 'Ultimate Shortcodes Library';
	usl_add_shortcode( $shortcode );
}

/**
 * Gets specified current user property.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The specified current user property.
 */
function _usl_sc_user_info( $atts = array() ) {

	$atts = shortcode_atts( array(
		'user'     => get_current_user_id(),
		'property' => false,
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	if ( $atts['property'] === false ) {
		return _usl_sc_error( 'No property selected' );
	}

	if ( ! $user = usl_sc_user_get_userdata( $atts['user'] ) ) {
		return 'Cannot find specified user.';
	}

	switch ( $atts['property'] ) {
		case 'id':

			return $user['ID'];
			break;

		case 'display_name':
		case 'full_name':
		case 'first_name':
		case 'last_name':
		case 'username':
		case 'email':
		case 'author_url':
		case 'description':
		case 'role':

			if ( is_callable( "_usl_sc_user_$atts[property]" ) ) {
				return call_user_func( "_usl_sc_user_$atts[property]", $user );
			}
			break;

		default:

			if ( isset( $user->{$atts['property']} ) ) {
				return $user->{$atts['property']};
			} else {
				return _usl_sc_error( "Cannot find user property $atts[property]." );
			}
			break;
	}
}

/**
 * Gets the current user full name.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param object $user The user object.
 *
 * @return string The current user full name.
 */
function _usl_sc_user_full_name( $user ) {

	if ( isset( $user['first_name']) && isset( $user['last_name']) ) {
		return $user['first_name']. ' ' . $user['last_name'];
	} else {
		return _usl_sc_error( 'Cannot get user full name.' );
	}
}

/**
 * Gets the current user first name.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param object $user The user object.
 *
 * @return string The the current user first name.
 */
function _usl_sc_user_first_name( $user ) {

	if ( isset( $user['first_name']) ) {
		return $user['first_name'];
	} else {
		return _usl_sc_error( 'Cannot get user first name.' );
	}
}

/**
 * Gets the current user last name.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param object $user The user object.
 *
 * @return string The the current user last name.
 */
function _usl_sc_user_last_name( $user ) {

	if ( isset( $user['last_name']) ) {
		return $user['last_name'];
	} else {
		return _usl_sc_error( 'Cannot get user last name.' );
	}
}

/**
 * Gets the current user username.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param object $user The user object.
 *
 * @return string The the current user username.
 */
function _usl_sc_user_username( $user ) {

	if ( isset( $user['user_login']) ) {
		return $user['user_login'];
	} else {
		return _usl_sc_error( 'Cannot get username.' );
	}
}

/**
 * Gets the current user email.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param object $user The user object.
 *
 * @return string The the current user email.
 */
function _usl_sc_user_email( $user ) {

	if ( isset( $user['user_email']) ) {
		return $user['user_email'];
	} else {
		return _usl_sc_error( 'Cannot get user email address.' );
	}
}

/**
 * Gets the current user display name.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param object $user The user object.
 *
 * @return string The the current user display name.
 */
function _usl_sc_user_display_name( $user ) {

	if ( isset( $user['display_name']) ) {
		return $user['display_name'];
	} else {
		return _usl_sc_error( 'Cannot get user display name.' );
	}
}

/**
 * Gets the current user author url.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param object $user The user object.
 *
 * @return string The the current user author url.
 */
function _usl_sc_user_author_url( $user ) {

	$url = get_author_posts_url( $user['ID ']);
	if ( ! empty( $url ) ) {
		return $url;
	} else {
		return _usl_sc_error( 'Cannot get author url.' );
	}
}

/**
 * Gets the current user description.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param object $user The user object.
 *
 * @return string The the current user description.
 */
function _usl_sc_user_description( $user ) {

	if ( isset( $user['description']) ) {
		return $user['description'];
	} else {
		return _usl_sc_error( 'Cannot get user description.' );
	}
}

/**
 * Gets the current user role.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param object $user The user object.
 *
 * @return string The the current user role.
 */
function _usl_sc_user_role( $user ) {

	$roles = $user['roles'];

	$output = '';
	if ( $roles ) {
		foreach ( $roles as $role ) {
			$output .= $role;
		}
	}

	if ( ! empty( $output ) ) {
		return $output;
	} else {
		return _usl_sc_error( 'Cannot get user role.' );
	}
}

/**
 * Gets the date the current user registered.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The the date the current user registered.
 */
function _usl_sc_user_registered_date( $atts = array() ) {

	$atts = shortcode_atts( array(
		'user' => get_current_user_id(),
		'date_format' => 'F jS, Y',
	), $atts );

	if ( ! $user = usl_sc_user_get_userdata( $atts['user'] ) ) {
		return 'Cannot find specified user.';
	}

	$output = $user['user_registered'];

	if ( empty( $output ) ) {
		return 'Cannot get user data.';
	}

	$output = date( $atts['date_format'], strtotime( $output ) );

	return $output;
}

/**
 * Helper function for getting the user data.
 *
 * @since  USL 1.0.0
 *
 * @param string $user_ID The user ID to get.
 *
 * @return bool|WP_User The user object on success and false on failure.
 */
function usl_sc_user_get_userdata( $user_ID ) {

	$user_data = get_user_meta( $user_ID );

	// Get some extra params
	$user_obj = get_user_by( 'id', $user_ID );
	if ( $user_obj instanceof WP_User ) {
		$data = (array) $user_obj->data;
		$user_data = array_merge( $user_data, $data );
	}

	if ( empty( $user_data ) ) {
		return false;
	}

	// "Flatten" array
	foreach ( $user_data as $property => $maybe_value ) {
		if ( is_array( $maybe_value ) ) {
			$user_data[ $property ] = $maybe_value[0];
		}
	}

	$user_data['roles'] = $user_obj->roles;

	return $user_data;
}

/**
 * Helper function for populating the user selectbox.
 *
 * @since  USL 1.0.0
 *
 * @return bool|array List of registered users.
 */
function usl_user_dropdown() {

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