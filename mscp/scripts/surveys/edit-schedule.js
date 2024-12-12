
/*********************************************************************************************\
***********************************************************************************************
**                                                                                           **
**  SCRP - School Construction and Rehabilitation Programme                                  **
**  Version 1.0                                                                              **
**                                                                                           **
**  http://www.humdaqam.pk                                                                   **
**                                                                                           **
**  Copyright 2015 (C) Triple Tree Solutions                                                 **
**  http://www.3-tree.com                                                                    **
**                                                                                           **
**  ***************************************************************************************  **
**                                                                                           **
**  Project Manager:                                                                         **
**                                                                                           **
**      Name  :  Muhammad Tahir Shahzad                                                      **
**      Email :  mtshahzad@sw3solutions.com                                                  **
**      Phone :  +92 333 456 0482                                                            **
**      URL   :  http://www.mtshahzad.com                                                    **
**                                                                                           **
***********************************************************************************************
\*********************************************************************************************/

$(document).ready(function( )
{
	$("#txtDate").datepicker(
	{
		showOn          : "both",
		buttonImage     : "images/icons/calendar.gif",
		buttonImageOnly : true,
		dateFormat      : "yy-mm-dd"
	});
	
	
	$("#txtCode").autocomplete({ source:"ajax/get-emis-codes-list.php", minLength:3 });
	
	$("#txtCode").blur(function( )
	{
		if ($("#txtCode").val( ) == "")
			return;

		$.post("ajax/surveys/check-schedule.php",
			{ ScheduleId:$("#ScheduleId").val( ), Code:$("#txtCode").val( ) },

			function (sResponse)
			{
				if (sResponse == "INVALID")
				{
					showMessage("#RecordMsg", "info", "The provided EMIS Code is Invalid, No school found in the system.");

					$("#DuplicateSchedule").val("0");
				}
				
				else if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "This School Survey is already scheduled. Please specify another EMIS Code.");

					$("#DuplicateSchedule").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateSchedule").val("0");
				}
			},

			"text");
	});
	
	
	
	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("ddEnumerator", "B", "Please select the Enumerator."))
			return false;

		if (!objFV.validate("txtCode", "B,N", "Please enter the EMIS Code."))
			return false;

		if (!objFV.validate("txtDate", "B", "Please select the Survey Date."))
			return false;
		
		if (objFV.value("DuplicateSchedule") == "1")
		{
			showMessage("#RecordMsg", "info", "This School Survey is already scheduled. Please specify another EMIS Code.");

			objFV.focus("txtCode");
			objFV.select("txtCode");

			return false;
		}

		
		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
		
		return true;
	});
});