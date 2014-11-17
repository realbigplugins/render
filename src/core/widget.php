<?php

class USL_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'usl_widget',
			'Shortcode',
			array(
				'description' => 'Adds a shortcode to your sidebar.'
			)
		);

		add_action( 'current_screen', array( __CLASS__, 'add_actions' ) );
	}

	public static function add_actions( $screen ) {

		if ( $screen->base !== 'widgets' ) {
			return;
		}

		add_filter( 'usl_att_pre_loop', array( __CLASS__, 'add_content_to_atts' ), 10, 3 );
	}

	public static function add_content_to_atts( $atts, $wrapping ) {

		if ( $wrapping ) {
			$atts = array_merge(
				array(
					'content' => array(
						'required' => true,
						'textarea' => array(),
					)
				),
				$atts
			);
		}

		return $atts;
	}

	public function widget( $args, $instance ) {

		echo $args['before_widget'];

		echo do_shortcode( isset( $instance['code'] ) ? htmlspecialchars_decode( $instance['code'] ) : '' );

		echo $args['after_widget'];
	}

	public function form( $instance ) {

		$code = isset( $instance['code'] ) ? $instance['code'] : '';

		?>
		<p class="usl-widget-add-shortcode-container">
			<span class="usl-widget-add-shortcode button">
				<?php echo empty( $code ) ? 'Add Shortcode' : 'Modify / Remove Shortcode'; ?>
			</span>
		</p>
		<p class="usl-widget-shortcode code">
			<?php echo ! empty( $code ) ? $code : 'No shortcode yet'; ?>
		</p>

		<input type="hidden" class="usl-widget-shortcode-field"
		       name="<?php echo $this->get_field_name( 'code' ); ?>" value="<?php echo htmlspecialchars( $code ); ?>"/>
	<?php
	}
}

function _usl_register_widget() {

	register_widget( 'USL_Widget' );
}

add_action( 'widgets_init', '_usl_register_widget' );