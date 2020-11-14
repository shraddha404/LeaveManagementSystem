<?php
include '../lib/db_connect.php';

$leave_data = $argv[1];

$fh = fopen($argv[1], "r");
    while($line = fgets($fh)){
        $line = rtrim($line);
        //echo "$line\n";
        $fields = explode("\t", $line);

	$emp_id = getEmpNo2IDs($fields[0],$fields[1]);

        $update = sprintf("UPDATE fi_leave_carry_forwards 
            SET no_of_leaves = '%s'
	    WHERE emp_id = '%s'",
            mysql_real_escape_string($fields[2]),
            mysql_real_escape_string($emp_id));					
        //echo $update."\n";
        mysql_query($update) or die(mysql_error() . $insert);
    }
fclose($fh);


function getEmpNo2IDs($username,$cname){
    $select = "SELECT id, empno FROM fi_emp_list WHERE username='$username' || cname='$cname'";
    $res = mysql_query($select) or die(mysql_error().$select);
    $row = mysql_fetch_assoc($res);
    return $row['id'];
}

?>
