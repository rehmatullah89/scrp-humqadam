
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
		dateFormat      : "yy-mm-dd",
		maxDate         : 0
	});
	
	
	$("#txtCode").blur(function( )
	{
		if ($("#txtCode").val( ) == "")
			return;


		$.post("ajax/surveys/check-survey.php",
			{ Code:$("#txtCode").val( ) },

			function (sResponse)
			{
				$("#Province").val(sResponse);
				
				
				if (sResponse == "1")
					$("#PefProgramme").show("blind");
				
				else
					$("#PefProgramme").hide("blind").val("");
			},

			"text");
	});

	
	$("#frmRecord select").change(function( )
	{
		var sData = $(this).attr("rel");
		
		if (typeof sData !== "undefined" && sData != "")
		{
			var sParams    = sData.split("|");
			var bException = false;
			
			if ($(this).attr("id") == "ddLandAvailable" && $("#Province").val( ) == "2")
				bException = true;
			
			
			if ($(this).val( ) == sParams[1] && bException == false)
			{
				$("#frmRecord div." + sParams[0]).each(function( )
				{
					if ($(this).css("display") == "block")
						$(this).hide("blind");
					
					$("#frmRecord div." + sParams[0] + " select, #frmRecord div." + sParams[0] + " input").val("");
				});
			}
			
			else
			{
				$("#frmRecord div." + sParams[0]).each(function( )
				{
					if ($(this).css("display") != "block")
						$(this).show("blind");
				});
			}
		}
	});
	
	
	$("#ddOperational").change(function( )
	{
		if ($(this).val( ) == "N")
			$("#ddNonOperational").show( );
		
		else
			$("#ddNonOperational").hide( ).val("");
	});
	
	
	$("#ddLandDispute").change(function( )
	{
		if ($(this).val( ) == "Y")
			$("#ddDispute").show( );
		
		else
			$("#ddDispute").hide( ).val("");
	});	
	
	
	$("#ddOtherFunding").trigger("change");
	$("#ddLandDispute").trigger("change");
	$("#ddLandAvailable").trigger("change");
	$("#ddOperational").trigger("change");
	$("#ddPefProgramme").trigger("change");

	
	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtCode", "B,N", "Please enter the EMIS Code."))
			return false;
		
		if ($("#Province").val( ) == "0")
		{
			showMessage("#RecordMsg", "alert", "Invalid EMIS Code, no school found. Please enter a valid EMIS Code.");
			
			return false;
		}


		if (!objFV.validate("txtEnumerator", "B,C", "Please enter the Enumerator Name."))
			return false;

		if (!objFV.validate("txtDate", "B", "Please select the Survey Date."))
			return false;

		if (!objFV.validate("ddOperational", "B", "Is the school operational?"))
			return false;

		if (objFV.value("ddOperational") == "Y")
		{
			if ($("#Province").val( ) == "1")
			{
				if (!objFV.validate("ddPefProgramme", "B", "Is the school part of the PEF (Punjab Education Foundation) Programme?"))
					return false;
			}

			if (objFV.value("ddPefProgramme") == "N" || objFV.value("ddPefProgramme") == "")
			{			
				if (!objFV.validate("ddLandAvailable", "B", "Does the school have enough land for new construction?"))
					return false;
				
				if (objFV.value("ddLandAvailable") == "Y" || $("#Province").val( ) == "2")
				{
					if (!objFV.validate("ddLandDispute", "B", "Is the school having any land dispute?"))
						return false;
					
					if (objFV.value("ddLandDispute") == "N")
					{
						if (!objFV.validate("ddOtherFunding", "B", "Is the school involved in any other project providing funding for classroom infrastructure?"))
							return false;
						
						if (objFV.value("ddOtherFunding") == "N")
						{
							if (!objFV.validate("txtClassRooms", "B,N", "How many classrooms does your school have?"))
								return false;
							
							if (!objFV.validate("txtEducationRooms", "B,N", "Out of the total number how many classrooms are in use for educational purposes?"))
								return false;
							
							
							if (parseInt(objFV.value("txtEducationRooms")) > parseInt(objFV.value("txtClassRooms")))
							{
								showMessage("#RecordMsg", "info", "Please enter valid No of Classrooms that are in use for educational purposes?.");
								
								return false;
							}
							
							
							if (!objFV.validate("ddShelterLess", "B", "Are there any shelter-less grades being taught?"))
								return false;
							
							if (!objFV.validate("ddMultiGrading", "B", "Are there more than 2 grades being taught in one classroom (multi-grading)?"))
								return false;
							
							if (!objFV.validate("txtAvgAttendance", "B,N", "What is the average attendance of school?"))
								return false;
							
							if (!objFV.validate("ddPreSelection", "B", "Does the School Qualify Pre-Selection?"))
								return false;
						}
					}
					
					else
					{
						if (!objFV.validate("ddDispute", "B", "Please select the Land Dispute."))
							return false;
					}
				}
			}
		}
		
		else
		{
			if (!objFV.validate("ddNonOperational", "B", "Please select the Non-Operational Reason."))
				return false;
		}

		
		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
		
		return true;
	});
});