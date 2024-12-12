<?
        $sSQL = "SELECT ssa.*,s.school_id FROM tbl_sor_section_a ssa,tbl_sors s WHERE s.id=ssa.sor_id AND ssa.sor_id='$iSorId'";
	$objDb->query($sSQL);

	$iCount  = $objDb->getCount( );
        
        $iSchool            = $objDb->getField(0, "school_id");
        $sAttendance        = $objDb->getField(0, "attendance");
        $sBlocks            = $objDb->getField(0, "blocks");
        $sGrades            = $objDb->getField(0, "grades");
        $sClassRooms        = $objDb->getField(0, "class_rooms");
        $sNToilets          = $objDb->getField(0, "normal_toilets");
        $sDToilets          = $objDb->getField(0, "disable_toilets");
        $sClassRamps        = $objDb->getField(0, "classroom_ramps");
        $sToiletRamps       = $objDb->getField(0, "toilet_ramps");
        $sSorAScienceLab    = $objDb->getField(0, "science_lab");
        $sSorAITLab         = $objDb->getField(0, "it_lab");
        $sSorALibrary       = $objDb->getField(0, "library");
        $sSorAExamHall      = $objDb->getField(0, "exam_hall");
        $sSorAPrincOffice   = $objDb->getField(0, "principal_office");
        $sSorAClerkOffice   = $objDb->getField(0, "clerk_office");
        $sSorAStaffRooms    = $objDb->getField(0, "staff_room");
        $sSorAChowkidarHut  = $objDb->getField(0, "chowkidar_hut");
        $sSorACycleStand    = $objDb->getField(0, "cycle_stand");
        $sInfoCorrect       = $objDb->getField(0, "info_correct");
        $sDesignCorrect     = $objDb->getField(0, "design_correct");
        $sComments          = $objDb->getField(0, "comments");
        
        
        $sSQL = "SELECT ss.id, ss.avg_attendance, s.blocks, ss.education_rooms
                    FROM tbl_surveys ss, tbl_schools s 
                    WHERE  s.id = ss.school_id AND s.id='$iSchool'";
        
	$objDb->query($sSQL);

        $iSurveyId           = $objDb->getField(0, "id");
	$iAvgAttendance      = $objDb->getField(0, "avg_attendance");
        $iEducationRooms     = $objDb->getField(0, "education_rooms");
        $iBlocks             = $objDb->getField(0, "blocks");
        $iAdditionalRoomsReq = @ceil($iAvgAttendance/40) - $iEducationRooms;  
        $iToiletRequired     = @ceil($iAvgAttendance/80) - $AvailableToilets; 
        $DAbledStudents      = getDbValue("Sum(boys_count+girls_count)", "tbl_survey_differently_abled_student_numbers", "survey_id='$iSurveyId'");
        $AvailableToilets    = getDbValue("SUM(total)", "tbl_survey_school_block_details", " (room_type_code='TFS' OR room_type_code='TMS' OR room_type_code='US' OR room_type_code='TB') AND survey_id='$iSurveyId'");
        $sAnswers            = getList("tbl_survey_answers", "question_id", "answer", "survey_id='{$iSurveyId}'");
