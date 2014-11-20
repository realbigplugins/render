<?php

class USL_ShortcodesTable extends WP_List_Table {

	function __construct() {

		parent::__construct( array(
			'singular' => 'usl_shortcodes_table',
			'plural'   => 'usl_shortcodes_tables',
			'ajax'     => false,
		) );
	}

	public function extra_tablenav( $which ) {

		?>
		<div class="alignleft actions">
			<form method="get">

				<?php
				if ( ! empty( $_GET ) ) {
					foreach ( $_GET as $name => $value ) {
						echo "<input type='hidden' name='$name' value='$value' />";
					}
				}
				?>

				<?php $this->categories_dropdown(); ?>
				<input type="submit" id="post-query-submit" class="button" value="Filter">
			</form>
		</div>
	<?php
	}

	private function categories_dropdown() {

		$categories  = _usl_get_categories();
		$current_cat = isset( $_GET['category'] ) ? $_GET['category'] : '';
		?>

		<label for="filter-by-category" class="screen-reader-text">
			Filter by category
		</label>

		<select name="category" id="filter-by-category" class="postform">
			<option value="0">All categories</option>

			<?php foreach ( $categories as $category ) : ?>
				<option class="level-0" value="<?php echo $category; ?>" <?php selected( $category, $current_cat ); ?>>
					<?php echo _usl_translate_id_to_name( $category ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	<?php
	}

	public function get_columns() {

		return $columns = array(
			'usl_col_name'        => 'Name',
			'usl_col_code'        => 'Code',
			'usl_col_description' => 'Description',
			'usl_col_category'    => 'Category',
			'usl_col_attributes'  => 'Attributes',
			'usl_col_example'     => 'Example',
		);
	}

	public function get_sortable_columns() {

		return $sortable = array(
			'usl_col_name'     => array( 'name', false ),
			'usl_col_code'     => array( 'code', false ),
			'usl_col_category' => array( 'category', false ),
		);
	}

	public function prepare_items() {

		// Setup some basic data
		$this->_column_headers = $this->get_column_info();

		// Get our items and setup the basic array
		$items          = array();
		$all_shortcodes = _usl_get_merged_shortcodes();

		foreach ( $all_shortcodes as $code => $shortcode ) {

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
				'usl_col_name'        => $shortcode['title'],
				'usl_col_code'        => $code,
				'usl_col_description' => $shortcode['description'],
				'usl_col_category'    => $shortcode['category'],
				'usl_col_attributes'  => $shortcode['atts'],
				'usl_col_example'     => $shortcode['example'],
			) );
		}

		// Sort them by the defined order
		usort( $items, array( __CLASS__, 'usort_reorder' ) );

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
		$this->items = $this->found_data;
	}

	public static function usort_reorder( $a, $b ) {

		// If no sort, default to title
		$orderby = 'usl_col_' . ( ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'category' );

		// If no order, default to asc
		$order = ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'asc';

		// Determine sort order
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : - $result;
	}

	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'usl_col_name':
				return $item[ $column_name ];

			case 'usl_col_code':
				return $item[ $column_name ];

			case 'usl_col_description':
				return $item[ $column_name ];

			case 'usl_col_category':
				return _usl_translate_id_to_name( $item[ $column_name ] );

			case 'usl_col_attributes':

				if ( ! empty( $item[ $column_name ] ) ) {

					$atts = array();
					foreach ( $item[ $column_name ] as $name => $value ) {
						$atts[] = $name;
					}
					$output = implode( ', ', $atts );
				} else {
					$output = 'No attributes.';
				}

				return $output;

			case 'usl_col_example':
				return $item[ $column_name ];

			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	public function no_items() {
		echo 'Sorry, couldn\'t find any shortcodes.';
	}
}