
/*********************************************************************************************\
***********************************************************************************************
**                                                                                           **
**  SCRP - School Construction and Rehabilitation Programme                                  **
**  Version 1.0                                                                              **
**                                                                                           **
**  http://www.3-tree.com/imc/                                                               **
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
	$("#txtName").blur(function( )
	{
		if ($(this).val( ) == "")
			return;


		$.post("ajax/modules/check-faq-category.php",
			{ CategoryId:$("#CategoryId").val( ), Name:$(this).val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The specified Category Name is already used. Please specify another Name.");

					$("#DuplicateCategory").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateCategory").val("0");
				}
			},

			"text");
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtName", "B", "Please enter the Category Name."))
			return false;

		if (objFV.value("DuplicateCategory") == "1")
		{
			showMessage("#RecordMsg", "info", "The specified Category Name is already used. Please specify another Name.");

			objFV.focus("txtName");
			objFV.select("txtName");

			return false;
		}

		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});