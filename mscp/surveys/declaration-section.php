<?
	$sSQL = "SELECT * FROM tbl_survey_declaration WHERE survey_id='$iSurveyId'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
?>        
							
<h3>Declaration</h3>
<br/>
<p style="text-align: left; line-height: 30px;">   It is certified that the <b>establishment</b> <input type="text" name="txtEstablishment" value="<?= @getDbValue("answer", "tbl_survey_answers", "survey_id='{$iSurveyId}' AND question_id='74'") ?>" maxlength="250" size="50" class="textbox" readonly/> where I, <i><b>(Name)</b></i> <input type="text" name="txtHead" value="<?= @getDbValue("answer", "tbl_survey_answers", "survey_id='{$iSurveyId}' AND question_id='84'") ?>" maxlength="100" size="20" class="textbox" readonly/>, am serving as the Principal/ Head Teacher <i><b>since </b></i> <input type="text" name="txtServingDate" id="txtServingDate" value="<?= (($objDb->getField(0, "serving_date") == "") ? date("Y-m-d") : $objDb->getField(0, "serving_date")) ?>" maxlength="10" size="10" class="textbox" readonly /> has not received any grant or assistance from district, provincial and federal government or any other agencies national/ international (INGOs NGOs, World Bank) for the above mentioned establishment during 2014-2016. 
   
   I declare that any funding provided prior to this survey has been fully utilised, and I acknowledge that Humqadam may not provide all facilities and utilities discussed in this survey.
   I declare that all answers are accurate to the best of my knowledge.
</p>
   <br/><br/> Principal’s signature: _______________________________________




    <br/><br/>
    Principal’s stamp: ____________________________________________ <br/><br/>

    Date:<input type="text" name="txtSignDate" id="txtSignDate" value="<?= (($objDb->getField(0, "sign_date") == "") ? date("Y-m-d") : $objDb->getField(0, "sign_date")) ?>" maxlength="10" size="10" class="textbox" readonly /><br/><br/>

    Enumerators Signature:_________________________________________<br/><br/>

    Date:_________________________________________<br/><br/>

    <div class="br10"></div>
    <?
	$sSQL = "SELECT id, picture, question_id FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='$iSectionId'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	
	if ($iCount > 0)
	{		
?>
	<div>
	  <ul>	
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$iPicture = $objDb->getField($i, "id");
                        $iQuestionId = $objDb->getField($i, "question_id");
			$sPicture = $objDb->getField($i, "picture");
?>
	    <li>
		  <a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-")) ?></a>
<?
			if ($sUserRights["Edit"] == "Y" && @strpos($_SERVER['REQUEST_URI'], "edit-") !== FALSE)
			{
?>
		  &nbsp; (<a href="<?= $sCurDir ?>/delete-survey-picture.php?SurveyId=<?= $iSurveyId ?>&SectionId=<?= $iSectionId ?>&PictureId=<?= $iPicture ?>&Picture=<?= $sPicture ?>&QuestionId=<?=$iQuestionId?>"><b>x</b></a>)
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
?>
<div class="br10"></div>
		  <div class="br10"></div>
        <div style="border: 1px dotted; width: 360px;">
            <div style="padding: 2px;"><span style="color: grey; font-size: 10px;">(Upload Image Against Selected Section)</span></div>
            <div style="display: inline-block; margin-left: 5px; margin-bottom: 5px;">
            <label for="ddFixedSection">
            <select name="ddFixedSection">
                            <option value="">Select A Section</option>
                            <option value="0">Declaration</option>
           </select></label>
         </div><div style="display: inline-block; margin-left: 20px;">     
	    <input type="file" name="fileFixedSection" id="fileFixedSection" value="" maxlength="200" size="40" class="textbox" />
          </div>
        </div>
                  