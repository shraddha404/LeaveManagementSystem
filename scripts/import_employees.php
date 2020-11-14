<?php
include_once "../lib/db_connect.php";
include_once "../lib/User.class.php";
$datafile = $argv[1];

$u = new User();
$office_locations = $u->getOfficeLocations();
//print_r($office_locations);
$location_ids = array();
foreach($office_locations as $o_l){
    $location_ids[$o_l['location']] = $o_l['id'];
}
//print_r($location_ids);
$departments = $u->getDept();
//print_r($departments);
$dept_ids = array();
foreach($departments as $d){
    $dept_ids[$d['deptname']] = $d['id'];
}
//print_r($dept_ids);

// get Leave-type ids
$leave_types = $u->getLeaveTypes();
$flipped_types = array_flip($leave_types);
//print_r($flipped_types);

$fh = fopen($datafile, "r");
while(!feof($fh)){
    $line = fgets($fh);
    //echo $line."\n";
    $fields = explode("\t", $line);
    //echo "$fields[2]\t$fields[3]\t$fields[4]\t$fields[5]\t$fields[6]\t$fields[11]\n";
    $data = array($fields[2], $fields[3], $fields[4], $dept_ids[$fields[5]], $location_ids[$fields[6]]); 
    $emp_id = addEmployee($data);
    addCarryForward($emp_id, date('Y'), $flipped_types['Earned'], $fields[11]);
}
fclose($fh);


function addEmployee($fields){
    $insert = sprintf("INSERT INTO fi_emp_list
        (`empno`, `username`, `cname`, `dept`,`location`, `status`)
        VALUES('%s', '%s', '%s', '%s', '%s', 1)",
        mysql_real_escape_string($fields[0]),
        mysql_real_escape_string($fields[1]),
        mysql_real_escape_string($fields[2]),
        mysql_real_escape_string($fields[3]),
        mysql_real_escape_string($fields[4]));
    mysql_query($insert) or die(mysql_error(). $insert);
    return mysql_insert_id();
    //echo $insert."\n";
}

function addCarryForward($emp_id, $year, $leave_type_id, $carry_forward){
    $insert = sprintf("INSERT INTO fi_leave_carry_forwards 
        VALUES('%s', '%s', '%s', '%s')",
        mysql_real_escape_string($emp_id),
        mysql_real_escape_string($year),
        mysql_real_escape_string($leave_type_id),
        mysql_real_escape_string($carry_forward));
    mysql_query($insert) or die($insert . mysql_error());
}
