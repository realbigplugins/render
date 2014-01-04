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

$kjm_cats = array(
	array(
		'Holiday'=>'Christmas',
		'Month'=>'December',
		'Day'=>25,
		'Season'=>'Winter'
		),
	array(
		'Holiday'=>'Halloween',
		'Month'=>'October',
		'Day'=>31,
		'Season'=>'Fall'
		),
	array(
		'Holiday'=>'New Years Day',
		'Month'=>'January',
		'Day'=>1,
		'Season'=>'Winter'
		)
	);

/*This is what I'd have in every shortcode file
for adding the shortcodes created there
to the master array*/
$kjm_add = array(
		'Holiday'=>'Independence Day',
		'Month'=>'July',
		'Day'=>4,
		'Season'=>'Summer'
		);

$kjm_cats[]=$kjm_add;

//Generally I begin with a main plugin file (which would be this) and use it to assemble all the others together.
//It is good practice to segment your code into different files. This is a good technique for including them:
require_once (plugin_dir_path(__FILE__).'/admin/admin.php');
require_once (plugin_dir_path(__FILE__).'/shortcodes/all.php');


?>