<?
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

	header("Expires: Tue, 01 Jan 2000 12:12:12 GMT");
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	$sName     = IO::strValue('txtName', true);
	$sEmail    = IO::strValue('txtEmail');
	$sPhone    = IO::strValue('txtPhone', true);
	$sSubject  = IO::strValue('txtSubject', true);
	$sMessage  = IO::strValue('txtMessage', true);
	$sSpamCode = IO::strValue('txtSpamCode');

	if ($sName == "" || $sEmail == "" || $sSubject == "" || $sMessage == "" || $sSpamCode == "")
	{
		print "alert|-|Please provide all required fields to send your message.";
		exit( );
	}

	if (@md5(strtolower($sSpamCode)) != $_SESSION['Md5SpamCode'])
	{
		print "alert|-|Please provide exact Spam Protection Code as shown in image.";
		exit( );
	}


	$iMessage = getNextId("tbl_web_messages");

	$sSQL = "INSERT INTO tbl_web_messages SET id          = '$iMessage',
	                                          customer_id = '{$_SESSION['CustomerId']}',
	                                          name        = '$sName',
	                                          email       = '$sEmail',
	                                          phone       = '$sPhone',
	                                          subject     = '$sSubject',
	                                          message     = '$sMessage',
	                                          ip_address  = '{$_SERVER['REMOTE_ADDR']}',
	                                          date_time   = NOW( )";

	if ($objDb->execute($sSQL) == true)
	{
		$sSQL = "SELECT site_title, general_name, general_email, date_format, time_format FROM tbl_settings WHERE id='1'";
		$objDb->query($sSQL);

		$sSiteTitle      = $objDb->getField(0, "site_title");
		$sRecipientName  = $objDb->getField(0, "general_name");
		$sRecipientEmail = $objDb->getField(0, "general_email");
		$sDateFormat     = $objDb->getField(0, "date_format");
		$sTimeFormat     = $objDb->getField(0, "time_format");


		$sUserSubject = $sSubject;


		// Admin Email
		$sSQL = "SELECT subject, message, status FROM tbl_email_templates WHERE id='2'";
		$objDb->query($sSQL);

		$sSubject = $objDb->getField(0, "subject");
		$sBody    = $objDb->getField(0, "message");
		$sActive  = $objDb->getField(0, "status");


		if ($sActive == "A")
		{
			$sSubject = @str_replace("{SITE_TITLE}", $sSiteTitle, $sSubject);

			$sBody    = @str_replace("{NAME}", $sName, $sBody);
			$sBody    = @str_replace("{EMAIL}", $sEmail, $sBody);
			$sBody    = @str_replace("{PHONE}", $sPhone, $sBody);
			$sBody    = @str_replace("{SUBJECT}", $sUserSubject, $sBody);
			$sBody    = @str_replace("{MESSAGE}", nl2br($sMessage), $sBody);
			$sBody    = @str_replace("{DATE_TIME}", date("{$sDateFormat} {$sTimeFormat}"), $sBody);
			$sBody    = @str_replace("{SITE_TITLE}", $sSiteTitle, $sBody);
			$sBody    = @str_replace("{SITE_URL}", SITE_URL, $sBody);


			$objEmail = new PHPMailer( );

			$objEmail->Subject = $sSubject;
			$objEmail->MsgHTML($sBody);
			$objEmail->SetFrom($sEmail, $sName);
			$objEmail->AddAddress($sRecipientEmail, $sRecipientName);

			if (@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE)
				$objEmail->Send( );
		}



		// Reply
		$sSQL = "SELECT subject, message, status FROM tbl_email_templates WHERE id='3'";
		$objDb->query($sSQL);

		$sSubject = $objDb->getField(0, "subject");
		$sBody    = $objDb->getField(0, "message");
		$sActive  = $objDb->getField(0, "status");


		if ($sActive == "A")
		{
			$sSubject = @str_replace("{SITE_TITLE}", $sSiteTitle, $sSubject);

			$sBody    = @str_replace("{NAME}", $sName, $sBody);
			$sBody    = @str_replace("{EMAIL}", $sEmail, $sBody);
			$sBody    = @str_replace("{PHONE}", $sPhone, $sBody);
			$sBody    = @str_replace("{SUBJECT}", $sUserSubject, $sBody);
			$sBody    = @str_replace("{MESSAGE}", nl2br($sMessage), $sBody);
			$sBody    = @str_replace("{DATE_TIME}", date("{$sDateFormat} {$sTimeFormat}"), $sBody);
			$sBody    = @str_replace("{SITE_TITLE}", $sSiteTitle, $sBody);
			$sBody    = @str_replace("{SITE_URL}", SITE_URL, $sBody);


			$objEmail = new PHPMailer( );

			$objEmail->Subject = $sSubject;
			$objEmail->MsgHTML($sBody);
			$objEmail->SetFrom($sRecipientEmail, $sRecipientName);
			$objEmail->AddAddress($sEmail, $sName);

			if (@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE)
				$objEmail->Send( );
		}


		print "success|-|Your Message has been sent successfully.";
	}

	else
		print "error|-|An ERROR occured while processing your request, please try again.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>