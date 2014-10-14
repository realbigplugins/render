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

/*-------------------------------
Get current day (number)
-------------------------------*/
function usl_day_num() {
	return date("j");
}
add_usl_shortcode( 'usl_day_num', 'usl_day_num', 'Day of the month', 'Outputs the current day of the month.', 'Time' );

/*-------------------------------
Get current day
-------------------------------*/
function usl_day() {
	return date("l");
}
add_usl_shortcode( 'usl_day', 'usl_day', 'Current day', 'Outputs the current day of the week.', 'Time' );

/*-------------------------------
Day of the year
-------------------------------*/
function usl_day_of_year() {
	return date("z");
}
add_usl_shortcode( 'usl_day_of_year', 'usl_day_of_year', 'Day of year', 'Outputs the current day of the current year.', 'Time' );