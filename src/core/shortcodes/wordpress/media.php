<?php
/**
 * Contains all Render packaged shortcodes within the Design category.
 *
 * @since Render 1.0.0
 *
 * @package Render
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// Embed
	// TODO Test and fix up
	array(
		'code'        => 'embed',
		'title'       => __( 'Embed', 'Render' ),
		'description' => __( 'It\'s super easy to embed videos, images, tweets, audio, and other content into your WordPress site', 'Render' ),
		'atts'        => array(
			'width'  => array(
				'label'      => __( 'Width', 'Render' ),
				'type'       => 'slider',
				'properties' => array(
					'max' => 2000,
				),
			),
			'height' => array(
				'label'      => __( 'Height', 'Render' ),
				'type'       => 'slider',
				'properties' => array(
					'max' => 2000,
				),
			),
		),
		'wrapping'    => true,
	),
	// Caption
	// TODO Test and fix up
	array(
		'code'        => 'caption',
		'title'       => __( 'Caption', 'Render' ),
		'description' => __( 'The Caption feature allows you to wrap captions around content. This is primarily used with individual images.', 'Render' ),
		'atts'        => array(
			'id'    => array(
				'label' => __( 'ID', 'Render' ),
			),
			'class' => array(
				'label' => __( 'Class', 'Render' ),
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
			),
		),
		'wrapping'    => true,
	),
	// Gallery
	// TODO Test and fix up
	array(
		'code'        => 'gallery',
		'title'       => __( 'Gallery', 'Render' ),
		'description' => __( 'The Gallery feature allows you to add one or more image galleries to your posts and pages', 'Render' ),
		'atts'        => array(
			'ids'        => array(
				'label'    => __( 'IDs', 'Render' ),
				'required' => true,
			),
			'orderby'    => array(
				'label'      => __( 'Order By', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
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
					'options' => array(
						'ASC' => __( 'Ascending', 'Render' ),
						'DSC' => __( 'Descending', 'Render' ),
					),
				),
			),
			'columns'    => array(
				'label' => __( 'Columns', 'Render' ),
			),
			'size'       => array(
				'label' => __( 'Size', 'Render' ),
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
	// TODO Test and fix up
	array(
		'code'        => 'playlist',
		'title'       => __( 'Playlist', 'Render' ),
		'description' => __( 'The playlist shortcode implements the functionality of displaying a collection of WordPress audio or video files in a post', 'Render' ),
		'atts'        => array(
			'type'         => array(
				'label'     => __( 'Type', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'audio' => __( 'Audio', 'Render' ),
						'video' => __( 'Video', 'Render' ),
					),
				),
			),
			'orderby'      => array(
				'label'     => __( 'Order By', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
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
					'options' => array(
						'ASC' => __( 'Ascending', 'Render' ),
						'DSC' => __( 'Descending', 'Render' ),
					),
				),
			),
			'ids'          => array(
				'label'    => __( 'IDs', 'Render' ),
				'required' => true,
			),
			'include'      => array(
				'label' => __( 'Include', 'Render' ),
			),
			'exclude'      => array(
				'label' => __( 'Exclude', 'Render' ),
			),
			'style'        => array(
				'label'     => __( 'Style', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'light' => __( 'Light', 'Render' ),
						'dark'  => __( 'Dark', 'Render' ),
					),
				),
			),
			'tracklist'    => array(
				'label'     => __( 'Track List', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'Render' ),
						'false' => __( 'False', 'Render' ),
					),
				),
			),
			'tracknumbers' => array(
				'label'     => __( 'Track Numbers', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'Render' ),
						'false' => __( 'False', 'Render' ),
					),
				),
			),
			'images'       => array(
				'label'     => __( 'Images', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'Render' ),
						'false' => __( 'False', 'Render' ),
					),
				),
			),
			'artists'      => array(
				'label'     => __( 'Artists', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'Render' ),
						'false' => __( 'False', 'Render' ),
					),
				),
			),
		),
	),
	// Audio
	// TODO Test and fix up
	array(
		'code'        => 'audio',
		'title'       => __( 'Audio', 'Render' ),
		'description' => __( 'The Audio feature allows you to embed audio files and play them back', 'Render' ),
		'atts'        => array(
			'src'      => array(
				'label'    => __( 'Source', 'Render' ),
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
			),
			'autoplay' => array(
				'label'     => __( 'Autoplay', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'off' => __( 'Off', 'Render' ),
						'on'  => __( 'On', 'Render' ),
					),
				),
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
			),
		),
	),
	// Video
	// TODO Test and fix up
	array(
		'code'        => 'video',
		'title'       => __( 'Video', 'Render' ),
		'description' => __( 'The Video feature allows you to embed video files and play them back', 'Render' ),
		'atts'        => array(
			'src'      => array(
				'label'    => __( 'Source', 'Render' ),
				'required' => true,
			),
			'poster'   => array(
				'label' => __( 'Poster', 'Render' ),
			),
			'loop'     => array(
				'label'     => __( 'Loop', 'Render' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'off' => __( 'Off', 'Render' ),
						'on'  => __( 'On', 'Render' ),
					),
				),
			),
			'autoplay' => array(
				'label'     => __( 'Auto Play', 'Render' ),
				'type'    => 'selectbox', 'propreties' => array(
					'options' => array(
						'off' => __( 'Off', 'Render' ),
						'on'  => __( 'On', 'Render' ),
					),
				),
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
			),
			'height'   => array(
				'label' => __( 'Height', 'Render' ),
			),
			'width'    => array(
				'label' => __( 'Width', 'Render' ),
			),
		),
	)
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'media';
	$shortcode['source']   = 'WordPress';
	render_add_shortcode( $shortcode );
}