
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
	$("#frmRecord #txtReason").blur(function( )
	{
		if ($("#frmRecord #txtReason").val( ) == "")
			return;

		$.post("ajax/settings/check-reason.php",
			{ Reason:$("#frmRecord #txtReason").val( ), ReasonId:$("#ReasonId").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The specified Reason already exists in the System.");

					$("#DuplicateReason").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateReason").val("0");
				}
			},

			"text");
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");

		if (!objFV.validate("txtReason", "B", "Please enter the Reason."))
			return false;

		if (objFV.value("DuplicateReason") == "1")
		{
			showMessage("#RecordMsg", "info", "The specified Reason already exists in the System.");

			objFV.focus("txtReason");
			objFV.select("txtReason");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});
});