<?php

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Render_Pointers
 *
 * Adds in pointers to help new users to Render.
 *
 * @since      1.1-beta-1
 *
 * @package    Render
 * @subpackage Pointers
 */
class Render_Pointers {

	/**
	 * All WP pointers to incorporate into Render.
	 *
	 * @since  1.1-beta-1
	 * @access private
	 *
	 * @var array
	 */
	private $pointers = array();

	/**
	 * Initializes the pointers class and includes necessities.
	 *
	 * @since 1.1-beta-1
	 */
	public function __construct() {

		// Necessary scripts
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );

		// Localize the pointer data
		add_filter( 'render_localized_data', array( $this, '_pointers_data' ) );
	}

	/**
	 * Outputs the pointers' HTML and JS.
	 *
	 * @since  1.1-beta-1
	 * @access private
	 *
	 * @param array|null $data The pointers data.
	 *
	 * @return array The new pointers data.
	 */
	function _pointers_data( $data ) {

		/**
		 * Allows external filtering of the pointers.
		 *
		 * @since 1.1-beta-1
		 *
		 * @hooked Render_tinymce::add_pointers() 10
		 * @hooked Render::add_main_pointer()     10
		 * @hooked _render_widget_pointers()      10
		 */
		$this->pointers = apply_filters( 'render_pointers', $this->pointers );

		// Get dismissed pointers
		$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

		foreach ( $this->pointers as $pointer_ID => $pointer ) {

			// Allow pointer reset
			if ( isset( $_GET['RENDER_RESTORE_POINTERS'] ) ) {

				// Unset this pointer from dismissed pointers, if it's set
				if ( in_array( "render_$pointer_ID", $dismissed_pointers ) ) {
					if ( ( $key = array_search( "render_$pointer_ID", $dismissed_pointers ) ) !== false ) {
						unset( $dismissed_pointers[ $key ] );
					}
				}

				update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', implode( ',', (array) $dismissed_pointers ) );
			}

			// Skip if the user has dismissed the pointer
			if ( in_array( "render_$pointer_ID", $dismissed_pointers ) ) {
				continue;
			}

			$data['pointers'][ $pointer_ID ] = $pointer;
		}

		return $data;
	}
}