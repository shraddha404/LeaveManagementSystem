<?php
$ou = getEmployeeOUString($_SESSION['empid']);
if(empty($ou)){
	$ou = "OU=Standard,OU=Users,OU=Pune,OU=RG - India,DC=win,DC=ansys,DC=com";
}
?> 
