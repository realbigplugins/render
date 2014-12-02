<?php
/*
Plugin Name: Ultimate Shortcodes Library DEVELOPMENT
Description: The development build for Ultimate Shortcodes Library.
Version: 1.0.0-beta-4
Author: Joel Worsham & Kyle Maurer
Author URI: http://realbigmarketing.com
*/

define( 'USL_DEVELOPMENT', true );

if ( isset( $_GET['DEBUG_WPSCRIPTS'] ) ) {
	define( 'SCRIPT_DEBUG', true );
}

include_once( 'tools/tag-debug.php' );
include_once( 'tools/filter-content.php' );
include_once( 'src/ultimate-shortcodes-library.php' );