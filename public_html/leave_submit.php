<?php
session_start();
include ('../lib/HR.class.php');
?>

<?php
 foreach($_POST as $key=>$val)
	{
	 $$key=mysql_escape_string($val);
 		echo $key."=>".$val."<br>";
	}
	if($form_name=="compensatory")
	{
	if(checkWorkDate($work_dt))
	{

	 $sql="Insert into fi_compoff (empno,mgrno,application_dt,work_date,status) Values ('".$_SESSION['empno']."', '".$_SESSION['mngrno']."', '".date('Y-m-d')."', '".$work_dt."','Pending')";
			#exit($sql);
		if(mysql_query($sql,$con))
		{
		 $id=base64_encode("eno=".$_SESSION['empno']."&adt=".date('Y-m-d')."&wdt=$work_dt&frm=CompOff");
			$body = $_SESSION['cname']." would like to work on $work_dt , which is a company holiday. \nPlease click on the link to Approve/Cancel.\n";
			$body .="For approval or denial url : http://punwebapps.ansys.com/fi_leave/changestatus.php?id=$id";
			$sub="Application for Holiday Working By - ".$_SESSION['cname'];
			$to=$_SESSION['manager_email'];
			mail_sent($to,$sub,$body);
			header("Location: main.php?rs=y");
			exit;
		}
		else
		{
			header("Location: main.php?rs=f");
			exit;
		}
	}
	else
		{
			header("Location: main.php?rs=nc");
			exit;
		}
	}
		
	if($form_name=="leave")
	{
	 // get my location
	 $my_location = getEmployeeLocationId($_SESSION['empid']);

	 $holidays=Array();
		$sql="Select eventdate from fi_holidays WHERE location = 0 OR location = '$my_location' OR location IS NULL";
		$res=mysql_query($sql,$con);
		while($ro=mysql_fetch_array($res))
		{
		 $holidays[]=$ro[0];
		}
	/*	if($half_day=='0.5' && $to_dt==$from_dt)
		{
		 $leave_days=$half_day;
		}
		else
		{$leave_days=get_total_days($from_dt,$to_dt,$holidays);}*/
		if($type_leave=='1')
		{
		 $tmp_leave="Paid Leave";
		}
		else if($type_leave=='2')
		{
		 $tmp_leave="Unpaid Leave";
		}
		else if($type_leave=='3')
		{
		 $tmp_leave="Maternity Leave";
		}
		else if($type_leave=='4')
		{
		 $tmp_leave="Other Leave";
		}
		
		$leave_days=get_total_days($from_dt,$to_dt,$holidays);
		if(!empty($_SESSION['balance_leave']))
				$tmp_bal=$_SESSION['balance_leave']-$leave_days;
		else
				$tmp_bal=$leave_days;
			$sql="Insert into fi_leave (empno,mgrno,leave_year,type_of_leave,application_dt,from_dt,to_dt,no_of_days,reason,status) VALUES ('".$_SESSION['empno']."', '".$_SESSION['mngrno']."', '".date('Y')."','$type_leave','".date('Y-m-d')."', '".$from_dt."', '$to_dt', '$leave_days','$comments','Pending')";
			
			if(mysql_query($sql,$con) or die (mysql_error()))
			{
				$id=base64_encode("eno=".$_SESSION['empno']."&adt=".date('Y-m-d')."&fdt=$from_dt&tdt=$to_dt");
				$body = $_SESSION['cname']." has applied for leave. \nPlease click on the link to Approve/Cancel the leave application.\n";
				$body .="Total Number of Leave Days Requested : $leave_days \n";
				$body .="For approval or denial url : http://punwebapps.ansys.com/fi_leave/changestatus.php?id=$id";
				#$sub="Leave Application By- ".$_SESSION['cname']." for $tmp_leave" ;
                                $sub="Leave Application By- ".$_SESSION['cname']." for ".$tmp_leave." from ".$from_dt." to $to_dt";
				$to=$_SESSION['manager_email'];
				$frm='';
				if($type_leave!='1' || $tmp_bal < -3)
					{
					 $frm='hr@fluent.co.in';
						if ($tmp_bal < -3)
						{
						 $sub= $sub." (Insufficient Leave Balance)";
						 $body .= "\n Balance Leave : ".$tmp_bal;
						 $body .= "\n\n*Insufficient Leave Balance. Contact HR.*\n\n";
						}
					
						$body .= "Remarks By Applicant : ".nl2br($comments);
					}
				mail_sent($to,$sub,$body,$frm);
				header("Location: main.php");
				exit;
			}
			else
			{
				header("Location: main.php?rs=f");
				exit;
			}
		
	}
?>

<? include ("footer.php");?>
