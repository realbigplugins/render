<?php
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

		if ( ! current_user_can('manage_options') ) {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		// Activate/Deactivate plugins
		if ( isset( $_GET['usl_activate'] ) ) {
			activate_plugin( $_GET['usl_activate'] );
			$this->update_nag( 'Extension activated! Refresh for changes to take effect.' );
		}

		if ( isset( $_GET['usl_deactivate'] ) ) {
			deactivate_plugins( $_GET['usl_deactivate'] );
			$this->update_nag( 'Extension deactivated! Refresh for changes to take effect.' );
		}

		// Declare addons
		$addons = array(
			'Holiday Shortcodes'         => array(
				'url'           => 'https://github.com/brashrebel/holiday-shortcodes',
				'install-url'   => 'https://github.com/brashrebel/holiday-shortcodes/archive/master.zip',
				'activate-slug' => 'holiday-shortcodes/holiday-shortcodes.php',
				'installed'     => ( get_plugins( '/holiday-shortcodes' ) ? true : false ),
				'active'        => ( is_plugin_active( 'holiday-shortcodes/holiday-shortcodes.php' ) ? true : false ),
				'icon'          => 'heart'
			),
		);
		?>

		<h3>Available Ultimate Shortcode Library Addons</h3>
		<?php
		foreach ( $addons as $name => $props ) {
			// Set up activate/deactivate urls
			$url            = remove_query_arg( array( 'usl_deactivate', 'usl_activate' ) );
			$activate_url   = esc_url( add_query_arg( array( 'usl_activate' => $props['activate-slug'] ), $url ) );
			$deactivate_url = esc_url( add_query_arg( array( 'usl_deactivate' => $props['activate-slug'] ), $url ) );

			echo '<div class="usl-addon usl-col-three">';
			echo '<div class="usl-addon-container">';
			echo '<a href="' . $props['url'] . '"><span class="dashicons dashicons-' . $props['icon'] . '"></span>';
			echo '<h4>' . $name . '</h4></a>';

			if ( $props['active'] ) {
				echo '<a href="' . $deactivate_url . '" class="button">Deactivate</a>';
			} elseif ( $props['installed'] && ! $props['active'] ) {
				echo '<a href="' . $activate_url . '" class="button">Activate</a>';
			} elseif ( ! $props['installed'] ) {
				echo '<a href="' . $props['install-url'] . '" class="button">Install</a>';
			}

			echo '</div>';
			echo '</div>';
		}
	}
}

new USL_Addons();