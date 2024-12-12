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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	$sUser     = IO::strValue("User");
	$sDeviceId = IO::strValue("DeviceId");
	$iVersion  = IO::intValue("Version");


	$aResponse            = array( );
	$aResponse["Status"]  = "ERROR";

	if ($sUser == "" || $sDeviceId == "")
		$aResponse["Message"] = "Invalid Device Registration Request";

	else
	{
		$sSQL = "SELECT id, name FROM tbl_admins WHERE MD5(id)='$sUser'";
		$objDb->query($sSQL);

		if ($objDb->getCount( ) == 0)
			$aResponse["Message"] = "Invalid User";

		else
		{
			$iUser = $objDb->getField(0, "id");
			$sName = $objDb->getField(0, "name");

			
			$sAppDateTime = "";
			$iLastVersion = getDbValue("app_version", "tbl_admins", "id='$iUser'");
			
			if ($iLastVersion != $iVersion)
				$sAppDateTime = ", app_updated=NOW( ) ";
			
			

			$sSQL = "UPDATE tbl_admins SET device_id='$sDeviceId', app_version='$iVersion' $sAppDateTime WHERE id='$iUser'";
			$objDb->execute($sSQL);


			$aResponse["Status"]  = "OK";
			$aResponse["Message"] = "Device Registered successfully for Push Notifications.";
		}
	}


	print @json_encode($aResponse);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>