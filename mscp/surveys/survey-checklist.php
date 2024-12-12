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
	
	$sSQL = "SELECT * FROM tbl_survey_checklist WHERE survey_id='$iSurveyId'";
	$objDb->query($sSQL);

	$sSitePlan          = $objDb->getField(0, "site_plan");
	$sSitePlanPdf       = $objDb->getField(0, "site_plan_pdf");
	$sStructurePdf      = $objDb->getField(0, "structure_pdf");
	$sDrawing           = $objDb->getField(0, "drawing");
	$sStructure         = $objDb->getField(0, "structure");
	$fTotalArea         = $objDb->getField(0, "total_area");
	$iStudents          = $objDb->getField(0, "students");
	$sNorthArrow        = $objDb->getField(0, "north_arrow");
	$sClassRooms        = $objDb->getField(0, "class_rooms");
	$sClassRoomSize     = $objDb->getField(0, "class_room_size");
	$sToilets           = $objDb->getField(0, "toilets");
	$sPlaygrounds       = $objDb->getField(0, "playgrounds");
	$sWaterTanks        = $objDb->getField(0, "water_tanks");
	$sSepticTanks       = $objDb->getField(0, "spetic_tanks");
	$sBoundaryWall      = $objDb->getField(0, "boundary_wall");
	$sDrainage          = $objDb->getField(0, "drainage");
	$sNewDevelopment    = $objDb->getField(0, "new_development");
	$sMeasurements      = $objDb->getField(0, "measurements");
	$sSlopes            = $objDb->getField(0, "slopes");
	$sTreesSmaller      = $objDb->getField(0, "trees_smaller");
	$sTreesLarger       = $objDb->getField(0, "trees_larger");
	$sElectricity1Phase = $objDb->getField(0, "electricity_1_phase");
	$sElectricity3Phase = $objDb->getField(0, "electricity_3_phase");
?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr valign="top">
	    <td width="50%">
		  <label for="fileSitePlan">Site Plan <span><?= (($sSitePlan == "") ? '' : ('(<a href="'.(SITE_URL.SURVEYS_DOC_DIR.$sSitePlan).'" class="colorbox">'.substr($sSitePlan, strlen("{$iSurveyId}-{$iSectionId}-sp-")).'</a>)')) ?></span></label>

		  <div>
		    <input type="hidden" name="SitePlan" value="<?= $sSitePlan ?>" />
		    <input type="file" name="fileSitePlan" id="fileSitePlan" value="" maxlength="200" size="40" class="textbox" />
		  </div>
<?
	$sSQL = "SELECT id, picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='$iSectionId' AND question_id='0'";
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
			$sPicture = $objDb->getField($i, "picture");
?>
	    <li>
		  <a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-")) ?></a>
