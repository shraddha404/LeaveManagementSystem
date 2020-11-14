<?php
error_reporting(0);

// read csv file
define('CSV_PATH','/var/www/html/leavemanagement/scripts/');

$csv_file = CSV_PATH . "join3_dates.csv";
if(!file_exists($csv_file)) {
    die("File not found. Make sure you specified the correct path.");
}
if (($handle = fopen($csv_file, "r")) !== FALSE) {
fgetcsv($handle);
   while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {

        $num = count($data);
        for ($c=0; $c < $num; $c++) {
          $col[$c] = ltrim(rtrim($data[$c]));
        }
	//$handle1 = fopen($csv_file1, "w");
$string= (array($col[0]));
print_r($string);
//echo "replace - ".preg_replace($pattern, $replacement, $fields).""; echo "\n\n";  
$file = fopen("/home/pravin/Desktop/dates.csv","w");

foreach ($string as $fields) {

$replace=date('Y-m-d', strtotime($fields));
	echo "AFTER - ".$replace.""; echo "\n\n";
$date_array[]= $replace;
//$i=0;
}
foreach ($date_array as $date) {

  fputcsv($file,explode('\t',$date));
//$i++;

  }

fclose($file); 

 }
//exit;
    fclose($handle);
}

echo "File data successfully imported to database!!";
//mysql_close($connect);
?>

