<?php
if ( isset( $_GET['DEBUG_CONTENT'] ) ) {
	add_filter( 'the_content', function ( $content ) {
		var_dump( $content );
		return $content;
	} );
}