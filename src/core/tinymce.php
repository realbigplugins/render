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

	public $rendered_shortcodes = array();

	public $render_data = array();

	function __construct() {

		// TODO make this only load where needed
		$this->init();
	}

	public function init() {

		add_filter( 'mce_external_plugins', array( __CLASS__, 'add_tinymce_plugins' ) );
		add_filter( 'mce_buttons', array( __CLASS__, 'register_tinymce_buttons' ) );

		add_action( 'admin_init', array( $this, 'set_render_data' ) );

		add_action( 'usl_render_ajax', array( __CLASS__, 'render_ajax' ) );

		add_filter( 'tiny_mce_before_init', array( __CLASS__, 'modify_tinymce_init' ) );

		add_action( 'usl_localized_data', array( $this, 'rendering_data' ) );

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
		$mceinit['noneditable_editable_class'] = 'usl-tinymce-shortcode-content';
		$mceinit['extended_valid_elements'] = 'span[*]';
		return $mceinit;
	}

	public function set_render_data() {

		$this->render_data['post'] = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : 0;

		$this->render_data = apply_filters( 'usl_render_data', $this->render_data );
	}

	public static function add_tinymce_style() {
		add_editor_style( self::$url . "/assets/css/ultimate-shortcodes-library.min.css" );
	}

	public function rendering_data( $data ) {

		global $USL;

		$rendered = array();
		foreach ( $USL->shortcodes as $code => $shortcode ) {
			if ( $shortcode['render'] ) {
				$rendered[ $code ] = $shortcode['render'];
			}
		}
		$data['rendered_shortcodes'] = $rendered;
		$data['render_data'] = $this->render_data;
		return $data;
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

	public static function render_ajax() {

		global $post;

		if ( isset( $_REQUEST['post'] ) ) {
			$post = get_post( $_REQUEST['post'] );
		}
	}

	public static function render_shortcode() {

		do_action( 'usl_render_ajax' );

		$response = array();

		// The quotes will be escaped, so we need to strip the escaping
		$response['output'] = do_shortcode( stripslashes_deep( $_POST['shortcode'] ) );

		if ( isset( $_POST['atts'] ) ) {
			$response['atts'] = $_POST['atts'];
		}

		$response['shortcode'] = stripslashes_deep( $_POST['shortcode'] );
		$response['code'] = $_POST['code'];

		wp_send_json( $response );
	}
}

$USL_tinymce = new USL_tinymce();