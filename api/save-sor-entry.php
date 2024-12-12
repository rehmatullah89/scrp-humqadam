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


	$sUser            = IO::strValue("User");
	$iSchool          = IO::intValue("School");
	$sDate            = IO::strValue("Date");
	$sPrincipal       = IO::strValue("Principal");
	$sRepresentative  = IO::strValue("Representative");
	$sPosition        = IO::strValue("Position");
	$sDetails         = IO::strValue("Details");
	$iClassRooms      = IO::intValue("ClassRooms");
	$iStaffRooms      = IO::intValue("StaffRooms");
	$iScienceLabs     = IO::intValue("ScienceLabs");
	$iExamHalls       = IO::intValue("ExamHalls");
	$iLibraries       = IO::intValue("Libraries");
	$iPrincipalOffice = IO::intValue("PrincipalOffice");
	$iChowkidarHut    = IO::intValue("ChowkidarHut");
	$iWaterSupply     = IO::intValue("WaterSupply");
	$iStudentToilets  = IO::intValue("StudentToilets");
	$iStaffToilets    = IO::intValue("StaffToilets");
	$iItLabs          = IO::intValue("ItLabs");
	$iStores          = IO::intValue("Stores");
	$iClerkOffices    = IO::intValue("ClerkOffices");
	$iParking         = IO::intValue("Parking");
	$iSoakagePits     = IO::intValue("SoakagePits");
	$iElectricSupply  = IO::intValue("ElectricSupply");
	$sOther           = IO::strValue("Other");
	$iDateTime        = IO::intValue("DateTime");
	$sLatitude        = IO::strValue("Latitude");
	$sLongitude       = IO::strValue("Longitude");	

	
	logApiCall($_POST);
	
	
	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $iSchool == 0 || $sDate == "" || $sPrincipal == "" || $sRepresentative == "" || $sPosition == "" || $iDateTime == "")
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
				$sDateTime = date("Y-m-d H:i:s", ($iDateTime / 1000));
				
				
				if ((int)getDbValue("COUNT(1)", "tbl_school_sors", "school_id='$iSchool' AND site_engineer='$sName' AND created_by='$iUser' AND created_at='$sDateTime' AND `date`='$sDate'") > 0)
					$aResponse["Message"] = "Already Saved.";

				else
				{
					$bFlag = $objDb->execute("BEGIN", true, $iUser, $sName, $sEmail);
					
					
					$iSor = getNextId("tbl_school_sors");								
								
					$sSQL = "INSERT INTO tbl_school_sors SET id                  = '$iSor',
															 district_id         = '$iDistrict',
															 school_id           = '$iSchool',
															 `date`              = '$sDate',															 
		                                                     head_teacher        = '$sPrincipal',
															 site_engineer       = '$sName',
															 ptc_representative  = '$sRepresentative',
															 rep_position        = '$sPosition',
															 rep_contact_details = '$sDetails',
															 classrooms          = '$iClassRooms',
															 science_labs        = '$iScienceLabs',
															 libraries           = '$iLibraries',
															 chowkidar_huts      = '$iChowkidarHut',
															 student_toilets     = '$iStudentToilets',
															 it_labs             = '$iItLabs',
															 clerk_offices       = '$iClerkOffices',
															 soakage_pits        = '$iSoakagePits',    
															 staff_rooms         = '$iStaffRooms',    
															 exam_halls          = '$iExamHalls',
															 principal_office    = '$iPrincipalOffice',
															 water_supply        = '$iWaterSupply',
															 staff_toilets       = '$iStaffToilets',
															 stores              = '$iStores',
															 parking_stands      = '$iParking',
															 electric_supply     = '$iElectricSupply',    
															 other_requirements  = '$sOther',
															 latitude            = '$sLatitude',
															 longitude           = '$sLongitude',															 
															 created_by          = '$iUser',
															 created_at          = '$sDateTime',
															 modified_by         = '$iUser',
															 modified_at         = '$sDateTime'";
					$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
					
				
					if ($bFlag == true)
					{
						$objDb->execute("COMMIT", true, $iUser, $sName, $sEmail);

						$aResponse["Status"]  = "OK";
						$aResponse["Message"] = "SOR Entry saved successfully!";
					}

					else
					{
						$aResponse["Message"] = "An ERROR occured, please try again.";
						
						$objDb->execute("ROLLBACK", true, $iUser, $sName, $sEmail);
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