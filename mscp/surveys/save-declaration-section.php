<?
	$sSQL  = "DELETE FROM tbl_survey_declaration WHERE survey_id='$iSurveyId'";
	$bFlag = $objDb->execute($sSQL);
          
	if ($bFlag == true)
	{
		$sSQL = "INSERT INTO tbl_survey_declaration SET survey_id='$iSurveyId', ";

		$sServingDate  = (IO::strValue("txtServingDate") == '')?"0000-00-00":IO::strValue("txtServingDate");
                $sSignDate     = (IO::strValue("txtSignDate") == '')?"0000-00-00":IO::strValue("txtSignDate");
		
		$sSQL .= ("serving_date   = '".$sServingDate."',
                                  sign_date      = '".$sSignDate."'");
		
		$bFlag = $objDb->execute($sSQL);
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