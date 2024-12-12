<?
	$sSQL = "DELETE FROM tbl_survey_student_attendance_numbers WHERE survey_id='$iSurveyId'";
	$bFlag = $objDb->execute($sSQL);
          
	if ($bFlag == true)
	{
		for($i=0; $i<=12; $i++)
		{
			$sSQL = "INSERT INTO tbl_survey_student_attendance_numbers SET survey_id='$iSurveyId', ";
				
			$iNextId = getNextId("tbl_survey_student_attendance_numbers");
			$iBoysCountMorning  = IO::intValue("txtBoysCountMorning".$i);
			$iGirlsCountMorning = IO::intValue("txtGirlsCountMorning".$i);
			$iBoysCountEvening  = IO::intValue("txtBoysCountEvening".$i);
			$iGirlsCountEvening = IO::intValue("txtGirlsCountEvening".$i);
				
		   
			$sSQL .= ("id   = '".$iNextId."',
						class_grade                = '".$i."',
						boys_count_morning         = '".$iBoysCountMorning."',
						girls_count_morning        = '".$iGirlsCountMorning."',
						boys_count_evening         = '".$iBoysCountEvening."',
						girls_count_evening        = '".$iGirlsCountEvening."'");

			$bFlag = $objDb->execute($sSQL);
			
			if($bFlag == false)
				break;
		}
		
                if($bFlag == true)
                {
                    $sSQL = "DELETE FROM tbl_survey_differently_abled_student_numbers WHERE survey_id='$iSurveyId'";
                    $objDb->query($sSQL);

                    $iBoysCount      = IO::intValue("txtCountBoys");
                    $iBoysGrades     = IO::intValue("txtBoysGrades");
                    $iGirlsCount     = IO::intValue("txtCountGirls");
                    $iGirlsGrades    = IO::intValue("txtGirlsGrades");
                    $sTimeInMorning  = IO::strValue("txtTimeInMorning");
                    $sTimeOutMorning = IO::strValue("txtTimeOutMorning");
                    $sTimeInEvening  = IO::strValue("txtTimeInEvening");
                    $sTimeOutEvening = IO::strValue("txtTimeOutEvening");

                    $sSQL = "INSERT INTO tbl_survey_differently_abled_student_numbers (survey_id, boys_count, girls_count, boys_grades, girls_grades, morning_time_in, morning_time_out, evening_time_in, evening_time_out)
                                                                VALUES ('$iSurveyId', '$iBoysCount', '$iGirlsCount', '$iBoysGrades', '$iGirlsGrades', '$sTimeInMorning', '$sTimeOutMorning', '$sTimeInEvening', '$sTimeOutEvening')";

                    $bFlag = $objDb->execute($sSQL);

                    if($bFlag == false)
                        break;
                }
        }
     
        if ($bFlag == true && IO::intValue("ddFixedSection") > -1)
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
?>