<?php
include_once 'Manager.class.php';

class HR extends Manager{

public function __construct($user_id){
parent::__construct($user_id);
        if(!$this->isHR()){
        $this->_loginRedirect();
        throw new Exception('No privileges');
        }
}

function approveallpendingleave($data){
      $update = sprintf("UPDATE fi_leave
                        SET `status` = 'Approved' 
                        WHERE status='Pending'");
                       
                        mysqli_query($this->dbh,$update) or die(mysqli_error());
                        return true;
        }
public function getEmployeeReports($criteria,$location){
	$status = empty($criteria['status'])?1:0;
	$alphabet_clause = empty($criteria['arg1'])?'':" AND c.cname LIKE '".$criteria['arg1']."%' ";
	$search_clause = empty($criteria['semp'])? '' : " AND (c.cname LIKE '%".$criteria['semp']."%' 
				OR c.empno = '".$criteria['semp']."' 
				OR c.username = '".$criteria['semp']."')";
	$DOE_clause = empty($criteria['status'])?"AND (left_on is null OR left_on = '0000-00-00')": " AND (left_on is not null AND left_on > '1970-01-01')";
	if(!empty($criteria['status'])){
	$left_on_year = empty($criteria['year'])?" AND left_on LIKE '%".date('Y')."%' ":" AND left_on LIKE '%".$criteria['year']."%' ";
	}
	else{
	$left_on_year = '';
	}

	if($location == (-1) || $location == ''){
		$location_clause = '';
	}
	else{
		$location_clause = " AND c.location = '".$location."'";
	}
 
	 $sql="SELECT c.id, c.empno, c.cname, d.deptname, l.location, ou.ou_short_name as org_unit,c.left_on 
		FROM fi_emp_list c 
		LEFT JOIN fi_office_locations l ON l.id = c.location 
		LEFT JOIN fi_dept d ON d.id = c.dept 
		LEFT JOIN fi_ou ou ON ou.id=c.ou
		WHERE 1 
		$alphabet_clause
		$search_clause
		$location_clause
		$DOE_clause
		$left_on_year
		ORDER BY c.empno, d.deptname";
//echo $sql;
	$res = mysqli_query($this->dbh,$sql) or die(mysqli_error() . $sql);
	while($row = mysqli_fetch_assoc($res)){
		$reports[] = $row;
	}
	return $reports;
}

public function addEmployee($details){
	$sql=sprintf("INSERT INTO fi_emp_list 
		(`empno`,`username`,`password`,`dept`,`cname`,`location`,`ou`,`status`,`joining_date`) 
                VALUES ('%s','%s','%s','%d','%s','%d','%d','1','%s')",
		mysqli_real_escape_string($this->dbh,$details['empno']),
		mysqli_real_escape_string($this->dbh,$details['username']),
		md5($details['password']),
		mysqli_real_escape_string($this->dbh,$details['dept']),
		mysqli_real_escape_string($this->dbh,$details['cname']),
		mysqli_real_escape_string($this->dbh,$details['location']),
		mysqli_real_escape_string($this->dbh,$details['ou']),
		mysqli_real_escape_string($this->dbh,$details['joining_date'])
	);
	if(!mysqli_query($this->dbh,$sql)){
	//echo mysqli_error() . $sql;
        $this->setError("Error: ".$sql . mysqli_error());
        return false;
    }
	$emp_id = mysqli_insert_id();
	$this->addEmployeeLeaveBuckets($emp_id,$details);
	return true;
}

public function addEmployeeLeaveBuckets($emp_id,$details){
foreach($details as $k=>$v){
	if(empty($v)){
		continue;
	}
if(preg_match('/leave_type/',$k)){
	$leave_type_id = preg_replace('/leave_type_/','',$k);
	$insert=sprintf("INSERT INTO fi_employee_leave_buckets
		(`employee_id`,`year`,`leave_type_id`,`maximum`)
		VALUES('%s','%s','%s','%s')",
		mysqli_real_escape_string($this->dbh,$emp_id),
		mysqli_real_escape_string($this->dbh,date('Y')),
		mysqli_real_escape_string($this->dbh,$leave_type_id),
		mysqli_real_escape_string($this->dbh,$v)
		);
	$res = mysqli_query($insert) or die("Error: ".$insert);
}
}
	return true;
}

function deleteEmployee($employee){
	$select = "select * from fi_emp_list where username='$employee'";
	$result = mysqli_query($this->dbh,$select);
	$row = mysqli_fetch_assoc($result);
	$employee_id = $row['id'];

	if(mysqli_num_rows($result) > 0){
	$delete = "DELETE FROM fi_emp_list 
		WHERE id='$employee_id'";
	$res = mysqli_query($this->dbh,$delete);
        $sql="Delete from fi_compoff where emp_id = '$employee_id'";
        mysqli_query($this->dbh,$sql);
        $sql="Delete from fi_leave where emp_id = '$employee_id'";
        mysqli_query($this->dbh,$sql);
	return true;
	}
	else{
        $this->setError('This employee does not exist.');
	return false;
	}
}

function addOfficeLocation($data){
                $insert = sprintf("INSERT INTO fi_office_locations
                        (`location`) values('%s')",
                        mysqli_real_escape_string($this->dbh,$data['location']));

                mysqli_query($this->dbh,$insert) or die(mysqli_error());
                return true;
        }


function updateOfficeLocation($data){
      $update = sprintf("UPDATE fi_office_locations
                        SET `location` = '%s' 
                        WHERE id = '%s'",
                        mysqli_real_escape_string($this->dbh,$data['location']),
                        mysqli_real_escape_string($this->dbh,$data['loc']));
                        mysqli_query($this->dbh,$update) or die(mysqli_error());
                        return true;
        }

function deleteOfficeLocation($loc){
         $delete = sprintf("DELETE FROM fi_office_locations
                        WHERE id = '%s'",
                        mysqli_real_escape_string($this->dbh,$loc));
                mysqli_query($this->dbh,$delete) or die(mysqli_error());
                return true;
        }

