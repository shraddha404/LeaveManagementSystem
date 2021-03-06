<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/header.php';
$user_id = $_GET['empno'];


$emp_id = $user_id;
$criteria = array('emp_id'=>$emp_id,'year'=>date('Y'));
$e = new Employee($emp_id);
$emp_details = $e->getUserDetails($emp_id);

$leave_for_year = $e->getGeneralLeaveBuckets($criteria);
foreach($leave_for_year as $k=>$v){
        $leaves_for_year += $v;
}
$leaves_taken = $e->getMyLeaveRecord(date('Y'));
$leavetaken = 0;
foreach($leaves_taken as $v){
        $leavetaken += $v['leave_days'];
}
$leave_types = $e->getLeaveTypes();

$approved_leaves = $e->getMyApprovedLeaves($emp_id,date('Y'));

if($u->isHR())
{

	if($_REQUEST['asubmit']=="Submit")
	{
		if($u->updateEmployeeDetails($_POST)){
			$msg = "<font color='green'>Employee Details are edited Successfully!</font>";
		}
		else{
			$msg = "<font color='red'>Please try again</font>";
		}
	}
	
$employee_leave_buckets = $e->getEmployeeLeaveBuckets($emp_id,date('Y'));
$leaves_carry_forward = $e->getLeavesCarriedForward($criteria);
$leaves_carry_forwards = 0;
foreach($leaves_carry_forward as $k=>$v){
        $leaves_carry_forwards += $v;
}
	?>

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
	<?
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
	<h3><u>Edit Employee : </u></h3>
		<?php echo $msg;?>
		<table class="adminlist" cellpadding="3">
		<form name="form1" method="post" action="" onsubmit="return chckData();">
			<tr>
			<td width="18%"> Employee Number :</td>
			<td><input type="text" name="empno" value="<?php echo $emp_details['empno']; ?>"></td>
			</tr>
			<tr>
			<td width="18%"> Employee name :</td>
			<td><input type="text" name="cname" value="<?php echo $emp_details['cname']; ?>"></td>
			</tr>
					<tr>
			<td width="18%"> Leave from the last year:</td>
			<td><?php echo $leaves_carry_forwards; ?></td>
			</tr>
<!--
					<tr>
			<td width="18%"> Leave for year:</td>
			<td><input type="text" name="leave_current_year" value="<?php echo $leaves_for_year; ?>"></td>
			</tr>
-->
					<tr>
			<td width="18%"> Leaves taken:</td>
			<td><?php echo $leavetaken;  ?></td>
			</tr>
		</table>
		<br />
		
		<b>Employee Carry Forward Leaves:</b><br />
		<table class="adminlist" cellpadding="3">
			<tr>
			<td>
				<table>
				<?php 
				 foreach($leave_types as $k=>$v){
				if($v == 'Maternity') continue; 
				?>
				<tr><td><?php echo $v;?></td><td><input type="text" size="4" name="<?php echo "carry_forward_".$k;?>" value="<?php echo $leaves_carry_forward[$k];?>"></td></tr>
				<?php } ?>
				</table>
			</td>
		</table>
		<br />


		<table class="adminlist" cellpadding="3">
			<tr><td>
			<table>
			<tr>  
		<td colspan="2"><strong>Employee Leave Buckets<br/> <span class="red">Enter only if different from the buckets on the right.<br/>
		Make sure you enter in all three buckets even if 0.
		</span></strong></td>
		<td valign="top" colspan="2"><b>Employee Leave Buckets for all.</b></td>
                        </tr>   
                        <tr>  
                        <td valign="top" width="18%"> Employee Leave Buckets</td>
                        <td>    
                        <table> 
                        <?php   
                        foreach($leave_types as $k=>$v){
                        if($v == 'Maternity') continue;
                        ?>      
			</tr>
                        <tr>    
                        <td><?php echo $v;?></td>
                        <td><input type="text" name="leave_type_<?php echo $k;?>" value="<?php echo $employee_leave_buckets[$k]['maximum']; ?>"></td>
                        </tr>   
                        <?php   
                        }       
                        ?>      
                        </table>
			</td>

	                <td valign="top" width="18%"> Employee Leave Buckets</td>
                        <td valign="top">    
                        <?php   
                        foreach($leave_types as $k=>$v){
                        if($v == 'Maternity') continue;
                        ?>      
                        	<?php echo $v;?>:&nbsp;
                        	<?php echo $leave_for_year[$k]; ?><br />
                        <?php   
                        }       
                        ?>      
                        </td>   
                        </tr> 
			</table>
			</table>

		<br />
		<table class="adminlist" cellpadding="3">
			<tr>
			<td width="18%"> Department :</td>
			<td>
			<select name="dept" >
			<option value="none">Select Dept</option>
			<?
				$sql="Select * from fi_dept";
				$res=mysql_query($sql);
				while($ro=mysql_fetch_array($res))
				{
					if($ro['id'] == $emp_details['dept']){
						$selected = "selected";
					}
					else{
						$selected = '';
					}
					echo "<option value='".$ro['id']."' $selected> ".$ro['deptname']."</option>";
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
				$locations = $u->getOfficeLocations();
				foreach ($locations as $l){
					$selected = ($emp_details['location'] == $l['id'])? "selected" : "";
					echo "<option value=\"$l[id]\" $selected>$l[location]</option>";
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
				$org_units = $u->getOUs();
				foreach ($org_units as $ou){
					$selected = ($emp_details['ou'] == $ou['id'])? "selected" : "";
					echo "<option value=\"$ou[id]\" $selected>$ou[ou_short_name]</option>";
				}
			?>
			</select>
			</td>
			</tr>
					<tr>
			<td width="18%"> Current employee:</td>
			<td><input type="checkbox" name="status" value="1" <?php if($emp_details['status'] == 1) {?> checked <?php } ?>> Yes
			<br /><input type="checkbox" name="status" value="0" <?php if($emp_details['status'] == 0) {?> checked <?php } ?>> No</td>
			</tr>
					<tr>
			<td width="18%"> &nbsp;</td>
			<td><input type="submit" name="asubmit" value="Submit">
			<input type="hidden" name="emp_id" value="<?php echo $_GET['empno']; ?>"/>	
			</td>
			</tr>
			</form>
		</table>
		
<?
}else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
include ("footer.php");
?>
