<?php
/*
Plugin Name: Ultimate Shortcodes Library
Description: This plugin is the only shortcode plugin you will ever need.
Version: 1.0
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

/*
Tutorials and recources:
When I use snippets from other developers I generally leave a link here back to where I got the code, to give
them credit for being awesome and also as a reference for myself later. Not required but recommended.
*/

/* We will have one array that we can add to for categories */ 
$usl_cats = array('Social', 'Technical');
$usl_cats[]='WordPress';

/* and another array we can add to for codes */
$usl_codes = array();

/*This is what I'd have in every shortcode file
for adding the shortcodes created there
to the master array*/
$kjm_add = array(
		'Holiday'=>'Independence Day',
		'Month'=>'July',
		'Day'=>4,
		'Season'=>'Summer'
		);

$usl_codes[]=$kjm_add;

//Generally I begin with a main plugin file (which would be this) and use it to assemble all the others together.
//It is good practice to segment your code into different files. This is a good technique for including them:
require_once (plugin_dir_path(__FILE__).'/admin/admin.php');
require_once (plugin_dir_path(__FILE__).'/shortcodes/all.php');


?>