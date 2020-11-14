<?php //main.php start
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
$user_details = $u->getUserDetails($_SESSION['user_id']);
$joinleft_on_date = $u->getEmployeeJoinLeftOnDate();
$leave_types = $u->getLeaveTypes();

//print_r($leave_types);
#print_r($joinleft_on_date);
/*$select = "SELECT cname
FROM fi_emp_list D
WHERE NOT 
EXISTS (

SELECT * 
FROM fi_leave c
WHERE D.empno = c.emp_id
)";
	$res = mysql_query($select);

 while($row1 = mysql_fetch_array($res)){
                   echo $row1['cname'].'  ,   ';
                }
//echo "<pre>". print_r($row). "</pre>";
//print_r($user_details);*/

if(!empty($_POST)){
	if($_POST['submit'] == 'Apply Leave'){
		$insert_leave = $u->applyForLeave($_POST);
		if(!($insert_leave)){
			$msg = $u->error;
		}
		else{
			$msg = 'Leave Application Sent.'; 
		}
	}

	if($_POST['submit'] == 'Apply for Hol.Work'){
		if($u->applyForHolidayWork($_POST)){
			$msg1 = 'Application for Holiday Work sent.';
		}
		else{
			$msg1 = $u->error;
		}
	}

	if($_POST['leave_cancellation'] == 'Request Cancellation'){
//	echo "Request";
//exit;
		if($u->applyToCancelLeave($_POST['leave_id'])){
			$msg = 'Request sent for Leave Cancellation.';
		}
		else{
			$msg = $u->error;
		}
	}

	if($_POST['submit'] == 'Apply for Comp.Off'){
		if($u->applyForCompensatoryOff($_POST)){
			$msg1 = 'Application for Comp. off sent.';
		}
		else{
			$msg1 = $u->error;
		}
	}

	if($_POST['compoff_cancellation'] == 'Request Cancellation'){
		if($u->applyToCancelCompOff($_POST['compoff_id'])){
			$msg = 'Request sent for CompOff Cancellation.';
		}
		else{
			$msg = $u->error;
		}
	}
}

$criteria = array('emp_id'=>$_SESSION['user_id'],'year'=>date('Y'));
$lb = $u->getGeneralLeaveBuckets($criteria);//print_r($lb);
	$total_no_days_in_year=365;
$leave_for_year = $u->getYearLeaves($criteria);
foreach($leave_for_year as $k=>$v){
	$leaves_for_year += $v;
}
$leaves_carry_forward = $u->getLeavesCarriedForward($criteria);
foreach($leaves_carry_forward as $k=>$v){
	$leaves_carry_forwards += $v;
}
$leaves_taken = $u->getMyLeaveRecord(date('Y'));
$leaves_taken_withoutpay = $u->getMywithoutpayLeaveRecord(date('Y'));
//print_r($leaves_taken_withoutpay);
$comp_offs = $u->getMyCompensatoryOffsRecord(date('Y'));

$leavetaken = 0;//initialize

foreach($leaves_taken as $v){
	if($v['status'] == 'Approved' && ($leave_types[$v['leave_type']] != 'Maternity' && $leave_types[$v['leave_type']] != 'Bereavement'))
	$leavetaken += $v['leave_days'];
}

$approved_leaves = $u->getMyApprovedLeaves(date('Y'));
//print_r($approved_leaves);
$manager_name = $u->getMyManagerName();
?>

<html>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/header.php'; ?>

<script type='text/javascript'>
function toggleDiv(){
	//alert('Med');
	var sel = document.getElementById('leave_type');
	var leave_type = sel[sel.selectedIndex].text;
	if(leave_type == 'Sick'){
	var e = document.getElementById("Med");
	e.style.display = 'block';
	var g = document.getElementById("medical_div");
	g.style.display = 'block';
	}
}

function changestatus(eno,adt,fdt,tdt)
	{
	        var flg=confirm("Click 'OK' to request for cancelling leave.");
		if(flg)
	          {
		window.open("http://punwebapps.ansys.com/fi_leave/empchangestatus.php?eno="+eno+"&adt="+adt+"&fdt="+fdt+"&tdt="+tdt,"_blank","toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=1, height=1")
		}
	}
	
	function showyr(v,e)
	{
	 location.href="prev_yr_rpt.php?id="+v+"&eid="+e;
	}
