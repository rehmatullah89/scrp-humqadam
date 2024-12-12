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


	if ($_SESSION["AdminId"] == "")
	{
		print "alert|-|Please login first to update your account settings.";
		exit( );
	}

	$sName    = IO::strValue('txtName');
	$sMobile  = IO::strValue('txtMobile');
	$sEmail   = IO::strValue('txtEmail');
	$iRecords = IO::intValue('ddRecords');
	$sTheme   = IO::strValue('ddTheme');

	if ($sName == "" || $sMobile == "" || $sEmail == "" || $iRecords == 0 || $sTheme == "")
	{
		print "alert|-|Invalid request to update account settings.";
		exit( );
	}


	$sSQL = "SELECT * FROM tbl_admins WHERE id!='{$_SESSION["AdminId"]}' AND email='$sEmail'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) > 0)
	{
		print "info|-|The provided email address is already in use by another user. Please enter a new email address.";
		exit( );
	}


	$sSQL = "UPDATE tbl_admins SET name='$sName', mobile='$sMobile', email='$sEmail', records='$iRecords', theme='$sTheme' WHERE id='{$_SESSION["AdminId"]}'";

	if ($objDb->execute($sSQL) == true)
	{
		$_SESSION["AdminName"]    = $sName;
		$_SESSION["AdminEmail"]   = $sEmail;
		$_SESSION["PageRecords"] = $iRecords;
		$_SESSION["CmsTheme"]     = $sTheme;


		print "success|-|You account settings have been updated successfully.|-|{$sTheme}";
	}

	else
		print "error|-|An ERROR occured while processing your request, please try again.|-|{$_SESSION['CmsTheme']}";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>