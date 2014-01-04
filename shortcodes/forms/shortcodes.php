<?php
/*-------------------------------
Header info
-------------------------------*/
//Add this category (need to come back later and make it conditional)
$usl_cats[]='Forms';
/*-------------------------------
New form shortcode
-------------------------------*/
//[yippee] outputs the text in quotes
//This part just creates a function
function usl_form() {

	return "Three is the number to which thou shalt count.";
}
//This part first creates a shortcode, then names the function that gets run when we use this shortcode
add_shortcode('usl_form', 'usl_form');

$usl_form = array(
		'Title'=>'Form',
		'Code'=>'usl_form',
		'Atts'=>'target, action',
		'Description'=>'Creates a form',
		'Example'=>'[usl_form] - Creates a form',
		'Category'=>'Forms'
		);
$usl_codes[]=$usl_form;
?>