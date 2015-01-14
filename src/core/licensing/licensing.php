<?php

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'RENDER_STORE_URL', 'http://realbigplugins.com' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'RENDER_ITEM_NAME', 'Render' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

function edd_sl_sample_plugin_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'edd_sample_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( RENDER_STORE_URL, __FILE__, array(
			'version' 	=> '1.0.0', 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => RENDER_ITEM_NAME, 	// name of this plugin
			'author' 	=> 'Joel Worsham & Kyle Maurer'  // author of this plugin
		)
	);

}
add_action( 'admin_init', 'edd_sl_sample_plugin_updater', 0 );