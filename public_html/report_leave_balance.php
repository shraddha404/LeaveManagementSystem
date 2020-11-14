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

$leaves = $u->getLeaveCountsOfAll($year);

//echo $leaves[210][6];
//exit;
$carry_forwards = $u->getLeaveCarryForwardsOfAll($year);

$all_employees = $u->getEmployeeReports(array(), -1);

$leave_types = $u->getLeaveTypes();

$types_leave = array_flip($leave_types);

header('Content-type: application/tsv');
header('Content-Disposition: attachment; filename="LeavesBalance.tsv"');

echo "Report as on ".date('m/d/Y')."\n";
echo "Emp no\tName\tDepartment\tOffice location\tEarned Balance\tSick Balance\tCasual Balance\n";

foreach($all_employees as $i=>$info){
    $eid = $info['id'];
	if($eid <= 0){
	continue;
	}
	try{
	$e = new Employee($eid);
	}
	catch(Exception $e){
		//do nothing
		//echo $e->getMessage();
		continue;
	}
	$buckets = $e->getYearLeaves(array('year'=>$year));

	$earned_balance = ($buckets[$types_leave['Earned']]  
        - $leaves[$eid][$types_leave['Earned']]
        + $carry_forwards[$eid][$types_leave['Earned']]) ;
	$sick_balance = ($buckets[$types_leave['Sick']] 
        - $leaves[$eid][$types_leave['Sick']]
        + $carry_forwards[$eid][$types_leave['Sick']]) ;
	$casual_balance = ($buckets[$types_leave['Casual']] 
        - $leaves[$eid][$types_leave['Casual']]
        + $carry_forwards[$eid][$types_leave['Casual']]) ;

	echo $info['empno']."\t".
	$info['cname']."\t".
    $info['deptname']."\t".
    $info['location']."\t".
	$earned_balance."\t".$sick_balance."\t".$casual_balance."\n";
}
