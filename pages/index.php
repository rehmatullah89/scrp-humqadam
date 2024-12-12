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
?>
		  <?= $sPageContents ?>
<!--
		<h1 class="line"><span>Humqadam - Education Infrastructure Revolution</span></h1>

		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
				<tr valign="top">
					<td width="30%">
					<div class="topSchool"><a class="pic" href="./"><img alt="" src="images/schools/default.jpg" title="" /></a>

					<h2>School Name</h2>

					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad</p>
					</div>
					</td>
					<td width="5%"></td>
					<td width="30%">
					<div class="topSchool"><a class="pic" href="./"><img alt="" src="images/schools/default.jpg" title="" /></a>
					<h2>School Name</h2>

					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad</p>
					</div>
					</td>
					<td width="5%"></td>
					<td width="30%">
					<div class="topSchool"><a class="pic" href="./"><img alt="" src="images/schools/default.jpg" title="" /></a>
					<h2>School Name</h2>

					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad</p>
					</div>
					</td>
				</tr>
			</tbody>
		</table>
-->

<?
	if ($_SESSION['AdminId'] > 0)
	{
?>
		  <hr />
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
<?
	}
?>