<?php
/*-------------------------------
Header info
-------------------------------*/
//Add this category (need to come back later and make it conditional)
$usl_cats[]='Design';

/*-------------------------------
Yippee shortcode
-------------------------------*/
//[yippee] outputs the text in quotes
//This part just creates a function
function usl_button() {

	return "Three is the number to which thou shalt count.";
}
//This part first creates a shortcode, then names the function that gets run when we use this shortcode
add_shortcode('usl_button', 'usl_button');

$usl_button = array(
		'Title'=>'Button',
		'Code'=>'usl_button',
		'Atts'=>'link, shape, size',
		'Description'=>'Creates a sweet button',
		'Example'=>'[button link="#" shape="round"] - Creates a round button',
		'Category'=>'Design'
		);
$usl_codes[]=$usl_button;
/*-------------------------------
Yippee2 shortcode
-------------------------------*/
//[yippee] outputs the text in quotes
//This part just creates a function
function usl_box() {

	return "Three is the number to which thou shalt count.";
}
//This part first creates a shortcode, then names the function that gets run when we use this shortcode
add_shortcode('usl_box', 'usl_box');

$usl_box = array(
		'Title'=>'Box',
		'Code'=>'usl_box',
		'Description'=>'Creates a sweet box',
		'Example'=>'[box link="#" shape="round"] - Creates a box',
		'Category'=>'Design'
		);
$usl_codes[]=$usl_box;
/*-------------------------------
Yippee shortcode
-------------------------------*/
function DbExample_fxn()
{
	//declare the wpdb Global 
	global $wpdb;
	//getting the 
	$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
	return "Number of Users:" . $user_count;
	
}

add_shortcode('DbExample', 'DbExample_fxn');
?>