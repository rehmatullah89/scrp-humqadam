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
	$iBlock          = IO::intValue("Block");
	$iStage          = IO::intValue("Stage");
	$sTitle          = IO::strValue("Title");
	$sDate           = IO::strValue("Date");
	$sDetails        = @utf8_encode(IO::strValue("Details"));
	$sPicture        = IO::strValue("Picture");
	$sDocument       = IO::strValue("Document");
	$sLatitude       = IO::strValue("Latitude");
	$sLongitude      = IO::strValue("Longitude");
	$sInspectionCode = IO::strValue("InspectionCode");

	
	logApiCall($_POST);
	
	
	$iBlock = (($iBlock == 0) ? 1 : $iBlock);

	
	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $iSchool == 0 || $iStage == 0 || $sTitle == "" || $sDate == "")
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

			$iDistrict   = $objDb->getField(0, "district_id");
			$iProvince   = $objDb->getField(0, "province_id");	


			if ($objDb->getCount( ) == 0)
				$aResponse["Message"] = "Invalid Request, no School Found!";

			else if ( ($sSchools != "" && !@in_array($iSchool, $iSchools)) || ($sSchools == "" && (!@in_array($iProvince, $iProvinces) || !@in_array($iDistrict, $iDistricts))) )
				$aResponse["Message"] = "Request denied, You don't have permissions for requested School!";

			else
			{
				$sSQL = "SELECT storey_type, design_type, work_type FROM tbl_school_blocks WHERE school_id='$iSchool' AND block='$iBlock'";
				$objDb->query($sSQL);

				$sStoreyType = $objDb->getField(0, "storey_type");
				$sDesignType = $objDb->getField(0, "design_type");
				$sWorkType   = $objDb->getField(0, "work_type");

			
				$sBlockType = (($sWorkType == "R") ? "R" : (($sDesignType == "B") ? "B" : $sStoreyType));			
				$sAddress   = "";

				if ($sLatitude != "" && $sLongitude != "")
				{
					$sLocation = @json_decode(file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng={$sLatitude},{$sLongitude}&sensor=false"), true);

					if ($sLocation["results"][0]["address_components"][0]["long_name"] != "")
						$sAddress .= ($sLocation["results"][0]["address_components"][0]["long_name"]."\n");

					if ($sLocation["results"][0]["address_components"][1]["long_name"] != "")
						$sAddress .= ($sLocation["results"][0]["address_components"][1]["long_name"]."\n");

					if ($sLocation["results"][0]["address_components"][2]["long_name"] != "")
						$sAddress .= ($sLocation["results"][0]["address_components"][2]["long_name"]."\n");

					if ($sLocation["results"][0]["address_components"][3]["long_name"] != "")
						$sAddress .= ($sLocation["results"][0]["address_components"][3]["long_name"]."\n");
				}


				$sDateTime = date("Y-m-d H:i:s");
				$sAddress  = addslashes($sAddress);

				if ($sInspectionCode != "")
					$sDateTime = date("Y-m-d H:i:s", ($sInspectionCode / 1000));
				

				if (getDbValue("COUNT(1)", "tbl_inspections", "admin_id='$iUser' AND created_by='$iUser' AND created_at='$sDateTime'") > 0)
				{
					$aResponse["Status"]  = "OK";
					$aResponse["Message"] = "Already Saved.";
				}

				else
				{
					$bFlag = $objDb->execute("BEGIN", true, $iUser, $sName, $sEmail);
					
					
					$sSQL = "INSERT INTO tbl_inspections SET admin_id    = '$iUser',
															 latitude    = '$sLatitude',
															 longitude   = '$sLongitude',
															 location    = '$sAddress',
															 district_id = '$iDistrict',
															 school_id   = '$iSchool',
															 block       = '$iBlock',
															 stage_id    = '$iStage',
															 title       = '$sTitle',
															 `date`      = '$sDate',
															 details     = '$sDetails',
															 status      = '',
															 created_by  = '$iUser',
															 created_at  = '$sDateTime',
															 modified_by = '$iUser',
															 modified_at = '$sDateTime'";
					$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
					
					if ($bFlag == true)
						$iInspection = $objDb->getAutoNumber( );
					
					if ($bFlag == true)
					{
						$iMilestoneStage    = getDbValue("id", "tbl_stages", "parent_id='0' AND `type`='$sBlockType'", "position DESC");
						$iStagePosition     = getDbValue("position", "tbl_stages", "id='$iStage'");
						$iMilestonePosition = getDbValue("position", "tbl_stages", "id='$iMileStoneStage'");
						
						if ($iStagePosition > $iMilestonePosition || $sBlockType == "R")
						{
							$sSQL  = "UPDATE tbl_schools SET adopted='Y', qualified='Y' WHERE id='$iSchool'";
							$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
						}
					}
					
					
					if ($bFlag == true)
					{
						$objDb->execute("COMMIT", true, $iUser, $sName, $sEmail);
						
						
						$aResponse["Status"]  = "OK";
						$aResponse["Message"] = "Inspection saved successfully!";


						if ($sLatitude != "" && $sLongitude != "")
						{
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
					}

					else
					{
						$objDb->execute("ROLLBACK", true, $iUser, $sName, $sEmail);
						
						$aResponse["Message"] = "An ERROR occured, please try again.";
					}
				}
			}
		}
	}

	print @json_encode($aResponse);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>