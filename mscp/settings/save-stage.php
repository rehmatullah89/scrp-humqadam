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

	$sType      = IO::strValue("ddType");
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
	$bError     = true;


	if ($sType == "" || $sWorkType == "" || $sName == "" || $fWeightage < 0 || $iDays < 0 || $iPosition < 0 || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_stages WHERE `type`='$sType' AND name LIKE '$sName' AND parent_id='$iParent'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "STAGE_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$sParentWork = getDbValue("work", "tbl_stages", "id='$iParent'");
		
		if ($sParentWork == "N" || $sParentWork == "R")
			$sWorkType = $sParentWork;

		
		$bFlag = $objDb->execute("BEGIN");
		
		
		$iStage = getNextId("tbl_stages");

		$sSQL = "INSERT INTO tbl_stages SET id              = '$iStage',
											parent_id       = '$iParent',
											name            = '$sName',
											unit            = '$sUnit',
											weightage       = '$fWeightage',
											days            = '$iDays',
											failure_reasons = '$sReasons',
											skip            = '$sSkip',
											position        = '$iStage',
											`type`	  	    = '$sType',
											work            = '$sWorkType',
											status          = '$sStatus'";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == true)
		{
			$sSQL  = "UPDATE tbl_stages SET weightage='0' WHERE id='$iParent'";
			$bFlag = $objDb->execute($sSQL);
		}
		
		if ($bFlag == true && $iPosition > 0)
		{
			$sSQL  = "UPDATE tbl_stages SET position='$iPosition' WHERE id='$iStage'";
			$bFlag = $objDb->execute($sSQL);
			
			if ($bFlag == true && getDbValue("count(1)", "tbl_stages", "position='$iPosition' AND `type`='$sType'") > 1)
			{
				$sSQL  = "UPDATE tbl_stages SET position=(position + '1') WHERE position>='$iPosition' AND `type`='$sType' AND id!='$iStage'";
				$bFlag = $objDb->execute($sSQL);
			}
		}
		
		if ($bFlag == true && $fWeightage > 0)
		{
			$sSQL = "SELECT id FROM tbl_schools WHERE status='A' AND dropped!='Y' AND (design_type='$sType' OR (design_type='R' AND storey_type='$sType')) ORDER BY id";
			$objDb->query($sSQL);
			
			$iCount = $objDb->getCount( );
			
			for ($i = 0; $i < $iCount; $i ++)
			{
				$iSchool = $objDb->getField($i, "id");
				
				
				$iLastCompletedStage = getDbValue("s.position", "tbl_inspections i, tbl_stages s", "s.id=i.stage_id AND i.school_id='$iSchool' AND i.status='P' AND i.stage_completed='Y' AND s.skip!='Y' AND s.type='$sType'", "s.position DESC");
				$fSchoolProgress     = @round(getDbValue("COALESCE(SUM(weightage), '0')", "tbl_stages", "position <= '$iLastCompletedStage' AND `type`='$sType'"), 2);
				
				$sCompletionStages   = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "`type`='$sSchoolType' AND parent_id>'0' AND name='Finishing & Demobilization'");
				$sCompleted          = ((getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool' AND FIND_IN_SET(stage_id, '$sCompletionStages') AND status='P' AND stage_completed='Y'") > 0) ? "Y" : "N");
				$fSchoolProgress     = (($sCompleted == "Y") ? 100 : $fSchoolProgress);


				$sSQL  = "UPDATE tbl_schools SET progress='$fSchoolProgress' WHERE id='$iSchool'";
				$bFlag = $objDb2->execute($sSQL);
				
				if ($bFlag == false)
					break;
			}
		}
		
		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");
			
			redirect("stages.php", "STAGE_ADDED");
		}

		else
		{
			$objDb->execute("ROLLBACK");
			
			$_SESSION["Flag"] = "DB_ERROR";
		}
	}
?>