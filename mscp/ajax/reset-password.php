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


	$iAdminId  = IO::intValue('AdminId');
	$sEmail    = IO::strValue('Email');
	$sCode     = IO::strValue('Code');
	$sPassword = IO::strValue('txtNewPassword');

	if ($iAdminId == 0 || $sEmail == "" || $sCode == "" || $sPassword == "")
	{
		print "alert|-|Invalid password reset request.";
		exit( );
	}


	$sSQL = "SELECT name FROM tbl_admins WHERE id='$iAdminId' AND email='$sEmail' AND RIGHT(password, 10)='$sCode'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) == 0)
	{
		print "error|-|Invalid password reset request. No user found.";
		exit( );
	}


	$sSQL = "UPDATE tbl_admins SET password=PASSWORD('$sPassword') WHERE id='$iAdminId'";

	if ($objDb->execute($sSQL) == true)
		print "success|-|You account password has been changed successfully. Please use this password to login into your account.";

	else
		print "error|-|An ERROR occured while processing your request, please try again.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>