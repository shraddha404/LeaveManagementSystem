<?php 
include "db_connect.php";
include "Employee.class.php";
$u = new User();

$username = 'rkhandel';
$password = 'rAkh!0505';

if($u->authenticate($username, $password)){
echo "Success";
}
else{
echo "Failure";
}

/*
$u = new Employee(310);

$ou = $u->getUserIdFromUsername('aborkar');
print $ou;
*/
