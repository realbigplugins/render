<?php
/**
 * Contains all Render packaged shortcodes within the Visibility category.
 *
 * @since      1.0.0
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
		/*
		 * Logic
		 *
		 * Conditionally displays posts based on the given logic.
		 *
		 * @since 1.0.0
		 *
		 * @att {selectbox} arg1       The first argument to compare.
		 * @att {selectbox} operator   The operator to compare with.
		 * @att {selectbox} arg2       The second argument to compare.
		 * @att {selectbox} param      The conditional parameter for use in comparing some arguments.
		 * @att {text}      wp_version The version of WordPress to check the current site against.
		 */
		array(
			'code'        => 'render_logic',
			'function'    => '_render_sc_logic',
			'title'       => __( 'Logic', 'Render' ),
			'description' => __( 'Allows for the use of conditional statements for showing content.', 'Render' ),
			'atts'        => array(
				'arg1'       => array(
					'label'      => __( 'Argument One', 'Render' ),
					'type'       => 'selectbox',
					'required'   => true,
					'properties' => array(
						'options' => array(
							'logged_in'  => __( 'If user is logged in', 'Render' ),
							'home'       => __( 'If current page is home page', 'Render' ),
							'comments'   => __( 'If comments allowed', 'Render' ),
							'single'     => __( 'If is single post', 'Render' ),
							'page'       => __( 'If is page', 'Render' ),
							'category'   => __( 'If is category page', 'Render' ),
							'tag'        => __( 'If is tag page', 'Render' ),
							'tax'        => __( 'If is taxonomy page', 'Render' ),
							'author'     => __( 'If is archive of specific author', 'Render' ),
							'archive'    => __( 'If current page is an archive page', 'Render' ),
							'search'     => __( 'If current page is a search results page', 'Render' ),
							'404'        => __( 'If current page is a 404', 'Render' ),
							'wp_version' => __( 'If this site\'s WordPress install version', 'Render' ),
						),
					),
				),
				'operator'   => array(
					'label'      => __( 'Operator', 'Render' ),
					'type'       => 'selectbox',
					'required'   => true,
					'default'    => 'equals',
					'properties' => array(
						'options' => array(
							'equals'                => __( 'equals', 'Render' ),
							'identical'             => __( 'is identical to', 'Render' ),
							'not_equal'        => __( 'does not equal', 'Render' ),
							'not_identical'      => __( 'is not identical to', 'Render' ),
							'less_than'             => __( 'is less than', 'Render' ),
							'greater_than'          => __( 'is greater than', 'Render' ),
							'less_than_or_equal'    => __( 'is less than or equal to', 'Render' ),
							'greater_than_or_equal' => __( 'is greater than or equal to', 'Render' ),
						),
					),
				),
				'arg2'       => array(
					'label'       => __( 'Argument Two', 'Render' ),
					'type'        => 'selectbox',
					'required'    => true,
					'default'     => 'true',
					'properties'  => array(
						'allowCustomInput' => true,
						'options'          => array(
							'true'  => __( 'true', 'Render' ),
							'false' => __( 'false', 'Render' ),
						),
					),
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'arg1' => array(
									'type'  => 'NOT IN',
									'value' => 'wp_version',
								),
							),
						),
					),
				),
				'param'      => array(
					'label'       => __( 'Parameter (optional)', 'Render' ),
					'description' => __( 'Used in some conditions to further specify the condition.', 'Render' ),
					'type'        => 'selectbox',
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'arg1' => array(
									'type'  => 'IN',
									'value' => 'single,page,category,tag,tax,author',
								),
							),
						),
						'populate'   => array(
							'atts'     => array(
								'arg1',
							),
							'callback' => '_render_sc_logic_param_populate',
						),
					),
				),
				// Currently only in use for wp_version. If used for more, make sure all properties still applicable
				'wp_version' => array(
					'label'       => __( 'Version', 'Render' ),
					'description' => __( 'Enter the version number in the format "#.#.#".', 'Render' ),
					'required'    => true,
					'conditional' => array(
						'visibility' => array(
							'atts' => array(
								'arg1' => array(
									'type'  => '==',
									'value' => 'wp_version',
								),
							),
						),
					),
				),
			),
			'render'      => true,
			'wrapping'    => true,
		),
		/*
		 * Hide for times
		 *
		 * Only shows content at desired time-frames.
		 *
		 * @since 1.0.0
		 *
		 * @att {toggle}    visibility Whether to hide or show the content when the time meets the supplied time-frames.
		 * @att {repeater}  times      The time-frames to compare against.
		 *      @field {slider} time A time-frame to compare against.
		 *
		 * @att {selectbox} timezone   The timezone to use.
		 */
		array(
			'code'        => 'render_hide_for_times',
			'function'    => '_render_sc_hide_for_times',
			'title'       => __( 'Hide for Times', 'Render' ),
			'description' => __( 'Allows content to be visible only during set times', 'Render' ),
			'atts'        => array(
				'visibility' => array(
					'label'       => __( 'Visibility', 'Render' ),
					'description' => __( 'Hide or show for the below times.', 'Render' ),
					'type'        => 'toggle',
					'properties'  => array(
						'values' => array(
							'hide' => __( 'Hide', 'Render' ),
							'show' => __( 'Show', 'Render' ),
						),
					),
				),
				'times'      => array(
					'label'      => __( 'Times', 'Render' ),
					'type'       => 'repeater',
					'properties' => array(
						'startWith' => 1,
						'fields'    => array(
							'time' => array(
								'label'        => __( 'Hide / show between...', 'Render' ),
								'type'         => 'slider',
								'callback'     => 'render_sc_time_slider',
								'initCallback' => 'timeSliderInit',
								'properties'   => array(
									'range' => true,
								),
							),
						),
					),
				),
				'timezone'   => render_sc_attr_template( 'timezone' ),
			),
			'wrapping'    => true,
			'render'      => true,
		),
		/*
		 * Hide for users
		 *
		 * Hides or shows content for specific users.
		 *
		 * @since 1.0.0
		 *
		 * @att {toggle}    visibility Whether to hide or show for the specified users.
		 * @att {selectbox} users      Which users to compare against.
		 */
		array(
			'code'        => 'render_hide_for_users',
			'function'    => '_render_sc_hide_for_users',
			'title'       => __( 'Hide for Users', 'Render' ),
			'description' => __( 'Allows content to be visible only for specific users', 'Render' ),
			'atts'        => array(
				'visibility' => array(
					'label'       => __( 'Visibility', 'Render' ),
					'description' => __( 'Hide or show for below times.', 'Render' ),
					'type'        => 'toggle',
					'properties'  => array(
						'values' => array(
							'hide' => __( 'Hide', 'Render' ),
							'show' => __( 'Show', 'Render' ),
						),
					),
				),
				'users'      => array(
					'label'      => __( 'Users', 'Render' ),
					'type'       => 'selectbox',
					'properties' => array(
						'placeholder' => __( 'Select one or more users', 'Render' ),
						'multi'       => true,
						'callback'    => array(
							'function' => 'render_sc_user_list',
						),
					),
				),
			),
			'wrapping'    => true,
			'render'      => true,
		)
	) as $shortcode
) {

	$shortcode['category'] = 'visibility';
	$shortcode['source']   = 'Render';

	render_add_shortcode( $shortcode );
	render_add_shortcode_category( array(
		'id'    => 'visibility',
		'label' => __( 'Visibility', 'Render' ),
		'icon'  => 'dashicons-visibility',
	) );
}

