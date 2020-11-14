<?php
include '../lib/db_connect.php';

$datafile = $argv[1];

$fh = fopen($datafile, "r");
while(!feof($fh)){
    $line = fgets($fh);
    //echo $line."\n";
    $fields = explode("\t", $line);
    //echo "$fields[2]\t$fields[3]\t$fields[4]\t$fields[5]\t$fields[6]\t$fields[11]\n";
//print_r($fields);
    $emp_id = addEmployee($fields);
}
fclose($fh);


function addEmployee($data){
	$insert = sprintf("INSERT INTO fi_emp_list
		(`username`, `empno`, `cname`)
	VALUES('%s', '%s', '%s')",
	mysql_real_escape_string($data[1]),
	mysql_real_escape_string($data[0]),
	mysql_real_escape_string($data[2]));
	
	mysql_query($insert) or die(mysql_error(). $insert);
}
