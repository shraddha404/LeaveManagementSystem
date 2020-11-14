<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';

//$u = new Manager($_SESSION['user_id']);
$user_details = $u->getUserDetails($_SESSION['user_id']);
if(!empty($_POST)){
     
         if($_POST['submit'] =='Approve the leave' && !empty($_POST['leave_id']))
               {
			$approve_leave = $u->approveLeave($_POST['leave_id'], $_POST['manager_comment']);
			if(!($approve_leave)){
		        $msg = $u->error;
			}
			else{
			$msg = '<font color="green">Leave has been Approved.</font>';
		          }
               }
     
	if($_POST['submit'] =='Cancel the leave' && !empty($_POST['leave_id'])){
        	$cancel_leave = $u->cancelLeave($_POST['leave_id'], $_POST['manager_comment']);
        	if(!($cancel_leave)){
                $msg = $u->error;
        	}
		else{
		$msg = '<font color="red">Leave has been Cancelled.</font>';
		}
        }
	if($_POST['submit'] =='Cancel CompOff' && !empty($_POST['comp_off_id'])){
        	$cancel_leave = $u->cancelCompOff($_POST['comp_off_id'], $_POST['manager_comment']);
        	if(!($cancel_leave)){
                $msg = $u->error;
        	}
		else{
		$msg = '<font color="red">Compensatory off has been Cancelled.</font>';
		}
        }
        if($_POST['submit'] =='Cancel' && !empty($_POST['leave_id_Withoutpay'])){
        	$cancel_leave = $u->cancelWithoutpay($_POST['leave_id_Withoutpay'], $_POST['manager_comment']);
        	if(!($cancel_leave)){
                $msg = $u->error;
        	}
		else{
		$msg = '<font color="red">Withoutpay Leave has been Cancelled.</font>';
		}
        }
	if($_POST['submit'] =='Approve CompOff' && !empty($_POST['comp_off_id'])){
        	$cancel_leave = $u->approveCompOff($_POST['comp_off_id'], $_POST['manager_comment']);
        	if(!($cancel_leave)){
                $msg = $u->error;
        	}
		else{
		$msg = '<font color="red">Compensatory off has been Approved.</font>';
		}
        }
if($_POST['submit'] =='Approve holiday work' && !empty($_POST['comp_off_id'])){
        	$cancel_leave = $u->approveCompOff($_POST['comp_off_id'], $_POST['manager_comment']);
        	if(!($cancel_leave)){
                $msg = $u->error;
        	}
		else{
		$msg = '<font color="red">Approve holiday work  has been Approved.</font>';
		}
        }
           if($_POST['submit'] =='Approve' && !empty($_POST['leave_id_Withoutpay'])){
        	$approve_leave = $u->approveLeaveWithoutPay($_POST['leave_id_Withoutpay']);
        	if(!($approve_leave)){
                $msg = $u->error;
        	}
		else{
		$msg = '<font color="red">Withoutpay Leave has been Approved.</font>';
		}
        }
}

$reports = $u->getMyReports();
$leave_cancellation_report = $u->getMyReportsLeaveCancelRequest();
$reportsWithoutpay = $u->getReportsWithoutpay();//manger class

//$compoff = $u->getEmployeeCompOff();
$users = $u->getMyUsers($_SESSION['user_id']);
$coff_applications = $u->getCompOffApplications();

$todays_date = date("Y-m-d");
$today = strtotime($todays_date); 
$fut = strtotime((date('Y-m-d', $today).' +1Month'));
$fut_date=date("Y-m-d",$fut);
?>
<html>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/header.php'; ?>

