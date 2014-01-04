<?php
/*
This would be a great file for adding code that you want run in the backend. For example if you want to
create an admin or a settings page, this would be the appropriate place to work.
*/

if(is_admin())
{
	// Create the Paulund toolbar
	$shortcodes = new View_All_Available_Shortcodes();
}

/**
 * View all available shrotcodes on an admin page
 *
 * @author
 **/
class View_All_Available_Shortcodes
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
			'View All Shortcodes',
			'View All Shortcodes',
			'manage_options',
			'view-all-shortcodes',
			array(&$this,'Display_Admin_Page'));
	}

	/**
	 * Display the admin page
	 */
	public function Display_Admin_Page(){
		global $shortcode_tags;

        ?>
        <div class="wrap">
        	<div id="icon-options-general" class="icon32"><br /></div>
			<h2>View All Available Shortcodes</h2>
			<div class="section panel">
				<p>This page will display all of the available shortcodes that you can use on your WordPress blog.</p>
        	<table class="widefat importers">
        		<tr><td><strong>Shortcodes</strong></td></tr>
        <?php

	        foreach($shortcode_tags as $code => $function)
	        {
	        	?>
	        		<tr><td>[<?php echo $code; ?>]</td></tr>
	        	<?php
	        }
	    ?>

			</table>
			<table class="widefat importers">
				<tr><td>
					<h1>Using a foreach loop with our multidimensional array</h1>
					<ol>
				<?php global $kjm_cats;
					for ($row = 0; $row < 99; $row++) {
								echo "<li> Row #$row";
								echo "<ul>";
								foreach($kjm_cats[$row] as $key => $value) {
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