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

	$sInspectionsSQL = " AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";

	if ($_SESSION["AdminSchools"] != "")
		$sInspectionsSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";




	$iActiveEngineer        = getDbValue("admin_id", "tbl_inspections", "DATEDIFF(NOW( ), `date`) <= '7' $sInspectionsSQL", "COUNT(1) DESC", "admin_id");
	$iEngineerAudits        = getDbValue("COUNT(1)", "tbl_inspections", "DATEDIFF(NOW( ), `date`) <= '7' AND admin_id='$iActiveEngineer' $sInspectionsSQL");

	$iFailureDistrict       = getDbValue("district_id", "tbl_inspections", "status='F' AND DATEDIFF(NOW( ), `date`) <= '30' $sInspectionsSQL", "COUNT(1) DESC", "district_id HAVING COUNT(1)>'0'");
	$iDistrictFailed        = getDbValue("COUNT(1)", "tbl_inspections", "status='F' AND DATEDIFF(NOW( ), `date`) <= '30' AND district_id='$iFailureDistrict' $sInspectionsSQL");
	$iDistrictPassed        = getDbValue("COUNT(1)", "tbl_inspections", "status='P' AND DATEDIFF(NOW( ), `date`) <= '30' AND district_id='$iFailureDistrict' $sInspectionsSQL");
	$iDistrictReInspection  = getDbValue("COUNT(1)", "tbl_inspections", "status='R' AND DATEDIFF(NOW( ), `date`) <= '30' AND district_id='$iFailureDistrict' $sInspectionsSQL");

	$iFailureInspector      = getDbValue("admin_id", "tbl_inspections", "status='F' AND DATEDIFF(NOW( ), `date`) <= '30' $sInspectionsSQL", "COUNT(1) DESC", "admin_id HAVING COUNT(1)>'0'");
	$iInspectorFailed       = getDbValue("COUNT(1)", "tbl_inspections", "status='F' AND DATEDIFF(NOW( ), `date`) <= '30' AND admin_id='$iFailureInspector' $sInspectionsSQL");
	$iInspectorPassed       = getDbValue("COUNT(1)", "tbl_inspections", "status='P' AND DATEDIFF(NOW( ), `date`) <= '30' AND admin_id='$iFailureInspector' $sInspectionsSQL");
	$iInspectorReInspection = getDbValue("COUNT(1)", "tbl_inspections", "status='R' AND DATEDIFF(NOW( ), `date`) <= '30' AND admin_id='$iFailureInspector' $sInspectionsSQL");



	$sSQL = "SELECT DISTINCT(CONCAT(s.name, ' (', s.code, ')'))
	         FROM tbl_schools s, tbl_inspections i
	         WHERE s.id=i.school_id AND TIMESTAMPDIFF(MINUTE, i.`date`, NOW( )) <= '1440'
	               AND FIND_IN_SET(s.province_id, '{$_SESSION['AdminProvinces']}') AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') ";

	if ($_SESSION["AdminSchools"] != "")
		$sSQL .= " FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";

	$sSQL .= "ORDER BY s.name, s.code";
	$objDb->query($sSQL);

	$iActiveInspections = $objDb->getCount( );
	$sActiveInspections = "";

	for ($i = 0; $i < $iActiveInspections; $i ++)
		$sActiveInspections .= (($i + 1).". ".$objDb->getField($i, 0)."<br />");


	$sDistrictStats  = "Passed: {$iDistrictPassed}<br />Failed: {$iDistrictFailed}<br />Re-Inspection: {$iDistrictReInspection}";
	$sInspectorStats = "Passed: {$iInspectorPassed}<br />Failed: {$iInspectorFailed}<br />Re-Inspection: {$iInspectorReInspection}";
