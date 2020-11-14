<?php
include_once "User.class.php";

class Employee extends User{

    public function __construct($user_id){
	parent::__construct($user_id);
        if(!$this->isEmployee()){
        	$this->_loginRedirect();
        	throw new Exception('No privileges');
        }
    }

/*
function setError() 
    assign error to the class variable $error.
*/


/*
Leave application
*/
public function getConfig($id){
	$select = "SELECT value FROM fi_config
		WHERE param ='$id'";
	$res = mysqli_query($this->dbh,$select) or die(mysqli_error(). $select);
	$row = mysqli_fetch_assoc($res);
	return $row['value'];
}

public function UpdateConfig($post){

	 $update = sprintf("UPDATE fi_config SET value = '%s' WHERE param = '%s'",
		mysqli_real_escape_string($this->dbh,$post['eventdate']), 
		mysqli_real_escape_string($this->dbh,$post['eventname']));
	$res = mysqli_query($this->dbh,$update);
	if($res){
		$this->setError('Your values is updated now.');
	  return true;
	}
	
}
public function applyForLeave($details){
        $approved_leaves = $this->getApprovedCountOfLeaveType($details['leave_type']);  

//Get number of days of leave considering holidays
	 $applied_leave_days = $this->getTotalLeaveDays($details['from_dt'],$details['to_date']);

//Get number of days of leave without considering holidays, weekends
	$applied_leave_days_all = $this->getTotalLeaveDaysInverveningAll($details['from_dt'],$details['to_date']);

$criteria = array('emp_id'=>$details[empno],
			'year' =>date('Y'),
			'leave_type_id'=>$details['leave_type']);

    //Have enough balance? No? Show error
	$balance_leaves = $this->getMyLeaveBalance($criteria);	
	$leavemaxdate = $this->getConfig('leave_max_date');	
//echo $leavemaxdate."==================".date($details['to_date']);
        //$leavemaxdate= date('Y-m-d', strtotime('Dec 31'));
        //echo "hi".$leavemaxdate;
if(date($details['to_date']) > $leavemaxdate){
		$this->setError('Invalid leave period. Your selected to date is too long');
		return false;
	}
if(date($details['to_date']) < date($details['from_dt'])){
		$this->setError('Invalid leave period.');
		return false;
	}
	if($this->isLeaveOverlapping($details['from_dt'],$details['to_date'])){
		$this->setError('Your leave days overlap some other leave application. Please check and apply again.');
		return false;
	}
  
	$leave_types = $this->getLeaveTypes();


## Newly Added code 17 Nov 2015
	/*if($leave_types[$details['leave_type']] != 'Sick' && $leave_types[$details['leave_type']] != 'Earned' && $leave_types[$details['leave_type']] != 'Bereavement'){
		if($applied_leave_days > 2){
			$this->setError('You can not take 3 or more days leave under this Leave Type.');	
			return false;
		}
	}*/


	if($leave_types[$details['leave_type']] == 'Casual'){
		if($applied_leave_days > 2){
			$this->setError('You can\'t take Casual Leaves more than 2 Days at a time');	
			return false;
		}

        //Don't allow more than 10 Casual leaves in a year

        if(($approved_leaves + $applied_leave_days) > 10){
            $this->setError('You can not take more than 10 casual leaves in a year.');
            return false;
        }
	}
	//Don't allow more than 3 Bereavement leaves in a year

	if($leave_types[$details['leave_type']] == 'Bereavement'){
           if(($approved_leaves + $applied_leave_days) > 3){
			$this->setError('You can\'t take Bereavement Leaves more than 3 Days in a year.');	
			return false;
		}
            else{
		$insert = sprintf("INSERT INTO fi_leave
			(`emp_id`,`applied`,`manager`,`leave_type`,
			`from_dt`,`to_date`,`reason`,`manager_comment`,
			`status`,`leave_days`)
			VALUES('%s',NOW(),'%s','%s',
			'%s','%s','%s','%s',
			'Pending',$applied_leave_days)",
			mysqli_real_escape_string($this->dbh,$this->user_id),
			mysqli_real_escape_string($this->dbh,$details['manager']),
			mysqli_real_escape_string($this->dbh,$details['leave_type']),
			mysqli_real_escape_string($this->dbh,$details['from_dt']),
			mysqli_real_escape_string($this->dbh,$details['to_date']),
			mysqli_real_escape_string($this->dbh,$details['reason']),
			mysqli_real_escape_string($this->dbh,$details['manager_comment']));
		$res = mysqli_query($this->dbh,$insert);
		if($res){
			$this->notifyManager('Bereavement', mysqli_insert_id());
			return true;
		}
		else{
    			$this->setError('There was an error. Your Bereavement Leave application was not sent.');
    			return false;
		}
                  
	    }

       
	}
	
      


	if($leave_types[$details['leave_type']] == 'Withoutpay')
	{
	$insert = sprintf("INSERT INTO fi_lwp
				(`emp_id`,`from_dt`,`to_date`,`applied`,`approved`,`days`)
				VALUES('%s','%s','%s',NOW(),
				'0',$applied_leave_days)",
				mysqli_real_escape_string($this->dbh,$this->user_id),
				mysqli_real_escape_string($this->dbh,$details['from_dt']),
				mysqli_real_escape_string($this->dbh,$details['to_date']));
			
			$res = mysqli_query($this->dbh,$insert);
			if($res){
				$this->notifyManager('Withoutpay', mysqli_insert_id());                      
				//header('Location:main.php');
				return true;
			}
			else{
    				$this->setError('There was an error. Your LWP application was not sent.');
    				return false;
			}
	}

###### Code added  on 29 Feb 2016 to attach Medical Certificate
	if($leave_types[$details['leave_type']] == 'Sick' && $applied_leave_days_all > 2 && $_FILES['file']['name'] == ''){
			$this->setError('You need to upload Medical Certificate for Sick leave 3 or more than 3 days.');	
			return false;
	}
####### Code ends

	if(($balance_leaves >= $applied_leave_days) || 
        ($leave_types[$details['leave_type']] == 'Maternity') || 
        ($leave_types[$details['leave_type']] == 'Earned' && ($balance_leaves + 3) >= $applied_leave_days)){
		$insert = sprintf("INSERT INTO fi_leave
			(`emp_id`,`applied`,`manager`,`leave_type`,
			`from_dt`,`to_date`,`reason`,`manager_comment`,
			`status`,`leave_days`)
			VALUES('%s',NOW(),'%s','%s',
			'%s','%s','%s','%s',
			'Pending',$applied_leave_days)",
			mysqli_real_escape_string($this->dbh,$this->user_id),
			mysqli_real_escape_string($this->dbh,$details['manager']),
			mysqli_real_escape_string($this->dbh,$details['leave_type']),
			mysqli_real_escape_string($this->dbh,$details['from_dt']),
			mysqli_real_escape_string($this->dbh,$details['to_date']),
			mysqli_real_escape_string($this->dbh,$details['reason']),
			mysqli_real_escape_string($this->dbh,$details['manager_comment']));
		$res = mysqli_query($this->dbh,$insert) or die("Error: ".$insert);
		$last_leave_id = mysqli_insert_id();

###### Code added  on 29 Feb 2016 to attach Medical Certificate
		$path = "uploads/";
                if($_FILES['file']['name']!=''){

                        $fname=$this->user_id."_".$last_leave_id."_".$_FILES['file']['name'];
                        $allowed_file_types = array('jpeg','jpg','png');
                        $filecheck = basename($_FILES['file']['name']);
                        $ext = strtolower(substr($filecheck, strrpos($filecheck, '.') + 1));
			//echo $fname;
                        $ftmpname=$_FILES['file']['tmp_name'];

                        if(move_uploaded_file($ftmpname, $path.$fname))
                        {
			// insert statement
			$file_insert = sprintf("INSERT INTO fi_medical_certificates
                        (`leave_id`,`file_name`)
                        VALUES('$last_leave_id','%s')",
                        mysqli_real_escape_string($this->dbh,$fname));
                	$res = mysqli_query($this->dbh,$file_insert);
			}else{
                             $this->setError('File Not uploaded');
                        }

		}

########### Code 29 Feb ends

		if($res){
			$this->notifyManager('Leave', mysqli_insert_id());
			return true;
		}
	}
	else{
    		$this->setError('You don\'t have enough leave balance.');
    		return false;
	}
}

/*
function checkWorkDate($sdate)
{
 $res_date=Array('01-26','05-01','08-15','10-02');
 $exp_date = strtotime($sdate);
 $dt=date('m-d',$exp_date);
        if (in_array($dt,$res_date))
{ $valid = FALSE; }
else { $valid = TRUE; }
return $valid;
}
*/

public function isHoliday($date){
	// Check if a date is a Saturday/Sunday or a holiday for a Employee's location
	$holidays1 = $this->getMyHolidays();
	$holidays = array();

		foreach($holidays1 as $holiday =>$day){
			array_push($holidays,$day['eventdate']);
		}

	$time1 = strtotime($date);
	
	if(in_array($date, $holidays) || date('l', $time1) == "Saturday" || date('l', $time1) == "Sunday") {
		// date is not a holiday
		return true;
	}
	return false;
}


/*
Comp off application
*/
public function applyForHolidayWork($details){
    //ref fi_comp_offs
       /*$cd=$details['work_dt'];
           $coi=$this->user_id;
 echo $select = "SELECT id FROM fi_compoff
		WHERE work_date =".$cd." AND emp_id =".$coi;

	  $re = mysqli_query($this->dbh,$select);
echo $value = mysqli_result($re, 0);
	//$row = mysqli_fetch_assoc($re);
     if(empty($value)){ 
		$this->setError('You already applided for this date.');
		return false;
	}   */

	if($this->isHoliday($details['work_dt'])){
		$insert = sprintf("INSERT INTO fi_compoff 
			(`emp_id`,`work_date`,`applied`,`status`)
 			VALUES('%s','%s',NOW(),'Pending')",
			mysqli_real_escape_string($this->dbh,$this->user_id),
			mysqli_real_escape_string($this->dbh,$details['work_dt'])
			);
		$res = mysqli_query($this->dbh,$insert);
		if($res){
                        $body = $this->user_profile['cname']." would like to work on ".$details['work_dt']." , which is a company holiday. \nPlease click on the link to Approve/Cancel.\n";
                        $body .="For approval or denial url : http://punwebapps.ansys.com/fi_leave/changestatus.php?id=$id";
                        $sub="Application for Holiday Working By - ".$this->user_profile['cname'];
                        $to=$this->user_profile['manager_email'];
                        $this->mail_sent($to,$sub,$body);

		    $this->notifyManager('Holiday working', mysqli_insert_id());
		}

		else{
			$this->setError('You already applided for this date.');
			return false;
		}
		return true;
	}
	else{
		$this->setError('This is not a Company Holiday.');
		return false;
	}
 
}


public function applyForCompensatoryOff($details){
 


	$update = sprintf("UPDATE fi_compoff SET compoff_date = '%s',
			status = 'Pending'
			WHERE id = '%s' 
			AND status = 'Approved'",
		mysqli_real_escape_string($this->dbh,$details['compoff_date']),
		mysqli_real_escape_string($this->dbh,$details['comp_off_id']));
	$res = mysqli_query($this->dbh,$update);
	if(!$res){
		$this->setError('Your application was not sent.');
		return false;
	}
	$this->notifyManager('Compensatory off', mysqli_insert_id());
	return true;
}


/*
Leave records
*/
public function getMyLeaveRecord($year){
    // select all rows of leaves of a calendar year e.g. 2013
	$select = "SELECT * FROM fi_leave 
		WHERE from_dt LIKE '%$year%' AND emp_id=$this->user_id"; //AND status='Approved'";
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select);
	while ($row = mysqli_fetch_assoc($res)){
		$leaves[] = $row;
	}
	return $leaves;
}
/*
Leave WithOut Pay records
*/
public function getMywithoutpayLeaveRecord($year){
    // select all rows of leaves of a calendar year e.g. 2013
	$select = "SELECT * FROM fi_lwp 
		WHERE from_dt LIKE '%$year%' AND emp_id=$this->user_id"; //AND status='Approved'";
	$res = mysqli_query($this->dbh, $select) or die("Error: ".$select);
	while ($row = mysqli_fetch_assoc($res)){
		$leaves[] = $row;
	}
	return $leaves;
}

