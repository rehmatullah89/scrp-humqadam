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
	

        
	$objPhpExcel = new PHPExcel( );
	
	$objPhpExcel->getProperties()->setCreator($_SESSION["SiteTitle"])
								 ->setLastModifiedBy($_SESSION["SiteTitle"])
								 ->setTitle("Inspections")
								 ->setSubject("Contracted Schools Report")
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

	$sBorderStyle = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
					  'borders'  => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
												 'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										 'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										 'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	

	$sBorderStyleHighlight = array('font'       => array('bold' => false, 'size' => 11),
					 'fill'       => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFD579')),
					 'alignment'  => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
					 'borders'    => array('top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)));

	$sTotalStyle 	= array('font'       => array('bold' => false, 'size' => 12),
					 'fill'       => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'CCCCCC')),
					 'alignment'  => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
					 'borders'    => array('top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
									   
        
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


	/////////////////////////////////////////////////////////////////////
	
	$sActiveSchools = getList("tbl_inspections", "DISTINCT(school_id)", "school_id", "stage_id IN ($sMilestoneStages)");  
	

	$objPhpExcel->getActiveSheet()->setCellValue("A1", "EMIS Code");
	$objPhpExcel->getActiveSheet()->setCellValue("B1", "Province");
	$objPhpExcel->getActiveSheet()->setCellValue("C1", "School Name");
	$objPhpExcel->getActiveSheet()->setCellValue("D1", "Contractor");
	$objPhpExcel->getActiveSheet()->setCellValue("E1", "Start Date");
	$objPhpExcel->getActiveSheet()->setCellValue("F1", "End Date");

	$objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingStyle, ("A1:F1"));		


	
	$sSQL = "SELECT s.id, s.name, s.code, c.start_date, c.end_date, 
					(SELECT name From tbl_provinces where id=s.province_id) AS _Province,
					(SELECT company From tbl_contractors where id=c.contractor_id) AS _Contractor
			 FROM tbl_schools s, tbl_contracts c
			 WHERE s.status='A' AND s.dropped!='Y' AND s.qualified='Y' AND FIND_IN_SET(s.id, c.schools)
			 ORDER BY s.province_id, s.name";
    $objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	$iRow   = 2;
	
	for($i=0; $i < $iCount ; $i++, $iRow ++)
	{
		$iSchool            = $objDb->getField($i, "id");
		$sSchool            = $objDb->getField($i, "name");
		$sCode              = $objDb->getField($i, "code");
		$sProvince          = $objDb->getField($i, "_Province");
		$sContractor        = $objDb->getField($i, "_Contractor");
		$sStartDate         = $objDb->getField($i, "start_date");
		$sEndDate           = $objDb->getField($i, "end_date");
                
		$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", $sCode);
		$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", $sProvince);
		$objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", $sSchool);
		$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", $sContractor);
		$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", $sStartDate);
		$objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", $sEndDate);
                

		if (@!in_array($iSchool, $sActiveSchools))	
			$objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyleHighlight, "A{$iRow}:F{$iRow}");
		
		else
			$objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, "A{$iRow}:F{$iRow}");             
	}
        
   	$objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(50);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("F")->setWidth(20);
      

	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Contracted Schools &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Contracted Schools Report");
        
    
	////////////////////////// Download File ///////////////////////////////
		
	$sExcelFile = "Contracted Schools.xlsx";

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