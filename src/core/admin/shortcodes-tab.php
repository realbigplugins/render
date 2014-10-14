<?php
/*
The contents of this file create the admin page that is found under settings.
Within the contents of the page both the $usl_cats and $USL->shortcodes arrays
(which are defined in the plugin's main file) are used to create the master list.
*/

if ( is_admin() ) {
	$uslshortcodes = new View_Ultimate_Shortcodes_Library();
}

class View_Ultimate_Shortcodes_Library {
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
			array( $this, 'Display_USL_Page' ),
			'dashicons-editor-code',
			82.9
		);
		add_submenu_page(
			'view-all-shortcodes',
			'Shortcodes',
			'Shortcodes',
			'manage_options',
			'view-all-shortcodes',
			array( $this, 'Display_USL_Page' )
		);
	}

	/**
	 * Display the admin page
	 */
	public function Display_USL_Page() {
		global $shortcode_tags;
		global $USL;

		$categories = array();
		foreach( $USL->shortcodes as $code => $shortcode ) {
			if ( !in_array( $shortcode['categories'], $categories) ) {
				$categories[] = $shortcode['categories'];
			}
		}

		// GET vars
		if ( isset( $_GET['cat'] ) ) {
			$category = $_GET['cat'];
		}
		$order = null;
		if ( isset( $_GET['order'] ) ) {
			$order = $_GET['order'];
		}
		$orderby = null;
		if ( isset( $_GET['orderby'] ) ) {
			$orderby = $_GET['orderby'];
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
							<option value='All'>View all categories</option>
							<?php $level = 0;
							if ( $categories ) {
								foreach ( $categories as $cat ) {
									$level = ++ $level; ?>
									<option class="level-<?php echo $level; ?>"
									        value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
								<?php }
							} ?>
						</select>
						<input type="submit" name="" id="shortcode-query-submit" class="button" value="Filter"/>
					</div>
					<!--Number of items-->
					<div class='tablenav-pages one-page'>
						<span class="displaying-num"><?php echo count( $shortcode_tags ); ?> total shortcodes</span>
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

					<!--Row 1-->
					<?php
					if ( ! empty( $USL->shortcodes ) ) {
						foreach ( $USL->shortcodes as $code => $shortcode ) {
							if ( isset( $category ) && $shortcode['category'] == $category OR ! isset( $category ) OR $category == 'All' ) {
								?>
								<tr class="post-<?php echo $key; ?> type-post status-publish format-standard hentry category-uncategorized alternate iedit author-self level-0">
									<td class="post-title page-title column-title">
										<strong><?php echo $shortcode['title']; ?></strong>
									</td>
									<td class="code column-code">
										[<?php echo $code; ?>]
									</td>
									<td class="description column-description">
										<?php echo $shortcode['description']; ?>
									</td>
									<td class="atts column-atts"><?php echo $shortcode['atts']; ?></td>
									<td class="category column-category">
										<?php echo $shortcode['category']; ?>
									</td>
									<td class="example column-example">
										<?php echo $shortcode['example']; ?>
									</td>
								</tr>
							<?php } else {
							}
						}
					} ?>
					</tbody>
				</table>
			</form>
		</div>
	<?php
	}
} // END class