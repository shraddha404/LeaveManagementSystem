<?php
include_once "../lib/db_connect.php";
include_once "../lib/lib.php";

define('CSV_PATH','');

$csv_file = CSV_PATH . "New_employee_Number_for_Ketan.csv";
if(!file_exists($csv_file)) {
    die("File not found. Make sure you specified the correct path.");
}
//$cid =mysql_select_db('ansysleave',$connect);


if (($handle = fopen($csv_file, "r")) !== FALSE) {
   fgetcsv($handle);   
   while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        for ($c=0; $c < $num; $c++) {
          $col[$c] = ltrim(rtrim($data[$c]));
        }
   
//print_r($col);

$update = ( "UPDATE `fi_emp_list` SET `empno` ='".$col[0]."' WHERE `empno` = '".$col[1]."' " );
	$s = mysql_query($update);

//echo "Updated -".$col[0]." DATE - ".$col[4]." AND - ".$col[5]." \n";

//echo "Updated -".$col[0]." DATE OF Joining - ".$col[2]." \n";
//echo "New emp ".$col[0]." NAME - ".$col[1]."DATE - ".$col[2]."\n";

//print_r($values);exit;
 }
//exit;

}

echo "File data successfully imported to database!!";

?>
