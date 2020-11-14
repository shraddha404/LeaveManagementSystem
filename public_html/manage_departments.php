<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
include ("header.php");
if($u->isHR())
{

	if($_REQUEST['asubmit']=="Add" && !empty($_POST['deptname']))
	{
		$u->addDepartment($_POST);
	}
	else if($_REQUEST['asubmit']=="Update")
	{
		$u->updateDepartment($_POST);
	}
	else if($_REQUEST['asubmit'] == 'Delete'){
		$u->deleteDepartment($_POST['deptno']);
	}
	$departments = $u->getDepartments();	
	?>
	<h3><u>Manage Locations : </u></h3>
		<table class="adminlist" cellpadding="3">
		<form name="form1" method="post" action="manage_departments.php">
			<tr>
			<td width="18%"> Department :</td>
			<td><input type="text" name="deptname" value="<?php echo $_GET['deptname']; ?>"></td>
			</tr>
					<tr>
			<td width="18%"> &nbsp;</td>
			<td>
			<?php
			if(!$_GET['deptno']){
			?>
			<input type="submit" name="asubmit" value="Add">
			<?php
			}else{
			?>
			<input type="hidden" name="deptno" value="<?php echo $_GET['deptno']; ?>" />
			<input type="submit" name="asubmit" value="Update">
			<input type="submit" name="asubmit" value="Delete">
			<?php } ?>
			</td>
			</tr>
			</form>
		</table>

		<ul>
		<?php 
		foreach($departments as $d){
			echo "<li><a href=\"manage_departments.php?deptno=$d[id]&deptname=$d[deptname]\">$d[deptname]</a></li>";
		}
		?>
		</ul>
		
<?php
}else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
include ("footer.php");
?>
