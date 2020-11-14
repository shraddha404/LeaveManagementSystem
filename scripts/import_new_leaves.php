<?php
#leave_register_apache_bangalore_updated.csv
#leave_register_sequence_updated.csv

include '../lib/db_connect.php';
include_once "../lib/User.class.php";

error_reporting( error_reporting() & ~E_NOTICE );

$leave_data = $argv[1];
$fh = fopen($argv[1], "r");
    while($line = fgets($fh)){
        $line = rtrim($line);
        //echo "$line\n";
        $fields = explode("\t", $line);
	//print_r($fields);
	$emp_leaves[] = $fields;

        //$leave_type_id = ($fields[4]>2)?12:6;
    }
fclose($fh);

// get Leave-type ids
$u = new User();
$leave_types = $u->getLeaveTypes();
$flipped_types = array_flip($leave_types);
//print_r($flipped_types);


$leave_type_id=0;
foreach($emp_leaves as $k=>$fields){
	//echo $k." =>"; print_r($fields);
		$emp_id = getEmpNo2IDs($fields[1],$fields[0]);
		addCarryForward($emp_id, @date('Y'), $flipped_types['Earned'], $fields[2]);
	$i = 4;
	while($i <= 20){
		#$fields[0] - cname and $fields[1]-username

		if(isset($fields[$i])){
			$leave_date = @date('Y-m-d',strtotime($fields[$i]));
	if($emp_leaves[$k+1][0] == '' && $emp_leaves[$k+1][1] == '' && $emp_leaves[$k+1][2] == '' && $emp_leaves[$k+1][3] == ''){
	//echo $i."\n";
	//echo $emp_leaves[$k+1][$i]."\n";
				if($emp_leaves[$k+1][$i] == 'PL'){ $leave_type_id = 5;}
				else if($emp_leaves[$k+1][$i] == 'CL'){$leave_type_id = 6;}
				else if($emp_leaves[$k+1][$i] == 'SL'){$leave_type_id = 7;}
        			$insert = sprintf("INSERT INTO fi_leave 
           			 (`emp_id`,`leave_type`, `from_dt`, `to_date`,`status`,`leave_days`)
            			VALUES ('%s','%s', '%s','%s', 'Approved','%s')",
				mysql_real_escape_string($emp_id),
				$leave_type_id,
        		     	mysql_real_escape_string($leave_date),
            			mysql_real_escape_string($leave_date),
            			'1');

			//echo $delete."\n";
        		echo $insert."\n";
        		//mysql_query($delete) or die(mysql_error() . $delete);
        		mysql_query($insert) or die(mysql_error() . $insert);
			}
		}
	$i++;
	}
	$k = $k+2;
			//if($k == 4)exit;
	//echo "\n";
}


function addCarryForward($emp_id, $year, $leave_type_id, $carry_forward){
    $insert = sprintf("INSERT INTO fi_leave_carry_forwards
        VALUES('%s', '%s', '%s', '%s')",
        mysql_real_escape_string($emp_id),
        mysql_real_escape_string($year),
        mysql_real_escape_string($leave_type_id),
        mysql_real_escape_string($carry_forward));
    mysql_query($insert);// or die($insert . mysql_error());
}


function getEmpNo2IDs($username,$cname){
    $select = "SELECT id, empno FROM fi_emp_list WHERE username='$username' || cname='$cname'";
    $res = mysql_query($select) or die(mysql_error().$select);
    $row = mysql_fetch_assoc($res);
    return $row['id'];
}
