<?php
include_once "../lib/db_connect.php";
include_once "../lib/lib.php";
$filename=date('Y-m-d')."_csv_org_report.csv";
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");
$content = '';
$title = '';
$leave_types = getLeaveTypes();
if(!empty($_POST)){
	if($_POST['submit'] == 'Apply for org leave'){
$from_dt=$_POST['from_dt'];
$end_dt=$_POST['end_dt'];

$latest_report=getEmpOrgLeaveBalance($from_dt,$end_dt);
$i=0;

foreach($latest_report as $report){
#$tmp = $report['emp_id']."".$report['leave_type'];
#if(!in_array($tmp,$emp_no_list)){
#$emp_no_list[] = $report['emp_id']."".$report['leave_type'];

$leavetaken = 0;//initialize
$criteria = array('emp_id'=>$report['empno'],'year'=>date('Y'));
if(!empty($report['empno'])){
$leave_for_year =getEmployeesYearLeaves($criteria,$report['empno'],$report['leave_type']);
}
foreach($leave_for_year as $k=>$v){
	 $leaves_for_year= $v;
}

$leaves_carry_forward =getEmpLeavesCarriedForward($criteria,$report['empno'],$report['leave_type']);
$leaves_taken = getEmployeeLeaveRecord($criteria,$report['empno'],$report['leave_type']);
 $approved_leaves = getEmployeeApprovedLeaves($criteria,$report['empno'],$report['leave_type']);

$leaves_carry_forward =getEmpLeavesCarriedForward($criteria,$report['empno'],$report['leave_type']);

foreach($leaves_carry_forward as $k=>$v){
        if($k == $report['leave_type']){$leaves_carry_forwards += $v;}
} 

$leavetaken=0;
foreach($leaves_taken as $v){
	if($v['status'] == 'Approved' && $leave_types[$v['leave_type']] != 'Maternity'&& $leave_types[$v['leave_type']] == $report['typename'])
        echo $leavetaken += $v['leave_days'];
}

foreach($leave_types as $k=>$v){
if($v == $report['typename']){
 $ly=(int)$leave_for_year[$k];
 $lcf=(int)$leaves_carry_forward[$k];
 $lt=(int)$approved_leaves[$k]['leaves'];
$balance=$ly+$lcf-$lt;
}
}
$content .= stripslashes($report['id']). ',';
$content .= stripslashes($report['empno']). ',';
$content .= stripslashes($report['cname']). ',';
$content .= stripslashes($report['username']). ',';
$content .= stripslashes($report['typename']). ',';
$content .= stripslashes(date('d-M-Y',strtotime($report['from_dt']))). ',';
$content .= stripslashes(date('d-M-Y',strtotime($report['to_date']))). ',';
$content .= stripslashes($report['leave_days']). ',';
$content .= stripslashes($report['status']). ',';
$content .= stripslashes($report['location']). ',';

$content .= "\n";
$i++;
#}#if of emp_no_list ends
}#foreach ends

$title .= "Id, Employee id, Employee name,Employee UserName, Leave type, Start date, End date, Days, Status, Office Location"."\n";
echo $title;
echo $content;
     }

}
?>