?>            
	<h3>SECTION A - AGREEMENT WITH BASELINE RESULTS</h3>

	<div class="grid"  style="padding: 5px;">
           <table border="1" cellpadding="5" cellspacing="0" style="text-align: center; border-color: #ffffff;">    
    		<tr valign="top" class="header">
			<th style="padding: 5px;" width="255">DETAILS</th>
                        <th style="padding: 5px;">INFORMATION AS PER BASELINE - EXISITING</th>
                        <th style="padding: 5px;">INFORMATION AS PER BASELINE - PROPOSED</th>
                        <th style="padding: 5px;">AGREE WITH BASELINE SUGGESTIONS</th>
		</tr>
		
                <tr class="even">
                    <td><b>TOTAL ATTENDANCE</b></td><td><?=$iAvgAttendance?></td><td>Not Applicable</td><td> <input type="radio" name="attendance" value="Y" <?= ($sAttendance == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="attendance" value="N" <?= ($sAttendance == 'N'?'checked':'')?>> No<br></td>    
                </tr>
		<tr class="odd">
                    <td><b>BLOCKS</b></td><td><?=$iBlocks?></td><td>Not Available</td><td> <input type="radio" name="blocks" value="Y" <?= ($sBlocks == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="blocks" value="N" <?= ($sBlocks == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="even">
                    <td><b>GRADES</b></td><td><?=$sAnswers[75]?></td><td>Not Applicable</td><td> <input type="radio" name="grades" value="Y" <?= ($sGrades == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="grades" value="N" <?= ($sGrades == 'N'?'checked':'')?>> No<br></td>    
                </tr>
		<tr class="odd">
                    <td><b>CLASS ROOMS</b></td><td><?=$iEducationRooms?></td><td><?=($iAdditionalRoomsReq>0 && $iAvgAttendance>40)?$iAdditionalRoomsReq.' New Required':'No'?></td><td> <input type="radio" name="class_rooms" value="Y" <?= ($sClassRooms == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="class_rooms" value="N" <?= ($sClassRooms == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="even">
                    <td><b>TOILETS</b></td><td><?=$AvailableToilets?></td><td><?=($iToiletRequired>0 && $iAvgAttendance>80)?$iToiletRequired.' New Required':'No'?></td><td> <input type="radio" name="normal_toilets" value="Y" <?= ($sNToilets == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="normal_toilets" value="N" <?= ($sNToilets == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="odd">
                    <td><b>TOILETS (DISABLED)</b></td><td><?=($sAnswers[31]=='N'?0:'1')?></td><td><?=($DAbledStudents>0?'1':0)?></td><td> <input type="radio" name="disable_toilets" value="Y" <?= ($sDToilets == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="disable_toilets" value="N" <?= ($sDToilets == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="even">
                    <td><b>WHEELCHAIR ACCESS RAMP TO TOILETS</b></td><td><?=($sAnswers[43]=='Y'?1:0)?></td><td><?=($sAnswers[43]=='Y'?0:1)?></td><td> <input type="radio" name="toilet_ramps" value="Y" <?= ($sToiletRamps == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="toilet_ramps" value="N" <?= ($sToiletRamps == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="odd">
                    <td><b>WHEELCHAIR ACCESS TO CLASSROOMS</b></td><td><?=($sAnswers[42]=='Y'?1:0)?></td><td><?=($sAnswers[42]=='Y'?0:1)?></td><td> <input type="radio" name="classroom_ramps" value="Y" <?= ($sClassRamps == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="classroom_ramps" value="N" <?= ($sClassRamps == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="even">
                    <td><b>SCIENCE LAB</b></td><td>Not Available</td><td>Not Available</td><td> <input type="radio" name="science_lab" value="Y" <?= ($sSorAScienceLab == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="science_lab" value="N" <?= ($sSorAScienceLab == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="odd">
                    <td><b>IT LAB</b></td><td>Not Available</td><td>Not Available</td><td> <input type="radio" name="it_lab" value="Y" <?= ($sSorAITLab == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="it_lab" value="N" <?= ($sSorAITLab == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="even">
                    <td><b>LIBRARY</b></td><td>Not Available</td><td>Not Available</td><td> <input type="radio" name="library" value="Y" <?= ($sSorALibrary == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="library" value="N" <?= ($sSorALibrary == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="odd">
                    <td><b>EXAM HALL</b></td><td>Not Available</td><td>Not Available</td><td> <input type="radio" name="exam_hall" value="Y" <?= ($sSorAExamHall == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="exam_hall" value="N" <?= ($sSorAExamHall == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="even">
                    <td><b>PRINCIPAL OFFICE</b></td><td>Not Available</td><td>Not Available</td><td> <input type="radio" name="principal_office" value="Y" <?= ($sSorAPrincOffice == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="principal_office" value="N" <?= ($sSorAPrincOffice == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="odd">
                    <td><b>CLERK OFFICE</b></td><td>Not Available</td><td>Not Available</td><td> <input type="radio" name="clerk_office" value="Y" <?= ($sSorAClerkOffice == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="clerk_office" value="N" <?= ($sSorAClerkOffice == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="even">
                    <td><b>STAFF ROOM</b></td><td>Not Available</td><td>Not Available</td><td> <input type="radio" name="staff_room" value="Y" <?= ($sSorAStaffRooms == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="staff_room" value="N" <?= ($sSorAStaffRooms == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                <tr class="odd">
                    <td><b>CHOWKIDAR HUT</b></td><td>Not Available</td><td>Not Available</td><td> <input type="radio" name="chowkidar_hut" value="Y" <?= ($sSorAChowkidarHut == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="chowkidar_hut" value="N" <?= ($sSorAChowkidarHut == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                 <tr class="even">
                    <td><b>CYCLE STAND</b></td><td>Not Available</td><td>Not Available</td><td> <input type="radio" name="cycle_stand" value="Y" <?= ($sSorACycleStand == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="cycle_stand" value="N" <?= ($sSorACycleStand == 'N'?'checked':'')?>> No<br></td>    
                </tr>
                
  </table>
            <br/><br/>
            <span>IS ALL INFORMATION AS PER THE BASELINE SURVEY CORRECT? <input type="radio" name="info_correct" value="Y" <?= ($sInfoCorrect == 'Y'?'checked':'')?>> Yes &nbsp;  <input type="radio" name="info_correct" value="N" <?= ($sInfoCorrect == 'N'?'checked':'')?>> No</span><br/>
            <span>IS THE DESIGN (FAST-TRACK) CORRECT AS PER OUR REQUIREMENT? <input type="radio" name="design_correct" value="Y" <?= ($sDesignCorrect == 'Y'?'checked':'')?>> Yes &nbsp;  <input type="radio" name="design_correct" value="N" <?= ($sDesignCorrect == 'N'?'checked':'')?>> No</span><br><br/>
            <span><b>Comments</b><br><textarea style="padding: 10px;" name="comments" rows="4" cols="70"><?=$sComments?></textarea></span>
        </div>
        <div class="br10"></div>
          <div class="br10"></div>

       <h3>SOR Documents</h3>

        <div class="grid" style="padding: 20px;">
            <div style="padding: 2px; font-size: 12px; font-weight: bold;">Upload Files &nbsp;&nbsp;<span style="color: grey; font-size: 9px;">(Jpeg, Tiff, Gif, Bmp, Png & Pdf Only)</span></div>    
                <div style="float: left; width: 33%;">

                   <div style="display: inline-block; margin-left: 5px;">
                    <label for="fileFixedSection">
                        <img src="../mscp/images/icons/upload.png"/>
                    </label>

                    <input name="fileFixedSection[]" id="fileFixedSection" multiple="multiple" type="file" class="textbox" value="" maxlength="200" size="40" />
                </div>
                </div>
            
            <div style="float: left; width: 33%; margin-top:-15px;">
                
<?
	$sSQL = "SELECT id, document FROM tbl_sor_documents WHERE sor_id='$iSorId' AND section_id='$iSectionId'  AND document NOT LIKE '%.pdf%'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	
	if ($iCount > 0)
	{		
?>
        <div style="padding: 2px; font-size: 12px; font-weight: bold;">SOR Files</div>                                   
	<div>
	  <ul>	
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$iPicture = $objDb->getField($i, "id");
                        $sPicture = $objDb->getField($i, "document");
                        $exts = explode('.', $sPicture);
                        $extension = end($exts);
                        if($extension != 'pdf')
                        {
?>
	    <li>
		  <a href="<?= (SITE_URL.SORS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSorId}-")) ?></a>
<?
			if ($sUserRights["Edit"] == "Y" && @strpos($_SERVER['REQUEST_URI'], "edit-") !== FALSE)
			{
?>
		  &nbsp; (<a href="<?= $sCurDir ?>/delete-sor-document.php?SorId=<?= $iSorId ?>&SectionId=<?= $iSectionId ?>&PictureId=<?= $iPicture ?>&Picture=<?= $sPicture ?>"><b>x</b></a>)
<?
			}
?>
		</li>
<?
                        }
		}
?>
	  </ul>
	</div>	
<?
	}
?>
            </div>
    <div style="float: left; width: 33%; margin-top:-15px;">
<?
	$sSQL = "SELECT id, document FROM tbl_sor_documents WHERE sor_id='$iSorId' AND section_id='$iSectionId' AND document LIKE '%.pdf%'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	
	if ($iCount > 0)
	{		
?>
        <div style="padding: 2px; font-size: 12px; font-weight: bold;">Download Pdf Files</div>                           
	<div>
	  <ul>	
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$iPicture = $objDb->getField($i, "id");
                        $sPicture = $objDb->getField($i, "document");
                        $exts = explode('.', $sPicture);
                        $extension = end($exts);
                        if($extension == 'pdf')
                        {
?>
	    <li>
                  <a href="<?= (SITE_URL.SORS_DOC_DIR.$sPicture) ?>" target="_blank"><?= substr($sPicture, strlen("{$iSorId}-")) ?></a>
<?
			if ($sUserRights["Edit"] == "Y" && @strpos($_SERVER['REQUEST_URI'], "edit-") !== FALSE)
			{
?>
		  &nbsp; (<a href="<?= $sCurDir ?>/delete-sor-document.php?SorId=<?= $iSorId ?>&SectionId=<?= $iSectionId ?>&PictureId=<?= $iPicture ?>&Picture=<?= $sPicture ?>"><b>x</b></a>)
<?
			}
?>
		</li>
<?
                        }
		}
?>
	  </ul>
	</div>	
<?
	}
?>
            </div>
            <div class="br10"></div>
</div>
