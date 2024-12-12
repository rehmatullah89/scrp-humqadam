<?
        $sSQL = "SELECT * FROM tbl_survey_school_block_details WHERE survey_id='$iSurveyId' Order By id";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	$sSQL = "SELECT * FROM tbl_survey_school_blocks WHERE survey_id='$iSurveyId'";
	$objDb2->query($sSQL);
        
        
?>            
	<h3>Facility and Room Count, and Condition</h3>

	<div class="grid">
           <table border="1" cellpadding="0" cellspacing="0" style="text-align: left; border-color: #ffffff;">    
    		<tr valign="top" class="header">
			<th rowspan="4" style="padding-top: 50px; padding-left: 15px;">Block Information</th>
			<th colspan="4" style="padding: 5px;">Block 1</th>
			<th colspan="4" style="padding: 5px;">Block 2</th>
			<th colspan="4" style="padding: 5px;">Block 3</th>
			<th colspan="4" style="padding: 5px;">Block 4</th>
			<th colspan="4" style="padding: 5px;">Block 5</th>
			<th colspan="4" style="padding: 5px;">Block 6</th>
			<th colspan="4" style="padding: 5px;">Block 7</th>
			<th colspan="4" style="padding: 5px;">Block 8</th>
			<th colspan="4" style="padding: 5px;">Block 9</th>
			<th colspan="4" style="padding: 5px;">Block 10</th>
		</tr>
		<tr valign="top" class="header">
			<? for($age = 0; $age<10; $age++){ ?>
			<th colspan="4" style="padding: 5px;"><span style="padding-right: 24px;">Age:</span> <input type="text" name="age<?= $age+1?>" value="<?= $objDb2->getField($age, 'age') ?>" maxlength="5" size="12" class="textbox" /></th>
			<?}?>
		</tr>
		<tr valign="top" class="header">
		    <? for($storey = 0; $storey<10; $storey++){ ?>
			<th colspan="4" style="padding: 5px;"><span>Storeys:</span> <input type="text" name="storey<?= $storey+1?>" value="<?= $objDb2->getField($storey, 'storeys') ?>" maxlength="5" size="12" class="textbox" /></th>
			<?}?>
		</tr>
		<tr valign="top" class="header">
			<? for($CM = 0; $CM<10; $CM++){ 
                           $cmArr = @explode(',', $objDb2->getField($CM, 'cm')); 
                            ?>
			<th colspan="4" style="padding: 5px;" nowrap><span style="padding-right: 31px;">CM:</span> 
			<select name="cm<?= $CM+1?>[]" class="select" style="width: 110px;" multiple>
				<option value="">Select an Option</option>
                                <option value="R" <?= in_array('R',$cmArr)?'selected':'' ?>> Reinforced Brick Masonry or Confined Masonry</option>
				<option value="U" <?= in_array('U',$cmArr)?'selected':'' ?>> Unreinforced Brick Masonry or unconfined Masonry</option>
                                <option value="S" <?= in_array('S',$cmArr)?'selected':'' ?>> Stone Masonry</option>
                                <option value="F" <?= in_array('F',$cmArr)?'selected':'' ?>> Frame Structure</option>
			</select>
			</th>
			<?}?>
		</tr>
		<tr valign="top" class="header">
			<th style="padding: 5px;" nowrap>Type of Room</th>
			<? for($TGRD = 0; $TGRD<10; $TGRD++){ ?>
			<th style="padding: 2px;">T</th>
			<th style="padding: 2px;">G</th>
			<th style="padding: 2px;">R</th>
			<th style="padding: 2px;">D</th>
			<?}?>
		</tr>
                <tr class="even">
			<td style="padding: 5px;" nowrap>Classrooms used for educational purposes</td>
			 <?
			for($i=0; $i<100; $i = $i+10){
			?>
                            <td><input type="text" name="txtTotal<?= $i+1?>" value="<?= ($objDb->getField($i, "total") == 0?"":$objDb->getField($i, "total")) ?>" maxlength="5" size="3" class="textbox" /></td>
                            <td><input type="text" name="txtGood<?= $i+1?>" value="<?= ($objDb->getField($i, "good")==0?"":$objDb->getField($i, "good")) ?>" maxlength="5" size="3" class="textbox" /></td>
                            <td><input type="text" name="txtRehabilitation<?= $i+1?>" value="<?= ($objDb->getField($i, "rehabilitation") == 0?"":$objDb->getField($i, "rehabilitation")) ?>" maxlength="5" size="3" class="textbox" /></td>
                            <td><input type="text" name="txtDilapidated<?= $i+1?>" value="<?= ($objDb->getField($i, "dilapidated") == 0?"": $objDb->getField($i, "dilapidated")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<?
                        }     
			?>   
		</tr>
                <tr class="even">
			<td style="padding: 5px;" nowrap>Classrooms used for any other purposes</td>
			 <?
			for($i=1; $i<100; $i = $i + 10){
			?>
			<td><input type="text" name="txtTotal<?= $i+1?>" value="<?= ($objDb->getField($i, "total") == 0?"":$objDb->getField($i, "total")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtGood<?= $i+1?>" value="<?= ($objDb->getField($i, "good")==0?"":$objDb->getField($i, "good")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation<?= $i+1?>" value="<?= ($objDb->getField($i, "rehabilitation") == 0?"":$objDb->getField($i, "rehabilitation")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated<?= $i+1?>" value="<?= ($objDb->getField($i, "dilapidated") == 0?"": $objDb->getField($i, "dilapidated")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<?
				}     
			?>   
		</tr>
		<tr class="odd">
			<td style="padding: 5px;" nowrap>Safe Wheelchair Access Ramp</td>
			 <?
			for($i=2; $i<100; $i = $i + 10){
			?>
			<td><input type="text" name="txtTotal<?= $i+1?>" value="<?= ($objDb->getField($i, "total") == 0?"":$objDb->getField($i, "total")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtGood<?= $i+1?>" value="<?= ($objDb->getField($i, "good")==0?"":$objDb->getField($i, "good")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation<?= $i+1?>" value="<?= ($objDb->getField($i, "rehabilitation") == 0?"":$objDb->getField($i, "rehabilitation")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated<?= $i+1?>" value="<?= ($objDb->getField($i, "dilapidated") == 0?"": $objDb->getField($i, "dilapidated")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<?
				}     
			?>   
		</tr>
		<tr class="even">
			<td style="padding: 5px;" nowrap>Toilets – Female Students</td>
			 <?
			for($i=3; $i<100; $i = $i + 10){
			?>
			 <td><input type="text" name="txtTotal<?= $i+1?>" value="<?= ($objDb->getField($i, "total") == 0?"":$objDb->getField($i, "total")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtGood<?= $i+1?>" value="<?= ($objDb->getField($i, "good")==0?"":$objDb->getField($i, "good")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation<?= $i+1?>" value="<?= ($objDb->getField($i, "rehabilitation") == 0?"":$objDb->getField($i, "rehabilitation")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated<?= $i+1?>" value="<?= ($objDb->getField($i, "dilapidated") == 0?"": $objDb->getField($i, "dilapidated")) ?>" maxlength="5" size="3" class="textbox" /></td>
		   <?
				}     
			?>   
		</tr>
		<tr class="odd">
			<td style="padding: 5px;" nowrap>Toilets – Male Students</td>
			 <?
			for($i=4; $i<100; $i = $i + 10){
			?>
			 <td><input type="text" name="txtTotal<?= $i+1?>" value="<?= ($objDb->getField($i, "total") == 0?"":$objDb->getField($i, "total")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtGood<?= $i+1?>" value="<?= ($objDb->getField($i, "good")==0?"":$objDb->getField($i, "good")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation<?= $i+1?>" value="<?= ($objDb->getField($i, "rehabilitation") == 0?"":$objDb->getField($i, "rehabilitation")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated<?= $i+1?>" value="<?= ($objDb->getField($i, "dilapidated") == 0?"": $objDb->getField($i, "dilapidated")) ?>" maxlength="5" size="3" class="textbox" /></td>
		   <?
				}     
			?>   
		</tr>
		 <tr class="even">
			<td style="padding: 5px;" nowrap>Toilets – Female Teachers</td>
			 <?
			for($i=5; $i<100; $i = $i + 10){
			?>
			 <td><input type="text" name="txtTotal<?= $i+1?>" value="<?= ($objDb->getField($i, "total") == 0?"":$objDb->getField($i, "total")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtGood<?= $i+1?>" value="<?= ($objDb->getField($i, "good")==0?"":$objDb->getField($i, "good")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation<?= $i+1?>" value="<?= ($objDb->getField($i, "rehabilitation") == 0?"":$objDb->getField($i, "rehabilitation")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated<?= $i+1?>" value="<?= ($objDb->getField($i, "dilapidated") == 0?"": $objDb->getField($i, "dilapidated")) ?>" maxlength="5" size="3" class="textbox" /></td>
		   <?
				}     
			?>   
		</tr>
		<tr class="odd">
			<td style="padding: 5px;" nowrap>Toilets – Male Teachers</td>
			 <?
			for($i=6; $i<100; $i = $i + 10){
			?>
			 <td><input type="text" name="txtTotal<?= $i+1?>" value="<?= ($objDb->getField($i, "total") == 0?"":$objDb->getField($i, "total")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtGood<?= $i+1?>" value="<?= ($objDb->getField($i, "good")==0?"":$objDb->getField($i, "good")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation<?= $i+1?>" value="<?= ($objDb->getField($i, "rehabilitation") == 0?"":$objDb->getField($i, "rehabilitation")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated<?= $i+1?>" value="<?= ($objDb->getField($i, "dilapidated") == 0?"": $objDb->getField($i, "dilapidated")) ?>" maxlength="5" size="3" class="textbox" /></td>
		   <?
				}     
			?>   
		</tr>
		<tr class="even">
			<td style="padding: 5px;" nowrap>Unisex toilets for teachers</td>
			 <?
			for($i=7; $i<100; $i = $i + 10){
			?>
			 <td><input type="text" name="txtTotal<?= $i+1?>" value="<?= ($objDb->getField($i, "total") == 0?"":$objDb->getField($i, "total")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtGood<?= $i+1?>" value="<?= ($objDb->getField($i, "good")==0?"":$objDb->getField($i, "good")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation<?= $i+1?>" value="<?= ($objDb->getField($i, "rehabilitation") == 0?"":$objDb->getField($i, "rehabilitation")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated<?= $i+1?>" value="<?= ($objDb->getField($i, "dilapidated") == 0?"": $objDb->getField($i, "dilapidated")) ?>" maxlength="5" size="3" class="textbox" /></td>
		   <?
				}     
			?>   
		</tr>
                <tr class="even">
			<td style="padding: 5px;" nowrap>Unisex toilets for student</td>
			 <?
			for($i=8; $i<100; $i = $i + 10){
			?>
			 <td><input type="text" name="txtTotal<?= $i+1?>" value="<?= ($objDb->getField($i, "total") == 0?"":$objDb->getField($i, "total")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtGood<?= $i+1?>" value="<?= ($objDb->getField($i, "good")==0?"":$objDb->getField($i, "good")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation<?= $i+1?>" value="<?= ($objDb->getField($i, "rehabilitation") == 0?"":$objDb->getField($i, "rehabilitation")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated<?= $i+1?>" value="<?= ($objDb->getField($i, "dilapidated") == 0?"": $objDb->getField($i, "dilapidated")) ?>" maxlength="5" size="3" class="textbox" /></td>
		   <?
				}     
			?>   
		</tr>
		<tr class="odd">
			<td style="padding: 5px;" nowrap>Toilets – Both Teacher/Student</td>
			 <?
			for($i=9; $i<100; $i = $i + 10){
			?>
			<td><input type="text" name="txtTotal<?= $i+1?>" value="<?= ($objDb->getField($i, "total") == 0?"":$objDb->getField($i, "total")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtGood<?= $i+1?>" value="<?= ($objDb->getField($i, "good")==0?"":$objDb->getField($i, "good")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtRehabilitation<?= $i+1?>" value="<?= ($objDb->getField($i, "rehabilitation") == 0?"":$objDb->getField($i, "rehabilitation")) ?>" maxlength="5" size="3" class="textbox" /></td>
			<td><input type="text" name="txtDilapidated<?= $i+1?>" value="<?= ($objDb->getField($i, "dilapidated") == 0?"": $objDb->getField($i, "dilapidated")) ?>" maxlength="5" size="3" class="textbox" /></td>
		   <?
				}     
			?>   
		</tr>
  </table>
