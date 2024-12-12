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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );


	$sUser = IO::strValue('User');

	if ($sUser == "")
		die("Invalid Request");


	$sSQL = "SELECT id, status FROM tbl_admins WHERE MD5(id)='$sUser'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) == 0)
		die("Invalid User");

	else if ($objDb->getField(0, "status") != "A")
		die("User Account is Disabled");


	$iUser = $objDb->getField(0, "id");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
    "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">

<html lang="en">

<head>
<?
	@include("includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/FusionCharts/FusionCharts.js"></script>
</head>

<body style="width:100%; min-width:100%;">

<section id="KpisChart" style="padding:15px;">
  <div>
    <div id="KpisChartArea">Building KPI Chart ...</div>
  </div>
</section>

</body>

<script type="text/javascript">
<!--
	FusionCharts.setCurrentRenderer('javascript');


	$(document).ready(function( )
	{
		var objChart = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", "KpiChart", "100%", "300", "0", "1");


		$.post("<?= SITE_URL ?>api/ajax/get-kpi-chart-xml.php",
		{
			User  :  "<?= $sUser ?>",
			Type  :  "ProjectActivity"
		},

		function (sResponse)
		{
			objChart.setXMLData(sResponse);
			objChart.render("KpisChartArea");
		},

		"text");



		$(document).ajaxStart(function( )
		{
			if (typeof Android != 'undefined')
				Android.showProgressBox( );
		});

		$(document).ajaxStop(function( )
		{
			if (typeof Android != 'undefined')
				Android.hideProgressBox( );
		});
	});

-->
</script>

</html>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>