/* 
Compensatory offs of the logged in employee
*/

public function getMyCompensatoryOffsRecord($year){
	 $select = "SELECT * FROM fi_compoff 
		WHERE `work_date` LIKE '$year%' AND emp_id = $this->user_id
		ORDER BY work_date DESC";
	$res = mysqli_query($this->dbh, $select) or die($select. mysqli_error());
	$comp_offs = array();
	while($row = mysqli_fetch_assoc($res)){
		$comp_offs[] = $row;
	}
	return $comp_offs;
}

/* 
Calculate leave balance
*/
public function getMyLeaveBalance($criteria){
    //$criteria['year'], $criteria['leave_type_id']
    // should return array of balances
    // based on various leave buckets
    //return an array with leave_type_id as keys
	$year_leaves = $this->getYearLeaves($criteria);
    $leaves_carried_forward = $this->getLeavesCarriedForward($criteria);

	$bucket_leaves = $year_leaves[$criteria[leave_type_id]];
	$carried_forward_leaves = $leaves_carried_forward[$criteria[leave_type_id]];

	$total_leaves=$bucket_leaves + $carried_forward_leaves;
	$select = "SELECT SUM(leave_days) AS tot_leaves_taken 
		FROM fi_leave 
		WHERE from_dt LIKE '%$criteria[year]%'
		AND leave_type = '$criteria[leave_type_id]'
		AND emp_id = '$this->user_id'
		AND status = 'Approved'";
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select);
	$emp_leaves = mysqli_fetch_assoc($res);
	$leaves_taken = $emp_leaves['tot_leaves_taken'];
	
	$balance_leaves = $total_leaves - $leaves_taken;
	return $balance_leaves;
}

