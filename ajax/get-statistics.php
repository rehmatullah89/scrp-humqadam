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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	$sKeywords = IO::strValue("Keywords");
	$iPackage  = IO::intValue("Package");
	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");
	$sStatus   = IO::strValue("Status");


	$sConditions = "WHERE status='A' AND dropped!='Y' AND adopted='Y' 
	                      AND province_id IN ({$_SESSION['AdminProvinces']})
						  AND district_id IN ({$_SESSION['AdminDistricts']})";

	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND id IN ({$_SESSION['AdminSchools']}) ";


	if ($sKeywords != "")
	{
		$sKeywords    = str_replace(" ", "%", $sKeywords);

		$sConditions .= " AND (name LIKE '%{$sKeywords}%' OR code LIKE '{$sKeywords}' OR address LIKE '%{$sKeywords}%') ";
	}

	if ($iPackage > 0 || $iProvince > 0)
	{
		$sConditions .= " AND (";

		if ($iPackage > 0)
		{
			$sSchools = getDbValue("schools", "tbl_packages", "id='$iPackage'");

			$sConditions .= " FIND_IN_SET(id, '$sSchools') ";
		}

		if ($iPackage > 0 && $iProvince > 0)
			$sConditions .= " OR ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " (";

		if ($iProvince > 0)
			$sConditions .= " province_id='$iProvince' ";

		if ($iDistrict > 0)
			$sConditions .= " AND district_id='$iDistrict' ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " )";

		$sConditions .= ")";
	}



	$sSubConditions = "";

	if ($_SESSION["AdminSchools"] != "")
		$sSubConditions = " AND id IN ({$_SESSION['AdminSchools']}) ";


	if ($sStatus == "Active" || $sStatus == "InActive")
	{
		$iMilestoneStageS = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='S'", "position DESC");
		$iMilestoneStageD = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='D'", "position DESC");
		$iMilestoneStageT = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='T'", "position DESC");
		$iMilestoneStageB = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='B'", "position DESC");
		$iMilestoneStages = array( );
		
		$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND (`type`='R' OR (`type`='S' AND position>'$iMilestoneStageS') OR (`type`='D' AND position>'$iMilestoneStageD') OR (`type`='T' AND position>'$iMilestoneStageT') OR (`type`='B' AND position>'$iMilestoneStageB')) ORDER BY position";
		$objDb->query($sSQL);
		
		$iCount = $objDb->getCount( );
		
		for ($i = 0; $i < $iCount; $i ++)
			$iMilestoneStages[] = $objDb->getField($i, 0);
			
		$sMilestoneStages = @implode(",", $iMilestoneStages);


		if ($sStatus == "Active")
			$sConditions .= " AND id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions) ";

		else if ($sStatus == "InActive")
			$sConditions .= " AND id NOT IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions) ";
	}

	else if ($sStatus == "Delayed" || $sStatus == "OnTime")
	{
		$sSQL = "SELECT cs.school_id, csd.stage_id, MAX(csd.end_date) AS _EndDate
				 FROM tbl_contract_schedules cs, tbl_contract_schedule_details csd
				 WHERE cs.id=csd.schedule_id AND csd.end_date < CURDATE( ) AND csd.end_date!='0000-00-00'
				 GROUP BY cs.school_id";
		$objDb->query($sSQL);

		$iCount          = $objDb->getCount( );
		$iDelayedSchools = array( );
		$iOnTimeSchools  = array( );
		$iUserSchools    = array( );

		if ($_SESSION["AdminSchools"] != "")
			$iUserSchools = @explode(",", $_SESSION["AdminSchools"]);

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iSchool  = $objDb->getField($i, "school_id");
			$iStage   = $objDb->getField($i, "stage_id");
			$iEndDate = $objDb->getField($i, "_EndDate");

			if ($_SESSION["AdminSchools"] != "" && !@in_array($iSchool, $iUserSchools))
				continue;


			$iInspections = getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool' AND stage_id='$iStage' AND status='P' AND stage_completed='Y'");

			if ($iInspections == 0)
				$iDelayedSchools[] = $iSchool;

			else
				$iOnTimeSchools[] = $iSchool;
		}


		if ($sStatus == "Delayed")
		{
			$sDelayedSchools = @implode(",", $iDelayedSchools);
			$sConditions    .= " AND id IN ($sDelayedSchools) ";
		}

		if ($sStatus == "OnTime")
		{
			$sOnTimeSchools = @implode(",", $iOnTimeSchools);
			$sConditions   .= " AND id IN ($sOnTimeSchools) ";
		}
	}



	$iSchool     = 0;
	$sSchool     = "";
	$sContract   = "N/A";
	$iContractor = 0;


	$sSQL = "SELECT id, name FROM tbl_schools $sConditions ORDER BY name";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) == 1)
	{
		$iSchool = $objDb->getField($i, "id");
		$sSchool = $objDb->getField($i, "name");


		$sSQL = "SELECT title, contractor_id, start_date, end_date FROM tbl_contracts WHERE status='A' AND FIND_IN_SET('$iSchool', schools) ORDER BY id DESC LIMIT 1";
		$objDb->query($sSQL);

		$sContract   = $objDb->getField(0, "title");
		$iContractor = $objDb->getField(0, "contractor_id");
		$sStartDate  = $objDb->getField(0, "start_date");
		$sEndDate    = $objDb->getField(0, "end_date");
	}


					
	$sSQL = "SELECT SUM(ex_class_rooms) AS _PreClassrooms, 
					SUM((ex_student_toilets + ex_staff_toilets)) AS _PreToilets, 
					SUM(cost) AS _Cost, 
					SUM(COALESCE(revised_cost, 0)) AS _RevisedCost, 
					SUM(covered_area) AS _CoveredArea, 
	                SUM((progress / 100) * covered_area) AS _Weightage,
					SUM((planned / 100) * covered_area) AS _Planned
	         FROM tbl_schools
	         $sConditions";
	$objDb->query($sSQL);

	$iSchools       = $objDb->getField(0, "_Schools");
	$iPreClassrooms = $objDb->getField(0, "_PreClassrooms");
	$iPreToilets    = $objDb->getField(0, "_PreToilets");
	$fCost          = $objDb->getField(0, "_Cost");
	$fRevisedCost   = $objDb->getField(0, "_RevisedCost");
	$fCoveredArea   = $objDb->getField(0, "_CoveredArea");
	$fWeightage     = $objDb->getField(0, "_Weightage");
	$fPlanned       = $objDb->getField(0, "_Planned");

	
	$sSQL = "SELECT SUM(IF(work_type='N', class_rooms, '0')) AS _NewClassrooms, 
					SUM(IF(work_type='N', (student_toilets + staff_toilets), '0')) AS _NewToilets, 
					SUM(IF(work_type='R', class_rooms, '0')) AS _RehabClassrooms, 
					SUM(IF(work_type='R', (student_toilets + staff_toilets), '0')) AS _RehabToilets
	         FROM tbl_school_blocks
	         WHERE school_id IN (SELECT id FROM tbl_schools $sConditions)";
	$objDb->query($sSQL);

	$iNewClassrooms   = $objDb->getField(0, "_NewClassrooms");
	$iRehabClassrooms = $objDb->getField(0, "_RehabClassrooms");
	$iNewToilets      = $objDb->getField(0, "_NewToilets");
	$iRehabToilets    = $objDb->getField(0, "_RehabToilets");	

	
	$iTotalClassrooms = ($iPreClassrooms + $iNewClassrooms);
	$iTotalToilets    = ($iPreToilets + $iNewToilets);

	$fProgress        = @round(($fWeightage / $fCoveredArea) * 100);
	$fPlanned         = @round(($fPlanned / $fCoveredArea) * 100);