<?
			if ($sUserRights["Edit"] == "Y" && @strpos($_SERVER['REQUEST_URI'], "edit-") !== FALSE)
			{
?>
		  &nbsp; (<a href="<?= $sCurDir ?>/delete-survey-picture.php?SurveyId=<?= $iSurveyId ?>&SectionId=<?= $iSectionId ?>&QuestionId=0&PictureId=<?= $iPicture ?>&Picture=<?= $sPicture ?>"><b>x</b></a>)
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
                  
		  <label for="fileDrawing">Drawing <span><?= (($sDrawing == "") ? '<span>(AutoCAD 2004 Format)</span>' : ('(<a href="'.(SITE_URL.SURVEYS_DOC_DIR.$sDrawing).'" target="_blank">'.substr($sDrawing, strlen("{$iSurveyId}-{$iSectionId}-dn-")).'</a>)')) ?></span></label>

		  <div>
		    <input type="hidden" name="Drawing" value="<?= $sDrawing ?>" />
		    <input type="file" name="fileDrawing" id="fileDrawing" value="" maxlength="200" size="40" class="textbox" />
		  </div>
		  
		  <div class="br10"></div>

		  <label for="fileSitePlanPdf">Drawing (PDF) <span><?= (($sSitePlanPdf == "") ? '<span>(PDF File Format)</span>' : ('(<a href="'.(SITE_URL.SURVEYS_DOC_DIR.$sSitePlanPdf).'" target="_blank">'.substr($sSitePlanPdf, strlen("{$iSurveyId}-{$iSectionId}-pdf-")).'</a>)')) ?></span></label>

		  <div>
		    <input type="hidden" name="SitePlanPdf" value="<?= $sSitePlanPdf ?>" />
		    <input type="file" name="fileSitePlanPdf" id="fileSitePlanPdf" value="" maxlength="200" size="40" class="textbox" />
		  </div>

		  <div class="br10"></div>

		  <label for="fileStructure">Proposed Structure <span><?= (($sStructure == "") ? '<span>(AutoCAD 2004 Format)</span>' : ('(<a href="'.(SITE_URL.SURVEYS_DOC_DIR.$sStructure).'" target="_blank">'.substr($sStructure, strlen("{$iSurveyId}-{$iSectionId}-ps-")).'</a>)')) ?></span></label>

		  <div>
		    <input type="hidden" name="Structure" value="<?= $sStructure ?>" />
		    <input type="file" name="fileStructure" id="fileStructure" value="" maxlength="200" size="40" class="textbox" />
		  </div>
		  
		  <div class="br10"></div>

		  <label for="fileStructurePdf">Proposed Structure (PDF) <span><?= (($sStructurePdf == "") ? '<span>(PDF File Format)</span>' : ('(<a href="'.(SITE_URL.SURVEYS_DOC_DIR.$sStructurePdf).'" target="_blank">'.substr($sStructurePdf, strlen("{$iSurveyId}-{$iSectionId}-spdf-")).'</a>)')) ?></span></label>

		  <div>
		    <input type="hidden" name="StructurePdf" value="<?= $sStructurePdf ?>" />
		    <input type="file" name="fileStructurePdf" id="fileStructurePdf" value="" maxlength="200" size="40" class="textbox" />
		  </div>

		  <br />
		  You can download the folowing Plugin to view AutoCAD Files in browser: <a href="https://a360.autodesk.com/viewer" target="_blank">https://a360.autodesk.com/viewer</a><br />
		</td>
		
		<td width="50%">
		  <label for="txtTotalArea">Total Area <span>(sqft)</span></label>
		  <div><input type="text" name="txtTotalArea" id="txtTotalArea" value="<?= $fTotalArea ?>" maxlength="10" size="10" class="textbox" /></div>

		  <div class="br10"></div>
		
		  <label for="txtStudents">No of Students</label>
		  <div><input type="text" name="txtStudents" id="txtStudents" value="<?= $iStudents ?>" maxlength="10" size="10" class="textbox" /></div>
		</td>
	  </tr>
	</table>	

	<br />
	<br />
	<h3>Tick when Marked on Drawing</h3>
	<br />
	
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr valign="top">
	    <td width="50%">
		  <label for="cbNorthArrow">
		    <input type="checkbox" name="cbNorthArrow" id="cbNorthArrow" value="Y" <?= (($sNorthArrow == "Y") ? "checked" : "") ?> />
		    North Arrow
		  </label>
		
		  <div class="br10"></div>
		
		  <label for="cbClassRooms">
		    <input type="checkbox" name="cbClassRooms" id="cbClassRooms" value="Y" <?= (($sClassRooms == "Y") ? "checked" : "") ?> />
		    No. of Classrooms in Block
		  </label>
		
		  <div class="br10"></div>
		
		  <label for="cbClassRoomSize">
		    <input type="checkbox" name="cbClassRoomSize" id="cbClassRoomSize" value="Y" <?= (($sClassRoomSize == "Y") ? "checked" : "") ?> />
		    Average Classroom Size/ Block Size
		  </label>
		
		  <div class="br10"></div>
		
		  <label for="cbToilets">
		    <input type="checkbox" name="cbToilets" id="cbToilets" value="Y" <?= (($sToilets == "Y") ? "checked" : "") ?> />
		    Toilets
		  </label>
		
		  <div class="br10"></div>
		
		  <label for="cbPlaygrounds">
		    <input type="checkbox" name="cbPlaygrounds" id="cbPlaygrounds" value="Y" <?= (($sPlaygrounds == "Y") ? "checked" : "") ?> />
		    Playgrounds
		  </label>
		
		  <div class="br10"></div>
		
		  <label for="cbWaterTanks">
		    <input type="checkbox" name="cbWaterTanks" id="cbWaterTanks" value="Y" <?= (($sWaterTanks == "Y") ? "checked" : "") ?> />
		    Water Tanks
		  </label>
		
		  <div class="br10"></div>

		  <label for="cbSepticTanks">
		    <input type="checkbox" name="cbSepticTanks" id="cbSepticTanks" value="Y" <?= (($sSepticTanks == "Y") ? "checked" : "") ?> />
		    Septic Tanks
		  </label>
		
		  <div class="br10"></div>
		
		  <label for="cbBoundaryWall">
		    <input type="checkbox" name="cbBoundaryWall" id="cbBoundaryWall" value="Y" <?= (($sBoundaryWall == "Y") ? "checked" : "") ?> />
		    Boundary Wall (and damaged area)
		  </label>
		</td>
		
	    <td width="50%">
		  <label for="cbDrainage">
		    <input type="checkbox" name="cbDrainage" id="cbDrainage" value="Y" <?= (($sDrainage == "Y") ? "checked" : "") ?> />
		    Drainage
		  </label>
		
		  <div class="br10"></div>

		  <label for="cbNewDevelopment">
		    <input type="checkbox" name="cbNewDevelopment" id="cbNewDevelopment" value="Y" <?= (($sNewDevelopment == "Y") ? "checked" : "") ?> />
		    Location of any New Development
		  </label>
		
		  <div class="br10"></div>
		
		  <label for="cbMeasurements">
		    <input type="checkbox" name="cbMeasurements" id="cbMeasurements" value="Y" <?= (($sMeasurements == "Y") ? "checked" : "") ?> />
		    Measurements
		  </label>
		
		  <div class="br10"></div>

		  <label for="cbSlopes">
		    <input type="checkbox" name="cbSlopes" id="cbSlopes" value="Y" <?= (($sSlopes == "Y") ? "checked" : "") ?> />
		    Slopes
		  </label>
		
		  <div class="br10"></div>
		
		  <label for="cbTreesSmaller">
 		    <input type="checkbox" name="cbTreesSmaller" id="cbTreesSmaller" value="Y" <?= (($sTreesSmaller == "Y") ? "checked" : "") ?> />
		    Trees < 3 inch
		  </label>
		
		  <div class="br10"></div>
		
		  <label for="cbTreesLarger">
		    <input type="checkbox" name="cbTreesLarger" id="cbTreesLarger" value="Y" <?= (($sTreesLarger == "Y") ? "checked" : "") ?> />
		    Trees > 3 inch
		  </label>
		
		  <div class="br10"></div>
		
		  <label for="cbElectricity1Phase">
		    <input type="checkbox" name="cbElectricity1Phase" id="cbElectricity1Phase" value="Y" <?= (($sElectricity1Phase == "Y") ? "checked" : "") ?> />
		    Electricity 1-Phase
		  </label>
		
		  <div class="br10"></div>
		
		  <label for="cbElectricity3Phase">
		    <input type="checkbox" name="cbElectricity3Phase" id="cbElectricity3Phase" value="Y" <?= (($sElectricity3Phase == "Y") ? "checked" : "") ?> />
		    Electricity 3-Phase
		  </label>		
		</td>
	  </tr>
	</table>