public function getYearLeaves($criteria){
	// If an employee has joined in the current year, 
	// his buckets will be different from the rest 
	// i.e. if fi_employee_leave_buckets has any buckets recorded for this user,
	// use them else use the general buckets for all.

	 $select = "SELECT leave_type_id, maximum
		FROM fi_employee_leave_buckets
		WHERE
		employee_id = $this->user_id
		AND 
		year='$criteria[year]'";
//echo $select;
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select); 
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type_id']] = $row['maximum'];
	}
	if(mysqli_num_rows($res)>0){
		return $leaves;
	}
	// else proceed
		
	$select="SELECT leave_type_id,maximum 
		FROM fi_leave_buckets 
		WHERE 
		year='$criteria[year]'";
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select); 
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type_id']] = $row['maximum'];
	}
	return $leaves;
}

public function getGeneralLeaveBuckets($criteria){
	$select="SELECT leave_type_id,maximum 
		FROM fi_leave_buckets 
		WHERE 
		year='$criteria[year]'";
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select); 
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type_id']] = $row['maximum'];
	}
	return $leaves;

}
public function getAccruedLeaves($emp_id){
$criteria = array('emp_id'=>$emp_id,'year'=>date('Y'));
$user_details = $this->getUserDetails($emp_id);
$lb = $this->getGeneralLeaveBuckets($criteria);//print_r($lb);
	$current_year = date('Y');
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

	return $days;		
}
public function getEmployeeLeaveBuckets($user_id,$year){
	$select = "SELECT * FROM fi_employee_leave_buckets 
		LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type_id 
		WHERE year='$year' AND employee_id='$user_id'";
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select);
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type_id']]=$row;
	}
	return $leaves;		
}

/* Get leave carried forward to year */
public function getLeavesCarriedForward($criteria){
   //keys of criteria may be year, leave_type_id 
   //refer db table fi_leaves_carry_forwards 
   //return an array with leave_type_id as keys
	$select="SELECT leave_type,no_of_leaves 
		FROM fi_leave_carry_forwards 
		WHERE 
		emp_id = $this->user_id
		AND year = '$criteria[year]'";
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select); 
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type']] = $row['no_of_leaves'];
	}
	return $leaves;
}

