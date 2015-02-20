<?php
/**
 * Contains all Render packaged shortcodes within the Post category.
 *
 * @since      1.0.0
 *
 * @package    Render
 * @subpackage Shortcodes
 */

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Loops through each shortcode and adds it to Render
foreach (
	array(
		// Post meta
		array(
			'code'        => 'render_post_meta',
			'function'    => '_render_sc_post_meta',
			'title'       => __( 'Post Meta', 'Render' ),
			'description' => __( 'Displays the supplied meta information about the post.', 'Render' ),
			'tags'        => 'id author title word count published date status type excerpt',
			'atts'        => array(
				'post'        => render_sc_attr_template( 'post_list' ),
				'meta'        => array(
					'label'       => __( 'Meta', 'Render' ),
					'type'        => 'selectbox',
					'description' => __( 'The meta information of the post to show.', 'Render' ),
					'properties'  => array(
						'default'          => 'title',
						'placeholder'      => __( 'Select which meta to get', 'Render' ),
						'allowCustomInput' => true,
						'options'          => array(
							'title'          => __( 'Title', 'Render' ),
							'author'         => __( 'Author', 'Render' ),
							'status'         => __( 'Status', 'Render' ),
							'type'           => __( 'Type', 'Render' ),
							'excerpt'        => __( 'Excerpt', 'Render' ),
							'content'        => __( 'Content', 'Render' ),
							'published_date' => __( 'Published Date', 'Render' ),
							'word_count'     => __( 'Word Count', 'Render' ),
							'id'             => __( 'ID', 'Render' ),
						),
					),
				),
				'date_format' => render_sc_attr_template( 'date_format', array(
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'meta' => array(
									'type' => '==',
									'value' => 'published_date',
								),
							),
						),
					),
				) ),
			),
			'render'      => true,
		),
	) as $shortcode
) {

	$shortcode['category'] = 'post';
	$shortcode['source']   = 'Render';

	render_add_shortcode( $shortcode );
	render_add_shortcode_category( array(
		'id'    => 'post',
		'label' => __( 'Post', 'Render' ),
		'icon'  => 'dashicons-admin-post',
	) );
}

/**
 * Gets the post ID.
 *
 * @since  0.3.0
 * @access private
 *
 * @param array $atts The attributes sent to the shortcode.
 *
 * @return int The post ID.
 */
function _render_sc_post_meta( $atts = array() ) {

	$atts = shortcode_atts( array(
		'post'        => get_the_ID(),
		'meta'        => 'title',
		'date_format' => get_option( 'date_format', 'F j, Y' ),
	), $atts );

	if ( $atts['date_format'] == 'default_date' ) {
		$atts['date_format'] = get_option( 'date_format', 'F jS, Y' );
	}

	// Escape atts
	$atts = render_esc_atts( $atts );

	// Get the post object
	if ( ( $post = get_post( $atts['post'] ) ) === null ) {
		return render_sc_error( 'Cannot get post object.' );
	}

	// Attempt to get the meta and return it
	switch ( $atts['meta'] ) {

		case 'id':

			return $atts['post'];
			break;

		case 'word_count':

			return _render_sc_post_word_count( $atts );
			break;

		case 'published_date':

			return _render_sc_post_published_date( $atts, $post );
			break;

		case 'author':
		case 'title':
		case 'type':
		case 'excerpt':
		case 'content':
		case 'status':

			if ( is_callable( "_render_sc_post_$atts[meta]" ) ) {
				return call_user_func( "_render_sc_post_$atts[meta]", $post );
			} else {
				return render_sc_error( "Cannot get the post \"$atts[meta]\"." );
			}
			break;

		default:

			if ( isset( $post->{$atts['meta']} ) ) {
				return $post->{$atts['meta']};
			} else {
				return render_sc_error( "Cannot get the post \"$atts[meta]\"." );
			}
			break;
	}
}

/**
 * Gets the post author.
 *
 * @since  0.3.0
 * @access private
 *
 * @param object $post The post object.
 *
 * @return string The post author.
 */
function _render_sc_post_author( $post ) {

	if ( isset( $post->post_author ) ) {

		$author = get_the_author_meta( 'display_name', $post->post_author );
		if ( ! empty( $author ) ) {
			return $author;
		}
	} else {
		return render_sc_error( 'Cannot get post author.' );
	}
}

/**
 * Gets the post title.
 *
 * @since  0.3.0
 * @access private
 *
 * @param object $post The post object.
 *
 * @return string The post title.
 */
function _render_sc_post_title( $post ) {

	$title = get_the_title( $post );
	if ( ! empty( $title ) ) {
		return $title;
	} else {
		return render_sc_error( 'Cannot get post title.' );
	}
}

