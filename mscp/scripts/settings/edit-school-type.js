
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
	$("#frmRecord #txtType").blur(function( )
	{
		if ($("#frmRecord #txtType").val( ) == "")
			return;

		$.post("ajax/settings/check-school-type.php",
			{ Type:$("#frmRecord #txtType").val( ), TypeId:$("#TypeId").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The specified School Type already exists in the System.");

					$("#DuplicateType").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateType").val("0");
				}
			},

			"text");
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");

		if (!objFV.validate("txtType", "B", "Please enter the School Type."))
			return false;

		if (objFV.value("DuplicateType") == "1")
		{
			showMessage("#RecordMsg", "info", "The specified School Type already exists in the System.");

			objFV.focus("txtType");
			objFV.select("txtType");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});
});