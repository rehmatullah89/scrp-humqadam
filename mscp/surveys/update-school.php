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
	$sAutoCorrect       = IO::strValue("cbAutoCorrect");
	$sDropped           = IO::strValue("ddDropped");
	$sQualified         = IO::strValue("ddQualified");
	$sDangerous         = IO::strValue("ddDangerous");
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
	
	$sOldPicture        = IO::strValue("Picture");
	$sPicture           = "";
	$sPictureSql        = "";
	$sAutoCorrectSql    = "";


	if ($sName == "" || $sCode == "" || $iType == 0 || $iBlocks < 1 || $iDistrict == 0 || $sAddress == "" || $sLatitude == "" || $sLongitude == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_schools WHERE `code` LIKE '$sCode' AND id!='$iSchoolId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SCHOOL_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT blocks, district_id, storey_type, design_type, last_stage_id, last_milestone_id FROM tbl_schools WHERE id='$iSchoolId'";
		$objDb->query($sSQL);

		$iOldDistrict   = $objDb->getField(0, "district_id");
		$iOldBlocks     = $objDb->getField(0, "blocks");
		$sOldStoreyType = $objDb->getField(0, "storey_type");
		$sOldDesignType = $objDb->getField(0, "design_type");
		$iLastStage     = $objDb->getField(0, "last_stage_id");
		$iLastMilestone = $objDb->getField(0, "last_milestone_id");

		
		$sLatitude  = str_replace(array(",", " "), "", $sLatitude);
		$sLongitude = str_replace(array(",", " "), "", $sLongitude);

		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iSchoolId."-".IO::getFileName($_FILES['filePicture']['name']));

			if (@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.SCHOOLS_IMG_DIR.$sPicture)))
				$sPictureSql = ", picture='$sPicture'";
		}


		if ($sAutoCorrect == "Y")
			$sAutoCorrectSql = ", auto_correct='Y' ";


		$iProvince = getDbValue("province_id", "tbl_districts", "id='$iDistrict'");
		$sDesigns  = array( );
		$sStoreys  = array( );
		$sWork     = array( );
		

		$objDb->execute("BEGIN");
		
		$sSQL = "UPDATE tbl_schools SET province_id         = '$iProvince',
									    district_id         = '$iDistrict',
										name                = '$sName',
									    `code`              = '$sCode',
									    type_id             = '$iType',
									    students            = '$iStudents',
										blocks              = '$iBlocks',
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
									    dangerous           = '$sDangerous',
										dropped             = '$sDropped',
										qualified           = '$sQualified',
										adopted             = '$sAdopted',
									    status              = '$sStatus'
									    $sPictureSql
									    $sAutoCorrectSql
		         WHERE id='$iSchoolId'";
		$bFlag = $objDb->execute($sSQL);
		
		if ($bFlag == true && $iDistrict != $iOldDistrict)
		{
			$sSQL  = "UPDATE tbl_inspections SET district_id='$iDistrict' WHERE district_id='$iOldDistrict' AND school_id='$iSchoolId'";
			$bFlag = $objDb->execute($sSQL);
			
			if ($bFlag == true)
			{
				$sSQL  = "UPDATE tbl_surveys SET district_id='$iDistrict' WHERE district_id='$iOldDistrict' AND school_id='$iSchoolId'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "UPDATE tbl_survey_schedules SET district_id='$iDistrict' WHERE district_id='$iOldDistrict' AND school_id='$iSchoolId'";
				$bFlag = $objDb->execute($sSQL);
			}			
		}
		
		if ($bFlag == true)
		{
			$sSQL = "DELETE FROM tbl_school_blocks WHERE school_id='$iSchoolId' AND block>'$iBlocks'";
			$bFlag = $objDb->execute($sSQL);			
		}
			
