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

	if (!@strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))
		die("ERROR: Invalid Request");


	$objDbGlobal = new Database( );
	$objDb       = new Database( );

    $sKeywords   = IO::strValue("Search");
	$iEnumerator = IO::intValue("Enumerator");
	$iDistrict   = IO::intValue("District");
	$sStatus     = IO::strValue("Status");
    $iProvince   = IO::intValue("Province");
	$sFromDate   = IO::strValue("FromDate");  
	$sToDate     = IO::strValue("ToDate");  	
	$sSection    = IO::strValue("Section");
        
		
	$sDistrictsList = getList("tbl_districts", "id", "name");
	$sConditions    = " WHERE ss.school_id=s.id AND FIND_IN_SET(ss.district_id, '{$_SESSION['AdminDistricts']}') ";
	
	if ($sKeywords != "")
	{
		$iSurvey = intval($sKeywords);
		
		$sConditions .= " AND (ss.id='$iSurvey' OR
							   s.name LIKE '%{$sKeywords}%' OR
							   s.code LIKE '%{$sKeywords}%' )";
	}

	if ($iEnumerator > 0)
		$sConditions .= " AND ss.admin_id='$iEnumerator' ";
	
    if ($iProvince > 0)
		$sConditions .= " AND s.province_id='$iProvince' ";
	
	else
		$sConditions .= " AND FIND_IN_SET(s.province_id, '{$_SESSION['AdminProvinces']}') ";	
        
	if ($iDistrict > 0)
		$sConditions .= " AND s.district_id='$iDistrict' ";
	
	else
		$sConditions .= " AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') ";	
	
    if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(ss.school_id, '{$_SESSION['AdminSchools']}') ";	
	
    if ($sFromDate != "" && $sToDate != "")
		$sConditions .= " AND (ss.date BETWEEN '$sFromDate' AND '$sToDate') ";
        
		
		
	$sFile = ($sRootDir.TEMP_DIR."Surveys.csv");
	$hFile = @fopen($sFile, 'w');


	if ($sSection == "" || $sSection == "Pending")
	{
		@fwrite($hFile, ('"Scheduled but not Received",'."\n\n"));
		@fwrite($hFile, ('"EMIS Code","School","District","Enumerator","Schedule Date"'."\n"));
			
			$sSQL = "SELECT ss.date,
							s.code, s.name, s.district_id,
						   (SELECT name FROM tbl_admins WHERE id=ss.admin_id) AS _Enumerator
				 FROM tbl_survey_schedules ss, tbl_schools s
				 $sConditions AND ss.status='P' 
				 ORDER BY ss.admin_id, ss.date";
			
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );
		
		for ($i = 0; $i < $iCount; $i ++)
		{
			$sEnumerator = $objDb->getField($i, "_Enumerator");
			$sDate       = $objDb->getField($i, "date");
			$sSchool     = $objDb->getField($i, "name");
			$sCode       = $objDb->getField($i, "code");
			$iDistrict   = $objDb->getField($i, "district_id");

			
			$sRecord  = array();

			$sRecord[0] = $sCode;
			$sRecord[1] = $sSchool;
			$sRecord[2] = $sDistrictsList[$iDistrict];
			$sRecord[3] = $sEnumerator;
			$sRecord[4] = formatDate($sDate, $_SESSION['DateFormat']);
			
			for ($j = 0; $j < count($sRecord); $j ++)
				$sRecord[$j] = str_replace(array("\n", "\r",","), '', $sRecord[$j]);
			
			
			@fwrite($hFile, (@implode(',', $sRecord)."\n"));
		}
	}
	
	

	if ($sSection == "")
	{
		@fwrite($hFile, ("\n\n".'" Not Scheduled but Incomplete",'."\n\n"));
		@fwrite($hFile, ('"EMIS Code","School","District","Enumerator","Survey Date",'."\n"));
		
		
		$sSQL = "SELECT ss.date, ss.enumerator AS _Enumerator,
						s.code, s.name, s.district_id
				 FROM tbl_surveys ss, tbl_schools s
				 $sConditions AND ss.status='I' AND ss.school_id NOT IN (SELECT school_id FROM tbl_survey_schedules)
				 ORDER BY ss.date";	
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );
		
		for ($i = 0; $i < $iCount; $i ++)
		{
			$sEnumerator = $objDb->getField($i, "_Enumerator");
			$sDate       = $objDb->getField($i, "date");
			$sSchool     = $objDb->getField($i, "name");
			$sCode       = $objDb->getField($i, "code");
			$iDistrict   = $objDb->getField($i, "district_id");

			
			$sRecord = array();

			$sRecord[0] = $sCode;
			$sRecord[1] = $sSchool;
			$sRecord[2] = $sDistrictsList[$iDistrict];
			$sRecord[3] = $sEnumerator;
			$sRecord[4] = formatDate($sDate, $_SESSION['DateFormat']);

			for ($j = 0; $j < count($sRecord); $j ++)
				$sRecord[$j] = str_replace(array("\n", "\r",","), '', $sRecord[$j]);

			
			@fwrite($hFile, (@implode(',', $sRecord)."\n"));
		}
	}
	
	
	
	if ($sSection == "NoDrawings")
	{
		@fwrite($hFile, ("\n\n".'"Surveys without Drawings",'."\n\n"));
		@fwrite($hFile, ('"EMIS Code","School","District","Enumerator","Survey Date",'."\n"));
		
		
		$sSQL = "SELECT ss.date, ss.enumerator AS _Enumerator,
						s.code, s.name, s.district_id
				 FROM tbl_surveys ss, tbl_schools s
				 $sConditions AND (ss.app='N' OR ss.status='C') AND ss.completed='N' AND ss.qualified='Y'
				 ORDER BY ss.date";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );
		
		for ($i = 0; $i < $iCount; $i ++)
		{
			$sEnumerator = $objDb->getField($i, "_Enumerator");
			$sDate       = $objDb->getField($i, "date");
			$sSchool     = $objDb->getField($i, "name");
			$sCode       = $objDb->getField($i, "code");
			$iDistrict   = $objDb->getField($i, "district_id");

			
			$sRecord = array();

			$sRecord[0] = $sCode;
			$sRecord[1] = $sSchool;
			$sRecord[2] = $sDistrictsList[$iDistrict];
			$sRecord[3] = $sEnumerator;
			$sRecord[4] = formatDate($sDate, $_SESSION['DateFormat']);

			for ($j = 0; $j < count($sRecord); $j ++)
				$sRecord[$j] = str_replace(array("\n", "\r",","), '', $sRecord[$j]);

			
			@fwrite($hFile, (@implode(',', $sRecord)."\n"));
		}
	}
	
	
	
	@fclose($hFile);

	
	// forcing csv file to download
	$fFileSize = @filesize($sFile);

	if(ini_get('zlib.output_compression'))
		@ini_set('zlib.output_compression', 'Off');

	header('Content-Description: File Transfer');
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header('Content-Type: application/force-download');
	header("Content-Type: application/download");
	header("Content-Type: text/csv");
	header("Content-Disposition: attachment; filename=\"".@basename($sFile)."\";");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: $fFileSize");

	@readfile($sFile);
	@unlink($sFile);
	

	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );      
?>