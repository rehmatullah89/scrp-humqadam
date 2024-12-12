<?
	$sSQL = "DELETE FROM tbl_survey_students_enrollment WHERE survey_id='$iSurveyId'";
	$bFlag = $objDb->execute($sSQL);
          
	if ($bFlag == true)
	{
		for($i=1; $i<=4; $i++)
		{
			$sSQL = "INSERT INTO tbl_survey_students_enrollment SET survey_id='$iSurveyId', ";
			
			$iNextId = getNextId("tbl_survey_students_enrollment");
			$iMaleStudentCount = IO::intValue("txtMaleStudentCount".$i);
			$iFemaleStudentCount = IO::intValue("txtFemaleStudentCount".$i);
			$iBothStudentCount = IO::intValue("txtBothStudentCount".$i);
                    
			if($i % 4 == 1){
				$sSchoolType = 'P';
			}else if($i % 4 == 2){
				$sSchoolType = 'M18';
			}else if($i % 4 == 3){
				$sSchoolType = 'M68';
			}else if($i % 4 == 0){
				$sSchoolType = 'H';
			}
			
			$sSQL .= ("id   = '".$iNextId."',
						male_count                 = '".$iMaleStudentCount."',
						female_count                 = '".$iFemaleStudentCount."',
						both_count                = '".$iBothStudentCount."',
                        school_type              = '".$sSchoolType."'");

			$bFlag = $objDb->execute($sSQL);
			
			if($bFlag == false)
				break;
                }
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
?>