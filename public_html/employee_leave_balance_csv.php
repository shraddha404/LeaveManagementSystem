<?php
include_once "../lib/db_connect.php";
include_once "../lib/lib.php";

if(!empty($_POST)){
$year=$_POST['year'];
}
else{
$year = date('Y');
}

$filename=date('Y-m-d')."_csv_employee_leave_report.csv";
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");
$content = '';
$title = '';
//$latest_report = getEmpLeaveBalance($year);	
$latest_report = getnotleftEmployeeList();

$i=0;
#### New Code ##########
$emp_no_list = array();
$leave_typesarray = getLeaveTypesarray();
//print_r($latest_report);exit;
foreach($latest_report as $report){

//$tmp = $report['emp_id']."".$report['leave_type'];
//if(!in_array($tmp,$emp_no_list)){
//$emp_no_list[] = $report['emp_id']."".$report['leave_type'];

//$criteria = array('emp_id'=>$report['id'],'year'=>date('Y'));
$criteria = array('emp_id'=>$report['id'],'year'=>$year);

	$leave_for_year = getEmployeesYearLeaves($criteria,$report['id']);	
	$leaves_carry_forward = getEmpLeavesCarriedForward($criteria,$report['id']);
        $leavetaken = 0;
	//$leaves_taken = getEmployeeLeaveRecord(date('Y'),$report['id']);
	$leaves_taken = getEmployeeLeaveRecord($year,$report['id']);
	$leave_types = getLeaveTypes();

	//$approved_leaves = getEmployeeApprovedLeaves(date('Y'),$report['id']);
	$approved_leaves = getEmployeeApprovedLeaves($year,$report['id']);

	//$approved_withoutpay = getEmployeeWithoutPay(date('Y'),$report['id']);
	$approved_withoutpay = getEmployeeWithoutPay($year,$report['id']);
	

foreach($leave_types as $leave=>$typename){
	//if($typename == 'Maternity' && $approved_leaves[$k]['leaves'] == 0){
         //       continue;
        //} 
$content .= stripslashes($report['id']). ',';
$content .= stripslashes($report['empno']). ',';
$content .= stripslashes($report['cname']). ',';
$content .= stripslashes($report['username']). ',';
$content .= stripslashes($typename). ',';

		//if(!empty($leave_for_year[$report['leave_type']])){ 
		//	$lfy= $leave_for_year[$report['leave_type']];
		//}
		if(!empty($leave_for_year[$leave])){ 
			$lfy= $leave_for_year[$leave];
		}
		else{
			$lfy= '0';
		}
$content .= stripslashes($lfy). ',';

		//if(!empty($leaves_carry_forward[$report['leave_type']])){$lcf=$leaves_carry_forward[$report['leave_type']];} else{$lcf= 0;}
		if(!empty($leaves_carry_forward[$leave])){$lcf=$leaves_carry_forward[$leave];} else{$lcf= 0;}
$content .= stripslashes($lcf). ',';

		 $tla=$leaves_carry_forward[$leave] + $leave_for_year[$leave];
$content .= stripslashes($tla). ',';

		if($typename == 'Withoutpay'){
	if(!empty($approved_withoutpay['leaves'])){$al = $approved_withoutpay['leaves'];} else{ $al = 0;}
		}
		else{	
		if(!empty($approved_leaves[$leave])){$al= $approved_leaves[$leave];} else{ $al= 0;}
		}

$content .= stripslashes($al). ',';

		if($typename == 'Maternity' || $typename == 'Withoutpay' || $typename == 'Bereavement'){$balance="N/A";}
		else{
			$balance=$leaves_carry_forward[$leave] + $leave_for_year[$leave] - $approved_leaves[$leave];}
$content .= stripslashes($balance). ',';
$content .= stripslashes($report['location']);
$content .= "\n";
}

$i++;//}
//}#if of emp_no_list ends
}#foreach ends
$title .= "Id, Employee id, Employee name,Employee UserName, Leave type, Leaves for Current Year,Carried Forward Leaves, Total available, Leaves Taken,Balance Leaves, Office Location"."\n";
echo $title;
echo $content;
     
?>




