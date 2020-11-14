<?php

include "old_db_connect.php";

$leaves = getAllLeaves();

//print_r($leaves);
foreach($leaves as $leave){
    $mgr_comment = str_replace("\n", "", str_replace("\r", "", $leave['mgr_comment']));
    $reason = str_replace("\n", "", str_replace("\r", "", $leave['reason']));
    /*
    echo "old - $leave[mgr_comment]\n";
    echo "new - $mgr_comment\n";
    */
    echo "$leave[empno]\t$leave[application_dt]\t$leave[from_dt]\t$leave[to_dt]\t$leave[no_of_days]\t$reason\t$mgr_comment\t$leave[status]\n";
}

function getAllLeaves(){
    $select = "SELECT * FROM fi_leave";
    $res = mysql_query($select);
    $leaves = array();
    while($row = mysql_fetch_assoc($res)){
        $leaves[] = $row;
    }
    return $leaves;
}
