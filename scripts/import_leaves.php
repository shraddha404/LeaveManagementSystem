<?php
include '../lib/db_connect.php';

$empno_mapping = getEmpNo2IDs();

//print_r($empno_mapping);
$leave_data = $argv[1];

$fh = fopen($argv[1], "r");
    while($line = fgets($fh)){
        $line = rtrim($line);
        //echo "$line\n";
        $fields = explode("\t", $line);

        $leave_type_id = ($fields[4]>2)?12:6;

        $insert = sprintf("INSERT INTO fi_leave 
            (`emp_id`, `applied`, `leave_type`, `from_dt`, `to_date`, `reason`, `manager_comment`, `status`, `leave_days`)
            VALUES ('%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s')",
            mysql_real_escape_string($empno_mapping[$fields[0]]),
            mysql_real_escape_string($fields[1]),
            $leave_type_id,
            mysql_real_escape_string($fields[2]),
            mysql_real_escape_string($fields[3]),
            mysql_real_escape_string($fields[5]),
            mysql_real_escape_string($fields[6]),
            mysql_real_escape_string($fields[7]),
            mysql_real_escape_string($fields[4]));

        //echo $insert."\n";
        mysql_query($insert) or die(mysql_error() . $insert);
    }
fclose($fh);

function getEmpNo2IDs(){
    $select = "SELECT id, empno FROM fi_emp_list";
    $res = mysql_query($select) or die(mysql_error().$select);
    $emp_nos = array();
    while($row = mysql_fetch_assoc($res)){
        $emp_nos[$row['empno']] = $row['id'];
    }
    return $emp_nos;
}
