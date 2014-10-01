<?php
/*-------------------------------
Button
-------------------------------*/
function usl_button( $atts, $content = null ) {
	global $usl_add_style;
	$usl_add_style = true;
	extract( shortcode_atts( array(
		'link'  => '#',
		'size'  => 'small',
		'color' => 'red',
		'shape' => 'rounded'
	), $atts ) );

	return "<a href='$link' class='usl-$color usl-$size usl-$shape'>" . $content . "</a>";
}

add_usl_shortcode( 'usl_button', 'usl_button', 'Button', 'Creates a sweet button', 'Design', 'link, color(red, blue, green, orange), size(large, medium, small), shape(square, rounded, round)', '[usl_button link="#" size="large" color="blue" shape="round"]Click here[/usl_button]' );

/*-------------------------------
Box
-------------------------------*/
function usl_box( $atts, $content = null ) {
	global $usl_add_style;
	$usl_add_style = true;
	extract( shortcode_atts( array(
		'color'   => 'red',
		'shape'   => 'rounded',
		'heading' => ''
	), $atts ) );

	return "<div class='usl-$color usl-$shape usl-box'><h3>$heading</h3>" . $content . "</div>";
}

add_usl_shortcode( 'usl_box', 'usl_box', 'Box', 'Creates a nice box for your content.', 'Design', 'color(red, blue, green, orange), shape(square, rounded, round), heading', '[usl_box color="blue" shape="round" heading="About me"]Lorem ipsum...[/usl_box]' );

/*-------------------------------
Columns
-------------------------------*/
function usl_column_two( $atts, $content = null ) {
	global $usl_add_style;
	$usl_add_style = true;

	return '<div class="usl-column-2">' . $content . '</div>';
}

function usl_column_three( $atts, $content = null ) {
	global $usl_add_style;
	$usl_add_style = true;

	return '<div class="usl-column-3">' . $content . '</div>';
}

function usl_column_four( $atts, $content = null ) {
	global $usl_add_style;
	$usl_add_style = true;

	return '<div class="usl-column-4">' . $content . '</div>';
}

function usl_column_five( $atts, $content = null ) {
	global $usl_add_style;
	$usl_add_style = true;

	return '<div class="usl-column-5">' . $content . '</div>';
}

add_usl_shortcode( 'usl_column_two', 'usl_column_two', 'Column 2', 'Creates a nice column that is half the width of the container.', 'Design', 'N/A', '[usl_column_two]Lorem ipsum...[/usl_column_two]' );
add_usl_shortcode( 'usl_column_three', 'usl_column_three', 'Column 3', 'Creates a nice column that is one third the width of the container.', 'Design', 'N/A', '[usl_column_three]Lorem ipsum...[/usl_column_three]' );
add_usl_shortcode( 'usl_column_four', 'usl_column_four', 'Column 4', 'Creates a nice column that is one fourth the width of the container.', 'Design', 'N/A', '[usl_column_four]Lorem ipsum...[/usl_column_four]' );
add_usl_shortcode( 'usl_column_five', 'usl_column_five', 'Column 5', 'Creates a nice column that is one fifth the width of the container.', 'Design', 'N/A', '[usl_column_five]Lorem ipsum...[/usl_column_five]' );