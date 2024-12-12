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


	$sUser           = IO::strValue("User");
	$iSchool         = IO::intValue("School");
	$iStage          = IO::intValue("Stage");
	$sPicture        = IO::strValue("Picture");
	$sDocument       = IO::strValue("Document");
	$sInspectionCode = IO::strValue("InspectionCode");

	
	logApiCall($_POST);
	

	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $sInspectionCode == "" || ($sPicture == "" && $sDocument == ""))
		$aResponse["Message"] = "Invalid Request";

	else
	{
		$sSQL = "SELECT id, name, email, provinces, districts, schools, status FROM tbl_admins WHERE MD5(id)='$sUser'";
		$objDb->query($sSQL);

		if ($objDb->getCount( ) == 0)
			$aResponse["Message"] = "Invalid User";

		else if ($objDb->getField(0, "status") != "A")
			$aResponse["Message"] = "User Account is Disabled";

		else
		{
			$iUser      = $objDb->getField(0, "id");
			$sName      = $objDb->getField(0, "name");
			$sEmail     = $objDb->getField(0, "email");
			$sProvinces = $objDb->getField(0, "provinces");
			$sDistricts = $objDb->getField(0, "districts");
			$sSchools   = $objDb->getField(0, "schools");

			$iProvinces = @explode(",", $sProvinces);
			$iDistricts = @explode(",", $sDistricts);
			$iSchools   = @explode(",", $sSchools);


			$sInspectionTime = date("Y-m-d H:i:s", ($sInspectionCode / 1000));
			$iInspection     = getDbValue("id", "tbl_inspections", "admin_id='$iUser' AND created_at='$sInspectionTime'");


			if ($iSchool > 0)
			{
				$sSQL = "SELECT district_id, province_id FROM tbl_schools WHERE id='$iSchool'";
				$objDb->query($sSQL);

				$iDistrict = $objDb->getField(0, "district_id");
				$iProvince = $objDb->getField(0, "province_id");
			}


			if ($iSchool > 0 && $objDb->getCount( ) == 0)
				$aResponse["Message"] = "Invalid Request, no School Found!";

			else if ($iSchool > 0 && (($sSchools != "" && !@in_array($iSchool, $iSchools)) || ($sSchools == "" && (!@in_array($iProvince, $iProvinces) || !@in_array($iDistrict, $iDistricts)))) )
				$aResponse["Message"] = "Request denied, You don't have permissions for requested School!";

			else if ($iInspection == 0 || getDbValue("COUNT(1)", "tbl_inspections", "id='$iInspection'") == 0)
				$aResponse["Message"] = "Invalid request, no Inspection Record Found!";

			else
			{
				$iFile = getNextId("tbl_inspection_documents");

				if ( ($sDocument != "" && @copy(($sRootDir.TEMP_DIR.$sDocument), ($sRootDir.INSPECTIONS_DOC_DIR."{$iInspection}-{$iFile}-{$sDocument}"))) ||
					 ($sPicture != "" && @copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.INSPECTIONS_IMG_DIR."{$iInspection}-{$iFile}-{$sPicture}"))) )
				{
					$sFile = ("{$iInspection}-{$iFile}-".(($sDocument != "") ? $sDocument : $sPicture));


					$sSQL = "INSERT INTO tbl_inspection_documents SET id            = '$iFile',
																	  inspection_id = '$iInspection',
																	  file          = '$sFile'";

					if ($objDb->execute($sSQL, true, $iUser, $sName, $sEmail) == true)
					{
						$aResponse['Status']  = "OK";
						$aResponse["Message"] = "Document saved successfully!";
					}

					else
						$aResponse["Message"] = "An ERROR occured, please try again.";



					if (@file_exists($sRootDir.TEMP_DIR.$sPicture))
						@unlink($sRootDir.TEMP_DIR.$sPicture);

					if (@file_exists($sRootDir.TEMP_DIR.$sDocument))
						@unlink($sRootDir.TEMP_DIR.$sDocument);
				}

				else
					$aResponse["Message"] = "Unable to copy the File.";
			}
		}
	}

	print @json_encode($aResponse);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>