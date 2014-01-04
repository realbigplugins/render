<?php
/*
This would be a great file for adding code that you want run in the backend. For example if you want to
create an admin or a settings page, this would be the appropriate place to work.
*/

if(is_admin())
{
	// Create the Paulund toolbar
	$shortcodes = new View_Ultimate_Shortcodes_Library();
}

/**
 * View all available shrotcodes on an admin page
 *
 * @author
 **/
class View_Ultimate_Shortcodes_Library
{
	public function __construct()
	{
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
		global $usl_cats;
		global $usl_codes;
        ?>
        <div class="wrap">
        	<div id="icon-options-general" class="icon32"><br /></div>
			<h2>View All Available Shortcodes</h2>
			<div class="section panel">
				<p>This is where you can view all the amazing shortcodes we gave you.</p>
<!--second try-->
			<table class="widefat importers">
				<tr><td>
					<ul>
				<?php 
				foreach ($usl_cats as $category) { ?>
					<li>
						<?php echo "<h3>$category</h3>"; 
						//start the codes loop
						for ($row = 0; $row < count($usl_codes); $row++) { ?>
						<ul>
							<?php foreach ($usl_codes[$row] as $key => $value) {
								echo "<li><b>".$key."</b> ".$value."</li>";
							} ?>
						</ul>
						<?php } //end codes loop ?>
					</li>
				<?php }
					?>
					</ul>
				</td></tr>
			</table>

<!--first try-->
			<table class="widefat importers">
				<tr><td>
					<ol>
				<?php 
					for ($row = 0; $row < 5; $row++) {
								echo "<li> Row #$row";
								echo "<ul>";
								foreach($usl_cats[$row] as $key => $value) {
									echo "<li>".$key.$value."</li>";
								}
								echo "</ul>";
								echo "</li>";

								}
					?>
					</ol>
				</td></tr>
			</table>
			</div>
		</div>
		<?php
	}
} // END class View_All_Available_Shortcodes
?>