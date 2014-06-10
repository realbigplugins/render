<?php
// Experimenting with the adding part
function add_usl_shortcode($name, $function, $title, $desc, $category, $atts, $example) {
	global $usl_codes;
add_shortcode( $name, $function );
$usl_codes[] = array(
		'Title' => $title,
		'Code' => $name,
		'Atts' => $atts,
		'Description' => $desc,
		'Example' => $example,
		'Category' => $category
		);
}