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

//	if (!@strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))
//		die("ERROR: Invalid Request");


	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );
	

        
	$objPhpExcel = new PHPExcel( );
	
	$objPhpExcel->getProperties()->setCreator($_SESSION["SiteTitle"])
								 ->setLastModifiedBy($_SESSION["SiteTitle"])
								 ->setTitle("Inspections")
								 ->setSubject("Weekly Progress Report")
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


	///////////////// Previous Week Stats Province Wise //////////
	
	$sLastStatsDate     = getDbValue("date", "tbl_weekly_stats", "", "date DESC");
	$sPreviousWeekStats = array();
	
	$sSQL = "SELECT COUNT(ws.school_id) as _MobilisedSchools, SUM(IF(ws.completed='Y', '1', '0')) AS _CompletedCount,  
	                s.province_id, 
				    p.schools, p.title
			 FROM tbl_weekly_stats ws, tbl_packages p, tbl_schools s
			 WHERE s.id=ws.school_id AND FIND_IN_SET(ws.school_id, p.schools) AND ws.date='$sLastStatsDate'
			 GROUP BY p.title
			 ORDER BY s.province_id, p.title";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
	{		
		$sSchools          = $objDb->getField($i, "schools");
		$sPackage          = $objDb->getField($i, "title");
		$iSchoolsMobilised = $objDb->getField($i, "_MobilisedSchools");
		$iSchoolsCompleted = $objDb->getField($i, "_CompletedCount");

		
		$sPreviousWeekStats[$sPackage] = array('MobilizedSchoolsCount' => $iSchoolsMobilised, 
                                               'MobilizedSchools'      => $sSchools,
											   'Completed'             => $iSchoolsCompleted);
	}
	
	////////////////////////////////// Province Wise Weekly Stats /////////////////////////////

	$sSQL = "SELECT COUNT(s.id) as _MobilisedSchools, SUM(IF(s.completed='Y', '1', '0')) AS _CompletedCount, s.province_id, 
	                p.title, p.schools
			 FROM tbl_schools s, tbl_packages p
			 WHERE s.status='A' AND s.dropped!='Y' AND s.qualified='Y' AND FIND_IN_SET(s.id, p.schools)
			       AND s.id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE stage_id IN ($sMilestoneStages))
			 GROUP BY p.title
			 ORDER BY s.province_id, p.title";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
        
	$iLastProvince = 0;
	$iRow          = -2;
	
	for($i=0; $i < $iCount ; $i++)
	{            
		$sSchools          = $objDb->getField($i, "schools");
		$iProvince         = $objDb->getField($i, "province_id");
		$sPackage          = $objDb->getField($i, "title");
		$iSchoolsMobilised = $objDb->getField($i, "_MobilisedSchools");
		$iSchoolsCompleted = $objDb->getField($i, "_CompletedCount");
		
		if ($iLastProvince != $iProvince)
		{            
			$iRow += 3;
		
			$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", (getDbValue("name", "tbl_provinces", "id='$iProvince'")." Schools Weekly Progress Summary"));
			$objPhpExcel->getActiveSheet()->getStyle("A{$iRow}")->getFont()->setSize(16);
			
			$iRow ++;

			
			$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "Package Name");
			
			$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "No. of Schools Mobilised");
			$objPhpExcel->getActiveSheet()->mergeCells("B{$iRow}:C{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "Milestone - 1");
			$objPhpExcel->getActiveSheet()->mergeCells("D{$iRow}:E{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", "Milestone - 2");
			$objPhpExcel->getActiveSheet()->mergeCells("F{$iRow}:G{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("H{$iRow}", "Milestone - 3");
			$objPhpExcel->getActiveSheet()->mergeCells("H{$iRow}:I{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", "Milestone - 4");
			$objPhpExcel->getActiveSheet()->mergeCells("J{$iRow}:K{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", "Milestone - 5");
			$objPhpExcel->getActiveSheet()->mergeCells("L{$iRow}:M{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", "Schools Completed");
			$objPhpExcel->getActiveSheet()->mergeCells("N{$iRow}:O{$iRow}");

			
			for ($j = 0; $j < 15; $j ++)
				$objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
			
			$objPhpExcel->getActiveSheet()->getStyle("B{$iRow}:O{$iRow}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			
			$iRow ++;

			$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "");
			$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "Last Week");
                        $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("H{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", "Today");
			
			for ($j = 0; $j < 15; $j ++)
				$objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
			
			$iRow ++;
		}

		
		$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", $sPackage);
                $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", @$sPreviousWeekStats[$sPackage]['MobilizedSchoolsCount']);
		$objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", $iSchoolsMobilised);

                $iMilestonNumber = 1;
		
		for ($j = 2; $j <= 10 ; $j += 2 )
		{
			$iSchools      = getDbValue("COUNT(1)", "tbl_schools", "status='A' AND dropped!='Y' AND qualified='Y' AND id IN ($sSchools) AND last_milestone_id IN (SELECT id FROM tbl_stages WHERE name LIKE '%Milestone-{$iMilestonNumber}%')");
			$sSchools      = @$sPreviousWeekStats[$sPackage]['MobilizedSchools'];
			$iSchoolsStats = getDbValue("COUNT(1)", "tbl_weekly_stats", "school_id IN ($sSchools) AND milestone_id IN (SELECT id FROM tbl_stages WHERE name LIKE '%Milestone-{$iMilestonNumber}%')");

			$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(($j + 1), $iRow, formatNumber($iSchoolsStats, false));
			$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(($j + 2), $iRow, formatNumber($iSchools, false));
			
			$iMilestonNumber++;
		}

		$objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", @$sPreviousWeekStats[$sPackage]['Completed']);
		$objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", $iSchoolsCompleted);
		

		for ($j = 0; $j < 15; $j ++)
			$objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));

		$iRow ++;
		$iLastProvince = $iProvince;
	}
        
		
	$objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(70);
	$objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("F")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("G")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("H")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("I")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("J")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("K")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("L")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("M")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("N")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("O")->setWidth(15);

	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Weekly Progress Report &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Province Wise Progress Report");


	/////////////////////////////////Last Week District Wise /////////////////////////
	
	$sPreviousWeekStats = array();
	
	$sSQL = "SELECT COUNT(ws.school_id) as _MobilisedSchools, SUM(IF(ws.completed='Y', '1', '0')) AS _CompletedCount, s.district_id
			 FROM tbl_weekly_stats ws, tbl_schools s
			 WHERE ws.school_id=s.id AND ws.date='$sLastStatsDate'
			 GROUP BY s.district_id";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
	{		
		$iDistrict         = $objDb->getField($i, "district_id");
		$iSchoolsMobilised = $objDb->getField($i, "_MobilisedSchools");
		$iSchoolsCompleted = $objDb->getField($i, "_CompletedCount");

		
		$sPreviousWeekStats[$iDistrict] = array('MobilizedSchoolsCount' => $iSchoolsMobilised,
                                                'Completed'             => $iSchoolsCompleted);
	}
	
	////////////////////////////////// District Wise Progress Report //////////////////////
	
	
	$objPhpExcel->createSheet(NULL, "District Wise Progress Report");
	$objPhpExcel->setActiveSheetIndex(1);
	
			 
	$sSQL = "SELECT COUNT(id) as _MobilisedSchools, SUM(IF(completed='Y', '1', '0')) AS _CompletedCount, province_id, district_id,
	                (SELECT name FROM tbl_districts WHERE id=tbl_schools.district_id) AS _District
			 FROM tbl_schools
			 WHERE status='A' AND dropped!='Y' AND qualified='Y'
			       AND id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE stage_id IN ($sMilestoneStages))
			 GROUP By district_id
			 ORDER BY province_id, _District";
        
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
        
	$iLastProvince = 0;
	$iRow          = -2;
	
	for($i=0; $i < $iCount ; $i++)
	{            
		$iProvince         = $objDb->getField($i, "province_id");
		$iDistrict         = $objDb->getField($i, "district_id");
		$sDistrict         = $objDb->getField($i, "_District");
		$iSchoolsMobilised = $objDb->getField($i, "_MobilisedSchools");
		$iSchoolsCompleted = $objDb->getField($i, "_CompletedCount");
		
		if ($iLastProvince != $iProvince)
		{            
			$iRow += 3;
		
			$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", (getDbValue("name", "tbl_provinces", "id='$iProvince'")." Schools Weekly Progress Summary"));
			$objPhpExcel->getActiveSheet()->getStyle("A{$iRow}")->getFont()->setSize(16);
			
			$iRow ++;

			$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "District");
			
			$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "No. of Schools Mobilised");
			$objPhpExcel->getActiveSheet()->mergeCells("B{$iRow}:C{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "Milestone - 1");
			$objPhpExcel->getActiveSheet()->mergeCells("D{$iRow}:E{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", "Milestone - 2");
			$objPhpExcel->getActiveSheet()->mergeCells("F{$iRow}:G{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("H{$iRow}", "Milestone - 3");
			$objPhpExcel->getActiveSheet()->mergeCells("H{$iRow}:I{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", "Milestone - 4");
			$objPhpExcel->getActiveSheet()->mergeCells("J{$iRow}:K{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", "Milestone - 5");
			$objPhpExcel->getActiveSheet()->mergeCells("L{$iRow}:M{$iRow}");
			
			$objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", "Schools Completed");
			$objPhpExcel->getActiveSheet()->mergeCells("N{$iRow}:O{$iRow}");
			
			
			for ($j = 0; $j < 15; $j ++)
				$objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
			
			$objPhpExcel->getActiveSheet()->getStyle("B{$iRow}:O{$iRow}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			 $iRow ++;
			 

			$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "");
			$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "Last Week");
            $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("H{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", "Today");
			$objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", "Last Week");
			$objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", "Today");
			
			for ($j = 0; $j < 15; $j ++)
				$objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
			
			$iRow ++;
		}

		
		$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}",$sDistrict);
        $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", @$sPreviousWeekStats[$iDistrict]['MobilizedSchoolsCount']);
		$objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", $iSchoolsMobilised);

		
        $iMilestonNumber = 1;
                
		for ($j = 2; $j <= 10 ; $j += 2)
		{                   
			$iSchoolsStats = getDbValue("COUNT(1)", "tbl_weekly_stats ws, tbl_schools s", "ws.school_id=s.id AND s.district_id='$iDistrict' AND ws.milestone_id IN (SELECT id FROM tbl_stages WHERE name LIKE '%Milestone-{$iMilestonNumber}%')");
			$iSchools      = getDbValue("COUNT(1)", "tbl_schools", "status='A' AND dropped!='Y' AND qualified='Y' AND district_id='$iDistrict' AND last_milestone_id IN (SELECT id FROM tbl_stages WHERE name LIKE '%Milestone-{$iMilestonNumber}%')");

			$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(($j + 1), $iRow, formatNumber($iSchoolsStats, false));
			$objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(($j + 2), $iRow, formatNumber($iSchools, false));

			$iMilestonNumber ++;
		}

		$objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", @$sPreviousWeekStats[$iDistrict]['Completed']);
		$objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", $iSchoolsCompleted);
		

		for ($j = 0; $j < 15; $j ++)
			$objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));

		$iRow ++;
		$iLastProvince = $iProvince;
	}
        
		
	$objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(35);
	$objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("F")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("G")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("H")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("I")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("J")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("K")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("L")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("M")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("N")->setWidth(15);
	$objPhpExcel->getActiveSheet()->getColumnDimension("O")->setWidth(15);
	
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Weekly Progress Report &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
	$objPhpExcel->getActiveSheet()->setTitle("District Wise Progress Report");
	

    ////////////////////////////////// Completed schools Previous Week Stats //////////////////////
	
    $iSchoolsList = getList("tbl_weekly_stats", "school_id", "school_id", "date='$sLastStatsDate'");
	
    ////////////////////////////////// Completed schools progress report //////////////////////

	
	$objPhpExcel->createSheet(NULL, "Weekly Progress Report");
	$objPhpExcel->setActiveSheetIndex(2);

	
	$sSQL = "SELECT s.id, s.name, s.code, s.province_id, s.work_type, 
                        s.blocks, s.class_rooms, s.student_toilets, s.staff_rooms, s.staff_toilets, s.science_labs, 
                        s.it_labs, s.exam_halls, s.library, s.clerk_offices, s.principal_office, s.parking_stand,
                        s.chowkidar_hut, s.soakage_pit, s.water_supply, s.stores,
				    cs.start_date, cs.end_date
			 FROM tbl_schools s
					LEFT JOIN tbl_contract_schedules cs
					ON s.id = cs.school_id
			 WHERE s.status='A' AND s.dropped!='Y' AND s.qualified='Y' AND s.completed='Y'
			 ORDER BY s.province_id, s.name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	
	$iLastProvince      = 0;
	$iRow               = -3;
        
        $iTotalBlocks            = 0;
        $iTotalClassRooms        = 0;
        $iTotalStudentToilets    = 0;
        $iTotalStaffRooms        = 0;
        $iTotalStaffToilets      = 0;
        $iTotalScienceLabs       = 0;
        $iTotalITLabs            = 0;
        $iTotalExamHalls         = 0;
        $iTotalLibrary           = 0;
        $iTotalClerkOffice       = 0;
        $iTotalPrincipalOffice   = 0;
        $iTotalParkingStand      = 0;
        $iTotalChowkidarHut      = 0;
        $iTotalSoakagePit        = 0;
        $iTotalWaterSupply       = 0;
        $iTotalStores            = 0;
	
	for($i=0; $i < $iCount ; $i++)
	{            
            
		$iSchool            = $objDb->getField($i, "id");
		$sSchool            = $objDb->getField($i, "name");
		$sCode              = $objDb->getField($i, "code");
		$iProvince          = $objDb->getField($i, "province_id");
		$sWorkType          = $objDb->getField($i, "work_type");
		$sStartDate         = $objDb->getField($i, "start_date");
		$sEndDate           = $objDb->getField($i, "end_date");
                $iBlocks            = $objDb->getField($i, "blocks");
                $iClassRooms        = $objDb->getField($i, "class_rooms");
                $iStudentToilets    = $objDb->getField($i, "student_toilets");
                $iStaffRooms        = $objDb->getField($i, "staff_rooms");
                $iStaffToilets      = $objDb->getField($i, "staff_toilets");
                $iScienceLabs       = $objDb->getField($i, "science_labs");
                $iITLabs            = $objDb->getField($i, "it_labs");
                $iExamHalls         = $objDb->getField($i, "exam_halls");
                $iLibrary           = $objDb->getField($i, "library");
                $iClerkOffice       = $objDb->getField($i, "clerk_offices");
                $iPrincipalOffice   = $objDb->getField($i, "principal_office");
                $iParkingStand      = $objDb->getField($i, "parking_stand");
                $iChowkidarHut      = $objDb->getField($i, "chowkidar_hut");
                $iSoakagePit        = $objDb->getField($i, "soakage_pit");
                $iWaterSupply       = $objDb->getField($i, "water_supply");
                $iStores            = $objDb->getField($i, "stores");
                
                
		if ($iLastProvince != $iProvince)
		{            
                    
                        if($iRow != '-3')
                        {
                            
                            $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "Totals");
                            $objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", $iTotalBlocks);
                            $objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", $iTotalClassRooms);
                            $objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", $iTotalStaffRooms);
                            $objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", $iTotalStudentToilets);
                            $objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", $iTotalStaffToilets);
                            $objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", $iTotalScienceLabs);
                            $objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", $iTotalITLabs);
                            $objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", $iTotalExamHalls);
                            $objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", $iTotalLibrary);
                            $objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", $iTotalClerkOffice);
                            $objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", $iTotalPrincipalOffice);
                            $objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", $iTotalParkingStand);
                            $objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", $iTotalChowkidarHut);
                            $objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", $iTotalSoakagePit);
                            $objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", $iTotalWaterSupply);
                            $objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", $iTotalStores);
                            
                            for ($j = 0; $j < 24; $j ++)
				$objPhpExcel->getActiveSheet()->duplicateStyleArray($sTotalStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
			
                            $iTotalBlocks            = 0;
                            $iTotalClassRooms        = 0;
                            $iTotalStudentToilets    = 0;
                            $iTotalStaffRooms        = 0;
                            $iTotalStaffToilets      = 0;
                            $iTotalScienceLabs       = 0;
                            $iTotalITLabs            = 0;
                            $iTotalExamHalls         = 0;
                            $iTotalLibrary           = 0;
                            $iTotalClerkOffice       = 0;
                            $iTotalPrincipalOffice   = 0;
                            $iTotalParkingStand      = 0;
                            $iTotalChowkidarHut      = 0;
                            $iTotalSoakagePit        = 0;
                            $iTotalWaterSupply       = 0;
                            $iTotalStores            = 0;
                            
                        }
                        
			$iRow += 4;
		
			$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", (getDbValue("name", "tbl_provinces", "id='$iProvince'")." Completed Schools List"));
			$objPhpExcel->getActiveSheet()->getStyle("A{$iRow}")->getFont()->setSize(16);
			
			$iRow ++;

			$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "School Name");
			$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "EMIS Code");
			$objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "Scope of Work");
			$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "Planned Start Date");
			$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", "Planned End Date");
			$objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", "Actual Start Date");
			$objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", "Actual End Date");
			$objPhpExcel->getActiveSheet()->setCellValue("H{$iRow}", "Final Contract");
                        $objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", "Blocks");
                        $objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", "Class Rooms");
                        $objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", "Staff Rooms");
                        $objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", "Student Toilets");
                        $objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", "Staff Toilets");
                        $objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", "Science Labs");
                        $objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", "IT Labs");
                        $objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", "Exam Halls");
                        $objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", "Library");
                        $objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", "Clerk Offices");
                        $objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", "Princial Office");
                        $objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", "Parking Stand");
                        $objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", "Chowkidar Hut");
                        $objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", "Soakge Pit");
                        $objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", "Water Supply");
                        $objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", "Stores");
                        
			
			
			for ($j = 0; $j < 24; $j ++)
				$objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
			
			$iRow ++;
		}
                
                $iTotalBlocks            += $iBlocks;
                $iTotalClassRooms        += $iClassRooms;
                $iTotalStudentToilets    += $iStudentToilets;
                $iTotalStaffRooms        += $iStaffRooms;
                $iTotalStaffToilets      += $iStaffToilets;
                $iTotalScienceLabs       += $iScienceLabs;
                $iTotalITLabs            += $iITLabs;
                $iTotalExamHalls         += $iExamHalls;
                $iTotalLibrary           += $iLibrary;
                $iTotalClerkOffice       += $iClerkOffice;
                $iTotalPrincipalOffice   += $iPrincipalOffice;
                $iTotalParkingStand      += $iParkingStand;
                $iTotalChowkidarHut      += $iChowkidarHut;
                $iTotalSoakagePit        += $iSoakagePit;
                $iTotalWaterSupply       += $iWaterSupply;
                $iTotalStores            += $iStores;

		$sActualStartDate = getDbValue("i.date", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.name LIKE '%Commencement Letter%' AND i.school_id = '$iSchool'", "i.date DESC");
                if(empty($sActualStartDate))
                    $sActualStartDate = '';
                
                $sActualEndDate   = getDbValue("i.date", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.name LIKE '%Finishing & Demobilization%' AND i.school_id = '$iSchool' AND i.stage_completed='Y' AND i.status='P'", "i.date DESC");
                if(empty($sActualEndDate))
                     $sActualEndDate = '';
                
		$objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", $sSchool);
		$objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", $sCode);
		$objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", ($sWorkType == 'B'?'New Construction & Rehabilitation' : ($sWorkType == 'N' ? 'New Construction' : 'Rehabilitation Only')));
		$objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", $sStartDate);
		$objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", $sEndDate);
                $objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", $sActualStartDate);
		$objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", $sActualEndDate);
		$objPhpExcel->getActiveSheet()->setCellValue("H{$iRow}", 'N/A');
                $objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", $iBlocks);
                $objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", $iClassRooms);
                $objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", $iStaffRooms);
                $objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", $iStudentToilets);
                $objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", $iStaffToilets);
                $objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", $iScienceLabs);
                $objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", $iITLabs);
                $objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", $iExamHalls);
                $objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", $iLibrary);
                $objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", $iClerkOffice);
                $objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", $iPrincipalOffice);
                $objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", $iParkingStand);
                $objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", $iChowkidarHut);
                $objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", $iSoakagePit);
                $objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", $iWaterSupply);
                $objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", $iStores);


		for ($j = 0; $j < 24; $j ++)
		{
			if (@!in_array($iSchool, $iSchoolsList))	
				$objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyleHighlight, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
			
			else
				$objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
		}
                
		$iRow ++;
                $iLastProvince = $iProvince;
                
                if($i+1 == $iCount){
                    $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "Totals");
                    $objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", $iTotalBlocks);
                    $objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", $iTotalClassRooms);
                    $objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", $iTotalStaffRooms);
                    $objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", $iTotalStudentToilets);
                    $objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", $iTotalStaffToilets);
                    $objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", $iTotalScienceLabs);
                    $objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", $iTotalITLabs);
                    $objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", $iTotalExamHalls);
                    $objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", $iTotalLibrary);
                    $objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", $iTotalClerkOffice);
                    $objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", $iTotalPrincipalOffice);
                    $objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", $iTotalParkingStand);
                    $objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", $iTotalChowkidarHut);
                    $objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", $iTotalSoakagePit);
                    $objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", $iTotalWaterSupply);
                    $objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", $iTotalStores);
                    
                    for ($j = 0; $j < 24; $j ++)
				$objPhpExcel->getActiveSheet()->duplicateStyleArray($sTotalStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
                }
	}
        
   	$objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(50);
	$objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(40);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("F")->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension("G")->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension("H")->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension("I")->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension("J")->setWidth(20);
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


	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Weekly Progress Report &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Completed Schools Report");
        
        //////////////////////////////////Completed Schools List /////////////////////////////////////////
          $iSchoolsCompletedList = getList("tbl_schools", "id", "id", "status='A' AND dropped!='Y' AND qualified='Y' AND completed='Y'");  
        
        ////////////////////////////////// Completed schools Detail progress report //////////////////////

	
	$objPhpExcel->createSheet(NULL, "Weekly Progress Report");
	$objPhpExcel->setActiveSheetIndex(3);

        $SchoolsData = array();

	$sSQL = "SELECT s.id, s.name, s.code, s.province_id, s.work_type, 
                        s.blocks, s.class_rooms, s.student_toilets, s.staff_rooms, s.staff_toilets, s.science_labs, 
                        s.it_labs, s.exam_halls, s.library, s.clerk_offices, s.principal_office, s.parking_stand,
                        s.chowkidar_hut, s.soakage_pit, s.water_supply, s.stores,
				    cs.start_date, cs.end_date
			 FROM tbl_schools s
					LEFT JOIN tbl_contract_schedules cs
					ON s.id = cs.school_id
			 WHERE s.blocks = '1' AND s.id IN (".  implode(',', $iSchoolsCompletedList).")
			 ORDER BY s.province_id, s.name";
        $objDb->query($sSQL);

	$iCount = $objDb->getCount( );
        
        for($i=0; $i < $iCount; $i++){
            
                $iSchool            = $objDb->getField($i, "id");
		$sSchool            = $objDb->getField($i, "name");
		$sCode              = $objDb->getField($i, "code");
		$iProvince          = $objDb->getField($i, "province_id");
		$sWorkType          = $objDb->getField($i, "work_type");
		$sStartDate         = $objDb->getField($i, "start_date");
		$sEndDate           = $objDb->getField($i, "end_date");
                $iBlocks            = $objDb->getField($i, "blocks");
                $iClassRooms        = $objDb->getField($i, "class_rooms");
                $iStudentToilets    = $objDb->getField($i, "student_toilets");
                $iStaffRooms        = $objDb->getField($i, "staff_rooms");
                $iStaffToilets      = $objDb->getField($i, "staff_toilets");
                $iScienceLabs       = $objDb->getField($i, "science_labs");
                $iITLabs            = $objDb->getField($i, "it_labs");
                $iExamHalls         = $objDb->getField($i, "exam_halls");
                $iLibrary           = $objDb->getField($i, "library");
                $iClerkOffice       = $objDb->getField($i, "clerk_offices");
                $iPrincipalOffice   = $objDb->getField($i, "principal_office");
                $iParkingStand      = $objDb->getField($i, "parking_stand");
                $iChowkidarHut      = $objDb->getField($i, "chowkidar_hut");
                $iSoakagePit        = $objDb->getField($i, "soakage_pit");
                $iWaterSupply       = $objDb->getField($i, "water_supply");
                $iStores            = $objDb->getField($i, "stores");
                
                $sActualStartDate = getDbValue("i.date", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.name LIKE '%Commencement Letter%' AND i.school_id = '$iSchool'", "i.date DESC");
                if(empty($sActualStartDate))
                    $sActualStartDate = '';
                
                $sActualEndDate   = getDbValue("i.date", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.name LIKE '%Finishing & Demobilization%' AND i.school_id = '$iSchool' AND i.stage_completed='Y' AND i.status='P'", "i.date DESC");
                if(empty($sActualEndDate))
                     $sActualEndDate = '';
                
                $SchoolsData[$iProvince][$sWorkType][$iSchool] =  array('name' => $sSchool, 'code' => $sCode, 'work_type' => ($sWorkType == 'R'?'Rehabilitation Only' : 'New Construction'), 'start_date' => $sStartDate, 'end_date' => $sEndDate, 'actual_start_date' => $sActualStartDate, 'actual_end_date' => $sActualEndDate, 'final_contract' => 'N/A', 'blocks' => $iBlocks, 'class_rooms' => $iClassRooms, 'staff_rooms' => $iStaffRooms, 'student_toilets' => $iStudentToilets, 'staff_toilets' => $iStaffRooms, 'science_labs' => $iScienceLabs, 'it_labs' => $iITLabs, 'exam_halls' => $iExamHalls, 'library' => $iLibrary, 'clerk_offices' => $iClerkOffice, 'principal_office' => $iPrincipalOffice, 'parking_stand' => $iParkingStand, 'chowkidar_hut' => $iChowkidarHut, 'soakage_pit' => $iSoakagePit, 'water_supply' => $iWaterSupply, 'stores' => $iStores);
        }
        
        $sSQL2 = "SELECT sb.school_id, s.name, s.code, s.province_id, 
                        sb.work_type, count(1) as blocks, SUM(sb.class_rooms) class_rooms, SUM(sb.student_toilets) as student_toilets, SUM(sb.staff_rooms) as staff_rooms, SUM(sb.staff_toilets) as staff_toilets, 
                        SUM(sb.science_labs) as science_labs, SUM(sb.it_labs) as it_labs, SUM(sb.exam_halls) as exam_halls, SUM(sb.library) as library, SUM(sb.clerk_offices) as clerk_offices, SUM(sb.principal_office) as principal_office, 
                        SUM(sb.parking_stand) as parking_stand, SUM(sb.chowkidar_hut) as chowkidar_hut, SUM(sb.soakage_pit) as soakage_pit, SUM(sb.water_supply) as water_supply, SUM(sb.stores) stores,
				    cs.start_date, cs.end_date
			 FROM tbl_school_blocks sb, tbl_schools s
					LEFT JOIN tbl_contract_schedules cs
					ON s.id = cs.school_id
			 WHERE s.id = sb.school_id AND s.blocks != '1' AND sb.school_id IN (".  implode(',', $iSchoolsCompletedList).") AND sb.school_id != '0'
			 Group By sb.school_id, sb.work_type
                         ORDER BY s.province_id, s.name";
       $objDb2->query($sSQL2);

	$iCount2 = $objDb2->getCount( );
        
        for($i=0; $i < $iCount2; $i++){
            
                $iSchool            = $objDb2->getField($i, "school_id");
		$sSchool            = $objDb2->getField($i, "name");
		$sCode              = $objDb2->getField($i, "code");
		$iProvince          = $objDb2->getField($i, "province_id");
		$sWorkType          = $objDb2->getField($i, "work_type");
		$sStartDate         = $objDb2->getField($i, "start_date");
		$sEndDate           = $objDb2->getField($i, "end_date");
                $iBlocks            = $objDb2->getField($i, "blocks");
                $iClassRooms        = $objDb2->getField($i, "class_rooms");
                $iStudentToilets    = $objDb2->getField($i, "student_toilets");
                $iStaffRooms        = $objDb2->getField($i, "staff_rooms");
                $iStaffToilets      = $objDb2->getField($i, "staff_toilets");
                $iScienceLabs       = $objDb2->getField($i, "science_labs");
                $iITLabs            = $objDb2->getField($i, "it_labs");
                $iExamHalls         = $objDb2->getField($i, "exam_halls");
                $iLibrary           = $objDb2->getField($i, "library");
                $iClerkOffice       = $objDb2->getField($i, "clerk_offices");
                $iPrincipalOffice   = $objDb2->getField($i, "principal_office");
                $iParkingStand      = $objDb2->getField($i, "parking_stand");
                $iChowkidarHut      = $objDb2->getField($i, "chowkidar_hut");
                $iSoakagePit        = $objDb2->getField($i, "soakage_pit");
                $iWaterSupply       = $objDb2->getField($i, "water_supply");
                $iStores            = $objDb2->getField($i, "stores");
                
                $sActualStartDate = getDbValue("i.date", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.name LIKE '%Commencement Letter%' AND i.school_id = '$iSchool'", "i.date DESC");
                if(empty($sActualStartDate))
                    $sActualStartDate = '';
                
                $sActualEndDate   = getDbValue("i.date", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.name LIKE '%Finishing & Demobilization%' AND i.school_id = '$iSchool' AND i.stage_completed='Y' AND i.status='P'", "i.date DESC");
                if(empty($sActualEndDate))
                     $sActualEndDate = '';
                
                $SchoolsData[$iProvince][$sWorkType][$iSchool] =  array('name' => $sSchool, 'code' => $sCode, 'work_type' => ($sWorkType == 'R'?'Rehabilitation Only' : 'New Construction'), 'start_date' => $sStartDate, 'end_date' => $sEndDate, 'actual_start_date' => $sActualStartDate, 'actual_end_date' => $sActualEndDate, 'final_contract' => 'N/A', 'blocks' => $iBlocks, 'class_rooms' => $iClassRooms, 'staff_rooms' => $iStaffRooms, 'student_toilets' => $iStudentToilets, 'staff_toilets' => $iStaffRooms, 'science_labs' => $iScienceLabs, 'it_labs' => $iITLabs, 'exam_halls' => $iExamHalls, 'library' => $iLibrary, 'clerk_offices' => $iClerkOffice, 'principal_office' => $iPrincipalOffice, 'parking_stand' => $iParkingStand, 'chowkidar_hut' => $iChowkidarHut, 'soakage_pit' => $iSoakagePit, 'water_supply' => $iWaterSupply, 'stores' => $iStores);
        }
	
        $iLastProvince      = 0;
        $sLastWorkType      = '';
	$iRow               = -3;
        
        $iTotalBlocks            = 0;
        $iTotalClassRooms        = 0;
        $iTotalStudentToilets    = 0;
        $iTotalStaffRooms        = 0;
        $iTotalStaffToilets      = 0;
        $iTotalScienceLabs       = 0;
        $iTotalITLabs            = 0;
        $iTotalExamHalls         = 0;
        $iTotalLibrary           = 0;
        $iTotalClerkOffice       = 0;
        $iTotalPrincipalOffice   = 0;
        $iTotalParkingStand      = 0;
        $iTotalChowkidarHut      = 0;
        $iTotalSoakagePit        = 0;
        $iTotalWaterSupply       = 0;
        $iTotalStores            = 0;
        $StepCount               = 0;
	$iRowsCount              = $iCount + $iCount2;
        
        foreach($SchoolsData as $iProvinceId => $sProvinceWiseData){
            foreach($sProvinceWiseData as $sWorkTypeKey => $sWorkTypeData){
                foreach($sWorkTypeData as $iSchool => $sSchoolData){
                    
                    $sSchool            = $sSchoolData["name"];
                    $sCode              = $sSchoolData["code"];
                    $iProvince          = $sSchoolData["province_id"];
                    $sWorkType          = $sSchoolData["work_type"];
                    $sStartDate         = $sSchoolData["start_date"];
                    $sEndDate           = $sSchoolData["end_date"];
                    $sActualStartDate   = $sSchoolData["actual_start_date"];
                    $sActualEndDate     = $sSchoolData["actual_end_date"];
                    $iBlocks            = $sSchoolData["blocks"];
                    $iClassRooms        = $sSchoolData["class_rooms"];
                    $iStudentToilets    = $sSchoolData["student_toilets"];
                    $iStaffRooms        = $sSchoolData["staff_rooms"];
                    $iStaffToilets      = $sSchoolData["staff_toilets"];
                    $iScienceLabs       = $sSchoolData["science_labs"];
                    $iITLabs            = $sSchoolData["it_labs"];
                    $iExamHalls         = $sSchoolData["exam_halls"];
                    $iLibrary           = $sSchoolData["library"];
                    $iClerkOffice       = $sSchoolData["clerk_offices"];
                    $iPrincipalOffice   = $sSchoolData["principal_office"];
                    $iParkingStand      = $sSchoolData["parking_stand"];
                    $iChowkidarHut      = $sSchoolData["chowkidar_hut"];
                    $iSoakagePit        = $sSchoolData["soakage_pit"];
                    $iWaterSupply       = $sSchoolData["water_supply"];
                    $iStores            = $sSchoolData["stores"];
                
                    if (($iLastProvince != $iProvinceId) || ($sLastWorkType != $sWorkTypeKey)){
                        if($iRow != '-3')
                        {
                            
                            $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "Totals");
                            $objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", $iTotalBlocks);
                            $objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", $iTotalClassRooms);
                            $objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", $iTotalStaffRooms);
                            $objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", $iTotalStudentToilets);
                            $objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", $iTotalStaffToilets);
                            $objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", $iTotalScienceLabs);
                            $objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", $iTotalITLabs);
                            $objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", $iTotalExamHalls);
                            $objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", $iTotalLibrary);
                            $objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", $iTotalClerkOffice);
                            $objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", $iTotalPrincipalOffice);
                            $objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", $iTotalParkingStand);
                            $objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", $iTotalChowkidarHut);
                            $objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", $iTotalSoakagePit);
                            $objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", $iTotalWaterSupply);
                            $objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", $iTotalStores);
                            
                            for ($j = 0; $j < 24; $j ++)
				$objPhpExcel->getActiveSheet()->duplicateStyleArray($sTotalStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
			
                            $iTotalBlocks            = 0;
                            $iTotalClassRooms        = 0;
                            $iTotalStudentToilets    = 0;
                            $iTotalStaffRooms        = 0;
                            $iTotalStaffToilets      = 0;
                            $iTotalScienceLabs       = 0;
                            $iTotalITLabs            = 0;
                            $iTotalExamHalls         = 0;
                            $iTotalLibrary           = 0;
                            $iTotalClerkOffice       = 0;
                            $iTotalPrincipalOffice   = 0;
                            $iTotalParkingStand      = 0;
                            $iTotalChowkidarHut      = 0;
                            $iTotalSoakagePit        = 0;
                            $iTotalWaterSupply       = 0;
                            $iTotalStores            = 0;
                            
                        }    //end if != -3                     
                            $iRow += 4;
                        
                            $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", (getDbValue("name", "tbl_provinces", "id='$iProvinceId'")." Completed Schools List ($sWorkType)"));
                            $objPhpExcel->getActiveSheet()->getStyle("A{$iRow}")->getFont()->setSize(16);

                            $iRow ++;

                            $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "School Name");
                            $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", "EMIS Code");
                            $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", "Scope of Work");
                            $objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", "Planned Start Date");
                            $objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", "Planned End Date");
                            $objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", "Actual Start Date");
                            $objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", "Actual End Date");
                            $objPhpExcel->getActiveSheet()->setCellValue("H{$iRow}", "Final Contract");
                            $objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", "Blocks");
                            $objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", "Class Rooms");
                            $objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", "Staff Rooms");
                            $objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", "Student Toilets");
                            $objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", "Staff Toilets");
                            $objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", "Science Labs");
                            $objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", "IT Labs");
                            $objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", "Exam Halls");
                            $objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", "Library");
                            $objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", "Clerk Offices");
                            $objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", "Princial Office");
                            $objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", "Parking Stand");
                            $objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", "Chowkidar Hut");
                            $objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", "Soakge Pit");
                            $objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", "Water Supply");
                            $objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", "Stores");

                            for ($j = 0; $j < 24; $j ++)
                                    $objPhpExcel->getActiveSheet()->duplicateStyleArray($sHeadingStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));

                            $iRow ++;
                    } // end if ($iLastProvince != $iProvinceId)
                    
                    $iTotalBlocks            += $iBlocks;
                    $iTotalClassRooms        += $iClassRooms;
                    $iTotalStudentToilets    += $iStudentToilets;
                    $iTotalStaffRooms        += $iStaffRooms;
                    $iTotalStaffToilets      += $iStaffToilets;
                    $iTotalScienceLabs       += $iScienceLabs;
                    $iTotalITLabs            += $iITLabs;
                    $iTotalExamHalls         += $iExamHalls;
                    $iTotalLibrary           += $iLibrary;
                    $iTotalClerkOffice       += $iClerkOffice;
                    $iTotalPrincipalOffice   += $iPrincipalOffice;
                    $iTotalParkingStand      += $iParkingStand;
                    $iTotalChowkidarHut      += $iChowkidarHut;
                    $iTotalSoakagePit        += $iSoakagePit;
                    $iTotalWaterSupply       += $iWaterSupply;
                    $iTotalStores            += $iStores;
                    
                    $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", $sSchool);
                    $objPhpExcel->getActiveSheet()->setCellValue("B{$iRow}", $sCode);
                    $objPhpExcel->getActiveSheet()->setCellValue("C{$iRow}", $sWorkType);
                    $objPhpExcel->getActiveSheet()->setCellValue("D{$iRow}", $sStartDate);
                    $objPhpExcel->getActiveSheet()->setCellValue("E{$iRow}", $sEndDate);
                    $objPhpExcel->getActiveSheet()->setCellValue("F{$iRow}", $sActualStartDate);
                    $objPhpExcel->getActiveSheet()->setCellValue("G{$iRow}", $sActualEndDate);
                    $objPhpExcel->getActiveSheet()->setCellValue("H{$iRow}", 'N/A');
                    $objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", $iBlocks);
                    $objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", $iClassRooms);
                    $objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", $iStaffRooms);
                    $objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", $iStudentToilets);
                    $objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", $iStaffToilets);
                    $objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", $iScienceLabs);
                    $objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", $iITLabs);
                    $objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", $iExamHalls);
                    $objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", $iLibrary);
                    $objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", $iClerkOffice);
                    $objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", $iPrincipalOffice);
                    $objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", $iParkingStand);
                    $objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", $iChowkidarHut);
                    $objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", $iSoakagePit);
                    $objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", $iWaterSupply);
                    $objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", $iStores);


                    for ($j = 0; $j < 24; $j ++)
                    {
                            if (@!in_array($iSchool, $iSchoolsList))	
                                    $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyleHighlight, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));

                            else
                                    $objPhpExcel->getActiveSheet()->duplicateStyleArray($sBorderStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
                    }
                    
                    $iRow ++;
                    $StepCount ++;
                    $iLastProvince = $iProvinceId;
                    $sLastWorkType = $sWorkTypeKey;
                    
                    if($StepCount == $iRowsCount){
                        $objPhpExcel->getActiveSheet()->setCellValue("A{$iRow}", "Totals");
                        $objPhpExcel->getActiveSheet()->setCellValue("I{$iRow}", $iTotalBlocks);
                        $objPhpExcel->getActiveSheet()->setCellValue("J{$iRow}", $iTotalClassRooms);
                        $objPhpExcel->getActiveSheet()->setCellValue("K{$iRow}", $iTotalStaffRooms);
                        $objPhpExcel->getActiveSheet()->setCellValue("L{$iRow}", $iTotalStudentToilets);
                        $objPhpExcel->getActiveSheet()->setCellValue("M{$iRow}", $iTotalStaffToilets);
                        $objPhpExcel->getActiveSheet()->setCellValue("N{$iRow}", $iTotalScienceLabs);
                        $objPhpExcel->getActiveSheet()->setCellValue("O{$iRow}", $iTotalITLabs);
                        $objPhpExcel->getActiveSheet()->setCellValue("P{$iRow}", $iTotalExamHalls);
                        $objPhpExcel->getActiveSheet()->setCellValue("Q{$iRow}", $iTotalLibrary);
                        $objPhpExcel->getActiveSheet()->setCellValue("R{$iRow}", $iTotalClerkOffice);
                        $objPhpExcel->getActiveSheet()->setCellValue("S{$iRow}", $iTotalPrincipalOffice);
                        $objPhpExcel->getActiveSheet()->setCellValue("T{$iRow}", $iTotalParkingStand);
                        $objPhpExcel->getActiveSheet()->setCellValue("U{$iRow}", $iTotalChowkidarHut);
                        $objPhpExcel->getActiveSheet()->setCellValue("V{$iRow}", $iTotalSoakagePit);
                        $objPhpExcel->getActiveSheet()->setCellValue("W{$iRow}", $iTotalWaterSupply);
                        $objPhpExcel->getActiveSheet()->setCellValue("X{$iRow}", $iTotalStores);

                        for ($j = 0; $j < 24; $j ++)
                                    $objPhpExcel->getActiveSheet()->duplicateStyleArray($sTotalStyle, (getExcelCol($j).$iRow.":".getExcelCol($j).$iRow));
                    }
                } // end foreach school wise
            } // end foreach work type            
        }// end foreach province wise
        
        
        $objPhpExcel->getActiveSheet()->getColumnDimension("A")->setWidth(50);
	$objPhpExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("C")->setWidth(40);
	$objPhpExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
	$objPhpExcel->getActiveSheet()->getColumnDimension("F")->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension("G")->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension("H")->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension("I")->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension("J")->setWidth(20);
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


	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&L&B Weekly Progress Report &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("Completed Schools Detail Report");

	////////////////////////// Download File ///////////////////////////////
		
	$objPhpExcel->setActiveSheetIndex(0);
		
	$sExcelFile = "Weekly Progress Report.xlsx";

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