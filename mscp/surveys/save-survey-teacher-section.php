<?
	$sSQL = "DELETE FROM tbl_survey_teacher_numbers WHERE survey_id='$iSurveyId'";
	$bFlag = $objDb->execute($sSQL);
          
	if ($bFlag == true)
	{
		for($i=1; $i<=9; $i++)
		{
			$sSQL = "INSERT INTO tbl_survey_teacher_numbers SET survey_id='$iSurveyId', ";
			
			$iNextId = getNextId("tbl_survey_teacher_numbers");
			$iMaleStaffTypeCount = IO::intValue("txtMaleStaffTypeCount".$i);
			$iFemaleStaffTypeCount = IO::intValue("txtFemaleStaffTypeCount".$i);
			$iBothStaffTypeCount = IO::intValue("txtBothStaffTypeCount".$i);
			
			if($i % 3 == 1){
				$sAttendanceType = 'S';
			}else if($i % 3 == 2){
				$sAttendanceType = 'F';
			}else if($i % 3 == 0){
				$sAttendanceType = 'R';
			}
			
			if($i<=3)
				$sStaffType = 'T';
			else if($i > 3 && $i<= 6)
				$sStaffType = 'SS';
			else if($i > 6 && $i<= 9)
				$sStaffType = 'MS';
			
	
			$sSQL .= ("id   = '".$iNextId."',
						male_count                 = '".$iMaleStaffTypeCount."',
						female_count                 = '".$iFemaleStaffTypeCount."',
						both_count                = '".$iBothStaffTypeCount."',
						staff_type                  = '".$sStaffType."',
						attendance_type              = '".$sAttendanceType."'");
			$bFlag = $objDb->execute($sSQL);
			
			if($bFlag == false)
				break;
		}
                
                if ($bFlag == true && IO::intValue("ddFixedSection") > 0)
		{
                    
			if ($_FILES["fileFixedSection"]['name'] != "")
			{
                                $iQuestion = IO::intValue("ddFixedSection");
                                $time = strtotime(date('Y-m-d h:i:s'));
                                $exts = explode('.', $_FILES['fileFixedSection']['name']);
                                $extension = end($exts);
                                $sPicture = ($iSurveyId."-S".$iSectionId."-Q".$iQuestion.'-'.$time.'.'.$extension);
                                
				if (@move_uploaded_file($_FILES["fileFixedSection"]['tmp_name'], ($sRootDir.SURVEYS_DOC_DIR.$sPicture)))
				{
                                    
                                    	$iPicture = getNextId("tbl_survey_pictures");
					
					$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																  survey_id   = '$iSurveyId',
																  section_id  = '$iSectionId',
																  question_id = '$iQuestion',
																  picture     = '$sPicture'";
					$bFlag = $objDb->execute($sSQL);
				}
			}
		}	
	}
?>