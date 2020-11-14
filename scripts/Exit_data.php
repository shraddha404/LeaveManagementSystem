<?php
include '../lib/db_connect.php';

//$argv[1]='Left-Employees-Data.csv';
$leave_data = $argv[1];
 if (($file = fopen($argv[1], "r")) !== FALSE)
 {  $i=0;
    while (($data = fgetcsv($file)) !== FALSE)
           {
  //print_r($data);
		if($leave_data=='Left-Employees-Data.csv'){
		$date = explode('/',$data[2]);//print_r($date);
		}
		else
		{
		$date = explode('/',$data[3]);//print_r($date);
		}

    $mysql_date = $date[2].'-'.$date[1].'-'.$date[0];

 $sql = "select * from fi_emp_list  WHERE  `fi_emp_list`.`empno`=".$data[0];
          $res= mysql_query($sql);
if (mysql_numrows($res) == 0){

               $a= "Error updating EMP NO=".$data[0].mysql_error();
               $a.="\n";

            } else {
        echo  $sql1 = "UPDATE `fi_emp_list`  SET `left_on`='".$mysql_date."' WHERE `empno`='".$data[0]."'";
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
