<?php
include '../lib/db_connect.php';

//$argv[1]='Left-Employees-Data.csv';
$leave_data = $argv[1];
 /*if (($file = fopen($argv[1], "r")) !== FALSE)
 {*/
 
 $sql = "SELECT fi_emp_list.cname,fi_emp_list.empno,fi_emp_list.email, fi_leave.emp_id , fi_leave.manager,fi_leave.status,fi_leave.applied FROM fi_leave  left join fi_emp_list on fi_emp_list.empno=fi_leave.emp_id WHERE DATE(fi_leave.applied) < (NOW() - INTERVAL 7 DAY) AND fi_leave.status='pending' group by fi_leave.manager ";  
 $res = mysql_query($sql) or die("Error:".$sql);
        while($row = mysql_fetch_assoc($res)){
echo $sqlmanager="SELECT * FROM fi_emp_list WHERE empno=".$row['manager'];
 $res1 = mysql_query($sqlmanager);
                $rowmanager = mysql_fetch_assoc($res1);
               $to=  $rowmanager['email'];
echo $subject ="<strong>Leave Management System: </strong> approve pending leaves";
    echo  $body="
Dear ".$rowmanager['cname'].",<br>

There are one or more leaves that are pending approval. Kindly login to the leave management system to approve them. <br>

Thank you.";   


 $headers  = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/plain; charset=us-ascii\n";
        $headers .= "Content-Transfer-Encoding: 7bit\n";
        $headers .= "X-Priority: 3\n";
        $headers .= "X-MSMail-Priority: Normal\n";
        $headers .= "X-Mailer: FluentMail\n";
        $headers .= "From: ".$row['cname']." <".$row['email'].">\n";
        mail($to, $subject, $body,$headers);  } 
/*}*/
//fclose($file);

?>
