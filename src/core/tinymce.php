<?php

/**
 * Class Render_tinymce
 *
 * All functionality for the tinyMCE button that Render adds to the standard editor.
 *
 * @since      Render 1.0.0
 *
 * @package    Render
 * @subpackage TinyMCE
 */
class Render_tinymce extends Render {

	public $rendered_shortcodes = array();

	public $render_data = array();

	function __construct() {

		include_once( RENDER_PATH . 'core/modal.php' );
		new Render_Modal();

		$this->set_render_data();
		self::add_tinymce_style();

		add_filter( 'mce_external_plugins', array( __CLASS__, 'add_tinymce_plugins' ) );
		add_filter( 'mce_buttons', array( __CLASS__, 'register_tinymce_buttons' ) );
		add_filter( 'tiny_mce_before_init', array( __CLASS__, 'modify_tinymce_init' ) );

		add_action( 'render_localized_data', array( __CLASS__, 'loading_messages' ) );
		add_action( 'render_localized_data', array( $this, 'rendering_data' ) );
		add_action( 'render_localized_data', array( __CLASS__, 'tinymce_external_scripts' ) );
		add_action( 'render_localized_data', array( __CLASS__, 'shortcode_regex' ) );
	}

	/**
	 * This filter allows the tinymce.init() args to be modified.
	 *
	 * Currently, I'm adding some extended_valid_elememnts so that tinymce doesn't strip my empty tags (mainly spans).
	 *
	 * @since Render 1.0.0
	 *
	 * @param array $mceinit The init settings for tinymce.
	 *
	 * @return mixed The modified init array.
	 */
	public static function modify_tinymce_init( $mceinit ) {

		$mceinit['noneditable_noneditable_class'] = 'render-tinymce-noneditable';
		$mceinit['noneditable_editable_class']    = 'render-tinymce-editable';
		$mceinit['extended_valid_elements']       = 'span[*]';
		$mceinit['entity_encoding']               = 'numeric';

		return $mceinit;
	}

	public static function tinymce_external_scripts( $data ) {

		$data['tinymceExternalScripts'][] = RENDER_URL . '/assets/js/render.min.js';

		return $data;
	}

	public static function shortcode_regex( $data ) {

		$data['shortcode_regex'] = get_shortcode_regex();

		return $data;
	}

	public static function loading_messages( $data ) {

		$data['loading_messages'] = apply_filters( 'render_loading_messages', array(
			__( 'Awesome-ifying your content...', 'Render' ),
			__( 'Cleaning up the bathroom...', 'Render' ),
			__( 'Making a cup of coffee for your content...', 'Render' ),
			__( 'Playing catch with the WYSIWYG...', 'Render' ),
			__( 'Taking your content to the next level...', 'Render' ),
			__( 'Making your dreams come true...', 'Render' ),
			__( 'Reducing synchronized load caching errors...', 'Render' ),
			__( 'Taking out the trash (you\'re welcome!)...', 'Render' ),
			__( 'Sending your content to the moon, and back...', 'Render' ),
			__( 'Giving your content a bubble bath...', 'Render' ),
			__( 'Taking your content to a classy restaurant...', 'Render' ),
			__( 'Showing your content a good time...', 'Render' ),
			__( 'Playing cards with the Automattic team...', 'Render' ),
			__( 'Strapping a jetpack onto your content...', 'Render' ),
		) );

		return $data;
	}

	public function set_render_data() {

		$this->render_data['post'] = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : 0;

		$this->render_data = apply_filters( 'render_render_data', $this->render_data );
	}

	public static function add_tinymce_style() {
		add_editor_style( RENDER_URL . "/assets/css/render.min.css" );
	}

	public function rendering_data( $data ) {

		global $Render;

		$rendered = array();
		foreach ( $Render->shortcodes as $code => $shortcode ) {
			if ( $shortcode['render'] ) {
				$rendered[ $code ] = $shortcode['render'];
			}
		}
		$data['rendered_shortcodes'] = $rendered;
		$data['render_data']         = $this->render_data;

		$data['do_render'] = get_option( 'render_render_visual', true );

		return $data;
	}

	/**
	 * Links our custom script to our tinyMCE button.
	 *
	 * @since Render 1.0.0
	 *
	 * @param null|array $plugins The array of button scripts.
	 *
	 * @return mixed|array
	 */
	public static function add_tinymce_plugins( $plugins ) {

		$plugins['render']      = RENDER_URL . '/assets/js/includes/tinymce-plugins/render/plugin.min.js';
		$plugins['noneditable'] = RENDER_URL . '/assets/js/includes/tinymce-plugins/noneditable/plugin.min.js';

		return $plugins;
	}

	/**
	 * Adds our custom button to the tinyMCE buttons.
	 *
	 * @since Render 1.0.0
	 *
	 * @param mixed|array $buttons All tinyMCE buttons.
	 *
	 * @return mixed|array
	 */
	public static function register_tinymce_buttons( $buttons ) {

		array_push( $buttons, 'render_open' );
		array_push( $buttons, 'render_refresh' );

		return $buttons;
	}

	public static function render_ajax() {

		global $post;

		if ( isset( $_REQUEST['post'] ) ) {
			$post = get_post( $_REQUEST['post'] );
		}
	}

