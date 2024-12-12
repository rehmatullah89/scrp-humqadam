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

	$sName              = IO::strValue("txtName");
	$sCode              = IO::strValue("txtCode");
	$iType              = IO::intValue("ddType");
	$iStudents          = IO::intValue("txtStudents");
	$iBlocks            = IO::intValue("txtBlocks");
	$fCost              = IO::floatValue("txtCost");
    $fRevisedCost       = IO::floatValue("txtRevisedCost");
	$iDistrict          = IO::intValue("ddDistrict");
	$sAddress           = IO::strValue("txtAddress");
	$sTehsil            = IO::strValue("txtTehsil");
	$sUc                = IO::strValue("txtUc");
	$sLatitude          = IO::strValue("txtLatitude");
	$sLongitude         = IO::strValue("txtLongitude");
	$sPhone             = IO::strValue("txtPhone");
	$sFax               = IO::strValue("txtFax");
	$sEmail             = IO::strValue("txtEmail");
	$sDescription       = IO::strValue("txtDescription");
	$sQualified         = IO::strValue("ddQualified");
	$sAdopted           = IO::strValue("ddAdopted");
	$sStatus            = IO::strValue("ddStatus");
	
	$iExClassRooms      = IO::intValue("txtExClassRooms");
	$iExStudentToilets  = IO::intValue("txtExStudentToilets");
	$iExStaffRooms      = IO::intValue("txtExStaffRooms");
	$iExStaffToilets    = IO::intValue("txtExStaffToilets");
	$iExScienceLabs     = IO::intValue("txtExScienceLabs");
	$iExItLabs          = IO::intValue("txtExItLabs");
	$iExExamHalls       = IO::intValue("txtExExamHalls");
	$iExLibrary         = IO::intValue("txtExLibrary");
	$iExClerkOffices    = IO::intValue("txtExClerkOffices");
	$iExPrincipalOffice = IO::intValue("txtExPrincipalOffice");
	$iExParkingStand    = IO::intValue("txtExParkingStand");
	$iExChowkidarHut    = IO::intValue("txtExChowkidarHut");
	$iExSoakagePit      = IO::intValue("txtExSoakagePit");
	$iExWaterSupply     = IO::intValue("txtExWaterSupply");
	$iExStores          = IO::intValue("txtExStores");	

	$sStoreyType        = IO::strValue("ddStoreyType");
	$sDesignType        = IO::strValue("ddDesignType");
	$sWorkType          = IO::strValue("ddWorkType");
	$fCoveredArea       = IO::floatValue("txtCoveredArea");
	$iClassRooms        = IO::intValue("txtClassRooms");
	$iStudentToilets    = IO::intValue("txtStudentToilets");
	$iStaffRooms        = IO::intValue("txtStaffRooms");
	$iStaffToilets      = IO::intValue("txtStaffToilets");
	$iScienceLabs       = IO::intValue("txtScienceLabs");
	$iItLabs            = IO::intValue("txtItLabs");
	$iExamHalls         = IO::intValue("txtExamHalls");
	$iLibrary           = IO::intValue("txtLibrary");
	$iClerkOffices      = IO::intValue("txtClerkOffices");
	$iPrincipalOffice   = IO::intValue("txtPrincipalOffice");
	$iParkingStand      = IO::intValue("txtParkingStand");
	$iChowkidarHut      = IO::intValue("txtChowkidarHut");
	$iSoakagePit        = IO::intValue("txtSoakagePit");
	$iWaterSupply       = IO::intValue("txtWaterSupply");
	$iStores            = IO::intValue("txtStores");
	
	$sPicture           = "";
	$bError             = true;


	if ($sName == "" || $sCode == "" || $iType == 0 || $iBlocks < 1 || $iDistrict == 0 || $sAddress == "" || $sLatitude == "" || $sLongitude == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_schools WHERE `code` LIKE '$sCode'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SCHOOL_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$sLatitude  = str_replace(array(",", " "), "", $sLatitude);
		$sLongitude = str_replace(array(",", " "), "", $sLongitude);
		$iProvince  = getDbValue("province_id", "tbl_districts", "id='$iDistrict'");
		$iSchool    = getNextId("tbl_schools");


		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iSchool."-".IO::getFileName($_FILES['filePicture']['name']));

			if (!@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.SCHOOLS_IMG_DIR.$sPicture)))
				$sPicture = "";
		}


		$objDb->execute("BEGIN");

		$sSQL = "INSERT INTO tbl_schools SET id                  = '$iSchool',
		                                     province_id         = '$iProvince',
		                                     district_id         = '$iDistrict',
		                                     name                = '$sName',
									    	 `code`              = '$sCode',
									    	 type_id             = '$iType',
									    	 blocks              = '$iBlocks',
											 students            = '$iStudents',
									    	 storey_type         = '$sStoreyType',
									    	 design_type         = '$sDesignType',
									    	 work_type           = '$sWorkType',
									    	 covered_area        = '$fCoveredArea',
									    	 class_rooms         = '$iClassRooms',
									    	 student_toilets     = '$iStudentToilets',
									    	 staff_rooms         = '$iStaffRooms',
									    	 staff_toilets       = '$iStaffToilets',
											 science_labs        = '$iScienceLabs',
											 it_labs             = '$iItLabs',
											 exam_halls          = '$iExamHalls',
											 library             = '$iLibrary',
											 clerk_offices       = '$iClerkOffices',
											 principal_office    = '$iPrincipalOffice',
											 parking_stand       = '$iParkingStand',
											 chowkidar_hut       = '$iChowkidarHut',
											 soakage_pit         = '$iSoakagePit',
											 water_supply        = '$iWaterSupply',
											 stores              = '$iStores',
									    	 cost                = '$fCost',
                                             revised_cost        = '$fRevisedCost',    
									    	 ex_class_rooms      = '$iExClassRooms',
									    	 ex_student_toilets  = '$iExStudentToilets',
									    	 ex_staff_rooms      = '$iExStaffRooms',
									    	 ex_staff_toilets    = '$iExStaffToilets',
											 ex_science_labs     = '$iExScienceLabs',
											 ex_it_labs          = '$iExItLabs',
											 ex_exam_halls       = '$iExExamHalls',
											 ex_library          = '$iExLibrary',
											 ex_clerk_offices    = '$iExClerkOffices',
											 ex_principal_office = '$iExPrincipalOffice',
											 ex_parking_stand    = '$iExParkingStand',
											 ex_chowkidar_hut    = '$iExChowkidarHut',
											 ex_soakage_pit      = '$iExSoakagePit',
											 ex_water_supply     = '$iExWaterSupply',
											 ex_stores           = '$iExStores',											 
		                                     address             = '$sAddress',
											 tehsil              = '$sTehsil',
											 uc                  = '$sUc',											 
		                                     latitude            = '$sLatitude',
		                                     longitude           = '$sLongitude',
		                                     phone               = '$sPhone',
		                                     fax                 = '$sFax',
		                                     email               = '$sEmail',
		                                     description         = '$sDescription',
		                                     picture             = '$sPicture',
		                                     position            = '$iSchool',
											 qualified           = '$sQualified',
											 adopted             = '$sAdopted',
		                                     status              = '$sStatus'";
		$bFlag = $objDb->execute($sSQL);
		
		if ($bFlag == true)
		{
			for ($i = 1; $i <= $iBlocks; $i ++)
			{
				$sSQL = "INSERT INTO tbl_school_blocks SET school_id        = '$iSchool',
														   block            = '$i',
														   name             = 'Block # {$i}',
														   storey_type      = '$sStoreyType',
														   design_type      = '$sDesignType',
														   work_type        = '$sWorkType',
														   covered_area     = '$fCoveredArea',
														   class_rooms      = '$iClassRooms',
														   student_toilets  = '$iStudentToilets',
														   staff_rooms      = '$iStaffRooms',
														   staff_toilets    = '$iStaffToilets',
														   science_labs     = '$iScienceLabs',
														   it_labs          = '$iItLabs',
														   exam_halls       = '$iExamHalls',
														   library          = '$iLibrary',
														   clerk_offices    = '$iClerkOffices',
														   principal_office = '$iPrincipalOffice',
														   parking_stand    = '$iParkingStand',
														   chowkidar_hut    = '$iChowkidarHut',
														   soakage_pit      = '$iSoakagePit',
														   water_supply     = '$iWaterSupply',
														   stores           = '$iStores'";
				$bFlag = $objDb->execute($sSQL);
		
				if ($bFlag == false)
					break;
				
				
				if ($i == 1 && $iBlocks > 1)
				{
					$fCoveredArea     = 0;
					$iClassRooms      = 0;
					$iStudentToilets  = 0;
					$iStaffRooms      = 0;
					$iStaffToilets    = 0;
					$iScienceLabs     = 0;
					$iItLabs          = 0;
					$iExamHalls       = 0;
					$iLibrary         = 0;
					$iClerkOffices    = 0;
					$iPrincipalOffice = 0;
					$iParkingStand    = 0;
					$iChowkidarHut    = 0;
					$iSoakagePit      = 0;
					$iWaterSupply     = 0;
					$iStores          = 0;
				}
			}
		}
		
		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");
			
			redirect("schools.php", "SCHOOL_ADDED");
		}

		else
		{
			$objDb->execute("ROLLBACK");
			
			$_SESSION["Flag"] = "DB_ERROR";

			if ($sPicture != "")
				@unlink($sRootDir.SCHOOLS_IMG_DIR.$sPicture);
		}
	}
?>