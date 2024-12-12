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

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );
	

        
	$objPhpExcel = new PHPExcel( );
	
	$objPhpExcel->getProperties()->setCreator($_SESSION["SiteTitle"])
								 ->setLastModifiedBy($_SESSION["SiteTitle"])
								 ->setTitle("Inspections")
								 ->setSubject("Active Schools")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Reports");

	$objPhpExcel->setActiveSheetIndex(0);

	
	$sHeadingStyle = array('font' => array('bold' => true, 'size' => 12),
						   'fill'       => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E6E6E6')),
						   'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
						   'borders'   => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)) );
        $sHeadingGreenStyle = array('font' => array('bold' => true, 'size' => 12),
						   'fill'       => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '6EA828')),
						   'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
						   'borders'   => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)) );
        
        $sHeadingYellowStyle = array('font' => array('bold' => true, 'size' => 12),
						   'fill'       => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFD400')),
						   'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
						   'borders'   => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)) );

	$sBorderStyle = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
					  'borders'  => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
												 'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										 'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										 'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	
	$sTotalStyle 	= array('font'       => array('bold' => false, 'size' => 12),
					 'fill'       => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'CCCCCC')),
					 'alignment'  => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
					 'borders'    => array('top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
									   
     
	 
	$sKeywords = IO::strValue("Keywords");
	$iPackage  = IO::intValue("Package");
	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");
	$sStatus   = IO::strValue("Status");
	

	$sConditions = "WHERE s.status='A' AND s.dropped!='Y' AND s.adopted='Y' AND s.qualified='Y' AND FIND_IN_SET(s.province_id, '{$_SESSION['AdminProvinces']}') AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";


	if ($sKeywords != "")
	{
		$sKeywords    = str_replace(" ", "%", $sKeywords);

		$sConditions .= " AND (s.name LIKE '%{$sKeywords}%' OR s.code LIKE '{$sKeywords}' OR s.address LIKE '%{$sKeywords}%') ";
	}

	if ($iPackage > 0 || $iProvince > 0)
	{
		$sConditions .= " AND (";

		if ($iPackage > 0)
		{
			$sSchools = getDbValue("schools", "tbl_packages", "id='$iPackage'");

			$sConditions .= " FIND_IN_SET(s.id, '$sSchools') ";
		}

		if ($iPackage > 0 && $iProvince > 0)
			$sConditions .= " OR ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " (";

		if ($iProvince > 0)
			$sConditions .= " s.province_id='$iProvince' ";

		if ($iDistrict > 0)
			$sConditions .= " AND s.district_id='$iDistrict' ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " )";

		$sConditions .= ")";
	}


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

	

	$sSubConditions = "";

	if ($_SESSION["AdminSchools"] != "")
		$sSubConditions = " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";


	//if ($sStatus == "Active")
	//	$sConditions .= " AND s.id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions) ";

	//else if ($sStatus == "InActive")
	//	$sConditions .= " AND s.id NOT IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions) ";

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
			$sConditions    .= " AND FIND_IN_SET(s.id, '$sDelayedSchools') ";
		}

		if ($sStatus == "OnTime")
		{
			$sOnTimeSchools = @implode(",", $iOnTimeSchools);
			$sConditions   .= " AND FIND_IN_SET(s.id, '$sOnTimeSchools') ";
		}
	}

	
        $sActiveSchoolsList = getList("tbl_inspections", "DISTINCT(school_id)", "school_id", "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages)");
        $sCompletionStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id>'0' AND name='Finishing & Demobilization'");

		
	$sSQL = "SELECT s.id, s.name, s.code, s.completed, s.province_id, s.storey_type, s.design_type, s.work_type, 
                    s.blocks, s.class_rooms, s.student_toilets, s.staff_rooms, s.staff_toilets, s.science_labs, 
                    s.it_labs, s.exam_halls, s.library, s.clerk_offices, s.principal_office, s.parking_stand,
                    s.chowkidar_hut, s.soakage_pit, s.water_supply, s.stores,
				    cs.start_date, cs.end_date,
                         (Select name from tbl_districts where s.district_id=id) as _District           
			 FROM tbl_schools s
					LEFT JOIN tbl_contract_schedules cs
					ON s.id = cs.school_id
			 $sConditions
			 ORDER BY s.province_id, s.name";
        $objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	
	$iLastProvince         = 0;
	$iRow                  = -3;
        
	$iTotalBlocks          = 0;
	$iTotalClassRooms      = 0;
	$iTotalStudentToilets  = 0;
	$iTotalStaffRooms      = 0;
	$iTotalStaffToilets    = 0;
	$iTotalScienceLabs     = 0;
	$iTotalITLabs          = 0;
	$iTotalExamHalls       = 0;
	$iTotalLibrary         = 0;
	$iTotalClerkOffice     = 0;
	$iTotalPrincipalOffice = 0;
	$iTotalParkingStand    = 0;
	$iTotalChowkidarHut    = 0;
	$iTotalSoakagePit      = 0;
	$iTotalWaterSupply     = 0;
	$iTotalStores          = 0;
	
	
	for ($i = 0; $i < $iCount ; $i ++)
	{                       
		$iSchool         = $objDb->getField($i, "id");
		$sSchool         = $objDb->getField($i, "name");
		$sCode           = $objDb->getField($i, "code");
                $sDistrict       = $objDb->getField($i, "_District");
                $sCompleted      = $objDb->getField($i, "completed");
		$iProvince       = $objDb->getField($i, "province_id");
		$sStoreyType     = $objDb->getField($i, "storey_type");
		$sDesignType     = $objDb->getField($i, "design_type");		
		$sWorkType       = $objDb->getField($i, "work_type");
		$sStartDate      = $objDb->getField($i, "start_date");
		$sEndDate        = $objDb->getField($i, "end_date");
		$iBlocks         = $objDb->getField($i, "blocks");
		$iClassRooms     = $objDb->getField($i, "class_rooms");
		$iStudentToilets = $objDb->getField($i, "student_toilets");
		$iStaffRooms     = $objDb->getField($i, "staff_rooms");
		$iStaffToilets   = $objDb->getField($i, "staff_toilets");
		$iScienceLabs    = $objDb->getField($i, "science_labs");
		$iITLabs         = $objDb->getField($i, "it_labs");
		$iExamHalls      = $objDb->getField($i, "exam_halls");
		$iLibrary        = $objDb->getField($i, "library");
		$iClerkOffice    = $objDb->getField($i, "clerk_offices");
		$iPrincipalOffice= $objDb->getField($i, "principal_office");
		$iParkingStand   = $objDb->getField($i, "parking_stand");
		$iChowkidarHut   = $objDb->getField($i, "chowkidar_hut");
		$iSoakagePit     = $objDb->getField($i, "soakage_pit");
		$iWaterSupply    = $objDb->getField($i, "water_supply");
		$iStores         = $objDb->getField($i, "stores");
		
                
		if ($iLastProvince != $iProvince)
		{
			if ($iRow != -3)
			{
				$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "Totals");
				$objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", $iTotalBlocks);
				$objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", $iTotalClassRooms);
				$objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", $iTotalStaffRooms);
				$objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", $iTotalStudentToilets);
				$objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", $iTotalStaffToilets);
				$objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", $iTotalScienceLabs);
				$objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", $iTotalITLabs);
				$objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", $iTotalExamHalls);
				$objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", $iTotalLibrary);
				$objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", $iTotalClerkOffice);
				$objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", $iTotalPrincipalOffice);
				$objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", $iTotalParkingStand);
				$objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", $iTotalChowkidarHut);
				$objPhpExcel->getActiveSheet()->setCellValue("Y{$iRow}", $iTotalSoakagePit);
				$objPhpExcel->getActiveSheet()->setCellValue("Z{$iRow}", $iTotalWaterSupply);
				$objPhpExcel->getActiveSheet()->setCellValue("AA{$iRow}", $iTotalStores);
	
				//for ($j = 0; $j < 27; $j ++)
				//$objPhpExcel->getActiveSheet()->duplicateStyleArray($sTotalStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
                                $objPhpExcel->getActiveSheet()->duplicateStyleArray($sTotalStyle, ("A{$iRow}:AB{$iRow}"));	
			
				$iTotalBlocks          = 0;
				$iTotalClassRooms      = 0;
				$iTotalStudentToilets  = 0;
				$iTotalStaffRooms      = 0;
				$iTotalStaffToilets    = 0;
				$iTotalScienceLabs     = 0;
				$iTotalITLabs          = 0;
				$iTotalExamHalls       = 0;
				$iTotalLibrary         = 0;
				$iTotalClerkOffice     = 0;
				$iTotalPrincipalOffice = 0;
				$iTotalParkingStand    = 0;
				$iTotalChowkidarHut    = 0;
				$iTotalSoakagePit      = 0;
				$iTotalWaterSupply     = 0;
				$iTotalStores          = 0;				
			}

			
			$iRow += 4;
		
			$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", (getDbValue("name", "tbl_provinces", "id='$iProvince'")." Adopted Schools List"));
			$objPhpExcel->getActiveSheet()->getStyle("A{$iRow}")->getFont()->setSize(16);
			
			$iRow ++;

			$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "School Name");
			$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "EMIS Code");
                        $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "District");
			$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "Scope of Work");
			$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", "Planned Start Date");
			$objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", "Planned End Date");
			$objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", "Actual Start Date");
			$objPhpExcel->getActiveSheet()->setCellValue("H{$iRow}", "Actual End Date");
			$objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", "Planned Completion Date");
			$objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", "Projected Completion Date");			
			$objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", "Final Contract");
			$objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", "Blocks");
			$objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", "Class Rooms");
			$objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", "Staff Rooms");
			$objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", "Student Toilets");
			$objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", "Staff Toilets");
			$objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", "Science Labs");
			$objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", "IT Labs");
			$objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", "Exam Halls");
			$objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", "Library");
			$objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", "Clerk Offices");
			$objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", "Princial Office");
			$objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", "Parking Stand");
			$objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", "Chowkidar Hut");
			$objPhpExcel->getActiveSheet()->setCellValue("Y{$iRow}", "Soakge Pit");
			$objPhpExcel->getActiveSheet()->setCellValue("Z{$iRow}", "Water Supply");
			$objPhpExcel->getActiveSheet()->setCellValue("AA{$iRow}", "Stores");
                        $objPhpExcel->getActiveSheet()->setCellValue("AB{$iRow}", "Status");
			

			
                        $objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingStyle, ("A{$iRow}:AB{$iRow}"));	
			
			$iRow ++;
		}

		
		$iTotalBlocks          += $iBlocks;
		$iTotalClassRooms      += $iClassRooms;
		$iTotalStudentToilets  += $iStudentToilets;
		$iTotalStaffRooms      += $iStaffRooms;
		$iTotalStaffToilets    += $iStaffToilets;
		$iTotalScienceLabs     += $iScienceLabs;
		$iTotalITLabs          += $iITLabs;
		$iTotalExamHalls       += $iExamHalls;
		$iTotalLibrary         += $iLibrary;
		$iTotalClerkOffice     += $iClerkOffice;
		$iTotalPrincipalOffice += $iPrincipalOffice;
		$iTotalParkingStand    += $iParkingStand;
		$iTotalChowkidarHut    += $iChowkidarHut;
		$iTotalSoakagePit      += $iSoakagePit;
		$iTotalWaterSupply     += $iWaterSupply;
		$iTotalStores          += $iStores;

		
		$sActualStartDate         = getDbValue("MIN(i.date)", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.name LIKE '%Commencement Letter%' AND i.school_id = '$iSchool'", "i.date DESC");
                $sActualEndDate           = getDbValue("MAX(i.date)", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.name LIKE '%Finishing & Demobilization%' AND i.school_id = '$iSchool' AND i.stage_completed='Y' AND i.status='P'", "i.date DESC");
		
		$sSchoolType              = (($sDesignType == "B") ? "B" : $sStoreyType);
		$iContract                = getDbValue("id", "tbl_contracts", "status='A' AND FIND_IN_SET('$iSchool', schools)", "id DESC");
		$sPlannedCompletionDate   = getDbValue("end_date", "tbl_contract_schedules", "school_id='$iSchool' AND contract_id='$iContract'");
		$iLastCompletedStage      = getDbValue("i.stage_id", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND i.status='P' AND i.stage_completed='Y' AND s.weightage>'0' AND i.school_id='$iSchool'", "s.position DESC");		
		$sProjectedCompletionDate = "";
		
		if ($sPlannedCompletionDate != "" && $sPlannedCompletionDate != "0000-00-00" && $iLastCompletedStage > 0)
		{
			$sLastStageCompletionDate = getDbValue("MAX(date)", "tbl_inspections", "stage_id='$iLastCompletedStage' AND status='P' AND stage_completed='Y' AND school_id='$iSchool'");	
			$sLastStagePlannedDate    = getDbValue("IF(csd.end_date='',csd.start_date, csd.end_date)", "tbl_contract_schedules cs, tbl_contract_schedule_details csd", "cs.id=csd.schedule_id AND cs.school_id='$iSchool' AND cs.contract_id='$iContract' AND csd.stage_id='$iLastCompletedStage'");

			if ($sLastStagePlannedDate != "" && $sLastStagePlannedDate != "0000-00-00")
				$sProjectedCompletionDate = date("Y-m-d", (time( ) + (strtotime($sPlannedCompletionDate) - strtotime($sLastStagePlannedDate))));
		}

                
		$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", $sSchool);
		$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", $sCode);
                $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", $sDistrict);
		$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", (($sWorkType == 'B') ? 'New Construction & Rehabilitation' : ($sWorkType == 'N' ? 'New Construction' : 'Rehabilitation Only')));
		$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", formatDate($sStartDate, $_SESSION['DateFormat']));
		$objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", formatDate($sEndDate, $_SESSION['DateFormat']));
                $objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", formatDate($sActualStartDate, $_SESSION['DateFormat']));
		$objPhpExcel->getActiveSheet()->setCellValue("H{$iRow}", formatDate($sActualEndDate, $_SESSION['DateFormat']));
                $objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", formatDate($sPlannedCompletionDate, $_SESSION['DateFormat']));
		$objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", formatDate($sProjectedCompletionDate, $_SESSION['DateFormat']));
		$objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", 'N/A');
		$objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", $iBlocks);
		$objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", $iClassRooms);
		$objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", $iStaffRooms);
		$objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", $iStudentToilets);
		$objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", $iStaffToilets);
		$objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", $iScienceLabs);
		$objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", $iITLabs);
		$objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", $iExamHalls);
		$objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", $iLibrary);
		$objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", $iClerkOffice);
		$objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", $iPrincipalOffice);
		$objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", $iParkingStand);
		$objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", $iChowkidarHut);
		$objPhpExcel->getActiveSheet()->setCellValue("Y{$iRow}", $iSoakagePit);
		$objPhpExcel->getActiveSheet()->setCellValue("Z{$iRow}", $iWaterSupply);
		$objPhpExcel->getActiveSheet()->setCellValue("AA{$iRow}", $iStores);
                $objPhpExcel->getActiveSheet()->setCellValue("AB{$iRow}", (in_array($iSchool, $sActiveSchoolsList)?'Active':'Non-Active'));


		if($sCompleted == 'Y')
                    $objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingGreenStyle, ("A{$iRow}:AB{$iRow}"));	
                else if (in_array($iSchool, $sActiveSchoolsList))
                    $objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingYellowStyle, ("A{$iRow}:AB{$iRow}"));	
                else
                    $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, ("A{$iRow}:AB{$iRow}"));	
                
		$iRow ++;
                $iLastProvince = $iProvince;
                
		if (($i + 1) == $iCount)
		{
			$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "Totals");
			$objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", $iTotalBlocks);
			$objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", $iTotalClassRooms);
			$objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", $iTotalStaffRooms);
			$objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", $iTotalStudentToilets);
			$objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", $iTotalStaffToilets);
			$objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", $iTotalScienceLabs);
			$objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", $iTotalITLabs);
			$objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", $iTotalExamHalls);
			$objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", $iTotalLibrary);
			$objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", $iTotalClerkOffice);
			$objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", $iTotalPrincipalOffice);
			$objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", $iTotalParkingStand);
			$objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", $iTotalChowkidarHut);
			$objPhpExcel->getActiveSheet()->setCellValue("Y{$iRow}", $iTotalSoakagePit);
			$objPhpExcel->getActiveSheet()->setCellValue("Z{$iRow}", $iTotalWaterSupply);
			$objPhpExcel->getActiveSheet()->setCellValue("AA{$iRow}", $iTotalStores);
			
			//for ($j = 0; $j < 26; $j ++)
			//$objPhpExcel->getActiveSheet()->duplicateStyleArray($sTotalStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
                        $objPhpExcel->getActiveSheet()->duplicateStyleArray($sTotalStyle, ("A{$iRow}:AB{$iRow}"));	
		}
	}
        
		
   	$objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(50);
	$objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(40);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("F")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("G")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("H")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("I")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("J")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("K")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("L")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("M")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("N")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("O")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("P")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("R")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("S")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("T")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("U")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("V")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("W")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("X")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("Z")->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AA")->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AB")->setWidth(20);


	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Adopted Schools List &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Adopted Schools List");
	$objPhpExcel->setActiveSheetIndex(0);
	
	
	$sExcelFile = "Adopted Schools.xlsx";

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=\"{$sExcelFile}\"");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');
	$objWriter->save("php://output");

	
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );      
?>