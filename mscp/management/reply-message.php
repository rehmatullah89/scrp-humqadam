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

	$_SESSION["Flag"] = "";

	$sSubject = IO::strValue("txtSubject");
	$sMessage = IO::strValue("txtMessage");

	if ($sSubject == "" || $sMessage == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";

	if ($_SESSION["Flag"] == "")
	{
		$iReplyId = getNextId("tbl_web_message_replies");


		$sSQL = "INSERT INTO tbl_web_message_replies (id, message_id, subject, message, date_time)
								              VALUES ('$iReplyId', '$iMessageId', '$sSubject', '$sMessage', NOW( ))";
		if ($objDb->execute($sSQL) == true)
		{
			$sSQL = "SELECT * FROM tbl_web_messages WHERE id='$iMessageId'";
			$objDb->query($sSQL);

			$sName  = $objDb->getField(0, "name");
			$sEmail = $objDb->getField(0, "email");


			$sSQL = "SELECT general_name, general_email FROM tbl_settings WHERE id='1'";
			$objDb->query($sSQL);

			$sSenderName  = $objDb->getField(0, "general_name");
			$sSenderEmail = $objDb->getField(0, "general_email");


			$objEmail = new PHPMailer( );

			$objEmail->Subject = $sSubject;

			$objEmail->MsgHTML(nl2br($sMessage));
			$objEmail->SetFrom($sSenderEmail, $sSenderName);
			$objEmail->AddAddress($sEmail, $sName);

			if (@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE)
				$objEmail->Send( );
?>
	<script type="text/javascript">
	<!--
		parent.$.colorbox.close( );
		parent.showMessage("#PageMsg", "success", "Your Reply to the selected Message has been Sent successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>