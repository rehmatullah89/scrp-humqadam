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


	$sUser              = IO::strValue("User");
	$iDistrictEngineer  = IO::intValue("DistrictEngineer");
	$sDate              = IO::strValue("Date");
	$sPrincipal         = IO::strValue("Principal");
	$sContactNo         = IO::strValue("ContactNo");
	$sPtc               = IO::strValue("Ptc");
	$sCcsi              = IO::strValue("Ccsi");
	
	
	logApiCall($_POST);
	
	
	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $iDistrictEngineer == "" || $sDate == "" || $sPrincipal == "" || $sContactNo == "" || $sPtc == "" || $sCcsi == "")
		$aResponse["Message"] = "Invalid Request";

	else
	{
		$sSQL = "SELECT id, name, email, status FROM tbl_admins WHERE MD5(id)='$sUser'";
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
        		$sDate      = substr($sDate, 0, 10);
                        $sStatus    = "I";
                        $sCompleted = "N";
	
			if ((int)getDbValue("COUNT(1)", "tbl_sors", "engineer_id='$iDistrictEngineer' AND principal='$sPrincipal' AND ccsi_representative='$sCcsi' AND ptc_representative='$sPtc' AND admin_id='$iUser' AND created_by='$iUser' AND `date`='$sDate'") > 0)
                            $aResponse["Message"] = "Already Saved.";

                        else
                        {
                            $bFlag = $objDb->execute("BEGIN", true, $iUser, $sName, $sEmail);
								
					$sSQL = "INSERT INTO tbl_sors SET admin_id      = '$iUser',
                                                                    engineer_id         = '$iDistrictEngineer',
                                                                    principal           = '$sPrincipal',
                                                                    `date`              = '$sDate',											 
                                                                    ccsi_representative = '$sCcsi',
                                                                    ptc_representative  = '$sPtc',
                                                                    contact_no          = '$sContactNo',
                                                                    status              = '$sStatus',
                                                                    created_by          = '$iUser',
                                                                    created_at          = '$sDateTime',
                                                                    modified_by         = '$iUser',
                                                                    modified_at         = '$sDateTime',
                                                                    completed           = '$sCompleted',
                                                                    app                 = 'Y'";
                                        
					$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
					
					if ($bFlag == true)
						$iSor = $objDb->getAutoNumber( );
					
					if ($bFlag == true)
					{
						$objDb->execute("COMMIT", true, $iUser, $sName, $sEmail);
						
						
						/*if ($sCompleted == "Y")
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
						}*/
						

						$aResponse["Status"]  = "OK";
						$aResponse["Message"] = "Sor Entry saved successfully!";
					}

					else
					{
						$objDb->execute("ROLLBACK", true, $iUser, $sName, $sEmail);
						
						$aResponse["Message"] = "An ERROR occured, please try again.";
					}
				}
			
		}
	}

	
	print @json_encode($aResponse);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>