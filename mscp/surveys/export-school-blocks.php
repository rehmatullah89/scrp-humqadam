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

     //if (!@strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))
     //    die("ERROR: Invalid Request");
        
        $iDistrict   = IO::intValue("District");
        $sConditions = "";
        
        if ($iDistrict > 0)
		$sConditions .= " AND sc.district_id='$iDistrict' ";

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	$objPhpExcel = new PHPExcel( );

	$objPhpExcel->getProperties()->setCreator($_SESSION["SiteTitle"])
								 ->setLastModifiedBy($_SESSION["SiteTitle"])
								 ->setTitle("Schools")
								 ->setSubject("Schools List")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Reports");

	$objPhpExcel->setActiveSheetIndex(0);

	$objPhpExcel->getActiveSheet()->setCellValue("A1", $_SESSION["SiteTitle"]);
	$objPhpExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(21);
	$objPhpExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);

	$objPhpExcel->getActiveSheet()->setCellValue("A2", "Class Rooms Information");
	$objPhpExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(16);
	$objPhpExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);


	$sHeadingStyle = array('font' => array('bold' => true, 'size' => 11),
						   'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
						   'borders'   => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											  'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)) );

	$sBorderStyle = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
						  'borders'  => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											 'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											 'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											 'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));

	$sBlockStyle = array('font'       => array('bold' => true, 'size' => 11),
                         'fill'       => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'DDDDDD')),
	                     'alignment'  => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
						 'borders'    => array('top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											   'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											   'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
											   'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)));


	$iRow = 4;

	$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "EMIS Code");
	$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "School Name");
	$objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "Province");	
	$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "District");
	$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", "Classrooms Being used for Educational Purposes as per Pre-Selection");
	$objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", "Classrooms being used for Educational Purposes from Facility and Room Count Section");
	$objPhpExcel->getActiveSheet()->duplicateStyleArray($sBlockStyle , ("A4:F4"));


	$iRow          += 1;
	$sTypesList     = getList("tbl_school_types", "id", "`type`");
	$sProvincesList = getList("tbl_provinces", "id", "name");
	$sDistrictsList = getList("tbl_districts", "id", "name");


	$sSQL = "SELECT su.education_rooms, sc.code, sc.name, SUM(sbd.total-sbd.dilapidated) AS _BlockRooms,
                    (Select name from tbl_districts where id=sc.district_id) AS _DISTRICT,
                    (Select name from tbl_provinces where id=sc.province_id) AS _Province
                    FROM tbl_surveys su, tbl_schools sc, tbl_survey_school_block_details sbd 
                    WHERE sc.id=su.school_id AND su.id=sbd.survey_id AND sbd.room_type_code='CRE' AND su.qualified='Y' $sConditions
                    GROUP BY sbd.survey_id
                    Having _BlockRooms != su.education_rooms
                    ORDER By sc.province_id,sc.name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$sName            = $objDb->getField($i, "name");
		$sCode            = $objDb->getField($i, "code");
		$sprovince        = $objDb->getField($i, "_Province");
		$sDistrict        = $objDb->getField($i, "_DISTRICT");
		$sSummaryRooms    = $objDb->getField($i, "education_rooms");
		$sBlockRooms      = $objDb->getField($i, "_BlockRooms");
		

		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(0, ($i + $iRow), $sCode);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, ($i + $iRow), $sName);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($i + $iRow), $sprovince);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($i + $iRow), $sDistrict);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(4, ($i + $iRow), $sSummaryRooms);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(5, ($i + $iRow), $sBlockRooms);
                
                $iRowNext = $i + $iRow;
                
                $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, "A{$iRowNext}:F{$iRowNext}");
		
		
	}


	$objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(25);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(25);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(65);
	$objPhpExcel->getActiveSheet()->getColumnDimension("F")->setWidth(75);


	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Schools List &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Class Rooms Information");
