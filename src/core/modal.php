<?php

class Render_Modal {

	public static $types = array(
		'textbox',
		'textarea',
		'checkbox',
		'selectbox',
		'slider',
		'colorpicker',
		'repeater',
	);

	public function __construct() {

		add_action( 'render_localized_data', array( __CLASS__, 'localize_shortcodes' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );
		add_action( 'admin_footer', array( __CLASS__, 'output' ) );
	}

	public static function admin_scripts() {

		global $wp_scripts;
		$jquery_ui = $wp_scripts->registered['jquery-ui-core'];

		// Allow WP accordion functionality for our shortcode list
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'jquery-effects-shake' );
		wp_enqueue_script( 'jquery-effects-drop' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'render-chosen' );

		wp_enqueue_style( 'render-chosen' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style(
			'jquery-ui',
			"http://ajax.googleapis.com/ajax/libs/jqueryui/$jquery_ui->ver/themes/ui-lightness/jquery-ui.min.css",
			null,
			$jquery_ui->ver
		);
	}

	public static function localize_shortcodes( $data ) {

		$all_shortcodes = _render_get_merged_shortcodes();

		$data['all_shortcodes'] = $all_shortcodes;

		return $data;
	}

	private static function atts_loop( $shortcode_atts, $advanced = false, $wrapping = false ) {

		foreach ( $shortcode_atts as $att_id => $att ) {

			/**
			 * Allows the filtering of the current att in the loop.
			 *
			 * @since Render 1.0.0
			 */
			$att = apply_filters( 'render_att_loop', $att, $att_id, $advanced, $wrapping );

			if ( ( ! $advanced && ! isset( $att['advanced'] ) ) || $advanced && isset( $att['advanced'] ) ) {

				$type = isset( $att['type'] ) ? $att['type'] : 'textbox';

				// Section breaks
				if ( $type == 'section_break' ) {
					?>
					<p class="render-modal-att-section-break">
						<?php echo isset( $att['label'] ) ? $att['label'] : ''; ?>

						<span class="render-modal-att-section-break-description">
							<?php echo isset( $att['description'] ) ? $att['description'] : ''; ?>
						</span>
					</p>
					<?php
					continue;
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

				self::att_content( $att_id, $att, $type );
			}
		}
	}

	private static function att_content( $att_id, $att, $type ) {
		?>
		<div class="render-modal-att-row <?php echo isset( $att['classes'] ) ? implode( ' ', $att['classes'] ) : ''; ?>"
		     data-att-name="<?php echo $att_id; ?>"
		     data-att-type="<?php echo $type; ?>"
		     data-required="<?php echo isset( $att['required'] ) ? $att['required'] : ''; ?>"
		     data-validate="<?php echo isset( $att['validate'] ) ? $att['validate'] : ''; ?>"
		     data-sanitize="<?php echo isset( $att['sanitize'] ) ? $att['sanitize'] : ''; ?>"
		     data-disabled="<?php echo isset( $att['disabled'] ) ? $att['disabled'] : ''; ?>"
		     data-init-callback="<?php echo isset( $att['initCallback'] ) ? $att['initCallback'] : ''; ?>"
		     data-no-init="<?php echo isset( $att['noInit'] ) ? $att['noInit'] : ''; ?>">

			<?php if ( isset( $att['label'] ) ) : ?>
				<div class="render-modal-att-name">
					<?php echo $att['label']; ?>
				</div>
			<?php endif; ?>

			<div class="render-modal-att-field">

				<?php
				// Output the att field
				$callback = isset( $att['callback'] ) ? $att['callback'] : array( __CLASS__, "att_type_$type" );
				if ( is_callable( $callback ) ) {
					call_user_func(
						$callback,
						$att_id,
						$att,
						isset( $att['properties'] ) ? $att['properties'] : array()
					);
				} else {
					echo 'ERROR: Not a valid attribute type!';
				}
				?>

				<div class="render-modal-att-errormsg"></div>

				<?php if ( isset( $att['description'] ) ) : ?>
					<p class="render-modal-att-description">
						<?php echo $att['description']; ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
	<?php
	}

	private static function att_type_textbox( $att_id, $att, $properties = array() ) {
		?>
		<input type="text" class="render-modal-att-input render-modal-att-textbox"
		       placeholder="<?php echo isset( $properties['placeholder'] ) ? $properties['placeholder'] : ''; ?>"
		       value="<?php echo isset( $att['default'] ) ? $att['default'] : ''; ?>"
		       name="<?php echo $att_id; ?>"/>
	<?php
	}

	private static function att_type_checkbox( $att_id, $att, $properties = array() ) {
		?>
		<input type="checkbox" class="render-modal-att-input render-modal-att-checkbox"
		       name="<?php echo $att_id; ?>"
		       value="<?php echo isset( $properties['value'] ) ? $properties['value'] : ''; ?>"><?php
	}

	private static function att_type_textarea( $att_id, $att ) {
		?>
		<textarea class="render-modal-att-input render-modal-att-textarea" name="<?php echo $att_id; ?>"><?php
			echo isset( $att['default'] ) ? $att['default_value'] : '';
			?></textarea>
	<?php
	}

	private static function att_type_selectbox( $att_id, $att, $properties ) {

		// If a callback is provided, use that to populate options
		if ( isset( $properties['callback'] ) && is_callable( $properties['callback']['function'] ) ) {
			$options = call_user_func( $properties['callback']['function'] );
		}

		if ( ! empty( $options ) ) {

			$which = isset( $properties['callback']['groups'] ) ? 'groups' : 'options';
			if ( ! empty( $properties[ $which ] ) ) {
				$properties[ $which ] = array_merge( $options, $properties[ $which ] );
			} else {
				$properties[ $which ] = $options;
			}
		}

		if ( empty( $properties['options'] ) && empty( $properties['groups'] ) ) {
			echo 'No options!';

			return;
		}

		// Optgroup support
		if ( ! isset( $properties['groups'] ) ) {

			$properties['groups'] = array(
				0 => array(
					'options' => $properties['options'],
				),
			);
		}

		// Chosen support
		if ( ! isset( $properties['disableChosen'] ) ) {
			$chosen = 'chosen' . ( isset( $properties['allowCustomInput'] ) ? ' allow-custom-input' : '' );
			$chosen .= isset( $properties['allowIcons'] ) ? ' allow-icons' : '';
		} else {
			$chosen = '';
		}

		if ( isset( $properties['allowCustomInput'] ) && ! isset( $att['description'] ) ) {
			$att['description'] = 'Custom input is allowed.';
		}
		?>

		<select name="<?php echo $att_id; ?>"
		        data-placeholder="<?php echo isset( $properties['placeholder'] ) ? $properties['placeholder'] : 'Select an option'; ?>"
		        class="render-modal-att-input <?php echo $chosen; ?>"
			<?php echo isset( $properties['multi'] ) ? 'multiple' : ''; ?>>

			<?php // Necessary for starting with nothing selected ?>
			<option></option>

			<?php foreach ( $properties['groups'] as $opt_group ) : ?>

				<?php if ( isset( $opt_group['label'] ) ) : ?>
					<optgroup label="<?php echo $opt_group['label']; ?>">
				<?php endif; ?>

				<?php foreach ( $opt_group['options'] as $option_value => $option ) : ?>
					<?php
					// Simple format support
					if ( ! is_array( $option ) ) {
						$option_label = $option;
						$option       = array(
							'label' => $option_label,
						);
					}
					?>
					<option
						<?php echo isset( $option['icon'] ) ?
							"data-icon='$option[icon]'" : ''; ?>
						value="<?php echo $option_value; ?>"
						<?php selected( $option_value, isset( $properties['default'] ) ? $properties['default'] : '' ); ?>
						>
						<?php echo ! isset( $option['label'] ) ? 'MOTHER EFF' : ''; ?>
						<?php echo $option['label']; ?>
					</option>
				<?php endforeach; ?>

				<?php if ( isset( $opt_group['label'] ) ) : ?>
					</optgroup>
				<?php endif; ?>

			<?php endforeach; ?>

		</select>
	<?php
	}

	private static function att_type_slider( $att_id, $att, $properties ) {

		// FIXME range not showing

		// Establish defaults
		$defaults   = array(
			'value' => isset( $att['default'] ) ? $att['default'] : 0,
			'min'   => 0,
			'max'   => 100,
			'step'  => 1,
		);
		$properties = wp_parse_args( $properties, $defaults );

		// If range slider
		if ( isset( $properties['range'] ) ) {
			$properties['values'] = isset( $properties['values'] ) ? $properties['values'] : '0-20';
			unset( $properties['value'] );
		}

		// Prepare data for the slider
		$data = '';
		foreach ( $properties as $data_name => $data_value ) {
			$data .= " data-$data_name='$data_value'";
		}

		if ( isset( $properties['range'] ) ) :

			$values = explode( '-', $properties['values'] );
			?>
			<input type="hidden" class="render-modal-att-slider-value render-modal-att-input"
			       value="<?php echo $properties['values']; ?>"
			       name="<?php echo $att_id; ?>"/>

			<div class="render-modal-att-slider-range-text">
				<span class="render-modal-att-slider-range-text-value1"><?php echo $values[0]; ?></span>
				&nbsp;-&nbsp;
				<span class="render-modal-att-slider-range-text-value2"><?php echo $values[1]; ?></span>
			</div>
		<?php else: ?>

			<input type="text" class="render-modal-att-slider-value render-modal-att-input"
			       value="<?php echo isset( $properties['value'] ) ? $properties['value'] : '0'; ?>"
			       name="<?php echo $att_id; ?>"/>
		<?php endif; ?>
		<div class="render-modal-att-slider" <?php echo $data; ?>></div>
	<?php
	}

	private static function att_type_colorpicker( $att_id, $att, $properties ) {

		?>
		<input type="text"
		       value="<?php echo isset( $att['default'] ) ? $att['default'] : '#bada55'; ?>"
		       class="render-modal-att-colorpicker render-modal-att-input"
		       name="<?php echo $att_id; ?>"/>
	<?php
	}

	private static function att_type_counter( $att_id, $att, $properties ) {

		// TODO Unit type

		// Establish defaults
		$defaults   = array(
			'min'        => 0,
			'max'        => 1000,
			'step'       => 1,
			'shift_step' => 10,
			'unit'       => false,
		);
		$properties = wp_parse_args( $properties, $defaults );

		$default = isset( $att['default'] ) ? $att['default'] : $properties['min'];
		?>
		<div class="render-modal-counter-container">
			<div class="render-modal-counter-down render-modal-button dashicons dashicons-minus"></div>

			<input type="text"
			       value="<?php echo $default; ?>"
			       class="render-modal-att-counter render-modal-att-input"
			       name="<?php echo $att_id; ?>"
			       data-min="<?php echo $properties['min']; ?>"
			       data-max="<?php echo $properties['max']; ?>"
			       data-step="<?php echo $properties['step']; ?>"
			       data-shift-step="<?php echo $properties['shift_step']; ?>"
				/>

			<div class="render-modal-counter-up render-modal-button dashicons dashicons-plus"></div>
		</div>

		<?php if ( ( $unit = $properties['unit'] ) !== false ) : ?>

			<div class="render-modal-counter-unit">

				<?php if ( isset( $unit['allowed'] ) ) : ?>

					<select data-placeholder="<?php _e( 'Unit', 'Render' ); ?>">
						<option></option>
						<?php foreach ( $unit['allowed'] as $unit_value => $unit_label ) :
							$unit_value = is_int( $unit_value ) ? $unit_label : $unit_value;
							$selected   = isset( $unit['default'] ) ? selected( $unit_value, $unit['default'], false ) : '';
							?>
							<option value="<?php echo $unit_value; ?>" <?php echo $selected; ?>>
								<?php echo $unit_label; ?>
							</option>
						<?php endforeach; ?>
					</select>

				<?php else: ?>

					<input type="text" class="render-modal-counter-unit-input" name="<?php echo "{$att_id}_unit"; ?>"
					       value="<?php echo $unit['default']; ?>"/>

				<?php endif; ?>
			</div>
		<?php endif;
	}

	private static function att_type_repeater( $att_id, $att, $properties ) {

		// Setup defaults
		$defaults   = array(
			'fields'    => false,
			'startWith' => 1,
		);
		$properties = wp_parse_args( $properties, $defaults );

		if ( ! $properties['fields'] ) {
			echo 'ERROR: No fields set!';

			return;
		}

		foreach ( $properties['fields'] as $field_ID => $field ) {
			$properties['fields'][ $field_ID ]['disabled'] = true;
		}

		for ( $i = 0; $i < intval( $properties['startWith'] ) + 1; $i ++ ) :

			// Make sure the dummy field (the first field) doesn't init any atts
			if ( $i == 0 ) {
				foreach ( $properties['fields'] as $field_ID => $field ) {
					$properties['fields'][ $field_ID ]['noInit'] = true;
				}
			} else {
				foreach ( $properties['fields'] as $field_ID => $field ) {
					unset( $properties['fields'][ $field_ID ]['noInit'] );
				}
			}
			?>
			<div class="render-modal-repeater-field <?php echo $i == 0 ? 'dummy-field' : ''; ?>"
				<?php echo $i == 0 ? 'style="display:none"' : ''; ?>>

				<?php // Dummy input to trigger field
				?>
				<input type="hidden" name="<?php echo $att_id; ?>" class="render-modal-att-input"/>

				<div class="render-modal-repeater-inputs">
					<?php self::atts_loop( $properties['fields'] ); ?>
				</div>

				<div class="render-modal-repeater-actions">
					<span class="render-modal-repeater-add render-modal-button dashicons dashicons-plus"></span>
					<span class="render-modal-repeater-remove render-modal-button dashicons dashicons-minus"></span>
				</div>
			</div>
		<?php
		endfor;
	}

	private static function att_type_media( $att_id, $att, $properties ) {

		$defaults = array(
			'type' => 'image',
		);

		$properties = wp_parse_args( $properties, $defaults );

		switch ( $properties['type'] ) :
			case 'image':
				?>
				<img src="" class="render-modal-att-media-preview-image"/>
				<?php
				break;
			case 'audio':
				// TODO Allow audio player preview here
				?>
				<code class="render-modal-att-media-preview-audio"></code>
				<?php
				break;
			case 'video':
				// TODO Allow video player preview here
				?>
				<code class="render-modal-att-media-preview-video"></code>
				<?php
				break;
		endswitch;
		?>
		<input type="button" value="Upload / Choose Media" class="render-modal-att-media-upload"
		       data-type="<?php echo $properties['type']; ?>"/>
		<input type="hidden"
		       value="<?php echo isset( $att['default'] ) ? $att['default'] : ''; ?>"
		       class="render-modal-att-media render-modal-att-input"
		       name="<?php echo $att_id; ?>"/>
	<?php
	}

	public static function output() {

		$all_shortcodes = _render_get_merged_shortcodes();

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

		$category_icons = apply_filters( 'render_modal_category_icons', array(
			'all'        => 'dashicons-tagcloud',
			'design'     => 'dashicons-admin-appearance',
			'post'       => 'dashicons-admin-post',
			'site'       => 'dashicons-admin-home',
			'time'       => 'dashicons-clock',
			'user'       => 'dashicons-admin-users',
			'media'      => 'dashicons-admin-media',
			'visibility' => 'dashicons-visibility',
		) );
		?>
		<div id="render-modal-backdrop"></div>
		<div id="render-modal-wrap" style="display: none;">
			<div class="render-modal-title">
				<span class="render-modal-logo"></span>
				<button type="button" class="render-modal-close">
					<span class="screen-reader-text"><?php _e( 'Close', 'Render' ); ?></span>
				</button>
			</div>

			<div class="render-modal-body">
				<div class="render-modal-search">
					<input type="text" name="render-modal-search"
					       placeholder="<?php _e( 'Search by name, description, code, category, or source', 'Render' ); ?>"/>
					<span class="dashicons dashicons-search"></span>

					<div class="render-modal-invalidsearch" style="display: none;">
						<?php _e( 'Sorry, but you can\'t search for that.', 'Render' ); ?>
					</div>
				</div>

				<div class="render-modal-categories">
					<div class="render-modal-categories-left dashicons dashicons-arrow-left-alt2"></div>
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
					<div class="render-modal-categories-right dashicons dashicons-arrow-right-alt2"></div>
				</div>

				<div class="render-modal-shortcodes-container">

					<ul class="render-modal-shortcodes accordion-container">
						<?php if ( ! empty( $all_shortcodes ) ) : ?>
							<?php foreach ( $all_shortcodes as $code => $shortcode ) :
								$wrapping          = isset( $shortcode['wrapping'] ) && $shortcode['wrapping'] ? true : false;

								/**
								 * Allows the filtering of the list of atts for the current shortcode.
								 *
								 * @since Render 1.0.0
								 */
								$shortcode['atts'] = apply_filters( 'render_att_pre_loop', $shortcode['atts'], $code, $shortcode );

								if ( $shortcode['noDisplay'] ) {
									continue;
								}
								?>
								<li data-category="<?php echo isset( $shortcode['category'] ) ?
									$shortcode['category'] : 'other'; ?>"
								    data-code="<?php echo $code; ?>"
								    data-title="<?php echo $shortcode['title']; ?>"
								    data-source="<?php echo $shortcode['source']; ?>"
								    data-tags="<?php echo $shortcode['tags']; ?>"
								    class="render-modal-shortcode
								    <?php echo ! empty( $shortcode['atts'] ) ? 'accordion-section' : ''; ?>
								    <?php echo $shortcode['wrapping'] ? 'wrapping' : ''; ?>">

									<div
										class="<?php echo ! empty( $shortcode['atts'] ) ?
											'accordion-section' : 'render-modal-sc'; ?>-title">
										<div class="render-modal-shortcode-title">
											<?php echo $shortcode['title']; ?>
											<br/>
												<span class="render-modal-shortcode-source">
													<?php echo $shortcode['source']; ?>
												</span>
										</div>

										<div class="render-modal-shortcode-description">
											<?php echo $shortcode['description'] ?
												$shortcode['description'] : 'No description'; ?>
										</div>
										<div style="clear: both; display: table;"></div>
									</div>

									<?php if ( ! empty( $shortcode['atts'] ) ): ?>
										<div class="accordion-section-content render-modal-atts">

											<div class="render-modal-shortcode-toolbar">
												<div class="render-modal-shortcode-toolbar-tools">
													<div class="render-modal-shortcode-toolbar-restore">
														<div class="render-modal-shortcode-toolbar-button-restore">
															<?php _e( 'Restore Shortcode', 'Render' ); ?>
														</div>

														<div
															class="render-modal-shortcode-toolbar-button-templates disabled">
															<?php _e( 'Templates (coming soon!)', 'Render' ); ?>
														</div>
													</div>
												</div>

												<div class="render-modal-shortcode-toolbar-toggle dashicons
													dashicons-arrow-down-alt2"></div>
											</div>

											<div class="render-modal-shortcode-atts">

												<?php self::atts_loop( $shortcode['atts'], $advanced = false, $wrapping ); ?>

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
													<a href="#"
													   class="render-modal-att-section-break render-modal-show-advanced-atts hidden">
														<span class="show-text">
															<?php _e( 'Show advanced options', 'Render' ); ?>
															<span class="dashicons dashicons-arrow-down"></span>
														</span>
														<span class="hide-text" style="display: none;">
															<?php _e( 'Hide advanced options', 'Render' ); ?>
															<span class="dashicons dashicons-arrow-up"></span>
														</span>
													</a>
													<div class="render-modal-advanced-atts" style="display: none;">
														<?php self::atts_loop( $shortcode['atts'], $advanced = true, $wrapping ); ?>
													</div>
												<?php endif; ?>
											</div>
										</div>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
					<div class="render-modal-shortcodes-spinner spinner"></div>
				</div>
			</div>

			<div class="render-modal-footer">
				<div class="render-modal-cancel">
					<a class="submitdelete deletion" href="#"><?php _e( 'Cancel', 'Render' ); ?></a>
				</div>
				<div class="render-modal-update">

					<div id="render-modal-submit">
						<p class="render-modal-submit-text-add" style="top: 0;"><?php // Needed for initial animation ?>
							<?php _e( 'Add Shortcode', 'Render' ); ?>
						</p>
						<br/>

						<p class="render-modal-submit-text-modify">
							<?php _e( 'Modify Current Shortcode', 'Render' ); ?>
						</p>
						<br/>

						<p class="render-modal-submit-text-change">
							<?php _e( 'Change To New Shortcode', 'Render' ); ?>
						</p>
					</div>

					<div id="render-modal-remove" class="button-secondary delete" style="display: none;">
						<?php _e( 'Remove Current Shortcode', 'Render' ); ?>
					</div>

					<?php do_action( 'render_modal_action_area' ); ?>
				</div>
			</div>
		</div>
	<?php
	}
}