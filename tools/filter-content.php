<?php
add_filter( 'the_content', function ( $content ) {

	var_dump( $content );
	return $content;
} );