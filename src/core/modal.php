<?php
// TODO Collapse / Expand animation for shortcodes
class USL_Modal {

	public function __construct() {

		add_action( 'usl_localized_data', array( __CLASS__, 'localize_shortcodes' ) );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );

		add_action( 'admin_print_footer_scripts', array( __CLASS__, 'output' ) );
	}

	public static function admin_scripts() {

		global $wp_scripts;
		$jquery_ui = $wp_scripts->registered['jquery-ui-core'];

		// Allow WP accordion functionality for our shortcode list
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style(
			'jquery-ui',
			"http://ajax.googleapis.com/ajax/libs/jqueryui/$jquery_ui->ver/themes/ui-lightness/jquery-ui.min.css",
			null,
			$jquery_ui->ver
		);
	}

	public static function localize_shortcodes( $data ) {

		$all_shortcodes = _usl_get_merged_shortcodes();

		$data['all_shortcodes'] = $all_shortcodes;

		return $data;
	}

	private static function atts_loop( $shortcode_atts, $advanced = false, $wrapping = false ) {

		foreach ( $shortcode_atts as $att_name => $att ) :

			/**
			 * Allows the filtering of the current att in the loop.
			 *
			 * @since USL 0.1.0
			 */
			$att = apply_filters( 'usl_att_loop', $att, $att_name, $advanced, $wrapping );

			if ( ( ! $advanced && ! isset( $att['advanced'] ) ) || $advanced && isset( $att['advanced'] ) ) :
				$type = null;
				if ( isset( $att['slider'] ) ) {
					$type = 'slider';
				} elseif ( isset( $att['colorpicker'] ) ) {
					$type = 'colorpicker';
				} elseif ( isset( $att['selectbox'] ) ) {
					$type = 'selectbox';
				} else {
					$type = 'textbox';
				}

				// Validation
				if ( ! isset( $att['validate'] ) ) {
					$att['validate'] = array();
				}
				$att['validate'] = implode( ',', $att['validate'] );

				// Sanitation
				if ( ! isset( $att['sanitize'] ) ) {
					$att['sanitize'] = array();
				}
				$att['sanitize'] = implode( ',', $att['sanitize'] );
				?>
				<div class="usl-modal-att-row" data-att-type="<?php echo $type; ?>"
				     data-required="<?php echo $att['required']; ?>"
				     data-validate="<?php echo $att['validate']; ?>"
				     data-sanitize="<?php echo $att['sanitize']; ?>">
					<div class="usl-modal-att-name">
						<?php echo _usl_translate_id_to_name( $att_name ); ?>
					</div>
					<div class="usl-modal-att-field">

						<?php if ( isset( $att['slider'] ) ) : ?>

							<?php
							// Default value support
							if ( isset( $att['default'] ) ) {
								$att['slider']['value'] = $att['default'];
							}

							$data = '';
							foreach ( $att['slider'] as $data_name => $data_value ) {
								$data .= " data-$data_name='$data_value'";
							}
							?>
							<input type="text" class="usl-modal-att-slider-value usl-modal-att-input"
							       value="<?php echo isset( $att['default'] ) ? $att['default'] : '0'; ?>"
							       name="<?php echo $att_name; ?>"/>
							<div class="usl-modal-att-slider" <?php echo $data; ?>></div>

						<?php elseif ( isset( $att['colorpicker'] ) ) : ?>

							<input type="text"
							       value="<?php echo isset( $att['default'] ) ? $att['default'] : ''; ?>"
							       class="usl-modal-att-colorpicker usl-modal-att-input"
							       name="<?php echo $att_name; ?>"/>

						<?php
						elseif ( isset( $att['selectbox'] ) ) : ?>

							<select name="<?php echo $att_name; ?>" class="usl-modal-att-input">
								<option value="">Select One</option>
								<?php foreach ( $att['selectbox'] as $att_value ) : ?>
									<option
										value="<?php echo $att_value; ?>">
										<?php echo ucfirst( $att_value ); ?>
									</option>
								<?php endforeach; ?>
							</select>

						<?php
						elseif ( isset( $att['textarea'] ) ) : ?>

							<textarea class="usl-modal-att-input" name="<?php echo $att_name; ?>"><?php echo isset( $att['default_value'] ) ? $att['default_value'] : ''; ?></textarea>

						<?php
						else: ?>

							<input type="text" class="usl-modal-att-input" value='' name="<?php echo $att_name; ?>"/>

						<?php endif; ?>

						<div class="usl-modal-att-errormsg"></div>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach;
	}

	public function output() {

		$all_shortcodes = _usl_get_merged_shortcodes();

		// Setup categories
		$categories = array(
			'all',
		);
		foreach ( $all_shortcodes as $shortcode ) {

			// Add a category if it's set, not empty, and doesn't already exist in our $categories array
			if ( ! empty( $shortcode['category'] ) && ! in_array( $shortcode['category'], $categories ) ) {
				$categories[] = $shortcode['category'];
			}
		}
		?>
		<div id="usl-modal-backdrop"></div>
		<div id="usl-modal-wrap" style="display: none;">
			<div class="usl-modal-title">
				Shortcodes
				<button type="button" class="usl-modal-close">
					<span class="screen-reader-text">Close</span>
				</button>
			</div>

			<div class="usl-modal-body">
				<div class="usl-modal-search">
					<input type="text" name="usl-modal-search" placeholder="Search"/>
					<span class="dashicons dashicons-search"></span>
				</div>

				<div class="usl-modal-categories">
					<ul>
						<?php if ( ! empty( $categories ) ) : ?>
							<?php foreach ( $categories as $category ) : ?>
								<li data-category="<?php echo $category; ?>">
									<?php // TODO unique icons ?>
									<span class="dashicons dashicons-admin-generic"></span>
									<br/>
									<?php echo ucwords( $category ); ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
				</div>
				<p class="dashicons dashicons-leftright"></p>

				<div class="usl-modal-shortcodes-container">

					<ul class="usl-modal-shortcodes accordion-container">
						<?php if ( ! empty( $all_shortcodes ) ) : ?>
							<?php foreach ( $all_shortcodes as $code => $shortcode ) :
								$wrapping = isset( $shortcode['wrapping'] ) && $shortcode['wrapping'] ? true : false;

								/**
								 * Allows the filtering of the list of atts for the current shortcode.
								 *
								 * @since USL 0.1.0
								 */
								$shortcode['atts'] = apply_filters( 'usl_att_pre_loop', $shortcode['atts'], $wrapping );
								?>
								<li data-category="<?php echo isset( $shortcode['category'] ) ? $shortcode['category'] : 'other'; ?>"
								    data-code="<?php echo $code; ?>"
								    class="<?php echo ! empty( $shortcode['atts'] ) ? 'accordion-section' : ''; ?> usl-modal-shortcode">

									<form class="usl-modal-shortcode-form">

										<div
											class="<?php echo ! empty( $shortcode['atts'] ) ? 'accordion-section' : 'usl-modal-sc'; ?>-title">
											<div class="usl-modal-shortcode-title">
												<?php echo $shortcode['title']; ?>
											</div>

											<div class="usl-modal-shortcode-description">
												<?php echo $shortcode['description'] ? $shortcode['description'] : 'No description'; ?>
											</div>
											<div style="clear: both; display: table;"></div>
										</div>

										<?php if ( ! empty( $shortcode['atts'] ) ): ?>
											<div class="accordion-section-content usl-modal-atts">

												<?php self::atts_loop( $shortcode['atts'], false, $wrapping ); ?>

												<?php
												// Figure out if any of the attributes are belong to the advanced section
												$advanced = false;
												foreach ( $shortcode['atts'] as $_shortcode ) {
													$advanced = array_key_exists( 'advanced', $_shortcode ) ? true : false;
													if ( $advanced ) {
														break;
													}
												}
												if ( $advanced ) :
													?>
													<a href="#" class="usl-modal-show-advanced-atts">Show advanced options</a>
													<div class="usl-modal-advanced-atts" style="display: none;">
														<?php self::atts_loop( $shortcode['atts'], true, $wrapping); ?>
													</div>
												<?php endif; ?>
											</div>
										<?php endif; ?>
									</form>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
					<div class="usl-modal-shortcodes-spinner spinner"></div>
				</div>
			</div>

			<div class="usl-modal-footer">
				<div class="usl-modal-cancel">
					<a class="submitdelete deletion" href="#">Cancel</a>
				</div>
				<div class="usl-modal-update">
					<input type="submit" value="Add Shortcode" class="button button-primary" id="usl-modal-submit"
					       name="usl-modal-submit">

					<input type="submit" value="Remove Shortcode" class="button-secondary delete" id="usl-modal-remove" />
					<?php do_action( 'usl_modal_action_area' ); ?>
				</div>
			</div>
		</div>
	<?php
	}
}