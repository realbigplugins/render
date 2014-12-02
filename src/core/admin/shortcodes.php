<?php

class USL_MenuPage extends USL {

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'save_screen_options' ), 10, 3 );
	}

	/**
	 * Function for the admin menu to create a menu item in the settings tree
	 */
	public function menu() {

		$hook = add_submenu_page(
			'usl-view-all-shortcodes',
			'Shortcodes',
			'Shortcodes',
			'manage_options',
			'usl-view-all-shortcodes',
			array( $this, 'page_output' )
		);

		add_action( "load-$hook", array( __CLASS__, 'screen_options' ) );
	}

	public static function screen_options() {

		global $USLShortcodesTable;

		add_filter( 'admin_body_class', array( __CLASS__, 'body_class' ) );

		$option = 'per_page';
		$args   = array(
			'label'   => 'Shortcodes',
			'default' => 10,
			'option'  => 'shortcodes_per_page'
		);

		add_screen_option( $option, $args );

		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		require_once( self::$path . 'core/admin/views/shortcodes-table.php' );
		$USLShortcodesTable = new USL_ShortcodesTable();
	}

	public static function body_class( $classes ) {

		$classes .= 'usl usl-shortcodes';

		return $classes;
	}

	public static function save_screen_options( $status, $option, $value ) {
		return $value;
	}

	/**
	 * Display the admin page
	 */
	public function page_output() {

		// TODO Sort by disabled shortcodes

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		global $USLShortcodesTable;

		$USLShortcodesTable->prepare_items();
		?>
		<div class="wrap">
			<h2>
				<?php _e( 'Shortcodes', 'USL' ); ?>
				<?php if ( ! empty( $_GET['s'] ) ) : ?>
					<span class="subtitle">
						<?php printf(
							__( 'Search results for %s', 'USL' ),
							'&ldquo;' . $_GET['s'] . '&rdquo;'
						); ?>
					</span>
				<?php endif; ?>
			</h2>

			<form method="get">
				<input type="hidden" name="page" value="<?php echo isset( $_GET['page'] ) ? $_GET['page'] : ''; ?>" />

				<?php $USLShortcodesTable->search_box( 'Search Shortcodes', 'usl_col_name' ); ?>

				<?php $USLShortcodesTable->display(); ?>
			</form>

		</div>
	<?php
	}
}

new USL_MenuPage();