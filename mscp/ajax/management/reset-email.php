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

	@require_once("../../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	if ($sUserRights["Edit"] != "Y" || $sUserRights["Delete"] != "Y")
	{
		print "info|-|You don't have enough Rights to perform the requested operation.";

		exit( );
	}


	$iEmailId = IO::intValue("EmailId");

	if ($iEmailId > 0)
	{
		$sSQL = "UPDATE tbl_email_templates SET subject=default_subject, message=default_message WHERE id='$iEmailId'";

		if ($objDb->execute($sSQL) == true)
		{
			$sSubject = getDbValue("subject", "tbl_email_templates", "id='$iEmailId'");

			print "success|-|The selected Email Message has been Reset successfully.|-|{$sSubject}";
		}

		else
			print "error|-|An error occured while processing your request, please try again.";
	}

	else
		print "info|-|Inavlid Email Reset request.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>