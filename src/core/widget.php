<?php

class USL_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'usl_widget',
			__( 'Shortcode', 'USL' ),
			array(
				'description' => __( 'Adds a shortcode to your sidebar.', 'USL' ),
			)
		);
	}

	public function widget( $args, $instance ) {

		echo $args['before_widget'];

		if ( isset( $instance['title'] ) ) {
			echo $args['before_title'];
			echo apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
			echo $args['after_title'];
		}

		echo apply_filters( 'widget_usl_before_content', '<p>', $instance, $this->id_base );
		echo do_shortcode( isset( $instance['code'] ) ? htmlspecialchars_decode( $instance['code'] ) : '' );
		echo apply_filters( 'widget_usl_after_content', '</p>', $instance, $this->id_base );

		echo $args['after_widget'];
	}

	public function form( $instance ) {

		$title           = isset( $instance['title'] ) ? strip_tags( esc_attr( $instance['title'] ) ) : '';
		$code            = isset( $instance['code'] ) ? $instance['code'] : '';
		$shortcode_title = isset( $instance['shortcode_title'] ) ? $instance['shortcode_title'] : '';

		?>
		<div class="usl-widget">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title', 'USL' ); ?>:
				</label>
				<br/>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat"
				       name="<?php echo $this->get_field_name( 'title' ); ?>"
				       value="<?php echo $title; ?>"/>
			</p>

			<p class="usl-widget-shortcode-preview">
				<?php echo ! empty( $shortcode_title ) ? $shortcode_title : __( 'No shortcode yet', 'USL' ); ?>
			</p>

			<p class="usl-widget-add-shortcode-container">
			<span class="usl-widget-add-shortcode button">
				<?php echo empty( $code ) ? __( 'Add Shortcode', 'USL' ) : __( 'Modify / Remove Shortcode', 'USL' ); ?>
			</span>
			</p>

			<p class="usl-widget-customizer-message" style="display: none;">
				<?php
				printf(
					__( ' In order to see a live preview, please use the %s.', 'USL' ),
					'<a href="/wp-admin/customize.php?return=%2Fwp-admin%2Fwidgets.php">customizer</a>'
				);
				?>
			</p>

			<input type="hidden" class="usl-widget-shortcode"
			       name="<?php echo $this->get_field_name( 'code' ); ?>"
			       value="<?php echo htmlspecialchars( $code ); ?>"/>

			<input type="hidden" class="usl-widget-shortcode-title"
			       name="<?php echo $this->get_field_name( 'shortcode_title' ); ?>"
			       value="<?php echo $shortcode_title; ?>"/>
		</div>
	<?php
	}
}

add_action( 'widgets_init', '_usl_register_widget' );
add_action( 'current_screen', '_usl_widget_add_actions' );

// Make sure this stuff also loads for the customizer
add_action( 'customize_controls_enqueue_scripts', array( 'USL_Modal', 'admin_scripts' ) );
add_action( 'customize_controls_enqueue_scripts', array( 'USL', '_admin_enqueue_files' ) );
add_action( 'customize_controls_print_footer_scripts', array( 'USL_Modal', 'output' ) );

function _usl_register_widget() {
	register_widget( 'USL_Widget' );
}

function _usl_widget_add_actions( $screen ) {

	if ( $screen->base !== 'widgets' && $screen->base !== 'customize' ) {
		return;
	}

	add_filter( 'usl_att_pre_loop', '_usl_add_content_to_atts', 10, 3 );

	include_once( USL::$path . '/core/modal.php' );
	new USL_Modal();
}

function _usl_add_content_to_atts( $atts, $wrapping ) {

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