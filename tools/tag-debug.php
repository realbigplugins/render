<?php
$usl_debug_tags = array();
add_action( 'all', function ( $tag ) {

	global $usl_debug_tags;

	if ( in_array( $tag, $usl_debug_tags ) ) {
		return;
	}

	$usl_debug_tags[] = $tag;
} );

add_action( 'shutdown', function () {

	global $usl_debug_tags;

//	if ( isset( $_GET['tagdebug'] ) ) {
//		var_dump( $usl_debug_tags );
//	}
} );