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


	$iContracts = getDbValue("COUNT(1)", "tbl_contracts", "status='A'");
	$iPackages  = getDbValue("COUNT(1)", "tbl_packages", "status='A'");


	header("Content-type: text/xml");
?>
<chart caption="Active Packages/Contracts" bgcolor="ffffff" canvasBgColor="ffffff" bgAlpha="100" canvasbgAlpha="100" showBorder="0" animation="1" numberPrefix="" showPercentageValues="0" formatNumberScale="0" isSmartLineSlanted="1" showValues="1" showLabels="0" showToolTip="1" showLegend="1" chartTopMargin="0" chartBottomMargin="0" pieRadius="60" radius3D="1" clickURL="">
<set value="<?= $iContracts ?>" label="Active Contracts" tooltext="Active Contracts: <?= $iContracts ?>" link="" />
<set value="<?= $iPackages ?>" label="Active Packages" tooltext="Active Packages: <?= $iPackages ?>" link="" />

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