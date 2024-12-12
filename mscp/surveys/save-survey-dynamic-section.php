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

	$sSQL = "SELECT id, `type`, options, other, picture FROM tbl_survey_questions WHERE status='A' AND section_id='$iSectionId'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iQuestion = $objDb->getField($i, "id");
		$sType     = $objDb->getField($i, "type");
		$sOptions  = $objDb->getField($i, "options");	
		$sOther    = $objDb->getField($i, "other");
		$sPicture  = $objDb->getField($i, "picture");
		
		
		if ($sOther == "Y")
			$sOther = IO::strValue("txtOther{$iQuestion}");
		
		else
			$sOther = "";
		

		
		if ($sType == "YN" || $sType == "SS")
			$sAnswer = IO::strValue("rbQuestion{$iQuestion}");

		else if ($sType == "MS")
			$sAnswer = @implode("\n", IO::getArray("cbQuestion{$iQuestion}"));	
		
		else if ($sType == "SL" || $sType == "ML")
			$sAnswer = IO::strValue("txtQuestion{$iQuestion}");
		
	
		if (getDbValue("COUNT(1)", "tbl_survey_answers", "survey_id='$iSurveyId' AND question_id='$iQuestion'") > 0)
		{
			$sSQL  = "UPDATE tbl_survey_answers SET answer = '$sAnswer',
													other  = '$sOther'
					  WHERE survey_id='$iSurveyId' AND question_id='$iQuestion'";
			$bFlag = $objDb2->execute($sSQL);
		}
		
		else
		{
			$sSQL = "INSERT INTO tbl_survey_answers SET survey_id   = '$iSurveyId',
														question_id = '$iQuestion',
														answer      = '$sAnswer',
														other       = '$sOther'";
			$bFlag = $objDb2->execute($sSQL);
		}
		
		
		if ($bFlag == true && $sPicture == "Y")
		{
			if ($_FILES["filePicture{$iQuestion}"]['name'] != "")
			{
				$sPicture = ($iSurveyId."-Q".$iQuestion."-".IO::getFileName($_FILES["filePicture{$iQuestion}"]['name']));

				if (@move_uploaded_file($_FILES["filePicture{$iQuestion}"]['tmp_name'], ($sRootDir.SURVEYS_DOC_DIR.$sPicture)))
				{
					$iPicture = getNextId("tbl_survey_pictures");
					
					
					$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																  survey_id   = '$iSurveyId',
																  section_id  = '$iSectionId',
																  question_id = '$iQuestion',
																  picture     = '$sPicture'";
					$bFlag = $objDb2->execute($sSQL);
				}
			}
		}	
		
		if ($bFlag == false)
			break;
	}
?>