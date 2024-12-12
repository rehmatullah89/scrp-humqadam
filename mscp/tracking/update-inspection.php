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

	$iUser           = IO::intValue("ddUser");
	$iDistrict       = IO::intValue("ddDistrict");
	$iSchool         = IO::intValue("ddSchool");
	$iBlock          = IO::intValue("ddBlock");
	$iStage          = IO::intValue("ddStage");
	$sTitle          = IO::strValue("txtTitle");
	$sDate           = IO::strValue("txtDate");
	$sDetails        = IO::strValue("txtDetails");
	$sStatus         = IO::strValue("ddStatus");
	$iReason         = IO::intValue("ddReason");
	$sStageCompleted = IO::strValue("ddCompleted");
	$sComments       = IO::strValue("txtComments");
	$sReInspection   = IO::strValue("txtReInspection");
	$sOldPicture     = IO::strValue("Picture");
	$sOldDocument    = IO::strValue("Document");
	$iFiles          = IO::intValue("Files_count");

	$sReInspection   = (($sReInspection == "" || $sStatus != "R") ? "0000-00-00" : $sReInspection);
	$sPicture        =  "";
	$sDocument       = "";
	$sPictureSql     = "";
	$sDocumentSql    = "";
	$sFiles          = array( );
	$sOldCompleted   = getDbValue("stage_completed", "tbl_inspections", "id='$iInspectionId'");

	if ($iUser == 0 || $iDistrict == 0 || $iSchool == 0 || $iBlock < 0 || $iBlock > getDbValue("blocks", "tbl_schools", "id='$iSchool'") || $iStage == 0 || $sTitle == "" || $sDate == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iInspectionId."-".IO::getFileName($_FILES['filePicture']['name']));

			if (@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.INSPECTIONS_IMG_DIR.$sPicture)))
				$sPictureSql = ", picture='$sPicture'";
		}


		if ($_FILES['fileDocument']['name'] != "")
		{
			$sDocument = ($iInspectionId."-".IO::getFileName($_FILES['fileDocument']['name']));

			if (@move_uploaded_file($_FILES['fileDocument']['tmp_name'], ($sRootDir.INSPECTIONS_DOC_DIR.$sDocument)))
				$sDocumentSql = ", file='$sDocument'";
		}



		$objDb->execute("BEGIN");

		$sSQL = "UPDATE tbl_inspections SET admin_id          = '$iUser',
										    district_id       = '$iDistrict',
										    school_id         = '$iSchool',
											block             = '$iBlock',
										    stage_id          = '$iStage',
										    title             = '$sTitle',
		                                    `date`            = '$sDate',
										    details           = '$sDetails',
		                                    status            = '$sStatus',
										    failure_reason_id = '$iReason',
										    failure_reason    = '$sComments',
		                                    stage_completed   = '$sStageCompleted',
		                                    re_inspection     = '$sReInspection',
		                                    modified_by       = '{$_SESSION['AdminId']}',
		                                    modified_at       = NOW( )
		                                    $sPictureSql
		                                    $sDocumentSql
		          WHERE id='$iInspectionId'";
		$bFlag = $objDb->execute($sSQL);
		
		if ($bFlag == true)
		{
			$sSQL = "SELECT name, code, work_type, storey_type, design_type, district_id, province_id FROM tbl_schools WHERE id='$iSchool'";
			$objDb->query($sSQL);

			$sSchool     = $objDb->getField(0, "name");
			$sCode       = $objDb->getField(0, "code");
			$sWorkType   = $objDb->getField(0, "work_type");			
			$sStoreyType = $objDb->getField(0, "storey_type");
			$sDesignType = $objDb->getField(0, "design_type");
			$iDistrict   = $objDb->getField(0, "district_id");
			$iProvince   = $objDb->getField(0, "province_id");

			
			$sSchoolType        = (($sDesignType == "B") ? "B" : $sStoreyType);
			$iMileStoneStage    = getDbValue("id", "tbl_stages", "parent_id='0' AND `type`='$sSchoolType'", "position DESC");
			$iStagePosition     = getDbValue("position", "tbl_stages", "id='$iStage'");
			$iMilestonePosition = getDbValue("position", "tbl_stages", "id='$iMileStoneStage'");
			
			if ($iStagePosition > $iMilestonePosition)
			{			
				$sSQL  = "UPDATE tbl_schools SET adopted='Y', qualified='Y' WHERE id='$iSchool'";
				$bFlag = $objDb->execute($sSQL);
			}
		}		

		if ($bFlag == true)
		{
			if ($sStageCompleted == "Y" || $sOldCompleted == "Y")
			{
				$iMilestoneStages = array( );
				
				$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iMileStoneStage' ORDER BY position DESC";
				$objDb->query($sSQL);
				
				$iCount = $objDb->getCount( );
				
				for ($i = 0; $i < $iCount; $i ++)
					$iMilestoneStages[] = $objDb->getField($i, 0);
				
			
				$iMilestonePosition = getDbValue("position", "tbl_stages", "id='$iMileStoneStage'");
				$iLastStage         = getDbValue("s.id", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.weightage>'0' AND i.school_id='$iSchool' AND s.position>'$iMilestonePosition' AND i.status='P' AND i.stage_completed='Y' AND s.type='$sSchoolType' AND s.skip!='Y'", "s.position DESC");
				$iLastMilestone     = 0;
				$iCurrentMilestone  = 0;
				
				if ($sStageCompleted == "Y")
				{
					$iLastPosition = getDbValue("position", "tbl_stages", "`type`='$sSchoolType'", "position DESC");
					
					
					foreach ($iMilestoneStages as $iMilestone)
					{
						$iMilestonePosition  = getDbValue("position", "tbl_stages", "id='$iMilestone'");
						$iMilestoneLastStage = getDbValue("id", "tbl_stages", "parent_id>'0' AND `type`='$sSchoolType' AND weightage>'0' AND skip!='Y' AND position>'$iMilestonePosition' AND position<'$iLastPosition'", "position DESC");
						
						if (intval($iMilestoneLastStage) == 0)
							continue;

						if ($iStage == $iMilestoneLastStage)
						{
							$iCurrentMilestone = $iMilestone;
							
							break;
						}
						
						$iLastPosition = $iMilestonePosition;							
					}
				}

			
				if ($iCurrentMilestone > 0)
					$iLastMilestone = $iCurrentMilestone;
				
				else if ($iLastStage > 0)
				{
					$iLastMilestone = getDbValue("parent_id", "tbl_stages", "id='$iLastStage'");

					if (!@in_array($iLastMilestone, $iMilestoneStages))
						$iLastMilestone = getDbValue("parent_id", "tbl_stages", "id='$iLastMilestone'");


					if (@in_array($iLastMilestone, $iMilestoneStages))
					{
						$sMilestoneStages = @implode(",", $iMilestoneStages);
						$iLastPosition    = getDbValue("position", "tbl_stages", "id='$iLastMilestone'");
						$iLastMilestone   = (int)getDbValue("id", "tbl_stages", "FIND_IN_SET(id, '$sMilestoneStages') AND position<'$iLastPosition'", "position DESC");
					}
					
					else
						$iLastMilestone = 0;
				}

					

				$iLastStage        = (int)getDbValue("s.id", "tbl_inspections i, tbl_stages s", "s.id=i.stage_id AND s.weightage>'0' AND i.school_id='$iSchool' AND i.status='P' AND i.stage_completed='Y' AND s.type='$sSchoolType' AND s.skip!='Y'", "s.position DESC");
				$iStagePosition    = getDbValue("position", "tbl_stages", "id='$iLastStage'");
				$fSchoolProgress   = @round(getDbValue("COALESCE(SUM(weightage), '0')", "tbl_stages", "position<='$iStagePosition' AND `type`='$sSchoolType'"), 2);					
				$sLastInspection   = (($iLastStage > 0) ? getDbValue("created_at", "tbl_inspections", "id='$iLastStage'") : "0000-00-00 00:00:00");
				$sLastInspection   = (($sLastInspection == "") ? "0000-00-00 00:00:00" : $sLastInspection);			
				
				$sCompletionStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "`type`='$sSchoolType' AND parent_id>'0' AND name='Finishing & Demobilization'");
				$sCompleted        = ((getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool' AND FIND_IN_SET(stage_id, '$sCompletionStages') AND status='P' AND stage_completed='Y'") > 0) ? "Y" : "N");
				$fSchoolProgress   = (($sCompleted == "Y") ? 100 : $fSchoolProgress);



				$sSQL = "UPDATE tbl_schools SET progress          = '$fSchoolProgress',
												last_inspection   = '$sLastInspection',
												last_stage_id     = '$iLastStage',
												last_milestone_id = '$iLastMilestone',
												completed         = '$sCompleted'
						 WHERE id='$iSchool'";
				$bFlag = $objDb->execute($sSQL);
				
				if ($bFlag == true && $sStageCompleted == "Y" && $iCurrentMilestone == $iLastMilestone && $iLastMilestone > 0)
				{
					$sSQL  = "UPDATE tbl_inspections SET milestone_id='$iLastMilestone' WHERE id='$iInspection'";
					$bFlag = $objDb->execute($sSQL);
				}
			}
		}


		if ($bFlag == true)
		{
			for ($i = 0; $i < $iFiles; $i ++)
			{
				$sUploadName   = IO::strValue("Files_{$i}_name");
				$sUploadStatus = IO::strValue("Files_{$i}_status");


				if ($sUploadStatus == "done" && $sUploadName != "")
				{
					$iFile = getNextId("tbl_inspection_documents");
					$sFile = ("{$iInspectionId}-{$iFile}-".IO::getFileName($sUploadName));

					$iPosition  = @strrpos($sUploadName, '.');
					$sExtension = @substr($sUploadName, $iPosition);

					if (@in_array($sExtension, array(".jpg", ".jpeg", ".png", ".gif")))
						copy(($sRootDir.TEMP_DIR.$sUploadName), ($sRootDir.INSPECTIONS_IMG_DIR.$sFile));

					else
						copy(($sRootDir.TEMP_DIR.$sUploadName), ($sRootDir.INSPECTIONS_DOC_DIR.$sFile));


					$sSQL = "INSERT INTO tbl_inspection_documents SET id            = '$iFile',
																	  inspection_id = '$iInspectionId',
																	  file          = '$sFile'";
					$bFlag = $objDb->execute($sSQL);

					if ($bFlag == false)
						break;


					$sFiles[] = $sFile;
				}


				@unlink($sRootDir.TEMP_DIR.IO::strValue("Files_{$i}_name"));
			}
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");
			
			
			$sSQL = "SELECT site_title, general_name, general_email, date_format, time_format FROM tbl_settings WHERE id='1'";
			$objDb->query($sSQL);

			$sSiteTitle   = $objDb->getField(0, "site_title");
			$sSenderName  = $objDb->getField(0, "general_name");
			$sSenderEmail = $objDb->getField(0, "general_email");
			$sDateFormat  = $objDb->getField(0, "date_format");
			$sTimeFormat  = $objDb->getField(0, "time_format");
			
			
			$sSQL = "SELECT name, email FROM tbl_admins WHERE id='$iUser'";
			$objDb->query($sSQL);

			$sName  = $objDb->getField(0, "name");
			$sEmail = $objDb->getField(0, "email");			


			$sLatitude         = "";
			$sLongitude        = "";
			$sAddress          = "";
			$sDateTime         = date("Y-m-d H:i:s");

			$sInspectionId     = str_pad($iInspectionId, 5, '0', STR_PAD_LEFT); 
			$sMapLink          = "";
			$sDistance         = "N/A";
			$sInspectionStatus = "";

			if ($sStatus == "P")
				$sInspectionStatus = "Pass";

			else if ($sStatus == "F")
				$sInspectionStatus = ("Fail (".getDbValue("reason", "tbl_failure_reasons", "id='$iReason'").")");

			else if ($sStatus == "R")
				$sInspectionStatus = ("Re-Inspection (Date: ".formatDate($sReInspection, $_SESSION['DateFormat']).")");


			$sProvince         = getDbValue("name", "tbl_provinces", "id='$iProvince'");
			$sDistrict         = getDbValue("name", "tbl_districts", "id='$iDistrict'");
			$sCompletionStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "`type`='$sSchoolType' AND parent_id>'0' AND name='Finishing & Demobilization'");
			$iCompletionStages = @explode(",", $sCompletionStages);
			$sLastMilestone    = (($iLastMilestone > 0) ? getDbValue("name", "tbl_stages", "id='$iLastMilestone'") : "-");
			

			
			// Audit Inspection Alert
			$sSQL = "SELECT subject, message, status FROM tbl_email_templates WHERE id='4'";
			$objDb->query($sSQL);

			$sSubject = $objDb->getField(0, "subject");
			$sBody    = $objDb->getField(0, "message");


			if ($objDb->getField(0, "status") == "A")
			{
				$sSubject = @str_replace("{SITE_TITLE}", $_SESSION["SiteTitle"], $sSubject);
				$sSubject = @str_replace("{INSPECTION_CODE}", $sInspectionId, $sSubject);
				
				if ($sStatus == "F")
					$sSubject = "Failed Inspection - {$sSubject}";
				
				else if ($sStatus == "P")
				{
					if ($sStageCompleted == "Y" && $iCurrentMilestone == $iLastMilestone && $iLastMilestone > 0)
						$sSubject = "{$sLastMilestone} Completed - {$sSubject}";
						
					else
						$sSubject = "Passed Inspection - {$sSubject}";
				}
				
				else if ($sStatus == "R")
					$sSubject = "Re-Inspection - {$sSubject}";
				
				

				$sBody = @str_replace("{INSPECTION_CODE}", $sInspectionId, $sBody);
				$sBody = @str_replace("{STAGE}", getDbValue("name", "tbl_stages", "id='$iStage'"), $sBody);
				$sBody = @str_replace("{SCHOOL}", $sSchool, $sBody);
				$sBody = @str_replace("{EMIS_CODE}", $sCode, $sBody);
				$sBody = @str_replace("{DISTRICT}", $sDistrict, $sBody);
				$sBody = @str_replace("{INSPECTION_USER}", $sName, $sBody);
				$sBody = @str_replace("{INSPECTION_TITLE}", $sTitle, $sBody);
				$sBody = @str_replace("{INSPECTION_DATE}", formatDate($sDate, $_SESSION['DateFormat']), $sBody);
				$sBody = @str_replace("{INSPECTION_DETAILS}", $sDetails, $sBody);
				$sBody = @str_replace("{INSPECTION_STATUS}", (($sStatus == "F") ? "<b style='color:#ff0000;'>Failed</b>" : ""), $sBody);
				$sBody = @str_replace("{LOCATION}", (($sAddress == "") ? "N/A" : $sAddress), $sBody);
				$sBody = @str_replace("{MAP_LINK}", $sMapLink, $sBody);
				$sBody = @str_replace("{DISTANCE}", $sDistance, $sBody);
				$sBody = @str_replace("{STATUS}", $sInspectionStatus, $sBody);
				$sBody = @str_replace("{STAGE_COMPLETED}", (($sStageCompleted == "Y") ? "Yes" : "No"), $sBody);
				$sBody = @str_replace("{MILESTONE_COMPLETED}", (($sStageCompleted == "Y" && $iCurrentMilestone == $iLastMilestone && $iLastMilestone > 0) ? "Yes" : "No"), $sBody);
				$sBody = @str_replace("{LAST_MILESTONE}", $sLastMilestone, $sBody);
				$sBody = @str_replace("{SITE_TITLE}", $_SESSION["SiteTitle"], $sBody);
				$sBody = @str_replace("{SITE_URL}", SITE_URL, $sBody);


				$objEmail = new PHPMailer( );

				$objEmail->Subject = $sSubject;
				$objEmail->MsgHTML($sBody);
				$objEmail->SetFrom($sSenderEmail, $sSenderName);
				$objEmail->AddAddress($sEmail, $sName);
				
				if (@strpos($sEmail, "@3-tree.com") === FALSE)
				{
					$objEmail->AddAddress("omer@3-tree.com", "Omer Rauf");
					$objEmail->AddAddress("Isfundiar.Kasuri@humqadam.pk", "Isfundiar Kasuri");
					$objEmail->AddAddress("Imran.Shakir@humqadam.pk", "Imran Shakir");

					if ($iProvince == 2)
					{
						$objEmail->AddAddress("Ismail.Ibrahim@humqadam.pk", "Ismail Ibrahim");
						$objEmail->AddAddress("Imran.Zaman@humqadam.pk", "Imran Zaman");
					}


					$sSeniorDistrictEngineersList         = getList("tbl_admins", "email", "name", "status='A' AND type_id='8' AND FIND_IN_SET('$iProvince', provinces) AND FIND_IN_SET('$iDistrict', districts) AND (schools='' OR FIND_IN_SET('$iSchool', schools))");
					$sDistrictManagersList                = getList("tbl_admins", "email", "name", "status='A' AND type_id='7' AND FIND_IN_SET('$iProvince', provinces) AND FIND_IN_SET('$iDistrict', districts) AND (schools='' OR FIND_IN_SET('$iSchool', schools))");
					$sProvincialConstructionEngineersList = getList("tbl_admins", "email", "name", "status='A' AND type_id='6' AND FIND_IN_SET('$iProvince', provinces) AND FIND_IN_SET('$iDistrict', districts) AND (schools='' OR FIND_IN_SET('$iSchool', schools))");

					foreach($sSeniorDistrictEngineersList as $sUserEmail => $sUserName)
					{
						$objEmail->AddAddress($sUserEmail, $sUserName);
					}

					foreach($sDistrictManagersList as $sUserEmail => $sUserName)
					{
						$objEmail->AddAddress($sUserEmail, $sUserName);
					}

					foreach($sProvincialConstructionEngineersList as $sUserEmail => $sUserName)
					{
						$objEmail->AddAddress($sUserEmail, $sUserName);
					}
				}

				
				if (@file_exists($sRootDir.INSPECTIONS_IMG_DIR.$sPicture))
					$objEmail->AddAttachment(($sRootDir.INSPECTIONS_IMG_DIR.$sPicture), $sPicture);

				if (@file_exists($sRootDir.INSPECTIONS_DOC_DIR.$sDocument))
					$objEmail->AddAttachment(($sRootDir.INSPECTIONS_DOC_DIR.$sDocument), $sDocument);

				if (@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE)
					$objEmail->Send( );
			}
			

			// School Completion Alert
			if ($sStatus == "P" && $sStageCompleted == "Y" && @in_array($iStage, $iCompletionStages))
			{
				$sSQL = "UPDATE tbl_schools SET completed='Y', progress='100' WHERE id='$iSchool'";
				$objDb->execute($sSQL);

						
				$sManager  = getDbValue("GROUP_CONCAT(name SEPARATOR ', ')", "tbl_admins", "status='A' AND type_id='7' AND FIND_IN_SET('$iProvince', provinces) AND FIND_IN_SET('$iDistrict', districts) AND FIND_IN_SET('$iSchool', schools)");
				$sEngineer = getDbValue("GROUP_CONCAT(name SEPARATOR ', ')", "tbl_admins", "status='A' AND type_id='9' AND FIND_IN_SET('$iProvince', provinces) AND FIND_IN_SET('$iDistrict', districts) AND FIND_IN_SET('$iSchool', schools)");
				
				if ($sManager == "")
					$sManager = getDbValue("GROUP_CONCAT(name SEPARATOR ', ')", "tbl_admins", "status='A' AND type_id='7' AND FIND_IN_SET('$iProvince', provinces) AND FIND_IN_SET('$iDistrict', districts)");
				
				if ($sEngineer == "")
					$sEngineer = getDbValue("GROUP_CONCAT(name SEPARATOR ', ')", "tbl_admins", "status='A' AND type_id='9' AND FIND_IN_SET('$iProvince', provinces) AND FIND_IN_SET('$iDistrict', districts)");
				
				
				$sContractor          = "";
				$iContractorTotal     = 0;
				$iContractorCompleted = 0;
				
				
				$sSQL = "SELECT contractor_id, schools FROM tbl_contracts WHERE status='A' AND FIND_IN_SET('$iSchool', schools) ORDER BY id DESC LIMIT 1";
				$objDb->query($sSQL);

				if ($objDb->getCount( ) == 1)
				{
					$iContractor = $objDb->getField(0, "contractor_id");
					$sSchools    = $objDb->getField(0, "schools");
				
					$sContractor          = getDbValue("company", "tbl_contractors", "id='$iContractor'");
					$iContractorTotal     = getDbValue("COUNT(1)", "tbl_schools", "status='A' AND dropped!='Y' AND FIND_IN_SET(id, '$sSchools')");
					$iContractorCompleted = getDbValue("COUNT(DISTINCT(school_id))", "tbl_inspections", "FIND_IN_SET(stage_id, '$sCompletionStages') AND status='P' AND stage_completed='Y' AND FIND_IN_SET(school_id, '$sSchools')");
				}
				

					
				$sSQL = "SELECT subject, message, status FROM tbl_email_templates WHERE id='7'";
				$objDb->query($sSQL);

				$sSubject = $objDb->getField(0, "subject");
				$sBody    = $objDb->getField(0, "message");


				if ($objDb->getField(0, "status") == "A")
				{
					$sSubject = @str_replace("{SITE_TITLE}", $_SESSION["SiteTitle"], $sSubject);
					$sSubject = @str_replace("{INSPECTION_CODE}", $sInspectionId, $sSubject);
					$sSubject = @str_replace("{SCHOOL}", $sSchool, $sSubject);
					$sSubject = @str_replace("{EMIS_CODE}", $sCode, $sSubject);
					$sSubject = @str_replace("{DISTRICT}", $sDistrict, $sSubject);
					$sSubject = @str_replace("{PROVINCE}", $sProvince, $sSubject);
					
					$sBody = @str_replace("{INSPECTION_CODE}", $sInspectionId, $sBody);
					$sBody = @str_replace("{STAGE}", getDbValue("name", "tbl_stages", "id='$iStage'"), $sBody);
					$sBody = @str_replace("{SCHOOL}", $sSchool, $sBody);
					$sBody = @str_replace("{EMIS_CODE}", $sCode, $sBody);
					$sBody = @str_replace("{WORK_TYPE}", (($sWorkType == "N") ? "New Construction" : (($sWorkType == "R") ? "Rehabilitation Only" : "New Construction & Rehabilitation")), $sBody);							
					$sBody = @str_replace("{DISTRICT}", $sDistrict, $sBody);
					$sBody = @str_replace("{PROVINCE}", $sProvince, $sBody);
					$sBody = @str_replace("{INSPECTION_USER}", $sName, $sBody);
					$sBody = @str_replace("{INSPECTION_TITLE}", $sTitle, $sBody);
					$sBody = @str_replace("{INSPECTION_DATE}", formatDate($sDate, $_SESSION['DateFormat']), $sBody);
					$sBody = @str_replace("{INSPECTION_DETAILS}", $sDetails, $sBody);
					$sBody = @str_replace("{INSPECTION_STATUS}", (($sStatus == "F") ? "<b style='color:#ff0000;'>Failed</b>" : ""), $sBody);
					$sBody = @str_replace("{LOCATION}", (($sAddress == "") ? "N/A" : $sAddress), $sBody);
					$sBody = @str_replace("{MAP_LINK}", $sMapLink, $sBody);
					$sBody = @str_replace("{DISTANCE}", $sDistance, $sBody);
					$sBody = @str_replace("{STATUS}", $sInspectionStatus, $sBody);
					$sBody = @str_replace("{MANAGER}", $sManager, $sBody);
					$sBody = @str_replace("{ENGINEER}", $sEngineer, $sBody);
					$sBody = @str_replace("{NO_OF_INSPECTIONS}", getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool'"), $sBody);
					$sBody = @str_replace("{CONTRACTOR_COMPLETED}", $iContractorCompleted, $sBody);
					$sBody = @str_replace("{CONTRACTOR_TOTAL}", $iContractorTotal, $sBody);
					$sBody = @str_replace("{CONTRACTOR}", $sContractor, $sBody);
					$sBody = @str_replace("{SITE_TITLE}", $_SESSION["SiteTitle"], $sBody);
					$sBody = @str_replace("{SITE_URL}", SITE_URL, $sBody);


					$objEmail = new PHPMailer( );

					$objEmail->Subject = $sSubject;
					$objEmail->MsgHTML($sBody);
					$objEmail->SetFrom($sSenderEmail, $sSenderName);
					
					if (@strpos($sEmail, "@3-tree.com") === FALSE)
					{
						$objEmail->AddAddress("omer@3-tree.com", "Omer Rauf");
						$objEmail->AddAddress("Isfundiar.Kasuri@humqadam.pk", "Isfundiar Kasuri");
						$objEmail->AddAddress("Imran.Shakir@humqadam.pk", "Imran Shakir");
						$objEmail->AddAddress("Ismail.Ibrahim@humqadam.pk", "Ismail Ibrahim");
					}
					
					else
						$objEmail->AddAddress($sEmail, $sName);


					if (@file_exists($sRootDir.INSPECTIONS_IMG_DIR.$sPicture))
						$objEmail->AddAttachment(($sRootDir.INSPECTIONS_IMG_DIR.$sPicture), $sPicture);

					if (@file_exists($sRootDir.INSPECTIONS_DOC_DIR.$sDocument))
						$objEmail->AddAttachment(($sRootDir.INSPECTIONS_DOC_DIR.$sDocument), $sDocument);

					if (@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE)
						$objEmail->Send( );
				}
			}
			


			if ($sOldPicture != "" && $sPicture != "" && $sOldPicture != $sPicture)
			{
				@unlink($sRootDir.INSPECTIONS_IMG_DIR.$sOldPicture);
				@unlink($sRootDir.INSPECTIONS_IMG_DIR."thumbs/".$sOldPicture);
			}

			if ($sOldDocument != "" && $sDocument != "" && $sOldDocument != $sDocument)
				@unlink($sRootDir.INSPECTIONS_DOC_DIR.$sOldDocument);


			$sSQL = "SELECT name, parent_id FROM tbl_stages WHERE id='$iStage'";
			$objDb->query($sSQL);

			$sStage  = $objDb->getField(0, "name");
			$iParent = $objDb->getField(0, "parent_id");

			$sStages = $sStage;

			if ($iParent > 0)
			{
				$sSQL = "SELECT name, parent_id FROM tbl_stages WHERE id='$iParent'";
				$objDb->query($sSQL);

				$sParent = $objDb->getField(0, "name");
				$iParent = $objDb->getField(0, "parent_id");

				$sStages = ($sParent.' &raquo; '.$sStages);
			}

			if ($iParent > 0)
			{
				$sSQL = "SELECT name, parent_id FROM tbl_stages WHERE id='$iParent'";
				$objDb->query($sSQL);

				$sParent = $objDb->getField(0, "name");
				$iParent = $objDb->getField(0, "parent_id");

				$sStages = ($sParent.' &raquo; '.$sStages);
			}

			if ($iParent > 0)
				$sStages = (getDbValue("name", "tbl_stages", "id='$iParent'").' &raquo; '.$sStages);


			$sStages = @utf8_encode($sStages);
			$sSchool = getDbValue("name", "tbl_schools", "id='$iSchool'");


			$sSQL = "SELECT created_at, modified_at,
							(SELECT name FROM tbl_admins WHERE id=tbl_inspections.created_by) AS _CreatedBy,
							(SELECT name FROM tbl_admins WHERE id=tbl_inspections.modified_by) AS _ModifiedBy
					 FROM tbl_inspections
					 WHERE id='$iInspectionId'";
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

		sFields[0] = "<?= @htmlentities($sTitle) ?>";
		sFields[1] = "<?= formatDate($sDate, $_SESSION['DateFormat']) ?>";
		sFields[2] = "<?= @addslashes($sStages) ?>";
		sFields[3] = "<?= @htmlentities($sSchool) ?>";
		sFields[4] = "<?= @htmlentities($sStatus) ?>";
		sFields[5] = '<img class="icon details" id="<?= $iStudentId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" /> ';
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[5] = (sFields[5] + '<img class="icnEdit" id="<?= $iInspectionId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
		sFields[5] = (sFields[5] + '<img class="icon icnMeasurements" id="<?= $iInspectionId ?>" src="images/icons/boqs.png" alt="Measurements" title="Measurements" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sFields[5] = (sFields[5] + '<img class="icnDelete" id="<?= $iInspectionId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}

			if ($sOldPicture != "" && @file_exists($sRootDir.INSPECTIONS_IMG_DIR.$sOldPicture))
			{
?>
		sFields[5] = (sFields[5] + '<img class="icnPicture" id="<?= (SITE_URL.INSPECTIONS_IMG_DIR.$sOldPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
<?
			}

			else if ($sPicture != "" && @file_exists($sRootDir.INSPECTIONS_IMG_DIR.$sPicture))
			{
?>
		sFields[5] = (sFields[5] + '<img class="icnPicture" id="<?= (SITE_URL.INSPECTIONS_IMG_DIR.$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
<?
			}

			if ($sOldDocument != "" && @file_exists($sRootDir.INSPECTIONS_DOC_DIR.$sOldDocument))
			{
?>
		sFields[5] = (sFields[5] + '<a href="<?= $sCurDir ?>/download-inspection.php?Id=<?= $iInspectionId ?>&File=<?= $sOldDocument ?>"><img class="icnDocument" src="images/icons/download.gif" alt="Download" title="Download" /></a> ');
<?
			}

			else if ($sDocument != "" && @file_exists($sRootDir.INSPECTIONS_DOC_DIR.$sDocument))
			{
?>
		sFields[5] = (sFields[5] + '<a href="<?= $sCurDir ?>/download-inspection.php?Id=<?= $iInspectionId ?>&File=<?= $sDocument ?>"><img class="icnDocument" src="images/icons/download.gif" alt="Download" title="Download" /></a> ');
<?
			}
?>
		sFields[5] = (sFields[5] + '<img class="icnView" id="<?= $iInspectionId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

		parent.updateRecord(<?= $iInspectionId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Inspection Record has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";


			if ($sPicture != "" && $sOldPicture != $sPicture)
			{
				@unlink($sRootDir.INSPECTIONS_IMG_DIR.$sPicture);
				@unlink($sRootDir.INSPECTIONS_IMG_DIR."thumbs/".$sPicture);
			}

			if ($sDocument != "" && $sOldDocument != $sDocument)
				@unlink($sRootDir.INSPECTIONS_DOC_DIR.$sDocument);


			for ($i = 0; $i < count($sFiles); $i ++)
			{
				@unlink($sRootDir.INSPECTIONS_DOC_DIR.$sFiles[$i]);
				@unlink($sRootDir.INSPECTIONS_IMG_DIR.$sFiles[$i]);
			}
		}
	}
?>