?>
<section id="Dashboard">
  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr valign="top">
	  <td><h2>&nbsp;</h2></td>

	  <td width="560">
	    <h2>Key Stats</h2>
	    <div class="br10"></div>

	    <table border="0" cellspacing="0" cellpadding="5" width="100%">
	      <tr>
	        <td width="250">No of Active Inspectors in last 24 Hrs</td>
	        <td><a href="./" onclick="return false;" class="tooltip" title="<?= $sActiveInspectors ?>"><?= formatNumber($iActiveInspectors, false) ?></a> &nbsp; <b>(<a href="<?= getPageUrl(11) ?>">Inspector Stats</a>)</b></td>
	      </tr>

	      <tr>
	        <td>No of Inspections done in last 24 Hrs</td>
	        <td><a href="./" onclick="return false;" class="<?= (($sActiveInspections != "") ? 'tooltip' : '') ?>" title="<?= $sActiveInspections ?>"><?= formatNumber(getDbValue("COUNT(1)", "tbl_inspections", "TIMESTAMPDIFF(MINUTE, `date`, NOW( )) <= '1440' $sInspectionsSQL"), false) ?></a></td>
	      </tr>

	      <tr>
	        <td>No of Inspections done in last 7 Days</td>
	        <td><a href="./" onclick="return false;"><?= formatNumber(getDbValue("COUNT(1)", "tbl_inspections", "DATEDIFF(NOW( ), `date`) <= '7' $sInspectionsSQL"), false) ?></a></td>
	      </tr>

	      <tr>
	        <td>Most Active Engineer in the last Week</td>
	        <td><a href="./" onclick="return false;"><?= (($iActiveEngineer == 0) ? "-" : getDbValue("name", "tbl_admins", "id='$iActiveEngineer'")) ?> <small><?= (($iEngineerAudits > 0) ? "({$iEngineerAudits} Inspections)" : "") ?></small></a></td>
	      </tr>

<?
	$sAdminsSQL = "(";
	$iDistricts = @explode(",", $_SESSION['AdminDistricts']);

	foreach($iDistricts as $iDistrict)
	{
		if ($sAdminsSQL != "(")
			$sAdminsSQL .= " OR ";

		$sAdminsSQL .= " FIND_IN_SET('$iDistrict', districts) ";
	}

	$sAdminsSQL .= ")";


	if ($_SESSION["AdminSchools"] != "")
	{
		$sSchoolsSQL = "(schools=''";
		$iSchools    = @explode(",", $_SESSION['AdminSchools']);

		foreach($iSchools as $iSchool)
		{
			$sSchoolsSQL .= " OR FIND_IN_SET('$iSchool', schools) ";
		}

		$sSchoolsSQL .= ")";
		$sAdminsSQL  .= " AND $sSchoolsSQL";
	}
?>
	      <tr>
	        <td>Total No of Deployed Field Engineers</td>
	        <td><a href="./" onclick="return false;"><?= formatNumber(getDbValue("COUNT(1)", "tbl_admins", "status='A' AND (type_id='8' OR type_id='9') AND $sAdminsSQL"), false) ?></a></td>
	      </tr>
	    </table>

	  </td>

	  <td width="40"><h2>&nbsp;</h2></td>

	  <td width="600">
	    <h2>Pass/Fail Stats ( Last 30 Days )</h2>
	    <div class="br10"></div>

	    <table border="0" cellspacing="0" cellpadding="5" width="100%">
	      <tr valign="top">
	        <td width="90">
	          <div style="background:#4bad48; border:solid 1px #494949; width:90px; height:90px; margin-bottom:5px;"></div>
	          <center><b><?= formatNumber(getDbValue("COUNT(1)", "tbl_inspections", "status='P' AND DATEDIFF(NOW( ), `date`) <= '30' $sInspectionsSQL"), false) ?></b></center>
	        </td>

	        <td width="5"></td>

	        <td width="90">
	          <div style="background:#ff0000; border:solid 1px #494949; width:90px; height:90px; margin-bottom:5px; cursor:pointer;" id="FailedInspections"></div>
	          <center><b><?= formatNumber(getDbValue("COUNT(1)", "tbl_inspections", "status='F' AND DATEDIFF(NOW( ), `date`) <= '30' $sInspectionsSQL"), false) ?></b></center>
	        </td>

	        <td width="5"></td>

	        <td>

			  <table border="0" cellspacing="0" cellpadding="5" width="100%">
			    <tr>
				  <td width="190">District with most Failures</td>
				  <td><a href="./" onclick="return false;" class="<?= (($iFailureDistrict > 0) ? 'tooltip' : '') ?>" title="<?= $sDistrictStats ?>"><?= (($iFailureDistrict == 0) ? "-" : getDbValue("name", "tbl_districts", "id='$iFailureDistrict'")) ?></a></td>
			    </tr>

			    <tr>
				  <td>Most Inspections Failed under</td>
				  <td><a href="./" onclick="return false;" class="<?= (($iFailureInspector > 0) ? 'tooltip' : '') ?>" title="<?= $sInspectorStats ?>"><?= (($iFailureInspector == 0) ? "-" : getDbValue("name", "tbl_admins", "id='$iFailureInspector'")) ?></a></td>
			    </tr>
			  </table>

	        </td>
	      </tr>
	    </table>
	  </td>

	  <td><h2>&nbsp;</h2></td>
    </tr>
  </table>

  <br />

  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr valign="top">
	  <td><h2 style="height:35px;">&nbsp;</h2></td>

	  <td width="1200">
	    <form name="frmSearch" id="frmSearch" onsubmit="return false;">
	    <table border="0" cellspacing="0" cellpadding="0" width="100%">
	      <tr>
	        <td><h2>Inspectors Activity</h2></td>

	        <td width="200">
			  <select name="Province" id="Province">
				<option value=""></option>