public function getMyApprovedLeaves($year){
	 $select = "SELECT leave_type,sum(leave_days) AS leaves,typename 
		FROM fi_leave LEFT JOIN fi_leave_types ON leave_type=fi_leave_types.id 
		WHERE fi_leave.status='Approved' 
		AND emp_id=$this->user_id AND from_dt LIKE '%$year%' 
		GROUP BY leave_type";
	$res = mysqli_query($this->dbh, $select) OR die("Error: ".$select);
	if(mysqli_num_rows($res)>0){
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type']] = $row;
	}
	return $leaves;
	}
}

function changeMyPassword($details){
	$update = sprintf("UPDATE fi_emp_list SET password='%s' WHERE id='%s'",
		mysqli_real_escape_string($this->dbh,md5($details['password'])),
		mysqli_real_escape_string($this->dbh,$this->user_id));
	if(!($res=mysqli_query($this->dbh,$update))){
		$this->setError(mysqli_error($update));
		return false;
	}
	return true;
}

function sendResetPasswordNotification($user_id){
        $user_details = $this->getUserDetails($user_id);
        $template_vars = $user_details;
        $template_vars['server'] = $_SERVER['HTTP_HOST'];
        sendTemplateEmail($user_details['email'],$this->app_config['reset_password_notification_subject_path'],$this->app_config['reset_password_notification_path'],$template_vars);
}

function changePassword($details){
        if($user_id = $this->authenticateMD5($details['username'],$details['pwd'])){
                if($details['newpwd']==$details['newpwdagn']){
                        $update = sprintf("UPDATE fi_emp_list SET password='%s' WHERE id='%s'",
                                mysqli_real_escape_string($this->dbh,md5($details['newpwd'])),
                                mysqli_real_escape_string($this->dbh,$user_id));
                        if(!($res=mysqli_query($this->dbh,$update))){
                                $this->setError(mysqli_error().$update);
                                return false;
                        }
                        $log = debug_backtrace();
                        $this->createActionLog($log);
                        return true;
                }else{
                        $this->setError("New Password and Retype Password does not match.");
                        return false;
                }
        }
}

public function getHolidaysBetweenDates($date1, $date2){ //echo "hiii";
    //return list of dates between a date range including both the start and end date
    // i.e. including the above two date parameters above
	$my_location = $this->user_profile['location'];
	$select = "SELECT eventdate FROM fi_holidays 
		WHERE eventdate between '$date1' and '$date2'
		AND (`location` is NULL OR `location` = '0' OR `location` =".$my_location.")";

	$res = mysqli_query($this->dbh,$select) or die($select . mysqli_error());
	$holidays = array();
	if(mysqli_num_rows($res) > 0){
		while($rows = mysqli_fetch_array($res)){
			$dates[] = $rows;	 
		}
		foreach($dates as $date){
			array_push($holidays,$date[0]);
		}
		//print_r($holidays);
		//return $dates;
		return $holidays;
	}
}



/*
    Calculate the number of leave-days for a particular vacation period
    Exclude Saturdays, Sundays and the holidays
*/
function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while( $current <= $last ) {

        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }

    return $dates;
}
function getTotalLeaveDays($date1, $date2) {  //echo $_SESSION['user_id'];
$holy_dates = $this->getHolidaysBetweenDates($date1, $date2);

  $time1  = strtotime($date1);
  $time2  = strtotime($date2);

while($time1 <= $time2){
	if(date('l', $time1) != "Saturday" && date('l', $time1) != "Sunday" && !in_array(date('Y-m-d', $time1),$holy_dates))
	$dates[] = date('Y-m-d', $time1);
	$time1 = strtotime((date('Y-m-d', $time1).' +1days'));
}//echo count($dates);
   return count($dates);
}


/*
    Calculate the number of leave-days for a particular vacation period
    Including Saturdays, Sundays and the holidays
*/
function getTotalLeaveDaysInverveningAll($date1, $date2) {
  $time1  = strtotime($date1);
  $time2  = strtotime($date2);

while($time1 <= $time2){
	$dates[] = date('Y-m-d', $time1);
	$time1 = strtotime((date('Y-m-d', $time1).' +1days'));
}//echo count($dates);
   return count($dates);
}

public function getOUs(){
	$select = "SELECT * FROM fi_ou";
	$res = mysqli_query($this->dbh,$select) or die("Error:".$select);
	while($row = mysqli_fetch_assoc($res)){
		$ous[] = $row; 
	}
	return $ous;
}


/* Get Previous Year Details */
public function getPrevYearLeaves($criteria){
	// If an employee has joined in the current year, 
	// his buckets will be different from the rest 
	// i.e. if fi_employee_leave_buckets has any buckets recorded for this user,
	// use them else use the general buckets for all.

	$select = "SELECT leave_type_id, maximum
		FROM fi_employee_leave_buckets
		WHERE
		employee_id = '$criteria[emp_id]'
		AND 
		year='$criteria[year]'";
//echo $select;
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select); 
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type_id']] = $row['maximum'];
	}
	if(mysqli_num_rows($res)>0){
		return $leaves;
	}
	// else proceed
		
	$select="SELECT leave_type_id,maximum 
		FROM fi_leave_buckets 
		WHERE 
		year='$criteria[year]'";
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select); 
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type_id']] = $row['maximum'];
	}
	return $leaves;
}

