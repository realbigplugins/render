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

		add_action( 'load-post.php', array( __CLASS__, 'init' ) );
	}

	public static function init() {

		add_filter( 'mce_external_plugins', array( __CLASS__, 'add_tinymce_buttons' ) );
		add_filter( 'mce_buttons', array( __CLASS__, 'register_tinymce_buttons' ) );

		include_once( self::$path . 'core/modal.php' );
		new USL_Modal();
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
}

new USL_tinymce();