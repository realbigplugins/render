<?php
/*
Plugin Name: Ultimate Shortcodes Library
Description: This plugin is the only shortcode plugin you will ever need.
Version: 0.3
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

/**
 * Lessons learned from...
 * https://github.com/GavickPro/TinyMCE-4-own-buttons
 * http://stackoverflow.com/questions/24339864/add-a-php-function-to-a-javascript-file-with-ajax-tinymce-wordpress-related
 */

/*
This plugin works by defining the $usl_codes array in the main file
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
	wp_register_script( 'usl-tinymce', plugins_url( 'js/tinymce.js', __FILE__ ) );
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

/**
 * Tiny MCE button
 */
add_action( 'init', 'usl_tinymce_buttons' );
add_action( 'admin_print_styles', 'usl_tinymce_button_style' );
function usl_tinymce_buttons() {
	add_filter( "mce_external_plugins", "usl_add_tinymce_buttons" );
	add_filter( 'mce_buttons', 'usl_register_tinymce_buttons' );
}

function usl_add_tinymce_buttons( $plugin_array ) {
	$plugin_array['usl'] = plugins_url( '/js/tinymce.js', __FILE__ );

	return $plugin_array;
}

function usl_register_tinymce_buttons( $buttons ) {
	array_push( $buttons, 'usl' );

	return $buttons;
}

function usl_tinymce_button_style() {
	echo '<style>i.mce-i-usl:before { content: "\f475"; } i.mce-i-usl { font: 400 20px/1 dashicons; }</style>';
}

/**
 * Merge all other shortcodes into $usl_codes
 */
add_action( 'init', 'usl_codes', 99 );
function usl_codes() {
	global $shortcode_tags;
	global $usl_codes;
	if ( $shortcode_tags ) {
		foreach ( $shortcode_tags as $tag => $v ) {
			$check = strpos( $tag, 'usl_' );
			if ( $check === false ) {
				$title       = str_replace( '_', ' ', $tag );
				$usl_codes[] = array(
					'Code'        => $tag,
					'Title'       => $title,
					'Description' => '',
					'Atts'        => '',
					'Category'    => usl_core_shortcodes( $tag ),
					'Example'     => ''
				);
			} else {
			}
		}
	}
}

function usl_output_codes() {
	global $usl_codes;
	foreach ( $usl_codes as $code ) {
		$output[] = array(
			$code
		);
	}
	return $output;
}
add_action('admin_enqueue_scripts', 'usl_mce');
function usl_mce($hook) {
	if ( $hook == 'post.php' || $hook == 'post-new.php') {
		add_action("admin_head-$hook", 'usl_mce_head');
	}
}
function usl_mce_head() {
	echo '<script type="text/javascript">var usl_mce_options=' . json_encode(array('codes'=>usl_output_codes(0))).'; </script>';
	echo '<script type="text/javascript">function uslCodes() { console.log(usl_mce_options); for (var i = 0; i < usl_mce_options.codes.length; i++) {console.log(usl_mce_options.codes[i]);} }</script>';
}