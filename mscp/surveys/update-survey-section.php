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

	$_SESSION["Flag"] = "";
	$bError           = true;
	$sStatus          = "C";
	$sCompleted       = "Y";
	$sNewPictures     = array( );


	$objDb->execute("BEGIN");
	
	
	$sSQL = "UPDATE tbl_survey_details SET status      = '$sStatus',
										   modified_by = '{$_SESSION['AdminId']}',
										   modified_at = NOW( )
			 WHERE survey_id='$iSurveyId' AND section_id='$iSectionId'";
	$bFlag = $objDb->execute($sSQL);

	if ($bFlag == true)
	{
		if (getDbValue("type", "tbl_survey_sections", "id='$iSectionId'") == "V")
			@include("save-survey-dynamic-section.php");
		
		else if ($iSectionId == 3)
			@include("save-survey-teacher-section.php");			

		else if ($iSectionId == 4)
			@include("save-student-enrollment-section.php");

		else if ($iSectionId == 5)
			@include("save-student-attendance-numbers-section.php");

		else if ($iSectionId == 13)
			@include("save-facility-roomcount-condition-section.php");

		else if ($iSectionId == 14)
			@include("save-school-facilities-section.php");
		
		else if ($iSectionId == 15)
			@include("save-survey-checklist.php");
		
		else if ($iSectionId == 16)
			@include("save-declaration-section.php");
	}
	
	if ($bFlag == true)
	{
		$iInCompletedSections = (int)getDbValue("COUNT(1)", "tbl_survey_details", "status!='C' AND survey_id='$iSurveyId'");

		
		$sSQL = "SELECT site_plan_pdf, structure_pdf, drawing, structure FROM tbl_survey_checklist WHERE survey_id='$iSurveyId'";
		$objDb->query($sSQL);
		
		$sDrawingPdf   = $objDb->getField(0, "site_plan_pdf");
		$sDrawingDwg   = $objDb->getField(0, "drawing");
		$sStructurePdf = $objDb->getField(0, "structure_pdf");
		$sStructureDwg = $objDb->getField(0, "structure");

		
		if ($sDrawingPdf == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sDrawingPdf) ||
		    $sDrawingDwg == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sDrawingDwg) ||
			$sStructurePdf == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sStructurePdf) ||
			$sStructureDwg == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sStructureDwg) ||
			$iInCompletedSections > 0)
			$sCompleted = "N";
			
		if ($sDrawingPdf == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sDrawingPdf) ||
		    $sDrawingDwg == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sDrawingDwg) ||
			$sStructurePdf == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sStructurePdf) ||
			$sStructureDwg == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sStructureDwg) )
			$sStatus = "I";
			
			
		$sSQL = "UPDATE tbl_surveys SET completed   = '$sCompleted',
									    modified_by = '{$_SESSION['AdminId']}',
									    modified_at = NOW( )
				 WHERE id='$iSurveyId'";
		$bFlag = $objDb->execute($sSQL);
		
		if ($bFlag == true && $iSectionId == 15)
		{
			$sSQL = "UPDATE tbl_survey_details SET status      = '$sStatus',
												   modified_by = '{$_SESSION['AdminId']}',
												   modified_at = NOW( )
					 WHERE survey_id='$iSurveyId' AND section_id='$iSectionId'";
			$bFlag = $objDb->execute($sSQL);
		}
	}
	
	
	if ($bFlag == true)				 
	{
		$objDb->execute("COMMIT");
		
				
		if ($sCompleted == "Y")
		{
			$sSQL = "SELECT site_title, general_name, general_email, date_format, time_format FROM tbl_settings WHERE id='1'";
			$objDb->query($sSQL);

			$sSiteTitle   = $objDb->getField(0, "site_title");
			$sSenderName  = $objDb->getField(0, "general_name");
			$sSenderEmail = $objDb->getField(0, "general_email");

			
			$sSQL = "SELECT subject, message, status FROM tbl_email_templates WHERE id='5'";
			$objDb->query($sSQL);

			$sSubject = $objDb->getField(0, "subject");
			$sBody    = $objDb->getField(0, "message");


			$sSQL = "SELECT s.code, s.name, s.code, s.province_id, s.district_id, 
							bs.qualified, bs.enumerator, bs.status, bs.comments, bs.class_rooms, bs.education_rooms, bs.avg_attendance
					 FROM tbl_surveys bs, tbl_schools s
					 WHERE  bs.school_id=s.id AND bs.id='$iSurveyId'";
			$objDb->query($sSQL);
			
			$iProvince = $objDb->getField(0, "province_id");
			$iDistrict = $objDb->getField(0, "district_id");
			

			$sDownloadPdf     = "<a href='http://www.3-tree.com/scrp/mscp/surveys/export-survey.php?Id={$iSurveyId}' target='_blank'>Download Basic Baseline Survey (PDF Format)</a>";
			$sDrawingPdf      = ("<a href='".(SITE_URL.SURVEYS_DOC_DIR.$sDrawingPdf)."' >Download Site Plan (PDF Format)</a>");
			$sDrawingDwg      = ("<a href='".(SITE_URL.SURVEYS_DOC_DIR.$sDrawingDwg)."' >Download Site Plan (AutoCAD Format)</a>");
			$sStructurePdf    = ("<a href='".(SITE_URL.SURVEYS_DOC_DIR.$sStructurePdf)."' >Download Proposed Structure (PDF Format)</a>");
			$sStructureDwg    = ("<a href='".(SITE_URL.SURVEYS_DOC_DIR.$sStructureDwg)."' >Download Proposed Structure (AutoCAD Format)</a>");
			
			$iAdditionalRooms = (@ceil($objDb->getField(0, "bs.avg_attendance") / 40) - $objDb->getField(0, "bs.education_rooms"));
			$sFirm            = (($iProvince == 1) ? 'Innovative Development Strategies (Pvt) Ltd' : 'Associates in Development (Pvt) Ltd');


			$sSubject = @str_replace("{SITE_TITLE}", $sSiteTitle, $sSubject);
			$sSubject = @str_replace("{EMIS_CODE}", $objDb->getField(0, "s.code"), $sSubject);
			$sSubject = @str_replace("{SCHOOL}", $objDb->getField(0, "s.name"), $sSubject);
			$sSubject = @str_replace("{DISTRICT_NAME}", getDbValue("name", "tbl_districts", "id='$iDistrict'"), $sSubject);
			
			$sBody = @str_replace("{SURVEY_ID}", str_pad($iSurveyId, '0', 5, STR_PAD_LEFT), $sBody);
			$sBody = @str_replace("{SCHOOL}", $objDb->getField(0, "s.name"), $sBody);
			$sBody = @str_replace("{EMIS_CODE}", $objDb->getField(0, "s.code"), $sBody);
			$sBody = @str_replace("{PROVINCE_NAME}", getDbValue("name", "tbl_provinces", "id='$iProvince'"), $sBody);
			$sBody = @str_replace("{DISTRICT_NAME}", getDbValue("name", "tbl_districts", "id='$iDistrict'"), $sBody);
			$sBody = @str_replace("{ENUMERATOR}",  $objDb->getField(0, "bs.enumerator"), $sBody);
			$sBody = @str_replace("{CONSULTING_FIRM}", $sFirm, $sBody);
			$sBody = @str_replace("{ASSESSMENT_RESULT}", ($objDb->getField(0, "bs.qualified") == 'Y' ? 'Qualified' : 'Disqualified'), $sBody);
			$sBody = @str_replace("{TOTAL_ROOMS}", $objDb->getField(0, "bs.class_rooms"), $sBody);
			$sBody = @str_replace("{EDUCATION_ROOMS}", $objDb->getField(0, "bs.education_rooms"), $sBody);
			$sBody = @str_replace("{AVG_ATTENDANCE}", $objDb->getField(0, "bs.avg_attendance"), $sBody);
			$sBody = @str_replace("{REQUIRED_ROOMS}", (($iAdditionalRooms > 0 && $objDb->getField(0, "bs.avg_attendance") > 40) ? $iAdditionalRooms : 'No'), $sBody);
			$sBody = @str_replace("{SURVEY_STATUS}", "Completed", $sBody);
			$sBody = @str_replace("{SITE_TITLE}", $sSiteTitle, $sBody);
			$sBody = @str_replace("{DOWNLOAD_BASE_LINE_PDF}", $sDownloadPdf, $sBody);
			$sBody = @str_replace("{SITE_PLAN_PDF}", $sDrawingPdf, $sBody);
			$sBody = @str_replace("{SITE_PLAN_AC}", $sDrawingDwg, $sBody);
			$sBody = @str_replace("{STRUCTURE_PDF}", $sStructurePdf, $sBody);
			$sBody = @str_replace("{STRUCTURE_AC}", $sStructureDwg, $sBody);
			$sBody = @str_replace("{SURVEY_COMMENTS}", nl2br($objDb->getField(0, "bs.comments")), $sBody);


			
			$objEmail = new PHPMailer( );

			$objEmail->Subject = $sSubject;
			$objEmail->MsgHTML($sBody);
			$objEmail->SetFrom($sSenderEmail, $sSenderName);
			
			$objEmail->AddAddress("omer@3-tree.com", "Omer Rauf");
			$objEmail->AddAddress("Nimra.Tariq@humqadam.pk", "Nimra Tariq");
			$objEmail->AddAddress("Samantha.Passmore@humqadam.pk", "Samantha Passmore");
			$objEmail->AddAddress("Muhammad.zubair2@humqadam.pk", "Muhammad Zubair");
			$objEmail->AddAddress("Saleha.sundas@humqadam.pk", "Saleha Sundas");
			
			if (@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE)
					$objEmail->Send( );
		}


		$sSQL = "SELECT created_at, modified_at,
						(SELECT name FROM tbl_admins WHERE id=tbl_survey_details.created_by) AS _CreatedBy,
						(SELECT name FROM tbl_admins WHERE id=tbl_survey_details.modified_by) AS _ModifiedBy
				 FROM tbl_survey_details
				 WHERE survey_id='$iSurveyId' AND section_id='$iSectionId'";
		$objDb->query($sSQL);

		$sCreatedAt  = $objDb->getField(0, "created_at");
		$sCreatedBy  = $objDb->getField(0, "_CreatedBy");
		$sModifiedAt = $objDb->getField(0, "modified_at");
		$sModifiedBy = $objDb->getField(0, "_ModifiedBy");


		$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

		if ($sCreatedAt != $sModifiedAt)
			$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= (($sStatus == "C") ? "Completed" : "In-Complete") ?>";
		sFields[1] = '<img class="icon details" survey="<?= $iSurveyId ?>" section="<?= $iSectionId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" /> ';
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[1] = (sFields[1] + '<img class="icnEdit" survey="<?= $iSurveyId ?>" section="<?= $iSectionId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}
?>
		sFields[1] = (sFields[1] + '<img class="icnView" survey="<?= $iSurveyId ?>" section="<?= $iSectionId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

		parent.updateSectionRecord(<?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#PageMsg", "success", "The selected Survey Section Record has been Updated successfully.");
	-->
	</script>
<?
		exit( );
	}

	else
	{
		$objDb->execute("ROLLBACK");
		
		
		foreach ($sNewPictures as $sPicture)
		{
			@unlink($sRootDir.SURVEYS_DOC_DIR.$sPicture);				
		}
		
		
		$_SESSION["Flag"] = "DB_ERROR";			
	}
?>