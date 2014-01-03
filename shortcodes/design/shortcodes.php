<?php
//Here is where we can make some cool shortcodes. Yippee!

//[yippee] outputs the text in quotes
//This part just creates a function
function my_shortcode() {

	return "Three is the number to which thou shalt count.";
}
//This part first creates a shortcode, then names the function that gets run when we use this shortcode
add_shortcode('yippee', 'my_shortcode');

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