<title>Online Leave Application - Fluent India</title>
<link rel="stylesheet" href="include/default.css" type="text/css">
<body>
<br>
<h3>Leave Applications</h3>
<?php echo $msg;?>
<br />
<table  class="adminlist" cellpadding="3">
<tr>
<th class="title"  >Sr.No</th>
<th class="title" width="35%" >Employee name</th>
<th class="title" width="25%" >Leave From Date</th>
<th class="title" width="25%" >Leave To Date</th>
<th class="title" width="25%" >Type of Leave</th>
<th class="title" width="5%" >Days</th>
<th class="title" width="35%" >Comment</th>
<th class="title" width="35%" >Attachments</th>
<th class="title" width="35%" >Leave Status</th>
<th class="title" width="35%" >Request For Cancellation</th>
</tr>
<?php
$k=1;
	if(!empty($reports)){
		$leave_types = $u->getLeaveTypes();
		foreach($reports as $row){
		$medical_certificate = $u->getMedicalCertificates($row['id']);
			$empdetails = $u->getUserDetails($row['emp_id']);
?>
			<form name="approveleave" method="post" action="">
			<input type="hidden" name="leave_id" value="<?php echo $row['id'];?>">			
<input type="hidden" name="leave_type" value="<?php echo $leave_types[$row['leave_type']];?>">
<?php
			echo "<tr class='$tr'>
			<td>".$k++."</td>
			<td><a href='viewempdetails.php?emp_id=".$row['emp_id']."'>".ucfirst($empdetails['cname'])."</a></td>
			<td>".date('d-M-Y',strtotime($row['from_dt']))."</td>
			<td>".date('d-M-Y',strtotime($row['to_date']))."</td>
			<td>".$leave_types[$row['leave_type']]."</td>
			<td>".$row['leave_days']."</td>
			<td><textarea name=\"manager_comment\">".$row['manager_comment']."</textarea></td>
			<td>"; 
			if(!empty($medical_certificate)){ echo "<a href='uploads/".$medical_certificate."' target='_blank'>Download</a>"; } echo "</td>";
		
			 echo "<td><input type='submit' name='submit' value='Approve the leave' onclick=\"return confirm('Okay to mark all pending leaves as aproved?')\"></td>
			<td><input type='submit' name='submit' value='Cancel the leave'>"; 
			if($row['leave_cancelled'] == 1 && $row['status'] != 'Cancelled'){ echo "Leave cancellation request received.";} echo "</td>";
		echo "</tr>"; 	
?>              
			</form>
<?php
		}
	}
	else
{
	 echo "<tr><td colspan='10'><div align='center'><b> No Data Present. </b></div></td></tr>";
	}

	?>

	</table>
<br/>


<table cellspacing="2" cellpadding="3" width = "700" border = "0">
<tr>
<td align="center">
<!-- Include the stylesheet -->
    <link type="text/css" rel="stylesheet" href="include/calendar_style.css"/>
    <!-- Include the jQuery library -->
    <script src="jquery.min.js"></script>
 <div id="calendar_div">
     <?php 
	include_once($_SERVER['DOCUMENT_ROOT'].'/calfunction.php');
	?>
    </div>
<br>

</td>
</tr>
</table>

<h3>WithOut Pay Leave Applications</h3>
<table  class="adminlist" cellpadding="3">
<tr>
<th class="title"  >Sr.No</th>
<th class="title" width="35%" >Employee name</th>
<th class="title" width="25%" >Leave From Date</th>
<th class="title" width="25%" >Leave To Date</th>
<th class="title" width="5%" >Days</th>
<th class="title" width="35%" colspan="2">Leave Status</th>
</tr>
<?php
$k=1;
	if(!empty($reportsWithoutpay)){
		$leave_types = $u->getLeaveTypes();
		foreach($reportsWithoutpay as $rowWithoutpay){
			$empdetails = $u->getUserDetails($rowWithoutpay['emp_id']);
			if($u->isHR())
			{
                          //echo "hiiiii";
			$a="<input type='submit' name='submit' value='Approve'>";

			}
?>
			<form name="approveWithoutpayleave" method="post" action="">
			<input type="hidden" name="leave_id_Withoutpay" value="<?php echo $rowWithoutpay['id'];?>">			
                        <input type="hidden" name="leave_type" value="Withoutpay">
<?php
			echo "<tr class='$tr'>
			<td>".$k++."</td>
			<td><a href='viewempdetails.php?emp_id=".$rowWithoutpay['emp_id']."'>".ucfirst($empdetails['cname'])."</a></td>
			<td>".date('d-M-Y',strtotime($rowWithoutpay['from_dt']))."</td>
			<td>".date('d-M-Y',strtotime($rowWithoutpay['to_date']))."</td>

			<td>".$rowWithoutpay['days']."</td>

			<td>".$a."</td>
			<td><input type='submit' name='submit' value='Cancel'></td>
			</tr>"; 	
?>
			</form>
<?php
		}
	}
	else
	{
	 echo "<tr><td colspan='9'><div align='center'><b> No Data Present. </b></div></td></tr>";
	}

	?>
	</table>