public function getCustomBucketYear($emp_id){
	$select = "SELECT year
                FROM fi_employee_leave_buckets
                WHERE
                employee_id = '$emp_id' limit 1";
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select); 
	$row = mysqli_fetch_assoc($res);
	return $row['year'];
}

public function getPrevYearReport($user_id,$year){
	/*$select = "SELECT a.*, b.typename, c.empno 
		FROM fi_leave a, fi_leave_types b, fi_emp_list c 
		WHERE a.emp_id=c.empno 
		AND a.leave_type=b.id AND (a.from_dt like '%$year%') 
		AND c.empno='$user_id' 
		ORDER BY a.from_dt DESC";
	*/
	$select = "SELECT *,fi_leave.status as leave_status FROM fi_leave 
		LEFT JOIN fi_leave_types ON leave_type=fi_leave_types.id
		WHERE emp_id = '$user_id'
		AND from_dt LIKE '%$year%'
		ORDER BY from_dt DESC";
	$res = mysqli_query($this->dbh,$select);
	while($row = mysqli_fetch_assoc($res)){
		$reports[] = $row;
	}
	return $reports;
}

public function getMyPrevCompOffsRecord($user_id,$year){
	$select = "SELECT * FROM fi_compoff 
		WHERE `work_date` LIKE '$year%' AND emp_id = '$user_id'
		ORDER BY work_date DESC";
	$res = mysqli_query($this->dbh,$select) or die($select. mysqli_error());
	$comp_offs = array();
	while($row = mysqli_fetch_assoc($res)){
		$comp_offs[] = $row;
	}
	return $comp_offs;
}

public function getMyPrevApprovedLeaves($user_id,$year){
	$select = "SELECT leave_type,sum(leave_days) AS leaves,typename 
		FROM fi_leave LEFT JOIN fi_leave_types ON leave_type=fi_leave_types.id 
		WHERE fi_leave.status='Approved' 
		AND emp_id='$user_id' AND from_dt LIKE '%$year%' 
		GROUP BY leave_type";
	$res = mysqli_query($this->dbh,$select) OR die("Error: ".$select);
	if(mysqli_num_rows($res)>0){
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type']] = $row;
	}
	return $leaves;
	}
}
public function getPrevLeavesCarriedForward($criteria){
	$select="SELECT leave_type,no_of_leaves 
		FROM fi_leave_carry_forwards 
		WHERE 
		emp_id = '$criteria[emp_id]'
		AND year = '$criteria[year]'";
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select); 
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type']] = $row['no_of_leaves'];
	}
	return $leaves;
}

/**** Prev year details section ends *********/


public function getLeaveRecordOfReport($employee_id, $year){
    /*
    if(!$this->isMyReport($employee_id)){
        $this->setError('Not your report');
        return false;
    }
    */
    //$select
        $select = "SELECT * FROM fi_leave
                WHERE emp_id='$employee_id' AND from_dt LIKE '%$year%'";
        $res = mysqli_query($this->dbh,$select);
        if(mysqli_num_rows($res) > 0){
                while($row = mysqli_fetch_assoc($res)){
                        $reports[] = $row;
                }
        }
        return $reports;
    //return array of leaves taken by employee in a year

}

/*
	Get holidays for the location of the Employee
*/
public function getMyHolidays(){
		$my_location = $this->user_profile['location'];
                $select = "SELECT eventdate, eventname
                FROM fi_holidays 
		WHERE  (`location` is NULL OR `location` = '0' OR `location` =".$my_location.")";
                $res = mysqli_query($this->dbh,$select) or die(mysqli_error().$select);
                $holidays = array();
                while($row = mysqli_fetch_assoc($res)){
                        $holidays[] = $row;
                }
                return $holidays;
        }

public function isLeaveOverlapping($start_date, $end_date){
	$select = "SELECT * FROM fi_leave
		WHERE emp_id = $this->user_id
		AND status != 'Cancelled'
		AND (
		(UNIX_TIMESTAMP(`from_dt`) <= UNIX_TIMESTAMP('$start_date') AND UNIX_TIMESTAMP(`to_date`) >= UNIX_TIMESTAMP('$start_date'))  
		OR
		(UNIX_TIMESTAMP(`from_dt`) <= UNIX_TIMESTAMP('$end_date') AND UNIX_TIMESTAMP(`to_date`) >= UNIX_TIMESTAMP('$end_date'))
		)";
//echo $select;
	$res = mysqli_query($this->dbh,$select) or die(mysqli_error().$select);
	if(mysqli_num_rows($res) > 0){
		$this->setError('Your leave days overlap some other leave application. Please check and apply again.');
		return true;
	}
	return false;
}

public function isMaternityLeave($leave_id){
	$select = "SELECT 1 FROM fi_leave 
		LEFT JOIN fi_leave_types ON fi_leave.leave_type = fi_leave_types.id
		WHERE fi_leave.id = $leave_id
		AND fi_leave_types.typename = 'Maternity'";
	$res = mysqli_query($this->dbh,$select) or die(mysqli_error().$select);
	if(mysqli_num_rows($res) > 0){
		return true;
	}
	return false;
}


