<?php

class USL_Modal {

	public function __construct() {

		add_action( 'usl_localized_data', array( __CLASS__, 'localize_shortcodes' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );
		add_action( 'admin_footer', array( __CLASS__, 'output' ) );
	}

	public static function admin_scripts() {

		global $wp_scripts;
		$jquery_ui = $wp_scripts->registered['jquery-ui-core'];

		// Allow WP accordion functionality for our shortcode list
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'jquery-effects-shake' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'usl-chosen' );

		wp_enqueue_style( 'usl-chosen' );
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

		foreach ( $shortcode_atts as $att_id => $att ) :

			/**
			 * Allows the filtering of the current att in the loop.
			 *
			 * @since USL 1.0.0
			 */
			$att = apply_filters( 'usl_att_loop', $att, $att_id, $advanced, $wrapping );

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
						<?php echo $att['label']; ?>
					</div>
					<div class="usl-modal-att-field">

						<?php
						// Output the att field
						if ( isset( $att['textarea'] ) ) {
							self::att_type_textarea( $att_id, $att, $att['textarea'] );
						} elseif ( isset( $att['selectbox'] ) ) {
							self::att_type_selectbox( $att_id, $att, $att['selectbox'] );
						} elseif ( isset( $att['slider'] ) ) {
							self::att_type_slider( $att_id, $att, $att['slider'] );
						} elseif ( isset( $att['colorpicker'] ) ) {
							self::att_type_colorpicker( $att_id, $att, $att['colorpicker'] );
						} else {
							self::att_type_textbox( $att_id, $att, isset( $att['textbox'] ) ? $att['textbox'] : array() );
						}
						?>

						<div class="usl-modal-att-errormsg"></div>

						<?php if ( isset( $att['description'] ) ) : ?>
							<p class="description">
								<?php echo $att['description']; ?>
							</p>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach;
	}

	private static function att_type_textbox( $att_name, $att, $properties = array() ) {
		?>
		<input type="text" class="usl-modal-att-input"
		       placeholder="<?php echo isset( $properties['placeholder'] ) ? $properties['placeholder'] : ''; ?>"
		       value="<?php echo isset( $att['default'] ) ? $att['default'] : ''; ?>"
		       name="<?php echo $att_name; ?>"/>
	<?php
	}

	private static function att_type_textarea( $att_name, $att ) {
		?>
		<textarea class="usl-modal-att-input" name="<?php echo $att_name; ?>"><?php
			echo isset( $att['default'] ) ? $att['default_value'] : '';
			?></textarea>
	<?php
	}

	private static function att_type_selectbox( $att_name, $att, $properties ) {

		// If a callback is provided, use that to populate options
		if ( isset( $properties['callback'] ) && is_callable( $properties['callback'] ) ) {
			$options = call_user_func( $properties['callback'] );
		}

		if ( ! empty( $options ) ) {
			if ( ! empty( $properties['options'] ) ) {
				$properties['options'] = array_merge( $options, $properties['options'] );
			} else {
				$properties['options'] = $options;
			}
		}

		if ( empty( $properties['options'] ) ) {
			echo 'No options!';
			return;
		}

		// Chosen support
		if ( ! isset( $properties['disableChosen'] ) ) {
			$chosen = 'chosen' . ( isset( $properties['allowCustomInput'] ) ? ' allow-custom-input' : '' );
		} else {
			$chosen = '';
		}

		if ( isset( $properties['allowCustomInput'] ) && ! isset( $att['description'] ) ) {
			$att['description'] = 'Custom input is allowed.';
		}
		?>

		<select name="<?php echo $att_name; ?>"
		        data-placeholder="<?php echo isset( $properties['placeholder'] ) ? $properties['placeholder'] : 'Select one'; ?>"
		        class="usl-modal-att-input <?php echo $chosen; ?>">

			<?php // Necessary for starting with nothing selected ?>
			<option></option>

			<?php
			// If this array is 2 levels deep, we have optgroups
			$optgroup = false;
			foreach ( $properties['options'] as $maybe_optgroup => $vals ) {
				if ( gettype( $vals ) === 'array' && ! isset( $vals['label'] ) ) {
					$optgroup = true;
					break;
				}
			}

			// No-optgroup support
			if ( ! $optgroup ) {
				$properties['options'] = array(
					'' => $properties['options'],
				);
			}
			?>

			<?php foreach ( $properties['options'] as $opt_group => $options ) : ?>

				<?php if ( ! empty( $opt_group ) ) : ?>
					<optgroup label="<?php echo $opt_group; ?>">
				<?php endif; ?>

				<?php foreach ( $options as $option_value => $option ) : ?>
					<?php
					$option_name = gettype( $option ) === 'array' ? $option['label'] : $option;
					?>
					<option
						<?php echo isset( $option['icon'] ) ?
							"data-icon='$option[icon]'" : ''; ?>
						value="<?php echo $option_value; ?>"
						<?php selected( $option_value, isset( $properties['default'] ) ? $properties['default'] : '' ); ?>
						>
						<?php echo $option_name; ?>
					</option>
				<?php endforeach; ?>

				<?php if ( ! empty( $opt_group ) ) : ?>
					</optgroup>
				<?php endif; ?>

			<?php endforeach; ?>

		</select>
	<?php
	}

	private static function att_type_slider( $att_name, $att, $properties ) {

		// Default value support
		if ( isset( $att['default'] ) ) {
			$properties['value'] = $att['default'];
		}

		$data = '';
		foreach ( $properties as $data_name => $data_value ) {
			$data .= " data-$data_name='$data_value'";
		}
		?>
		<input type="text" class="usl-modal-att-slider-value usl-modal-att-input"
		       value="<?php echo isset( $att['default'] ) ? $att['default'] : '0'; ?>"
		       name="<?php echo $att_name; ?>"/>
		<div class="usl-modal-att-slider" <?php echo $data; ?>></div>
	<?php
	}

