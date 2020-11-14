<?php
include $_SERVER['DOCUMENT_ROOT']."/include/php_header.php";
include ("header.php");
$emp_id = $_GET['emp_id'];

$criteria = array('emp_id'=>$emp_id,'year'=>date('Y'));
$e = new Employee($emp_id);
$user_details = $e->getUserDetails($emp_id);
$manager_id = $user_details['manager'];
$manager_details = $e->getUserDetails($manager_id);
$hr_details = $u->getUserDetails($u->user_id);
//print_r($user_details);
//print_r($_POST);

// for mail
$to = "$user_details[email],$manager_details[email]";
$from = $hr_details['email'];
$sub = "Application for Leave Without Pay";
$body = "Respected, \r\n Please approve my Leave. ";
$lb = $e->getGeneralLeaveBuckets($criteria);//print_r($lb);
	$total_no_days_in_year=365;
#print_r($to);
$leave_for_year = $e->getYearLeaves($criteria);//print_r($leave_for_year);
foreach($leave_for_year as $k=>$v){
        $leaves_for_year += $v;
}



$leaves_carry_forward = $e->getLeavesCarriedForward($criteria);
$leaves_carry_forwards=0;
foreach($leaves_carry_forward as $k=>$v){
        $leaves_carry_forwards += $v;
}
$leaves_taken = $e->getMyLeaveRecord(date('Y'));
$leave_types = $e->getLeaveTypes();
$leavetaken=0;
foreach($leaves_taken as $v){
	if($v['status'] == 'Approved' && ($leave_types[$v['leave_type']] != 'Maternity' && $leave_types[$v['leave_type']] != 'Bereavement'))
        $leavetaken += $v['leave_days'];
}

if($_POST['submit'] =='Cancel' && !empty($_POST['leave_id'])){
                $cancel_leave = $u->cancelLeave($_POST['leave_id'],$_POST['comment']);
                if(!($cancel_leave)){
                $msg = $u->error;
                }
                else{
                $msg = '<font color="red">Leave has been Cancelled.</font>';
                }
        }
if($_POST['submit'] =='Approve' && !empty($_POST['leave_id'])){
                $approval = $u->approveLeave($_POST['leave_id'],$_POST['comment']);
                if(!($approval)){
                $msg = $u->error;
                }
                else{
                $msg = '<font color="green">Leave has been Approved.</font>';
                }
        }
if($_POST['action'] =='Approve' && !empty($_POST['comp_off_id'])){
                $approval = $u->approveCompOff($_POST['comp_off_id'],$_POST['comments']);
                if(!($approval)){
                $msg = $u->error;
                }
                else{
                $msg = '<font color="green">Comp off has been Approved.</font>';
                }
        }
if($_POST['action'] =='Cancel' && !empty($_POST['comp_off_id'])){
                $approval = $u->cancelCompOff($_POST['comp_off_id'],$_POST['comments']);
                if(!($approval)){
                $msg = $u->error;
                }
                else{
                $msg = '<font color="green">Comp off has been cancelled.</font>';
                }
        }
if($_POST['submit'] == 'Approve'&& !empty($_POST['bre_id'])){

$approval = $u->approveLeave($_POST['bre_id'],$_POST['comment']);
                if(!($approval)){
                $msg = $u->error;
                }
                else{
                $msg = '<font color="green">Leave has been Approved.</font>';
                }
}
if($_POST['submit'] =='Cancel' && !empty($_POST['bre_id'])){
                $cancel_leave = $u->cancelBereavementLeave($_POST['bre_id'],$_POST['comment']);
                if(!($cancel_leave)){
                $msg = $u->error;
                }
                else{
                $msg = '<font color="red">Leave has been Cancelled.</font>';
                }
        }
if($_POST['action'] == 'Update leave type'){
	if($u->updateLeave($_POST)){
		$msg = '<font color="green">Leave updated.</font>';
	}
}
if($_POST['action'] == 'Add LWP'){
	$u->addLeaveWithoutPay($emp_id, $_POST['from_dt'], $_POST['to_date']);
	mail_sent($to,$sub,$body,$frm); 
}