?>
		<h1 class="line"><span>Statistics</span></h1>

		<table border="0" cellspacing="0" cellpadding="0" width="100%">
		  <tr valign="top">
			<td width="340">
<?
	if ($sSchool != "")
	{
?>
			  <b>School:</b> <span style="color:#89cf34;"><?= $sSchool ?></span><br />
<?
	}
?>
			  <b>Package:</b> <span style="color:#89cf34; max-width:"><?= (($iSchool > 0) ? getDbValue("title", "tbl_packages", "status='A' AND FIND_IN_SET('$iSchool', schools)", "id DESC") : "N/A") ?></span><br />
			  <b>Contractor:</b> <span><?= (($iContractor > 0) ? getDbValue("company", "tbl_contractors", "id='$iContractor'") : "N/A") ?></span><br />
			  <b>Contract:</b> <span><?= $sContract ?></span><br />
			  <b>Covered Area:</b> <span><?= formatNumber($fCoveredArea, false) ?> sft</span><br />
<?
	if ($iSchools > 1)
	{
?>
			  <b>No of Active Schools:</b> <span><?= formatNumber($iSchools, false) ?></span><br />
<?
	}
?>
			</td>

			<td></td>

			<td width="340" align="right">
			  <b>Total Estimated Construction<br />Contract Value Awarded (PKR):</b> <span><?= formatNumber((($fRevisedCost > 0) ? $fRevisedCost : $fCost), false) ?></span><br />
			  <b>Total No of Classrooms:</b> <span><?= formatNumber($iTotalClassrooms, false) ?></span><br />
			  <b>Pre-Existing No of Classrooms:</b> <span><?= formatNumber($iPreClassrooms, false) ?></span><br />
			  <b>New Classrooms being Added:</b> <span><?= formatNumber($iNewClassrooms, false) ?></span><br />
			  <b>Total No of Toilets:</b> <span><?= formatNumber($iTotalToilets, false) ?></span><br />
			  <b>Pre-Existing No of Toilets:</b> <span><?= formatNumber($iPreToilets, false) ?></span><br />
			  <b>New Toilets being Added:</b> <span><?= formatNumber($iNewToilets, false) ?></span><br />
			</td>
		  </tr>
		</table>


		<div id="Progress" params="Keywords=<?= $sKeywords ?>&Package=<?= $iPackage ?>&Province=<?= $iProvince ?>&District=<?= $iDistrict ?>&Status=<?= $sStatus ?>"><?= formatNumber($fProgress, false) ?>%</div>
		<div id="Planned" params="Keywords=<?= $sKeywords ?>&Package=<?= $iPackage ?>&Province=<?= $iProvince ?>&District=<?= $iDistrict ?>&Status=<?= $sStatus ?>"><?= formatNumber($fPlanned, false) ?>%</div>

		<br />
		<br />
		|-|
		<br />

		<h2>Actual Progress</h2>
		<br />

		<table border="0" cellspacing="0" cellpadding="0" width="100%">
		  <tr>
			<td width="56">
			  <div class="circle<?= (($fProgress >= $fPlanned) ? ' green' : ' red') ?>">
				<div class="radius"></div>
				<div class="text"><?= formatNumber($fProgress, false) ?>%</div>
			  </div>
			</td>

			<td>
			  <div class="outerLine">
				<div class="innerLine<?= (($fProgress >= $fPlanned) ? ' green' : ' red') ?>" style="width:<?= @round($fProgress) ?>%;"></div>
			  </div>
			</td>

			<td width="56">
			  <div class="circle<?= (($fProgress >= $fPlanned) ? ' green' : (($fProgress == 100) ? ' red' : '')) ?>">
				<div class="radius"></div>
			  </div>
			</td>
		  </tr>
		</table>
		
		<br />
		<br />
		<h2>Planned ProgresS</h2>
		<br />

		<table border="0" cellspacing="0" cellpadding="0" width="100%">
		  <tr>
			<td width="56">
			  <div class="circle<?= (($fPlanned > 0) ? ' blue' : '') ?>">
				<div class="radius"></div>
				<div class="text"><?= formatNumber($fPlanned, false) ?>%</div>
			  </div>
			</td>

			<td>
			  <div class="outerLine">
				<div class="innerLine blue" style="width:<?= @round($fPlanned) ?>%;"></div>
			  </div>
			</td>

			<td width="56">
			  <div class="circle<?= (($fPlanned == 100) ? ' blue' : '') ?>">
				<div class="radius"></div>
			  </div>
			</td>
		  </tr>
		</table>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>