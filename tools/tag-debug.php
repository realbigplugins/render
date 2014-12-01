<?php
if ( isset( $_GET['DEBUG_TAGS'] ) ) {

	add_action( 'all', '_usl_tools_tagdebug_gather' );
	add_action( 'wp_enqueue_scripts', '_usl_tools_tagdebug_scripts' );
	add_action( 'admin_enqueue_scripts', '_usl_tools_tagdebug_scripts' );
	add_action( 'shutdown', '_usl_tools_tagdebug_output' );

	$usl_debug_tags = array();

	function _usl_tools_tagdebug_gather( $tag ) {

		global $usl_debug_tags;

		if ( $usl_debug_tags === null ) {
			$usl_debug_tags = array();
		}

		if ( in_array( $tag, $usl_debug_tags ) ) {
			return;
		}

		$usl_debug_tags[] = $tag;
	}

	function _usl_tools_tagdebug_scripts() {

		wp_enqueue_style(
			'usl-tools-tagdebug',
			plugins_url( 'css/tagdebug.css', __FILE__ )
		);
	}

	function _usl_tools_tagdebug_output() {

		global $usl_debug_tags;

		echo '<pre id="usl-tools-tagdebug">';
		echo '<p>TAGS</p>';
		var_dump( $usl_debug_tags );
		echo '</pre>';
	}
}