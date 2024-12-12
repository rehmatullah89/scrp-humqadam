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
	
	
	$iContractAwardedPu     = getDbValue("SUM(IF(revised_cost>'0', revised_cost, cost))", "tbl_schools", "status='A' AND dropped!='Y' AND adopted='Y' AND qualified='Y' AND province_id='1'");
	$iGrossAmountPaidPu     = getDbValue("SUM(gross_amount)", "tbl_invoices", "status='P' AND school_id IN (SELECT id FROM tbl_schools WHERE status='A' AND dropped!='Y' AND adopted='Y' AND qualified='Y' AND province_id='1')");
	$iRetentionMoneyPu      = getDbValue("SUM(retention_money)", "tbl_invoices", "status='P' AND school_id IN (SELECT id FROM tbl_schools WHERE status='A' AND dropped!='Y' AND adopted='Y' AND qualified='Y' AND province_id='1')");
	$iMobAdvancePu          = getDbValue("SUM(gross_amount)", "tbl_invoices", "title LIKE '%Advance%' AND status='P' AND school_id IN (SELECT id FROM tbl_schools WHERE status='A' AND dropped!='Y' AND adopted='Y' AND qualified='Y' AND province_id='1')");
	$iOutstandingAdvancePu  = getDbValue("SUM(mob_advance)", "tbl_invoices", "status='P' AND school_id IN (SELECT id FROM tbl_schools WHERE status='A' AND dropped!='Y' AND adopted='Y' AND qualified='Y' AND province_id='1')");
	
	$iContractAwardedKpk    = getDbValue("SUM(IF(revised_cost>'0', revised_cost, cost))", "tbl_schools", "status='A' AND dropped!='Y' AND adopted='Y' AND qualified='Y' AND province_id='2'");
	$iGrossAmountPaidKpk    = getDbValue("SUM(gross_amount)", "tbl_invoices", "status='P' AND school_id IN (SELECT id FROM tbl_schools WHERE status='A' AND dropped!='Y' AND adopted='Y' AND qualified='Y' AND province_id='2')");
	$iRetentionMoneyKpk     = getDbValue("SUM(retention_money)", "tbl_invoices", "status='P' AND school_id IN (SELECT id FROM tbl_schools WHERE status='A' AND dropped!='Y' AND adopted='Y' AND qualified='Y' AND province_id='2')");
	$iMobAdvanceKpk         = getDbValue("SUM(gross_amount)", "tbl_invoices", "title LIKE '%Advance%' AND status='P' AND school_id IN (SELECT id FROM tbl_schools WHERE status='A' AND dropped!='Y' AND adopted='Y' AND qualified='Y' AND province_id='2')");
	$iOutstandingAdvanceKpk = getDbValue("SUM(mob_advance)", "tbl_invoices", "status='P' AND school_id IN (SELECT id FROM tbl_schools WHERE status='A' AND dropped!='Y' AND adopted='Y' AND qualified='Y' AND province_id='2')");
	
	
	$iOutstandingAdvancePu  = ($iMobAdvancePu - $iOutstandingAdvancePu);
	$iOutstandingAdvanceKpk = ($iMobAdvanceKpk - $iOutstandingAdvanceKpk);
?>
	      <br />
		  <h1 class="line"><span>Summary</span></h1>
		  
		  <div class="grid">
			<table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
			  <tr class="header" valign="top">
				<td width="15%">Province</td>
				<td width="17%">Contract Awarded</td>
				<td width="17%">Gross Amount Paid</td>
				<td width="17%">Retention Money</td>
				<td width="17%">Mob Advances</td>
				<td width="17%">Outstanding Adv</td>
			  </tr>
			  
			  <tr class="even">
			    <td>Punjab</td>
			    <td><?= formatNumber($iContractAwardedPu, false) ?></td>
			    <td><?= formatNumber($iGrossAmountPaidPu, false) ?></td>
			    <td><?= formatNumber($iRetentionMoneyPu, false) ?></td>
			    <td><?= formatNumber($iMobAdvancePu, false) ?></td>
			    <td><?= formatNumber($iOutstandingAdvancePu, false) ?></td>
			  </tr>
			  
			  <tr class="odd">
			    <td>KPK</td>
			    <td><?= formatNumber($iContractAwardedKpk, false) ?></td>
			    <td><?= formatNumber($iGrossAmountPaidKpk, false) ?></td>
			    <td><?= formatNumber($iRetentionMoneyKpk, false) ?></td>
			    <td><?= formatNumber($iMobAdvanceKpk, false) ?></td>
			    <td><?= formatNumber($iOutstandingAdvanceKpk, false) ?></td>
			  </tr>
			  
			  <tr class="footer">
			    <td>Total</td>
			    <td><?= formatNumber(($iContractAwardedPu + $iContractAwardedKpk), false) ?></td>
			    <td><?= formatNumber(($iGrossAmountPaidPu + $iGrossAmountPaidKpk), false) ?></td>
			    <td><?= formatNumber(($iRetentionMoneyPu + $iRetentionMoneyKpk), false) ?></td>
			    <td><?= formatNumber(($iMobAdvancePu + $iMobAdvanceKpk), false) ?></td>
			    <td><?= formatNumber(($iOutstandingAdvancePu + $iOutstandingAdvanceKpk), false) ?></td>
			  </tr>
		    </table>
		  </div>
		  
		  
	      <br />
		  <br />
		  <br />
		  <h1 class="line"><span>Upcoming Invoices</span></h1>
		  
		  <div class="grid">
			<table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
			  <tr class="header" valign="top">
				<td width="5%">#</td>
				<td width="22%">School</td>
				<td width="10%">EMIS Code</td>
				<td width="15%">District</td>
				<td width="30%">Last Completed Milestone</td>
				<td width="10%">Date</td>
				<td width="8%">Actions</td>
			  </tr>
<?
	$sDistrictsList = getList("tbl_districts", "id", "name");
	
	
	$sSQL = "SELECT id, district_id, name, `code`, last_milestone_id, last_inspection FROM tbl_schools WHERE status='A' AND last_milestone_id>'0' ORDER BY last_inspection DESC LIMIT 15";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId        = $objDb->getField($i, "id");
		$iDistrict  = $objDb->getField($i, "district_id");
		$sName      = $objDb->getField($i, "name");
		$sCode      = $objDb->getField($i, "code");
		$iMilestone = $objDb->getField($i, "last_milestone_id");
		$sDateTime  = $objDb->getField($i, "last_inspection");
?>

				<tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
				  <td><?= str_pad(($i + 1), 4, '0', STR_PAD_LEFT) ?></td>
				  <td><?= $sName  ?></td>
				  <td><?= $sCode ?></td>				  
				  <td><?= $sDistrictsList[$iDistrict] ?></td>
				  <td><?= getDbValue("name", "tbl_stages", "id='$iMilestone'") ?></td>
				  <td><?= formatDate($sDateTime, $sDateFormat) ?></td>				  
				  <td></td>
				</tr>
<?
	}
?>
			</table>
		  </div>
  
		  
          <br />
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
				var objChart1 = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", "KpiChart1", "100%", "260", "0", "1");

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



				var objChart2 = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", "KpiChart2", "100%", "260", "0", "1");

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



				var objChart3 = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", "KpiChart3", "100%", "260", "0", "1");

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
