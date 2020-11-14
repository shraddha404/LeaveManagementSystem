<?PHP

function openconnection()
{
  global $con;
  $con = mysql_connect("localhost","leavedb","leavedb123") or die("Err: Can not connect to database!"  . mysql_error());
  mysql_select_db("leavedb",$con) or die("Err: Can not connect to specified database!");
			
} 

function closeconnection()
{
  global $con;
  mysql_close( $con);
} 


function checksel($cur ,$chk )
{
 	
	return ( strcmp(trim($cur), trim($chk)) == 0 ) ? "selected" : "" ;
} 

function GetFileName($pathurl )
{
  
  $l = strlen( $pathurl  ) ;
  $jj = (strpos($pathurl ,"?" ) ? strpos($pathurl ,"?" )+1 : 0) ;
  for ($i= $l ; $i>=1 ; $i=$i-1) {
    if (substr( $pathurl  , $i  -1, 1 )  == "/" ){
      if ($jj  > 0 ) {
        $function_ret = substr( $pathurl  , $i  + 1 -1, $jj  - ( $i  + 1 ) ) ;
      }else {
        $function_ret = substr($pathurl,$i) ;
      } 
      return $function_ret;
    } 
  } 
  return $function_ret;
} 


function Session_Check()
{
$tmp=$_SESSION['empid'];
if (empty($tmp))
{
 $trp=base64_decode($_GET['id']) ;
	$tmp_url=explode("?",$_SERVER['REQUEST_URI']);
	$tmp_url=$tmp_url[0]."?".$trp;
	
	$_SESSION['url']=$tmp_url;
	header("Location: index.php?f=sessionexpire");
 exit();
}

}

function get_total_days($date1, $date2,$holy_dates) {
  $time1  = strtotime($date1);
  $time2  = strtotime($date2);
  $my     = date('Ymd', $time2);
		
  if(date('l', $time1) != "Saturday" && date('l', $time1) != "Sunday" && !array_search(date('Y-m-d', $time1),$holy_dates))
   $dates = array(date('Y-m-d', $time1));
 
 	$f      = '';
  while($time1 < $time2) {
      $time1 = strtotime((date('Y-m-d', $time1).' +1days'));
      if(date('Ymd', $time1) != $f) {
         $f = date('Ymd', $time1);
         if(date('Ymd', $time1) != $my && ($time1 < $time2) && date('l', $time1) != "Saturday" && date('l', $time1) != "Sunday" && !array_search(date('Y-m-d', $time1),$holy_dates))
            $dates[] = date('Y-m-d', $time1);
      }
   }

  if(date('l', $time2) != "Saturday" && date('l', $time2) != "Sunday" && !array_search(date('Y-m-d', $time1),$holy_dates))
      $dates[] = date('Y-m-d', $time2);
 if($time1==$time2)
	 $dates=array_unique($dates);
		
   return count($dates);
} 

function mail_sent($to,$sub,$body,$frm)
{
        
		$headers  = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/plain; charset=us-ascii\n";
        $headers .= "Content-Transfer-Encoding: 7bit\n";
        $headers .= "X-Priority: 3\n";
        $headers .= "X-MSMail-Priority: Normal\n";
        $headers .= "X-Mailer: FluentMail\n";
        $headers .= "From: ".$_SESSION['cname']." <".$_SESSION['email'].">\n";
							
 
        mail($to, $sub, $body,$headers);
}

function getcname($val)
{
 $select = sprintf("SELECT cname FROM fi_emp_list
    WHERE empid = '%s'",
    mysql_real_escape_string($val));
    $res = mysql_query($select) or die(mysql_error());
    $row = mysql_fetch_assoc($res);
    return $row['cname'];
/*
 $ds=ldap_connect("pundc2.win.ansys.com","389");

  include ("ou.php"); 

	if ($ds) {
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);	
	 $r=ldap_bind($ds,'ansys\\'.$_SESSION['empid'],$_SESSION['lp']);
		$filter="(sAMAccountName=$val)";
  $sr=ldap_search($ds, $ou, "$filter"); 
  $info = ldap_get_entries($ds, $sr);
  $name=$info[0]['cn'][0];
		ldap_close($ds);
} 
return trim($name);
*/
}

