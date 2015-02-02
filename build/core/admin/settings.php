<?php
// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Render_AdminPage_Settings
 *
 * Provides the admin page for adjusting Render settings.
 *
 * @since      1.0.0
 *
 * @package    Render
 * @subpackage Admin
 */
class Render_AdminPage_Settings extends Render {

	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Function for the admin menu to create a menu item in the settings tree.
	 *
	 * @since 1.0.0
	 */
	public function menu() {

		add_submenu_page(
			'render-settings',
			'Settings',
			'Settings',
			'manage_options',
			'render-settings',
			array( $this, 'page_output' )
		);
	}

	/**
	 * Actions hooked only into this admin page.
	 *
	 * @since 1.0.0
	 */
	public static function page_specific() {
		add_action( 'admin_body_class', array( __CLASS__, 'body_class' ) );
	}

	/**
	 * Registers Render settings for the options page.
	 *
	 * @since 1.0.0
	 */
	public static function register_settings() {

		register_setting( 'render_options', 'render_render_visual' );

		// EDD Licensing
		register_setting( 'render_options', 'render_license_key', 'edd_render_sanitize_license' );
	}

	/**
	 * Adds on custom admin body classes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Admin body classes.
	 * @return string New classes.
	 */
	public static function body_class( $classes ) {

		$classes .= 'render render-options';

		return $classes;
	}

	/**
	 * Display the admin page.
	 *
	 * @since 1.0.0
	 */
	public function page_output() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$render_visual = get_option( 'render_render_visual', '1' );
		$license       = get_option( 'render_license_key' );
		$status        = get_option( 'render_license_status' );

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
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e( 'License Key', 'Render' ); ?>
						</th>
						<td>
							<label>
								<input id="render_license_key" name="render_license_key" type="text"
								       class="regular-text" value="<?php esc_attr_e( $license ); ?>"/>
							</label>
						</td>
					</tr>
					<?php if ( ! empty( $license ) ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Activate License', 'Render' ); ?>
							</th>
							<td>
								<?php if ( $status !== false && $status == 'valid' ) { ?>
									<?php wp_nonce_field( 'edd_render_nonce', 'edd_render_nonce' ); ?>
									<span class="render-license-status valid">
										<span class="dashicons dashicons-yes"></span>
										<?php _e( 'active', 'Render' ); ?>
									</span>
									<input type="submit" class="button-secondary button-red"
									       name="edd_render_license_deactivate"
									       value="<?php _e( 'Deactivate License', 'Render' ); ?>"/>
								<?php } else {
									wp_nonce_field( 'edd_render_nonce', 'edd_render_nonce' ); ?>
									<span class="render-license-status invalid">
										<span class="dashicons dashicons-no"></span>
										<?php _e( 'inactive', 'Render' ); ?>
									</span>
									<input type="submit" class="button-secondary" name="edd_render_license_activate"
									       value="<?php _e( 'Activate License', 'Render' ); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
					<tr>
						<th scope="row">
							<?php _e( 'Use the magical visual renderer?', 'Render' ); ?>
						</th>
						<td>
							<div class="render-switch large">
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

new Render_AdminPage_Settings();

function edd_render_sanitize_license( $new ) {
	$old = get_option( 'render_license_key' );
	if ( $old && $old != $new ) {
		delete_option( 'render_license_status' ); // new license has been entered, so must reactivate
	}

	return $new;
}