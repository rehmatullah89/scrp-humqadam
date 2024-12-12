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

	$iSchool      = $objDb->getField(0, "school_id");
	$sEnumerator  = $objDb->getField(0, "enumerator");
	$sDate        = $objDb->getField(0, "date");
	$sComments    = $objDb->getField(0, "comments");
	
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
        
        //$iProvince = getDbValue("province_id", "tbl_districts", "id='$iDistrict'");
        /////////////////////////////////////////Page #1///////////////////////////// 
	$objPdf = new FPDI("P", "pt", "A4");
	$objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
	$iTemplateId = $objPdf->importPage(1, '/MediaBox');

	$objPdf->addPage( );
	$objPdf->useTemplate($iTemplateId, 0, 0);
	$objPdf->SetTextColor(0, 0, 0);
	$objPdf->SetFont('Arial', '', 8);

	
	//$objPdf->Text(120, 191.2, getDbValue("company", "tbl_contractors", "id='$iContractor'"));
	//$objPdf->Text(470, 116, formatNumber($iClassRooms, false));
	

	 /////////////////////////////////////////Page #2///////////////////////////// 
	$objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
	$iTemplateId = $objPdf->importPage(2, '/MediaBox');
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
        
        
        if($iProvince == 2)
            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 448, 110, 10);
        else if($iProvince == 1)        
            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 110, 10);
	
        if($iEduLevel == 'P')
            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 402, 145, 10);
        else if($iEduLevel == 'M')
            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 145, 10);
        else if($iEduLevel == 'H')
            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 402, 170, 10);
        else if($iEduLevel == 'S')
            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 170, 10);
        
        if($sDesignationType == 'M')    
            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 402, 215, 10);
        else if($sDesignationType == 'F')
            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 215, 10);
        else if($sDesignationType == 'DM')
            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 402, 235, 10);
        else if($sDesignationType == 'DF')
            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 235, 10);
        else if($sDesignationType == 'CO')
            $objPdf->Image(("{$sRootDir}images/icons/tick.gif"), 518, 280, 10);
        
        $objPdf->Text(157, 325, $sCode);        
        $objPdf->Text(157, 344, getDbValue("name", "tbl_districts", "id='$iDistrict'"));
        $objPdf->Text(157, 362, $sTehsil);        
        $objPdf->Text(157, 380, $sCity);
        $objPdf->Text(157, 398, $sMarkazUC);
        $objPdf->Text(157, 416, $sMoozaVillage);

        $objPdf->Text(402, 325, $sHeadTeacher);        
        $objPdf->Text(402, 344, $sPhone);
        $objPdf->Text(402, 362, $sHeadTeacherPhone);        
        $objPdf->Text(402, 380, $sEmail);
        $objPdf->Text(402, 398, $sElevation);
        $objPdf->Text(402, 416, $sLatitude);
        $objPdf->Text(402, 434, $sLongitude);
        
        /* -------- -------------------------------- Answers --------------------------- ---------  */
        $sAnswers    = getList("tbl_survey_answers", "question_id", "answer", "survey_id='{$iSurveyId}'");
        $sOthers     = getList("tbl_survey_answers", "question_id", "other", "survey_id='{$iSurveyId}'");
                                    /* -------------------------------- */
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
            
         /////////////////////////////////////////Page #3///////////////////////////// 
	$objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
	$iTemplateId = $objPdf->importPage(3, '/MediaBox');
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
            $objPdf->Text(210, 480+$heigt1, $objDb->getField(0+$j, "male_count")); 
            $objPdf->Text(255, 480+$heigt1, $objDb->getField(0+$j, "female_count"));
            $objPdf->Text(290, 480+$heigt1, $objDb->getField(0+$j, "both_count")); 

            $objPdf->Text(340, 480+$heigt1, $objDb->getField(1+$j, "male_count")); 
            $objPdf->Text(380, 480+$heigt1, $objDb->getField(1+$j, "female_count"));

            $objPdf->Text(420, 480+$heigt1, $objDb->getField(2+$j, "male_count")); 
            $objPdf->Text(460, 480+$heigt1, $objDb->getField(2+$j, "female_count"));
            $objPdf->Text(500, 480+$heigt1, $objDb->getField(2+$j, "both_count")); 
            
            $heigt1 += 20;
        }
        
        $sSQL = "SELECT * FROM tbl_survey_students_enrollment WHERE survey_id='$iSurveyId'";
	$objDb->query($sSQL);
        
        $objPdf->Text(135, 685, $objDb->getField(0, "male_count")); 
        $objPdf->Text(175, 685, $objDb->getField(0, "female_count"));
        $objPdf->Text(220, 685, $objDb->getField(0, "both_count")); 

        $objPdf->Text(270, 685, $objDb->getField(1, "male_count")); 
        $objPdf->Text(315, 685, $objDb->getField(1, "female_count"));
        $objPdf->Text(365, 685, $objDb->getField(1, "both_count")); 

        $objPdf->Text(410, 685, $objDb->getField(2, "male_count")); 
        $objPdf->Text(460, 685, $objDb->getField(2, "female_count"));
        $objPdf->Text(510, 685, $objDb->getField(2, "both_count")); 
        
         /////////////////////////////////////////Page #4///////////////////////////// 
	$objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
	$iTemplateId = $objPdf->importPage(4, '/MediaBox');
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
            $objPdf->Text(190, 209+$height2, $objDb->getField($i, "boys_count_morning")); 
            $objPdf->Text(270, 209+$height2, $objDb->getField($i, "girls_count_morning")); 
            $objPdf->Text(380, 209+$height2, $objDb->getField($i, "boys_count_evening")); 
            $objPdf->Text(480, 209+$height2, $objDb->getField($i, "girls_count_evening")); 
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
        $objPdf->Text(120, 492, $objDb->getField(0, "boys_count"));
        $objPdf->Text(220, 492, $objDb->getField(0, "boys_grades"));
        $objPdf->Text(350, 492, $objDb->getField(0, "girls_count"));
        $objPdf->Text(450, 492, $objDb->getField(0, "girls_grades"));
        
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
            
        /////////////////////////////////////////Page #5///////////////////////////// 
	$objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
	$iTemplateId = $objPdf->importPage(5, '/MediaBox');
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
        
        /////////////////////////////////////////Page #6///////////////////////////// 
	$objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
	$iTemplateId = $objPdf->importPage(6, '/MediaBox');
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
        
        /////////////////////////////////////////Page #7///////////////////////////// 
	$objPdf->setSourceFile("{$sRootDir}templates/baseline_survey.pdf");
	$iTemplateId = $objPdf->importPage(7, '/MediaBox');
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
            
            
        ///////******** output PDF *********///////
        $objPdf->Output("BaseLineSurveyNo{$iId}.pdf", "D");



	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>