/**
 * Returns the content if the condition is met, otherwise, returns nothing.
 *
 * @since  1.0.0
 * @access private
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 * @return bool True if statement succeeds, false otherwise. Doy!
 */
function _render_sc_logic( $atts = array(), $content = '' ) {

	$atts = shortcode_atts( array(
		'arg1'       => 'logged_in',
		'arg2'       => 'true',
		'operator'   => 'equals',
		'param'      => '',
		'wp_version' => '',
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	// Correctly set arg1 to appropriate function
	$argument_1 = $atts['arg1'];
	switch ( $argument_1 ) {
		case 'logged_in':
			$argument_1 = is_user_logged_in();
			break;
		case 'home':
			$argument_1 = is_home();
			break;
		case 'single':
			$argument_1 = is_single( $atts['param'] );
			break;
		case 'page':
			$argument_1 = is_page( $atts['param'] );
			break;
		case 'category':
			$argument_1 = is_category( $atts['param'] );
			break;
		case 'tag':
			$argument_1 = is_tag( $atts['param'] );
			break;
		case 'tax':
			$argument_1 = is_tax( $atts['param'] );
			break;
		case 'author':
			$argument_1 = is_author( $atts['param'] );
			break;
		case 'archive':
			$argument_1 = is_archive();
			break;
		case 'search':
			$argument_1 = is_search();
			break;
		case '404':
			$argument_1 = is_404();
			break;
		case 'comments':
			$argument_1 = comments_open();
			break;
		case 'wp_version':

			/**
			 * If we're doing the version, we need to call on a special function for comparing.
			 *
			 * @since 1.0.0
			 */
			add_filter( 'render_sc_logic_operator', function ( $output, $argument_1, $argument_2, $operator, $content, $atts ) {


				if ( $argument_1 == 'wp_version' ) {

					$passing = false;

					$argument_1 = get_bloginfo( 'version' );
					$argument_2 = $atts['wp_version'];

					switch ( $operator ) {

						case 'equals':
						case 'identical':

						if ( version_compare( $argument_1, $argument_2, '=' ) ) {
								$passing = true;
							};
							break;

						case 'not_equal':
						case 'not_identical':

							if ( version_compare( $argument_1, $argument_2, '!=' ) ) {
								$passing = true;
							};
							break;

						case 'less_than':

							if ( version_compare( $argument_1, $argument_2, '<' ) ) {
								$passing = true;
							};
							break;

						case 'less_than_or_equal':

							if ( version_compare( $argument_1, $argument_2, '<=' ) ) {
								$passing = true;
							};
							break;

						case 'greater_than':

							if ( version_compare( $argument_1, $argument_2, '>' ) ) {
								$passing = true;
							};
							break;

						case 'greater_than_or_equal':

							if ( version_compare( $argument_1, $argument_2, '>=' ) ) {
								$passing = true;
							};
							break;
					}

					return $passing ? $content : '';
				} else {
					return $content;
				}
			}, 10, 6 );

			break;
		default:
			$argument_1 = true;
	}

	$argument_1 = apply_filters( 'render_sc_logic_arg1', $argument_1, $atts );

	// Correctly set arg2 to boolean
	switch ( $atts['arg2'] ) {
		case 'false':
			$argument_2 = false;
			break;
		case 'true':
			$argument_2 = true;
			break;
		default:
			$argument_2 = $atts['arg2'];
	}

	$argument_2 = apply_filters( 'render_sc_logic_arg2', $argument_2, $atts );

	// Checks for operator
	$output   = '';
	$operator = $atts['operator'];
	switch ( $operator ) {
		case 'identical':
			if ( $argument_1 === $argument_2 ) {
				$output = $content;
			}
			break;
		case 'equals':
			if ( $argument_1 == $argument_2 ) {
				$output = $content;
			}
			break;
		case 'not_equal':
			if ( $argument_1 != $argument_2 ) {
				$output = $content;
			}
			break;
		case 'not_identical':
			if ( $argument_1 !== $argument_2 ) {
				$output = $content;
			}
			break;
		case 'less_than':
			if ( $argument_1 < $argument_2 ) {
				$output = $content;
			}
			break;
		case 'greater_than':
			if ( $argument_1 > $argument_2 ) {
				$output = $content;
			}
			break;
		case 'greater_than_or_equal':
			if ( $argument_1 >= $argument_2 ) {
				$output = $content;
			}
			break;
		case 'less_than_or_equal':
			if ( $argument_1 <= $argument_2 ) {
				$output = $content;
			}
			break;
		default:
			$output = $content;
	}

	$output = apply_filters( 'render_sc_logic_operator', $output, $argument_1, $argument_2, $operator, $content, $atts );

	return do_shortcode( $output );
}

/**
 * The tinymce mirror for the Logic shortcode.
 *
 * @since  1.0.0
 * @access private
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 * @return string The appropriately wrapped content.
 */
function _render_sc_logic_tinymce( $atts = array(), $content = '' ) {

	$output = _render_sc_logic( $atts, $content );

	return render_tinymce_visibility_wrap( $content, empty( $output ) ? 'hidden' : 'visible' );
}

/**
 * Returns the content if the specified time properties are not met.
 *
 * @since  1.0.0
 * @access private
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 * @return string The content, if it's returned
 */
function _render_sc_hide_for_times( $atts = array(), $content = '' ) {

	$atts = wp_parse_args( $atts, array(
		'visibility' => 'hide',
		'timezone'   => get_option( 'timezone_string', 'UTC' ),
	) );

	$atts = render_esc_atts( $atts );

	$time_blocks = render_associative_atts( $atts, 'times' );

	$orig_timezone = date_default_timezone_get();
	date_default_timezone_set( $atts['timezone'] );

	// See if the time is in the currently hidden blocks
	$hidden = $atts['visibility'] === 'hide' ? false : true;
	foreach ( $time_blocks as $times ) {

		$times   = explode( '-', $times['time'] );
		$current = round( ( time() - strtotime( 'today' ) ) / 60 );

		if ( $current > intval( $times[0] ) && $current < intval( $times[1] ) ) {
			$hidden = $atts['visibility'] === 'hide' ? true : false;
		}
	}

	date_default_timezone_set( $orig_timezone );

	$content = $hidden ? '' : $content;

	return $content;
}

/**
 * The tinymce mirror for the Hide for Times shortcode.
 *
 * @since  1.0.0
 * @access private
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 * @return string The appropriately wrapped content.
 */
function _render_sc_hide_for_times_tinymce( $atts = array(), $content = '' ) {

	$output = _render_sc_hide_for_times( $atts, $content );

	return render_tinymce_visibility_wrap( $content, empty( $output ) ? 'hidden' : 'visible' );
}

/**
 * Returns the content if the specified users are or are not logged in.
 *
 * @since  1.0.0
 * @access private
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 * @return string The content, if it's returned
 */
function _render_sc_hide_for_users( $atts = array(), $content = '' ) {

	$atts = shortcode_atts( array(
		'users'      => false,
		'visibility' => 'hide',
	), $atts );

	$atts = render_esc_atts( $atts );

	$output = '';

	if ( empty( $atts['users'] ) ) {
		return 'ERROR: No users entered!';
	}

	$users = explode( ',', $atts['users'] );

	if ( $atts['visibility'] === 'show' ) {
		if ( in_array( get_current_user_id(), $users ) ) {
			$output = $content;
		}
	} else {
		if ( ! in_array( get_current_user_id(), $users ) ) {
			$output = $content;
		}
	}

	return $output;
}

/**
 * The tinymce mirror for the Hide for Users shortcode.
 *
 * @since  1.0.0
 * @access private
 * @param array  $atts    The attributes sent to the shortcode.
 * @param string $content The content inside the shortcode.
 * @return string The appropriately wrapped content.
 */
function _render_sc_hide_for_users_tinymce( $atts = array(), $content = '' ) {

	$output = _render_sc_hide_for_users( $atts, $content );

	return render_tinymce_visibility_wrap( $content, empty( $output ) ? 'hidden' : 'visible' );
}

/**
 * Wraps content appropriately based on its existence, for use within the TinyMCE.
 *
 * @since 1.0.0
 * @param string $content    The content output.
 * @param string $visibility Whether to hide or show content. Accepts 'hidden' or 'visible'.
 * @return string The appropriately wrapped content.
 */
function render_tinymce_visibility_wrap( $content = '', $visibility = 'visible' ) {

	$tag = preg_match( '/' . render_block_regex() . '/', $content ) ? 'div' : 'span';

	return "<$tag class='render-content-$visibility'>" . do_shortcode( $content ) . "<span class='render-visibility-icon dashicons dashicons-" . ( $visibility == 'visible' ? 'visibility' : 'no' ) . "'></span></$tag>";
}

/**
 * Provides a selectbox attribute with options of all timezones.
 *
 * @since 1.0.0
 * @return array Timezones.
 */
function render_sc_timezone_dropdown() {

	return $timeszones = array(
		'Pacific/Midway'       => "(GMT-11:00) Midway Island",
		'US/Samoa'             => "(GMT-11:00) Samoa",
		'US/Hawaii'            => "(GMT-10:00) Hawaii",
		'US/Alaska'            => "(GMT-09:00) Alaska",
		'US/Pacific'           => "(GMT-08:00) Pacific Time (US &amp; Canada)",
		'America/Tijuana'      => "(GMT-08:00) Tijuana",
		'US/Arizona'           => "(GMT-07:00) Arizona",
		'US/Mountain'          => "(GMT-07:00) Mountain Time (US &amp; Canada)",
		'America/Chihuahua'    => "(GMT-07:00) Chihuahua",
		'America/Mazatlan'     => "(GMT-07:00) Mazatlan",
		'America/Mexico_City'  => "(GMT-06:00) Mexico City",
		'America/Monterrey'    => "(GMT-06:00) Monterrey",
		'Canada/Saskatchewan'  => "(GMT-06:00) Saskatchewan",
		'US/Central'           => "(GMT-06:00) Central Time (US &amp; Canada)",
		'US/Eastern'           => "(GMT-05:00) Eastern Time (US &amp; Canada)",
		'US/East-Indiana'      => "(GMT-05:00) Indiana (East)",
		'America/Bogota'       => "(GMT-05:00) Bogota",
		'America/Lima'         => "(GMT-05:00) Lima",
		'America/Caracas'      => "(GMT-04:30) Caracas",
		'Canada/Atlantic'      => "(GMT-04:00) Atlantic Time (Canada)",
		'America/La_Paz'       => "(GMT-04:00) La Paz",
		'America/Santiago'     => "(GMT-04:00) Santiago",
		'Canada/Newfoundland'  => "(GMT-03:30) Newfoundland",
		'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
		'Greenland'            => "(GMT-03:00) Greenland",
		'Atlantic/Stanley'     => "(GMT-02:00) Stanley",
		'Atlantic/Azores'      => "(GMT-01:00) Azores",
		'Atlantic/Cape_Verde'  => "(GMT-01:00) Cape Verde Is.",
		'Africa/Casablanca'    => "(GMT) Casablanca",
		'Europe/Dublin'        => "(GMT) Dublin",
		'Europe/Lisbon'        => "(GMT) Lisbon",
		'Europe/London'        => "(GMT) London",
		'Africa/Monrovia'      => "(GMT) Monrovia",
		'Europe/Amsterdam'     => "(GMT+01:00) Amsterdam",
		'Europe/Belgrade'      => "(GMT+01:00) Belgrade",
		'Europe/Berlin'        => "(GMT+01:00) Berlin",
		'Europe/Bratislava'    => "(GMT+01:00) Bratislava",
		'Europe/Brussels'      => "(GMT+01:00) Brussels",
		'Europe/Budapest'      => "(GMT+01:00) Budapest",
		'Europe/Copenhagen'    => "(GMT+01:00) Copenhagen",
		'Europe/Ljubljana'     => "(GMT+01:00) Ljubljana",
		'Europe/Madrid'        => "(GMT+01:00) Madrid",
		'Europe/Paris'         => "(GMT+01:00) Paris",
		'Europe/Prague'        => "(GMT+01:00) Prague",
		'Europe/Rome'          => "(GMT+01:00) Rome",
		'Europe/Sarajevo'      => "(GMT+01:00) Sarajevo",
		'Europe/Skopje'        => "(GMT+01:00) Skopje",
		'Europe/Stockholm'     => "(GMT+01:00) Stockholm",
		'Europe/Vienna'        => "(GMT+01:00) Vienna",
		'Europe/Warsaw'        => "(GMT+01:00) Warsaw",
		'Europe/Zagreb'        => "(GMT+01:00) Zagreb",
		'Europe/Athens'        => "(GMT+02:00) Athens",
		'Europe/Bucharest'     => "(GMT+02:00) Bucharest",
		'Africa/Cairo'         => "(GMT+02:00) Cairo",
		'Africa/Harare'        => "(GMT+02:00) Harare",
		'Europe/Helsinki'      => "(GMT+02:00) Helsinki",
		'Europe/Istanbul'      => "(GMT+02:00) Istanbul",
		'Asia/Jerusalem'       => "(GMT+02:00) Jerusalem",
		'Europe/Kiev'          => "(GMT+02:00) Kyiv",
		'Europe/Minsk'         => "(GMT+02:00) Minsk",
		'Europe/Riga'          => "(GMT+02:00) Riga",
		'Europe/Sofia'         => "(GMT+02:00) Sofia",
		'Europe/Tallinn'       => "(GMT+02:00) Tallinn",
		'Europe/Vilnius'       => "(GMT+02:00) Vilnius",
		'Asia/Baghdad'         => "(GMT+03:00) Baghdad",
		'Asia/Kuwait'          => "(GMT+03:00) Kuwait",
		'Africa/Nairobi'       => "(GMT+03:00) Nairobi",
		'Asia/Riyadh'          => "(GMT+03:00) Riyadh",
		'Asia/Tehran'          => "(GMT+03:30) Tehran",
		'Europe/Moscow'        => "(GMT+04:00) Moscow",
		'Asia/Baku'            => "(GMT+04:00) Baku",
		'Europe/Volgograd'     => "(GMT+04:00) Volgograd",
		'Asia/Muscat'          => "(GMT+04:00) Muscat",
		'Asia/Tbilisi'         => "(GMT+04:00) Tbilisi",
		'Asia/Yerevan'         => "(GMT+04:00) Yerevan",
		'Asia/Kabul'           => "(GMT+04:30) Kabul",
		'Asia/Karachi'         => "(GMT+05:00) Karachi",
		'Asia/Tashkent'        => "(GMT+05:00) Tashkent",
		'Asia/Kolkata'         => "(GMT+05:30) Kolkata",
		'Asia/Kathmandu'       => "(GMT+05:45) Kathmandu",
		'Asia/Yekaterinburg'   => "(GMT+06:00) Ekaterinburg",
		'Asia/Almaty'          => "(GMT+06:00) Almaty",
		'Asia/Dhaka'           => "(GMT+06:00) Dhaka",
		'Asia/Novosibirsk'     => "(GMT+07:00) Novosibirsk",
		'Asia/Bangkok'         => "(GMT+07:00) Bangkok",
		'Asia/Jakarta'         => "(GMT+07:00) Jakarta",
		'Asia/Krasnoyarsk'     => "(GMT+08:00) Krasnoyarsk",
		'Asia/Chongqing'       => "(GMT+08:00) Chongqing",
		'Asia/Hong_Kong'       => "(GMT+08:00) Hong Kong",
		'Asia/Kuala_Lumpur'    => "(GMT+08:00) Kuala Lumpur",
		'Australia/Perth'      => "(GMT+08:00) Perth",
		'Asia/Singapore'       => "(GMT+08:00) Singapore",
		'Asia/Taipei'          => "(GMT+08:00) Taipei",
		'Asia/Ulaanbaatar'     => "(GMT+08:00) Ulaan Bataar",
		'Asia/Urumqi'          => "(GMT+08:00) Urumqi",
		'Asia/Irkutsk'         => "(GMT+09:00) Irkutsk",
		'Asia/Seoul'           => "(GMT+09:00) Seoul",
		'Asia/Tokyo'           => "(GMT+09:00) Tokyo",
		'Australia/Adelaide'   => "(GMT+09:30) Adelaide",
		'Australia/Darwin'     => "(GMT+09:30) Darwin",
		'Asia/Yakutsk'         => "(GMT+10:00) Yakutsk",
		'Australia/Brisbane'   => "(GMT+10:00) Brisbane",
		'Australia/Canberra'   => "(GMT+10:00) Canberra",
		'Pacific/Guam'         => "(GMT+10:00) Guam",
		'Australia/Hobart'     => "(GMT+10:00) Hobart",
		'Australia/Melbourne'  => "(GMT+10:00) Melbourne",
		'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
		'Australia/Sydney'     => "(GMT+10:00) Sydney",
		'Asia/Vladivostok'     => "(GMT+11:00) Vladivostok",
		'Asia/Magadan'         => "(GMT+12:00) Magadan",
		'Pacific/Auckland'     => "(GMT+12:00) Auckland",
		'Pacific/Fiji'         => "(GMT+12:00) Fiji",
	);
}

/**
 * Outputs the field HTML of a custom time-formatted slider attribute.
 *
 * @since 1.0.0
 * @param string $att_id     The attribute ID.
 * @param array  $att        Properties of the attribute.
 * @param array  $properties Properties of the attribute field type.
 */
function render_sc_time_slider( $att_id, $att, $properties ) {

	// Establish defaults
	$properties = wp_parse_args( $properties, array(
		'values' => $att['default'] !== false ? $att['default'] : '480-840',
		'step'   => 15,
	) );

	// Custom slide callback
	$properties['slide'] = 'timeSlider';

	// Non-editables
	$properties['min'] = 0;
	$properties['max'] = 1440;

	// Range
	if ( isset( $properties['range'] ) ) {
		$properties['range'] = $properties['range'] === true ? 'true' : '';
	}

	// Prepare data for the slider
	$data = '';
	foreach ( $properties as $data_name => $data_value ) {
		$data .= " data-$data_name='$data_value'";
	}

	$values = explode( '-', $properties['values'] );
	?>
	<div class="render-modal-att-extend-slider-time">
		<input type="hidden" class="render-modal-att-slider-value render-modal-att-input"
		       value="<?php echo $properties['values']; ?>"
		       name="<?php echo $att_id; ?>"/>

		<div class="render-modal-att-slider-range-text">
			<span class="render-modal-att-slider-range-text-value1"><?php echo $values[0]; ?></span>
			&nbsp;-&nbsp;
			<span class="render-modal-att-slider-range-text-value2"><?php echo $values[1]; ?></span>
		</div>

		<div class="render-modal-att-slider" <?php echo $data; ?>></div>
	</div>
<?php
}

/**
 * Populates the parameter selectbox conditionally for the logic shortcode.
 *
 * @since  {{VERSION}}
 * @access private
 * @param array $atts The dependent attributes.
 * @return array The selectbox options.
 */
function _render_sc_logic_param_populate( $atts ) {

	$response = array(
		'options'         => array(),
		'no_options_text' => false,
		'description'     => false,
	);

	switch ( $atts['arg1'] ) {

		case 'single':

			$response['options']         = render_sc_post_list( array(
				'post_type' => 'post',
			) );
			$response['no_options_text'] = __( 'No posts available.', 'Render' );
			$response['description']     = __( 'Leave blank to just check if this is any post.', 'Render' );
			break;

		case 'page':

			$response['options']         = render_sc_post_list( array(
				'post_type' => 'page',
			) );
			$response['no_options_text'] = __( 'No pages available.', 'Render' );
			$response['description']     = __( 'Leave blank to just check if this is any page.', 'Render' );
			break;

		case 'category':

			$response['options']         = render_sc_term_list( array(
				'taxonomies' => array( 'category' ),
			) );
			$response['no_options_text'] = __( 'No categories available.', 'Render' );
			$response['description']     = __( 'Leave blank to just check if this is any category page.', 'Render' );
			break;

		case 'tag':

			$response['options']         = render_sc_term_list( array(
				'taxonomies' => array( 'post_tag' ),
			) );
			$response['no_options_text'] = __( 'No tags available.', 'Render' );
			$response['description']     = __( 'Leave blank to just check if this is any tag page.', 'Render' );
			break;

		case 'tax':

			$response['options']         = render_sc_term_list();
			$response['no_options_text'] = __( 'No taxonomies available.', 'Render' );
			$response['description']     = __( 'Leave blank to just check if this is any taxonomy page.', 'Render' );
			break;

		case 'author':

			$response['options']         = render_sc_user_list( 'edit_posts' );
			$response['no_options_text'] = __( 'No authors available.', 'Render' );
			$response['description']     = __( 'Leave blank to just check if this is any author archive.', 'Render' );
			break;
	}

	// Convert the options to HTML
	$response['options'] = ! empty( $response['options'] ) ? render_build_options_html( $response['options'] ) : '';

	return $response;
}