<?
	$sFromDate      = date("Y-m-d", strtotime("-6 days"));
	$sToDate        = date("Y-m-d");
	$sProvincesList = getList("tbl_provinces", "id", "name");

	foreach ($sProvincesList as $iProvince => $sProvince)
	{
?>
            	<option value="<?= $iProvince ?>"><?= $sProvince ?></option>
<?
	}
?>
			  </select>
	        </td>

	        <td width="300">
			  <select name="District" id="District">
				<option value=""></option>
			  </select>
	        </td>

	        <td width="15"></td>

	        <td width="200">
			  <script type="text/javascript" src="scripts/moment.js"></script>
			  <script type="text/javascript" src="scripts/daterangepicker.js"></script>

			  <link type="text/css" rel="stylesheet" href="css/bootstrap.css" />
			  <link type="text/css" rel="stylesheet" href="css/daterangepicker.css" />

			  <input type="hidden" name="FromDate" id="FromDate" value="<?= $sFromDate ?>" />
			  <input type="hidden" name="ToDate" id="ToDate" value="<?= $sToDate ?>" />

			  <div id="DateRange" class="pull-right">
				<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
				<span><?= formatDate($sFromDate, "d-M-Y") ?> / <?= formatDate($sToDate, "d-M-Y") ?></span> <b class="caret"></b>
			  </div>
	        </td>

	        <td width="10"></td>

	        <td width="80">
			  <div id="SearchButton">
				<input type="submit" name="btnSearch" id="BtnSearch" value="" class="button" />
				<span class="fa fa-search"></span>
			  </div>
	        </td>
	      </tr>
	    </table>
	    </form>


	    <div id="ActivityChart">Loading Graph...</div>

		<script type="text/javascript">
		<!--
			var objChart = new FusionCharts("scripts/FusionCharts/charts/StackedColumn3D.swf", "InspectorsActivity", "100%", "500", "0", "1");

			objChart.setXMLData("<chart caption='' bgcolor='ffffff' canvasBgColor='ffffff' numDivLines='10' formatNumberScale='0' showValues='0' showSum='1' showLabels='1' decimals='0' numberSuffix='' chartBottomMargin='5' plotFillAlpha='95' labelDisplay='AUTO' exportEnabled='1' exportShowMenuItem='1' exportAtClient='0' exportHandler='scripts/FusionCharts/PHP/FCExporter.php' exportAction='download' exportFileName='inspectors-performance'>" +
								"<categories>" +
<?
	$sSQL = "SELECT admin_id,
	                SUM(IF(status='P','1','0')) AS _Passed,
	                SUM(IF(status='R','1','0')) AS _ReInspections,
	                SUM(IF(status='F','1','0')) AS _Failed,
	                COUNT(1) AS _Inspections,
	                (SELECT name FROM tbl_admins WHERE id=tbl_inspections.admin_id) AS _Inspector,
	                (SELECT type_id FROM tbl_admins WHERE id=tbl_inspections.admin_id) AS _Type
	         FROM tbl_inspections
	         WHERE DATEDIFF(NOW( ), `date`) <= '7' $sInspectionsSQL
	         GROUP BY admin_id
	         ORDER BY _Inspections DESC";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$sInspector = $objDb->getField($i, "_Inspector");
?>
								"<category label='<?= $sInspector ?>' />" +
<?
	}
?>
								"</categories>" +

								"<dataset seriesName='Pass' color='7fff7f'>" +
