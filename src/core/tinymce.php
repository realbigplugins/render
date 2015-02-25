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
		add_filter( 'mce_buttons', array( __CLASS__, 'remove_tinymce_buttons' ), 10000 );
		add_filter( 'tiny_mce_before_init', array( __CLASS__, 'modify_tinymce_init' ) );

		// Localize data for rendering in the TinyMCE
		add_action( 'render_localized_data', array( $this, 'rendering_data' ) );

		// Localize translations
		add_action( 'render_localized_data', array( __CLASS__, '_translations' ) );

		// Add a pointer
		add_filter( 'render_pointers', function ( $pointers ) {

			$pointers['tinymce_button'] = array(
				'title' => __( 'Add A Shortcode', 'Render' ),
				'content' => __( 'This is your new, easy way to add shortcodes to the editor. Click here to get started!', 'Render' ),
				'target' => 'i.mce-i-render-mce-icon',
				'position' => array(
					'edge' => 'bottom',
					'align' => 'center',
				),
				'trigger' => 'render-tinymce-post-render',
				'classes' => 'tinymce-pointer'
			);

			return $pointers;
		});
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
	 * Removes TinyMCE buttons that have been added via Render extensions.
	 *
	 * @since 1.0.3
	 *
	 * @param mixed|array $buttons All tinyMCE buttons.
	 *
	 * @return mixed|array
	 */
	public static function remove_tinymce_buttons( $buttons ) {

		/**
		 * Allow extensions to remove TinyMCE Media Buttons.
		 *
		 * @since 1.0.3
		 */
		$media_buttons = apply_filters( 'render_disabled_tinymce_media_buttons', array() );

		/** This filter is documented in src/core/licensing/settings.php */
		foreach ( (array) apply_filters( 'render_disabled_tinymce_buttons', array() ) as $button_ID => $button_label ) {

			// Remove unless that button has been enabled
			if ( get_option( "render_enable_tinymce_button_$button_ID" ) != 'enabled' ) {

				// Different method for media buttons
				if ( isset( $media_buttons[ $button_ID ] ) ) {
					continue;
				}

				if ( ( $key = array_search( $button_ID, $buttons ) ) !== false ) {
					unset( $buttons[ $key ] );
				}
			}
		}

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
	 * Provides translations for TinyMCE pages.
	 *
	 * @since  {{VERSION}}
	 * @access private
	 *
	 * @param array $data The current localization data.
	 * @return array The new localization data.
	 */
	static function _translations( $data ) {

		$data['l18n']['add_shortcode'] = __( 'Add Shortcode', 'Render' );
		$data['l18n']['select_content_from_editor'] = __( 'Please select content from the editor to enable this shortcode.', 'Render' );
		$data['l18n']['cannot_place_shortcode_here'] = __( 'You cannot place this shortcode here.', 'Render' );

		return $data;
	}

	/**
	 * Adds localized data for rendering.
	 *
	 * @since 1.0.0`
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

	/**
	 * Fires when rendering the TinyMCE.
	 *
	 * Adds in some functionality needed for some shortcode TinyMCE callbacks to work in the backend.
	 *
	 * @since 1.0.0
	 */
	public static function render_ajax() {

		global $post, $content, $wp_query;

		if ( isset( $_REQUEST['post'] ) ) {

			$wp_query = new WP_Query( array(
				'p' => $_REQUEST['post'],
			));

			// Could be a page, and for some reason that requires a different parameter
			if ( $wp_query->post_count === 0 ) {
				$wp_query = new WP_Query( array(
					'page_id' => $_REQUEST['post'],
				));
			}

			if ( $post = get_post( $_REQUEST['post'] ) ) {
				$post->post_content = $content;
			}
		}
	}

	/**
	 * Renders shortcodes in the TinyMCE.
	 *
	 * This is the AJAX callback for rendering shortcodes to be previewed in the TinyMCE editor.
	 *
	 * @since 1.0.0
	 */
	public static function render_shortcodes() {

		global $render_shortcode_data, $Render, $content;

		define( 'RENDER_TINYMCE', true );

		$render_shortcode_data = $_POST['shortcode_data'];
		$content               = stripslashes( $_POST['content'] );

		// Remove any disabled shortcodes
		foreach ( render_get_disabled_shortcodes() as $code ) {
			$Render->remove_shortcode( $code );
		}

		/**
		 * Allows hooking into the tinymce AJAX rendering call.
		 *
		 * Plugins may find this useful to globalize data for their tinymce shortcode callback.
		 *
		 * @hooked $this->render_ajax() 1
		 *
		 * @since 1.0.0
		 */
		do_action( 'render_tinymce_ajax' );

		/**
		 * Log out for TinyMCE display purposes.
		 *
		 * @since 1.0.0
		 */
		if ( apply_filters( 'render_tinyme_logged_out', false ) ) {
			render_tinyme_log_out();
		}

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
	 * @since  1.0.0
	 * @access private
	 *
	 * @global array $render_shortcode_data Extra data supplied from JS.
	 * @global array $shortcode_tags        WP registered shortcodes.
	 *
	 * @param array  $matches               Matches supplied from preg_replace_callback(),
	 * @return string The substituted output.
	 */
	public static function _replace_shortcodes( $matches ) {

		global $render_shortcode_data, $shortcode_tags;

		// "Extract" some of the found matches
		$entire_code = $matches[0];
		$code        = $matches[2];
		$atts        = $matches[3];
		$_content    = $matches[5];

		// Get our shortcode data
		$data = $render_shortcode_data[ $code ];

		// Get our atts
		$atts = shortcode_parse_atts( $atts );

		// Nested shortcode children
		if ( isset( $data['nested']['parent'] ) ) {

			// Don't allow this shortcode to be edited
			$data['hideActions'] = true;

			// Set default dummy content
			if ( ! isset( $data['dummyContent'] ) ) {
				$data['dummyContent'] = '(Enter section content)';
			}
		}

		// Search again for any nested shortcodes (loops infinitely)
		if ( ! empty( $_content ) ) {
			$pattern = get_shortcode_regex();
			$content = preg_replace_callback( "/$pattern/s", array( __CLASS__, '_replace_shortcodes' ), $_content );
		}

		// If this is a wrapping code, but no content is provided, use dummy content
		if ( empty( $content ) &&
		     isset( $data['wrapping'] ) &&
		     $data['wrapping'] === 'true'
		) {
			if ( isset( $data['dummyContent'] ) ) {
				$content = $data['dummyContent'];
			} else {
				$content = __( 'No content selected.', 'Render' );
			}
		}

		// Properly wrap the content
		if ( ! empty( $content ) ) {

			// Wrap the content in a special element, but first decide if it needs to be div or span
			$tag      = preg_match( render_block_regex(), $content ) ? 'div' : 'span';

			// Override tag
			$tag = isset( $data['displayBlock'] ) ? 'div' : $tag;

			$editable = isset( $data['contentNonEditable'] ) ? '' : 'render-tinymce-editable';
			$content  = "<$tag class='render-tinymce-shortcode-content $editable'>$content</$tag>";
		}

		// Replace the content with the new content
		if ( ! empty( $content ) ) {
			$entire_code = str_replace( "]{$_content}[", "]{$content}[", $entire_code );
		}

		// Get the atts prepared for JSON
		if ( ! empty( $atts ) ) {
			$atts = json_encode( $atts );
		}

		// Check for tinymce callback
		if ( is_callable( $shortcode_tags[ $code ] . '_tinymce' ) ) {
			$shortcode_tags[ $code ] = $shortcode_tags[ $code ] . '_tinymce';
		}

		// Get the shortcode output (if rendering set)
		if ( ! isset( $data ) ) {
			$shortcode_output = $entire_code;
		} elseif ( isset( $data['useText'] ) ) {
			$shortcode_output = $data['useText'];
		} else {

			$shortcode_output = do_shortcode( $entire_code );

			// Un-escape the output
			$shortcode_output = render_sc_attr_unescape( $shortcode_output );
		}

		// If the output contains any block tags, make sure the wrapper tag is a div
		$tag = preg_match( render_block_regex(), $shortcode_output ) ? 'div' : 'span';

		// Override tag
		$tag = isset( $data['displayBlock'] ) ? 'div' : $tag;

		$classes = array();

		// The code
		$classes[] = $code;

		// Whether or not to style the code
		$classes[] = ! isset( $data['noStyle'] ) ? 'styled' : '';

		// If the code should be forced as displayBlock
		$classes[] = isset( $data['displayBlock'] ) ? 'block' : '';

		// If the shortcode is a nested child
		$classes[] = isset( $data['nested']['parent'] ) ? 'nested-child' : '';

		// Hidden tooltip
		$classes[] = isset( $data['hideActions'] ) ? 'hide-actions' : '';

		/**
		 * Allows external filtering of the wrapper classes.
		 *
		 * @since {{VERSION}}
		 */
		$classes = apply_filters( "render_tinymce_shortcode_wrapper_classes_$code", $classes );

		$output = '';

		// Start the wrapper

		if ( ! empty( $atts ) ) {
			$atts = htmlentities( preg_replace( '/<br.*?\/>/', '::br::', $atts ) );
		}

		$output .= "<$tag class='render-tinymce-shortcode-wrapper render-tinymce-noneditable " . implode( ' ', $classes ) . "' data-code='$code' data-atts='$atts'>";

		$output .= ! empty( $shortcode_output ) ? $shortcode_output : '<span class="render-shortcode-no-output">(no output)</span>';

		// Close the wrapper

		// Delete notification
		$output .= '<span class=\'render-tinymce-shortcode-wrapper-delete render-tinymce-tooltip\'>' . __( 'Press again to delete', 'Render' ) . "</span>";

		// Action button
		if ( ! isset( $data['hideActions'] ) ) {
			$output .= '<span class="render-tinymce-shortcode-wrapper-actions render-tinymce-tooltip">';
			$output .= '<span class="render-tinymce-tooltip-spacer"></span>';
			$output .= '<span class="render-tinymce-shortcode-wrapper-edit dashicons dashicons-edit">edit</span>';
			$output .= '<span class="render-tinymce-shortcode-wrapper-remove dashicons dashicons-no">remove</span>';
			$output .= '</span>';
		}

		$output .= "</$tag>";

		return $output;
	}
}

/**
 * Instantiates the class if on a screen that uses it.
 *
 * @since 1.0.0
 */
add_action( 'current_screen', function ( $screen ) {

	/**
	 * Allows external filtering of what screens the TinyMCE functionality can appear on.
	 *
	 * @since 1.0.0
	 */
	$allowed_screens = apply_filters( 'render_tinymce_allowed_screens', array(
		'post',
		'widgets',
		'customize',
	), $screen->base, $screen );

	if ( in_array( $screen->base, $allowed_screens ) ) {
		new Render_tinymce();
	}
} );

// Always add the AJAX
add_action( 'render_tinymce_ajax', array( 'Render_tinymce', 'render_ajax' ), 1 );
add_action( 'wp_ajax_render_render_shortcode', array( 'Render_tinymce', 'render_shortcode' ) );
add_action( 'wp_ajax_render_render_shortcodes', array( 'Render_tinymce', 'render_shortcodes' ) );