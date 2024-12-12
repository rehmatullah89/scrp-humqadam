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
	$objDb2      = new Database( );

	$iSchool = IO::intValue("School");


	$sSQL = "SELECT name, code, province_id, district_id, blocks, work_type, ex_class_rooms, ex_student_toilets, ex_staff_toilets, cost, revised_cost, covered_area, storey_type, design_type, progress, planned, completed FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);

	$sSchool         = $objDb->getField(0, "name");
	$sCode           = $objDb->getField(0, "code");
	$iProvince       = $objDb->getField(0, "province_id");
	$iDistrict       = $objDb->getField(0, "district_id");
	$iBlocks         = $objDb->getField(0, "blocks");
	$sWorkType       = $objDb->getField(0, "work_type");
	$iPreClassrooms  = $objDb->getField(0, "ex_class_rooms");
	$iPreToilets     = $objDb->getField(0, "ex_student_toilets");
	$iPreToilets    += $objDb->getField(0, "ex_staff_toilets");
	$fCost           = $objDb->getField(0, "cost");
	$fRevisedCost    = $objDb->getField(0, "revised_cost");
	$fCoveredArea    = $objDb->getField(0, "covered_area");
	$fProgress       = $objDb->getField(0, "progress");
	$fPlanned        = $objDb->getField(0, "planned");
	$sStoreyType     = $objDb->getField(0, "storey_type");
	$sDesignType     = $objDb->getField(0, "design_type");
	$sCompleted      = $objDb->getField(0, "completed");
	
	
	
	$sSQL = "SELECT SUM(IF(work_type='N', class_rooms, '0')) AS _NewClassrooms, 
					SUM(IF(work_type='N', (student_toilets + staff_toilets), '0')) AS _NewToilets, 
					SUM(IF(work_type='R', class_rooms, '0')) AS _RehabClassrooms, 
					SUM(IF(work_type='R', (student_toilets + staff_toilets), '0')) AS _RehabToilets
	         FROM tbl_school_blocks
	         WHERE school_id='$iSchool'";
	$objDb->query($sSQL);

	$iNewClassrooms   = $objDb->getField(0, "_NewClassrooms");
	$iRehabClassrooms = $objDb->getField(0, "_RehabClassrooms");
	$iNewToilets      = $objDb->getField(0, "_NewToilets");
	$iRehabToilets    = $objDb->getField(0, "_RehabToilets");	

	
	$iTotalClassrooms = ($iPreClassrooms + $iNewClassrooms);
	$iTotalToilets    = ($iPreToilets + $iNewToilets);	
?>
        <h1 rel="<?= $iSchool ?>">
          <img src="images/icons/close.png" alt="Close" title="Close" />
          <b>&lt;</b> <?= getDbValue("name", "tbl_provinces", "id='$iProvince'") ?>
        </h1>

        <h2><?= $sSchool ?></h2>
        <h4>EMIS CODE: <?= $sCode ?></h4>

        <div id="Accordion">
