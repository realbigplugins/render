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
class USL_tinymce extends USL{

	function __construct() {

		// Add all of our action hooks
		self::_add_actions();
	}

	/**
	 * Adds all of the USL action hooks for the tinyMCE button.
	 *
	 * @since USL 1.0.0
	 */
	private static function _add_actions() {

		add_filter( 'mce_external_plugins', array( __CLASS__, 'add_tinymce_buttons' ) );
		add_filter( 'mce_buttons', array( __CLASS__, 'register_tinymce_buttons' ) );

		add_action( 'admin_head', array( __CLASS__, 'localize_shortcodes' ), 9999 );
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

		global $shortcode_tags, $USL;

		$non_usl_shortcodes = array();
		foreach ( $shortcode_tags as $code => $func ) {

			if ( array_key_exists( $code, $USL->shortcodes ) ) {
				continue;
			}

			$non_usl_shortcodes[ $code ] = array(
				'category' => 'Other',
			);
		}

		$data['all_shortcodes'] = array_merge( $non_usl_shortcodes, $USL->shortcodes );

		wp_localize_script( 'common', 'USL_Data', $data );
	}
}

new USL_tinymce();