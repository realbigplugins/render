<?php
/**
 * Outputs basic debugging info for Render.
 *
 * @since {{VERSION}}
 */

global $wpdb;

// Browser info
require_once RENDER_PATH . '/includes/Browser.php';
$browser = new Browser();

// Theme info
$theme_data = wp_get_theme();
$theme      = $theme_data->Name . ' ' . $theme_data->Version;

// mySQL version
$mysql = mysqli_connect( 'localhost', DB_USER, DB_PASSWORD );
if ( mysqli_connect_errno() ) {
	$mysql_ver = sprintf( "Connection failed: %s\n", mysqli_connect_error() );
} else {
	$mysql_ver = sprintf( "MySQL Version: %s\n", mysqli_get_server_info( $mysql ) );
}
mysqli_close( $mysql );


do_action( 'render_debug_log_before' ); ?>

Multisite:                 <?php echo is_multisite() ? 'Yup' . "\n" : 'Negatory' . "\n" ?>

SITE_URL:                  <?php echo site_url() . "\n"; ?>
HOME_URL:                  <?php echo home_url() . "\n"; ?>

Render Version:            <?php echo RENDER_VERSION . "\n"; ?>
WordPress Version:         <?php echo get_bloginfo( 'version' ) . "\n"; ?>
Permalink Structure:       <?php echo get_option( 'permalink_structure' ) . "\n"; ?>
Active Theme:              <?php echo $theme . "\n"; ?>

<?php echo $browser; ?>

PHP Version:               <?php echo PHP_VERSION . "\n"; ?>
MySQL Version:             <?php echo $mysql_ver . "\n"; ?>
Web Server Info:           <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

WordPress Memory Limit:    <?php echo ( WP_MEMORY_LIMIT ); ?><?php echo "\n"; ?>
PHP Safe Mode:             <?php echo ini_get( 'safe_mode' ) ? "Yup" : "Negatory\n"; ?>
PHP Memory Limit:          <?php echo ini_get( 'memory_limit' ) . "\n"; ?>
PHP Upload Max Size:       <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Post Max Size:         <?php echo ini_get( 'post_max_size' ) . "\n"; ?>
PHP Upload Max Filesize:   <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Time Limit:            <?php echo ini_get( 'max_execution_time' ) . "\n"; ?>
PHP Max Input Vars:        <?php echo ini_get( 'max_input_vars' ) . "\n"; ?>
PHP Arg Separator:         <?php echo ini_get( 'arg_separator.output' ) . "\n"; ?>
PHP Allow URL File Open:   <?php echo ini_get( 'allow_url_fopen' ) ? "Yup" : "Negatory\n"; ?>

WP_DEBUG:                  <?php echo defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Crap aint set' . "\n" ?>

WP Table Prefix:           <?php echo "Length: " . strlen( $wpdb->prefix );
echo " Status:";
if ( strlen( $wpdb->prefix ) > 16 ) {
	echo " ERROR: Too Long";
} else {
	echo " Acceptable";
}
echo "\n"; ?>

Show On Front:             <?php echo get_option( 'show_on_front' ) . "\n" ?>
Page On Front:             <?php $id = get_option( 'page_on_front' );
echo get_the_title( $id ) . '(#' . $id . ')' . "\n" ?>
Page For Posts:            <?php $id = get_option( 'page_for_posts' );
echo get_the_title( $id ) . '(#' . $id . ')' . "\n" ?>

Session:                   <?php echo isset( $_SESSION ) ? 'Enabled' : 'Disabled'; ?><?php echo "\n"; ?>
Session Name:              <?php echo esc_html( ini_get( 'session.name' ) ); ?><?php echo "\n"; ?>
Cookie Path:               <?php echo esc_html( ini_get( 'session.cookie_path' ) ); ?><?php echo "\n"; ?>
Save Path:                 <?php echo esc_html( ini_get( 'session.save_path' ) ); ?><?php echo "\n"; ?>
Use Cookies:               <?php echo ini_get( 'session.use_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>
Use Only Cookies:          <?php echo ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>

DISPLAY ERRORS:            <?php echo ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A'; ?><?php echo "\n"; ?>
FSOCKOPEN:                 <?php echo ( function_exists( 'fsockopen' ) ) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.'; ?><?php echo "\n"; ?>
cURL:                      <?php echo ( function_exists( 'curl_init' ) ) ? 'Your server supports cURL.' : 'Your server does not support cURL.'; ?><?php echo "\n"; ?>
SOAP Client:               <?php echo ( class_exists( 'SoapClient' ) ) ? 'Your server has the SOAP Client enabled.' : 'Your server does not have the SOAP Client enabled.'; ?><?php echo "\n"; ?>
SUHOSIN:                   <?php echo ( extension_loaded( 'suhosin' ) ) ? 'Your server has SUHOSIN installed.' : 'Your server does not have SUHOSIN installed.'; ?><?php echo "\n"; ?>

ACTIVE PLUGINS:

<?php
$plugins        = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $plugins as $plugin_path => $plugin ) {
	// If the plugin isn't active, don't show it.
	if ( ! in_array( $plugin_path, $active_plugins ) ) {
		continue;
	}

	echo $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
}

if ( is_multisite() ) :
	?>

	NETWORK ACTIVE PLUGINS:

	<?php
	$plugins        = wp_get_active_network_plugins();
	$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

	foreach ( $plugins as $plugin_path ) {
		$plugin_base = plugin_basename( $plugin_path );

		// If the plugin isn't active, don't show it.
		if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
			continue;
		}

		$plugin = get_plugin_data( $plugin_path );

		echo $plugin['Name'] . ' :' . $plugin['Version'] . "\n";
	}

endif;

do_action( 'render_debug_log_after' );