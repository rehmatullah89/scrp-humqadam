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

	$_SESSION["Flag"] = "";

	$sName        = IO::strValue("txtName");
	$iProvince    = IO::intValue("ddProvince");
	$sSefUrl      = IO::strValue("Url");
	$sCoordinates = IO::strValue("txtCoordinates");
	$sDescription = IO::strValue("txtDescription");
	$sLatitude    = IO::strValue("txtLatitude");
	$sLongitude   = IO::strValue("txtLongitude");
	$sStatus      = IO::strValue("ddStatus");
	$sPicture     = "";
	$bError       = true;


	if ($sName == "" || $iProvince == 0 || $sSefUrl == "" || $sLatitude == "" || $sLongitude == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_districts WHERE sef_url LIKE '$sSefUrl'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "DISTRICT_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$iDistrict = getNextId("tbl_districts");


		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iDistrict."-".IO::getFileName($_FILES['filePicture']['name']));

			if (!@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.DISTRICTS_IMG_DIR.$sPicture)))
				$sPicture = "";
		}


		$sSQL = "INSERT INTO tbl_districts SET id          = '$iDistrict',
		                                       province_id = '$iProvince',
		                                       name        = '$sName',
		                                       sef_url     = '$sSefUrl',
											   latitude    = '$sLatitude',
											   longitude   = '$sLongitude',
		                                       coordinates = '$sCoordinates',
		                                       description = '$sDescription',
		                                       picture     = '$sPicture',
		                                       title_tag   = '{$_SESSION["SiteTitle"]} | $sName',
		                                       position    = '$iDistrict',
		                                       status      = '$sStatus'";

		if ($objDb->execute($sSQL) == true)
			redirect("districts.php", "DISTRICT_ADDED");

		else
		{
			$_SESSION["Flag"] = "DB_ERROR";

			if ($sPicture != "")
				@unlink($sRootDir.DISTRICTS_IMG_DIR.$sPicture);
		}
	}
?>