<?
	$sSchoolType   = (($sDesignType == "B") ? "B" : $sStoreyType);
	$sStagesList   = getList("tbl_stages", "id", "name", "parent_id='0' AND status='A' AND `type`='$sSchoolType'", "position");
	$sSubStages    = array( );
	$sAllSubStages = array( );

	foreach ($sStagesList as $iParent => $sParent)
	{
		$sSubStages[$iParent]    = "";
		$sAllSubStages[$iParent] = "";


		$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND parent_id='$iParent' ORDER BY name";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );


		if ($iCount == 0)
		{
			$sSubStages[$iParent]    = $iParent;
			$sAllSubStages[$iParent] = $iParent;
		}


		for ($i = 0; $i < $iCount; $i ++)
		{
			$iStage = $objDb->getField($i, "id");


			$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "status='A' AND parent_id='$iStage'");

			if ($sChildStages == "")
			{
				$sSubStages[$iStage]      = $iStage;
				$sSubStages[$iParent]    .= ((($sSubStages[$iParent] != "") ? "," : "").$iStage);

				$sAllSubStages[$iStage]   = "{$iParent},{$iStage}";
				$sAllSubStages[$iParent] .= ((($sAllSubStages[$iParent] != "") ? "," : "")."{$iParent},{$iStage}");
			}

			else if ($sChildStages != "")
			{
				$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND parent_id='$iStage' ORDER BY name";
				$objDb2->query($sSQL);

				$iCount2 = $objDb2->getCount( );

				for ($j = 0; $j < $iCount2; $j ++)
				{
					$iSubStage = $objDb2->getField($j, "id");


					$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "status='A' AND parent_id='$iSubStage'");

					if ($sChildStages == "")
					{
						$sSubStages[$iSubStage]    = $iSubStage;
						$sSubStages[$iStage]      .= ((($sSubStages[$iStage] != "") ? "," : "").$iSubStage);
						$sSubStages[$iParent]     .= ((($sSubStages[$iParent] != "") ? "," : "").$iSubStage);

						$sAllSubStages[$iSubStage] = $iSubStage;
						$sAllSubStages[$iStage]   .= ((($sAllSubStages[$iStage] != "") ? "," : "").$iSubStage);
						$sAllSubStages[$iParent]  .= ((($sAllSubStages[$iParent] != "") ? "," : "").$iSubStage);
					}

					else if ($sChildStages != "")
					{
						$sSubStages[$iSubStage]    = $sChildStages;
						$sSubStages[$iStage]      .= ((($sSubStages[$iStage] != "") ? "," : "").$sChildStages);
						$sSubStages[$iParent]     .= ((($sSubStages[$iParent] != "") ? "," : "").$sChildStages);

						$sAllSubStages[$iSubStage] = "{$iSubStage},{$sChildStages}";
						$sAllSubStages[$iStage]   .= ((($sAllSubStages[$iStage] != "") ? "," : "")."{$iSubStage},{$sChildStages}");
						$sAllSubStages[$iParent]  .= ((($sAllSubStages[$iParent] != "") ? "," : "")."{$iSubStage},{$sChildStages}");
					}
				}
			}
		}
	}



	foreach ($sStagesList as $iStage => $sStage)
	{
		$iRequiredStages  = @count(explode(",", $sSubStages[$iStage]));
		$sDocumentStages  = getDbValue("GROUP_CONCAT(DISTINCT(stage_id) SEPARATOR ',')", "tbl_inspections", "school_id='$iSchool' AND FIND_IN_SET(stage_id, '{$sSubStages[$iStage]}')");
		$iCompletedStages = 0;

		if ($sDocumentStages != "")
			$iCompletedStages = @count(explode(",", $sDocumentStages));
?>
          <h3 class="<?= (($iCompletedStages > 0 && $iCompletedStages < $iRequiredStages) ? 'started' : (($iCompletedStages == $iRequiredStages) ? 'Completed' : 'notStarted')) ?>"><?= $sStage ?></h3>

          <div class="stages">
            <div>
              <ul>
<?
		$sSubStagesList = getList("tbl_stages", "id", "name", "parent_id='$iStage' AND status='A'", "position");

		foreach ($sSubStagesList as $iStage => $sStage)
		{
			$iSubStages       = getDbValue("COUNT(1)", "tbl_stages", "parent_id='$iStage' AND status='A'");
			$iRequiredStages  = @count(explode(",", $sSubStages[$iStage]));
			$sDocumentStages  = getDbValue("GROUP_CONCAT(DISTINCT(stage_id) SEPARATOR ',')", "tbl_inspections", "school_id='$iSchool' AND FIND_IN_SET(stage_id, '{$sSubStages[$iStage]}')");
			$iCompletedStages = 0;

			if ($sDocumentStages != "")
				$iCompletedStages = @count(explode(",", $sDocumentStages));
?>
                <li<?= (($iSubStages > 0) ? (' class="subStages" rel="'.$iStage.'"') : '') ?>><span class="<?= (($iCompletedStages > 0 && $iCompletedStages < $iRequiredStages) ? 'working' : (($iCompletedStages == $iRequiredStages) ? 'tick' : 'cross')) ?>"></span><?= $sStage ?></li>
<?
		}
?>
              </ul>
            </div>
          </div>
<?
	}
?>
        </div>


        <ul id="Actions">
          <li><a href="./" class="timeline" id="<?= $iSchool ?>" rel="<?= getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool'") ?>"><img src="images/icons/timeline.png" alt="Timeline" title="Timeline" /></a></li>
