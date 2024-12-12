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
	$iDistrict   = IO::strValue("District");
        $sStatus     = IO::strValue("Status");
        
        $styleEMIS   = array(
            'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '4B4B4B'),
            'size'  => 12,
            'name'  => 'Arial'
        ));
        $styleArray = array(
            'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '787878'),
            'size'  => 10,
            'name'  => 'Arial'
        ));
	$styleArray2 = array(
                'borders' => array(
                  'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                  )
                )
        );
	
	if ($sKeywords != "")
	{
		$sConditions .= " AND ( enumerator LIKE '%{$sKeywords}%' )";
        }


        if($iDistrict > 0)
            $sConditions .= " AND su.district_id='$iDistrict' ";
		
        if ($sStatus != '')
		$sConditions .= " AND su.status='$sStatus' ";
        
        $objPhpExcel = new PHPExcel( );
        
        $objReader   = PHPExcel_IOFactory::createReader('Excel2007');
	$objPhpExcel = $objReader->load("{$sRootDir}templates/surveys.xlsx");
        
	$objPhpExcel->getProperties()->setCreator($_SESSION["SiteTitle"])
								 ->setLastModifiedBy($_SESSION["SiteTitle"])
								 ->setTitle("Surveys")
								 ->setSubject("Surveys List")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Reports");

	$objPhpExcel->setActiveSheetIndex(0);


	$sBorderStyle = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
						  'borders'  => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											 'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											 'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											 'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));

	$sBlockStyle = array('font'       => array('bold' => true, 'size' => 11),
                         'fill'       => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '538DD5')),
	                     'alignment'  => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
						 'borders'    => array('top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											   'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											   'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											   'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $sFailedBlockStyle = array('font'       => array('bold' => false, 'size' => 11),
                         'fill'       => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFD579')),
	                     'alignment'  => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
						 'borders'    => array('top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											   'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											   'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											   'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)));

        $sSummaryBlockStyle = array('font'       => array('bold' => true, 'size' => 12),
                         'fill'       => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFC000')),
	                     'alignment'  => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
						 'borders'    => array());


	$iRow = 3;
        
        $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "EMIS Code");
        $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "EMIS Code (Enumerator)");
	$objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "School Name");
	$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "Type");
	$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", "Address");
	$objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", "District");
	$objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", "Province");
        $objPhpExcel->getActiveSheet()->setCellValue("H{$iRow}", "Enumerator");
	$objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", "Date");
        $objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", "Latitude (Pre-Entered)");
	$objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", "Longitude (Pre-Entered)");
        $objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", "Latitude (Enumerator)");
	$objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", "Longitude (Enumerator)");
        $objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", "Latitude (Device)");
	$objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", "Longitude (Device)");
        $objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", "Is the operational?");
        $objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", "Is the school part of the PEF (Punjab Education Foundation) Programme?");
	$objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", "Does the school have enough land for new construction?");	
	$objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", "Is the school having any land dispute?");
	$objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", "Is the school involved in any other project providing funding classroom infrastructure?");
	$objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", "How many classrooms does your school have?");
	$objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", "Out of total how many classrooms are being used for purpose?");
	$objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", "Are there any shelter-less grades being taught?");
	$objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", "Are there more than 2 grades being taught in one classroom?");
	$objPhpExcel->getActiveSheet()->setCellValue("Y{$iRow}", "What is the average attendance of school?");
        
        
            $index = 25;
            $sQuestionsList   = getList("tbl_survey_questions", "id", "question", "status='A' AND id NOT IN (73,89,90)", "section_id,position");
            foreach ($sQuestionsList as $Id => $sQuestion){
                $iCol = getExcelCol($index);
                $objPhpExcel->getActiveSheet()->setCellValue("{$iCol}{$iRow}", "{$sQuestion}");
                $index++;
            }
            
        $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBlockStyle , ((getExcelCol(0))."{$iRow}:".(getExcelCol(114)).$iRow));
        $objPhpExcel->getActiveSheet()->getStyle("A3:DJ3")->applyFromArray($styleArray2);
        
	$iRow          += 1;
        $failedSurveys  = 0; 
	$sTypesList     = getList("tbl_school_types", "id", "`type`");
	$sProvincesList = getList("tbl_provinces", "id", "name");
	$sDistrictsList = getList("tbl_districts", "id", "name");
        $SurveyIds      = array();
      
	$sSQL = "SELECT * FROM tbl_surveys su, tbl_schools sc where su.school_id=sc.id AND sc.status='A' $sConditions ORDER BY su.id";
        $objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
                
                $iSurveyId        = $objDb->getField($i, "su.id");
		$sName            = $objDb->getField($i, "sc.name");
		$sCode            = $objDb->getField($i, "sc.code");
		$iType            = $objDb->getField($i, "sc.type_id");
		$iProvince        = $objDb->getField($i, "sc.district_id");
		$iDistrict        = $objDb->getField($i, "sc.district_id");
		$sAddress         = $objDb->getField($i, "sc.address");
		$sLatitude        = $objDb->getField($i, "sc.latitude");
		$sLongitude       = $objDb->getField($i, "sc.longitude");
                $sLatitudeDevice  = $objDb->getField($i, "su.latitude");
		$sLongitudeDevice = $objDb->getField($i, "su.longitude");
                $sEnumerator      = $objDb->getField($i, "su.enumerator");
		$sDate            = $objDb->getField($i, "su.date");
                $sOperational     = $objDb->getField($i, "su.operational");
                $sPefProgramme    = $objDb->getField($i, "su.pef_programme");
                $sLandAvailable   = $objDb->getField($i, "su.land_available");
                $sLandDispute     = $objDb->getField($i, "su.land_dispute");
                $sOtherFunding    = $objDb->getField($i, "su.other_funding");
                $sClassRooms      = $objDb->getField($i, "su.class_rooms");
                $sEducation_Rooms = $objDb->getField($i, "su.education_rooms");
                $sShelterLess     = $objDb->getField($i, "su.shelter_less");
                $sMultiGrading    = $objDb->getField($i, "su.multi_grading");
                $sAvgAttendance   = $objDb->getField($i, "su.avg_attendance");
                $sQualified       = $objDb->getField($i, "su.qualified");
               
                if($sQualified == 'N')
                    $failedSurveys ++;
                
                $sAnswers   = getList("tbl_survey_answers", "question_id", "answer", "survey_id='{$iSurveyId}'");
                
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(0, ($i + $iRow), $sCode);
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, ($i + $iRow), @$sAnswers[73]);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($i + $iRow), $sName);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($i + $iRow), $sTypesList[$iType]);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(4, ($i + $iRow), $sAddress);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(5, ($i + $iRow), $sDistrictsList[$iDistrict]);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(6, ($i + $iRow), $sProvincesList[$iProvince]);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(7, ($i + $iRow), $sEnumerator);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(8, ($i + $iRow), $sDate);
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(9, ($i + $iRow), $sLatitude);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(10, ($i + $iRow), $sLongitude);
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(11, ($i + $iRow), @$sAnswers[89]);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(12, ($i + $iRow), @$sAnswers[90]);
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(13, ($i + $iRow), $sLatitudeDevice);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(14, ($i + $iRow), $sLongitudeDevice);                
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(15, ($i + $iRow), $sOperational);
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(16, ($i + $iRow), $sPefProgramme);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(17, ($i + $iRow), $sLandAvailable);
                $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(18, ($i + $iRow), $sLandDispute);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(19, ($i + $iRow), $sOtherFunding);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(20, ($i + $iRow), $sClassRooms);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(21, ($i + $iRow), $sEducation_Rooms);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(22, ($i + $iRow), $sShelterLess);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(23, ($i + $iRow), $sMultiGrading);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(24, ($i + $iRow), $sAvgAttendance);
                
                $index2 = 25;
                foreach ($sQuestionsList as $Id => $sQuestion){
                    $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow($index2, ($i + $iRow), @$sAnswers[$Id]);
                    $index2++;
                }
                
                
                        if($sQualified == 'N')
                            $objPhpExcel->getActiveSheet()->duplicateStyleArray($sFailedBlockStyle , (getExcelCol(0).($i + $iRow).":".getExcelCol(114).($i + $iRow)));
                        
                            $objPhpExcel->getActiveSheet()->duplicateStyleArray($styleEMIS , (getExcelCol(0).($i + $iRow).":".getExcelCol(0).($i + $iRow)));
                            
                            $objPhpExcel->getActiveSheet()->duplicateStyleArray($styleArray , (getExcelCol(1).($i + $iRow).":".getExcelCol(114).($i + $iRow)));
                            
                           $objPhpExcel->getActiveSheet()->getStyle("A".($i + $iRow).":DI".($i + $iRow))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                           
                           $objPhpExcel->getActiveSheet()->getStyle("A".($i + $iRow).":DI".($i + $iRow))->applyFromArray($styleArray2);
	}
        
        $iSummaryRow = $iCount + 5;
        $iSummaryRow1 = $iSummaryRow+1;
        $iSummaryRow2 = $iSummaryRow1+1;
        $iSummaryCol = 0;

        $objPhpExcel->getActiveSheet()->duplicateStyleArray($sSummaryBlockStyle , ((getExcelCol(0))."{$iSummaryRow}:".(getExcelCol(114)).$iSummaryRow));
        
        $objPhpExcel->getActiveSheet()->setCellValue("A{$iSummaryRow}", "Summary");
        $objPhpExcel->getActiveSheet()->setCellValue("A{$iSummaryRow1}", "No of Basline Surveys Conducted: {$iCount}");
        $objPhpExcel->getActiveSheet()->setCellValue("A{$iSummaryRow2}", "No of Schools Which failed in Pre-Assessment: {$failedSurveys}");
        $objPhpExcel->getActiveSheet()->getStyle("A{$iSummaryRow1}")->applyFromArray($styleArray);
        $objPhpExcel->getActiveSheet()->getStyle("A{$iSummaryRow2}")->applyFromArray($styleArray);
            
        
	$objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(40);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(10);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(50);
	$objPhpExcel->getActiveSheet()->getColumnDimension("F")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("G")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("H")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("I")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("J")->setWidth(25);
	$objPhpExcel->getActiveSheet()->getColumnDimension("K")->setWidth(25);
    	$objPhpExcel->getActiveSheet()->getColumnDimension("L")->setWidth(25);
	$objPhpExcel->getActiveSheet()->getColumnDimension("M")->setWidth(25);
	$objPhpExcel->getActiveSheet()->getColumnDimension("N")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("O")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("P")->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("R")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("S")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("T")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("U")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("V")->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension("W")->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension("X")->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(30);
        
        $index = 25;
        foreach ($sQuestionsList as $Id => $sQuestion){
            $iCol = getExcelCol($index);
            $objPhpExcel->getActiveSheet()->getColumnDimension($iCol)->setWidth(30);
            $index++;
        }

	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Surveys List &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Surveys");


	$sExcelFile = "Surveys.xlsx";

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=\"{$sExcelFile}\"");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');
	$objWriter->save("php://output");

	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
        
?>