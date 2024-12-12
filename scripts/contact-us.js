
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
	$("#frmContact").submit(function( )
	{
		var objFV = new FormValidator("frmContact", "ErrorMsg");


		if (!objFV.validate("txtName", "B", "Please enter your Name."))
			return false;

		if (!objFV.validate("txtEmail", "B,E", "Please enter your valid Email Address."))
			return false;

		if (!objFV.validate("txtSubject", "B", "Please enter the Message Subject."))
			return false;

		if (!objFV.validate("txtMessage", "B", "Please enter your Message."))
			return false;

		if (!objFV.validate("txtSpamCode", "B,L(5)", "Please enter the valid Code as shown."))
			return false;


		$("#BtnSubmit").attr('disabled', true);
		$("#ErrorMsg").hide( );

		$.post("ajax/send-mail.php", 
			$("#frmContact").serialize( ),

			function (sResponse)
			{			       
				var sParams = sResponse.split("|-|");

				showMessage("#ErrorMsg", sParams[0], sParams[1]);
				
				if (sParams[0] == "success")
				{
					$('#frmContact')[0].reset( );
					$("#Captcha").attr("src", ($("#Captcha").attr("src") + "?" + Math.random( )));
				}

				$("#BtnSubmit").attr('disabled', false);
			},

			"text");
	});
	
	
	$("#BtnClear").click(function( )
	{
		$("#frmContact")[0].reset( );
		$("#frmContact #txtName").focus( );
		$("#ErrorMsg").hide( );
		$("#BtnSubmit").attr('disabled', false);
	});
});