<?
	$iInvoice = getDbValue("id", "tbl_invoices", "school_id='$iSchool'", "`date` DESC");

	if ($iInvoice > 0)
	{
?>
          <li><a href="<?= ADMIN_CP_DIR ?>/surveys/export-invoice.php?Id=<?= $iInvoice ?>"><img src="images/icons/documents.png" alt="Documents" title="Documents" /></a></li>
<?
	}
?>
        </ul>
        |-|
<?
	$sSQL = "SELECT id, title, contractor_id, start_date, end_date FROM tbl_contracts WHERE status='A' AND FIND_IN_SET('$iSchool', schools) ORDER BY id DESC LIMIT 1";
	$objDb->query($sSQL);

	$iContract   = $objDb->getField(0, "id");
	$sContract   = $objDb->getField(0, "title");
	$iContractor = $objDb->getField(0, "contractor_id");
	$sStartDate  = $objDb->getField(0, "start_date");
	$sEndDate    = $objDb->getField(0, "end_date");
?>
		<h1 class="line"><span>Statistics</span></h1>

		<table border="0" cellspacing="0" cellpadding="0" width="100%">
		  <tr valign="top">
			<td width="340">
			  <b>School:</b> <span style="color:#89cf34;"><?= $sSchool ?></span><br />
			  <b>Package:</b> <span style="color:#89cf34;"><?= getDbValue("title", "tbl_packages", "status='A' AND FIND_IN_SET('$iSchool', schools)", "id DESC") ?></span><br />
			  <b>Contractor:</b> <span><?= getDbValue("company", "tbl_contractors", "id='$iContractor'") ?></span><br />
			  <b>Contract:</b> <span><?= $sContract ?></span><br />
			  <b>Covered Area:</b> <span><?= formatNumber($fCoveredArea, false) ?> sft</span><br />
			  <b>Associated Engineers:</b><br />
<?
	$sDistrictEngineersList               = getList("tbl_admins", "email", "name", "status='A' AND type_id='9' AND FIND_IN_SET('$iProvince', provinces) AND FIND_IN_SET('$iDistrict', districts) AND FIND_IN_SET('$iSchool', schools)");
//	$sSeniorDistrictEngineersList         = getList("tbl_admins", "email", "name", "status='A' AND type_id='8' AND FIND_IN_SET('$iProvince', provinces) AND FIND_IN_SET('$iDistrict', districts) AND (schools='' OR FIND_IN_SET('$iSchool', schools))");
	$sDistrictManagersList                = getList("tbl_admins", "email", "name", "status='A' AND type_id='7' AND FIND_IN_SET('$iProvince', provinces) AND FIND_IN_SET('$iDistrict', districts) AND (schools='' OR FIND_IN_SET('$iSchool', schools))");
//	$sProvincialConstructionEngineersList = getList("tbl_admins", "email", "name", "status='A' AND type_id='6' AND FIND_IN_SET('$iProvince', provinces) AND FIND_IN_SET('$iDistrict', districts) AND (schools='' OR FIND_IN_SET('$iSchool', schools))");

	foreach($sDistrictEngineersList as $sEmail => $sName)
	{
?>
			  <span><?= $sName ?> <!--<small>(<?= getDbValue("title", "tbl_admin_types", "id='9'") ?>)</small>--></span><br />
<?
	}
	
	if (count($sDistrictEngineersList) == 0)
	{
?>
			  <span><small>Not Assigned Yet</small></span><br />
<?
	}	
/*
	foreach($sSeniorDistrictEngineersList as $sEmail => $sName)
	{
?>
			  <span><?= $sName ?> <small>(<?= getDbValue("title", "tbl_admin_types", "id='8'") ?>)</small></span><br />
<?
	}

	foreach($sProvincialConstructionEngineersList as $sEmail => $sName)
	{
?>
			  <span><?= $sName ?> <small>(<?= getDbValue("title", "tbl_admin_types", "id='6'") ?>)</small></span><br />
<?
	}
*/
?>
			  <b>Associated Manager:</b><br />
