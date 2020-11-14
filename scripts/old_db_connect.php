<?php
$db = 'fluentleavedb';
$db_host = 'localhost';
$db_user = 'leavedb';
$db_pass = 'leavedb123';
$dbh = mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());
mysql_select_db($db, $dbh);
?>
