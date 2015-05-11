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

		// Enqueue styles and scripts for TinyMCE
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );

		// Localize data for rendering in the TinyMCE
		add_action( 'render_localized_data', array( $this, 'rendering_data' ) );

		// Localize translations
		add_action( 'render_localized_data', array( __CLASS__, '_translations' ) );

		// Add a pointer
		add_filter( 'render_pointers', array( __CLASS__, 'add_pointers' ) );

		// Add styles
		add_filter( 'render_editor_styles', array( __CLASS__, 'add_render_editor_styles' ), 100 );

		// Add editor styles
		self::add_editor_styles();

		// Output shortcode content editor
		add_action( 'admin_footer', array( __CLASS__, '_output_shortcode_content_editor' ) );
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
	 * @param mixed|array $buttons All TinyMCE buttons.
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
	 * Adds the TinyMCE pointer.
	 *
	 * @since {{VERSION}}
	 *
	 * @param array $pointers The pointers to use.
	 *
	 * @return array The new pointers.
	 */
	public static function add_pointers( $pointers ) {

		$pointers['tinymce_button'] = array(
			'title'    => __( 'Add A Shortcode', 'Render' ),
			'content'  => __( 'This is your new, easy way to add shortcodes to the editor. Click here to get started!', 'Render' ),
			'target'   => 'i.mce-i-render-mce-icon',
			'position' => array(
				'edge'  => 'bottom',
				'align' => 'center',
			),
			'trigger'  => 'render-tinymce-post-render',
			'classes'  => 'tinymce-pointer'
		);

		return $pointers;
	}

	/**
	 * Loads in included scripts needed for the TinyMCE functionality.
	 *
	 * @since {{VERSION}}
	 */
	static function admin_scripts() {
		wp_enqueue_script( 'jquery-effects-scale' );
	}

	/**
	 * Adds the Render specific editor styles.
	 *
	 * @since {{VERSION}}
	 *
	 * @param array $styles The editor styles.
	 *
	 * @return array The new styles
	 */
	public static function add_render_editor_styles( $styles ) {

		$styles[] = RENDER_URL . '/assets/css/render.min.css';
		$styles[] = RENDER_URL . '/assets/css/render-tinymce.min.css';

		return $styles;
	}

	/**
	 * Easy way of adding extra styles to TinyMCE, via Render.
	 *
	 * This is also where add_theme_support() for Render will add the custom stylesheet.
	 *
	 * @since 1.0.0
	 */
	public static function add_editor_styles() {

		global $_wp_theme_features;

		$styles = array();

		if ( isset( $_wp_theme_features['render'] ) && is_array( $_wp_theme_features['render'] ) ) {
			$styles = array_merge( $styles, $_wp_theme_features['render'] );
		}

		/**
		 * Allows developers to easily add or remove Render added styles from TinyMCE.
		 *
		 * @since  1.0.0
		 *
		 * @hooked Render_tinymce::add_render_editor_styles 100
		 */
		$styles = apply_filters( 'render_editor_styles', $styles );

		foreach ( (array) $styles as $style ) {
			add_editor_style( $style );
		}
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

		$mceinit['noneditable_noneditable_class'] = 'render-tinymce-shortcode-noneditable';
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
	 *
	 * @return array The new localization data.
	 */
	static function _translations( $data ) {

		$data['l18n']['add_shortcode']               = __( 'Add Shortcode', 'Render' );
		$data['l18n']['select_content_from_editor']  = __( 'Please select content from the editor to enable this shortcode.', 'Render' );
		$data['l18n']['cannot_place_shortcode_here'] = __( 'You cannot place this shortcode here.', 'Render' );
		$data['l18n']['cannot_nest_identical']       = __( 'Cannot nest identical shortcodes.', 'Render' );

		// Translators: Please do not translate anything between curly-brackets: {shortcode1}, {shortcode2}. You may move them,
		// but don't delete them. They will be replaced with the shortcode's being edited (EG: accordion, tab, button, etc.).
		$data['l18n']['cannot_edit_sc_content']        = __( 'Update the content of this {shortcode1} first.', 'Render' );
		$data['l18n']['cannot_edit_sc_content_detail'] = __( 'Update this {shortcode1} content, and then click on the {shortcode2} to edit the {shortcode2} content.', 'Render' );

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
	 *
	 * @return array The new rendering data.
	 */
	public function rendering_data( $data ) {

		global $Render;

		// Block regex
		$data['block_regex'] = render_block_regex();

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
			) );

			// Could be a page, and for some reason that requires a different parameter
			if ( $wp_query->post_count === 0 ) {
				$wp_query = new WP_Query( array(
					'page_id' => $_REQUEST['post'],
				) );
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
	public static function render_shortcode() {

		global $render_shortcode_data, $Render, $content;

//		$render_shortcode_data = $_POST['shortcode_data'];
//		$content               = stripslashes( $_POST['content'] );

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

		$post_ID = stripslashes( $_POST['post_ID'] );
		$shortcode = stripslashes( $_POST['shortcode'] );

		// Setup current postdata
		if ( ! empty( $post_ID ) ) {

			global $post;
			$post = get_post( $post_ID );
			setup_postdata( $post );
		}

		if ( ! empty( $shortcode ) ) {
			$shortcode = do_shortcode( $shortcode );
		}

		wp_send_json_success( $shortcode );

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
	 *
	 * @return string The substituted output.
	 */
	public static function _replace_shortcodes( $matches ) {

		global $render_shortcode_data, $shortcode_tags, $Render;

		static $parent;

		// "Extract" some of the found matches
		$entire_code = $matches[0];
		$code        = $matches[2];
		$atts        = $matches[3];
		$_content    = $matches[5];

		// Declare shortcode parent
		if ( $parent === null ) {
			$parent = $code;
		}

		// Get our shortcode data
		if ( isset( $render_shortcode_data [ $code ] ) ) {
			$data = $render_shortcode_data[ $code ];
		}

		// If no data is provided, do not render
		if ( ! isset( $data ) ) {
			return $entire_code;
		}

		// If the shortcode explicitly said to leave alone, completely pass over
		if ( isset( $data['ignore'] ) && ( $data['ignore'] == true || $data['ignore'] == 'true' ) ) {
			return $entire_code;
		}

		// Get our atts
		$atts = shortcode_parse_atts( $atts );

		// Nested shortcode children
		if ( isset( $data['nested']['parent'] ) ) {

			// Don't allow this shortcode to be edited
			$data['hideActions'] = true;

			// Set default dummy content
			if ( ! isset( $data['dummyContent'] ) ) {
				$data['dummyContent'] = 'Enter section content';
			}

			$data['dummyContent'] = "<span class=\"render-tinymce-shortcode-placeholder\">$data[dummyContent]</span>";
		}

		// Search again for any nested shortcodes (loops infinitely)
		if ( ! empty( $_content ) ) {
			$pattern = get_shortcode_regex();
			$content = preg_replace_callback( "/$pattern/s", array( __CLASS__, '_replace_shortcodes' ), $_content );
		}

		// If this is a wrapping code, but no content is provided, use dummy content
		if ( empty( $content ) &&
		     isset( $data['wrapping'] ) &&
		     $data['wrapping'] == 'true'
		) {
			if ( isset( $data['dummyContent'] ) ) {
				$content = $data['dummyContent'];
			} else {
				$content = __( 'No content selected.', 'Render' );
			}
		}

		$tag = isset( $data['displayInline'] ) ? 'span' : 'div';

		// Unrecognized shortcodes should be span
		$tag = $Render->shortcodes[ $code ]['category'] == 'other' ? 'span' : $tag;

		// Properly wrap the content
		if ( ! empty( $content ) ) {

			$content = "<$tag class='render-tinymce-shortcode-content'>$content</$tag>";
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

			// Make sure images are non-editable (unless told otherwise)
			if ( ! isset( $data['wrapping'] ) || $data['wrapping'] == 'false' ) {
				$shortcode_output = preg_replace(
					'/<img/',
					'<img data-mce-placeholder="1" style="outline: none !important;"',
					$shortcode_output
				);
			}
		}

		$classes = array();

		// The code
		$classes[] = $code;

		// Whether or not to style the code
		$classes[] = ! isset( $data['noStyle'] ) ? 'styled' : '';

		// Wrapping class
		$classes[] = isset( $data['wrapping'] ) && $data['wrapping'] == 'true' ? 'wrapping' : '';

		// If the code should be forced as displayBlock
		$classes[] = isset( $data['displayBlock'] ) ? 'block' : '';

		// If the shortcode is a nested child
		$classes[] = isset( $data['nested']['parent'] ) ? 'nested-child' : '';

		// Hidden tooltip
		$classes[] = isset( $data['hideActions'] ) ? 'hide-actions' : '';

		// Noneditable only if we're in a top-level shortcode
		$classes[] = $parent == $code ? 'render-tinymce-shortcode-noneditable' : '';

		/**
		 * Allows external filtering of the wrapper classes.
		 *
		 * @since {{VERSION}}
		 */
		$classes = apply_filters( "render_tinymce_shortcode_wrapper_classes_$code", $classes );

		$classes = array_filter( $classes );

		// Parse the atts
		if ( ! empty( $atts ) ) {
			$atts = htmlentities( preg_replace( '/<br.*?\/>/', '::br::', $atts ) );
		}

		$name = $Render->shortcodes[ $code ]['title'];

		// Start the wrapper
		$output = '';

		// If block element, provide special wrapper
		if ( $tag == 'div' && ! isset( $data['nested']['parent'] ) ) {
//			$output .= '<div class="render-tinymce-shortcode-container wpview-wrap"><p class="wpview-selection-before"></p>';
//			$output .= '<div class="render-tinymce-shortcode-container wpview-wrap">';
			$classes[] = 'wpview-wrap';
		}

		$output .= "<$tag class='render-tinymce-shortcode-wrapper " . implode( ' ', $classes ) . "' data-code='$code' data-atts='$atts' data-name='$name'>";

		if ( $tag == 'div' && ! isset( $data['nested']['parent'] ) ) {
			$output .= '<p class="wpview-selection-before"></p>';
		}

		$output .= ! empty( $shortcode_output ) ? $shortcode_output : '<span class="render-shortcode-no-output">(no output)</span>';

		// Close the wrapper

		// Change this so no edit content button is produced in the sc content editor
		if ( isset( $data['nested']['child'] ) ) {
			$data['wrapping'] = false;
		}

		// Possibly disable the edit
		$disable_edit = $_POST['editor_id'] == 'render-tinymce-shortcode-content' ? 'disabled' : '';

		$edit_content = isset( $data['wrapping'] ) && $data['wrapping'] === 'true' ? 'render-tinymce-edit-content' : '';

		// Unrecognized shortcodes should have edit content button, if there is content
		if ( $Render->shortcodes[ $code ]['category'] == 'other' && isset( $content ) && ! empty( $content ) ) {
			$data['wrapping'] = 'true';
		}

		// Action button
		if ( ! isset( $data['hideActions'] ) ) {
			$output .= '<span class="render-tinymce-shortcode-wrapper-actions render-tinymce-tooltip ' . $edit_content . '">';
			$output .= '<span class="render-tinymce-tooltip-spacer"></span>';

			if ( isset( $data['wrapping'] ) && $data['wrapping'] === 'true' ) {
				$output .= '<span class="render-tinymce-shortcode-wrapper-edit-content dashicons dashicons-edit ' . $disable_edit . '">edit content</span>';
			}

			$output .= '<span class="render-tinymce-shortcode-wrapper-edit dashicons render-icon-render-logo-condensed">edit</span>';
			$output .= '<span class="render-tinymce-shortcode-wrapper-remove dashicons dashicons-no">remove</span>';
			$output .= '</span>';
		}

		// If block element, provide special wrapper (close)
		if ( $tag == 'div' && ! isset( $data['nested']['parent'] ) ) {
			$output .= '<p class="wpview-selection-after"></p>';
		}

		$output .= "</$tag>";


		// Reset the parent
		if ( $parent == $code ) {
			$parent = null;
		}

		return $output;
	}

	static function _output_shortcode_content_editor() {
		?>
		<div id="render-tinymce-sc-content-editor">
			<div class="render-tinymce-sc-content-editor-container">

				<h1 class="render-tinymce-sc-content-editor-title">
					<?php _e( 'Edit', 'Render' ) ?>
					<span class="render-tinymce-sc-content-editor-title-sc-name"></span>
					<?php _e( 'Content', 'Render' ); ?>
				</h1>

				<?php
				wp_editor( '<div id="content"></div>', 'render-tinymce-shortcode-content', array(
					'textarea_rows' => 10,
				) );
				?>

				<div class="render-tinymce-sc-content-editor-error"></div>

				<div class="render-tinymce-sc-content-editor-actions">
					<a class="submitdelete deletion cancel" href="#"><?php _e( 'Cancel', 'Render' ); ?></a>

					<a href="#" class="submit button render-button">
						<?php _e( 'Update Content' ); ?>
					</a>
				</div>

				<div class="render-tinymce-sc-content-editor-cover"></div>
			</div>
		</div>
	<?php
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
add_action( 'wp_ajax_render_shortcode', array( 'Render_tinymce', 'render_shortcode' ) );
//add_action( 'wp_ajax_render_render_shortcodes', array( 'Render_tinymce', 'render_shortcodes' ) );