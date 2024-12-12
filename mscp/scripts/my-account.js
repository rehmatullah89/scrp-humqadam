
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
	$("#AccountTabs").tabs( );
	$("#frmMyAccount button").button({ icons:{ primary:'ui-icon-disk' } });
	$("#frmMyPassword button").button({ icons:{ primary:'ui-icon-key' } });

	$("#txtMobile").mask("03nnnnnnnnn", { placeholder:"" });


	$("#frmMyAccount").submit(function( )
	{
		var objFV = new FormValidator("frmMyAccount", "AccountMsg");


		if (!objFV.validate("txtName", "B", "Please enter your Name."))
			return false;

		if (!objFV.validate("txtMobile", "B,N,L(11)", "Please enter a valid Mobile Number."))
			return false;

		if (!objFV.validate("txtEmail", "B,E", "Please enter a valid Email Address."))
			return false;


		$("#BtnSave").attr('disabled', true);

		$.post("ajax/save-settings.php",
			$("#frmMyAccount").serialize( ),

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");

				showMessage("#AccountMsg", sParams[0], sParams[1]);

				if (sParams[2] != $("#Theme").val( ))
					document.location.reload( );

				$("#BtnSave").attr('disabled', false);
			},

			"text");
	});



	$("#frmMyPassword").submit(function( )
	{
		var objFV = new FormValidator("frmMyPassword", "PasswordMsg");


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

		$.post("ajax/save-password.php",
			$("#frmMyPassword").serialize( ),

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");

				showMessage("#PasswordMsg", sParams[0], sParams[1]);

				if (sParams[0] == "success")
				{
					$("#txtNewPassword").val("");
					$("#txtConfirmPassword").val("");
				}

				$("#BtnPassword").attr('disabled', false);
			},

			"text");
	});
});