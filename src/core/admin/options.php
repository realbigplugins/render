<?php

class Render_OptionsPage extends Render {

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Function for the admin menu to create a menu item in the settings tree
	 */
	public function menu() {

		$hook = add_submenu_page(
			'render-settings',
			'Settings',
			'Settings',
			'manage_options',
			'render-settings',
			array( $this, 'page_output' )
		);
	}

	public static function page_specific() {
		add_action( 'admin_body_class', array( __CLASS__, 'body_class' ) );
	}

	public static function register_settings() {

		register_setting( 'render_options', 'render_render_visual' );
	}

	public static function body_class( $classes ) {

		$classes .= 'render render-options';

		return $classes;
	}

	/**
	 * Display the admin page
	 */
	public function page_output() {

		// TODO Brand and style this page
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$render_visual = get_option( 'render_render_visual', '1' );
		require( ABSPATH . 'wp-admin/options-head.php' );
		?>
		<div class="wrap render-wrap">
			<h2 class="render-page-title">
				<img src="<?php echo RENDER_URL; ?>/assets/images/render-logo.svg" class="render-page-title-logo"/>
				<?php _e( 'Settings', 'Render' ); ?>
			</h2>

			<form method="post" action="options.php">

				<?php settings_fields( 'render_options' ); ?>

				<table class="render-table">
					<tr>
						<th scope="row">
							<?php _e( 'Use the magical visual renderer?', 'Render' ); ?>
						</th>
						<td>
							<div class="render-switch">
								<input type="checkbox" id="render_render_visual"
								       name="render_render_visual" value="1" <?php checked( '1', $render_visual ); ?> />
								<label for="render_render_visual"></label>
							</div>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>

			</form>

		</div>
	<?php
	}
}

new Render_OptionsPage();