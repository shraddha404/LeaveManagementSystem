<html>
<?php
include ("../lib/HR.class.php");
include ("header.php");
session_start();
echo $_SESSION['user_id'];
if($_SESSION['user_id']==''){
        header('Location:/index.php');
}

$u = new Employee($_SESSION['user_id']);
$user_details = $u->getUserDetails($_SESSION['user_id']);
print_r($user_details);
if(!empty($_POST)){
        $insert_leave = $u->applyForLeave($_POST);
        if(!($insert_leave)){
                $msg = $u->error;
        }
}

$criteria = array('emp_id'=>$_SESSION['user_id'],'year'=>date('Y'));

$leave_for_year = $u->getYearLeaves($criteria);
foreach($leave_for_year as $k=>$v){
        $leaves_for_year += $v;
}
$leaves_carry_forward = $u->getLeavesCarriedForward($criteria);
foreach($leaves_carry_forward as $k=>$v){
        $leaves_carry_forwards += $v;
}
$leaves_taken = $u->getMyLeaveRecord(date('Y'));
foreach($leaves_taken as $v){
        $leavetaken += $v['leave_days'];
}

 $todays_date = date("Y-m-d"); 
 $today = strtotime($todays_date); 
 $fut = strtotime((date('Y-m-d', $today).' +1Month'));
	$fut_date=date("Y-m-d",$fut);
	?>

<title>Online Leave Application - Fluent India</title>
<link rel="stylesheet" href="include/default.css" type="text/css">
<body>
<br>
<table cellspacing="2" cellpadding="3" width = "700" border = "0">
<tr>
<td align="center">
<div style="font-weight: bold;" align="centre">Fluent India Pvt. Ltd. (HR Dept.)<br><br></div>
<div style="font-weight: bold;" align="centre">Leave (Vacation) Record Report From :<u> <? echo $todays_date ; ?></u> to : <u><? echo $fut_date ; ?></u></div>
</td>
</tr>
</table>
<br>
<?
	$table_head = <<< EOF
<table  class="adminlist" cellpadding="3">
<tr>
<th class="title"  >Sr.No</th>
<th class="title" width="35%" >Employee name</th>
<th class="title" width="25%" >Leave From Date</th>
<th class="title" width="25%" >Leave To Date</th>
<th class="title" width="5%" >Days</th>
</tr>
EOF;
		echo $table_head;
		$flag=1;
		$k=1;
	$result=mysql_query($sql,$con) or die(mysql_error()."<br>$sql");
	if(mysql_num_rows($result))
	{
		while($row=mysql_fetch_array($result))
		{
			if ($flag)
			{
				$tr="row1";
				$flag=0;
			}
			else
			{
				$tr="row0";
				$flag=1;
			}
		echo "<tr class='$tr'><td>".$k++."</td><td>".ucfirst($row['empid'])."</td><td>".date('d-M-Y',strtotime($row['from_dt']))."</td><td>".date('d-M-Y',strtotime($row['to_dt']))."</td> <td>".$row['no_of_days']."</td></tr>"; 	
	}
	}
	else
	{
	 echo "<tr><td colspan='9'><div align='center'><b> No Data Present. </b></div></td></tr>";
	}

	echo "</table>";
	?>
<? include ("footer.php");?>
