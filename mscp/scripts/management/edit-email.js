
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
	$("#txtMessage").ckeditor({ height:(($(window).height( ) - 350) + "px") }, function( ) { CKFinder.setupCKEditor(this, ($("base").attr("href") + "plugins/ckfinder/")); });
	$("#Variables").css("height", (($(window).height( ) - 230) + "px"));

	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtSubject", "B", "Please enter the Email Subject."))
			return false;

		if ($("#txtMessage").val() == "")
		{
			showMessage("#RecordMsg", "alert", "Please enter the Email Message.");

			return false;
		}

		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});