</script>
<?php if($u->isHR()){ 
$manager_name = $u->getMyManager($user_details['empno']);?>
<table>
<tr> 
<td><b> View Your Previous Year Leave Record : </b></td>
<td>
<?php
$current_year = date('Y');
//$y = 2006;
$y = 2013;
while($y < $current_year){
	$pipe = ($y == ($current_year - 1))? '':'|';
	echo "<a href=\"javascript:showyr('$y','".$_SESSION['user_id']."');\">$y</a> $pipe ";
	$y++;
}
?>
</td></tr>
</table>
<?php } ?>
<br>
<table class="tabForm" border="0" cellpadding="1" cellspacing="1" width="60%">
<tbody>
<tr height="17">
<td colsapn="2"><b>Employee Name :</b>&nbsp;&nbsp;<?php echo ucfirst($user_details['cname']);?></td>
<td colsapn="2"><b>Employee No :</b>&nbsp;&nbsp;<?php echo $user_details['empno'];?></td>
</tr>
<tr height="17">
<td colsapn="2"><b>Department :</b>&nbsp;&nbsp;<?php echo $user_details['deptname'];?></td>
<td colsapn="2"><b>Balance leave carried from previous year :</b>
&nbsp;&nbsp;<?php if(!empty($leaves_carry_forwards)){ echo $leaves_carry_forwards;}else{ echo 0;}?>
</td>
</tr>

<tr height="17">
<td colsapn="1"><b>Leave for the Year :</b>&nbsp;&nbsp;<?php echo $leaves_for_year;?></td>
<td colsapn="1"><b>Manager :</b>&nbsp;&nbsp;<?php echo $manager_name;?></td>
<td colsapn="2">&nbsp;</td>
</tr>

</tbody>
</table>
<br/><br/>
<h3>Description of the leave record:</h3>
<font color="red"><?php echo $msg; ?></font>
<table  class="adminlist" cellpadding="3">
<tr>
<th class="title">Sr.No</th>
<th class="title" width="10%" >Application Date</th>
<th class="title" width="10%" >Leave From Date</th>
<th class="title" width="10%" >Leave To Date</th>
<th class="title" width="5%" >Days</th>
<th class="title" width="5%" >Leave Type</th>
<th class="title" width="20%" >Reasons</th>
<th class="title" width="20%" >Manager/HR Comment</th>
<th class="title" id="Med" style="width:96%; margin-top:0px; border-top:none; display:none;" >Medical Certificate</th>
<th class="title" width="10%" >Status</th>
<th class="title" width="10%" >Attachments</th>
<th class="title" width="10%" >Request Cancellation</th>
</tr>
<tr>
<form name='form1' action='' method="POST" onSubmit="return checkData1();" enctype="multipart/form-data">
<input type="hidden" name="form_name" value="leave">
<input type="hidden" name="manager" value="<?php echo $user_details['manager'];?>">
<input type="hidden" name="empno" value="<?php echo $user_details['id'];?>">
<td><img src="images/write.gif"></td>
<td><?php echo " ".date('d-M-Y');?></td>
<td><input type="text" name="from_dt" id="sel1" size="10" readonly>
<img src="images/calendar-Icon.gif" onclick="return showCalendar('sel1', 'y-mm-dd');"></td>
<td><input type="text" name="to_date" id="sel2" size="10" onfocus='this.blur();' readonly >
<img src="images/calendar-Icon.gif" onclick="return showCalendar('sel2', 'y-mm-dd');"></td>
<td></td>
<td>
<select name="leave_type" id="leave_type" onChange="toggleDiv();">
<option value="Not_Selected" selected >Select---</option>
<?php
$sql="Select * from fi_leave_types where status ='Y'";
$res=mysql_query($sql);
while($ro=mysql_fetch_array($res))
{
 echo "<option value='".$ro['id']."'>".$ro['typename']."</option>";
}
?>
</select>
</td>
<td><textarea name='reason' cols="15" rows="1" wrap='hard'></textarea></td>
<td>&nbsp;</td>
<td id="medical_div" style=" border:none;display:none;"><input type="file" name="file" id="pic" value="Photo Upload"/>
</td>
<td><input type="submit" name="submit" value="Apply Leave"></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
</tr>
</form>
<?php
#if(!empty($joinleft_on_date)){
$balance_leaves_count =	($leaves_for_year+$leaves_carry_forwards-$leavetaken);
#}
#else{
#$balance_leaves_count = 0;
#}
		echo "<tr><td colspan='4'>Total No. Of Leaves : <b>". ($leaves_for_year+$leaves_carry_forwards)."</b></td>
		<td colspan='3'>Total No. Of Leave Taken: <b>$leavetaken</b></td>
		<td colspan='4'>Balance Leave: <b>".$balance_leaves_count."</b></td>
		</tr>";

        foreach($leaves_taken as $leave){
	$medical_certificate = $u->getMedicalCertificates($leave['id']);
	if($leave_types[$leave['leave_type']] == 'Bereavement') continue;
	$i++;
	echo "<form name=\"request\" method=\"post\" action=\"\"><tr>
	<td>$i</td>
	<td>".date_format(date_create($leave['applied']), 'd-M-Y')."</td>
	<td>".date_format(date_create($leave['from_dt']), 'd-M-Y')."</td>
	<td>".date_format(date_create($leave['to_date']), 'd-M-Y')."</td>
	<td>$leave[leave_days]</td>
	<td>".$leave_types[$leave['leave_type']]."</td>
	<td>$leave[reason]</td>
	<td>$leave[manager_comment]</td>
	<td>$leave[status]</td>
	<td>"; if(!empty($medical_certificate)){ echo "<a href='uploads/".$medical_certificate."' target='_blank'>Download</a>";} echo "</td>
	<td>";
	if($leave['status'] != 'Cancelled' AND $leave['leave_cancelled'] != 1){ 
	echo "<input type=\"submit\" name=\"leave_cancellation\" value=\"Request Cancellation\"><input type=\"hidden\" name=\"leave_id\" value=\"".$leave[id]."\">";
	} 
	else if($leave['leave_cancelled'] == 1 && $leave['status'] != 'Cancelled'){ echo "Request for Cancellation of Leave Sent";}
	echo "</td>
	</tr></form>";
}

