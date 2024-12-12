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


	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");
	$sFromDate = IO::strValue("FromDate");
	$sToDate   = IO::strValue("ToDate");


	$sConditions = " WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND (`date` BETWEEN '$sFromDate' AND '$sToDate') ";

	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";

	if ($iProvince > 0)
	{
		$sDistricts = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_districts", "province_id='$iProvince'");

		$sConditions .= " AND FIND_IN_SET(district_id, '$sDistricts') ";
	}

	if ($iDistrict > 0)
		$sConditions .= " AND district_id='$iDistrict' ";



	$sSQL = "SELECT admin_id,
	                SUM(IF(status='P','1','0')) AS _Passed,
	                SUM(IF(status='R','1','0')) AS _ReInspections,
	                SUM(IF(status='F','1','0')) AS _Failed,
	                COUNT(1) AS _Inspections,
					COUNT(DISTINCT(school_id)) AS _UniqueVisits,
	                (SELECT name FROM tbl_admins WHERE id=tbl_inspections.admin_id) AS _Inspector,
	                (SELECT type_id FROM tbl_admins WHERE id=tbl_inspections.admin_id) AS _Type
	         FROM tbl_inspections
	         $sConditions
	         GROUP BY admin_id
	         ORDER BY _Inspections DESC";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );



	$sInspectorTypes = getList("tbl_admin_types", "id", "title");

	header("Content-type: text/xml");
?>
<chart caption='' bgcolor='ffffff' canvasBgColor='ffffff' numDivLines='10' formatNumberScale='0' showValues='0' showSum='1' showLabels='1' decimals='0' numberSuffix='' chartBottomMargin='5' plotFillAlpha='95' labelDisplay='AUTO' exportEnabled='1' exportShowMenuItem='1' exportAtClient='0' exportHandler='scripts/FusionCharts/PHP/FCExporter.php' exportAction='download' exportFileName='inspectors-performance'>
	<categories>
<?
	for ($i = 0; $i < $iCount; $i ++)
	{
		$sInspector = $objDb->getField($i, "_Inspector");
?>
	<category label='<?= $sInspector ?>' />
<?
	}
?>
	</categories>

	<dataset seriesName='Pass' color='7fff7f'>
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
		$iUniqueVisits  = $objDb->getField($i, "_UniqueVisits");
?>
	<set tooltext='<?= $sInspector ?>{br}<?= $sInspectorTypes[$iType] ?>{br}{br}Total: <?= $iInspections ?>{br}Passed: <?= $iPassed ?>{br}Re-Inspections: <?= $iReInspections ?>{br}Failed: <?= $iFailed ?>{br}{br}Unique Visits: <?= $iUniqueVisits ?>' value='<?= $iPassed ?>' link='javascript:showInspections("<?= $iInspector ?>", "P", "<?= $iProvince ?>", "<?= $iDistrict ?>", "<?= $sFromDate ?>", "<?= $sToDate ?>")' />
<?
	}
?>
	</dataset>

	<dataset seriesName='Re-Inspection' color='fcbf04'>
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
		$iUniqueVisits  = $objDb->getField($i, "_UniqueVisits");
?>
	<set tooltext='<?= $sInspector ?>{br}<?= $sInspectorTypes[$iType] ?>{br}{br}Total: <?= $iInspections ?>{br}Passed: <?= $iPassed ?>{br}Re-Inspections: <?= $iReInspections ?>{br}Failed: <?= $iFailed ?>{br}{br}Unique Visits: <?= $iUniqueVisits ?>' value='<?= $iReInspections ?>' link='javascript:showInspections("<?= $iInspector ?>", "R", "<?= $iProvince ?>", "<?= $iDistrict ?>", "<?= $sFromDate ?>", "<?= $sToDate ?>")' />
<?
	}
?>
	</dataset>

	<dataset seriesName='Failed' color='ff0000'>
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
		$iUniqueVisits  = $objDb->getField($i, "_UniqueVisits");
?>
	<set tooltext='<?= $sInspector ?>{br}<?= $sInspectorTypes[$iType] ?>{br}{br}Total: <?= $iInspections ?>{br}Passed: <?= $iPassed ?>{br}Re-Inspections: <?= $iReInspections ?>{br}Failed: <?= $iFailed ?>{br}{br}Unique Visits: <?= $iUniqueVisits ?>' value='<?= $iFailed ?>' link='javascript:showInspections("<?= $iInspector ?>", "F", "<?= $iProvince ?>", "<?= $iDistrict ?>", "<?= $sFromDate ?>", "<?= $sToDate ?>")' />
<?
	}
?>
	</dataset>
</chart>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>
