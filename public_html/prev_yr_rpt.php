<?php
include $_SERVER['DOCUMENT_ROOT']."/include/php_header.php";
include ("header.php");

$_SESSION['empid'] = $_SESSION['user_id'];
$emp_id=$_GET['eid'];
//$u = new Employee($_SESSION['user_id']);
$user_details = $u->getUserDetails($_SESSION['user_id']);

$criteria = array('emp_id'=>$emp_id,'year'=>$_GET['id']);
$user_details = $u->getUserDetails($emp_id);

$joinleft_on_date = $u->getEmployeeJoinLeftOnDate($criteria);

$joining_year = date('Y', strtotime($joinleft_on_date['joining_date']));

//$leave_for_year = $u->getYearLeaves($criteria);
$custom_bucket_year = $u->getCustomBucketYear($emp_id);
#if($_GET['id'] < $custom_bucket_year){
if($_GET['id'] < $joining_year && $joining_year != '1970'){
	$leave_for_year = 0;
}
else{
	$leave_for_year = $u->getPrevYearLeaves($criteria);
}
foreach($leave_for_year as $k=>$v){
        $leaves_for_year += $v;
}
$leaves_carry_forward = $u->getPrevLeavesCarriedForward($criteria);
foreach($leaves_carry_forward as $k=>$v){
        $leaves_carry_forwards += $v;
}
$leaves_taken = $u->getMyLeaveRecord($_GET['id']);
foreach($leaves_taken as $v){
        $leavetaken += $v['leave_days'];
}
$leave_types = $u->getLeaveTypes();

//$comp_off = $u->getMyCompensatoryOffsRecord($_GET['id']);
//print_r($comp_off);


if(!empty($_GET['eid']))
{
 $user_id=$_GET['eid'];
}
else
{
 $user_id=$_SESSION['empid'];
}

$approved_leaves = $u->getMyPrevApprovedLeaves($user_id,$_GET['id']);
if(!empty($_GET['id']))
{ 
$year = $_GET['id'];
?>
 <h3>Description of employee leave record: <u><? echo $user_details['cname'];?></u> for Year : <?=$_GET['id']?></h3>
	<table  class="adminlist" cellpadding="3">
<tr>
<th class="title"  >Sr.No</th>
<th class="title" width="10%" >Application Date</th>
<th class="title" width="10%" >Leave From Date</th>
<th class="title" width="10%" >Leave To Date</th>
<th class="title" width="5%" >Days</th>
<th class="title" width="5%" >Leave Type</th>
<th class="title" width="20%" >Remark</th>
<th class="title" width="20%" >Manager/HR Comment</th>
<th class="title" width="10%" >Status</th>
</tr>
<?
 $flag=1;
	$i=1;
	$tmp_taken=0;
	$reports = $u->getPrevYearReport($user_id,$year);
	//$result=mysql_query($sql);
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
			echo "<tr class='$tr'><td>".$i++."</td><td>".date('d-M-Y',strtotime($row['applied']))."</td><td>".date('d-M-Y',strtotime($row['from_dt']))."</td><td>".date('d-M-Y',strtotime($row['to_date']))."</td> <td>".$row['leave_days']."</td><td>".$row['typename']."</td><td>".$row['reason']."</td><td>".$row['manager_comment']."</td>"; 
			if($row['leave_status']=="Pending")
			{
			 echo "<td><font color='red'><b>".$row['leave_status']."</b></font></td></tr>";
			}
			else if($row['leave_status']=="Cancelled")
			{
			 echo "<td><font color='orange'><b>".$row['leave_status']."</b></font></td></tr>";
			}
			else
			{
			 echo "<td><font color='green'><b>".$row['leave_status']."</b></font></td></tr>";
				$tmp_taken +=$row['leave_days'];
			}
		}
		
		echo "<tr><td colspan='9'>Total No. Of Leave Taken: <b>$tmp_taken</b></td>
		</tr>";
		
	}
	else
	{
	 echo "<tr><td colspan='9'><div align='center'><b> No Data Present. </b></div></td></tr>";
	}
	echo "<tr><td colspan='9'>&nbsp;</td></tr>";
