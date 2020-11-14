<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/../lib/User.class.php'; 
include_once $_SERVER['DOCUMENT_ROOT']."/simplesamlphp/index.php";
$username = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0];
$username = preg_replace('/@ansys.com/','',$username);

$email = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0];
#$email = $_GET['email'];

$u = new User();

$user_id = $u->getUserIdFromUsername($username);
if(empty($user_id) || $user_id == ''){
$user_id = $u->getUserIdFromEmail($email);
}

// start session
$_SESSION['user_id'] = $user_id;
header('location:/main.php');
