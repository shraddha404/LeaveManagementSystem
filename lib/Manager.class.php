<?php
include_once 'Employee.class.php';

class Manager extends Employee{

public function __construct($user_id){
parent::__construct($user_id);

        if(!$this->isManager() && !$this->isHR()){
        $this->_loginRedirect();
        throw new Exception('No privileges');
        }
}

/*
Approve leave
*/
public function approveLeave($leave_id,$comment){
	if($this->isMaternityLeave($leave_id)){
		$this->setError('Only HR can approve maternity leaves. Please contact HR');
		return false;
	}
        $details = $this->getLeaveDetails($leave_id);
	$leave_types = $this->getLeaveTypes();
	if($leave_types[$details['leave_type']]!= 'Bereavement'){
    	if(!$this->hasLeaveBalance($leave_id)){
        $this->setError('This employee does not have enough leaves in balance.');
        return false;
    	}
	}
       
    if($leave_types[$details['leave_type']] == 'Casual'){
        $e = new Employee($details['emp_id']);
        $approved_leaves = $e->getApprovedCountOfLeaveType($details['leave_type']);
        if(($approved_leaves + $details['leave_days']) > 10){
            $this->setError('Only up to 10 sick leaves may be taken in a year.');
            return false;
        }
    }

	$update= sprintf("UPDATE fi_leave SET status='Approved',approved_date=NOW(),
		manager_comment = '%s'
		WHERE id='%s'",
		mysqli_real_escape_string($this->dbh,$comment),
		mysqli_real_escape_string($this->dbh,$leave_id));

	$res = mysqli_query($this->dbh,$update);
	if($res){
		//inform employee
		$this->notifyEmployee($leave_id);
		return true;
	}
	else{
	return false;	
	}
}

function approveCompOff($comp_off_id, $comment){
	$update= sprintf("UPDATE fi_compoff SET status='Approved',
		comments = '%s'
		WHERE id='%s'",
		mysqli_real_escape_string($this->dbh,$comment),
		mysqli_real_escape_string($this->dbh,$comp_off_id));

	$res = mysqli_query($this->dbh,$update) or die(mysqli_error(). $update);
    if($res){			$this->notifyEmployeeCompoff($comp_off_id);
    return true;}else{
    return false;}
}

function cancelCompOff($comp_off_id, $comment){
	$update= sprintf("UPDATE fi_compoff SET status='Cancelled',
		comments = '%s'
		WHERE id='%s'",
		mysqli_real_escape_string($this->dbh,$comment),
		mysqli_real_escape_string($this->dbh,$comp_off_id));

	$res = mysqli_query($this->dbh,$update);
    if($res)
    return true;
    return false;
}

/* Cancel withoutpay leave Added by Rupali*/
function cancelWithoutpay($leave_id_Withoutpay, $comment){
	 $update= sprintf("DELETE FROM fi_lwp WHERE id='%s'",
		mysqli_real_escape_string($this->dbh,$leave_id_Withoutpay));
	$res = mysqli_query($this->dbh,$update);
    if($res)
    return true;
    return false;
}
function notifyEmployee($leave_id){
			$leave_details = $this->getLeaveDetails($leave_id);
                        $leave_types = $this->getLeaveTypes();
			if($leave_details['status'] == 'Cancelled'){
                        $sub= "Leave cancelled";
                        //$body = $this->user_profile['cname']." has cancelled your leave application.\nPlease login to the leave management system to check.\n";
                        $body = "Your leave has been cancelled.\n\n";
                        $body .= "From Date : ". $leave_details['from_dt']."\n";
                        $body .= "To Date : ". $leave_details['to_date']."\n";
                        $body .= "Comments by Manager/HR : ". $leave_details['manager_comment']."\n";
			}
			else{
			$sub= "Leave approved";
			$body = "Your leave has been approved.\n\n";
			$body .= "From Date : ". $leave_details['from_dt']."\n";
			$body .= "To Date : ". $leave_details['to_date']."\n";
			$body .= "Comments by Manager/HR : ". $leave_details['manager_comment']."\n";




                        //$body = $this->user_profile['cname']." has approved your leave application.\nPlease login to the leave management system to check.\n";
			}

                        $to=$this->getEmployeeEmailFromLeaveId($leave_id);
//echo "$sub\n $body\n $to\n";
$this->mail_sent($to,$sub,$body);
//$this->sendSMTPEmail($to,$sub,$body);
}
function notifyEmployeeCompoff($leave_id){
			$leave_details = $this->getLeaveDetailsCompoff($leave_id);
                        $row=$this->getEmployeeEmailFromLeaveIdCompoff($leave_id);
$to=$row['email'];
$name=$row['cname'];
                        $leave_types = $this->getLeaveTypes();
			if($leave_details['status'] == 'Cancelled'){
                        $sub= "Leave cancelled";
                        //$body = $this->user_profile['cname']." has cancelled your leave application.\nPlease login to the leave management system to check.\n";
                        $body = "Hello".$name."\n\n";
                        $body = "Your leave has been cancelled.\n\n";
			$body .= "Applied Date : ". $leave_details['applied']."\n";
			$body .= "Compoff Date : ". $leave_details['compoff_date']."\n";
			$body .= "Work Date : ". $leave_details['work_date']."\n";
			$body .= "Comments by Manager/HR : ". $leave_details['comments']."\n";
			}
			else{
			$sub= "Leave approved";
                        $body = "Hello".$name."\n\n";
			$body = "Your leave has been approved.\n\n";
			$body .= "Applied Date : ". $leave_details['applied']."\n";
			$body .= "Compoff Date : ". $leave_details['compoff_date']."\n";
			$body .= "Work Date : ". $leave_details['work_date']."\n";
			$body .= "Comments by Manager/HR : ". $leave_details['comments']."\n";




                        //$body = $this->user_profile['cname']." has approved your leave application.\nPlease login to the leave management system to check.\n";
			}

//echo "$sub\n $body\n $to\n";
 $this->mail_sent($to,$sub,$body);

//$this->sendSMTPEmail($to,$sub,$body);
}

public function getEmployeeEmailFromLeaveId($leave_id){
	$select = "SELECT email FROM fi_emp_list, fi_leave 	
		WHERE fi_leave.id=$leave_id
		AND fi_emp_list.id = fi_leave.emp_id";
	$res = mysqli_query($this->dbh,$select);
	$row = mysqli_fetch_assoc($res);
	return $row['email'];
}
public function getEmployeeEmailFromLeaveIdCompoff($leave_id){
	 $select = "SELECT email,cname FROM fi_emp_list, fi_compoff 	
		WHERE fi_compoff.id=$leave_id
		AND fi_emp_list.id = fi_compoff.emp_id";
	$res = mysqli_query($this->dbh,$select);
	$row = mysqli_fetch_assoc($res);
	return $row;
}
public function cancelLeave($leave_id,$comment){
	$update= sprintf("UPDATE fi_leave SET status='Cancelled',
		manager_comment = '%s'
		WHERE id='%s'",
		mysqli_real_escape_string($this->dbh,$comment),
		mysqli_real_escape_string($this->dbh,$leave_id));
	$res = mysqli_query($this->dbh,$update);
	if($res){
	//notify employee
		$this->notifyEmployee($leave_id);
		return true;
	}
	else{
	return false;	
	}
}
public function cancelBereavementLeave($leave_id,$comment){
	$update= sprintf("UPDATE fi_leave SET status='Cancelled',
		manager_comment = '%s'
		WHERE id='%s'",
		mysqli_real_escape_string($this->dbh,$comment),
		mysqli_real_escape_string($this->dbh,$leave_id));
	$res = mysqli_query($this->dbh,$update);
	if($res){
	//notify employee
		$this->notifyEmployee($leave_id);
		return true;
	}
	else{
	return false;	
	}
}
public function getMyReports(){
    //return a list of reports i.e. WHERE manager = $this->user_id
	$select="SELECT * FROM fi_leave 
		WHERE manager=$this->user_id AND status='Pending' order by id desc"; 
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select);
	if(mysqli_num_rows($res)>0){
		while($row=mysqli_fetch_assoc($res)){
			$reports[] = $row;
		}
	}
	return $reports;
}


