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
	$objDb2      = new Database( );


	$sUser   = IO::strValue("User");
	$sSchool = IO::strValue("School");


	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $sSchool == "")
		$aResponse["Message"] = "Invalid Request";

	else
	{
		$sSQL = "SELECT id, name, provinces, districts, schools, status FROM tbl_admins WHERE MD5(id)='$sUser'";
		$objDb->query($sSQL);

		if ($objDb->getCount( ) == 0)
			$aResponse["Message"] = "Invalid User";

		else if ($objDb->getField(0, "status") != "A")
			$aResponse["Message"] = "User Account is Disabled";

		else
		{
			$iUser      = $objDb->getField(0, "id");
			$sName      = $objDb->getField(0, "name");
			$sProvinces = $objDb->getField(0, "provinces");
			$sDistricts = $objDb->getField(0, "districts");
			$sSchools   = $objDb->getField(0, "schools");



			$sSQL = "SELECT * FROM tbl_schools WHERE status='A' AND code LIKE '$sSchool'";

			if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			{
				$iSchool     = $objDb->getField(0, "id");
				$sCode       = $objDb->getField(0, "code");
				$sSchool     = $objDb->getField(0, "name");
				$sDropped    = $objDb->getField(0, "dropped");
				$iDistrict   = $objDb->getField(0, "district_id");
				$iProvince   = $objDb->getField(0, "province_id");
				$iBlocks     = $objDb->getField(0, "blocks");


				$iProvinces  = @explode(",", $sProvinces);
				$iDistricts  = @explode(",", $sDistricts);
				$iSchools    = @explode(",", $sSchools);
				$iSurveys    = getDbValue("COUNT(1)", "tbl_surveys", "school_id='$iSchool'");
				$sBlocksInfo = array( );
				
				
				$sSQL = "SELECT * FROM tbl_school_blocks WHERE school_id='$iSchool' ORDER BY block";
				$objDb2->query($sSQL);
				
				$iCount2 = $objDb2->getCount( );
				
				for ($j = 0; $j < $iCount2; $j ++)
				{
					$iBlock           = $objDb2->getField($j, "block");
					$sName            = $objDb2->getField($j, "name");
					$sStoreyType      = $objDb2->getField($j, "storey_type");
					$sDesignType      = $objDb2->getField($j, "design_type");
					$sWorkType        = $objDb2->getField($j, "work_type");
					$fCoveredArea     = $objDb2->getField($j, "covered_area");
					$iClassRooms      = $objDb2->getField($j, "class_rooms");
					$iStudentToilets  = $objDb2->getField($j, "student_toilets");
					$iStaffRooms      = $objDb2->getField($j, "staff_rooms");
					$iStaffToilets    = $objDb2->getField($j, "staff_toilets");
					$iScienceLabs     = $objDb2->getField($j, "science_labs");
					$iItLabs          = $objDb2->getField($j, "it_labs");
					$iExamHalls       = $objDb2->getField($j, "exam_halls");
					$iLibrary         = $objDb2->getField($j, "library");
					$iClerkOffices    = $objDb2->getField($j, "clerk_offices");
					$iPrincipalOffice = $objDb2->getField($j, "principal_office");
					$iParkingStand    = $objDb2->getField($j, "parking_stand");
					$iChowkidarHut    = $objDb2->getField($j, "chowkidar_hut");
					$iSoakagePit      = $objDb2->getField($j, "soakage_pit");
					$iWaterSupply     = $objDb2->getField($j, "water_supply");			
					$iStores          = $objDb2->getField($j, "stores");
					
					
					$sDetails = "";
					
					if ($iClassRooms > 0)
						$sDetails .= "Class Rooms: {$iClassRooms}<br />";
					
					if ($iStudentToilets > 0)
						$sDetails .= "Student Toilets: {$iStudentToilets}<br />";
					
					if ($iStaffRooms > 0)
						$sDetails .= "Staff Rooms: {$iStaffRooms}<br />";
					
					if ($iStaffToilets > 0)
						$sDetails .= "Staff Toilets: {$iStaffToilets}<br />";
					
					if ($iScienceLabs > 0)
						$sDetails .= "Science Labs: {$iScienceLabs}<br />";
					
					if ($iItLabs > 0)
						$sDetails .= "IT Labs: {$iItLabs}<br />";
					
					if ($iExamHalls > 0)
						$sDetails .= "Exam Halls: {$iExamHalls}<br />";

					if ($iLibrary > 0)
						$sDetails .= "Library: {$iLibrary}<br />";

					if ($iClerkOffices > 0)
						$sDetails .= "Clerk Offices: {$iClerkOffices}<br />";
					
					if ($iPrincipalOffice > 0)
						$sDetails .= "Principal Office: {$iPrincipalOffice}<br />";
					
					if ($iParkingStand > 0)
						$sDetails .= "Parking Stand: {$iParkingStand}<br />";
					
					if ($iChowkidarHut > 0)
						$sDetails .= "Chowkidar Hut: {$iChowkidarHut}<br />";

					if ($iSoakagePit > 0)
						$sDetails .= "Soakage Pit: {$iSoakagePit}<br />";
					
					if ($iWaterSupply > 0)
						$sDetails .= "Water Supply: {$iWaterSupply}<br />";
					
					if ($iStores > 0)
						$sDetails .= "Store Rooms: {$iStores}<br />";
					
					
					$sBlocksInfo[] = array("Block"   => $iBlock, 
										   "Name"    => $sName, 
										   "Storey"  => $sStoreyType, 
										   "Design"  => $sDesignType, 
										   "Work"    => $sWorkType,
										   "Area"    => formatNumber($fCoveredArea),
										   "Details" => $sDetails);
				}


				if ( ($sSchools != "" && !@in_array($iSchool, $iSchools)) || ($sSchools == "" && (!@in_array($iProvince, $iProvinces) || !@in_array($iDistrict, $iDistricts))) )
					$aResponse["Message"] = "Request denied, You don't have permissions for requested School!";
				
				else
				{
					$aResponse['Status']     = "OK";
					$aResponse['SchoolId']   = $iSchool;  // to be deleted
					$aResponse['School']     = $sSchool;  // to be deleted
					$aResponse['Type']       = (($sDesignType == "B") ? "B" : $sStoreyType);  // to be deleted
					$aResponse['Id']         = $iSchool;
					$aResponse['Name']       = $sSchool;					
					$aResponse['Code']       = $sCode;
					$aResponse['Province']   = $iProvince;
					$aResponse['Dropped']    = $sDropped;
					$aResponse['Survey']     = (($iSurveys > 0 && !@in_array($iSchool, array(1509,1540,3090))) ? "Y" : "N");
					$aResponse['Blocks']     = $iBlocks;
					$aResponse['BlocksInfo'] = $sBlocksInfo;
				}
			}

			else
				$aResponse["Message"] = "Invalid EMIS Code, no School Found!";
		}
	}

	print @json_encode($aResponse);


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>