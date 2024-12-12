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


	$sConditions = "status='A' AND dropped!='Y' AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";

	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(id, '{$_SESSION['AdminSchools']}') ";

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



	$sProvincesList = getList("tbl_provinces", "id", "code", "FIND_IN_SET(id, '{$_SESSION['AdminProvinces']}')");
	$iSchools       = array( );

	foreach ($sProvincesList as $iProvince => $sProvince)
	{
		$iSchools[$iProvince] = getDbValue("COUNT(1)", "tbl_schools", "{$sConditions} AND province_id='$iProvince'");
	}


	header("Content-type: text/xml");
?>
<chart caption="School Stats" subcaption="" bgcolor="ffffff" canvasBgColor="ffffff" radius3D="1" pieRadius="60" bgAlpha="100" canvasbgAlpha="100" showBorder="0" animation="1" numberPrefix="" formatNumberScale="0" showPercentageValues="0" isSmartLineSlanted="1" showValues="1" showLabels="0" showToolTip="1" showLegend="1" chartTopMargin="0" chartBottomMargin="0" legendPosition='BOTTOM' legendCaption='' clickURL="">
<?
	foreach ($sProvincesList as $iProvince => $sProvince)
	{
?>
<set value="<?= $iSchools[$iProvince] ?>" label="<?= $sProvince ?>" tooltext="<?= $sProvince ?>: <?= $iSchools[$iProvince] ?>" link="" />
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