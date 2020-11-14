<?
	
session_start();
$_SESSION['hr_dept'] = true;
$_SESSION['flag_manager'] = true;
$_SESSION['empid'] = 'sasane';
$_SESSION['report_list'] = array('mvaze'=>'Megha Vaze', 'hvardhan'=>'Harsh Vardhan');
header('Location:../main.php');

include ("dbinfo.php");
openconnection();

$rp=0;
$pflag=1;
$ldap_server = 'pundc2.win.ansys.com';
$ldap_port = '389';
$login = $_POST['loginname'];
$password = $_POST['password'];

if(empty($password))
{
 $pflag=0;
}

$ldap_user = 'ansys\\'.$login;
$ldap_password = $password;
$ds=ldap_connect($ldap_server, $ldap_port);
if ($ds && $pflag) {
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);	
$r=ldap_bind($ds, $ldap_user, $ldap_password) or die(ldap_error($ds));
$attributes = array("displayname", "mail","manager","title","directReports");
	$uid=trim($login);
 $_SESSION['empid']=$uid;
 $_SESSION['lp']=$password;

 include ("ou.php");  
   $filter="(sAMAccountName=$uid)";
   #$sr=ldap_search($ds, "OU=RG - India,DC=win,DC=ansys,DC=com", $filter); 
   $sr=ldap_search($ds, $ou, $filter); 
   $info = ldap_get_entries($ds, $sr);

        $_SESSION['cname']=$cn=$info[0]['displayname'][0];
		$_SESSION['manager']=$mgr=$info[0]['manager'][0];
		$_SESSION['title']=$info[0]['title'][0];
		$_SESSION['email']=$info[0]['mail'][0];

		//for mgr email
		$filter_mgr="(sAMAccountName=*)";
		$sr_mgr=ldap_search($ds, $info[0]["manager"][0],$filter_mgr);
		$info_mgr = ldap_get_entries($ds, $sr_mgr);
		$_SESSION['manager_email']=$info_mgr[0]['mail'][0];
		$_SESSION['manager']=strtolower($info_mgr[0]['sAMAccountName'][0]);
		///mgr srch ends
		
		// srch if user is manager
		if($info[0]["directreports"]["count"]){
  		for($i=0;$i<$info[0]["directreports"]["count"]; $i++)
		{
					$result_d=search_info($ds,$info[0]["directreports"][$i]);
					#echo "DirectReports $i: ".$result_d[0]["displayname"][0].": ".$result_d[0]["mail"][0]."<br/>";
                    $tmp_cn=$result_d[0]["displayname"][0]; # common name
                    $tmp_email=getnickname($result_d[0]["mail"][0]); # email addres
                    if(empty($tmp_email)){
	                    $tmp_email = getEmpidFromCname($tmp_cn);
                    }
                    $rpt[$tmp_email]=$tmp_cn;
                    # empid => name

		}
   }
   
   
   
   
		
	if(!isset($rpt))
		{
		 $_SESSION['flag_manager']=false; #flag for if user is manager 
			$_SESSION['dept_head']=false; #flag for if user is dept header
			$_SESSION['hr_dept']=false; #flag for if user is in hr dept. 
		}
		else
		{
		 $_SESSION['flag_manager']=true;
			$_SESSION['report_list']=$rpt;
		}
		ldap_unbind($ds);
	#	srch if user is manager ends


		$sql="Select empno, deptname, dept_mgr, leave_frm_last_year, leave_current_year, leave_taken from fi_emp_list JOIN fi_dept ON fi_emp_list.dept = fi_dept.deptno where empid = '$uid'"; #Getting Emp details from fi_emp_list.
		$result = mysql_query($sql,$con);
		$row=mysql_fetch_array($result);
		$_SESSION['empno']= $row['empno'];
		if (empty($_SESSION['empno']))
		{
		 header("Location: ../logout.php");
			exit();
		}
	    $_SESSION['deptname']= $row['deptname'];
		if($_SESSION['cname']==$row['dept_mgr'])
		{$_SESSION['dept_head']=true;}
		if($_SESSION['deptname']=="HR")
		{$_SESSION['hr_dept']=true;}
		$_SESSION['lst_yr_leave']= $row['leave_frm_last_year'];
		$_SESSION['leave_curr']= $row['leave_current_year'];
		
		$_SESSION['taken_leave']= $row['leave_taken'];
		mysql_free_result($result);
		
		$sql="Select empno from fi_emp_list where empid='".$_SESSION['manager']."'"; #Getting Manager No.
		$result = mysql_query($sql,$con);
		$_SESSION['mngrno']=mysql_result($result,0,0);
		mysql_free_result($result);
		
		
		/*Edit for mngr no 120 and 160 to get reportees list*/
		if($_SESSION['empno']=="120" || $_SESSION['empno']=="160")
		{
			$sql = "Select empid from fi_emp_list where mgrno='".$_SESSION['empno']."'";
			$result = mysql_query($sql,$con);
		   if(mysql_num_rows($result))
		   {
			$sma="";
			while($row=mysql_fetch_array($result))
			{
				$sma = trim($row[0]);
				
				$tmp_cn=getcname($sma);
				$tmp_email=$sma;
				$rpt[$tmp_email]=$tmp_cn;
			}
			$_SESSION['flag_manager']=true;
			$_SESSION['report_list']=$rpt;
		   }
		   
		
		}
		
		/*Update manager with table manager.....*/
		
		$sql="Select mgrno from fi_emp_list where empno='".$_SESSION['empno']."'";
		$result = mysql_query($sql,$con);
		if(mysql_num_rows($result))
		{
			
			$d= mysql_result($result,0,0);
			if($d){
			$sql="Select empid from fi_emp_list where empno='".$d."'";
			$result = mysql_query($sql,$con);
			$mid = mysql_result($result,0,0);
			$_SESSION['manager_email']= getmail($mid);
			$_SESSION['manager']=$mid;
			$_SESSION['mngrno']=$d;
		 }
		}
		
		
		closeconnection();
		if(isset($_SESSION['url']))
		 header("Location: ".$_SESSION['url']);
		else
		 header("Location: ../main.php");
		exit();
	 

} 
else
{
	header("Location:  ../index.php?f=logincheck");
		 exit();
}
 
?>
