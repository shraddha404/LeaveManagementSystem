<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';

$dept = $u->getDept();
$locations = $u->getOfficeLocations();
$org_units = $u->getOUs();
$leave_types= $u->getLeaveTypes();

if($u->isHR())
{
if($_REQUEST['asubmit']=="Submit")
{
	if ($u->addEmployee($_POST))
		{
			$msg1="Successfully Added Employee Record ";
		}
		else
		{
			$msg1="There was an error. Employee record was not added.";
		}
	}	
		if ($_REQUEST['srchsubmit']=="Submit") 
		{
			if ($u->deleteEmployee($_POST['username']))
			{
				$msg1="Successfully Deleted Employee Record.";
			}
			else
			{
				$msg1=$u->error;
			}
		}
	
	?>
<html>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/header.php'; ?>
	<script language='Javascript'>
	function chckData()
	{
		var f= document.form1;
		if (f.eno.value=="")
		{
			alert("Enter Employee Number.");
			return false;
		}
		if (f.eid.value=="")
		{
			alert("Enter Employee ID.");
			return false;
		}
		if (f.dept.value=="none")
		{
			alert("Select Employee Department.");
			return false;
		}
		if (f.lyear.value=="")
		{
			alert("Enter Leave for the Year.");
			return false;
		}
		return true;
	}
	</script>
	<?php
	if(!empty($msg1)){
	$show = "
	<table class='adminlist'>
	<tr>
	<td><font color='red'><b>$msg1</b></font></td>
	</tr>
	</table>
	"; 
	echo $show;
	}
	?>
	<h3><u>Add New Employee : </u></h3>
		<table class="adminlist" cellpadding="3">
		<form name="form1" method="post" action="" onsubmit="return chckData();">
			<tr>
			<td width="18%"> Employee login id :</td>
			<td><input type="text" name="username" value=""></td>
			</tr>
			<tr>
			<td width="18%"> Password :<br/><span class="red">(Enter only if the user is not present in LDAP)</span></td>
			<td><input type="password" name="password" value=""></td>
			</tr>
			<tr>
			<td width="18%"> Employee number :</td>
			<td><input type="text" name="empno" value=""></td>
			</tr>
			<tr>
			<td width="18%"> Employee Full name :</td>
			<td><input type="text" name="cname" value=""></td>
			</tr>
			<tr>
                        <td width="18%"> Joining Date:</td>
                        <td><input type="text" id="sel1" name="joining_date" value="<?php echo $emp_details['joining_date']; ?>">
                        <img src="images/calendar-Icon.gif" onclick="return showCalendar('sel1', 'y-mm-dd');"></td>
                        </tr>
			<tr>
			<td colspan="2"><b>Enter Employee Leave Buckets only if the Employee has joined in the current year.</b></td>
			</tr>
			<tr>
			<td width="18%"> Employee Leave Buckets :</td>
			<td>
			<table>
			<?php
			foreach($leave_types as $k=>$v){
			if($v == 'Maternity') continue;
			?>
			<tr>
			<td><?php echo $v;?> :</td>
			<td><input type="text" name="leave_type_<?php echo $k;?>" value=""></td>
			</tr>
			<?php
			}
			?>
			</table>
			</td>
			</tr>
			<tr>
			<td width="18%"> Department :</td>
			<td>
			<select name="dept" >
			<option value="none">Select Dept</option>
			<?php
				foreach($dept as $ro){
					echo "<option value='".$ro['id']."'> ".$ro['deptname']."</option>";
				}
			?>
			</select >
			</td>
			</tr>
					<tr>
			<td width="18%"> Office location:</td>
			<td>
			<select name="location">
			<option value="" />Select location</option>
			<?php
				foreach ($locations as $l){
					echo "<option value=\"$l[id]\">$l[location]</option>";
				}
			?>
			</select>
			</td>
			</tr>
					<tr>
			<td width="18%"> Org Unit:</td>
			<td>
			<select name="ou">
			<option value="" />Select Org Unit</option>
			<?php
				foreach ($org_units as $ou){
					echo "<option value=\"$ou[id]\">$ou[ou_short_name]</option>";
				}
			?>
			</select>
			</td>
			</tr>
					<tr>
			<td width="18%"> &nbsp;</td>
			<td><input type="submit" name="asubmit" value="Submit"></td>
			</tr>
			</form>
		</table>
		
		<p>&nbsp;</p>
		<h3><u>Delete Employee : </u></h3>
		<table class="adminlist" cellpadding="3">
		<form name="form2" method="post" action="" >
			<tr>
			<td width="18%">Employee  Login Id :</td>
			<td><input type="text" name="username" value=""></td>
			</tr>
					<tr>
			<td width="18%"> &nbsp;</td>
			<td><input type="submit" name="srchsubmit" value="Submit"></td>
			</tr>
			</form>
		</table>

	
<?php
}else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
include ("footer.php");
?>