</div>
     <br/><br/>   
    <?
        $sSQL = "SELECT * FROM tbl_survey_school_other_blocks WHERE survey_id='$iSurveyId'";
        $objDb->query($sSQL);
    ?>
     <h3>Other Rooms/Blocks Information</h3>
        <div class="grid">
            <table border="1" cellpadding="0" cellspacing="0" style="text-align: center; width: 940px;">    
    		<tr valign="top" class="header">
			<th style="padding: 5px;">Other Block #1</th>
			<th style="padding: 5px;">Details</th>
			<th style="padding: 5px;">Other Block #2</th>
			<th style="padding: 5px;">Details</th>
			<th style="padding: 5px;">Other Block #3</th>
			<th style="padding: 5px;">Details</th>
		</tr>
                <tr>
                   <td><input type="text" name="txtOtherBlock1" value="<?= $objDb->getField(0, "other_block_1") ?>" maxlength="11" size="11" class="textbox" /></td> 
                   <td><input type="text" name="txtOtherDetails1" value="<?= $objDb->getField(0, "other_details_1") ?>" maxlength="18" size="18" class="textbox" /></td> 
                   <td><input type="text" name="txtOtherBlock2" value="<?= $objDb->getField(0, "other_block_2") ?>" maxlength="11" size="11" class="textbox" /></td> 
                   <td><input type="text" name="txtOtherDetails2" value="<?= $objDb->getField(0, "other_details_2") ?>" maxlength="18" size="18" class="textbox" /></td> 
                   <td><input type="text" name="txtOtherBlock3" value="<?= $objDb->getField(0, "other_block_3") ?>" maxlength="11" size="11" class="textbox" /></td> 
                   <td><input type="text" name="txtOtherDetails3" value="<?= $objDb->getField(0, "other_details_3") ?>" maxlength="18" size="18" class="textbox" /></td> 
                </tr>
            </table>
            <br/><br/>
            <table border="1" cellpadding="0" cellspacing="0" style="text-align: center;  width: 940px;">    
                <tr valign="top" class="header"><td style="text-align: left;"><b>Comments</b></td></tr>
                <tr><td><textarea name="Comments" cols="128" rows="6"><?= $objDb->getField(0, "comments") ?></textarea></td></tr>
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
        <div style="border: 1px dotted; width: 360px;">
            <div style="padding: 2px;"><span style="color: grey; font-size: 10px;">(Upload Image Against Selected Section)</span></div>
            <div style="display: inline-block; margin-left: 5px; margin-bottom: 5px;">
            <label for="ddFixedSection">
            <select name="ddFixedSection">
                            <option value="">Select A Section</option>
                            <option value="1">Block # 1</option>
                            <option value="2">Block # 2</option>
                            <option value="3">Block # 3</option>
                            <option value="4">Block # 4</option>
                            <option value="5">Block # 5</option>
                            <option value="6">Block # 6</option>
                            <option value="7">Block # 7</option>
                            <option value="8">Block # 8</option>
                            <option value="9">Block # 9</option>
                            <option value="10">Block # 10</option>
           </select></label>
         </div><div style="display: inline-block; margin-left: 20px;">     
	    <input type="file" name="fileFixedSection" id="fileFixedSection" value="" maxlength="200" size="40" class="textbox" />
          </div>
        </div>