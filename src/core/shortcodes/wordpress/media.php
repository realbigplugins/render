<?php
/**
 * Contains all USL packaged shortcodes within the Design category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// Embed
	array(
		'code'        => 'embed',
		'title'       => __( 'Embed', 'USL' ),
		'description' => __( 'It\'s super easy to embed videos, images, tweets, audio, and other content into your WordPress site', 'USL' ),
		'atts'        => array(
			'width'  => array(
				'label'      => __( 'Width', 'USL' ),
				'type'       => 'slider',
				'properties' => array(
					'max' => 2000,
				),
			),
			'height' => array(
				'label'      => __( 'Height', 'USL' ),
				'type'       => 'slider',
				'properties' => array(
					'max' => 2000,
				),
			),
		),
		'wrapping'    => true,
	),
	// Caption
	array(
		'code'        => 'caption',
		'title'       => __( 'Caption', 'USL' ),
		'description' => __( 'The Caption feature allows you to wrap captions around content. This is primarily used with individual images.', 'USL' ),
		'atts'        => array(
			'id'    => array(
				'label' => __( 'ID', 'USL' ),
			),
			'class' => array(
				'label' => __( 'Class', 'USL' ),
			),
			'align' => array(
				'label'      => __( 'Align', 'USL' ),
				'type'       => 'selectbox',
				'properties' => array(
					'options' => array(
						'alignnone'   => __( 'None', 'USL' ),
						'aligncenter' => __( 'Center', 'USL' ),
						'alignright'  => __( 'Right', 'USL' ),
						'alignleft'   => __( 'Left', 'USL' ),
					),
				),
			),
			'width' => array(
				'label' => __( 'Width', 'USL' ),
			),
		),
		'wrapping'    => true,
	),
	// Gallery
	array(
		'code'        => 'gallery',
		'title'       => __( 'Gallery', 'USL' ),
		'description' => __( 'The Gallery feature allows you to add one or more image galleries to your posts and pages', 'USL' ),
		'atts'        => array(
			'ids'        => array(
				'label'    => __( 'IDs', 'USL' ),
				'required' => true,
			),
			'orderby'    => array(
				'label'      => __( 'Order By', 'USL' ),
				'type'       => 'selectbox',
				'properties' => array(
					'options' => array(
						'menu_order' => __( 'Menu Order', 'USL' ),
						'title'      => __( 'Title', 'USL' ),
						'post_date'  => __( 'Post Date', 'USL' ),
						'rand'       => __( 'Random', 'USL' ),
						'ID'         => __( 'ID', 'USL' ),
					),
				),
			),
			'order'      => array(
				'label'      => __( 'Order', 'USL' ),
				'type'       => 'selectbox',
				'properties' => array(
					'options' => array(
						'ASC' => __( 'Ascending', 'USL' ),
						'DSC' => __( 'Descending', 'USL' ),
					),
				),
			),
			'columns'    => array(
				'label' => __( 'Columns', 'USL' ),
			),
			'size'       => array(
				'label' => __( 'Size', 'USL' ),
			),
			'include'    => array(
				'label'    => __( 'Include', 'USL' ),
				'advanced' => true,
			),
			'exclude'    => array(
				'label'    => __( 'Exclude', 'USL' ),
				'advanced' => true,
			),
			'itemtag'    => array(
				'label'    => __( 'Item Tag', 'USL' ),
				'advanced' => true,
			),
			'icontag'    => array(
				'label'    => __( 'Icon Tag', 'USL' ),
				'advanced' => true,
			),
			'captiontag' => array(
				'label'    => __( 'Caption Tag', 'USL' ),
				'advanced' => true,
			),
			'link'       => array(
				'label'    => __( 'Link', 'USL' ),
				'advanced' => true,
			),
		),
	),
	// Playlist
	array(
		'code'        => 'playlist',
		'title'       => __( 'Playlist', 'USL' ),
		'description' => __( 'The playlist shortcode implements the functionality of displaying a collection of WordPress audio or video files in a post', 'USL' ),
		'atts'        => array(
			'type'         => array(
				'label'     => __( 'Type', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'audio' => __( 'Audio', 'USL' ),
						'video' => __( 'Video', 'USL' ),
					),
				),
			),
			'orderby'      => array(
				'label'     => __( 'Order By', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'menu_order' => __( 'Menu Order', 'USL' ),
						'title'      => __( 'Title', 'USL' ),
						'post_date'  => __( 'Post Date', 'USL' ),
						'rand'       => __( 'Random', 'USL' ),
						'ID'         => __( 'ID', 'USL' ),
					),
				),
			),
			'order'        => array(
				'label'     => __( 'Order', 'USL' ),
				'type'    => 'selectbox', 'properties'=> array(
					'options' => array(
						'ASC' => __( 'Ascending', 'USL' ),
						'DSC' => __( 'Descending', 'USL' ),
					),
				),
			),
			'ids'          => array(
				'label'    => __( 'IDs', 'USL' ),
				'required' => true,
			),
			'include'      => array(
				'label' => __( 'Include', 'USL' ),
			),
			'exclude'      => array(
				'label' => __( 'Exclude', 'USL' ),
			),
			'style'        => array(
				'label'     => __( 'Style', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'light' => __( 'Light', 'USL' ),
						'dark'  => __( 'Dark', 'USL' ),
					),
				),
			),
			'tracklist'    => array(
				'label'     => __( 'Track List', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'USL' ),
						'false' => __( 'False', 'USL' ),
					),
				),
			),
			'tracknumbers' => array(
				'label'     => __( 'Track Numbers', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'USL' ),
						'false' => __( 'False', 'USL' ),
					),
				),
			),
			'images'       => array(
				'label'     => __( 'Images', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'USL' ),
						'false' => __( 'False', 'USL' ),
					),
				),
			),
			'artists'      => array(
				'label'     => __( 'Artists', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'true'  => __( 'True', 'USL' ),
						'false' => __( 'False', 'USL' ),
					),
				),
			),
		),
	),
	// Audio
	array(
		'code'        => 'audio',
		'title'       => __( 'Audio', 'USL' ),
		'description' => __( 'The Audio feature allows you to embed audio files and play them back', 'USL' ),
		'atts'        => array(
			'src'      => array(
				'label'    => __( 'Source', 'USL' ),
				'required' => true,
			),
			'loop'     => array(
				'label'     => __( 'Loop', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'off' => __( 'Off', 'USL' ),
						'on'  => __( 'Onf', 'USL' ),
					),
				),
			),
			'autoplay' => array(
				'label'     => __( 'Autoplay', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'off' => __( 'Off', 'USL' ),
						'on'  => __( 'Onf', 'USL' ),
					),
				),
			),
			'preload'  => array(
				'label'     => __( 'Pre Load', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'metadata' => __( 'Metadata', 'USL' ),
						'none'     => __( 'None', 'USL' ),
						'auto'     => __( 'Auto', 'USL' ),
					),
				),
			),
		),
	),
	// Video
	array(
		'code'        => 'video',
		'title'       => __( 'Video', 'USL' ),
		'description' => __( 'The Video feature allows you to embed video files and play them back', 'USL' ),
		'atts'        => array(
			'src'      => array(
				'label'    => __( 'Source', 'USL' ),
				'required' => true,
			),
			'poster'   => array(
				'label' => __( 'Poster', 'USL' ),
			),
			'loop'     => array(
				'label'     => __( 'Loop', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'off' => __( 'Off', 'USL' ),
						'on'  => __( 'On', 'USL' ),
					),
				),
			),
			'autoplay' => array(
				'label'     => __( 'Auto Play', 'USL' ),
				'type'    => 'selectbox', 'propreties' => array(
					'options' => array(
						'off' => __( 'Off', 'USL' ),
						'on'  => __( 'On', 'USL' ),
					),
				),
			),
			'preload'  => array(
				'label'     => __( 'Pre Load', 'USL' ),
				'type'    => 'selectbox', 'properties' => array(
					'options' => array(
						'metadata' => __( 'Metadata', 'USL' ),
						'none'     => __( 'None', 'USL' ),
						'auto'     => __( 'Auto', 'USL' ),
					),
				),
			),
			'height'   => array(
				'label' => __( 'Height', 'USL' ),
			),
			'width'    => array(
				'label' => __( 'Width', 'USL' ),
			),
		),
	)
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'media';
	$shortcode['source']   = 'WordPress';
	usl_add_shortcode( $shortcode );
}