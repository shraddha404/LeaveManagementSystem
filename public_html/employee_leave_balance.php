<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';


?>
<html>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/header.php'; ?>
<form name='form2' action='employee_leave_balance_csv.php' method="POST" >
<table><tr><td colspan="7"><h2>Org Leave report</h2></td></tr>
<tr>

<td>Select Year for report:</td>
<td><?php yearDropdown(2014, 2050); ?> </td>
<td><input type="submit" name="submit" value="Get Report"></td>
</tr>
</form>
