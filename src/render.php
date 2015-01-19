<?php
/*
 * Plugin header will go here (don't forget the text domain, and the domain path!)
 */

// Licensing
require_once __DIR__ . '/core/licensing/licensing.php';

define( 'RENDER_VERSION', '1.0.0' );
define( 'RENDER_PRIMARY_COLOR', '#50A4B3' );
define( 'RENDER_PRIMARY_COLOR_DARK', '#39818E' );
define( 'RENDER_PRIMARY_FONT_COLOR', '#fff' );
define( 'RENDER_PATH', plugin_dir_path( __FILE__ ) );
define( 'RENDER_URL',  plugins_url( '', __FILE__ ) );

if ( ! class_exists( 'Render' ) ) {
	/**
	 * Class Render
	 *
	 * The main class for Render. This class is what sets the plugin into motion. It requires files, adds actions, and
	 * setups up any initial requirements.
	 */
	class Render {

		/**
		 * This will contain the self instance. Prevents duplicate instantiations.
		 *
		 * @since Render 1.0.0
		 *
		 * @var null|Object
		 */
		private static $_instance = null;

		/**
		 * This is where ALL shortcodes will exist.
		 *
		 * @since Render 1.0.0
		 *
		 * @var array
		 */
		public $shortcodes = array();

		/**
		 * Default values for a shortcode.
		 *
		 * @since Render 1.0.0
		 *
		 * @var array
		 */
		public static $shortcode_defaults = array(
			'code'        => '',
			'function'    => '',
			'title'       => '',
			'description' => '',
			'source'      => 'Unknown',
			'tags'        => '',
			'category'    => 'other',
			'atts'        => array(),
			'example'     => '',
			'wrapping'    => false,
			'render'      => false,
			'noDisplay'   => false,
		);

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

		private function __construct() {

			// Initialize functions
			$this->_require_files();
			$this->_add_actions();
			$this->_admin();

			// Can't use functions in propertiy declaration
			self::$shortcode_defaults['source'] = __( 'Unknown', 'Render' );
		}

		private final function __clone() {
		}

		private final function __sleep() {
			throw new Exception( 'Serializing of Render is not allowed' );
		}

		/**
		 * Returns self. Makes sure it only happens once.
		 *
		 * @since Render 1.0.0
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
		 * Requires all plugin necessities.
		 *
		 * @since Render 1.0.0
		 */
		private function _require_files() {

			require_once( RENDER_PATH . 'core/tinymce.php' );
			require_once( RENDER_PATH . 'core/functions.php' );
			require_once( RENDER_PATH . 'core/widget.php' );
		}

		private function _admin() {

			if ( is_admin() ) {

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

				include_once __DIR__ . '/core/admin/options.php';
				include_once __DIR__ . '/core/admin/shortcodes.php';
//				include_once __DIR__ . '/core/admin/addons.php';
			}
		}

		public static function _disable_shortcodes() {

			foreach ( get_option( 'render_disabled_shortcodes', array() ) as $shortcode ) {
				remove_shortcode( $shortcode );
			}
		}

		/**
		 * Adds all Render shortcodes.
		 *
		 * @since Render 1.0.0
		 */
		public static function _shortcodes_init() {

			// Cycle through all Render categories and shortcodes, requiring category files and adding each shortcode
			foreach ( self::$_shortcodes_extensions as $type => $categories ) {
				foreach ( $categories as $category ) {
					require_once( RENDER_PATH . "core/shortcodes/$type/$category.php" );
				}
			}
		}

		/**
		 * Adds all startup WP actions.
		 *
		 * @since Render 1.0.0
		 */
		private function _add_actions() {

			// Files and scripts
			add_action( 'init', array( __CLASS__, '_register_files' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, '_enqueue_files' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, '_admin_enqueue_files' ) );

			// Disabled shortcodes
			add_action( 'init', array( __CLASS__, '_disable_shortcodes' ) );

			// Translations
			add_action( 'init', array( __CLASS__, 'i18n' ) );

			// Filter content
			add_filter( 'the_content', 'render_strip_paragraphs_around_shortcodes' );
		}

		/**
		 * Registers the Render javascript and css files.
		 *
		 * @since Render 1.0.0
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
		 * @since Render 1.0.0
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
		 * @since Render 1.0.0
		 */
		public static function _admin_enqueue_files() {

			wp_localize_script( 'render-admin', 'Render_Data', apply_filters( 'render_localized_data', array() ) );

			wp_enqueue_script( 'render-admin' );
			wp_enqueue_style( 'render-admin' );
		}

		public static function i18n() {
			load_plugin_textdomain( 'Render', false, RENDER_PATH . 'languages' );
		}
	}

	// Instantiate the class and then initialize the shortcodes
	$Render = Render::_getInstance();
	$Render::_shortcodes_init();
}