/*
	echo "<tr><td colspan='9'>
	
   If for any reason, you would like to cancel your leave, please contact your manager.
	</td>
*/
	echo "</tr>";
?>
</table>
<br>

 <h3>Description of My Leaves for <?php echo date('Y');?>: &nbsp; </h3>
<br />
        <table  class="adminlist" cellpadding="3">
<tr>
<th class="title">Type of Leave</th>
<th class="title" width="20%" >Leaves for Current Year</th>
<th class="title" width="20%" >Carried Forward Leaves</th>
<th class="title" width="20%" >Total available</th>
<th class="title" width="20%" >Leaves Taken</th>
<th class="title" width="20%" >Balance Leaves</th>
<?php 	$format = "Y-m-d";
 $chkdate = date($format, strtotime("1970-01-01")); ?>
<?php if($user_details['left_on'] > $chkdate){ ?>  <?php } else {?><th class="title" width="20%" >Accrued Leaves for this year</th><?php }?>
</tr>
        <?php
                $i=1;
		foreach($leave_types as $k=>$v){
            if($v == 'Maternity' && $approved_leaves[$k]['leaves'] == 0){
                continue;
            } if($v == 'Withoutpay'){
                continue;
            }
		if($v == 'Bereavement'){
                continue;
            }?>
                <tr>
                <td><?php echo $v;?></td>
                <td><?php if(!empty($leave_for_year[$k])){ echo $leave_for_year[$k];}else{echo 0;}?><br /></td>
                <td><?php if(!empty($leaves_carry_forward[$k])){echo $leaves_carry_forward[$k];} else{ echo 0;}?><br /></td>
                <td><?php echo ($leaves_carry_forward[$k] + $leave_for_year[$k]);?><br /></td>
                <td><?php if(!empty($approved_leaves[$k][leaves])){echo $approved_leaves[$k]['leaves'];} else{ echo 0;}?><br /></td>
                <td>
		<?php 
		if($v == 'Maternity'){
			echo "N/A";
		}
		else{
#if(!empty($joinleft_on_date)){
		echo ($leave_for_year[$k]-$approved_leaves[$k]['leaves']+$leaves_carry_forward[$k]);
#}
#else{
#echo 0;
#}
		}
		?>

		<br />
		</td>
<?php if($user_details['left_on'] > $chkdate){ ?>  <?php } else {?><td><?php
/*
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
/*$current_year = date('Y');
$joining = explode("-",$user_details['joining_date']);//print_r($joining);
$joining_month = $joining[1];
 $joining_year = $joining[0];
$left_on = explode("-",$user_details['left_on']);
$left_on_month = $left_on[1];
$left_on_year = $left_on[0];
$start_date;
$end_date;
$format = "m/d/Y";   // what format to output in
$year   = date("Y"); // Current year 
$first = date($format, strtotime($year."-01-01")); 
$total_no_days_in_year=365;

if($joining_year==$current_year) {
$start_date = $user_details['joining_date'];
}
else{
$start_date = $first;
}


if(($left_on_year==$current_year) && ($user_details['left_on'] < date('Y-m-d'))){
$end_date = $user_details['left_on'];
}
else{
$end_date = date('Y-m-d');
}
//echo $start_date."<br/>";
//echo $end_date."<br/>";
//echo $leave_for_year[$k]."<br/>";
//$days_served_in_year = $end_date - $start_date;   //(find out the days)
$days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
//print $days."<br/>";
$accrued_leaves =round(($days/$total_no_days_in_year)*$leave_for_year[$k]);

echo $accrued_leaves;*/
/*$current_year = date('Y');
$joining = explode("-",$user_details['joining_date']);//print_r($joining);
$joining_month = $joining[1];
 $joining_year = $joining[0];
$left_on = explode("-",$user_details['left_on']);
$left_on_month = $left_on[1];
$left_on_year = $left_on[0];
$start_date;
$end_date;
$format = "Y-m-d";   // what format to output in
$year   = date("Y"); // Current year 
 $first = date($format, strtotime($year."-01-01")); 
$total_no_days_in_year=365;

if($joining_year==$current_year) {
$start_date = $user_details['joining_date'];
}
else{
$start_date = $first;
}


if(($left_on_year==$current_year) && ($user_details['left_on'] < date('Y-m-d'))){
$end_date = $user_details['left_on'];
}
else{
$end_date = date('Y-m-d');
}
$criteria['year']=date("Y");
$criteria['month']=date("m");
$criteria['date']=date("d");
 $start_date."<br/>";
 $end_date."<br/>";
//echo $lb[$k]."<br/>";
//echo $k;
//$diff =ceil(abs(strtotime($end_date)-strtotime($start_date)) / 86400);
//print_r($leaves_b)."<br/>";
 $days=round(((strtotime($end_date)-strtotime($start_date)) / 86400));
//$days = round(strtotime($end_date)-strtotime($start_date)/(60*60*24));
//$days = $u->_date_diff($end_date, $start_date);
//echo $days=dateDiff($end_date, $start_date);
//print $days."<br/>";
$accrued_leaves =round(($days/$total_no_days_in_year)*$lb[$k]);*/
$days=$u->getAccruedLeaves($emp_id);
echo $accrued_leaves =round(($days/$total_no_days_in_year)*$lb[$k]);



?>
</td><?php }?>
                </tr>
                <?php }
        ?>
                </table>

