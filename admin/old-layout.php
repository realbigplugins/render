<?php
/**
 * Created by PhpStorm.
 * User: kylemaurer
 * Date: 9/4/14
 * Time: 11:43 AM
 */
?>
<div class="postbox-container">
	<div id="normal-sortables" class="section panel meta-box-sortables ui-sortable">
		<p>This is where you can view all the amazing shortcodes we gave you.</p>

	<?php foreach(apply_filters('usl_extend_cats', $usl_cats) as $element) { ?>
	<div id="usl_<?php echo $element; ?>" class="postbox">
		<div class='handlediv' title='Click to toggle' onclick="usl_tog_vis('<?php echo $element; ?>-inside')"><br/></div>
		<h3 class='hndle'><span><?php echo $element; ?></span></h3>
		<div id="<?php echo $element; ?>-inside" class='inside' style="display: none;">

			<?php foreach($usl_codes as $row) {
				if($row["Category"] === $element) {
					$usl_title=$row['Title'];
					$usl_desc=$row['Description'];
					$usl_code=$row['Code'];
					$usl_example=$row['Example'];
					$usl_atts=$row['Atts']; ?>
					<div class="usl_codes">
						<h4><?php echo $usl_title; ?></h4>
						<p>
							<b>Shortcode: </b><code>[<?php echo $usl_code; ?>]</code><br/>
							<?php if(!empty($usl_atts)) { ?><b>Attributes: </b><?php echo $usl_atts; ?><br/><?php } ?>
							<b>Description: </b><?php echo $usl_desc; ?><br/>
							<?php if(!empty($usl_example)) { ?><b>Example: </b><code><?php echo $usl_example; ?></code><?php } ?>
						</p>
					</div>
				<?php } } // Shortcodes loop ?>
		</div>
	</div>
<?php	} // Categories loop

	?>