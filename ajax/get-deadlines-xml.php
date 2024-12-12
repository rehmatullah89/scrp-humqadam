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


	$iPackage  = IO::intValue("Package");
	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");


	$sConditions = " AND s.status='A' AND s.dropped!='Y' AND FIND_IN_SET(s.province_id, '{$_SESSION['AdminProvinces']}') AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') ";

	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";


	if ($iPackage > 0 || $iProvince > 0)
	{
		$sConditions .= " AND (";

		if ($iPackage > 0)
		{
			$sSchools = getDbValue("schools", "tbl_packages", "id='$iPackage'");

			$sConditions .= " FIND_IN_SET(s.id, '$sSchools') ";
		}

		if ($iPackage > 0 && $iProvince > 0)
			$sConditions .= " OR ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " (";

		if ($iProvince > 0)
			$sConditions .= " s.province_id='$iProvince' ";

		if ($iDistrict > 0)
			$sConditions .= " AND s.district_id='$iDistrict' ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " )";

		$sConditions .= ")";
	}



	$sSQL = "SELECT s.id, s.code, s.progress
	         FROM tbl_contract_schedules cs, tbl_contract_schedule_details csd, tbl_schools s
	         WHERE cs.id=csd.schedule_id AND s.id=cs.school_id AND csd.end_date >= CURDATE( ) $sConditions
	         GROUP BY s.id
	         ORDER BY csd.end_date ASC
	         LIMIT 5";
	$objDb->query($sSQL);

	$iCount   = $objDb->getCount( );
	$sSchools = array( );

	for ($i = 0; $i < $iCount; $i ++)
		$sSchools[$objDb->getField($i, "code")] = $objDb->getField($i, "progress");


	header("Content-type: text/xml");
?>
<chart caption="Upcoming Deadlines" subcaption="" bgcolor="ffffff" canvasBgColor="ffffff" bgAlpha="100" canvasbgAlpha="100" yAxisMinValue="0" yAxisMaxValue="100" numDivLines="10" formatNumberScale="0" showBorder="0" animation="1" numberPrefix="" numberSuffix="%" showPercentageValues="1" isSmartLineSlanted="0" showValues="1" showLabels="1" showToolTip="1" showLegend="1" slantLabels="1" labelDisplay="ROTATE" chartTopMargin="0" chartBottomMargin="0" legendPosition='BOTTOM' legendCaption='' clickURL="">
<?
	foreach ($sSchools as $sCode => $fProgress)
	{
?>
<set value="<?= intval($fProgress) ?>" label="<?= $sCode ?>" tooltext="<?= $sCode ?>" link="" />
<?
	}
?>

<styles>
<definition>
<style name="CaptionFont" type="FONT" face="Verdana" size="14" color="333333" align="left" bold="1" />
</definition>

<application>
<apply toObject="CAPTION" styles="CaptionFont" />
</application>
</styles>

</chart>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>