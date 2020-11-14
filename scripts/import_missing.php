<?php
include '../lib/db_connect.php';

$datafile = $argv[1];

$empno = 10000;

$fh = fopen($datafile, "r");
while(!feof($fh)){
	$empno++;
    $line = fgets($fh);
    //echo $line."\n";
    $fields = explode("\t", $line);
    $emp_id = addEmployee($empno, $fields);
}
fclose($fh);


function addEmployee($empno,$data){
	$insert = sprintf("INSERT INTO fi_emp_list
		(`username`, `empno`, `cname`)
	VALUES('%s', $empno, '%s')",
	mysql_real_escape_string($data[0]),
	mysql_real_escape_string($data[1]));
//echo $insert;	
	$res = mysql_query($insert);// or die(mysql_error(). $insert);
}
