<?php
/*
 * Plugin Name: Ultimate Shortcodes Library
 * Description: This plugin is the only shortcode plugin you will ever need.
 * Version: 1.0.0
 * Author: Kyle Maurer
 * Author URI: http://realbigmarketing.com/staff/kyle
 */

/*
 * Lessons learned from...
 * https://github.com/GavickPro/TinyMCE-4-own-buttons
 * http://stackoverflow.com/questions/24339864/add-a-php-function-to-a-javascript-file-with-ajax-tinymce-wordpress-related
 */

/*
 * This plugin works by defining the $usl_codes array in the main file
 * Then it includes the admin page which displays all the available shortcodes.
 * Then it includes the shortcodes file which includes all the other files where
 * the actual shortcodes are created.
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
		public static $version = '1.0.0';

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
				'code' => '',
				'function' => '',
				'title' => '',
				'description' => '',
				'category' => 'other',
				'atts' => array(),
				'example' => '',
				'wrapping' => false,
		);

		private static $_shortcodes_extensions = array(
			'core' => array(
				'design',
				'meta',
				'site',
				'time',
				'user',
			),
			'wordpress' => array(
				'wordpress',
			),
		);

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

		private function __construct() {

			// Set up the path and url
			self::$path = plugin_dir_path( __FILE__ );
			self::$url  = plugins_url( '', __FILE__ );

			// Initialize functions
			$this->_require_files();
			$this->_add_actions();
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

			require_once( self::$path . 'core/functions.php' );
			require_once( self::$path . 'core/tinymce.php' );
			require_once( self::$path . 'core/admin/admin.php' );
			require_once( self::$path . 'core/widget.php' );
		}

		/**
		 * Adds all USL shortcodes.
		 *
		 * @since USL 1.0.0
		 */
		public static function _shortcodes_init() {

			// Cycle through all USL categories and shortcodes, requiring category files and adding each shortcode
			foreach ( self::$_shortcodes_extensions as $type => $categories) {
				foreach ( $categories as $category ) {
					require_once( self::$path . "core/shortcodes/$type/$category.php");
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
				defined( 'USL_DEVELOPMENT' ) ? time() : self::$version
			);

			wp_register_style(
				'usl-admin',
				self::$url . "/assets/css/ultimate-shortcodes-library-admin.min.css",
				null,
				defined( 'USL_DEVELOPMENT' ) ? time() : self::$version
			);

			wp_register_script(
				'usl',
				self::$url . "/assets/js/ultimate-shortcodes-library.min.js",
				array( 'jquery' ),
				defined( 'USL_DEVELOPMENT' ) ? time() : self::$version
			);

			wp_register_script(
				'usl-admin',
				self::$url . "/assets/js/ultimate-shortcodes-library-admin.min.js",
				array( 'jquery' ),
				defined( 'USL_DEVELOPMENT' ) ? time() : self::$version
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

			wp_localize_script( 'common', 'USL_Data', apply_filters( 'usl_localized_data', array()) );

			wp_enqueue_script( 'usl-admin' );
			wp_enqueue_style( 'usl-admin' );
		}
	}

	// Instantiate the class and then initialize the shortcodes
	$USL = USL::_getInstance();
	$USL::_shortcodes_init();
}