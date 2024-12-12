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

	$iContract = IO::intValue("ddContract");
	$iSchool   = IO::intValue("ddSchool");
	$iSchools  = IO::getArray("cbSchools");
	$bError    = true;
	
	if ($iContract == 0 || $iSchool == 0 || count($iSchools) == 0)
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT id, start_date, end_date FROM tbl_contract_schedules WHERE contract_id='$iContract' AND school_id='$iSchool'";
		$objDb->query($sSQL);

		if ($objDb->getCount( ) == 0)
			$_SESSION["Flag"] = "SCHEDULE_NOT_EXISTS";

		else
		{
			$iSchedule      = $objDb->getField(0, "id");
			$sScheduleStart = $objDb->getField(0, "start_date");
			$sScheduleEnd   = $objDb->getField(0, "end_date");
		}
	}

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT storey_type, design_type FROM tbl_schools WHERE id='$iSchool'";
		$objDb->query($sSQL);

		$sStoreyType = $objDb->getField(0, "storey_type");
		$sDesignType = $objDb->getField(0, "design_type");	

		$sSchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);

	
		$bFlag = $objDb->execute("BEGIN");


		foreach ($iSchools as $iCopySchool)
		{
			$sSQL = "SELECT storey_type, design_type FROM tbl_schools WHERE id='$iCopySchool'";
			$objDb->query($sSQL);

			$sStoreyType = $objDb->getField(0, "storey_type");
			$sDesignType = $objDb->getField(0, "design_type");	

			$sCopySchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);

			
			if ($sSchoolType != $sCopySchoolType)
				continue;


                    
			$iCopySchedule = getNextId("tbl_contract_schedules");

			$sSQL  = "INSERT INTO tbl_contract_schedules SET id          = '$iCopySchedule',
															 contract_id = '$iContract',
															 school_id   = '$iCopySchool',
															 start_date  = '$sScheduleStart',
															 end_date    = '$sScheduleEnd'";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == true)
			{
				$sSQL  = "INSERT INTO tbl_contract_schedule_details (schedule_id, stage_id, start_date, end_date)
				                                      (SELECT '$iCopySchedule', csd.stage_id, csd.start_date, csd.end_date FROM tbl_contract_schedule_details csd WHERE csd.schedule_id='$iSchedule')";
				$bFlag = $objDb->execute($sSQL);
			}
					
			if ($bFlag == true)
			{
				$iPlannedStage    = getDbValue("csd.stage_id", "tbl_contract_schedules cs, tbl_contract_schedule_details csd", "cs.id=csd.schedule_id AND cs.school_id='$iCopySchool' AND csd.end_date<=CURDATE( )", "csd.end_date DESC");
				$iStagePosition   = getDbValue("position", "tbl_stages", "id='$iPlannedStage'");
				$fPlannedProgress = @round(getDbValue("COALESCE(SUM(weightage), '0')", "tbl_stages", "position<='$iStagePosition' AND `type`='$sCopySchoolType'"), 2);

				
				$sSQL  = "UPDATE tbl_schools SET planned='$fPlannedProgress' WHERE id='$iCopySchool'";
				$bFlag = $objDb->execute($sSQL);
			}			

			if ($bFlag == false)
				break;
		}


		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			redirect("schedules.php", "SCHEDULE_COPIED");
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";
		}
	}
?>