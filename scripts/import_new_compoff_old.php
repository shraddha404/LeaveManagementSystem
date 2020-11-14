<?php
include '../lib/db_connect.php';
error_reporting( error_reporting() & ~E_NOTICE );

$empno_mapping = getEmpNo2IDs();

//print_r($empno_mapping);
$leave_data = $argv[1];

$fh = fopen($argv[1], "r");
    while($line = fgets($fh)){
        $line = rtrim($line);
        //echo "$line\n";
        $fields = explode("\t", $line);


        $insert = sprintf("INSERT INTO fi_compoff 
            (`emp_id`, `approved_by`, `work_date`,`compoff_date`,`applied`, `comments`, `status`)
            VALUES ('%s', '%s', '%s','%s', '%s', '%s','%s')",
            mysql_real_escape_string($empno_mapping[$fields[0]]),
            mysql_real_escape_string($fields[1]),
            mysql_real_escape_string($fields[2]),
            mysql_real_escape_string($fields[3]),
            mysql_real_escape_string($fields[4]),
            mysql_real_escape_string($fields[5]),
            mysql_real_escape_string($fields[6]));

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
