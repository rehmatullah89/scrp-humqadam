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


	$sUser             = IO::strValue("User");
	$iSchool           = IO::intValue("School");
	$iStage            = IO::intValue("Stage");
	$sInspectionCode   = IO::strValue("InspectionCode");
	$sMeasurementsTime = IO::strValue("DateTime");
	$sTitle            = IO::strValue("Title");
	$sBoqItem          = IO::strValue("BoqItem");
	$sLength           = IO::strValue("Length");
	$sWidth            = IO::strValue("Width");
	$sHeight           = IO::strValue("Height");
	$sMultiplier       = IO::strValue("Multiplier");
	$sNegative         = IO::strValue("Negative");

	
	logApiCall($_POST);
	

	$sBoqItem     = substr($sBoqItem, 1, -1);
	$sBoqItems    = str_replace(", ", ",", $sBoqItem);

	$sTitle       = substr($sTitle, 1, -1);
	$sTitles      = str_replace(", ", ",", $sTitle);

	$sLength      = substr($sLength, 1, -1);
	$sLengths     = str_replace(", ", ",", $sLength);

	$sWidth       = substr($sWidth, 1, -1);
	$sWidths      = str_replace(", ", ",", $sWidth);

	$sHeight      = substr($sHeight, 1, -1);
	$sHeights     = str_replace(", ", ",", $sHeight);

	$sMultiplier  = substr($sMultiplier, 1, -1);
	$sMultipliers = str_replace(", ", ",", $sMultiplier);
	
	$sNegative    = substr($sNegative, 1, -1);
	$sNegatives   = str_replace(", ", ",", $sNegative);


	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $iSchool == 0 || $iStage == 0 || $sInspectionCode == "")
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


			$sDateTime = date("Y-m-d H:i:s");

			if ($sMeasurementsTime != "")
				$sDateTime = date("Y-m-d H:i:s", ($sMeasurementsTime / 1000));



			$sSQL = "SELECT district_id, province_id FROM tbl_schools WHERE id='$iSchool'";
			$objDb->query($sSQL);

			$iDistrict = $objDb->getField(0, "district_id");
			$iProvince = $objDb->getField(0, "province_id");


			if ($objDb->getCount( ) == 0)
				$aResponse["Message"] = "Invalid Request, no School Found!";

			else if ( ($sSchools != "" && !@in_array($iSchool, $iSchools)) || ($sSchools == "" && (!@in_array($iProvince, $iProvinces) || !@in_array($iDistrict, $iDistricts))) )
				$aResponse["Message"] = "Request denied, You don't have permissions for requested School!";

			else if ($iInspection == 0 || getDbValue("COUNT(1)", "tbl_inspections", "id='$iInspection'") == 0)
				$aResponse["Message"] = "Invalid request, no Inspection Record Found!";

			else if (getDbValue("COUNT(1)", "tbl_inspection_measurements", "inspection_id='$iInspection'") > 0)
				$aResponse["Message"] = "Already Saved";

			else
			{
				$sDate        = getDbValue("date", "tbl_inspections", "id='$iInspection'");
				$iContract    = getDbValue("id", "tbl_contracts", "FIND_IN_SET('$iSchool', schools) AND ('$sDate' BETWEEN start_date AND end_date)", "id DESC");

				$sBoqItems    = @explode(",", $sBoqItems);
				$sTitles      = @explode(",", $sTitles);
				$sLengths     = @explode(",", $sLengths);
				$sWidths      = @explode(",", $sWidths);
				$sHeights     = @explode(",", $sHeights);
				$sMultipliers = @explode(",", $sMultipliers);
				$sNegatives   = @explode(",", $sNegatives);


				$bFlag            = $objDb->execute("BEGIN", $iUser, $sName, $sEmail);
				$iLastMeasurement = 0;

				for ($i = 0; $i < count($sBoqItems); $i ++)
				{
					$iBoqItem    = intval($sBoqItems[$i]);
					$sTitle      = $sTitles[$i];
					$fLength     = floatval($sLengths[$i]);
					$fWidth      = floatval($sWidths[$i]);
					$fHeight     = floatval($sHeights[$i]);
					$fMultiplier = floatval($sMultipliers[$i]);
					$sNegative   = $sNegatives[$i];

					if ($fMultiplier <= 0)
						$fMultiplier = 1;


					$sUnit         = getDbValue("unit", "tbl_stages", "id='$iStage'");
					$fRate         = getDbValue("rate", "tbl_contract_boqs", "contract_id='$iContract' AND boq_id='$iBoqItem'");
					$fMeasurements = 0;
					$fAmount       = 0;

					if ($sUnit == "cft")
						$fMeasurements = ($fLength * $fWidth * $fHeight);

					else if ($sUnit == "sft")
						$fMeasurements = ($fLength * $fWidth);

					else
						$fMeasurements = $fLength;


					$fMeasurements *= $fMultiplier;
					$fAmount        = ($fMeasurements * $fRate);



					$iMeasurement = getNextId("tbl_inspection_measurements");
					$iParent      = (($sNegative == "Y") ? $iLastMeasurement : 0);
					
					if ($iParent > 0)
						$fAmount *= -1;
					

					$sSQL = "INSERT INTO tbl_inspection_measurements SET id            = '$iMeasurement',
																		 parent_id     = '$iParent',
																		 inspection_id = '$iInspection',
																		 school_id     = '$iSchool',
																		 stage_id      = '$iStage',
																		 boq_id        = '$iBoqItem',
																		 title         = '$sTitle',
																		 length        = '$fLength',
																		 width         = '$fWidth',
																		 height        = '$fHeight',
																		 multiplier    = '$fMultiplier',
																		 measurements  = '$fMeasurements',
																		 amount        = '$fAmount',
																		 created_by    = '$iUser',
																		 created_at    = '$sDateTime',
																		 modified_by   = '$iUser',
																		 modified_at   = '$sDateTime'";
					$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);

					if ($bFlag == false)
						break;
					

					if ($sNegative != "Y")
						$iLastMeasurement = $iMeasurement;
				}
				
				if ($bFlag == true)
				{
					$sSQL  = "UPDATE tbl_inspections SET measurements='Y' WHERE id='$iInspection'";							  
					$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
				}

				if ($bFlag == true)
				{
					$objDb->execute("COMMIT", $iUser, $sName, $sEmail);

					$aResponse['Status']  = "OK";
					$aResponse["Message"] = "Measurements saved successfully!";
				}

				else
				{
					$objDb->execute("ROLLBACK", $iUser, $sName, $sEmail);

					$aResponse["Message"] = "An ERROR occured, please try again.";
				}
			}
		}
	}

	print @json_encode($aResponse);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>