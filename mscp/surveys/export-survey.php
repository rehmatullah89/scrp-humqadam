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

			
	$sSQL = "SELECT CONCAT(first_name,' ',last_name) as _Contractor
             FROM tbl_contractors
			 WHERE id IN (Select contractor_id from tbl_contracts where contractor_id=tbl_contractors.id AND FIND_IN_SET($iSchool, schools))";
	$objDb->query($sSQL);
	$sContractor      = $objDb->getField(0, "_Contractor");
	
	
	$sSQL = "SELECT * FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);

	$sSchool            = $objDb->getField(0, "name");
	$sCode              = $objDb->getField(0, "code");
	$sType              = $objDb->getField(0, "type");
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
	$objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
	$iTemplateId = $objPdf->importPage(1, '/MediaBox');

	$objPdf->addPage( );
	$objPdf->useTemplate($iTemplateId, 0, 0);
	$objPdf->SetTextColor(128, 128, 128);
	$objPdf->SetFont('Arial', 'B', 12);
	
	$objPdf->Text(180, 385, $sProvinceList[$iProvince]);
	$objPdf->Text(180, 417, getDbValue("name", "tbl_districts", "id='$iDistrict'"));
	$objPdf->Text(180, 452, ($sAnswers[74]==''?$sSchool:$sAnswers[74]));
	$objPdf->Text(180, 485, $sCode); 
	$objPdf->Text(180, 520, $sEnumerator);
	$objPdf->Text(180, 553, (($iProvince == 1) ? 'Innovative Development Strategies (Pvt) Ltd' : 'Associates in Development (Pvt) Ltd'));
	$objPdf->Text(240, 585, (($sQualified == 'Y') ? 'Qualified' : 'Disqualified'));
	$objPdf->Text(240, 620, (($sStatus == 'C') ? 'Completed' : 'In-Complete'));
	$objPdf->Text(240, 654, $iTotalClassRooms);
	$objPdf->Text(380, 689, $iEducationRooms);
	$objPdf->Text(340, 723, $iAvgAttendance);
	
	$iAdditionalRoomsReq = @ceil($iAvgAttendance/40) - $iEducationRooms;    
	$objPdf->Text(340, 754, ($iAdditionalRoomsReq>0 && $iAvgAttendance>40)?$iAdditionalRoomsReq:'No');

	/////////////////////////////////////////Page #2///////////////////////////// 

	$objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
	$iTemplateId = $objPdf->importPage(2, '/MediaBox');
	$objPdf->addPage( );
	$objPdf->useTemplate($iTemplateId, 0, 0);

	$objPdf->SetTextColor(128, 128, 128);
	$objPdf->SetFont('Arial', 'B', 10);
	$objPdf->Text(55, 93, ('Baseline and Technical Survey for '.$sType.' '.(($sAnswers[74] == '') ? $sSchool : $sAnswers[74])));
	$objPdf->SetTextColor(14, 149, 69);
	$objPdf->SetFont('Arial', 'B', 10);
	$objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
	
	$objPdf->SetTextColor(0, 0, 0);
	$objPdf->SetFont('Arial', '', 10);

	$objPdf->Text(205, 147, (($sAnswers[74] == '') ? $sSchool : $sAnswers[74]));
	$objPdf->Text(205, 173, (($sAnswers[76] == '') ? $sAddress : $sAnswers[76]));
	$objPdf->Text(205, 205, $sCode); 
	$objPdf->Text(205, 228, (getDbValue("name", "tbl_districts", "id='$iDistrict'").', '.$sProvinceList[$iProvince]));
	$objPdf->Text(205, 250, $sEnumerator);
	$objPdf->Text(205, 273, formatDate($sDate, $_SESSION['DateFormat']));

	
	if ($sOperational == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 295, 12);
	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 295, 12);

	if ($sPefProgramme == 'Y')
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 330, 12);
	else
		 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 330, 12);

	if ($sLandAvailable == 'Y')
		$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 370, 12);
	else
		$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 370, 12);
		 
	if ($sLandDispute == 'N')
	{
		$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 420, 12);
		
		if ($sOtherFunding == 'Y')
			$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 465, 12);
		else
			$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 465, 12); 		
	}

	else
		$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 420, 12);		
		

	$objPdf->Text(350, 515, (($iTotalClassRooms > 0) ? $iTotalClassRooms : ''));
	$objPdf->Text(470, 552, (($iEducationRooms > 0) ? $iEducationRooms : ''));

	if ($sShelterLess == 'Y')
		$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 585, 12);

	else if ($sShelterLess != '')
		$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 585, 12);       

	if ($sMultiGrading == 'Y')
		$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 630, 12);
	
	else if ($sMultiGrading != '')
		$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 630, 12);

	$objPdf->Text(350, 675, (($iAvgAttendance > 0) ? $iAvgAttendance : ''));
	
	if ($sPreSelection == 'Y')
		$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 705, 12);

	else
		$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 705, 12);
		  
	$objPdf->SetXY(70, 738);
	$objPdf->MultiCell(420, 10, $sComments);

	$objPdf->SetFont('Arial', 'B', 10);

	if ($sQualified == 'Y')
	   $objPdf->Text(500, 800, "Page 1 of {$iTotalPages}");

	else 
	   $objPdf->Text(500, 800, "Page 1 of 1"); 

   
    ////////////////  ******** Main Survey Check ********* ////////////////////////
        if ($sQualified == 'Y'){
                 /////////////////////////////////////////Page #3///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                $iTemplateId = $objPdf->importPage(3, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                $objPdf->SetTextColor(14, 149, 69);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
            
                $objPdf->SetTextColor(0, 0, 0);
                $objPdf->SetFont('Arial', '', 10);

                $objPdf->SetFont('Arial', '', 10);
                $objPdf->SetXY(150, 143);
                $objPdf->MultiCell(130, 8, ($sAnswers[74]==''?$sSchool:$sAnswers[74]));

                $objPdf->SetXY(150, 167);
                $objPdf->MultiCell(400, 8, ($sAnswers[76]==''?$sAddress:$sAnswers[76]));

                $schoolTypeArr =  array_map('trim',explode("\n", $sAnswers[75]));
                if(in_array('Primary', $schoolTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 185, 220, 10);
                else if(in_array('Middle (1-8)', $schoolTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 335, 220, 10);
                else if(in_array('Middle (6-8)', $schoolTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 497, 220, 10);
                else if(in_array('High School', $schoolTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 335, 242, 10);
                else if(in_array('Higher Secondary', $schoolTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 497, 242, 10);

                $genderTypeArr =  array_map('trim',explode("\n", $sAnswers[77]));
                if(in_array('Male', $genderTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 185, 290, 10);
                else if(in_array('Female', $genderTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 335, 290, 10);
                else if(in_array('Co-education', $genderTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 497, 290, 10);
                else if(in_array('Designated Male, both attend', $genderTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 335, 310, 10);
                else if(in_array('Designated Female, both attend', $genderTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 497, 310, 10);
                
                $objPdf->SetFont('Arial', '', 8);
                
                $objPdf->Text(240, 360, ($sAnswers[73]==''?$sCode:$sAnswers[73]));        
                $objPdf->Text(240, 384, getDbValue("name", "tbl_districts", "id='$iDistrict'"));
                $objPdf->Text(240, 407, $sAnswers[80]);        
                $objPdf->Text(240, 430, $sAnswers[81]);
                $objPdf->Text(240, 455, $sAnswers[82]);
                $objPdf->Text(240, 475, $sAnswers[83]);

                $objPdf->Text(430, 360, $sAnswers[84]);        
                $objPdf->Text(430, 384, ($sAnswers[85]==''?$sPhone:$sAnswers[85]));
                $objPdf->Text(430, 407, $sAnswers[86]);        
                $objPdf->Text(430, 430, ($sAnswers[87]==''?$sEmail:$sAnswers[87]));
                $objPdf->Text(430, 455, $sAnswers[88]);
                $objPdf->Text(430, 475, ($sAnswers[89]==''?$sLatitude:$sAnswers[89]).', '.($sAnswers[90]==''?$sLongitude:$sAnswers[90]));
       
                $objPdf->Text(390, 540, $sAnswers[1]);

                    $classStructArr = array_map('trim',explode("\n", $sAnswers[5]));
                    if (in_array("Partially shelterless school", $classStructArr))
			$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 346, 580, 10);    
                    if (in_array("Completely shelterless", $classStructArr))
                    	$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 510, 580, 10);    
                    if (in_array("House", $classStructArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 346, 605, 10);    
                    if (in_array("School", $classStructArr))
			$objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 510, 605, 10);    
                    $objPdf->Text(135, 637, $sOthers[5]);
		
                $classShiftArr =  array_map('trim',explode("\n", $sAnswers[6]));
                if (in_array("Morning", $classShiftArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 660, 10);    
                if (in_array("Evening", $classShiftArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 510, 660, 10);    
 
                if ($sAnswers[7] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 690, 10);
                else if ($sAnswers[7] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 510, 690, 10);

                $objPdf->Text(460, 733, $sAnswers[8]);     
                
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(500, 800, "Page 2 of {$iTotalPages}");
             /////////////////////////////////////////Page #4///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                $iTemplateId = $objPdf->importPage(4, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                $objPdf->SetTextColor(14, 149, 69);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
            
                $objPdf->SetTextColor(0, 0, 0);
                $objPdf->SetFont('Arial', '', 8);    

                $sSQL = "SELECT * FROM tbl_survey_teacher_numbers WHERE survey_id='$iSurveyId'";
                $objDb->query($sSQL);
                $heigt1 = 16;
                for($j=0; $j < 9; $j+=3){
                    $objPdf->Text(205, 205+$heigt1, ($objDb->getField(0+$j, "male_count") == 0?'':$objDb->getField(0+$j, "male_count"))); 
                    $objPdf->Text(250, 205+$heigt1, ($objDb->getField(0+$j, "female_count") == 0?'':$objDb->getField(0+$j, "female_count")));
                    $objPdf->Text(290, 205+$heigt1, ($objDb->getField(0+$j, "both_count") == 0?'':$objDb->getField(0+$j, "both_count"))); 

                    $objPdf->Text(340, 205+$heigt1, ($objDb->getField(1+$j, "male_count")==0?'':$objDb->getField(1+$j, "male_count"))); 
                    $objPdf->Text(380, 205+$heigt1, ($objDb->getField(1+$j, "female_count")==0?'':$objDb->getField(1+$j, "female_count")));

                    $objPdf->Text(418, 205+$heigt1, ($objDb->getField(2+$j, "male_count")==0?'':$objDb->getField(2+$j, "male_count"))); 
                    $objPdf->Text(458, 205+$heigt1, ($objDb->getField(2+$j, "female_count")==0?'':$objDb->getField(2+$j, "female_count")));
                    $objPdf->Text(498, 205+$heigt1, ($objDb->getField(2+$j, "both_count")==0?'':$objDb->getField(2+$j, "both_count"))); 

                    $heigt1 += 20;
                }

                $sSQL = "SELECT * FROM tbl_survey_students_enrollment WHERE survey_id='$iSurveyId'";
                $objDb->query($sSQL);

                $objPdf->Text(115, 360, ($objDb->getField(0, "male_count")==0?'':$objDb->getField(0, "male_count"))); 
                $objPdf->Text(155, 360, ($objDb->getField(0, "female_count")==0?'':$objDb->getField(0, "female_count")));
                $objPdf->Text(193, 360, ($objDb->getField(0, "both_count")==0?'':$objDb->getField(0, "both_count"))); 

                $objPdf->Text(222, 360, ($objDb->getField(1, "male_count")==0?'':$objDb->getField(1, "male_count"))); 
                $objPdf->Text(260, 360, ($objDb->getField(1, "female_count")==0?'':$objDb->getField(1, "female_count")));
                $objPdf->Text(300, 360, ($objDb->getField(1, "both_count")==0?'':$objDb->getField(1, "both_count"))); 

                $objPdf->Text(335, 360, ($objDb->getField(2, "male_count")==0?'':$objDb->getField(2, "male_count")));
                $objPdf->Text(375, 360, ($objDb->getField(2, "female_count")==0?'':$objDb->getField(2, "female_count")));
                $objPdf->Text(412, 360, ($objDb->getField(2, "both_count")==0?'':$objDb->getField(2, "both_count"))); 

                $objPdf->Text(440, 360, ($objDb->getField(3, "male_count")==0?'':$objDb->getField(3, "male_count")));
                $objPdf->Text(478, 360, ($objDb->getField(3, "female_count")==0?'':$objDb->getField(3, "female_count")));
                $objPdf->Text(510, 360, ($objDb->getField(3, "both_count")==0?'':$objDb->getField(3, "both_count"))); 
                
                $sSQL = "SELECT * FROM tbl_survey_student_attendance_numbers WHERE survey_id='$iSurveyId'";
                $objDb->query($sSQL);
                $height2 = 292;
                $bMorning = 0;
                $gMorning = 0;
                $bEvening = 0;
                $gEvening = 0;
                for($i=0; $i<=12; $i++){ 
                    $objPdf->Text(190, 208+$height2, ($objDb->getField($i, "boys_count_morning")==0?'':$objDb->getField($i, "boys_count_morning"))); 
                    $objPdf->Text(270, 208+$height2, ($objDb->getField($i, "girls_count_morning")==0?'':$objDb->getField($i, "girls_count_morning"))); 
                    $objPdf->Text(380, 208+$height2, ($objDb->getField($i, "boys_count_evening")==0?'':$objDb->getField($i, "boys_count_evening"))); 
                    $objPdf->Text(480, 208+$height2, ($objDb->getField($i, "girls_count_evening")==0?'':$objDb->getField($i, "girls_count_evening"))); 
                    $height2 += 16.7;
                    $bMorning +=  $objDb->getField($i, "boys_count_morning");
                    $gMorning +=  $objDb->getField($i, "girls_count_morning");
                    $bEvening +=  $objDb->getField($i, "boys_count_evening");
                    $gEvening +=  $objDb->getField($i, "girls_count_evening");
                }
                $objPdf->Text(190, 715, $bMorning); 
                $objPdf->Text(270, 715, $gMorning); 
                $objPdf->Text(380, 715, $bEvening); 
                $objPdf->Text(480, 715, $gEvening); 

                $sSQL = "SELECT * FROM tbl_survey_differently_abled_student_numbers WHERE survey_id='$iSurveyId'";
                $objDb->query($sSQL);
                $objPdf->Text(120, 770, ($objDb->getField(0, "boys_count")==0?'':$objDb->getField(0, "boys_count")));
                $objPdf->Text(220, 770, ($objDb->getField(0, "boys_grades")==0?'':$objDb->getField(0, "boys_grades")));
                $objPdf->Text(350, 770, ($objDb->getField(0, "girls_count")==0?'':$objDb->getField(0, "girls_count")));
                $objPdf->Text(450, 770, ($objDb->getField(0, "girls_grades")==0?'':$objDb->getField(0, "girls_grades")));
                
                $objPdf->Text(220, 444, $objDb->getField(0, "morning_time_in"));
                $objPdf->Text(260, 444, $objDb->getField(0, "morning_time_out"));
                $objPdf->Text(420, 444, $objDb->getField(0, "evening_time_in"));
                $objPdf->Text(465, 444, $objDb->getField(0, "evening_time_out"));
                
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(500, 800, "Page 3 of {$iTotalPages}");
             /////////////////////////////////////////Page #5///////////////////////////// 
                
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                $iTemplateId = $objPdf->importPage(5, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                $objPdf->SetTextColor(14, 149, 69);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
            
                $objPdf->SetTextColor(0, 0, 0);
                $objPdf->SetFont('Arial', '', 8);    

                if ($sAnswers[9] == 'Y'){
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 450, 142, 10);
			if ($sAnswers[91] == 'New Construction')
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 390, 180, 10);
			else if ($sAnswers[91] == 'Rehabilitation work')
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 180, 10);
	
                }else if ($sAnswers[9] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 142, 10);
			
			
                $objPdf->Text(280, 217, $sAnswers[10]);
                $objPdf->Text(410, 247, $sAnswers[12]);


            if ($sAnswers[13] == 'Y')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 450, 265, 10);
            else if ($sAnswers[13] == 'N')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 265, 10);

            $objPdf->SetXY(180, 320);
            $objPdf->MultiCell(300, 10, $sAnswers[14]);
            
            $landStatusArr =  array_map('trim',explode("\n", $sAnswers[15]));
            if (in_array("Community Land", $landStatusArr))
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 355, 415, 10);    
            if (in_array("Rented", $landStatusArr))
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 508, 415, 10);    
            if (in_array("Owned by Government", $landStatusArr))    
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 355, 438, 10); 
            if (in_array("Land Mutated to Government", $landStatusArr))    
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 508, 438, 10);     
            $objPdf->Text(135, 466, $sOthers[15]);    
            
            if ($sAnswers[16] == 'Y')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 453, 487, 10);
            else if ($sAnswers[16] == 'N'){
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 508, 487, 10);
                $objPdf->SetXY(180, 520);
                $objPdf->MultiCell(300, 10, $sAnswers[17]);
            }
            
            if ($sAnswers[18] == 'Y')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 205, 610, 10);
            else if ($sAnswers[19] == 'Y')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 355, 610, 10);
            else if ($sAnswers[19] == 'N')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 508, 610, 10);
            $objPdf->Text(410, 643, $sAnswers[20]);   
            
            if ($sAnswers[21] == 'Y')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 325, 680, 10);
            else if ($sAnswers[21] == 'N')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 410, 680, 10);
            else 
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 508, 680, 10);

            $objPdf->SetXY(180, 717);
            $objPdf->MultiCell(300, 10, $sAnswers[92]);

            $objPdf->SetFont('Arial', 'B', 10);
            $objPdf->Text(500, 800, "Page 4 of {$iTotalPages}");
            /////////////////////////////////////////Page #6///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                $iTemplateId = $objPdf->importPage(6, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                $objPdf->SetTextColor(14, 149, 69);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
            
                $objPdf->SetTextColor(0, 0, 0);
                $objPdf->SetFont('Arial', '', 8);    

                if ($sAnswers[22] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 447, 142, 10);
                else if ($sAnswers[22] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 142, 10);    

                $schoolWaterArr =  array_map('trim',explode("\n", $sAnswers[23]));
                if (in_array("Open/ Covered Well",$schoolWaterArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 200, 185, 10);    
                if (in_array("Public Supply", $schoolWaterArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 355, 185, 10);    
                if (in_array("Shallow borehole hand pump", $schoolWaterArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 185, 10); 
                if (in_array("Borehole with motor pump", $schoolWaterArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 355, 210, 10);   
                if (in_array("Deep borehole hand pump", $schoolWaterArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 210, 10); 
                $objPdf->Text(140, 240, $sOthers[23]);

                if ($sAnswers[24] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 447, 265, 10);
                else if ($sAnswers[24] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 265, 10);
 
                if ($sAnswers[25] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 447, 295, 10);
                else if ($sAnswers[25] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 295, 10);
                
                $storageArr =  array_map('trim',explode("\n", $sAnswers[26]));
                if (in_array(htmlentities("Overhead Concrete Tank"), $storageArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 355, 345, 10);    
                if (in_array("Ground Storage Tank", $storageArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 345, 10);    
                if (in_array("Overhead Brick Masonry Tank", $storageArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 355, 365, 10); 
                if (in_array("Fiberglass", $storageArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 365, 10);   
                $objPdf->Text(140, 406, $sOthers[26]);    
                $objPdf->Text(440, 406, $sAnswers[27]);    

                if ($sAnswers[28] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 450, 425, 10);
                else if ($sAnswers[28] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 425, 10);
                
                $schoolDrainArr =  array_map('trim',explode("\n", $sAnswers[29]));
                if (in_array(htmlentities("Open Drain"), $schoolDrainArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 202, 480, 10);    
                if (in_array("Connected to Municipality Line", $schoolDrainArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 352, 480, 10); 
                if (in_array("Culverts", $schoolDrainArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 480, 10);
                if (in_array("Open Field", $schoolDrainArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 352, 500, 10);
                if (in_array("No Drainage System", $schoolDrainArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 505, 500, 10);    
                $objPdf->Text(135, 530, $sOthers[29]);
                
                $objPdf->SetXY(175, 560);
                $objPdf->MultiCell(300, 10, $sAnswers[93]);
                
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(500, 800, "Page 5 of {$iTotalPages}");
            /////////////////////////////////////////Page #7///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                $iTemplateId = $objPdf->importPage(7, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                
                $objPdf->SetTextColor(128, 128, 128);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                $objPdf->SetTextColor(14, 149, 69);
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
            
                $objPdf->SetTextColor(0, 0, 0);
                $objPdf->SetFont('Arial', '', 8);    
                
                if ($sAnswers[30] == 'Y'){
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 445, 144, 10);

                    if ($sAnswers[31] == 'Y'){
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 445, 177, 10);
                        
                        if ($sAnswers[32] == 'Y')
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 445, 215, 10);
                        else if ($sAnswers[32] == 'N')
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 502, 215, 10);

                        $objPdf->Text(380, 255, $sAnswers[33]);

                        if ($sAnswers[34] == 'Yes')
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 342, 290, 10);
                        else if ($sAnswers[34] == 'No')
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 400, 290, 10);
                        else 
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 500, 290, 10);    

                    }else if ($sAnswers[31] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 502, 177, 10);
                    
                    $schoolDisposalArr =  array_map('trim',explode("\n", $sAnswers[35]));
                    if (in_array(htmlentities("Septic Tank"), $schoolDisposalArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 175, 345, 10);    
                    if (in_array("Connected to Sewer", $schoolDisposalArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 282, 345, 10);    
                    if (in_array("Dry Pit", $schoolDisposalArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 391, 345, 10); 
                    if (in_array("Leach pit", $schoolDisposalArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 500, 345, 10);
                    $objPdf->Text(165, 386, $sOthers[35]);     
                    $objPdf->Text(425, 386, $sAnswers[36]);   
                   
                    if ($sAnswers[37] == 'Y'){
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 445, 407, 10);
                    
                        if ($sAnswers[38] == 'Yes')
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 293, 445, 10);
                        else if ($sAnswers[38] == 'No')
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 370, 445, 10);
                        else
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 500, 445, 10);   
                            
                    }else if ($sAnswers[37] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 500, 407, 10);
                       
                        $objPdf->SetXY(170, 480);
                        $objPdf->MultiCell(300, 10, $sAnswers[94]);
                        
                }else if ($sAnswers[30] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 502, 144, 10);   

                if ($sAnswers[39] == 'Y'){
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 445, 615, 10);   
                        
                        $schoolMeterArr =  array_map('trim',explode("\n", $sAnswers[40]));
                        if (in_array(htmlentities('Single Phase meter'), $schoolMeterArr))
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 360, 650, 10);
                        if (in_array(htmlentities('Three Phase meter'), $schoolMeterArr))
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 500, 650, 10);   

                        if ($sAnswers[41] == 'Y')
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 445, 680, 10);
                        else if ($sAnswers[41] == 'N')
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 500, 680, 10);    
                            
                }else if ($sAnswers[39] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 500, 615, 10);    
                
                $objPdf->SetXY(170, 712);
                $objPdf->MultiCell(300, 10, $sAnswers[95]);    
                
                $objPdf->SetFont('Arial', 'B', 10);
                $objPdf->Text(500, 800, "Page 6 of {$iTotalPages}");
            /////////////////////////////////////////Page #8///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(8, '/MediaBox');
                    $objPdf->addPage( );
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    
                    $objPdf->SetTextColor(128, 128, 128);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                    $objPdf->SetTextColor(14, 149, 69);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);

                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8);    
                    
                    if ($sAnswers[42] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 140, 10);
                    else if ($sAnswers[42] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 140, 10); 

                    if ($sAnswers[43] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 170, 10);
                    else if ($sAnswers[43] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 170, 10); 
                    
                    $objPdf->SetXY(165, 200);
                    $objPdf->MultiCell(310, 10, $sAnswers[96]);
                        
                    if ($sAnswers[44] == 'Urban')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 440, 253, 10);
                    else if ($sAnswers[44] == 'Rural')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 253, 10); 

                    $schoolHazardArr =  array_map('trim',explode("\n", $sAnswers[45]));
                    if (in_array(htmlentities("Waste Water Dump"), $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 190, 290, 10);    
                    if (in_array("Dirty Water Pond", $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 340, 290, 10);    
                    if (in_array(htmlentities("Power Lines/ Poles"), $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 290, 10);    
   
                    if (in_array("Graveyard", $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 190, 315, 10);    
                    if (in_array(htmlentities("Uncovered Open Well"), $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 340, 315, 10);    
                    if (in_array("Located in Waterway", $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 315, 10);   

                    if (in_array("Water Logged Areas", $schoolHazardArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 190, 338, 10); 
                    if (in_array("Landslides", $schoolHazardArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 340, 338, 10);
                    if (in_array("Floods", $schoolHazardArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 338, 10);
                        
                    if (in_array("Open Waste Pits", $schoolHazardArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 190, 360, 10);
                    if (in_array("Creek/ River through School", $schoolHazardArr))    
                       $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 340, 360, 10);
                    if (in_array("", $schoolHazardArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 360, 10);
                    $objPdf->Text(120, 390, $sOthers[45]);
                    
                    if (in_array("Floods", $schoolHazardArr)){
                        $objPdf->Text(170, 438, $sAnswers[46]);  
                        $objPdf->Text(328, 438, $sAnswers[47]);  
                        $objPdf->Text(475, 438, $sAnswers[48]); 
                    }
                    
                    $schoolSurrondArr =  array_map('trim',explode("\n", $sAnswers[49]));
                    if (in_array("Hilly", $schoolSurrondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 190, 475, 10); 
                    else if (in_array("Plain", $schoolSurrondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 340, 475, 10);
                    else if (in_array("Undulating", $schoolSurrondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 492, 475, 10);
                    
                    $schoolSoilArr =  array_map('trim',explode("\n", $sAnswers[50]));
                    if (in_array("Built up area", $schoolSoilArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 190, 515, 10); 
                    else if (in_array("Fields", $schoolSoilArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 340, 515, 10);
                    else if (in_array("Vegetation", $schoolSoilArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 492, 515, 10);
                    $objPdf->Text(120, 547, $sOthers[50]);

                    $schoolSoilCondArr =  array_map('trim',explode("\n", $sAnswers[51]));
                    if (in_array("Sandy", $schoolSoilCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 168, 580, 10); 
                    else if (in_array("Clay", $schoolSoilCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 275, 580, 10);
                    else if (in_array("Shingle", $schoolSoilCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 383, 580, 10);
                    else if (in_array("Fill", $schoolSoilCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 580, 10);
                    
                    $pos1 = strpos($sAnswers[52], 'x');
                    $pos2 = strpos($sAnswers[52], 'X');
                    if ($pos1 == true){
                        $expo = explode("x", $sAnswers[52]);
                        $objPdf->Text(376, 617, $expo[0]);
                        $objPdf->Text(476, 617, $expo[1]);
                    }else if ($pos2 == true){
                        $expo = explode("X", $sAnswers[52]);
                        $objPdf->Text(376, 617, $expo[0]);
                        $objPdf->Text(476, 617, $expo[1]);
                    }else    
                        $objPdf->Text(376, 617, $sAnswers[52]);

                    $schoolFoundationArr =  array_map('trim',explode("\n", $sAnswers[53]));
                    if (in_array("Isolated footing", $schoolFoundationArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 168, 660, 10); 
                    if (in_array("Step-brick", $schoolFoundationArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 275, 660, 10);
                    if (in_array("Stone", $schoolFoundationArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 383, 660, 10);
                    if (in_array("Strip footings", $schoolFoundationArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 660, 10);
                    $objPdf->Text(120, 690, $sOthers[53]);
                    
                    $objPdf->Text(470, 717, $sAnswers[54]);
                    $objPdf->Text(350, 740, $sAnswers[55]);				
                    $objPdf->Text(470, 740, $sAnswers[56]);
                    
                    $objPdf->SetXY(160, 755);
                    $objPdf->MultiCell(310, 10, $sAnswers[97]);
                    
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(500, 800, "Page 7 of {$iTotalPages}");
                    /////////////////////////////////////////Page #9///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(9, '/MediaBox');
                    $objPdf->addPage( );
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    
                    $objPdf->SetTextColor(128, 128, 128);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                    $objPdf->SetTextColor(14, 149, 69);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
            
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8);    
                    
                    $natureExpectedArr =  array_map('trim',explode("\n", $sAnswers[57]));
                    if (in_array("Non-obstructed/ plain", $natureExpectedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 165, 155, 10); 
                    else if (in_array("Obstructed", $natureExpectedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 248, 155, 10);
                    else if (in_array("Narrow", $natureExpectedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 327, 155, 10);
                    else if (in_array("Steep", $natureExpectedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 410, 155, 10);
                    else if (in_array("Sharp turns", $natureExpectedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 155, 10);
                    $objPdf->Text(120, 187, $sOthers[57]);

                    $constrSuppliedArr =  array_map('trim',explode("\n", $sAnswers[58]));
                    if (in_array("Heavy Vehicle", $constrSuppliedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 165, 220, 10); 
                    if (in_array("Tractor", $constrSuppliedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 248, 220, 10);
                    if (in_array("Pick-up Truck", $constrSuppliedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 327, 220, 10);
                    if (in_array("Animal Transport", $constrSuppliedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 410, 220, 10);
                    if (in_array("Car", $constrSuppliedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 220, 10);
                            $objPdf->Text(120, 250, $sOthers[58]);

                    $roadCondArr =  array_map('trim',explode("\n", $sAnswers[59]));
                    if (in_array("Black top/ PCC", $roadCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 185, 287, 10); 
                    else if (in_array("Shingled/ Stone Soled", $roadCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 340, 287, 10);
                    else if (in_array("Kacha", $roadCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 287, 10);
                    $objPdf->Text(120, 317, $sOthers[59]);

                    $roadDistArr =  array_map('trim',explode("\n", $sAnswers[60]));
                    if (in_array("0-2km", $roadDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 168, 355, 10); 
                    else if (in_array("2-10km", $roadDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 275, 355, 10);
                    else if (in_array("10-30km", $roadDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 385, 355, 10);
                    else if (in_array(">30km", $roadDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 355, 10);

                    $townDistArr =  array_map('trim',explode("\n", $sAnswers[61]));
                    if (in_array("0-2km", $townDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 168, 405, 10); 
                    else if (in_array("2-10km", $townDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 275, 405, 10);
                    else if (in_array("10-30km", $townDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 385, 405, 10);
                    else if (in_array(">30km", $townDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 405, 10);


                    if ($sAnswers[62] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 438, 438, 10);
                    else if ($sAnswers[62] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 490, 438, 10);   	

                    
                    $objPdf->SetXY(165, 475);
                    $objPdf->MultiCell(300, 10, $sAnswers[98]);  

                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(500, 800, "Page 8 of {$iTotalPages}");
                    /////////////////////////////////////////Page #10///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(10, '/MediaBox');
                    $objPdf->addPage('');
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    
                    $objPdf->SetTextColor(128, 128, 128);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                    $objPdf->SetTextColor(14, 149, 69);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
            
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8);    

                    $iAge    = getList("tbl_survey_school_blocks", "block", "age", "survey_id='{$iSurveyId}'");
                    $iStorey = getList("tbl_survey_school_blocks", "block", "storeys", "survey_id='{$iSurveyId}'");
                    $sCM     = getList("tbl_survey_school_blocks", "block", "cm", "survey_id='{$iSurveyId}'");


                    $objPdf->Text(120, 160, $iAge['b1']);
                    $objPdf->Text(300, 160, $iStorey['b1']);
                    
                    $sBlock1 = explode(',', $sCM['b1']);
                    if(in_array('R', $sBlock1))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 75, 185, 10);
                    if(in_array('S', $sBlock1))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 345, 185, 10);                   
                    if(in_array('U', $sBlock1))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 75, 205, 10);
                    if(in_array('F', $sBlock1))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 345, 205, 10);
                        
                    $sSQL = "SELECT * FROM tbl_survey_school_block_details WHERE survey_id='$iSurveyId' Order By id";
                    $objDb->query($sSQL);
                    $iHeight1 = 0;
                  
                    for($i=0; $i<10; $i++){
                        $objPdf->Text(280, 255 + $iHeight1, ($objDb->getField($i, "total")==0?'':$objDb->getField($i, "total")));
                        $objPdf->Text(360, 255 + $iHeight1, ($objDb->getField($i, "good")==0?'':$objDb->getField($i, "good")));
                        $objPdf->Text(430, 255 + $iHeight1, ($objDb->getField($i, "rehabilitation")==0?'':$objDb->getField($i, "rehabilitation")));
                        $objPdf->Text(500, 255 + $iHeight1, ($objDb->getField($i, "dilapidated")==0?'':$objDb->getField($i, "dilapidated")));
                        $iHeight1 += 16.4;
                    }
                            
                  

                    $objPdf->Text(120, 470, $iAge['b2']);
                    $objPdf->Text(300, 470, $iStorey['b2']);
                    
                    $sBlock2 = explode(',', $sCM['b2']);
                    if(in_array('R', $sBlock2))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 75, 495, 10);
                    if(in_array('S', $sBlock2))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 345, 495, 10);                   
                    if(in_array('U', $sBlock2))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 75, 515, 10);
                    if(in_array('F', $sBlock2))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 345, 515, 10);
                        
                    $iHeight1 = 307;
                    for($i=10; $i<20; $i++){
                        $objPdf->Text(280, 255 + $iHeight1, ($objDb->getField($i, "total")==0?'':$objDb->getField($i, "total")));
                        $objPdf->Text(360, 255 + $iHeight1, ($objDb->getField($i, "good")==0?'':$objDb->getField($i, "good")));
                        $objPdf->Text(430, 255 + $iHeight1, ($objDb->getField($i, "rehabilitation")==0?'':$objDb->getField($i, "rehabilitation")));
                        $objPdf->Text(500, 255 + $iHeight1, ($objDb->getField($i, "dilapidated")==0?'':$objDb->getField($i, "dilapidated")));
                        $iHeight1 += 16.4;
                    }            
                    
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(500, 800, "Page 9 of {$iTotalPages}");
                    /////////////////////////////////////////Page #11///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(11, '/MediaBox');
                    $objPdf->addPage('');
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    
                    $objPdf->SetTextColor(128, 128, 128);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                    $objPdf->SetTextColor(14, 149, 69);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
            
                    $objPdf->SetTextColor(0, 0, 0);
                    $objPdf->SetFont('Arial', '', 8);    

                    $objPdf->Text(120, 160, $iAge['b3']);
                    $objPdf->Text(300, 160, $iStorey['b3']);
                    
                    $sBlock3 = explode(',', $sCM['b3']);
                    if(in_array('R', $sBlock3))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 75, 185, 10);
                    if(in_array('S', $sBlock3))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 345, 185, 10);                   
                    if(in_array('U', $sBlock3))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 75, 205, 10);
                    if(in_array('F', $sBlock3))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 345, 205, 10);
                        
                    $iHeight1 = 0;
                    for($i=20; $i<30; $i++){
                        $objPdf->Text(280, 255 + $iHeight1, ($objDb->getField($i, "total")==0?'':$objDb->getField($i, "total")));
                        $objPdf->Text(360, 255 + $iHeight1, ($objDb->getField($i, "good")==0?'':$objDb->getField($i, "good")));
                        $objPdf->Text(430, 255 + $iHeight1, ($objDb->getField($i, "rehabilitation")==0?'':$objDb->getField($i, "rehabilitation")));
                        $objPdf->Text(500, 255 + $iHeight1, ($objDb->getField($i, "dilapidated")==0?'':$objDb->getField($i, "dilapidated")));
                        $iHeight1 += 16.4;
                    }   

                    $objPdf->Text(120, 470, $iAge['b4']);
                    $objPdf->Text(300, 470, $iStorey['b4']);
                    
                    $sBlock4 = explode(',', $sCM['b4']);
                    if(in_array('R', $sBlock4))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 75, 495, 10);
                    if(in_array('S', $sBlock4))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 345, 495, 10);                   
                    if(in_array('U', $sBlock4))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 75, 515, 10);
                    if(in_array('F', $sBlock4))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 345, 515, 10);
                        
                    $iHeight1 = 307;
                    for($i=30; $i<40; $i++){
                        $objPdf->Text(280, 255 + $iHeight1, ($objDb->getField($i, "total")==0?'':$objDb->getField($i, "total")));
                        $objPdf->Text(360, 255 + $iHeight1, ($objDb->getField($i, "good")==0?'':$objDb->getField($i, "good")));
                        $objPdf->Text(430, 255 + $iHeight1, ($objDb->getField($i, "rehabilitation")==0?'':$objDb->getField($i, "rehabilitation")));
                        $objPdf->Text(500, 255 + $iHeight1, ($objDb->getField($i, "dilapidated")==0?'':$objDb->getField($i, "dilapidated")));
                        $iHeight1 += 16.4;
                    }        
                    
                    $sSQL = "SELECT * FROM tbl_survey_school_other_blocks WHERE survey_id='$iSurveyId'";
                    $objDb->query($sSQL);
                    $objPdf->Text(110, 420, 'Block #'.$objDb->getField(0, "other_block_1").' & '.$objDb->getField(0, "other_details_1"));
                    $objPdf->Text(110, 730, 'Block #'.$objDb->getField(0, "other_block_2").' & '.$objDb->getField(0, "other_details_2"));
                    $objPdf->SetXY(110, 745);
                    $objPdf->MultiCell(350, 10, $objDb->getField(0, "comments"));
                    
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(500, 800, "Page 10 of {$iTotalPages}");
                    /////////////////////////////////////////Page #12///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(12, '/MediaBox');
                    $objPdf->addPage('');
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    
                    $objPdf->SetTextColor(128, 128, 128);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                    $objPdf->SetTextColor(14, 149, 69);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
            
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8); 

                    $sSQL = "SELECT * FROM tbl_survey_school_facilities WHERE survey_id='$iSurveyId'";
                    $objDb->query($sSQL);
                    $iHeight2 = 8;

                    for($i=0; $i<6; $i++){
                            $objPdf->Text(220 , 170 + $iHeight2, ($objDb->getField($i, "total")==0?'':$objDb->getField($i, "total")));
                            $objPdf->Text(275 , 170 + $iHeight2, ($objDb->getField($i, "good")==0?'':$objDb->getField($i, "good")));
                            $objPdf->Text(335 , 170 + $iHeight2, ($objDb->getField($i, "rehabilitation")==0?'':$objDb->getField($i, "rehabilitation")));
                            $objPdf->Text(392 , 170 + $iHeight2, ($objDb->getField($i, "dilapidated")==0?'':$objDb->getField($i, "dilapidated")));

                            $objPdf->Text(445 , 170 + $iHeight2, ($objDb->getField($i, "material")=='0'?'':$objDb->getField($i, "material")));
                            $objPdf->Text(510 , 170 + $iHeight2, ($objDb->getField($i, "height")==0?'':$objDb->getField($i, "height")));
                            $iHeight2 += 16;
                    }

                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(500, 800, "Page 11 of {$iTotalPages}");
                    /////////////////////////////////////////Page #13///////////////////////////// 
                    $sSQL = "SELECT * FROM tbl_survey_checklist WHERE survey_id='$iSurveyId'";
                    $objDb->query($sSQL);

                    $iPageNumber = 12;
                    
                    if ($iCheckListPicsCount == 0 || @getimagesize(SITE_URL.SURVEYS_DOC_DIR.$objDb->getField(0, "site_plan"))){        
                        
                        $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                        $iTemplateId = $objPdf->importPage(13, '/MediaBox');
                        $objPdf->addPage('');
                        $objPdf->useTemplate($iTemplateId, 0, 0);
                        $objPdf->SetTextColor(0, 0, 0);
                        $objPdf->SetFont('Arial', '', 8);

                        $objPdf->RotatedText(545,740,$objDb->getField(0, "total_area"),270);
                        $objPdf->RotatedText(530, 740, $objDb->getField(0, "students"),270);

                        if ($objDb->getField(0, "north_arrow") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 510, 758, 10, 10, 270);

                        if ($objDb->getField(0, "class_rooms") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 495, 758, 10, 10, 270);

                        if ($objDb->getField(0, "class_room_size") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 482, 758, 10, 10, 270);

                        if ($objDb->getField(0, "toilets") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 468, 758, 10, 10, 270);

                        if ($objDb->getField(0, "playgrounds") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 454, 758, 10, 10, 270);

                        if ($objDb->getField(0, "water_tanks") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 440, 758, 10, 10, 270);

                        if ($objDb->getField(0, "spetic_tanks") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 425, 758, 10, 10, 270);

                        if ($objDb->getField(0, "boundary_wall") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 410, 758, 10, 10, 270);

                        if ($objDb->getField(0, "drainage") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 396, 758, 10, 10, 270);

                        if ($objDb->getField(0, "new_development") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 383, 758, 10, 10, 270);

                        if ($objDb->getField(0, "measurements") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 370, 758, 10, 10, 270);

                        if ($objDb->getField(0, "slopes") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 357, 758, 10, 10, 270);

                        if ($objDb->getField(0, "trees_smaller") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 342, 712, 10, 10, 270);

                        if ($objDb->getField(0, "trees_larger") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 342, 758, 10, 10, 270);

                        if ($objDb->getField(0, "electricity_1_phase") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 327, 712, 10, 10, 270);

                        if ($objDb->getField(0, "electricity_3_phase") == 'Y')
                            $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 327, 758, 10, 10, 270);

                        if ($objDb->getField(0, "site_plan") != '' && @getimagesize(SITE_URL.SURVEYS_DOC_DIR.$objDb->getField(0, "site_plan")))    
                            $objPdf->Image(SITE_URL.SURVEYS_DOC_DIR.$objDb->getField(0, "site_plan"), 30, 50, 537, 570);

                        $objPdf->SetFont('Arial', '', 6); 
                        $objPdf->RotatedText(105,685,($sAnswers[74]==''?$sSchool:$sAnswers[74]),270);
                        $objPdf->RotatedText(90,685,$sCode,270);
                        $objPdf->RotatedText(76,685,($sAnswers[76]==''?$sAddress:$sAnswers[76]),270);
                        $objPdf->RotatedText(62,685,($sAnswers[89]==''?$sLatitude:$sAnswers[89]).','.($sAnswers[90]==''?$sLongitude:$sAnswers[90]),270);
                        $objPdf->RotatedText(50,685,$sEnumerator,270);
                        $objPdf->RotatedText(35,685,$sDate,270);

                        $objPdf->SetFont('Arial', 'B', 10);
                        $objPdf->Text(500, 800, "Page {$iPageNumber} of {$iTotalPages}");
                        $iPageNumber++;
                    }
                    
                    $sSQL2 = "SELECT * FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='15'";
                    $objDb2->query($sSQL2);
                    $iCount2 = $objDb2->getCount( );
                    for($j=0; $j<$iCount2 ; $j++){
					
			if ($objDb2->getField($j, "picture") != '' && @getimagesize(SITE_URL.SURVEYS_DOC_DIR.$objDb2->getField($j, "picture"))){    
						
                            $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                            $iTemplateId = $objPdf->importPage(13, '/MediaBox');
                            $objPdf->addPage('');
                            $objPdf->useTemplate($iTemplateId, 0, 0);
                            $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8);

                            $objPdf->RotatedText(545,740,$objDb->getField(0, "total_area"),270);
                            $objPdf->RotatedText(530, 740, $objDb->getField(0, "students"),270);

                            if ($objDb->getField(0, "north_arrow") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 510, 758, 10, 10, 270);

                            if ($objDb->getField(0, "class_rooms") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 495, 758, 10, 10, 270);

                            if ($objDb->getField(0, "class_room_size") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 482, 758, 10, 10, 270);

                            if ($objDb->getField(0, "toilets") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 468, 758, 10, 10, 270);

                            if ($objDb->getField(0, "playgrounds") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 454, 758, 10, 10, 270);

                            if ($objDb->getField(0, "water_tanks") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 440, 758, 10, 10, 270);

                            if ($objDb->getField(0, "spetic_tanks") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 425, 758, 10, 10, 270);

                            if ($objDb->getField(0, "boundary_wall") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 410, 758, 10, 10, 270);

                            if ($objDb->getField(0, "drainage") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 396, 758, 10, 10, 270);

                            if ($objDb->getField(0, "new_development") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 383, 758, 10, 10, 270);

                            if ($objDb->getField(0, "measurements") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 370, 758, 10, 10, 270);

                            if ($objDb->getField(0, "slopes") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 357, 758, 10, 10, 270);

                            if ($objDb->getField(0, "trees_smaller") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 342, 712, 10, 10, 270);

                            if ($objDb->getField(0, "trees_larger") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 342, 758, 10, 10, 270);

                            if ($objDb->getField(0, "electricity_1_phase") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 327, 712, 10, 10, 270);

                            if ($objDb->getField(0, "electricity_3_phase") == 'Y')
                                $objPdf->RotatedImage(("{$sRootDir}images/icons/tick.gif"), 327, 758, 10, 10, 270);

                            $objPdf->Image(SITE_URL.SURVEYS_DOC_DIR.$objDb2->getField($j, "picture"), 30, 50, 537, 570);

                            $objPdf->SetFont('Arial', '', 6); 
                            $objPdf->RotatedText(105,685,($sAnswers[74]==''?$sSchool:$sAnswers[74]),270);
                            $objPdf->RotatedText(90,685,$sCode,270);
                            $objPdf->RotatedText(76,685,($sAnswers[76]==''?$sAddress:$sAnswers[76]),270);
                            $objPdf->RotatedText(62,685,($sAnswers[89]==''?$sLatitude:$sAnswers[89]).','.($sAnswers[90]==''?$sLongitude:$sAnswers[90]),270);
                            $objPdf->RotatedText(50,685,$sEnumerator,270);
                            $objPdf->RotatedText(35,685,$sDate,270);
                            
                            $objPdf->SetFont('Arial', 'B', 10);
                            $objPdf->Text(500, 800, "Page {$iPageNumber} of {$iTotalPages}");
                            $iPageNumber++;
                        }		
                    }
                    
                    /////////////////////////////////////////Page #14///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(14, '/MediaBox');
                    $objPdf->addPage( );
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    
                    $objPdf->SetTextColor(128, 128, 128);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                    $objPdf->SetTextColor(14, 149, 69);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
            
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', 'B', 10); 

                    $sSQL = "SELECT * FROM tbl_survey_declaration WHERE survey_id='$iSurveyId'";
                    $objDb->query($sSQL);

                    $objPdf->Text(240 , 155 , $sSchool);
                    $objPdf->Text(120 , 180 , @$sAnswers[84]);
                    $objPdf->Text(142 , 202 , $objDb->getField(0, "serving_date"));
                    $objPdf->Text(120 , 525 , $objDb->getField(0, "sign_date"));
                    $objPdf->Text(185, 590, $sEnumerator);
                    $objPdf->Text(120, 673, $sDate);
                    
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(500, 800, "Page {$iPageNumber} of {$iTotalPages}");
                    $iPageNumber++;
                    /////////////////////////////////////////Page #15///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(15, '/MediaBox');
                    $objPdf->addPage( );
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    
                    $objPdf->SetTextColor(128, 128, 128);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                    $objPdf->SetTextColor(14, 149, 69);
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);
            
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8); 

                    if ($sAnswers[64] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 515, 142, 10);
                    else if ($sAnswers[64] == 'N')
                        $objPdf->Text(515, 150, 'X');

                    if ($sAnswers[65] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 515, 165, 10);
                    else if ($sAnswers[65] == 'N')
                        $objPdf->Text(515, 175, 'X');

                    if ($sAnswers[66] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 515, 190, 10);
                    else if ($sAnswers[66] == 'N')
                        $objPdf->Text(515, 200, 'X');

                            $enumCheckArr =  array_map('trim',explode("\n", $sAnswers[67]));
                            if (in_array("Average Classroom", $enumCheckArr))  
                                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 515, 235, 10);
                            if (in_array("Toilet Block", $enumCheckArr))  
                                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 515, 262, 10);
                            if (in_array("General Layout", $enumCheckArr))  
                                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 515, 289, 10);
                            if (in_array("Topography", $enumCheckArr))  
                                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 515, 315, 10);

                    if ($sAnswers[68] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 515, 345, 10);
                    else if ($sAnswers[68] == 'N')
                        $objPdf->Text(515, 352, 'X');

                    if ($sAnswers[69] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 515, 367, 10);
                    else if ($sAnswers[69] == 'N')
                        $objPdf->Text(515, 377, 'X');

                    if ($sAnswers[70] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 515, 390, 10);
                    else if ($sAnswers[70] == 'N')
                        $objPdf->Text(515, 400, 'X');
                    
                    if ($sAnswers[71] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 457, 467, 10);
                    else if ($sAnswers[71] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 515, 467, 10);
                     
                            $objPdf->SetXY(180, 510);
                            $objPdf->MultiCell(350, 10, $sAnswers[72]);
                            $objPdf->Text(400, 730, $sDate);
                            $objPdf->Text(175, 730, $sEnumerator);
                    
                    $objPdf->SetFont('Arial', 'B', 10);
                    $objPdf->Text(500, 800, "Page {$iPageNumber} of {$iTotalPages}");
                    $iPageNumber++;
	        /////////////////////////////////////// Page#16 Survey Images /////////////////////////// 
                       $sSQL = "SELECT * FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id!='15'";
                       $objDb->query($sSQL);
                       $iCount = $objDb->getCount( );
                       $sSectionsList       = getList("tbl_survey_sections", "id", "name", "status='A'");
                       $sSectionTypeList   = getList("tbl_survey_sections", "id", "type", "status='A'");
                       $sQuestionsList      = getList("tbl_survey_questions", "id", "question", "status='A'", "section_id,position");
                       
                        if ($iCount > 0)
                        {
                                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                                $iTemplateId = $objPdf->importPage(16, '/MediaBox');


                                $iPages = @ceil($iCount / 4);
                                $iIndex = 0;

                                for ($i = 0; $i < $iPages; $i ++, $iPageNumber ++)
                                {
                                        $objPdf->addPage("P", "A4");
                                        $objPdf->useTemplate($iTemplateId, 0, 0);
                                        
                                        $objPdf->SetTextColor(128, 128, 128);
                                        $objPdf->SetFont('Arial', 'B', 10);
                                        $objPdf->Text(55, 93, 'Baseline and Technical Survey for '.$sType.' '.($sAnswers[74]==''?$sSchool:$sAnswers[74]));
                                        $objPdf->SetTextColor(14, 149, 69);
                                        $objPdf->SetFont('Arial', 'B', 10);
                                        $objPdf->Text(440, 93, 'EMIS CODE: '.$sCode);

                                        $objPdf->SetFont('Arial', '', 7);
                                        $objPdf->SetTextColor(50, 50, 50);

                                        for ($j = 0; $j < 4 && $iIndex < $iCount; $j ++, $iIndex ++)
                                        {
                                                $iSectionId   = $objDb->getField($iIndex, "section_id");
                                                $iQuestionId  = $objDb->getField($iIndex, "question_id");
                                             
                                                $iLeft = 60;
                                                $iTop  = 175;

                                                if ($j == 1 || $j == 3)
                                                        $iLeft = 310;

                                                if ($j == 2 || $j == 3)
                                                        $iTop = 467;

                                                if ($iSectionId == 0){
                                                    $sInfo = "Section: Pre-Selection Questions! \n";
                                                    if ($iQuestionId == 1)
                                                        $sInfo .= "Sub-Section: Is the school operational?\n";
                                                    else if ($iQuestionId == 2)
                                                        $sInfo .= "Sub-Section: Does the school have enough land for new construction?\n";
                                                    else if ($iQuestionId == 3)
                                                        $sInfo .= "Sub-Section: Is the school having any land dispute?\n";
                                                    else if ($iQuestionId == 4)
                                                        $sInfo .= "Sub-Section: Is the school involved in any other project providing funding for classroom infrastructure?\n";
                                                    else if ($iQuestionId == 5)
                                                        $sInfo .= "Sub-Section: How many classrooms does your school have? \n";
                                                    else if ($iQuestionId == 6)
                                                        $sInfo .= "Sub-Section: Are there any shelter-less grades being taught?\n";
                                                    else if ($iQuestionId == 7)
                                                        $sInfo .= "Sub-Section: Are there more than 2 grades being taught in one classroom (multi-grading)?\n";
                                                    else if ($iQuestionId == 8)
                                                        $sInfo .= "Sub-Section: What is the average attendance of school?\n";
                                                    else if ($iQuestionId == 9)
                                                        $sInfo .= "Sub-Section: Is the school part of the PEF (Punjab Education Foundation) Programme?\n";
                                                    
                                                }else{   
                                                    
                                                    $sInfo  = "Section: {$sSectionsList[$iSectionId]}\n";
                                                    
                                                    if ($sSectionTypeList[$iSectionId] == 'F'){
                                                        if ($iSectionId == 3){

                                                            if ($iQuestionId == 1)
                                                                $sInfo .= "Sub-Section: Teachers Sanctioned\n";
                                                            else if ($iQuestionId == 2)
                                                                $sInfo .= "Sub-Section: Teachers Filled\n";
                                                            else if ($iQuestionId == 3)
                                                                $sInfo .= "Sub-Section: Teachers Regularly Attending\n";
                                                            else if ($iQuestionId == 4)
                                                                $sInfo .= "Sub-Section: Support Staff Sanctioned\n";
                                                            else if ($iQuestionId == 5)
                                                                $sInfo .= "Sub-Section: Support Staff Filled\n";
                                                            else if ($iQuestionId == 6)
                                                                $sInfo .= "Sub-Section: Support Staff Regularly Attending\n";
                                                            else if ($iQuestionId == 7)
                                                                $sInfo .= "Sub-Section: Management Staff Sanctioned\n";
                                                            else if ($iQuestionId == 8)
                                                                $sInfo .= "Sub-Section: Management Staff Filled\n";
                                                            else if ($iQuestionId == 9)
                                                                $sInfo .= "Sub-Section: Management Staff Regularly Attending\n";
                                                            
                                                        }else if ($iSectionId == 4){
                                                            $sInfo .= "Sub-Section: Attendance Register\n";

                                                        }else if ($iSectionId == 5){
                                                            $sInfo .= "Sub-Section: Grade #{$iQuestionId}\n";

                                                        }else if ($iSectionId == 13){
                                                            $sInfo .= "Sub-Section: Block #{$iQuestionId}\n";

                                                        }else if ($iSectionId == 14){
                                                            if ($iQuestionId == 1)
                                                                $sInfo .= "Sub-Section: Play Ground for Girls\n";
                                                            else if ($iQuestionId == 2)
                                                                $sInfo .= "Sub-Section: Play Ground for Boys\n";
                                                            else if ($iQuestionId == 3)
                                                                $sInfo .= "Sub-Section: Play Ground for Both\n";
                                                            else if ($iQuestionId == 4)
                                                                $sInfo .= "Sub-Section: Boundry Wall\n";
                                                            else if ($iQuestionId == 5)
                                                                $sInfo .= "Sub-Section: Main Gate\n";
                                                            else if ($iQuestionId == 6)
                                                                $sInfo .= "Sub-Section: Retaining Wall\n";

                                                        }else if ($iSectionId == 16){
                                                            $sInfo .= "Sub-Section: Declaration Attachment\n";
                                                        }

                                                    }else
                                                        $sInfo .= "Question: {$sQuestionsList[$iQuestionId]}\n";
                                                }
                                                $objPdf->SetXY($iLeft, ($iTop + 230));
                                                $objPdf->MultiCell(220, 8, $sInfo, 0, "L", false);

                                                if ($objDb->getField($iIndex, "picture") != '' && @getimagesize(SITE_URL.SURVEYS_DOC_DIR.$objDb->getField($iIndex, "picture")))    
                                                    $objPdf->Image(SITE_URL.SURVEYS_DOC_DIR.$objDb->getField($iIndex, "picture"), $iLeft, $iTop, 220, 220);
                    
                                        }
                                    $objPdf->SetFont('Arial', 'B', 10);
                                    $objPdf->Text(500, 800, "Page {$iPageNumber} of {$iTotalPages}");
                                }
                        }
	
        } // main if ends
        
	
        ///////******** output PDF *********///////
        $objPdf->Output("BaseLineSurvey{$sCode}.pdf", "D");



	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>