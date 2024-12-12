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
	$sPicture        = IO::strValue("Picture");
	$sInspectionCode = IO::strValue("InspectionCode");


	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $sInspectionCode == "" || $sPicture == "")
		$aResponse["Message"] = "Invalid Request";

	else
	{
		$sSQL = "SELECT id, name, email, status FROM tbl_admins WHERE MD5(id)='$sUser'";
		$objDb->query($sSQL);

		if ($objDb->getCount( ) == 0)
			$aResponse["Message"] = "Invalid User";

		else if ($objDb->getField(0, "status") != "A")
			$aResponse["Message"] = "User Account is Disabled";

		else
		{
			$iUser  = $objDb->getField(0, "id");
			$sName  = $objDb->getField(0, "name");
			$sEmail = $objDb->getField(0, "email");


			$sInspectionTime = date("Y-m-d H:i:s", ($sInspectionCode / 1000));


			$sSQL = "SELECT id, picture, file FROM tbl_inspections WHERE admin_id='$iUser' AND created_at='$sInspectionTime'";
			$objDb->query($sSQL);

			$iInspection = $objDb->getField(0, "id");
			$sOldPicture = $objDb->getField(0, "picture");
			$sOldFile    = $objDb->getField(0, "file");


			if ($iInspection == 0)
				$aResponse["Message"] = "Invalid request, no Inspection Record Found!";

			else
			{
				$sImagesList = array(".jpg", ".jpeg", ".png", ".gif");
				$iPosition   = @strrpos($sPicture, '.');
				$sExtension  = @substr($sPicture, $iPosition);


				if ($sOldPicture == "" && @in_array($sExtension, $sImagesList) && @copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.INSPECTIONS_IMG_DIR."{$iInspection}-{$sPicture}")) )
				{
					$sSQL = "UPDATE tbl_inspections SET picture='{$iInspection}-{$sPicture}' WHERE id='$iInspection'";

					if ($objDb->execute($sSQL, true, $iUser, $sName, $sEmail) == true)
					{
						$aResponse['Status']  = "OK";
						$aResponse["Message"] = "Document saved successfully!";
					}

					else
						$aResponse["Message"] = "An ERROR occured, please try again.";


					if (@file_exists($sRootDir.TEMP_DIR.$sPicture))
						@unlink($sRootDir.TEMP_DIR.$sPicture);
				}

				else
					$aResponse["Message"] = "Unable to copy the File.";



				if ($aResponse["Message"] == "" && @file_exists($sRootDir.TEMP_DIR.$sPicture))
				{
					if ($sOldFile == "" && @copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.INSPECTIONS_DOC_DIR."{$iInspection}-{$sPicture}")) )
					{
						$sSQL = "UPDATE tbl_inspections SET file='{$iInspection}-{$sPicture}' WHERE id='$iInspection'";

						if ($objDb->execute($sSQL, true, $iUser, $sName, $sEmail) == true)
						{
							$aResponse['Status']  = "OK";
							$aResponse["Message"] = "Document saved successfully!";
						}

						else
							$aResponse["Message"] = "An ERROR occured, please try again.";


						if (@file_exists($sRootDir.TEMP_DIR.$sPicture))
							@unlink($sRootDir.TEMP_DIR.$sPicture);
					}

					else
						$aResponse["Message"] = "Unable to copy the File.";
				}
			}
		}
	}

	print @json_encode($aResponse);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>