<?php

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Render_tinymce
 *
 * All functionality for the tinyMCE button that Render adds to the standard editor.
 *
 * @since      1.0.0
 *
 * @package    Render
 * @subpackage TinyMCE
 */
class Render_tinymce extends Render {

	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		// Que up the Render Modal
		render_enqueue_modal();

		// Setup TinyMCE
		add_filter( 'mce_external_plugins', array( __CLASS__, 'add_tinymce_plugins' ) );
		add_filter( 'mce_buttons', array( __CLASS__, 'register_tinymce_buttons' ) );
		add_filter( 'tiny_mce_before_init', array( __CLASS__, 'modify_tinymce_init' ) );

		// Localize data for rendering in the TinyMCE
		add_action( 'render_localized_data', array( $this, 'rendering_data' ) );
	}

	/**
	 * Links our custom script to our tinyMCE button.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
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

	/**
	 * This filter allows the tinymce.init() args to be modified.
	 *
	 * Currently, I'm adding some extended_valid_elements so that tinymce doesn't strip my empty tags (mainly spans).
	 *
	 * @since 1.0.0
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

	/**
	 * Adds localized data for rendering.
	 *
	 * @since 1.0.0
	 *
	 * @global Render $Render The main Render object.
	 *
	 * @param array   $data   The previous rendering data.
	 * @return array The new rendering data.
	 */
	public function rendering_data( $data ) {

		global $Render;

		// WP shortcode regex
		$data['shortcode_regex'] = get_shortcode_regex();

		// Whether or not to render at all
		if ( ! $data['do_render'] = get_option( 'render_render_visual', true ) ) {
			return $data;
		}

		// Provides a list of shortcodes that allow rendering
		$rendered = array();
		foreach ( $Render->shortcodes as $code => $shortcode ) {
			if ( $shortcode['render'] ) {
				$rendered[ $code ] = $shortcode['render'];
			}
		}
		$data['rendered_shortcodes'] = $rendered;

		/**
		 * Extra filterable data used when rendering in the TinyMCE.
		 *
		 * @since 1.0.0
		 */
		$data['render_data'] = apply_filters( 'render_rendering_data',
			array(
				'post' => isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : 0,
			)
		);

		// Messages to display when loading
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

	public static function render_ajax() {

		global $post;

		if ( isset( $_REQUEST['post'] ) ) {
			$post = get_post( $_REQUEST['post'] );
		}
	}

	public static function render_shortcodes() {

		global $render_shortcode_data, $Render;

		define( 'RENDER_TINYMCE', true );

		// Remove any disabled shortcodes
		foreach ( render_get_disabled_shortcodes() as $code ) {
			$Render->remove_shortcode( $code );
		}

		/**
		 * Allows hooking into the tinymce AJAX rendering call.
		 *
		 * Plugins may find this useful to globalize data for their tinymce shortcode callback.
		 *
		 * @hooked $this->render_ajax() 10
		 *
		 * @since 1.0.0
		 */
		do_action( 'render_tinymce_ajax' );

		$render_shortcode_data = $_POST['shortcode_data'];

		$content = stripslashes( $_POST['content'] );

		$pattern = get_shortcode_regex();

		$content = render_strip_paragraphs_around_shortcodes( $content );
		$content = preg_replace_callback( "/$pattern/s", array( __CLASS__, '_replace_shortcodes' ), $content );

		echo $content;

		die();
	}

	/**
	 * Callback for RegEx replacement of shortcodes.
	 *
	 * Calls and wraps all shortcodes in the content.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @global array $render_shortcode_data Extra data supplied from JS.
	 * @global array $shortcode_tags WP registered shortcodes.
	 *
	 * @param array $matches Matches supplied from preg_replace_callback(),
	 * @return string The substituted output.
	 */
	public static function _replace_shortcodes( $matches ) {

		global $render_shortcode_data, $shortcode_tags;

		// "Extract" some of the found matches
		$entire_code = $matches[0];
		$code        = $matches[2];
		$atts        = $matches[3];
		$_content    = $matches[5];

		// Search again for any nested shortcodes (loops infinitely)
		if ( ! empty( $_content ) ) {
			$pattern = get_shortcode_regex();
			$content = preg_replace_callback( "/$pattern/s", array( __CLASS__, '_replace_shortcodes' ), $_content );
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

		// Properly wrap the content
		if ( ! empty( $content ) ) {

			// Wrap the content in a special element, but first decide if it needs to be div or span
			$tag     = preg_match( render_block_regex(), $content ) ? 'div' : 'span';
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

		// Check for tinymce callback
		if ( is_callable( $shortcode_tags[ $code ] . '_tinymce' ) ) {
			$shortcode_tags[ $code ] = $shortcode_tags[ $code ] . '_tinymce';
		}

		// Get the shortcode output (if rendering set)
		if ( ! isset( $render_shortcode_data[ $code ] ) ) {
			$shortcode_output = $entire_code;
		} elseif ( isset( $render_shortcode_data[ $code ]['useText'] ) ) {
			$shortcode_output = $render_shortcode_data[ $code ]['useText'];
		} else {
			$shortcode_output = do_shortcode( $entire_code );
		}

		// If the output contains any block tags, make sure the wrapper tag is a div
		$tag = preg_match( render_block_regex(), $shortcode_output ) ? 'div' : 'span';

		// Override tag
		$tag = isset( $render_shortcode_data[ $code ]['displayBlock'] ) ? 'div' : $tag;

		$output = '';

		// Start the wrapper
		if ( ! isset( $render_shortcode_data[ $code ]['noWrap'] ) ) {

			if ( ! empty( $atts ) ) {
				$atts = htmlentities( preg_replace( '/<br.*?\/>/', '::br::', $atts ) );
			}

			$output .= "<$tag class='render-tinymce-shortcode-wrapper render-tinymce-noneditable $code $nostyle' data-code='$code' data-atts='$atts'>";
		}

		$output .= ! empty( $shortcode_output ) ? $shortcode_output : '<span class="render-shortcode-no-output">(no output)</span>';

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

// Always add the AJAX
add_action( 'render_tinymce_ajax', array( 'Render_tinymce', 'render_ajax' ) );
add_action( 'wp_ajax_render_render_shortcode', array( 'Render_tinymce', 'render_shortcode' ) );
add_action( 'wp_ajax_render_render_shortcodes', array( 'Render_tinymce', 'render_shortcodes' ) );

// Instantiates the class if on a screen that uses it
add_action( 'current_screen', '_render_init_tinymce' );

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