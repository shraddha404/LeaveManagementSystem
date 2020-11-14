<?php
#include 'db_connect.php';
function initializeDB(){
        #echo "init";
        include 'db_connect.php';
        $dbh = mysqli_connect($db_host, $db_user, $db_pass, $db) or die("Could not connect");
	return $dbh;
}

function mail_sent($to,$sub,$body,$frm=null)
{

        $headers  = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/plain; charset=us-ascii\n";
        $headers .= "Content-Transfer-Encoding: 7bit\n";
        $headers .= "X-Priority: 3\n";
        $headers .= "X-MSMail-Priority: Normal\n";
        $headers .= "X-Mailer: FluentMail\n";
        $headers .= "From: ".$_SESSION['cname']." <".$_SESSION['email'].">\n";
	$headers .= "Cc: ". $frm . "\r\n";


        mail($to, $sub, $body,$headers);
}
/*  get years By Rupali*/
function yearDropdown($startYear, $endYear, $id="year"){ 
    $dbh = initializeDB();
    //start the select tag 
    echo "<select id=".$id." name='year'>"; 
          
        //echo each year as an option     
        for ($i=$startYear;$i<=$endYear;$i++){ 
        echo "<option value=".$i.">".$i."</option>";     
        } 
      
    //close the select tag 
    echo "</select>"; 

}
/*  get Employee leave Balance By Rupali*/
function getEmpLeaveBalance($year){
    	$dbh = initializeDB();
	$select = "SELECT r.emp_id, fi_emp_list.cname, fi_dept.deptname,fi_leave_types.typename,fi_emp_list.username,fi_emp_list.id, fi_office_locations.location, r.applied,r.from_dt,r.to_date,r.leave_type,r.leave_days, fi_emp_list.empno,fi_leave_types.typename,r.status 
		FROM fi_leave r LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id
		LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type 
                LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
                LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id 
		WHERE r.leave_type IN(SELECT DISTINCT r.leave_type FROM fi_leave c WHERE r.emp_id=c.emp_id )AND r.status ='Approved' AND YEAR(r.from_dt) ='$year' AND YEAR(r.to_date) = '$year'  ORDER BY r.emp_id DESC";
	$result = mysqli_query($dbh,$select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}
/*  get Employee all leave type Balance By Rupali*/
function getEmpallLeaveBalance($id){
    	$dbh = initializeDB();
	$select = "SELECT r.emp_id, fi_emp_list.cname, fi_dept.deptname,fi_leave_types.typename,fi_emp_list.username,fi_emp_list.id, fi_office_locations.location, r.applied,r.from_dt,r.to_date,r.leave_type,r.leave_days, fi_emp_list.empno,fi_leave_types.typename,r.status 
		FROM fi_emp_list r LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id
		LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type 
                LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
                LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id 
		WHERE r.id='$id' ORDER BY r.emp_id DESC";
	$result = mysqli_query($dbh, $select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}
/*  get Employee all leave type Balance By Rupali*/
function getEmpallLeaveBalancenew($id,$typename){
    	$dbh = initializeDB();
	   $select = "SELECT empno, cname, deptname,fi_leave_types.typename,fi_emp_list.username,fi_emp_list.id, fi_office_locations.location, emp_id, applied,from_dt,to_date,leave_type,leave_days, typename,fi_leave.status ,fi_leave.approved_date 
		FROM fi_leave LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id
		LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type 
                LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
                LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id
		WHERE emp_id='".$id."'AND typename='".$typename."' ORDER BY empno DESC";
	$result = mysqli_query($dbh,$select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}
/*  get Employee all leave type Balance By Rupali*/
function getEmpwithoutpayallLeaveBalancenew($id,$typename){
    	$dbh = initializeDB();
	   $select = "SELECT empno, cname, deptname,fi_leave_types.typename,fi_emp_list.username,fi_emp_list.id, fi_office_locations.location, emp_id,from_dt, applied,to_date,days as leave_days, typename,approved,approved_date 
		FROM fi_lwp LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id
		LEFT JOIN fi_leave_types ON fi_leave_types.typename = '".$typename."' 
                LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
                LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id
		WHERE emp_id='".$id."'AND typename='".$typename."' ORDER BY empno DESC";
	$result = mysqli_query($dbh, $select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}
/*  get Employee leave Balance By Rupali*/
function getEmpOrgLeaveBalance($from_dt,$end_dt){
    	$dbh = initializeDB();
	 $select = "SELECT empno, cname,left_on, deptname,fi_leave_types.typename,fi_emp_list.username,fi_emp_list.id, fi_office_locations.location, emp_id, applied,from_dt,to_date,leave_type,leave_days, typename,fi_leave.status 
		FROM fi_leave LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id
		LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type 
                LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
                LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id
		WHERE (`from_dt` <= '".$from_dt."' && `to_date` >='".$from_dt."') || (`from_dt` >= '".$from_dt."' && `to_date` <= '".$end_dt."') || (`from_dt`<='".$end_dt."'&& `to_date` >='".$end_dt."') AND (left_on is null OR left_on = '0000-00-00') ORDER BY empno DESC";
	$result = mysqli_query($dbh, $select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}
function getManagerEmail($manager_id){
    	$dbh = initializeDB();
	$sqlmanager="SELECT * FROM fi_emp_list WHERE id=".$manager_id;
	$res1 = mysqli_query($dbh, $sqlmanager);
	$rowmanager = mysqli_fetch_assoc($res1);
	return $rowmanager;
}
function getPendingLeaves(){
    	$dbh = initializeDB();
	$sql = "SELECT fi_emp_list.cname,fi_emp_list.empno,fi_emp_list.email,
		fi_leave.emp_id , fi_leave.manager,fi_leave.status,fi_leave.applied
       		FROM fi_leave  left join fi_emp_list on
		fi_emp_list.id=fi_leave.emp_id
        	WHERE DATE(fi_leave.applied) < (NOW() - INTERVAL 7 DAY)
        	AND fi_leave.status='pending' order by fi_leave.manager";
	$res = mysqli_query($sql) or die("Error:".$sql);
        while($row = mysqli_fetch_assoc($res)){
		$pending_leaves[] = $row;
	}
	return $pending_leaves;
}

/*  get leave types By Rupali*/
function getLeaveTypes(){
	$dbh = initializeDB();
	$select="SELECT * 
		FROM fi_leave_types ORDER BY id"; 
	$res = mysqli_query($dbh, $select) or die("Error: ".$select); 
	while($row = mysqli_fetch_assoc($res)){
		$leave_types[$row['id']] = $row['typename'];
	}
	return $leave_types;
}
/*  get leave types By Rupali*/
function getLeaveTypesarray(){
	$dbh = initializeDB();
	$select="SELECT *
		FROM fi_leave_types ORDER BY id"; 
	$res = mysqli_query($dbh, $select) or die("Error: ".$select); 

	while($row = mysqli_fetch_assoc($res)){
		$leave_types[] = $row['typename'];
	}
	return $leave_types;

	//while($row = mysqli_fetch_assoc($res)){
		//$leave_types[] = $row;
	//}
	//return $leave_types;

}
/*  get employees year leaves By Rupali*/
function getEmployeesYearLeaves($criteria,$empid){
	$dbh = initializeDB();
	// If an employee has joined in the current year, 
	// his buckets will be different from the rest 
	// i.e. if fi_employee_leave_buckets has any buckets recorded for this user,
	// use them else use the general buckets for all.

	$select = "SELECT *
		FROM fi_employee_leave_buckets
		WHERE 
		employee_id = $empid
		AND
		year LIKE '%$criteria[year]%'";
	$res = mysqli_query($dbh, $select) or die("Error: ".$select); 
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type_id']] =  $row['maximum'];
	}
	if(mysqli_num_rows($res)>0){
		return $leaves;
	}
	// else proceed
		
	   $select="SELECT *
		FROM fi_leave_buckets 
		WHERE year LIKE '%$criteria[year]%'";
	$res = mysqli_query($dbh, $select) or die("Error: ".$select); 
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type_id']] =  $row['maximum'];
	}
	return $leaves;
}
/* Get leave carried forward to year By Rupali*/
function getEmpLeavesCarriedForward($criteria,$empid){
	$dbh = initializeDB();
   //keys of criteria may be year, leave_type_id 
   //refer db table fi_leaves_carry_forwards 
   //return an array with leave_type_id as keys
	
	$select="SELECT * 
		FROM fi_leave_carry_forwards 
		WHERE 
		emp_id = '$empid'AND year = '$criteria[year]'";
	$res = mysqli_query($dbh, $select) or die("Error: ".$select); 
		if(mysqli_num_rows($res)>0){
                while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type']] = $row['no_of_leaves'];
                    }
	}
	return $leaves;
}
/*Leave records By Rupali*/
function getEmployeeLeaveRecord($criteria,$empid){
	$dbh = initializeDB();
    // select all rows of leaves of a calendar year e.g. 2013
	 $select = "SELECT * FROM fi_leave 
		WHERE from_dt LIKE '%$criteria%' AND emp_id='$empid'"; //AND status='Approved'";
	$res = mysqli_query($dbh, $select) or die("Error: ".$select);
	if(mysqli_num_rows($res)>0){
                 while ($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type']] = $row['leave_days'];
            }
	}
	return $leaves;
}
/*Approved Leave records By Rupali*/
function getEmployeeApprovedLeaves($criteria,$empid){
	  $dbh = initializeDB();
	  $select = "SELECT leave_type,sum(leave_days) AS leaves,typename ,emp_id
		FROM fi_leave LEFT JOIN fi_leave_types ON leave_type=fi_leave_types.id 
		WHERE fi_leave.status='Approved' 
		AND emp_id='$empid' AND from_dt LIKE '%$criteria%'
		GROUP BY leave_type";
	$res = mysqli_query($dbh, $select) OR die("Error: ".$select);
	if(mysqli_num_rows($res)>0){
	while($row = mysqli_fetch_assoc($res)){
		$leaves[$row['leave_type']] = $row['leaves'];
	}
	return $leaves;
	}
}

function getEmployeeWithoutPay($criteria,$empid){
	  $dbh = initializeDB();
	  $select = "SELECT sum(days) AS leaves,emp_id
		FROM fi_lwp  
		WHERE fi_lwp.approved='1' 
		AND emp_id='$empid' AND from_dt LIKE '%$criteria%'
		";
	$res = mysqli_query($dbh, $select) OR die("Error: ".$select);
	if(mysqli_num_rows($res)>0){
	$leaves = mysqli_fetch_assoc($res);
	return $leaves;
	}
}
function getnotleftEmployeeList(){
	$dbh = initializeDB();
	$select = "SELECT fi_emp_list.cname, fi_emp_list.left_on,fi_dept.deptname,
		fi_emp_list.username,fi_emp_list.id, 
		fi_office_locations.location, fi_emp_list.empno 
		FROM fi_emp_list 
		LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept 
		LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id 
		WHERE (fi_emp_list.left_on is NULL OR fi_emp_list.left_on = '0000-00-00' 
			OR fi_emp_list.left_on = '1970-01-01') 
		ORDER BY fi_emp_list.id";
	$res = mysqli_query($dbh, $select);
	while($row = mysqli_fetch_assoc($res)){
		$emp_list[] = $row;
	}
	return $emp_list;
}
function getEmployeeList(){
	$dbh = initializeDB();
	$select = "SELECT fi_emp_list.cname, fi_dept.deptname,
		fi_emp_list.username,fi_emp_list.id, 
		fi_office_locations.location, fi_emp_list.empno 
		FROM fi_emp_list 
		LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept 
		LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id
		ORDER BY fi_emp_list.id";
	$res = mysqli_query($dbh, $select);
	while($row = mysqli_fetch_assoc($res)){
		$emp_list[] = $row;
	}
	return $emp_list;
}
function getLatest30daysLeavesReport(){
	$dbh = initializeDB();
	echo $select = "SELECT empno, cname, deptname, fi_office_locations.location, emp_id, applied, 
		from_dt, to_date, leave_days, typename, fi_leave.status
		FROM fi_leave
		LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id
		LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type
		LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
		LEFT JOIN fi_office_locations ON fi_emp_list.office_location = fi_office_locations.id
		WHERE fi_leave.from_dt > NOW( ) - INTERVAL 0
		DAY AND fi_leave.from_dt < NOW( ) + INTERVAL 30
		DAY ORDER BY empno";

	$result = mysqli_query($dbh,$select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}


/*
 * Get months options list.
 */
function getAllMonths($selected = ''){
    $options = '';
    for($i=1;$i<=12;$i++)
    {
        $value = ($i < 10)?'0'.$i:$i;
        $selectedOpt = ($value == $selected)?'selected':'';
        $options .= '<option value="'.$value.'" '.$selectedOpt.' >'.date("F", mktime(0, 0, 0, $i+1, 0, 0)).'</option>';
    }
    return $options;
}

/*
 * Get years options list.
 */
function getYearList($selected = ''){
    $options = '';
    for($i=2015;$i<=2025;$i++)
    {
        $selectedOpt = ($i == $selected)?'selected':'';
        $options .= '<option value="'.$i.'" '.$selectedOpt.' >'.$i.'</option>';
    }
    return $options;
}




#######################

error_reporting(0);
?>