<br>

 <h3>Description of My Without Pay Leaves for <?php echo date('Y');?>: &nbsp; </h3>
<br />
        <table  class="adminlist" cellpadding="3">
<tr>
<th class="title">Type of Leave</th>
<th class="title" width="20%" >From date</th>
<th class="title" width="20%" >To Date</th>
<th class="title" width="20%" >Status</th>
<th class="title" width="20%" >Total Days</th>
</tr>
  <?php foreach($leaves_taken_withoutpay as $v){?>
                <tr>
                <td>Without Pay Leaves</td>
                <td><?php echo date_format(date_create($v['from_dt']), 'd-M-Y');?></td>
		<td><?php echo date_format(date_create($v['to_date']), 'd-M-Y');?></td>
		<td><?php if($v['approved']==0){ echo "Pending";}else {echo "Approved";}?></td>
		<td><?php echo $v['days'];?></td>
                </tr>
     <?php }
        ?>          
 </table>


 <h3>Description of My Bereavement Leaves for <?php echo date('Y');?>: &nbsp; </h3>
<br />
        <table  class="adminlist" cellpadding="3">
<tr>
<th class="title">Type of Leave</th>
<th class="title" width="20%" >From date</th>
<th class="title" width="20%" >To Date</th>
<th class="title" width="20%" >Status</th>
<th class="title" width="20%" >Total Days</th>
</tr>
  <?php foreach($leaves_taken as $leave){
	if($leave_types[$leave['leave_type']] == 'Bereavement'){
?>
                <tr>
                <td>Bereavement Leaves</td>
                <td><?php echo date_format(date_create($leave['from_dt']), 'd-M-Y');?></td>
                <td><?php echo date_format(date_create($leave['to_date']), 'd-M-Y');?></td>
		<td><?php echo $leave['status'];?></td>
		<td><?php echo $leave['leave_days'];?></td>
                </tr>
     <?php 
	} #if ends
	} #foreach ends
        ?>          
 </table>

