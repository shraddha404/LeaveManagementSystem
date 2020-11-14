<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/php_header.php';
#print_r($u);
/*
 * Function requested by Ajax
 */
if(isset($_POST['func']) && !empty($_POST['func'])){
    switch($_POST['func']){
        case 'getCalender':
            getCalender($_POST['year'],$_POST['month'],$_POST['leave_dates']);
            break;
        case 'getEvents':
            getEvents($_POST['date']);
            break;
        default:
            break;
    }
}
$sessionuserid=$_SESSION['user_id'];

/*
 * Get calendar full HTML
 */
 $month = $_REQUEST['month'];
 $year = $_REQUEST['year'];
    $dateYear = ($year != '')?$year:date("Y");
    $dateMonth = ($month != '')?$month:date("m");
    $date = $dateYear.'-'.$dateMonth.'-01';
    $currentMonthFirstDay = date("N",strtotime($date));
    //$totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN,$dateMonth,$dateYear);
    $totalDaysOfMonth = date('t', mktime(0, 0, 0, $dateMonth, 1, $dateYear)); 
    $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 7)?($totalDaysOfMonth):($totalDaysOfMonth + $currentMonthFirstDay);
    $boxDisplay = ($totalDaysOfMonthDisplay <= 35)?35:42;
?>
<body onload="getCalendar('calendar_div','<?php echo date("Y",strtotime($date)); ?>','<?php echo date("m",strtotime($date)); ?>');">
    <div id="calender_section">
        <h2>
            <a href="javascript:void(0);" onclick="getCalendar('calendar_div','<?php echo date("Y",strtotime($date.' - 1 Month')); ?>','<?php echo date("m",strtotime($date.' - 1 Month')); ?>');">&lt;&lt;</a>
            <select name="month_dropdown" class="month_dropdown dropdown"><?php echo getAllMonths($dateMonth); ?></select>
            <select name="year_dropdown" class="year_dropdown dropdown"><?php echo getYearList($dateYear); ?></select>
            <a href="javascript:void(0);" onclick="getCalendar('calendar_div','<?php echo date("Y",strtotime($date.' + 1 Month')); ?>','<?php echo date("m",strtotime($date.' + 1 Month')); ?>');">&gt;&gt;</a>
        </h2>
        <div id="event_list" class="none"></div>
        <div id="calender_section_top">
            <ul>
                <li>Sun</li>
                <li>Mon</li>
         	<li>Tue</li>
                <li>Wed</li>
                <li>Thu</li>
                <li>Fri</li>
                <li>Sat</li>
            </ul>
        </div>
        <div id="calender_section_bot">
            <ul>
            <?php
	############# New code by SKK
			$new_leave_dates = $u->getEmployeeLeaveDays($month,$year);
//print_r($new_leave_dates);
	############### Ends

//print_r($new_leave_dates);
                $dayCount = 01; 
                for($cb=1;$cb<=$boxDisplay;$cb++){
                    if(($cb >= $currentMonthFirstDay+1 || $currentMonthFirstDay == 7) && $cb <= ($totalDaysOfMonthDisplay)){
		    #echo $currentDate."skk";
                    $currentDate = $dateYear.'-'.$dateMonth.'-'.$dayCount;
                    $sessionuserid=$_SESSION['user_id'];
                    $eventNum = 0;
                    //Get number of events based on the current date
		$currentDate = $dateYear.'-'.$dateMonth.'-'.sprintf("%02d",$dayCount);

                        //Date cell
              		echo '<li date="'.$currentDate.'" class="date_cell"><span>'. $dayCount. '</span>'.
			implode("<br/>",$new_leave_dates[$currentDate]);
                        
                        //Hover event popup
                        /*echo '<div id="date_popup_'.$currentDate.'" class="date_popup_wrap none">';
                        echo '<div class="date_window">';
                        echo '<div class="popup_event">Leaves ('.$eventNum.')</div>';
                        echo ($eventNum > 0)?'<a href="javascript:;" onclick="getEvents(\''.$currentDate.'\');">view leaves</a>':'';
                        echo '</div></div>';*/
                        
                        echo '</li>';
                        $dayCount++;
            ?>
            <?php }else{ ?>
                <li><span>&nbsp;</span></li>
            <?php } } ?>
            </ul>
        </div>
    </div>
<?php 
#} ### getCalender function ends
?>

    <script type="text/javascript">
        function getCalendar(target_div,year,month){
            $.ajax({
                type:'GET',
                url:'calfunction.php',
                data:'func=getCalender&year='+year+'&month='+month,
                success:function(html){
                    $('#'+target_div).html(html);
                }
            });
        }
        
        function getEvents(date){
            $.ajax({
                type:'POST',
                url:'calfunction.php',
                data:'func=getEvents&date='+date,
                success:function(html){
                    $('#event_list').html(html);
                    $('#event_list').slideDown('slow');
                }
            });
        }
        
        function addEvent(date){
            $.ajax({
                type:'POST',
                url:'calfunction.php',
                data:'func=addEvent&date='+date,
                success:function(html){
                    $('#event_list').html(html);
                    $('#event_list').slideDown('slow');
                }
            });
        }
        
        $(document).ready(function(){
            $('.date_cell').mouseenter(function(){
                date = $(this).attr('date');
                $(".date_popup_wrap").fadeOut();
                $("#date_popup_"+date).fadeIn();    
            });
            $('.date_cell').mouseleave(function(){
                $(".date_popup_wrap").fadeOut();        
            });
            $('.month_dropdown').on('change',function(){
                getCalendar('calendar_div',$('.year_dropdown').val(),$('.month_dropdown').val());
            });
            $('.year_dropdown').on('change',function(){
                getCalendar('calendar_div',$('.year_dropdown').val(),$('.month_dropdown').val());
            });
            $(document).click(function(){
                $('#event_list').slideUp('slow');
            });
        });
    </script>
</body>
