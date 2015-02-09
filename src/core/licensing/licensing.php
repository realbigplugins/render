<?php
/**
 * Render licensing.
 *
 * @since 1.0.0
 */

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
if ( ! defined( 'REALBIGPLUGINS_STORE_URL' ) ) {
	define( 'REALBIGPLUGINS_STORE_URL', 'http://realbigplugins.com' );
}

// load our custom updater
if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	include( __DIR__ . '/EDD_SL_Plugin_Updater.php' );
}

/**
 * This filter is documented in src/core/licensing/settings.php
 */
$extension_licenses = apply_filters( 'render_licensing_extensions', array() );

foreach ( $extension_licenses as $extension => $label ) {

	/**
	 * Activate the license.
	 *
	 * @since 1.0.0
	 */
	add_action( 'admin_init', function() use ( $extension, $label ) {

		// listen for our activate button to be clicked
		if ( isset( $_POST["{$extension}_license_activate"] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( 'render_licensing_nonce', 'render_licensing_nonce' ) ) {
				return false; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = trim( get_option( "{$extension}_license_key" ) );

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => urlencode( $extension !== 'render' ? "Render {$label}" : 'Render' ), // the name of our product in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_get(
				add_query_arg( $api_params, REALBIGPLUGINS_STORE_URL ),
				array(
					'timeout'   => 15,
					'sslverify' => false,
				)
			);

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "valid" or "invalid"
			update_option( "{$extension}_license_status", $license_data->license );
		}
	});

	/**
	 * Deactivate the license.
	 *
	 * @since 1.0.0
	 */
	add_action( 'admin_init', function() use ( $extension, $label )  {

		// listen for our activate button to be clicked
		if ( isset( $_POST["{$extension}_license_deactivate"] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( 'render_licensing_nonce', 'render_licensing_nonce' ) ) {
				return false; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = trim( get_option( "{$extension}_license_key" ) );

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_name'  => urlencode( $extension !== 'render' ? "Render {$label}" : 'Render' ),
				// the name of our product in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_get(
				add_query_arg( $api_params, REALBIGPLUGINS_STORE_URL ),
				array(
					'timeout'   => 15,
					'sslverify' => false
				)
			);

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if ( $license_data->license == 'deactivated' ) {
				delete_option( "{$extension}_license_status" );
			}
		}
	});
}