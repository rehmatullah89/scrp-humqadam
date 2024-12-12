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

	if ($_SESSION["AdminId"] == 0 || $_SESSION["AdminTypeId"] == 12)
	{
		if ($_SESSION["AdminId"] == 0)
			$_SESSION['RequestUrl'] = $_SERVER['REQUEST_URI'];
		
		redirect(SITE_URL);
	}

	$sKeywords = IO::strValue("Keywords");
	$iPackage  = IO::intValue("Package");
	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");
	$sStatus   = IO::strValue("Status");

	
	$sConditions = "WHERE status='A' AND dropped!='Y' AND qualified='Y' AND adopted='Y'
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
		$sSubConditions = " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";
	
	$sConditions .= " AND id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions) ";
	

	$sSQL = "SELECT SUM(ex_class_rooms) AS _PreClassrooms, 
					SUM((ex_student_toilets + ex_staff_toilets)) AS _PreToilets, 
					SUM(cost) AS _Cost, 
					SUM(COALESCE(revised_cost, 0)) AS _RevisedCost, 
	                SUM(IF(completed='Y', '0', '1')) AS _ActiveSchools,
					SUM(IF(completed='Y', '1', '0')) AS _CompletedSchools,
					SUM(covered_area) AS _CoveredArea, 
	                SUM((progress / 100) * covered_area) AS _Weightage,
					SUM((planned / 100) * covered_area) AS _Planned
	         FROM tbl_schools
	         $sConditions";
	$objDb->query($sSQL);

	$iActiveSchools    = $objDb->getField(0, "_ActiveSchools");
	$iCompletedSchools = $objDb->getField(0, "_CompletedSchools");
	$iPreClassrooms    = $objDb->getField(0, "_PreClassrooms");
	$iPreToilets       = $objDb->getField(0, "_PreToilets");
	$fCost             = $objDb->getField(0, "_Cost");
	$fRevisedCost      = $objDb->getField(0, "_RevisedCost");
	$fCoveredArea      = $objDb->getField(0, "_CoveredArea");
	$fWeightage        = $objDb->getField(0, "_Weightage");
	$fPlanned          = $objDb->getField(0, "_Planned");
	
	
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
  		  <script type="text/javascript" src="scripts/project-tracker.js"></script>

          <div id="Statistics">
			<h1 class="line"><span>Statistics</span></h1>

			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			  <tr valign="top">
				<td width="340">
				  <b>Package:</b> <span style="color:#89cf34;">N/A</span><br />
				  <b>Contractor:</b> <span>N/A</span><br />
				  <b>Contract:</b> <span>N/A</span><br />
				  <b>Covered Area:</b> <span><?= formatNumber($fCoveredArea, false) ?> sft</span><br />
				  <b>No of Active Schools:</b> <span><?= formatNumber($iActiveSchools, false) ?></span><br />
				  <b>No of Completed Schools:</b> <span><?= formatNumber($iCompletedSchools, false) ?></span><br />
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
				  <b>Work Type:</b> <span>-</span><br />
				</td>
			  </tr>
			</table>


			<div id="Progress" params=""><?= formatNumber($fProgress, false) ?>%</div>
			<div id="Planned" params=""><?= formatNumber($fPlanned, false) ?>%</div>
			<br />
			<br />
          </div>


		  <h1 class="line"><span>GeoIntel Timeline</span></h1>

		  <div id="GeoTimelineOverall">
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
		  </div>

	      <br />

          <h1 class="line"><span>Project Tracker</span></h1>
          <br />
          <br />

          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr valign="top">
              <td width="30%">
                <div id="KpisChartArea1">Building KPI Chart ...</div>
              </td>

              <td width="5%"></td>

              <td width="30%">
                <div id="KpisChartArea2">Building KPI Chart ...</div>
              </td>

              <td width="5%"></td>

              <td width="30%">
                <div id="KpisChartArea3">Building KPI Chart ...</div>
              </td>
            </tr>
          </table>

		  <script type="text/javascript">
		  <!--
		 	FusionCharts.setCurrentRenderer('javascript');


			$(document).ready(function( )
			{
				var objChart1 = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", "KpiChart1", "100%", "300", "0", "1");

				$.postq("Graphs", "ajax/get-kpi-chart-xml.php",
				{
					Type  :  "ProjectActivity"
				},

				function (sResponse)
				{
					objChart1.setXMLData(sResponse);
					objChart1.render("KpisChartArea1");
				},

				"text");



				var objChart2 = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", "KpiChart2", "100%", "300", "0", "1");

				$.postq("Graphs", "ajax/get-kpi-chart-xml.php",
				{
					Type  :  "Contracts"
				},

				function (sResponse)
				{
					objChart2.setXMLData(sResponse);
					objChart2.render("KpisChartArea2");
				},

				"text");



				var objChart3 = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", "KpiChart3", "100%", "300", "0", "1");

				$.postq("Graphs", "ajax/get-kpi-chart-xml.php",
				{
					Type  :  "Dropped"
				},

				function (sResponse)
				{
					objChart3.setXMLData(sResponse);
					objChart3.render("KpisChartArea3");
				},

				"text");
			});
		  -->
		  </script>
