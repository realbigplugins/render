<?php
/*-------------------------------
Header info
-------------------------------*/
$usl_cats[]='Design';

/*-------------------------------
Button
-------------------------------*/
function usl_button() {
	global $usl_add_style;
	$usl_add_style = true;
}
add_shortcode('usl_button', 'usl_button');

$usl_button = array(
		'Title'=>'Button',
		'Code'=>'usl_button',
		'Atts'=>'link, shape, size',
		'Description'=>'Creates a sweet button',
		'Example'=>'[button link="#" shape="round"]',
		'Category'=>'Design'
		);
$usl_codes[]=$usl_button;
/*-------------------------------
Create a content box
-------------------------------*/
function usl_box() {
	return "A content box";
}
add_shortcode('usl_box', 'usl_box');

$usl_box = array(
		'Title'=>'Box',
		'Code'=>'usl_box',
		'Description'=>'Creates a sweet box',
		'Example'=>'[box link="#" shape="round"]',
		'Category'=>'Design'
		);
$usl_codes[]=$usl_box;

?>