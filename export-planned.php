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

	@require_once("requires/common.php");
	@require_once("requires/PHPExcel.php");

     if (!@strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))
         die("ERROR: Invalid Request");


	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );
	$objDb3      = new Database( );


	$sKeywords = IO::strValue("Keywords");
	$iPackage  = IO::intValue("Package");
	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");
	$sStatus   = IO::strValue("Status");
	$iSchool   = IO::intValue("School");


	$sConditions = "WHERE status='A' AND dropped!='Y' AND adopted='Y' AND FIND_IN_SET(province_id, '{$_SESSION['AdminProvinces']}') AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(id, '{$_SESSION['AdminSchools']}') ";


	if ($iSchool > 0)
		$sConditions .= " AND id='$iSchool' ";

	if ($sKeywords != "")
	{
		$sKeywords    = str_replace(" ", "%", $sKeywords);

		$sConditions .= " AND (name LIKE '%{$sKeywords}%' OR code LIKE '{$sKeywords}' OR address LIKE '%{$sKeywords}%') ";
	}

	if ($iPackage > 0 || $iProvince > 0)
	{
		$sConditions .= " AND (";

		if ($iPackage > 0)
		{
			$sSchools = getDbValue("schools", "tbl_packages", "id='$iPackage'");

			$sConditions .= " FIND_IN_SET(id, '$sSchools') ";
		}

		if ($iPackage > 0 && $iProvince > 0)
			$sConditions .= " OR ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " (";

		if ($iProvince > 0)
			$sConditions .= " province_id='$iProvince' ";

		if ($iDistrict > 0)
			$sConditions .= " AND district_id='$iDistrict' ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " )";

		$sConditions .= ")";
	}


	$sSubConditions = "";

	if ($_SESSION["AdminSchools"] != "")
		$sSubConditions = " AND FIND_IN_SET(id, '{$_SESSION['AdminSchools']}') ";


	if ($sStatus == "Active")
	{
		$iMilestoneStageS = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='S'", "position DESC");
		$iMilestoneStageD = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='D'", "position DESC");
		$iMilestoneStageT = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='T'", "position DESC");
		$iMilestoneStageB = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='B'", "position DESC");
		$iMilestoneStages = array( );
		
		$sSQL = "SELECT id FROM tbl_stages WHERE ((`type`='S' AND position>'$iMilestoneStageS') OR (`type`='D' AND position>'$iMilestoneStageD') OR (`type`='T' AND position>'$iMilestoneStageT') OR (`type`='B' AND position>'$iMilestoneStageB')) ORDER BY position";
		$objDb->query($sSQL);
		
		$iCount = $objDb->getCount( );
		
		for ($i = 0; $i < $iCount; $i ++)
			$iMilestoneStages[] = $objDb->getField($i, 0);
			
		$sMilestoneStages = @implode(",", $iMilestoneStages);
		
		
		$sConditions .= " AND id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions) ";
	}

	else if ($sStatus == "InActive")
	{
		$iMilestoneStageS = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='S'", "position DESC");
		$iMilestoneStageD = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='D'", "position DESC");
		$iMilestoneStageT = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='T'", "position DESC");
		$iMilestoneStageB = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='B'", "position DESC");
		$iMilestoneStages = array( );
		
		$sSQL = "SELECT id FROM tbl_stages WHERE ((`type`='S' AND position>'$iMilestoneStageS') OR (`type`='D' AND position>'$iMilestoneStageD') OR (`type`='T' AND position>'$iMilestoneStageT') OR (`type`='B' AND position>'$iMilestoneStageB')) ORDER BY position";
		$objDb->query($sSQL);
		
		$iCount = $objDb->getCount( );
		
		for ($i = 0; $i < $iCount; $i ++)
			$iMilestoneStages[] = $objDb->getField($i, 0);
			
		$sMilestoneStages = @implode(",", $iMilestoneStages);

		
		$sConditions .= " AND id NOT IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions) ";
	}

	else if ($sStatus == "Delayed" || $sStatus == "OnTime")
	{
		$sSQL = "SELECT cs.school_id, csd.stage_id, MAX(csd.end_date) AS _EndDate
				 FROM tbl_contract_schedules cs, tbl_contract_schedule_details csd
				 WHERE cs.id=csd.schedule_id AND csd.end_date < CURDATE( ) AND csd.end_date!='0000-00-00'
				 GROUP BY cs.school_id";
		$objDb->query($sSQL);

		$iCount          = $objDb->getCount( );
		$iDelayedSchools = array( );
		$iOnTimeSchools  = array( );
		$iUserSchools    = array( );

		if ($_SESSION["AdminSchools"] != "")
			$iUserSchools = @explode(",", $_SESSION["AdminSchools"]);

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iSchool  = $objDb->getField($i, "school_id");
			$iStage   = $objDb->getField($i, "stage_id");
			$iEndDate = $objDb->getField($i, "_EndDate");

			if ($_SESSION["AdminSchools"] != "" && !@in_array($iSchool, $iUserSchools))
				continue;


			$iInspections = getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool' AND stage_id='$iStage' AND status='P' AND stage_completed='Y'");

			if ($iInspections == 0)
				$iDelayedSchools[] = $iSchool;

			else
				$iOnTimeSchools[] = $iSchool;
		}


		if ($sStatus == "Delayed")
		{
			$sDelayedSchools = @implode(",", $iDelayedSchools);
			$sConditions    .= " AND FIND_IN_SET(id, '$sDelayedSchools') ";
		}

		if ($sStatus == "OnTime")
		{
			$sOnTimeSchools = @implode(",", $iOnTimeSchools);
			$sConditions   .= " AND FIND_IN_SET(id, '$sOnTimeSchools') ";
		}
	}


	$sAllStagesList = getList("tbl_stages", "id", "name", "status='A'");
	$sSsStagesList  = getList("tbl_stages", "id", "name", "parent_id='0' AND status='A' AND `type`='S'", "position");
	$sDsStagesList  = getList("tbl_stages", "id", "name", "parent_id='0' AND status='A' AND `type`='D'", "position");
	$sBsStagesList  = getList("tbl_stages", "id", "name", "parent_id='0' AND status='A' AND `type`='B'", "position");
	$sSsSubStages   = array( );
	$sDsSubStages   = array( );
	$sBsSubStages   = array( );


	// Single Storey
	foreach ($sSsStagesList as $iParent => $sParent)
	{
		$sSsSubStages[$iParent] = "";


		$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND parent_id='$iParent' ORDER BY position";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		if ($iCount == 0)
			$sSsSubStages[$iParent] = $iParent;


		for ($i = 0; $i < $iCount; $i ++)
		{
			$iStage = $objDb->getField($i, "id");


			$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iStage'");

			if ($sChildStages == "")
			{
				$sSsSubStages[$iStage]   = $iStage;
				$sSsSubStages[$iParent] .= ((($sSsSubStages[$iParent] != "") ? "," : "").$iStage);
			}

			else if ($sChildStages != "")
			{
				$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND parent_id='$iStage' ORDER BY position";
				$objDb2->query($sSQL);

				$iCount2 = $objDb2->getCount( );

				for ($j = 0; $j < $iCount2; $j ++)
				{
					$iSubStage = $objDb2->getField($j, "id");


					$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "status='A' AND parent_id='$iSubStage'", "position");

					if ($sChildStages == "")
					{
						$sSsSubStages[$iSubStage]  = $iSubStage;
						$sSsSubStages[$iStage]    .= ((($sSsSubStages[$iStage] != "") ? "," : "").$iSubStage);
						$sSsSubStages[$iParent]   .= ((($sSsSubStages[$iParent] != "") ? "," : "").$iSubStage);
					}

					else if ($sChildStages != "")
					{
						$sSsSubStages[$iSubStage]  = $sChildStages;
						$sSsSubStages[$iStage]    .= ((($sSsSubStages[$iStage] != "") ? "," : "").$sChildStages);
						$sSsSubStages[$iParent]   .= ((($sSsSubStages[$iParent] != "") ? "," : "").$sChildStages);
					}
				}
			}
		}
	}
	
	
	// Double Storey
	foreach ($sDsStagesList as $iParent => $sParent)
	{
		$sDsSubStages[$iParent] = "";


		$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND parent_id='$iParent' ORDER BY position";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		if ($iCount == 0)
			$sDsSubStages[$iParent] = $iParent;


		for ($i = 0; $i < $iCount; $i ++)
		{
			$iStage = $objDb->getField($i, "id");


			$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iStage'");

			if ($sChildStages == "")
			{
				$sDsSubStages[$iStage]   = $iStage;
				$sDsSubStages[$iParent] .= ((($sDsSubStages[$iParent] != "") ? "," : "").$iStage);
			}

			else if ($sChildStages != "")
			{
				$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND parent_id='$iStage' ORDER BY position";
				$objDb2->query($sSQL);

				$iCount2 = $objDb2->getCount( );

				for ($j = 0; $j < $iCount2; $j ++)
				{
					$iSubStage = $objDb2->getField($j, "id");


					$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "status='A' AND parent_id='$iSubStage'", "position");

					if ($sChildStages == "")
					{
						$sDsSubStages[$iSubStage]  = $iSubStage;
						$sDsSubStages[$iStage]    .= ((($sDsSubStages[$iStage] != "") ? "," : "").$iSubStage);
						$sDsSubStages[$iParent]   .= ((($sDsSubStages[$iParent] != "") ? "," : "").$iSubStage);
					}

					else if ($sChildStages != "")
					{
						$sDsSubStages[$iSubStage]  = $sChildStages;
						$sDsSubStages[$iStage]    .= ((($sDsSubStages[$iStage] != "") ? "," : "").$sChildStages);
						$sDsSubStages[$iParent]   .= ((($sDsSubStages[$iParent] != "") ? "," : "").$sChildStages);
					}
				}
			}
		}
	}
	
	
	// Bespoke
	foreach ($sBsStagesList as $iParent => $sParent)
	{
		$sBsSubStages[$iParent] = "";


		$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND parent_id='$iParent' ORDER BY position";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		if ($iCount == 0)
			$sBsSubStages[$iParent] = $iParent;


		for ($i = 0; $i < $iCount; $i ++)
		{
			$iStage = $objDb->getField($i, "id");


			$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iStage'");

			if ($sChildStages == "")
			{
				$sBsSubStages[$iStage]   = $iStage;
				$sBsSubStages[$iParent] .= ((($sBsSubStages[$iParent] != "") ? "," : "").$iStage);
			}

			else if ($sChildStages != "")
			{
				$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND parent_id='$iStage' ORDER BY position";
				$objDb2->query($sSQL);

				$iCount2 = $objDb2->getCount( );

				for ($j = 0; $j < $iCount2; $j ++)
				{
					$iSubStage = $objDb2->getField($j, "id");


					$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "status='A' AND parent_id='$iSubStage'", "position");

					if ($sChildStages == "")
					{
						$sBsSubStages[$iSubStage]  = $iSubStage;
						$sBsSubStages[$iStage]    .= ((($sBsSubStages[$iStage] != "") ? "," : "").$iSubStage);
						$sBsSubStages[$iParent]   .= ((($sBsSubStages[$iParent] != "") ? "," : "").$iSubStage);
					}

					else if ($sChildStages != "")
					{
						$sBsSubStages[$iSubStage]  = $sChildStages;
						$sBsSubStages[$iStage]    .= ((($sBsSubStages[$iStage] != "") ? "," : "").$sChildStages);
						$sBsSubStages[$iParent]   .= ((($sBsSubStages[$iParent] != "") ? "," : "").$sChildStages);
					}
				}
			}
		}
	}




	$objPhpExcel = new PHPExcel( );

	$objReader   = PHPExcel_IOFactory::createReader('Excel2007');
	$objPhpExcel = $objReader->load("templates/planned.xlsx");

	$objPhpExcel->getProperties()->setCreator("Humqadam")
								 ->setLastModifiedBy("Humqadam")
								 ->setTitle("Project Tracker")
								 ->setSubject("Project Tracker")
								 ->setDescription("Project Tracker")
								 ->setKeywords("")
								 ->setCategory("Humqadam");

	$objPhpExcel->setActiveSheetIndex(0);
	$objPhpExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(13);
	$objPhpExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$objPhpExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(40);



	$sSQL = "SELECT id, storey_type, design_type, province_id, name, code, class_rooms, student_toilets, covered_area, progress, planned,
	                ((progress / 100) * covered_area) AS _Weightage,
					((planned / 100) * covered_area) AS _PlannedWeightage
	         FROM tbl_schools
			 $sConditions
			 ORDER BY province_id, code, name";
	$objDb->query($sSQL);

	$iCount             = $objDb->getCount( );
	$iLastProvince      = 0;
	$iRow               = 4;
	$fOvrallCoveredArea = 0;
	$fOverallWeightage  = 0;
	$fOverallPlanned    = 0;

	for ($i = 0; $i < $iCount; $i ++, $iRow ++)
	{
		$iSchool           = $objDb->getField($i, "id");
		$sSchool           = $objDb->getField($i, "name");
		$sCode             = $objDb->getField($i, "code");
		$iProvince         = $objDb->getField($i, "province_id");
		$iClassRooms       = $objDb->getField($i, "class_rooms");
		$iToilets          = $objDb->getField($i, "student_toilets");
		$fCoveredArea      = $objDb->getField($i, "covered_area");
		$fProgress         = $objDb->getField($i, "progress");
		$fPlanned          = $objDb->getField($i, "planned");
		$fWeightage        = $objDb->getField($i, "_Weightage");
		$fPlannedWeightage = $objDb->getField($i, "_PlannedWeightage");
		$sStoreyType       = $objDb->getField($i, "storey_type");
		$sDesignType       = $objDb->getField($i, "design_type");		


		if ($iProvince != $iLastProvince)
		{
			$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $iRow, getDbValue("name", "tbl_provinces", "id='$iProvince'"));
			$objPhpExcel->getActiveSheet()->mergeCells("B{$iRow}:R{$iRow}");
			$objPhpExcel->getActiveSheet()->getRowDimension($iRow)->setRowHeight(30);

			$objPhpExcel->getActiveSheet()->getStyle("A{$iRow}:R{$iRow}")->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => (($iProvince == 1) ? '2db084' : '66497d'))),
												 									            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size' => 14)) );

			$iRow ++;
		}


		$sSchoolType              = (($sDesignType == "B") ? "B" : $sStoreyType);
		$iStatementOfRequirements = 4;
		$iCommencementLetter      = 5;
		$iSitePocessionLetter     = 6;
		$iContractDocumentation   = 2;
		$iMileStones              = 3;
		$sThirdStage              = $sSsSubStages[3];
		
		if ($sSchoolType == "D")
		{
			$iStatementOfRequirements = 119;
			$iCommencementLetter      = 120;
			$iSitePocessionLetter     = 121;
			$iContractDocumentation   = 117;
			$iMileStones              = 118;
			$sThirdStage              = $sDsSubStages[3];
		}
		
		else if ($sSchoolType == "B")
		{
			$iStatementOfRequirements = 234;
			$iCommencementLetter      = 235;
			$iSitePocessionLetter     = 236;
			$iContractDocumentation   = 232;
			$iMileStones              = 233;
			$sThirdStage              = $sBsSubStages[3];
		}
		
		
		$sStatementOfRequirements              = getDbValue("`date`", "tbl_inspections", "school_id='$iSchool' AND stage_id='$iStatementOfRequirements'", "`date` DESC");
		$sCommencementLetter                   = getDbValue("`date`", "tbl_inspections", "school_id='$iSchool' AND stage_id='$iCommencementLetter'", "`date` DESC");
		$sSitePocessionLetter                  = getDbValue("`date`", "tbl_inspections", "school_id='$iSchool' AND stage_id='$iSitePocessionLetter'", "`date` DESC");
		$sPreMobilizationApprovals             = (($sStatementOfRequirements != "" && $sCommencementLetter != "" && $sSitePocessionLetter != "") ? "Y" : "N");
		$sPlannedStartDate                     = "";
		$sPlannedEndDate                       = "";
		$sContractDocumentationStages          = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iContractDocumentation'");
		$sPreMobilizationContractDocumentation = getDbValue("MAX(`date`)", "tbl_inspections", "school_id='$iSchool' AND FIND_IN_SET(stage_id, '$sContractDocumentationStages')", "`date` DESC");
		$iLastStage                            = getDbValue("s.id", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.weightage>'0' AND i.school_id='$iSchool' AND FIND_IN_SET(i.stage_id, '$sThirdStage') AND i.status='P' AND i.stage_completed='Y'", "i.date DESC");
		$sMilestoneStages                      = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iMileStones'");
		$iMilestoneStages                      = @explode(",", $sMilestoneStages);
		$iLastMilestone                        = 0;

		
		if ($iLastStage > 0)
		{
			$iCurrentMilestone = getDbValue("parent_id", "tbl_stages", "id='$iLastStage'");

			if (!@in_array($iCurrentMilestone, $iMilestoneStages))
				$iCurrentMilestone = getDbValue("parent_id", "tbl_stages", "id='$iCurrentMilestone'");


			if (@in_array($iCurrentMilestone, $iMilestoneStages))
			{
				$iCurrentPosition = getDbValue("position", "tbl_stages", "id='$iCurrentMilestone'");
				$iLastMilestone   = getDbValue("id", "tbl_stages", "FIND_IN_SET(id, '$sMilestoneStages') AND position<'$iCurrentPosition'", "position DESC");
			}
		}


		$sSQL = "SELECT contract_id, start_date, end_date FROM tbl_contract_schedules WHERE school_id='$iSchool' ORDER BY id DESC LIMIT 1";
		$objDb2->query($sSQL);

		if ($objDb2->getCount( ) == 1)
		{
			$iContract         = $objDb2->getField(0, "contract_id");
			$sPlannedStartDate = $objDb2->getField(0, "start_date");
			$sPlannedEndDate   = $objDb2->getField(0, "end_date");
		}


		$iColumn = 1;

		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, " {$sCode}");
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, $sSchool);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, formatDate($sPlannedStartDate, $_SESSION['DateFormat']));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, formatDate($sPlannedEndDate, $_SESSION['DateFormat']));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, " {$iClassRooms}");
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, " {$iToilets}");
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, (" ".formatNumber($fCoveredArea, false)));


		$objImage = @imagecreatetruecolor(@round($fPlanned * 3), 32);
		$objColor = @imagecolorallocate($objImage, 29, 112, 163);

		@imagefilledrectangle($objImage, 0, 0, @round($fPlanned * 3), 32, $objColor);


		$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
		$objDrawing->setName('Planned');
		$objDrawing->setImageResource($objImage);
		$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
		$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
		$objDrawing->setHeight(32);
		$objDrawing->setOffsetX(5);
		$objDrawing->setOffsetY(4);
		$objDrawing->setCoordinates(getExcelCol($iColumn ++).$iRow);
		$objDrawing->setWorksheet($objPhpExcel->getActiveSheet());


		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, (" ".formatNumber($fPlanned, false)."%"));

		
		$objImage = @imagecreatetruecolor(@round($fProgress * 3), 32);
		$objColor = @imagecolorallocate($objImage, 240, 144, 30);

		@imagefilledrectangle($objImage, 0, 0, @round($fProgress * 3), 32, $objColor);


		$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
		$objDrawing->setName('Progress');
		$objDrawing->setImageResource($objImage);
		$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
		$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
		$objDrawing->setHeight(32);
		$objDrawing->setOffsetX(5);
		$objDrawing->setOffsetY(4);
		$objDrawing->setCoordinates(getExcelCol($iColumn ++).$iRow);
		$objDrawing->setWorksheet($objPhpExcel->getActiveSheet());


		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, (" ".formatNumber($fProgress, false)."%"));


		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Status');
		$objDrawing->setPath("images/icons/".(($iInspections > 0) ? "on-time" : (($iLastStage > 0) ? "delayed" : "not-started")).".png");
		$objDrawing->setWidth(20);
		$objDrawing->setOffsetX(20);
		$objDrawing->setOffsetY(10);
		$objDrawing->setCoordinates(getExcelCol($iColumn ++).$iRow);
		$objDrawing->setWorksheet($objPhpExcel->getActiveSheet());


		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Status');
		$objDrawing->setPath("images/icons/".(($sPreMobilizationApprovals == "Y") ? "completed" : "incomplete").".png");
		$objDrawing->setWidth(20);
		$objDrawing->setOffsetX(105);
		$objDrawing->setOffsetY(10);
		$objDrawing->setCoordinates(getExcelCol($iColumn ++).$iRow);
		$objDrawing->setWorksheet($objPhpExcel->getActiveSheet());


		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, formatDate($sStatementOfRequirements, $_SESSION['DateFormat']));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, formatDate($sCommencementLetter, $_SESSION['DateFormat']));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, formatDate($sSitePocessionLetter, $_SESSION['DateFormat']));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, formatDate($sPreMobilizationContractDocumentation, $_SESSION['DateFormat']));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, (($iLastMilestone > 0) ? $sAllStagesList[$iLastMilestone] : ""));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($iColumn++, $iRow, (($iLastStage > 0) ? $sAllStagesList[$iLastStage] : ""));

		$objPhpExcel->getActiveSheet()->getRowDimension($iRow)->setRowHeight(30);
		$objPhpExcel->getActiveSheet()->getStyle("A{$iRow}:T{$iRow}")->applyFromArray(array('font' => array('bold' => false, 'color' => array('rgb' => '444444'), 'size' => 13)) );


		$fOvrallCoveredArea += $fCoveredArea;
		$fOverallWeightage  += $fWeightage;
		$fOverallPlanned    += $fPlannedWeightage;
		$iLastProvince       = $iProvince;
	}


	$fPlannedProgress = @round(($fOverallPlanned / $fOvrallCoveredArea) * 100);
	$fOverallProgress = @round(($fOverallWeightage / $fOvrallCoveredArea) * 100);


	$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $iRow, ("No of Schools: ".formatNumber($iCount, false)));
	$objPhpExcel->getActiveSheet()->mergeCells("B{$iRow}:F{$iRow}");

	
	$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $iRow, "Overall Progress");

	$objImage = @imagecreatetruecolor(@round($fPlannedProgress * 3), 32);
	$objColor = @imagecolorallocate($objImage, 29, 112, 163);

	@imagefilledrectangle($objImage, 0, 0, @round($fPlannedProgress * 3), 32, $objColor);


	$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
	$objDrawing->setName('Progress');
	$objDrawing->setImageResource($objImage);
	$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
	$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
	$objDrawing->setHeight(32);
	$objDrawing->setOffsetX(5);
	$objDrawing->setOffsetY(4);
	$objDrawing->setCoordinates(getExcelCol(8).$iRow);
	$objDrawing->setWorksheet($objPhpExcel->getActiveSheet());

	$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $iRow, (" ".formatNumber($fPlannedProgress, false)."%"));

	
//	$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $iRow, "Overall Progress");

	$objImage = @imagecreatetruecolor(@round($fOverallProgress * 3), 32);
	$objColor = @imagecolorallocate($objImage, 240, 144, 30);

	@imagefilledrectangle($objImage, 0, 0, @round($fOverallProgress * 3), 32, $objColor);


	$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
	$objDrawing->setName('Progress');
	$objDrawing->setImageResource($objImage);
	$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
	$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
	$objDrawing->setHeight(32);
	$objDrawing->setOffsetX(5);
	$objDrawing->setOffsetY(4);
	$objDrawing->setCoordinates(getExcelCol(10).$iRow);
	$objDrawing->setWorksheet($objPhpExcel->getActiveSheet());

	$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $iRow, (" ".formatNumber($fOverallProgress, false)."%"));

	$objPhpExcel->getActiveSheet()->getRowDimension($iRow)->setRowHeight(30);
	$objPhpExcel->getActiveSheet()->getStyle("A{$iRow}:T{$iRow}")->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '777777')),
																						'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size' => 14)) );


	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Project Tracker &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Project Tracker - Planned");



	$sExcelFile = "Project Tracker - Planned.xlsx";

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=\"{$sExcelFile}\"");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');
	$objWriter->save("php://output");



	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>