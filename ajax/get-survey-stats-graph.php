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


	$sFromDate = IO::strValue("FromDate");
	$sToDate   = IO::strValue("ToDate");
	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");
	
	
	$iDistricts    = @explode(",", $_SESSION['AdminDistricts']);
	$sDistrictsSQL = "";

	foreach ($iDistricts as $iAdminDistrict)
	{
		if ($sDistrictsSQL != "")
			$sDistrictsSQL .= " OR ";

		$sDistrictsSQL .= " FIND_IN_SET('$iAdminDistrict', districts) ";
	}

	if ($sDistrictsSQL != "")
		$sDistrictsSQL = " AND ($sDistrictsSQL) ";

	if ($iProvince > 0)
		$sDistrictsSQL .= " AND FIND_IN_SET('$iProvince', provinces) ";
	
	if ($iDistrict > 0)
		$sDistrictsSQL .= " AND FIND_IN_SET('$iDistrict', districts) ";
	

	$sActiveEnumerators = "";


	$sSQL = "SELECT name FROM tbl_admins WHERE status='A' AND type_id='12' AND TIMESTAMPDIFF(MINUTE, location_time, NOW( )) <= '1440' $sDistrictsSQL ORDER BY name";
	$objDb->query($sSQL);


	$iActiveEnumerators = $objDb->getCount( );

	for ($i = 0; $i < $iActiveEnumerators; $i ++)
	{
		$sEnumerator = $objDb->getField($i, "name");

		$sActiveEnumerators .= (($i + 1).". {$sEnumerator}<br />");
	}
	
		
	$sSurveysSQL = " AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";
	
	if ($iDistrict > 0)
		$sSurveysSQL .= " AND district_id='$iDistrict' ";
	
	else if ($iProvince > 0)
		$sSurveysSQL .= " AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='$iProvince') ";

	if ($_SESSION["AdminSchools"] != "")
		$sSurveysSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";


	$iActiveEnumerator  = getDbValue("created_by", "tbl_surveys", "status='C' AND DATEDIFF(NOW( ), `date`) <= '7' $sSurveysSQL", "COUNT(1) DESC", "created_by");
	$iEnumeratorSurveys = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND DATEDIFF(NOW( ), `date`) <= '7' AND created_by='$iActiveEnumerator' $sSurveysSQL");


	$sSQL = "SELECT DISTINCT(CONCAT(s.name, ' (', s.code, ')'))
	         FROM tbl_schools s, tbl_surveys bs
	         WHERE s.id=bs.school_id AND bs.status='C' AND TIMESTAMPDIFF(MINUTE, bs.`date`, NOW( )) <= '1440'
	               AND FIND_IN_SET(s.province_id, '{$_SESSION['AdminProvinces']}') AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') ";
				   
	if ($iDistrict > 0)
		$sSQL .= " AND s.district_id='$iDistrict' ";
	
	else if ($iProvince > 0)
		$sSQL .= " AND s.province_id='$iProvince' ";

	if ($_SESSION["AdminSchools"] != "")
		$sSQL .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";

	$sSQL .= "ORDER BY s.name, s.code";
	$objDb->query($sSQL);

	$iSurveySchools = $objDb->getCount( );
	$sSurveySchools = "";

	for ($i = 0; $i < $iSurveySchools; $i ++)
		$sSurveySchools .= (($i + 1).". ".$objDb->getField($i, 0)."<br />");
	
	
	$iTotalSurveys                 = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL");
	$iPunjabSurveys                = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='1') $sSurveysSQL");
	$iKpkSurveys                   = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='2') $sSurveysSQL");
	
	$iTotalDisqualified            = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSurveysSQL");
	$iPunjabDisqualified           = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='1') $sSurveysSQL");
	$iKpkDisqualified              = getDbValue("COUNT(1)", "tbl_surveys", "status='C' AND qualified='N' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='2') $sSurveysSQL");
	
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
	          <td><a href="<?= (ADMIN_CP_DIR."/surveys/export-cumulative-schedules.php?Section=Pending&FromDate={$sFromDate}&ToDate={$sToDate}&Province={$iProvince}&District={$iDistrict}") ?>" <?= (($iTotalPendingSurveys == 0) ? 'onclick="return false;"' : '') ?> class="tooltip" title="Punjab: <?= formatNumber($iPunjabPendingSurveys, false) ?><br />KPK: <?= formatNumber($iKpkPendingSurveys, false) ?>"><?= formatNumber($iTotalPendingSurveys, false) ?></a></td>
	          <td>No of Additional Classrooms Identified</td>
	          <td><a href="./" onclick="return false;" class="tooltip" title="<?= $sDistrictClassRooms ?>"><?= formatNumber($iTotalClassRooms, false) ?></a></b></td>
	        </tr>

	        <tr>
	          <td>Surveys without Concluding Drawings</td>
			  <td><a href="<?= (ADMIN_CP_DIR."/surveys/export-cumulative-schedules.php?Section=NoDrawings&FromDate={$sFromDate}&ToDate={$sToDate}&Province={$iProvince}&District={$iDistrict}") ?>" <?= (($iTotalSurveysWithoutDrawings == 0) ? 'onclick="return false;"' : '') ?> class="tooltip" title="Punjab: <?= formatNumber($iPunjabSurveysWithoutDrawings, false) ?><br />KPK: <?= formatNumber($iKpkSurveysWithoutDrawings, false) ?>"><?= formatNumber($iTotalSurveysWithoutDrawings, false) ?></a></td>
	          <td>Surveys with Incomplete Information</td>
	          <td><a href="./" onclick="return false;"><?= formatNumber($iIncompleteSurveys, false) ?></a></b></td>
	        </tr>
	      </table>
		  
		  |-|
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
			<chart caption='' bgcolor='ffffff' canvasBgColor='ffffff' numDivLines='10' formatNumberScale='0' showValues='1' showLabels='1' decimals='0' numberSuffix='' chartBottomMargin='5' plotFillAlpha='95' labelDisplay='AUTO' exportEnabled='1' exportShowMenuItem='1' exportAtClient='0' exportHandler='scripts/FusionCharts/PHP/FCExporter.php' exportAction='download' exportFileName='inspectors-performance'>
			<set tooltext='' label='Not Operational' value='<?= $iNotOperational ?>' link='' />
			<set tooltext='' label='PEF Programme' value='<?= $iPefProgramme ?>' link='' />
			<set tooltext='' label='Land Not Available' value='<?= $iLandNotAvailble ?>' link='' />
			<set tooltext='' label='Land Disputed' value='<?= $iLandDisputed ?>' link='' />
			<set tooltext='' label='Other Funding' value='<?= $iOtherFunding ?>' link='' />
			<set tooltext='' label='Students &lt; 100' value='<?= $iLessStudents ?>' link='' />
			<set tooltext='' label='Pre Selection' value='<?= $iPreSelection ?>' link='' />
			</chart>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>