<?php
include_once "../lib/db_connect.php";
include_once "../lib/User.class.php";
$datafile = $argv[1];

$u = new User();
$departments = $u->getDept();
//print_r($departments);
$dept_ids = array();
foreach($departments as $d){
    $dept_ids[$d['deptname']] = $d['id'];
}

$fh = fopen($datafile, "r");
while(!feof($fh)){
    $line = fgets($fh);
    //echo $line."\n";
    $fields = explode("\t", $line);
    //echo "$fields[2]\t$fields[3]\t$fields[4]\t$fields[5]\t$fields[6]\t$fields[11]\n";
    //$data = array($fields[2], $fields[3], $fields[4], $dept_ids[$fields[5]], $location_ids[$fields[6]]); 
    updateDept($fields[3], $dept_ids[$fields[5]]);
}
fclose($fh);

function updateDept($username, $dept_id){
    if($dept_id == 0){
        echo "Dept of $username was not found.\n";
    }
    $update = sprintf("UPDATE fi_emp_list SET dept = '%s'
        WHERE username='%s'",
        mysql_real_escape_string($dept_id),
        mysql_real_escape_string($username));
    $res = mysql_query($update) or die(mysql_error() . $update);
}
