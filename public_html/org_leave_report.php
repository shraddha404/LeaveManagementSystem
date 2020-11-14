<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';

?>
<html>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/header.php'; ?>
<form name='form2' action='csv_org_leave_report.php' method="POST" >
<table><tr><td colspan="7"><h2>Org Leave report</h2></td></tr>
<tr>
<td><img src="images/write.gif"></td>
<td>From Date:</td>
<td><input type="text" name="from_dt" id="sel3" size="10" readonly>
<img src="images/calendar-Icon.gif" onclick="return showCalendar('sel3', 'y-mm-dd');"></td>
<td>To Date:</td>
<td><input type="text" name="end_dt" id="sel4" size="10" readonly>
<img src="images/calendar-Icon.gif" onclick="return showCalendar('sel4', 'y-mm-dd');">
&nbsp;</td>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Apply for org leave"></td>
</tr>
</form>
