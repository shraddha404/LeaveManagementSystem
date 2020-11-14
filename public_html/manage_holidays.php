<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
include ("header.php");
if($u->isHR())
{
	if($_REQUEST['asubmit']=="Add")
	{
		$u->addHoliday($_POST);
	}
	$holidays = $u->getAllHolidays();	
	$locations = $u->getOfficeLocations()
	?>
	<h3><u>Manage Holidays : </u></h3>
		<table class="adminlist" cellpadding="3">
		<form name="form1" method="post" action="">
			<tr>
			<td width="18%"> Event Name:</td>
			<td><input type="text" name="eventname" value=""></td>
			</tr>
			<tr>
			<td width="18%"> Event Date:</td>
			<td><input type="text" id="sel1" name="eventdate" value="" onfocus='this.blur();' readonly>
			<img src="images/calendar-Icon.gif" onclick="return showCalendar('sel1', 'y-mm-dd');">	
			</td>
			</tr>
			<tr>
			<td width="18%"> Location:</td>
			<td>
			<select name="location">
			<option value="0">All</option>
			<?php
				foreach($locations as $l){
				echo "<option value=\"$l[id]\">$l[location]</option>";
				}
			?>
			</select>
			</td>
			</tr>
			<tr>
			<td width="18%"> &nbsp;</td>
			<td>
			<input type="submit" name="asubmit" value="Add">
			</td>
			</tr>
			</form>
		</table>

		<table border="1">
		<?php 
		foreach($holidays as $h){
			$location_string = empty($h['location'])?'All':$h['location'];
			echo "<tr>
			<td>$h[eventname]</td>
			<td>". date_format(date_create($h[eventdate]), 'd-M-Y')."</td>
			<td>$location_string</td>
			<td><a href=\"delete_holiday.php?holiday=$h[id]\">X</a></td>
			</tr>";
		}
		?>
		</table>
		
<?php
}else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
include ("footer.php");
?>
