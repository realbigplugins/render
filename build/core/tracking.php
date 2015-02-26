<?php

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Render_Tracking
 *
 * Allows Render to track site information and send it back for logging. If user does not opt-in, this is not used at
 * all.
 *
 * @since      1.1-beta-1
 *
 * @package    Render
 * @subpackage Tracking
 */
class Render_Tracking {

	/**
	 * Instance of this class.
	 *
	 * @since 1.1-beta-1
	 *
	 * @var object
	 */
	public static $instance;

	private function __construct() {

		// Only track once a week
		if ( get_transient( 'render_last_tracked' ) ) {
			return;
		}
		set_transient( 'render_last_tracked', 1, WEEK_IN_SECONDS );

		$this->track();
	}

	/**
	 * Get the singleton instance of this class.
	 *
	 * This allows the class to only be instantiated once.
	 *
	 * @since 1.1-beta-1
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Gathers tracking data and sends it off.
	 *
	 * @since 1.1-beta-1
	 */
	private function track() {

		global $wpdb;

		$hash = md5( site_url() );

		$pts        = array();
		$post_types = get_post_types( array( 'public' => true ) );
		if ( is_array( $post_types ) && $post_types !== array() ) {
			foreach ( $post_types as $post_type ) {
				$count             = wp_count_posts( $post_type );
				$pts[ $post_type ] = $count->publish;
			}
		}

		$comments_count = wp_count_comments();

		$theme_data     = wp_get_theme();
		$theme          = array(
			'name'       => $theme_data->display( 'Name', false, false ),
			'theme_uri'  => $theme_data->display( 'ThemeURI', false, false ),
			'version'    => $theme_data->display( 'Version', false, false ),
			'author'     => $theme_data->display( 'Author', false, false ),
			'author_uri' => $theme_data->display( 'AuthorURI', false, false ),
		);
		$theme_template = $theme_data->get_template();
		if ( $theme_template !== '' && $theme_data->parent() ) {
			$theme['template'] = array(
				'version'    => $theme_data->parent()->display( 'Version', false, false ),
				'name'       => $theme_data->parent()->display( 'Name', false, false ),
				'theme_uri'  => $theme_data->parent()->display( 'ThemeURI', false, false ),
				'author'     => $theme_data->parent()->display( 'Author', false, false ),
				'author_uri' => $theme_data->parent()->display( 'AuthorURI', false, false ),
			);
		} else {
			$theme['template'] = '';
		}

		$plugins       = array();
		$active_plugin = get_option( 'active_plugins' );
		foreach ( $active_plugin as $plugin_path ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );

			$slug             = str_replace( '/' . basename( $plugin_path ), '', $plugin_path );
			$plugins[ $slug ] = array(
				'version'    => $plugin_info['Version'],
				'name'       => $plugin_info['Name'],
				'plugin_uri' => $plugin_info['PluginURI'],
				'author'     => $plugin_info['AuthorName'],
				'author_uri' => $plugin_info['AuthorURI'],
			);
		}

		$users = get_users();

		if ( function_exists( 'curl_version' ) ) {
			$curl = curl_version();
		}
		else {
			$curl = null;
		}

		$data = array(
			'site'     => array(
				'hash'      => $hash,
				'version'   => get_bloginfo( 'version' ),
				'multisite' => is_multisite(),
				'users'     => count( $users ),
				'lang'      => get_locale(),
			),
			'pts'      => $pts,
			'comments' => array(
				'total'    => $comments_count->total_comments,
				'approved' => $comments_count->approved,
				'spam'     => $comments_count->spam,
				'pings'    => $wpdb->get_var( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_type = 'pingback'" ),
			),
			'options'  => array(
				'site_db_charset'             => DB_CHARSET,
				'webserver_apache_version'    => function_exists( 'apache_get_version' ) ? apache_get_version() : 0,
				'webserver_server_software'   => $_SERVER['SERVER_SOFTWARE'],
				'webserver_gateway_interface' => $_SERVER['GATEWAY_INTERFACE'],
				'webserver_server_protocol'   => $_SERVER['SERVER_PROTOCOL'],
				'php_version'                 => phpversion(),
				'php_max_execution_time'      => ini_get( 'max_execution_time' ),
				'php_memory_limit'            => ini_get( 'memory_limit' ),
				'php_open_basedir'            => ini_get( 'open_basedir' ),
				'php_bcmath_enabled'          => extension_loaded( 'bcmath' ) ? 1 : 0,
				'php_ctype_enabled'           => extension_loaded( 'ctype' ) ? 1 : 0,
				'php_curl_enabled'            => extension_loaded( 'curl' ) ? 1 : 0,
				'php_curl_version_a'          => phpversion( 'curl' ),
				'php_curl'                    => ( ! is_null( $curl ) ) ? $curl['version'] : 0,
				'php_dom_enabled'             => extension_loaded( 'dom' ) ? 1 : 0,
				'php_dom_version'             => phpversion( 'dom' ),
				'php_filter_enabled'          => extension_loaded( 'filter' ) ? 1 : 0,
				'php_mbstring_enabled'        => extension_loaded( 'mbstring' ) ? 1 : 0,
				'php_mbstring_version'        => phpversion( 'mbstring' ),
				'php_pcre_enabled'            => extension_loaded( 'pcre' ) ? 1 : 0,
				'php_pcre_version'            => phpversion( 'pcre' ),
				'php_pcre_with_utf8_a'        => @preg_match( '/^.{1}$/u', 'ñ', $UTF8_ar ),
				'php_pcre_with_utf8_b'        => defined( 'PREG_BAD_UTF8_ERROR' ),
				'php_spl_enabled'             => extension_loaded( 'spl' ) ? 1 : 0,
			),
			'theme'    => $theme,
			'plugins'  => $plugins,
		);

		$args = array(
			'body'      => $data,
			'blocking'  => false,
			'sslverify' => false,
		);

		wp_remote_post( 'https://tracking.realbigplugins.com/', $args );
	}
}

/**
 * Allows or disallows tracking.
 *
 * @since 1.1-beta-1
 */
add_action( 'wp_ajax_render_tracking_ajax', function () {

	$allow = isset( $_REQUEST['allow'] ) ? $_REQUEST['allow'] : null;

	// Something went wrong
	if ( $allow === null ) {
		wp_send_json( array( 'fail' => true ) );
	}

	// Update tracking
	if ( $allow === 'true' ) {
		update_option( 'render_allow_tracking', '1' );
	} else {
		delete_option( 'render_allow_tracking' );
	}

	wp_send_json( array( 'allow' => $allow ) );
} );


/**
 * Provides translations for TinyMCE pages.
 *
 * @since  1.1-beta-1
 * @access private
 *
 * @param array $data The current localization data.
 * @return array The new localization data.
 */
add_filter( 'render_localized_data', function ( $data ) {

	$data['l18n']['tracking_allow']   = sprintf(
		__( 'Thanks! You can always turn this off via %sRender Settings%s.', 'Render' ),
		'<a href="' . admin_url( 'admin.php?page=render-settings' ) . '">',
		'</a>'
	);

	$data['l18n']['tracking_noallow'] = sprintf(
		__( 'Okay. If you change your mind, you can turn this on via %sRender Settings%s.', 'Render' ),
		'<a href="' . admin_url( 'admin.php?page=render-settings' ) . '">',
		'</a>'
	);

	return $data;
} );