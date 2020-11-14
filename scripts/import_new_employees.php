<?php
#employee_register_apache_bangalore.csv
#employee_register_sequence.csv

include '../lib/db_connect.php';
error_reporting( error_reporting() & ~E_NOTICE );

//$empno_mapping = getEmpNo2IDs();

//print_r($empno_mapping);
$leave_data = $argv[1];
if($leave_data == 'employee_register_apache_bangalore1.csv'){
	$location = getLocation('Bangalore');
}
else{
	$location= getLocation('Noida');
}

$fh = fopen($argv[1], "r");
    while($line = fgets($fh)){
        $line = rtrim($line);
        //echo "$line\n";
	if(isset($line))
        $fields = explode("\t", $line);
	print_r($fields);
	//if(isset($fields[0])){
	$data = array($fields[0],$fields[1]);		
	$emp_id = addEmployee($data,$location);
	//}

    }
fclose($fh);

function addEmployee($fields,$location){
    $insert = sprintf("INSERT INTO fi_emp_list
        (`username`, `cname`,`location`, `status`)
        VALUES('%s', '%s', '%s', 1)",
        mysql_real_escape_string($fields[0]),
        mysql_real_escape_string($fields[1]),
	mysql_real_escape_string($location)
        );
    mysql_query($insert);// or die(mysql_error(). $insert);
    return mysql_insert_id();
    //echo $insert."\n";
}

function getLocation($city){
    $select = "SELECT id FROM fi_office_locations WHERE location='$city'";
    $res = mysql_query($select) or die(mysql_error().$select);
    $row = mysql_fetch_assoc($res);
    $office_location = $row['id'];
    return $office_location;
}	

function getEmpNo2IDs(){
    $select = "SELECT id, empno FROM fi_emp_list";
    $res = mysql_query($select) or die(mysql_error().$select);
    $emp_nos = array();
    while($row = mysql_fetch_assoc($res)){
        $emp_nos[$row['empno']] = $row['id'];
    }
    return $emp_nos;
}