	function addDepartment($data){
                $insert = sprintf("INSERT INTO fi_dept
                (`deptname`, `dept_mgr`)
                VALUES('%s', '%s')",
                mysqli_real_escape_string($this->dbh,$data['deptname']),
                mysqli_real_escape_string($this->dbh,$data['dept_mgr']));
                mysqli_query($this->dbh,$insert) or die(mysqli_error());
                return 1;
        }

        function updateDepartment($data){
                $update = sprintf("UPDATE fi_dept
                        SET deptname = '%s', dept_mgr = '%s'
                        WHERE id = '%s'",
                        mysqli_real_escape_string($this->dbh,$data['deptname']),
                        mysqli_real_escape_string($this->dbh,$data['dept_mgr']),
                        mysqli_real_escape_string($this->dbh,$data['deptno']));
                mysqli_query($this->dbh,$update) OR die(mysqli_error());
                return 1;
        }

        function deleteDepartment($deptno){
                $delete = sprintf("DELETE FROM fi_dept 
                        WHERE id = '%s'",
                        mysqli_real_escape_string($this->dbh,$deptno));
                mysqli_query($this->dbh,$delete) or die(mysqli_error());
                return 1;
        }

	function getDepartments(){
                $select = "SELECT * FROM fi_dept";
                $res = mysqli_query($this->dbh,$select);
                $depts = array();
                while($row = mysqli_fetch_assoc($res)){
                        $depts[] = $row;
                }
                return $depts;
        }



        function addHoliday($data){
                $insert = sprintf("INSERT INTO fi_holidays
                (`eventname`, `eventdate`, `location`)
                values('%s', '%s', '%s')",
                mysqli_real_escape_string($this->dbh,$data['eventname']),
                mysqli_real_escape_string($this->dbh,$data['eventdate']),
                mysqli_real_escape_string($this->dbh,$data['location']));

                mysqli_query($this->dbh,$insert) or die(mysqli_error());
                return true;
        }

        function deleteHoliday($holiday_id){
                $delete = sprintf("DELETE FROM fi_holidays 
                        WHERE id= '%s'",
                        mysqli_real_escape_string($this->dbh,$holiday_id));
                mysqli_query($this->dbh,$delete) or die(mysqli_error());
                return true;
        }

 function addOrgUnit($data){
                $insert = sprintf("INSERT INTO fi_ou 
                        (`ou_short_name`, `ou_long_string`)
                        VALUES('%s', '%s')",
                        mysqli_real_escape_string($this->dbh,$data['ou_short_name']),
                        mysqli_real_escape_string($this->dbh,$data['ou_long_string']));
                mysqli_query($this->dbh,$insert) or die(mysqli_error());
                return 1;
        }

