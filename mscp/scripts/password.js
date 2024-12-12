
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
					
					sLocation = sLocation.replace("password.php", "");
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


		if (!objFV.validate("txtNewPassword", "B", "Please enter a new password for your account."))
			return false;

		if (!objFV.validate("txtNewPassword", "P", "Please enter a valid password. The Password must meet the following criteria:<br /><br />- Password must be of atleast 6 Characters<br />- Password must contain 1 Lower Case Alphabet<br />- Password must contain 1 Upper Case Alphabet<br />- Password must contain 1 Digit<br />- Password must contain 1 Special Character"))
			return false;

		if (!objFV.validate("txtConfirmPassword", "B", "Please confirm your new account password."))
			return false;
			
		if (!objFV.validate("txtConfirmPassword", "P", "The Password does not MATCH with the Confirm Password."))
			return false;

		if (objFV.value("txtNewPassword") != objFV.value("txtConfirmPassword"))
		{
			objFV.focus("txtConfirmPassword");
			objFV.select("txtConfirmPassword");
			
			showMessage("#PasswordMsg", "alert", "The New Password does not MATCH with the Confirm Password");

			return false;
		}


		$("#BtnPassword").attr('disabled', true);
		
		$.post("ajax/reset-password.php", 
			$("#frmPassword").serialize( ),

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");
				
				showMessage("#PasswordMsg", sParams[0], sParams[1]);
				
				
				if (sParams[0] == "success")
				{
					$("#frmPassword :input").attr('disabled', true);
					
					setTimeout(function( ) { $("#Tabs").tabs('select', 1); }, 5000);
				}

				else
					$("#BtnPassword").attr('disabled', false);
			},

			"text");
	});
});