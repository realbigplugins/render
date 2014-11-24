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

	public $rendered_shortcodes = array();

	public $render_data = array();

	function __construct() {

		include_once( self::$path . 'core/modal.php' );
		new USL_Modal();

		$this->set_render_data();
		self::add_tinymce_style();

		add_filter( 'mce_external_plugins', array( __CLASS__, 'add_tinymce_plugins' ) );
		add_filter( 'mce_buttons', array( __CLASS__, 'register_tinymce_buttons' ) );
		add_filter( 'tiny_mce_before_init', array( __CLASS__, 'modify_tinymce_init' ) );

		add_action( 'usl_localized_data', array( __CLASS__, 'loading_messages' ) );
		add_action( 'usl_localized_data', array( $this, 'rendering_data' ) );
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
		$mceinit['noneditable_editable_class']    = 'usl-tinymce-shortcode-content';
		$mceinit['extended_valid_elements']       = 'span[*]';
		$mceinit['entity_encoding']               = 'numeric';

		return $mceinit;
	}

	public static function loading_messages( $data ) {

		$data['loading_messages'] = apply_filters( 'usl_loading_messages', array(
			'Awesome-ifying your content...',
			'Cleaning up the bathroom...',
			'Making a cup of coffee for your content...',
			'Playing catch with the WYSIWYG...',
			'Taking your content to the next level...',
			'Making your dreams come true...',
			'Reducing synchronized load caching errors...',
			'Taking out the trash (you\'re welcome!)...',
			'Sending your content to the moon, and back...',
			'Giving your content a bubble bath...',
			'Taking your content to a classy restaurant...',
			'Showing your content a good time...',
			'Playing cards with the Automattic team...',
			'Strapping a jetpack onto your content...',
		) );

		return $data;
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
		$data['render_data']         = $this->render_data;

		$data['do_render'] = get_option( 'usl_render_visual', true );

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

		$plugins['usl']         = self::$url . '/assets/js/includes/tinymce-plugins/usl/plugin.min.js';
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

	public static function render_shortcodes() {

		global $usl_shortcode_data;

		do_action( 'usl_render_ajax' );

		$content            = stripslashes( $_POST['content'] );
		$usl_shortcode_data = $_POST['shortcode_data'];

		$pattern = get_shortcode_regex();
		$content = preg_replace_callback( "/$pattern/s", array( __CLASS__, 'replace_shortcodes' ), $content );

		echo $content;

		die();
	}

	public static function replace_shortcodes( $matches ) {

		global $usl_shortcode_data;

		// "Extract" some of the found matches
		$entire_code = $matches[0];
		$code = $matches[2];
		$atts = $matches[3];
		$content = $matches[5];

		// Search again for any nested shortcodes (loops infinitely)
		$pattern = get_shortcode_regex();
		$_content = preg_replace_callback( "/$pattern/s", array( __CLASS__, 'replace_shortcodes' ), $content );

		// Replace the content with the new content
		if ( ! empty( $_content ) ) {
			$entire_code = str_replace( $content, $_content, $entire_code );
		}

		// Get the type of tag needed and whether or not to style the code
		$tag     = isset( $usl_shortcode_data[ $code ]['displayBlock'] ) ? 'div' : 'span';
		$nostyle = isset( $usl_shortcode_data[ $code ]['noStyle'] ) ? '' : ' styled';

		// Get the atts prepared for JSON
		$atts = shortcode_parse_atts( $atts );
		if ( ! empty( $atts ) ) {
			$atts = json_encode( $atts );
		}

		// Start the wrapper
		$output = "<$tag class='usl-tinymce-shortcode-wrapper $code $nostyle' data-code='$code' data-atts='$atts'>";

		$output .= do_shortcode( $entire_code );

		// Close the wrapper
		$output .= "</$tag>&#8203;";

		return $output;
	}
}

add_action( 'current_screen', '_usl_init_tinymce' );


// Always add the AJAX
add_action( 'usl_render_ajax', array( 'USL_tinymce', 'render_ajax' ) );
add_action( 'wp_ajax_usl_render_shortcode', array( 'USL_tinymce', 'render_shortcode' ) );
add_action( 'wp_ajax_usl_render_shortcodes', array( 'USL_tinymce', 'render_shortcodes' ) );

function _usl_init_tinymce( $screen ) {

	$allowed_screens = apply_filters( 'usl_tinymce_allowed_screens', array(
		'post',
	), $screen->base, $screen );

	if ( in_array( $screen->base, $allowed_screens ) ) {
		new USL_tinymce();
	}
}