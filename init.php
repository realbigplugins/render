<?php
/*
Plugin Name: Render Development
Description: The development build for Render.
Version: {{VERSION}}
Author: Joel Worsham & Kyle Maurer
Author URI: http://realbigmarketing.com
*/

define( 'RENDER_DEVELOPMENT', true );

define( 'SCRIPT_DEBUG', true );

include_once( 'tools/tag-debug.php' );
include_once( 'tools/filter-content.php' );
include_once( 'src/render.php' );