function getEmpidFromCname($cname){
       $select = sprintf("SELECT empid FROM fi_emp_list
               WHERE cname = '%s'",
               mysql_real_escape_string($cname));
       $res = mysql_query($select) or die(mysql_error());
       $row = mysql_fetch_assoc($res);
       return $row['empid'];
}


function getnickname($val)
{
 $ds=ldap_connect("pundc2.win.ansys.com","389");

  include ("ou.php"); 
  
	if ($ds) {
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);	
	 $r=ldap_bind($ds,'ansys\\'.$_SESSION['empid'],$_SESSION['lp']);
		$filter="(mail=$val)";
	$ou = "OU=Standard,OU=Users,OU=Pune,OU=RG - India,DC=win,DC=ansys,DC=com";	
  $sr=ldap_search($ds, $ou, "$filter"); 
  $info = ldap_get_entries($ds, $sr);
  $name=strtolower($info[0]['samaccountname'][0]);
		ldap_close($ds);
} 
return trim($name);
}

function getmail($val)
{
 $ds=ldap_connect("pundc2.win.ansys.com","389");
 
 include ("ou.php"); 
 
	if ($ds) {
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);	
	 $r=ldap_bind($ds,'ansys\\'.$_SESSION['empid'],$_SESSION['lp']);
		$filter="(sAMAccountName=$val)";
  $sr=ldap_search($ds, $ou, "$filter"); 
  $info = ldap_get_entries($ds, $sr);
  $name=$info[0]['mail'][0];
		ldap_close($ds);
} 
return trim($name);
}

function checkExpDate($sdate)
{
$exp_date = strtotime($sdate); 
$todays_date = date("Y-m-d"); 
$today = strtotime($todays_date); 
$expiration_date = strtotime((date('Y-m-d', $exp_date).' +1Month'));
if ($expiration_date > $today) 
{ $valid = TRUE; } 
else { $valid = FALSE; }
return $valid;
}

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

function getceno($val)
{
 global $con;
	$sql="Select empno from fi_emp_list where empid='$val'" ;
	$res=mysql_query($sql,$con);
	$id=trim(mysql_result($res,0,0));
return trim($id);
}

function getsrno($val){
 global $con;
	$sql="Select srno from fi_emp_list where empid='$val'" ;
	$res=mysql_query($sql,$con);
	$id=trim(mysql_result($res,0,0));
return trim($id);
}

function search_info($ds,$search_fld,$fields=NULL){

  include ("ou.php"); 
		$_real_primarygroup=true;
		if ($search_fld==NULL){ return (false); }
		if ($r){ return (false); }

		$filter="samaccountname=*";
		if ($fields==NULL){ $fields=array("displayname","mail"); }
		$sr=ldap_search($ds,$search_fld,$filter,$fields);
		$entries = ldap_get_entries($ds, $sr);
		
		// AD does not return the primary group in the ldap query, we may need to fudge it
		if ($_real_primarygroup){
			$entries[0]["memberof"][]=group_cn($ds,$entries[0]["primarygroupid"][0]);
		} else {
			$entries[0]["memberof"][]="CN=Domain Users,CN=Users,".$ou;
		}
		
		$entries[0]["memberof"]["count"]++;
		return ($entries);
	}
	
	
