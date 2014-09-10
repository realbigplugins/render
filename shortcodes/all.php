<?php
$sources = array(
	'design',
	'time',
	'user',
	'site'
);
foreach ( $sources as $source ) {
	include_once( $source . '/shortcodes.php' );
}

class USL_All {

	public function shortcode() {
		global $shortcode_tags;
		global $usl_codes;
		if ( $shortcode_tags ) {
			foreach ( $shortcode_tags as $tag => $v ) {
				$check = strpos( $tag, 'usl_' );
				if ( $check === false ) {
					$title       = str_replace( '_', ' ', $tag );
					$usl_codes[] = array(
						'Code'        => $tag,
						'Title'       => $title,
						'Description' => '',
						'Atts'        => '',
						'Category'    => usl_core_shortcodes( $tag ),
						'Example'     => ''
					);
				} else {
				}
			}
		}
		$output = '<ul>';
		foreach( $usl_codes as $code ) {
			$output .= '<li>'.$code['Title'].'</li>';
		}
		$output .= '</ul>';
		return $output;
	}
}
$uslall = new USL_All();
add_usl_shortcode(
	'usl_all',
	array( $uslall, 'shortcode' ),
	'All Shortcodes',
	'Gets a list of all the shortcodes',
	'Site'
);