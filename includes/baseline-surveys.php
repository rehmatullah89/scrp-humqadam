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

	$sSurveysSQL = " AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";

	if ($_SESSION["AdminSchools"] != "")
		$sSurveysSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";


	$iActiveEnumerator  = getDbValue("created_by", "tbl_surveys", "status='C' AND DATEDIFF(NOW( ), `date`) <= '7' $sSurveysSQL", "COUNT(1) DESC", "created_by");
	$iEnumeratorSurveys = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND DATEDIFF(NOW( ), `date`) <= '7' AND created_by='$iActiveEnumerator' $sSurveysSQL");


	$sSQL = "SELECT DISTINCT(CONCAT(s.name, ' (', s.code, ')'))
	         FROM tbl_schools s, tbl_surveys bs
	         WHERE s.id=bs.school_id AND bs.status='C' AND TIMESTAMPDIFF(MINUTE, bs.`date`, NOW( )) <= '1440'
	               AND FIND_IN_SET(s.province_id, '{$_SESSION['AdminProvinces']}') AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') ";

	if ($_SESSION["AdminSchools"] != "")
		$sSQL .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";

	$sSQL .= "ORDER BY s.name, s.code";
	$objDb->query($sSQL);

	$iSurveySchools = $objDb->getCount( );
	$sSurveySchools = "";

	for ($i = 0; $i < $iSurveySchools; $i ++)
		$sSurveySchools .= (($i + 1).". ".$objDb->getField($i, 0)."<br />");
	
	
//	$sFromDate                     = date("Y-m-d", ((date("N") == 1) ? strtotime("Today") : strtotime("Last Monday")));
//	$sToDate                       = date("Y-m-d", ((date("N") >= 6) ? strtotime("This Saturday") : strtotime("Next Saturday")));
	$sFromDate                     = getDbValue("MIN(`date`)", "tbl_surveys", "id>'0' $sSurveysSQL");
	$sToDate                       = getDbValue("MAX(`date`)", "tbl_surveys", "id>'0' $sSurveysSQL");


	$iTotalSurveys                 = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL");
	$iPunjabSurveys                = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='1') $sSurveysSQL");
	$iKpkSurveys                   = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='2') $sSurveysSQL");
	
	$iTotalDisqualified            = getDbValue("COUNT(1)", "tbl_surveys", "qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL");
	$iPunjabDisqualified           = getDbValue("COUNT(1)", "tbl_surveys", "qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='1') $sSurveysSQL");
	$iKpkDisqualified              = getDbValue("COUNT(1)", "tbl_surveys", "qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='2') $sSurveysSQL");
	
	$iTotalSurveysPlanned          = getDbValue("COUNT(1)", "tbl_survey_schedules", "(`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL");
	$iPunjabSurveysPlanned         = getDbValue("COUNT(1)", "tbl_survey_schedules", "(`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='1') $sSurveysSQL");
	$iKpkSurveysPlanned            = getDbValue("COUNT(1)", "tbl_survey_schedules", "(`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='2') $sSurveysSQL");
	
	$iTotalSurveysConducted        = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL");
	$iPunjabSurveysConducted       = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='1') $sSurveysSQL");
	$iKpkSurveysConducted          = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='2') $sSurveysSQL");
	
	$iTotalPendingSurveys          = getDbValue("COUNT(1)", "tbl_survey_schedules", "status='P' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL");
	$iPunjabPendingSurveys         = getDbValue("COUNT(1)", "tbl_survey_schedules", "status='P' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='1') $sSurveysSQL");
	$iKpkPendingSurveys            = getDbValue("COUNT(1)", "tbl_survey_schedules", "status='P' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='2') $sSurveysSQL");
	
	$iTotalSurveysWithoutDrawings  = getDbValue("COUNT(1)", "tbl_surveys", "completed='N' AND qualified='Y' AND (app='N' OR status='C') AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL");
	$iPunjabSurveysWithoutDrawings = getDbValue("COUNT(1)", "tbl_surveys", "completed='N' AND qualified='Y' AND (app='N' OR status='C') AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='1') $sSurveysSQL");
	$iKpkSurveysWithoutDrawings    = getDbValue("COUNT(1)", "tbl_surveys", "completed='N' AND qualified='Y' AND (app='N' OR status='C') AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='2') $sSurveysSQL");
	
	$iIncompleteSurveys            = getDbValue("COUNT(1)", "tbl_surveys", "status='I' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL");
	$iTotalClassRooms              = getDbValue("SUM(IF((CEIL(avg_attendance / '40') - education_rooms) < '0', '0', (CEIL(avg_attendance / 40) - education_rooms)))", "tbl_surveys", "status='C' AND qualified='Y' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL");

	
	$sDistrictClassRoomsList = getList("tbl_surveys", 
	                                   "(SELECT d.name FROM tbl_districts d, tbl_schools s WHERE s.district_id=.d.id AND s.id=tbl_surveys.school_id) AS _District", 
									   "SUM(IF((CEIL(avg_attendance / '40') - education_rooms) < '0', '0', (CEIL(avg_attendance / 40) - education_rooms)))",
									   "status='C' AND qualified='Y' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL",
									   "_District",
									   "district_id");

	$sDistrictClassRooms = "";
	
	foreach ($sDistrictClassRoomsList as $sDistrict => $iClassRooms)
	{
		if ($iClassRooms == 0)
			continue;
		
		$sDistrictClassRooms .= "{$sDistrict} ($iClassRooms)<br />";
	}
