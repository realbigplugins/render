<?php
if ( isset( $_GET['DEBUG_TAGS'] ) ) {

	add_action( 'all', '_render_tools_tagdebug_gather' );
	add_action( 'wp_enqueue_scripts', '_render_tools_tagdebug_scripts' );
	add_action( 'admin_enqueue_scripts', '_render_tools_tagdebug_scripts' );
	add_action( 'shutdown', '_render_tools_tagdebug_output' );

	$render_debug_tags = array();

	function _render_tools_tagdebug_gather( $tag ) {

		global $render_debug_tags;

		if ( $render_debug_tags === null ) {
			$render_debug_tags = array();
		}

		if ( in_array( $tag, $render_debug_tags ) ) {
			return;
		}

		$render_debug_tags[] = $tag;
	}

	function _render_tools_tagdebug_scripts() {

		wp_enqueue_style(
			'render-tools-tagdebug',
			plugins_url( 'css/tagdebug.css', __FILE__ )
		);
	}

	function _render_tools_tagdebug_output() {

		global $render_debug_tags;

		echo '<pre id="render-tools-tagdebug">';
		echo '<p>TAGS</p>';
		var_dump( $render_debug_tags );
		echo '</pre>';
	}
}