	private static function att_type_colorpicker( $att_name, $att ) {
		?>
		<input type="text"
		       value="<?php echo isset( $att['default'] ) ? $att['default'] : ''; ?>"
		       class="usl-modal-att-colorpicker usl-modal-att-input"
		       name="<?php echo $att_name; ?>"/>
	<?php
	}

	public static function output() {

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

		$category_icons = apply_filters( 'usl_modal_category_icons', array(
			'all'    => 'dashicons-tagcloud',
			'design' => 'dashicons-admin-appearance',
			'post'   => 'dashicons-admin-post',
			'site'   => 'dashicons-admin-home',
			'time'   => 'dashicons-clock',
			'user'   => 'dashicons-admin-users',
			'media'  => 'dashicons-admin-media',
			'logic'  => 'dashicons-randomize',
		) );
		?>
		<div id="usl-modal-backdrop"></div>
		<div id="usl-modal-wrap" style="display: none;">
			<div class="usl-modal-title">
				<?php _e( 'Shortcodes', 'USL' ); ?>
				<button type="button" class="usl-modal-close">
					<span class="screen-reader-text"><?php _e( 'Close', 'USL' ); ?></span>
				</button>
			</div>

			<div class="usl-modal-body">
				<div class="usl-modal-search">
					<input type="text" name="usl-modal-search"
					       placeholder="<?php _e( 'Search by name, description, code, category, or source', 'USL' ); ?>"/>
					<span class="dashicons dashicons-search"></span>

					<div class="usl-modal-invalidsearch" style="display: none;">
						<?php _e( 'Sorry, but you can\'t search for that.', 'USL' ); ?>
					</div>
				</div>

				<div class="usl-modal-categories">
					<div class="usl-modal-categories-left dashicons dashicons-arrow-left-alt2"></div>
					<ul>
						<?php if ( ! empty( $categories ) ) : ?>
							<?php $i = 0; ?>
							<?php foreach ( $categories as $category ) : ?>
								<?php $i ++; ?>
								<li data-category="<?php echo $category; ?>"
								    class="<?php echo $i === 1 ? 'active' : ''; ?>">
									<span class="dashicons <?php echo isset( $category_icons[ $category ] ) ?
										$category_icons[ $category ] : 'dashicons-admin-generic'; ?>"></span>
									<br/>
									<?php echo ucwords( $category ); ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
					<div class="usl-modal-categories-right dashicons dashicons-arrow-right-alt2"></div>
				</div>

				<div class="usl-modal-shortcodes-container">

					<ul class="usl-modal-shortcodes accordion-container">
						<?php if ( ! empty( $all_shortcodes ) ) : ?>
							<?php foreach ( $all_shortcodes as $code => $shortcode ) :
								$wrapping = isset( $shortcode['wrapping'] ) && $shortcode['wrapping'] ? true : false;

								/**
								 * Allows the filtering of the list of atts for the current shortcode.
								 *
								 * @since USL 1.0.0
								 */
								$shortcode['atts'] = apply_filters( 'usl_att_pre_loop', $shortcode['atts'], $wrapping );
								?>
								<li data-category="<?php echo isset( $shortcode['category'] ) ?
									$shortcode['category'] : 'other'; ?>"
								    data-code="<?php echo $code; ?>"
								    data-source="<?php echo $shortcode['source']; ?>"
								    class="<?php echo ! empty( $shortcode['atts'] ) ? 'accordion-section' : ''; ?>
								    usl-modal-shortcode">

									<form class="usl-modal-shortcode-form">

										<div
											class="<?php echo ! empty( $shortcode['atts'] ) ?
												'accordion-section' : 'usl-modal-sc'; ?>-title">
											<div class="usl-modal-shortcode-title">
												<?php echo $shortcode['title']; ?>
												<br/>
												<span class="usl-modal-shortcode-source">
													<?php echo $shortcode['source']; ?>
												</span>
											</div>

											<div class="usl-modal-shortcode-description">
												<?php echo $shortcode['description'] ?
													$shortcode['description'] : 'No description'; ?>
											</div>
											<div style="clear: both; display: table;"></div>
										</div>

										<?php if ( ! empty( $shortcode['atts'] ) ): ?>
											<div class="accordion-section-content usl-modal-atts">

												<?php self::atts_loop( $shortcode['atts'], false, $wrapping ); ?>

												<?php
												// Figure out if any of the attributes belong to the advanced section
												$advanced = false;
												foreach ( $shortcode['atts'] as $_shortcode ) {
													$advanced = array_key_exists( 'advanced', $_shortcode ) ? true : false;
													if ( $advanced ) {
														break;
													}
												}
												if ( $advanced ) :
													?>
													<a href="#" class="usl-modal-show-advanced-atts">
														<?php _e( 'Show advanced options', 'USL' ); ?>
													</a>
													<div class="usl-modal-advanced-atts" style="display: none;">
														<?php self::atts_loop( $shortcode['atts'], true, $wrapping ); ?>
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
					<a class="submitdelete deletion" href="#"><?php _e( 'Cancel', 'USL' ); ?></a>
				</div>
				<div class="usl-modal-update">
					<input type="submit" value="<?php _e( 'Add Shortcode', 'USL' ); ?>" class="button button-primary"
					       id="usl-modal-submit" name="usl-modal-submit">

					<input type="submit" value="<?php _e( 'Remove Shortcode', 'USL' ); ?>" class="button-secondary delete"
					       id="usl-modal-remove"/>
					<?php do_action( 'usl_modal_action_area' ); ?>
				</div>
			</div>
		</div>
	<?php
	}
}