?>
<section id="Dashboard">
  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr valign="top">
	  <td><h2 style="height:35px;">&nbsp;</h2></td>

	  <td width="1200">
	    <form name="frmSearch" id="frmSearch" onsubmit="return false;">
	    <table border="0" cellspacing="0" cellpadding="0" width="100%">
	      <tr>
	        <td><h2>Key Stats</h2></td>

	        <td width="200">
			  <select name="Province" id="Province">
				<option value=""></option>
<?
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

	    <div class="br10"></div>

	    <div id="KeyStats">
		  <table border="0" cellspacing="0" cellpadding="5" width="100%">
	        <tr>
	          <td width="250">No of Active Enumerators in last 24 Hrs</td>
	          <td><a href="./" onclick="return false;" class="tooltip" title="<?= $sActiveEnumerators ?>"><?= formatNumber($iActiveEnumerators, false) ?></a></b></td>
	          <td width="250">No of Surveys Conducted so far</td>
	          <td><a href="./" onclick="return false;" class="tooltip" title="Punjab: <?= formatNumber($iPunjabSurveys, false) ?><br />KPK: <?= formatNumber($iKpkSurveys, false) ?>"><?= formatNumber($iTotalSurveys, false) ?></a></b></td>
	        </tr>

	        <tr>
	          <td>No of Surveys done in last 24 Hrs</td>
	          <td><a href="./" onclick="return false;" class="<?= (($sSurveySchools != "") ? 'tooltip' : '') ?>" title="<?= $sSurveySchools ?>"><?= formatNumber(getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND TIMESTAMPDIFF(MINUTE, `date`, NOW( )) <= '1440' $sSurveysSQL"), false) ?></a></td>
	          <td>No of Schools Disqualified so far</td>
	          <td><a href="./" onclick="return false;" class="tooltip" title="Punjab: <?= formatNumber($iPunjabDisqualified, false) ?><br />KPK: <?= formatNumber($iKpkDisqualified, false) ?>"><?= formatNumber($iTotalDisqualified, false) ?></a></b></td>
	        </tr>

	        <tr>
	          <td>No of Cumulative Surveys done so far</td>
	          <td><a href="./" onclick="return false;"><?= formatNumber(getDbValue("COUNT(1)", "tbl_surveys", "status='C' $sSurveysSQL"), false) ?></a></td>
	          <td>Surveys Planned</td>
	          <td><a href="./" onclick="return false;" class="tooltip" title="Punjab: <?= formatNumber($iPunjabSurveysPlanned, false) ?><br />KPK: <?= formatNumber($iKpkSurveysPlanned, false) ?>"><?= formatNumber($iTotalSurveysPlanned, false) ?></a></b></td>
	        </tr>

	        <tr>
	          <td>Most Active Enumerator in last Week</td>
	          <td><a href="./" onclick="return false;"><?= (($iActiveEnumerator == 0) ? "-" : getDbValue("name", "tbl_admins", "id='$iActiveEnumerator'")) ?> <small><?= (($iEnumeratorSurveys > 0) ? "({$iEnumeratorSurveys} Surveys)" : "") ?></small></a></td>
	          <td>Surveys Conducted</td>
	          <td><a href="./" onclick="return false;" class="tooltip" title="Punjab: <?= formatNumber($iPunjabSurveysConducted, false) ?><br />KPK: <?= formatNumber($iKpkSurveysConducted, false) ?>"><?= formatNumber($iTotalSurveysConducted, false) ?></a></b></td>
	        </tr>
			
	        <tr>
	          <td>Pending Surveys as per Schedule</td>
	          <td><a href="<?= (ADMIN_CP_DIR."/surveys/export-cumulative-schedules.php?Section=Pending&FromDate={$sFromDate}&ToDate={$sToDate}") ?>" <?= (($iTotalPendingSurveys == 0) ? 'onclick="return false;"' : '') ?> class="tooltip" title="Punjab: <?= formatNumber($iPunjabPendingSurveys, false) ?><br />KPK: <?= formatNumber($iKpkPendingSurveys, false) ?>"><?= formatNumber($iTotalPendingSurveys, false) ?></a></td>
	          <td>No of Additional Classrooms Identified</td>
	          <td><a href="./" onclick="return false;" class="tooltip" title="<?= $sDistrictClassRooms ?>"><?= formatNumber($iTotalClassRooms, false) ?></a></b></td>
	        </tr>

	        <tr>
	          <td>Surveys without Concluding Drawings</td>
	          <td><a href="<?= (ADMIN_CP_DIR."/surveys/export-cumulative-schedules.php?Section=NoDrawings&FromDate={$sFromDate}&ToDate={$sToDate}") ?>" <?= (($iTotalSurveysWithoutDrawings == 0) ? 'onclick="return false;"' : '') ?> class="tooltip" title="Punjab: <?= formatNumber($iPunjabSurveysWithoutDrawings, false) ?><br />KPK: <?= formatNumber($iKpkSurveysWithoutDrawings, false) ?>"><?= formatNumber($iTotalSurveysWithoutDrawings, false) ?></a></td>
	          <td>Surveys with Incomplete Information</td>
	          <td><a href="./" onclick="return false;"><?= formatNumber($iIncompleteSurveys, false) ?></a></b></td>
	        </tr>
	      </table>
		</div>  

		<br />
		<h2 style="padding-left:10px;">School Disqualifications Analysis</h2>
		
	    <div id="DisqualificationChart">Loading Graph...</div>

		<script type="text/javascript">
		<!--
			var objChart = new FusionCharts("scripts/FusionCharts/charts/Column3D.swf", "SchoolsDisqualification", "100%", "500", "0", "1");

			objChart.setXMLData("<chart caption='' bgcolor='ffffff' canvasBgColor='ffffff' numDivLines='10' formatNumberScale='0' showValues='1' showLabels='1' decimals='0' numberSuffix='' chartBottomMargin='5' plotFillAlpha='95' labelDisplay='AUTO' exportEnabled='1' exportShowMenuItem='1' exportAtClient='0' exportHandler='scripts/FusionCharts/PHP/FCExporter.php' exportAction='download' exportFileName='inspectors-performance'>" +
<?
	$sPunjabDistricts = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_districts", "province_id='1'");
	$sKpkDistricts    = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_districts", "province_id='2'");
	$iDisqualified    = array( );
	$iDisqualified[]  = 0;

	
	$sSQL = "SELECT school_id FROM tbl_surveys WHERE operational!='Y' AND qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
		$iDisqualified[] = $objDb->getField($i, 0);
	
	$iNotOperational = $iCount;
	$sDisqualified   = @implode(",", $iDisqualified);
	
	
	$sSQL = "SELECT school_id FROM tbl_surveys WHERE pef_programme='Y' AND qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN ($sPunjabDistricts) $sSurveysSQL AND school_id NOT IN ($sDisqualified)";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
		$iDisqualified[] = $objDb->getField($i, 0);
	
	$iPefProgramme = $iCount;
	$sDisqualified  = @implode(",", $iDisqualified);


	$sSQL = "SELECT school_id FROM tbl_surveys WHERE land_available='N' AND qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN ($sPunjabDistricts) $sSurveysSQL AND school_id NOT IN ($sDisqualified)";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
		$iDisqualified[] = $objDb->getField($i, 0);
	
	$iLandNotAvailble = $iCount;
	$sDisqualified    = @implode(",", $iDisqualified);
	
	
	
	$sSQL = "SELECT school_id FROM tbl_surveys WHERE land_dispute!='N' AND land_dispute!='' AND qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL AND school_id NOT IN ($sDisqualified)";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
		$iDisqualified[] = $objDb->getField($i, 0);
	
	$iLandDisputed = $iCount;
	$sDisqualified = @implode(",", $iDisqualified);
	
	
	$sSQL = "SELECT school_id FROM tbl_surveys WHERE other_funding='Y' AND qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL AND school_id NOT IN ($sDisqualified)";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
		$iDisqualified[] = $objDb->getField($i, 0);
	
	$iOtherFunding = $iCount;
	$sDisqualified = @implode(",", $iDisqualified);
	
	
	$sSQL = "SELECT school_id FROM tbl_surveys WHERE avg_attendance<'100' AND qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN ($sKpkDistricts) $sSurveysSQL AND school_id NOT IN ($sDisqualified)";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
		$iDisqualified[] = $objDb->getField($i, 0);
	
	$iLessStudents = $iCount;
	$sDisqualified = @implode(",", $iDisqualified);	
	
	
	$sSQL = "SELECT school_id FROM tbl_surveys WHERE pre_selection='N' AND qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL AND school_id NOT IN ($sDisqualified)";
	$objDb->query($sSQL);
	
	$iPreSelection = $objDb->getCount( );
?>
								"<set tooltext='' label='Not Operational' value='<?= $iNotOperational ?>' link='' />" +
								"<set tooltext='' label='PEF Programme' value='<?= $iPefProgramme ?>' link='' />" +
								"<set tooltext='' label='Land Not Available' value='<?= $iLandNotAvailble ?>' link='' />" +
								"<set tooltext='' label='Land Disputed' value='<?= $iLandDisputed ?>' link='' />" +
								"<set tooltext='' label='Other Funding' value='<?= $iOtherFunding ?>' link='' />" +
								"<set tooltext='' label='Students &lt; 100' value='<?= $iLessStudents ?>' link='' />" +
								"<set tooltext='' label='Pre Selection' value='<?= $iPreSelection ?>' link='' />" +
								"</chart>");


			objChart.render("DisqualificationChart");


			$(document).ready(function( )
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
						{ Province:$(this).val( ), Type:"Surveys" },

						function (sResponse)
						{
							$("#District").html(sResponse);
						},

						"text");
				});


				$("#Dashboard #frmSearch #BtnSearch, #Dashboard #frmSearch #SearchButton .fa").click(function( )
				{
					$.post("ajax/get-survey-stats-graph.php",
						$("#Dashboard #frmSearch").serialize( ),

						function (sResponse)
						{
							var sData = sResponse.split("|-|");
							
							$("#KeyStats").html(sData[0]);
							
							objChart.setXMLData(sData[1]);
							objChart.render("DisqualificationChart");
							
							
							$(".tooltip").tooltipster(
							{
								arrow          :  true,
								contentAsHTML  :  true,
								interactive    :  true,
								theme          :  'tooltipster-light'
							});							
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

<?
	@include("schedules-dashboard.php");
?>

  <br />

  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr valign="top">
	  <td><h2>&nbsp;</h2></td>

	  <td width="1200">
	    <h2>Images from recent Surveys</h2>

<?
	$sSubConditions = "";

	if ($_SESSION["AdminSchools"] != "")
		$sSubConditions .= " AND FIND_IN_SET(s.school_id, '{$_SESSION['AdminSchools']}') ";


	$sSQL = "SELECT p.id, p.survey_id, p.picture, s.school_id, s.district_id, s.date, s.qualified
	         FROM tbl_survey_pictures p, tbl_surveys s
	         WHERE p.survey_id=s.id AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') $sSubConditions
	         ORDER BY p.id DESC
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

		$iSchools       = array( );
		$sCodes         = array( );
		$iDistricts     = array( );


		for ($i = 0; ($i < $iCount && $iIndex < 18); $i ++)
		{
			$iPictureId = $objDb->getField($i, "id");
			$iSurvey    = $objDb->getField($i, "survey_id");
			$sDate      = $objDb->getField($i, "date");
			$sPicture   = $objDb->getField($i, "picture");
			$sQualified = $objDb->getField($i, "qualified");
			$iSchool    = $objDb->getField($i, "school_id");
			$iDistrict  = $objDb->getField($i, "district_id");


			if (!@file_exists(SURVEYS_DOC_DIR.$sPicture))
				continue;
			
			if (!@file_exists(SURVEYS_DOC_DIR.'thumbs/'.$sPicture))
				createImage((SURVEYS_DOC_DIR.$sPicture), (SURVEYS_DOC_DIR.'thumbs/'.$sPicture), 200, 200);


			if (!@in_array($iSchool, $sCodes))
			{
				$sCode = getDbValue("code", "tbl_schools", "id='$iSchool'");

				$sCodes[$iSchool] = $sCode;
			}

			else
				$sCode = $sCodes[$iSchool];
?>
	            <li>
	              <a href="survey-details.php?Id=<?= $iSurvey ?>" class="survey status<?= (($sQualified == "Y") ? "P" : "F") ?>">
	                <img src="<?= (SITE_URL.SURVEYS_DOC_DIR.'thumbs/'.$sPicture) ?>" width="116" height="116" alt="" title="" rel="<?= $iPictureId ?>" /><br />
	                <?= $sCode ?>
	              </a>

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


					$.post("ajax/get-survey-pictures.php",
						{ Id : $("#" + ui.oldPanel.attr("id") + " img:last").attr("rel") },

						function (sResponse)
						{
							ui.newPanel.addClass("slide");
							ui.newPanel.html(sResponse);


							$("a.survey").colorbox({ width:"900px", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });


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
				
				if ((iIndex + 2) == (iTabsCounter - 1))
					$("#Images #Next").addClass("disabled");
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
			
			if (iTabsCounter > 2)
				$("#Images #Next").removeClass("disabled");
 		});
	});
  -->
  </script>
</section>
