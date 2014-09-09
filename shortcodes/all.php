<?php
$sources = array(
	'design',
	'time',
	'user',
	'site'
);
foreach ( $sources as $source ) {
	include_once( $source . '/shortcodes.php' );
}