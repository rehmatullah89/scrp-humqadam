
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
	$("#txtTitle, #ddParent").blur(function( )
	{
		var sTitle = $("#txtTitle").val( );

		if (sTitle == "")
			return;


		$.post("ajax/settings/check-boq.php",
			{ BoqId:$("#BoqId").val( ), Title:sTitle },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The BOQ Title is already used. Please specify another Title.");

					$("#DuplicateBoq").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateBoq").val("0");
				}
			},

			"text");
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtTitle", "B", "Please enter the BOQ Title."))
			return false;

		if (!objFV.validate("ddUnit", "B", "Please select the BOQ Unit."))
			return false;


		if (objFV.value("DuplicateBoq") == "1")
		{
			showMessage("#RecordMsg", "info", "The BOQ Title is already used. Please specify another Title.");

			objFV.focus("txtTitle");
			objFV.select("txtTitle");

			return false;
		}

		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});