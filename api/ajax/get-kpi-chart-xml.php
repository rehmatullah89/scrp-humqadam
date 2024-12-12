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

	@require_once("../../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );


	$sUser = IO::strValue('User');
	$sType = IO::strValue('Type');


	if ($sUser == "")
		die("Invalid Request");


	$sSQL = "SELECT id, provinces, districts, schools, status FROM tbl_admins WHERE MD5(id)='$sUser'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) == 0)
		die("Invalid User");

	else if ($objDb->getField(0, "status") != "A")
		die("User Account is Disabled");

	else
	{
		$sProvinces = $objDb->getField(0, "provinces");
		$sDistricts = $objDb->getField(0, "districts");
		$sSchools   = $objDb->getField(0, "schools");
	}



	$sConditions = "";

	if ($sSchools != "")
		$sConditions .= " AND FIND_IN_SET(s.id, '$sSchools') ";

	else
	{
		if ($sProvinces != "" && $sProvinces != "0")
			$sConditions .= " AND FIND_IN_SET(s.province_id, '$sProvinces') ";

		if ($sDistricts != "" && $sDistricts != "0")
			$sConditions .= " AND FIND_IN_SET(s.district_id, '$sDistricts') ";
	}


	$iSchools = getDbValue("COUNT(1)", "tbl_schools s", "s.status='A' AND s.dropped!='Y' $sConditions");
	$iActive  = getDbValue("COUNT(DISTINCT(s.id))", "tbl_schools s, tbl_contracts c", "s.status='A' AND s.dropped!='Y' AND c.status='A' AND FIND_IN_SET(s.id, c.schools) $sConditions");

	$fActive  = @round( (($iActive / $iSchools) * 100), 2);


	header("Content-type: text/xml");
?>
<chart caption=" Active Projects <?= formatNumber($fActive, true) ?>% " bgcolor="ffffff" canvasBgColor="ffffff" bgAlpha="100" canvasbgAlpha="100" showBorder="0" animation="1" numberPrefix="%" showPercentageValues="1" isSmartLineSlanted="0" showValues="0" showLabels="0" showToolTip="0" showLegend="0" chartTopMargin="0" chartBottomMargin="0" pieRadius="96" clickURL="">
<set value="<?= formatNumber($fActive) ?>" label="" color="f49e1d" alpha="100" link="" />
<set value="<?= formatNumber(100 - $fActive) ?>" label="" color="d1d1d2" alpha="100" link="" />

<styles>
<definition>
<style name="CaptionFont" type="FONT" face="Verdana" size="15" color="f49e1d" align="left" bold="1" />
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