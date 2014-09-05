<?php
/*
The contents of this file create the admin page that is found under settings.
Within the contents of the page both the $usl_cats and $usl_codes arrays
(which are defined in the plugin's main file) are used to create foreach loops.
*/

if(is_admin()) {
	$shortcodes = new View_Ultimate_Shortcodes_Library();
}

class View_Ultimate_Shortcodes_Library {
	public function __construct() {
		$this->Admin();
	}
	/**
	 * Create the admin area
	 */
	public function Admin(){
		add_action( 'admin_menu', array(&$this,'Admin_Menu') );
	}

	/**
	 * Function for the admin menu to create a menu item in the settings tree
	 */
	public function Admin_Menu(){
		add_submenu_page(
			'options-general.php',
			'Ultimate Shortcodes Library',
			'Ultimate Shortcodes Library',
			'manage_options',
			'view-all-shortcodes',
			array(&$this,'Display_USL_Page'));
	}

	/**
	 * Display the admin page
	 */
	public function Display_USL_Page(){
		global $shortcode_tags;
		global $usl_cats;
		global $usl_codes;
		if ( $shortcode_tags ) {
			foreach ( $shortcode_tags as $tag => $v ) {
				$check = strpos( $tag, 'usl_' );
				if ( $check === false ) {
					$title = str_replace( '_', ' ', $tag );
					$usl_codes[] = array(
						'Code' => $tag,
						'Title' => $title,
						'Description' => '',
						'Atts' => '',
						'Category' => usl_core_shortcodes( $tag ),
						'Example' => ''
					);
				} else { }
			}
		}
		/*
		echo '<pre>';
		print_r( $shortcode_tags );
		echo 'USL Codes';
		print_r( $usl_codes );
		echo 'USL Cats';
		print_r( $usl_cats );
		echo '</pre>';
        */ ?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2>View All Available Shortcodes</h2>

		<form id="posts-filter" action="" method="get">

			<!--Search-->
			<p class="search-box">
				<label class="screen-reader-text" for="post-search-input">Search Shortcodes:</label>
				<input type="search" id="post-search-input" name="s" value="" />
				<input type="submit" name="" id="search-submit" class="button" value="Search Shortcodes"  /></p>

			<!--Not sure-->
			<!--<input type="hidden" name="post_status" class="post_status_page" value="all" />-->
			<!--<input type="hidden" name="post_type" class="post_type_page" value="post" />-->

			<!--<input type="hidden" id="_wpnonce" name="_wpnonce" value="ad493613e7" />-->
			<!--<input type="hidden" name="_wp_http_referer" value="/wp-admin/edit.php" />-->
			<div class="tablenav top">

				<!--Date select-->
				<div class="alignleft actions">

					<!--Category select-->
					<select name='cat' id='cat' class='postform' >
						<option value='0'>View all categories</option>
						<?php $level = 0;
						if ( $usl_cats ) {
							foreach ( $usl_cats as $cat ) {
								$level = ++$level; ?>
							<option class="level-<?php echo $level; ?>" value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
						<?php } }  ?>
					</select>
					<input type="submit" name="" id="post-query-submit" class="button" value="Filter"  />
				</div>
				<!--Number of items-->
				<div class='tablenav-pages one-page'>
					<span class="displaying-num"><?php echo count( $shortcode_tags ); ?> total shortcodes</span>
				</div>

				<br class="clear" />
			</div>

			<!--Actual list table-->
			<table class="wp-list-table widefat fixed posts">
				<!--Table header row-->
				<thead>
				<tr>
					<th scope='col' id='title' class='manage-column column-title sortable desc'>
						<a href="http://plugins.dev/wp-admin/edit.php?orderby=title&#038;order=asc">
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
					<th scope='col'  class='manage-column column-title sortable desc'>
						<a href="http://plugins.dev/wp-admin/edit.php?orderby=title&#038;order=asc">
							<span>Title</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope='col'  class='manage-column column-code'>Code</th>
					<th scope='col'  class='manage-column column-description'>Description</th>
					<th scope='col'  class='manage-column column-atts'>Attributes</th>
					<th scope='col'  class='manage-column column-category'>Category</th>
					<th scope='col'  class='manage-column column-example sortable asc'>Example</th>
				</tr>
				</tfoot>

				<tbody id="the-list">

				<!--The rows-->

				<!--Row 1-->
				<?php
				if ( isset( $_GET['cat'] ) ) {
					$category = $_GET['cat'];
				}

				if ( $usl_codes ) {
					foreach ( $usl_codes as $key => $code ) {
						if ( isset( $category ) && $code['Category'] == $category OR !isset( $category ) ) {
				?>
				<tr class="post-<?php echo $key; ?> type-post status-publish format-standard hentry category-uncategorized alternate iedit author-self level-0">
					<td class="post-title page-title column-title">
						<strong><?php echo $code['Title']; ?></strong>
						<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
						<div class="row-actions">
							<span class='edit'>
								<a href="#" title="Copy this shortcode">Copy to clipboard</a>
							</span>
						</div>
					</td>
					<td class="code column-code">
						[<?php echo $code['Code']; ?>]
					</td>
					<td class="description column-description">
						<?php echo $code['Description']; ?>
					</td>
					<td class="atts column-atts"><?php echo $code['Atts']; ?></td>
					<td class="category column-category">
						<?php echo $code['Category']; ?>
					</td>
					<td class="example column-example">
						<?php echo $code['Example']; ?>
					</td>
				</tr>
					<?php } else { } } } ?>
				</tbody>
			</table>
		</form>
	</div>
		<?php
	}
} // END class