
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
	$("#txtName, #ddParent, #ddType").blur(function( )
	{
		var sName = $("#txtName").val( );
		var sType = $("#ddType").val( );

		if (sName == "" || sType == "")
			return;


		$.post("ajax/settings/check-stage.php",
			{ StageId:$("#StageId").val( ), ParentId:$("#ddParent").val( ), Name:sName, Type:sType },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The Stage Name is already used. Please specify another Name.");

					$("#DuplicateStage").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateStage").val("0");
				}
			},

			"text");
	});


	$("#ddNature").change(function( )
	{
		if ($(this).val( ) == "S")
			$("#Parent").show( );

		else
			$("#Parent").hide( );
	});
	
	
	$("#ddType").change(function( )
	{
		if ($("#ddType").val( ) == "")
		{
			$("#ddParent").html("");
			
			return false;
		}
		
		
		$.post("ajax/settings/get-parent-stages.php",
			{ Type:$("#ddType").val( ) },

			function (sResponse)
			{
				$("#ddParent").html(sResponse);
			},

			"text");
	});		


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("ddType", "B", "Please enter the School Type."))
			return false;
		
		if (!objFV.validate("ddNature", "B", "Please enter the Stage Nature."))
			return false;

		if (objFV.value("ddNature") == "S")
		{
			if (!objFV.validate("ddParent", "B", "Please select the Parent Stage."))
				return false;
		}

		if (!objFV.validate("txtName", "B", "Please enter the Stage Name."))
			return false;

		if (!objFV.validate("txtWeightage", "F", "Please enter the valid Stage Weightage."))
			return false;


		if (objFV.value("DuplicateStage") == "1")
		{
			showMessage("#RecordMsg", "info", "The Stage Name is already used. Please specify another Name.");

			objFV.focus("txtName");
			objFV.select("txtName");

			return false;
		}

		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});