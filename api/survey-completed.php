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


	$sUser      = IO::strValue("User");
	$iSchool    = IO::intValue("School");
	$sCode      = IO::strValue("Code");
	$sLatitude  = IO::strValue("Latitude");
	$sLongitude = IO::strValue("Longitude");	
	$sDateTime  = IO::strValue("DateTime");

	
	logApiCall($_POST);
	

	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $iSchool == 0 || $sDateTime == "")
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



			$sSQL = "SELECT district_id, province_id FROM tbl_schools WHERE id='$iSchool'";
			$objDb->query($sSQL);

			$iDistrict = $objDb->getField(0, "district_id");
			$iProvince = $objDb->getField(0, "province_id");


			if ($objDb->getCount( ) == 0)
				$aResponse["Message"] = "Invalid Request, no School Found!";

			else if ( ($sSchools != "" && !@in_array($iSchool, $iSchools)) || ($sSchools == "" && (!@in_array($iProvince, $iProvinces) || !@in_array($iDistrict, $iDistricts))) )
				$aResponse["Message"] = "Request denied, You don't have permissions for requested School!";

			else
			{
				$iSurveyId = getDbValue("id", "tbl_surveys", "school_id='$iSchool' AND enumerator='$sName' AND created_by='$iUser'", "id DESC");
		
				if ($iSurveyId > 0)
				{
					$sPictures = array( );
					
					
					$bFlag = $objDb->execute("BEGIN", true, $iUser, $sName, $sEmail);					
					
					$sSQL = "UPDATE tbl_surveys SET status      = 'C',
													latitude    = IF(latitude='', '$sLatitude', latitude),
													longitude   = IF(longitude='', '$sLongitude', longitude),
													modified_at = '$sDateTime'
							 WHERE id='$iSurveyId'";
					$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
					
					if ($bFlag == true)
					{
						for ($i = 1; $i <= 8; $i ++)
						{
							$sSurveyPictures = @glob($sRootDir.TEMP_DIR."EMIS{$sCode}-Q0{$i}-*.*");
							
							foreach ($sSurveyPictures as $sPicture)
							{
								$sPicture   = @basename($sPicture);
								$sSurveyPic = str_replace("EMIS{$sCode}-", "{$iSurveyId}-", $sPicture);
								
								if (@copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.SURVEYS_DOC_DIR.$sSurveyPic)) == true)
								{
									$iPicture = getNextId("tbl_survey_pictures");
									
									$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																				  survey_id   = '$iSurveyId',
																				  section_id  = '0',
																				  question_id = '$i',
																				  picture     = '$sSurveyPic'";
									$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
									
									if ($bFlag == false)
										break;
									
									$sPictures[] = $sPicture;
								}
							}
							
							
							if ($bFlag == false)
								break;							
						}						
					}
				
				
					if ($bFlag == true && $sLatitude != "" && $sLongitude != "")
					{
						$sAddress  = "";
						$sLocation = @json_decode(file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng={$sLatitude},{$sLongitude}&sensor=false"), true);

						if ($sLocation["results"][0]["address_components"][0]["long_name"] != "")
							$sAddress .= ($sLocation["results"][0]["address_components"][0]["long_name"]."\n");

						if ($sLocation["results"][0]["address_components"][1]["long_name"] != "")
							$sAddress .= ($sLocation["results"][0]["address_components"][1]["long_name"]."\n");

						if ($sLocation["results"][0]["address_components"][2]["long_name"] != "")
							$sAddress .= ($sLocation["results"][0]["address_components"][2]["long_name"]."\n");

						if ($sLocation["results"][0]["address_components"][3]["long_name"] != "")
							$sAddress .= ($sLocation["results"][0]["address_components"][3]["long_name"]."\n");
						
						$sAddress = addslashes($sAddress);

						
						$sSQL = "UPDATE tbl_admins SET latitude='$sLatitude', longitude='$sLongitude', location_address='$sAddress', location_time=NOW() WHERE id='$iUser'";
						$objDb->execute($sSQL, true, $iUser, $sName, $sEmail);

						if (getDbValue("auto_correct", "tbl_schools", "id='$iSchool'") == "Y")
						{
							$sSQL = "UPDATE tbl_schools SET auto_correct = 'N',
															latitude     = '$sLatitude',
															longitude    = '$sLongitude'
									 WHERE id='$iSchool'";
							$objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
						}
					}

					if ($bFlag == true)
					{
						$objDb->execute("COMMIT", true, $iUser, $sName, $sEmail);
						
						$aResponse["Status"]  = "OK";
						$aResponse["Message"] = "Survey marked Completed successfully!";

						
						foreach ($sPictures as $sPicture)							
						{
							@unlink($sRootDir.TEMP_DIR.$sPicture);
						}						
					}

					else
					{
						$aResponse["Message"] = "An ERROR occured, please try again.";
						
						$objDb->execute("ROLLBACK", true, $iUser, $sName, $sEmail);
					}
				}
				
				else
				{
					$aResponse["Status"]  = "OK";
					$aResponse["Message"] = "Survey Already marked Completed!";					
				}
			}
		}
	}

	print @json_encode($aResponse);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>