<?
	for ($i = 0; $i < $iCount; $i ++)
	{
		$iInspector     = $objDb->getField($i, "admin_id");
		$sInspector     = $objDb->getField($i, "_Inspector");
		$iType          = $objDb->getField($i, "_Type");
		$iPassed        = $objDb->getField($i, "_Passed");
		$iReInspections = $objDb->getField($i, "_ReInspections");
		$iFailed        = $objDb->getField($i, "_Failed");
		$iInspections   = $objDb->getField($i, "_Inspections");
?>
								"<set tooltext='<?= $sInspector ?>{br}<?= $sInspectorTypes[$iType] ?>{br}{br}Total: <?= $iInspections ?>{br}Passed: <?= $iPassed ?>{br}Re-Inspections: <?= $iReInspections ?>{br}Failed: <?= $iFailed ?>' value='<?= $iPassed ?>' link='javascript:showInspections(\"<?= $iInspector ?>\", \"P\")' />" +
<?
	}
?>
								"</dataset>" +

								"<dataset seriesName='Re-Inspection' color='fcbf04'>" +
<?
	for ($i = 0; $i < $iCount; $i ++)
	{
		$iInspector     = $objDb->getField($i, "admin_id");
		$sInspector     = $objDb->getField($i, "_Inspector");
		$iType          = $objDb->getField($i, "_Type");
		$iPassed        = $objDb->getField($i, "_Passed");
		$iReInspections = $objDb->getField($i, "_ReInspections");
		$iFailed        = $objDb->getField($i, "_Failed");
		$iInspections   = $objDb->getField($i, "_Inspections");
?>
								"<set tooltext='<?= $sInspector ?>{br}<?= $sInspectorTypes[$iType] ?>{br}{br}Total: <?= $iInspections ?>{br}Passed: <?= $iPassed ?>{br}Re-Inspections: <?= $iReInspections ?>{br}Failed: <?= $iFailed ?>' value='<?= $iReInspections ?>' link='javascript:showInspections(\"<?= $iInspector ?>\", \"R\")' />" +
<?
	}
?>
								"</dataset>" +

								"<dataset seriesName='Failed' color='ff0000'>" +
<?
	for ($i = 0; $i < $iCount; $i ++)
	{
		$iInspector     = $objDb->getField($i, "admin_id");
		$sInspector     = $objDb->getField($i, "_Inspector");
		$iType          = $objDb->getField($i, "_Type");
		$iPassed        = $objDb->getField($i, "_Passed");
		$iReInspections = $objDb->getField($i, "_ReInspections");
		$iFailed        = $objDb->getField($i, "_Failed");
		$iInspections   = $objDb->getField($i, "_Inspections");
?>
								"<set tooltext='<?= $sInspector ?>{br}<?= $sInspectorTypes[$iType] ?>{br}{br}Total: <?= $iInspections ?>{br}Passed: <?= $iPassed ?>{br}Re-Inspections: <?= $iReInspections ?>{br}Failed: <?= $iFailed ?>' value='<?= $iFailed ?>' link='javascript:showInspections(\"<?= $iInspector ?>\", \"F\")' />" +
<?
	}
