<?php

/**
 * Contains all USL packaged shortcodes within the Post category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */
class USL_CoreShortcodes_Post {

	private $_shortcodes = array(
		// ID
		array(
			'code'        => 'usl_post_id',
			'function'    => '_usl_sc_post_id',
			'title'       => 'Post ID',
			'description' => 'Displays the ID of the post.',
			'atts'        => array(
				'post' => array(
					'selectbox' => array(
						'callback'    => '_usl_sc_post_list',
						'placeholder' => 'Defaults to the current post.',
					),
				),
			),
			'render'      => true,
		),
		// Author
		array(
			'code'        => 'usl_author',
			'function'    => '_usl_sc_author',
			'title'       => 'Post Author',
			'description' => 'Displays the author of the post.',
			'atts'        => array(
				'post' => array(
					'selectbox' => array(
						'callback'    => '_usl_sc_post_list',
						'placeholder' => 'Defaults to the current post.',
					),
				),
			),
			'render'      => true,
		),
		// Title
		array(
			'code'        => 'usl_title',
			'function'    => '_usl_sc_title',
			'title'       => 'Post Title',
			'description' => 'Displays the title of the post.',
			'atts'        => array(
				'post' => array(
					'selectbox' => array(
						'callback'    => '_usl_sc_post_list',
						'placeholder' => 'Defaults to the current post.',
					),
				),
			),
			'render'      => true,
		),
		// Word Count
		array(
			'code'        => 'usl_word_count',
			'function'    => '_usl_sc_word_count',
			'title'       => 'Post Word Count',
			'description' => 'Outputs the total word count for the post.',
			'atts'        => array(
				'post' => array(
					'description' => 'NOTE: To update the word count, update the post.',
					'selectbox'   => array(
						'callback'    => '_usl_sc_post_list',
						'placeholder' => 'Defaults to the current post.',
					),
				),
			),
			'example'     => '[usl_word_count]',
			'wrapping'    => false,
		),
		// Published Date
		array(
			'code'        => 'usl_published',
			'function'    => '_usl_sc_published',
			'title'       => 'Post Published',
			'description' => 'Displays the published date of the post.',
			'atts'        => array(
				'post' => array(
					'selectbox' => array(
						'callback'    => '_usl_sc_post_list',
						'placeholder' => 'Defaults to the current post.',
					),
				),
			),
			'render'      => true,
		),
		// Status
		array(
			'code'        => 'usl_status',
			'function'    => '_usl_sc_status',
			'title'       => 'Post Status',
			'description' => 'Displays the status of the post.',
			'atts'        => array(
				'post' => array(
					'selectbox' => array(
						'callback'    => '_usl_sc_post_list',
						'placeholder' => 'Defaults to the current post.',
					),
				),
			),
			'render'      => true,
		),
		// Type
		array(
			'code'        => 'usl_type',
			'function'    => '_usl_sc_type',
			'title'       => 'Post Type',
			'description' => 'Displays the type of the post.',
			'atts'        => array(
				'post' => array(
					'selectbox' => array(
						'callback'    => '_usl_sc_post_list',
						'placeholder' => 'Defaults to the current post.',
					),
				),
			),
		),
		// Excerpt
		array(
			'code'        => 'usl_excerpt',
			'function'    => '_usl_sc_excerpt',
			'title'       => 'Post Excerpt',
			'description' => 'Displays the excerpt of the post.',
			'atts'        => array(
				'post' => array(
					'selectbox' => array(
						'callback'    => '_usl_sc_post_list',
						'placeholder' => 'Defaults to the current post.',
					),
				),
			),
			'render'      => true,
		),
	);

	function __construct() {

		foreach ( $this->_shortcodes as $shortcode ) {
			$shortcode['category'] = 'post';
			usl_add_shortcode( $shortcode );
		}
	}
}

new USL_CoreShortcodes_Post();

/**
 * Gets the post ID.
 *
 * @since 0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return int The post ID.
 */
function _usl_sc_post_id( $atts ) {

	$atts = shortcode_atts( array(
		'post' => get_the_ID(),
	), $atts );

	return $atts['post'];
}

/**
 * Gets the post author.
 *
 * @since 0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The post author.
 */
function _usl_sc_author( $atts ) {

	$atts = shortcode_atts( array(
		'post' => get_the_ID(),
	), $atts );

	$post = get_post( $atts['post'] );

	$user = get_userdata( $post->post_author );

	return $user->user_nicename;
}

/**
 * Gets the post title.
 *
 * @since 0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The post title.
 */
function _usl_sc_title( $atts ) {

	$atts = shortcode_atts( array(
		'post' => get_the_ID(),
	), $atts );

	$post = get_post( $atts['post'] );;

	return $post->post_title;
}

/**
 * Gets the post word count.
 *
 * @since 0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The post word count.
 */
function _usl_sc_word_count( $atts ) {

	$atts = shortcode_atts( array(
		'post' => get_the_ID(),
	), $atts );

	$post = get_post( $atts['post'] );;

	return str_word_count( $post->post_content ) - 3;
}

/**
 * Gets the post publish date.
 *
 * @since 0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The post publish date.
 */
function _usl_sc_published( $atts ) {

	$atts = shortcode_atts( array(
		'post' => get_the_ID(),
	), $atts );

	$post = get_post( $atts['post'] );;

	return $post->post_date;
}

/**
 * Gets the post status.
 *
 * @since 0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The post status
 */
function _usl_sc_status( $atts ) {

	$atts = shortcode_atts( array(
		'post' => get_the_ID(),
	), $atts );

	$post = get_post( $atts['post'] );;

	return $post->post_status;
}

/**
 * Gets the post type.
 *
 * @since 0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The post type.
 */
function _usl_sc_type( $atts ) {

	$atts = shortcode_atts( array(
		'post' => get_the_ID(),
	), $atts );

	$post = get_post( $atts['post'] );;

	return $post->post_type;
}

/**
 * The post excerpt.
 *
 * @since 0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The post excerpt.
 */
function _usl_sc_excerpt( $atts ) {

	$atts = shortcode_atts( array(
		'post' => get_the_ID(),
	), $atts );

	$post = get_post( $atts['post'] );;

	return $post->post_excerpt;
}

/**
 * Helper function to get all posts.
 *
 * @since 1.0.0
 * @access Private
 *
 * @return null|array List of all posts
 */
function _usl_sc_post_list() {

	$posts = get_posts( array(
			'post_type'   => 'any',
			'numberposts' => '-1',
		)
	);

	$output = array();
	if ( ! empty( $posts ) ) {
		foreach ( $posts as $post ) {
			$obj = get_post_type_object( $post->post_type );
			$output[ $obj->labels->name ][ $post->ID ] = $post->post_title;
		}
	}

	return $output;
}