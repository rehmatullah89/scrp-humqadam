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
	@require_once("{$sRootDir}requires/PHPExcel.php");

        //ini_set('display_errors', 1);
        //error_reporting(E_ALL);
        
     if (!@strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))
         die("ERROR: Invalid Request");


	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	$sKeywords   = IO::strValue("Keywords");
	$iDistrict   = IO::intValue("District");
	$iType       = IO::intValue("Type");
	$sWorkType   = IO::strValue("WorkType");
        $sStatuses   = IO::strValue("Status");
	$sDesignType = IO::strValue("DesignType");
	$sStoreyType = IO::strValue("StoreyType");

	
	$sConditions = " WHERE s.id = sb.school_id AND s.id>'0' ";

	if ($sKeywords != "")
	{
		$sStatus = ((strtolower($sKeywords) == "active") ? "A" : ((strtolower($sKeywords) == "in-active") ? "I" : ""));

		$sConditions .= " AND ( s.name LIKE '%{$sKeywords}%' OR
		                        s.code LIKE '%{$sKeywords}%' OR
		                        s.address LIKE '%{$sKeywords}%'";

		if ($sStatus != "")
			$sConditions .= " OR s.status='$sStatus' ";

		$sConditions .= " ) ";
	}


	if ($iDistrict > 0)
		$sConditions .= " AND s.district_id='$iDistrict' ";

	if ($iType > 0)
		$sConditions .= " AND s.type_id='$iType' ";

	if ($sWorkType != "")
		$sConditions .= " AND s.work_type='$sWorkType' ";
	
	if ($sDesignType != "")
		$sConditions .= " AND s.design_type='$sDesignType' ";
	
	if ($sStoreyType != "")
		$sConditions .= " AND s.storey_type='$sStoreyType' ";

        if ($sStatuses != "")
	{
		if ($sStatuses == "active")
		{
			$iMilestoneStageS = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='S'", "position DESC");
			$iMilestoneStageD = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='D'", "position DESC");
			$iMilestoneStageT = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='T'", "position DESC");
			$iMilestoneStageB = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='B'", "position DESC");
			$iMilestoneStages = array( );
			
			$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND (`type`='R' OR (`type`='S' AND position>'$iMilestoneStageS') OR (`type`='D' AND position>'$iMilestoneStageD') OR (`type`='T' AND position>'$iMilestoneStageT') OR (`type`='B' AND position>'$iMilestoneStageB')) ORDER BY position";
			$objDb->query($sSQL);
			
			$iCount = $objDb->getCount( );
			
			for ($i = 0; $i < $iCount; $i ++)
				$iMilestoneStages[] = $objDb->getField($i, 0);
				
			$sMilestoneStages = @implode(",", $iMilestoneStages);			

			
			$sConditions .= " AND s.status='A' AND s.dropped!='Y' AND s.qualified='Y' AND s.completed!='Y' AND s.id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE stage_id IN ($sMilestoneStages) ";
			
			if ($iDistrict > 0)
				$sConditions .= " AND s.district_id='$iDistrict' ";
			
			else
				$sConditions .= " AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') ";
			
			if ($_SESSION["AdminSchools"] != "")
				$sConditions .= " AND FIND_IN_SET(s.school_id, '{$_SESSION['AdminSchools']}') ";
			
			$sConditions .= ")";
		}
		
		else if ($sStatuses == "completed")
			$sConditions .= " AND s.status='A' AND s.dropped!='Y' AND s.qualified='Y' AND s.completed='Y' ";
		
		else if ($sStatuses == "qualified")
			$sConditions .= " AND s.status='A' AND s.dropped!='Y' AND s.qualified='Y' ";
		
		else
			$sConditions .= " AND s.status='A' AND s.{$sStatuses}='Y' ";
	}        

	$objPhpExcel = new PHPExcel( );
        
        $objReader   = PHPExcel_IOFactory::createReader('Excel2007');
	$objPhpExcel = $objReader->load("{$sRootDir}templates/Schools.xlsx");

	$objPhpExcel->getProperties()->setCreator($_SESSION["SiteTitle"])
								 ->setLastModifiedBy($_SESSION["SiteTitle"])
								 ->setTitle("Schools")
								 ->setSubject("Schools List")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Reports");

	$objPhpExcel->setActiveSheetIndex(0);

	$sBorderStyle = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
						  'borders'  => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											 'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											 'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											 'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));


	$iRow = 5;

	$sTypesList     = getList("tbl_school_types", "id", "`type`");
	$sProvincesList = getList("tbl_provinces", "id", "name");
	$sDistrictsList = getList("tbl_districts", "id", "name");

            
        $TotalNewClassRooms         = 0;
        $TotalRehabClassRooms       = 0;
        $TotalNewStudentToilets     = 0;
        $TotalRehabStudentToilets   = 0;
        $TotalNewStaffRooms         = 0;
        $TotalRehabStaffRooms       = 0;
        $TotalNewStaffToilets       = 0;
        $TotalRehabStaffToilets     = 0;
        $TotalNewScienceLabs        = 0;
        $TotalRehabScienceLabs      = 0;
        $TotalNewITLabs             = 0;
        $TotalRehabITLabs           = 0;
        $TotalNewExamHalls          = 0;
        $TotalRehabExamHalls        = 0;
        $TotalNewLibraries          = 0;
        $TotalRehabLibraries        = 0;
        $TotalNewClerkOffices       = 0;
        $TotalRehabClerkOffices     = 0;
        $TotalNewPrincipalOffice    = 0;
        $TotalRehabPrincipalOffice  = 0;
        $TotalNewParkingStand       = 0;
        $TotalRehabParkingStand     = 0;
        $TotalNewChowkidarHut       = 0;
        $TotalRehabChowkidarHut     = 0;
        $TotalNewSoakagePit         = 0;
        $TotalRehabSoakagePit       = 0;
        $TotalNewWaterSupply        = 0;
        $TotalRehabWaterSupply      = 0;
        

	$sSQL = "SELECT s.id, s.name, s.code, s.blocks, s.type_id, s.students, s.storey_type, s.design_type, s.covered_area, s.dropped, s.district_id, s.province_id, s.address, s.cost, s.latitude, s.longitude,  
                    SUM(IF(sb.work_type='N', sb.class_rooms, '0')) AS _NewClassrooms, 
                    SUM(IF(sb.work_type='R', sb.class_rooms, '0')) AS _RehabClassrooms, 
                    SUM(IF(sb.work_type='N', sb.student_toilets, '0')) AS _NewStudentToilets, 
                    SUM(IF(sb.work_type='R', sb.student_toilets, '0')) AS _RehabStudentToilets, 
                    SUM(IF(sb.work_type='N', sb.staff_rooms, '0')) AS _NewStaffRooms, 
                    SUM(IF(sb.work_type='R', sb.staff_rooms, '0')) AS _RehabStaffRooms, 
                    SUM(IF(sb.work_type='N', sb.staff_toilets, '0')) AS _NewStaffToilets, 
                    SUM(IF(sb.work_type='R', sb.staff_toilets, '0')) AS _RehabStaffToilets, 
                    SUM(IF(sb.work_type='N', sb.science_labs, '0')) AS _NewScienceLabs, 
                    SUM(IF(sb.work_type='R', sb.science_labs, '0')) AS _RehabScienceLabs, 
                    SUM(IF(sb.work_type='N', sb.it_labs, '0')) AS _NewITLabs, 
                    SUM(IF(sb.work_type='R', sb.it_labs, '0')) AS _RehabITLabs, 
                    SUM(IF(sb.work_type='N', sb.exam_halls, '0')) AS _NewExamHalls, 
                    SUM(IF(sb.work_type='R', sb.exam_halls, '0')) AS _RehabExamHalls, 
                    SUM(IF(sb.work_type='N', sb.library, '0')) AS _NewLibraries, 
                    SUM(IF(sb.work_type='R', sb.library, '0')) AS _RehabLibraries, 
                    SUM(IF(sb.work_type='N', sb.clerk_offices, '0')) AS _NewClerkOffices, 
                    SUM(IF(sb.work_type='R', sb.clerk_offices, '0')) AS _RehabClerkOffices, 
                    SUM(IF(sb.work_type='N', sb.principal_office, '0')) AS _NewPrincipalOffice, 
                    SUM(IF(sb.work_type='R', sb.principal_office, '0')) AS _RehabPrincipalOffice,
                    SUM(IF(sb.work_type='N', sb.parking_stand, '0')) AS _NewParkingStand, 
                    SUM(IF(sb.work_type='R', sb.parking_stand, '0')) AS _RehabParkingStand,
                    SUM(IF(sb.work_type='N', sb.chowkidar_hut, '0')) AS _NewChowkidarHut, 
                    SUM(IF(sb.work_type='R', sb.chowkidar_hut, '0')) AS _RehabChowkidarHut,
                    SUM(IF(sb.work_type='N', sb.soakage_pit, '0')) AS _NewSoakagePit, 
                    SUM(IF(sb.work_type='R', sb.soakage_pit, '0')) AS _RehabSoakagePit,
                    SUM(IF(sb.work_type='N', sb.water_supply, '0')) AS _NewWaterSupply, 
                    SUM(IF(sb.work_type='R', sb.water_supply, '0')) AS _RehabWaterSupply,
                    sb.work_type  
                    FROM tbl_schools s, tbl_school_blocks sb
                 $sConditions 
                 Group By sb.school_id    
                 ORDER BY s.id";
        
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++, $iRow++)
	{
		$sName                  = $objDb->getField($i, "name");
		$sCode                  = $objDb->getField($i, "code");
                $iBlocks                = $objDb->getField($i, "blocks");
		$iType                  = $objDb->getField($i, "type_id");
		$iStudents              = $objDb->getField($i, "students");
		$sStoreyType            = $objDb->getField($i, "storey_type");
		$sDesignType            = $objDb->getField($i, "design_type");
		$fCoveredArea           = $objDb->getField($i, "covered_area");
                $iNewClassRooms         = $objDb->getField($i, "_NewClassrooms");
                $iRehabClassRooms       = $objDb->getField($i, "_RehabClassrooms");
                $iNewStudentToilets     = $objDb->getField($i, "_NewStudentToilets");
		$iRehabStudentToilets   = $objDb->getField($i, "_RehabStudentToilets");
                $iNewStaffRooms         = $objDb->getField($i, "_NewStaffRooms");
		$iRehabStaffRooms       = $objDb->getField($i, "_RehabStaffRooms");
                $iNewStaffToilets       = $objDb->getField($i, "_NewStaffToilets");
                $iRehabStaffToilets     = $objDb->getField($i, "_RehabStaffToilets");
                $iNewScienceLabs        = $objDb->getField($i, "_NewScienceLabs");
                $iRehabScienceLabs      = $objDb->getField($i, "_RehabScienceLabs");
		$iNewItLabs             = $objDb->getField($i, "_NewITLabs");
		$iRehabItLabs           = $objDb->getField($i, "_RehabITLabs");
                $iNewExamHalls          = $objDb->getField($i, "_NewExamHalls");
                $iRehabExamHalls        = $objDb->getField($i, "_RehabExamHalls");
                $iNewLibrary            = $objDb->getField($i, "_NewLibraries");
		$iRehabLibrary          = $objDb->getField($i, "_RehabLibraries");
                $iNewClerkOffices       = $objDb->getField($i, "_NewClerkOffices");
                $iRehabClerkOffices     = $objDb->getField($i, "_RehabClerkOffices");
		$iNewPrincipalOffice    = $objDb->getField($i, "_NewPrincipalOffice");
                $iRehabPrincipalOffice  = $objDb->getField($i, "_RehabPrincipalOffice");
		$iNewParkingStand       = $objDb->getField($i, "_NewParkingStand");
                $iRehabParkingStand     = $objDb->getField($i, "_RehabParkingStand");
		$iNewChowkidarHut       = $objDb->getField($i, "_NewChowkidarHut");
		$iRehabChowkidarHut     = $objDb->getField($i, "_RehabChowkidarHut");
                $iNewSoakagePit         = $objDb->getField($i, "_NewSoakagePit");
                $iRehabSoakagePit       = $objDb->getField($i, "_RehabSoakagePit");
		$iNewWaterSupply        = $objDb->getField($i, "_NewWaterSupply");		
		$iRehabWaterSupply      = $objDb->getField($i, "_RehabWaterSupply");		
                $fCost                  = $objDb->getField($i, "cost");
		$iProvince              = $objDb->getField($i, "province_id");
		$iDistrict              = $objDb->getField($i, "district_id");
		$sAddress               = $objDb->getField($i, "address");
		$sLatitude              = $objDb->getField($i, "latitude");
		$sLongitude             = $objDb->getField($i, "longitude");
		$sDropped               = $objDb->getField($i, "dropped");

                $TotalNewClassRooms         += $iNewClassRooms;
                $TotalRehabClassRooms       += $iRehabClassRooms;
                $TotalNewStudentToilets     += $iNewStudentToilets;
                $TotalRehabStudentToilets   += $iRehabStudentToilets;
                $TotalNewStaffRooms         += $iNewStaffRooms;
                $TotalRehabStaffRooms       += $iRehabStaffRooms;
                $TotalNewStaffToilets       += $iNewStaffToilets;
                $TotalRehabStaffToilets     += $iRehabStaffToilets;
                $TotalNewScienceLabs        += $iNewScienceLabs;
                $TotalRehabScienceLabs      += $iRehabScienceLabs;
                $TotalNewITLabs             += $iNewItLabs;
                $TotalRehabITLabs           += $iRehabItLabs;
                $TotalNewExamHalls          += $iNewExamHalls;
                $TotalRehabExamHalls        += $iRehabExamHalls;
                $TotalNewLibraries          += $iNewLibrary;
                $TotalRehabLibraries        += $iRehabLibrary;
                $TotalNewClerkOffices       += $iNewClerkOffices;
                $TotalRehabClerkOffices     += $iRehabClerkOffices;
                $TotalNewPrincipalOffice    += $iNewPrincipalOffice;
                $TotalRehabPrincipalOffice  += $iRehabPrincipalOffice;
                $TotalNewParkingStand       += $iNewParkingStand;
                $TotalRehabParkingStand     += $iRehabParkingStand;
                $TotalNewChowkidarHut       += $iNewChowkidarHut;
                $TotalRehabChowkidarHut     += $iRehabChowkidarHut;
                $TotalNewSoakagePit         += $iNewSoakagePit;
                $TotalRehabSoakagePit       += $iRehabSoakagePit;
                $TotalNewWaterSupply        += $iNewWaterSupply;
                $TotalRehabWaterSupply      += $iRehabWaterSupply;

		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $iRow, $sCode);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $iRow, $sName);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $iRow, $sTypesList[$iType]);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $iRow, $sAddress);
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $iRow, formatNumber($iBlocks, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $iRow, $sDistrictsList[$iDistrict]);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $iRow, $sProvincesList[$iProvince]);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $iRow, $sLatitude);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $iRow, $sLongitude);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $iRow, (($sStoreyType == "S") ? "Single" : "Double"));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $iRow, (($sDesignType == "R") ? "Regular" : "Bespoke"));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $iRow, formatNumber($fCoveredArea));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $iRow, formatNumber($fCost));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $iRow, formatNumber($iStudents, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $iRow, formatNumber($iNewClassRooms, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $iRow, formatNumber($iRehabClassRooms, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $iRow, formatNumber($iNewStudentToilets, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $iRow, formatNumber($iRehabStudentToilets, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $iRow, formatNumber($iNewStaffRooms, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $iRow, formatNumber($iRehabStaffRooms, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $iRow, formatNumber($iNewStaffToilets, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $iRow, formatNumber($iRehabStaffToilets, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $iRow, formatNumber($iNewScienceLabs, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $iRow, formatNumber($iRehabScienceLabs, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(24, $iRow, formatNumber($iNewItLabs, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(25, $iRow, formatNumber($iRehabItLabs, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $iRow, formatNumber($iNewExamHalls, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(27, $iRow, formatNumber($iRehabExamHalls, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(28, $iRow, formatNumber($iNewLibrary, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(29, $iRow, formatNumber($iRehabLibrary, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(30, $iRow, formatNumber($iNewClerkOffices, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(31, $iRow, formatNumber($iRehabClerkOffices, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(32, $iRow, formatNumber($iNewPrincipalOffice, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(33, $iRow, formatNumber($iRehabPrincipalOffice, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(34, $iRow, formatNumber($iNewParkingStand, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(35, $iRow, formatNumber($iRehabParkingStand, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(36, $iRow, formatNumber($iNewChowkidarHut, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(37, $iRow, formatNumber($iRehabChowkidarHut, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(38, $iRow, formatNumber($iNewSoakagePit, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(39, $iRow, formatNumber($iRehabSoakagePit, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(40, $iRow, formatNumber($iNewWaterSupply, false));
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(41, $iRow, formatNumber($iRehabWaterSupply, false));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(42, $iRow, (($sDropped == "Y") ? "Yes" : "No"));


                $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, "A{$iRow}:AQ{$iRow}");
	}
        
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $iRow, "Totals");
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $iRow, formatNumber($TotalNewClassRooms, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $iRow, formatNumber($TotalRehabClassRooms, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $iRow, formatNumber($TotalNewStudentToilets, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $iRow, formatNumber($TotalRehabStudentToilets, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $iRow, formatNumber($TotalNewStaffRooms, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $iRow, formatNumber($TotalRehabStaffRooms, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $iRow, formatNumber($TotalNewStaffToilets, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $iRow, formatNumber($TotalRehabStaffToilets, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $iRow, formatNumber($TotalNewScienceLabs, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $iRow, formatNumber($TotalRehabScienceLabs, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(24, $iRow, formatNumber($TotalNewITLabs, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(25, $iRow, formatNumber($TotalRehabITLabs, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $iRow, formatNumber($TotalNewExamHalls, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(27, $iRow, formatNumber($TotalRehabExamHalls, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(28, $iRow, formatNumber($TotalNewLibraries, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(29, $iRow, formatNumber($TotalRehabLibraries, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(30, $iRow, formatNumber($TotalNewClerkOffices, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(31, $iRow, formatNumber($TotalRehabClerkOffices, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(32, $iRow, formatNumber($TotalNewPrincipalOffice, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(33, $iRow, formatNumber($TotalRehabPrincipalOffice, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(34, $iRow, formatNumber($TotalNewParkingStand, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(35, $iRow, formatNumber($TotalRehabParkingStand, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(36, $iRow, formatNumber($TotalNewChowkidarHut, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(37, $iRow, formatNumber($TotalRehabChowkidarHut, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(38, $iRow, formatNumber($TotalNewSoakagePit, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(39, $iRow, formatNumber($TotalRehabSoakagePit, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(40, $iRow, formatNumber($TotalNewWaterSupply, false));
        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(41, $iRow, formatNumber($TotalRehabWaterSupply, false));

        $objPhpExcel->getActiveSheet()->getStyle("A{$iRow}:AQ{$iRow}")->applyFromArray(array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
                                                                                                                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '92CDDC')),
                                                                                                                'font' => array('bold' => true, 'color' => array('rgb' => '000000'), 'size' => 12),
                                                                                                                'borders'  => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                                                                                                                                 'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                                                                                                                                 'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                                                                                                                                 'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN))) );

        $objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(15);
  	$objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(50);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(10);
	$objPhpExcel->getActiveSheet()->getColumnDimension("F")->setWidth(25);
	$objPhpExcel->getActiveSheet()->getColumnDimension("G")->setWidth(25);
	$objPhpExcel->getActiveSheet()->getColumnDimension("H")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("I")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("J")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("K")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("L")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("M")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("N")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("O")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("P")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("R")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("S")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("T")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("U")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("V")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("W")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("X")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("Z")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("AA")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("AB")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AC")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AD")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AE")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AF")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AG")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AH")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AI")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AJ")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AK")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AL")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AM")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AN")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AO")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AP")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("AQ")->setWidth(15);
        

	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Schools List &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Schools");


	$sExcelFile = "Schools.xlsx";

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=\"{$sExcelFile}\"");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');
	$objWriter->save("php://output");



	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>