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

	$sSQL = "SELECT id, `type`, question, options, other, picture, link_id, link_value FROM tbl_survey_questions WHERE status='A' AND section_id='$iSectionId' Order By position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iQuestion = $objDb->getField($i, "id");
		$iLink     = $objDb->getField($i, "link_id");
		$sLink     = $objDb->getField($i, "link_value");	
		$sType     = $objDb->getField($i, "type");
		$sQuestion = $objDb->getField($i, "question");
		$sOptions  = $objDb->getField($i, "options");	
		$sOther    = $objDb->getField($i, "other");
		$sPicture  = $objDb->getField($i, "picture");
		
		
		$sAnswer        = "";
		$sAnswerOther   = "";
		$sAnswerPicture = "";
		
		$sSQL = "SELECT answer, other FROM tbl_survey_answers WHERE survey_id='$iSurveyId' AND question_id='$iQuestion'";		
		$objDb2->query($sSQL);
		
		if ($objDb2->getCount( ) == 1)
		{
			$sAnswer      = $objDb2->getField(0, "answer");
			$sAnswerOther = $objDb2->getField(0, "other");
		}
		
?>

	<div class="question" id="Question<?= $iQuestion ?>" type="<?= $sType ?>" link="<?= (($iLink > 0) ? "{$iLink}|{$sLink}" : "") ?>">
	  <br />
<?
		if ($sType == "YN")
		{
?>
	  <label for=""><b><?= $sQuestion ?></b></label>

	  <div>
	    <label for="rbQuestionY<?= $iQuestion ?>"><input type="radio" name="rbQuestion<?= $iQuestion ?>" id="rbQuestionY<?= $iQuestion ?>" value="Y" <?= (($sAnswer == "Y") ? "checked" : "") ?> /> Yes</label>
	    <label for="rbQuestionN<?= $iQuestion ?>"><input type="radio" name="rbQuestion<?= $iQuestion ?>" id="rbQuestionN<?= $iQuestion ?>" value="N" <?= (($sAnswer == "N") ? "checked" : "") ?> /> No</label>
	  </div>
<?
		}
		
		else if ($sType == "SS")
		{
?>
	  <label for=""><b><?= $sQuestion ?></b></label>

	  <div>
<?
			$sOptions = @explode("\n", $sOptions);
			$iIndex   = 1;
			
			foreach ($sOptions as $sOption)
			{
				$sOption = trim($sOption);
?>
	    <label for="rbQuestion<?= $iQuestion ?>_<?= $iIndex ?>"><input type="radio" name="rbQuestion<?= $iQuestion ?>" id="rbQuestion<?= $iQuestion ?>_<?= $iIndex ?>" value="<?= $sOption ?>"  <?= (($sOption == $sAnswer) ? "checked" : "") ?> /> <?= $sOption ?></label>
<?
				$iIndex ++;
			}
?>
	  </div>
<?
		}

		else if ($sType == "MS")
		{
?>
	  <label for=""><b><?= $sQuestion ?></b></label>

	  <div>
<?
			$sOptions = @explode("\n", $sOptions);
			$sAnswers = @explode("\n", $sAnswer);
			$sAnswers = @array_map("trim", $sAnswers);
			$iIndex   = 1;
		
			foreach ($sOptions as $sOption)
			{
				$sOption = trim($sOption);
?>
	    <label for="cbQuestion<?= $iQuestion ?>_<?= $iIndex ?>"><input type="checkbox" name="cbQuestion<?= $iQuestion ?>[]" id="cbQuestion<?= $iQuestion ?>_<?= $iIndex ?>" value="<?= $sOption ?>"  <?= ((@in_array($sOption, $sAnswers)) ? "checked" : "") ?> /> <?= $sOption ?></label>
<?
				$iIndex ++;
			}
?>
	  </div>
<?
		}
		
		
		else if ($sType == "SL")
		{
?>
	  <label for="txtQuestion<?= $iQuestion ?>"><b><?= $sQuestion ?></b></label>
	  <div><input type="text" name="txtQuestion<?= $iQuestion ?>" id="txtQuestion<?= $iQuestion ?>" value="<?= formValue($sAnswer) ?>" maxlength="200" size="40" class="textbox" /></div>
<?
		}
		
		
		else if ($sType == "ML")
		{
?>
	  <label for="txtQuestion<?= $iQuestion ?>"><b><?= $sQuestion ?></b></label>
	  <div><textarea name="txtQuestion<?= $iQuestion ?>" id="txtQuestion<?= $iQuestion ?>" rows="5" cols="60"><?= formValue($sAnswer) ?></textarea></div>
<?
		}
		
		
		if ($sOther == "Y")
		{
?>
	  <div class="br5"></div>
	
	  <label for="txtOther<?= $iQuestion ?>">Other</label>
	  <div><input type="text" name="txtOther<?= $iQuestion ?>" id="txtOther<?= $iQuestion ?>" value="<?= formValue($sAnswerOther) ?>" maxlength="200" size="40" class="textbox other" /></div>
<?
		}
		
		
		if ($sPicture == "Y")
		{
?>
	  <div class="br10"></div>
	
	  <label for="filePicture<?= $iQuestion ?>">Picture <span>(Optional)</span></label>
	  <div><input type="file" name="filePicture<?= $iQuestion ?>" id="filePicture<?= $iQuestion ?>" value="" maxlength="100" size="40" class="textbox" /></div>
<?
			$sSQL = "SELECT id, picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='$iSectionId' AND question_id='$iQuestion'";
			$objDb2->query($sSQL);

			$iCount2 = $objDb2->getCount( );
			
			if ($iCount2 > 0)
			{		
?>
	  <div>
	    <ul>	
<?
				for ($j = 0; $j < $iCount2; $j ++)
				{
					$iPicture = $objDb2->getField($j, "id");
					$sPicture = $objDb2->getField($j, "picture");
?>
	      <li>
		    <a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-Q{$iQuestion}-")) ?></a>
<?
					if ($sUserRights["Edit"] == "Y" && @strpos($_SERVER['REQUEST_URI'], "edit-") !== FALSE)
					{
?>
		    &nbsp; (<a href="<?= $sCurDir ?>/delete-survey-picture.php?SurveyId=<?= $iSurveyId ?>&SectionId=<?= $iSectionId ?>&QuestionId=<?= $iQuestion ?>&PictureId=<?= $iPicture ?>&Picture=<?= $sPicture ?>"><b>x</b></a>)
<?
					}
?>
		  </li>
<?
				}
?>
	    </ul>
	  </div>	
<?
			}
		}		
?>
	</div>	
<?
	}
?>