<h3>Compensatory off applications</h3>
<table  class="adminlist" cellpadding="3">
<tr>
<th class="title"  >Sr.No</th>
<th class="title" width="35%" >Employee name</th>
<th class="title" width="25%" >Worked on</th>
<th class="title" width="25%" >C-off date</th>
<th class="title" width="35%" >Comment</th>
<th class="title" width="35%"colspan="2" >Status</th>
<th class="title" width="35%" >Request For Cancellation</th>
</tr>
<?php
$k=1;
	if(count($coff_applications)>0){
		foreach($coff_applications as $row){
			$empdetails = $u->getUserDetails($row['emp_id']);
?>
			<form name="approveoff" method="post" action="">
			<input type="hidden" name="comp_off_id" value="<?php echo $row['id'];?>">
<?php
			echo "<tr class='$tr'>
			<td>".$k++."</td>
			<td><a href='viewempdetails.php?emp_id=".$row['emp_id']."'>".ucfirst($empdetails['cname'])."</a></td>
			<td>".date('d-M-Y',strtotime($row['work_date']))."</td>
			<td>"; if(!empty($row['compoff_date']) && $row['compoff_date'] != '0000-00-00' && !is_null($row['compoff_date'])){ echo date('d-M-Y',strtotime($row['compoff_date']));} else { echo "&nbsp;"; } echo "</td>
			<td><textarea name=\"manager_comment\">".$row['manager_comment']."</textarea></td>

			<td>"; 
if(!empty($row['compoff_date']) && $row['compoff_date'] != '0000-00-00' && !is_null($row['compoff_date']))
{ echo "<input type='submit' name='submit' value='Approve CompOff' onclick=\"return confirm('Okay to mark all pending leaves as aproved?')\">";
} 
else { echo "<input type='submit' name='submit' value='Approve holiday work' onclick=\"return confirm('Okay to mark all pending leaves as aproved?')\">";  } 

echo "</td>


			<!--td><input type='submit' name='submit' value='Approve CompOff' onclick=\"return confirm('Okay to mark all pending leaves as aproved?')\"></td-->



			<td><input type='submit' name='submit' value='Cancel CompOff'></td><td>";
			if($row['compoff_cancelled'] == 1 && $row['status'] != 'Cancelled'){ echo "Requested to Cancel the CompOff.";} echo "</td>
			</tr>"; 	
?>
			</form>
<?php
		}
	}
	else
	{
	 echo "<tr><td colspan='9'><div align='center'><b> No Data Present. </b></div></td></tr>";
	}

	?>
	</table>

<h3>Employee List:</h3>
<table class="adminlist" cellpadding="3">
<tr>
<th class="title">Sr.No</th>
<th class="title" width="35%" >Employee name</th>
<th class="title" width="35%" >Leave Report</th>
</tr>
<?php
$i=1;
	if(!empty($users)){
		foreach($users as $row){
?>
		<tr class='$tr'><td><?php echo $i++; ?></td><td><?php echo $row['cname'];?></td><td><a href="viewempdetails.php?emp_id=<?php echo $row['id'];?>">Leave Report</a></td></tr>
<?php
		}
	}
?>
</table>
    



<? include ("footer.php");?>
