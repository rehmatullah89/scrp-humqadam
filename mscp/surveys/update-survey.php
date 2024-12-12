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

	$sCode           = IO::strValue("txtCode");
	$sEnumerator     = IO::strValue("txtEnumerator");
	$sDate           = IO::strValue("txtDate");
	$sOperational    = ((IO::strValue("ddOperational") == "N") ? IO::strValue("ddNonOperational") : "Y");
	$sPefProgramme   = IO::strValue("ddPefProgramme");	
	$sLandAvailable  = IO::strValue("ddLandAvailable");
	$sLandDispute    = ((IO::strValue("ddLandDispute") == "Y") ? IO::strValue("ddDispute") : "N");
	$sOtherFunding   = IO::strValue("ddOtherFunding");
	$iClassRooms     = IO::intValue("txtClassRooms");
	$iEducationRooms = IO::intValue("txtEducationRooms");
	$sShelterLess    = IO::strValue("ddShelterLess");
	$sMultiGrading   = IO::strValue("ddMultiGrading");
	$iAvgAttendance  = IO::intValue("txtAvgAttendance");
	$sPreSelection   = IO::strValue("ddPreSelection");
	$sComments       = IO::strValue("txtComments");
	$bError          = true;
	

	if ($sCode == "" || $sEnumerator == "" || $sDate == "" || $sOperational == "" || $iClassRooms < 0 || $iEducationRooms < 0 || $iEducationRooms > $iClassRooms)
		$_SESSION["Flag"] = "INCOMPLETE_FORM";
	
	if ($_SESSION["Flag"] == "")
	{
		$iSchool = getDbValue("id", "tbl_schools", "`code`='$sCode'");
		
		if ($iSchool == 0)
			$_SESSION["Flag"] = "INVALID_EMIS_CODE";
	}
	
	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_surveys WHERE school_id='$iSchool' AND `date`='$sDate' AND id!='$iSurveyId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SURVEY_EXISTS";
	}
	

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT app, status, completed FROM tbl_surveys WHERE id='$iSurveyId'";
		$objDb->query($sSQL);
		
		$sApp       = $objDb->getField(0, "app");
		$sStatus    = $objDb->getField(0, "status");
		$sCompleted = $objDb->getField(0, "completed");

			
		$iDistrict  = getDbValue("district_id", "tbl_schools", "id='$iSchool'");
		$iProvince  = getDbValue("province_id", "tbl_schools", "id='$iSchool'");
		$sQualified = "Y";
	
		if ($sOperational != "Y" || ($iProvince == 1 && $sPefProgramme == "Y") || ($sLandAvailable == "N" && $iProvince == 1) || $sLandDispute != "N" || $sOtherFunding == "Y" || ($iAvgAttendance < 100 && $iProvince == 2) || $sPreSelection == "N")
		{
			$sStatus    = "C";
			$sQualified = "N";
			$sCompleted = "Y";
		}
		
		
		$objDb->execute("BEGIN");
		
		$sSQL = "UPDATE tbl_surveys SET school_id       = '$iSchool',
										district_id     = '$iDistrict',
										enumerator      = '$sEnumerator',
										`date`          = '$sDate',											 
										operational     = '$sOperational',
										pef_programme   = '$sPefProgramme',
										land_available  = '$sLandAvailable',
										land_dispute    = '$sLandDispute',
										other_funding   = '$sOtherFunding',
										class_rooms     = '$iClassRooms',
										education_rooms = '$iEducationRooms',
										shelter_less    = '$sShelterLess',
										multi_grading   = '$sMultiGrading',
										avg_attendance  = '$iAvgAttendance',
										pre_selection   = '$sPreSelection',
										comments        = '$sComments',
										qualified       = '$sQualified',
										completed       = '$sCompleted',
										status          = '$sStatus',
										modified_by     = '{$_SESSION['AdminId']}',
										modified_at     = NOW( )
	             WHERE id='$iSurveyId'";
		$bFlag = $objDb->execute($sSQL);
		
		if ($bFlag == true && $sQualified == "Y")
		{
			$sSQL  = "INSERT INTO tbl_survey_details (survey_id, section_id, `status`, created_by, created_at, modified_by, modified_at)
			                              (SELECT '$iSurveyId', id, 'I', '{$_SESSION['AdminId']}', NOW( ), '{$_SESSION['AdminId']}', NOW( )
 										   FROM tbl_survey_sections
										   WHERE id NOT IN (SELECT section_id FROM tbl_survey_details WHERE survey_id='$iSurveyId'))";
			$bFlag = $objDb->execute($sSQL);	
		}
		
		if ($bFlag == true)
		{
			$sSQL  = "UPDATE tbl_schools SET qualified='$sQualified' WHERE id='$iSchool'";
			$bFlag = $objDb->execute($sSQL);
		}		
		
		if ($bFlag == true && $sQualified == "N")
		{
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_details WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_answers WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_declaration WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_differently_abled_student_numbers WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_school_block_details WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_school_blocks WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}			
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_school_facilities WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}	

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_student_attendance_numbers WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}	
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_students_enrollment WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}	
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_teacher_numbers WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}		
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_school_other_blocks WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_checklist WHERE survey_id='$iSurveyId'";
				$bFlag = $objDb->execute($sSQL);
			}
		}
		
		if ($bFlag == true)
		{
			for ($i = 1; $i <= 10; $i ++)
			{
				if ($_FILES["filePicture{$i}"]['name'] != "")
				{
					$sPicture = ($iSurveyId."-Q0".$i."-".IO::getFileName($_FILES["filePicture{$i}"]['name']));

					if (@move_uploaded_file($_FILES["filePicture{$i}"]['tmp_name'], ($sRootDir.SURVEYS_DOC_DIR.$sPicture)))
					{
						$iPicture = getNextId("tbl_survey_pictures");
						
						
						$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																	  survey_id   = '$iSurveyId',
																	  section_id  = '0',
																	  question_id = '$i',
																	  picture     = '$sPicture'";
						$bFlag = $objDb->execute($sSQL);
					}
				}
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
				
				
				$sSQL = "SELECT site_plan_pdf, structure_pdf, drawing, structure FROM tbl_survey_checklist WHERE survey_id='$iSurveyId'";
				$objDb->query($sSQL);
				
				$sDrawingPdf   = $objDb->getField(0, "site_plan_pdf");
				$sDrawingDwg   = $objDb->getField(0, "drawing");
				$sStructurePdf = $objDb->getField(0, "structure_pdf");
				$sStructureDwg = $objDb->getField(0, "structure");


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
				
				if ($sQualified == "N")
				{
					$sDrawingPdf   = "";
					$sDrawingDwg   = "";
					$sStructurePdf = "";
					$sStructureDwg = "";
				}


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
						
			
			$sSchool   = getDbValue("name", "tbl_schools", "id='$iSchool'");
			$sDistrict = getDbValue("name", "tbl_districts", "id=(SELECT district_id FROM tbl_schools WHERE id='$iSchool')");


			$sSQL = "SELECT created_at, modified_at,
							(SELECT name FROM tbl_admins WHERE id=tbl_surveys.created_by) AS _CreatedBy,
							(SELECT name FROM tbl_admins WHERE id=tbl_surveys.modified_by) AS _ModifiedBy
					 FROM tbl_surveys
					 WHERE id='$iSurveyId'";
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

		sFields[0] = "<?= @htmlentities($sSchool) ?>";
		sFields[1] = "<?= @htmlentities($sCode) ?>";
		sFields[2] = "<?= @htmlentities($sDistrict) ?>";
		sFields[3] = "<?= @htmlentities($sEnumerator) ?>";
		sFields[4] = "<?= formatDate($sDate, $_SESSION['DateFormat']) ?>";
		sFields[5] = "<?= (($sApp == 'Y' && $sStatus == 'I') ? "Syncing" : "Synced") ?>";
		sFields[6] = "<?= (($sCompleted == "Y") ? "Completed" : "In-Complete") ?>";
		sFields[7] = '<img class="icon details" id="<?= $iSurveyId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" /> ';
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[7] = (sFields[7] + '<img class="icnEdit" id="<?= $iSurveyId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
				if ($sQualified == "Y")
				{
?>
		sFields[7] = (sFields[7] + '<img class="icon icnSurvey" id="<?= $iSurveyId ?>" src="images/icons/stats.gif" alt="Survey Details" title="Survey Details" rel="<?= @htmlentities($sSchool) ?>" /> ');
<?
				}
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sFields[7] = (sFields[7] + '<img class="icnDelete" id="<?= $iSurveyId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}
?>
		sFields[7] = (sFields[7] + '<img class="icnView" id="<?= $iSurveyId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');
<?
			if ($sStatus == "C")
			{
?>
		sFields[7] = (sFields[7] + '<a href="<?= $sCurDir ?>/export-survey.php?Id=<?= $iSurveyId ?>"><img class="icnPdf" src="images/icons/pdf.png" alt="Export PDF" title="Export PDF" /></a>');
<?
			}
?>

		parent.updateRecord(<?= $iSurveyId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Survey Record has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
		{
			$objDb->execute("ROLLBACK");
			
			$_SESSION["Flag"] = "DB_ERROR";			
		}
	}
?>