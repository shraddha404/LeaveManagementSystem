<?php
error_reporting(0);
#$db = 'leaveman_ansysleave'; ## Original our portal database
$db = 'ansysleave';
$db_host = 'localhost';
$db_user = 'ansysleave';
$db_pass = 'ansysleave123';
$dbh = mysqli_connect("$db_host", "$db_user", "$db_pass", "$db") or die(mysql_error());
#mysqli_select_db($dbh, "$db") or die("Unable to connect");
?>
