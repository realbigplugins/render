<?php
/**
 * Contains all Render packaged shortcodes within the Visibility category.
 *
 * @since      Render 1.0.0
 *
 * @package    Render
 * @subpackage Shortcodes
 */

// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Loops through each shortcode and adds it to Render
foreach ( array(
	// Logic
	// TODO Test and fix up
	array(
		'code'        => 'render_logic',
		'function'    => '_render_sc_logic',
		'title'       => __( 'Logic', 'Render' ),
		'description' => __( 'Allows for the use of conditional statements for showing content.', 'Render' ),
		'atts'        => array(
			'arg1'     => array(
				'label'      => __( 'Argument One', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'allowCustomInput' => true,
					'options'          => array(
						'logged_in' => __( 'User is logged in', 'Render' ),
						'home'      => __( 'Current page is home page', 'Render' ),
						'comments'  => __( 'Comments allowed', 'Render' ),
						'single'    => __( 'Is single post', 'Render' ),
						'page'      => __( 'Is page', 'Render' ),
						'category'  => __( 'Is category page', 'Render' ),
						'tag'       => __( 'Is tag page', 'Render' ),
						'tax'       => __( 'Is taxonomy page', 'Render' ),
						'author'    => __( 'Is archive of specific author', 'Render' ),
						'archive'   => __( 'Current page is an archive page', 'Render' ),
						'search'    => __( 'Current page is a search results page', 'Render' ),
						'404'       => __( 'Current page is a 404', 'Render' ),
					),
				),
			),
			'arg2'     => array(
				'label'      => __( 'Argument Two', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'allowCustomInput' => true,
					'placeholder'      => __( 'True', 'Render' ),
					'options'          => array(
						'true'  => __( 'True', 'Render' ),
						'false' => __( 'False', 'Render' ),
					),
				),
			),
			'operator' => array(
				'label'      => __( 'Operator', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'allowCustomInput' => true,
					'placeholder'      => __( 'Equals', 'Render' ),
					'options'          => array(
						'===' => __( 'Identical', 'Render' ),
						'=='  => __( 'Equals', 'Render' ),
						'!='  => __( 'Does not equal', 'Render' ),
						'!==' => __( 'Not identical', 'Render' ),
						'<'   => __( 'Less than', 'Render' ),
						'>'   => __( 'Greater than', 'Render' ),
						'<='  => __( 'Less than or equal to', 'Render' ),
						'>='  => __( 'Greater than or equal', 'Render' ),
					),
				),
			),
			'param'    => array(
				'label'       => __( 'Parameter (optional)', 'Render' ),
				'description' => __( 'Used in some conditions to further specify the condition.', 'Render' ),
			),
		),
		'render'      => array(
			'noStyle' => true,
		),
		'wrapping'    => true,
	),
	// Hide for times
	// TODO Test and fix up
	array(
		'code'        => 'render_hide_for_times',
		'function'    => '_render_sc_hide_for_times',
		'title'       => __( 'Hide for Times', 'Render' ),
		'description' => __( 'Allows content to be visible only during set times', 'Render' ),
		'atts'        => array(
			'visibility' => array(
				'label'      => __( 'Visibility', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Hide for these times', 'Render' ),
					'options'     => array(
						'hide' => __( 'Hide for these times', 'Render' ),
						'show' => __( 'Show for these times', 'Render' ),
					),
				),
			),
			'timezone' => array(
				'label' => __( 'Timezone', 'Render' ),
				'type' => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Defaults to timezone set in Settings -> General', 'Render' ),
					'callback' => array(
						'function' => 'render_sc_timezone_dropdown',
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
							'callback'     => array(
								'function' => 'render_sc_time_slider',
							),
							'initCallback' => 'timeSliderInit',
							'properties'   => array(
								'range' => true,
							),
						),
					),
				),
			),
		),
		'wrapping'    => true,
		'render'      => array(),
	),
	// Hide for users
	// TODO Test and fix up
	array(
		'code'        => 'render_hide_for_users',
		'function'    => '_render_sc_hide_for_users',
		'title'       => __( 'Hide for Users', 'Render' ),
		'description' => __( 'Allows content to be visible only for specific users', 'Render' ),
		'atts'        => array(
			'users'      => array(
				'label'      => __( 'Users', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Select one or more users', 'Render' ),
					'multi'       => true,
					'callback'    => array(
						'function' => '_render_user_dropdown',
					),
				),
			),
			'visibility' => array(
				'label'      => __( 'Visibility', 'Render' ),
				'type'       => 'selectbox',
				'properties' => array(
					'placeholder' => __( 'Hide for these users', 'Render' ),
					'options'     => array(
						'hide' => __( 'Hide for these users', 'Render' ),
						'show' => __( 'Show for these users', 'Render' ),
					),
				),
			),
		),
		'wrapping'    => true,
		'render'      => array(
			'noStyle' => true,
		),
	)
) as $shortcode ) {

	$shortcode['category'] = 'visibility';
	$shortcode['source']   = 'Render';

	// Adds shortcode to Render
	add_filter( 'render_add_shortcodes', function( $shortcodes ) use ( $shortcode ) {
		$shortcodes[] = $shortcode;
		return $shortcodes;
	});

	// Add shortcode category
	add_filter( 'render_modal_categories', function( $categories ) {
		$categories['visibility'] = array(
			'label' => __( 'Visibility', 'Render' ),
			'icon'  => 'dashicons-visibility',
		);
		return $categories;
	});
}

