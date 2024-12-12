<?
	$sSQL = "SELECT * FROM tbl_survey_students_enrollment WHERE survey_id='$iSurveyId'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
?>          
	<h3>Student Enrolment</h3>
	
	<div class="grid">
            <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">  
		<tr valign="top" class="header">
			<th rowspan="2">Detail</th>
			<th colspan="3">Primary</th>
			<th colspan="3">Middle (grades 1-8)</th>
                        <th colspan="3">Middle (grades 6-8)</th>
			<th colspan="3">High / HSS</th>
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
			<th>Both</th>
                        <th>Male</th>
			<th>Female</th>
			<th>Both</th>
		</tr>
                <tr class="odd">
			<td>Enrolled</td>
			 <?
			for($i=0; $i<=3; $i++){
			?>
			<td><input type="text" name="txtMaleStudentCount<?= $i+1?>" value="<?= $objDb->getField($i, "male_count") ?>" maxlength="5" size="4" class="textbox" /></td>
			<td><input type="text" name="txtFemaleStudentCount<?= $i+1?>" value="<?= $objDb->getField($i, "female_count") ?>" maxlength="5" size="4" class="textbox" /></td>
			<td><input type="text" name="txtBothStudentCount<?= $i+1?>" value="<?= $objDb->getField($i, "both_count") ?>" maxlength="5" size="4" class="textbox" /></td>
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
                            <option value="1">Attendance Register</option>
           </select></label>
         </div><div style="display: inline-block; margin-left: 20px;">     
	    <input type="file" name="fileFixedSection" id="fileFixedSection" value="" maxlength="200" size="40" class="textbox" />
          </div>
        </div>