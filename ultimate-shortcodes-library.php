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

/* ~JWP
 * The first thing we will need to do when we create a plugin is regiser the activation hook to create any
 * components or resources we may need to consume during the life of the program. For example if there is a 
 * database component to our plugin we will need to creata the tables. I will go ahead and do that and bind 
 * the acivation hook below. Also if the plugin will use any Wordpress Options you should initialize them 
 * here, which we will also do below.
 */
function register_starter_plugin()
{
	//create database table for our plugin
	global $wpdb;
	$table_name = $wpdb->prefix . "starter_plugin_table"; //get proper prefix and create table with our desired name
	$createTableSQL = "create table $table_name (id int NOT NULL, starter_strings varchar(100));"; //generate SQL insert statement
	$wpdb->query($createTableSQL);//execute query
	
	update_option("starter_plugin_version","1.0"); //create option to house the version of the plugin
}
 
//bind function to the plugin activation event
register_activation_hook(__FILE__,'register_starter_plugin'); 
//~JWP


//Generally I begin with a main plugin file (which would be this) and use it to assemble all the others together.
//It is good practice to segment your code into different files. This is a good technique for including them:
require_once (plugin_dir_path(__FILE__).'/admin/admin.php');
require_once (plugin_dir_path(__FILE__).'/shortcodes.php');


	//Use this command if you want your CSS on the backend
    //add_action('admin_enqueue_scripts', 'my_styles');

    //Use this command if you want your CSS on the frontend
    //add_action('wp_enqueue_scripts', 'my_styles');
	function my_styles() {
		
		//This is for setting a condition for when you want to include your style (if you don't want it everywhere)
		global $post_type; //variable for getting current post type if needed
        if ($post_type == 'my-post-type' || is_singular('my-post-type')) :

        	//Now we actually register the stylesheet
        wp_enqueue_style("starter-plugin", plugins_url("/css/style.css", __FILE__), FALSE); 
		endif;
}


//Now we'll include some javascript
	//first define the action (how), the hook (when) and finally the function (what)
    //add_action('wp_enqueue_scripts', 'my_cool_script');

	function my_cool_script() {
        wp_enqueue_script("coolscript", plugins_url("/js/script.js", __FILE__), FALSE);
}

//~JWP
//Here we are adding a filter function.
function addFootText($content) //$content is the content of the post being rendered
{
	$content .= "</br>";
	$content .= "This is some text";
	return $content; //return the content we wnat to be displayed. This content could be completely new content or just modified
	
	//For example, if you comment the return above and un-comment the return below the contents of the post 
	//will be compltely replaced. With three lines you can take a subset or all content offline in wordpress
	//return "We are experiencing technical difficulties and cannot display any content";
}

add_filter('the_content','addFootText');//connect the filter function we created to the_content filter hook.
//~JWP
?>