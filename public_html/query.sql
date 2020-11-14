select leave_type,sum(leave_days) from fi_leave group by leave_type;
select leave_type,sum(leave_days),typename from fi_leave left join fi_leave_types on leave_type=fi_leave_types.id group by leave_type;
