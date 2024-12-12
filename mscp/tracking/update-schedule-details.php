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

	$iSchool = IO::intValue("SchoolId");
	

	$sSQL = "SELECT storey_type, design_type FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);

	$sStoreyType = $objDb->getField(0, "storey_type");
	$sDesignType = $objDb->getField(0, "design_type");	

	$sSchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);
	$iMainStage  = getDbValue("id", "tbl_stages", "status='A' AND parent_id='0' AND `type`='$sSchoolType'", "position DESC");      
	$sStages     = "0";
	$iStageDays  = array( );

	
        
	$sSQL = "SELECT id, days FROM tbl_stages WHERE parent_id='$iMainStage' AND `type`='$sSchoolType' ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iParent = $objDb->getField($i, "id");
		$iDays   = $objDb->getField($i, "days");

		$sStages             .= ",{$iParent}";
		$iStageDays[$iParent] = $iDays;


		$sSQL = "SELECT id, days FROM tbl_stages WHERE parent_id='$iParent' ORDER BY position";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iStage = $objDb2->getField($j, "id");
			$iDays  = $objDb2->getField($j, "days");

			$sStages            .= ",{$iStage}";
			$iStageDays[$iStage] = $iDays;


			$sSQL = "SELECT id, days FROM tbl_stages WHERE parent_id='$iStage' ORDER BY position";
			$objDb3->query($sSQL);

			$iCount3 = $objDb3->getCount( );

			for ($k = 0; $k < $iCount3; $k ++)
			{
				$iSubStage = $objDb3->getField($k, "id");
				$iDays     = $objDb3->getField($k, "days");

				$sStages               .= ",{$iSubStage}";
				$iStageDays[$iSubStage] = $iDays;
			}
		}
	}


	$sSQL = "SELECT start_date, end_date FROM tbl_contracts WHERE id='$iContractId'";
	$objDb->query($sSQL);

	$sContractStart = $objDb->getField(0, "start_date");
	$sContractEnd   = $objDb->getField(0, "end_date");


	$objDb->execute("BEGIN");


	$iStages   = @explode(",", $sStages);
	$sPrevDate = $sContractStart;

	foreach ($iStages as $iStage)
	{
		if ($iStage == 0)
			continue;


		$sStartDate = IO::strValue("txtStartDate{$iStage}");
		$sEndDate   = IO::strValue("txtEndDate{$iStage}");

		$sStartDate = (($sStartDate == "") ? "0000-00-00" : date("Y-m-d", strtotime($sStartDate)));
		$sEndDate   = (($sEndDate == "") ? "0000-00-00" : date("Y-m-d", strtotime($sEndDate)));

/*
		if (strtotime($sStartDate) < strtotime($sContractStart) || strtotime($sStartDate) > strtotime($sContractEnd))
			$sStartDate = "0000-00-00";

		if (strtotime($sEndDate) < strtotime($sContractStart) || strtotime($sEndDate) > strtotime($sContractEnd))
			$sEndDate = "0000-00-00";


		if (($sStartDate == "0000-00-00" || $sEndDate == "0000-00-00") && $iStageDays[$iStage] > 0)
		{
			if ($sStartDate == "0000-00-00")
				$sStartDate = date("Y-m-d", (strtotime($sPrevDate) + 86400));


			if ($sEndDate == "0000-00-00")
				$sEndDate = date("Y-m-d", (strtotime($sStartDate) + ($iStageDays[$iStage] * 86400)));
		}
*/


		$sSQL = "UPDATE tbl_contract_schedule_details SET start_date = '$sStartDate',
														  end_date   = '$sEndDate'
				 WHERE schedule_id='$iScheduleId' AND stage_id='$iStage'";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == false)
			break;


		if ($sEndDate != "0000-00-00")
			$sPrevDate = $sEndDate;
	}
	
	if ($bFlag == true)
	{
		$iPlannedStage    = getDbValue("csd.stage_id", "tbl_contract_schedules cs, tbl_contract_schedule_details csd, tbl_stages s", "cs.id=csd.schedule_id AND s.id=csd.stage_id AND cs.school_id='$iSchool' AND csd.end_date<=CURDATE( )", "csd.end_date DESC, s.position DESC");
		$iStagePosition   = getDbValue("position", "tbl_stages", "id='$iPlannedStage'");
		$fPlannedProgress = @round(getDbValue("COALESCE(SUM(weightage), '0')", "tbl_stages", "position<='$iStagePosition' AND `type`='$sSchoolType'"), 2);

		
		$sSQL  = "UPDATE tbl_schools SET planned='$fPlannedProgress' WHERE id='$iSchool'";
		$bFlag = $objDb->execute($sSQL);
	}

	if ($bFlag == true)
	{
		$objDb->execute("COMMIT");
?>
<script type="text/javascript">
<!--
	parent.$.colorbox.close( );
	parent.showMessage("#GridMsg", "success", "The selected Construction Schedule has been Updated successfully.");
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
?>