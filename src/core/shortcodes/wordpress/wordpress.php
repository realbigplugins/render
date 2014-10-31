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
				'width'  => array(),
				'height' => array(),
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
					'accepted_values' => array(
						'alignnone',
						'aligncenter',
						'alignright',
						'alignleft',
					),
				),
				'width' => array(),
			),
			'wrapping'    => true,
			'example'     => '[caption id="attachment_6" align="alignright" width="300"]<img src="http://localhost/wp-content/uploads/2010/07/800px-Great_Wave_off_Kanagawa2-300x205.jpg" alt="Kanagawa" title="The Great Wave" width="300" height="205" class="size-medium wp-image-6" /> The Great Wave[/caption]',
		),
		// Gallery
		array(
			'code'        => 'gallery',
			'title'       => 'Gallery',
			'description' => 'The Gallery feature allows you to add one or more image galleries to your posts and pages',
			'atts'        => array(
				'ids'         => array(
					'required' => true,
				),
				'orderby'    => array(
					'accepted_values' => array(
						'menu_order',
						'title',
						'post_date',
						'rand',
						'ID',
					),
				),
				'order'      => array(
					'accepted_values' => array(
						'ASC',
						'DSC',
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
					'accepted_values' => array(
						'audio',
						'video',
					),
				),
				'orderby'      => array(
					'accepted_values' => array(
						'menu_order',
						'title',
						'post_date',
						'rand',
						'ID',
					),
				),
				'order'        => array(
					'accepted_values' => array(
						'ASC',
						'DSC',
					),
				),
				'ids'          => array(
					'required' => true,
				),
				'include'      => array(),
				'exclude'      => array(),
				'style'        => array(
					'accepted_values' => array(
						'light',
						'dark',
					),
				),
				'tracklist'    => array(
					'accepted_values' => array(
						'true',
						'false',
					),
				),
				'tracknumbers' => array(
					'accepted_values' => array(
						'true',
						'false',
					),
				),
				'images'       => array(
					'accepted_values' => array(
						'true',
						'false',
					),
				),
				'artists'      => array(
					'accepted_values' => array(
						'true',
						'false',
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
					'off',
					'on',
				),
				'autoplay' => array(
					'accepted_values' => array(
						'off',
						'on',
					),
				),
				'preload'  => array(
					'accepted_values' => array(
						'metadata',
						'none',
						'auto',
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
					'accepted_values' => array(
						'off',
						'on',
					),
				),
				'autoplay' => array(
					'accepted_values' => array(
						'off',
						'on',
					),
				),
				'preload'  => array(
					'accepted_values' => array(
						'metadata',
						'none',
						'auto',
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
			$shortcode['category'] = 'WordPress';
			usl_add_shortcode( $shortcode );
		}
	}
}

new USL_WordPressShortcodes();