<?
	foreach($sDistrictManagersList as $sEmail => $sName)
	{
?>
			  <span><?= $sName ?></span><br />
<?
	}
	
	if (count($sDistrictManagersList) == 0)
	{
?>
			  <span><small>Not Assigned Yet</small></span><br />
<?
	}
	
	
	$iPaidAmount            = getDbValue("SUM((gross_amount - mob_advance))", "tbl_invoices", "school_id='$iSchool' AND status='P'");
	$sCompletionStages      = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "status='A' AND `type`='$sSchoolType' AND parent_id>'0' AND name='Finishing & Demobilization'");
	$sPlannedCompletionDate = getDbValue("end_date", "tbl_contract_schedules", "school_id='$iSchool' AND contract_id='$iContract'");
	$iLastCompletedStage    = getDbValue("i.stage_id", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND i.status='P' AND i.stage_completed='Y' AND s.weightage>'0' AND i.school_id='$iSchool'", "s.position DESC");
	
	if ($sCompleted == "Y")
	{
		$sProjectedCompletionDate = getDbValue("MIN(`date`)", "tbl_inspections", "status='P' AND stage_completed='Y' AND school_id='$iSchool' AND FIND_IN_SET(stage_id, '$sCompletionStages')");
		$iDelayDays               = @round((strtotime($sProjectedCompletionDate) - strtotime($sPlannedCompletionDate)) / 86400);
	}
	
	else if ($sPlannedCompletionDate != "" && $sPlannedCompletionDate != "0000-00-00" && $iLastCompletedStage > 0)
	{
		$sLastStageCompletionDate = getDbValue("MAX(date)", "tbl_inspections", "stage_id='$iLastCompletedStage' AND status='P' AND stage_completed='Y' AND school_id='$iSchool'");	
		$sLastStagePlannedDate    = getDbValue("IF(csd.end_date='',csd.start_date, csd.end_date)", "tbl_contract_schedules cs, tbl_contract_schedule_details csd", "cs.id=csd.schedule_id AND cs.school_id='$iSchool' AND cs.contract_id='$iContract' AND csd.stage_id='$iLastCompletedStage'");

		if ($sLastStagePlannedDate != "" && $sLastStagePlannedDate != "0000-00-00")
		{
			$sProjectedCompletionDate = date("Y-m-d", (time( ) + (strtotime($sPlannedCompletionDate) - strtotime($sLastStagePlannedDate))));
			$iDelayDays               = @round((strtotime($sProjectedCompletionDate) - strtotime($sPlannedCompletionDate)) / 86400);
		}
	}
?>
			</td>

			<td></td>

			<td width="340" align="right">
			  <b>Total Estimated Construction<br />Contract Value Awarded (PKR):</b> <span><?= formatNumber((($fRevisedCost > 0) ? $fRevisedCost : $fCost), false) ?></span><br />
			  <b>Gross Amount Paid so far (PKR):</b> <span><?= formatNumber($iPaidAmount, false) ?></span><br />
			  <b>Total No of Classrooms:</b> <span><?= formatNumber($iTotalClassrooms, false) ?></span><br />
			  <b>Pre-Existing No of Classrooms:</b> <span><?= formatNumber($iPreClassrooms, false) ?></span><br />
			  <b>New Classrooms being Added:</b> <span><?= formatNumber($iNewClassrooms, false) ?></span><br />
			  <b>Total No of Toilets:</b> <span><?= formatNumber($iTotalToilets, false) ?></span><br />
			  <b>Pre-Existing No of Toilets:</b> <span><?= formatNumber($iPreToilets, false) ?></span><br />
			  <b>New Toilets being Added:</b> <span><?= formatNumber($iNewToilets, false) ?></span><br />
			  <b>Work Type:</b> <span><?= (($sWorkType == "N") ? "New Construction" : (($sWorkType == "R") ? "Rehabilitation Only" : "New Construction & Rehabilitation")) ?></span><br />
			  <b>No of Blocks:</b> <span><?= $iBlocks ?></span><br />
			  <b>Planned Completion Date:</b> <span><?= (($sPlannedCompletionDate != "" && $sPlannedCompletionDate != "0000-00-00") ? formatDate($sPlannedCompletionDate, $_SESSION['DateFormat']) : "N/A") ?></span><br />
			  <b><?= (($sCompleted != "Y") ? "Projected " : "") ?>Completion Date:</b> <span><?= (($sProjectedCompletionDate != "" && $sProjectedCompletionDate != "0000-00-00") ? formatDate($sProjectedCompletionDate, $_SESSION['DateFormat']) : "N/A") ?></span><br />
<?
	if ($iDelayDays > 0)
	{
?>
			  <b>Delay in No of Days:</b> <span><?= $iDelayDays ?></span><br />			  
<?
	}
?>
			</td>
		  </tr>
		</table>


		<div id="Progress" params="School=<?= $iSchool ?>"><?= formatNumber($fProgress, false) ?>%</div>
		<div id="Planned" params="School=<?= $iSchool ?>"><?= formatNumber($fPlanned, false) ?>%</div>

		<br />
		
		<h1 class="line"><span>Block wise Progress</span></h1>
		
		<ul id="SchoolBlocks">
<?
	$sSQL = "SELECT * FROM tbl_school_blocks WHERE school_id='$iSchool' ORDER BY block";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
	{
		$sBlockName            = $objDb->getField($i, "name");
		$sBlockStoreyType      = $objDb->getField($i, "storey_type");
		$sBlockDesignType      = $objDb->getField($i, "design_type");
		$sBlockWorkType        = $objDb->getField($i, "work_type");
		$fBlockCoveredArea     = $objDb->getField($i, "covered_area");
		$iBlockClassRooms      = $objDb->getField($i, "class_rooms");
		$iBlockStudentToilets  = $objDb->getField($i, "student_toilets");
		$iBlockStaffRooms      = $objDb->getField($i, "staff_rooms");
		$iBlockStaffToilets    = $objDb->getField($i, "staff_toilets");
		$iBlockScienceLabs     = $objDb->getField($i, "science_labs");
		$iBlockItLabs          = $objDb->getField($i, "it_labs");
		$iBlockExamHalls       = $objDb->getField($i, "exam_halls");
		$iBlockLibrary         = $objDb->getField($i, "library");
		$iBlockClerkOffices    = $objDb->getField($i, "clerk_offices");
		$iBlockPrincipalOffice = $objDb->getField($i, "principal_office");
		$iBlockParkingStand    = $objDb->getField($i, "parking_stand");
		$iBlockChowkidarHut    = $objDb->getField($i, "chowkidar_hut");
		$iBlockSoakagePit      = $objDb->getField($i, "soakage_pit");
		$iBlockWaterSupply     = $objDb->getField($i, "water_supply");			
		$iBlockStores          = $objDb->getField($i, "stores");	
		$iBlockLastStage       = $objDb->getField($i, "last_stage_id");	
		$fBlockPlanned         = $objDb->getField($i, "planned");
		$fBlockProgress        = $objDb->getField($i, "progress");		
		$sBlockCompleted       = $objDb->getField($i, "completed");
		
		
		$sDetails = "";
		
		if ($iBlockClassRooms > 0)
			$sDetails .= "Class Rooms:{$iBlockClassRooms}<br />";
		
		if ($iBlockStudentToilets > 0)
			$sDetails .= "Student Toilets:{$iBlockStudentToilets}<br />";
		
		if ($iBlockStaffRooms > 0)
			$sDetails .= "Staff Rooms:{$iBlockStaffRooms}<br />";
		
		if ($iBlockStaffToilets > 0)
			$sDetails .= "Staff Toilets:{$iBlockStaffToilets}<br />";
		
		if ($iBlockScienceLabs > 0)
			$sDetails .= "Science Labs:{$iBlockScienceLabs}<br />";
		
		if ($iBlockItLabs > 0)
			$sDetails .= "IT Labs:{$iBlockItLabs}<br />";
		
		if ($iBlockExamHalls > 0)
			$sDetails .= "Exam Halls:{$iBlockExamHalls}<br />";

		if ($iBlockLibrary > 0)
			$sDetails .= "Library:{$iBlockLibrary}<br />";

		if ($iBlockClerkOffices > 0)
			$sDetails .= "Clerk Offices:{$iBlockClerkOffices}<br />";
		
		if ($iBlockPrincipalOffice > 0)
			$sDetails .= "Principal Office:{$iBlockPrincipalOffice}<br />";
		
		if ($iBlockParkingStand > 0)
			$sDetails .= "Parking Stand:{$iBlockParkingStand}<br />";
		
		if ($iBlockChowkidarHut > 0)
			$sDetails .= "Chowkidar Hut:{$iBlockChowkidarHut}<br />";

		if ($iBlockSoakagePit > 0)
			$sDetails .= "Soakage Pit:{$iBlockSoakagePit}<br />";
		
		if ($iBlockWaterSupply > 0)
			$sDetails .= "Water Supply:{$iBlockWaterSupply}<br />";
		
		if ($iBlockStores > 0)
			$sDetails .= "Store Rooms:{$iBlockStores}<br />";
?>
		 <li>
		   <div>
		     <span class="progress"><?= formatNumber($fBlockProgress, false) ?>%</span>
			 <center><img src="images/blocks/<?= (($sBlockDesignType == "B") ? "B" : $sBlockStoreyType) ?><?= $sBlockWorkType ?>.jpg" alt="" title="" /></center>
			 <h2><?= $sBlockName ?></h2>
			 <b>Area:</b> <span><?= formatNumber($fBlockCoveredArea, false) ?> sft</span><br />
			 <br />
			 <b>Scope:</b><br />
			 <?= $sDetails ?><br />
		   </div>
		 </li>
<?
	}
?>
		</ul>
		
		<div class="br10"></div>

		
		<br />
		
		<h1 class="line"><span>GeoIntel Timeline</span></h1>

		<div id="GeoTimeline">
<?
	$sMainStages = getList("tbl_stages", "id", "name", "parent_id='0' AND status='A' AND `type`='$sSchoolType'", "position");
	$bStarted    = ((getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool'") > 0) ? true : false);
	$sStartDate  = getDbValue("MIN(csd.start_date)", "tbl_contract_schedules cs, tbl_contract_schedule_details csd", "cs.id=csd.schedule_id AND cs.school_id='$iSchool' AND cs.contract_id='$iContract'");
	
	
	foreach ($sMainStages as $iMainStage => $sMainStage)
	{
		$sStages = getList("tbl_stages", "id", "name", "parent_id='$iMainStage' AND status='A'", "position");
?>
		  <br />
		  <h2><?= $sMainStage ?></h2>

		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
		    <tr valign="bottom">
<?
		$iIndex = 0;

		foreach ($sStages as $iStage => $sStage)
		{
			if ($iIndex > 0)
			{
?>
		      <td></td>
<?
			}
?>
		      <td width="100" align="<?= (($iIndex == 0) ? 'left' : (($iIndex == (count($sStages) - 1)) ? 'right' : 'center')) ?>"><span class="title"><?= $sStage ?></span></td>
<?
			$iIndex ++;
		}
?>
		    </tr>
		  </table>

		  <div class="br5"></div>

		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
		    <tr>
<?
		$sClass = "";
		$iIndex = 0;

		foreach ($sStages as $iStage => $sStage)
		{
			$sSubStagesList = getList("tbl_stages", "id", "name", "parent_id='$iStage' AND status='A'", "position");
			
			$sStagesHtml    = '<div class="stageStatus">';
			$sStagesHtml   .= '<table border="0" cellspacing="0" cellpadding="2" width="500">';
			$sStagesHtml   .= '<tr bgcolor="#f6f6f6">';
			$sStagesHtml   .= '  <td width="60%"><b>Stage</b></td>';
			$sStagesHtml   .= '  <td width="21%"><b>Planned Date</b></td>';
			$sStagesHtml   .= '  <td width="19%"><b>Actual Date</b></td>';
			$sStagesHtml   .= '</tr>';


			foreach ($sSubStagesList as $iSubStage => $sSubStage)
			{
				$sFourthLevelStagesList = getList("tbl_stages", "id", "name", "parent_id='$iSubStage' AND status='A'", "position");
				$sPlannedDate           = getDbValue("csd.end_date", "tbl_contract_schedules cs, tbl_contract_schedule_details csd", "cs.id=csd.schedule_id AND cs.school_id='$iSchool' AND cs.contract_id='$iContract' AND csd.stage_id='$iSubStage'");
				$iInspection            = 0;
				$sActualDate            = "";
				$sCompleted             = "N";

				
				$sSQL = "SELECT id, `date`, stage_completed FROM tbl_inspections WHERE school_id='$iSchool' AND stage_id='$iSubStage' ORDER BY `date` DESC, id DESC LIMIT 1";
				$objDb->query($sSQL);

				if ($objDb->getCount( ) == 1)
				{
					$iInspection = $objDb->getField(0, "id");
					$sActualDate = $objDb->getField(0, "date");
					$sCompleted  = $objDb->getField(0, "stage_completed");
				}


				$sStagesHtml .= '<tr valign="top">';

				if ($iInspection > 0)
					$sStagesHtml .= ('  <td><a href="inspection-details.php?Id='.$iInspection.'" class="inspection">'.((count($sFourthLevelStagesList) > 0) ? '<b>' : '').$sSubStage.((count($sFourthLevelStagesList) > 0) ? '</b>' : '').'</a></td>');

				else
					$sStagesHtml .= ('  <td>'.((count($sFourthLevelStagesList) > 0) ? '<b>' : '').$sSubStage.((count($sFourthLevelStagesList) > 0) ? '</b>' : '').'</td>');


				$sColor = "#444444";

				if ($sPlannedDate != "" && $sPlannedDate != "0000-00-00" &&  time( ) >= strtotime($sPlannedDate))
					$sColor = (($sCompleted == "Y" && $sActualDate != "" && strtotime($sActualDate) <= strtotime($sPlannedDate)) ? '#36b24f' : '#eb212e');


				$sStagesHtml .= ('  <td style="color:'.$sColor.';">'.formatDate($sPlannedDate, $_SESSION['DateFormat']).'</td>');
				$sStagesHtml .= ('  <td>'.formatDate($sActualDate, $_SESSION['DateFormat']).'</td>');
				$sStagesHtml .= '</tr>';


				foreach ($sFourthLevelStagesList as $iSubStage => $sSubStage)
				{
					$sPlannedDate = getDbValue("csd.end_date", "tbl_contract_schedules cs, tbl_contract_schedule_details csd", "cs.id=csd.schedule_id AND cs.school_id='$iSchool' AND cs.contract_id='$iContract' AND csd.stage_id='$iSubStage'");
					$iInspection  = 0;
					$sActualDate  = "";
					$sCompleted   = "N";

					
					$sSQL = "SELECT id, `date`, stage_completed FROM tbl_inspections WHERE school_id='$iSchool' AND stage_id='$iSubStage' ORDER BY `date` DESC, id DESC LIMIT 1";
					$objDb->query($sSQL);

					if ($objDb->getCount( ) == 1)
					{
						$iInspection = $objDb->getField(0, "id");
						$sActualDate = $objDb->getField(0, "date");
						$sCompleted  = $objDb->getField(0, "stage_completed");
					}


					$sStagesHtml .= '<tr valign="top">';

					if ($iInspection > 0)
						$sStagesHtml .= ('  <td><span style="padding-left:20px; display:inline-block;"><a href="inspection-details.php?Id='.$iInspection.'" class="inspection">'.$sSubStage.'</a></span></td>');

					else
						$sStagesHtml .= ('  <td><span style="padding-left:20px; display:inline-block;">'.$sSubStage.'</span></td>');


					$sColor = "#444444";

					if ($sPlannedDate != "" && $sPlannedDate != "0000-00-00" &&  time( ) >= strtotime($sPlannedDate))
						$sColor = (($sCompleted == "Y" && $sActualDate != "" && strtotime($sActualDate) <= strtotime($sPlannedDate)) ? '#36b24f' : '#eb212e');


					$sStagesHtml .= ('  <td style="color:'.$sColor.';">'.formatDate($sPlannedDate, $_SESSION['DateFormat']).'</td>');
					$sStagesHtml .= ('  <td>'.formatDate($sActualDate, $_SESSION['DateFormat']).'</td>');
					$sStagesHtml .= '</tr>';
				}
			}

			$sStagesHtml .= '</table>';
			$sStagesHtml .= '</div>';


			if (count($sSubStagesList) == 0)
				$sStagesHtml = "";


			if ($bStarted == true)
			{
				if (@in_array($iMainStage, array(1,2,116,117,231,232)))
					$sClass = " green";

				else if (@in_array($iMainStage, array(3,118,233)))
				{
					$sClass = "";


					$sSQL = "SELECT csd.stage_id, csd.end_date
					         FROM tbl_contract_schedules cs, tbl_contract_schedule_details csd, tbl_stages s
					         WHERE cs.id=csd.schedule_id AND s.id=csd.stage_id AND s.weightage>'0' AND cs.school_id='$iSchool' AND cs.contract_id='$iContract' AND csd.stage_id IN ({$sAllSubStages[$iStage]})
					               AND csd.end_date <= CURDATE( ) AND csd.end_date != '0000-00-00'
					         ORDER BY csd.end_date DESC
					         LIMIT 1";
					$objDb->query($sSQL);

					if ($objDb->getCount( ) == 1)
					{
						$iMaxStage = $objDb->getField(0, "stage_id");
						$sMaxDate  = $objDb->getField(0, "end_date");


						$sClass = " red";

						if (getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool' AND stage_id='$iMaxStage' AND status='P' AND stage_completed='Y' AND `date` <= '$sMaxDate'") > 0)
							$sClass = " green";
					}
				}
			}

			else if ($sStartDate != "" && $sStartDate != "0000-00-00")
			{
				if (time( ) > strtotime($sStartDate) && $bStarted == false)
					$sClass = " red";
			}


			if ($iIndex > 0)
			{
?>
		      <td width="<?= (($iIndex == 1) ? 44 : 22) ?>"><div class="line<?= $sClass ?>"></div></td>
		      <td><div class="line<?= $sClass ?>"></div></td>
		      <td width="<?= (($iIndex == (count($sStages) - 1)) ? 44 : 22) ?>"><div class="line<?= $sClass ?>"></div></td>
<?
			}
?>
		      <td width="56">
		        <div class="circle tooltip<?= $sClass ?>"<?= (($sStagesHtml != "") ? ('title="'.@htmlentities($sStagesHtml, ENT_QUOTES).'"') : '') ?>>
		          <div class="radius"></div>
		        </div>
		      </td>
<?
			$iIndex ++;
		}
?>
		    </tr>
		  </table>

		  <div class="br5"></div>

		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
		    <tr>
<?
		$iIndex = 0;

		foreach ($sStages as $iStage => $sStage)
		{
			if ($iIndex > 0)
			{
?>
		      <td></td>
<?
			}


			$sEndDate = getDbValue("MAX(csd.end_date)", "tbl_contract_schedules cs, tbl_contract_schedule_details csd", "cs.id=csd.schedule_id AND cs.school_id='$iSchool' AND cs.contract_id='$iContract' AND csd.stage_id IN ({$sAllSubStages[$iStage]})");
?>
		      <td width="100" align="<?= (($iIndex == 0) ? 'left' : (($iIndex == (count($sStages) - 1)) ? 'right' : 'center')) ?>"><span class="date"><?= formatDate($sEndDate, $_SESSION['DateFormat']) ?></span></td>
<?
			$iIndex ++;
		}
?>
		    </tr>
		  </table>

		  <br />
<?
	}
?>
		</div>

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
		
<?
	if ($iLastCompletedStage > 0)
	{
?>
		<br />
		<b>Last Completed Stage:</b> <?= getDbValue("name", "tbl_stages", "id='$iLastCompletedStage'") ?><br />
<?
	}
?>
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
	$iPlannedStage = getDbValue("csd.stage_id", "tbl_contract_schedules cs, tbl_contract_schedule_details csd, tbl_stages s", "cs.id=csd.schedule_id AND s.id=csd.stage_id AND cs.school_id='$iSchool' AND csd.end_date<=CURDATE( ) AND s.weightage>'0' AND s.type='$sSchoolType' AND s.skip!='Y'", "csd.end_date DESC");
	
	if ($iPlannedStage > 0)
	{
?>
		<br />
		<b>Planned Stage Completion:</b> <?= getDbValue("name", "tbl_stages", "id='$iPlannedStage'") ?><br />
<?
	}
	
	
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>