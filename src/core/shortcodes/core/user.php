<?php
/**
 * Contains all Render packaged shortcodes within the User category.
 *
 * @since      1.0.0
 *
 * @package    Render
 * @subpackage Shortcodes
 */

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Loops through each shortcode and adds it to Render
foreach (
	array(
		/*
		 * User Information
		 *
		 * Gets specific information about a specific user (or the current user).
		 *
		 * @since 1.0.0
		 *
		 * @att {selectbox} user        The user to get information from.
		 * @att {selectbox} property    What information to get about the user.
		 * @att {selectbox} date_format How to format the date if the user information is a date.
		 */
		array(
			'code'        => 'render_user_info',
			'function'    => '_render_sc_user_info',
			'title'       => __( 'User Information', 'Render' ),
			'description' => __( 'Get a property of the specified user.', 'Render' ),
			'tags'        => 'display name full first last user email id author url description role',
			'atts'        => array(
				'user'        => array(
					'label'      => __( 'User', 'Render' ),
					'type'       => 'selectbox',
					'properties' => array(
						'placeholder' => __( 'Defaults to the current user', 'Render' ),
						'callback'    => array(
							'function' => 'render_sc_user_list',
						),
					),
				),
				'property'    => array(
					'label'       => __( 'Property', 'Render' ),
					'description' => __( 'Select what information to get or input custom information to get.', 'Render' ),
					'required'    => true,
					'type'        => 'selectbox',
					'properties'  => array(
						'groups' => array(
							array(
								'label'   => __( 'Name', 'Render' ),
								'options' => array(
									'display_name' => __( 'Display Name', 'Render' ),
									'full_name'    => __( 'Full Name', 'Render' ),
									'first_name'   => __( 'First Name', 'Render' ),
									'last_name'    => __( 'Last Name', 'Render' ),
									'username'     => __( 'User Name', 'Render' ),
								),
							),
							array(
								'label'   => __( 'Meta', 'Render' ),
								'options' => array(
									'email'           => __( 'Email Address', 'Render' ),
									'id'              => __( 'ID', 'Render' ),
									'author_url'      => __( 'Author Link', 'Render' ),
									'description'     => __( 'Description', 'Render' ),
									'role'            => __( 'Role', 'Render' ),
									'user_registered' => __( 'Registered Date', 'Render' ),
								),
							),
						),
					),
				),
				'date_format' => render_sc_attr_template( 'date_format', array(
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'property' => array(
									'type'  => '==',
									'value' => 'user_registered'
								),
							),
						),
					),
				) ),
			),
			'render'      => array(
				'displayInline' => true,
			),
		),
		// Login form
		array(
			'code'        => 'render_login_form',
			'function'    => '_render_sc_login_form',
			'title'       => __( 'Login form', 'Render' ),
			'description' => __( 'Displays a login form to logged out users.', 'Render' ),
			'atts'        => array(
				'message'        => array(
					'label'       => __( 'Logged in Message', 'Render' ),
					'description' => __( 'Message to display to already logged in users.', 'Render' ),
					'properties'  => array(
						'placeholder' => __( 'You are already logged in.', 'Render' ),
					),
				),
				'redirect'       => array(
					'label'       => __( 'Redirect', 'Render' ),
					'description' => __( 'Redirect to this page after login.', 'Render' ),
					'type'        => 'selectbox',
					'properties'  => array(
						'groups'      => array(),
						'callback'    => array(
							'groups'   => true,
							'function' => 'render_sc_post_list',
						),
						'placeholder' => __( 'Same page', 'Render' ),
					),
				),
				'remember'       => array(
					'label'      => __( 'Remember Me', 'Render' ),
					'type'       => 'checkbox',
					'properties' => array(
						'value' => 'true',
						'label' => __( 'Show "Remember Me" checkbox.', 'Render' ),
					),
				),
				array(
					'type'     => 'section_break',
					'label'    => __( 'Form Labels', 'Render' ),
					'advanced' => true,
				),
				'label_username' => array(
					'label'      => __( 'Username', 'Render' ),
					'advanced'   => true,
					'properties' => array(
						'placeholder' => __( 'Username' ),
					),
				),
				'label_password' => array(
					'label'      => __( 'Password', 'Render' ),
					'advanced'   => true,
					'properties' => array(
						'placeholder' => __( 'Password' ),
					),
				),
				'label_remember' => array(
					'label'      => __( 'Remember Me', 'Render' ),
					'advanced'   => true,
					'properties' => array(
						'placeholder' => __( 'Remember Me' ),
					),
				),
				'Label_log_in'   => array(
					'label'      => __( 'Log In', 'Render' ),
					'advanced'   => true,
					'properties' => array(
						'placeholder' => __( 'Log In' ),
					),
				),
				array(
					'type'     => 'section_break',
					'label'    => __( 'Default field values.', 'Render' ),
					'advanced' => true,
				),
				'value_username' => array(
					'label'    => __( 'Username', 'Render' ),
					'advanced' => true,
				),
				'value_remember' => array(
					'label'      => __( 'Remember Me', 'Render' ),
					'advanced'   => true,
					'type'       => 'checkbox',
					'properties' => array(
						'label' => __( '(start checked)', 'Render' ),
						'value' => 'true',
					),
				),
				array(
					'type'     => 'section_break',
					'label'    => __( 'HTML ID\'s', 'Render' ),
					'advanced' => true,
				),
				'form_id'        => array(
					'label'      => __( 'Form', 'Render' ),
					'advanced'   => true,
					'properties' => array(
						'placeholder' => 'loginform',
					),
				),
				'id_username'    => array(
					'label'      => __( 'Username', 'Render' ),
					'advanced'   => true,
					'properties' => array(
						'placeholder' => 'user_login',
					),
				),
				'id_password'    => array(
					'label'      => __( 'Password', 'Render' ),
					'advanced'   => true,
					'properties' => array(
						'placeholder' => 'user_pass',
					),
				),
				'id_remember'    => array(
					'label'      => __( 'Remember Me', 'Render' ),
					'advanced'   => true,
					'properties' => array(
						'placeholder' => 'rememberme',
					),
				),
				'id_submit'      => array(
					'label'      => __( 'Submit', 'Render' ),
					'advanced'   => true,
					'properties' => array(
						'placeholder' => 'wp-submit',
					),
				),
			),
			'render'      => true,
		),
	) as $shortcode
) {

	$shortcode['category'] = 'user';
	$shortcode['source']   = 'Render';

	render_add_shortcode( $shortcode );
	render_add_shortcode_category( array(
		'id'    => 'user',
		'label' => __( 'User', 'Render' ),
		'icon'  => 'dashicons-admin-users',
	) );
}

