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
	

	$sActiveEngineers = "";


	$sSQL = "SELECT name FROM tbl_admins WHERE status='A' AND type_id='6' AND TIMESTAMPDIFF(MINUTE, location_time, NOW( )) <= '1440' $sDistrictsSQL ORDER BY name";
	$objDb->query($sSQL);


	$iActiveEngineers = $objDb->getCount( );

	for ($i = 0; $i < $iActiveEngineers; $i ++)
	{
		$sEngineer = $objDb->getField($i, "name");

		$sActiveEngineers .= (($i + 1).". {$sEngineer}<br />");
	}
	
		
	$sSorsSQL = " AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";
	
	if ($iDistrict > 0)
		$sSorsSQL .= " AND district_id='$iDistrict' ";
	
	else if ($iProvince > 0)
		$sSorsSQL .= " AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='$iProvince') ";

	if ($_SESSION["AdminSchools"] != "")
		$sSorsSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";


	$iActiveEngineer = getDbValue("admin_id", "tbl_sors", "status='C' AND DATEDIFF(NOW( ), `date`) <= '7' $sSorsSQL", "COUNT(1) DESC", "admin_id");
	$iEngineerSors   = getDbValue("COUNT(1)", "tbl_sors", "status='C' AND DATEDIFF(NOW( ), `date`) <= '7' AND admin_id='$iActiveEngineer' $sSorsSQL");


	$sSQL = "SELECT DISTINCT(CONCAT(s.name, ' (', s.code, ')'))
	         FROM tbl_schools s, tbl_sors bs
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
	
	
	$iTotalSors  = getDbValue("COUNT(1)", "tbl_sors", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') $sSorsSQL");
	$iPunjabSors = getDbValue("COUNT(1)", "tbl_sors", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='1') $sSorsSQL");
	$iKpkSors    = getDbValue("COUNT(1)", "tbl_sors", "status='C' AND (`date` BETWEEN '$sFromDate' AND '$sToDate') AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='2') $sSorsSQL");
?>
		  <table border="0" cellspacing="0" cellpadding="5" width="100%">
	        <tr>
	          <td width="250">No of Active Engineers in last 24 Hrs</td>
	          <td><a href="./" onclick="return false;" class="tooltip" title="<?= $sActiveEngineers ?>"><?= formatNumber($iActiveEngineers, false) ?></a></b></td>
	          <td width="250">No of SORs Conducted so far</td>
	          <td><a href="./" onclick="return false;" class="tooltip" title="Punjab: <?= formatNumber($iPunjabSors, false) ?><br />KPK: <?= formatNumber($iKpkSors, false) ?>"><?= formatNumber($iTotalSors, false) ?></a></b></td>
	        </tr>

	        <tr>
	          <td>No of SORs done in last 24 Hrs</td>
	          <td><a href="./" onclick="return false;" class="<?= (($sSurveySchools != "") ? 'tooltip' : '') ?>" title="<?= $sSurveySchools ?>"><?= formatNumber(getDbValue("COUNT(1)", "tbl_sors", "status='C' AND TIMESTAMPDIFF(MINUTE, `date`, NOW( )) <= '1440' $sSorsSQL"), false) ?></a></td>
	          <td></td>
	          <td></td>
	        </tr>

	        <tr>
	          <td>Most Active Engineer in last Week</td>
	          <td><a href="./" onclick="return false;"><?= (($iActiveEngineer == 0) ? "-" : getDbValue("name", "tbl_admins", "id='$iActiveEngineer'")) ?> <small><?= (($iEngineerSors > 0) ? "({$iEngineerSors} SORs)" : "") ?></small></a></td>
	          <td></td>
	          <td></td>
	        </tr>
	      </table>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>