/**
 * Returns the content if the condition is met, otherwise, returns nothing.
 *
 * @since  Render 1.0.0
 * @access Private
 *
 * @param null|array  $atts    The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return bool True if statement succeeds, false otherwise. Doy!
 */
function _render_sc_logic( $atts = array(), $content = null ) {

	$atts = shortcode_atts( array(
		'arg1'     => 'logged_in',
		'arg2'     => 'true',
		'operator' => '==',
		'param'    => '',
	), $atts );

	// Escape atts
	render_esc_atts( $atts );

	$param = $atts['param'];

	// Correctly set arg1 to appropriate function
	$arg1 = $atts['arg1'];
	switch ( $arg1 ) {
		case 'logged_in':
			$arg1 = is_user_logged_in();
			break;
		case 'home':
			$arg1 = is_home();
			break;
		case 'single':
			$arg1 = is_single( $param );
			break;
		case 'page':
			$arg1 = is_page( $param );
			break;
		case 'category':
			$arg1 = is_category( $param );
			break;
		case 'tag':
			$arg1 = is_tag( $param );
			break;
		case 'tax':
			$arg1 = is_tax( $param );
			break;
		case 'author':
			$arg1 = is_author( $param );
			break;
		case 'archive':
			$arg1 = is_archive();
			break;
		case 'search':
			$arg1 = is_search();
			break;
		case '404':
			$arg1 = is_404();
			break;
		case 'comments':
			$arg1 = comments_open();
			break;
		default:
			$arg1 = true;
	}

	$arg1 = apply_filters( 'render_sc_logic_arg1', $arg1, $atts );

	// Correctly set arg2 to boolean
	$arg2 = $atts['arg2'] === 'false' ? false : true;

	// Checks for operator
	$output   = '';
	$operator = $atts['operator'];
	switch ( $operator ) {
		case '===':
			if ( $arg1 === $arg2 ) {
				$output = $content;
			}
			break;
		case '==':
			if ( $arg1 == $arg2 ) {
				$output = $content;
			}
			break;
		case '!=':
			if ( $arg1 != $arg2 ) {
				$output = $content;
			}
			break;
		case '!==':
			if ( $arg1 !== $arg2 ) {
				$output = $content;
			}
			break;
		case '<':
			if ( $arg1 < $arg2 ) {
				$output = $content;
			}
			break;
		case '>':
			if ( $arg1 > $arg2 ) {
				$output = $content;
			}
			break;
		case '>=':
			if ( $arg1 >= $arg2 ) {
				$output = $content;
			}
			break;
		case '<=':
			if ( $arg1 <= $arg2 ) {
				$output = $content;
			}
			break;
		default:
			$output = $content;
	}

	// Differ for tinymce output
	if ( defined( 'Render_SHORTCODE_RENDERING' ) && Render_SHORTCODE_RENDERING ) {
		return '<div class="' . ( empty( $output ) ? 'hidden' : 'visible' ) . '">' . $content . '</div>';
	}

	return $output;
}

/**
 * Returns the content if the specified time properties are not met.
 *
 * @since  Render 1.0.0
 * @access Private
 *
 * @param null|array  $atts    The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return null|string The content, if it's returned
 */
function _render_sc_hide_for_times( $atts = array(), $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'visibility' => 'hide',
		'timezone' => get_option( 'timezone_string', 'UTC' ),
	) );

	$atts = render_esc_atts( $atts );

	$time_blocks = render_associative_atts( $atts, 'times' );

	$orig_timezone = date_default_timezone_get();
	date_default_timezone_set( $atts['timezone'] );

	// See if the time is in the currently hidden blocks
	$hidden = $atts['visibility'] === 'hide' ? false : true;
	foreach ( $time_blocks as $times ) {

		$times = explode( '-', $times['time'] );
		$current = round( ( time() - strtotime('today') ) / 60 );

		if ( $current > intval ( $times[0] ) && $current < intval ( $times[1] ) ) {
			$hidden = $atts['visibility'] === 'hide' ? true : false;
		}
	}

	date_default_timezone_set( $orig_timezone );

	// Differ for tinymce output
	if ( defined( 'Render_SHORTCODE_RENDERING' ) && Render_SHORTCODE_RENDERING ) {
		return '<div class="' . ( $hidden ? 'render-content-hidden' : 'render-content-visible' ) . '">' . $content . '</div>';
	}

	$content = $hidden ? '' : $content;
	return $content;
}

/**
 * Returns the content if the specified users are or are not logged in.
 *
 * @since  Render 1.0.0
 * @access Private
 *
 * @param null|array  $atts    The attributes sent to the shortcode.
 * @param null|string $content The content inside the shortcode.
 *
 * @return null|string The content, if it's returned
 */
function _render_sc_hide_for_users( $atts = array(), $content = null ) {

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

	// Differ for tinymce output
	if ( defined( 'Render_SHORTCODE_RENDERING' ) && Render_SHORTCODE_RENDERING ) {
		return '<div class="' . ( empty( $output ) ? 'hidden' : 'visible' ) . '">' . $content . '</div>';
	}

	return $output;
}

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

function render_sc_time_slider( $att_id, $att, $properties ) {

	// Establish defaults
	$defaults   = array(
		'values' => isset( $att['default'] ) ? $att['default'] : '480-840',
		'step'   => 15,
	);
	$properties = wp_parse_args( $properties, $defaults );

	// Custom slide callback
	$properties['slide'] = 'timeSlider';

	// Non-editables
	$properties['min'] = 0;
	$properties['max'] = 1440;

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