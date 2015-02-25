<?php
//{{HEADER}}

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Define all plugin constants.

/**
 * The version of Render.
 *
 * @since 1.0.0
 */
define( 'RENDER_VERSION', '1.0.5' );

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
 * The light version of the primary color of Render.
 *
 * Primarily used for hovering.
 *
 * @since 1.0.0
 */
define( 'RENDER_PRIMARY_COLOR_LIGHT', '#74b6c2' );

/**
 * The primary font color used throughout Render.
 *
 * @since 1.0.0
 */
define( 'RENDER_PRIMARY_FONT_COLOR', '#ffffff' );

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
		 * The default settings for shortcodes (set later).
		 *
		 * @since {{VERSION}}
		 *
		 * @var array
		 */
		public static $shortcode_defaults = array();

		/**
		 * The default settings for shortcode attributes (set later).
		 *
		 * @since {{VERSION}}
		 *
		 * @var array
		 */
		public static $att_defaults = array();

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
		 * Escapes for shortcode attributes
		 *
		 * @since {{VERSION}}
		 *
		 * @var array
		 */
		public static $sc_attr_escapes = array(
			'\'',
			'[',
			']'
		);

		/**
		 * Constructs the class.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {

			// Make sure we should be here
			if ( version_compare( '5.3', phpversion(), '>' ) ) {
				$this->notice();

				return;
			}

			define( 'RENDER_ACTIVE', true );

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

			// Licensing
			render_setup_license( 'render', 'Render', RENDER_VERSION, __FILE__ );

			// Translations
			load_plugin_textdomain( 'Render', false, RENDER_PATH . 'languages' );

			// Filter content
			add_filter( 'the_content', 'render_strip_paragraphs_around_shortcodes' );
			add_filter( 'the_content', 'render_sc_attr_unescape', 15 );

			// Initialize Render shortcodes
			self::_shortcodes_init();
		}

		/**
		 * Initializes and loads the plugin late stages.
		 *
		 * @since 1.0.0
		 */
		public function post_init() {

			/**
			 * Defaults for the shortcode.
			 *
			 * Allows external plugins to modify the defaults for a Render shortcode setup.
			 *
			 * @since 1.0.0
			 */
			self::$shortcode_defaults = apply_filters( 'render_shortcode_defaults', array(
				'function'    => '',
				'title'       => '',
				'description' => '',
				'source'      => __( 'Unknown', 'Render' ),
				'tags'        => '',
				'category'    => 'other',
				'atts'        => array(),
				'example'     => '',
				'wrapping'    => false,
				'render'      => false,
				//
				// Some more obscure ones
				'noDisplay'   => false,
			) );

			/**
			 * Defaults for a shortcode attribute.
			 *
			 * Allows external plugins to modify defaults for a Render shortcode attribute.
			 *
			 * @since 1.0.0
			 */
			self::$att_defaults = apply_filters( 'render_shortcode_att_defaults', array(
				'label'            => false,
				'description'      => false,
				'required'         => false,
				'type'             => 'textbox',
				'properties'       => array(),
				'validate'         => false,
				'sanitize'         => false,
				'conditional'      => false,
				'default'          => false,
				'advanced'         => false,
				'callback'         => false,
				//
				// Some more obscure ones
				'descriptionAbove' => false,
				'descriptionBelow' => true,
				'initCallback'     => false,
				'noInit'           => false,
			) );

			// Add shortcodes
			$this->add_shortcodes();

			// Remove disabled shortcodes
			if ( ! is_admin() ) {
				$this->remove_disabled_shortcodes();
			}

			// Add editor styles
			self::add_editor_styles();

			// Licensing
			require_once __DIR__ . '/core/licensing/licensing.php';

			// Pointers
			add_filter( 'current_screen', array( $this, '_pointers' ), 1 );
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

			// Modal
			require_once __DIR__ . '/core/modal.php';
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

			wp_register_script(
				'render-masked-input',
				RENDER_URL . '/includes/input-mask/jquery.masked-input.js',
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
		public function add_shortcodes() {

			global $shortcode_tags;

			// Add in all existing shortcode tags first (to account for "unregistered with Render" shortcodes)
			$shortcodes = array();
			if ( ! empty( $shortcode_tags ) ) {
				foreach ( $shortcode_tags as $code => $callback ) {

					$shortcode             = array();
					$shortcode['title']    = render_translate_id_to_name( $code );
					$shortcode['function'] = $callback;

					// Add shortcode to Render
					$shortcodes[ $code ] = $shortcode;
				}
			}

			$shortcodes = apply_filters( 'render_add_shortcodes', $shortcodes );

			if ( $shortcodes !== false ) {

				foreach ( $shortcodes as $code => $args ) {

					// Setup shortcode defaults
					$args = $this->parse_shortcode( $args );

					// Create the actual shortcode if it hasn't yet been created
					if ( ! array_key_exists( $code, $shortcode_tags ) ) {
						add_shortcode( $code, $args['function'] );
					}

					// Add the shortcode info to our list if it hasn't yet been added
					if ( empty( $this->shortcodes ) || ! array_key_exists( $code, $this->shortcodes ) ) {
						$this->shortcodes[ $code ] = $args;
					}
				}
			}
		}

		/**
		 * Parses the shortcode and sets up all defaults (including attribute defaults).
		 *
		 * @since {{VERSION}}
		 *
		 * @param array $shortcode The shortcode to parse.
		 * @return array The parsed shortcode.
		 */
		public static function parse_shortcode( $shortcode ) {

			// Setup shortcode defaults
			$shortcode = wp_parse_args( $shortcode, self::$shortcode_defaults );

			// If there are attributes, set up their defaults
			if ( ! empty( $shortcode['atts'] ) ) {
				$shortcode['atts'] = array_map( array( __CLASS__, 'parse_shortcode_att' ), $shortcode['atts'] );
			}

			// Add the wrapping property to the render data
			if ( $shortcode['render'] ) {
				if ( ! is_array( $shortcode['render'] ) ) {
					$shortcode['render'] = array();
				}
				$shortcode['render']['wrapping'] = $shortcode['wrapping'];
			}

			return $shortcode;
		}

		/**
		 * Sets up the shortcode attribute's defaults.
		 *
		 * @since {{VERSION}}
		 *
		 * @param array $att The att to parse.
		 * @return array The parsed att.
		 */
		public static function parse_shortcode_att( $att ) {

			// Establish default attribute properties (if any exist)
			$att = wp_parse_args( $att, self::$att_defaults );

			// Apply defaults to repeater fields as well
			if ( isset( $att['properties']['fields'] ) ) {
				array_walk( $att['properties']['fields'], function ( &$properties ) {
					$properties = self::parse_shortcode_att( $properties );
				} );
			}

			// Setup conditionals
			if ( $att['conditional'] !== false ) {

				// Flip array key / value and set the value to an empty array for populate conditionals.
				// This makes it easier when creating the shortcode array because you can input a single
				// dimensional, non-associative array.
				if ( isset( $att['conditional']['populate'] ) ) {
					$att['conditional']['populate']['atts'] = array_flip( $att['conditional']['populate']['atts'] );
					array_walk( $att['conditional']['populate']['atts'], function ( &$value ) {
						$value = array();
					} );
				}
			}

			return $att;
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

			foreach ( (array) $styles as $style ) {
				add_editor_style( $style );
			}
		}

		/**
		 * Includes pointer necessities as needed and includes the primary Render pointer.
		 *
		 * @since  {{VERSION}}
		 * @access private
		 *
		 * @param WP_Screen|null $screen The current screen object.
		 */
		function _pointers( $screen ) {

			// Add the primary pointer, just not to the customize page
			if ( $screen->id != 'customize' ) {
				add_filter( 'render_pointers', function ( $pointers ) {

					$pointers['admin_menu'] = array(
						'title'    => __( 'Welcome!', 'Render' ),
						'content'  => __( 'Thanks for installing Render! You can access Render settings as well as view all available shortcodes here.', 'Render' ),
						'target'   => '#toplevel_page_render-settings',
						'position' => array(
							'edge'  => 'bottom',
							'align' => 'bottom',
						),
						'classes'  => 'admin-menu-pointer',
					);

					return $pointers;
				} );
			}

			// Include pointers if necessary
			if ( apply_filters( 'render_pointers', false ) ) {

				// Include pointers
				include_once __DIR__ . '/core/pointers.php';
				new Render_Pointers();
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

		/**
		 * Warns user about PHP version.
		 *
		 * @since 1.0.3
		 */
		private function notice() {
			?>
			<div class="error">
				<p>
					<?php
					printf(
						__( 'Render is not active because your server is not running at least PHP version 5.3. Please update or contact your server administrator. (PS: PHP 5.3 is %s year\'s old!)', 'Render' ),
						intval( date( 'y' ) ) - 9
					);
					?>
				</p>
			</div>
		<?php
		}
	}

	// Instantiate the class and then initialize the shortcodes
	$Render = Render::_getInstance();
}