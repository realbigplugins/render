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
		global $usl_cats;
		global $usl_codes;
        ?>
        <div class="wrap">
        	<div id="icon-options-general" class="icon32"><br /></div>
			<h2>View All Available Shortcodes</h2>
			<div class="section panel">
				<p>This is where you can view all the amazing shortcodes we gave you.</p>
			<table class="widefat importers">
			<?php foreach($usl_cats as $element) {
				echo "<tr><td>";
				echo "<h3>$element</h3>";
				echo "<ul>";
				foreach($usl_codes as $row) {
				if($row["Category"] === $element) {
					$usl_title=$row['Title'];
					$usl_desc=$row['Description'];
					$usl_code=$row['Code'];
					$usl_example=$row['Example'];
					$usl_atts=$row['Atts']; ?>
					<li>
						<h4><?php echo $usl_title; ?></h4>
						<b>Shortcode: </b><code>[<?php echo $usl_code; ?>]</code><br/>
						<?php if(!empty($usl_atts)) { ?><b>Attributes: </b><?php echo $usl_atts; ?><br/><?php } ?>
						<b>Description: </b><?php echo $usl_desc; ?><br/>
						<?php if(!empty($usl_example)) { ?><b>Example: </b><?php echo $usl_example; ?><?php } ?>
					</li>
				<?php }
				echo "</ul>";
			} ?>
			</td></tr>
	<?php	} ?>

			</table>
			</div>
		</div>
		<?php
	}
} // END class
?>