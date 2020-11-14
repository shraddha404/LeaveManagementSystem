<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
include ("header.php");
if($u->isHR())
{
	if($_REQUEST['asubmit']=="Update")
	{
		$u->UpdateConfig($_REQUEST);
	}
	$configs = $u->getConfig('leave_max_date');	

	?>
	<h3><u>Manage Config values : </u></h3>
		<table class="adminlist" cellpadding="3">
		<form name="form1" method="post" action="">
			<tr>
			<td width="18%"> Parameter Name:</td>
			<td><input type="text" name="eventname" value="leave_max_date" readonly></td>
			</tr>
			<tr>
			<td width="18%">Value:</td>
			<td><input type="text" id="sel1" name="eventdate" value="<?php echo $configs;?>" onfocus='this.blur();'>
			<img src="images/calendar-Icon.gif" onclick="return showCalendar('sel1', 'y-mm-dd');">	
			</td>
			</tr>
			
			<tr>
			<td width="18%"> &nbsp;</td>
			<td>
			<input type="submit" name="asubmit" value="Update">
			</td>
			</tr>
			</form>
		</table>

		
		
<?php
}else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
include ("footer.php");
?>
