<?php
/**
 * Handles plugin cleanup by remove values from the database.
 *
 * @since 1.1-beta-1
 *
 * @package Render
 */

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Fires on uninstalling (deleting) the plugin. Removes data.
 *
 * @since 1.1-beta-1
 */
function render_uninstall() {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	check_admin_referer( 'bulk-plugins' );

	// Delete the options Render has tracked
	if ( $options = get_option( 'render_updated_options' ) ) {

		$options[] = 'render_updated_options';

		foreach ( $options as $option ) {
			delete_option( $option );
		}
	}

	die();
}