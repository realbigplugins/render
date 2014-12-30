<?php
/**
 * Contains all USL packaged shortcodes within the Site category.
 *
 * @since      USL 1.0.0
 *
 * @package    USL
 * @subpackage Shortcodes
 */

$_shortcodes = array(
	// Site Info
	array(
		'code'        => 'usl_site_info',
		'function'    => '_usl_sc_site_info',
		'title'       => __( 'Site Information', 'USL' ),
		'description' => __( 'Gets specified info about the current site.', 'USL' ),
		'tags'        => 'title tag line admin email url version language description name',
		'atts'        => array(
			'info' => array(
				'label'       => __( 'Info', 'USL' ),
				'description' => __( 'Which information to get about the site. Either choose an option or input your own.', 'USL' ),
				'type'        => 'selectbox',
				'properties'  => array(
					'allowCustomInput' => true,
					'default'          => 'title',
					'options'          => array(
						'name'       => __( 'Title', 'USL' ),
						'description'     => __( 'Tag Line', 'USL' ),
						'admin_email' => __( 'Admin Email', 'USL' ),
						'url'     => __( 'Site URL', 'USL' ),
						'version'     => __( 'WordPress Version', 'USL' ),
						'language'     => __( 'Language', 'USL' ),
					),
				),
			),
		),
		'render'      => true,
	),
);

foreach ( $_shortcodes as $shortcode ) {
	$shortcode['category'] = 'site';
	$shortcode['source']   = 'Ultimate Shortcodes Library';
	usl_add_shortcode( $shortcode );
}

/**
 * Gets info about the current site.
 *
 * @since  USL 1.0.0
 * @access Private
 *
 * @param null|array $atts The attributes sent to the shortcode.
 *
 * @return string The desired blog info.
 */
function _usl_sc_site_info( $atts ) {

	$atts = shortcode_atts( array(
		'info' => 'name'
	), $atts );

	// Escape atts
	usl_esc_atts( $atts );

	return get_bloginfo( $atts['info'] );
}