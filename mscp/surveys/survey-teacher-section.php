<?
	$sSQL = "SELECT * FROM tbl_survey_teacher_numbers WHERE survey_id='$iSurveyId'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
?>        
	<h3>Teacher Numbers</h3>
<div class="grid">
	<table  width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">   
		<tr valign="top" class="header">
			<th rowspan="2" style="width: 120px;">Detail</th>
			<th colspan="3">Sanctioned</th>
			<th colspan="3">Filled</th>
			<th colspan="3">Regularly Attending</th>
		</tr>
		<tr valign="top" class="header">
			<th>Male</th>
			<th>Female</th>
			<th>Both</th>
			<th>Male</th>
			<th>Female</th>
			<th>Both</th>
			<th>Male</th>
			<th>Female</th>
			<th>Total</th>
		</tr>
		<tr class="even">
			<td><b>Teachers</b></td>
			<?
			for($i=0; $i<3; $i++){
			?>
			<td><input type="text" name="txtMaleStaffTypeCount<?= $i+1?>" value="<?= $objDb->getField($i, "male_count") ?>" maxlength="5" size="6" class="textbox" /></td>
			<td><input type="text" name="txtFemaleStaffTypeCount<?= $i+1?>" value="<?= $objDb->getField($i, "female_count") ?>" maxlength="5" size="6" class="textbox" /></td>
			<td><input type="text" name="txtBothStaffTypeCount<?= $i+1?>" value="<?= $objDb->getField($i, "both_count") ?>" maxlength="5" size="6" class="textbox" /></td>
			<?
				}     
			?>   
		</tr>
		<tr class="odd">
			<td><b>Support Staff</b></td>
			<?
			for($i=3; $i<6; $i++){
			?>
			<td><input type="text" name="txtMaleStaffTypeCount<?= $i+1?>" value="<?= $objDb->getField($i, "male_count") ?>" maxlength="5" size="6" class="textbox" /></td>
			<td><input type="text" name="txtFemaleStaffTypeCount<?= $i+1?>" value="<?= $objDb->getField($i, "female_count") ?>" maxlength="5" size="6" class="textbox" /></td>
			<td><input type="text" name="txtBothStaffTypeCount<?= $i+1?>" value="<?= $objDb->getField($i, "both_count") ?>" maxlength="5" size="6" class="textbox" /></td>
			<?
				}     
			?>   
		</tr>
		<tr class="even">
			<td><b>Management Staff</b></td>
			<?
			for($i=6; $i<9; $i++){
			?>
			<td><input type="text" name="txtMaleStaffTypeCount<?= $i+1?>" value="<?= $objDb->getField($i, "male_count") ?>" maxlength="5" size="6" class="textbox" /></td>
			<td><input type="text" name="txtFemaleStaffTypeCount<?= $i+1?>" value="<?= $objDb->getField($i, "female_count") ?>" maxlength="5" size="6" class="textbox" /></td>
			<td><input type="text" name="txtBothStaffTypeCount<?= $i+1?>" value="<?= $objDb->getField($i, "both_count") ?>" maxlength="5" size="6" class="textbox" /></td>
			<?
				}     
			?>   
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
        <br/>
        <div style="border: 1px dotted; width: 490px;">
            <div style="padding: 2px;"><span style="color: grey; font-size: 10px;">(Upload Image Against Selected Section)</span></div>
            <div style="display: inline-block; margin-left: 5px; margin-bottom: 5px;">
            <label for="ddFixedSection">
            <select name="ddFixedSection">
                            <option value="">Select A Section</option>
                            <option value="1">Teachers Sanctioned</option>
                            <option value="2">Teachers Filled</option>
                            <option value="3">Teachers Regularly Attending</option>
                            <option value="4">Support Staff Sanctioned</option>
                            <option value="5">Support Staff Filled</option>
                            <option value="6">Support Staff Regularly Attending</option>
                            <option value="7">Management Staff Sanctioned</option>
                            <option value="8">Management Staff Filled</option>
                            <option value="9">Management Staff Regularly Attending</option>
           </select></label>
         </div><div style="display: inline-block; margin-left: 20px;">     
	    <input type="file" name="fileFixedSection" id="fileFixedSection" value="" maxlength="200" size="40" class="textbox" />
          </div>
        </div>