/////////////////////////////////////////////////Sheet # 2 /////////////////////////////////////////////
        
        $objPhpExcel->createSheet(NULL, "Attendance Information");
        $objPhpExcel->setActiveSheetIndex(1);

        $objPhpExcel->getActiveSheet()->setCellValue("A1", $_SESSION["SiteTitle"]);
	$objPhpExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(21);
	$objPhpExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);

	$objPhpExcel->getActiveSheet()->setCellValue("A2", "Attendance Information");
	$objPhpExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(16);
	$objPhpExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);

	$iRow = 4;

	$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "EMIS Code");
	$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "School Name");
	$objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "Province");	
	$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "District");
	$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", "Average Attendance as per Pre-Selection Section");
	$objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", "Attendance as per Student Attendance Section");
	$objPhpExcel->getActiveSheet()->duplicateStyleArray($sBlockStyle , ("A4:F4"));


	$iRow          += 1;
	
	$sSQL = "SELECT su.avg_attendance, sc.code, sc.name, SUM(ssan.boys_count_morning+ssan.girls_count_morning+ssan.boys_count_evening+ssan.girls_count_evening) AS _TotalStudents,
                    (Select name from tbl_districts where id=sc.district_id) AS _DISTRICT,
                    (Select name from tbl_provinces where id=sc.province_id) AS _Province
                    FROM tbl_surveys su, tbl_schools sc, tbl_survey_student_attendance_numbers ssan 
                    WHERE sc.id=su.school_id AND su.id=ssan.survey_id AND su.qualified='Y' $sConditions
                    GROUP BY ssan.survey_id
                    Having _TotalStudents != su.avg_attendance
                    ORDER By sc.province_id,sc.name";
        $objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$sName            = $objDb->getField($i, "name");
		$sCode            = $objDb->getField($i, "code");
		$sprovince        = $objDb->getField($i, "_Province");
		$sDistrict        = $objDb->getField($i, "_DISTRICT");
		$sAvgAttendance   = $objDb->getField($i, "avg_attendance");
		$sTotalStudents      = $objDb->getField($i, "_TotalStudents");
		

		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(0, ($i + $iRow), $sCode);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, ($i + $iRow), $sName);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($i + $iRow), $sprovince);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($i + $iRow), $sDistrict);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(4, ($i + $iRow), $sAvgAttendance);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(5, ($i + $iRow), $sTotalStudents);
                
                $iRowNext = $i + $iRow;
                
                $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, "A{$iRowNext}:F{$iRowNext}");
		
		
	}


	$objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(25);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(25);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(50);
	$objPhpExcel->getActiveSheet()->getColumnDimension("F")->setWidth(50);


	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Schools List &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Attendance Information");   

/////////////////////////////////////////////////Sheet # 3 /////////////////////////////////////////////
        
        $objPhpExcel->createSheet(NULL, "Toilets Information");
        $objPhpExcel->setActiveSheetIndex(2);

        $objPhpExcel->getActiveSheet()->setCellValue("A1", $_SESSION["SiteTitle"]);
	$objPhpExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(21);
	$objPhpExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);

	$objPhpExcel->getActiveSheet()->setCellValue("A2", "Toilets Information");
	$objPhpExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(16);
	$objPhpExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);

	$iRow = 4;

	$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "EMIS Code");
	$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "School Name");
	$objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "Province");	
	$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "District");
	$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", "Toilets from Facility and Room Count Section");
	$objPhpExcel->getActiveSheet()->duplicateStyleArray($sBlockStyle , ("A4:E4"));


	$iRow          += 1;
	
	$sSQL = "SELECT sc.code, sc.name, SUM(sbd.total) AS _TotalStudentToilets,
                    (Select name from tbl_districts where id=sc.district_id) AS _DISTRICT,
                    (Select name from tbl_provinces where id=sc.province_id) AS _Province
                    FROM tbl_surveys su, tbl_schools sc, tbl_survey_school_block_details sbd
                    WHERE sc.id=su.school_id AND su.id=sbd.survey_id AND su.qualified='Y' AND (sbd.room_type_code='TFS' OR sbd.room_type_code='TMS' OR sbd.room_type_code='US' OR sbd.room_type_code='TB') $sConditions
                    GROUP BY sbd.survey_id
                    ORDER By sc.province_id,sc.name";
        $objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$sName            = $objDb->getField($i, "name");
		$sCode            = $objDb->getField($i, "code");
		$sprovince        = $objDb->getField($i, "_Province");
		$sDistrict        = $objDb->getField($i, "_DISTRICT");
		$sTotalStudentToilets      = $objDb->getField($i, "_TotalStudentToilets");
		

		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(0, ($i + $iRow), $sCode);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, ($i + $iRow), $sName);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($i + $iRow), $sprovince);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($i + $iRow), $sDistrict);
		$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(4, ($i + $iRow), $sTotalStudentToilets);
		
                $iRowNext = $i + $iRow;
                
                $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, "A{$iRowNext}:E{$iRowNext}");
		
		
	}


	$objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(25);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(25);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(50);


	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Schools List &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Toilets Information");   
        
///////////////////////////Download File .///////////////////////////////////////////////////////   

        $objPhpExcel->setActiveSheetIndex(0);
	$sExcelFile = "Data Verification Report.xlsx";

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=\"{$sExcelFile}\"");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');
	$objWriter->save("php://output");



	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>