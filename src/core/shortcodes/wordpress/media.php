<?php

/**
 * Contains all USL packaged shortcodes within the Design category.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Shortcodes
 */
class USL_WordPressShortcodes {

	private $_shortcodes = array(
		// Embed
		array(
			'code'        => 'embed',
			'title'       => 'Embed',
			'description' => 'It\'s super easy to embed videos, images, tweets, audio, and other content into your WordPress site',
			'atts'        => array(
				'width'  => array(
					'slider' => array(
						'max' => 2000,
					),
				),
				'height' => array(
					'slider' => array(
						'max' => 2000,
					),
				),
			),
			'example'     => '[embed]http://www.youtube.com/watch?v=dQw4w9WgXcQ[/embed]',
			'wrapping'    => true,
		),
		// Caption
		array(
			'code'        => 'caption',
			'title'       => 'Caption',
			'description' => 'The Caption feature allows you to wrap captions around content. This is primarily used with individual images.',
			'atts'        => array(
				'id'    => array(),
				'class' => array(),
				'align' => array(
					'selectbox' => array(
						'options' => array(
							'alignnone' => 'None',
							'aligncenter' => 'Center',
							'alignright' => 'Right',
							'alignleft' => 'Left',
						),
					),
				),
				'width' => array(),
			),
			'wrapping'    => true,
			'example'     => '[caption id="attachment_6" align="alignright" width="300"]&lt;img src="my-image.jpg" /&gt; Awesome Image[/caption]',
		),
		// Gallery
		array(
			'code'        => 'gallery',
			'title'       => 'Gallery',
			'description' => 'The Gallery feature allows you to add one or more image galleries to your posts and pages',
			'atts'        => array(
				'ids'        => array(
					'required' => true,
				),
				'orderby'    => array(
					'selectbox' => array(
						'options' => array(
							'menu_order' => 'Menu Order',
							'title' => 'Title',
							'post_date' => 'Post Date',
							'rand' => 'Random',
							'ID' => 'ID',
						),
					),
				),
				'order'      => array(
					'selectbox' => array(
						'options' => array(
							'ASC' => 'Ascending',
							'DSC' => 'Descending',
						),
					),
				),
				'columns'    => array(),
				'size'       => array(),
				'include'    => array(
					'advanced' => true,
				),
				'exclude'    => array(
					'advanced' => true,
				),
				'itemtag'    => array(
					'advanced' => true,
				),
				'icontag'    => array(
					'advanced' => true,
				),
				'captiontag' => array(
					'advanced' => true,
				),
				'link'       => array(
					'advanced' => true,
				),
			),
			'example'     => '[gallery ids="729,732,731,720"]',
		),
		// Playlist
		array(
			'code'        => 'playlist',
			'title'       => 'Playlist',
			'description' => 'The playlist shortcode implements the functionality of displaying a collection of WordPress audio or video files in a post',
			'atts'        => array(
				'type'         => array(
					'selectbox' => array(
						'options' => array(
							'audio' => 'Audio',
							'video' => 'Video',
						),
					),
				),
				'orderby'      => array(
					'selectbox' => array(
						'options' => array(
							'menu_order' => 'Menu Order',
							'title' => 'Title',
							'post_date' => 'Post Date',
							'rand' => 'Random',
							'ID' => 'ID',
						),
					),
				),
				'order'        => array(
					'selectbox' => array(
						'options' => array(
							'ASC' => 'Ascending',
							'DSC' => 'Descending',
						),
					),
				),
				'ids'          => array(
					'required' => true,
				),
				'include'      => array(),
				'exclude'      => array(),
				'style'        => array(
					'selectbox' => array(
						'options' => array(
							'light' => 'Light',
							'dark' => 'Dark',
						),
					),
				),
				'tracklist'    => array(
					'selectbox' => array(
						'options' => array(
							'true' => 'True',
							'false' => 'False',
						),
					),
				),
				'tracknumbers' => array(
					'selectbox' => array(
						'options' => array(
							'true' => 'True',
							'false' => 'False',
						),
					),
				),
				'images'       => array(
					'selectbox' => array(
						'options' => array(
							'true' => 'True',
							'false' => 'False',
						),
					),
				),
				'artists'      => array(
					'selectbox' => array(
						'options' => array(
							'true' => 'True',
							'false' => 'False',
						),
					),
				),
			),
			'example'     => '[playlist type="video" ids="123,456,789" style="dark"]',
		),
		// Audio
		array(
			'code'        => 'audio',
			'title'       => 'Audio',
			'description' => 'The Audio feature allows you to embed audio files and play them back',
			'atts'        => array(
				'src'      => array(
					'required' => true,
				),
				'loop'     => array(
					'selectbox' => array(
						'options' => array(
							'off' => 'Off',
							'on' => 'Onf',
						),
					),
				),
				'autoplay' => array(
					'selectbox' => array(
						'options' => array(
							'off' => 'Off',
							'on' => 'Onf',
						),
					),
				),
				'preload'  => array(
					'selectbox' => array(
						'options' => array(
							'metadata' => 'Metadata',
							'none' => 'None',
							'auto' => 'Auto',
						),
					),
				),
			),
			'example'     => '[audio src="audio-source.mp3"]',
		),
		// Video
		array(
			'code'        => 'video',
			'title'       => 'Video',
			'description' => 'The Video feature allows you to embed video files and play them back',
			'atts'        => array(
				'src'      => array(
					'required' => true,
				),
				'poster'   => array(),
				'loop'     => array(
					'selectbox' => array(
						'options' => array(
							'off' => 'Off',
							'on' => 'On',
						),
					),
				),
				'autoplay' => array(
					'selectbox' => array(
						'options' => array(
							'off' => 'Off',
							'on' => 'Onf',
						),
					),
				),
				'preload'  => array(
					'selectbox' => array(
						'options' => array(
							'metadata' => 'Metadata',
							'none' => 'None',
							'auto' => 'Auto',
						),
					),
				),
				'height'   => array(),
				'width'    => array(),
			),
			'example'     => '[video src="video-source.mp4"]',
		)
	);

	function __construct() {

		foreach ( $this->_shortcodes as $shortcode ) {
			$shortcode['category'] = 'media';
			usl_add_shortcode( $shortcode );
		}
	}
}

new USL_WordPressShortcodes();