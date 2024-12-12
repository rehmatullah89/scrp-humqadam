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


        $style = array(
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,
                                      'color' => array('rgb' => PHPExcel_Style_Color::COLOR_WHITE)
                                      )
            )
        );
        
        $objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );
        $objDb3      = new Database( );
	$objDb4      = new Database( );

        
	$objPhpExcel = new PHPExcel( );
	
	$objPhpExcel->getProperties()->setCreator($_SESSION["SiteTitle"])
								 ->setLastModifiedBy($_SESSION["SiteTitle"])
								 ->setTitle("Inspections")
								 ->setSubject("Weekly Progress Report")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Reports");

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
	

	$sTotalStyle 	= array('font'       => array('bold' => false, 'size' => 12),
					 'fill'       => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'CCCCCC')),
					 'alignment'  => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
					 'borders'    => array('top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
										   'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
									   
        
        $sStoreyTypes = array(0=>'Single', 1=>'Double', 2=>'Triple', 3=>'Bespoke');
        $sSchoolStoreyTypes = array(0=>'S', 1=>'D', 2=>'T', 3=>'B');
        
        foreach($sSchoolStoreyTypes as $key => $sSchoolType){

            $objPhpExcel->createSheet(NULL, "{$sStoreyTypes[$key]} Storey Stages");
            $objPhpExcel->setActiveSheetIndex($key);
        
            $iRow            = 1;
            $iTotalUnit      = 0;   
            $iTotalWeightage = 0;
            $iTotalDays      = 0;  
                    
            $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "Level 1");
            $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "Level 2");
            $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "Level 3");
            $objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "Level 4");
            $objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", "Unit");
            $objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", "Weightage");
            $objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", "Duration");
            $objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingStyle, "A1:G1");
            
            $iRow++;
            
            $sSQL = "SELECT id, name, unit, weightage, days FROM tbl_stages WHERE parent_id='0' AND `type`='$sSchoolType' ORDER BY position";
            $objDb->query($sSQL);
            $iCount = $objDb->getCount( );
            
            for ($i = 0; $i < $iCount; $i ++){

                $iParent    = $objDb->getField($i, "id");
                $sStage     = $objDb->getField($i, "name");
                $iUnit      = $objDb->getField($i, "unit");
                $iWeightage = $objDb->getField($i, "weightage");
                $iDays      = $objDb->getField($i, "days");
                
                $iTotalUnit      += $iUnit;   
                $iTotalWeightage += $iWeightage;
                $iTotalDays      += $iDays;  
            
                $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", $sStage);
                $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "");
                $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "");
                $objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "");
                $objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", $iUnit);
                $objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", $iWeightage);
                $objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", $iDays);
                $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, "A{$iRow}:G{$iRow}");
                
                $iRow++;
                
                $sSQL2 = "SELECT id, name, unit, weightage, days FROM tbl_stages WHERE parent_id='$iParent' ORDER BY position";
                $objDb2->query($sSQL2);
                $iCount2 = $objDb2->getCount( );

                for ($j = 0; $j < $iCount2; $j ++){

                    $iParent2    = $objDb2->getField($j, "id");
                    $sStage2     = $objDb2->getField($j, "name");
                    $iUnit2      = $objDb2->getField($j, "unit");
                    $iWeightage2 = $objDb2->getField($j, "weightage");
                    $iDays2      = $objDb2->getField($j, "days");

                    $iTotalUnit      += $iUnit2;   
                    $iTotalWeightage += $iWeightage2;
                    $iTotalDays      += $iDays2;  

                    $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "");
                    $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", $sStage2);
                    $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "");
                    $objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "");
                    $objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", $iUnit2);
                    $objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", $iWeightage2);
                    $objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", $iDays2);
                    $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, "A{$iRow}:G{$iRow}");
                    
                    $iRow++;
                
                    $sSQL3 = "SELECT id, name, unit, weightage, days FROM tbl_stages WHERE parent_id='$iParent2' ORDER BY position";
                    $objDb3->query($sSQL3);
                    $iCount3 = $objDb3->getCount( );

                    for ($k = 0; $k < $iCount3; $k ++){

                        $iParent3    = $objDb3->getField($k, "id");
                        $sStage3     = $objDb3->getField($k, "name");
                        $iUnit3      = $objDb3->getField($k, "unit");
                        $iWeightage3 = $objDb3->getField($k, "weightage");
                        $iDays3      = $objDb3->getField($k, "days");
                        
                        $iTotalUnit      += $iUnit3;   
                        $iTotalWeightage += $iWeightage3;
                        $iTotalDays      += $iDays3;  

                        $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "");
                        $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "");
                        $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", $sStage3);
                        $objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "");
                        $objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", $iUnit3);
                        $objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", $iWeightage3);
                        $objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", $iDays3);
                        $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, "A{$iRow}:G{$iRow}");
                        
                        $iRow++;
                        
                        $sSQL4 = "SELECT id, name, unit, weightage, days FROM tbl_stages WHERE parent_id='$iParent3' ORDER BY position";
                        $objDb4->query($sSQL4);
                        $iCount4 = $objDb4->getCount( );
                        
                        for ($m = 0; $m < $iCount4; $m ++){

                            $iParent4    = $objDb4->getField($m, "id");
                            $sStage4     = $objDb4->getField($m, "name");
                            $iUnit4      = $objDb4->getField($m, "unit");
                            $iWeightage4 = $objDb4->getField($m, "weightage");
                            $iDays4      = $objDb4->getField($m, "days");

                            $iTotalUnit      += $iUnit4;   
                            $iTotalWeightage += $iWeightage4;
                            $iTotalDays      += $iDays4;  

                            $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "");
                            $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "");
                            $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "");
                            $objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", $sStage4);
                            $objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", $iUnit4);
                            $objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", $iWeightage4);
                            $objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", $iDays4);
                            $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, "A{$iRow}:G{$iRow}");

                            $iRow++;
                        }// level 4
                    
                    }//level 3
                }//level 2
            }//level 1
            
            $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "Totals");
            $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "");
            $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "");
            $objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "");
            $objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", "");
            $objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", $iTotalWeightage);
            $objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", $iTotalDays);
            $objPhpExcel->getActiveSheet()->duplicateStyleArray($sTotalStyle, "A{$iRow}:G{$iRow}");
            
            $objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(45);
            $objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(45);
            $objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(45);
            $objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(45);
            $objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(15);
            $objPhpExcel->getActiveSheet()->getColumnDimension("F")->setWidth(15);
            $objPhpExcel->getActiveSheet()->getColumnDimension("G")->setWidth(15);

            $objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
            $objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B {$sStoreyTypes[$key]} Storey Stages &R Generated on ".date("d-M-Y"));

            $objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
            $objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            $objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
            $objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
            $objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
            $objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

            $objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

            $objPhpExcel->getActiveSheet()->setTitle("{$sStoreyTypes[$key]} Storey Stages Report");
        }
        
        ////////////////////////// Download File ///////////////////////////////
		
	$objPhpExcel->setActiveSheetIndex(0);
		
	$sExcelFile = "Storey Wise Stages Report.xlsx";

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