
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
	$("#Tabs").tabs( );
	$("#frmLogin button").button({ icons:{ primary:'ui-icon-locked' } });
	$("#frmPassword button").button({ icons:{ primary:'ui-icon-key' } });
	
	
	$("#frmLogin").submit(function( )
	{
		var objFV = new FormValidator("frmLogin", "LoginMsg");
		

		if (!objFV.validate("txtEmail", "B,E", "Please enter your Login Email Address."))
			return false;

		if (!objFV.validate("txtPassword", "B,L(3)", "Please enter the valid Password."))
			return false;


		$("#BtnLogin").attr('disabled', true);

		$.post("ajax/login.php", 
			$("#frmLogin").serialize( ),

			function (sResponse)
			{			       
				var sParams = sResponse.split("|-|");

				showMessage("#LoginMsg", sParams[0], sParams[1]);

				if (sParams[0] == "success")
				{
					$("#frmLogin :input").attr('disabled', true);
					
					var sLocation = new String(document.location);
					
					sLocation = sLocation.replace("index.php", "");
					sLocation = (sLocation + "dashboard.php");
				
					document.location = sLocation;
				}
					
				else
					$("#BtnLogin").attr('disabled', false);
			},

			"text");
	});
	
	
	
	$("#frmPassword").submit(function( )
	{
		var objFV = new FormValidator("frmPassword", "PasswordMsg");

		if (!objFV.validate("txtLoginEmail", "B,E", "Please enter your Login Email Address."))
			return false;


		$("#BtnPassword").attr('disabled', true);
		
		$.post("ajax/password.php", 
			{ txtEmail:$("#txtLoginEmail").val( ) },

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");
				
				showMessage("#PasswordMsg", sParams[0], sParams[1]);
				
				
				if (sParams[0] == "success")
					$("#frmPassword :input").attr('disabled', true);

				else
					$("#BtnPassword").attr('disabled', false);
			},

			"text");
	});
});