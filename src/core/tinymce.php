<?php

// TODO Have entire visual editor show a loading image while I use AJAX to generate all visual shortcodes (instead of requiring it to be done via JS)

// TODO allow turning off of visual editor shortcode rendering

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

		// TODO make this only load where needed
		self::init();
	}

	public static function init() {

		add_filter( 'mce_external_plugins', array( __CLASS__, 'add_tinymce_plugins' ) );
		add_filter( 'mce_buttons', array( __CLASS__, 'register_tinymce_buttons' ) );

		add_filter( 'tiny_mce_before_init', array( __CLASS__, 'modify_tinymce_init' ) );

		add_action( 'after_setup_theme', array( __CLASS__, 'add_tinymce_style' ) );

		add_action( 'wp_ajax_usl_render_shortcode', array( __CLASS__, 'render_shortcode' ) );

		include_once( self::$path . 'core/modal.php' );
		new USL_Modal();
	}

	/**
	 * This filter allows the tinymce.init() args to be modified.
	 *
	 * Currently, I'm adding some extended_valid_elememnts so that tinymce doesn't strip my empty tags (mainly spans).
	 *
	 * @since USL 1.0.0
	 *
	 * @param array $mceinit The init settings for tinymce.
	 *
	 * @return mixed The modified init array.
	 */
	public static function modify_tinymce_init( $mceinit ) {

		$mceinit['noneditable_noneditable_class'] = 'usl-tinymce-shortcode-wrapper';
//		$mceinit['noneditable_editable_class'] = 'usl-tinymce-shortcode-content';
		$mceinit['extended_valid_elements'] = 'span[*]';
		return $mceinit;
	}

	public static function add_tinymce_style() {
		add_editor_style( self::$url . "/assets/css/ultimate-shortcodes-library.min.css" );
	}

	/**
	 * Links our custom script to our tinyMCE button.
	 *
	 * @since USL 1.0.0
	 *
	 * @param null|array $plugins The array of button scripts.
	 *
	 * @return mixed|array
	 */
	public static function add_tinymce_plugins( $plugins ) {

		$plugins['usl'] = self::$url . '/assets/js/includes/tinymce-plugins/usl/plugin.min.js';
		$plugins['noneditable'] = self::$url . '/assets/js/includes/tinymce-plugins/noneditable/plugin.min.js';
//		$plugins['jquery'] =

		return $plugins;
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

		array_push( $buttons, 'usl' );

		return $buttons;
	}

	public static function render_shortcode() {
		echo do_shortcode( $_POST['shortcode'] );
		die();
	}
}

new USL_tinymce();