function notifyManager($type_of_notification, $id){
//echo "18June 2015 ".$type_of_notification." Notify Manager";
//echo $type_of_notification;exit;
		$headers = '';
		if($type_of_notification == 'Leave'){
                        $leave_details = $this->getLeaveDetails($id);
                        $leave_types = $this->getLeaveTypes();
                        $leave_type = $leave_types[$leave_details['leave_type']];

//echo $leave_type;
                        $sub= "Leave Application by ".$this->user_profile['cname']. " for ".$leave_type." leave from ".$leave_details['from_dt']." to ".$leave_details['to_date'];
                        $body = $this->user_profile['cname']." has applied for leave.\nPlease click on the link to Approve/Cancel the leave application.\n";
                        $body .= "Total number of leave Days Requested : ".$leave_details['leave_days']."\n";
                        //$body .= "For approval or denial url : http://".$_SERVER['HTTP_HOST']."/\n";
                        $body .= "For approval or denial url : https://account.activedirectory.windowsazure.com/r#/applications/\n";
                        $body .= "Please select Leave management System - IN.\n";

			
		}
		else if($type_of_notification == 'Leave Cancellation'){
                        $leave_details = $this->getLeaveDetails($id);
                        $leave_types = $this->getLeaveTypes();
                        $leave_type = $leave_types[$leave_details['leave_type']];

//echo $leave_type;
                        $sub= "Leave Cancellation request by ".$this->user_profile['cname']. " for ".$leave_type." leave from ".$leave_details['from_dt']." to ".$leave_details['to_date'];
                        $body = $this->user_profile['cname']." has applied for leave cancellation.\n";
                        $body .= "Please click on the link to Cancel the application.\nhttps://account.activedirectory.windowsazure.com/r#/applications/\n";
                        $body .= "Please select Leave management System - IN.\n";
                        $body .= "Total number of leave Days Requested : ".$leave_details['leave_days']."\n";
                        $body .= "Date: ".$leave_details['from_dt'].' to '.$leave_details['to_date'];
//echo $sub."\n";
//echo $body;
			$headers = "megha.vaze@ansys.com , mir.hussain@ansys.com";

		}
		else if($type_of_notification == 'Holiday working'){
			//compoff application
                        $sub= "Holiday Working Application by ".$this->user_profile['cname'];
                        $body = $this->user_profile['cname']." has applied for work on holiday.\nPlease login to the leave management system to Approve/Cancel.\n";
			/*
			$body .= "\n\nLeave Management Application\n\n
			http://".$_SERVER['HTTP_HOST']."/\n\n";
			*/
			$body .= "\n\nLeave Management Application\n\n
			https://account.activedirectory.windowsazure.com/r#/applications/\n\n";
                        $body .= "Please select Leave management System - IN.\n";
//echo $sub."\n";
//echo $body;
		}
                else if($type_of_notification == 'Withoutpay')
			{
                        $leave_details = $this->getwithoutpayLeaveDetails($id);
                        $leave_types = $this->getLeaveTypes();
                        $leave_type = $leave_types[$leave_details['leave_type']];

                        $sub= "WithoutPay Leave Application by ".$this->user_profile['cname'];
                        $body = $this->user_profile['cname']." has applied for withoutpay.\nPlease login to the leave management system to Approve/Cancel.\n";
			
			$body .= "\n\nLeave Management Application\n\n
			https://account.activedirectory.windowsazure.com/r#/applications/\n\n";
                        $body .= "Please select Leave management System - IN.\n";
			$this->notifyHR('Withoutpay', $id);
						
			}
		else if($type_of_notification == 'Bereavement')
			{
			$leave_details = $this->getLeaveDetails($id);
                        $leave_types = $this->getLeaveTypes();
                        $leave_type = $leave_types[$leave_details['leave_type']];

                        $sub= "Bereavement Leave Application by ".$this->user_profile['cname'];
                        $body = $this->user_profile['cname']." has applied for Bereavement.\nPlease login to the leave management system to Approve/Cancel.\n";
			
			$body .= "\n\nLeave Management Application\n\n
			https://account.activedirectory.windowsazure.com/r#/applications/\n\n";
                        $body .= "Please select Leave management System - IN.\n";
			 $leave_details = $this->getLeaveDetails($id);		
			$this->notifyHR('Bereavement', $id);
						
			}
		else{
			//compoff application
                        $body = $this->user_profile['cname']." has applied for a compensatory off.\nPlease login to the leave management system to Approve/Cancel.\n";
                        $sub= $type_of_notification ." by ".$this->user_profile['cname'];
			/*
			$body .= "\n\nLeave Management Application\n\n
			http://".$_SERVER['HTTP_HOST']."/\n\n";
			*/
			$body .= "\n\nLeave Management Application\n\n
			https://account.activedirectory.windowsazure.com/r#/applications/\n\n";
                        $body .= "Please select Leave management System - IN.\n";
		}
$to=$this->getMyManagerEmail();
//$this->mail_sent($to,$sub,$body,$headers);
$this->sendSMTPEmail($to,$sub,$body);
}

public function getMyManagerEmail(){
	$select = "SELECT email FROM fi_emp_list
		WHERE id = (SELECT manager FROM fi_emp_list 
			WHERE id = $this->user_id)";
	$res = mysqli_query($this->dbh,$select) or die(mysqli_error(). $select);
	$row = mysqli_fetch_assoc($res);
//echo $row['email'];
	return $row['email'];
}

