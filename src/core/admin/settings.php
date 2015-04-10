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
class Render_AdminPage_Settings {

	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );

		// Download debug file
		if ( isset( $_GET['render_download_debug'] ) ) {
			add_action( 'admin_init', array( $this, 'download_debug_log' ) );
		}
	}

	/**
	 * Loads initial notices for this Settings page.
	 *
	 * @since {{VERSION}}
	 */
	function _initial_notices() {

		global $Render;

		$notices = array();

		// Add notices for integration plugins
		if ( $Render->integrations instanceof Render_Integrations && ! empty( $Render->integrations->available_integrations ) ) {

			foreach ( (array) $Render->integrations->available_integrations as $plugin => $integration ) {

				if ( is_plugin_active( $integration['name'] ) &&
				     ! is_plugin_active( $integration['render_name'] )
				) {
					$notices[] = array(
						'ID'          => "render-integration-notice-$plugin-persistent",
						'message'     => sprintf(
							__( 'Render has an integration available for %s. You can get it %shere%s. %s shortcodes will not work as well as they could in Render without the integration.', 'Render' ),
							"<strong>$integration[title]</strong>",
							"<a href=\"$integration[link]\" target=\"_blank\">",
							'</a>',
							"<strong>$integration[title]</strong>"
						),
					);
				}

				$Render->notices->remove( "render-integration-notice-$plugin" );
			}
		}

		if ( ! empty ( $notices ) ) {
			foreach ( (array) $notices as $notice ) {

				$notice = wp_parse_args( $notice, array(
					'message'     => '',
					'type'        => 'error',
					'ID'          => false,
					'hide_button' => false,
				) );

				$Render->notices->add( $notice['ID'], $notice['message'], $notice['type'], $notice['hide_button'] );
			}
		}
	}

	/**
	 * Function for the admin menu to create a menu item in the settings tree.
	 *
	 * @since 1.0.0
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

		add_action( "load-$hook", array( $this, 'page_specific' ) );
	}

	/**
	 * Only fires on this settings page.
	 *
	 * @since {{VERSION}}
	 */
	public function page_specific() {

		// Load up any notices
		$this->_initial_notices();
	}

	/**
	 * Registers Render settings for the options page.
	 *
	 * @since 1.0.0
	 */
	public static function register_settings() {

		register_setting( 'render_options', 'render_render_visual' );
		register_setting( 'render_options', 'render_delete_on_uninstall' );
		register_setting( 'render_options', 'render_allow_tracking' );

		// TinyMCE
		/** This filter is documented in src/core/licensing/settings.php */
		$disabled_tinymce_buttons = apply_filters( 'render_disabled_tinymce_buttons', array() );

		// Register all TinyMCE disabled buttons
		foreach ( (array) $disabled_tinymce_buttons as $button_ID => $button_label ) {
			register_setting( 'render_options', "render_enable_tinymce_button_$button_ID" );
		}

		// Licensing

		/** This filter is documented in src/core/licensing/settings.php */
		$extension_licenses = apply_filters( 'render_licensing_extensions', array() );

		// Register all license settings
		foreach ( (array) $extension_licenses as $extension => $label ) {

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
	 * Sets the download headers and outputs debugging information.
	 *
	 * @since {{VERSION}}
	 */
	public function download_debug_log() {

		header('Content-Type: text/plain'); // you can change this based on the file type
		header('Content-Disposition: attachment; filename="render-debug.txt"');

		include __DIR__ . '/debug.php';
		exit();
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

		/**
		 * Allow extensions to disable TinyMCE buttons.
		 *
		 * @since 1.0.3
		 */
		$disabled_tinymce_buttons = apply_filters( 'render_disabled_tinymce_buttons', array() );

		// Other options
		$render_visual = get_option( 'render_render_visual', '1' );

		require( ABSPATH . 'wp-admin/options-head.php' );
		?>
		<div class="wrap render-wrap">
			<h2 class="render-page-title">
				<img src="<?php echo RENDER_URL; ?>/assets/images/render-logo.svg" class="render-page-title-logo"
				     onerror="this.src='<?php echo RENDER_URL; ?>/assets/images/render-header-logo.png';"/>
				<?php _e( 'Settings', 'Render' ); ?>
			</h2>

			<form method="post" action="options.php">

				<?php settings_fields( 'render_options' ); ?>

				<?php submit_button(); ?>

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
							<?php _e( 'Use the magical visual renderer', 'Render' ); ?>
						</th>
						<td>
							<div class="render-switch large">
								<input type="checkbox" id="render_render_visual"
								       name="render_render_visual" value="1" <?php checked( '1', $render_visual ); ?> />
								<label for="render_render_visual"></label>
							</div>
						</td>
					</tr>
					<?php if ( ! empty( $disabled_tinymce_buttons ) ) : ?>
						<tr valign="top">
							<th scope="row">
								<?php _e( 'TinyMCE buttons', 'Render' ); ?>
							</th>
							<td>
								<?php foreach ( (array) $disabled_tinymce_buttons as $button_ID => $button_label ) : ?>
									<?php $id = "render_enable_tinymce_button_$button_ID"; ?>

									<div class="render-settings-tinymce">

										<div class="render-switch toggle">

											<input type="checkbox"
											       name="<?php echo $id; ?>"
											       id="<?php echo $id; ?>"
											       value="enabled"
												<?php checked( 'enabled', get_option( $id ) ); ?> />

											<label for="<?php echo $id; ?>" class="disabled-style">

												<span class="render-modal-att-toggle-first">
													<?php _e( 'Disabled', 'Render' ); ?>
												</span>

												<span class="render-modal-att-toggle-second">
													<?php _e( 'Enabled', 'Render' ); ?>
												</span>

											</label>

										</div>

										<?php echo $button_label; ?>

									</div>

								<?php endforeach; ?>
							</td>
						</tr>
					<?php endif; ?>

				</table>

				<h3>Advanced Settings</h3>

				<table class="render-table form-table">

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Delete ALL Render data on uninstall', 'Render' ); ?>
						</th>
						<td>

							<?php $delete_on_uninstall = get_option( 'render_delete_on_uninstall' ); ?>

							<div class="render-switch">
								<input type="checkbox"
								       name="render_delete_on_uninstall"
								       id="render_delete_on_uninstall"
								       value="1"
									<?php checked( '1', $delete_on_uninstall ); ?> />

								<label for="render_delete_on_uninstall" class="disabled-style"></label>
							</div>

							<?php if ( $delete_on_uninstall ) : ?>
								<p class="description">
									<?php _e( '<strong>WARNING</strong>: if you uninstall Render, you can NOT restore saved information.', 'Render' ); ?>
								</p>
							<?php endif; ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Allow anonymous data tracking', 'Render' ); ?>
						</th>
						<td>

							<?php $allow_tracking = get_option( 'render_allow_tracking' ); ?>

							<div class="render-switch">
								<input type="checkbox"
								       name="render_allow_tracking"
								       id="render_allow_tracking"
								       value="1"
									<?php checked( '1', $allow_tracking ); ?> />

								<label for="render_allow_tracking" class="disabled-style"></label>
							</div>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Debugging Information', 'Render' ); ?>
						</th>
						<td>
							<a href="<?php echo add_query_arg('render_download_debug', 'true'); ?>" class="button">Download</a>
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