        function deleteOrgUnit($ou){
                $delete = sprintf("DELETE FROM fi_ou
                        WHERE id = '%s'",
                        mysqli_real_escape_string($this->dbh,$ou));
                mysqli_query($this->dbh,$delete) or die(mysqli_error());
                return 1;
        }

        function updateOrgUnit($data){
                $update = sprintf("UPDATE fi_ou
                        SET ou_short_name = '%s',
                        ou_long_string = '%s'
                        WHERE id = '%s'",
                        mysqli_real_escape_string($this->dbh,$data['ou_short_name']),
                        mysqli_real_escape_string($this->dbh,$data['ou_long_string']),
                        mysqli_real_escape_string($this->dbh,$data['ou']));
                mysqli_query($this->dbh,$update) or die(mysqli_error());
                return 1;
        }

	public function getAllHolidays(){
                $select = "SELECT eventdate, eventname, fi_holidays.id, fi_office_locations.location
                FROM fi_holidays LEFT JOIN fi_office_locations ON
                fi_holidays.location = fi_office_locations.id";
                $res = mysqli_query($this->dbh,$select) or die(mysqli_error().$select);
                $holidays = array();
                while($row = mysqli_fetch_assoc($res)){
                        $holidays[] = $row;
                }
                return $holidays;
        }


	function addLeaveType($data){
		$insert = sprintf("INSERT INTO fi_leave_types
		(`typename`,`comments`,`status`)
		VALUES('%s','%s','%s')",
		mysqli_real_escape_string($this->dbh,$data['typename']),
		mysqli_real_escape_string($this->dbh,$data['comments']),
		mysqli_real_escape_string($this->dbh,'Y'));
		mysqli_query($this->dbh,$insert) or die(mysqli_error());
		return mysqli_insert_id();
	}
        function deleteLeaveType($type_id){
                $delete = sprintf("DELETE FROM fi_leave_types 
                        WHERE id = '%s'",
                        mysqli_real_escape_string($this->dbh,$type_id));
                mysqli_query($this->dbh,$delete) or die(mysqli_error());
                return 1;
        }

	function addLeaveBuckets($data){
                $insert = sprintf("INSERT INTO fi_leave_buckets
                (`leave_type_id`, `year`, `maximum`)
                VALUES('%s', '%s', '%s')",
                mysqli_real_escape_string($this->dbh,$data['leave_type_id']),
                mysqli_real_escape_string($this->dbh,date('Y')),
                mysqli_real_escape_string($this->dbh,$data['maximum']));
                mysqli_query($this->dbh,$insert) or die(mysqli_error());
                return 1;
        }
        function updateLeaveBuckets($data){
		$select = "SELECT maximum FROM fi_leave_buckets
				WHERE leave_type_id = '$data[leave_type_id]' 
				AND year = '$data[year]'";
		$res = mysqli_query($this->dbh,$select) or die(mysqli_error());
		if(mysqli_num_rows($res) > 0){
                $update = sprintf("UPDATE fi_leave_buckets
                        SET maximum='%s'
                        WHERE leave_type_id = '%s'
			AND year = '%s'",
                        mysqli_real_escape_string($this->dbh,$data['maximum']),
                        mysqli_real_escape_string($this->dbh,$data['leave_type_id']),
                        mysqli_real_escape_string($this->dbh,$data['year'])
			);
                mysqli_query($this->dbh,$update) OR die(mysqli_error());
                return 1;
		}
		else{
		$this->addLeaveBuckets($data);
		return 1;
		}
        }

        function deleteLeaveBuckets($leave_type_id,$year){
		/*
                $delete = sprintf("DELETE FROM fi_leave_buckets 
                        WHERE leave_type_id = '%s' AND year = '%s'",
                        mysqli_real_escape_string($this->dbh,$leave_type_id),
			mysqli_real_escape_string($this->dbh,$year));
		*/
                $delete = sprintf("UPDATE fi_leave_buckets 
			SET maximum = '0'
                        WHERE leave_type_id = '%s' AND year = '%s'",
                        mysqli_real_escape_string($this->dbh,$leave_type_id),
			mysqli_real_escape_string($this->dbh,$year));
                mysqli_query($this->dbh,$delete) or die(mysqli_error());
                return 1;
        }

