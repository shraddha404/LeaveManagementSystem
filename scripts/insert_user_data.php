<?php
include ("../lib/db_connect.php");
$leave_data = $argv[1];

 if (($file = fopen($argv[1], "r")) !== FALSE)
 { 
    while (($data = fgetcsv($file)) !== FALSE)
           {

		
 $selectlocation="SELECT * FROM `fi_office_locations` WHERE `location` LIKE '$data[5]'";
		$reslocation=mysql_query($selectlocation);
               $rowlocation = mysql_fetch_assoc($reslocation);


$selectdept="SELECT * FROM `fi_dept` WHERE `deptname` LIKE '$data[7]'";
		$resdept=mysql_query($selectdept);
               $rowdept = mysql_fetch_assoc($resdept);


$selectou="SELECT * FROM `fi_ou` WHERE `ou_short_name` LIKE '$data[6]'";
		$resou=mysql_query($selectou);
               $rowou = mysql_fetch_assoc($resou);



	if($data[1]=="-"){
			$sql ="UPDATE `fi_emp_list` SET `empno`='$data[0]',`cname`='$data[2]',`dept`='$rowdept[id]',`location`='$rowlocation[id]',`ou`='$rowou[id]' WHERE `username` LIKE 'admin'";
	}else if(!empty($data[1])){
			if($data[1]=="-") continue;
			$sql ="UPDATE `fi_emp_list` SET `empno`='$data[0]',`cname`='$data[2]',`dept`='$rowdept[id]',`location`='$rowlocation[id]',`ou`='$rowou[id]' WHERE `email` LIKE '$data[1]'";
	}else{    
			$select="SELECT * FROM `fi_emp_list` WHERE `email` LIKE '$data[1]'";
				if (mysql_num_rows($select)==0) {
					$sql ="INSERT INTO `fi_emp_list` (`empno`, `cname`, `dept`, `location`, `ou`, `email`)
					VALUES ('$data[0]','$data[2]','$rowdept[id]','$rowlocation[id]','$rowou[id]','$data[1]')";
				}
	       }
	       if (mysql_query($sql)) {
		        echo "Updated!";
		    } else {
		       echo "Error updating " . mysql_error();
		    }
		}

    }

fclose($file);?>
