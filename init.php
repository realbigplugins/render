<?php
/*
Plugin Name: Render DEVELOPMENT
Description: The development build for Render.
Version: 1.0.0-beta-4
Author: Joel Worsham & Kyle Maurer
Author URI: http://realbigmarketing.com
*/

define( 'RENDER_DEVELOPMENT', true );

if ( isset( $_GET['DEBUG_WPSCRIPTS'] ) ) {
	define( 'SCRIPT_DEBUG', true );
}

include_once( 'tools/tag-debug.php' );
include_once( 'tools/filter-content.php' );
include_once( 'src/render.php' );