
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
	$("#txtDetails").ckeditor({ height:"300px" }, function( ) { CKFinder.setupCKEditor(this, ($("base").attr("href") + "plugins/ckfinder/")); });


	$("#txtDate").datepicker({ showOn          : "both",
	                           buttonImage     : "images/icons/calendar.gif",
	                           buttonImageOnly : true,
	                           dateFormat      : "yy-mm-dd"
	                        });


	$("#txtTitle").blur(function( )
	{
		var sUrl = $("#txtTitle").val( );

		$("#txtSefUrl").val(sUrl.getSefUrl(".html"));

	});


	$("#txtTitle, #txtSefUrl").blur(function( )
	{
		var sUrl = $("#txtSefUrl").val( );

		if (sUrl == "")
			return;


		sUrl = sUrl.getSefUrl(".html");

		$("#txtSefUrl").val(sUrl);


		$.post("ajax/modules/check-news.php",
			{ NewsId:$("#NewsId").val( ), SefUrl:sUrl },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The News SEF URL is already used. Please specify another URL.");

					$("#DuplicateNews").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateNews").val("0");
				}
			},

			"text");
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtTitle", "B", "Please enter the News Title."))
			return false;

		if (!objFV.validate("txtSefUrl", "B", "Please enter the SEF URL."))
			return false;

		if ($("#txtDetails").val() == "")
		{
			showMessage("#RecordMsg", "alert", "Please enter the News Details.");

			return false;
		}

		if (objFV.value("filePicture") != "")
		{
			if (!checkImage(objFV.value("filePicture")))
			{
				showMessage("#RecordMsg", "alert", "Invalid File Format. Please select an image file of type jpg, gif or png.");

				objFV.focus("filePicture");
				objFV.select("filePicture");

				return false;
			}
		}

		if (!objFV.validate("txtDate", "B", "Please enter the News Date."))
			return false;

		if (objFV.value("DuplicateNews") == "1")
		{
			showMessage("#RecordMsg", "info", "The News SEF URL is already used. Please specify another URL.");

			objFV.focus("txtSefUrl");
			objFV.select("txtSefUrl");

			return false;
		}

		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});