if($_POST['action'] == 'Delete LWP'){
	$u->removeLeaveWithoutPay($_POST['lwp_id']);
}
if($_POST['action'] == 'Delete comp off')
{
	$u->removeLeavecompoff($_POST['comp_off_id']);
}

if($_POST['action'] == 'Delete Bereavement Leave')
{
	$u->removeLeavebereavement($_POST['bre_id']);
}
if($_POST['action'] == 'Delete Leave')
{
	$u->removeLeave($_POST['leave_id']);
}
if($_POST['action'] == 'Disapprove'){
        $u->disApproveLeaveWithoutPay($_POST['lwp_id']);
}

if($_POST['action'] == 'Approve'){
        $u->approveLeaveWithoutPay($_POST['lwp_id']);
}

$reports = $u->getLeaveRecordOfReport($emp_id, date('Y'));
$approved_leaves = $e->getMyApprovedLeaves(date('Y'));
$compoff = $e->getMyCompensatoryOffsRecord(date('Y'));


if($_SESSION['user_id'])
{ ?>
<script language='Javascript'>
function changestatus(eno,adt,fdt,tdt)
	{
	 //window.open("http://india-internal.ansys.com/local/hr/fi_leave/hrchangestatus.php?eno="+eno+"&adt="+adt+"&fdt="+fdt+"&tdt="+tdt,"_blank", "width=700,height=600");
	 window.open("hrchangestatus.php?eno="+eno+"&adt="+adt+"&fdt="+fdt+"&tdt="+tdt,"_blank", "width=700,height=600");
	}
	
	function showyr(v,e)
	{
	 location.href="prev_yr_rpt.php?id="+v+"&eid="+e;
	}
</script>
<?php 
if($u->isHR()){
?>
<table>
<tr> 
<td><b> View Previous Year Leave Record : </b></td>
<td> 
<?php
$current_year = date('Y');
//$y = 2006;
$y = 2013;
while($y < $current_year){
	$pipe = ($y == ($current_year - 1))? '':'|';
	echo "<a href=\"javascript:showyr('$y','".$_GET['emp_id']."');\">$y</a> $pipe ";
	$y++;
}
?>
</td></tr>
</table>
<?php }//isHR check ?>

 <h3>Description of employee leave record: <u><? echo $user_details['cname'];?></u>&nbsp;&nbsp;   EmpNo: <?php echo $user_details['empno'];?></h3>
<?php
//changes by rutuja
if($u->isHR()){
$manager_name = $u->getMyManager($user_details['empno']);?>
 <a href="edit_emp.php?empno=<?php echo $_GET['emp_id']; ?>">Edit this employee</a> &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;
&emsp; &emsp;&emsp; &emsp; &emsp; &emsp;&emsp;  &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp;<span align="right">
    <b>Manager Name:&emsp;<?php echo ucwords($manager_name);?></b></span>
<br />
<?php
echo $msg;
}
 else {
    $manager_name = $u->getMyManager($user_details['empno']);?>
 <a href="edit_emp.php?empno=<?php echo $_GET['emp_id']; ?>">Edit this employee</a> &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;
&emsp; &emsp;&emsp; &emsp; &emsp; &emsp;&emsp;  &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp;<span align="right">
    <b>Manager Name:&emsp;<?php echo ucwords($manager_name);?></b></span>
<br />
<?php
echo $msg;
}
?>
<br />
	<table  class="adminlist" cellpadding="3">
<tr>
<th class="title"  >Sr.No</th>
<th class="title" width="10%" >Application Date</th>
<th class="title" width="10%" >Leave From Date</th>
<th class="title" width="10%" >Leave To Date</th>
<th class="title" width="5%" >Days</th>
<th class="title" width="5%" >Leave Type</th>
<th class="title" width="10%" >Remark</th>
<th class="title" width="10%" >Manager/HR Comment</th>
<th class="title" width="10%" >Status</th>
<th class="title" width="10%" >Attachments</th>
<th class="title" width="10%" >Cancel/Approve Leave</th>
</tr>
<?php
$i=1;
$leave_types_id = $u->getLeaveTypes();
$leaves_id = array_flip($leave_types);
$bereavement_id = $leaves_id['Bereavement'];

//echo "<br> ";
	if(!empty($reports))
	{
		foreach($reports as $row)
		{
		$medical_certificate = $u->getMedicalCertificates($row['id']);
			if($row['leave_type'] == $bereavement_id) continue;
			echo "<tr class='$tr'>
			<td>".$i++."</td><td>".date('d-M-Y',strtotime($row['applied']))."</td><td>".date('d-M-Y',strtotime($row['from_dt']))."</td><td>".date('d-M-Y',strtotime($row['to_date']))."</td> <td>".$row['leave_days']."</td>
		<td>";
		if($u->isHR()){
			echo "<form name=\"update_leave_type\" method=\"post\" action=\"\">
			<input type=\"hidden\" name=\"leave_id\" value=\"$row[id]\">
			<input type=\"hidden\" name=\"action\" value=\"Update leave type\">";
			echo "<select name=\"leave_type\" onchange=\"this.form.submit();\">";
			foreach($leave_types as $lt_id=>$lt){
				if($lt_id == $row['leave_type']){
				$selected = "selected";
				}
				else{ $selected = '';}
				echo "<option value=\"".$lt_id."\" $selected>$lt</option>";
			}
			echo "</select>";
			echo "</form>";
			
		}
		else{
			echo $leave_types[$row['leave_type']];
		}
		echo "</td>
		<td>".$row['reason']."</td>
		<td>
			<form method=\"post\" action=\"\">
			<textarea cols='5' rows='5' name='comment'>".$row['manager_comment']."</textarea>
		</td>"; 
		    if($row['status']=="Pending")
                        {
                         echo "<td><font color='red'><b>".$row['status']."</b></font></td>";
                        }
                        else if($row['status']=="Cancelled")
                        {
                         echo "<td><font color='orange'><b>".$row['status']."</b></font></td>";
                        }
                        else
                        {
                         echo "<td><font color='green'><b>".$row['status']."</b></font></td>";
                                $tmp_taken +=$row['leave_days'];
                        }
			echo "<td>"; if(!empty($medical_certificate)){ echo "<a href='uploads/".$medical_certificate."' target='_blank'>Download</a>"; } echo "</td>";


			//if($row['status'] == ''){
			echo "<td>";
			echo "<input type=\"hidden\" value=\"$row[id]\" name=\"leave_id\">";
//Added By Rupali
if($u->isHR() && $row['status']=='Cancelled')
                        { echo "<input type=\"submit\" name=\"action\" value=\"Delete Leave\" onclick=\"return confirm('Are you sure you want to delete leave?')";echo "<input type=\"hidden\" value=\"$row[id]\" name=\"leave_id\">"; }
//end ?>

			<?php if(($u->isManager() || $u->isHR()) && $row['status'] != 'Approved')
                              {
			       echo "<input type=\"submit\" value=\"Approve\" name=\"submit\">";
			      } 
                             else if($row['status'] != 'Approved' && $u->isHR()  && $leave_types[$row['leave_type']]=='Bereavement'){
			       echo "<input type=\"submit\" value=\"Approve\" name=\"submit\" >";
                              }
			if($row['status'] != 'Cancelled'){
			echo "<input type=\"submit\" value=\"Cancel\" name=\"submit\">";
			}

			echo "</form>";
			echo "</td>";
			//}
			echo "</tr>";
		}
		echo "<tr><td colspan='4'>Total No. Of Leave : <b>".($leaves_for_year+$leaves_carry_forwards)."</b><br />
		Year Leaves: <b>".$leaves_for_year."</b><br /> Leaves Carried Forward: <b>".$leaves_carry_forwards."</b></td> 
		<td colspan='3'>Total No. Of Leave Taken: <b>$leavetaken</b></td>
		<td colspan='2'>Balance Leave: <b>".($leaves_for_year+$leaves_carry_forwards-$leavetaken)."</b></td>
		</tr>";
	}	
	else
	{
	 echo "<tr><td colspan='9'><div align='center'><b> No Data Present. </b></div></td></tr>";
	}
?>
</table>

 
<h3>Description of Leaves for <?php echo date('Y');?>: </h3>
        <table  class="adminlist" cellpadding="3">
<tr>
<th class="title">Type of Leave</th>
<th class="title" width="20%" >Total Leaves for Current Year</th>
<th class="title" width="20%" >Carried Forward Leaves</th>
<th class="title" width="20%" >Leaves Taken</th>
<th class="title" width="20%" >Balance Leaves</th>
<?php 	$format = "Y-m-d";
 $chkdate = date($format, strtotime("1970-01-01")); ?>
<?php //if($user_details['left_on'] > $chkdate){ ?>  <?php //} else {?><th class="title" width="20%" >Accrued Leaves for this year</th><?php //}?>
</tr>
        <?php
                $i=1;
                foreach($leave_types as $k=>$v){
                    //if(($v == 'Maternity' || $v == 'Withoutpay' || $v == 'Bereavement') && $approved_leaves[$k]['leaves'] == 0){
                    if(($v == 'Maternity' || $v == 'Withoutpay' || $v == 'Bereavement')){
                        continue;
                    }
                ?>
                <tr>
                <td><?php echo $v;?></td>
                <td><?php if(!empty($leave_for_year[$k])){ echo $leave_for_year[$k];}else{echo 0;}?><br /></td>
                <td><?php if(!empty($leaves_carry_forward[$k])){echo $leaves_carry_forward[$k];} else{ echo 0;}?><br /></td>
                <td><?php if(!empty($approved_leaves[$k]['leaves'])){echo $approved_leaves[$k]['leaves'];} else{ echo 0;}?><br /></td>
                <td>
		<?php 
		if($v == 'Maternity'){
		echo "N/A";
		}
		else{
		echo ($leave_for_year[$k]-$approved_leaves[$k]['leaves']+$leaves_carry_forward[$k]); //$lbal=$leave_for_year[$k]-$approved_leaves[$k]['leaves']+$leaves_carry_forward[$k];
		}
		?>
		<br /></td>
<?php //if($user_details['left_on'] > $chkdate){ ?>  <?php// } else {?>
<td><?php  /*$m= date('m'); $accearnl=$leave_for_year[$k]/12*$m; 
$current_year = date('Y');
$joining = explode("-",$user_details['joining_date']);
$joining_month = $joining[1];
$left_on = explode("-",$user_details['left_on']);
$left_on_month = $left_on[1];

if(preg_match("/$current_year/",$user_details['left_on'])){
	if($m >= $left_on_month){
		$accearnl = 0;
	}
	else{
		$accearnl=$leave_for_year[$k]/12*$m;
	}
}
else{
	if(preg_match("/$current_year/",$user_details['joining_date'])){
		$accearnl = ($leave_for_year[$k]/12)*($m-$joining_month);
	}
	else{
		$accearnl=$leave_for_year[$k]/12*$m;
	}
}
echo round($accearnl);*/


 $days=$e->getAccruedLeaves($emp_id);
echo $accrued_leaves =round(($days/$total_no_days_in_year)*$lb[$k]);
?></td><?php //} ?>
                </tr>
                <?php } 
                //}
        //}
        //else
        //{
         //echo "<tr><td colspan='9'><div align='center'><b> No Data Present. </b></div></td></tr>";
        //}
       ?>
                </table>



	<h3>Description of employee's compensatory offs: </h3>
	<table  class="adminlist" cellpadding="3">
<tr>
<th class="title" width="1%" >Sr.No</th>
<th class="title" width="10%" >Application Date</th>
<th class="title" width="15%" >Date of Holiday Worked</th>
<th class="title" width="15%" >Date of Comp. Off taken</th>
<th class="title" width="25%" >Manager/HR Comment</th>
<th class="title" width="10%" >Status</th>
<th class="title" width="10%" >Action</th>
</tr>
<script type="text/javascript">
    var elems = document.getElementsByClassName('confirmation');
    var confirmIt = function (e) {
        if (!confirm('Are you sure?')) e.preventDefault();
    };
    for (var i = 0, l = elems.length; i < l; i++) {
        elems[i].addEventListener('click', confirmIt, false);
    }
</script>
<?php
 $flag=1;
	$i=1;
	if(!empty($compoff))
	{
		foreach($compoff as $row)
		{
			echo "<form action=\"\" method=\"post\">";
			echo "<input type=\"hidden\" name=\"comp_off_id\" value=\"$row[id]\" />";
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
				echo "<tr class='$tr'><td>".$i++."</td><td>".date('d-M-Y',strtotime($row['applied']))."</td><td>".date('d-M-Y',strtotime($row['work_date']))."</td><td>";
				echo ($row['compoff_date']=="0000-00-00" || $row['compoff_date'] == null)?" ":date('d-M-Y',strtotime($row['compoff_date']));
				echo "</td> 
				<td><textarea name=\"comments\">".$row['comments']."</textarea></td>";
				echo "<td>".$row['status']."</td>";
		 if($row['status']=="Pending" )
			{
			 echo "<td>
				<input type=\"submit\" name=\"action\" value=\"Approve\"/> 
				<input type=\"submit\" name=\"action\" value=\"Cancel\"/>"; 
				"</td></tr>";
			}
			else if($row['status']=="Cancelled")
			{
			 echo "<td>";
				if($u->isHR()){ echo "<input type=\"submit\" name=\"action\" value=\"Delete comp off\" onclick=\"return confirm('Are you sure you want to delete comp off?');\">";
						 }"<!--input type=\"submit\" name=\"action\" value=\"Approve\"/--> 
			</td></tr>";
			}
			else		
			{
			 echo "<td>
				<input type=\"submit\" name=\"action\" value=\"Cancel\"/> 
			</td></tr>";
			}
		echo "</form>";
	}
	}
	else
	{
	 echo "<tr><td colspan='9'><div align='center'><b> No Data Present. </b></div></td></tr>";
	}
	
?>
</table>

<?php
	$lwp = $u->getLeavesWithoutPay($emp_id);
?>

<h3>Description of employee's leaves without pay: </h3>
<table  class="adminlist" cellpadding="3">
<tr>
<th class="title" width="1%" >Sr.No</th>
<th class="title" width="10%" >From</th>
<th class="title" width="10%" >To</th>
<th class="title" width="2%" >Days</th>
<th class="title" width="5%" >Status</th>
<?php if($u->isHR()){ ?>
<th class="title" width="15%" >Action</th>
<?php } ?>
</tr>
<?php if($u->isHR()){
?>
<tr>
<form method="post">
<td></td>
<td>
<input id="sel1" type="text" readonly="" size="10" name="from_dt">
<img onclick="return showCalendar('sel1', 'y-mm-dd');" src="images/calendar-Icon.gif">
</td>
<td>
<input id="sel2" type="text" readonly="" onfocus="this.blur();" size="10" name="to_date">
<img onclick="return showCalendar('sel2', 'y-mm-dd');" src="images/calendar-Icon.gif">
</td>
<td></td>
<td></td>
<!--<td><input type="checkbox" value="1" name="approved"/></td>-->
<td><input type="submit" name="action" value="Add LWP" /></td>
</form>
</tr>
<?php } ?>
<?php
foreach ($lwp as $l){
echo "<tr>
<td>".++$j."</td>
<td>".date('d-M-Y',strtotime($l[from_dt]))."</td>
<td>".date('d-M-Y',strtotime($l[to_date]))."</td>
<td>$l[days]</td>";
if($l['approved']==1){
echo "<td><font color=\"green\" ><b>Approved</b></font></td>";
}else{
echo "<td><font color=\"red\" ><b>Disapproved</b></font></td>";
}
if($u->isHR()){
?>
<td>
	<?php
	echo "
	<form method=\"post\">
	<input type=\"hidden\" name=\"lwp_id\" value=\"$l[id]\" />
	<input type=\"submit\" name=\"action\" value=\"Delete LWP\"/>";
	if($l['approved']==1){
	echo "<input type=\"submit\" name=\"action\" value=\"Disapprove\"/>";
	}else{
	echo "<input type=\"submit\" name=\"action\" value=\"Approve\" />";
	}

	echo "</form>";
	?>
</td>
<?php }} ?>
</tr>
 
</table>


<?php
	$bereavement_leaves = $u->getBereavementLeaves($emp_id);
$b = 0;
?>

<h3>Description of employee's Bereavement Leaves: </h3>
<table  class="adminlist" cellpadding="3">
<tr>
<th class="title" width="1%" >Sr.No</th>
<th class="title" width="10%" >From</th>
<th class="title" width="10%" >To</th>
<th class="title" width="2%" >Days</th>
<th class="title" width="5%" >Status</th>
<?php if($u->isHR()){
?><th class="title" width="15%" >Action</th><?php }?>
</tr>
<?php if($u->isHR()){
?>
<!--
<tr>
<form method="post">
<td></td>
<td>
<input id="sel1" type="text" readonly="" size="10" name="from_dt">
<img onclick="return showCalendar('sel1', 'y-mm-dd');" src="images/calendar-Icon.gif">
</td>
<td>
<input id="sel2" type="text" readonly="" onfocus="this.blur();" size="10" name="to_date">
<img onclick="return showCalendar('sel2', 'y-mm-dd');" src="images/calendar-Icon.gif">
</td>
<td></td>
<td></td>
<td><input type="submit" name="action" value="Add LWP" /></td>
</form>
</tr>
-->
<?php } ?>
<?php
foreach ($bereavement_leaves as $l){
echo "<tr>
<td>".++$b."</td>
<td>".date('d-M-Y',strtotime($l[from_dt]))."</td>
<td>".date('d-M-Y',strtotime($l[to_date]))."</td>
<td>$l[leave_days]</td>";
if($l['status']=="Pending")
                        {
                         echo "<td><font color='red'><b>".$l['status']."</b></font></td>";
                        }
                        else if($row['status']=="Cancelled")
                        {
                         echo "<td><font color='orange'><b>".$l['status']."</b></font></td>";
                        }
                        else
                        {
                         echo "<td><font color='green'><b>".$l['status']."</b></font></td>";
                                $tmp_taken +=$l['leave_days'];
                        }
if($u->isHR()){ 
echo "<td><form method=\"post\">";
//Added By Rupali
	if($l['status']=='Cancelled')
                        { echo "<input type=\"submit\" name=\"action\" value=\"Delete Bereavement Leave\" onclick=\"return confirm('Are you sure you want to delete bereavementleave?')";echo "<input type=\"hidden\" name=\"bre_id\" value=\"$l[id]\" />"; }
//End
	echo "<input type=\"hidden\" name=\"bre_id\" value=\"$l[id]\" />";


?>
			<?php  if($l['status']=="Pending"){
			       echo "<input type=\"submit\" value=\"Approve\" name=\"submit\" >";
                              }

			if($l['status'] != 'Cancelled'){
			echo "<input type=\"submit\" value=\"Cancel\" name=\"submit\">";
			} 
	echo "</form></td>";
 }
echo "</tr>";
}
?>
</table>

<?php
} else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
?>

<?php
include ("footer.php");
?>