	function getLeaveBuckets($year){
                $select = "SELECT * FROM fi_leave_types 
			LEFT JOIN fi_leave_buckets ON leave_type_id=fi_leave_types.id 
			WHERE year = $year AND status ='Y'";
                $res = mysqli_query($this->dbh,$select);
                $depts = array();
                while($row = mysqli_fetch_assoc($res)){
                        $leave_buckets[] = $row;
                }
                return $leave_buckets;
        }

/*
Approve leave
Check of maternity leave has been removed from here.
*/
public function approveLeave($leave_id,$comment){

$details = $this->getLeaveDetails($leave_id);
	$leave_types = $this->getLeaveTypes();
if($leave_types[$details['leave_type']]!= 'Bereavement'){
    if(!$this->isMaternityLeave($leave_id) && !$this->hasLeaveBalance($leave_id)){
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
//echo $update;
        $res = mysqli_query($this->dbh,$update) or die(mysqli_error(). $update);
        if($res){
		//inform employee
		$this->notifyEmployee($leave_id);
        	return true;
        }
        return false;
}



function getBucketsAndBalancesOfYear($last_year=null){
    //get a list of all active employees
    $select = "SELECT id FROM fi_emp_list 
        WHERE status = 1";
    $res = mysqli_query($this->dbh,$select) or die(mysqli_error());
    $employees = array();
    while($row = mysqli_fetch_assoc($res)){
        $employees[] = $row['id'];
    }

	//get buckets, leaves and balances for each employee
    $buckets_leaves_balances = array();
    $leave_types = $this->getLeaveTypes();	
    foreach($employees as $e_id){
        //$e = new Employee($e_id);

	if(empty($last_year)) $last_year = date('Y') - 1;

	foreach($leave_types as $k=>$v){
		$criteria = array('year'=>$last_year,'leave_type_id'=>$k);
		//$buckets_leaves_balances[$e_id][$v] = array($e->getMyLeaveBalance($criteria),$v,$k);
		$buckets_leaves_balances[$e_id]['buckets_leaves_balances'] = $this->getLeaveRecordOfReport($e_id, $last_year);
	}
    }
	return $buckets_leaves_balances;
}

/*
Function to carry forward leaves from the last to the current year
After applying caps
*/

public function carryForwardLeaves($last_year=null){
    //get a list of all active employees
    $select = "SELECT id FROM fi_emp_list 
        WHERE status = 1";
    $res = mysqli_query($this->dbh,$select) or die(mysqli_error());
    $employees = array();
    while($row = mysqli_fetch_assoc($res)){
        $employees[] = $row['id'];
    }

    // Now, get balances of last year
    $leave_balances = array();
    $leave_types = $this->getLeaveTypes();	
    foreach($employees as $e_id){
        $e = new Employee($e_id);

	if(empty($last_year)) $last_year = date('Y') - 1;

	foreach($leave_types as $k=>$v){
	$criteria = array('year'=>$last_year,'leave_type_id'=>$k);
	$leave_balances[$e_id][$v] = array($e->getMyLeaveBalance($criteria),$v,$k);
	}
    }
	//print_r($leave_balances);
	return $leave_balances;
}

	function addCarryForwardLeaves($data){
                $insert = sprintf("INSERT INTO fi_leave_carry_forwards
                (`emp_id`,`year`,`leave_type`, `no_of_leaves`)
                VALUES('%s', '%s', '%s','%s')",
                mysqli_real_escape_string($this->dbh,$data['emp_id']),
                mysqli_real_escape_string($this->dbh,$data['year']),
                mysqli_real_escape_string($this->dbh,$data['leave_type_id']),
                mysqli_real_escape_string($this->dbh,$data['no_of_leaves']));
	//	echo $insert;
                mysqli_query($this->dbh,$insert) or die(mysqli_error());
                return 1;
        }

	/*
        function updateCarryForwardLeaves($leaves){
		foreach($leaves as $user=>$details){
			foreach($details as $l){
			if(($l[1] == 'Sick' || $l[1] == 'Earned') && $l[0] != 0) {
			//	echo "No Of Leaves = ".$l[0];
                        //      echo "Leave Type Id = ".$l[2];
			if($l[1] == 'Earned' && $l[0] > 7){
				$data['no_of_leaves'] = 7;
			}
			else{
			$data['no_of_leaves'] = $l[0];
			}
			$data['leave_type_id'] = $l[2];
		        $data['emp_id'] = $user;		

		$select = "SELECT no_of_leaves FROM fi_leave_carry_forwards
				WHERE leave_type = '$data[leave_type_id]'
				AND emp_id = '$data[emp_id]'";
//echo $select;
		$res = mysqli_query($this->dbh,$select) or die(mysqli_error());
		if(mysqli_num_rows($res) > 0){
                $update = sprintf("UPDATE fi_leave_carry_forwards
                        SET no_of_leaves='%s'
                        WHERE leave_type = '%s' AND emp_id='%s'",
                        mysqli_real_escape_string($this->dbh,$data['no_of_leaves']),
                        mysqli_real_escape_string($this->dbh,$data['leave_type_id']),
			mysqli_real_escape_string($this->dbh,$user));
                mysqli_query($this->dbh,$update) OR die(mysqli_error());
                //return 1;
		}
		else{
		$this->addCarryForwardLeaves($data);
		//return 1;
		}
			} #if of Sick or Earned ends
			}# $details foreach ends
		}# $leaves foreach ends
		return true;
        }

        function deleteCarryForwardLeaves($data){
                $delete = sprintf("DELETE FROM fi_leave_carry_forwards 
                        WHERE leave_type = '%s' AND emp_id='%s'",
                        mysqli_real_escape_string($this->dbh,$data['leave_type_id']),
			mysqli_real_escape_string($this->dbh,$data['emp_id']));
                mysqli_query($this->dbh,$delete) or die(mysqli_error());
                return 1;
        }
	*/

	function deleteCarryForwardsOfYear($year){
		$delete = sprintf("DELETE FROM fi_leave_carry_forwards
			WHERE year = '%s'",
			mysqli_real_escape_string($this->dbh,$year));
		mysqli_query($this->dbh,$delete);
	}

public function updateLeave($data){
	$update = sprintf("UPDATE fi_leave SET leave_type='%s'
		WHERE id='%s'",
		mysqli_real_escape_string($this->dbh,$data['leave_type']),
		mysqli_real_escape_string($this->dbh,$data['leave_id']));
	$res = mysqli_query($this->dbh,$update) or die(mysqli_error(). $update);
	if($res){
		return true;
	}
	else{
		return false;
	}
}


public function updateEmployeeCarryForwardLeaves($data){
$year = date('Y');
foreach($data as $k=>$v){
        if(preg_match('/carry_forward/',$k)){
                $leave_type_id = preg_replace('/carry_forward_/','',$k);
                $select = "SELECT * FROM fi_leave_carry_forwards
                        WHERE leave_type = '$leave_type_id'
                        AND emp_id = '$data[emp_id]'";
                $res = mysqli_query($this->dbh,$select);
                if(mysqli_num_rows($res) > 0){
                $update = sprintf("UPDATE fi_leave_carry_forwards
                        SET no_of_leaves = '%d'
                        WHERE leave_type = '%d'
                        AND emp_id = '%s' AND year= '$year'",
                        mysqli_real_escape_string($this->dbh,$v),
                        mysqli_real_escape_string($this->dbh,$leave_type_id),
                        mysqli_real_escape_string($this->dbh,$data['emp_id']));
                mysqli_query($this->dbh,$update) or die(mysqli_error());
                }
                else{
                        $insert = sprintf("INSERT INTO fi_leave_carry_forwards
                                VALUES('%d','%d','%d','%d')",
                                mysqli_real_escape_string($this->dbh,$data['emp_id']),
                                mysqli_real_escape_string($this->dbh,date('Y')),
                                mysqli_real_escape_string($this->dbh,$leave_type_id),
                                mysqli_real_escape_string($this->dbh,$v));
                        mysqli_query($this->dbh,$insert) or die(mysqli_error());
                }
        }
}
                return true;
}

/*

*/

function updateEmployeeLeaveBuckets($details){

	//first delete all related records
	$delete = sprintf("DELETE FROM fi_employee_leave_buckets
		WHERE employee_id='%s'",
		mysqli_real_escape_string($this->dbh,$details['emp_id']));
	//echo $delete;
	mysqli_query($this->dbh,$delete);
//print_r($details);
foreach($details as $k=>$v){
if(!preg_match("/^\d+/", $v)){continue;}
if(preg_match('/leave_type/',$k)){
        $leave_type_id = preg_replace('/leave_type_/','',$k);

	$insert = sprintf("INSERT INTO fi_employee_leave_buckets
	(`employee_id`, `year`, `leave_type_id`, `maximum`)
	VALUES('%s', '%s', '%s', '%s')",
	mysqli_real_escape_string($this->dbh,$details['emp_id']),
	mysqli_real_escape_string($this->dbh,date('Y')),
	mysqli_real_escape_string($this->dbh,$leave_type_id),
	mysqli_real_escape_string($this->dbh,$v));
//echo $insert;
	if(!mysqli_query($this->dbh,$insert)){
        $this->setError($insert . mysqli_error());
        return false;
    }
	/*
		$update = sprintf("UPDATE fi_employee_leave_buckets
		SET maximum = '%s'
		WHERE 
		leave_type_id='%s' AND employee_id='%s'",
		mysqli_real_escape_string($this->dbh,$v),
		mysqli_real_escape_string($this->dbh,$leave_type_id),
		mysqli_real_escape_string($this->dbh,$details['emp_id']));
		mysqli_query($this->dbh,$update) or die(mysqli_error());
	*/
}
}
		return true;
}

public function getLeaveCountsOfAll($year){
        $select = "SELECT emp_id, leave_type, SUM(leave_days) as days FROM fi_leave
                WHERE from_dt LIKE '$year-%' 
                AND status = 'Approved'
                GROUP BY emp_id, leave_type";
                //WHERE from_dt > '$year-01-01 00:00:00' 
        $res = mysqli_query($this->dbh,$select);
        $leaves = array();
        while($row = mysqli_fetch_assoc($res)){
                $leaves[$row['emp_id']][$row['leave_type']] = $row['days'];
        }
        return $leaves;
}

public function getLeaveCarryForwardsOfAll($year){
        $select = "SELECT * FROM fi_leave_carry_forwards
                WHERE year = '$year'";
        $res = mysqli_query($this->dbh,$select);
        $carry_forwards = array();
        while($row = mysqli_fetch_assoc($res)){
                $carry_forwards[$row['emp_id']][$row['leave_type']] = $row['no_of_leaves'];
        }
        return $carry_forwards;
}

function getLatestLeavesReport($year){
	$select = "SELECT empno, cname, deptname,left_on, fi_office_locations.location, emp_id, applied,
		from_dt,to_date,leave_days, typename,fi_leave.status 
		FROM fi_leave LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id 
		LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type 
        LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
        LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id
		WHERE `from_dt` > '$year-01-01 00:00:00' AND (left_on is null OR left_on = '0000-00-00')  ORDER BY empno";
	$result = mysqli_query($this->dbh,$select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}

function getLatestOneMonthLeavesReport($year,$month,$days){
	$select = "SELECT empno, cname, deptname,left_on, fi_office_locations.location, emp_id, applied,
		from_dt,to_date,leave_days, typename,fi_leave.status 
		FROM fi_leave LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id 
		LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type 
        LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
        LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id
	WHERE `from_dt` > '$year-$month-01 00:00:00' AND `to_date` < '$year-$month-$days 00:00:00' 
	AND (left_on is null OR left_on = '0000-00-00')  ORDER BY empno";

	$result = mysqli_query($this->dbh,$select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}

/*function getLatest30daysLeavesReport(){
	$select = "SELECT empno, cname, deptname, fi_office_locations.location, emp_id, applied,
		from_dt,to_date,leave_days, typename,fi_leave.status 
		FROM fi_leave LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id 
		LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type 
        LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
        LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id
	WHERE fi_leave.from_dt > NOW() - INTERVAL 0 DAY AND fi_leave.from_dt < NOW() + INTERVAL 30 DAY ORDER BY empno";

	$result = mysqli_query($this->dbh,$select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}
*/
function addLeaveWithoutPay($emp_id, $from_date, $to_date){
	$insert = sprintf("INSERT INTO fi_lwp 
		(`emp_id`, `from_dt`, `to_date`, `days`, `created_by`, `applied`)
		VALUES('%s', '%s', '%s', '%s', '%s',NOW())",
		mysqli_real_escape_string($this->dbh,$emp_id),
		mysqli_real_escape_string($this->dbh,$from_date),
		mysqli_real_escape_string($this->dbh,$to_date),
		mysqli_real_escape_string($this->dbh,$this->getTotalLeaveDays($from_date, $to_date)),
		mysqli_real_escape_string($this->dbh,$this->user_id));
	mysqli_query($this->dbh,$insert) or die(mysqli_error(). $insert);
	return 1;
}

function getLeavesWithoutPay($emp_id){
	$select = sprintf("SELECT * FROM fi_lwp WHERE emp_id = '%s'",
		mysqli_real_escape_string($this->dbh,$emp_id));
//echo $select;
	$res = mysqli_query($this->dbh,$select) or die(mysqli_error() . $select);
	$lwp = array();
	while($row = mysqli_fetch_assoc($res)){
		$lwp[] = $row;
	}
	return $lwp;
}

function removeLeaveWithoutPay($lwp_id){
	$delete = sprintf("DELETE FROM fi_lwp
		WHERE id = '%s'",
		mysqli_real_escape_string($this->dbh,$lwp_id));
	mysqli_query($this->dbh,$delete) or die(mysqli_error() . $delete);
	return true;
}
function removeLeavecompoff($comp_off_id){
	$delete = sprintf("DELETE FROM fi_compoff
		WHERE id = '%s'",
		mysqli_real_escape_string($this->dbh,$comp_off_id));
	mysqli_query($this->dbh,$delete) or die(mysqli_error() . $delete);
	return true;
}	
function removeLeavebereavement($bre_id){
	$delete = sprintf("DELETE FROM fi_leave
		WHERE id = '%s'",
		mysqli_real_escape_string($this->dbh,$bre_id));
	mysqli_query($this->dbh,$delete) or die(mysqli_error() . $delete);
	return true;
}
function removeLeave($leave_id){
	$delete = sprintf("DELETE FROM fi_leave
		WHERE id = '%s'",
		mysqli_real_escape_string($this->dbh,$leave_id));
	mysqli_query($this->dbh,$delete) or die(mysqli_error() . $delete);
	return true;
}
function disApproveLeaveWithoutPay($lwp_id){
        $update = sprintf("UPDATE fi_lwp SET approved = '0'
                WHERE id = '%s'",
                mysqli_real_escape_string($this->dbh,$lwp_id));
        mysqli_query($this->dbh,$update) or die(mysqli_error() . $update);
        return true;
}

function approveLeaveWithoutPay($lwp_id){
        $update = sprintf("UPDATE fi_lwp SET approved = '1',approved_date=NOW()
                WHERE id = '%s'",
                mysqli_real_escape_string($this->dbh,$lwp_id));
        mysqli_query($this->dbh,$update) or die(mysqli_error() . $update);
       $update1 = sprintf("UPDATE fi_leave SET status = 'Approved',approved_date=NOW()
                WHERE id = '%s'",
                mysqli_real_escape_string($this->dbh,$lwp_id));
        mysqli_query($this->dbh,$update1) or die(mysqli_error() . $update1);
           $this->setError('Leave Approved.');
        return true;
}
/*
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
*/
/* Newly added function by SKK for Carry Forward Leaves */
function getAllEmployees(){
	$sql="SELECT c.id, c.empno, c.cname, d.deptname, l.location
                FROM fi_emp_list c
                LEFT JOIN fi_office_locations l ON l.id = c.location
                LEFT JOIN fi_dept d ON d.id = c.dept
                WHERE 1
                AND c.status=1
                ORDER BY c.empno, d.deptname";
//echo $sql;
//exit;
        $res = mysqli_query($this->dbh,$sql) or die(mysqli_error() . $sql);
        while($row = mysqli_fetch_assoc($res)){
                $employees[] = $row;
        }
        return $employees;
}

function getLeavesReport(){
	echo $select = "SELECT empno, cname, deptname, fi_office_locations.location, emp_id, applied,
		from_dt,to_date,leave_days, typename,fi_leave.status 
		FROM fi_leave LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id 
		LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type 
                LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
                LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id
		WHERE `from_dt` > '2014-01-01 00:00:00' AND `from_dt` > '2014-12-31 00:00:00' ORDER BY empno";
	$result = mysqli_query($this->dbh,$select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}



}// class ends

