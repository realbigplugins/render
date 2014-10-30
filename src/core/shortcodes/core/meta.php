<?php
/**
 * Contains all USL packaged shortcodes within the Meta category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */
class USL_CoreShortcodes_Meta {

	private $_shortcodes = array(
		// ID
		array(
			'code' => 'usl_id',
			'function' => '_usl_sc_id',
			'title' => 'Post ID',
			'description' => 'Displays the ID of the current post.',
		),
		// Author
		array(
			'code' => 'usl_author',
			'function' => '_usl_sc_author',
			'title' => 'Post Author',
			'description' => 'Displays the author of the current post.',
		),
		// Title
		array(
			'code' => 'usl_title',
			'function' => '_usl_sc_title',
			'title' => 'Post Title',
			'description' => 'Displays the title of the current post.',
		),
		// Published date
		array(
			'code' => 'usl_published',
			'function' => '_usl_sc_published',
			'title' => 'Post Published',
			'description' => 'Displays the published date of the current post.',
		),
		// Status
		array(
			'code' => 'usl_status',
			'function' => '_usl_sc_status',
			'title' => 'Post Status',
			'description' => 'Displays the status of the current post.',
		),
		// Type
		array(
			'code' => 'usl_type',
			'function' => '_usl_sc_type',
			'title' => 'Post Type',
			'description' => 'Displays the type of the current post.',
		),
		// Excerpt
		array(
			'code' => 'usl_excerpt',
			'function' => '_usl_sc_excerpt',
			'title' => 'Post Excerpt',
			'description' => 'Displays the excerpt of the current post.',
		),
	);

	function __construct() {

		foreach ( $this->_shortcodes as $shortcode ) {
			$shortcode['category'] = 'Meta';
			usl_add_shortcode( $shortcode );
		}
	}
}

new USL_CoreShortcodes_Meta();

/**
 * Gets the current post ID.
 *
 * @since 0.3.0
 * @access Private
 *
 * @return int The current post ID.
 */
function _usl_sc_id() {
	return get_the_ID();
}

/**
 * Gets the current post author.
 *
 * @since 0.3.0
 * @access Private
 *
 * @global object $post The current post object.
 *
 * @return string The current post author.
 */
function _usl_sc_author() {

	global $post;

	$user = get_userdata( $post->post_author );
	return $user->user_nicename;
}

/**
 * Gets the current post title.
 *
 * @since 0.3.0
 * @access Private
 *
 * @global object $post The current post object.
 *
 * @return string The current post title.
 */
function _usl_sc_title() {

	global $post;

	return $post->post_title;
}

/**
 * Gets the current post publish date.
 *
 * @since 0.3.0
 * @access Private
 *
 * @global object $post The current post object.
 *
 * @return string The current post publish date.
 */
function _usl_sc_published() {

	global $post;

	return $post->post_date;
}

/**
 * Gets the current post status.
 *
 * @since 0.3.0
 * @access Private
 *
 * @global object $post The current post object.
 *
 * @return string The current post status
 */
function _usl_sc_status() {

	global $post;

	return $post->post_status;
}

/**
 * Gets the current post type.
 *
 * @since 0.3.0
 * @access Private
 *
 * @global object $post The current post object.
 *
 * @return string The current post type.
 */
function _usl_sc_type() {

	global $post;

	return $post->post_type;
}

/**
 * The current post excerpt.
 *
 * @since 0.3.0
 * @access Private
 *
 * @global object $post The current post object.
 *
 * @return string The current post excerpt.
 */
function _usl_sc_excerpt() {

	global $post;

	return $post->post_excerpt;
}