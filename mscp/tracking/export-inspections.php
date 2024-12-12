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
	
        $iProvince   = IO::intValue("Province");
        $iDistrict   = IO::intValue("District");
	$sFromDate   = IO::strValue("FromDate");  
	$sToDate     = IO::strValue("ToDate"); 
        
        $sConditions    = " WHERE FIND_IN_SET(i.district_id, '{$_SESSION['AdminDistricts']}') ";
        
       	if ($iProvince > 0){
            $iDistricts  = getDbValue("GROUP_CONCAT(id SEPARATOR ',') as districts", "tbl_districts", "province_id='$iProvince'");
            $sConditions .= " AND FIND_IN_SET(i.district_id, '{$iDistricts}') ";
        }
        
        if ($iDistrict > 0)
            $sConditions .= " AND i.district_id='$iDistrict' ";
        
        if ($sFromDate != "" && $sToDate != "")
            $sConditions .= " AND (i.date BETWEEN '$sFromDate' AND '$sToDate') ";
        
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
	

	/////////////////////////////////////////////////////////////////////
	
	
        $objPhpExcel->getActiveSheet()->setCellValue("A1", "#");
	$objPhpExcel->getActiveSheet()->setCellValue("B1", "DE Name");
	$objPhpExcel->getActiveSheet()->setCellValue("C1", "Location");
	$objPhpExcel->getActiveSheet()->setCellValue("D1", "Total Inspections");
	$objPhpExcel->getActiveSheet()->setCellValue("E1", "Visit to Unique Sites");

	$objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingStyle, ("A1:E1"));		


	
	$sSQL = "SELECT (Select name from tbl_admins where id=i.admin_id) as _DeName, 
                        (Select name from tbl_districts where id=i.district_id) as _District, 
                        Count(Distinct i.school_id) _UniqueSites, count(i.id) as _TotalInspections 
                        FROM tbl_inspections i
                        $sConditions
		Group By i.admin_id
                ORDER BY _DeName";
        $objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	$iRow   = 2;
	
	for($i=0; $i < $iCount ; $i++, $iRow ++)
	{
		$sDeName            = $objDb->getField($i, "_DeName");
		$iUniqueSites       = $objDb->getField($i, "_UniqueSites");
		$iTotalInspections  = $objDb->getField($i, "_TotalInspections");
		$sDistrict          = $objDb->getField($i, "_District");
                
                $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", $i+1);
		$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", $sDeName);
		$objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", $sDistrict);
		$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", $iTotalInspections);
		$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", $iUniqueSites);                

        	$objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, "A{$iRow}:E{$iRow}");             
	}
        
   	$objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(5);
	$objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(30);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
      

	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Unique Inspections &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Inspections Report");
        
    
	////////////////////////// Download File ///////////////////////////////
		
	$sExcelFile = "Site Inspections.xlsx";

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