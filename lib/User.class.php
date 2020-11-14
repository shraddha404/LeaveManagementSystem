<?php
#include_once "db_connect.php";
include_once "lib.php";
include_once 'PHPMailer_5.2.0/class.phpmailer.php';
include_once 'PHPMailer_5.2.0/PHPMailerAutoload.php';

class User{
    //class variables
    var $user_id = null;
    var $user_type = null;
    var $ua = null; //user agent
    var $error = null;
    var $error_code = null;
    var $user_profile = null;
    var $app_config = null;

    public function __construct($user_id=null){
        $this->user_id = $user_id;
	$this->initializeDB();
        if(!empty($user_id)){
            $this->user_profile = $this->getUserDetails($user_id);
        }
		#$this->app_config = getConfig();
    }


   function __call($functionName, $argumentsArray ){
        $this->setError('undefined_function '.$functionName);
    }

   private function initializeDB(){
	#echo "init";
	include 'db_connect.php';
	$this->dbh = mysqli_connect($db_host, $db_user, $db_pass, $db) or die("Could not connect");
   }

public function authenticate($username, $password){

	$ldap_server = "pundc2.win.ansys.com";
	$ldap_port = '389';
	$ldap_user = 'ansys\\'.$username;
	//$ds=ldap_connect($ldap_server, $ldap_port) or die("Could not connect to LDAP server.");
$ds = 1;
	//bind only if password is not empty
	// and connection with LDAP server is established
	#if($ds && !empty($password)){ 
		/*ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);	
		if(!ldap_bind($ds, $ldap_user, $password)){
			$this->setError(ldap_error($ds));
			// Can not bind to LDAP
            		// Try local authentication
        */    		
            		if($this->authenticateLocal($username, $password)){
                		$this->user_id = $this->getUserIdFromUsername($username);
                		return true;
            		}
          /*  		// at this point, local authentication has also failed
            		return false;
		}
		*/
		// set the user_id and return true
    		$this->user_id = $this->getUserIdFromUsername($username);
        	if(empty($this->user_id)){
            		$this->setError('Could not find your account in Leave Management System');
            		return false;
        	}
		// sync local db with info from LDAP
		//pass the LDAP connection object
		$this->syncWithLDAP($username,$password);
		//Unbind LDAP
		ldap_unbind($ds);
		return true;
	#}
    return false;
}

public function getUserIdFromUsername($username){
		$select = sprintf("SELECT id FROM fi_emp_list
			WHERE username='%s' AND status = 1",
			mysqli_real_escape_string($this->dbh,$username));
		$res = mysqli_query($this->dbh,$select);
		$row = mysqli_fetch_assoc($res);
		return $row['id'];
}

public function getUserIdFromEmail($email){
		$select = sprintf("SELECT id FROM fi_emp_list
			WHERE email='%s' AND status = 1",
			mysqli_real_escape_string($this->dbh, $email));
		$res = mysqli_query($this->dbh, $select);
		$row = mysqli_fetch_assoc($res);
		return $row['id'];
}

public function getEmployeeOUString($username){
	$select = "SELECT ou_long_string
		FROM fi_ou LEFT JOIN fi_emp_list
		ON fi_ou.id = fi_emp_list.ou
                WHERE fi_emp_list.username = '$username'";
//echo $select;
        $res = mysqli_query($this->dbh, $select) or die(mysqli_error().$select);
	$row = mysqli_fetch_assoc($res);
	if(empty($row['ou_long_string'])){
		//default OU string
		return "OU=Standard,OU=Users,OU=Pune,OU=RG - India,DC=win,DC=ansys,DC=com";
	}
        return $row['ou_long_string'];
}
public function getLatest30daysLeavesReport(){
	 $select = "SELECT empno, cname, deptname, fi_office_locations.location, emp_id, applied,
		from_dt,to_date,leave_days, typename,fi_leave.status 
		FROM fi_leave LEFT JOIN fi_emp_list ON emp_id = fi_emp_list.id 
		LEFT JOIN fi_leave_types ON fi_leave_types.id = leave_type 
        LEFT JOIN fi_dept ON fi_dept.id = fi_emp_list.dept
        LEFT JOIN fi_office_locations ON fi_emp_list.location = fi_office_locations.id
	WHERE fi_leave.from_dt > NOW() - INTERVAL 0 DAY AND fi_leave.from_dt < NOW() + INTERVAL 30 DAY ORDER BY empno";

	$result = mysqli_query($this->dbh, $select);
	while($row = mysqli_fetch_assoc($result)){
		$latest_report[] = $row;
	}
	return $latest_report;
}
public function syncWithLDAP($username, $password){
        $ldap_server = 'pundc2.win.ansys.com';
        $ldap_port = '389';
        $ldap_user = 'ansys\\'.$username;
        $ds=ldap_connect($ldap_server, $ldap_port);
        //bind only if password is not empty
        // and connection with LDAP server is established
        if($ds && !empty($password)){
                ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
                if(!ldap_bind($ds, $ldap_user, $password)){
                        $this->setError(ldap_error($ds));
                        return false;
		}
        }
	$ou = $this->getEmployeeOUString($username);
	$attributes = array("displayname", "mail","manager","title","directReports");
        //$username=trim($this->user_profile['username']);
	$filter="(sAMAccountName=$username)";
	$sr=ldap_search($ds, $ou, $filter); 
	$info = ldap_get_entries($ds, $sr);

	// my email 
	$my_email = $info[0]['mail'][0];
	$this->updateMyEmail($my_email);

//print_r($info);
//print $info[0]['manager'][0];
	// Now enter/update this info in the local db
	$manager_string = $info[0]['manager'][0];
	$manager_fields = explode(",", $manager_string);
	$manager = preg_replace("/^CN=/","", $manager_fields[0]);
	//echo $manager;
//for mgr email
        $filter_mgr="(sAMAccountName=*)";
        $sr_mgr=ldap_search($ds, $info[0]["manager"][0],$filter_mgr);
        $info_mgr = ldap_get_entries($ds, $sr_mgr);
//print_r($info_mgr);
	$manager_email = $info_mgr[0]['mail'][0];
	$manager_login = $info_mgr[0]['samaccountname'][0];
	//echo $manager_email;
	// currently, syncing with LDAP means updating manager info
	// update of cname may not be required
	if(!empty($manager_login)){
		// manager login fetched successfully from LDAP
		return $this->updateMyManager($username, $manager_login, $manager_email);
	}
	else{
		// manager info could not be fetched from LDAP for some reason
		$this->setError('Your manager info could not be updated.');
		return false;
	}
}

public function updateMyEmail($email){
	$update = sprintf("UPDATE fi_emp_list SET email = '%s'
		WHERE id = $this->user_id",
		mysqli_real_escape_string($this->dbh, $email));
	$res = mysqli_query($this->dbh, $update);
	if($res)
	return true;
	return false;
}

public function updateMyManager($username, $manager_login, $manager_email){
	$manager_id = $this->getUserIdFromUsername($manager_login);
	$update = "UPDATE fi_emp_list SET manager = $manager_id
		WHERE username = '$username'";
//echo $update;
	if(!mysqli_query($this->dbh, $update)){
		$this->setError('Your manager info could not be updated.');
		return false;
	}  
	$update = "UPDATE fi_emp_list SET email = '$manager_email'
		WHERE id = $manager_id";
	mysqli_query($this->dbh, $update) or die(mysqli_error() .$update);
	return true;
}

function authenticateLocal($username,$password){
        $select = sprintf("SELECT id FROM fi_emp_list 
            WHERE username = '%s' AND password = '%s' AND status = 1",
            mysqli_real_escape_string($this->dbh, $username),
            md5($password));
        if(!($res = mysqli_query($this->dbh, $select))){
            return false;
        }
        if(mysqli_num_rows($res) > 0){
            $res_ar = mysqli_fetch_assoc($res);
            // set user id of the logged in user    
            $this->user_id = $res_ar['id'];
            if(empty($this->user_id)){
                $this->setError('Could not find your account in Leave Management System');
                return false;
            }
            //$this->user_type = $this->getUserType();
            return true;
        }
        else{
        	$this->setError('Invalid Username or Password');
            return false;
	
        }
    }

public function isEmployee(){
	if(!empty($this->user_id)){
		return true;
	}
	return false;
}


