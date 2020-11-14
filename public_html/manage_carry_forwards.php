<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
include ("header.php");
if($u->isHR())
{

	$leaves = $u->carryForwardLeaves();	
	if($_REQUEST['asubmit']=="Update")
	{
		$u->updateCarryForwardLeaves($leaves);
	}
	else if($_REQUEST['asubmit'] == 'Delete'){
		$u->deleteCarryForwardLeaves($_POST);
	}
	?>
	<h3><u>Manage Carry Forward Leaves : </u></h3>
		<form name="form1" method="post" action="manage_carry_forwards.php">
			<input type="submit" name="asubmit" value="Update">
		</form>
<!--
		<table class="adminlist" cellpadding="3">
			<tr>
			<td width="18%"> Employee Name </td>
			<td width="18%"> Carry Forward Leaves</td>
			</tr>
		<?php foreach($leaves as $user=>$details){?>
			<tr>
			<td><?php echo $user; print_r($details);?></td>
			<td>
			<table>
			<tr>
		<form name="form1" method="post" action="manage_carry_forwards.php">
			<td><?php echo $details['Sick'][1]; ?></td>
			<td><input type="text" name="no_of_leaves" value="<?php echo $details['Sick'][0]; ?>"> Days
			<input type="hidden" name="leave_type_id" value="<?php echo $details['Sick'][2]; ?>" /></td>
			<input type="hidden" name="emp_id" value="<?php echo $user; ?>" /></td>
			<td>
			<input type="submit" name="asubmit" value="Update">
			<input type="submit" name="asubmit" value="Delete">
			</td>
		</form>
			</tr>

			<tr>	
		<form name="form1" method="post" action="manage_carry_forwards.php">
			<td>
			<?php echo $details['Earned'][1]; ?></td>
			<td><input type="text" name="no_of_leaves" value="<?php echo $details['Earned'][0]; ?>"> Days
			<input type="hidden" name="leave_type_id" value="<?php echo $details['Earned'][2]; ?>" /</td>
			<input type="hidden" name="emp_id" value="<?php echo $user; ?>" /></td>
			<td>
			<input type="submit" name="asubmit" value="Update">
			<input type="submit" name="asubmit" value="Delete">
			</td>
		</form>
			</tr>
			</table>
			</td></tr>
			</form>
		<?php } ?>
		</table>
-->
<?
}else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
include ("footer.php");
?>
