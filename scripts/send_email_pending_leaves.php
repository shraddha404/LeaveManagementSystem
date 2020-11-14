<?php
include __DIR__ . '/../lib/db_connect.php';
include __DIR__ . '/../lib/lib.php';

$pending_leaves = getPendingLeaves();
//print_r($pending_leaves);
foreach ($pending_leaves as $row){
$pending_leave_managers[$row['manager']][] = $row;
}


foreach($pending_leave_managers as $manager_id=> $leaves){
	$report_names = array();
	$rowmanager = getManagerEmail($manager_id);

	foreach($leaves as $l){
	if(!in_array($l['cname'],$report_names))
	$report_names[]=$l['cname'];
	}

        $to=  $rowmanager['email'];
        if(empty($to)){
                // can not send email if address is blank
                continue;
        }
	$subject ="Leave Management System: approve pending leaves";
	$body="
Dear ".$rowmanager['cname'].",

There are one or more leaves that are pending for approval.  Leave applications of the following employees are awaiting approval for more than 7 days.

".
implode("\n",$report_names)
."

Please click on the link to Approve/Cancel the application.

https://account.activedirectory.windowsazure.com/r#/applications

Please select Leave management System - IN.

Thank you.";
/*
*/

	$headers  = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/plain; charset=us-ascii\n";
        $headers .= "Content-Transfer-Encoding: 7bit\n";
        $headers .= "X-Priority: 3\n";
        $headers .= "X-MSMail-Priority: Normal\n";
        $headers .= "X-Mailer: FluentMail\n";
        $headers .= "From: Leave Management System <no-reply@ansys.com>\n";
        echo "Sending email to $to with subject - $subject\n";
	//$to = 'ketan404@gmail.com';
        mail($to, $subject, $body,$headers);
	//break;
        }

?>
