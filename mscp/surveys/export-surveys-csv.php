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

    $sKeywords     = IO::strValue("Keywords");
	$iProvince     = IO::intValue("Province");
	$iDistrict     = IO::intValue("District");
	$sSurveyStatus = IO::strValue("SurveyStatus");
	$sSyncStatus   = IO::strValue("SyncStatus");
	$sQualified    = IO::strValue("Qualified");
	$sFromDate     = IO::strValue("FromDate");  
	$sToDate       = IO::strValue("ToDate");  

	
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
	
	else
		$sConditions .= " AND s.province_id IN ({$_SESSION['AdminProvinces']}) ";	
	
	if ($iDistrict > 0)
		$sConditions .= " AND s.district_id='$iDistrict' ";
	
	else
		$sConditions .= " AND s.district_id IN ({$_SESSION['AdminDistricts']}) ";	

    if ($sFromDate != "" && $sToDate != "")
		$sConditions .= " AND (bs.date BETWEEN '$sFromDate' AND '$sToDate') ";
		
	if ($sSurveyStatus != "")
		$sConditions .= " AND bs.completed='$sSurveyStatus' ";

	if ($sQualified != "")
		$sConditions .= " AND bs.qualified='$sQualified' ";

	if ($sSyncStatus != "")
	{
		if ($sSyncStatus == 'C')
			$sConditions .= " AND bs.status='C' ";
	
		else
			$sConditions .= " AND bs.app='Y' AND bs.status='I' ";
	}           
	
	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND bs.school_id IN ({$_SESSION['AdminSchools']}) ";	
        
		
		
	$sFile = ($sRootDir.TEMP_DIR."surveys.csv");
	$hFile = @fopen($sFile, 'w');


	$sTypesList     = getList("tbl_school_types", "id", "`type`");
	$sProvincesList = getList("tbl_provinces", "id", "name");
	$sDistrictsList = getList("tbl_districts", "id", "name");
	
	
	$sSQL = "SELECT id, `type`, question, options, other FROM tbl_survey_questions WHERE status='A' AND id NOT IN (73,89,90) ORDER BY section_id, position";
	$objDb->query($sSQL);
	
	$iCount     = $objDb->getCount( );
	$sQuestions = array( );
	
	for ($i = 0; $i < $iCount; $i ++)
	{
		$iQuestion = $objDb->getField($i, "id");
		$sType     = $objDb->getField($i, "type");
		$sQuestion = $objDb->getField($i, "question");
		$sOptions  = $objDb->getField($i, "options");
		$sOther    = $objDb->getField($i, "other");
		
		$sQuestions[$iQuestion] = array($sQuestion, $sType, @explode("\n", $sOptions), $sOther);
	}
	

	
	$sQuestionLabels = array();
	
	foreach ($sQuestions as $iQuestion => $sQuestion)
	{
		$sQuestionLabels[] = str_replace(',', ' ', $sQuestion[0]);
		
		if ($sQuestion[1] == "MS")
		{
			for ($i = 1; $i < count($sQuestion[2]); $i ++)
				$sQuestionLabels[] = "";
		}
		
		if ($sQuestion[3] == "Y")
			$sQuestionLabels[] = "";
	}
	
	
	
	$sQuestionLabels[] = "Teachers - Sanctioned - Male";
	$sQuestionLabels[] = "Teachers - Sanctioned - Female";
	$sQuestionLabels[] = "Teachers - Sanctioned - Both";
	$sQuestionLabels[] = "Teachers - Filled - Male";
	$sQuestionLabels[] = "Teachers - Filled - Female";
	$sQuestionLabels[] = "Teachers - Regularly Attending - Male";
	$sQuestionLabels[] = "Teachers - Regularly Attending - Female";
	$sQuestionLabels[] = "Teachers - Regularly Attending - Total";
	$sQuestionLabels[] = "Support Staff - Sanctioned - Male";
	$sQuestionLabels[] = "Support Staff - Sanctioned - Female";
	$sQuestionLabels[] = "Support Staff - Sanctioned - Both";
	$sQuestionLabels[] = "Support Staff - Filled - Male";
	$sQuestionLabels[] = "Support Staff - Filled - Female";
	$sQuestionLabels[] = "Support Staff - Regularly Attending - Male";
	$sQuestionLabels[] = "Support Staff - Regularly Attending - Female";
	$sQuestionLabels[] = "Support Staff - Regularly Attending - Total";
	$sQuestionLabels[] = "Management Staff (Head Teacher/ Principal) - Sanctioned - Male";
	$sQuestionLabels[] = "Management Staff (Head Teacher/ Principal) - Sanctioned - Female";
	$sQuestionLabels[] = "Management Staff (Head Teacher/ Principal) - Sanctioned - Both";
	$sQuestionLabels[] = "Management Staff (Head Teacher/ Principal) - Filled - Male";
	$sQuestionLabels[] = "Management Staff (Head Teacher/ Principal) - Filled - Filled - Female";
	$sQuestionLabels[] = "Management Staff (Head Teacher/ Principal) - Regularly Attending - Male";
	$sQuestionLabels[] = "Management Staff (Head Teacher/ Principal) - Regularly Attending - Female";
	$sQuestionLabels[] = "Management Staff (Head Teacher/ Principal) - Regularly Attending - Total";
	
	
	$sQuestionLabels[] = "Enrolled - Primary - Male";
	$sQuestionLabels[] = "Enrolled - Primary - Female";
	$sQuestionLabels[] = "Enrolled - Primary - Both";
	$sQuestionLabels[] = "Enrolled - Middle (grades 1-8) - Male";
	$sQuestionLabels[] = "Enrolled - Middle (grades 1-8) - Female";
	$sQuestionLabels[] = "Enrolled - Middle (grades 1-8) - Both";
	$sQuestionLabels[] = "Enrolled - Middle (grade 6-8) - Male";
	$sQuestionLabels[] = "Enrolled - Middle (grade 6-8) - Female";
	$sQuestionLabels[] = "Enrolled - Middle (grade 6-8) - Both";
	$sQuestionLabels[] = "Enrolled - High - Male";
	$sQuestionLabels[] = "Enrolled - High - Female";
	$sQuestionLabels[] = "Enrolled - High - Both";
	$sQuestionLabels[] = "Enrolled - HSS - Male";
	$sQuestionLabels[] = "Enrolled - HSS - Female";
	$sQuestionLabels[] = "Enrolled - HSS - Both";

	
	$sQuestionLabels[] = "MORNING SHIFT - TIME FROM";
	$sQuestionLabels[] = "MORNING SHIFT - TIME TO";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 0 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 1 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 2 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 3 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 4 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 5 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 6 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 7 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 8 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 9 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 10 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 11 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 12 BOYS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE TOTAL BOYS";

	$sQuestionLabels[] = "MORNING SHIFT - GRADE 0 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 1 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 2 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 3 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 4 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 5 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 6 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 7 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 8 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 9 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 10 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 11 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE 12 GIRLS";
	$sQuestionLabels[] = "MORNING SHIFT - GRADE TOTAL GIRLS";

	$sQuestionLabels[] = "EVENING SHIFT - TIME FROM";
	$sQuestionLabels[] = "EVENING SHIFT - TIME TO";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 0 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 1 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 2 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 3 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 4 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 5 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 6 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 7 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 8 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 9 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 10 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 11 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 12 BOYS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE TOTAL BOYS";

	$sQuestionLabels[] = "EVENING SHIFT - GRADE 0 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 1 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 2 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 3 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 4 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 5 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 6 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 7 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 8 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 9 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 10 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 11 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE 12 GIRLS";
	$sQuestionLabels[] = "EVENING SHIFT - GRADE TOTAL GIRLS";

	$sQuestionLabels[] = "Number of differently abled students regularly attending - Boys";
	$sQuestionLabels[] = "Number of differently abled students regularly attending - Boys - Grades (list)";
	$sQuestionLabels[] = "Number of differently abled students regularly attending - Girls";
	$sQuestionLabels[] = "Number of differently abled students regularly attending - Girls - Grades (list)";

	for ($i = 1; $i <= 10; $i ++)
	{
		$sQuestionLabels[] = "Block {$i} - Age";
		$sQuestionLabels[] = "Block {$i} - Storeys";
		$sQuestionLabels[] = "Block {$i} - CM";
		$sQuestionLabels[] = "Block {$i} - Classrooms used for educational purposes - T";
		$sQuestionLabels[] = "Block {$i} - Classrooms used for educational purposes - G";
		$sQuestionLabels[] = "Block {$i} - Classrooms used for educational purposes - R";
		$sQuestionLabels[] = "Block {$i} - Classrooms used for educational purposes - D";
		$sQuestionLabels[] = "Block {$i} - Classrooms used for any other purposes - T";
		$sQuestionLabels[] = "Block {$i} - Classrooms used for any other purposes - G";
		$sQuestionLabels[] = "Block {$i} - Classrooms used for any other purposes - R";
		$sQuestionLabels[] = "Block {$i} - Classrooms used for any other purposes - D";
		$sQuestionLabels[] = "Block {$i} - Safe Wheelchair Access Ramp - T";
		$sQuestionLabels[] = "Block {$i} - Safe Wheelchair Access Ramp - G";
		$sQuestionLabels[] = "Block {$i} - Safe Wheelchair Access Ramp - R";
		$sQuestionLabels[] = "Block {$i} - Safe Wheelchair Access Ramp - D";
		$sQuestionLabels[] = "Block {$i} - Toilets – Female Students - T";
		$sQuestionLabels[] = "Block {$i} - Toilets – Female Students - G";
		$sQuestionLabels[] = "Block {$i} - Toilets – Female Students - R";
		$sQuestionLabels[] = "Block {$i} - Toilets – Female Students - D";
		$sQuestionLabels[] = "Block {$i} - Toilets – Male Students - T";
		$sQuestionLabels[] = "Block {$i} - Toilets – Male Students - G";
		$sQuestionLabels[] = "Block {$i} - Toilets – Male Students - R";
		$sQuestionLabels[] = "Block {$i} - Toilets – Male Students - D";
		$sQuestionLabels[] = "Block {$i} - Toilets – Female Teachers - T";
		$sQuestionLabels[] = "Block {$i} - Toilets – Female Teachers - G";
		$sQuestionLabels[] = "Block {$i} - Toilets – Female Teachers - R";
		$sQuestionLabels[] = "Block {$i} - Toilets – Female Teachers - D";
		$sQuestionLabels[] = "Block {$i} - Toilets – Male Teachers - T";
		$sQuestionLabels[] = "Block {$i} - Toilets – Male Teachers - G";
		$sQuestionLabels[] = "Block {$i} - Toilets – Male Teachers - R";
		$sQuestionLabels[] = "Block {$i} - Toilets – Male Teachers - D";
		$sQuestionLabels[] = "Block {$i} - Unisex toilets for teachers - T";
		$sQuestionLabels[] = "Block {$i} - Unisex toilets for teachers - G";
		$sQuestionLabels[] = "Block {$i} - Unisex toilets for teachers - R";
		$sQuestionLabels[] = "Block {$i} - Unisex toilets for teachers - D";
		$sQuestionLabels[] = "Block {$i} - Unisex toilets for students - T";
		$sQuestionLabels[] = "Block {$i} - Unisex toilets for students - G";
		$sQuestionLabels[] = "Block {$i} - Unisex toilets for students - R";
		$sQuestionLabels[] = "Block {$i} - Unisex toilets for students - D";
		$sQuestionLabels[] = "Block {$i} - Toilets – Both Teacher/Student - T";
		$sQuestionLabels[] = "Block {$i} - Toilets – Both Teacher/Student - G";
		$sQuestionLabels[] = "Block {$i} - Toilets – Both Teacher/Student - R";
		$sQuestionLabels[] = "Block {$i} - Toilets – Both Teacher/Student - D";
	}

	$sQuestionLabels[] = "Other Block # 1";
	$sQuestionLabels[] = "Other Details # 1";
	$sQuestionLabels[] = "Other Block # 2";
	$sQuestionLabels[] = "Other Details # 2";
	$sQuestionLabels[] = "Other Block # 3";
	$sQuestionLabels[] = "Other Details # 3";
	$sQuestionLabels[] = "Facility and Room Count and Condition Comments";

	
	$sQuestionLabels[] = "Playgrounds for Girls (sq. ft) T";
	$sQuestionLabels[] = "Playgrounds for Girls (sq. ft) G";
	$sQuestionLabels[] = "Playgrounds for Girls (sq. ft) R";
	$sQuestionLabels[] = "Playgrounds for Girls (sq. ft) D";
	
	$sQuestionLabels[] = "Playgrounds for Boys (sq. ft) T";
	$sQuestionLabels[] = "Playgrounds for Boys (sq. ft) G";
	$sQuestionLabels[] = "Playgrounds for Boys (sq. ft) R";
	$sQuestionLabels[] = "Playgrounds for Boys (sq. ft) D";
	
	$sQuestionLabels[] = "Playgrounds for Both (sq. ft) T";
	$sQuestionLabels[] = "Playgrounds for Both (sq. ft) G";
	$sQuestionLabels[] = "Playgrounds for Both (sq. ft) R";
	$sQuestionLabels[] = "Playgrounds for Both (sq. ft) D";
	
	$sQuestionLabels[] = "Boundary Wall (length in ft) T";
	$sQuestionLabels[] = "Boundary Wall (length in ft) G";
	$sQuestionLabels[] = "Boundary Wall (length in ft) R";
	$sQuestionLabels[] = "Boundary Wall (length in ft) D";
	$sQuestionLabels[] = "Boundary Wall (length in ft) Material";
	$sQuestionLabels[] = "Boundary Wall (length in ft) Height (ft)";
	
	$sQuestionLabels[] = "Main Gate (length in ft) T";
	$sQuestionLabels[] = "Main Gate (length in ft) G";
	$sQuestionLabels[] = "Main Gate (length in ft) R";
	$sQuestionLabels[] = "Main Gate (length in ft) D";
	$sQuestionLabels[] = "Main Gate (length in ft) Material";
	$sQuestionLabels[] = "Main Gate (length in ft) Height (ft)";
	
	$sQuestionLabels[] = "Retaining Wall (length in ft) T";
	$sQuestionLabels[] = "Retaining Wall (length in ft) G";
	$sQuestionLabels[] = "Retaining Wall (length in ft) R";
	$sQuestionLabels[] = "Retaining Wall (length in ft) D";
	$sQuestionLabels[] = "Retaining Wall (length in ft) Material";
	$sQuestionLabels[] = "Retaining Wall (length in ft) Height (ft)";
	
	
	$iQuestionsCount = count($sQuestionLabels);
	
	@fwrite($hFile, ('"EMIS Code","EMIS Code (Enumerator)","School Name","Type","Address","District","Province","Enumerator","Date","Latitude (Pre-Entered)","Longitude (Pre-Entered)","Latitude (Enumerator)","Longitude (Enumerator)","Latitude (Device)","Longitude (Device)","Is the school Operational?","Non-Operational Reason","Is the school part of the PEF (Punjab Education Foundation) Programme?","Does the school have enough land for new construction?","Is the school having any land dispute?","Land Dispute","Is the school involved in any other project providing funding classroom infrastructure?","How many classrooms does your school have?","Out of total how many classrooms are being used for purpose?","Are there any shelter-less grades being taught?","Are there more than 2 grades being taught in one classroom?","What is the average attendance of school?","Does the School Qualify Pre-Selection?","Comments",'.@implode(',', $sQuestionLabels)."\n"));
	
	
	
	$sQuestionLabels = array();
	
	foreach ($sQuestions as $iQuestion => $sQuestion)
	{
		if ($sQuestion[1] == "MS")
		{
			for ($i = 0; $i < count($sQuestion[2]); $i ++)
				$sQuestionLabels[] = trim(str_replace(',', ' ', $sQuestion[2][$i]));
		}
		
		else
			$sQuestionLabels[] = "";

		if ($sQuestion[3] == "Y")
			$sQuestionLabels[] = "Other";		
	}
	
	for ($i = count($sQuestionLabels); $i < $iQuestionsCount; $i ++)
		$sQuestionLabels[] = "";

	@fwrite($hFile, ('"","","","","","","","","","","","","","","","","","","","","","","","","","","","","",'.@implode(',', $sQuestionLabels)."\n"));


	
	
	$sSQL = "SELECT * FROM tbl_surveys bs, tbl_schools s WHERE bs.school_id=s.id $sConditions ORDER BY bs.date";
	$objDb->query($sSQL);

	$iCount   = $objDb->getCount( );
	$sSurveys = "0";
	
	for ($i = 0; $i < $iCount; $i ++)
		$sSurveys .= (",".$objDb->getField($i, "bs.id"));
	
	
	$sTeacherNumbers   = getList("tbl_survey_teacher_numbers", "CONCAT(survey_id, '-', staff_type, '-', attendance_type)", "CONCAT(male_count, ',', female_count, ',', both_count)", "survey_id IN ($sSurveys)");
	$sStudentEnrolment = getList("tbl_survey_students_enrollment", "CONCAT(survey_id, '-', school_type)", "CONCAT(male_count, ',', female_count, ',', both_count)", "survey_id IN ($sSurveys)");
	$sStudentNumbers   = getList("tbl_survey_student_attendance_numbers", "CONCAT(survey_id, '-', class_grade)", "CONCAT(boys_count_morning, ',', boys_count_evening, ',', girls_count_morning, ',', girls_count_evening)", "survey_id IN ($sSurveys)");
	$sStudentShifts    = getList("tbl_survey_differently_abled_student_numbers", "survey_id", "CONCAT(boys_count, ',', girls_count, ',', boys_grades, ',', girls_grades, ',', morning_time_in, ',', morning_time_out, ',', evening_time_in, ',', evening_time_out)", "survey_id IN ($sSurveys)");
	$sSchoolBlocks     = getList("tbl_survey_school_blocks", "CONCAT(survey_id, '-', block)", "CONCAT(age, '|-|', storeys, '|-|', cm)", "survey_id IN ($sSurveys)");
	$sOtherRooms       = getList("tbl_survey_school_other_blocks", "survey_id", "CONCAT(other_block_1, '|-|', other_details_1, '|-|', other_block_2, '|-|', other_details_2, '|-|', other_block_3, '|-|', other_details_3, '|-|', comments)", "survey_id IN ($sSurveys)");
	$sBlockDetails     = getList("tbl_survey_school_block_details", "CONCAT(survey_id, '-', block, '-', room_type_code)", "CONCAT(total, ',', good, ',', rehabilitation, ',', dilapidated)", "survey_id IN ($sSurveys)");
	$sSchoolFacilities = getList("tbl_survey_school_facilities", "CONCAT(survey_id, '-', type)", "CONCAT(total, '|-|', good, '|-|', rehabilitation, '|-|', dilapidated, '|-|', material, '|-|', height)", "survey_id IN ($sSurveys)");
	
	
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
		$sPreSelection    = $objDb->getField($i, "bs.pre_selection");
		$sComments        = $objDb->getField($i, "bs.comments");
		$sQualified       = $objDb->getField($i, "bs.qualified");		

		
		
		$sAnswers = getList("tbl_survey_answers", "question_id", "answer", "survey_id='$iSurvey'");
		$sOthers  = getList("tbl_survey_answers", "question_id", "other", "survey_id='$iSurvey'");
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
		$sRecord[15] = (($sOperational != "Y") ? "No" : "Yes");
		$sRecord[16] = (($sOperational != "Y") ? $sOperational : "");
		$sRecord[17] = (($sPefFunding == "Y") ? "Yes" : (($sPefFunding == "N") ? "No" : ""));
		$sRecord[18] = (($sLandAvailable == "Y") ? "Yes" : (($sLandAvailable == "N") ? "No" : ""));
		$sRecord[19] = (($sLandDispute == "N") ? "No" : (($sLandDispute != "") ? "Yes" : ""));
		$sRecord[20] = (($sLandDispute != "" && $sLandDispute != "N") ? $sLandDispute : "");
		$sRecord[21] = (($sOtherFunding == "Y") ? "Yes" : (($sOtherFunding == "N") ? "No" : ""));
		$sRecord[22] = $iClassRooms;
		$sRecord[23] = $iEducationRooms;
		$sRecord[24] = (($sShelterLess == "Y") ? "Yes" : (($sShelterLess == "N") ? "No" : ""));
		$sRecord[25] = (($sMultiGrading == "Y") ? "Yes" : (($sMultiGrading == "N") ? "No" : ""));
		$sRecord[26] = $iAvgAttendance;
		$sRecord[27] = (($sPreSelection == "Y") ? "Yes" : "No");
		$sRecord[28] = $sComments;
                
				
		for ($j = 0; $j < count($sRecord); $j ++)
			$sRecord[$j] = str_replace(array("\n", "\r", ","), "", $sRecord[$j]);

			
		foreach ($sQuestions as $iQuestion => $sQuestion)
		{
			if ($sQuestion[1] == "MS")
			{
				$sOptions  = $sQuestion[2];
				$sSelected = @explode("\n", $sAnswers[$iQuestion]);
				$sSelected = @array_map("trim", $sSelected);
			
				for ($k = 0; $k < count($sOptions); $k ++)
					$sRecord[$j ++] = ((@in_array(trim($sOptions[$k]), $sSelected)) ? str_replace(array("\n", "\r", ","), "", $sOptions[$k]) : "");
			}
			
			else
				$sRecord[$j ++] = str_replace(array("\n", "\r", ","), "", @$sAnswers[$iQuestion]);
			
			
			if ($sQuestion[3] == "Y")
				$sRecord[$j ++] = str_replace(array("\n", "\r", ","), "", @$sOthers[$iQuestion]);
		}
		
			
	
		// Teacher Numbers
		@list($iSanctionedMale, $iSanctionedFemale, $iSanctionedBoth) = @explode(",", $sTeacherNumbers["{$iSurvey}-T-S"]);
		@list($iFilledMale, $iFilledFemale, $iFilledBoth)             = @explode(",", $sTeacherNumbers["{$iSurvey}-T-F"]);
		@list($iRegularMale, $iRegularFemale, $iRegularBoth)          = @explode(",", $sTeacherNumbers["{$iSurvey}-T-R"]);
		
		$sRecord[$j ++] = (($iSanctionedMale > 0) ? $iSanctionedMale : "");
		$sRecord[$j ++] = (($iSanctionedFemale > 0) ? $iSanctionedFemale : "");
		$sRecord[$j ++] = (($iSanctionedBoth > 0) ? $iSanctionedBoth : "");
		$sRecord[$j ++] = (($iFilledMale > 0) ? $iFilledMale : "");
		$sRecord[$j ++] = (($iFilledFemale > 0) ? $iFilledFemale : "");
		$sRecord[$j ++] = (($iRegularMale > 0) ? $iRegularMale : "");
		$sRecord[$j ++] = (($iRegularFemale > 0) ? $iRegularFemale : "");
		$sRecord[$j ++] = (($iRegularBoth > 0) ? $iRegularBoth : "");
		
		
		@list($iSanctionedMale, $iSanctionedFemale, $iSanctionedBoth) = @explode(",", $sTeacherNumbers["{$iSurvey}-SS-S"]);
		@list($iFilledMale, $iFilledFemale, $iFilledBoth)             = @explode(",", $sTeacherNumbers["{$iSurvey}-SS-F"]);
		@list($iRegularMale, $iRegularFemale, $iRegularBoth)          = @explode(",", $sTeacherNumbers["{$iSurvey}-SS-R"]);
		
		$sRecord[$j ++] = (($iSanctionedMale > 0) ? $iSanctionedMale : "");
		$sRecord[$j ++] = (($iSanctionedFemale > 0) ? $iSanctionedFemale : "");
		$sRecord[$j ++] = (($iSanctionedBoth > 0) ? $iSanctionedBoth : "");
		$sRecord[$j ++] = (($iFilledMale > 0) ? $iFilledMale : "");
		$sRecord[$j ++] = (($iFilledFemale > 0) ? $iFilledFemale : "");
		$sRecord[$j ++] = (($iRegularMale > 0) ? $iRegularMale : "");
		$sRecord[$j ++] = (($iRegularFemale > 0) ? $iRegularFemale : "");
		$sRecord[$j ++] = (($iRegularBoth > 0) ? $iRegularBoth : "");
		
		
		@list($iSanctionedMale, $iSanctionedFemale, $iSanctionedBoth) = @explode(",", $sTeacherNumbers["{$iSurvey}-MS-S"]);
		@list($iFilledMale, $iFilledFemale, $iFilledBoth)             = @explode(",", $sTeacherNumbers["{$iSurvey}-MS-F"]);
		@list($iRegularMale, $iRegularFemale, $iRegularBoth)          = @explode(",", $sTeacherNumbers["{$iSurvey}-MS-R"]);
		
		$sRecord[$j ++] = (($iSanctionedMale > 0) ? $iSanctionedMale : "");
		$sRecord[$j ++] = (($iSanctionedFemale > 0) ? $iSanctionedFemale : "");
		$sRecord[$j ++] = (($iSanctionedBoth > 0) ? $iSanctionedBoth : "");
		$sRecord[$j ++] = (($iFilledMale > 0) ? $iFilledMale : "");
		$sRecord[$j ++] = (($iFilledFemale > 0) ? $iFilledFemale : "");
		$sRecord[$j ++] = (($iRegularMale > 0) ? $iRegularMale : "");
		$sRecord[$j ++] = (($iRegularFemale > 0) ? $iRegularFemale : "");
		$sRecord[$j ++] = (($iRegularBoth > 0) ? $iRegularBoth : "");
		
		
		
		// Student Enrolment
		@list($iMale, $iFemale, $iBoth) = @explode(",", $sStudentEnrolment["{$iSurvey}-P"]);
		
		$sRecord[$j ++] = (($iMale > 0) ? $iMale : "");
		$sRecord[$j ++] = (($iFemale > 0) ? $iFemale : "");
		$sRecord[$j ++] = (($iBoth > 0) ? $iBoth : "");
		
		
		@list($iMale, $iFemale, $iBoth) = @explode(",", $sStudentEnrolment["{$iSurvey}-M18"]);
		
		$sRecord[$j ++] = (($iMale > 0) ? $iMale : "");
		$sRecord[$j ++] = (($iFemale > 0) ? $iFemale : "");
		$sRecord[$j ++] = (($iBoth > 0) ? $iBoth : "");


		@list($iMale, $iFemale, $iBoth) = @explode(",", $sStudentEnrolment["{$iSurvey}-M68"]);
		
		$sRecord[$j ++] = (($iMale > 0) ? $iMale : "");
		$sRecord[$j ++] = (($iFemale > 0) ? $iFemale : "");
		$sRecord[$j ++] = (($iBoth > 0) ? $iBoth : "");
		
		
		@list($iMale, $iFemale, $iBoth) = @explode(",", $sStudentEnrolment["{$iSurvey}-H"]);
		
		$sRecord[$j ++] = (($iMale > 0) ? $iMale : "");
		$sRecord[$j ++] = (($iFemale > 0) ? $iFemale : "");
		$sRecord[$j ++] = (($iBoth > 0) ? $iBoth : "");
		
		
		@list($iMale, $iFemale, $iBoth) = @explode(",", $sStudentEnrolment["{$iSurvey}-HS"]);
		
		$sRecord[$j ++] = (($iMale > 0) ? $iMale : "");
		$sRecord[$j ++] = (($iFemale > 0) ? $iFemale : "");
		$sRecord[$j ++] = (($iBoth > 0) ? $iBoth : "");
		
		
		// Student Shift Numbers
		@list($iBoysCount, $iGirlsCount, $sBoyGrades, $sGirlGrades, $sMorningTimeIn, $sMorningTimeOut, $sEveningTimeIn, $sEveningTimeOut) = @explode(",", $sStudentShifts[$iSurvey]);

		$iBoysMorning  = array( );
		$iBoysEvening  = array( );
		$iGirlsMorning = array( );
		$iGirlsEvening = array( );
		
		for ($k = 0; $k <= 12; $k ++)
		{
			@list($iMorningBoys, $iEveningBoys, $iMorningGirls, $iEveningGirls) = @explode(",", $sStudentNumbers["{$iSurvey}-{$k}"]);
			
			$iBoysMorning[]  = $iMorningBoys;
			$iBoysEvening[]  = $iEveningBoys;
			$iGirlsMorning[] = $iMorningGirls;
			$iGirlsEvening[] = $iEveningGirls;
		}

		
		$sRecord[$j ++] = formatTime($sMorningTimeIn);
		$sRecord[$j ++] = formatTime($sMorningTimeOut);
		
		for ($k = 0; $k <= 12; $k ++)
			$sRecord[$j ++] = $iBoysMorning[$k];
	
		$sRecord[$j ++] = @array_sum($iBoysMorning);
		
		
		for ($k = 0; $k <= 12; $k ++)
			$sRecord[$j ++] = $iGirlsMorning[$k];
		
		$sRecord[$j ++] = @array_sum($iGirlsMorning);

		
		$sRecord[$j ++] = formatTime($sEveningTimeIn);
		$sRecord[$j ++] = formatTime($sEveningTimeOut);
		

		for ($k = 0; $k <= 12; $k ++)
			$sRecord[$j ++] = $iBoysEvening[$k];
		
		$sRecord[$j ++] = @array_sum($iBoysEvening);
		

		for ($k = 0; $k <= 12; $k ++)
			$sRecord[$j ++] = $iGirlsEvening[$k];
		
		$sRecord[$j ++] = @array_sum($iGirlsEvening);

		$sRecord[$j ++] = (($iBoysCount > 0) ? $iBoysCount : "");
		$sRecord[$j ++] = $sBoyGrades;
		$sRecord[$j ++] = (($iGirlsCount > 0) ? $iGirlsCount : "");
		$sRecord[$j ++] = $sGirlGrades;
	
	
		// Room Count & Conditions
		for ($k = 1; $k <= 10; $k ++)
		{
			@list($iAge, $iStoreys, $sCm) = @explode("|-|", $sSchoolBlocks["{$iSurvey}-b{$k}"]);

			
			$sMaterials = "";
			
			if (@strpos($sCm, "R") !== FALSE)
				$sMaterials .= "Reinforced Brick Masonry or Confined Masonry";

			if (@strpos($sCm, "U") !== FALSE)
			{
				if ($sMaterials != "")
					$sMaterials .= " + ";
				
				$sMaterials .= "Unreinforced Brick Masonry or unconfined Masonry";
			}

			if (@strpos($sCm, "S") !== FALSE)
			{
				if ($sMaterials != "")
					$sMaterials .= " + ";
				
				$sMaterials .= "Stone Masonry";
			}

			if (@strpos($sCm, "F") !== FALSE)
			{
				if ($sMaterials != "")
					$sMaterials .= " + ";
				
				$sMaterials .= "Frame Structure";
			}
			
			
			$sRecord[$j ++] = intval($iAge);
			$sRecord[$j ++] = intval($iStoreys);
			$sRecord[$j ++] = $sMaterials;
			
			
			@list($iTotal, $iGood, $iRehabilitation, $iDilapidated) = @explode(",", $sBlockDetails["{$iSurvey}-b{$k}-CRE"]);
			
			$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
			$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
			$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
			$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
			

			@list($iTotal, $iGood, $iRehabilitation, $iDilapidated) = @explode(",", $sBlockDetails["{$iSurvey}-b{$k}-CRO"]);
			
			$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
			$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
			$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
			$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
			
			
			@list($iTotal, $iGood, $iRehabilitation, $iDilapidated) = @explode(",", $sBlockDetails["{$iSurvey}-b{$k}-SWA"]);
			
			$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
			$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
			$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
			$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
			

			@list($iTotal, $iGood, $iRehabilitation, $iDilapidated) = @explode(",", $sBlockDetails["{$iSurvey}-b{$k}-TFS"]);
			
			$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
			$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
			$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
			$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
			

			@list($iTotal, $iGood, $iRehabilitation, $iDilapidated) = @explode(",", $sBlockDetails["{$iSurvey}-b{$k}-TMS"]);
			
			$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
			$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
			$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
			$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
			

			@list($iTotal, $iGood, $iRehabilitation, $iDilapidated) = @explode(",", $sBlockDetails["{$iSurvey}-b{$k}-TFT"]);
			
			$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
			$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
			$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
			$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
			

			@list($iTotal, $iGood, $iRehabilitation, $iDilapidated) = @explode(",", $sBlockDetails["{$iSurvey}-b{$k}-TMT"]);
			
			$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
			$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
			$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
			$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
			
			
			@list($iTotal, $iGood, $iRehabilitation, $iDilapidated) = @explode(",", $sBlockDetails["{$iSurvey}-b{$k}-UT"]);
			
			$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
			$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
			$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
			$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
			

			@list($iTotal, $iGood, $iRehabilitation, $iDilapidated) = @explode(",", $sBlockDetails["{$iSurvey}-b{$k}-US"]);
			
			$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
			$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
			$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
			$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
			

			@list($iTotal, $iGood, $iRehabilitation, $iDilapidated) = @explode(",", $sBlockDetails["{$iSurvey}-b{$k}-TB"]);
			
			$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
			$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
			$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
			$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
		}


		@list($sOtherBlock1, $sOtherDetails1, $sOtherBlock2, $sOtherDetails2, $sOtherBlock3, $sOtherDetails3, $sComments) = @explode("|-|", $sOtherRooms[$iSurvey]);
		
		
		$sRecord[$j ++] = str_replace(array("\n", "\r", ","), "", $sOtherBlock1);
		$sRecord[$j ++] = str_replace(array("\n", "\r", ","), "", $sOtherDetails1);
		$sRecord[$j ++] = str_replace(array("\n", "\r", ","), "", $sOtherBlock2);
		$sRecord[$j ++] = str_replace(array("\n", "\r", ","), "", $sOtherDetails2);
		$sRecord[$j ++] = str_replace(array("\n", "\r", ","), "", $sOtherBlock3);
		$sRecord[$j ++] = str_replace(array("\n", "\r", ","), "", $sOtherDetails3);
		$sRecord[$j ++] = str_replace(array("\n", "\r", ","), "", $sComments);
		

		// School Facilities
		@list($iTotal, $iGood, $iRehabilitation, $iDilapidated, $sMaterial, $sHeight) = @explode("|-|", $sSchoolFacilities["{$iSurvey}-PG"]);
		
		$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
		$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
		$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
		$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
		
		
		@list($iTotal, $iGood, $iRehabilitation, $iDilapidated, $sMaterial, $sHeight) = @explode("|-|", $sSchoolFacilities["{$iSurvey}-PB"]);
		
		$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
		$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
		$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
		$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
		
		
		@list($iTotal, $iGood, $iRehabilitation, $iDilapidated, $sMaterial, $sHeight) = @explode("|-|", $sSchoolFacilities["{$iSurvey}-BT"]);
		
		$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
		$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
		$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
		$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
		
		
		@list($iTotal, $iGood, $iRehabilitation, $iDilapidated, $sMaterial, $sHeight) = @explode("|-|", $sSchoolFacilities["{$iSurvey}-BW"]);
		
		$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
		$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
		$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
		$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
		$sRecord[$j ++] = str_replace(",", " ", $sMaterial);
		$sRecord[$j ++] = str_replace(",", " ", $sHeight);
		
		
		@list($iTotal, $iGood, $iRehabilitation, $iDilapidated, $sMaterial, $sHeight) = @explode("|-|", $sSchoolFacilities["{$iSurvey}-MG"]);
		
		$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
		$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
		$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
		$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
		$sRecord[$j ++] = str_replace(",", " ", $sMaterial);
		$sRecord[$j ++] = str_replace(",", " ", $sHeight);
		
		
		@list($iTotal, $iGood, $iRehabilitation, $iDilapidated, $sMaterial, $sHeight) = @explode("|-|", $sSchoolFacilities["{$iSurvey}-RW"]);
		
		$sRecord[$j ++] = (($iTotal > 0) ? $iTotal : "");
		$sRecord[$j ++] = (($iGood > 0) ? $iGood : "");
		$sRecord[$j ++] = (($iRehabilitation > 0) ? $iRehabilitation : "");
		$sRecord[$j ++] = (($iDilapidated > 0) ? $iDilapidated : "");
		$sRecord[$j ++] = str_replace(",", " ", $sMaterial);
		$sRecord[$j ++] = str_replace(",", " ", $sHeight);
		
			
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
	@unlink($sFile);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );      
?>