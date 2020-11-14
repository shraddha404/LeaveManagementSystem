<?php
include "../lib/db_connect.php";

$employees = dumpAll();
//print_r($employees);
foreach($employees as $e){
	echo $e['username']."\n";
}

function dumpAll(){
	$select = "SELECT username FROM fi_emp_list";
	$res = mysql_query($select);
	$employees = array();
	while($row = mysql_fetch_assoc($res)){
		$employees[] = $row;
	}
	return $employees;
}
