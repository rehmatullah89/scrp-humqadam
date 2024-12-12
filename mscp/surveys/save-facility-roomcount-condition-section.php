<?
	$sSQL = "DELETE FROM tbl_survey_school_block_details WHERE survey_id='$iSurveyId'";
	$bFlag = $objDb->execute($sSQL);
          
	if ($bFlag == true)
	{
            $block = 0;
            for($i=1; $i<=100; $i = $i + 10){
                
                $block ++ ;
                $section = 1;
                for($j=$i; $j<10+$i; $j++){
               		$sSQL = "INSERT INTO tbl_survey_school_block_details SET survey_id='$iSurveyId', ";
			
			$iNextId         = getNextId("tbl_survey_school_block_details");
			$iTotalt        = IO::intValue("txtTotal".$j);
			$iGood          = IO::intValue("txtGood".$j);
			$iRehab         = IO::intValue("txtRehabilitation".$j);
			$iDilapidated   = IO::intValue("txtDilapidated".$j);
                        
                        $sBlockNo = 'b'.$block;
                        
                        if($section == 1)
                            $sRoomTypeCode = 'CRE';
			else if($section == 2)
                            $sRoomTypeCode = 'CRO';
			else if($section == 3)
                            $sRoomTypeCode = 'SWA';
			else if($section == 4)
                            $sRoomTypeCode = 'TFS';
			else if($section == 5)
                            $sRoomTypeCode = 'TMS';
			else if($section == 6)
                            $sRoomTypeCode = 'TFT';
			else if($section == 7)
                            $sRoomTypeCode = 'TMT';
			else if($section == 8)
                            $sRoomTypeCode = 'UT';
                        else if($section == 9)
                            $sRoomTypeCode = 'US';
                        else if($section == 10)
                            $sRoomTypeCode = 'TB';
                    
                        $section++;
                        
                    $sSQL = "INSERT INTO tbl_survey_school_block_details SET survey_id='$iSurveyId', ";
	
			$sSQL .= ("id   = '".$iNextId."',
						block                = '".$sBlockNo."',
						room_type_code       = '".$sRoomTypeCode."',
						total                = '".$iTotalt."',
						good                 = '".$iGood."',
                        rehabilitation       = '".$iRehab."',
						dilapidated          = '".$iDilapidated."'");

			$bFlag = $objDb->execute($sSQL);
			
			if($bFlag == false)
				break;
                }
			
        }
            
		if($bFlag == true)
		{
			$sSQL = "DELETE FROM tbl_survey_school_blocks WHERE survey_id='$iSurveyId'";
			$bFlag = $objDb->execute($sSQL);
		}
		
		if ($bFlag == true)
		{		
			for($i=1; $i<=10; $i++)
			{
				$sSQL = "INSERT INTO tbl_survey_school_blocks SET survey_id='$iSurveyId', ";
				
				$iNextId     = getNextId("tbl_survey_school_blocks");
				$iAge       = IO::intValue("age".$i);
				$iStorey    = IO::intValue("storey".$i);
				$sCM        = @implode(',',IO::getArray("cm".$i));                    
			  	$sSQL .= ("id   = '".$iNextId."',
							block   = 'b".$i."',
							age     = '".$iAge."',
							storeys = '".$iStorey."',
							cm      = '".$sCM."'");

				$bFlag = $objDb->execute($sSQL);

				if($bFlag == false)
					break;
			}
		}
                
                if ($bFlag == true){
                    
                    $sSQL = "DELETE FROM tbl_survey_school_other_blocks WHERE survey_id='$iSurveyId'";
                    $bFlag = $objDb->execute($sSQL);
                    
                }
                
                if ($bFlag == true)
		{	
                    
                    $sOtherBlock1 = IO::intValue("txtOtherBlock1");
                    $sDetails1    = IO::strValue("txtOtherDetails1");
                    $sOtherBlock2 = IO::intValue("txtOtherBlock2");
                    $sDetails2    = IO::strValue("txtOtherDetails2");
                    $sOtherBlock3 = IO::intValue("txtOtherBlock3");
                    $sDetails3    = IO::strValue("txtOtherDetails3");
                    $sComments    = IO::strValue("Comments");
                    
                    $sSQL = "INSERT INTO tbl_survey_school_other_blocks SET survey_id='$iSurveyId', ";
				
                    $sSQL .= ("other_block_1   = '".$sOtherBlock1."',
				other_details_1   = '".$sDetails1."',
				other_block_2     = '".$sOtherBlock2."',
				other_details_2   = '".$sDetails2."',
				other_block_3     = '".$sOtherBlock3."',
                                other_details_3   = '".$sDetails3."',
                                comments      = '".$sComments."'");
                    
                    $bFlag = $objDb->execute($sSQL);                       
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