
/// Form Check Functions

function checkData1()
{
 var f= document.form1;
	
	if (f.from_dt.value==0)
	{
	 alert("Please provide us with a Leave From Date");
		return false;
	}
	if (f.to_dt.value==0)
	{
	 alert("Please provide us with a Leave To Date");
		return false;
	}
	if(f.from_dt.value > f.to_dt.value)
	{
	 alert("Invalid Leave To Date. \n");
  return false;
	}
	if (f.type_leave.value == 'Not_Selected') 
	{
  alert("Please provide us with a Leave Type.\n");
  return false;
 }
	if (f.type_leave.value != '1' && f.comments.value.length==0) 
	{
  alert("Please provide us with a Other Leave Type in a Remark field.\n");
		f.comments.focus();
  return false;
 }
	return true;
}

function checkData2()
{
 var f= document.form2;
	
	if (f.work_dt.value==0)
	{
	 alert("Please provide us with a Date of Holiday Worked");
		return false;
	}
}

function chkdt()
{
//  alert('i m here');
 var edt = new Date(document.form1.to_dt.value);
 var sdt = new Date(document.form1.from_dt.value);
	if(sdt!=edt)
	{
	 document.form1.half_day.disabled='true';
	}
	else
		{
	 document.form1.half_day.enabled='true';
	}
	
}