public function getMyManagerName(){
	$select = "SELECT cname FROM fi_emp_list
		WHERE id = (SELECT manager FROM fi_emp_list 
			WHERE id = $this->user_id)";
	$res = mysqli_query($this->dbh, $select) or die(mysqli_error(). $select);
	$row = mysqli_fetch_assoc($res);
	return $row['cname'];
}
public function getMyManager($id){
	 /*$select = "SELECT cname FROM fi_emp_list
		WHERE id = (SELECT manager FROM fi_emp_list 
			WHERE id = $id)";*/
    //changes by rutuja :in where clause column name is changed from id to empno
         $select = "SELECT cname FROM fi_emp_list
		WHERE id = (SELECT manager FROM fi_emp_list 
			WHERE empno = $id)";
        
	$res = mysqli_query($this->dbh, $select) or die(mysqli_error(). $select);
	$row = mysqli_fetch_assoc($res);
	return $row['cname'];
}
public function getallManager(){
	
         $select = "SELECT cname,id FROM fi_emp_list ";
	$res = mysqli_query($this->dbh, $select) or die(mysqli_error(). $select);
	while($row = mysqli_fetch_assoc($res)){
		$m_name[] = $row; 
	}
	return $m_name;
}
public function getLeaveDetails($leave_id){
	$select = "SELECT * FROM fi_leave 
		WHERE id = $leave_id";
	$res = mysqli_query($this->dbh,$select);
	$row = mysqli_fetch_assoc($res);
	return $row;
}
public function getLeaveDetailsCompoff($leave_id){
	$select = "SELECT * FROM fi_compoff
		WHERE id = $leave_id";
	$res = mysqli_query($this->dbh,$select);
	$row = mysqli_fetch_assoc($res);
	return $row;
}
public function getCompOffDetails($compoff_id){
	$select = "SELECT * FROM fi_compoff 
		WHERE id = $compoff_id";
	$res = mysqli_query($this->dbh,$select);
	$row = mysqli_fetch_assoc($res);
	return $row;
}
public function getwithoutpayLeaveDetails($leave_id){
	$select = "SELECT * FROM fi_lwp 
		WHERE id = $leave_id";
	$res = mysqli_query($this->dbh,$select);
	$row = mysqli_fetch_assoc($res);
	return $row;
}
public function getApprovedCountOfLeaveType($leave_type_id){
    $start_of_year = date('Y').'-01-01 00:00:00';
   $select = "SELECT SUM(leave_days) AS sum FROM fi_leave
        WHERE emp_id = $this->user_id
        AND from_dt > '$start_of_year'
        AND leave_type = $leave_type_id
        AND status = 'Approved'";

    $res = mysqli_query($this->dbh,$select);
    $row = mysqli_fetch_assoc($res);
    return $row['sum'];
}

public function getDeptId($deptname){
	$select = "SELECT id FROM fi_dept WHERE deptname='$deptname'";
	$res = mysqli_query($this->dbh,$select);
	$row = mysqli_fetch_assoc($res);       
	return $row['id'];
}

public function getLocationHREmail(){
	$dept = $this->getDeptId('1501 Human Resources');
        //echo $dept;
        
	$my_location = $this->user_profile['location'];
	$select = "SELECT email FROM fi_emp_list
		WHERE id IN (SELECT id FROM fi_emp_list 
			WHERE dept = $dept AND location = $my_location)";
        //echo $select;exit;
	$res = mysqli_query($this->dbh,$select) or die(mysqli_error(). $select);
	while($row = mysqli_fetch_assoc($res)){
		$emails[] = $row['email'];
	}
	return $emails;
}

function notifyHR($type_of_notification, $id){
//echo "18June 2015 ".$type_of_notification." Notify HR";
		if($type_of_notification == 'Leave Cancellation'){
                        $leave_details = $this->getLeaveDetails($id);
                        $leave_types = $this->getLeaveTypes();
                        $leave_type = $leave_types[$leave_details['leave_type']];

//echo $leave_type;
                        $sub= "Leave Cancellation request by ".$this->user_profile['cname']. " for ".$leave_type." leave from ".$leave_details['from_dt']." to ".$leave_details['to_date'];
                        $body = $this->user_profile['cname']." has applied for leave cancellation.\n";
                        $body .= "Please click on the link to Cancel the application.\nhttps://account.activedirectory.windowsazure.com/r#/applications/\n";
                        $body .= "Please select Leave management System - IN.\n";
                        $body .= "Total number of leave Days Requested : ".$leave_details['leave_days']."\n";
                        $body .= "Date: ".$leave_details['from_dt'].' to '.$leave_details['to_date'];
//echo $sub."\n";
//echo $body;
		}
                  if($type_of_notification == 'Withoutpay'){
                        $leave_details = $this->getwithoutpayLeaveDetails($id);
                        $leave_types = $this->getLeaveTypes();
                        $leave_type = $leave_types[$leave_details['leave_type']];
//echo "hiii";
//echo $leave_type;
                        $sub= "Leave WithoutPay request by ".$this->user_profile['cname']. " for ".$leave_type." leave from ".$leave_details['from_dt']." to ".$leave_details['to_date'];
                        $body = $this->user_profile['cname']." has applied for leave Without Pay.\n";
                        //$body .= "Please click on the link to Cancel the application.\nhttp://punlms1.ansys.com/\n";
                         $body .= "For approval or denial url : https://account.activedirectory.windowsazure.com/r#/applications/\n";
                        $body .= "Please select Leave management System - IN.\n";
                        $body .= "Total number of leave Days Requested : ".$leave_details['days']."\n";
                        $body .= "Date: ".$leave_details['from_dt'].' to '.$leave_details['to_date'];
//echo $sub."\n";
//echo $body;
		}   
		if($type_of_notification == 'Bereavement'){
                        $leave_details = $this->getLeaveDetails($id);
                        $leave_types = $this->getLeaveTypes();
                        $leave_type = $leave_types[$leave_details['leave_type']];

//echo $leave_type;
                        $sub= "Leave Bereavement request by ".$this->user_profile['cname']. " for ".$leave_type." leave from ".$leave_details['from_dt']." to ".$leave_details['to_date'];
                        $body = $this->user_profile['cname']." has applied for leave Bereavement.\n";
                        //$body .= "Please click on the link to Cancel the application.\nhttp://punlms1.ansys.com/\n";
                         $body .= "For approval or denial url : https://account.activedirectory.windowsazure.com/r#/applications/\n";
                        $body .= "Please select Leave management System - IN.\n";
                        $body .= "Total number of leave Days Requested : ".$leave_details['leave_days']."\n";
                        $body .= "Date: ".$leave_details['from_dt'].' to '.$leave_details['to_date'];
//echo $sub."\n";
//echo $body;
		}
   $emails=$this->getLocationHREmail();

			foreach($emails as $to){
                        	//$this->mail_sent($to,$sub,$body);
                                $this->sendSMTPEmail($to,$sub,$body);
			}
}

