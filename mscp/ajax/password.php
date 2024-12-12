<?
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

	header("Expires: Tue, 01 Jan 2000 12:12:12 GMT");
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	if ($_SESSION["AdminId"] != "")
	{
		print "alert|-|You are already logged into your account.";
		exit( );
	}


	$sEmail = IO::strValue('txtEmail');

	if ($sEmail == "")
	{
		print "alert|-|Please provide your login email address.";
		exit( );
	}


	$sSQL = "SELECT id, name, password FROM tbl_admins WHERE email='$sEmail'";

	if ($objDb->query($sSQL) == true)
	{
		if ($objDb->getCount( ) == 1)
		{
			$iAdmin    = $objDb->getField(0, 'id');
			$sName     = $objDb->getField(0, 'name');
			$sPassword = $objDb->getField(0, 'password');


			$sSQL = "SELECT site_title, general_name, general_email FROM tbl_settings WHERE id='1'";
			$objDb->query($sSQL);

			$sSiteTitle   = $objDb->getField(0, "site_title");
			$sSenderName  = $objDb->getField(0, "general_name");
			$sSenderEmail = $objDb->getField(0, "general_email");


			$sSQL = "SELECT subject, message FROM tbl_email_templates WHERE id='11'";
			$objDb->query($sSQL);

			$sSubject = $objDb->getField(0, "subject");
			$sBody    = $objDb->getField(0, "message");


			$sCode = substr($sPassword, -10);
			$sUrl  = (SITE_URL.ADMIN_CP_DIR."password.php?aid={$iAdmin}&email={$sEmail}&code={$sCode}");


			$sSubject = @str_replace("{SITE_TITLE}", $sSiteTitle, $sSubject);

			$sBody    = @str_replace("{NAME}", $sName, $sBody);
			$sBody    = @str_replace("{EMAIL}", $sEmail, $sBody);
			$sBody    = @str_replace("{PASSWORD_RESET_URL}", $sUrl, $sBody);
			$sBody    = @str_replace("{SITE_TITLE}", $sSiteTitle, $sBody);
			$sBody    = @str_replace("{SITE_URL}", SITE_URL, $sBody);


			$objEmail = new PHPMailer( );

			$objEmail->Subject = $sSubject;
			$objEmail->MsgHTML($sBody);
			$objEmail->SetFrom($sSenderEmail, $sSenderName);
			$objEmail->AddAddress($sEmail, $sName);

			if (@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE && $objEmail->Send( ))
				print "success|-|Please check you email inbox and follow the instructions to reset your admin account password.";

			else
				print "error|-|Unable to send you an email, please try again.";
		}

		else
			print "info|-|Invalid login email address.";
	}

	else
		print "error|-|An ERROR occured while processing your request, please try again.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>