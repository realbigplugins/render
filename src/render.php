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
define( 'RENDER_VERSION', '1.1.0' );

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

// Uninstall
if ( isset( $callable ) && $callable == 'render_uninstall' ) {
	define( 'RENDER_UNINSTALLING', true );
	require_once __DIR__ . '/core/uninstall.php';
}

if ( ! class_exists( 'Render' ) && ! defined( 'RENDER_UNINSTALLING' ) ) {

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
		 * Contains available Render integrations information.
		 *
		 * @since {{VERSION}}
		 *
		 * @var Render_Integrations
		 */
		public $integrations;

		/**
		 * Handles Render admin notices.
		 *
		 * @since {{VERSION}}
		 *
		 * @var Render_Notices
		 */
		public $notices;

		/**
		 * Constructs the class.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {

			// Setup notices
			require_once __DIR__ . '/core/notices.php';
			$this->notices = new Render_Notices();

			// Make sure we should be here
			if ( version_compare( '5.3', phpversion(), '>' ) ) {

				$this->notices->add(
					'render_version',
					sprintf(
						__( 'Render is not active because your server is not running at least PHP version 5.3. Please update or contact your server administrator. (PS: PHP 5.3 is %s year\'s old!)', 'Render' ),
						intval( date( 'y' ) ) - 9
					)
				);

				return;
			}

			define( 'RENDER_ACTIVE', true );

			add_action( 'init', array( $this, 'pre_init' ), 0.1 );
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
		public function pre_init() {

			// Hook database interactions for options
			add_action( 'updated_option', array( __CLASS__, 'updated_option' ), 10, 1 );
			add_action( 'deleted_option', array( __CLASS__, 'deleted_option' ), 10, 1 );

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
			render_setup_license(
				'render',
				'Render',
				RENDER_VERSION,
				defined( 'RENDER_DEVELOPMENT' ) ? 'render-development/init.php' : __FILE__
			);

			// Translations
			load_plugin_textdomain( 'Render', false, RENDER_PATH . 'languages' );

			// Filter content
			add_filter( 'the_content', 'render_strip_paragraphs_around_shortcodes' );
			add_filter( 'the_content', 'render_sc_attr_unescape', 15 );

			// Initialize Render shortcodes
			self::_shortcodes_init();

			// Tracking
			self::_tracking_init();

			// Uninstall hook. Only apply if the setting is set
			if ( get_option( 'render_delete_on_uninstall' ) ) {
				register_uninstall_hook( plugin_basename( __FILE__ ), 'render_uninstall' );
			} else {

				// Make sure to remove the hook (no way to do so via WP functions)
				// @see is_uninstallable_plugin()
				$uninstallable_plugins = (array) get_option( 'uninstall_plugins', array() );
				unset( $uninstallable_plugins[ plugin_basename( __FILE__ ) ] );
				update_option( 'uninstall_plugins', $uninstallable_plugins );
			}
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
				'description' => __( 'Unrecognized shortcode.', 'Render' ),
				'source'      => __( 'Source Unknown', 'Render' ),
				'tags'        => '',
				'category'    => 'other',
				'atts'        => array(),
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

			// Licensing
			require_once __DIR__ . '/core/licensing/licensing.php';

			// Pointers
			add_filter( 'current_screen', array( $this, '_pointers' ), 1 );


			// Notices
			add_action( 'admin_init', array( $this, '_initial_notices' ) );
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

			// Tracking
			require_once __DIR__ . '/core/tracking.php';
		}

		/**
		 * Fires on updating an option.
		 *
		 * When the option being updated is a Render option, that option is stored in a different option, to keep track
		 * of it for plugin uninstall.
		 *
		 * @since {{VERSION}}
		 *
		 * @param string $option The option name that was just updated.
		 */
		public static function updated_option( $option ) {

			// Only apply to render options (and not the render_updated_options option, that would cause an infinite loop)
			if ( strpos( $option, 'render_' ) !== false && $option != 'render_updated_options' ) {

				// Get the options list
				$update_options = get_option( 'render_updated_options', array() );

				// Add the new option in
				$update_options[] = $option;
				$update_options   = array_unique( $update_options );

				// Save it
				update_option( 'render_updated_options', $update_options );
			}
		}

		/**
		 * Fires on deleting an option.
		 *
		 * When the option being deleted is a Render option, that option is deleted from a different option, to keep
		 * track of it for plugin uninstall.
		 *
		 * @since {{VERSION}}
		 *
		 * @param string $option The option name that was just updated.
		 */
		public static function deleted_option( $option ) {

			// Only apply to render options (and not the render_updated_options option, that would cause an infinite loop)
			if ( strpos( $option, 'render_' ) !== false && $option != 'render_updated_options' ) {

				// Get the options list
				$update_options = get_option( 'render_updated_options', array() );

				// Remove the option
				unset( $update_options[ $option ] );

				// Save it
				update_option( 'render_updated_options', $update_options );
			}
		}

		/**
		 * Admin-only related tasks.
		 *
		 * Includes menu pages and files.
		 *
		 * @since 1.0.0
		 */
		private static function _admin() {

			add_action( 'admin_menu', function () {
				add_menu_page(
					'Render',
					'Render',
					'manage_options',
					'render-settings',
					null,
					'dashicons-admin-generic',
					82.9
				);
			} );

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

			/*--------*
			 * Styles *
			 *--------*/

			// Frontend
			wp_register_style(
				'render',
				RENDER_URL . "/assets/css/render.min.css",
				null,
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			// Admin
			wp_register_style(
				'render-admin',
				RENDER_URL . "/assets/css/render-admin.min.css",
				null,
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			// TinyMCE
			wp_register_style(
				'render-tinymce',
				RENDER_URL . "/assets/css/render-tinymce.min.css",
				null,
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			// Chosen
			wp_register_style(
				'render-chosen',
				RENDER_URL . '/includes/chosen/chosen.min.css',
				null,
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			/*---------*
			 * Scripts *
			 *---------*/

			// Frontend
			wp_register_script(
				'render',
				RENDER_URL . "/assets/js/render.min.js",
				array( 'jquery' ),
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			// Admin
			wp_register_script(
				'render-admin',
				RENDER_URL . "/assets/js/render-admin.min.js",
				array( 'jquery' ),
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			// Chosen
			wp_register_script(
				'render-chosen',
				RENDER_URL . '/includes/chosen/chosen.jquery.min.js',
				array( 'jquery' ),
				defined( 'Render_DEVELOPMENT' ) ? time() : RENDER_VERSION
			);

			// Masked input
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

			wp_localize_script( 'render-admin', 'Render_Data', apply_filters( 'render_localized_data', array(
				'primary_color' => RENDER_PRIMARY_COLOR,
			) ) );

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

					// If shortcode is in category "Other", add the default sc_attribute and add a description
					if ( ! isset( $args['category'] ) || $args['category'] == 'other' ) {
						$args = $this->parse_unrecognized_shortcode( $args, $code );
					}

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
		 *
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
		 *
		 * @return array The parsed att.
		 */
		public static function parse_shortcode_att( $att ) {

			// Establish default attribute properties (if any exist)
			$att = wp_parse_args( $att, self::$att_defaults );

			// Apply defaults to repeater fields as well
			if ( isset( $att['properties']['fields'] ) ) {
				array_walk( $att['properties']['fields'], function ( &$properties ) {
					$properties = Render::parse_shortcode_att( $properties );
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
		 * Gives the shortcode extra properties.
		 *
		 * @since {{VERSION}}
		 *
		 * @param $args array The old args.
		 * @param $code string The code of the current shortcode.
		 *
		 * @return array The new args.
		 */
		function parse_unrecognized_shortcode( $args, $code ) {

			// Get integrations
			if ( ! ( $this->integrations instanceof Render_Integrations ) ) {
				include_once __DIR__ . '/core/integrations.php';
				$this->integrations = new Render_Integrations();
			}

			$integrated_shortcodes = $this->integrations->all_shortcodes;

			// The default explanation for the user
			$shortcode_explanation = __( 'Render does not recognize this shortcode. You may still use it, but you will not get a preview and you must manually enter attributes.', 'Render' );

			// Check to see if Render has an integration available, and if it does, modify the description
			if ( isset( $integrated_shortcodes[ $code ] ) ) {

				$integrated_shortcode = $integrated_shortcodes[ $code ];
				$integrated_plugin = $this->integrations->available_integrations[ $integrated_shortcode['plugin'] ];

				if ( isset( $integrated_shortcode[ 'noDisplay'] ) ) {
					$args['noDisplay'] = true;
					return $args;
				}

				/*
				 * Translators:
				 * %s:1 = Dependent plugin title
				 * %s:2 = Opening of anchor
				 * %s:3 = Closing of anchor
				 */
				$shortcode_explanation = sprintf(
					__( 'This shortcode belongs to %s and is not currently integrated. Good news though, Render has an integration available %shere%s!', 'Render' ),
					"<strong>$integrated_plugin[title]</strong>",
					"<a href=\"$integrated_plugin[link]\" target=\"_blank\" class=\"render-stop-propagation\">",
					'</a>'
				);
			}

			// Lets the user know what's going on
			$args['atts']['shortcode_explanation'] = array(
				'type'        => 'section_break',
				'description' => $shortcode_explanation,
			);

			// Allows manual adding of shortcodes
			$args['atts']['sc_attributes'] = array(
				'label'       => __( 'Attributes', 'Render' ),
				'description' => __( 'Enter the shortcode\'s attributes here.', 'Render' ),
				'type'        => 'repeater',
				'properties'  => array(
					'fields' => array(
						'attribute_name'  => array(
							'label'    => __( 'Attribute Name', 'Render' ),
							'validate' => array(
								'DOES NOT CONTAIN' => '[]"\'',
							),
						),
						'attribute_value' => array(
							'label'    => __( 'Attribute Value', 'Render' ),
							'validate' => array(
								'DOES NOT CONTAIN' => '[]"\'',
							),
						),
					),
				),
			);

			return $args;
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
				add_filter( 'render_pointers', array( __CLASS__, 'add_main_pointer' ) );
			}

			// Include pointers if necessary
			if ( apply_filters( 'render_pointers', false ) ) {

				// Include pointers
				include_once __DIR__ . '/core/pointers.php';
				new Render_Pointers();
			}
		}

		/**
		 * Adds the main Render pointer.
		 *
		 * @since {{VERSION}}
		 *
		 * @param array $pointers The pointers to use.
		 *
		 * @return array The new pointers.
		 */
		public static function add_main_pointer( $pointers ) {

			$content = __( 'Thanks for installing Render! You can access Render settings as well as view all available shortcodes here.', 'Render' );
			$content .= '</p><p><span id="render-tracking-message">';
			$content .= __( 'Render would like to gather anonymous tracking data about your site to improve your Render experience!', 'Render' );
			$content .= '<br/><br/><span style="text-align: center; width: 100%; display: inline-block;">';
			$content .= '<input type="button" class="button render-button" name="render-allow-tracking" value="Allow Tracking" />';
			$content .= '&nbsp;';
			$content .= '<input type="button" class="button" value="Do Not Allow" />';
			$content .= '</span></span>';

			$pointers['admin_menu'] = array(
				'title'    => __( 'Welcome To Render!', 'Render' ),
				'content'  => $content,
				'target'   => '#toplevel_page_render-settings',
				'position' => array(
					'edge'  => 'bottom',
					'align' => 'bottom',
				),
				'classes'  => 'admin-menu-pointer',
			);

			return $pointers;
		}

		/**
		 * Shows initial notices.
		 *
		 * @since  {{VERSION}}
		 * @access private
		 */
		function _initial_notices() {

			$notices = array();

			// Add notices for integration plugins
			if ( $this->integrations instanceof Render_Integrations && ! empty( $this->integrations->available_integrations ) ) {

				foreach ( (array) $this->integrations->available_integrations as $plugin => $integration ) {

					if ( is_plugin_active( $integration['name'] ) &&
					     ! is_plugin_active( $integration['render_name'] )
					) {
						$notices[] = array(
							'ID'          => "render-integration-notice-$plugin",
							'message'     => sprintf(
								__( 'Render has an integration available for %s. You can get it %shere%s.', 'Render' ),
								"<strong>$integration[title]</strong>",
								"<a href=\"$integration[link]\" target=\"_blank\">",
								'</a>'
							),
							'type'        => 'update-nag',
							'hide_button' => true,
						);
					}
				}
			}

			if ( ! empty ( $notices ) ) {
				foreach ( (array) $notices as $notice ) {

					$notice = wp_parse_args( $notice, array(
						'message'     => '',
						'type'        => 'error',
						'ID'          => false,
						'hide_button' => false,
					) );

					$this->notices->add( $notice['ID'], $notice['message'], $notice['type'], $notice['hide_button'] );
				}
			}
		}

		/**
		 * Initializes tracking.
		 *
		 * @since {{VERSION}}
		 */
		private static function _tracking_init() {

			// Only do it if it's been approved
			if ( get_option( 'render_allow_tracking' ) === '1' ) {
				Render_Tracking::get_instance();
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

	// Don't initialize the class on uninstall
	$Render = Render::_getInstance();
}