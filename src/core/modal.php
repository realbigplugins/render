<?php
// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Render_Modal
 *
 * Creates the Render modal.
 *
 * @since      1.0.0
 *
 * @package    Render
 * @subpackage Modal
 */
class Render_Modal {

	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Localize the shortcodes
		add_filter( 'render_localized_data', array( __CLASS__, 'localize_shortcodes' ) );

		// Enqueue styles and scripts for the modal
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );

		// Output the Modal HTML
		add_action( 'admin_footer', array( __CLASS__, '_modal_output' ) );

		// Add translations
		add_action( 'render_localized_data', array( __CLASS__, '_translations' ) );
	}

	/**
	 * Localizes all shortcodes for the Render Modal.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Old data to be localized to Render.
	 * @return array New data to be localized to Render.
	 */
	public static function localize_shortcodes( $data ) {

		global $Render;

		$data['all_shortcodes'] = $Render->shortcodes;

		// Also add in some extra data
		$data['sc_attr_escapes'] = $Render::$sc_attr_escapes;

		return $data;
	}

	/**
	 * Loads all required scripts and styles for the Modal.
	 *
	 * @since 1.0.0
	 *
	 * @global array $wp_scripts Contains all registered scripts.
	 */
	public static function admin_scripts() {

		// Get the version of jQuery UI for loading the matching jQuery UI stylesheets
		global $wp_scripts;

		/**
		 * The version of the jQuery UI stylesheet to load.
		 *
		 * @since 1.0.0
		 */
		$jquery_ui_version = apply_filters(
			'render_jquery_ui_style_version',
			$wp_scripts->registered['jquery-ui-core']->ver
		);

		/**
		 * The stylesheet URL for jQuery UI.
		 *
		 * @since 1.0.0
		 */
		$jquery_ui_url = apply_filters(
			'render_jquery_ui_style_url',
			"http://ajax.googleapis.com/ajax/libs/jqueryui/$jquery_ui_version/themes/ui-lightness/jquery-ui.min.css"
		);

		// Necessary scripts
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'jquery-effects-shake' );
		wp_enqueue_script( 'jquery-effects-drop' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'render-chosen' );
		wp_enqueue_script( 'render-masked-input' );
		wp_enqueue_media();

		// Necessary styles
		wp_enqueue_style( 'render-chosen' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style(
			'jquery-ui',
			$jquery_ui_url,
			null,
			$jquery_ui_version
		);
	}

	/**
	 * Provides translations for the modal.
	 *
	 * @since  1.1-beta-1
	 * @access private
	 *
	 * @param array $data The current localization data.
	 * @return array The new localization data.
	 */
	static function _translations( $data ) {

		$data['l18n']['enter_valid_email']            = __( 'Please enter a valid email address', 'Render' );
		$data['l18n']['enter_valid_url']              = __( 'Please enter a valid URL', 'Render' );
		$data['l18n']['too_many_chars']               = __( 'too many chars', 'Render' );
		$data['l18n']['too_few_chars']                = __( 'too few chars', 'Render' );
		$data['l18n']['invalid_chars']                = __( 'Invalid characters', 'Render' );
		$data['l18n']['no_numbers']                   = __( 'No numbers please', 'Render' );
		$data['l18n']['only_numbers']                 = __( 'Only numbers please', 'Render' );
		$data['l18n']['cannot_change_from_shortcode'] = __( 'Cannot change from current shortcode. Remove first.', 'Render' );
		$data['l18n']['this_field_required']          = __( 'This field is required', 'Render' );

		return $data;
	}

	/**
	 * Loops through all the current shortcode attributes and outputs them in the Modal.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param array  $shortcode_atts All of the current shortcode's attributes.
	 * @param bool   $advanced       Whether or not the shortcode should be categorized under "Advanced Atts".
	 * @param bool   $wrapping       Whether or not the shortcode wraps around content.
	 * @param string $code           The current shortcode.
	 */
	private static function _atts_loop( $shortcode_atts, $advanced = false, $wrapping = false, $code = '' ) {

		foreach ( $shortcode_atts as $att_id => $att ) {

			/**
			 * Allows the filtering of the current att in the loop.
			 *
			 * @since 1.0.0
			 */
			$att = apply_filters( 'render_att_loop', $att, $att_id, $advanced, $wrapping );

			if ( ( $advanced && $att['advanced'] ) || ( ! $advanced && ! $att['advanced'] ) ) {

				// Advanced atts can't be required
				if ( $advanced ) {
					$att['required'] = false;
				}

				// Section breaks
				if ( $att['type'] == 'section_break' ) {
					?>
					<p class="render-modal-att-section-break">
						<?php echo $att['label'] ?>

						<span class="render-modal-att-section-break-description">
							<?php echo $att['description'] ?>
						</span>
					</p>
					<?php
					continue;
				}

				// Placeholder
				if ( $att['type'] == 'placeholder' ) {
					?>
					<p class="render-modal-att-placeholder">
						<?php echo $att['label'] ?>

						<span class="render-modal-att-placeholder-description">
							<?php echo $att['description'] ?>
						</span>
					</p>
					<?php
					continue;
				}

				// Validation
				if ( ! empty( $att['validate'] ) ) {
					$att['validate'] = implode( ',', (array) $att['validate'] );
				}

				// Sanitation
				if ( ! empty( $att['sanitize'] ) ) {
					$att['sanitize'] = implode( ',', (array) $att['sanitize'] );
				}

				self::att_content( $att_id, $att, $att['type'], $code );
			}
		}
	}

	/**
	 * Outputs the HTML of each attribute.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id The ID of the attribute.
	 * @param array  $att    Attribute properties.
	 * @param string $code   The current shortcode.
	 */
	private static function att_content( $att_id, $att, $code ) {

		// Setup classes
		$att['classes'][] = 'render-modal-att-row';
		$att['classes'][] = $att['label'] === false ? 'render-modal-att-hide-label' : '';
		$att['classes'][] = $att['type'] == 'hidden' ? 'hidden' : '';
		$att['classes'][] = isset( $att['conditional']['populate'] ) ? 'render-modal-att-conditional-populate' : '';
		$att['classes']   = array_filter( $att['classes'] );

		// Setup data
		$data                  = array();
		$data['att-name']      = $att_id;
		$data['att-type']      = $att['type'];
		$data['required']      = $att['required'] !== false ? 'true' : 'false';
		$data['validate']      = $att['validate'] !== false ? 'true' : 'false';
		$data['sanitize']      = $att['sanitize'] !== false ? 'true' : 'false';
		$data['init-callback'] = $att['initCallback'] !== false ? $att['initCallback'] : 'false';
		$data['no-init']       = $att['noInit'] !== false ? 'true' : 'false';

		$data_output = '';
		foreach ( $data as $data_name => $data_value ) {
			$data_output .= " data-$data_name=\"$data_value\"";
		}

		// Repeater should have description above always
		if ( $att['type'] == 'repeater' ) {
			$att['descriptionAbove'] = true;
			$att['descriptionBelow'] = false;
		}
		?>
		<div class="<?php echo implode( ' ', $att['classes'] ); ?>" <?php echo $data_output; ?>>

			<?php if ( ! empty( $att['label'] ) ) : ?>
				<div class="render-modal-att-name">
					<?php echo $att['label']; ?>
				</div>
			<?php endif; ?>

			<?php if ( $att['descriptionAbove'] && $att['description'] !== false ) : ?>
				<p class="render-modal-att-description">
					<?php echo $att['description']; ?>
				</p>
			<?php endif; ?>

			<div class="render-modal-att-field">

				<?php
				// Output the att field
				$callback = $att['callback'] !== false ? $att['callback'] : array( __CLASS__, "att_type_$att[type]" );
				if ( is_callable( $callback ) ) {
					call_user_func(
						$callback,
						$att_id,
						$att,
						$att['properties'],
						$code
					);
				} else {
					echo 'ERROR: Not a valid attribute type!';
				}
				?>

				<?php if ( $att['descriptionBelow'] && $att['description'] !== false ) : ?>
					<p class="render-modal-att-description">
						<?php echo $att['description']; ?>
					</p>
				<?php endif; ?>

				<div class="render-modal-att-errormsg"></div>
			</div>
		</div>
	<?php
	}

	/**
	 * Outputs the field HTML of the hidden attribute.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id The attribute ID.
	 * @param array  $att    Properties of the attribute.
	 */
	private static function att_type_hidden( $att_id, $att ) {
		?>
		<input type="hidden" class="render-modal-att-input render-modal-att-hidden"
		       value="<?php echo $att['default']; ?>"
		       name="<?php echo $att_id; ?>"
		       data-default="<?php echo $att['default']; ?>"
			/>
	<?php
	}

	/**
	 * Outputs the field HTML of the textbox attribute.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id     The attribute ID.
	 * @param array  $att        Properties of the attribute.
	 * @param array  $properties Properties of the attribute field type.
	 */
	private static function att_type_textbox( $att_id, $att, $properties = array() ) {

		$properties = wp_parse_args( $properties, array(
			'prefix'       => false,
			'prefixWidth'  => 20,
			'postfix'      => false,
			'postfixWidth' => 20,
			'mask'         => false,
		) );

		// Determine width
		$width = 100;
		$width = $width - ( $properties['prefix'] ? $properties['prefixWidth'] : 0 );
		$width = $width - ( $properties['postfix'] ? $properties['postfixWidth'] : 0 );

		// Input mask
		$mask = $properties['mask'] ? 'data-mask="1"' : '';

		if ( $properties['prefix'] || $properties['postfix'] ) : ?>
			<div class="render-modal-att-textbox-fix">
		<?php endif;

		if ( $properties['prefix'] ) : ?>
			<div class="render-modal-att-textbox-prefix" style="width: <?php echo "$properties[prefixWidth]%"; ?>">
				<?php echo $properties['prefix']; ?>
			</div>
		<?php endif; ?>

		<input type="text" class="render-modal-att-input render-modal-att-textbox"
		       style="width: <?php echo "$width%"; ?>"
		       placeholder="<?php echo isset( $properties['placeholder'] ) ? $properties['placeholder'] : ''; ?>"
		       value="<?php echo $att['default']; ?>"
		       name="<?php echo $att_id; ?>"
		       data-default="<?php echo $att['default']; ?>"
			<?php echo $mask; ?> />

		<?php if ( $properties['postfix'] ) : ?>
			<div class="render-modal-att-textbox-postfix" style="width: <?php echo "$properties[postfixWidth]%"; ?>">
				<?php echo $properties['postfix']; ?>
			</div>
		<?php endif;

		if ( $properties['prefix'] || $properties['postfix'] ) : ?>
			</div>
		<?php endif;
	}

	/**
	 * Outputs the field HTML of the checkbox attribute.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id     The attribute ID.
	 * @param array  $att        Properties of the attribute.
	 * @param array  $properties Properties of the attribute field type.
	 * @param string $shortcode  The current shortcode.
	 */
	private static function att_type_checkbox( $att_id, $att, $properties = array(), $shortcode ) {

		$properties = wp_parse_args( $properties, array(
			'value' => '1',
			'label' => false,
		) );

		$unique_ID = md5( $shortcode . $att_id );
		?>
		<div class="render-switch">

			<input type="checkbox" class="render-modal-att-input render-modal-att-checkbox"
			       name="<?php echo $att_id; ?>"
			       id="<?php echo $unique_ID; ?>"
			       value="<?php echo $properties['value']; ?>"/>

			<label for="<?php echo $unique_ID; ?>"></label>

		</div>

		<?php if ( $properties['label'] !== false ) : ?>
			<span class="render-modal-att-checkbox-label">
				<?php echo $properties['label']; ?>
			</span>
		<?php endif; ?>
	<?php
	}

	/**
	 * Outputs the field HTML of the toggle attribute.
	 *
	 * This is technically still a checkbox, but instead of one value or nothing, it's one value or the other. It also
	 * has the visual of a toggle switch.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id     The attribute ID.
	 * @param array  $att        Properties of the attribute.
	 * @param array  $properties Properties of the attribute field type.
	 * @param string $shortcode  The current shortcode.
	 */
	private static function att_type_toggle( $att_id, $att, $properties = array(), $shortcode ) {

		$properties = wp_parse_args( $properties, array(
			'values'        => array(),
			'flip'          => false,
			'deselectStyle' => false,
		) );

		$unique_ID = md5( $shortcode . $att_id );

		// Get values
		$values = array();
		foreach ( $properties['values'] as $value => $label ) {
			$values[] = array( $value, $label );
		}
		?>
		<div class="render-switch toggle">

			<input type="checkbox" class="render-modal-att-input render-modal-att-toggle"
			       name="<?php echo $att_id; ?>"
			       id="<?php echo $unique_ID; ?>"
			       value="<?php echo $values[1][0]; ?>"
				<?php echo $properties['flip'] ? 'checked' : ''; ?>
				/>

			<label for="<?php echo $unique_ID; ?>"
			       class="<?php echo $properties['deselectStyle'] ? 'disabled-style' : ''; ?>">

				<span class="render-modal-att-toggle-first">
					<?php echo $values[0][1]; ?>
				</span>

				<span class="render-modal-att-toggle-second">
					<?php echo $values[1][1]; ?>
				</span>

			</label>

			<?php // This is the default (un-unchecked) value ?>
			<input type="hidden" name="<?php echo $att_id; ?>"
			       value="<?php echo $values[0][0]; ?>"/>

		</div>
	<?php
	}

	/**
	 * Outputs the field HTML of the textarea attribute.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id The attribute ID.
	 * @param array  $att    Properties of the attribute.
	 */
	private static function att_type_textarea( $att_id, $att ) {
		?>
		<textarea class="render-modal-att-input render-modal-att-textarea"
		          name="<?php echo $att_id; ?>"
		          data-default="<?php echo $att['default']; ?>"><?php
			echo $att['default'];
			?></textarea>
	<?php
	}

	/**
	 * Outputs the field HTML of the selectbox attribute.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id     The attribute ID.
	 * @param array  $att        Properties of the attribute.
	 * @param array  $properties Properties of the attribute field type.
	 */
	private static function att_type_selectbox( $att_id, $att, $properties = array() ) {

		$properties = wp_parse_args( $properties, array(
			'callback'         => false,
			'groups'           => array(),
			'options'          => array(),
			'multi'            => false,
			'placeholder'      => __( 'Select an option', 'Render' ),
			'no_options'       => __( 'No options available.', 'Render' ),
			'allowCustomInput' => false,
			'allowIcons'       => false,
			'allowDeselect'    => true,
			'disableChosen'    => false,
		) );

		// Can't have these without Chosen
		if ( $properties['disableChosen'] ) {
			$properties['allowCustomInput'] = false;
			$properties['allowIcons']       = false;
		}

		// If a callback is provided, use that to populate options
		if ( $properties['callback'] !== false && is_callable( $properties['callback']['function'] ) ) {

			// Call with args, if they're set
			if ( isset( $properties['callback']['args'] ) ) {
				$options = call_user_func( $properties['callback']['function'], $properties['callback']['args'] );
			} else {
				$options = call_user_func( $properties['callback']['function'] );
			}
		}

		if ( ! empty( $options ) ) {

			// We need to merge our arrays, but we need to do it with either groups or options (whichever is in use)
			$which = ! empty( $properties['groups'] ) ? 'groups' : 'options';
			if ( ! empty( $properties[ $which ] ) ) {
				$properties[ $which ] = array_merge( $options, $properties[ $which ] );
			} else {
				$properties[ $which ] = $options;
			}
		}

		$no_options = empty( $properties['options'] ) && empty( $properties['groups'] );

		if ( ! $no_options ) {

			// Optgroup support
			if ( empty( $properties['groups'] ) ) {

				$properties['groups'] = array(
					0 => array(
						'options' => $properties['options'],
					),
				);
			}
		}

		// Chosen support
		if ( $properties['disableChosen'] === false ) {
			$chosen = 'chosen' . ( $properties['allowCustomInput'] ? ' allow-custom-input' : '' );
			$chosen .= $properties['allowIcons'] ? ' allow-icons' : '';
		} else {
			$chosen = '';
		}

		// Classes
		$classes   = array();
		$classes[] = 'render-chosen-container';
		$classes[] = $properties['allowCustomInput'] ? 'render-chosen-custom-input' : '';
		$classes   = array_filter( $classes );
		?>

		<span class="render-modal-selectbox-no-options" style="<?php echo ! $no_options ? 'display: none;' : ''; ?>">
			<?php echo $properties['no_options']; ?>
		</span>

		<div class="<?php echo implode( ' ', $classes ); ?>" style="<?php echo $no_options ? 'display: none;' : ''; ?>">
			<select name="<?php echo $att_id; ?>"
			        data-placeholder="<?php echo $properties['placeholder']; ?>"
			        class="render-modal-att-input <?php echo $chosen; ?>"
			        data-default="<?php echo $att['default']; ?>"
			        data-deselect="<?php echo $properties['allowDeselect'] ? 'true' : 'false'; ?>"
				<?php echo $properties['multi'] ? 'multiple' : ''; ?>>

				<?php // Necessary for starting with nothing selected ?>
				<option></option>

				<?php if ( ! empty( $properties['groups'] ) ) : ?>
					<?php foreach ( $properties['groups'] as $opt_group ) : ?>

						<?php if ( isset( $opt_group['label'] ) ) : ?>
							<optgroup label="<?php echo $opt_group['label']; ?>">
						<?php endif; ?>

						<?php foreach ( (array) $opt_group['options'] as $option_value => $option ) : ?>
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
								<?php echo isset( $option['icon'] ) ? "data-icon='$option[icon]'" : ''; ?>
								value="<?php echo $option_value; ?>"
								<?php selected( $option_value, $att['default'] ); ?>
								>
								<?php echo $option['label']; ?>
							</option>
						<?php endforeach; ?>

						<?php if ( isset( $opt_group['label'] ) ) : ?>
							</optgroup>
						<?php endif; ?>

					<?php endforeach; ?>

				<?php endif; ?>

			</select>

			<?php if ( $properties['allowCustomInput'] ) : ?>
				<div class="render-chosen-custom-input-icon dashicons dashicons-edit"></div>
			<?php endif; ?>
		</div>
	<?php
	}

	/**
	 * Outputs the field HTML of the slider attribute.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id     The attribute ID.
	 * @param array  $att        Properties of the attribute.
	 * @param array  $properties Properties of the attribute field type.
	 */
	private static function att_type_slider( $att_id, $att, $properties ) {

		// Establish defaults
		$properties = wp_parse_args( $properties, array(
			'value' => $att['default'] ? $att['default'] : 0,
			'min'   => 0,
			'max'   => 100,
			'step'  => 1,
			'range' => false,
		) );

		$default = $properties['value'];

		// If range slider
		if ( $properties['range'] ) {

			$properties['range']  = 'true';
			$properties['values'] = isset( $properties['values'] ) ? $properties['values'] : '0-20';
			$default              = $properties['values'];
			unset( $properties['value'] );

		} else {
			unset( $properties['range'] );
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
			       name="<?php echo $att_id; ?>"
			       data-default="<?php echo $default; ?>"
				/>

			<div class="render-modal-att-slider-range-text">
				<span class="render-modal-att-slider-range-text-value1"><?php echo $values[0]; ?></span>
				&nbsp;-&nbsp;
				<span class="render-modal-att-slider-range-text-value2"><?php echo $values[1]; ?></span>
			</div>
		<?php else: ?>

			<input type="text" class="render-modal-att-slider-value render-modal-att-input"
			       value="<?php echo $properties['value']; ?>"
			       name="<?php echo $att_id; ?>"
			       data-default="<?php echo $default; ?>"
				/>
		<?php endif; ?>
		<div class="render-modal-att-slider" <?php echo $data; ?>></div>
	<?php
	}

	/**
	 * Outputs the field HTML of the color-picker attribute.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id The attribute ID.
	 * @param array  $att    Properties of the attribute.
	 */
	private static function att_type_colorpicker( $att_id, $att ) {

		$default = $att['default'] ? $att['default'] : '#bada55';
		?>
		<input type="text"
		       value="<?php echo $default ?>"
		       class="render-modal-att-colorpicker render-modal-att-input"
		       name="<?php echo $att_id; ?>"
		       data-default="<?php echo $default; ?>"
			/>
	<?php
	}

	/**
	 * Outputs the field HTML of the counter attribute.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id     The attribute ID.
	 * @param array  $att        Properties of the attribute.
	 * @param array  $properties Properties of the attribute field type.
	 */
	private static function att_type_counter( $att_id, $att, $properties ) {

		// Establish defaults
		$properties = wp_parse_args( $properties, array(
			'min'        => 0,
			'max'        => 1000,
			'step'       => 1,
			'shift_step' => 10,
			'unit'       => false,
		) );

		$default = $att['default'] ? $att['default'] : $properties['min'];
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
			       data-default="<?php echo $default; ?>"
				/>

			<div class="render-modal-counter-up render-modal-button dashicons dashicons-plus"></div>
		</div>

		<?php if ( ( $unit = $properties['unit'] ) !== false ) : ?>

			<div class="render-modal-counter-unit">

				<?php if ( isset( $unit['allowed'] ) ) : ?>

					<select data-placeholder="<?php _e( 'Unit', 'Render' ); ?>">
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

	/**
	 * Outputs the field HTML of the repeater attribute.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id     The attribute ID.
	 * @param array  $att        Properties of the attribute.
	 * @param array  $properties Properties of the attribute field type.
	 */
	private static function att_type_repeater( $att_id, $att, $properties ) {

		// Setup defaults;
		$properties = wp_parse_args( $properties, array(
			'fields'    => array(
				'dummy_field' => array(
					'type'    => 'hidden',
					'default' => 1,
				),
			),
			'startWith' => 1,
		) );

		// Add content for nested shortcodes
		if ( $att_id == 'nested_children' && ! isset( $properties['fields']['content'] ) ) {
			$properties['fields']['content'] = Render::parse_shortcode_att( array(
				'type' => 'hidden',
			) );
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
					$properties['fields'][ $field_ID ]['noInit'] = false;
				}
			}
			?>
			<div class="render-modal-repeater-field <?php echo $i == 0 ? 'dummy-field' : ''; ?>"
				<?php echo $i == 0 ? 'style="display:none"' : ''; ?>
				<?php echo isset( $properties['max'] ) ? "data-max='$properties[max]'" : ''; ?>>

				<?php // Dummy input to trigger field
				?>
				<input type="hidden" name="<?php echo $att_id; ?>" class="render-modal-att-input"/>

				<div class="render-modal-repeater-inputs">
					<?php
					if ( ! $properties['fields'] ) {
						echo isset( $properties['noFields'] ) ? $properties['noFields'] : __( 'No fields set' );
					} else {
						self::_atts_loop( $properties['fields'] );
					}
					?>
				</div>

				<div class="render-modal-repeater-actions">
					<span class="render-modal-repeater-remove render-modal-button dashicons dashicons-minus"></span>
					<span class="render-modal-repeater-add render-modal-button dashicons dashicons-plus"></span>
				</div>
			</div>
		<?php
		endfor;
	}

	/**
	 * Outputs the field HTML of the media attribute.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $att_id     The attribute ID.
	 * @param array  $att        Properties of the attribute.
	 * @param array  $properties Properties of the attribute field type.
	 */
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

		$default = $att['default'] ? $att['default'] : '';
		?>
		<input type="button" value="Upload / Choose Media" class="render-modal-att-media-upload"
		       data-type="<?php echo $properties['type']; ?>"/>
		<input type="hidden"
		       value="<?php echo $default ?>"
		       class="render-modal-att-media render-modal-att-input"
		       name="<?php echo $att_id; ?>"
		       data-default="<?php echo $default; ?>"
			/>
	<?php
	}

	/**
	 * Outputs the Render Modal HTML.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @global Render $Render The main Render object.
	 */
	public static function _modal_output() {

		global $Render;

		foreach ( render_get_disabled_shortcodes() as $code ) {
			$Render->remove_shortcode( $code );
		}

		// Sort alphabetically by title.
		uasort( $Render->shortcodes, function ( $a, $b ) {
			return strcmp( $a['title'], $b['title'] );
		} );

		// Gets all categories in use
		$used_categories = render_get_shortcode_used_categories();
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
						<?php if ( ! empty( $used_categories ) ) : ?>
							<?php
							$i = 0;
							foreach ( $used_categories as $category_ID => $category ) :
								$i ++;
								?>
								<li data-category="<?php echo $category_ID; ?>"
								    class="<?php echo $i === 1 ? 'active' : ''; ?>">
									<span class="dashicons <?php echo $category['icon']; ?>"></span>
									<br/>
									<?php echo $category['label'] ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
					<div class="render-modal-categories-right dashicons dashicons-arrow-right-alt2"></div>
				</div>

				<div class="render-modal-shortcodes-container">

					<ul class="render-modal-shortcodes accordion-container">
						<?php if ( ! empty( $Render->shortcodes ) ) : ?>
							<?php foreach ( $Render->shortcodes as $code => $shortcode ) :
								$wrapping          = isset( $shortcode['wrapping'] ) && $shortcode['wrapping'] ? true : false;

								/**
								 * Allows the filtering of the list of atts for the current shortcode.
								 *
								 * @since 1.0.0
								 */
								$shortcode['atts'] = apply_filters( 'render_att_pre_loop', $shortcode['atts'], $code, $shortcode );

								if ( $shortcode['noDisplay'] ) {
									continue;
								}

								// TODO Construct data in similar fashion to .att-row
								?>
								<li data-category="<?php echo isset( $shortcode['category'] ) ?
									$shortcode['category'] : 'other'; ?>"
								    data-code="<?php echo $code; ?>"
								    data-title="<?php echo $shortcode['title']; ?>"
								    data-source="<?php echo $shortcode['source']; ?>"
								    data-tags="<?php echo $shortcode['tags']; ?>"
								    class="render-modal-shortcode
								    <?php echo ! empty( $shortcode['atts'] ) ? 'accordion-section' : ''; ?>
								    <?php echo $shortcode['wrapping'] ? 'wrapping' : ''; ?>
								    <?php echo isset( $shortcode['render']['nested']['child'] ) ? 'nested-parent' : ''; ?>
								    ">

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
												$shortcode['description'] : __( 'No description available.', 'Render' ); ?>
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
													</div>
												</div>

												<div class="render-modal-shortcode-toolbar-toggle dashicons
													dashicons-arrow-down-alt2"></div>
											</div>

											<div class="render-modal-shortcode-atts">

												<?php self::_atts_loop( $shortcode['atts'], $advanced = false, $wrapping, $code ); ?>

												<?php
												// Figure out if any of the attributes belong to the advanced section
												$advanced = false;
												foreach ( $shortcode['atts'] as $att ) {
													if ( $advanced = $att['advanced'] ) {
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
														<?php self::_atts_loop( $shortcode['atts'], $advanced = true, $wrapping, $code ); ?>
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

					<div id="render-modal-remove" class="button-secondary button-red delete" style="display: none;">
						<?php _e( 'Remove Current Shortcode', 'Render' ); ?>
					</div>

					<?php do_action( 'render_modal_action_area' ); ?>
				</div>
			</div>
		</div>
	<?php
	}
}

/**
 * AJAX callback for populating conditional shortcode attributes.
 *
 * @since 1.1-beta-1
 */
add_action( 'wp_ajax_render_conditional_att_populate', function () {

	$callback = isset( $_POST['callback'] ) ? $_POST['callback'] : '';
	$atts     = isset( $_POST['atts'] ) ? $_POST['atts'] : array();

	// Send back our options
	if ( is_callable( $callback ) ) {
		$options = call_user_func( $callback, $atts );
		wp_send_json( $options );
	}

	die();
} );