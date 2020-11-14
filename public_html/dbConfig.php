<?php
//db details
$dbHost = 'localhost';
$dbUsername = 'ansysleave';
$dbPassword = 'ansysleave123';
$dbName = 'ansysleave';

//Connect and select the database
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>
