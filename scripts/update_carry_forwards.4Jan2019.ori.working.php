<?php

include_once "../lib/HR.class.php";

$year = $argv[1];
$next_year = $year+1;

echo "Updating carry forwards for $next_year from balances of $year\n";

$u = new HR(1);
//$leave_balances = $u->getBucketsAndBalancesOfYear($year);

$leave_types = $u->getLeaveTypes();

$lt = array_flip($leave_types);

$leave_counts = $u->getLeaveCountsOfAll($year);
$all_employees = $u->getAllEmployees();
//print_r($all_employees);
//exit;

//print_r($leave_types);
//print_r($leave_counts);

$c = array();

//foreach($leave_counts as $eid=>$count_ar){
foreach($all_employees as $employee){
//print_r($employee);
	try{
$eid = $employee['id'];
$count_ar=$leave_counts[$eid];
//print_r($count_ar);
//exit;
	$e = new Employee($eid);
	$buckets = $e->getYearLeaves(array('year'=>$year));
	$old_carry_forwards = $e->getLeavesCarriedForward(array('year'=>$year));
	/*
	echo $eid." ".$employee['cname']."\n";	
	echo "Buckets\t".(int)$buckets[$lt['Earned']]."\t".(int)$buckets[$lt['Sick']]."\n";
	echo "Availed\t".(int)$count_ar[$lt['Earned']]."\t".(int)$count_ar[$lt['Sick']]."\n";
	echo "Old carry forwards\t".(int)$old_carry_forwards[$lt['Earned']]."\t".(int)$old_carry_forwards[$lt['Sick']]."\n";

	echo "New Carry forwards \n";
	*/
	
	$c[$eid]['Earned'] = (int)$buckets[$lt['Earned']] - (int)$count_ar[$lt['Earned']];
	if($c[$eid]['Earned'] > 7) $c[$eid]['Earned'] = 7;
	$c[$eid]['Earned'] += (int)$old_carry_forwards[$lt['Earned']];
	if($c[$eid]['Earned'] > 60) $c[$eid]['Earned'] = 60;

	$c[$eid][$lt['Earned']] = $c[$eid]['Earned'];

	$c[$eid]['Sick'] = (int)$buckets[$lt['Sick']] - (int)$count_ar[$lt['Sick']];
	$c[$eid]['Sick'] += (int)$old_carry_forwards[$lt['Sick']];
	#if($c[$eid]['Sick'] > 30) $c[$eid]['Sick'] = 60;	## Old code	
	if($c[$eid]['Sick'] > 30) $c[$eid]['Sick'] = 30;	## Change done on 2Jan2018 for max sick leaves carry forward is 30

	$c[$eid][$lt['Sick']] = $c[$eid]['Sick'];

	//echo $c[$eid]['Earned']."\t".$c[$eid]['Sick']."\n";
	}
	catch(Exception $er){
		//echo $er->getMessage();	
	}	
	
}

//print_r($c);

//Actual update to the db
$u->deleteCarryForwardsOfYear($next_year);

foreach($c as $eid=>$carry_forwards){
	foreach($carry_forwards as $k=>$days){
		if(!is_int($k)) continue;
		$data = array('year'=>$next_year, 'emp_id'=>$eid, 'no_of_leaves'=>$days, 'leave_type_id' => $k);
		$u->addCarryForwardLeaves($data);
	}
}
