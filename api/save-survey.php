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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	$sUser           = IO::strValue("User");
	$iSchool         = IO::intValue("School");
	$sCode           = IO::strValue("Code");
	$sOperational    = IO::strValue("Operational");
	$sPefProgramme   = IO::strValue("PefProgramme");
	$sLandAvailable  = IO::strValue("LandAvailable");
	$sLandDispute    = IO::strValue("LandDispute");
	$sOtherFunding   = IO::strValue("OtherFunding");
	$iClassRooms     = IO::intValue("ClassRooms");
	$iEducationRooms = IO::intValue("EducationRooms");
	$sShelterLess    = IO::strValue("ShelterLess");
	$sMultiGrading   = IO::strValue("MultiGrading");
	$iAvgAttendance  = IO::intValue("AvgAttendance");
	$sPreSelection   = IO::strValue("QualifyPreSelection");
	$sComments       = @utf8_encode(IO::strValue("Comments"));
	$sDateTime       = IO::strValue("DateTime");
	$sLatitude       = IO::strValue("Latitude");
	$sLongitude      = IO::strValue("Longitude");	

	
	logApiCall($_POST);
	
	
	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $iSchool == 0 || $sOperational == "" || $iClassRooms < 0 || $iEducationRooms < 0 || $iEducationRooms > $iClassRooms || $sDateTime == "")
		$aResponse["Message"] = "Invalid Request";

	else
	{
		$sSQL = "SELECT id, name, email, provinces, districts, schools, status FROM tbl_admins WHERE MD5(id)='$sUser'";
		$objDb->query($sSQL);

		if ($objDb->getCount( ) == 0)
			$aResponse["Message"] = "Invalid User";

		else if ($objDb->getField(0, "status") != "A")
			$aResponse["Message"] = "User Account is Disabled";

		else
		{
			$iUser      = $objDb->getField(0, "id");
			$sName      = $objDb->getField(0, "name");
			$sEmail     = $objDb->getField(0, "email");
			$sProvinces = $objDb->getField(0, "provinces");
			$sDistricts = $objDb->getField(0, "districts");
			$sSchools   = $objDb->getField(0, "schools");

			$iProvinces = @explode(",", $sProvinces);
			$iDistricts = @explode(",", $sDistricts);
			$iSchools   = @explode(",", $sSchools);



			$sSQL = "SELECT district_id, province_id FROM tbl_schools WHERE id='$iSchool'";
			$objDb->query($sSQL);

			$iDistrict = $objDb->getField(0, "district_id");
			$iProvince = $objDb->getField(0, "province_id");


			if ($objDb->getCount( ) == 0)
				$aResponse["Message"] = "Invalid Request, no School Found!";

			else if ( ($sSchools != "" && !@in_array($iSchool, $iSchools)) || ($sSchools == "" && (!@in_array($iProvince, $iProvinces) || !@in_array($iDistrict, $iDistricts))) )
				$aResponse["Message"] = "Request denied, You don't have permissions for requested School!";

			else
			{
				$sDate      = substr($sDateTime, 0, 10);
				$sQualified = "Y";
				$sStatus    = "I";
				$sCompleted = "N";
	
				if ($sOperational != "Y" || $sPefProgramme == "Y" || ($sLandAvailable == "N" && $iProvince == 1) || $sLandDispute != "N" || $sOtherFunding == "Y" || ($iAvgAttendance < 100 && $iProvince == 2) || $sPreSelection == "N")
				{
					$sStatus    = "C";
					$sQualified = "N";
					$sCompleted = "Y";
				}

		
				if ((int)getDbValue("COUNT(1)", "tbl_surveys", "school_id='$iSchool' AND enumerator='$sName' AND created_by='$iUser' AND `date`='$sDate'") > 0)
					$aResponse["Message"] = "Already Saved.";

				else
				{
					$bFlag = $objDb->execute("BEGIN", true, $iUser, $sName, $sEmail);
								
					$sSQL = "INSERT INTO tbl_surveys SET district_id       = '$iDistrict',
														 school_id         = '$iSchool',
														 enumerator        = '$sName',
														 `date`            = '$sDate',											 
														 operational       = '$sOperational',
														 pef_programme     = '$sPefProgramme',
														 land_available    = '$sLandAvailable',
														 land_dispute      = '$sLandDispute',
														 other_funding     = '$sOtherFunding',
														 class_rooms       = '$iClassRooms',
														 education_rooms   = '$iEducationRooms',
														 shelter_less      = '$sShelterLess',
														 multi_grading     = '$sMultiGrading',
														 avg_attendance    = '$iAvgAttendance',
														 pre_selection     = '$sPreSelection',
														 comments          = '$sComments',
														 qualified         = '$sQualified',
														 status            = '$sStatus',
														 latitude          = '$sLatitude',
														 longitude         = '$sLongitude',
														 created_by        = '$iUser',
														 created_at        = '$sDateTime',
														 modified_by       = '$iUser',
														 modified_at       = '$sDateTime',
														 completed         = '$sCompleted',
														 app               = 'Y'";
					$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
					
					if ($bFlag == true)
						$iSurvey = $objDb->getAutoNumber( );
					
					if ($bFlag == true && $sQualified == "Y")
					{
						$sSQL  = "INSERT INTO tbl_survey_details (survey_id, section_id, `status`, created_by, created_at, modified_by, modified_at)
													      (SELECT '$iSurvey', id, 'I', '$iUser', '$sDateTime', '$iUser', '$sDateTime' FROM tbl_survey_sections)";
						$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
					}
					
					if ($bFlag == true)
					{
						$sSQL  = "UPDATE tbl_schools SET qualified='$sQualified' WHERE id='$iSchool'";
						$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
					}
					
					if ($bFlag == true)
					{
						$sSQL  = "UPDATE tbl_survey_schedules SET status='C' WHERE school_id='$iSchool'";
						$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);						
					}
					
					if ($bFlag == true)
					{
						$objDb->execute("COMMIT", true, $iUser, $sName, $sEmail);
						
						
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
									 WHERE  bs.school_id=s.id AND bs.id='$iSurvey'";
							$objDb->query($sSQL);
							
							$iProvince = $objDb->getField(0, "province_id");
							$iDistrict = $objDb->getField(0, "district_id");
							

							$sDownloadPdf     = "<a href='http://www.3-tree.com/scrp/mscp/surveys/export-survey.php?Id={$iSurvey}' target='_blank'>Download Basic Baseline Survey (PDF Format)</a>";			
							$iAdditionalRooms = (@ceil($objDb->getField(0, "bs.avg_attendance") / 40) - $objDb->getField(0, "bs.education_rooms"));
							$sFirm            = (($iProvince == 1) ? 'Innovative Development Strategies (Pvt) Ltd' : 'Associates in Development (Pvt) Ltd');

							
							$sSubject = @str_replace("{SITE_TITLE}", $sSiteTitle, $sSubject);
							$sSubject = @str_replace("{EMIS_CODE}", $objDb->getField(0, "s.code"), $sSubject);
							$sSubject = @str_replace("{SCHOOL}", $objDb->getField(0, "s.name"), $sSubject);
							$sSubject = @str_replace("{DISTRICT_NAME}", getDbValue("name", "tbl_districts", "id='$iDistrict'"), $sSubject);
							
							$sBody = @str_replace("{SURVEY_ID}", str_pad($iSurvey, '0', 5, STR_PAD_LEFT), $sBody);
							$sBody = @str_replace("{SCHOOL}", $objDb->getField(0, "s.name"), $sBody);
							$sBody = @str_replace("{EMIS_CODE}", $objDb->getField(0, "s.code"), $sBody);
							$sBody = @str_replace("{PROVINCE_NAME}", getDbValue("name", "tbl_provinces", "id='$iProvince'"), $sBody);
							$sBody = @str_replace("{DISTRICT_NAME}", getDbValue("name", "tbl_districts", "id='$iDistrict'"), $sBody);
							$sBody = @str_replace("{ENUMERATOR}",  $objDb->getField(0, "bs.enumerator"), $sBody);
							$sBody = @str_replace("{CONSULTING_FIRM}", $sFirm, $sBody);
							$sBody = @str_replace("{ASSESSMENT_RESULT}", (($objDb->getField(0, "bs.qualified") == 'Y') ? 'Qualified' : 'Disqualified'), $sBody);
							$sBody = @str_replace("{TOTAL_ROOMS}", $objDb->getField(0, "bs.class_rooms"), $sBody);
							$sBody = @str_replace("{EDUCATION_ROOMS}", $objDb->getField(0, "bs.education_rooms"), $sBody);
							$sBody = @str_replace("{AVG_ATTENDANCE}", $objDb->getField(0, "bs.avg_attendance"), $sBody);
							$sBody = @str_replace("{REQUIRED_ROOMS}", (($iAdditionalRooms > 0 && $objDb->getField(0, "bs.avg_attendance") > 40) ? $iAdditionalRooms : 'No'), $sBody);
							$sBody = @str_replace("{SURVEY_STATUS}", "Completed", $sBody);
							$sBody = @str_replace("{SITE_TITLE}", $sSiteTitle, $sBody);
							$sBody = @str_replace("{DOWNLOAD_BASE_LINE_PDF}", $sDownloadPdf, $sBody);
							$sBody = @str_replace("{SITE_PLAN_PDF}","", $sBody);
							$sBody = @str_replace("{SITE_PLAN_AC}", "", $sBody);
							$sBody = @str_replace("{STRUCTURE_PDF}", "", $sBody);
							$sBody = @str_replace("{STRUCTURE_AC}", "", $sBody);
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
						

						$aResponse["Status"]  = "OK";
						$aResponse["Message"] = "Survey Entry saved successfully!";
					}

					else
					{
						$objDb->execute("ROLLBACK", true, $iUser, $sName, $sEmail);
						
						$aResponse["Message"] = "An ERROR occured, please try again.";
					}
				}
			}
		}
	}

	
	print @json_encode($aResponse);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>