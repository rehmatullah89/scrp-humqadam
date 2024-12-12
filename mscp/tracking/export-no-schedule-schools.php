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

     if (!@strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))
         die("ERROR: Invalid Request");


	$objDbGlobal = new Database( );
	$objDb       = new Database( );
        
	$sKeywords   = IO::strValue("Keywords");
	$iContract   = IO::strValue("Contract");
	$sDesignType = IO::strValue("DesignType");
	$sStoreyType = IO::strValue("StoreyType");
	$iProvince   = IO::intValue("Province");
	$iDistrict   = IO::intValue("District");
	$iPackage    = IO::intValue("Package");
	$sSchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);


	$sConditions = "WHERE status='A' AND qualified='Y' AND adopted='Y' AND completed!='Y'";
	
	if ($sKeywords != "")
		$sConditions .= " AND (code='{$sKeywords}' OR name LIKE '%{$sKeywords}%') ";
        
	if ($iProvince > 0)
		$sConditions .= " AND province_id='$iProvince' ";
	
	else
		$sConditions .= " AND FIND_IN_SET(province_id, '{$_SESSION['AdminProvinces']}') ";

	if ($iDistrict > 0)
		$sConditions .= " AND district_id='$iDistrict' ";
	
	else
		$sConditions .= " AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";
	
	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(id, '{$_SESSION['AdminSchools']}') ";	

	if ($iPackage > 0)
	{
		$sSchools = getDbValue("schools", "tbl_packages", "id='$iPackage'");
		
		$sConditions .= " AND id IN ($sSchools)";
	}

	if ($sDesignType != "")
		$sConditions .= " AND design_type='{$sDesignType}'";

	if ($sStoreyType != "")
		$sConditions .= " AND storey_type='{$sStoreyType}'";

	if ($iContract > 0)
	{
		$sSchools = getDbValue("schools", "tbl_contracts", "id='$iContract'");
		
		$sConditions .= " AND id IN ($sSchools)";		
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
	$sSubConditions   = "";

	if ($_SESSION["AdminSchools"] != "")
		$sSubConditions = " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";	
	
	
	$sConditions .= " AND id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions) ";
	$sConditions .= " AND id NOT IN (SELECT school_id FROM tbl_contract_schedules)  ";
	
	
		
	$objPhpExcel = new PHPExcel( );
	
	$objReader   = PHPExcel_IOFactory::createReader('Excel2007');
	$objPhpExcel = $objReader->load("{$sRootDir}templates/non-schedule-schools.xlsx");
        
	$objPhpExcel->getProperties()->setCreator($_SESSION["SiteTitle"])
								 ->setLastModifiedBy($_SESSION["SiteTitle"])
								 ->setTitle("Non-Scheduled-Schools")
								 ->setSubject("Non Construction Scheduled Schools")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Reports");

	$objPhpExcel->setActiveSheetIndex(0);


	$iRow = 4;
	
	
	$sTypesList     = getList("tbl_school_types", "id", "`type`");
	$sProvincesList = getList("tbl_provinces", "id", "name");
	$sDistrictsList = getList("tbl_districts", "id", "name");
	
	
	$sSQL = "SELECT name, code, province_id, district_id, type_id, students, storey_type, design_type, address 
	         FROM tbl_schools
	         $sConditions
			 ORDER BY id";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++, $iRow ++)
	{
		$sCode       = $objDb->getField($i, "code");
		$sSchool     = $objDb->getField($i, "name");
		$iDistrict   = $objDb->getField($i, "district_id");
		$iProvince   = $objDb->getField($i, "province_id");
		$iType       = $objDb->getField($i, "type_id");
		$iStudents   = $objDb->getField($i, "students");
		$sStoreyType = $objDb->getField($i, "storey_type");
		$sDesignType = $objDb->getField($i, "design_type");
		$sAddress    = $objDb->getField($i, "address");

		
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $iRow, $sCode);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $iRow, $sSchool);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $iRow, $sTypesList[$iType]);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $iRow, (($sStoreyType == "S") ? "Single" : "Double"));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $iRow, (($sDesignType == "R") ? "Regular" : "Bespoke"));
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $iRow, $sDistrictsList[$iDistrict]);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $iRow, $sProvincesList[$iProvince]);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $iRow, $iStudents);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $iRow, $sAddress);
	}
                

	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&Non-Schedule Schools List &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Non-Scheduled Schools");



	$sExcelFile = "Non-Scheduled Schools.xlsx";

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=\"{$sExcelFile}\"");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');
	$objWriter->save("php://output");

	
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );      
?>