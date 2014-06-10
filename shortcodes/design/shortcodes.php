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
    'link' => '#',
    'size'   => 'small',
    'color'  => 'red',
    'shape' => 'rounded'
    ), $atts));

    return "<a href='$link' class='usl-$color usl-$size usl-$shape'>".$content."</a>";
}
add_usl_shortcode('usl_button', 'usl_button', 'Button', 'Creates a sweet button', 'Design', 'link, color(red, blue, green, orange), size(large, medium, small), shape(square, rounded, round)', '[usl_button link="#" size="large" color="blue" shape="round"]Click here[/usl_button]');

/*-------------------------------
Box
-------------------------------*/
function usl_box($atts, $content = null) {
	global $usl_add_style;
	$usl_add_style = true;
	extract(shortcode_atts(array(
    'color'  => 'red',
    'shape' => 'rounded',
    'heading' => ''
    ), $atts));

    return "<div class='usl-$color usl-$shape usl-box'><h3>$heading</h3>".$content."</div>";
}
add_shortcode('usl_box', 'usl_box');

$usl_codes[] = array(
		'Title'=>'Box',
		'Code'=>'usl_box',
		'Atts'=>'color(red, blue, green, orange), shape(square, rounded, round), heading',
		'Description'=>'Creates a nice box for your content.',
		'Example'=>'[usl_box color="blue" shape="round" heading="About me"]Lorem ipsum...[/usl_box]',
		'Category'=>'Design'
		);
?>