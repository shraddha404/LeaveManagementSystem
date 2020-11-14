<?php
//include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
?>
<html>
<?php //include_once $_SERVER['DOCUMENT_ROOT'].'/header.php';
//session_start();
//error_reporting(0);

//include_once $_SERVER['DOCUMENT_ROOT']."/lib/HR.class.php";
//$u = new HR($_SESSION['user_id']);
include_once "../lib/db_connect.php";
include_once "../lib/lib.php";

/*if(!empty($_GET)){
$year = $_GET['year'];
$month = $_GET['month'];
$month = date('m',strtotime($month));
}
else{
$year = date('Y');
$month = date('m');
}*/
//$days = cal_days_in_month(CAL_GREGORIAN,$month,$year);

$leaves = getLatest30daysLeavesReport();?>
<h2> Report for the month of <?php echo date('M-Y');?> </h2>
<table class="tabForm" border="1" cellpadding="1" cellspacing="1" width="80%">
<?php
if(!empty($leaves))
{?>
<tr>
<!--th>Emp no</th--><th>Name</th><!--th>Department</th--><th>Office location</th><!--th>Application Date</th--><th>Leave From</th><th>Leave To</th><th>No Of Leaves</th><!--th>Leave Type</th><th>Status</th--></tr>

<?php

foreach($leaves as $k=>$v){
	if($v['emp_id'] <= 0 || $v['empno'] <= 0){
	continue;
	}
?><tr>
	<!--td><?php echo $v['empno'];?></td-->
	<td><?php echo $v['cname'];?></td>
	<!--td><?php echo $v['deptname'];?></td-->
	<td><?php echo $v['location'];?></td>
	<!--td><?php echo date('m/d/Y',strtotime($v['applied']));?></td-->
	<td><?php echo date('d-M-Y',strtotime($v['from_dt']));?></td>
	<td><?php echo date('d-M-Y',strtotime($v['to_date']));?></td>
	<td><?php echo $v['leave_days'];?></td>
	<!--td><?php echo $v['typename'];?></td-->
	<!--td><?php echo $v['status'];?></td-->
</tr>
<?php }
} ?>
</table>

<?php
include ("footer.php");
?>
