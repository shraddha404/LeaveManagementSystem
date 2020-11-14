<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
include ("header.php");



if($u->isHR())
{

if($_GET['id']=='a')
{
$approve=$u->approveallpendingleave();
header("location:admin_menu.php");
}
$locations = $u->getOfficeLocations();
	?>
	<h3><u>Admin menu: </u></h3>
	<ul>
		<li><a href="manage_office_locations.php">Manage Office Locations</a></li>
		<li><a href="manage_org_units.php">Manage Org Units</a></li>
		<li><a href="manage_holidays.php">Manage Holidays</a></li>
		<li><a href="manage_departments.php">Manage Departments</a></li>
		<li><a href="manage_leave_buckets.php">Manage Leave Buckets</a></li>
		<li><a href="latest_report.php">Latest Report of Leaves</a></li>
		<li><a href="latest_one_month_report.php">Latest One Month Report of Leaves</a></li>
                <li><a href="org_leave_report.php">Org Leave report</a></li>
		<li><a href="employee_leave_balance.php">Report of leave balances</a></li>
		<li><a href="employee_leave_report.php">Annual Database Dump</a></li>
		<li><a href="manage_configs.php">Manage config values</a></li>
		<!--<li><a href="bulk_add_leaves.php">Bulk Add Leaves</a></li>-->
		<li><a href="admin_menu.php?id=a" onclick="return confirm('Okay to mark all pending leaves as aproved?')">Approve All Pending Leaves</a></li>
	</ul>
<h3>Employees on Vacation</h3>
                <form name="employee_locations" method="post" action="">
                <table class="adminlist" cellpadding="3">
                <?php
                foreach($locations as $l){
		?>
                <tr><td>
               	<a href="employee_on_vacation.php?location=<?php echo $l['id'];?>"><?php echo $l['location'];?></a> 
                </td></tr>
		<?php
                }
                ?>
                </table>
                </form>

<?php
}else {
 echo "<h3>You are not authorised to view this page. Contact HR.</h3>";
}
include ("footer.php");
?>
