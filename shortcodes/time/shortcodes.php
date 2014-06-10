<?php
/*-------------------------------
Get current month
-------------------------------*/
function usl_month() {
$usl_date=getdate(date("U"));
return "$usl_date[month]";
}
add_usl_shortcode( 'usl_month', 'usl_month', 'Current month', 'Outputs the current month.', 'Time' );

/*-------------------------------
Get current year
-------------------------------*/
function usl_year() {
	return date("Y");
}
add_usl_shortcode( 'usl_year', 'usl_year', 'Current year', 'Outputs the current year.', 'Time' );