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
	$objDb3      = new Database( );
	$objDb4      = new Database( );

	if ($sUserRights["Delete"] != "Y")
	{
		print "info|-|You don't have enough Rights to perform the requested operation.";

		exit( );
	}


	$sInspections = IO::strValue("Inspections");

	if ($sInspections != "")
	{
		$iInspections = @explode(",", $sInspections);
		$sPictures    = array( );
		$sFiles       = array( );
		$sDocuments   = array( );


		$objDb->execute("BEGIN");

		for ($i = 0; $i < count($iInspections); $i ++)
		{
			$sSQL = "SELECT school_id, block, file, picture FROM tbl_inspections WHERE id='{$iInspections[$i]}'";
			$objDb->query($sSQL);

			$iSchool = $objDb->getField(0, "school_id");
			$iBlock  = $objDb->getField(0, "block");

			if ($objDb->getField(0, "picture") != "")
				$sPictures[] = $objDb->getField(0, "picture");

			if ($objDb->getField(0, "file") != "")
				$sFiles[] = $objDb->getField(0, "file");


			$sSQL = "SELECT file FROM tbl_inspection_documents WHERE inspection_id='{$iInspections[$i]}'";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($j = 0; $j < $iCount; $j ++)
				$sDocuments[] = $objDb->getField($j, 0);


			$sSQL  = "DELETE FROM tbl_inspections WHERE id='{$iInspections[$i]}'";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_inspection_documents WHERE inspection_id='{$iInspections[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_inspection_measurements WHERE inspection_id='{$iInspections[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == true)
			{
				$sSQL = "SELECT storey_type, design_type, work_type FROM tbl_school_blocks WHERE school_id='$iSchool' AND block='$iBlock'";
				$objDb->query($sSQL);

				$sStoreyType = $objDb->getField(0, "storey_type");
				$sDesignType = $objDb->getField(0, "design_type");
				$sWorkType   = $objDb->getField(0, "work_type");				

				
				$sBlockType       = (($sWorkType == "R") ? "R" : (($sDesignType == "B") ? "B" : $sStoreyType));		
				$iMileStoneStage  = getDbValue("id", "tbl_stages", "status='A' AND parent_id='0' AND `type`='$sBlockType'", "position DESC");
				$iMilestoneStages = array( );
				
				$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND parent_id='$iMileStoneStage' ORDER BY position DESC";
				$objDb->query($sSQL);
				
				$iCount = $objDb->getCount( );
				
				for ($j = 0; $j < $iCount; $j ++)
					$iMilestoneStages[] = $objDb->getField($j, 0);
				
			
				$iMilestonePosition = getDbValue("position", "tbl_stages", "id='$iMileStoneStage'");
				$iLastStage         = getDbValue("s.id", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND s.weightage>'0' AND i.school_id='$iSchool' AND i.block='$iBlock' AND s.position>'$iMilestonePosition' AND i.status='P' AND i.stage_completed='Y' AND s.type='$sBlockType' AND s.skip!='Y'", "s.position DESC");
				$iLastMilestone     = getDbValue("parent_id", "tbl_stages", "id='$iLastStage'");

				if (!@in_array($iLastMilestone, $iMilestoneStages))
					$iLastMilestone = getDbValue("parent_id", "tbl_stages", "id='$iLastMilestone'");


				if (@in_array($iLastMilestone, $iMilestoneStages))
				{
					$sMilestoneStages = @implode(",", $iMilestoneStages);
					$iLastPosition    = getDbValue("position", "tbl_stages", "id='$iLastMilestone'");
					$iLastMilestone   = (int)getDbValue("id", "tbl_stages", "status='A' AND FIND_IN_SET(id, '$sMilestoneStages') AND position<'$iLastPosition'", "position DESC");
				}
				
				else
					$iLastMilestone = 0;
							

				$iLastStage        = (int)getDbValue("s.id", "tbl_inspections i, tbl_stages s", "s.id=i.stage_id AND s.weightage>'0' AND i.school_id='$iSchool' AND i.block='$iBlock' AND i.status='P' AND i.stage_completed='Y' AND s.type='$sBlockType' AND s.skip!='Y'", "s.position DESC");
				$iStagePosition    = getDbValue("position", "tbl_stages", "id='$iLastStage'");
				$fSchoolProgress   = @round(getDbValue("COALESCE(SUM(weightage), '0')", "tbl_stages", "status='A' AND position<='$iStagePosition' AND `type`='$sBlockType'"), 2);
				
				$sLastInspection   = (($iLastStage > 0) ? getDbValue("created_at", "tbl_inspections", "id='$iLastStage'") : "0000-00-00 00:00:00");
				$sLastInspection   = (($sLastInspection == "") ? "0000-00-00 00:00:00" : $sLastInspection);			
				
				$sFinishingStages  = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "status='A' AND `type`='$sBlockType' AND name='Finishing & Demobilization'");
				$sCompletionStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "status='A' AND `type`='$sBlockType' AND FIND_IN_SET(parent_id, '$sFinishingStages')");
				$sCompleted        = ((getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool' AND block='$iBlock' AND FIND_IN_SET(stage_id, '$sCompletionStages') AND status='P' AND stage_completed='Y'") > 0) ? "Y" : "N");
				$fBlockProgress    = (($sCompleted == "Y") ? 100 : $fBlockProgress);
				
				if ($sBlockType == "R" && $sCompleted != "Y")
					$fBlockProgress = @round(getDbValue("COALESCE(SUM(weightage), '0')", "tbl_stages", "status='A' AND `type`='$sBlockType' AND id IN (SELECT DISTINCT(stage_id) FROM tbl_inspections WHERE school_id='$iSchool' AND block='$iBlock' AND status='P' AND stage_completed='Y')"), 2);
				
				if ($sBlockType == "R")
					$iLastMilestone = $iLastStage;
				

				$sSQL = "UPDATE tbl_school_blocks SET progress          = '$fBlockProgress',
													  last_inspection   = '$sLastInspection',
													  last_stage_id     = '$iLastStage',
													  last_milestone_id = '$iLastMilestone',
													  completed         = '$sCompleted'
						 WHERE school_id='$iSchool' AND block='$iBlock'";
				$bFlag = $objDb->execute($sSQL);
				
				if ($bFlag == true)
				{
					$sCompleted = ((getDbValue("COUNT(1)", "tbl_school_blocks", "school_id='$iSchool' AND completed!='Y'") > 0) ? "N" : "Y");
					
					
					$sSQL = "SELECT SUM(covered_area) AS _CoveredArea, SUM((progress / 100) * covered_area) AS _Weightage FROM tbl_school_blocks WHERE school_id='$iSchool'";
					$objDb->query($sSQL);

					$fCoveredArea = $objDb->getField(0, "_CoveredArea");
					$fWeightage   = $objDb->getField(0, "_Weightage");
					
					$fProgress    = @round(($fWeightage / $fCoveredArea) * 100);
					
					$sSQL  = "UPDATE tbl_schools SET completed = '$sCompleted',
					                                 progress  = '$fProgress'
							  WHERE id='$iSchool'";
					$bFlag = $objDb->execute($sSQL);
				}				
			}

			if ($bFlag == false)
				break;
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			if (count($iInspections) > 1)
				print "success|-|The selected Inspection Records have been Deleted successfully.";

			else
				print "success|-|The selected Inspection Record has been Deleted successfully.";


			for ($i = 0; $i < count($sFiles); $i ++)
				@unlink($sRootDir.INSPECTIONS_DOC_DIR.$sFiles[$i]);

			for ($i = 0; $i < count($sPictures); $i ++)
			{
				@unlink($sRootDir.INSPECTIONS_IMG_DIR.$sPictures[$i]);
				@unlink($sRootDir.INSPECTIONS_IMG_DIR."thumbs/".$sPictures[$i]);
			}

			for ($i = 0; $i < count($sDocuments); $i ++)
			{
				@unlink($sRootDir.INSPECTIONS_DOC_DIR.$sDocuments[$i]);
				@unlink($sRootDir.INSPECTIONS_IMG_DIR.$sDocuments[$i]);
			}
		}

		else
		{
			print "error|-|An error occured while processing your request, please try again.";

			$objDb->execute("ROLLBACK");
		}
	}

	else
		print "info|-|Inavlid Inspection Record Delete request.";


	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDb4->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>