print $iOldBlocks." - ".$iBlocks."<br>";
		if ($bFlag == true && $iBlocks > $iOldBlocks)
		{
			for ($i = ($iOldBlocks + 1); $i <= $iBlocks; $i ++)
			{
				$sSQL = "INSERT INTO tbl_school_blocks SET school_id        = '$iSchoolId',
														   block            = '$i',
														   name             = 'Block # {$i}',
														   storey_type      = 'S',
														   design_type      = 'R',
														   work_type        = 'N',
														   covered_area     = '0',
														   class_rooms      = '0',
														   student_toilets  = '0',
														   staff_rooms      = '0',
														   staff_toilets    = '0',
														   science_labs     = '0',
														   it_labs          = '0',
														   exam_halls       = '0',
														   library          = '0',
														   clerk_offices    = '0',
														   principal_office = '0',
														   parking_stand    = '0',
														   chowkidar_hut    = '0',
														   soakage_pit      = '0',
														   water_supply     = '0'";
				$bFlag = $objDb->execute($sSQL);

				if ($bFlag == false)
					break;
			}
		}		
		
		if ($bFlag == true)
		{
			for ($i = 1; $i <= $iBlocks; $i ++)
			{
				$sBlockName            = IO::strValue("txtName{$i}");
				$sBlockStoreyType      = IO::strValue("ddStoreyType{$i}");
				$sBlockDesignType      = IO::strValue("ddDesignType{$i}");
				$sBlockWorkType        = IO::strValue("ddWorkType{$i}");
				$fBlockCoveredArea     = IO::floatValue("txtCoveredArea{$i}");
				$iBlockClassRooms      = IO::intValue("txtClassRooms{$i}");
				$iBlockStudentToilets  = IO::intValue("txtStudentToilets{$i}");
				$iBlockStaffRooms      = IO::intValue("txtStaffRooms{$i}");
				$iBlockStaffToilets    = IO::intValue("txtStaffToilets{$i}");
				$iBlockScienceLabs     = IO::intValue("txtScienceLabs{$i}");
				$iBlockItLabs          = IO::intValue("txtItLabs{$i}");
				$iBlockExamHalls       = IO::intValue("txtExamHalls{$i}");
				$iBlockLibrary         = IO::intValue("txtLibrary{$i}");
				$iBlockClerkOffices    = IO::intValue("txtClerkOffices{$i}");
				$iBlockPrincipalOffice = IO::intValue("txtPrincipalOffice{$i}");
				$iBlockParkingStand    = IO::intValue("txtParkingStand{$i}");
				$iBlockChowkidarHut    = IO::intValue("txtChowkidarHut{$i}");
				$iBlockSoakagePit      = IO::intValue("txtSoakagePit{$i}");
				$iBlockWaterSupply     = IO::intValue("txtWaterSupply{$i}");				
				$iBlockStores          = IO::intValue("txtStores{$i}");

				$sBlockName       = (($sBlockName == "") ? "Block # {$i}" : $sBlockName);
				$sBlockStoreyType = (($sBlockStoreyType == "") ? IO::strValue("ddStoreyType1") : $sBlockStoreyType);
				$sBlockDesignType = (($sBlockDesignType == "") ? IO::strValue("ddDesignType1") : $sBlockDesignType);
				$sBlockWorkType   = (($sBlockWorkType == "") ? IO::strValue("ddWorkType1") : $sBlockWorkType);
				
				if (!@in_array($sBlockStoreyType, $sStoreys))
					$sStoreys[] = $sBlockStoreyType;
			
				if (!@in_array($sBlockDesignType, $sDesigns))
					$sDesigns[] = $sBlockDesignType;
				
				if (!@in_array($sBlockWorkType, $sWork))
					$sWork[] = $sBlockWorkType;
				
				
				
				$sSQL = "UPDATE tbl_school_blocks SET name             = '$sBlockName',
													  storey_type      = '$sBlockStoreyType',
													  design_type      = '$sBlockDesignType',
													  work_type        = '$sBlockWorkType',
													  covered_area     = '$fBlockCoveredArea',
													  class_rooms      = '$iBlockClassRooms',
													  student_toilets  = '$iBlockStudentToilets',
													  staff_rooms      = '$iBlockStaffRooms',
													  staff_toilets    = '$iBlockStaffToilets',
													  science_labs     = '$iBlockScienceLabs',
													  it_labs          = '$iBlockItLabs',
													  exam_halls       = '$iBlockExamHalls',
													  library          = '$iBlockLibrary',
													  clerk_offices    = '$iBlockClerkOffices',
													  principal_office = '$iBlockPrincipalOffice',
													  parking_stand    = '$iBlockParkingStand',
													  chowkidar_hut    = '$iBlockChowkidarHut',
													  soakage_pit      = '$iBlockSoakagePit',
												      water_supply     = '$iBlockWaterSupply',
													  stores           = '$iBlockStores'
						 WHERE school_id='$iSchoolId' AND block='$i'";
				$bFlag = $objDb->execute($sSQL);
				
				if ($bFlag == false)
					break;
			}
		}
			
		if ($bFlag == true)
		{
			$sStoreyType = $sStoreys[0];
			$sDesignType = $sDesigns[0];
			$sWorkType   = $sWork[0];
			

			if (count($sStoreys) > 0 && @in_array("T", $sStoreys))
				$sStoreyType = "T";
			
			else if (count($sStoreys) > 0 && @in_array("D", $sStoreys))
				$sStoreyType = "D";
			
			
			if (count($sDesigns) > 1)
				$sDesignType = "B";
			
			if (count($sWork) > 1)
				$sWorkType = "B";
				
			
			$sSQL = "UPDATE tbl_schools SET storey_type      = '$sStoreyType',
											design_type      = '$sDesignType',
											work_type        = '$sWorkType',
											covered_area     = (SELECT SUM(covered_area) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											class_rooms      = (SELECT SUM(class_rooms) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											student_toilets  = (SELECT SUM(student_toilets) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											staff_rooms      = (SELECT SUM(staff_rooms) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											staff_toilets    = (SELECT SUM(staff_toilets) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											science_labs     = (SELECT SUM(science_labs) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											it_labs          = (SELECT SUM(it_labs) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											exam_halls       = (SELECT SUM(exam_halls) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											library          = (SELECT SUM(library) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											clerk_offices    = (SELECT SUM(clerk_offices) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											principal_office = (SELECT SUM(principal_office) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											parking_stand    = (SELECT SUM(parking_stand) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											chowkidar_hut    = (SELECT SUM(chowkidar_hut) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											soakage_pit      = (SELECT SUM(soakage_pit) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											water_supply     = (SELECT SUM(water_supply) FROM tbl_school_blocks WHERE school_id='$iSchoolId'),
											stores           = (SELECT SUM(stores) FROM tbl_school_blocks WHERE school_id='$iSchoolId')
					 WHERE id='$iSchoolId'";
			$bFlag = $objDb->execute($sSQL);
		}
		
		if ($bFlag == true && ($sOldStoreyType != $sStoreyType || $sOldDesignType != $sDesignType))
		{
			$iSchedule = getDbValue("id", "tbl_contract_schedules", "school_id='$iSchoolId'");
			
			
			$sSQL  = "DELETE FROM tbl_contract_schedules WHERE id='$iSchedule'";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_contract_schedule_details WHERE schedule_id='$iSchedule'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);
				$sAllStages  = getList("tbl_stages", "id", "name");
				$iStages     = array( );
				
				
				$sSQL = "SELECT id FROM tbl_stages WHERE `type`='$sSchoolType' ORDER BY position";
				$objDb->query($sSQL);
				
				$iCount = $objDb->getCount( );
				
				for ($i = 0; $i < $iCount; $i ++)
					$iStages[] = $objDb->getField($i, 0);
					
				$sStages = @implode(",", $iStages);
				
				
				
				$sSQL = "SELECT id, stage_id FROM tbl_inspections WHERE school_id='$iSchoolId' AND stage_id NOT IN ($sStages)";		
				$objDb->query($sSQL);

				$iCount = $objDb->getCount( );
				
				for ($i = 0; $i < $iCount; $i ++)
				{
					$iInspection = $objDb->getField($i, "id");
					$iStage      = $objDb->getField($i, "stage_id");
					
					
					$iNewStage = (int)getDbValue("id", "tbl_stages", "`type`='$sSchoolType' AND name LIKE '{$sAllStages[$iStage]}'");
					
					if ($iNewStage > 0)
					{		
						$sSQL  = "UPDATE tbl_inspections SET stage_id='$iNewStage' WHERE id='$iInspection' AND stage_id='$iStage'";
						$bFlag = $objDb2->execute($sSQL);
						
						if ($bFlag == false)
							break;
					}
				}
			}
			
			
			if ($bFlag == true)
			{
				$iNewStage = (int)getDbValue("id", "tbl_stages", "`type`='$sSchoolType' AND name LIKE '{$sAllStages[$iLastStage]}'");
				
				if ($iNewStage > 0)
				{		
					$sSQL  = "UPDATE tbl_schools SET last_stage_id='$iNewStage' WHERE id='$iSchoolId'";
					$bFlag = $objDb->execute($sSQL);
				}
			}
			
			if ($bFlag == false)
			{
				$iNewMilestone = (int)getDbValue("id", "tbl_stages", "`type`='$sSchoolType' AND name LIKE '{$sAllStages[$iLastMilestone]}'");
				
				if ($iNewMilestone > 0)
				{		
					$sSQL  = "UPDATE tbl_schools SET last_milestone_id='$iNewMilestone' WHERE id='$iSchoolId'";
					$bFlag = $objDb->execute($sSQL);
				}		
			}
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			
			if ($sOldPicture != "" && $sPicture != "" && $sOldPicture != $sPicture)
				@unlink($sRootDir.SCHOOLS_IMG_DIR.$sOldPicture);


			$sType     = getDbValue("type", "tbl_school_types", "id='$iType'");
			$sDistrict = getDbValue("name", "tbl_districts", "id='$iDistrict'");
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sName) ?>";
		sFields[1] = "<?= $sCode ?>";
		sFields[2] = "<?= $sType ?>";
		sFields[3] = "<?= (($sStoreyType == "S") ? "Single" : (($sStoreyType == "D") ? "Double" : "Triple")) ?>";
		sFields[4] = "<?= (($sDesignType == "R") ? "Regular" : "Bespoke") ?>";
		sFields[5] = "<?= formatNumber($iStudents, false) ?>";
		sFields[6] = "<?= formatNumber($fRevisedCost) ?>";
		sFields[7] = "<?= addslashes($sDistrict) ?>";
		sFields[8] = "";
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[8] = (sFields[8] + '<img class="icnToggle" id="<?= $iSchoolId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" /> ');
		sFields[8] = (sFields[8] + '<img class="icnEdit" id="<?= $iSchoolId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sFields[8] = (sFields[8] + '<img class="icnDelete" id="<?= $iSchoolId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}

			if ($sOldPicture != "" && @file_exists($sRootDir.SCHOOLS_IMG_DIR.$sOldPicture))
			{
?>
		sFields[8] = (sFields[8] + '<img class="icnPicture" id="<?= (SITE_URL.SCHOOLS_IMG_DIR.$sOldPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
<?
			}

			else if ($sPicture != "" && @file_exists($sRootDir.SCHOOLS_IMG_DIR.$sPicture))
			{
?>
		sFields[8] = (sFields[8] + '<img class="icnPicture" id="<?= (SITE_URL.SCHOOLS_IMG_DIR.$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
<?
			}
?>
		sFields[8] = (sFields[8] + '<img class="icnView" id="<?= $iSchoolId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

                sFields[8] = (sFields[8] + '<img class="icon icnMembers" id="<?= $iSchoolId ?>" src="images/icons/members.png" alt="Members" title="Members" /> ');
                
		parent.updateRecord(<?= $iSchoolId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected School has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";

			if ($sPicture != "" && $sOldPicture != $sPicture)
				@unlink($sRootDir.SCHOOLS_IMG_DIR.$sPicture);
		}
	}
?>