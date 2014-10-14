<?php
/**
 * Created by PhpStorm.
 * User: kylemaurer
 * Date: 8/29/14
 * Time: 11:59 AM
 */
/*-------------------------------
Get current user
-------------------------------*/
function usl_user( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'value1' => 'user_firstname',
		'value2' => ' '
	), $atts));
	$current_user = wp_get_current_user();
	return $current_user->$value1.' '.$current_user->$value2;
}
add_usl_shortcode( 'usl_user', 'usl_user', 'Current User', 'Get a property of the current user.', 'User', 'value1, value2', '[usl_user value1="first_name" value2="last_name"]  ' );
/*-------------------------------
Get current user Full Name
-------------------------------*/
function usl_user_full_name( $atts, $content = null ) {
	$atts = shortcode_atts(array(
		'value1' => 'user_firstname',
		'value2' => 'user_lastname'
		), $atts);
	return usl_user( $atts );
}
add_usl_shortcode( 'usl_user_full_name', 'usl_user_full_name', 'Current user full name', 'Displays the full name of the current user.', 'User' );
/*-------------------------------
Get current user First Name
-------------------------------*/
function usl_user_first_name( $atts, $content = null ) {
	$atts = shortcode_atts(array(
		'value1' => 'user_firstname'
	), $atts);
	return usl_user( $atts );
}
add_usl_shortcode( 'usl_user_first_name', 'usl_user_first_name', 'Current user first name', 'Displays the first name of the current user.', 'User' );
/*-------------------------------
Get current user Last Name
-------------------------------*/
function usl_user_last_name( $atts, $content = null ) {
	$atts = shortcode_atts(array(
		'value1' => 'user_lastname'
	), $atts);
	return usl_user( $atts );
}
add_usl_shortcode( 'usl_user_last_name', 'usl_user_last_name', 'Current user last name', 'Displays the last name of the current user.', 'User' );
/*-------------------------------
Get current user's username
-------------------------------*/
function usl_user_login( $atts, $content = null ) {
	$atts = shortcode_atts(array(
		'value1' => 'user_login'
	), $atts);
	return usl_user( $atts );
}
add_usl_shortcode( 'usl_user_login', 'usl_user_login', 'Current user login', 'Displays the username of the current user.', 'User' );
/*-------------------------------
Get current user Email
-------------------------------*/
function usl_user_email( $atts, $content = null ) {
	$atts = shortcode_atts(array(
		'value1' => 'user_email'
	), $atts);
	return usl_user( $atts );
}
add_usl_shortcode( 'usl_user_email', 'usl_user_email', 'Current user email', 'Displays the email address of the current user.', 'User' );
/*-------------------------------
Get current user Display Name
-------------------------------*/
function usl_user_display_name( $atts, $content = null ) {
	$atts = shortcode_atts(array(
		'value1' => 'display_name'
	), $atts);
	return usl_user( $atts );
}
add_usl_shortcode( 'usl_user_display_name', 'usl_user_display_name', 'Current user display name', 'Displays the display name of the current user.', 'User' );
/*-------------------------------
Get current user ID
-------------------------------*/
function usl_user_id( $atts, $content = null ) {
	$atts = shortcode_atts(array(
		'value1' => 'ID'
	), $atts);
	return usl_user( $atts );
}
add_usl_shortcode( 'usl_user_id', 'usl_user_id', 'Current user ID', 'Displays the ID of the current user.', 'User' );
/*-------------------------------
Get current user URL
-------------------------------*/
function usl_user_url( $atts, $content = null ) {
	$atts = shortcode_atts(array(
		'value1' => 'user_url'
	), $atts);
	return usl_user( $atts );
}
add_usl_shortcode( 'usl_user_url', 'usl_user_url', 'Current user URL', 'Displays the URL of the current user.', 'User' );
/*-------------------------------
Get current user first registered time
-------------------------------*/
function usl_user_registered() {
	$user = get_userdata( wp_get_current_user()->ID );
	return $user->user_registered;
}
add_usl_shortcode( 'usl_user_registered', 'usl_user_registered', 'Current user first registered', 'Displays the date and time the current user first registered on this site.', 'User' );
/*-------------------------------
Get current user description
-------------------------------*/
function usl_user_description() {
	$user = get_userdata( wp_get_current_user()->ID );
	return $user->description;
}
add_usl_shortcode( 'usl_user_description', 'usl_user_description', 'Current user description', 'Displays the user\'s description.', 'User' );
/*-------------------------------
Get current user role
-------------------------------*/
function usl_user_role() {
	$user = get_userdata( wp_get_current_user()->ID );
	$roles = $user->roles;
	$output = '';
	if ( $roles ) {
		foreach ( $roles as $role ) {
			$output .= $role;
		}
	}
	return $output;
}
add_usl_shortcode( 'usl_user_role', 'usl_user_role', 'Current user role', 'Displays the user\'s role.', 'User' );
/*-------------------------------
Get current user capabilities
-------------------------------*/
function usl_user_caps() {
	$user = get_userdata( wp_get_current_user()->ID );
	$caps = $user->allcaps;
	$output = '';
	if ( $caps ) {
		foreach ( $caps as $cap => $val ) {
			$output .= $cap. '<br/>';
		}
	}
	return $output;
}
add_usl_shortcode( 'usl_user_capabilities', 'usl_user_caps', 'Current user role', 'Displays the user\'s role.', 'User' );
/*-------------------------------
Get current user admin color
-------------------------------*/
function usl_user_admin_theme() {
	$user = get_userdata( wp_get_current_user()->ID );
	return $user->admin_color;
}
add_usl_shortcode( 'usl_user_admin_theme', 'usl_user_admin_theme', 'Current user admin theme', 'Displays the user\'s admin theme.', 'User' );
/*-------------------------------
Get current user primary blog
-------------------------------*/
function usl_user_primary_blog() {
	$user = get_userdata( wp_get_current_user()->ID );
	return $user->primary_blog;
}
add_usl_shortcode( 'usl_user_primary_blog', 'usl_user_primary_blog', 'Current user primary blog', 'Displays the user\'s primary blog.', 'User' );
/*-------------------------------
Get current user source domain
-------------------------------*/
function usl_user_source_domain() {
	$user = get_userdata( wp_get_current_user()->ID );
	return $user->source_domain;
}
add_usl_shortcode( 'usl_user_source_domain', 'usl_user_source_domain', 'Current user source domain', 'Displays the user\'s source domain.', 'User' );