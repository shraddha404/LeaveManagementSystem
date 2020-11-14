<?php
include '../lib/db_connect.php';

//$argv[1]='EMPLOYEE_DOJ.csv';


$leave_data = $argv[1];
 if (($file = fopen($argv[1], "r")) !== FALSE)
 {  $i=0;
    while (($data = fgetcsv($file)) !== FALSE)
           {
  //print_r($data);
		if($leave_data=='DOJ_as_on_1st_June_2017.csv'){
		$date = explode(' ',$data[3]);//print_r($date);
$nmonth = date("m", strtotime($date[1]));
     $mysql_date = $date[2].'-'.$nmonth.'-'.$date[0];
 $mysql_date = date('Y-m-d', strtotime($mysql_date));
if($data[1] == '1091'){
 echo $sql = "select * from fi_emp_list  WHERE  `fi_emp_list`.`empno`=".$data[1];
}

		}
		else
		{
		$date = explode(' ',$data[2]);//print_r($date);
$nmonth = date("m", strtotime($date[1]));
     $mysql_date = $date[2].'-'.$nmonth.'-'.$date[0];
 $mysql_date = date('Y-m-d', strtotime($mysql_date));
 echo $sql = "select * from fi_emp_list  WHERE  `fi_emp_list`.`empno`=".$data[0];

		}
          $res= mysql_query($sql);
if (mysql_numrows($res) == 0){

               $a= "Error updating EMP NO=".$data[1].mysql_error();
               $a.="\n";

            } else {
		if($leave_data=='DOJ_as_on_1st_June_2017.csv'){
          echo $sql1 = "UPDATE `fi_emp_list`  SET `joining_date`='".$mysql_date."' WHERE `empno`='".$data[1]."'";
		}
else{
          echo $sql1 = "UPDATE `fi_emp_list`  SET `joining_date`='".$mysql_date."' WHERE `empno`='".$data[0]."'";
}
          $res1= mysql_query($sql1);
                $a= "Updated!";               $a.="\n";
            }
$my_file = 'file.txt';
$handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file);
$data = $a;
fwrite($handle, $data);


        $i++;
        }


printf("Records Updated: %d\n", $i);
    }
fclose($file);?>
