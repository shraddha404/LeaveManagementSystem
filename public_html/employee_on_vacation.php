<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
$u = new User();
	$criteria = array();
	if($_GET){$criteria = $_GET;}
	$criteria = array('vacation'=>'Vacation');
 		$_SESSION['location'] = $_GET['location'];
 
	$reports = $u->getEmployeeVacationReports($criteria,$_SESSION['location']);
//print_r($reports);

?>
<html>
<head>
<title>Online Leave Application - Fluent India</title>
<script language="JavaScript" src="include/checkform.js"></script>
<link rel="stylesheet" href="include/style.css" type="text/css">
<link rel="stylesheet" href="include/default.css" type="text/css">
<style>
body{
        background:#fff;
}
</style>
</head>
<body>

<!--
<h3>Change Office Location : </h3>
		<form name="employee_locations" method="post" action="">
		<table class="adminlist" cellpadding="3">
		<tr><td>
		<select name="location" onChange="form.submit();">
		<option value="-1" >ALL</option>
		<?php
			foreach($locations as $l){
		$selected = '';
		if($_SESSION['location'] == $l['id']){
			$selected = 'selected';
		}
		?>
		<option value="<?php echo $l[id];?>" <?php echo $selected;?>><?php echo $l[location]; ?></option>
		<?php
		}
		?>
		</select>
		</td>
		</tr>
		</table>
		</form>
-->
		<p>&nbsp;</p>

		<h3>Description of the leave record: <?php echo $dept; ?></h3>
<table  class="adminlist" cellpadding="3">
<tr>
<th class="title" width="20" >Emp No</th>
<th class="title" width="80" >Name</th>
<th class="title" width="40" >Dept Name</th>
<th class="title" width="40" >Location</th>
<th class="title" width="20" >Leave From Date</th>
<th class="title" width="20" >Leave To Date</th>
</tr>
<?php
$flag=1;
		$k=1;
		//if(mysql_num_rows($result))
		if(!empty($reports))	
	{
		//while($row=mysql_fetch_array($result))
		foreach($reports as $row)
		{
		if ($flag)
			{
				$tr="row1";
				$flag=0;
			}
			else
			{
				$tr="row0";
				$flag=1;
			}

			$total_leaves_for_year =$leaves_carry_forwards+$leaves_for_year;
		        $bal=$total_leaves_for_year-$leavetaken;
			echo "<tr class='$tr'>";
			//<td>".$k++."</td>
			echo "<td>".$row['empno']."</td>
			<td>".$row['cname']."</td>
			<td>".$row['deptname']."</td> 
			<td> $row[location]</td>
			<td>". date_format(date_create($row[from_dt]), 'd-M-Y')."</td>
			<td>". date_format(date_create($row[to_date]), 'd-M-Y')." </td>
			";
	//echo "<td align='center'><a href=\"viewempdetails.php?emp_id=".$row['id']."\"><img src='images/editbutton.png' border='0' ></a></td></tr>";
		}
		
	}
	else
	{
	 echo "<tr><td colspan='9'><div align='center'><b> No Data Present. </b></div></td></tr>";
	}
	echo "</table>";
?>
<? include ("footer.php");?>