/**
 * Gets the post status.
 *
 * @since  0.3.0
 * @access private
 *
 * @param object $post The post object.
 *
 * @return string The post status
 */
function _render_sc_post_status( $post ) {

	if ( isset( $post->post_status ) ) {
		return $post->post_status;
	} else {
		return render_sc_error( "Cannot get post status." );
	}
}

/**
 * Gets the post type.
 *
 * @since  0.3.0
 * @access private
 *
 * @param object $post The post object.
 *
 * @return string The post type.
 */
function _render_sc_post_type( $post ) {

	if ( isset( $post->post_type ) ) {
		return $post->post_type;
	} else {
		return render_sc_error( "Cannot get post type." );
	}
}

/**
 * The post excerpt.
 *
 * @since  0.3.0
 * @access private
 *
 * @param object $post The post object.
 *
 * @return string The post excerpt.
 */
function _render_sc_post_excerpt( $post ) {

	if ( isset( $post->post_excerpt ) ) {
		return ! empty( $post->post_excerpt ) ? $post->post_excerpt : 'No excerpt.';
	} else {
		return render_sc_error( "Cannot get post excerpt." );
	}
}

/**
 * The post content.
 *
 * @since  0.3.0
 * @access private
 *
 * @param object $post The post object.
 *
 * @return string The post excerpt.
 */
function _render_sc_post_content( $post ) {

	$content = apply_filters( 'the_content', $post->post_content );

	return ! empty( $content ) ? $content : 'No content.';
}

/**
 * Gets the post publish date.
 *
 * @since  0.3.0
 * @access private
 *
 * @param array $atts The attributes sent to the shortcode.
 * @param array $post The post object to use.
 *
 * @return string The post publish date.
 */
function _render_sc_post_published_date( $atts = array(), $post ) {

	if ( isset( $post->post_date ) ) {
		return date( $atts['date_format'], strtotime( $post->post_date ) );
	} else {
		return render_sc_error( "Cannot get post published date." );
	}
}

/**
 * Gets the post word count.
 *
 * @since  0.3.0
 * @access private
 *
 * @param array $atts The attributes sent to the shortcode.
 *
 * @return string The post word count.
 */
function _render_sc_post_word_count( $atts = array() ) {

	$atts = shortcode_atts( array(
		'post' => get_the_ID(),
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	// Get the post object
	if ( ( $post = get_post( $atts['post'] ) ) === null ) {
		return render_sc_error( 'Cannot get post object.' );
	}

	// Get the filtered content
	$content = $post->post_content;

	// Strip this shortcode out to count the rest
	$content = preg_replace( "/\\[render_post_word_count]/s", '', $content );
	$content = do_shortcode( $content );

	// Strip tags
	$content = strip_tags( $content );

	// Convert nbsp to real space
	$content = preg_replace( '/&nbsp;/', ' ', $content );

	// And then count it!
	return str_word_count( $content );
}

/**
 * Helper function to get all posts.
 *
 * @since  1.0.0
 *
 * @param array $args Arguments to send to get_terms().
 * @return array List of all posts
 */
function render_sc_post_list( $args = array() ) {

	global $post;

	$posts = get_posts( wp_parse_args( $args, array(
		'post_type'   => 'any',
		'numberposts' => '-1',
	) ) );

	$output = array();
	if ( ! empty( $posts ) ) {
		foreach ( $posts as $_post ) {

			$object = get_post_type_object( $_post->post_type );

			if ( ! isset( $output[ $object->labels->name ] ) ) {
				$output[ $object->labels->name ] = array(
					'label'   => $object->labels->name,
					'options' => array(),
				);
			}

			$title = $_post->post_title;
			$title .= $post && (int) $post->ID === (int) $_post->ID ? ' (current post)' : '';

			$output[ $object->labels->name ]['options'][ $_post->ID ] = $title;;
		}
	}

	// Collapse if only one post type
	if ( count( (array) $output ) === 1 ) {
		foreach ( $output as $group ) {
			$output = $group['options'];
			break;
		}
	}

	return $output;
}

/**
 * Helper function to get all terms in taxonomies.
 *
 * @since  1.0.0
 *
 * @param array $args Arguments to send to get_terms().
 * @return array List of all terms in the taxonomies.
 */
function render_sc_term_list( $args = array() ) {

	if ( ! ( $taxonomies = isset( $args['taxonomies'] ) ? $args['taxonomies'] : false ) ) {
		return array();
	}

	unset( $args['taxonomies'] );

	$terms = get_terms( $taxonomies, wp_parse_args( $args, array(
		'fields' => 'id=>name',
	) ) );

	// Deal with errors
	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}

	return $terms;
}