    function _loginRedirect(){
            // send user to the login page
            header("Location:/index.php");
    }

    function setError($error){
            error_log($error);
            $this->error = $error;
    }

/* Get details of a user */
function getUserDetails($user_id){
    //
	$select = sprintf("SELECT *,fi_emp_list.id AS id
		FROM fi_emp_list
		LEFT JOIN fi_dept ON fi_emp_list.dept = fi_dept.id
		WHERE fi_emp_list.id='$user_id'");
//echo $select;
	$res = mysqli_query($this->dbh,$select);
	if(mysqli_num_rows($res) > 0){
		$user_details = mysqli_fetch_assoc($res);
	}
		return $user_details;
}

/*
Get office locations
*/

public function getOfficeLocations(){
	$select = "SELECT * FROM fi_office_locations";
	$res = mysqli_query($this->dbh,$select) or die("Error:".$select);
	while($row = mysqli_fetch_assoc($res)){
		$locations[] = $row; 
	}
	return $locations;
}
/*
Get Departments
*/
public function getDept(){
	$select = "SELECT * FROM fi_dept";
	$res = mysqli_query($this->dbh, $select) or die("Error:".$select);
	while($row = mysqli_fetch_assoc($res)){
		$dept[] = $row; 
	}
	return $dept;
}

/* 
Get leave types
*/
public function getLeaveTypes(){
	$select="SELECT * FROM fi_leave_types ORDER BY id";
	$res = mysqli_query($this->dbh,$select) or die("Error: ".$select); 
	while($row = mysqli_fetch_assoc($res)){
		$leave_types[$row['id']] = $row['typename'];
	}
	return $leave_types;
}

public function isManager(){
	$select = "SELECT * FROM fi_emp_list 
		WHERE manager = $this->user_id
        AND status=1";
	$res = mysqli_query($this->dbh,$select);
	if(mysqli_num_rows($res)>0){
		return true;
	}
	else{
		return false;
	}
}

public function isHR(){
	$select = "SELECT deptname FROM fi_dept 
		LEFT JOIN fi_emp_list ON fi_dept.id=fi_emp_list.dept
		WHERE fi_emp_list.id = $this->user_id";
	$res = mysqli_query($this->dbh,$select);
	if(mysqli_num_rows($res)>0){
		$dept_array = mysqli_fetch_assoc($res);
		if($dept_array['deptname'] == 'HR' ||  $dept_array['deptname'] == '1501 Human Resources'){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}
function sendSMTPEmail($to, $name, $email_subject, $email_body) {
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
        $mail->SetFrom('shraddha404@gmail.com','Shraddha');
        $mail->AddAddress($to, $name);
        $mail->AddAddress('rupali@carvingit.com', $name);
        $mail->AddAddress('shraddha404@gmail.com','Shraddha');
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
    }
/*(public function sendSMTPEmail($to, $subject, $email_body){

############ Send mail by SMTP

$mail = new PHPMailer();
$mail->IsSMTP(true);
$mail->Host = "bh-13.webhostbox.net";  // specify main and backup server
$mail->SMTPAuth= true;
$mail->Username = "carvingitinfo@carvingit.com";  // SMTP username
$mail->Password = "carvingitinfo123"; // SMTP password
$mail->Port = 465; // not 587 for ssl 
$mail->SMTPDebug = 2; 
$mail->SMTPSecure = 'ssl';
$mail->SetFrom('rupali@carvingit.com', 'rupali');
$mail->AddAddress('priyanka@carvingit.com', 'priyanka');
$mail->AddAddress('rupali@carvingit.com', 'rupali');
$mail->AddAddress($to, 'rupali');

$mail->Subject = $subject;
$mail->Body    = $email_body;

if(!$mail->Send()) {
echo 'Error : ' . $mail->ErrorInfo;//echo $email_body;exit;
} else { //echo "Hiiiielse";
//echo $email_body;exit;

}        
	

}*/
function mail_sent($to,$sub,$body,$frm=null)
{

                $headers  = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/plain; charset=us-ascii\n";
        $headers .= "Content-Transfer-Encoding: 7bit\n";
        $headers .= "X-Priority: 3\n";
        $headers .= "X-MSMail-Priority: Normal\n";
        $headers .= "X-Mailer: FluentMail\n";
        $headers .= "From: ".$this->user_profile['cname']." <".$this->user_profile['email'].">\n";
        mail($to, $sub, $body,$headers);
}

public function getEmployeeVacationReports($criteria,$location){
        $status = empty($criteria['status'])?1:0;
        $alphabet_clause = empty($criteria['arg1'])?'':" AND c.cname LIKE '".$criteria['arg1']."%' ";
        $search_clause = empty($criteria['semp'])? '' : " AND (c.cname LIKE '%".$criteria['semp']."%'
                                OR c.empno = '".$criteria['semp']."'
                                OR c.username = '".$criteria['semp']."')";

        if($location == (-1)){
                $location_clause = '';
        }
        else{
                $location_clause = " AND c.location = '".$location."'";
        }

        if($criteria['vacation'] == 'Vacation'){
		$date = date('Y-m-d');
                $vacation_clause = " AND v.to_date >= '$date' AND v.from_dt <= DATE_ADD(NOW(), INTERVAL 30 DAY)";

        }

        $sql="SELECT c.id, c.empno, c.cname, d.deptname, l.location,
                v.from_dt, v.to_date,v.reason
                FROM fi_emp_list c
                LEFT JOIN fi_office_locations l ON l.id = c.location
                LEFT JOIN fi_dept d ON d.id = c.dept
                LEFT JOIN fi_leave v ON v.emp_id = c.id
                WHERE 1
                AND c.status=$status
                $alphabet_clause
                $search_clause
                $location_clause
                $vacation_clause
                ORDER BY d.deptname, c.empno";
        $res = mysqli_query($this->dbh,$sql) or die(mysqli_error() . $sql);
//echo $sql;
        while($row = mysqli_fetch_assoc($res)){
                $reports[] = $row;
        }
        return $reports;
}


} // class ends
