<?php
if ( !is_writable(session_save_path()) ) {
   echo 'Session save path "'.session_save_path().'" is not writable!'; 
}
else{
	echo session_save_path().' is writable';
}
/*
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
include ("header.php");
$leaves = $u->carryForwardLeaves();             
print_r($leaves);

	echo "<br />";
foreach($leaves as $user=>$details){
	echo $user." ";
	print_r($details);
	echo $details[Sick];
	echo $details[Earned];
	echo "<br />";
}
*/
?>
