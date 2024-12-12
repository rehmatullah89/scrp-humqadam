
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
	$("#txtTitle").change(function( )
	{
		if ($("#txtSefUrl").attr("readonly"))
			return;


		var sUrl = $("#txtTitle").val( );

		$("#txtSefUrl").val(sUrl.getSefUrl(".html"));
	});


	$("#txtTitle, #txtSefUrl").blur(function( )
	{
		if ($("#txtSefUrl").attr("readonly"))
			return;


		var sUrl = $("#txtSefUrl").val( );

		if (sUrl == "")
			return;


		sUrl = sUrl.getSefUrl(".html");

		$("#txtSefUrl").val(sUrl);


		$.post("ajax/contents/check-web-page.php",
			{ PageId:$("#PageId").val( ), SefUrl:sUrl },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The SEF URL is already used. Please specify another URL.");

					$("#DuplicatePage").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicatePage").val("0");
				}
			},

			"text");
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtTitle", "B", "Please enter the Page Name."))
			return false;

		if ($("#PageId").val( ) != "1")
		{
			if (!objFV.validate("txtSefUrl", "B", "Please enter the SEF URL."))
				return false;
		}

		if (objFV.value("DuplicatePage") == "1")
		{
			showMessage("#RecordMsg", "info", "The SEF URL is already used. Please specify another URL.");

			objFV.focus("txtSefUrl");
			objFV.select("txtSefUrl");

			return false;
		}

		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});