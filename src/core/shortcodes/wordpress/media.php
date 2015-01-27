<?php
/**
 * Contains all Render packaged shortcodes within the Design category.
 *
 * @since 1.0.0
 *
 * @package Render
 * @subpackage Shortcodes
 */

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Get some default values (mimicked how WP gets them)
$embed_width = isset( $GLOBALS['content_width'] ) ? $GLOBALS['content_width'] : 500;
$embed_height = min( ceil( $embed_width * 1.5 ), 1000 );

foreach ( array(
	// Embed
	array(
		'code'        => 'embed',
		'title'       => __( 'Embed', 'Render' ),
		'description' => __( 'It\'s super easy to embed videos, images, tweets, audio, and other content into your WordPress site', 'Render' ),
		'atts'        => array(
			'width'  => array(
				'label'      => __( 'Width', 'Render' ),
				'type'       => 'slider',
				'properties' => array(
					'value' => $embed_width,
					'max' => $embed_width * 2,
				),
			),
			'height' => array(
				'label'      => __( 'Height', 'Render' ),
				'type'       => 'slider',
				'properties' => array(
					'value' => $embed_height,
					'max' => $embed_width * 2,
				),
			),
		),
		'wrapping'    => true,
	),
	// Caption
	// TODO Get working. Captions are currently being stripped out, need to figure out why
	array(
		'noDisplay' => true,
		'code'        => 'caption',
		'title'       => __( 'Caption', 'Render' ),
		'description' => __( 'The Caption feature allows you to wrap captions around content. This is primarily used with individual images.', 'Render' ),
		'atts'        => array(
			'caption' => array(
				'label' => __( 'Caption', 'Render' ),
				'description' => __( 'If no ID is supplied, this will be used instead.', 'Render' ),
			),
			'id'    => array(
				'label' => __( 'ID', 'Render' ),
				'description' => __( 'The ID of the image inside the caption from.', 'Render' ),
			),
			'align' => array(
				'label'      => __( 'Align', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'options' => array(
						'alignnone'   => __( 'None', 'Render' ),
						'aligncenter' => __( 'Center', 'Render' ),
						'alignright'  => __( 'Right', 'Render' ),
						'alignleft'   => __( 'Left', 'Render' ),
					),
				),
			),
			'width' => array(
				'label' => __( 'Width', 'Render' ),
				'advanced' => true,
			),
			'class' => array(
				'label' => __( 'Class', 'Render' ),
				'description' => __( 'CSS class to add.', 'Render' ),
				'advanced' => true,
			),
		),
		'wrapping'    => true,
	),
	// Gallery
	array(
		'code'        => 'gallery',
		'title'       => __( 'Gallery', 'Render' ),
		'description' => __( 'The Gallery feature allows you to add one or more image galleries to your posts and pages', 'Render' ),
		'atts'        => array(
			// TODO Need to get working. Gallery media uploader needs to be figured out
			'ids'        => array(
				'label'    => __( 'IDs', 'Render' ),
				'description' => __( 'Comma delimited list of attachment IDs to use', 'Render' ),
				'required' => true,
			),
			'orderby'    => array(
				'label'      => __( 'Order By', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'default' => 'post_date',
					'options' => array(
						'menu_order' => __( 'Menu Order', 'Render' ),
						'title'      => __( 'Title', 'Render' ),
						'post_date'  => __( 'Post Date', 'Render' ),
						'rand'       => __( 'Random', 'Render' ),
						'ID'         => __( 'ID', 'Render' ),
					),
				),
			),
			'order'      => array(
				'label'      => __( 'Order', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'default' => 'DSC',
					'options' => array(
						'ASC' => __( 'Ascending', 'Render' ),
						'DSC' => __( 'Descending', 'Render' ),
					),
				),
			),
			'columns'    => array(
				'label' => __( 'Columns', 'Render' ),
				'type' => 'slider',
				'properties' => array(
					'value' => 4,
					'min' => 1,
					'max' => 8,
				),
			),
			'size'       => array(
				'label' => __( 'Size', 'Render' ),
				'description' => __( 'Image size to use.', 'Render' ),
				'type' => 'selectbox',
				'properties' => array(
					'default' => 'full',
					'callback'    => array(
						'function' => 'render_image_sizes_dropdown',
					),
				),
			),
			'include'    => array(
				'label'    => __( 'Include', 'Render' ),
				'advanced' => true,
			),
			'exclude'    => array(
				'label'    => __( 'Exclude', 'Render' ),
				'advanced' => true,
			),
			'itemtag'    => array(
				'label'    => __( 'Item Tag', 'Render' ),
				'advanced' => true,
			),
			'icontag'    => array(
				'label'    => __( 'Icon Tag', 'Render' ),
				'advanced' => true,
			),
			'captiontag' => array(
				'label'    => __( 'Caption Tag', 'Render' ),
				'advanced' => true,
			),
			'link'       => array(
				'label'    => __( 'Link', 'Render' ),
				'advanced' => true,
			),
		),
	),
	// Playlist
	array(
		'code'        => 'playlist',
		'title'       => __( 'Playlist', 'Render' ),
		'description' => __( 'The playlist shortcode implements the functionality of displaying a collection of WordPress audio or video files in a post', 'Render' ),
		'atts'        => array(
			'ids'          => array(
				'label'    => __( 'IDs', 'Render' ),
				'description' => __( 'Comma delimited list of attachment IDs to use', 'Render' ),
				'required' => true,
			),
			// TODO Integrate gallery media uploader
//			'ids'          => array(
//				'label'    => __( 'IDs', 'Render' ),
//				'type' => 'media',
//				'properties' => array(
//					'type' => 'gallery',
//				),
//				'required' => true,
//			),
			'type'         => array(
				'label'     => __( 'Type', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'default' => 'audio',
					'options' => array(
						'audio' => __( 'Audio', 'Render' ),
						'video' => __( 'Video', 'Render' ),
					),
				),
			),
			'orderby'      => array(
				'label'     => __( 'Order By', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'default' => 'post_date',
					'options' => array(
						'menu_order' => __( 'Menu Order', 'Render' ),
						'title'      => __( 'Title', 'Render' ),
						'post_date'  => __( 'Post Date', 'Render' ),
						'rand'       => __( 'Random', 'Render' ),
						'ID'         => __( 'ID', 'Render' ),
					),
				),
			),
			'order'        => array(
				'label'     => __( 'Order', 'Render' ),
				'type'    => 'selectbox', 'properties'=> array(
					'default' => 'DSC',
					'options' => array(
						'ASC' => __( 'Ascending', 'Render' ),
						'DSC' => __( 'Descending', 'Render' ),
					),
				),
			),
			'style'        => array(
				'label'     => __( 'Style', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'default' => 'light',
					'options' => array(
						'light' => __( 'Light', 'Render' ),
						'dark'  => __( 'Dark', 'Render' ),
					),
				),
			),
			'include'      => array(
				'label' => __( 'Include', 'Render' ),
				'advanced' => true,
			),
			'exclude'      => array(
				'label' => __( 'Exclude', 'Render' ),
				'advanced' => true,
			),
			'tracklist'    => array(
				'label'     => __( 'Track List', 'Render' ),
				'type'    => 'selectbox',
				'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'Render' ),
						'false' => __( 'False', 'Render' ),
					),
				),
				'advanced' => true,
			),
			'tracknumbers' => array(
				'label'     => __( 'Track Numbers', 'Render' ),
				'type'    => 'selectbox',
				'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'Render' ),
						'false' => __( 'False', 'Render' ),
					),
				),
				'advanced' => true,
			),
			'images'       => array(
				'label'     => __( 'Images', 'Render' ),
				'type'    => 'selectbox',
				'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'Render' ),
						'false' => __( 'False', 'Render' ),
					),
				),
				'advanced' => true,
			),
			'artists'      => array(
				'label'     => __( 'Artists', 'Render' ),
				'type'    => 'selectbox',
				'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'Render' ),
						'false' => __( 'False', 'Render' ),
					),
				),
				'advanced' => true,
			),
		),
	),
	// Audio
	array(
		'code'        => 'audio',
		'title'       => __( 'Audio', 'Render' ),
		'description' => __( 'The Audio feature allows you to embed audio files and play them back', 'Render' ),
		'atts'        => array(
			'src'      => array(
				'label'    => __( 'Source', 'Render' ),
				'type' => 'media',
				'properties' => array(
					'type' => 'audio',
				),
				'required' => true,
			),
			'loop'     => array(
				'label'     => __( 'Loop', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'off' => __( 'Off', 'Render' ),
						'on'  => __( 'On', 'Render' ),
					),
				),
				'advanced' => true,
			),
			'autoplay' => array(
				'label'     => __( 'Autoplay', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'off' => __( 'Off', 'Render' ),
						'on'  => __( 'On', 'Render' ),
					),
				),
				'advanced' => true,
			),
			'preload'  => array(
				'label'     => __( 'Pre Load', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'metadata' => __( 'Metadata', 'Render' ),
						'none'     => __( 'None', 'Render' ),
						'auto'     => __( 'Auto', 'Render' ),
					),
				),
				'advanced' => true,
			),
		),
	),
	// Video
	array(
		'code'        => 'video',
		'title'       => __( 'Video', 'Render' ),
		'description' => __( 'The Video feature allows you to embed video files and play them back', 'Render' ),
		'atts'        => array(
			'src'      => array(
				'label'    => __( 'Source', 'Render' ),
				'type' => 'media',
				'properties' => array(
					'type' => 'video',
				),
				'required' => true,
			),
			'width'    => array(
				'label' => __( 'Width', 'Render' ),
				'type' => 'slider',
				'properties' => array(
					'value' => 640,
					'min' => 1,
					'max' => 2000,
				),
			),
			'height'   => array(
				'label' => __( 'Height', 'Render' ),
				'type' => 'slider',
				'properties' => array(
					'value' => 360,
					'min' => 1,
					'max' => 2000,
				),
			),
			'loop'     => array(
				'label'     => __( 'Loop', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'off' => __( 'Off', 'Render' ),
						'on'  => __( 'On', 'Render' ),
					),
				),
				'advanced' => true,
			),
			'autoplay' => array(
				'label'     => __( 'Auto Play', 'Render' ),
				'type'    => 'selectbox',
				'properties' => array(
					'options' => array(
						'off' => __( 'Off', 'Render' ),
						'on'  => __( 'On', 'Render' ),
					),
				),
				'advanced' => true,
			),
			'preload'  => array(
				'label'     => __( 'Pre Load', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'metadata' => __( 'Metadata', 'Render' ),
						'none'     => __( 'None', 'Render' ),
						'auto'     => __( 'Auto', 'Render' ),
					),
				),
				'advanced' => true,
			),
			'poster'   => array(
				'label' => __( 'Poster', 'Render' ),
				'type' => 'media',
				'advanced' => true,
			),
		),
	)
) as $shortcode ) {

	$shortcode['category'] = 'media';
	$shortcode['source']   = 'WordPress';

	render_add_shortcode( $shortcode );
	render_add_shortcode_category( array(
		'id' => 'media',
		'label' => __( 'Media', 'Render'),
		'icon' => 'dashicons-admin-media',
	) );
}

// Remove wp_caption from Render shortcodes (is duplicate of caption)
add_filter( 'render_add_shortcodes', function( $shortcodes ) {

	foreach( $shortcodes as $key => $shortcode ) {
		if ( $shortcode['code'] == 'wp_caption' ) {
			unset( $shortcodes[ $key ] );
		}
	}
	return $shortcodes;
}, 999 );

/**
 * Provides a Render selectbox with options for all registered image sizes.
 *
 * @since 1.0.0
 *
 * @return array All WP image sizes.
 */
function render_image_sizes_dropdown() {

	$output = array(
		'full' => 'Full',
	);

	foreach( get_intermediate_image_sizes() as $size ) {
		$output[ $size ] = render_translate_id_to_name( $size );
	}

	return $output;
}