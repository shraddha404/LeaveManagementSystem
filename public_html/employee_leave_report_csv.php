<?php
include_once "../lib/db_connect.php";
include_once "../lib/lib.php";

if(!empty($_POST)){
$year=$_POST['year'];
}
else{
$year = date('Y');
}

$filename=date('d-M-Y')."_new_csv_employee_leave_report.csv";
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");
$content = '';
$title = '';
//$latest_report = getEmpLeaveBalance($year);	
$latest_report = getEmployeeList();
	$leave_types = getLeaveTypes();
$i=0;
#### New Code ##########
$emp_no_list = array();


foreach($latest_report as $report){
$criteria = array('emp_id'=>$report['id'],'year'=>$year);

foreach($leave_types as $leave=>$typename){
	//if($typename == 'Maternity' && $approved_leaves[$k]['leaves'] == 0){
         //       continue;
   $emp = getEmpallLeaveBalancenew($year,$report['id'],$typename);	     //} 
//print_r($emp);
$content .= stripslashes($report['id']). ',';
$content .= stripslashes($report['cname']). ',';
$content .= stripslashes($typename). ',';
	if(!empty($emp[0]['from_dt'])){$fdt = $emp[0]['from_dt'];} else{ $fdt = "---";}
$content .= stripslashes($fdt). ',';
	if(!empty($emp[0]['to_date'])){$tdt = $emp[0]['to_date'];} else{ $tdt = "---";}
$content .= stripslashes($tdt). ',';
	if(!empty($emp[0]['leave_days'])){$ld = $emp[0]['leave_days'];} else{ $ld = "---";}
$content .= stripslashes($ld). ',';
	if(!empty($emp[0]['status'])){$st = $emp[0]['status'];} else{ $st = "---";}
$content .= stripslashes($st). ',';
	if(!empty($emp[0]['location'])){$lo = $emp[0]['location'];} else{ $lo = "---";}
$content .= stripslashes($lo). ',';
	if(!empty($emp[0]['applied'])){$apd = $emp[0]['applied'];} else{ $apd = "---";}
$content .= stripslashes($apd). ',';
	if(!empty($emp[0]['approved_date'])){$appd = $emp[0]['approved_date'];} else{ $appd = "---";}
$content .= stripslashes($appd);
$content .= "\n";
}

//$i++;//}
//}#if of emp_no_list ends
}#foreach ends
$title .= "Employee Number, Employee name,Leave type, Start Date,End Date, Number Of Days, Status, Office Location,Application Date,Leave approval date"."\n";
echo $title;
echo $content;
     
?>
