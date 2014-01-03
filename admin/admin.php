<?php
/*
This would be a great file for adding code that you want run in the backend. For example if you want to
create an admin or a settings page, this would be the appropriate place to work.
*/

//put your code here

add_action("admin_menu","starter_menu_fcn");

function starter_menu_fcn()
{
	add_menu_page("PageTitle","MenuTitle","manage_options","starter-plugin.php", "menu_body_fcn");
}

function menu_body_fcn()
{
/*	
 * THE FOLLOWING WILL YIELD A SIMPLE TABLE THAT HOUSES A RADIO ARRAY THAT WILL ALLOW THE SELECTION OF VALUES
 * FOR THE OPTION var1
 * THE OPTION VALUE IS SET/CREATED BY THE UPDATE_OPTION FUNCTION WHICH CREATES OPTIONS IF THEY DON'T EXIST
 * THE OPTIONS ARE FRAMED IN A FORM THAT CAN BE SUBMITTED WITH A SUBMIT BUTTON 
 * THE SAVE OPTIONS PORTION OF THE FEATURE IS ACTIVATED BY THE isset($_POST["submitOptions"]) CLAUSE 
 * 
 */
	if(isset($_POST["submitOptions"])) //Catches the fact that the submit button has been pressed
	{
		//by using implode we can encode the selected checkboxes into a single comma delimited string.
		//You should look out for the possibility of the values having unescaped commas as this could cause problems.
		update_option("checkboxVar",implode(",",$_POST["checkboxArray"]));
		update_option("radioVar",$_POST["option1Arr"]);
		update_option("textoption", $_POST["textinput"]);
	}
	//since there is an array of values we will need an array of elements to see which checkboxes should be checked
	$checkboxarr = explode(",",	get_option("checkboxVar"));
	?>
	<!-- The checkboxes and the radio buttons both use the "checked" tag to indicate that the element is active. 
		The conditional operator in the following form is used to determine if the element is selected
			print (ISSELECTED?"checked":""); 
		this is a condensed "If" statment and ensures that the option selected in the database shows checked 
		in the table.  
		-->
	<div>
		Plugin Version : <?php print get_option("starter_plugin_version"); ?> (<b>Value created when plugin activated</b>)
		
		<form name="option-form" method="post">
		<!-- This table is going to house an array of checkboxes. The table is not important but the [] in the name of each element tells the form to handle them as an array -->
		</br>
		<table>
			<tr><th> Value </th><th>Checkbox</th></tr>
			<tr>
				<td>First Option</td>
				<td><input type="checkbox" name="checkboxArray[]" value="First Value" <?php print (in_array("First Value", $checkboxarr)?"checked":"");?> /></td>
			</tr>
			<tr>
				<td>Second Option</td>
				<td><input type="checkbox" name="checkboxArray[]" value="Second Value" <?php print (in_array("Second Value", $checkboxarr)?"checked":"");?> /></td>
			</tr>
			<tr>
				<td>Third Option</td>
				<td><input type="checkbox" name="checkboxArray[]" value="Third Value" <?php print (in_array("Third Value", $checkboxarr)?"checked":"");?> /></td>
			</tr>
		</table>
		<!-- This table holds the radio buttons, once again the table is not important but the radio buttons will need
			to have the same name so that they will be part of the same array of radio buttons -->
		</br>
		<table>
			<tr><th> Value </th><th>Radio</th></tr>
			<tr>
				<td>Val 1 </td>
				<td><input type="radio" name="option1Arr" value="Val1" <?php print (get_option("radioVar")=="Val1"?"checked":"");?> /></td>
			</tr>
			<tr>
				<td>Val 2 </td>
				<td><input type="radio" name="option1Arr" value="Val2" <?php print (get_option("radioVar")=="Val2"?"checked":"");?> /></td>
			</tr>
		</table>
		
		<!-- This textbox could in principle also be a textarea for a large blob of text -->
		</br>
		<b>Text Value</b> </br>
		<input type="text" name="textinput" value="<?php print get_option("textoption"); ?>"/>
		
		</br>
		<input type="submit" name="submitOptions" />
		</form>
	</div>
	<?php
}
?>