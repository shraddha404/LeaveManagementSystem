<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
if($u->isHR() )
	{
	$criteria = array();
	if($_GET){$criteria = $_GET;}
	
 ######### Code added on 1Jun 2018
 if(empty($_POST['location']) && empty($_GET['semp']) && empty($_GET['year'])){
     $location = $_SESSION['location'];
 }
 else if(!empty($_POST['location'])){
     $location = $_POST['location'];
 }
 ######## Code 1 Jun 2018 ends
 
	#$reports = $u->getEmployeeReports($criteria,$_SESSION['location']); ## Commented on 1Jun 2018
	$reports = $u->getEmployeeReports($criteria,$location);
#print_r($reports);
#exit;
	}
$locations = $u->getOfficeLocations();
		
?>
<html>
<script>
function displayLeftOnyearDiv(){
	document.getElementById("left_on_year").style.display="block";
}
</script>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/header.php'; ?>
<h3><u>Search Employee : </u></h3>
		<table class="adminlist" cellpadding="3">
		<form name="form2" method="get" action="dept_reports.php" >
			<tr>
			<td width="18%"> Search Employee :</td>
			<td><input type="text" name="semp" value=""></td>
			</tr>
			<tr>
			<td width="18%"> &nbsp;</td>
			<td><input type="submit" name="srchsubmit" value="Submit"></td>
			</tr>
			</form>
		</table>
		<p>&nbsp;</p>

<h3>Change Office Location : </h3>

		<form name="employee_locations" method="post" action="">
		<table class="adminlist" cellpadding="3">
		<tr><td>
		<select name="location" onChange="form.submit();">
		<option value="-1">ALL</option>
		<?php
			foreach($locations as $l){
		$selected = '';
		if(!empty($_POST['location']) && $_POST['location'] == $l['id']){
		    $selected = 'selected';
		}
		else if($_SESSION['location'] == $l['id'] && !$_POST['location']){
			$selected = 'selected';
		}
		?>
		<option value="<?php echo $l[id];?>" <?php echo $selected;?>><?php echo $l[location]; ?></option>
		<?php
		}
		?>
		</select>
		</td>
		</tr>
		</table>
		</form>

		<p>&nbsp;</p>

                <table class="tabForm" border="0" cellpadding="1" cellspacing="1" style="width: auto!important">
<tr>
		<td><center>
		<a href="dept_reports.php?arg1=A">A</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=B">B</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=C">C</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=D">D</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=E">E</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=F">F</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=G">G</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=H">H</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=I">I</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=J">J</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=K">K</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=L">L</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=M">M</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=N">N</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=O">O</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=P">P</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=Q">Q</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=R">R</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=S">S</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=T">T</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=U">U</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=V">V</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=W">W</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=X">X</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=Y">Y</a>
&nbsp;&nbsp;
<a href="dept_reports.php?arg1=Z">Z</a>
&nbsp;&nbsp;
<a href="dept_reports.php">ALL</a>
&nbsp;&nbsp;
<a href="dept_reports.php?status=ex">Left Employees</a>

</center>
		</td>
		</tr>
</table>
		<h3>Description of the leave record: <?php echo $dept; ?></h3>
<?php if($_GET['status'] == 'ex'){?>
<form name="form2" method="get" action="dept_reports.php" >
<table ><tr><td colspan="7"><strong>Left Employees Report based on Year</strong></td></tr>
<tr>
<td>Select Year for report:</td>
<td>
<select name="year">
<?php 
if(empty($_GET['year'])){
$year = date("Y");
}
else{
$year = $_GET['year'];
}

for ($i = 2010; $i <= 2050; $i++){
?>
<option value=<?php echo $i; ?><?php if($i == $year) echo " selected";?> ><?php echo $i; ?></option> 
<?php
        }
?>
</select>

<input type="hidden" name="status" value="<?php echo $_GET['status'];?>" </td>
<td><input type="submit" name="submit" value="Get Report"></td>
</tr>
</table>
</form>
<?php }?>
                <table  class="adminlist" cellpadding="3" style="width:auto!important">
<tr>
<th class="title" width="10%" >Emp No</th>
<th class="title" width="10%" >Name</th>
<th class="title" width="10%" >Dept Name</th>
<th class="title" width="10%" >Location</th>
<th class="title" width="10%" >Org Unit</th>
<!--th class="title" width="20" >Left On Date</th-->
<th class="title" width="20%" >Leave From Last Year</th>
<th class="title" width="20%" >Leave For This Year</th>
<th class="title" width="40%" >Total Leave For this Year</th>
<th class="title" width="10%" >Leave Taken</th>
<th class="title" width="10%" >Balance Leave</th>
<th class="title" width="10%" >View/Edit Record</th>

</tr>
<?php
$flag=1;
		$k=1;
		//if(mysql_num_rows($result))
		if(!empty($reports))	
	{
		//while($row=mysql_fetch_array($result))
		foreach($reports as $row)
		{
		if ($flag)
			{
				$tr="row1";
				$flag=0;
			}
			else
			{
				$tr="row0";
				$flag=1;
			}

$emp_id = $row['id'];
$leaves_carry_forwards = 0;
$leavetaken = 0;
$leaves_for_year = 0;
$criteria = array('emp_id'=>$emp_id,'year'=>date('Y'));
$user_details = $u->getUserDetails($emp_id);

$e = new Employee($emp_id);
$leave_for_year = $e->getYearLeaves($criteria);
foreach($leave_for_year as $k=>$v){
        $leaves_for_year += $v;
}
$leaves_carry_forward = $e->getLeavesCarriedForward($criteria);
foreach($leaves_carry_forward as $k=>$v){
        $leaves_carry_forwards += $v;
}
$leaves_taken = $e->getMyLeaveRecord(date('Y'));
$leave_types = $e->getLeaveTypes();
foreach($leaves_taken as $v){
	if($v['status'] == 'Approved' && ($leave_types[$v['leave_type']] != 'Maternity' && $leave_types[$v['leave_type']] != 'Bereavement'))
        $leavetaken += $v['leave_days'];
}

$approved_leaves = $e->getMyApprovedLeaves($emp_id,date('Y'));

			$total_leaves_for_year =$leaves_carry_forwards+$leaves_for_year;
		        $bal=$total_leaves_for_year-$leavetaken;
			echo "<tr class='$tr'>";
			//<td>".$k++."</td>
			echo "<td>".$row['empno']."</td>
			<td>".ucwords($row['cname'])."</td>
			<td>".$row['deptname']."</td> 
			<td>" .$row['location']."</td>
			<td>".$row['org_unit']."</td>
			<!--td>".$row['left_on']."</td-->
			<td>".$leaves_carry_forwards."</td><td>".$leaves_for_year."</td><td>$total_leaves_for_year</td><td>".$leavetaken."</td>";
			 if($bal > -1)
				 echo "<td>$bal</td>"; 
				else
				 echo "<td><font color='red'><b>$bal</b></font></td>";
					
				echo "<td align='center'><a href=\"viewempdetails.php?emp_id=".$row['id']."\"><img src='images/editbutton.png' border='0' ></a></td></tr>";
		}
		
	}
	else
	{
	 echo "<tr><td colspan='9'><div align='center'><b> No Data Present. </b></div></td></tr>";
	}
	echo "</table>";
?>
<? include ("footer.php");?>
