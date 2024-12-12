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

    $sKeywords = IO::strValue("Keywords");
	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");
	$sStatus   = IO::strValue("Status");
	$sFromDate = IO::strValue("FromDate");  
	$sToDate   = IO::strValue("ToDate");  

	
	$sConditions = "";
	
	if ($sKeywords != "")
	{
		$iSurvey = intval($sKeywords);

		$sConditions .= " AND (bs.id='$iSurvey' OR
		                       bs.enumerator LIKE '%{$sKeywords}%' OR
							   s.name LIKE '%{$sKeywords}%' OR
							   s.code LIKE '%{$sKeywords}%' )";		
	}

	if ($iProvince > 0)
		$sConditions .= " AND s.province_id='$iProvince' ";
	
	if ($iDistrict > 0)
		$sConditions .= " AND s.district_id='$iDistrict' ";

    if ($sFromDate != "" && $sToDate != "")
		$sConditions .= " AND (bs.date BETWEEN '$sFromDate' AND '$sToDate') ";
		
    if ($sStatus != '')
		$sConditions .= " AND bs.status='$sStatus' ";
	
	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(bs.school_id, '{$_SESSION['AdminSchools']}') ";	
        
		
		
	$sFile = ($sRootDir.TEMP_DIR."surveys.csv");
	$hFile = @fopen($sFile, 'w');


	$sTypesList     = getList("tbl_school_types", "id", "`type`");
	$sProvincesList = getList("tbl_provinces", "id", "name");
	$sDistrictsList = getList("tbl_districts", "id", "name");
	$sQuestionsList = getList("tbl_survey_questions", "id", "question", "status='A' AND id NOT IN (73,89,90)", "section_id, position");
	
	
	$sQuestionLabels = array();
	
	foreach ($sQuestionsList as $iQuestion => $sQuestion)
	{
		$sQuestionLabels[] = str_replace(',', '', $sQuestion);
	}

	
	@fwrite($hFile, ('"EMIS Code","EMIS Code (Enumerator)","School Name","Type","Address","District","Province","Enumerator","Date","Latitude (Pre-Entered)","Longitude (Pre-Entered)","Latitude (Enumerator)","Longitude (Enumerator)","Latitude (Device)","Longitude (Device)","Is the school Operational?","Is the school part of the PEF (Punjab Education Foundation) Programme?","Does the school have enough land for new construction?","Is the school having any land dispute?","Is the school involved in any other project providing funding classroom infrastructure?","How many classrooms does your school have?","Out of total how many classrooms are being used for purpose?","Are there any shelter-less grades being taught?","Are there more than 2 grades being taught in one classroom?","What is the average attendance of school?",'.@implode(',', $sQuestionLabels)."\n"));
        

        
	$sSQL = "SELECT * FROM tbl_surveys bs, tbl_schools s WHERE bs.school_id=s.id AND FIND_IN_SET(bs.district_id, '{$_SESSION['AdminDistricts']}') $sConditions ORDER BY bs.date";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
	{
		$sName            = $objDb->getField($i, "s.name");
		$sCode            = $objDb->getField($i, "s.code");
		$iType            = $objDb->getField($i, "s.type_id");
		$iProvince        = $objDb->getField($i, "s.province_id");
		$iDistrict        = $objDb->getField($i, "s.district_id");
		$sAddress         = $objDb->getField($i, "s.address");
		$sLatitude        = $objDb->getField($i, "s.latitude");
		$sLongitude       = $objDb->getField($i, "s.longitude");
		
		$iSurvey          = $objDb->getField($i, "bs.id");
		$sLatitudeDevice  = $objDb->getField($i, "bs.latitude");
		$sLongitudeDevice = $objDb->getField($i, "bs.longitude");
		$sEnumerator      = $objDb->getField($i, "bs.enumerator");
		$sDate            = $objDb->getField($i, "bs.date");
		$sOperational     = $objDb->getField($i, "bs.operational");
		$sLandAvailable   = $objDb->getField($i, "bs.land_available");
		$sLandDispute     = $objDb->getField($i, "bs.land_dispute");
		$sOtherFunding    = $objDb->getField($i, "bs.other_funding");
		$sPefFunding      = $objDb->getField($i, "bs.pef_programme");
		$iClassRooms      = $objDb->getField($i, "bs.class_rooms");
		$iEducationRooms  = $objDb->getField($i, "bs.education_rooms");
		$sShelterLess     = $objDb->getField($i, "bs.shelter_less");
		$sMultiGrading    = $objDb->getField($i, "bs.multi_grading");
		$iAvgAttendance   = $objDb->getField($i, "bs.avg_attendance");
		$sQualified       = $objDb->getField($i, "bs.qualified");

		
		$sAnswers = getList("tbl_survey_answers", "question_id", "answer", "survey_id='$iSurvey'");
		$sRecord  = array();

		$sRecord[0]  = $sCode;
		$sRecord[1]  = @$sAnswers[73];
		$sRecord[2]  = $sName;
		$sRecord[3]  = $sTypesList[$iType];
		$sRecord[4]  = $sAddress;
		$sRecord[5]  = $sDistrictsList[$iDistrict];
		$sRecord[6]  = $sProvincesList[$iProvince];
		$sRecord[7]  = $sEnumerator;
		$sRecord[8]  = formatDate($sDate, $_SESSION['DateFormat']);
		$sRecord[9]  = $sLatitude;
		$sRecord[10] = $sLongitude;
		$sRecord[11] = @$sAnswers[89];
		$sRecord[12] = @$sAnswers[90];
		$sRecord[13] = $sLatitudeDevice;
		$sRecord[14] = $sLongitudeDevice;
		$sRecord[15] = $sOperational;
		$sRecord[16] = $sPefFunding;
		$sRecord[17] = $sLandAvailable;
		$sRecord[18] = $sLandDispute;
		$sRecord[19] = $sOtherFunding;
		$sRecord[20] = $iClassRooms;
		$sRecord[21] = $iEducationRooms;
		$sRecord[22] = $sShelterLess;
		$sRecord[23] = $sMultiGrading;
		$sRecord[24] = $iAvgAttendance;
                
				
		for ($j = 0; $j < count($sRecord); $j ++)
			$sRecord[$j] = str_replace(array("\n", "\r",","), '', $sRecord[$j]);

		
		foreach ($sQuestionsList as $iQuestion => $sQuestion)
		{
            $sRecord[$j] = str_replace(array("\n", "\r",","), '', @$sAnswers[$iQuestion]);
			
            $j ++;
        }
		
		
   		@fwrite($hFile, (@implode(',', $sRecord)."\n"));
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


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );      
?>