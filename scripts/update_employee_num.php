<?php
include '../lib/db_connect.php';

$leave_data = $argv[1];

$fh = fopen($argv[1], "r");
    while($line = fgets($fh)){
        $line = rtrim($line);
        //echo "$line\n";
        $fields = explode("\t", $line);

        $update = sprintf("UPDATE fi_emp_list 
            SET empno = '%s'
	    WHERE username = '%s'",
            mysql_real_escape_string($fields[1]),
            mysql_real_escape_string($fields[0]));					
        //echo $update."\n";
        mysql_query($update) or die(mysql_error() . $insert);
    }
fclose($fh);
?>
