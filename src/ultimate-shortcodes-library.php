<?php
/*
 * Plugin header will go here (don't forget the text domain, and the domain path!)
 */

if ( ! class_exists( 'USL' ) ) {
	/**
	 * Class USL
	 *
	 * The main class for USL. This class is what sets the plugin into motion. It requires files, adds actions, and
	 * setups up any initial requirements.
	 */
	class USL {

		/**
		 * This will contain the self instance. Prevents duplicate instantiations.
		 *
		 * @since USL 1.0.0
		 *
		 * @var null|Object
		 */
		private static $_instance = null;

		/**
		 * USL's version.
		 *
		 * @since USL 1.0.0
		 *
		 * @var string
		 */
		CONST VERSION = '1.0.0';

		/**
		 * The path to the main plugin file.
		 *
		 * @since USL 1.0.0
		 *
		 * @var string
		 */
		public static $path;

		/**
		 * The url to the main plugin file.
		 *
		 * @since USL 1.0.0
		 *
		 * @var string
		 */
		public static $url;

		/**
		 * This is where ALL shortcodes will exist.
		 *
		 * @since USL 1.0.0
		 *
		 * @var array
		 */
		public $shortcodes = array();

		/**
		 * Default values for a shortcode.
		 *
		 * @since USL 1.0.0
		 *
		 * @var array
		 */
		public static $shortcode_defaults = array(
			'code'        => '',
			'function'    => '',
			'title'       => '',
			'description' => '',
			'category'    => 'other',
			'atts'        => array(),
			'example'     => '',
			'wrapping'    => false,
			'render'      => false,
		);

		private static $_shortcodes_extensions = array(
			'core'      => array(
				'design',
				'post',
				'site',
				'time',
				'user',
				'logic',
			),
			'wordpress' => array(
				'media',
			),
		);

		private function __construct() {

			// Set up the path and url
			self::$path = plugin_dir_path( __FILE__ );
			self::$url  = plugins_url( '', __FILE__ );

			// Initialize functions
			$this->_require_files();
			$this->_add_actions();
			$this->_admin();
		}

		private final function __clone() {
		}

		private final function __sleep() {
			throw new Exception( 'Serializing of USL is not allowed' );
		}

		/**
		 * Returns self. Makes sure it only happens once.
		 *
		 * @since USL 1.0.0
		 *
		 * @return USL Self.
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
		 * @since USL 1.0.0
		 */
		private function _require_files() {

			require_once( self::$path . 'core/tinymce.php' );
			require_once( self::$path . 'core/functions.php' );
			require_once( self::$path . 'core/widget.php' );
		}

		private function _admin() {

			if ( is_admin() ) {

				add_action( 'admin_menu', 'admin_page' );

				function admin_page() {
					add_menu_page(
						'Shortcodes',
						'Shortcodes',
						'manage_options',
						'usl-view-all-shortcodes',
						null,
						'dashicons-editor-code',
						82.9
					);
				}

				include_once( self::$path . 'core/admin/shortcodes.php' );
				include_once( self::$path . 'core/admin/options.php' );
				include_once( self::$path . 'core/admin/addons.php' );
			}
		}

		public static function _disable_shortcodes() {

			foreach ( get_option( 'usl_disabled_shortcodes', array() ) as $shortcode ) {
				remove_shortcode( $shortcode );
			}
		}

		/**
		 * Adds all USL shortcodes.
		 *
		 * @since USL 1.0.0
		 */
		public static function _shortcodes_init() {

			// Cycle through all USL categories and shortcodes, requiring category files and adding each shortcode
			foreach ( self::$_shortcodes_extensions as $type => $categories ) {
				foreach ( $categories as $category ) {
					require_once( self::$path . "core/shortcodes/{$type}/{$category}.php" );
				}
			}
		}

		/**
		 * Adds all startup WP actions.
		 *
		 * @since USL 1.0.0
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
		}

		/**
		 * Registers the USL javascript and css files.
		 *
		 * @since USL 1.0.0
		 */
		public static function _register_files() {

			wp_register_style(
				'usl',
				self::$url . "/assets/css/ultimate-shortcodes-library.min.css",
				null,
				defined( 'USL_DEVELOPMENT' ) ? time() : self::VERSION
			);

			wp_register_style(
				'usl-admin',
				self::$url . "/assets/css/ultimate-shortcodes-library-admin.min.css",
				null,
				defined( 'USL_DEVELOPMENT' ) ? time() : self::VERSION
			);

			wp_register_style(
				'usl-chosen',
				self::$url . '/includes/chosen/chosen.min.css',
				null,
				defined( 'USL_DEVELOPMENT' ) ? time() : self::VERSION
			);

			wp_register_script(
				'usl',
				self::$url . "/assets/js/ultimate-shortcodes-library.min.js",
				array( 'jquery' ),
				defined( 'USL_DEVELOPMENT' ) ? time() : self::VERSION
			);

			wp_register_script(
				'usl-admin',
				self::$url . "/assets/js/ultimate-shortcodes-library-admin.min.js",
				array( 'jquery' ),
				defined( 'USL_DEVELOPMENT' ) ? time() : self::VERSION
			);

			wp_register_script(
				'usl-chosen',
				self::$url . '/includes/chosen/chosen.jquery.js',
				array( 'jquery' ),
				defined( 'USL_DEVELOPMENT' ) ? time() : self::VERSION
			);
		}

		/**
		 * Enqueues the USL javascript and css files.
		 *
		 * @since USL 1.0.0
		 */
		public static function _enqueue_files() {

			wp_enqueue_script( 'usl' );
			wp_enqueue_style( 'usl' );
		}

		/**
		 * Enqueues the admin USL javascript and css files.
		 *
		 * @since USL 1.0.0
		 */
		public static function _admin_enqueue_files() {

			wp_localize_script( 'usl-admin', 'USL_Data', apply_filters( 'usl_localized_data', array() ) );

			wp_enqueue_script( 'usl-admin' );
			wp_enqueue_style( 'usl-admin' );
		}

		public static function i18n() {
			load_plugin_textdomain( 'USL', false, self::$path . 'languages' );
		}
	}

	// Instantiate the class and then initialize the shortcodes
	$USL = USL::_getInstance();
	$USL::_shortcodes_init();
}