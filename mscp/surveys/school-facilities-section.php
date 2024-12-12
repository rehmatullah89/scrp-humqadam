<?
	$sSQL = "SELECT * FROM tbl_survey_school_facilities WHERE survey_id='$iSurveyId' ORDER BY id";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
?>             
	<h3>School Facilities</h3>

	<div class="grid">
          <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">  
		<tr valign="top" class="header">
			<th style="width:180px;">Type of Facility</th>
			<th>T</th>
			<th>G</th>
			<th>R</th>
			<th>D</th>
			<th>Material</th>
			<th>Height</th>
		</tr>
                <tr class="even">
			<td nowrap>Playgrounds for Girls (sq. feet)</td>
			<td><input type="text" name="txtTotal1" value="<?= $objDb->getField(0, "total") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtGood1" value="<?= $objDb->getField(0, "good") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation1" value="<?= $objDb->getField(0, "rehabilitation") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated1" value="<?= $objDb->getField(0, "dilapidated") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="hidden" name="txtMaterial1" value="<?= $objDb->getField(0, "material") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="hidden" name="txtHeight1" value="<?= $objDb->getField(0, "height") ?>" maxlength="5" size="9" class="textbox" /></td>
		</tr>
		<tr class="odd">
			<td nowrap>Playgrounds for Boys (sq. feet)</td>
			<td><input type="text" name="txtTotal2" value="<?= $objDb->getField(1, "total") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtGood2" value="<?= $objDb->getField(1, "good") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation2" value="<?= $objDb->getField(1, "rehabilitation") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated2" value="<?= $objDb->getField(1, "dilapidated") ?>" maxlength="5" size="9" class="textbox" /></td>
                        <td><input type="hidden" name="txtMaterial2" value="<?= $objDb->getField(1, "material") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="hidden" name="txtHeight2" value="<?= $objDb->getField(1, "height") ?>" maxlength="5" size="9" class="textbox" /></td>
		</tr>
		<tr class="even">
			<td nowrap>Playgrounds for Both (sq. feet)</td>
			<td><input type="text" name="txtTotal3" value="<?= $objDb->getField(2, "total") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtGood3" value="<?= $objDb->getField(2, "good") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation3" value="<?= $objDb->getField(2, "rehabilitation") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated3" value="<?= $objDb->getField(2, "dilapidated") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="hidden" name="txtMaterial3" value="<?= $objDb->getField(2, "material") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="hidden" name="txtHeight3" value="<?= $objDb->getField(2, "height") ?>" maxlength="5" size="9" class="textbox" /></td>
		 </tr>
                <tr class="even">
			<td nowrap>Boundary Wall (length in ft)</td>
			<td><input type="text" name="txtTotal4" value="<?= $objDb->getField(3, "total") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtGood4" value="<?= $objDb->getField(3, "good") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation4" value="<?= $objDb->getField(3, "rehabilitation") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated4" value="<?= $objDb->getField(3, "dilapidated") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtMaterial4" value="<?= $objDb->getField(3, "material") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtHeight4" value="<?= $objDb->getField(3, "height") ?>" maxlength="5" size="9" class="textbox" /></td>
		 </tr>
		 <tr class="odd">
			<td nowrap>Main Gate (length in feet)</td>
			<td><input type="text" name="txtTotal5" value="<?= $objDb->getField(4, "total") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtGood5" value="<?= $objDb->getField(4, "good") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation5" value="<?= $objDb->getField(4, "rehabilitation") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated5" value="<?= $objDb->getField(4, "dilapidated") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtMaterial5" value="<?= $objDb->getField(4, "material") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtHeight5" value="<?= $objDb->getField(4, "height") ?>" maxlength="5" size="9" class="textbox" /></td>
		</tr>
		   <tr class="even">
			<td nowrap>Retaining Wall (length in ft)</td>
                        <td><input type="text" name="txtTotal6" value="<?= $objDb->getField(5, "total") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtGood6" value="<?= $objDb->getField(5, "good") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation6" value="<?= $objDb->getField(5, "rehabilitation") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated6" value="<?= $objDb->getField(5, "dilapidated") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtMaterial6" value="<?= $objDb->getField(5, "material") ?>" maxlength="5" size="9" class="textbox" /></td>
			<td><input type="text" name="txtHeight6" value="<?= $objDb->getField(5, "height") ?>" maxlength="5" size="9" class="textbox" /></td>
		</tr>
	 </table>
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
        </div>
         <div class="br10"></div>
        <div style="border: 1px dotted; width: 395px;">
            <div style="padding: 2px;"><span style="color: grey; font-size: 10px;">(Upload Image Against Selected Section)</span></div>
            <div style="display: inline-block; margin-left: 5px; margin-bottom: 5px;">
            <label for="ddFixedSection">
            <select name="ddFixedSection">
                            <option value="">Select A Section</option>
                            <option value="1">Play Ground for Girls</option>
                            <option value="2">Play Ground for Boys</option>
                            <option value="3">Play Ground for Both</option>
                            <option value="4">Boundry Wall</option>
                            <option value="5">Main Gate</option>
                            <option value="6">Retaining Wall</option>
           </select></label>
         </div><div style="display: inline-block; margin-left: 20px;">     
	    <input type="file" name="fileFixedSection" id="fileFixedSection" value="" maxlength="200" size="40" class="textbox" />
          </div>
        </div>
         