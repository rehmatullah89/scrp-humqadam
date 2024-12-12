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
        $objDb2      = new Database( );
        $objDb3      = new Database( );
        $objDb4      = new Database( );
        
        $sKeywords   = IO::strValue("Keywords");
	$iContract   = IO::strValue("Contract");
        $sDesignType = IO::strValue("DesignType");
        $sStoreyType = IO::strValue("StoreyType");
        $iProvince   = IO::intValue("Province");
        $iDistrict   = IO::intValue("District");
        $iPackage    = IO::intValue("Package");
        $sSchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);
        
        $styleArray = array(
                'borders' => array(
                  'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                  )
                )
        );
	
	if ($sKeywords != "")
	{
		$sConditions .= " AND (s.code='{$sKeywords}' OR s.name LIKE '%{$sKeywords}%') ";
	}
        
        if ($iProvince > 0)
		$sConditions .= " AND s.province_id='$iProvince' ";
        
        if ($iDistrict > 0)
		$sConditions .= " AND s.district_id='$iDistrict' ";
        
        if ($iPackage > 0){

            $sPackages = getDbValue("schools", "tbl_packages", "id='$iPackage'");
            $sConditions .= " AND s.id IN ($sPackages)";
            
        }
                
        if ($sDesignType != "")
		$sConditions .= " AND s.design_type='{$sDesignType}'";
        
        if ($sStoreyType != "")
		$sConditions .= " AND s.storey_type='{$sStoreyType}'";
        
	if ($iContract > 0)
		$sConditions .= " AND cs.contract_id='$iContract' ";
        
        $iMainStage  = getDbValue("id", "tbl_stages", "status='A' AND parent_id='0' AND `type`='$sSchoolType'", "position DESC"); 
        $sSQL2 = "SELECT id FROM tbl_stages WHERE parent_id='$iMainStage' AND `type`='$sSchoolType' ORDER BY position";
        $objDb2->query($sSQL2);

        $iCount2 = $objDb2->getCount( );
        
        $sStages = "0";
        
        for ($j = 0; $j < $iCount2; $j ++){
            
            $iParent = $objDb2->getField($j, "id");

            $sStages .= ",{$iParent}";

            $sSQL3 = "SELECT id FROM tbl_stages WHERE parent_id='$iParent' ORDER BY position";
            $objDb3->query($sSQL3);

            $iCount3 = $objDb3->getCount( );

            for ($k = 0; $k < $iCount3; $k ++){
                                            
                $iStage = $objDb3->getField($k, "id");
                $sStages .= ",{$iStage}";

                $sSQL4 = "SELECT id FROM tbl_stages WHERE parent_id='$iStage' ORDER BY position";
                $objDb4->query($sSQL4);

                $iCount4 = $objDb4->getCount( );

                for ($m = 0; $m < $iCount4; $m ++){
                                                    
                    $iSubStage = $objDb4->getField($m, "id");

                    $sStages .= ",{$iSubStage}";
                }
            }
        }
                       
        
        $objPhpExcel = new PHPExcel( );
        
        $objReader   = PHPExcel_IOFactory::createReader('Excel2007');
	$objPhpExcel = $objReader->load("{$sRootDir}templates/schedules.xlsx");
        
	$objPhpExcel->getProperties()->setCreator($_SESSION["SiteTitle"])
								 ->setLastModifiedBy($_SESSION["SiteTitle"])
								 ->setTitle("Schedules")
								 ->setSubject("Construction Schedules")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Reports");

	$objPhpExcel->setActiveSheetIndex(0);


	$iRow  = 3;
        $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "School Name");
        $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "EMIS Code");
	$objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "Contractor");
        $objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "District");
        
        $index  = 4;
        $sSpaceString = '                                                                       '; 
        $sStagesList = getList("tbl_stages", "id", "name", "type='$sSchoolType' AND id IN ($sStages)", "FIELD(id,$sStages)");
        
        foreach ($sStagesList as $Id => $sStage){
            
            $iCol  =  getExcelCol($index);
            $iCol2 =  getExcelCol($index+1);
            $sStage = $sStage.$sSpaceString;
            
            $objPhpExcel->getActiveSheet()->setCellValue("{$iCol}{$iRow}", "{$sStage}");
            $objPhpExcel->getActiveSheet()->setCellValue("{$iCol}4", "Start Date");
            $objPhpExcel->getActiveSheet()->getColumnDimension($iCol)->setWidth(30);
            $objPhpExcel->getActiveSheet()->setCellValue("{$iCol2}4", "End Date");
            $objPhpExcel->getActiveSheet()->getColumnDimension($iCol2)->setWidth(20);
            $index = $index + 2;
            
        }
        
        $iCol  =  getExcelCol((2*count($sStagesList))+3);
        $objPhpExcel->getActiveSheet()->getStyle("A3:{$iCol}3")->applyFromArray($styleArray);
        $objPhpExcel->getActiveSheet()->getStyle("A4:{$iCol}4")->applyFromArray($styleArray);
        
        $iRow = 4;
        $sSQL = "SELECT cs.id, cs.contract_id, s.id, s.code, s.name, s.district_id
	         FROM tbl_contract_schedules cs, tbl_schools s
	         WHERE cs.school_id=s.id AND s.id in (Select school_id from tbl_inspections where stage_id IN ($sStages)) $sConditions
	         ORDER BY cs.id";
	
            $objDb->query($sSQL);
            $iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iSchedule   = $objDb->getField($i, "cs.id");
			$iContract   = $objDb->getField($i, "contract_id");
                        $iDistrict   = $objDb->getField($i, "district_id");
                        $sContract   = getDbValue("title", "tbl_contracts", "id='$iContract'");
                        $sDistrict   = getDbValue("name", "tbl_districts", "id='$iDistrict'");
			$sCode       = $objDb->getField($i, "code");
                        $sSchool     = $objDb->getField($i, "name");
                        
                	
                        $iRow += 1;
                        
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $iRow, $sSchool);
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $iRow, $sCode);
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $iRow, $sContract);
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $iRow, $sDistrict);
                        
                        $sSQL2 = "SELECT csd.start_date, csd.end_date 
				FROM tbl_contract_schedule_details csd, tbl_stages s
                                WHERE csd.stage_id=s.id AND csd.schedule_id='$iSchedule' AND s.type='$sSchoolType' 
                                        ORDER BY FIELD(s.id,$sStages)";
                        
                        $objDb2->query($sSQL2);

                        $iCount5 = $objDb2->getCount( );
 
                        $index2 = 4;
                        for ($n = 0; $n < $iCount5; $n ++)
                        {
                            $sStartDate = $objDb2->getField($n, "start_date");
                            $sEndDate   = $objDb2->getField($n, "end_date");
                            $sStartDate = (($sStartDate == "0000-00-00") ? "" : $sStartDate);
                            $sEndDate   = (($sEndDate == "0000-00-00") ? "" : $sEndDate);
                            
                            $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($index2, $iRow, $sStartDate);
                            $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(($index2+1),  $iRow, $sEndDate);
                            $index2 = $index2 +2;
                        }
                        
                } // end main for loop
                

	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Schedules List &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("DetailedSchedules");


	$sExcelFile = "DetailedSchedules.xlsx";

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=\"{$sExcelFile}\"");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');
	$objWriter->save("php://output");

	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
        
?>