function applyToCancelLeave($leave_id){
	 $update_query = "UPDATE fi_leave set leave_cancelled = '1' WHERE id='$leave_id'";

	$result = mysqli_query($this->dbh,$update_query) or die("Could not cancel this leave")
;
	$leave_details = $this->getLeaveDetails($leave_id);
	if($this->user_id == $leave_details['emp_id']){
		//check the status of leave, pending -> sendmail to manager else sendmail to hr
//18June edit asked by Megha
//		if($leave_details['status'] == 'Pending'){
			$this->notifyManager('Leave Cancellation', $leave_id);
//			return true;
//		}
//		else if($leave_details['status'] == 'Approved'){
			//sendmail to HR
			$this->notifyHR('Leave Cancellation', $leave_id);
			return true;
//		}
	}
	else{
		$this->setError('You did not apply for cancellation of this leave.');
		return false;
	}
}

function applyToCancelCompOff($compoff_id){
	$update_query = "UPDATE fi_compoff set compoff_cancelled = '1' WHERE id='$compoff_id'";
	$result = mysqli_query($this->dbh,$update_query) or die("Could not calcel this leave")
;
	$leave_details = $this->getCompOffDetails($compoff_id);
	if($this->user_id == $leave_details['emp_id']){
		//check the status of leave, pending -> sendmail to manager else sendmail to hr
//6June17 edit asked by Megha
			$this->notifyManager('CompOff Cancellation', $compoff_id);
			//sendmail to HR
			$this->notifyHR('Leave Cancellation', $compoff_id);
			return true;
	}
	else{
		$this->setError('You did not apply for cancellation of this compoff.');
		return false;
	}
}
function getEmployeeJoinLeftOnDate($criteria){
	$emp_id = $criteria['emp_id'];
	$left_on_date = $criteria['year']."-01-01";
	$select = "SELECT * FROM fi_emp_list WHERE 
	joining_date < NOW() AND (left_on > '$left_on_date' or left_on is null or left_on = 0) 
	AND id='$emp_id'";
	$res = mysqli_query($this->dbh,$select);
	$result = mysqli_fetch_assoc($res);
	return $result;
}

function getMedicalCertificates($leave_id){
        $select = "SELECT file_name FROM fi_medical_certificates
                WHERE leave_id = '$leave_id'";
        $res = mysqli_query($this->dbh, $select) or die(mysqli_error() . $select);
        $medical_certificate = mysqli_fetch_assoc($res);
        return $medical_certificate['file_name'];
}

/*
function getLeaveTypes(){

}
*/

##################################################################################
}// End Of Employee class


/*function sendSMTPEmail($to, $name, $email_subject, $email_body) {
        if (empty($to)) {
            return false;
        }
############ Send mail by SMTP
        $app_config = $this->app_config;
        //print_r($app_config);
        $mail = new PHPMailer();
        $mail->IsSMTP(true);
        $mail->Host = $app_config['mail_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $app_config['mail_username'];  // SMTP username
        $mail->Password = $app_config['mail_password']; // SMTP password
        $mail->Port = $app_config['port']; // not 587 for ssl
        $mail->SMTPSecure = $app_config['SMTPSecure'];
        $mail->SetFrom('carvingtesting@gmail.com', 'Resume Portal');
        $mail->AddAddress($to, $name);
        $mail->AddAddress('rutuja@carvingit.com', $name);
        $mail->AddAddress('jadhavpriyanka26@gmail.com');

        $mail->SMTPDebug = 1;
        $mail->IsHTML(true);
        $mail->Subject = $email_subject;
        $mail->Body = $email_body;

        if (!$mail->Send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            $this->setError("<span class=\"message\" style='color:#FF0000'>Mailer Error:" . $mail->ErrorInfo . "</span>");
            echo $this->setError();
            return false;
        }
        ///Upload user profile photo

        return true;
    }*/
?>
