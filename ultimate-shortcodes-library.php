<?php
/*
Plugin Name: Ultimate Shortcodes Library
Description: This plugin is the only shortcode plugin you will ever need.
Version: 1.0
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

/*
This plugin works by defining two arrays in this main file.
Then it includes the admin page which displays all the available shortcodes.
Then it includes the shortcodes file which includes all the other files where
the actual shortcodes are created.

Within each shortcode file, if necessary a new category may be added to the
$usl_cats array. For each individual shortcode, an array is created with the 
shortcode's Title, Description, Atts, Code and Category. All of this is added
to the $usl_codes array.
*/

/* We will have one array that we can add to for categories */ 
$usl_cats = array();

/* and another array we can add to for codes */
$usl_codes = array();

require_once (plugin_dir_path(__FILE__).'/admin/admin.php');
require_once (plugin_dir_path(__FILE__).'/shortcodes/all.php');
//Register shortcodes stylesheet
add_action('init', 'usl_reg_style');
function usl_reg_style() {
wp_register_style('usl-shortcodes', plugins_url('css/shortcodes.css', __FILE__));
}
//Conditionally enqueue stylesheet
add_filter('query', 'usl_print_style');
function usl_print_style($posts) {
if (empty($posts)) return $posts;
global $usl_add_style;
if ($usl_add_style) {
	var_dump($posts);
	wp_enqueue_style('usl-shortcodes');
	return $posts; } else { return $posts; }
}
//Add stylesheet to admin page
add_action('admin_enqueue_scripts', 'usl_admin_styles');
function usl_admin_styles($page) {
	if ( 'settings_page_view-all-shortcodes' != $page ) {
		return;
	}
	wp_enqueue_style('usl-admin', plugins_url('css/style.css', __FILE__) );
	wp_enqueue_script('usl_admin_scripts', plugin_dir_url( __FILE__ ) . 'js/script.js' );
}
?>