?>
</table>


<h3>Description of My Leaves for <?php echo $_GET['id'];?>: </h3>
        <table  class="adminlist" cellpadding="3">
<tr>
<th class="title">Type of Leave</th>
<th class="title" width="20%" >Total Leaves for Current Year</th>
<th class="title" width="20%" >Carried Forward Leaves</th>
<th class="title" width="20%" >Leaves Taken</th>
<th class="title" width="20%" >Balance Leaves</th>
</tr>
        <?php
#echo date('Y', strtotime($joinleft_on_date['joining_date']));

        if($_GET['id'] >= $joining_year && $joining_year != '1970' ){
                $i=1;
                //foreach($approved_leaves as $approved){
                foreach($leave_types as $k=>$v){
                ?>
                <tr>
                <td><?php echo $v;?></td>
                <td><?php if(!empty($leave_for_year[$k])){ echo $leave_for_year[$k];}else{echo 0;}?><br /></td>
                <td><?php if(!empty($leaves_carry_forward[$k])){echo $leaves_carry_forward[$k];} else{ echo 0;}?><br /></td>
                <td><?php if(!empty($approved_leaves[$k][leaves])){echo $approved_leaves[$k]['leaves'];} else{ echo 0;}?><br /></td>
                <td><?php echo ($leave_for_year[$k]-$approved_leaves[$k]['leaves']+$leaves_carry_forward[$k]);?><br /></td>
                </tr>
                <?php }
                //}
        }
        else
        {
        echo "<tr><td colspan='9'><div align='center'><b> No Data Present. </b></div></td></tr>";
        }
        ?>
                </table>

 
	<h3>Description of Employee Compensatory Off: </h3>
	<table  class="adminlist" cellpadding="3">
<tr>
<th class="title" width="1%" >Sr.No</th>
<th class="title" width="10%" >Application Date</th>
<th class="title" width="15%" >Date of Holiday Worked</th>
<th class="title" width="15%" >Date of Comp. Off taken</th>
<th class="title" width="25%" >Manager/HR Comment</th>
<th class="title" width="10%" >Status</th>
</tr>
<tr>
<?php
$flag=1;
	$i=1;
	//if(mysql_num_rows($result)){
	$comp_off = $u->getMyPrevCompOffsRecord($user_id,$year);
	if(!empty($comp_off)){	
	//	while($row=mysql_fetch_array($result)){
	foreach($comp_off as $c_o){
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
				echo "<tr class='$tr'><td>".$i++."</td><td>".date('d-M-Y',strtotime($c_o['applied']))."</td><td>".date('d-M-Y',strtotime($c_o['work_date']))."</td><td>";
				//echo ($c_o['compoff_date']=="0000-00-00")?" ":date('d-M-Y',strtotime($c_o['compoff_date']));
				if(date('d-M-Y',strtotime($c_o['compoff_date'])) != '01-Jan-1970'){
					echo date('d-M-Y',strtotime($c_o['compoff_date']));
				}
				echo "</td> <td>".$c_o['comments']."</td>";
		 	if($c_o['status']=="Pending" )
			{
			 echo "<td><font color='red'><b>".$c_o['status']."</b></font></td></tr>";
			}
			else if($c_o['status']=="Cancelled")
			{
			 echo "<td><font color='orange'><b>".$c_o['status']."</b></font></td></tr>";
			}
			else
			{
			 echo "<td><font color='green'><b>".$c_o['status']."</b></font></td></tr>";
			}
	}
	}
	else
	{
	 echo "<tr><td colspan='9'><div align='center'><b> No Data Present. </b></div></td></tr>";
	}
?>
</table>

<?php
/*} else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
*/
}
include ("footer.php");
?>
