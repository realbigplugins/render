<?php
// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Render_AdminPage_Shortcodes
 *
 * Provides the admin page for viewing all shortcodes.
 *
 * @since 1.0.0
 *
 * @package Render
 * @subpackage Admin
 */
class Render_AdminPage_Shortcodes extends Render {

	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'save_screen_options' ), 10, 3 );
	}

	/**
	 * Function for the admin menu to create a menu item in the settings tree.
	 *
	 * @since 1.0.0
	 */
	public function menu() {

		$hook = add_submenu_page(
			'render-settings',
			'Shortcodes',
			'Shortcodes',
			'manage_options',
			'render-shortcodes',
			array( $this, 'page_output' )
		);

		add_action( "load-$hook", array( __CLASS__, 'screen_options' ) );
	}

	/**
	 * Adds screen options for the shortcodes
	 *
	 * @since 1.0.0
	 */
	public static function screen_options() {

		global $RenderShortcodesTable;

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
		require_once( RENDER_PATH . '/core/admin/views/shortcodes-table.php' );
		$RenderShortcodesTable = new Render_ShortcodesTable();
	}

	/**
	 * Adds on custom admin body classes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Admin body classes.
	 * @return string New classes.
	 */
	public static function body_class( $classes ) {

		$classes .= 'render render-shortcodes';

		return $classes;
	}

	/**
	 * Necessary for saving screen options.
	 *
	 * @since 1.0.0
	 */
	public static function save_screen_options( $status, $option, $value ) {
		return $value;
	}

	/**
	 * Display the admin page HTML.
	 *
	 * @since 1.0.0
	 */
	public function page_output() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		global $RenderShortcodesTable;

		$RenderShortcodesTable->prepare_items();
		?>
		<div class="wrap">
			<h2 class="render-page-title">
				<img src="<?php echo RENDER_URL; ?>/assets/images/render-logo.svg" class="render-page-title-logo"/>
				<?php _e( 'Shortcodes', 'Render' ); ?>
			</h2>

			<form method="get">
				<input type="hidden" name="page" value="<?php echo isset( $_GET['page'] ) ? $_GET['page'] : ''; ?>" />

				<?php $RenderShortcodesTable->search_box( 'Search Shortcodes', 'render_col_name' ); ?>

				<?php if ( ! empty( $_GET['s'] ) ) : ?>
					<div class="render-search">
						<?php printf(
							__( 'Search results for %s', 'Render' ),
							'&ldquo;<strong>' . esc_html( $_GET['s'] ) . '</strong>&rdquo;'
						); ?>
					</div>
				<?php endif; ?>

				<?php $RenderShortcodesTable->display(); ?>
			</form>

		</div>
	<?php
	}
}

new Render_AdminPage_Shortcodes();