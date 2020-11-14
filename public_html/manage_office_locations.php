<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
include ("header.php");

	if($_REQUEST['asubmit']=="Add" && !empty($_POST['location']))
	{
		$u->addOfficeLocation($_POST);
	}
	else if($_REQUEST['asubmit']=="Update")
	{
		$u->updateOfficeLocation($_POST);
	}
	else if($_REQUEST['asubmit'] == 'Delete'){
		$u->deleteOfficeLocation($_POST['loc']);
	}
	$locations = $u->getOfficeLocations();	
	?>
	<h3><u>Manage Locations : </u></h3>
		<table class="adminlist" cellpadding="3">
		<form name="form1" method="post" action="manage_office_locations.php">
			<tr>
			<td width="18%"> Location :</td>
			<td><input type="text" name="location" value="<?php echo $_GET['location']; ?>"></td>
			</tr>
					<tr>
			<td width="18%"> &nbsp;</td>
			<td>
			<?php
			if(!$_GET['loc']){
			?>
			<input type="submit" name="asubmit" value="Add">
			<?php
			}else{
			?>
			<input type="hidden" name="loc" value="<?php echo $_GET['loc']; ?>" />
			<input type="submit" name="asubmit" value="Update">
			<input type="submit" name="asubmit" value="Delete">
			<?php } ?>
			</td>
			</tr>
			</form>
		</table>

		<ul>
		<?php 
		foreach($locations as $l){
			echo "<li><a href=\"manage_office_locations.php?loc=$l[id]&location=$l[location]\">$l[location]</a></li>";
		}
		?>
		</ul>
		
<?
include ("footer.php");
?>