function group_cn($ds,$gid){
		// coping with AD not returning the primary group
		// http://support.microsoft.com/?kbid=321360
		// for some reason it's not possible to search on primarygrouptoken=XXX
		// if someone can show otherwise, I'd like to know about it :)
		// this way is resource intensive and generally a pain in the @#%^
		
		if ($gid==NULL){ return (false); }
		$r=false;
	 
	  include ("ou.php"); 	 
		$filter="(&(objectCategory=group)(samaccounttype=". ADLDAP_SECURITY_GLOBAL_GROUP ."))";
		$fields=array("primarygrouptoken","samaccountname","distinguishedname");
		$sr=ldap_search($ds,$ou,$filter,$fields);
		$entries = ldap_get_entries($ds, $sr);
		
		for ($i=0; $i<$entries["count"]; $i++){
			if ($entries[$i]["primarygrouptoken"][0]==$gid){
				$r=$entries[$i]["distinguishedname"][0];
				$i=$entries["count"];
			}
		}

		return ($r);
	}

	function bulkAddRemoveLeaves($leaves){
		if($leaves < 0)
		return false;
		// add code here
        $update = sprintf("UPDATE fi_emp_list
            SET leave_frm_last_year = leave_frm_last_year + leave_current_year - leave_taken,
            leave_current_year = '%s', leave_taken = 0
            WHERE (`disabled` <> 1 OR `disabled` is NULL)",
            mysql_real_escape_string($leaves));
        mysql_query($update) or die(mysql_error());
        # set leave_frm_last_year = 20 if it exceeds that number
        $update = "UPDATE fi_emp_list
            SET leave_frm_last_year = 20, leave_taken = 0
            WHERE leave_frm_last_year > 20";
        mysql_query($update) or die(mysql_error());
        return true;
	}

	function getEmployeeDetails($srno){
		$select = sprintf("SELECT * from fi_emp_list 
				WHERE srno = '%s'", 
				mysql_real_escape_string($srno));
		$res = mysql_query($select);
		$row = mysql_fetch_assoc($res);
		return $row;
	}

	function updateEmployeeDetails($data){
		$update = sprintf("UPDATE fi_emp_list 
			SET empno = '%s', empid='%s',
			dept = '%s', leave_frm_last_year = '%s',
			leave_current_year = '%s', leave_taken = '%s',
			mgrno = '%s', cname = '%s', location = '%s', 
			ou = '%s', `disabled`='%s'
			WHERE srno = '%s'",
			mysql_real_escape_string($data['empno']),
			mysql_real_escape_string($data['empid']),
			mysql_real_escape_string($data['dept']),
			mysql_real_escape_string($data['leave_frm_last_year']),
			mysql_real_escape_string($data['leave_current_year']),
			mysql_real_escape_string($data['leave_taken']),
			mysql_real_escape_string($data['mgrno']),
			mysql_real_escape_string($data['cname']),
			mysql_real_escape_string($data['location']),
			mysql_real_escape_string($data['ou']),
			mysql_real_escape_string($data['disabled']),
			mysql_real_escape_string($data['srno']));
		mysql_query($update) or die(mysql_error());
		return true;
	}

	function addOfficeLocation($data){
		$insert = sprintf("INSERT INTO fi_office_locations
			(`location`) values('%s')",
			mysql_real_escape_string($data['location']));

		mysql_query($insert) or die(mysql_error());
		return true;
	}

	function updateOfficeLocation($data){
		$update = sprintf("UPDATE fi_office_locations
			SET `location` = '%s' 
			WHERE id = '%s'",
			mysql_real_escape_string($data['location']),
			mysql_real_escape_string($data['loc']));

			mysql_query($update) or die(mysql_error());
			return true;
	}

	function deleteOfficeLocation($loc){
		$delete = sprintf("DELETE FROM fi_office_locations
			WHERE id = '%s'",
			mysql_real_escape_string($loc));
		mysql_query($delete) or die(mysql_error());
		return true;
	}

	function getOfficeLocations(){
		$select = "SELECT * from fi_office_locations";
		$res = mysql_query($select) or die(mysql_error());
		$locations = array();
		while($row = mysql_fetch_assoc($res)){
			$locations[] = $row;
		}
		return $locations;
	}

	function addDepartment($data){
		$insert = sprintf("INSERT INTO fi_dept
		(`deptname`, `dept_mgr`)
		VALUES('%s', '%s')",
		mysql_real_escape_string($data['deptname']),
		mysql_real_escape_string($data['dept_mgr']));
		mysql_query($insert) or die(mysql_error());
		return 1;
	}

	function updateDepartment($data){
		$update = sprintf("UPDATE fi_dept
			SET deptname = '%s', dept_mgr = '%s'
			WHERE deptno = '%s'",
			mysql_real_escape_string($data['deptname']),
			mysql_real_escape_string($data['dept_mgr']),
			mysql_real_escape_string($data['deptno']));
		mysql_query($update) OR die(mysql_error());
		return 1;
	}

	function deleteDepartment($deptno){
		$delete = sprintf("DELETE FROM fi_dept 
			WHERE deptno = '%s'",
			mysql_real_escape_string($deptno));
		mysql_query($delete) or die(mysql_error());
		return 1;
	}

	function getDepartments(){
		$select = "SELECT * FROM fi_dept";
		$res = mysql_query($select);
		$depts = array();
		while($row = mysql_fetch_assoc($res)){
			$depts[] = $row;
		}
		return $depts;
	}

	function getHolidays(){
		$select = "SELECT eventdate, eventname, fi_holidays.srno, fi_office_locations.location 
		FROM fi_holidays LEFT JOIN fi_office_locations ON
		fi_holidays.location = fi_office_locations.id";
		$res = mysql_query($select);
		$holidays = array();
		while($row = mysql_fetch_assoc($res)){
			$holidays[] = $row;
		}
		return $holidays;
	}

	function addHoliday($data){
		$insert = sprintf("INSERT INTO fi_holidays
		(`eventname`, `eventdate`, `location`)
		values('%s', '%s', '%s')",
		mysql_real_escape_string($data['eventname']),
		mysql_real_escape_string($data['eventdate']),
		mysql_real_escape_string($data['location']));

		mysql_query($insert) or die(mysql_error());
		return true;
	}

	function deleteHoliday($srno){
		$delete = sprintf("DELETE FROM fi_holidays 
			WHERE srno = '%s'",
			mysql_real_escape_string($srno));
		mysql_query($delete) or die(mysql_error());
		return true;
	}

	function getEmployeeLocationId($empid){
		$select = sprintf("SELECT location 
			FROM fi_emp_list WHERE empid = '%s'",
			mysql_real_escape_string($empid));
		$res = mysql_query($select) or die(mysql_error());
		$row = mysql_fetch_assoc($res);
		return $row['location'];
	}

	function getEmployeeOUString($empid){
		$select = sprintf("SELECT ou_long_string 
			FROM fi_ou LEFT JOIN fi_emp_list
			ON fi_ou.id = fi_emp_list.ou
			WHERE empid = '%s'",
			mysql_real_escape_string($empid));
		$res = mysql_query($select) or die(mysql_error());
		$row = mysql_fetch_assoc($res);
		return $row['ou_long_string'];
	}

	function getOUs(){
		$select = "SELECT * FROM fi_ou";
		$res = mysql_query($select);
		$ou = array();
		while($row = mysql_fetch_assoc($res)){
			$ou[] = $row;
		}
		return $ou;
	}

	function addOrgUnit($data){
		$insert = sprintf("INSERT INTO fi_ou 
			(`ou_short_name`, `ou_long_string`)
			VALUES('%s', '%s')",
			mysql_real_escape_string($data['ou_short_name']),
			mysql_real_escape_string($data['ou_long_string']));
		mysql_query($insert) or die(mysql_error());
		return 1;
	}

	function deleteOrgUnit($ou){
		$delete = sprintf("DELETE FROM fi_ou
			WHERE id = '%s'",
			mysql_real_escape_string($ou));
		mysql_query($delete) or die(mysql_error());
		return 1;
	}

	function updateOrgUnit($data){
		$update = sprintf("UPDATE fi_ou 
			SET ou_short_name = '%s',
			ou_long_string = '%s' 
			WHERE id = '%s'",
			mysql_real_escape_string($data['ou_short_name']),
			mysql_real_escape_string($data['ou_long_string']),
			mysql_real_escape_string($data['ou']));
		mysql_query($update) or die(mysql_error());
		return 1;
	}
?>
