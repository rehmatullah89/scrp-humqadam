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

	$sName      = IO::strValue("txtName");
	$sMobile    = IO::strValue("txtMobile");
	$sEmail     = IO::strValue("txtEmail");
	$sPassword  = IO::strValue("txtPassword");
	$sProvinces = @implode(",", IO::getArray("cbProvinces", "int"));
	$sDistricts = @implode(",", IO::getArray("cbDistricts", "int"));
	$sSchools   = IO::strValue("txtSchools");
	$iType      = IO::intValue("ddType");
	$iRecords   = IO::intValue("ddRecords");
	$sTheme     = IO::strValue("ddTheme");
	$sStatus    = IO::strValue("ddStatus");
	$sPicture   = "";
	$bError     = true;


	if ($sDistricts == "" && $sSchools != "")
		$sDistricts = getDbValue("GROUP_CONCAT(DISTINCT(district_id) SEPARATOR ',')", "tbl_schools", "FIND_IN_SET(id, '$sSchools')");


	if ($sName == "" || $sMobile == "" || $sEmail == "" || $sPassword == "" || $iType == 0 || $iRecords == 0 || $sTheme == "" || $sStatus == "" || $sProvinces == "" || $sDistricts == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_admins WHERE email='$sEmail'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "USER_EMAIL_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$iUser = getNextId("tbl_admins");

		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iUser."-".IO::getFileName($_FILES['filePicture']['name']));

			if (@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.ADMINS_IMG_DIR.'originals/'.$sPicture)))
				createImage(($sRootDir.ADMINS_IMG_DIR.'originals/'.$sPicture), ($sRootDir.ADMINS_IMG_DIR.'thumbs/'.$sPicture), ADMINS_IMG_WIDTH, ADMINS_IMG_HEIGHT);

			if (!@file_exists($sRootDir.ADMINS_IMG_DIR.'originals/'.$sPicture))
				$sPicture = "";
		}



		$objDb->execute("BEGIN");

		$sSQL = "INSERT INTO tbl_admins SET id        = '$iUser',
										    type_id   = '$iType',
										    name      = '$sName',
										    mobile    = '$sMobile',
										    email     = '$sEmail',
										    password  = PASSWORD('$sPassword'),
										    picture   = '$sPicture',
										    provinces = '$sProvinces',
										    districts = '$sDistricts',
										    schools   = '$sSchools',
										    records   = '$iRecords',
										    theme     = '$sTheme',
										    status    = '$sStatus',
										    date_time = NOW( )";
		$bFlag = $objDb->execute($sSQL);


		if ($bFlag == true)
		{
			$sSQL = "SELECT * FROM tbl_admin_type_rights WHERE type_id='$iType'";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($i = 0; $i < $iCount; $i ++)
			{
				$iPageId = $objDb->getField($i, "page_id");
				$sView   = $objDb->getField($i, "view");
				$sAdd    = $objDb->getField($i, "add");
				$sEdit   = $objDb->getField($i, "edit");
				$sDelete = $objDb->getField($i, "delete");


				$sSQL = "INSERT INTO tbl_admin_rights SET admin_id = '$iUser',
														  page_id  = '$iPageId',
														  `view`   = '$sView',
														  `add`    = '$sAdd',
														  `edit`   = '$sEdit',
														  `delete` = '$sDelete'";
				$bFlag = $objDb2->execute($sSQL);

				if ($bFlag == false)
					break;
			}
		}


		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			redirect("users.php", "USER_ADDED");
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";

			if ($sPicture != "")
			{
				@unlink($sRootDir.ADMINS_IMG_DIR.'thumbs/'.$sPicture);
				@unlink($sRootDir.ADMINS_IMG_DIR.'originals/'.$sPicture);
			}
		}
	}
?>