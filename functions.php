<?php
// Create the new function for adding shortcodes
function add_usl_shortcode( $name, $function, $title, $desc, $category, $atts = '', $example = '' ) {

// Create the actual shortcode
add_shortcode( $name, $function );

// Add the shortcode info to our list
global $usl_codes;
	$usl_codes[] = array(
			'Title' => $title,
			'Code' => $name,
			'Atts' => $atts,
			'Description' => $desc,
			'Example' => $example,
			'Category' => $category
			);

	// Add the category if it isn't there already
	global $usl_cats;
	if (in_array( $category, $usl_cats )) {
		return $usl_cats;
	} else {
		$usl_cats[]=$category;
		return $usl_cats;
	}
}