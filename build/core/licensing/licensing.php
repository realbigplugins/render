<?php
/**
 * Integrates Render with EDD licensing.
 *
 * @since 1.0.0
 *
 * @package Render
 * @subpackage EDD
 */

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'EDD_REALBIGPLUGINS_STORE_URL', 'http://realbigplugins.com' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'EDD_RENDER_NAME', 'Render' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

function edd_render_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'render_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( EDD_REALBIGPLUGINS_STORE_URL, RENDER_PATH . 'render.php', array(
			'version' 	=> RENDER_VERSION, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => EDD_RENDER_NAME, 	// name of this plugin
			'author' 	=> 'Joel Worsham & Kyle Maurer'  // author of this plugin
		)
	);
}
add_action( 'admin_init', 'edd_render_updater', 0 );


function edd_render_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_render_license_activate'] ) ) {

		// run a quick security check
		if( ! check_admin_referer( 'edd_render_nonce', 'edd_render_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'render_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( EDD_RENDER_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_REALBIGPLUGINS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'render_license_status', $license_data->license );

	}
}
add_action('admin_init', 'edd_render_activate_license');


/***********************************************
 * Illustrates how to deactivate a license key.
 * This will descrease the site count
 ***********************************************/

function edd_render_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_render_license_deactivate'] ) ) {

		// run a quick security check
		if( ! check_admin_referer( 'edd_render_nonce', 'edd_render_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'render_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( EDD_RENDER_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_REALBIGPLUGINS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'render_license_status' );

	}
}
add_action('admin_init', 'edd_render_deactivate_license');