/**
 * Gets specified current user property.
 *
 * @since  1.0.0
 * @access private
 *
 * @param array $atts The attributes sent to the shortcode.
 *
 * @return string The specified current user property.
 */
function _render_sc_user_info( $atts = array() ) {

	$atts = shortcode_atts( array(
		'user'     => get_current_user_id(),
		'property' => false,
		'date_format' => get_option( 'date_format', 'F j, Y' ),
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	if ( $atts['property'] === false ) {
		return '';
	}

	if ( ! $user = render_sc_user_get_userdata( $atts['user'] ) ) {
		return '';
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
		case 'user_registered':

			if ( is_callable( "_render_sc_user_$atts[property]" ) ) {
				return call_user_func( "_render_sc_user_$atts[property]", $user, $atts );
			}
			break;

		default:

			if ( isset( $user->{$atts['property']} ) ) {
				return $user->{$atts['property']};
			} else {
				return '';
			}
			break;
	}
}

function _render_sc_user_info_tinymce( $atts = array() ) {

	$atts = shortcode_atts( array(
		'user'        => get_current_user_id(),
		'property'    => false,
		'date_format' => get_option( 'date_format', 'F j, Y' ),
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	if ( $atts['property'] === false ) {
		return render_tinymce_visibility_wrap( render_sc_error( __( 'No property selected', 'Render' ) ), 'hidden' );
	}

	if ( ! $user = render_sc_user_get_userdata( $atts['user'] ) ) {
		return render_tinymce_visibility_wrap( __( 'Cannot find specified user.', 'Render' ), 'hidden' );
	}

	$output = _render_sc_user_info( $atts );

	if ( empty( $output ) ) {
		return render_tinymce_visibility_wrap( __( "Cannot get user $atts[property].", 'Render' ), 'hidden' );
	} else {
		return $output;
	}
}

/**
 * Gets the current user full name.
 *
 * @since  1.0.0
 * @access private
 *
 * @param object $user The user object.
 *
 * @return string The current user full name.
 */
function _render_sc_user_full_name( $user ) {

	if ( isset( $user['first_name'] ) && isset( $user['last_name'] ) ) {
		return $user['first_name'] . ' ' . $user['last_name'];
	} else {
		return '';
	}
}

/**
 * Gets the current user first name.
 *
 * @since  1.0.0
 * @access private
 *
 * @param object $user The user object.
 *
 * @return string The the current user first name.
 */
function _render_sc_user_first_name( $user ) {

	if ( isset( $user['first_name'] ) ) {
		return $user['first_name'];
	} else {
		return '';
	}
}

/**
 * Gets the current user last name.
 *
 * @since  1.0.0
 * @access private
 *
 * @param object $user The user object.
 *
 * @return string The the current user last name.
 */
function _render_sc_user_last_name( $user ) {

	if ( isset( $user['last_name'] ) ) {
		return $user['last_name'];
	} else {
		return '';
	}
}

/**
 * Gets the current user username.
 *
 * @since  1.0.0
 * @access private
 *
 * @param object $user The user object.
 *
 * @return string The the current user username.
 */
function _render_sc_user_username( $user ) {

	if ( isset( $user['user_login'] ) ) {
		return $user['user_login'];
	} else {
		return '';
	}
}

/**
 * Gets the current user email.
 *
 * @since  1.0.0
 * @access private
 *
 * @param object $user The user object.
 *
 * @return string The the current user email.
 */
function _render_sc_user_email( $user ) {

	if ( isset( $user['user_email'] ) ) {
		return $user['user_email'];
	} else {
		return '';
	}
}

/**
 * Gets the current user display name.
 *
 * @since  1.0.0
 * @access private
 *
 * @param object $user The user object.
 *
 * @return string The the current user display name.
 */
function _render_sc_user_display_name( $user ) {

	if ( isset( $user['display_name'] ) ) {
		return $user['display_name'];
	} else {
		return '';
	}
}

/**
 * Gets the current user author url.
 *
 * @since  1.0.0
 * @access private
 *
 * @param object $user The user object.
 *
 * @return string The the current user author url.
 */
function _render_sc_user_author_url( $user ) {

	$url = get_author_posts_url( $user['ID '] );
	if ( ! empty( $url ) ) {
		return $url;
	} else {
		return '';
	}
}

/**
 * Gets the current user description.
 *
 * @since  1.0.0
 * @access private
 *
 * @param object $user The user object.
 *
 * @return string The the current user description.
 */
function _render_sc_user_description( $user ) {

	if ( isset( $user['description'] ) ) {
		return $user['description'];
	} else {
		return '';
	}
}

/**
 * Gets the current user role.
 *
 * @since  1.0.0
 * @access private
 *
 * @param object $user The user object.
 *
 * @return string The the current user role.
 */
function _render_sc_user_role( $user ) {

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
		return '';
	}
}

/**
 * Gets the date the current user registered.
 *
 * @since  1.0.0
 * @access private
 *
 * @param object $user The user object.
 * @param array $atts The attributes sent to the shortcode.
 *
 * @return string The the date the current user registered.
 */
function _render_sc_user_user_registered( $user, $atts = array() ) {

	if ( $atts['date_format'] == 'default_date' ) {
		$atts['date_format'] = get_option( 'date_format', 'F jS, Y' );
	}

	if ( isset( $user['user_registered'] ) ) {
		return date( $atts['date_format'], strtotime( $user['user_registered'] ) );
	} else {
		return '';
	}
}

/**
 * Outputs a login form for logged out users
 *
 * @since 1.0.0
 *
 * @param array $atts The attributes sent to the shortcode.
 *
 * @return string The HTML login form
 */
function _render_sc_login_form( $atts = array() ) {

	$atts = shortcode_atts( array(
		'message'        => __( 'You are already logged in.', 'Render' ),
		'echo'           => false, // Not a visible option
		'redirect'       => '',
		'form_id'        => '',
		'label_username' => '',
		'label_password' => '',
		'label_remember' => '',
		'label_log_in'   => '',
		'id_username'    => '',
		'id_password'    => '',
		'id_remember'    => '',
		'id_submit'      => '',
		'remember'       => false, // Show remember me checkbox or not
		'value_username' => '',
		'value_remember' => '',
	), $atts );

	$atts = render_esc_atts( $atts );

	// Don't allow empty atts to be passed through
	foreach ( $atts as $i => $att ) {
		if ( $att === '' ) {
			unset( $atts[ $i ] );
		}
	}

	// Strip out message
	$message = $atts['message'];
	unset( $atts['message'] );

	if ( is_user_logged_in() ) {
		return "<p class='render-logged-in-message'>$message</p>";
	} else {
		return wp_login_form( $atts );
	}
}


/**
 * TinyMCE version.
 *
 * Logs out the user before displaying form.
 *
 * @since 1.0.0
 *
 * @param array $atts The attributes sent to the shortcode.
 *
 * @return string The HTML login form
 */
function _render_sc_login_form_tinymce( $atts = array() ) {

	render_tinyme_log_out();

	return _render_sc_login_form( $atts );
}

/**
 * Helper function for getting the user data.
 *
 * @since  1.0.0
 *
 * @param string $user_ID The user ID to get.
 *
 * @return bool|WP_User The user object on success and false on failure.
 */
function render_sc_user_get_userdata( $user_ID ) {

	$user_data = get_user_meta( $user_ID );

	// Get some extra params
	$user_obj = get_user_by( 'id', $user_ID );
	if ( $user_obj instanceof WP_User ) {
		$data      = (array) $user_obj->data;
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
 * @since  1.0.0
 *
 * @param string $capability Min capability required to include user in drop-down.
 *
 * @return bool|array List of registered users.
 */
function render_sc_user_list( $capability = 'read' ) {

	$users = get_users();

	$output = array();
	foreach ( $users as $user ) {
		$capabilities = isset( $user->allcaps ) ? $user->allcaps : false;
		if ( $capabilities && isset( $capabilities[ (string) $capability ] ) ) {
			$output[ $user->ID ] = $user->display_name;
		}
	}

	return $output;
}