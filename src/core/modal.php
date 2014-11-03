<?php

class USL_Modal {

	public function __construct() {

		add_action( 'admin_head', array( __CLASS__, 'localize_shortcodes' ), 9999 );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );

		add_action( 'admin_print_footer_scripts', array( __CLASS__, 'modal_html' ) );
	}

	public static function admin_scripts() {

		global $wp_scripts;
		$jquery_ui = $wp_scripts->registered['jquery-ui-core'];

		// Allow WP accordion functionality for our shortcode list
		wp_enqueue_script( 'accordion' );
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

	public static function localize_shortcodes() {

		$all_shortcodes = _usl_get_merged_shortcodes();

		$data['all_shortcodes'] = $all_shortcodes;

		wp_localize_script( 'common', 'USL_Data', $data );
	}

	private static function atts_loop( $shortcode_atts, $advanced = false ) {

		foreach ( $shortcode_atts as $att_name => $att ) : ?>
			<?php if ( ( ! $advanced && ! isset( $att['advanced'] ) ) || $advanced && isset( $att['advanced'] ) ) : ?>
				<div class="usl-modal-sc-att-row">
					<div class="usl-modal-sc-att-name">
						<?php echo ucfirst( $att_name ); ?>
					</div>
					<div class="usl-modal-sc-att-field"
					     data-required="<?php echo $att['required']; ?>">
						<?php if ( isset( $att['slider'] ) ) : ?>
							<?php
							$data = '';
							foreach ( $att['slider'] as $data_name => $data_value ) {
								$data .= " data-$data_name='$data_value'";
							}
							?>
							<input type="text" class="slider-value" value="0"/>
							<div class="slider" <?php echo $data; ?>></div>
						<?php elseif ( isset( $att['colorpicker'] ) ) : ?>
							<input type="text" value="#bada55" class="colorpicker" />
						<?php elseif ( isset( $att['selectbox'] ) ) : ?>
							<select name="<?php echo $att_name; ?>">
								<option value="">Select One</option>
								<?php foreach ( $att['selectbox'] as $att_value ) : ?>
									<option
										value="<?php echo $att_value; ?>">
										<?php echo ucfirst( $att_value ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						<?php
						else: ?>
							<input type="text" class="text-input" value='' name="<?php echo $att_name; ?>"/>
						<?php
						endif; ?>
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
							<?php foreach ( $all_shortcodes as $code => $shortcode ) : ?>
								<li data-category="<?php echo isset( $shortcode['category'] ) ? $shortcode['category'] : 'other'; ?>"
								    data-code="<?php echo $code; ?>"
								    class="<?php echo ! empty( $shortcode['atts'] ) ? 'accordion-section' : ''; ?>">

									<form class="usl-modal-shortcode-form">

										<div
											class="<?php echo ! empty( $shortcode['atts'] ) ? 'accordion-section' : 'usl-modal-sc'; ?>-title">
											<div class="title">
												<?php echo $shortcode['title']; ?>
											</div>

											<div class="description">
												<?php echo $shortcode['description'] ? $shortcode['description'] : 'No description'; ?>
											</div>
											<div style="clear: both; display: table;"></div>
										</div>

										<?php if ( ! empty( $shortcode['atts'] ) ): ?>
											<div class="accordion-section-content">

												<?php self::atts_loop( $shortcode['atts'] ); ?>

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
													<a href="#" class="show-advanced-atts">
														Show advanced options
													</a>
													<div class="advanced-atts" style="display: none;">
														<?php self::atts_loop( $shortcode['atts'], true ); ?>
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
				</div>
			</div>
		</div>
	<?php
	}
}