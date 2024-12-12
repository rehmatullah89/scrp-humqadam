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

	header("Expires: Tue, 01 Jan 2000 12:12:12 GMT");
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	@require_once("../../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );

	if ($sUserRights["Delete"] != "Y")
	{
		print "info|-|You don't have enough Rights to perform the requested operation.";

		exit( );
	}


	$sStages = IO::strValue("Stages");

	if ($sStages != "")
	{
		$iStages = @explode(",", $sStages);


		$objDb->execute("BEGIN");

		for ($i = 0; $i < count($iStages); $i ++)
		{
			$sChain = "";

			$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='{$iStages[$i]}'";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($j = 0; $j < $iCount; $j ++)
			{
				if ($j > 0)
					$sChain .= ",";

				$sChain .= $objDb->getField($j, 0);
			}


			if ($sChain != "")
			{
				$sSQL = "SELECT id FROM tbl_stages WHERE FIND_IN_SET(parent_id, '$sChain')";
				$objDb->query($sSQL);

				$iCount = $objDb->getCount( );

				for ($j = 0; $j < $iCount; $j ++)
					$sChain .= (",".$objDb->getField($j, 0));
			}


			if ($sChain != "")
			{
				$sSQL = "SELECT id FROM tbl_stages WHERE FIND_IN_SET(parent_id, '$sChain')";
				$objDb->query($sSQL);

				$iCount = $objDb->getCount( );

				for ($j = 0; $j < $iCount; $j ++)
					$sChain .= (",".$objDb->getField($j, 0));
			}



			$sSQL  = "DELETE FROM tbl_stages WHERE id='{$iStages[$i]}'";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == true)
			{
				$sSQL  = "UPDATE tbl_inspections SET status='I', stage_id='0' WHERE (stage_id='{$iStages[$i]}' OR FIND_IN_SET(stage_id, '$sChain'))";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == true)
			{
				$sSQL  = "UPDATE tbl_stages SET status='I', parent_id='0' WHERE FIND_IN_SET(id, '$sChain')";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == false)
				break;
		}
		
		if ($bFlag == true)
		{
			$sSQL = "SELECT id, storey_type, design_type FROM tbl_schools WHERE status='A' AND dropped!='Y' AND qualified='Y' ORDER BY id";
			$objDb->query($sSQL);
			
			$iCount = $objDb->getCount( );
			
			for ($i = 0; $i < $iCount; $i ++)
			{
				$iSchool     = $objDb->getField($i, "id");
				$sStoreyType = $objDb->getField($i, "storey_type");
				$sDesignType = $objDb->getField($i, "design_type");
				
				
				$sSchoolType         = (($sDesignType == "B") ? "B" : $sStoreyType);
				$iMileStoneStage  = getDbValue("id", "tbl_stages", "parent_id='0' AND `type`='$sSchoolType'", "position DESC");
				$iMilestoneStages = array( );
				
				$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iMileStoneStage' ORDER BY position DESC";
				$objDb->query($sSQL);
				
				$iCount = $objDb->getCount( );
				
				for ($j = 0; $j < $iCount; $j ++)
					$iMilestoneStages[] = $objDb->getField($j, 0);
				
			
				$iMilestonePosition = getDbValue("position", "tbl_stages", "id='$iMileStoneStage'");
				$iLastStage         = getDbValue("s.id", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.weightage>'0' AND i.school_id='$iSchool' AND s.position>'$iMilestonePosition' AND i.status='P' AND i.stage_completed='Y' AND s.type='$sSchoolType' AND s.skip!='Y'", "s.position DESC");
				$iLastMilestone     = getDbValue("parent_id", "tbl_stages", "id='$iLastStage'");

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
							

				$iLastStage      = (int)getDbValue("s.id", "tbl_inspections i, tbl_stages s", "s.id=i.stage_id AND s.weightage>'0' AND i.school_id='$iSchool' AND i.status='P' AND i.stage_completed='Y' AND s.type='$sSchoolType' AND s.skip!='Y'", "s.position DESC");
				$iStagePosition  = getDbValue("position", "tbl_stages", "id='$iLastStage'");
				$fSchoolProgress = @round(getDbValue("COALESCE(SUM(weightage), '0')", "tbl_stages", "position<='$iStagePosition' AND `type`='$sSchoolType'"), 2);
				
				$sLastInspection = (($iLastStage > 0) ? getDbValue("created_at", "tbl_inspections", "id='$iLastStage'") : "0000-00-00 00:00:00");
				$sLastInspection = (($sLastInspection == "") ? "0000-00-00 00:00:00" : $sLastInspection);			
				
				$sCompletionStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "`type`='$sSchoolType' AND parent_id>'0' AND name='Finishing & Demobilization'");
				$sCompleted       = ((getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool' AND FIND_IN_SET(stage_id, '$sCompletionStages') AND status='P' AND stage_completed='Y'") > 0) ? "Y" : "N");
				$fSchoolProgress  = (($sCompleted == "Y") ? 100 : $fSchoolProgress);


				$sSQL = "UPDATE tbl_schools SET progress          = '$fSchoolProgress',
												last_inspection   = '$sLastInspection',
												last_stage_id     = '$iLastStage',
												last_milestone_id = '$iLastMilestone',
												completed         = '$sCompleted'
						 WHERE id='$iSchool'";
				$bFlag = $objDb2->execute($sSQL);
				
				if ($bFlag == false)
					break;
			}
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			if (count($iStages) > 1)
				print "success|-|The selected Stages have been Deleted successfully.";

			else
				print "success|-|The selected Stage has been Deleted successfully.";
		}

		else
		{
			$objDb->execute("ROLLBACK");

			print "error|-|An error occured while processing your request, please try again.";
		}
	}

	else
		print "info|-|Inavlid Stage Delete request.";


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>