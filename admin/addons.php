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
				'url'           => 'http://wordpress.org/plugins/client-dash-wp-help-add-on/',
				'install-url'   => network_admin_url( 'plugin-install.php' ) . '?tab=search&s=client+dash+wp+help+add+on&plugin-search-input=Search+Plugins',
				'activate-slug' => 'client-dash-wp-help/client-dash-wp-help.php',
				'installed'     => ( get_plugins( '/client-dash-wp-help' ) ? true : false ),
				'active'        => ( is_plugin_active( 'client-dash-wp-help/client-dash-wp-help.php' ) ? true : false ),
				'icon'          => 'editor-help'
			),
			'Client Dash Extension Boilerplate' => array(
				'url'           => 'https://github.com/brashrebel/client-dash-extension-boilerplate',
				'install-url'   => 'https://github.com/brashrebel/client-dash-extension-boilerplate/archive/master.zip',
				'activate-slug' => 'client-dash-extension-boilerplate/client-dash-extension-boilerplate.php',
				'installed'     => ( get_plugins( '/client-dash-extension-boilerplate' ) ? true : false ),
				'active'        => ( is_plugin_active( 'client-dash-extension-boilerplate/client-dash-extension-boilerplate.php' ) ? true : false ),
				'icon'          => 'admin-tools'
			),
			'Client Dash Status Cake Addon'     => array(
				'url'           => 'https://wordpress.org/plugins/client-dash-status-cake-add-on/',
				'install-url'   => network_admin_url( 'plugin-install.php' ) . '?tab=search&s=client+dash+status+cake&plugin-search-input=Search+Plugins',
				'activate-slug' => 'client-dash-status-cake/client-dash-status-cake.php',
				'installed'     => ( get_plugins( '/client-dash-status-cake' ) ? true : false ),
				'active'        => ( is_plugin_active( 'client-dash-status-cake/client-dash-status-cake.php' ) ? true : false ),
				'icon'          => 'smiley'
			),
			'Client Dash BackupBuddy Addon'     => array(
				'url'           => 'https://wordpress.org/plugins/client-dash-backup-buddy/',
				'install-url'   => network_admin_url( 'plugin-install.php' ) . '?tab=search&s=client+dash+backup+buddy&plugin-search-input=Search+Plugins',
				'activate-slug' => 'client-dash-backup-buddy/client-dash-backup-buddy.php',
				'installed'     => ( get_plugins( '/client-dash-backup-buddy' ) ? true : false ),
				'active'        => ( is_plugin_active( 'client-dash-backup-buddy/client-dash-backup-buddy.php' ) ? true : false ),
				'icon'          => 'backup'
			)
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
} // END class