	public static function render_shortcodes() {

		global $render_shortcode_data;

		define( 'Render_SHORTCODE_RENDERING', true );
		do_action( 'render_render_ajax' );

		$content               = stripslashes( $_POST['content'] );
		$render_shortcode_data = $_POST['shortcode_data'];

		$pattern = get_shortcode_regex();

		// FIXME Weird paragraph tags
		$content = preg_replace( '/<span class="render-tinymce-divider render-tinymce-noneditable">.*?<\/span>/', '', $content );
		$content = render_strip_paragraphs_around_shortcodes( $content );
		$content = preg_replace_callback( "/$pattern/s", array( __CLASS__, 'replace_shortcodes' ), $content );

		echo $content;

		die();
	}

	public static function replace_shortcodes( $matches ) {

		global $render_shortcode_data;

		// "Extract" some of the found matches
		$entire_code = $matches[0];
		$code        = $matches[2];
		$atts        = $matches[3];
		$_content    = $matches[5];

		// Search again for any nested shortcodes (loops infinitely)
		if ( ! empty( $_content ) ) {
			$pattern = get_shortcode_regex();
			$content = preg_replace_callback( "/$pattern/s", array( __CLASS__, 'replace_shortcodes' ), $_content );
		}

		// Get out of here if rendering is not set
		if ( ! isset( $render_shortcode_data[ $code ] ) ) {
			return $entire_code;
		}

		// If this is a wrapping code, but no content is provided, use dummy content
		if ( empty( $content ) &&
		     isset( $render_shortcode_data[ $code ]['wrapping'] ) &&
		     $render_shortcode_data[ $code ]['wrapping'] === 'true'
		) {
			if ( isset( $render_shortcode_data[ $code ]['dummyContent'] ) ) {
				$content = $render_shortcode_data[ $code ]['dummyContent'];
			} else {
				$content = 'No content selected.';
			}

			$entire_code = str_replace( '][', "]{$content}[", $entire_code );
		}

		// Match any of all block level elements
		// https://developer.mozilla.org/en-US/docs/Web/HTML/Block-level_elements
		$block_regex = '/<(address|figcaption|ol|article|figure|output|aside|footer|p|audio|form|pre|blockquote|h[1-6]|section|canvas|header|table|dd|hgroup|tfoot|div|hr|ul|dl|video|fieldset|noscript)/';

		// Properly wrap the content
		if ( ! empty( $content ) ) {

			// Wrap the content in a special element, but first decide if it needs to be div or span
			$tag     = preg_match( $block_regex, $content ) ? 'div' : 'span';
			$content = "<$tag class='render-tinymce-shortcode-content render-tinymce-editable'>$content</$tag>";
		}

		// Replace the content with the new content
		if ( ! empty( $_content ) ) {
			$entire_code = str_replace( "]{$_content}[", "]{$content}[", $entire_code );
		}

		// Whether or not to style the code
		$nostyle = isset( $render_shortcode_data[ $code ]['noStyle'] ) ? '' : ' styled';

		// Get the atts prepared for JSON
		if ( ! empty( $atts ) ) {
			$atts = shortcode_parse_atts( $atts );
			if ( ! empty( $atts ) ) {
				$atts = json_encode( $atts );
			}
		}

		// Get the shortcode output
		$shortcode_output = do_shortcode( $entire_code );

		// If the output contains any block tags, make sure the wrapper tag is a div
		$tag = preg_match( $block_regex, $shortcode_output ) ? 'div' : 'span';

		$output = '';

		// Start the wrapper
		if ( ! isset( $render_shortcode_data[ $code ]['noWrap'] ) ) {

			if ( ! empty( $atts ) ) {
				$atts = htmlentities( preg_replace( '/<br.*?\/>/', '::br::', $atts ) );
			}

			$output .= "<$tag class='render-tinymce-shortcode-wrapper render-tinymce-noneditable $code $nostyle' data-code='$code' data-atts='$atts'>";
		}

		$output .= $shortcode_output;

		// Close the wrapper
		if ( ! isset( $render_shortcode_data[ $code ]['noWrap'] ) ) {

			// Delete notification
			$output .= "<$tag class='render-tinymce-shortcode-wrapper-delete render-tinymce-tooltip'>" . __( 'Press again to delete', 'Render' ) . "</$tag>";

			// Action button
			$output .= "<$tag class='render-tinymce-shortcode-wrapper-actions render-tinymce-tooltip'>";
			$output .= "<$tag class='render-tinymce-shortcode-wrapper-edit dashicons dashicons-edit'>edit</$tag>";
			$output .= "<$tag class='render-tinymce-shortcode-wrapper-remove dashicons dashicons-no'>remove</$tag>";
			$output .= "</$tag>";

			$output .= "</$tag>";
		}

		return $output;
	}
}

add_action( 'current_screen', '_render_init_tinymce' );

// Always add the AJAX
add_action( 'render_render_ajax', array( 'Render_tinymce', 'render_ajax' ) );
add_action( 'wp_ajax_render_render_shortcode', array( 'Render_tinymce', 'render_shortcode' ) );
add_action( 'wp_ajax_render_render_shortcodes', array( 'Render_tinymce', 'render_shortcodes' ) );

function _render_init_tinymce( $screen ) {

	$allowed_screens = apply_filters( 'render_tinymce_allowed_screens', array(
		'post',
		'widgets',
		'customize',
	), $screen->base, $screen );

	if ( in_array( $screen->base, $allowed_screens ) ) {
		new Render_tinymce();
	}
}