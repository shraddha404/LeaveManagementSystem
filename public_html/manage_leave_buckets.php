<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
include ("header.php");
if($u->isHR())
{
if(empty($_GET)){
$current_year = date('Y');
$next_year = $current_year + 1;
}
else{
$current_year = $_GET['year'];
if($current_year == date('Y')){
$next_year = $current_year + 1;
}
}

	if($_REQUEST['asubmit']=="Update")
	{
		$u->updateLeaveBuckets($_POST);
		header("Location:/manage_leave_buckets.php?year=".$_POST['year']);
	}
	else if($_REQUEST['asubmit'] == 'Delete'){
		$u->deleteLeaveBuckets($_POST['leave_type_id'],$_POST['year']);
		header("Location:/manage_leave_buckets.php?year=".$_POST['year']);
	}
	//$leavebuckets = $u->getLeaveBuckets(date('Y'));	
	$leavebuckets = $u->getLeaveBuckets($current_year);	
	$leave_types = $u->getLeaveTypes();
	?>
	<h3><u>Manage Leave Buckets : </u></h3>
		Year:
		<select name="yearlist" onchange="window.location.href='manage_leave_buckets.php?year='+this.value;">
		<?php
			$y = date('Y');
			//$y = $current_year;
			$selected = '';
			while($y>=2014){
			if($y == $current_year){
				$selected = 'selected';
			}
			else{
				$selected = '';
			}
				echo "<option value=\"$y\"".$selected.">$y</option>";
				$y--;
			}
		?>
		</select>
		<table class="adminlist" cellpadding="3">
			<tr>
			<td width="18%"> Leave Type </td>
			<td width="18%"> Maximum Leave Days </td>
			<td width="18%"> Update </td>
			</tr>
		<?php 
			$year_num = 0;
			foreach($leave_types as $id=>$type){
			if($type == 'Maternity' || $type == 'Withoutpay' || $type == 'Bereavement') continue;
			$i = 0;
		?>

		<form name="form1" method="post" action="manage_leave_buckets.php">
			<tr>
			<td><?php echo $type; ?></td>
			<td><input type="text" name="maximum" value="<?php foreach($leavebuckets as $bucket){if($bucket['leave_type_id'] == $id){ echo $bucket['maximum'];}}?>"> Days</td>
			<input type="hidden" name="leave_type_id" value="<?php echo $id; ?>" />
			<td>
			<input type="submit" name="asubmit" value="Update">
			<input type="submit" name="asubmit" value="Delete">
			<input type="hidden" name="year" id="year" value="<?php echo $current_year;?>" />
			</td>
			</tr>
			</form>
		<?php 
		$i++;
		} ## foreach of $leave_types ends ?>
		</table>
<?php
}else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
include ("footer.php");
?>
