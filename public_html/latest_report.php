<?php 
session_start();
error_reporting(0);
include "../lib/HR.class.php";
$u = new HR($_SESSION['user_id']);

if(!empty($_GET)){
$year = $_GET['year'];
}
else{
$year = date('Y');
}

$leaves = $u->getLatestLeavesReport($year);

header('Content-type: unknown');
header('Content-type: application/tsv');
header('Content-Disposition: attachment; filename="LeavesReport.tsv"');
echo "Report as on ".date('m/d/Y')."\n";
echo "Emp no\tName\tDepartment\tOffice location\tApplication Date\tLeave From\tLeave To\tNo Of Leaves\tLeave Type\tStatus\n";

foreach($leaves as $k=>$v){
	if($v['emp_id'] <= 0 || $v['empno'] <= 0){
	continue;
	}

	echo $v['empno']."\t".
	$v['cname']."\t".
	$v['deptname']."\t".
	$v['location']."\t".
	date_format(date_create($v['applied']), 'd-M-Y')."\t".
	date_format(date_create($v['from_dt']), 'd-M-Y')."\t".
	date_format(date_create($v['to_date']), 'd-M-Y')."\t".
	$v['leave_days']."\t".
	$v['typename']."\t".
	$v['status']."\n";
}
//exit;
