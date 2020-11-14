<?php
/*  steps of Import file
	1-> convert date using chng_date_format.php script
	2-> check date column which is in sript for step-1
	3-> copy output date and paste in 3rd column in csv file
	4-> then run Import_script.php script

*/
error_reporting(0);
$connect = mysql_connect('localhost','ansysleave','ansysleave123');

if (!$connect) {
 die('Could not connect to MySQL: ' . mysql_error());
}

$cid =mysql_select_db('ansysleave',$connect);

// read csv file
#define('CSV_PATH','/var/www/html/leavemanagement/assets/');
define('CSV_PATH','');

$csv_file = CSV_PATH . "join_dates.csv";
if(!file_exists($csv_file)) {
    die("File not found. Make sure you specified the correct path.");
}

if (($handle = fopen($csv_file, "r")) !== FALSE) {
   fgetcsv($handle);   
   while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        for ($c=0; $c < $num; $c++) {
          $col[$c] = ltrim(rtrim($data[$c]));
        }
   
//$query = "INSERT INTO fi_emp_list(empno\cname\joining_date) 
//	VALUES('".$col[0]."'\t'".$col[1]."'\t'".$col[2]."')";

$update = ( "UPDATE `fi_emp_list` SET `joining_date` ='".$col[4]."',left_on ='".$col[5]."' 
                         WHERE `empno` = '".$col[0]."' " );
	$s = mysql_query($update, $connect);

echo "Updated -".$col[0]." DATE - ".$col[4]." AND - ".$col[5]." \n";

//echo "Updated -".$col[0]." DATE OF Joining - ".$col[2]." \n";
//echo "New emp ".$col[0]." NAME - ".$col[1]."DATE - ".$col[2]."\n";

//print_r($values);exit;
 }
//exit;
    fclose($handle);
}

echo "File data successfully imported to database!!";
mysql_close($connect);
?>

