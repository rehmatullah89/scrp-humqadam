
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
	$("#txtQuestion, #ddSection, #ddSchool, #ddStage").blur(function( )
	{
		if ($("#txtQuestion").val( ) == "" || $("#ddSection").val( ) == "")
			return;


		$.post("ajax/surveys/check-question.php",
			{ QuestionId:$("#QuestionId").val( ), Title:$("#txtQuestion").val( ), Section:$("#ddSection").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "This Survey Section/Question is already entered. Please specify another Question.");

					$("#DuplicateQuestion").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateQuestion").val("0");
				}
			},

			"text");
	});



	$("#ddType").change(function( )
	{
		if ($(this).val( ) == "MS" || $(this).val( ) == "SS")
		{
			if ($("#Options").css("display") != "block")
				$("#Options").show("blind");
		}

		else
		{
			if ($("#Options").css("display") == "block")
				$("#Options").hide("blind");

			$("#txtOptions").val("");
		}
		
		
		if ($(this).val( ) == "SL")
		{
			if ($("#InputType").css("display") != "block")
				$("#InputType").show("blind");
		}

		else
		{
			if ($("#InputType").css("display") == "block")
				$("#InputType").hide("blind");

			$("#ddInputType").val("T");
		}		
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("ddSection", "B", "Please select the Survey Section."))
			return false;

		if (!objFV.validate("ddType", "B", "Please select the Question Type."))
			return false;
		
		if (!objFV.validate("txtQuestion", "B", "Please enter the Question."))
			return false;

		if (objFV.value("ddType") == "MS" || objFV.value("ddType") == "SS")
		{
			if (!objFV.validate("txtOptions", "B", "Please enter the Question Options (One per Line)."))
				return false;			
		}
		
		if (!objFV.validate("txtLink", "N", "Please enter the Linked Question ID."))
			return false;

		if (objFV.value("txtLink") != "")
		{
			if (!objFV.validate("ddLink", "B", "Please select the Linked Question Value."))
				return false;
		}


		if (!objFV.validate("txtPosition", "B,N", "Please enter the Question Position."))
			return false;

		if (objFV.value("DuplicateQuestion") == "1")
		{
			showMessage("#RecordMsg", "info", "This Survey Section/Question is already entered. Please specify another Question.");

			objFV.focus("txtQuestion");
			objFV.select("txtQuestion");

			return false;
		}

		
		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
		
		return true;
	});
});