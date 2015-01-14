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
			'render',
			'Render Options',
			'Options',
			'manage_options',
			'render-options',
			array( $this, 'page_output' )
		);

		add_action( "load-$hook", array( __CLASS__, 'page_specific' ) );
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
		require(ABSPATH . 'wp-admin/options-head.php');
		?>
		<div class="wrap">
			<h2><?php _e( 'Ultimate Shortcode Library Options', 'Render' ); ?></h2>

			<form method="post" action="options.php">

				<?php settings_fields( 'render_options' ); ?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Use the magical visual renderer?', 'Render' ); ?>
						</th>
						<td>
							<input type="checkbox" id="render_render_visual"
							       name="render_render_visual" value="1" <?php checked( '1', $render_visual ); ?> />
							<label for="render_render_visual"><?php _e( 'Heck Yes!', 'Render' ); ?></label>
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