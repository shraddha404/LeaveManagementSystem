<?php
//include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/../lib/User.class.php'; 

$u = new User();
if(!empty($_POST)){
	$loginname = $_POST['loginname'];
	$password = $_POST['password'];
	if(!empty($_POST['loginname']) && $u->authenticate($loginname,$password)){
		$_SESSION['user_id']=$u->user_id;
		$user_details = $u->getUserDetails($u->user_id);
		$_SESSION['location'] = $user_details[location];
		header("Location:main.php");
	}
}
?>
<html>
<head>
<title>Online Leave Application - Fluent India : Login</title>
<style>
table{
border:1px !important;
border-color:#ccc;
}
</style>
</head>

<body>
<form name="form1" method="post" action="" autocomplete="off">
<center>
<div style="margin-top:20%;">
<table  cellspacing = "0" cepllpadding = "0" width = "30%" bordercolor = "#000000">
<tr><td  align="center" bgcolor="#EFEFEF"><img src="/images/ansys_logo.png" /></td></tr>
<tr><td  bgcolor="#EFEFEF"><b>Use your ANSYS login and password</b></td></tr>
<tr><td>
<table  cellspacing = "0" cepllpadding = "0" width = "100%">
<tr><td bgcolor="#EFEFEF" valign = "top" width = "35%">Login Name</td>
<td bgcolor="#EFEFEF" width = "65%" valign = "top"><input type = "text" name = "loginname">
</tr>
<tr><td bgcolor="#EFEFEF" valign = "top" width = "35%">Password</td>
<td bgcolor="#EFEFEF" width = "65%" valign = "top"><input type = "password" name = "password">

</tr> 
<tr><td bgcolor="#EFEFEF" colspan = "2" align="center"><input type = "submit" name = "submit" value = "submit"></td></tr>
</table>
</tr></td>
</table>
</center>
</form>
</body>

</html>
