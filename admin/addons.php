<?php
/**
 * Created by PhpStorm.
 * User: kylemaurer
 * Date: 9/9/14
 * Time: 10:40 PM
 */

if ( is_admin() ) {
	$usladdons = new USL_Addons();
}

class USL_Addons {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}

	/**
	 * Function for the admin menu to create a menu item in the settings tree
	 */
	public function menu() {
		add_submenu_page(
			'view-all-shortcodes',
			'Shortcode Addons',
			'Addons',
			'manage_options',
			'shortcodes-addons',
			array( $this, 'display' )
		);
	}

	/**
	 * Contents of the addons submenu
	 */
	public function display() {
		echo "stuff";
	}
} // END class