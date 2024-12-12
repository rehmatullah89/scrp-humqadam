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
            $sMergedDenotified  = $objDb->getField(0, "merged_denotified");
            $sLandAvailable     = $objDb->getField(0, "land_available");
            $sLandDispute       = $objDb->getField(0, "land_dispute");
            $sOtherFunding      = $objDb->getField(0, "other_funding");
            $iClassRooms        = $objDb->getField(0, "class_rooms");
            $sEduPurpose        = $objDb->getField(0, "education_purpose");
            $sShelterLess       = $objDb->getField(0, "shelter_less");
            $sMultiGrading      = $objDb->getField(0, "multi_grading");
            $sAvgAttendance     = $objDb->getField(0, "avg_attendance");
            $sStatus            = $objDb->getField(0, "status");
            $sQualified         = $objDb->getField(0, "qualified");
            $sComments          = $objDb->getField(0, "comments");

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

            /////////////////////////////////////////Page #1///////////////////////////// 
            $objPdf = new FPDI("P", "pt", "A4");
            if($sQualified == 'Y')
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
            else 
                $objPdf->setSourceFile("{$sRootDir}templates/survey_questions.pdf");
                
            $iTemplateId = $objPdf->importPage(1, '/MediaBox');

            $objPdf->addPage( );
            $objPdf->useTemplate($iTemplateId, 0, 0);
            $objPdf->SetTextColor(0, 0, 0);
            $objPdf->SetFont('Arial', '', 8);

            /////////////////////////////////////////Page #2///////////////////////////// 
            if($sQualified == 'Y')
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
            else 
                $objPdf->setSourceFile("{$sRootDir}templates/survey_questions.pdf");
                
            $iTemplateId = $objPdf->importPage(2, '/MediaBox');
            $objPdf->addPage( );
            $objPdf->useTemplate($iTemplateId, 0, 0);
            $objPdf->SetTextColor(0, 0, 0);
            $objPdf->SetFont('Arial', '', 8);

            $objPdf->Text(205, 105, $sSchool);
            $objPdf->Text(205, 125, $sAddress);
            $objPdf->Text(205, 147, $sCode); 
            $objPdf->Text(205, 171, getDbValue("name", "tbl_districts", "id='$iDistrict'").' '.$sAnswers[73]);
            $objPdf->Text(205, 195, $sEnumerator);
            $objPdf->Text(205, 218, $sDate);

            
            if($sMergedDenotified == 'Y')
                 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 450, 260, 12);
            else
                 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 260, 12);

            if($sLandAvailable == 'Y'){
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 450, 300, 12);

                if($sLandDispute == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 450, 360, 12);
                   else{
                       $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 360, 12);
                        
                       if($sOtherFunding == 'Y')
                            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 450, 415, 12);
                       else{
                           $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 415, 12); 
                           
                           $objPdf->Text(420, 490, ($iClassRooms >0?$iClassRooms:''));

                            if($sEduPurpose == 'Y')
                                 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 450, 520, 12);
                            else
                                 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 520, 12);  

                            if($sShelterLess == 'Y')
                                 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 450, 570, 12);
                            else
                                 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 570, 12);       

                            if($sMultiGrading == 'Y')
                                 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 450, 618, 12);
                            else
                                 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 618, 12);

                            $objPdf->Text(420, 675, ($sAvgAttendance >0?$sAvgAttendance:''));
                       }
                            
                   }
          
            }                 
            else
                 $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 300, 12);

            $objPdf->SetXY(70, 710);
            $objPdf->MultiCell(100, 4, $sComments);

        ////////////////  ******** Main Survey Check ********* ////////////////////////
        if($sQualified == 'Y'){
                 /////////////////////////////////////////Page #3///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                $iTemplateId = $objPdf->importPage(3, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                $objPdf->SetTextColor(0, 0, 0);
                $objPdf->SetFont('Arial', '', 8);

                $objPdf->Text(160, 98, $sEnumerator);
                $objPdf->Text(400, 98, $sDate);

                $objPdf->SetXY(155, 150);
                $objPdf->MultiCell(100, 2, $sSchool);

                $objPdf->SetXY(155, 195);
                $objPdf->MultiCell(100, 2, $sAddress);

                $objPdf->Text(400, 98, $sDate);

                /* -------- -------------------------------- Answers --------------------------- ---------  */
                $sAnswers    = getList("tbl_survey_answers", "question_id", "answer", "survey_id='{$iSurveyId}'");
                $sOthers     = getList("tbl_survey_answers", "question_id", "other", "survey_id='{$iSurveyId}'");
                                            /* -------------------------------- */

                $provinceArr =  explode("\n", $sAnswers[73]);
                if(in_array('KP', $provinceArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 448, 110, 10);
                else if(in_array('Punjab', $provinceArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 110, 10);

                $schoolTypeArr =  explode("\n", $sAnswers[75]);
                if(in_array('Primary', $schoolTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 402, 145, 10);
                else if(in_array('Middle', $schoolTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 145, 10);
                else if(in_array('High School', $schoolTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 402, 170, 10);
                else if(in_array('Higher Secondary', $schoolTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 170, 10);

                $genderTypeArr =  explode("\n", $sAnswers[77]);
                if(in_array('Male', $genderTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 402, 215, 10);
                else if(in_array('Female', $genderTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 215, 10);
                else if(in_array('Designated Male, both attend', $genderTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 402, 235, 10);
                else if(in_array('Designated Female, both attend', $genderTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 235, 10);
                else if(in_array('Co-education', $genderTypeArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 280, 10);

                $objPdf->Text(157, 325, $sCode);        
                $objPdf->Text(157, 344, getDbValue("name", "tbl_districts", "id='$iDistrict'"));
                $objPdf->Text(157, 362, $sAnswers[80]);        
                $objPdf->Text(157, 380, $sAnswers[81]);
                $objPdf->Text(157, 398, $sAnswers[82]);
                $objPdf->Text(157, 416, $sAnswers[83]);

                $objPdf->Text(402, 325, $sAnswers[84]);        
                $objPdf->Text(402, 344, $sPhone);
                $objPdf->Text(402, 362, $sAnswers[86]);        
                $objPdf->Text(402, 380, $sEmail);
                $objPdf->Text(402, 398, $sAnswers[88]);
                $objPdf->Text(402, 416, $sLatitude);
                $objPdf->Text(402, 434, $sLongitude);

                $objPdf->Text(370, 509, $sAnswers[1]);

                if($sAnswers[2] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 441, 545, 10);
                else if($sAnswers[2] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 545, 10);

                $reasonArr =  explode("\n", $sAnswers[3]);

                if (in_array("No Teachers", $reasonArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 441, 587, 10);
                if(in_array("No Students", $reasonArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 587, 10);
                if(in_array("Insufficient Facilities or Infrastructure", $reasonArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 441, 610, 10);
                if(in_array("Inaccessible", $reasonArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 610, 10);
                if(in_array("Dispute", $reasonArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 441, 632, 10);
                if(in_array("Security", $reasonArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 632, 10);
                if(in_array("Merged", $reasonArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 441, 652, 10);
                if(in_array("De-Notified", $reasonArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 652, 10);
                if(in_array("Consolidated", $reasonArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 441, 675, 10);

                $objPdf->Text(363, 710, $sOthers[3]);    
                $objPdf->Text(467, 755, $sAnswers[4]);    

             /////////////////////////////////////////Page #4///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                $iTemplateId = $objPdf->importPage(4, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                $objPdf->SetTextColor(0, 0, 0);
                $objPdf->SetFont('Arial', '', 8);    

                $classStructArr =  explode("\n", $sAnswers[5]);
                if (in_array("Shelterless", $classStructArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 441, 110, 10);    
                if (in_array("School", $classStructArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 110, 10);    
                if (in_array("House", $classStructArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 441, 135, 10);    


                $classShiftArr =  explode("\n", $sAnswers[6]);
                if (in_array("Morning", $classShiftArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 441, 220, 10);    
                if (in_array("Evening", $classShiftArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 220, 10);    

                if($sAnswers[7] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 441, 265, 10);
                else if($sAnswers[7] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 265, 10);

                $objPdf->Text(467, 322, $sAnswers[8]);  

                $sSQL = "SELECT * FROM tbl_survey_teacher_numbers WHERE survey_id='$iSurveyId'";
                $objDb->query($sSQL);
                $heigt1 = 0;
                for($j=0; $j < 9; $j+=3){
                    $objPdf->Text(210, 480+$heigt1, ($objDb->getField(0+$j, "male_count") == 0?'':$objDb->getField(0+$j, "male_count"))); 
                    $objPdf->Text(255, 480+$heigt1, ($objDb->getField(0+$j, "female_count") == 0?'':$objDb->getField(0+$j, "female_count")));
                    $objPdf->Text(290, 480+$heigt1, ($objDb->getField(0+$j, "both_count") == 0?'':$objDb->getField(0+$j, "both_count"))); 

                    $objPdf->Text(340, 480+$heigt1, ($objDb->getField(1+$j, "male_count")==0?'':$objDb->getField(1+$j, "male_count"))); 
                    $objPdf->Text(380, 480+$heigt1, ($objDb->getField(1+$j, "female_count")==0?'':$objDb->getField(1+$j, "female_count")));

                    $objPdf->Text(420, 480+$heigt1, ($objDb->getField(2+$j, "male_count")==0?'':$objDb->getField(2+$j, "male_count"))); 
                    $objPdf->Text(460, 480+$heigt1, ($objDb->getField(2+$j, "female_count")==0?'':$objDb->getField(2+$j, "female_count")));
                    $objPdf->Text(500, 480+$heigt1, ($objDb->getField(2+$j, "both_count")==0?'':$objDb->getField(2+$j, "both_count"))); 

                    $heigt1 += 20;
                }

                $sSQL = "SELECT * FROM tbl_survey_students_enrollment WHERE survey_id='$iSurveyId'";
                $objDb->query($sSQL);

                $objPdf->Text(135, 685, ($objDb->getField(0, "male_count")==0?'':$objDb->getField(0, "male_count"))); 
                $objPdf->Text(175, 685, ($objDb->getField(0, "female_count")==0?'':$objDb->getField(0, "female_count")));
                $objPdf->Text(220, 685, ($objDb->getField(0, "both_count")==0?'':$objDb->getField(0, "both_count"))); 

                $objPdf->Text(270, 685, ($objDb->getField(1, "male_count")==0?'':$objDb->getField(1, "male_count"))); 
                $objPdf->Text(315, 685, ($objDb->getField(1, "female_count")==0?'':$objDb->getField(1, "female_count")));
                $objPdf->Text(365, 685, ($objDb->getField(1, "both_count")==0?'':$objDb->getField(1, "both_count"))); 

                $objPdf->Text(410, 685, ($objDb->getField(2, "male_count")==0?'':$objDb->getField(2, "male_count")));
                $objPdf->Text(460, 685, ($objDb->getField(2, "female_count")==0?'':$objDb->getField(2, "female_count")));
                $objPdf->Text(510, 685, ($objDb->getField(2, "both_count")==0?'':$objDb->getField(2, "both_count"))); 

             /////////////////////////////////////////Page #5///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                $iTemplateId = $objPdf->importPage(5, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                $objPdf->SetTextColor(0, 0, 0);
                $objPdf->SetFont('Arial', '', 8);    

                $sSQL = "SELECT * FROM tbl_survey_student_attendance_numbers WHERE survey_id='$iSurveyId'";
                $objDb->query($sSQL);
                $height2 = 0;
                $bMorning = 0;
                $gMorning = 0;
                $bEvening = 0;
                $gEvening = 0;
                for($i=0; $i<12; $i++){ 
                    $objPdf->Text(190, 209+$height2, ($objDb->getField($i, "boys_count_morning")==0?'':$objDb->getField($i, "boys_count_morning"))); 
                    $objPdf->Text(270, 209+$height2, ($objDb->getField($i, "girls_count_morning")==0?'':$objDb->getField($i, "girls_count_morning"))); 
                    $objPdf->Text(380, 209+$height2, ($objDb->getField($i, "boys_count_evening")==0?'':$objDb->getField($i, "boys_count_evening"))); 
                    $objPdf->Text(480, 209+$height2, ($objDb->getField($i, "girls_count_evening")==0?'':$objDb->getField($i, "girls_count_evening"))); 
                    $height2 += 17.5;
                    $bMorning +=  $objDb->getField($i, "boys_count_morning");
                    $gMorning +=  $objDb->getField($i, "girls_count_morning");
                    $bEvening +=  $objDb->getField($i, "boys_count_evening");
                    $gEvening +=  $objDb->getField($i, "girls_count_evening");
                }
                $objPdf->Text(190, 420, $bMorning); 
                $objPdf->Text(270, 420, $gMorning); 
                $objPdf->Text(380, 420, $bEvening); 
                $objPdf->Text(480, 420, $gEvening); 

                $sSQL = "SELECT * FROM tbl_survey_differently_abled_student_numbers WHERE survey_id='$iSurveyId'";
                $objDb->query($sSQL);
                $objPdf->Text(120, 492, ($objDb->getField(0, "boys_count")==0?'':$objDb->getField(0, "boys_count")));
                $objPdf->Text(220, 492, ($objDb->getField(0, "boys_grades")==0?'':$objDb->getField(0, "boys_grades")));
                $objPdf->Text(350, 492, ($objDb->getField(0, "girls_count")==0?'':$objDb->getField(0, "girls_count")));
                $objPdf->Text(450, 492, ($objDb->getField(0, "girls_grades")==0?'':$objDb->getField(0, "girls_grades")));

            if($sAnswers[9] == 'Y')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 462, 540, 10);
            else if($sAnswers[9] == 'N')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 540, 10);

                $objPdf->Text(340, 595, $sAnswers[10]);

            if($sAnswers[11] == 'Y')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 462, 630, 10);
            else if($sAnswers[11] == 'N')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 630, 10);

                $objPdf->Text(455, 680, $sAnswers[12]);

            if($sAnswers[13] == 'Y')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 462, 720, 10);
            else if($sAnswers[13] == 'N')
                $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 720, 10);

            /////////////////////////////////////////Page #6///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                $iTemplateId = $objPdf->importPage(6, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                $objPdf->SetTextColor(0, 0, 0);
                $objPdf->SetFont('Arial', '', 8);    

                $objPdf->SetXY(70, 110);
                $objPdf->MultiCell(420, 10, $sAnswers[14]);

                $landStatusArr =  explode("\n", $sAnswers[15]);
                if (in_array("Community Land", $landStatusArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 355, 185, 10);    
                if (in_array("Rented", $landStatusArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 185, 10);    
                if (in_array("Owned by Government", $landStatusArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 355, 210, 10); 
                if (in_array("Land Mutated to Government", $landStatusArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 210, 10);     
                $objPdf->Text(405, 260, $sOthers[15]);    

                if($sAnswers[16] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 477, 300, 10);
                else if($sAnswers[16] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 300, 10);

                if($sAnswers[18] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 435, 370, 10);
                if($sAnswers[19] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 435, 340, 10);
                else if($sAnswers[19] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 340, 10);
                 $objPdf->Text(463, 390, $sAnswers[20]);    

                if($sAnswers[21] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 462, 430, 10);
                else if($sAnswers[21] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 430, 10);
                else 
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 462, 450, 10);

                if($sAnswers[22] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 457, 555, 10);
                else if($sAnswers[22] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 555, 10);    

                $schoolWaterArr =  explode("\n", $sAnswers[23]);
                if (in_array(htmlentities("Open/ Covered Well"), $landStatusArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 377, 600, 10);    
                if (in_array("Public Supply", $schoolWaterArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 600, 10);    
                if (in_array("Shallow borehole hand pump", $schoolWaterArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 377, 627, 10); 
                if (in_array("Borehole with motor pump", $schoolWaterArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 627, 10);   
                if (in_array("Deep borehole hand pump", $schoolWaterArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 377, 650, 10); 
                if (in_array("", $schoolWaterArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 650, 10); 
                $objPdf->Text(387, 700, $sOthers[23]);

                if($sAnswers[24] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 457, 735, 10);
                else if($sAnswers[24] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 735, 10);

            /////////////////////////////////////////Page #7///////////////////////////// 
                $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                $iTemplateId = $objPdf->importPage(7, '/MediaBox');
                $objPdf->addPage( );
                $objPdf->useTemplate($iTemplateId, 0, 0);
                $objPdf->SetTextColor(0, 0, 0);
                $objPdf->SetFont('Arial', '', 8);    

                if($sAnswers[25] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 457, 70, 10);
                else if($sAnswers[25] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 70, 10);

                $storageArr =  explode("\n", $sAnswers[26]);
                if (in_array(htmlentities("Overhead Concrete Tank"), $storageArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 377, 115, 10);    
                if (in_array("Ground Storage Tank", $storageArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 115, 10);    
                if (in_array("Overhead Brick Masonry Tank", $storageArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 377, 135, 10); 
                if (in_array("Fiberglass", $storageArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 135, 10);   
                $objPdf->Text(222, 190, $sOthers[26]);    
                $objPdf->Text(477, 190, $sAnswers[27]);    

                if($sAnswers[28] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 457, 222, 10);
                else if($sAnswers[28] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 222, 10);

                $schoolDrainArr =  explode("\n", $sAnswers[29]);
                if (in_array(htmlentities("Open Drain"), $schoolDrainArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 395, 290, 10);    
                if (in_array("No Drainage System", $schoolDrainArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 290, 10);    
                if (in_array("Connected to Municipality Line", $schoolDrainArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 395, 313, 10); 
                if (in_array("Open Field", $schoolDrainArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 313, 10);
                if (in_array("Culverts", $schoolDrainArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 395, 340, 10);
                $objPdf->Text(415, 362, $sOthers[29]);

                if($sAnswers[30] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 460, 452, 10);
                else if($sAnswers[30] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 452, 10);   

                if($sAnswers[31] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 460, 490, 10);
                else if($sAnswers[31] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 490, 10);   

                if($sAnswers[32] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 460, 537, 10);
                else if($sAnswers[32] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 537, 10);

                $objPdf->Text(430, 587, $sAnswers[33]);

                if($sAnswers[34] == 'Y')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 460, 625, 10);
                else if($sAnswers[34] == 'N')
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 625, 10);

                $schoolDisposalArr =  explode("\n", $sAnswers[35]);
                if (in_array(htmlentities("Septic Tank"), $schoolDisposalArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 670, 10);    
                if (in_array("Connected to Sewer", $schoolDisposalArr))
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 670, 10);    
                if (in_array("Dry Pit", $schoolDisposalArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 700, 10); 
                if (in_array("Leach pit", $schoolDisposalArr))    
                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 700, 10);
                $objPdf->Text(230, 745, $sOthers[35]);     
                $objPdf->Text(480, 745, $sAnswers[36]);    

            /////////////////////////////////////////Page #8///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(8, '/MediaBox');
                    $objPdf->addPage( );
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8);    

                    if($sAnswers[37] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 461, 87, 10);
                    else if($sAnswers[37] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 87, 10);

                    if($sAnswers[38] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 461, 134, 10);
                    else if($sAnswers[38] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 134, 10);

                    if($sAnswers[39] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 461, 240, 10);
                    else if($sAnswers[39] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 240, 10);    

                    $objPdf->Text(480, 290, $sAnswers[40]);

                    if($sAnswers[41] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 461, 330, 10);
                    else if($sAnswers[41] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 330, 10);    

                    if($sAnswers[42] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 461, 430, 10);
                    else if($sAnswers[42] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 430, 10); 

                    if($sAnswers[43] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 461, 475, 10);
                    else if($sAnswers[43] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 475, 10); 

                    if($sAnswers[44] == 'Urban')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 582, 10);
                    else if($sAnswers[44] == 'Rural')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 582, 10); 

                    $schoolHazardArr =  explode("\n", $sAnswers[45]);
                    if (in_array(htmlentities("Waste Water Dump"), $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 620, 10);    
                    if (in_array("Dirty Water Pond", $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 620, 10);    

                    if (in_array(htmlentities("Power Lines/ Poles"), $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 642, 10);    
                    if (in_array("Graveyard", $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 642, 10);    

                            if (in_array(htmlentities("Uncovered Open Well"), $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 670, 10);    
                    if (in_array("Located in Waterway", $schoolHazardArr))
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 670, 10);   

                    if (in_array("Water Logged Areas", $schoolHazardArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 695, 10); 
                    if (in_array("Landslides", $schoolHazardArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 695, 10);

                    if (in_array("Floods", $schoolHazardArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 720, 10); 
                    if (in_array("Open Waste Pits", $schoolHazardArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 720, 10);
                            if (in_array("Creek/ River through School", $schoolHazardArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 743, 10);
                    $objPdf->Text(390, 768, $sOthers[45]);

                    /////////////////////////////////////////Page #9///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(9, '/MediaBox');
                    $objPdf->addPage( );
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8);    

                    $objPdf->Text(292, 100, $sAnswers[46]);  
                    $objPdf->Text(475, 100, $sAnswers[47]);  
                    $objPdf->Text(475, 140, $sAnswers[48]); 

                    $schoolSurrondArr =  explode("\n", $sAnswers[49]);
                    if (in_array("Hilly", $schoolSurrondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 180, 10); 
                    if (in_array("Plain", $schoolSurrondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 180, 10);
                    if (in_array("Undulating", $schoolSurrondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 205, 10);

                            $schoolSoilArr =  explode("\n", $sAnswers[50]);
                    if (in_array("Built up area", $schoolSoilArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 245, 10); 
                    if (in_array("Fields", $schoolSoilArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 245, 10);
                    if (in_array("Vegetation", $schoolSoilArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 270, 10);
                            $objPdf->Text(390, 300, $sOthers[50]);

                            $schoolSoilCondArr =  explode("\n", $sAnswers[51]);
                    if (in_array("Sandy", $schoolSoilCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 360, 10); 
                    if (in_array("Clay", $schoolSoilCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 360, 10);
                    if (in_array("Shingle", $schoolSoilCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 372, 385, 10);
                    if (in_array("Fill", $schoolSoilCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 385, 10);

                    $objPdf->Text(389, 430, $sAnswers[52]);

                    $schoolFoundationArr =  explode("\n", $sAnswers[53]);
                    if (in_array("Isolated footing", $schoolFoundationArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 368, 465, 10); 
                    if (in_array("Step-brick", $schoolFoundationArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 465, 10);
                    if (in_array("Stone", $schoolFoundationArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 368, 490, 10);
                    if (in_array("Strip footings", $schoolFoundationArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 490, 10);
                    $objPdf->Text(388, 545, $sOthers[53]);

                    $objPdf->Text(482, 590, $sAnswers[54]);
                    $objPdf->Text(352, 653, $sAnswers[55]);				
                    $objPdf->Text(507, 653, $sAnswers[56]);						

                    /////////////////////////////////////////Page #10///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(10, '/MediaBox');
                    $objPdf->addPage( );
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8);    

                    $natureExpectedArr =  explode("\n", $sAnswers[57]);
                    if (in_array("Non-obstructed/ plain", $natureExpectedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 122, 10); 
                    else if (in_array("Obstructed", $natureExpectedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 122, 10);
                            else if (in_array("Narrow", $natureExpectedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 145, 10);
                            else if (in_array("Steep", $natureExpectedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 145, 10);
                            else if (in_array("Sharp turns", $natureExpectedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 170, 10);
                            $objPdf->Text(384, 200, $sOthers[57]);

                            $constrSuppliedArr =  explode("\n", $sAnswers[58]);
                    if (in_array("Heavy Vehicle", $constrSuppliedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 263, 10); 
                    if (in_array("Tractor", $constrSuppliedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 263, 10);
                    if (in_array("Pick-up Truck", $constrSuppliedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 282, 10);
                    if (in_array("Donkey/ livestock", $constrSuppliedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 282, 10);
                    if (in_array("Car", $constrSuppliedArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 303, 10);
                            $objPdf->Text(391, 340, $sOthers[58]);

                            $roadCondArr =  explode("\n", $sAnswers[59]);
                    if (in_array("Sealed", $roadCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 390, 10); 
                    else if (in_array("Stone Soled", $roadCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 390, 10);
                    else if (in_array("Kacha", $roadCondArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 415, 10);
                            $objPdf->Text(393, 442, $sOthers[59]);

                            $roadDistArr =  explode("\n", $sAnswers[60]);
                            if (in_array("0-2km", $roadDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 500, 10); 
                    else if (in_array("2-10km", $roadDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 500, 10);
                            else if (in_array("10-30km", $roadDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 525, 10);
                            else if (in_array(">30km", $roadDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 525, 10);


                            $townDistArr =  explode("\n", $sAnswers[61]);
                            if (in_array("0-2km", $townDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 565, 10); 
                    else if (in_array("2-10km", $townDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 565, 10);
                            else if (in_array("10-30km", $townDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 371, 590, 10);
                            else if (in_array(">30km", $townDistArr))    
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 520, 590, 10);


                            if($sAnswers[62] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 460, 635, 10);
                    else if($sAnswers[62] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 521, 635, 10);   	

                    /////////////////////////////////////////Page #11///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(11, '/MediaBox');
                    $objPdf->addPage('L');
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8);    

                    $iAge    = getList("tbl_survey_school_blocks", "block", "age", "survey_id='{$iSurveyId}'");
                    $iStorey = getList("tbl_survey_school_blocks", "block", "storeys", "survey_id='{$iSurveyId}'");
                    $sCM     = getList("tbl_survey_school_blocks", "block", "cm", "survey_id='{$iSurveyId}'");


                    $objPdf->Text(270, 226, $iAge['b1']);
                    $objPdf->Text(360, 226, $iAge['b2']);
                    $objPdf->Text(455, 226, $iAge['b3']);
                    $objPdf->Text(555, 226, $iAge['b4']);
                    $objPdf->Text(655, 226, $iAge['b5']);

                    $objPdf->Text(285, 242, $iStorey['b1']);
                    $objPdf->Text(380, 242, $iStorey['b2']);
                    $objPdf->Text(465, 242, $iStorey['b3']);
                    $objPdf->Text(565, 242, $iStorey['b4']);
                    $objPdf->Text(665, 242, $iStorey['b5']);

                    $objPdf->Text(250, 270, $sCM['b1']);
                    $objPdf->Text(350, 270, $sCM['b2']);
                    $objPdf->Text(450, 270, $sCM['b3']);
                    $objPdf->Text(550, 270, $sCM['b4']);
                    $objPdf->Text(650, 270, $sCM['b5']);


                    $sSQL = "SELECT * FROM tbl_survey_school_other_blocks WHERE survey_id='$iSurveyId'";
                    $objDb->query($sSQL);
                    $objPdf->Text(170, 492, $objDb->getField(0, "other_block_1"));
                    $objPdf->Text(192, 492, $objDb->getField(0, "other_details_1"));

                    $objPdf->Text(325, 492, $objDb->getField(0, "other_block_2"));
                    $objPdf->Text(350, 492, $objDb->getField(0, "other_details_2"));

                    $objPdf->Text(485, 492, $objDb->getField(0, "other_block_3"));
                    $objPdf->Text(510, 492, $objDb->getField(0, "other_details_3"));
                    $objPdf->Text(125, 515, $objDb->getField(0, "comments"));

                    $sSQL = "SELECT * FROM tbl_survey_school_block_details WHERE survey_id='$iSurveyId'";
                    $objDb->query($sSQL);
                    $iHeight1 = 0;
                    for($j = 0; $j<100 ; $j+=10){
                            $iWidth1 = 25;
                            $k=1;
                            for($i=$j; $i<5+$j; $i++){
                                    if($i > $j)
                                            $iWidth1 += 25;
                                    $objPdf->Text(220 + $iWidth1, 305 + $iHeight1, ($objDb->getField($i, "total")==0?'':$objDb->getField($i, "total")));
                                    $objPdf->Text(240 + $iWidth1, 305 + $iHeight1, ($objDb->getField($i, "good")==0?'':$objDb->getField($i, "good")));
                                    $objPdf->Text(265 + $iWidth1, 305 + $iHeight1, ($objDb->getField($i, "rehabilitation")==0?'':$objDb->getField($i, "rehabilitation")));
                                    $objPdf->Text(290 + $iWidth1, 305 + $iHeight1, ($objDb->getField($i, "dilapidated")==0?'':$objDb->getField($i, "dilapidated")));
                                    $iWidth1 =  93 * $k;
                                    $k++;
                            }
                            $iHeight1 += 16.5;
                    }

                    /////////////////////////////////////////Page #12///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(12, '/MediaBox');
                    $objPdf->addPage('L');
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8);    

                    $objPdf->Text(270, 226, $iAge['b6']);
                    $objPdf->Text(360, 226, $iAge['b7']);
                    $objPdf->Text(455, 226, $iAge['b8']);
                    $objPdf->Text(555, 226, $iAge['b9']);
                    $objPdf->Text(655, 226, $iAge['b10']);

                    $objPdf->Text(285, 242, $iStorey['b6']);
                    $objPdf->Text(380, 242, $iStorey['b7']);
                    $objPdf->Text(465, 242, $iStorey['b8']);
                    $objPdf->Text(565, 242, $iStorey['b9']);
                    $objPdf->Text(665, 242, $iStorey['b10']);

                    $objPdf->Text(250, 270, $sCM['b6']);
                    $objPdf->Text(350, 270, $sCM['b7']);
                    $objPdf->Text(450, 270, $sCM['b8']);
                    $objPdf->Text(550, 270, $sCM['b9']);
                    $objPdf->Text(650, 270, $sCM['b10']);


                    $iHeight1 = 0;
                    for($j = 5; $j<100 ; $j+=10){
                            $iWidth1 = 25;
                            $k=1;
                            for($i=$j; $i<5+$j; $i++){
                                    if($i > $j)
                                            $iWidth1 += 25;
                                    $objPdf->Text(220 + $iWidth1, 305 + $iHeight1, ($objDb->getField($i, "total")==0?'':$objDb->getField($i, "total")));
                                    $objPdf->Text(240 + $iWidth1, 305 + $iHeight1, ($objDb->getField($i, "good")==0?'':$objDb->getField($i, "good")));
                                    $objPdf->Text(265 + $iWidth1, 305 + $iHeight1, ($objDb->getField($i, "rehabilitation")==0?'':$objDb->getField($i, "rehabilitation")));
                                    $objPdf->Text(290 + $iWidth1, 305 + $iHeight1, ($objDb->getField($i, "dilapidated")==0?'':$objDb->getField($i, "dilapidated")));
                                    $iWidth1 =  93 * $k;
                                    $k++;
                            }
                            $iHeight1 += 16.5;
                    }

                    /////////////////////////////////////////Page #13///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(13, '/MediaBox');
                    $objPdf->addPage('L');
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8); 

                    $sSQL = "SELECT * FROM tbl_survey_school_facilities WHERE survey_id='$iSurveyId'";
                    $objDb->query($sSQL);
                    $iHeight2 = 50;

                    for($i=0; $i<5; $i++){
                            $objPdf->Text(225 , 250 + $iHeight2, ($objDb->getField($i, "total")==0?'':$objDb->getField($i, "total")));
                            $objPdf->Text(250 , 250 + $iHeight2, ($objDb->getField($i, "good")==0?'':$objDb->getField($i, "good")));
                            $objPdf->Text(275 , 250 + $iHeight2, ($objDb->getField($i, "rehabilitation")==0?'':$objDb->getField($i, "rehabilitation")));
                            $objPdf->Text(300 , 250 + $iHeight2, ($objDb->getField($i, "dilapidated")==0?'':$objDb->getField($i, "dilapidated")));

                            $objPdf->Text(325 , 250 + $iHeight2, ($objDb->getField($i, "material")==0?'':$objDb->getField($i, "material")));
                            $objPdf->Text(430 , 250 + $iHeight2, ($objDb->getField($i, "height")==0?'':$objDb->getField($i, "height")));
                            $iHeight2 += 16;
                    }

                    /////////////////////////////////////////Page #14///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(14, '/MediaBox');
                    $objPdf->addPage('L');
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8); 

                    /////////////////////////////////////////Page #15///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(15, '/MediaBox');
                    $objPdf->addPage('L');
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8); 

                    /////////////////////////////////////////Page #16///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(16, '/MediaBox');
                    $objPdf->addPage( );
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', 'B', 10); 

                    $sSQL = "SELECT * FROM tbl_survey_declaration WHERE survey_id='$iSurveyId'";
                    $objDb->query($sSQL);

                    $objPdf->Text(250 , 138 , $objDb->getField(0, "establishment"));
                    $objPdf->Text(120 , 160 , $objDb->getField(0, "head"));
                    $objPdf->Text(142 , 185 , $objDb->getField(0, "date"));

                    /////////////////////////////////////////Page #17///////////////////////////// 
                    $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                    $iTemplateId = $objPdf->importPage(17, '/MediaBox');
                    $objPdf->addPage( );
                    $objPdf->useTemplate($iTemplateId, 0, 0);
                    $objPdf->SetTextColor(0, 0, 0);
                            $objPdf->SetFont('Arial', '', 8); 

                    if($sAnswers[64] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 120, 10);
                    else if($sAnswers[64] == 'N')
                        $objPdf->Text(527, 120, 'X');

                    if($sAnswers[65] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 160, 10);
                    else if($sAnswers[65] == 'N')
                        $objPdf->Text(527, 160, 'X');

                    if($sAnswers[66] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 205, 10);
                    else if($sAnswers[66] == 'N')
                        $objPdf->Text(527, 205, 'X');

                    if($sAnswers[67] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 205, 10);
                    else if($sAnswers[67] == 'N')
                        $objPdf->Text(527, 205, 'X');

                            $enumCheckArr =  explode("\n", $sAnswers[58]);
                            if (in_array("Average Classroom", $enumCheckArr))  
                                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 270, 10);
                            if (in_array("Toilet Block", $enumCheckArr))  
                                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 302, 10);
                            if (in_array("General Layout", $enumCheckArr))  
                                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 325, 10);
                            if (in_array("Topography", $enumCheckArr))  
                                    $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 350, 10);

                    if($sAnswers[68] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 385, 10);
                    else if($sAnswers[68] == 'N')
                        $objPdf->Text(527, 390, 'X');

                    if($sAnswers[69] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 428, 10);
                    else if($sAnswers[69] == 'N')
                        $objPdf->Text(527, 433, 'X');

                            if($sAnswers[70] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 472, 10);
                    else if($sAnswers[70] == 'N')
                        $objPdf->Text(527, 477, 'X');

                            /////////////////////////////////////////Page #18///////////////////////////// 
                            $objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
                            $iTemplateId = $objPdf->importPage(18, '/MediaBox');
                            $objPdf->addPage( );
                            $objPdf->useTemplate($iTemplateId, 0, 0);
                            $objPdf->SetTextColor(0, 0, 0);
                                    $objPdf->SetFont('Arial', '', 8); 

                    if($sAnswers[71] == 'N')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 527, 100, 10);
                    else if($sAnswers[71] == 'Y')
                        $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 455, 100, 10);

                            $objPdf->SetXY(75, 200);
                            $objPdf->MultiCell(120, 4, $sAnswers[72]);
	
        } // main if ends
        
	
        ///////******** output PDF *********///////
        $objPdf->Output("BaseLineSurveyNo{$iId}.pdf", "D");



	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>