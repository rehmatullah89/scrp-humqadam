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

	$sType      = IO::strValue('ddType');
	$sWorkType  = IO::strValue("ddWorkType");
	$sName      = IO::strValue("txtName");
	$iParent    = IO::intValue("ddParent");
	$sUnit      = IO::strValue("ddUnit");
	$fWeightage = IO::floatValue("txtWeightage");
	$iDays      = IO::intValue("txtDays");
	$sReasons   = @implode(",", IO::getArray('cbReasons'));
	$sStatus    = IO::strValue("ddStatus");
	$sSkip      = IO::strValue("cbSkip");
	$iPosition  = IO::intValue("txtPosition");
	$sAdjust    = IO::strValue("cbAdjust");



	if ($sType == "" || $sWorkType == "" || $sName == "" || $fWeightage < 0 || $iDays < 0 || $sStatus == "" || $iPosition <= 0)
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_stages WHERE (`type`='$sType' AND name LIKE '$sName' AND parent_id='$iParent') AND id!='$iStageId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "STAGE_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$sStages     = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iStageId'");
		$sParentWork = getDbValue("work", "tbl_stages", "id='$iParent'");

		if ($sStages != "")
			$fWeightage = 0;
		
		if ($sParentWork == "N" || $sParentWork == "R")
			$sWorkType = $sParentWork;
		
		
		$sSQL = "SELECT weightage, skip, position FROM tbl_stages WHERE id='$iStageId'";
		$objDb->query($sSQL);
		
		$fOldWeightage = $objDb->getField(0, "weightage");
		$sOldSkip      = $objDb->getField(0, "skip");
		$iOldPosition  = $objDb->getField(0, "position");

		
		$bFlag = $objDb->execute("BEGIN");

		$sSQL = "UPDATE tbl_stages SET parent_id       = '$iParent',
									   name            = '$sName',
									   unit            = '$sUnit',
									   weightage       = '$fWeightage',
									   days            = '$iDays',
									   failure_reasons = '$sReasons',
									   status          = '$sStatus',
									   skip            = '$sSkip',
									   `type`  		   = '$sType',
									   work            = '$sWorkType',
									   position        = '$iPosition'
		         WHERE id='$iStageId'";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == true && $iPosition != $iOldPosition && $sAdjust == "Y")
		{
			if ($iPosition < $iOldPosition)
			{
				$sSQL  = "UPDATE tbl_stages SET position=(position + '1') WHERE position>='$iPosition' AND position<='$iOldPosition' AND `type`='$sType' AND id!='$iStageId'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			else
			{

				$sSQL  = "UPDATE tbl_stages SET position=(position - '1') WHERE position>='$iOldPosition' AND position<='$iPosition' AND `type`='$sType' AND id!='$iStageId'";
				$bFlag = $objDb->execute($sSQL);
			}
		}

		if ($bFlag == true && ($fOldWeightage != $fWeightage || $sOldSkip != $sSkip))
		{
			$sSQL = "SELECT id FROM tbl_schools WHERE status='A' AND dropped!='Y' AND (design_type='$sType' OR (design_type='R' AND storey_type='$sType')) ORDER BY id";
			$objDb->query($sSQL);
			
			$iCount = $objDb->getCount( );
			
			for ($i = 0; $i < $iCount; $i ++)
			{
				$iSchool = $objDb->getField($i, "id");
				
				
				$iLastCompletedStage = getDbValue("s.position", "tbl_inspections i, tbl_stages s", "s.id=i.stage_id AND i.school_id='$iSchool' AND i.status='P' AND i.stage_completed='Y' AND s.skip!='Y' AND s.type='$sType'", "s.position DESC");
				$fSchoolProgress     = @round(getDbValue("COALESCE(SUM(weightage), '0')", "tbl_stages", "position <= '$iLastCompletedStage' AND `type`='$sType'"), 2);
				
				$sCompletionStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "`type`='$sSchoolType' AND parent_id>'0' AND name='Finishing & Demobilization'");
				$sCompleted        = ((getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool' AND FIND_IN_SET(stage_id, '$sCompletionStages') AND status='P' AND stage_completed='Y'") > 0) ? "Y" : "N");
				$fSchoolProgress   = (($sCompleted == "Y") ? 100 : $fSchoolProgress);


				$sSQL  = "UPDATE tbl_schools SET progress='$fSchoolProgress' WHERE id='$iSchool'";
				$bFlag = $objDb2->execute($sSQL);

				if ($bFlag == false)
					break;
			}
		}
		
		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			
			$sParentStage = "";


			if ($iParent > 0)
			{
				$sSQL = "SELECT parent_id, name FROM tbl_stages WHERE id='$iParent'";
				$objDb->query($sSQL);

				$iParent = $objDb->getField(0, "parent_id");
				$sParent = $objDb->getField(0, "name");

				$sParentStage = $sParent;


				if ($iParent > 0)
				{
					$sSQL = "SELECT parent_id, name FROM tbl_stages WHERE id='$iParent'";
					$objDb->query($sSQL);

					$iParent = $objDb->getField(0, "parent_id");
					$sParent = $objDb->getField(0, "name");


					$sParentStage = "{$sParent} &raquo; {$sParentStage}";

					if ($iParent > 0)
						$sParentStage = (getDbValue("name", "tbl_stages", "id='$iParent'")." &raquo; {$sParentStage}");
				}
			}
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= $iPosition ?>";
		sFields[1] = "<?= addslashes($sName) ?>";
		sFields[2] = "<?= addslashes($sParentStage) ?>";
		sFields[3] = "<?= formatNumber($sUnit) ?>";
		sFields[4] = "<?= formatNumber($fWeightage) ?>";
		sFields[5] = "<?= formatNumber($iDays, false) ?>";
		sFields[6] = "";
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnToggle" id="<?= $iStageId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" /> ');
		sFields[6] = (sFields[6] + '<img class="icnEdit" id="<?= $iStageId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnDelete" id="<?= $iStageId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}
?>

		parent.updateRecord(<?= $iStageId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Stage has been Updated successfully.");
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