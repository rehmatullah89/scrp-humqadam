<?
	$sSQL = "DELETE FROM tbl_survey_school_facilities WHERE survey_id='$iSurveyId'";
	$bFlag = $objDb->execute($sSQL);
          
	if ($bFlag == true)
	{
		for($i=1; $i<=6; $i++)
		{
			$sSQL = "INSERT INTO tbl_survey_school_facilities SET survey_id='$iSurveyId', ";
			
			$iNextId   = getNextId("tbl_survey_school_facilities");
			$fTotal    = IO::floatValue("txtTotal".$i);
			$fGood     = IO::floatValue("txtGood".$i);
			$fRehab    = IO::floatValue("txtRehabilitation".$i);
			$fDilap    = IO::floatValue("txtDilapidated".$i);
			$sMaterial = IO::strValue("txtMaterial".$i);
			$fHeight   = IO::floatValue("txtHeight".$i);

			if($i == 1)
				$sType = 'PG';
			else if($i == 2)
				$sType = 'PB';
            else if($i == 3)
				$sType = 'BT';
			else if($i == 4)
				$sType = 'BW';
			else if($i == 5)
				$sType = 'MG';
			else if($i == 6)
				$sType = 'RW';
                    

			$sSQL .= ("id   = '".$iNextId."',
						type                = '".$sType."',
						total               = '".$fTotal."',
						good                = '".$fGood."',
						rehabilitation      = '".$fRehab."',
						dilapidated         = '".$fDilap."',
                        material            = '".$sMaterial."',
						height              = '".$fHeight."'");

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