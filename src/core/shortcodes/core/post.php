<?php
/**
 * Contains all USL packaged shortcodes within the Post category.
 *
 * @since      USL 1.0.0
 *
 * @package    USL
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// Post meta
	array(
		'code'        => 'usl_post_meta',
		'function'    => '_usl_sc_post_meta',
		'title'       => __( 'Post Meta', 'USL' ),
		'description' => __( 'Displays the supplied meta information about the post.', 'USL' ),
		'tags'        => 'id author title word count published date status type excerpt',
		'atts'        => array(
			'post' => usl_sc_attr_template( 'post_list' ),
			'meta' => array(
				'label'       => __( 'Meta', 'USL' ),
				'type'        => 'selectbox',
				'description' => __( 'The meta information of the post to show (custom input allowed).', 'USL' ),
				'properties'  => array(
					'placeholder'      => __( 'Title', 'USL' ),
					'allowCustomInput' => true,
					'options'          => array(
						'title'          => __( 'Title', 'USL' ),
						'author'         => __( 'Author', 'USL' ),
						'status'         => __( 'Status', 'USL' ),
						'type'           => __( 'Type', 'USL' ),
						'excerpt'        => __( 'Excerpt', 'USL' ),
						'content'        => __( 'Content', 'USL' ),
						'published_date' => __( 'Published Date', 'USL' ),
						'word_count'     => __( 'Word Count', 'USL' ),
						'id'             => __( 'ID', 'USL' ),
					),
				),
			),
		),
		'render'      => true,
	),
	// Post published date
	array(
		'code'        => 'usl_post_published_date',
		'function'    => '_usl_sc_post_published_date',
		'title'       => __( 'Post Published Date', 'USL' ),
		'description' => __( 'Displays the published date of the supplied post.', 'USL' ),
		'atts'        => array(
			'post'        => usl_sc_attr_template( 'post_list' ),
			'date_format' => usl_sc_attr_template( 'date_format' ),
		),
		'render'      => true,
	),
	// Post word count
	array(
		'code'        => 'usl_post_word_count',
		'function'    => '_usl_sc_post_word_count',
		'title'       => __( 'Post Word Count', 'USL' ),
		'description' => __( 'Displays the word count of the supplied post.', 'USL' ),
		'atts'        => array(
			'post' => usl_sc_attr_template( 'post_list' ),
		),
		'render'      => true,
	),
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'post';
	$shortcode['source']   = 'Ultimate Shortcodes Library';
	usl_add_shortcode( $shortcode );
}

/**
 * Gets the post ID.
 *
 * @since  0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return int The post ID.
 */
