<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
include ("header.php");
if($u->isHR())
{
	if($_REQUEST['asubmit']=="Add" && !empty($_POST['ou_short_name']))
	{
		$u->addOrgUnit($_POST);
	}
	else if($_REQUEST['asubmit']=="Update")
	{
		$u->updateOrgUnit($_POST);
	}
	else if($_REQUEST['asubmit'] == 'Delete'){
		$u->deleteOrgUnit($_POST['ou']);
	}
	$org_units = $u->getOUs();	
	?>
	<h3><u>Manage Org Units : </u></h3>
		<table class="adminlist" cellpadding="3">
		<form name="form1" method="post" action="manage_org_units.php">
			<tr>
			<td width="18%"> Org Unit :</td>
			<td><input type="text" name="ou_short_name" value="<?php echo $_GET['ou_short_name']; ?>"></td>
			</tr>
			<tr>
			<td width="18%"> LDAP string :<br/> Take from your System administrator</td>
			<td><input type="text" name="ou_long_string" value="<?php echo $_GET['ou_long_string']; ?>"></td>
			</tr>
					<tr>
			<td width="18%"> &nbsp;</td>
			<td>
			<?php
			if(!$_GET['ou']){
			?>
			<input type="submit" name="asubmit" value="Add">
			<?php
			}else{
			?>
			<input type="hidden" name="ou" value="<?php echo $_GET['ou']; ?>" />
			<input type="submit" name="asubmit" value="Update">
			<input type="submit" name="asubmit" value="Delete">
			<?php } ?>
			</td>
			</tr>
			</form>
		</table>

		<ul>
		<?php 
		foreach($org_units as $o){
			echo "<li><a href=\"manage_org_units.php?ou=$o[id]&ou_short_name=$o[ou_short_name]&ou_long_string=$o[ou_long_string]\">$o[ou_short_name]</a></li>";
		}
		?>
		</ul>
		
<?php
}else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
include ("footer.php");
?>