?>
								"</dataset>" +

								"</chart>");


			objChart.render("ActivityChart");


			function showInspections(iInspector, sStatus)
			{
				$.colorbox({ href:("inspections.php?Inspector=" + iInspector + "&Status=" + sStatus), width:"1100px", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });
			}



			$(function()
			{
				$("#Province").select2(
				{
					allowClear               :  true,
					placeholder              :  "Select a Province",
					minimumResultsForSearch  :  Infinity
				});


				$("#District").select2(
				{
					allowClear   :  true,
					placeholder  :  "Select a District"
				});


				$("#Province").change(function( )
				{
					$("#District").select2("close").val(null).trigger("change");


					$.post("ajax/get-districts.php",
						{ Province:$(this).val( ) },

						function (sResponse)
						{
							$("#District").html(sResponse);
						},

						"text");
				});


				$("#Dashboard #frmSearch #BtnSearch, #Dashboard #frmSearch #SearchButton .fa").click(function( )
				{
					$.post("ajax/get-inspectors-activity-xml.php",
						$("#Dashboard #frmSearch").serialize( ),

						function (sResponse)
						{
							objChart.setXMLData(sResponse);
							objChart.render("ActivityChart");
						},

						"text");
				});



				function setDates(start, end)
				{
					$("#FromDate").val(start.format('YYYY-MM-DD'));
					$("#ToDate").val(end.format('YYYY-MM-DD'));

					$('#DateRange span').html(start.format('DD-MMM-YYYY') + ' / ' + end.format('DD-MMM-YYYY'));
				}


				$('#DateRange').daterangepicker(
				{
					locale           :  { format: 'DD-MMM-YYYY' },
					startDate        :  '<?= formatDate($sFromDate, "d-M-Y") ?>',
					endDate          :  '<?= formatDate($sToDate, "d-M-Y") ?>',
					maxDate          :  '<?= formatDate($sToDate, "d-M-Y") ?>',
					showWeekNumbers  :  true,
					opens            :  'left',

					ranges    :  {  'Today'        : [ moment(), moment()],
					                'Yesterday'    : [ moment().subtract(1, 'days'), moment().subtract(1, 'days') ],
					                'Last 7 Days'  : [ moment().subtract(6, 'days'), moment() ],
					                'Last 30 Days' : [ moment().subtract(29, 'days'), moment() ],
					                'This Month'   : [ moment().startOf('month'), moment().endOf('month') ],
					                'Last Month'   : [ moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month') ]
					            }
				}, setDates);
			});
		-->
		</script>
	  </td>

	  <td><h2 style="height:35px;">&nbsp;</h2></td>
    </tr>
  </table>

  <br />

  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr valign="top">
	  <td><h2>&nbsp;</h2></td>

	  <td width="1200">
	    <h2>Images from recent Inspections</h2>

<?
	$sSubConditions = "";

	if ($_SESSION["AdminSchools"] != "")
		$sSubConditions .= " AND FIND_IN_SET(i.school_id, '{$_SESSION['AdminSchools']}') ";


	$sSQL = "SELECT '0' AS _PictureId, id AS _Inspection, picture AS _Picture, status AS _Status, school_id AS _School, district_id AS _District, `date` AS _Date
			 FROM tbl_inspections
			 WHERE picture!='' $sInspectionsSQL

	         UNION

	         SELECT d.id AS _PictureId, d.inspection_id AS _Inspection, d.file AS _Picture, i.status AS _Status, i.school_id AS _School, i.district_id AS _District, i.date AS _Date
	         FROM tbl_inspection_documents d, tbl_inspections i
	         WHERE d.inspection_id=i.id AND d.file LIKE '%.jpg' AND FIND_IN_SET(i.district_id, '{$_SESSION['AdminDistricts']}') $sSubConditions

	         ORDER BY _Date DESC, _Inspection DESC, _PictureId ASC
	         LIMIT 0, 50";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	$iIndex = 0;

	if ($iCount > 0)
	{
?>
	    <div id="Images">
	      <div id="Tabs" rel="<?= $_SERVER['REQUEST_URI'] ?>">
	        <ul class="hidden">
	          <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1">Tab-1</a></li>
	        </ul>


	        <div id="tabs-1" class="slide">
	          <ul>
<?
		$sDistrictsList = getList("tbl_districts", "id", "name");
		$sImagesList    = array(".jpg", ".jpeg", ".png", ".gif");

		$sStatuses      = array( );
		$iSchools       = array( );
		$sCodes         = array( );
		$iDistricts     = array( );


		for ($i = 0; ($i < $iCount && $iIndex < 18); $i ++)
		{
			$iPictureId  = $objDb->getField($i, "_PictureId");
			$iInspection = $objDb->getField($i, "_Inspection");
			$sDate       = $objDb->getField($i, "_Date");
			$sPicture    = $objDb->getField($i, "_Picture");
			$sStatus     = $objDb->getField($i, "_Status");
			$iSchool     = $objDb->getField($i, "_School");
			$iDistrict   = $objDb->getField($i, "_District");


			$iPosition  = @strrpos($sPicture, '.');
			$sExtension = @substr($sPicture, $iPosition);


			if (!@in_array($sExtension, $sImagesList))
				continue;

			if (!@file_exists(INSPECTIONS_IMG_DIR.$sPicture) && !@file_exists(INSPECTIONS_DOC_DIR.$sPicture))
				continue;


			if (!@in_array($iInspection, $sCodes))
			{
				$sCode = getDbValue("code", "tbl_schools", "id='$iSchool'");

				$sCodes[$iSchool] = $sCode;
			}

			else
				$sCode = $sCodes[$iSchool];
?>
	            <li>
	              <a href="inspection-details.php?Id=<?= $iInspection ?>" class="inspection status<?= $sStatus ?>">
	                <img src="<?= ((@file_exists(INSPECTIONS_IMG_DIR.$sPicture)) ? (SITE_URL.INSPECTIONS_IMG_DIR.$sPicture) : (SITE_URL.INSPECTIONS_DOC_DIR.$sPicture)) ?>" width="116" height="116" alt="" title="" rel="<?= $sDate ?>|<?= $iInspection ?>|<?= $iPictureId ?>" /><br />
	                <?= $sCode ?>
	              </a>

	              <br />
	              <?= $sDistrictsList[$iDistrict] ?><br />
	            </li>
<?
			$iIndex ++;
		}
?>
	          </ul>

	          <div class="br10"></div>
	        </div>
	      </div>

	      <div id="Next"<?= (($iIndex < 18) ? ' class="disabled"' : '') ?>>&gt;</div>
	      <div id="Back" class="disabled">&lt;</div>
	    </div>
<?
	}
?>
	  </td>

	  <td><h2>&nbsp;</h2></td>
    </tr>
  </table>


  <script type="text/javascript">
  <!--
	$(document).ready(function( )
	{
		$("#FailedInspections").click(function( )
		{
			$.colorbox({ href:"failed-inspections.php", width:"1100px", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });
		});



		var sTabTemplate = ("<li><a href='" + $("#Tabs").attr("rel") + "#{href}'>#{label}</a></li>");
		var iTabsCounter = 2;
		var bLastSlide   = <?= (($iIndex < 18) ? 'true' : 'false') ?>;
		var bProcessing  = false;

 		var objTabs = $("#Images #Tabs").tabs(
 		{
			fx        :  [ {width:'toggle', duration:'normal'}, {width:'toggle', duration:'fast'} ],

			activate  :  function(event, ui)
			{
				var sHtml = ui.newPanel.html( );

				if (sHtml.indexOf("images/waiting.gif") >= 0)
				{
					bProcessing = true;


					$.post("ajax/get-inspection-pictures.php",
						{ Id : $("#" + ui.oldPanel.attr("id") + " img:last").attr("rel") },

						function (sResponse)
						{
							ui.newPanel.addClass("slide");
							ui.newPanel.html(sResponse);


							$("a.inspection").colorbox({ width:"900px", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });


							var iImages = (sResponse.match(/img/g) || []).length;

							if (iImages < 18)
							{
								$("#Images #Next").addClass("disabled");

								bLastSlide = true;
							}


							bProcessing = false;
						},

						"text");
				}
			}
 		});


 		$("#Images #Next").click(function( )
 		{
 			if (bProcessing == true)
 				return;


 			var iIndex = objTabs.tabs('option', 'active');


 			if ((iIndex + 1) < (iTabsCounter - 1))
 			{
				objTabs.tabs("option", "active", (iIndex + 1));

				$("#Images #Back").removeClass("disabled");
			}

 			else if (bLastSlide == false)
 			{
				var sTitle = ("Tab-" + iTabsCounter);
				var sTabId = ("tabs-" + iTabsCounter);
				var sTabLi = $(sTabTemplate.replace(/#\{href\}/g, ("#" + sTabId)).replace(/#\{label\}/g, sTitle));

				objTabs.find(".ui-tabs-nav").append(sTabLi);
				objTabs.append("<div id='" + sTabId + "'><center><img src='images/waiting.gif' vspace='100' alt='' title='' /></center></div>");
				objTabs.tabs("refresh");

				objTabs.tabs("option", "active", (iTabsCounter - 1));

				iTabsCounter ++;


				$("#Images #Back").removeClass("disabled");
			}
 		});


 		$("#Images #Back").click(function( )
 		{
 			if (bProcessing == true)
 				return;


			var iIndex = objTabs.tabs('option', 'active');

			if (iIndex > 0)
				objTabs.tabs("option", "active", (iIndex - 1));


			if (iIndex == 1)
				$("#Images #Back").addClass("disabled");
 		});
	});
  -->
  </script>
</section>
