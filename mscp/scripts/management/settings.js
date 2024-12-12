
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
	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");

		if (!objFV.validate("txtSiteTitle", "B", "Please enter the Site Title."))
			return false;

		if (!objFV.validate("txtCopyright", "B", "Please enter the Copyright."))
			return false;


		if (!objFV.validate("txtGeneralName", "B", "Please enter the Sender Name [General]."))
		{
			$("#PageTabs").tabs("option", "active", 1);

			return false;
		}

		if (!objFV.validate("txtGeneralEmail", "B,E", "Please enter a valid Sender Email Address [General]."))
		{
			$("#PageTabs").tabs("option", "active", 1);

			return false;
		}

		return true;
	});
});