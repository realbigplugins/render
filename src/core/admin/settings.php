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

		// Licensing

		/**
		 * This filter is documented in src/core/licensing/settings.php
		 */
		$extension_licenses = apply_filters( 'render_licensing_extensions', array() );

		// Register all license settings
		foreach ( $extension_licenses as $extension => $label ) {

			register_setting( 'render_options', "{$extension}_license_key", function ( $new ) use ( $extension ) {

				$old = get_option( "{$extension}_license_key" );
				if ( $old && $old != $new ) {
					delete_option( "{$extension}_license_status" ); // new license has been entered, so must reactivate
				}

				return $new;
			} );
		}
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

		/**
		 * Allow extensions to inject their licensing field here.
		 *
		 * @since 1.0.3
		 */
		$extension_licenses = apply_filters( 'render_licensing_extensions', array() );

		// Create our licenses array
		$licenses = array();
		foreach ( $extension_licenses as $extension => $extension_label ) {
			$licenses[ $extension ] = array(
				'key'    => get_option( "{$extension}_license_key" ),
				'status' => get_option( "{$extension}_license_status" ),
				'label'  => $extension_label,
			);
		}

		// Other options
		$render_visual = get_option( 'render_render_visual', '1' );

		require( ABSPATH . 'wp-admin/options-head.php' );
		?>
		<div class="wrap render-wrap">
			<h2 class="render-page-title">
				<img src="<?php echo RENDER_URL; ?>/assets/images/render-logo.svg" class="render-page-title-logo"
				 onerror="this.src='<?php echo RENDER_URL; ?>/assets/images/render-header-logo.png';" />
				<?php _e( 'Settings', 'Render' ); ?>
			</h2>

			<form method="post" action="options.php">

				<?php settings_fields( 'render_options' ); ?>

				<table class="render-table form-table">
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e( 'Licenses', 'Render' ); ?>
						</th>
						<td>
							<?php
							$i = 0;
							foreach ( $licenses as $extension => $license_info ) :
								$i ++;
								?>
								<p>
									<label>
										<strong>
											<?php echo $license_info['label']; ?>
										</strong>

										<br/>

										<input id="<?php echo $extension; ?>_license_key"
										       name="<?php echo $extension; ?>_license_key" type="text"
										       class="regular-text"
										       value="<?php esc_attr_e( $license_info['key'] ); ?>"/>
									</label>

									<?php if ( ! empty( $license_info['key'] ) ) { ?>

										<?php if ( $license_info['status'] == 'valid' ) { ?>

											<span class="render-license-status valid">
											<span class="dashicons dashicons-yes"></span>
												<?php _e( 'active', 'Render' ); ?>
										</span>

											<input type="submit" class="button-secondary button-red"
											       name="<?php echo $extension; ?>_license_deactivate"
											       value="<?php _e( 'Deactivate License', 'Render' ); ?>"/>

										<?php } else { ?>

											<span class="render-license-status invalid">
											<span class="dashicons dashicons-no"></span>
												<?php _e( 'inactive', 'Render' ); ?>
										</span>

											<input type="submit" class="button-secondary"
											       name="<?php echo $extension; ?>_license_activate"
											       value="<?php _e( 'Activate License', 'Render' ); ?>"/>

										<?php } ?>
									<?php } ?>
								</p>

								<?php if ( $i < count( $licenses ) ) : ?>
								<hr/>
							<?php endif; ?>

							<?php endforeach; ?>

							<?php wp_nonce_field( 'render_licensing_nonce', 'render_licensing_nonce' ); ?>
						</td>
					</tr>
					<tr valign="top">
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