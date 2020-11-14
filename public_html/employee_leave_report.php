<?php
include_once "../lib/db_connect.php";
include_once "../lib/lib.php";

if(!empty($_POST)){
$year=$_POST['year'];
}
else{
$year = date('Y');
}

$filename=date('Y-m-d')."_new_csv_employee_leave_report.csv";
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
$criteria = array('emp_id'=>$report['id']);

foreach($leave_types as $leave=>$typename){
	//if($typename == 'Maternity' && $approved_leaves[$k]['leaves'] == 0){
         //       continue;
if($typename == 'Withoutpay')
{
 $emp_record = getEmpwithoutpayallLeaveBalancenew($report['id'],$typename);	
}
else
{
   $emp_record = getEmpallLeaveBalancenew($report['id'],$typename);	
}
foreach($emp_record as $emp){     //} 
//print_r($emp);
$content .= stripslashes($report['id']). ',';
$content .= stripslashes($report['empno']). ',';
$content .= stripslashes($report['cname']). ',';
$content .= stripslashes($typename). ',';
	if(!empty($emp['from_dt'])){$fdt = date_format(date_create($emp['from_dt']), 'd-M-Y');} else{ $fdt = "---";}
$content .= stripslashes($fdt). ',';
	if(!empty($emp['to_date'])){$tdt = date_format(date_create($emp['to_date']), 'd-M-Y');} else{ $tdt = "---";}
$content .= stripslashes($tdt). ',';
	if(!empty($emp['leave_days'])){$ld = $emp['leave_days'];} else{ $ld = "---";}
$content .= stripslashes($ld). ',';
if($typename == 'Withoutpay')
{
      if($emp['approved']=='1'){$st = "Approved";} else{ $st = "Pending";}
        $content .= stripslashes($st). ',';

}
else{

	if(!empty($emp['status'])){$st = $emp['status'];} else{ $st = "---";}
         $content .= stripslashes($st). ',';
    }

	if(!empty($emp['location'])){$lo = $emp['location'];} else{ $lo = "---";}
$content .= stripslashes($lo). ',';
	if(!empty($emp['applied'])){$apd = date_format(date_create($emp['applied']), 'd-M-Y');} else{ $apd = "---";}
$content .= stripslashes($apd). ',';
	if(!empty($emp['approved_date'])){$appd = date_format(date_create($emp['approved_date']), 'd-M-Y');} else{ $appd = "---";}
$content .= stripslashes($appd);
$content .= "\n";
}
}
//$i++;//}
//}#if of emp_no_list ends
}#foreach ends
$title .= "Id, Employee Number, Employee name,Leave type, Start Date,End Date, Number Of Days, Status, Office Location,Application Date,Leave approval date"."\n";
echo $title;
echo $content;
     
?>