function _usl_sc_post_meta( $atts ) {

	$atts = shortcode_atts( array(
		'post' => get_the_ID(),
		'meta' => 'title',
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	// Get the post object
	if ( ( $post = get_post( $atts['post'] ) ) === null ) {
		return _usl_sc_error( 'Cannot get post object.' );
	}

	// Attempt to get the meta and return it
	switch ( $atts['meta'] ) {

		case 'id':

			return $atts['post'];
			break;

		case 'author':
		case 'title':
		case 'type':
		case 'excerpt':
		case 'content':
		case 'status':

			if ( is_callable( "_usl_sc_post_$atts[meta]" ) ) {
				return call_user_func( "_usl_sc_post_$atts[meta]", $post );
			}
			break;

		default:

			if ( isset( $post->{$atts['meta']} ) ) {
				return $post->{$atts['meta']};
			} else {
				return _usl_sc_error( "Cannot get post $atts[meta]." );
			}
			break;
	}
}

/**
 * Gets the post author.
 *
 * @since  0.3.0
 * @access Private
 *
 * @param object $post The post object.
 *
 * @return string The post author.
 */
function _usl_sc_post_author( $post ) {

	if ( isset( $post->post_author ) ) {

		$author = get_the_author_meta( 'display_name', $post->post_author );
		if ( ! empty( $author ) ) {
			return $author;
		}
	} else {
		return _usl_sc_error( 'Cannot get post author.' );
	}
}

/**
 * Gets the post title.
 *
 * @since  0.3.0
 * @access Private
 *
 * @param object $post The post object.
 *
 * @return string The post title.
 */
function _usl_sc_post_title( $post ) {

	$title = get_the_title( $post );
	if ( ! empty( $title ) ) {
		return $title;
	} else {
		return _usl_sc_error( 'Cannot get post title.' );
	}
}

/**
 * Gets the post status.
 *
 * @since  0.3.0
 * @access Private
 *
 * @param object $post The post object.
 *
 * @return string The post status
 */
function _usl_sc_post_status( $post ) {

	if ( isset( $post->post_status ) ) {
		return $post->post_status;
	} else {
		return _usl_sc_error( "Cannot get post status." );
	}
}

/**
 * Gets the post type.
 *
 * @since  0.3.0
 * @access Private
 *
 * @param object $post The post object.
 *
 * @return string The post type.
 */
function _usl_sc_post_type( $post ) {

	if ( isset( $post->post_type ) ) {
		return $post->post_type;
	} else {
		return _usl_sc_error( "Cannot get post type." );
	}
}

/**
 * The post excerpt.
 *
 * @since  0.3.0
 * @access Private
 *
 * @param object $post The post object.
 *
 * @return string The post excerpt.
 */
function _usl_sc_post_excerpt( $post ) {

	if ( isset( $post->post_excerpt ) ) {
		return ! empty( $post->post_excerpt ) ? $post->post_excerpt : 'No excerpt.';
	} else {
		return _usl_sc_error( "Cannot get post excerpt." );
	}
}

/**
 * The post content.
 *
 * @since  0.3.0
 * @access Private
 *
 * @param object $post The post object.
 *
 * @return string The post excerpt.
 */
function _usl_sc_post_content( $post ) {

	$content = apply_filters( 'the_content', $post->post_content );

	return ! empty( $content ) ? $content : 'No content.';
}

/**
 * Gets the post publish date.
 *
 * @since  0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The post publish date.
 */
function _usl_sc_post_published_date( $atts ) {

	// TODO Once conditional atts are implemented, move this back into the main post meta shortcode and have the date attr be a conditional attr that only shows when the meta attr has date selected.

	$atts = shortcode_atts( array(
		'post'        => get_the_ID(),
		'date_format' => get_option( 'date_format', 'F j, Y' ),
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	// Get the post object
	if ( ( $post = get_post( $atts['post'] ) ) === null ) {
		return _usl_sc_error( 'Cannot get post object.' );
	}

	if ( isset( $post->post_date ) ) {
		return date( $atts['date_format'], strtotime( $post->post_date ) );
	} else {
		return _usl_sc_error( "Cannot get post published date." );
	}
}

/**
 * Gets the post word count.
 *
 * @since  0.3.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The post word count.
 */
function _usl_sc_post_word_count( $atts ) {

	$atts = shortcode_atts( array(
		'post' => get_the_ID(),
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	// Get the post object
	if ( ( $post = get_post( $atts['post'] ) ) === null ) {
		return _usl_sc_error( 'Cannot get post object.' );
	}

	// Get the filtered content
	$content = apply_filters( 'the_content', $post->post_content );

	// Convert nbsp to real space
	$content = preg_replace( '/&nbsp;/', ' ', $content );

	// Strip tags
	$content = strip_tags( $content );

	// And then count it!
	return str_word_count( $content );
}

/**
 * Helper function to get all posts.
 *
 * @since  1.0.0
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
			$object = get_post_type_object( $post->post_type );

			if ( ! isset( $output[ $object->labels->name ] ) ) {
				$output[ $object->labels->name ] = array(
					'label'   => $object->labels->name,
					'options' => array(),
				);
			}
			$output[ $object->labels->name ]['options'][ $post->ID ] = $post->post_title;
		}
	}

	return $output;
}