public function getMyReportsWithoutpay(){
    //return a list of reports i.e. WHERE manager = $this->user_id
	$select="SELECT * FROM fi_lwp 
		WHERE emp_id=$this->user_id AND approved='0'"; 
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select);
	if(mysqli_num_rows($res)>0){
		while($row=mysqli_fetch_assoc($res)){
			$reports[] = $row;
		}
	}
	return $reports;
}
public function getCompOffApplications(){
        $select = "SELECT * FROM fi_compoff
                WHERE status='Pending' 
                AND emp_id IN (SELECT id FROM fi_emp_list 
                        WHERE manager = $this->user_id)";
        $res = mysqli_query($this->dbh,$select) or die(mysqli_error() . $select);
        $applications = array();
        while($row = mysqli_fetch_assoc($res)){
                $applications[] = $row;
        }
        return $applications;
}


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
//echo $select;
	$res = mysqli_query($this->dbh,$select);
	if(mysqli_num_rows($res) > 0){
		while($row = mysqli_fetch_assoc($res)){
			$reports[] = $row;
		}
	}
	return $reports;
    //return array of leaves taken by employee in a year 

}

public function getMyUsers($userid){
    //return a list of users i.e. WHERE manager = $this->user_id
	$select="SELECT * FROM fi_emp_list 
		WHERE manager=$userid AND status=1"; 
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select);
	if(mysqli_num_rows($res)>0){
		while($row=mysqli_fetch_assoc($res)){
			$users[] = $row;
		}
	}
	return $users;
}
//update manager name is added by-rutuja
function updateEmployeeDetails($data){
$update = sprintf("UPDATE fi_emp_list 
                        SET empno = '%s',
                        dept = '%s',
                        cname = '%s', location = '%s', 
                        ou = '%s',  `joining_date` = '%s', `left_on` = '%s',
			email = '%s',manager='%s'
                        WHERE id = '%s'",
                        mysqli_real_escape_string($this->dbh,$data['empno']),
                        mysqli_real_escape_string($this->dbh,$data['dept']),
                        mysqli_real_escape_string($this->dbh,$data['cname']),
                        mysqli_real_escape_string($this->dbh,$data['location']),
                        mysqli_real_escape_string($this->dbh,$data['ou']),
                        //mysqli_real_escape_string($this->dbh,$data['status']),
                        mysqli_real_escape_string($this->dbh,$data['joining_date']),
                        mysqli_real_escape_string($this->dbh,$data['left_on']),
                        mysqli_real_escape_string($this->dbh,$data['email']),
                        mysqli_real_escape_string($this->dbh,$data['mng_name']),
                        mysqli_real_escape_string($this->dbh,$data['emp_id']));
                        
              /* $update = sprintf("UPDATE fi_emp_list 
                        SET empno = '%s',
                        dept = '%s',
                        cname = '%s', location = '%s', 
                        ou = '%s', `joining_date` = '%s', `left_on` = '%s'
                        WHERE id = '%s'",
                        mysqli_real_escape_string($this->dbh,$data['empno']),
                        mysqli_real_escape_string($this->dbh,$data['dept']),
                        mysqli_real_escape_string($this->dbh,$data['cname']),
                        mysqli_real_escape_string($this->dbh,$data['location']),
                        mysqli_real_escape_string($this->dbh,$data['ou']),
                        mysqli_real_escape_string($this->dbh,$data['joining_date']),
                        mysqli_real_escape_string($this->dbh,$data['left_on']),
                        mysqli_real_escape_string($this->dbh,$data['emp_id']));*/
//echo $update;
                mysqli_query($this->dbh,$update) or die(mysqli_error());
		$this->updateEmployeeCarryForwardLeaves($data);
		$this->updateEmployeeLeaveBuckets($data);
                return true;
        }


