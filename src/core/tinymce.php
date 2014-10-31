<?php

/**
 * Class USL_tinymce
 *
 * All functionality for the tinyMCE button that USL adds to the standard editor.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage TinyMCE
 */
class USL_tinymce extends USL {

	function __construct() {

		add_filter( 'mce_external_plugins', array( __CLASS__, 'add_tinymce_buttons' ) );
		add_filter( 'mce_buttons', array( __CLASS__, 'register_tinymce_buttons' ) );

		add_action( 'admin_head', array( __CLASS__, 'localize_shortcodes' ), 9999 );

		add_action( 'admin_print_footer_scripts', array( __CLASS__, 'modal_html' ) );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );
	}

	public static function admin_scripts() {

		// Allow WP accordion functionality for our shortcode list
		wp_enqueue_script( 'accordion' );
	}

	/**
	 * Links our custom script to our tinyMCE button.
	 *
	 * @since USL 1.0.0
	 *
	 * @param null|array $plugin_array The array of button scripts.
	 *
	 * @return mixed|array
	 */
	public static function add_tinymce_buttons( $plugin_array ) {

		$plugin_array['usl_button'] = self::$url . '/assets/js/source/admin/tinymce.js';

		return $plugin_array;
	}

	/**
	 * Adds our custom button to the tinyMCE buttons.
	 *
	 * @since USL 1.0.0
	 *
	 * @param mixed|array $buttons All tinyMCE buttons.
	 *
	 * @return mixed|array
	 */
	public static function register_tinymce_buttons( $buttons ) {

		array_push( $buttons, 'usl_button' );

		return $buttons;
	}

	public static function localize_shortcodes() {

		$all_shortcodes = _usl_get_merged_shortcodes();

		$data['all_shortcodes'] = $all_shortcodes;

		wp_localize_script( 'common', 'USL_Data', $data );
	}

	private static function atts_loop( $shortcode_atts, $advanced = false ) {

		foreach ( $shortcode_atts as $att_name => $att ) : ?>
			<?php if ( ( ! $advanced && ! isset( $att['advanced'] ) ) || $advanced && isset( $att['advanced'] ) ) : ?>
				<div class="usl-mce-sc-att-row">
					<label>
						<span class="usl-mce-sc-att-name">
							<?php echo ucfirst( $att_name ); ?>
						</span>
						<span class="usl-mce-sc-att-field"
						      data-required="<?php echo $att['required']; ?>">
							<?php if ( isset( $att['accepted_values'] ) ) : ?>
								<select name="<?php echo $att_name; ?>">
									<option value="">Select One</option>
									<?php foreach ( $att['accepted_values'] as $att_value ) : ?>
										<option
											value="<?php echo $att_value; ?>">
											<?php echo ucfirst( $att_value ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							<?php else: ?>
								<input name="<?php echo $att_name; ?>"/>
							<?php endif; ?>
						</span>
					</label>
				</div>
			<?php endif; ?>
		<?php endforeach;
	}

	public static function modal_html() {

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
		<div id="usl-mce-backdrop"></div>
		<div id="usl-mce-wrap" style="display: none;">
			<div class="usl-mce-title">
				Shortcodes
				<button type="button" class="usl-mce-close">
					<span class="screen-reader-text">Close</span>
				</button>
			</div>

			<div class="usl-mce-body">
				<div class="usl-mce-search">
					<input type="text" name="usl-mce-search" placeholder="Search"/>
					<span class="dashicons dashicons-search"></span>
				</div>

				<div class="usl-mce-categories">
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

				<div class="usl-mce-shortcodes-container">

					<ul class="usl-mce-shortcodes accordion-container">
						<?php if ( ! empty( $all_shortcodes ) ) : ?>
							<?php foreach ( $all_shortcodes as $code => $shortcode ) : ?>
								<li data-category="<?php echo isset( $shortcode['category'] ) ? $shortcode['category'] : 'other'; ?>"
								    data-code="<?php echo $code; ?>"
								    class="<?php echo ! empty( $shortcode['atts'] ) ? 'accordion-section' : ''; ?>">

									<form class="usl-mce-shortcode-form">

										<div
											class="<?php echo ! empty( $shortcode['atts'] ) ? 'accordion-section' : 'usl-mce-sc'; ?>-title">
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
					<div class="usl-mce-shortcodes-spinner spinner"></div>
				</div>
			</div>

			<div class="usl-mce-footer">
				<div class="usl-mce-cancel">
					<a class="submitdelete deletion" href="#">Cancel</a>
				</div>
				<div class="usl-mce-update">
					<input type="submit" value="Add Shortcode" class="button button-primary" id="usl-mce-submit"
					       name="usl-mce-submit">
				</div>
			</div>
		</div>
	<?php
	}
}

new USL_tinymce();