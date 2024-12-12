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
		$sSQL = "SELECT * FROM tbl_surveys WHERE school_id='$iSchool' AND `date`='$sDate'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SURVEY_EXISTS";
	}
	

	if ($_SESSION["Flag"] == "")
	{
		$iDistrict  = getDbValue("district_id", "tbl_schools", "id='$iSchool'");
		$iProvince  = getDbValue("province_id", "tbl_schools", "id='$iSchool'");
		$sQualified = "Y";
		$sStatus    = "I";
		$sCompleted = "N";
		
		if ($sOperational != "Y" || ($iProvince == 1 && $sPefProgramme == "Y") || ($sLandAvailable == "N" && $iProvince == 1) || $sLandDispute != "N" || $sOtherFunding == "Y" || ($iAvgAttendance < 100 && $iProvince == 2) || $sPreSelection == "N")
		{
			$sStatus    = "C";
			$sQualified = "N";
			$sCompleted = "Y";
		}

		
		
		$objDb->execute("BEGIN");
		
		
		$iSurvey = getNextId("tbl_surveys");
		
		$sSQL = "INSERT INTO tbl_surveys SET id                = '$iSurvey',
											 district_id       = '$iDistrict',
											 school_id         = '$iSchool',
											 enumerator        = '$sEnumerator',
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
											 completed         = '$sCompleted',
											 status            = '$sStatus',
											 created_by        = '{$_SESSION['AdminId']}',
											 created_at        = NOW( ),
											 modified_by       = '{$_SESSION['AdminId']}',
											 modified_at       = NOW( )";
		$bFlag = $objDb->execute($sSQL);
		
        if ($bFlag == true && $sQualified == "Y")
		{
			$sSQL  = "INSERT INTO tbl_survey_details (survey_id, section_id, `status`, created_by, created_at, modified_by, modified_at)
			                                  (SELECT '$iSurvey', id, 'I', '{$_SESSION['AdminId']}', NOW( ), '{$_SESSION['AdminId']}', NOW( ) FROM tbl_survey_sections)";
			$bFlag = $objDb->execute($sSQL);	
		}

		if ($bFlag == true)
		{
			$sSQL  = "UPDATE tbl_schools SET qualified='$sQualified' WHERE id='$iSchool'";
			$bFlag = $objDb->execute($sSQL);
		}

		if ($bFlag == true)
		{
			$sSQL  = "UPDATE tbl_survey_schedules SET status='C' WHERE school_id='$iSchool'";
			$bFlag = $objDb->execute($sSQL);
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
						 WHERE  bs.school_id=s.id AND bs.id='$iSurvey'";
				$objDb->query($sSQL);
				
				$iProvince = $objDb->getField(0, "province_id");
				$iDistrict = $objDb->getField(0, "district_id");
				

				$sDownloadPdf     = "<a href='http://www.3-tree.com/scrp/mscp/surveys/export-survey.php?Id={$iSurvey)}' target='_blank'>Download Basic Baseline Survey (PDF Format)</a>";			
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
				$sBody = @str_replace("{ASSESSMENT_RESULT}", ($objDb->getField(0, "bs.qualified") == 'Y' ? 'Qualified' : 'Disqualified'), $sBody);
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
				$objEmail->AddAddress("Samaviya.Asghar@humqadam.pk", "Samaviya Asghar");
				$objEmail->AddAddress("Nimra.Tariq@humqadam.pk", "Nimra Tariq");
				$objEmail->AddAddress("Samantha.Passmore@humqadam.pk", "Samantha Passmore");
				$objEmail->AddAddress("Muhammad.zubair2@humqadam.pk", "Muhammad Zubair");
				$objEmail->AddAddress("Saleha.sundas@humqadam.pk", "Saleha Sundas");
				
				if (@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE)
						$objEmail->Send( );
			}

			
			redirect("surveys.php", "SURVEY_ADDED");
		}
		
		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";
		}
	}
?>