/*
function updateEmployeeCarryForwardLeaves($data){
	
foreach($data as $k=>$v){
	if(preg_match('/carry_forward/',$k)){
       		$leave_type_id = preg_replace('/carry_forward_/','',$k);
		$update = sprintf("UPDATE fi_leave_carry_forwards
			SET no_of_leaves = '%s'
			WHERE leave_type = '%s'
			AND emp_id = '%s'",
			mysqli_real_escape_string($this->dbh,$v),
			mysqli_real_escape_string($this->dbh,$leave_type_id),
			mysqli_real_escape_string($this->dbh,$data['emp_id']));	
		mysqli_query($this->dbh,$update) or die(mysqli_error());
	}
}
		return true;
}

function updateEmployeeLeaveBuckets($details){

foreach($details as $k=>$v){
if(preg_match('/leave_type/',$k)){
        $leave_type_id = preg_replace('/leave_type_/','',$k);
		$update = sprintf("UPDATE fi_employee_leave_buckets
		SET maximum = '%s'
		WHERE 
		leave_type_id='%s' AND employee_id='%s'",
		mysqli_real_escape_string($this->dbh,$v),
		mysqli_real_escape_string($this->dbh,$leave_type_id),
		mysqli_real_escape_string($this->dbh,$details['emp_id']));
		mysqli_query($this->dbh,$update) or die(mysqli_error());
}
}
		return true;
}
*/

