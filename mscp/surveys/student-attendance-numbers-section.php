<?
	$sSQL = "SELECT * FROM tbl_survey_student_attendance_numbers WHERE survey_id='$iSurveyId'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	$sSQL = "SELECT * FROM tbl_survey_differently_abled_student_numbers WHERE survey_id='$iSurveyId'";
	$objDb2->query($sSQL);
        
        $sAnswers    = getList("tbl_survey_answers", "question_id", "answer", "survey_id='{$iSurveyId}'");
        $classShiftArr =  explode("\n", $sAnswers[6]);
                
                //else if($sAnswers[75] == ''){}
                //else if($sAnswers[75] == ''){}
                //else if($sAnswers[75] == ''){}
?>                 
<h3>Student Attendance Numbers</h3>
            <div class="grid">
                <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff" style="text-align: center;">  
                <tr valign="top" class="header">
                    <th rowspan="2">Class/Grade</th>
                    <?if (in_array("Morning", $classShiftArr)){?>
                    <th colspan="2">MORNING SHIFT<br/>
                                    Time: <input type="text" name="txtTimeInMorning" value="<?= $objDb2->getField(0, "morning_time_in") ?>" maxlength="5" size="4" class="textbox" /> to<input type="text" name="txtTimeOutMorning" value="<?= $objDb2->getField(0, "morning_time_out") ?>" maxlength="5" size="4" class="textbox" /><br/>
                                    Average number of students attending daily over previous month
                    </th>
                    <?}
                    if (in_array("Evening", $classShiftArr)){?>
                    <th colspan="2">EVENING SHIFT<br/>
                                Time: <input type="text" name="txtTimeInEvening" value="<?= $objDb2->getField(0, "evening_time_in") ?>" maxlength="5" size="4" class="textbox" /> to<input type="text" name="txtTimeOutEvening" value="<?= $objDb2->getField(0, "evening_time_out") ?>" maxlength="5" size="4" class="textbox" /><br/>
                                Average number of students attending daily over previous month
                    </th>
                    <?}?>
                </tr>
                <tr valign="top" class="header">
                 <?if (in_array("Morning", $classShiftArr)){?>    
                    <th>Boys</th>
                    <th>Girls</th>
                <?}
                    if (in_array("Evening", $classShiftArr)){?>
                    <th>Boys</th>
                    <th>Girls</th>
                 <?}?>
                </tr>
                <? 
                $fieldHidden = 'hidden';
                $fieldText   = 'text';
                for($i=0; $i<=12; $i++){ ?>
                <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
                    <?
                        if(($sAnswers[75] == 'Primary') && $i>5)
                            $fieldText = 'hidden';
                        else if(($sAnswers[75] == 'Middle') && $i>8)
                            $fieldText = 'hidden';
                        else if(($sAnswers[75] == 'High School') && $i>10)
                            $fieldText = 'hidden'; 
                        
                        if($sAnswers[75] == 'Primary' && $i<=5)
                            echo '<td>'.$i.'</td>';
                        else if(($sAnswers[75] == 'Middle') && $i<=8)
                            echo '<td>'.$i.'</td>';
                        else if(($sAnswers[75] == 'High School') && $i<=10)
                            echo '<td>'.$i.'</td>';
                        else if(($sAnswers[75] == 'Higher Secondary') && $i<=12)
                            echo '<td>'.$i.'</td>';
                        else
                            echo '<td> </td>';
                    ?>
                    <?if (in_array("Morning", $classShiftArr) && in_array("Evening", $classShiftArr)){?>
                    <td><input type="<?= $fieldText?>" name="txtBoysCountMorning<?= $i?>" value="<?= $objDb->getField($i, "boys_count_morning") ?>" maxlength="5" size="17" class="textbox" /></td>
                    <td><input type="<?= $fieldText?>" name="txtGirlsCountMorning<?= $i?>" value="<?= $objDb->getField($i, "girls_count_morning") ?>" maxlength="5" size="17" class="textbox" /></td>
                    <td><input type="<?= $fieldText?>" name="txtBoysCountEvening<?= $i?>" value="<?= $objDb->getField($i, "boys_count_evening") ?>" maxlength="5" size="17" class="textbox" /></td>
                    <td><input type="<?= $fieldText?>" name="txtGirlsCountEvening<?= $i?>" value="<?= $objDb->getField($i, "girls_count_evening") ?>" maxlength="5" size="17" class="textbox" /></td>
                    
                    <?}else if (in_array("Morning", $classShiftArr) && !in_array("Evening", $classShiftArr)){?>
                    <td><input type="<?= $fieldText?>" name="txtBoysCountMorning<?= $i?>" value="<?= $objDb->getField($i, "boys_count_morning") ?>" maxlength="5" size="17" class="textbox" /></td>
                    <td><input type="<?= $fieldText?>" name="txtGirlsCountMorning<?= $i?>" value="<?= $objDb->getField($i, "girls_count_morning") ?>" maxlength="5" size="17" class="textbox" /></td>
                    <input type="<?= $fieldHidden?>" name="txtBoysCountEvening<?= $i?>" value="<?= $objDb->getField($i, "boys_count_evening") ?>" maxlength="5" size="17" class="textbox" />
                    <input type="<?= $fieldHidden?>" name="txtGirlsCountEvening<?= $i?>" value="<?= $objDb->getField($i, "girls_count_evening") ?>" maxlength="5" size="17" class="textbox" />
                    
                    <?}else if (!in_array("Morning", $classShiftArr) && in_array("Evening", $classShiftArr)){?>
                    <input type="<?= $fieldHidden?>" name="txtBoysCountMorning<?= $i?>" value="<?= $objDb->getField($i, "boys_count_morning") ?>" maxlength="5" size="17" class="textbox" />
                    <input type="<?= $fieldHidden?>" name="txtGirlsCountMorning<?= $i?>" value="<?= $objDb->getField($i, "girls_count_morning") ?>" maxlength="5" size="17" class="textbox" />
                    <td><input type="<?= $fieldText?>" name="txtBoysCountEvening<?= $i?>" value="<?= $objDb->getField($i, "boys_count_evening") ?>" maxlength="5" size="17" class="textbox" /></td>
                    <td><input type="<?= $fieldText?>" name="txtGirlsCountEvening<?= $i?>" value="<?= $objDb->getField($i, "girls_count_evening") ?>" maxlength="5" size="17" class="textbox" /></td>
                    <?} ?>    
                  </tr>
                <?}?>
             </table> 
            </div>

         <div class="br10"></div><div class="br10"></div>     
          <h3>Number of differently abled students regularly attending</h3>
           <div class="grid">
	      <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff"  style="text-align: center;">
                <tr valign="top" class="header">
                    <th>Boys</th>
                    <th>Grades (list)</th>
                    <th>Girls</th>
                    <th>Grades (list)</th>
                </tr>
                <tr class="odd">
                    <td><input type="text" name="txtCountBoys" value="<?= $objDb2->getField(0, "boys_count") ?>" maxlength="5" size="22" class="textbox" /></td>
                    <td><input type="text" name="txtBoysGrades" value="<?= $objDb2->getField(0, "boys_grades") ?>" maxlength="5" size="22" class="textbox" /></td>
                    <td><input type="text" name="txtCountGirls" value="<?= $objDb2->getField(0, "girls_count") ?>" maxlength="5" size="22" class="textbox" /></td>
                    <td><input type="text" name="txtGirlsGrades" value="<?= $objDb2->getField(0, "girls_grades") ?>" maxlength="5" size="22" class="textbox" /></td>
                </tr>
             </table>
           </div>
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
                  <br/>
        <div style="border: 1px dotted; width: 380px;">
            <div style="padding: 2px;"><span style="color: grey; font-size: 10px;">(Upload Image Against Selected Section)</span></div>
            <div style="display: inline-block; margin-left: 5px; margin-bottom: 5px;">
            <label for="ddFixedSection">
            <select name="ddFixedSection">
                            <option value="">Select A Section</option>
                            <option value="0">Grade 0</option>
                            <option value="1">Grade 1</option>
                            <option value="2">Grade 2</option>
                            <option value="3">Grade 3</option>
                            <option value="4">Grade 4</option>
                            <option value="5">Grade 5</option>
                            <option value="6">Grade 6</option>
                            <option value="7">Grade 7</option>
                            <option value="8">Grade 8</option>
                            <option value="9">Grade 9</option>
                            <option value="10">Grade 10</option>
                            <option value="11">Grade 11</option>
                            <option value="12">Grade 12</option>
           </select></label>
         </div><div style="display: inline-block; margin-left: 20px;">     
	    <input type="file" name="fileFixedSection" id="fileFixedSection" value="" maxlength="200" size="40" class="textbox" />
          </div>
        </div>