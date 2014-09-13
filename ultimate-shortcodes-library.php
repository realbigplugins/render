<?php
/*
Plugin Name: Ultimate Shortcodes Library
Description: This plugin is the only shortcode plugin you will ever need.
Version: 0.3
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

/*
This plugin works by defining two arrays in this main file.
Then it includes the admin page which displays all the available shortcodes.
Then it includes the shortcodes file which includes all the other files where
the actual shortcodes are created.
*/

/* Array we can add to for codes */
$usl_codes = array();
require_once( 'functions.php' );
require_once( plugin_dir_path( __FILE__ ) . '/admin/admin.php' );
require_once( plugin_dir_path( __FILE__ ) . '/shortcodes/all.php' );

//Register shortcodes stylesheet
add_action( 'init', 'usl_reg_style' );
function usl_reg_style() {
	wp_register_style( 'usl-shortcodes', plugins_url( 'css/shortcodes.css', __FILE__ ) );
}

//Conditionally enqueue stylesheet
add_filter( 'query', 'usl_print_style' );
add_filter( 'the_post', 'usl_print_style' );
function usl_print_style( $posts ) {
	if ( empty( $posts ) ) {
		return $posts;
	}
	global $usl_add_style;
	if ( $usl_add_style ) {
		wp_enqueue_style( 'usl-shortcodes' );

		return $posts;
	} else {
		return $posts;
	}
}

//Add stylesheet to admin page
add_action( 'admin_enqueue_scripts', 'usl_admin_styles' );
function usl_admin_styles( $page ) {
	if ( 'toplevel_page_view-all-shortcodes' == $page OR 'shortcodes_page_shortcodes-addons' == $page ) {
		wp_enqueue_style( 'usl-admin', plugins_url( 'css/style.css', __FILE__ ) );
		wp_enqueue_script( 'usl_admin_scripts', plugin_dir_url( __FILE__ ) . 'js/script.js' );
	} else {
		return;
	}
}