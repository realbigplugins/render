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
/*-------------------------------
Number of days until...
-------------------------------*/
function usl_days_until($atts, $content = null){
  extract(shortcode_atts(array(
     'month' => '',
     'day'   => '',
     'year'  => ''
    ), $atts));
    $remain = ceil((mktime( 0,0,0,(int)$month,(int)$day,(int)$year) - time())/86400);
    if( $remain >= 1 ){
        return $daysremain = "$remain";
    } else {
        return $content;
    }
}
add_shortcode('usl_days_until', 'usl_days_until');
$usl_codes[] = array(
		'Title'=>'Number of days until specific date',
		'Code'=>'usl_days_until',
		'Atts'=>'day, month, year',
		'Description'=>'Outputs the number of days until a specified date.',
		'Example'=>'[usl_days_until month="2" day="14" year="2014"]',
		'Category'=>'Technical'
		);
?>