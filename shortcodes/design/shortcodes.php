<?php
/*-------------------------------
Header info
-------------------------------*/
$usl_cats[]='Design';

/*-------------------------------
Button
-------------------------------*/
function usl_button($atts, $content = null) {
	global $usl_add_style;
	$usl_add_style = true;
	extract(shortcode_atts(array(
    'link' => '',
    'size'   => 'small',
    'color'  => 'red'
    ), $atts));

    return "<a href='$link' class='usl-$color usl-$size'>".$content."</a>";
}
add_shortcode('usl_button', 'usl_button');

$usl_button = array(
		'Title'=>'Button',
		'Code'=>'usl_button',
		'Atts'=>'link, color(red, blue), size(large, medium, small)',
		'Description'=>'Creates a sweet button',
		'Example'=>'[usl_button link="#" size="large" color="blue"]Click here[/usl_button]',
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