<?php
// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Render_ShortcodesTable
 *
 * Displays the WP table for the Render shortocdes page.
 *
 * @since 1.0.0
 *
 * @package Render
 * @subpackage Admin
 */
class Render_ShortcodesTable extends WP_List_Table {

	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		parent::__construct( array(
			'singular' => 'render_shortcodes_table',
			'plural'   => 'render_shortcodes_tables',
			'ajax'     => false,
		) );
	}

	/**
	 * Adds HTML to the top or bottom of the table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $which Top or bottom of table.
	 */
	public function extra_tablenav( $which ) {

		if ( $which === 'top' ) :
			?>
			<div class="alignleft actions">
				<?php $this->categories_dropdown(); ?>
				<input type="submit" id="post-query-submit" class="button" value="<?php _e( 'Filter', 'Render' ); ?>">
			</div>
		<?php
		endif;
	}

	/**
	 * Provides the category dropdown for filtering.
	 *
	 * @since 1.0.0
	 */
	private function categories_dropdown() {

		$categories  = render_get_shortcode_categories();
		$current_cat = isset( $_GET['category'] ) ? $_GET['category'] : '';

		unset( $categories['all'] );
		?>

		<label for="filter-by-category" class="screen-reader-text">
			<?php _e( 'Filter by category', 'Render' ); ?>
		</label>

		<select name="category" id="filter-by-category" class="postform">
			<option value="0"><?php _e( 'All categories', 'Render' ); ?></option>
			<?php foreach ( $categories as $category_ID => $category ) : ?>
				<option class="level-0" value="<?php echo $category_ID; ?>" <?php selected( $category_ID, $current_cat ); ?>>
					<?php echo $category['label']; ?>
				</option>
			<?php endforeach; ?>
		</select>
	<?php
	}

	/**
	 * Tells WP which table columns are sortable.
	 *
	 * @since 1.0.0
	 *
	 * @return array Sortable columns.
	 */
	public function get_sortable_columns() {

		return $sortable = array(
			'name'     => array( 'name', false ),
			'code'     => array( 'code', false ),
			'source'   => array( 'source', false ),
			'category' => array( 'category', false ),
		);
	}

	/**
	 * Tells WP what the table columns are.
	 *
	 * @since 1.0.0
	 *
	 * @return array Columns.
	 */
	public function get_columns() {

		return $columns = array(
			'cb'          => '<input type="checkbox" />',
			'name'        => __( 'Name', 'Render' ),
			'description' => __( 'Description', 'Render' ),
			'category'    => __( 'Category', 'Render' ),
			'source'      => __( 'Source', 'Render' ),
			'attributes'  => __( 'Attributes', 'Render' ),
			'code'        => __( 'Code', 'Render' ),
		);
	}

	/**
	 * Adds bulk actions to the table.
	 *
	 * @since 1.0.0
	 *
	 * @return array Bulk actions.
	 */
	public function get_bulk_actions() {
		return $actions = array(
			'disable' => __( 'Disable', 'Render' ),
			'enable'  => __( 'Enable', 'Render' ),
		);
	}

	/**
	 * Adds a checkbox column on the left of the table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $item The current row item.
	 * @return string The checkbox HTML.
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="shortcodes[]" value="%s" />', $item['code']
		);
	}

	/**
	 * Outputs the HTML to a table row.
	 *
	 * @since 1.0.0
	 *
	 * @param object $item The current row item.
	 */
	public function single_row( $item ) {

		global $render_sc_table_disabled;

		static $alternate = '';

		$alternate = ( $alternate == '' ? 'alternate' : '' );
		$disabled  = isset( $render_sc_table_disabled ) && in_array( $item['code'], $render_sc_table_disabled ) ? 'disabled' : '';

		echo "<tr class='$alternate $disabled'>";
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Prepares the shortcodes for the WP table.
	 *
	 * @since 1.0.0
	 */
	public function prepare_items() {

		global $Render;

		// Save previous data
		$this->save_shortcode_options();

		// Setup some basic data
		$this->_column_headers = $this->get_column_info();

		// Get our items and setup the basic array
		$items          = array();
		$all_shortcodes = $Render->shortcodes;

		foreach ( $all_shortcodes as $code => $shortcode ) {

			// Skip those set to hidden
			if ( isset( $shortcode['noDisplay'] ) && $shortcode['noDisplay'] ) {
				continue;
			}

			// Filter if there was a search
			if ( ! empty( $_GET['s'] ) ) {

				$merged = $shortcode['title'] . $shortcode['category'] . $shortcode['description'] . $code;

				if ( strpos( strtolower( $merged ), strtolower( $_GET['s'] ) ) === false ) {
					continue;
				}
			}

			// Filter if there was a selected category
			if ( ! empty( $_GET['category'] ) ) {
				if ( ! isset( $shortcode['category'] ) || $shortcode['category'] != $_GET['category'] ) {
					continue;
				}
			}

			array_push( $items, array(
				'name'        => $shortcode['title'],
				'code'        => $code,
				'description' => $shortcode['description'],
				'source'      => $shortcode['source'],
				'category'    => $shortcode['category'],
				'attributes'  => $shortcode['atts'],
			) );
		}

		// Sort them by the defined order
		uasort( $items, function( $a, $b ) {

			// If no sort, default to title
			$orderby = '' . ( ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'name' );

			// If no order, default to asc
			$order = ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'asc';

			// Determine sort order
			$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

			// Send final sort direction to usort
			return ( $order === 'asc' ) ? $result : - $result;
		});

		// Pagination
		$per_page     = $this->get_items_per_page( 'shortcodes_per_page', 10 );
		$current_page = $this->get_pagenum();
		$total_items  = count( $items );

		$this->found_data = array_slice( $items, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
		) );

		// Finally, output the items
		$this->items = array_slice( $items, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}

	/**
	 * Sets the name of each table row.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item The current row item.
	 * @return string The column name.
	 */
	public function column_name( $item ) {

		global $render_sc_table_disabled;

		$extra_params = isset( $_REQUEST['category'] ) ? "&category=$_REQUEST[category]" : '';

		if ( in_array( $item['code'], (array) $render_sc_table_disabled ) ) {

			$actions['enable'] = sprintf(
				"<a href='?page=%s&action=%s&shortcodes=%s%s'>%s</a>",
				$_REQUEST['page'],
				'enable',
				$item['code'],
				$extra_params,
				__( 'Enable', 'Render' )
			);
		} else {

			$actions['delete'] = sprintf(
				"<a href='?page=%s&action=%s&shortcodes=%s%s'>%s</a>",
				$_REQUEST['page'],
				'disable',
				$item['code'],
				$extra_params,
				__( 'Disable', 'Render' )
			);
		}

		$disabled = in_array( $item['code'], (array) $render_sc_table_disabled ) ? ' (' . __( 'disabled', 'Render' ) . ')' : '';

		return sprintf(
			'%1$s %2$s',
			$item['name'] . " <span class='render-sc-list-disabled'>$disabled</span>",
			$this->row_actions( $actions )
		);
	}

	/**
	 * Sets up each column's output.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item The current row item.
	 * @param string $column_name The name of the column.
	 * @return mixed|string The shortcode column output.
	 */
	public function column_default( $item, $column_name ) {

		global $render_sc_table_disabled;

		$render_sc_table_disabled = (array) $render_sc_table_disabled;

		$categories = render_get_shortcode_categories();

		switch ( $column_name ) {
			case 'code':
			case 'source':
			case 'description':

			return $item[ $column_name ];
				break;

			case 'category':

				if ( isset( $categories[ $item[ $column_name ] ] ) ) {
					return $categories[ $item[ $column_name ] ]['label'];
				} else {
					return $item[ $column_name ];
				}
				break;

			case 'attributes':

				if ( ! empty( $item[ $column_name ] ) ) {

					$atts = array();
					foreach ( $item[ $column_name ] as $att ) {
						if ( isset( $att['label'] ) ) {
							$atts[] = $att['label'];
						}
					}
					$output = implode( ', ', $atts );
				} else {
					$output = 'None';
				}

				return $output;

			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Saves shortcode options (currently disable | enable ).
	 *
	 * @since 1.0.0
	 */
	public function save_shortcode_options() {

		global $render_sc_table_disabled;

		$shortcodes = render_get_disabled_shortcodes();

		if ( isset( $_REQUEST['action'] ) && isset( $_REQUEST['shortcodes'] ) ) {

			$_shortcodes = array();
			if ( is_array( $_REQUEST['shortcodes'] ) ) {
				foreach ( $_REQUEST['shortcodes'] as $shortcode ) {
					$_shortcodes[] = $shortcode;
				}
			} else {
				$_shortcodes[] = $_REQUEST['shortcodes'];
			}

			if ( $this->current_action() === 'disable' ) {
				$shortcodes = array_merge( $shortcodes, $_shortcodes );
			} elseif ( $this->current_action() === 'enable' ) {
				$shortcodes = array_diff( $shortcodes, $_shortcodes );
			}

			update_option( 'render_disabled_shortcodes', array_unique( $shortcodes ) );
		}

		if ( ! empty( $shortcodes ) ) {
			$render_sc_table_disabled = array_unique( $shortcodes );
		}
	}

	/**
	 * Message for if there are no shortcodes found.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		_e( 'Sorry, couldn\'t find any shortcodes.', 'Render' );
	}
}