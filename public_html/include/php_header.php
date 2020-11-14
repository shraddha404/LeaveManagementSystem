<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/../lib/HR.class.php';
$u = new User($_SESSION['user_id']);
//echo $_SESSION['user_id'];
if($u->isHR()){
	$u = new HR($_SESSION['user_id']);
}
else if($u->isManager()){
	$u = new Manager($_SESSION['user_id']);

}
else {
	$u = new Employee($_SESSION['user_id']);
}
/*
else{
	$u = new User($_SESSION['user_id']);
}
*/
?>

