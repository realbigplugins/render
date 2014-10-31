<?php

if ( is_admin() ) {
	new USL_MenuPage();
}

class USL_MenuPage {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}

	/**
	 * Function for the admin menu to create a menu item in the settings tree
	 */
	public function menu() {

		add_menu_page(
			'Shortcodes',
			'Shortcodes',
			'manage_options',
			'view-all-shortcodes',
			array( $this, 'page_output' ),
			'dashicons-editor-code',
			82.9
		);

		add_submenu_page(
			'view-all-shortcodes',
			'Shortcodes',
			'Shortcodes',
			'manage_options',
			'view-all-shortcodes',
			array( $this, 'page_output' )
		);
	}

	public static function _sort_title_asc( $a, $b ) {
		return strcmp( $a['title'], $b['title'] );
	}

	public static function _sort_title_desc( $a, $b ) {
		return strcmp( $b['title'], $a['title'] );
	}

	/**
	 * Display the admin page
	 */
	public function page_output() {
		global $shortcode_tags, $USL;

		// TODO Redo table meeting WP standards

		$all_shortcodes = _usl_get_merged_shortcodes();

		// Setup categories
		$categories = array();
		foreach ( $all_shortcodes as $shortcode ) {

			// Add a category if it's set, not empty, and doesn't already exist in our $categories array
			if ( ! empty( $shortcode['category'] ) && ! in_array( $shortcode['category'], $categories ) ) {
				$categories[] = $shortcode['category'];
			}
		}

		// GET vars
		$category = isset( $_GET['cat'] ) ? $_GET['cat'] : false;
		$order    = isset( $_GET['order'] ) ? $_GET['order'] : 'asc';
		$orderby  = isset( $_GET['orderby'] ) ? $_GET['orderby'] : false;

		// Sort by title
		if ( ! $orderby || $orderby === 'title' ) {
			uasort( $all_shortcodes, array( __CLASS__, "_sort_title_$order" ) );
		}

		// TODO Enable sorting by title
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br/></div>
			<h2>View All Available Shortcodes</h2>

			<form id="posts-filter" action="" method="get">

				<input type="hidden" name="page" class="post_type_page" value="view-all-shortcodes"/>

				<div class="tablenav top">

					<!--Date select-->
					<div class="alignleft actions">

						<!--Category select-->
						<select name='cat' id='cat' class='postform'>
							<option value=''>View all categories</option>
							<?php
							if ( $categories ) {
								foreach ( $categories as $select_category ) {
									?>
									<option value="<?php echo $select_category; ?>" <?php selected( $select_category, $category ); ?>>
										<?php echo ucwords( $select_category ); ?>
									</option>
								<?php
								}
							} ?>
						</select>
						<input type="submit" name="" id="shortcode-query-submit" class="button" value="Filter"/>
					</div>
					<!--Number of items-->
					<div class='tablenav-pages one-page'>
						<span class="displaying-num"><?php echo count( $all_shortcodes ); ?> total shortcodes</span>
					</div>

					<br class="clear"/>
				</div>

				<!--Actual list table-->
				<table class="wp-list-table widefat fixed posts">
					<!--Table header row-->
					<thead>
					<tr>
						<th scope='col' id='title' class='manage-column column-title sortable desc'>
							<a href="?orderby=title&#038;order=<?php if ( $order == 'asc' ) {
								echo 'desc';
							} else {
								echo 'asc';
							} ?>&page=view-all-shortcodes">
								<span>Title</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<th scope='col' id='author' class='manage-column column-code'>Code</th>
						<th scope='col' id='categories' class='manage-column column-description'>Description</th>
						<th scope='col' id='tags' class='manage-column column-atts'>Attributes</th>
						<th scope='col' id='comments' class='manage-column column-category'>Category</th>
						<th scope='col' id='date' class='manage-column column-example'>Example</th>
					</tr>
					</thead>

					<tfoot>
					<!--Table footer-->
					<tr>
						<th scope='col' class='manage-column column-title sortable desc'>
							<a href="?orderby=title&#038;order=<?php if ( $order == 'asc' ) {
								echo 'desc';
							} else {
								echo 'asc';
							} ?>&page=view-all-shortcodes">
								<span>Title</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<th scope='col' class='manage-column column-code'>Code</th>
						<th scope='col' class='manage-column column-description'>Description</th>
						<th scope='col' class='manage-column column-atts'>Attributes</th>
						<th scope='col' class='manage-column column-category'>Category</th>
						<th scope='col' class='manage-column column-example sortable asc'>Example</th>
					</tr>
					</tfoot>

					<tbody id="the-list">

					<!--The rows-->
					<?php
					if ( ! empty( $all_shortcodes ) ) {
						$i = 0;
						foreach ( $all_shortcodes as $code => $shortcode ) {
							$i ++;
							if ( ( $category && ( $shortcode['category'] == $category ) ) ||  ! $category ) {
								?>
								<tr class="format-standard category-uncategorized <?php echo $i % 2 ? 'alternate' : ''; ?> level-0">
									<td class="post-title page-title column-title">
										<strong><?php echo $shortcode['title']; ?></strong>
									</td>
									<td class="code column-code">
										[<?php echo $code; ?>]
									</td>
									<td class="description column-description">
										<?php echo $shortcode['description']; ?>
									</td>
									<td class="atts column-atts">
										<?php
										$all_attributes = array();
										if ( ! empty( $shortcode['atts'] ) ) {
											foreach ( $shortcode['atts'] as $attribute_name => $attribute ) {

												if ( is_array( $attribute ) ) {
													if ( isset( $attribute['selectbox'] ) ) {
														$all_attributes[] = "$attribute_name (" . implode( ', ', $attribute['selectbox'] ) . ')';
													} else {
														$all_attributes[] = $attribute_name;
													}
												} else {
													$all_attributes[] = $attribute;
												}
											}
										}

										echo implode( ' | ', $all_attributes );
										?>
									</td>
									<td class="category column-category">
										<?php echo ucwords( $shortcode['category'] ); ?>
									</td>
									<td class="example column-example">
										<?php echo $shortcode['example']; ?>
									</td>
								</tr>
							<?php
							} else {
							}
						}
					} ?>
					</tbody>
				</table>
			</form>
		</div>
	<?php
	}
}