function getEmployeeCompOff(){
	$select = "SELECT fi_compoff.* FROM fi_compoff LEFT JOIN fi_emp_list 
                ON  emp_id=fi_emp_list.id 
		WHERE applied LIKE '%".date('Y')."%' 
        AND `emp_id` = $this->user_id
		ORDER BY work_date DESC";
	$res = mysqli_query($this->dbh,$select);
	while($row = mysqli_fetch_assoc($res)){
		$compoff[] = $row;
	}
	return $compoff;	
}

public function hasLeaveBalance($leave_id){
    //check if the employee has balance for the leave-type
    // first get leave details
    $select = sprintf("SELECT * FROM fi_leave WHERE id = '%s'",
            mysqli_real_escape_string($this->dbh,$leave_id));
    $res = mysqli_query($this->dbh,$select);
    $row = mysqli_fetch_assoc($res);

    // now create employee and get their balance
    $e = new Employee($row['emp_id']);
    $leave_balance = $e->getMyLeaveBalance(
            array('year'=>date('Y'),
            'leave_type_id'=>$row['leave_type'])
            );

    //Get number of days of leave considering holidays
    $applied_leave_days = $e->getTotalLeaveDays($row['from_dt'],$row['to_date']);

    $leave_types = $this->getLeaveTypes();

    if(($leave_balance < $applied_leave_days && $leave_types[$row['leave_type']] != 'Earned') ||
    ($leave_types[$row['leave_type']] == 'Earned' && $leave_balance + 3 < $applied_leave_days)){
        return false;
    }
    return true;
}
public function getReportsWithoutpay(){
         $select = "SELECT * FROM fi_lwp
                WHERE emp_id IN (SELECT id FROM fi_emp_list 
                        WHERE manager = $this->user_id) AND approved='0'";
        $res = mysqli_query($this->dbh,$select) or die(mysqli_error() . $select);
        $applications = array();
        while($row = mysqli_fetch_assoc($res)){
                $applications[] = $row;
        }
        return $applications;
}

function getBereavementLeaves($emp_id){
        $leave_types = $this->getLeaveTypes();
        $leaves_id = array_flip($leave_types);
        $bereavement_id = $leaves_id['Bereavement'];
        $select = sprintf("SELECT * FROM fi_leave
                WHERE emp_id = '%s'
                AND leave_type = '%s'",
                mysqli_real_escape_string($this->dbh,$emp_id),
                mysqli_real_escape_string($this->dbh,$bereavement_id));
        $res = mysqli_query($this->dbh,$select) or die(mysqli_error() . $select);
        $lwp = array();
        while($row = mysqli_fetch_assoc($res)){
                $bereavement_leaves[] = $row;
        }
        return $bereavement_leaves;
}

function getEmployeeLeaveDays($month,$year){

 	$dateYear = ($year != '')?$year:date("Y");
        $dateMonth = ($month != '')?$month:date("m");
        $date = $dateYear.'-'.$dateMonth.'-01';
	$start_date = $dateYear.'-'.$dateMonth.'-01';
	$end_date = $dateYear.'-'.$dateMonth.'-31';
        $sessionuserid=$this->user_id;

 /*       $query = "SELECT fi_leave.id, fi_leave.from_dt, fi_leave.to_date, fi_emp_list.cname,
	fi_emp_list.id FROM fi_leave
       	left join fi_emp_list ON fi_emp_list.id= fi_leave.emp_id 
	WHERE fi_leave.manager=".$sessionuserid." 
	AND (fi_leave.status='Pending' OR  fi_leave.status='Approved') AND
	(fi_leave.from_dt BETWEEN '$start_date' AND '$end_date' OR 
	fi_leave.to_date BETWEEN '$start_date' AND '$end_date')";
*/
        $query = "SELECT fi_leave.id, fi_leave.from_dt, fi_leave.to_date, fi_emp_list.cname,
	fi_emp_list.id FROM fi_leave
       	left join fi_emp_list ON fi_emp_list.id= fi_leave.emp_id 
	WHERE fi_leave.manager=".$sessionuserid." 
	AND ((fi_leave.status='Pending' OR  fi_leave.status='Approved') AND 
	((fi_leave.from_dt='$start_date' OR fi_leave.from_dt='$end_date') 
        OR (fi_leave.to_date='$start_date' OR fi_leave.to_date='$end_date'))
	OR
	(fi_leave.from_dt BETWEEN '$start_date' AND '$end_date' OR 
	fi_leave.to_date BETWEEN '$start_date' AND '$end_date') AND (fi_leave.status != 'Cancelled'))";
        $result = mysqli_query($this->dbh,$query) or die("Error: ".$query);
	
	$i = 0;
	while($row = mysqli_fetch_assoc($result)){
	$leave_dates[$i]['range'] = $this->date_range($row['from_dt'],$row['to_date']);
	$leave_dates[$i]['user'] = $row['cname'];
	$i++;
	}

	$date_names = array();
	foreach($leave_dates as $leave_array){
		foreach($leave_array['range'] as $dt){
			$date_names[$dt][] = $leave_array['user'];
		}	
	} 
return $date_names;
}




