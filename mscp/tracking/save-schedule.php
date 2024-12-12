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

	$iContract  = IO::intValue("ddContract");
	$iSchool    = IO::intValue("ddSchool");
	$sStartDate = IO::strValue("txtStartDate");
	$sEndDate   = IO::strValue("txtEndDate");
	$bError     = true;
	

	$sSQL = "SELECT storey_type, design_type FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);

	$sStoreyType = $objDb->getField(0, "storey_type");
	$sDesignType = $objDb->getField(0, "design_type");	

	$sSchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);
	$iMainStage  = getDbValue("id", "tbl_stages", "status='A' AND parent_id='0' AND `type`='$sSchoolType'", "position DESC");

	
	if ($iContract == 0 || $iSchool == 0 || $sStartDate == "" || $sEndDate == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_contract_schedules WHERE contract_id='$iContract' AND school_id='$iSchool'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SCHEDULE_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT start_date, end_date FROM tbl_contracts WHERE id='$iContract'";
		$objDb->query($sSQL);

		$sContractStart = $objDb->getField(0, "start_date");
		$sContractEnd   = $objDb->getField(0, "end_date");


		if (strtotime($sStartDate) < strtotime($sContractStart) || strtotime($sStartDate) > strtotime($sContractEnd) ||
			strtotime($sEndDate) < strtotime($sContractStart) || strtotime($sEndDate) > strtotime($sContractEnd) )
			$_SESSION["Flag"] = "INVALID_SCHEDULE_DATES";
	}


	if ($_SESSION["Flag"] == "")
	{
		$objDb->execute("BEGIN");


		$iSchedule = getNextId("tbl_contract_schedules");

		$sSQL  = "INSERT INTO tbl_contract_schedules SET id          = '$iSchedule',
														 contract_id = '$iContract',
														 school_id   = '$iSchool',
														 start_date  = '$sStartDate',
														 end_date    = '$sEndDate'";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == true)
		{
			$sStages = "0";


			$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iMainStage' AND `type`='$sSchoolType' ORDER BY position";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($i = 0; $i < $iCount; $i ++)
			{
				$iParent = $objDb->getField($i, "id");

				$sStages .= ",{$iParent}";


				$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iParent' AND `type`='$sSchoolType' ORDER BY position";
				$objDb2->query($sSQL);

				$iCount2 = $objDb2->getCount( );

				for ($j = 0; $j < $iCount2; $j ++)
				{
					$iStage = $objDb2->getField($j, "id");

					$sStages .= ",{$iStage}";


					$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iStage' AND `type`='$sSchoolType' ORDER BY position";
					$objDb3->query($sSQL);

					$iCount3 = $objDb3->getCount( );

					for ($k = 0; $k < $iCount3; $k ++)
					{
						$iSubStage = $objDb3->getField($k, "id");

						$sStages .= ",{$iSubStage}";
					}
				}
			}

			
			$sSQL  = "INSERT INTO tbl_contract_schedule_details (schedule_id, stage_id, start_date, end_date) (SELECT '$iSchedule', id, '0000-00-00', '0000-00-00' FROM tbl_stages WHERE FIND_IN_SET(id, '$sStages'))";
			$bFlag = $objDb->execute($sSQL);
		}


		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			redirect("schedules.php", "SCHEDULE_ADDED");
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";
		}
	}
?>