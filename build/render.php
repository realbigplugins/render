<?php
/*
 * Plugin Name: Render
 * Description: Render makes adding complex elements to your content simple and pain free. See what you can Render.
 * Version: 1.0.0
 * Author: Kyle Maurer & Joel Worsham
 * Author URI: http://realbigmarketing.com
 * Plugin URI: http://renderwp.com
 * Text Domain: Render
 * Domain Path: /languages/
 */

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Licensing
require_once __DIR__ . '/core/licensing/licensing.php';

// Define all plugin constants.

/**
 * The version of Render.
 *
 * @since 1.0.0
 */
define( 'RENDER_VERSION', '1.0.0' );

/**
 * The absolute server path to Render's root directory.
 *
 * @since 1.0.0
 */
define( 'RENDER_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The URI to Render's root directory.
 *
 * @since 1.0.0
 */
define( 'RENDER_URL', plugins_url( '', __FILE__ ) );

/**
 * The primary color of Render.
 *
 * @since 1.0.0
 */
define( 'RENDER_PRIMARY_COLOR', '#50A4B3' );

/**
 * The dark version of the primary color of Render.
 *
 * Primarily used for hovering.
 *
 * @since 1.0.0
 */
define( 'RENDER_PRIMARY_COLOR_DARK', '#39818E' );

/**
 * The primary font color used throughout Render.
 *
 * @since 1.0.0
 */
define( 'RENDER_PRIMARY_FONT_COLOR', '#fff' );

if ( ! class_exists( 'Render' ) ) {

	/**
	 * Class Render
	 *
	 * The main class for Render. This class is what sets the plugin into motion. It requires files, adds actions, and
	 * setups up any initial requirements.
	 *
	 * @since   1.0.0
	 *
	 * @package Render
	 */
	class Render {

		/**
		 * This will contain the self instance. Prevents duplicate instantiations.
		 *
		 * @since 1.0.0
		 *
		 * @var null|Object
		 */
		private static $_instance = null;

		/**
		 * This is where ALL shortcodes will exist.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		public $shortcodes = array();

		/**
		 * Copy of $shortcodes, but not effected by anything that removes shortcodes.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		public $admin_shortcodes = array();

		/**
		 * All core shortcodes to include.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		private static $_shortcodes_extensions = array(
			'core'      => array(
				'design',
				'post',
				'site',
				'time',
				'user',
				'visibility',
				'query',
			),
			'wordpress' => array(
				'media',
			),
		);

		/**
		 * Constructs the class.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			add_action( 'init', array( __CLASS__, 'pre_init' ), 0.1 );
			add_action( 'init', array( $this, 'post_init' ), 100 );
		}

		private final function __clone() {
		}

		private final function __sleep() {
			throw new Exception( 'Serializing of Render is not allowed' );
		}

		/**
		 * Returns self. Makes sure it only happens once.
		 *
		 * @since 1.0.0
		 *
		 * @return Render Self.
		 * @throws Exception If trying to instantiate again.
		 */
		public static function _getInstance() {
			if ( self::$_instance === null ) {
				self::$_instance = new self();

				return self::$_instance;
			} else {
				throw new Exception( 'You may only instantiate this class once' );
			}
		}

		/**
		 * Initializes and loads the plugin early stages.
		 *
		 * @since 1.0.0
		 */
		public static function pre_init() {

			// Initialize functions
			self::_require_files();

			if ( is_admin() ) {
				self::_admin();
			}

			// Files and scripts
			self::_register_files();
			add_action( 'wp_enqueue_scripts', array( __CLASS__, '_enqueue_files' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, '_admin_enqueue_files' ) );

			// Translations
			load_plugin_textdomain( 'Render', false, RENDER_PATH . 'languages' );

			// Filter content
			add_filter( 'the_content', 'render_strip_paragraphs_around_shortcodes' );

			// Initialize Render shortcodes
			self::_shortcodes_init();
		}

		/**
		 * Initializes and loads the plugin late stages.
		 *
		 * @since 1.0.0
		 */
		public function post_init() {

			// Add shortcodes
			self::add_shortcodes();

			// Remove disabled shortcodes
			if ( ! is_admin() ) {
				$this->remove_disabled_shortcodes();
			}

			// Add editor styles
			self::add_editor_styles();
		}

		/**
		 * Requires all plugin necessities.
		 *
		 * @since 1.0.0
		 */
		private static function _require_files() {

			// Global functions
			require_once __DIR__ . '/core/functions.php';

			// Any page requiring tinymce functionality
			require_once __DIR__ . '/core/tinymce.php';

			// Any page requiring widget functionality
			require_once __DIR__ . '/core/widget.php';
		}

		/**
		 * Admin-only related tasks.
		 *
		 * Includes menu pages and files.
		 *
		 * @since 1.0.0
		 */
		private static function _admin() {

			add_action( 'admin_menu', 'admin_page' );

			function admin_page() {
				add_menu_page(
					'Render',
					'Render',
					'manage_options',
					'render-settings',
					null,
					'dashicons-admin-generic',
					82.9
				);
			}

			include_once __DIR__ . '/core/admin/settings.php';
			include_once __DIR__ . '/core/admin/shortcodes.php';
		}

		/**
		 * Adds all Render shortcodes.
		 *
		 * @since 1.0.0
		 */
		public static function _shortcodes_init() {

			// Cycle through all Render categories and shortcodes, requiring category files and adding each shortcode
			foreach ( self::$_shortcodes_extensions as $type => $categories ) {
				foreach ( $categories as $category ) {
					require_once RENDER_PATH . "core/shortcodes/$type/$category.php";
				}
			}
		}

		/**
		 * Registers the Render javascript and css files.
		 *
		 * @since 1.0.0
		 */
		public static function _register_files() {

			wp_register_style(
				'render',
				RENDER_URL . "/assets/css/render.min.css",
				null,
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			wp_register_style(
				'render-admin',
				RENDER_URL . "/assets/css/render-admin.min.css",
				null,
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			wp_register_style(
				'render-chosen',
				RENDER_URL . '/includes/chosen/chosen.min.css',
				null,
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			wp_register_script(
				'render',
				RENDER_URL . "/assets/js/render.min.js",
				array( 'jquery' ),
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			wp_register_script(
				'render-admin',
				RENDER_URL . "/assets/js/render-admin.min.js",
				array( 'jquery' ),
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			wp_register_script(
				'render-chosen',
				RENDER_URL . '/includes/chosen/chosen.jquery.min.js',
				array( 'jquery' ),
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);
		}

		/**
		 * Enqueues the Render javascript and css files.
		 *
		 * @since 1.0.0
		 */
		public static function _enqueue_files() {

			wp_enqueue_script( 'render' );
			wp_enqueue_style( 'render' );

			// Dashicons
			wp_enqueue_style( 'dashicons' );
		}

		/**
		 * Enqueues the admin Render javascript and css files.
		 *
		 * @since 1.0.0
		 */
		public static function _admin_enqueue_files() {

			wp_localize_script( 'render-admin', 'Render_Data', apply_filters( 'render_localized_data', array() ) );

			wp_enqueue_script( 'render-admin' );
			wp_enqueue_style( 'render-admin' );
		}

		/**
		 * Adds all shortcodes into Render and / or WordPress.
		 *
		 * @since 1.0.0
		 *
		 * @global Render $Render         The main Render object.
		 * @global array  $shortcode_tags All registered shortcodes.
		 */
		public static function add_shortcodes() {

			global $Render, $shortcode_tags;

			// Add in all existing shortcode tags first (to account for "unregistered with Render" shortcodes)
			if ( ! empty( $shortcode_tags ) ) {
				foreach ( $shortcode_tags as $code => $callback ) {

					$shortcode             = array();
					$shortcode['code']     = $code;
					$shortcode['function'] = $callback;

					// Add shortcode to Render
					add_filter( 'render_add_shortcodes', function ( $shortcodes ) use ( $shortcode ) {
						$shortcodes[] = $shortcode;

						return $shortcodes;
					} );
				}
			}

			$shortcodes = apply_filters( 'render_add_shortcodes', false );

			if ( $shortcodes !== false ) {

				foreach ( $shortcodes as $args ) {

					/**
					 * Defaults for the shortcode.
					 *
					 * Allows external plugins to modify the defaults for a Render shortcode setup.
					 *
					 * @since 1.0.0
					 *
					 * @param array    $defaults    {
					 * @var string     $code        The shortcode "code" itself.
					 * @var string     $function    The callback function for the shortcode.
					 * @var string     $title       Title to show when identifying shortcode.
					 * @var string     $description Description to show when identifying shortcode.
					 * @var string     $source      Where the shortcode comes from (EG: Render, WordPress, Gravity Forms).
					 * @var string     $tags        Searchable tags that describe the shortcode (comma delimited).
					 * @var array      $category    Category for the shortcode (must be a registered category).
					 * @var array      $atts        Shortcode attributes.
					 * @var string     $example     Example of shortcode in use.
					 * @var bool       $wrapping    Whether or not this shortcode accepts content.
					 * @var bool|array $render      Whether or not to render this shortcode, also accepts properties.
					 * @var bool       $noDisplay   Hides the shortcode from the modal if set to true.
					 *                              }
					 * @param array    $args        The current shortcode args.
					 */
					$defaults = apply_filters( 'render_shortcode_defaults', array(
						'code'        => '',
						'function'    => '',
						'title'       => render_translate_id_to_name( $args['code'] ),
						'description' => '',
						'source'      => __( 'Unknown', 'Render' ),
						'tags'        => '',
						'category'    => 'other',
						'atts'        => array(),
						'example'     => '',
						'wrapping'    => false,
						'render'      => false,
						'noDisplay'   => false,
					), $args );
					$args     = wp_parse_args( $args, $defaults );

					/**
					 * Defaults for a shortcode attribute.
					 *
					 * Allows external plugins to modify defaults for a Render shortcode attribute.
					 *
					 * @since 1.0.0
					 *
					 * @param array $att_defaults {
					 * @var bool    $required     Whether or not this attribute is required for the shortcode.
					 * @var bool    $disabled     Disables the attribute if set to true.
					 *                            }
					 * @param array $args         The current shortcode args.
					 */
					$att_defaults = apply_filters( 'render_shortcode_att_defaults', array(
						'required' => false,
						'validate' => array(),
						'sanitize' => array(),
					), $args );

					// Establish default attribute properties (if any exist)
					if ( ! empty( $args['atts'] ) ) {

						foreach ( $args['atts'] as $i => $att ) {
							$args['atts'][ $i ] = wp_parse_args( $args['atts'][ $i ], $att_defaults );
						}
					}

					// Add the wrapping property to the render data
					if ( $args['render'] ) {
						if ( ! is_array( $args['render'] ) ) {
							$args['render'] = array();
						}
						$args['render']['wrapping'] = $args['wrapping'];
					}

					// Create the actual shortcode if it hasn't yet been created
					if ( ! array_key_exists( $args['code'], $shortcode_tags ) ) {
						add_shortcode( $args['code'], $args['function'] );
					}

					// Add the shortcode info to our list if it hasn't yet been added
					if ( empty( $Render->shortcodes ) || ! array_key_exists( $args['code'], $Render->shortcodes ) ) {

						// Code will be used for the key
						$code = $args['code'];
						unset( $args['code'] );

						$Render->shortcodes[ $code ] = $args;
					}
				}
			}
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

			$styles = array(
				RENDER_URL . '/assets/css/render.min.css',
			);

			if ( isset( $_wp_theme_features['render'] ) && is_array( $_wp_theme_features['render'] ) ) {
				$styles = array_merge( $styles, $_wp_theme_features['render'] );
			}

			/**
			 * Allows developers to easily add or remove Render added styles from TinyMCE.
			 *
			 * @since 1.0.0
			 */
			$styles = apply_filters( 'render_editor_styles', $styles );

			foreach ( $styles as $style ) {
				add_editor_style( $style );
			}
		}

		/**
		 * Removes a shortcode from Render.
		 *
		 * @since 1.0.0
		 *
		 * @param string $code The shortcode to remove.
		 */
		public function remove_shortcode( $code ) {
			unset( $this->shortcodes[ $code ] );
			remove_shortcode( $code );
		}

		/**
		 * Removes all shortcodes disabled through Render on the front-end.
		 *
		 * @hooked init 10
		 *
		 * @since  1.0.0
		 */
		public function remove_disabled_shortcodes() {

			foreach ( render_get_disabled_shortcodes() as $code ) {
				$this->remove_shortcode( $code );
			}
		}
	}

	// Instantiate the class and then initialize the shortcodes
	$Render = Render::_getInstance();
}