function getEmployeeLeaves($currentDate){

                        $sessionuserid=$_SESSION['user_id'];
                         $query = "SELECT fi_leave.*, fi_emp_list.cname,fi_emp_list.id FROM fi_leave
                left join fi_emp_list ON fi_emp_list.id= fi_leave.emp_id WHERE fi_leave.manager=".$sessionuserid." AND (fi_leave.status='Pending' OR  fi_leave.status='Approved') AND
 (fi_leave.from_dt= '".$currentDate."' OR fi_leave.to_date= '".$currentDate."' OR (DATE($currentDate) BETWEEN fi_leave.from_dt AND fi_leave.to_date))";
#echo $query;
                        $result = mysqli_query($this->dbh,$query) or die("Error: ".$query);
                        $row = mysqli_fetch_assoc($result);
 			$eventNum = mysqli_num_rows($result);
                       
return $row;

}
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


function getnames($currentDate)
{
$sessionuserid=$_SESSION['user_id'];
$query = "SELECT fi_leave.*, fi_emp_list.cname,fi_emp_list.id as emp FROM fi_leave 
		left join fi_emp_list ON fi_emp_list.id= fi_leave.emp_id WHERE fi_leave.manager=".$sessionuserid." AND (fi_leave.status='Pending' OR  fi_leave.status='Approved') AND
 (fi_leave.from_dt= '".$currentDate."' OR fi_leave.to_date= '".$currentDate."' OR (DATE($currentDate) BETWEEN fi_leave.from_dt AND fi_leave.to_date))";

//echo $query;
$result = mysqli_query($this->dbh,$query) or die("Error: ".$query);

while($row = mysqli_fetch_assoc($result)){  
$a[]= $row['cname'];
//$b.=$row['cname'].',';
}
$listemp=implode(',',$a);
return $listemp;
}

/*
 * Get events by date
 */
function getEvents($date = ''){
    //Include db configuration file
$sessionuserid=$_SESSION['user_id'];
   // include 'dbConfig.php';
    $eventListHTML = '';
    $date = $date?$date:date("Y-m-d");
    //Get events based on the current date
      $query="SELECT fi_leave.*, fi_emp_list.cname,fi_emp_list.id FROM fi_leave 
		left join fi_emp_list ON fi_emp_list.id= fi_leave.emp_id WHERE fi_leave.manager=".$sessionuserid." AND (fi_leave.status='Pending' OR  fi_leave.status='Approved') AND (fi_leave.from_dt= '".$date."' OR fi_leave.to_date= '".$date."' OR (DATE($date) BETWEEN fi_leave.from_dt AND fi_leave.to_date))" ;
//SELECT title FROM events WHERE date = '".$date."' AND status = 1");
                        $result = mysqli_query($this->dbh,$query) or die("Error: ".$query);
		$eventNum = mysqli_num_rows($result);
			if(in_array($date,$dates) && $eventNum==0){ $eventNum=1;}
    if($eventNum > 0){
        $eventListHTML = '<h2>Leaves on '.date("l, d M Y",strtotime($date)).'</h2>';
        $eventListHTML .= '<ul>';
while($row = mysqli_fetch_assoc($result)){  
            $eventListHTML .= '<li>'.$row['cname'].'Is taking Leave for  '.$row['reason'].'</li>';
        }
        $eventListHTML .= '</ul>';
    }
    echo $eventListHTML;
}
function getLatest30daysLeavesReport(){
	echo $select = "SELECT empno, cname, deptname, fi_office_locations.location, emp_id, applied, from_dt, to_date, leave_days, typename, fi_leave.status
FROM fi_leave
LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id
LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type
LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
LEFT JOIN fi_office_locations ON fi_emp_list.office_location = fi_office_locations.id
WHERE fi_leave.from_dt > NOW( ) - INTERVAL 0
DAY AND fi_leave.from_dt < NOW( ) + INTERVAL 30
DAY ORDER BY empno";

	$result = mysqli_query($this->dbh,$select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}

}// class ends
