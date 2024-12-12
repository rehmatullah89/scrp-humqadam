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
	@require_once("{$sRootDir}requires/fpdf/fpdf.php");
	@require_once("{$sRootDir}requires/fpdi/fpdi.php");
	@require_once("{$sRootDir}requires/fpdi/rotation.php");
	
	class PDF extends PDF_Rotate
	{
		function RotatedText($x,$y,$txt,$angle)
		{
			//Text rotated around its origin
			$this->Rotate($angle,$x,$y);
			$this->Text($x,$y,$txt);
			$this->Rotate(0);
		}

		function RotatedImage($file,$x,$y,$w,$h,$angle)
		{
			//Image rotated around its upper-left corner
			$this->Rotate($angle,$x,$y);
			$this->Image($file,$x,$y,$w,$h);
			$this->Rotate(0);
		}
	}
	
		
	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );

	$iSurveyId = IO::intValue("Id");
        $iSorId = IO::intValue("SorId");

        if(($iSurveyId == 0 || $iSurveyId == "") && ($iSorId != 0 && $iSorId != "")){

            $iSchoolId = getDbValue('school_id', "tbl_sors", "id='$iSorId'");
            $iSurveyId = getDbValue('id', "tbl_surveys", "school_id='$iSchoolId'");
            
        }

	$sSQL = "SELECT * FROM tbl_surveys WHERE id='$iSurveyId'";
	$objDb->query($sSQL);
	
	$iId                = $objDb->getField(0, "id");
	$iSchool            = $objDb->getField(0, "school_id");
	$sEnumerator        = $objDb->getField(0, "enumerator");
	$sDate              = $objDb->getField(0, "date");
	$sOperational       = $objDb->getField(0, "operational");
	$sPefProgramme      = $objDb->getField(0, "pef_programme");
	$sLandAvailable     = $objDb->getField(0, "land_available");
	$sLandDispute       = $objDb->getField(0, "land_dispute");
	$sOtherFunding      = $objDb->getField(0, "other_funding");
	$iTotalClassRooms   = $objDb->getField(0, "class_rooms");
	$iEducationRooms    = $objDb->getField(0, "education_rooms");
	$sShelterLess       = $objDb->getField(0, "shelter_less");
	$sMultiGrading      = $objDb->getField(0, "multi_grading");
	$sPreSelection      = $objDb->getField(0, "pre_selection");
	$iAvgAttendance     = $objDb->getField(0, "avg_attendance");
	$sStatus            = $objDb->getField(0, "status");
	$sQualified         = $objDb->getField(0, "qualified");
	$sComments          = $objDb->getField(0, "comments");

        $sSQL = "SELECT tbl_sors.*,
                (Select name from tbl_admins where tbl_sors.admin_id=id) As _CDC,
                (Select name from tbl_admins where tbl_sors.engineer_id=id) As _DistrictEngineer
                FROM tbl_sors WHERE school_id='$iSchool'";
	$objDb->query($sSQL);
	
        $iSorId             = $objDb->getField(0, "id");
	$sCDC               = $objDb->getField(0, "_CDC");
	$sSorDate           = $objDb->getField(0, "date");
	$sDistrictEngineer  = $objDb->getField(0, "_DistrictEngineer");
	$sPrincipal         = $objDb->getField(0, "principal");
        $sCcsi              = $objDb->getField(0, "ccsi");    
        $sPtc               = $objDb->getField(0, "ptc");    
        $sContactNo         = $objDb->getField(0, "contact_no");    
        
	$sSQL = "SELECT CONCAT(first_name,' ',last_name) as _Contractor
             FROM tbl_contractors
			 WHERE id IN (Select contractor_id from tbl_contracts where contractor_id=tbl_contractors.id AND FIND_IN_SET($iSchool, schools))";
	$objDb->query($sSQL);
	$sContractor      = $objDb->getField(0, "_Contractor");
	
	
	$sSQL = "SELECT * FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);

	$sSchool            = $objDb->getField(0, "name");
	$sCode              = $objDb->getField(0, "code");
        $iStudents          = $objDb->getField(0, "students");
        $iBlocks            = $objDb->getField(0, "blocks");
        $iType              = $objDb->getField(0, "type_id");
	$iClassRooms        = $objDb->getField(0, "class_rooms");
	$iStudentToilets    = $objDb->getField(0, "student_toilets");
	$iStaffRooms        = $objDb->getField(0, "staff_rooms");
	$iStaffToilets      = $objDb->getField(0, "staff_toilets");
	$fCost              = $objDb->getField(0, "cost");
	$iDistrict          = $objDb->getField(0, "district_id");
	$iProvince          = $objDb->getField(0, "province_id");
	$iEduLevel          = $objDb->getField(0, "edu_level");
	$sAddress           = $objDb->getField(0, "address");
	$sDesignationType   = ($objDb->getField(0, "designation_type") == '')?'M':$objDb->getField(0, "designation_type");
	$sTehsil            = $objDb->getField(0, "tehsil");
	$sCity              = $objDb->getField(0, "city");
	$sMarkazUC          = $objDb->getField(0, "markaz_uc");
	$sMoozaVillage      = $objDb->getField(0, "mooza_village");
	$sHeadTeacher       = $objDb->getField(0, "head_teacher_name");
	$sPhone             = $objDb->getField(0, "phone");
	$sHeadTeacherPhone  = $objDb->getField(0, "head_teacher_phone");
	$sEmail             = $objDb->getField(0, "email");
	$sElevation         = $objDb->getField(0, "elevation");
	$sLatitude          = $objDb->getField(0, "latitude");
	$sLongitude          = $objDb->getField(0, "longitude");

	
	 /* -------- -------------------------------- Answers --------------------------- ---------  */
	$sAnswers       = getList("tbl_survey_answers", "question_id", "answer", "survey_id='{$iSurveyId}'");
	$sOthers        = getList("tbl_survey_answers", "question_id", "other", "survey_id='{$iSurveyId}'");
	$sProvinceList  = getList("tbl_provinces", "id", "name");
        $sSchoolTypes   = getList("tbl_school_types", "id", "type", "status='A'");
							/* -------------------------------- */

	$iCheckListPicsCount = getDbValue("COUNT(1)", "tbl_survey_pictures", "survey_id='{$iSurveyId}' AND section_id='15'");
	$iOtherPicsCount     = getDbValue("COUNT(1)", "tbl_survey_pictures", "survey_id='{$iSurveyId}' AND section_id!='15'");
	$iOtherPicsCount     = ($iOtherPicsCount > 0)?(@ceil(($iOtherPicsCount) / 4)):0;

	if ($iCheckListPicsCount == 0 || getDbValue("site_plan", "tbl_survey_checklist", "survey_id='$iSurveyId'") != '')
		$iPrePdfPages  = 14;

	else
		$iPrePdfPages  = 13;

	$iTotalPages = $iPrePdfPages + $iOtherPicsCount + $iCheckListPicsCount;
	
	
	/////////////////////////////////////////Page #1///////////////////////////// 

	$objPdf = new PDF("P", "pt", "A4");
	$objPdf->setSourceFile("{$sRootDir}templates/survey_sor_form.pdf");
	$iTemplateId = $objPdf->importPage(1, '/MediaBox');

	$objPdf->addPage( );
	$objPdf->useTemplate($iTemplateId, 0, 0);
	$objPdf->SetTextColor(128, 128, 128);
	$objPdf->SetFont('Arial', 'B', 12);
	
	$objPdf->Text(175, 382, $sProvinceList[$iProvince]);
	$objPdf->Text(175, 414, getDbValue("name", "tbl_districts", "id='$iDistrict'"));
	$objPdf->Text(175, 448.5, $sSchool);
	$objPdf->Text(175, 481.5, $sCode);
        $objPdf->Text(175, 514.5, $sCDC);
        $objPdf->Text(280, 547.5, $sDistrictEngineer);
        $objPdf->Text(230, 580.5, $sPrincipal);
        $objPdf->Text(230, 613.5, $sCcsi);
        $objPdf->Text(230, 646.5, $sPtc);
	
        $objPdf->Text(340, 684, ($sAnswers[75] == 'Higher Secondary'?'HSS':'Regular'));
	$iAdditionalRoomsReq = @ceil($iAvgAttendance/40) - $iEducationRooms;    
	$objPdf->Text(340, 719, ($iAdditionalRoomsReq>0 && $iAvgAttendance>40)?$iAdditionalRoomsReq:'No');
        
        $AvailableToilets = getDbValue("SUM(total)", "tbl_survey_school_block_details", " (room_type_code='TFS' OR room_type_code='TMS' OR room_type_code='US' OR room_type_code='TB') AND survey_id='$iSurveyId'");
        $iToiletRequired  = @ceil($iAvgAttendance/80) - $AvailableToilets;    
        $objPdf->Text(340, 750, ($iToiletRequired>0 && $iAvgAttendance>40)?$iToiletRequired:'No');
        
	/////////////////////////////////////////Page #2///////////////////////////// 

	$objPdf->setSourceFile("{$sRootDir}templates/survey_sor_form.pdf");
	$iTemplateId = $objPdf->importPage(2, '/MediaBox');
	$objPdf->addPage( );
	$objPdf->useTemplate($iTemplateId, 0, 0);

	$objPdf->SetTextColor(50, 50, 50);
	$objPdf->SetFont('Arial', '', 10);

        $sSrDate = explode('-', $sSorDate);
        $sYear   = $sSrDate[0];
        $sMonth  = $sSrDate[1];
        $sDay    = $sSrDate[2];
        
        $objPdf->Text(100, 162, implode('  ',str_split($sDay))); 
        $objPdf->Text(177, 162, implode('  ',str_split($sMonth))); 
        $objPdf->Text(240, 162, implode('    ',str_split($sYear))); 
        $objPdf->Text(200, 192, $sCode); 
	$objPdf->Text(200, 218, $sSchool);
	
        $objPdf->Text(200, 247, ($sAnswers[81] == "")?$sCity:$sAnswers[81]);
        $objPdf->Text(200, 274, ($sAnswers[80] == "")?$sTehsil:$sAnswers[80]);
        $objPdf->Text(200, 302, $sAnswers[84].' (Ph# '.$sAnswers[86].')');
        $objPdf->Text(200, 330, $sDistrictEngineer);
        $objPdf->Text(200, 358, $sPtc);
        $objPdf->Text(200, 414, $sContactNo);
	
        $objPdf->Text(415, 247, (getDbValue("name", "tbl_districts", "id='$iDistrict'")));
	$objPdf->Text(415, 274, ($sAnswers[83] == "")?$sMoozaVillage:$sAnswers[83]);
        
        
        $objPdf->Text(104, 472, implode('  ',str_split($sDay))); 
        $objPdf->Text(180, 472, implode('  ',str_split($sMonth))); 
        $objPdf->Text(241, 472, implode('    ',str_split($sYear)));
        $objPdf->Text(102, 508, $sDistrictEngineer);
        $objPdf->Text(102, 554, $sCDC);
        $objPdf->Text(185, 598, $sCcsi);
        $objPdf->Text(185, 643, $sPrincipal);
        $objPdf->Text(185, 686, $sPtc);
   
    ////////////////  ******** Main Survey Check ********* ////////////////////////
        if ($sQualified == 'Y'){
                 /////////////////////////////////////////Page #3///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/survey_sor_form.pdf");
                $iTemplateId = $objPdf->importPage(3, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(102, 112, $sCode);
                $objPdf->SetTextColor(14, 149, 69);
                $objPdf->Text(265, 112, $sSchool);
            
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', '', 10);
                
                $DAbledStudents = getDbValue("Sum(boys_count+girls_count)", "tbl_survey_differently_abled_student_numbers", "survey_id='$iSurveyId'");
        
                $objPdf->Text(175, 200, $iAvgAttendance);
                $objPdf->Text(175, 223, $iBlocks);
                $objPdf->Text(175, 246, $sAnswers[75]);
                $objPdf->Text(175, 272, $iEducationRooms);
                $objPdf->Text(175, 300, $AvailableToilets);
                $objPdf->Text(175, 325, ($sAnswers[31]=='N'?0:'1'));
                $objPdf->Text(175, 350, ($sAnswers[43]=='Y'?1:0));
                $objPdf->Text(175, 375, ($sAnswers[42]=='Y'?1:0));
                $objPdf->Text(175, 407, "Not Available");
                $objPdf->Text(175, 432, "Not Available");
                $objPdf->Text(175, 457, "Not Available");
                $objPdf->Text(175, 482, "Not Available");
                $objPdf->Text(175, 510, "Not Available");
                $objPdf->Text(175, 535, "Not Available");
                $objPdf->Text(175, 560, "Not Available");
                $objPdf->Text(175, 587, "Not Available");
                $objPdf->Text(175, 613, "Not Available");
                
                $objPdf->Text(310, 200, "Not Applicable");
                $objPdf->Text(310, 223, "Not Available");
                $objPdf->Text(310, 246, "Not Applicable");
                $objPdf->Text(310, 272, ($iAdditionalRoomsReq>0 && $iAvgAttendance>40)?$iAdditionalRoomsReq.' New Required':'No');
                $objPdf->Text(310, 300, ($iToiletRequired>0 && $iAvgAttendance>80)?$iToiletRequired.' New Required':'No');
                $objPdf->Text(310, 325, ($DAbledStudents>0?'1':0));
                $objPdf->Text(310, 350, ($sAnswers[43]=='Y'?0:1));
                $objPdf->Text(310, 375, ($sAnswers[42]=='Y'?0:1));
                $objPdf->Text(310, 407, "Not Available");
                $objPdf->Text(310, 432, "Not Available");
                $objPdf->Text(310, 457, "Not Available");
                $objPdf->Text(310, 482, "Not Available");
                $objPdf->Text(310, 510, "Not Available");
                $objPdf->Text(310, 535, "Not Available");
                $objPdf->Text(310, 560, "Not Available");
                $objPdf->Text(310, 587, "Not Available");
                $objPdf->Text(310, 613, "Not Available");
                
               	$sSQL = "SELECT * FROM tbl_sor_section_a WHERE sor_id='$iSorId'";
                $objDb->query($sSQL);

                $sSorAAttendance    = $objDb->getField(0, "attendance");
                $sSorABlocks        = $objDb->getField(0, "blocks");
                $sSorAGrades        = $objDb->getField(0, "grades");
                $sSorAClassRooms    = $objDb->getField(0, "class_rooms");
                $sSorANToilets      = $objDb->getField(0, "normal_toilets");
                $sSorADToilets      = $objDb->getField(0, "disable_toilets");
                $sSorAClassRamps    = $objDb->getField(0, "classroom_ramps");
                $sSorAToiletRamps   = $objDb->getField(0, "toilet_ramps");
                $sSorAScienceLab    = $objDb->getField(0, "science_lab");
                $sSorAITLab         = $objDb->getField(0, "it_lab");
                $sSorALibrary       = $objDb->getField(0, "library");
                $sSorAExamHall      = $objDb->getField(0, "exam_hall");
                $sSorAPrincOffice   = $objDb->getField(0, "principal_office");
                $sSorAClerkOffice   = $objDb->getField(0, "clerk_office");
                $sSorAStaffRooms    = $objDb->getField(0, "staff_room");
                $sSorAChowkidarHut  = $objDb->getField(0, "chowkidar_hut");
                $sSorACycleStand    = $objDb->getField(0, "cycle_stand");
                $sSorAInfoCorrect   = $objDb->getField(0, "info_correct");
                $sSorADesignCorrect = $objDb->getField(0, "design_correct");
                $sSorAComments      = $objDb->getField(0, "comments");
                
                if ($sSorAAttendance == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 185, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 185, 12);
                if ($sSorABlocks == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 212, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 212, 12);
                if ($sSorAGrades == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 240, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 240, 12);
                if ($sSorAClassRooms == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 265, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 265, 12);
                if ($sSorANToilets == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 290, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 290, 12);
                if ($sSorADToilets == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 317, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 317, 12);
                if ($sSorAToiletRamps == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 345, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 345, 12);
                if ($sSorAClassRamps == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 370, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 370, 12);
                if ($sSorAScienceLab == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 395, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 395, 12);
                if ($sSorAITLab == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 420, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 420, 12);
                if ($sSorALibrary == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 445, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 445, 12);
                if ($sSorAExamHall == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 473, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 473, 12);
                if ($sSorAPrincOffice == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 500, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 500, 12);
                if ($sSorAClerkOffice == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 525, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 525, 12);
                if ($sSorAStaffRooms == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 550, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 550, 12);
                if ($sSorAChowkidarHut == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 578, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 578, 12);
                if ($sSorACycleStand == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 603, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 603, 12);
                if ($sSorAInfoCorrect == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 634, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 634, 12);
                if ($sSorADesignCorrect == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 475, 660, 12);
        	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 525, 660, 12);

                $objPdf->SetFont('Arial', '', 8);
                $objPdf->SetTextColor(50, 50, 50);
                
                $objPdf->SetXY(95, 687);
                $objPdf->MultiCell(450, 7, $sSorAComments);
             /////////////////////////////////////////Page #4///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/survey_sor_form.pdf");
                $iTemplateId = $objPdf->importPage(4, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(102, 112, $sCode);
                $objPdf->SetTextColor(14, 149, 69);
                $objPdf->Text(265, 112, $sSchool);
            
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', '', 8);
                
                $sFacilitiesList = getList("tbl_sor_facilities", "id", "name", "status='A' AND position>0", "position");
                
                $fTop = 210;
                
                foreach($sFacilitiesList as $iFacilityId => $sFacility){

                    $sSQL = "SELECT * FROM tbl_sor_section_b_details WHERE sor_id='$iSorId' AND facility_id='$iFacilityId'";
                    $objDb->query($sSQL);
                    $iNumbers       = $objDb->getField(0, "numbers");
                    $sSpaceAvailable= $objDb->getField(0, "space_available");
                    $sSorBComments  = $objDb->getField(0, "comments");
                    
                    $sNumber = ($iNumbers<10?"0".$iNumbers:$iNumbers);
                    $objPdf->Text(130, $fTop, implode('  ',str_split($sNumber)));
                    
                    if ($sSpaceAvailable == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 190, $fTop-8, 12);
                    else if($sSpaceAvailable == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 220, $fTop-8, 12);

                    $objPdf->Text(290, $fTop, $sSorBComments);
                    
                    $fTop += 27.5;
                }
                
                $objPdf->SetXY(95, 678);
                $objPdf->MultiCell(450, 7, getDbValue("comments", "tbl_sor_section_b","sor_id='$iSorId'"));
             /////////////////////////////////////////Page #5///////////////////////////// 
                
                $objPdf->setSourceFile("{$sRootDir}templates/survey_sor_form.pdf");
                $iTemplateId = $objPdf->importPage(5, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(102, 112, $sCode);
                $objPdf->SetTextColor(14, 149, 69);
                $objPdf->Text(265, 112, $sSchool);
            
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', '', 10); 

            /////////////////////////////////////////Page #6///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/survey_sor_form.pdf");
                $iTemplateId = $objPdf->importPage(6, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(102, 112, $sCode);
                $objPdf->SetTextColor(14, 149, 69);
                $objPdf->Text(265, 112, $sSchool);
            
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', '', 10); 
                
                $objPdf->Text(100, 170, implode('  ',str_split($sDay))); 
                $objPdf->Text(177, 170, implode('  ',str_split($sMonth))); 
                $objPdf->Text(240, 170, implode('    ',str_split($sYear))); 

                $sMeetingParticipants = getList("tbl_sor_participants", "name", "designation", "sor_id='$iSorId'");
                
                $objPdf->SetTextColor(50, 50, 50);
                $objPdf->SetFont('Arial', '', 8);
                
                $fTop = 223;
                foreach($sMeetingParticipants as $sParticipant => $sDesignation){
                    
                    $objPdf->Text(70, $fTop, $sParticipant);
                    $objPdf->Text(230, $fTop, $sDesignation);
                    
                    $fTop += 57;
                }
                
                /////////////////////////////////////////Page #7///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/survey_sor_form.pdf");
                $iTemplateId = $objPdf->importPage(7, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                
                $objPdf->SetTextColor(0, 0, 0);
                $objPdf->SetFont('Arial', '', 8);    
                
                /////////////////////////////////////////Page #8///////////////////////////// 
                $sSitePlanPdf = getDbValue('site_plan_pdf', 'tbl_survey_checklist', "survey_id='$iSurveyId'");
                $SourceFile = "{$sRootDir}files/surveys/".$sSitePlanPdf;
                $ext = pathinfo($sSitePlanPdf, PATHINFO_EXTENSION);

                if($sSitePlanPdf != "" && @file_exists($SourceFile)){
                    
                    $objPdf->setSourceFile($SourceFile);
                    $iTemplateId = $objPdf->importPage(1, '/MediaBox');
                    $objPdf->addPage("L");
                    //$objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->useTemplate($iTemplateId, null, null, 850, $size['h'], true);
                    
                    $objPdf->SetTextColor(128, 128, 128);
                    $objPdf->SetFont('Arial', '', 10);
                    
                    $objPdf->Text(30, 50, "Site Plan");
                }
                
                 /////////////////////////////////////////Page #9///////////////////////////// 
                $sStructurePdf = getDbValue('structure_pdf', 'tbl_survey_checklist', "survey_id='$iSurveyId'");
                $SourceFile = "{$sRootDir}files/surveys/".$sStructurePdf;
                $ext = pathinfo($sStructurePdf, PATHINFO_EXTENSION);
                
                if($sStructurePdf != "" && @file_exists($SourceFile)){
                
                    $objPdf->setSourceFile($SourceFile);
                    $iTemplateId = $objPdf->importPage(1, '/MediaBox');
                    $objPdf->addPage("L");
                    //$objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->useTemplate($iTemplateId, null, null, 850, $size['h'], true);

                    $objPdf->SetTextColor(128, 128, 128);
                    $objPdf->SetFont('Arial', '', 10);
                    
                    $objPdf->Text(50, 50, "Proposed Structure Plan");
                }
                
        } // main if ends
        
	
        ///////******** output PDF *********///////
        $objPdf->Output("SOR-{$sCode}.pdf", "D");



	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>