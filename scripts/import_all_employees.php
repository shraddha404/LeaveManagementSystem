<?php
include_once "../lib/db_connect.php";
include_once "../lib/User.class.php";
$datafile = $argv[1];

$u = new User();
$office_locations = $u->getOfficeLocations();
//print_r($office_locations);
$location_ids = array();
foreach($office_locations as $o_l){
    $location_ids[$o_l['location']] = $o_l['id'];
}
//print_r($location_ids);

$ous = getOUs();
//print_r($ous);
//exit;

$location_map = getLocationMap();
//print_r($location_map);
//exit;
$locations = getOfficeLocations();

$fh = fopen($datafile, "r");
while(!feof($fh)){
    $line = fgets($fh);
	$line = rtrim($line);
    //echo $line."\n";
    $fields = explode("\t", $line);

	$ou_str = str_replace('"', '', $fields[5]);
	$location_str = str_replace('"','',$fields[4]);

	$data = $fields;
	$data[4] = $locations[$location_map[$location_str]];
	$data[5] = $ous[$ou_str];
	$data[3] = getUserIdFromUsername($fields[3]);

	
	if(empty($data[3]) || empty($data[4]) || empty($data[5])){\
	print_r($data);
	}
//print_r($data);
    $emp_id = updateEmployee($data);
}
fclose($fh);

function updateEmployee($data){
	$manager= getUserIdFromUsername($data[3]);
	$update = sprintf("UPDATE fi_emp_list 
		SET cname='%s', email = '%s', manager='$data[3]', location='$data[4]', ou='$data[5]' 
		WHERE username='%s'",
		mysql_real_escape_string($data[1]),
		mysql_real_escape_string($data[2]),
		mysql_real_escape_string($data[0]));

	//echo $update."\n";
	mysql_query($update) or die(mysql_error().$update);

}

function getUserIdFromUsername($username){
                $select = sprintf("SELECT id FROM fi_emp_list
                        WHERE username='%s' AND status = 1",
                        mysql_real_escape_string($username));
                $res = mysql_query($select);
                $row = mysql_fetch_assoc($res);
                return $row['id'];
}

function getOUs(){
        $select = "SELECT * FROM fi_ou";
        $res = mysql_query($select) or die("Error:".$select);
        while($row = mysql_fetch_assoc($res)){
                $ous[$row['ou_long_string']] = $row['id'];
        }
        return $ous;
}

function getOfficeLocations(){
        $select = "SELECT * FROM fi_office_locations";
        $res = mysql_query($select) or die("Error:".$select);
        while($row = mysql_fetch_assoc($res)){
                $locations[$row['location']] = $row['id'];
        }
        return $locations;
}

function getLocationMap(){
	return array(
		'Pune (Sales), India' => 'Pune - Sales',
		'Pune, India' => 'Pune',
		'Bangalore, India' => 'Bangalore',
		'Hyderabad, India' => 'Hyderabad',
		'Noida, India' => 'Noida',
		'Noida - Apache, India' => 'Noida - Apache',
		'Bangalore - Apache, India' => 'Bangalore - Apache',
	);
}