<h3>Description of the Compensatory Work done:</h3>
<font color="red"><?php echo $msg1; ?></font>
<table  class="adminlist" cellpadding="3">
<tr>
<th class="title" width="1%" >Sr.No</th>
<th class="title" width="10%" >Application Date</th>
<th class="title" width="15%" >Date of Holiday Worked</th>
<th class="title" width="15%" >Date of Comp. Off taken</th>
<th class="title" width="25%" >Manager/HR Comment</th>
<th class="title" width="10%" >Status</th>
<th class="title" width="10%" >Request Cancellation</th>
</tr>
<tr class='row0'>
<form name='form2' action='' method="POST" onSubmit="return checkData2();">
<input type="hidden" name="form_name" value="compensatory">
<td><img src="images/write.gif"></td>
<td><?php echo " ".date('d-M-Y');?></td>
<td><input type="text" name="work_dt" id="sel3" size="10" readonly>
<img src="images/calendar-Icon.gif" onclick="return showCalendar('sel3', 'y-mm-dd');"></td>
<td><!--<input type="text" name="compoff_date" id="sel4" size="10" readonly>
<img src="images/calendar-Icon.gif" onclick="return showCalendar('sel4', 'y-mm-dd');">-->
&nbsp;</td>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Apply for Hol.Work"></td>
</form>
</tr>
<?php
foreach($comp_offs as $c_o){
?>
<form name="apply_for_comp_off" method="post" action="">
<?php
	echo "<tr>
	<td>$i</td>
	<td>".date_format(date_create($c_o['applied']),'d-M-Y')."</td>
	<td>".date_format(date_create($c_o['work_date']),'d-M-Y')."</td>
	<td>"; 
    if(!empty($c_o['compoff_date'])){ echo date_format(date_create($c_o['compoff_date']), 'd-M-Y');} else if($c_o[status] == 'Approved'){ ?><input type="text" name="compoff_date" id="sel4" size="10" readonly>
<img src="images/calendar-Icon.gif" onclick="return showCalendar('sel4', 'y-mm-dd');"> <?php } else{ echo "&nbsp;"; }  echo "</td>
	<td>$c_o[comments]</td>
	<td>"; if($c_o[status] == 'Approved' && empty($c_o[compoff_date])){ echo "<input type=\"submit\" name=\"submit\" value=\"Apply for Comp.Off\">"; } else{ echo $c_o[status]; } echo "</td>
	<td>";
	if($c_o['status'] != 'Cancelled' AND $c_o['compoff_cancelled'] != 1){ 
	echo "<input type=\"submit\" name=\"compoff_cancellation\" value=\"Request Cancellation\"><input type=\"hidden\" name=\"compoff_id\" value=\"".$c_o[id]."\">";
	} 
	else if($c_o['compoff_cancelled'] == 1 && $c_o['status'] != 'Cancelled'){ echo "Request for Cancellation of CompOff Sent";}
	echo "</td>
	</tr>";
    $i++;
?>
<input type="hidden" name="comp_off_id" value="<?php echo $c_o[id];?>">
</form>
<?php
}
?>
</table>
<?php 
include 'footer.php';
?>
</body>
</html>
