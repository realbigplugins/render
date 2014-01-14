<?php
/*-------------------------------
Header info
-------------------------------*/
$usl_cats[]='Technical';

/*-------------------------------
Get current month
-------------------------------*/
function usl_month() {
$usl_date=getdate(date("U"));
return "$usl_date[month]";
}
add_shortcode( 'usl_month', 'usl_month' );
$usl_codes[] = array(
		'Title'=>'Current month',
		'Code'=>'usl_month',
		'Description'=>'Outputs the current month.',
		'Category'=>'Technical'
		);

?>