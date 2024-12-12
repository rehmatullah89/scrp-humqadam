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
        
	//if ($iContract > 0)
	//	$sConditions .= " AND cs.contract_id='$iContract' ";
        
        
        $objPhpExcel = new PHPExcel( );
        
        $objReader   = PHPExcel_IOFactory::createReader('Excel2007');
	$objPhpExcel = $objReader->load("{$sRootDir}templates/NSSchools.xlsx");
        
	$objPhpExcel->getProperties()->setCreator($_SESSION["SiteTitle"])
								 ->setLastModifiedBy($_SESSION["SiteTitle"])
								 ->setTitle("Non-Scheduled-Schools")
								 ->setSubject("Non Construction Scheduled Schools")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Reports");

	$objPhpExcel->setActiveSheetIndex(0);


	$iRow = 4;
        $sSQL = "SELECT s.id, s.name, s.code, s.province_id, s.district_id, s.type_id, s.students, s.storey_type, s.design_type, s.address 
            	 FROM tbl_schools s
		 Where s.id Not In (Select school_id from tbl_contract_schedules) $sConditions Order By s.id";
	
            $objDb->query($sSQL);
            $iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$sCode       = $objDb->getField($i, "code");
                        $sSchool     = $objDb->getField($i, "name");
                        $iDistrict   = $objDb->getField($i, "district_id");
                        $sDistrict   = getDbValue("name", "tbl_districts", "id='$iDistrict'");
                        $iProvince   = $objDb->getField($i, "province_id");
                        $sProvince   = getDbValue("name", "tbl_provinces", "id='$iProvince'");
                        $iType       = $objDb->getField($i, "type_id");
                        $sType       = getDbValue("type", "tbl_school_types", "id='$iType'");
                        $iStudents   = $objDb->getField($i, "students");
                        $sStoreyType = $objDb->getField($i, "storey_type");
                        $sDesignType = $objDb->getField($i, "design_type");
                        $sAddress    = $objDb->getField($i, "address");
                        
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $iRow, $sCode);
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $iRow, $sSchool);
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $iRow, $sType);
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $iRow, (($sStoreyType == "S") ? "Single" : "Double"));
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $iRow, (($sDesignType == "R") ? "Regular" : "Bespoke"));
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $iRow, $sDistrict);
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $iRow, $sProvince);
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $iRow, $iStudents);
                        $objPhpExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $iRow, $sAddress);
                        
                        $iRow ++;
                } // end main for loop
                

	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('');
	$objPhpExcel->getActiveSheet()->getHeaderFooter()->setOddFooter("&NS Schools List &R Generated on ".date("d-M-Y"));

	$objPhpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPhpExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

	$objPhpExcel->getActiveSheet()->getPageMargins()->setTop(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setLeft(0.4);
	$objPhpExcel->getActiveSheet()->getPageMargins()->setBottom(0);

	$objPhpExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);

	$objPhpExcel->getActiveSheet()->setTitle("NonScheduledSchools");


	$sExcelFile = "NonScheduledSchools.xlsx";

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=\"{$sExcelFile}\"");
	header("Cache-Control: max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');
	$objWriter->save("php://output");

	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
        
?>