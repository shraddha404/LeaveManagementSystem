<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
include ("header.php");
if($u->isHR())
{

	if($_REQUEST['asubmit']=="Add")
	{
		$u->addLeaveType($_POST);
	}
	else if($_REQUEST['asubmit'] == 'Delete'){
		$u->deleteLeaveType($_POST['type_id']);
	}
	$leavetypes = $u->getLeaveTypes();	
	?>
	<h3><u>Manage Leave Types : </u></h3>
		<table class="adminlist" cellpadding="3">
		<form name="form1" method="post" action="manage_leave_types.php">
			<tr>
			<td width="18%"> Leave Type :</td>
			<td><input type="text" name="typename" value="<?php echo $_GET['typename']; ?>"></td>
			</tr>
					<tr>
			<td width="18%"> &nbsp;</td>
			<td>
			<?php
			if(!$_GET['id']){
			?>
			<input type="submit" name="asubmit" value="Add">
			<?php
			}else{
			?>
			<input type="hidden" name="type_id" value="<?php echo $_GET['id']; ?>" />
			<input type="submit" name="asubmit" value="Delete">
			<?php } ?>
			</td>
			</tr>
			</form>
		</table>

		<ul>
		<?php 
		foreach($leavetypes as $type=>$typename){
			echo "<li><a href=\"manage_leave_types.php?id=$type&typename=$typename\">$typename</a></li>";
		}
		?>
		</ul>
		
<?
}else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
include ("footer.php");
?>
