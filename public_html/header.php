<head>
<title>Online Leave Application - Fluent India</title>
<script language="JavaScript" src="calendar/calendar.js"></script>
<script language="JavaScript" src="calendar/calendar_1.js"></script>
<script language="JavaScript" src="include/checkform.js"></script>
<link rel="stylesheet" href="calendar/calendar.css" type="text/css">
<link rel="stylesheet" href="include/style.css" type="text/css">
<link rel="stylesheet" href="include/default.css" type="text/css">
</head>
<body>
<br>
<table width="60%" border = "0" cellpadding="3" cellspacing="2">
<tr>
<td align="center">
<div style="font-weight: bold;" align="centre">ANSYS India (HR Dept.)<br><br></div>
<div style="font-weight: bold;" align="centre"><u>Paid Leave (Vacation) Record Year - <?php echo Date('Y'); ?></u></div>
</td>
</tr>
<tr>
  <td >&nbsp;</td>
</tr>
<tr>
  <td>
  <div id="navcontainer">
  <ul id="navlist">
  <li <?php echo ($fn=="main.php")?"id=\"active\"":"";?>> <a href="main.php"<?php echo ($fn=="main.php")?"id=\"current\"":"";?> >Main Page</a></li>
  <?php if($u->isManager()){ ?>
  <li <?php echo ($fn=="my_reports.php")?"id=\"active\"":"";?>> <a href="my_reports.php" <?php echo ($fn=="my_reports.php")?"id=\"current\"":"";?> >My Reports</a></li>
  <?php }if ($u->isHR()){?>
  <li <?php echo ($fn=="dept_reports.php")?"id=\"active\"":"";?>> <a href="dept_reports.php"<?php echo ($fn=="dept_reports.php")?"id=\"current\"":"";?> >Dept Reports</a></li>
  <?php } ?>
  <?php if($u->isHR()){ ?>
  <li <?php echo ($fn=="emp.php")?"id=\"active\"":"";?>> <a href="emp.php" <?php echo ($fn=="emp.php")?"id=\"current\"":"";?> >Add/Del Emp</a></li>
  <li <?php echo ($fn=="admin_menu.php")?"id=\"active\"":"";?>> <a href="admin_menu.php" <?php echo ($fn=="admin_menu.php")?"id=\"current\"":"";?> >Admin Menu</a></li>
  <?php } ?>
  <li <?php echo ($fn=="logout.php")?"id=\"active\"":""; ?>> <a href="logout.php"<?php echo ($fn=="logout.php")?"id=\"current\"":"";?> >Logout</a></li>
  </ul>
  </div>
  </td>
</tr>
<?php 
/*
*/
?>
</table>

<br>
