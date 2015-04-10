<?php
// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Render_Notices
 *
 * Creating and displaying Render admin notices.
 *
 * @since      1.0.0
 *
 * @package    Render
 * @subpackage Modal
 */
class Render_Notices {

	/**
	 * All notices to show.
	 *
	 * @since  {{VERSION}}
	 * @access private
	 *
	 * @var array
	 */
	private $notices;

	/**
	 * Initializes the class.
	 *
	 * @since {{VERSION}}
	 */
	public function __construct() {

		add_action( 'admin_notices', array( $this, '_show' ) );

		add_action( 'wp_ajax_render_hide_notice', array( $this, '_hide_notice' ) );
	}

	/**
	 * Warns user about PHP version.
	 *
	 * @since 1.0.3
	 *
	 * @param $ID          string Notice ID.
	 * @param $message     string The message to show.
	 * @param $type        string Which type of notice to show.
	 * @param $hide_button bool Show or hide the "hide" button.
	 */
	public function add( $ID, $message = '', $type = 'error', $hide_button = false ) {

		$this->notices[ $ID ] = array(
			'message'     => $message,
			'type'        => $type,
			'hide_button' => $hide_button,
		);
	}

	/**
	 * Removes a Render admin notice.
	 *
	 * @since {{VERSION}}
	 *
	 * @param $notice_ID string The ID of the notice to remove.
	 */
	public function remove( $notice_ID ) {
		unset( $this->notices[ $notice_ID ] );
	}

	/**
	 * Displays all Render notices.
	 *
	 * @since  {{VERSION}}
	 * @access private
	 */
	function _show() {

		/**
		 * Allows external filtering of admin notices Render displays.
		 *
		 * @since {{VERSION}}
		 */
		$this->notices = apply_filters( 'render_notices', $this->notices );

		if ( ! empty( $this->notices ) ) {

			// Used for hiding the notice effect
			wp_enqueue_script( 'jquery-effects-blind');

			foreach ( (array) $this->notices as $ID => $notice ) {

				// Skip if hidden
				if ( get_user_meta( get_current_user_id(), "_render_notice_hidden_$ID", true ) ) {
					continue;
				}
				?>
				<div class="render-notice <?php echo $notice['type']; ?>" id="<?php echo $ID; ?>">
					<p>
						<?php echo $notice['message']; ?>

						<?php if ( $notice['hide_button'] ) : ?>
							<a href="#" class="button render-hide-notice" data-notice="<?php echo $ID; ?>">
								Hide Notice
							</a>
						<?php endif; ?>
					</p>
				</div>
			<?php
			}
		}
	}

	/**
	 * Hides a notice for the user forever. AJAX call.
	 *
	 * @since  {{VERSION}}
	 * @access private
	 */
	function _hide_notice() {

		$notice_ID = $_POST['notice_ID'];
		update_user_meta( get_current_user_id(), "_render_notice_hidden_$notice_ID", '1' );

		die();
	}
}