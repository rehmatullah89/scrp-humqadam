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
	$sType     = IO::strValue('Type');


	if ($_SESSION['AdminProvinces'] == "" && $_SESSION['AdminDistricts'] == "")
	{
		$sSchoolsConditions   = "status='A' AND dropped!='Y' AND qualified='Y' ";
		$sInspectedConditions = "s.status='A' AND s.dropped!='Y' AND s.id=i.school_id ";
		$sContractsConditions = "s.status='A' AND s.dropped!='Y' AND c.status='A' AND FIND_IN_SET(s.id, c.schools) ";
		$sDroppedConditions   = "status='A' AND dropped='Y' ";
	}

	else
	{
		$sSchoolsConditions   = "status='A' AND dropped!='Y' AND qualified='Y' AND FIND_IN_SET(province_id, '{$_SESSION['AdminProvinces']}') AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";
		$sInspectedConditions = "s.status='A' AND s.dropped!='Y' AND s.id=i.school_id AND FIND_IN_SET(s.province_id, '{$_SESSION['AdminProvinces']}') AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') ";
		$sContractsConditions = "s.status='A' AND s.dropped!='Y' AND c.status='A' AND FIND_IN_SET(s.id, c.schools) AND FIND_IN_SET(s.province_id, '{$_SESSION['AdminProvinces']}') AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') ";
		$sDroppedConditions   = "status='A' AND dropped='Y' AND FIND_IN_SET(province_id, '{$_SESSION['AdminProvinces']}') AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";
	}


	if ($_SESSION["AdminSchools"] != "")
	{
		$sSchoolsConditions   .= " AND FIND_IN_SET(id, '{$_SESSION['AdminSchools']}') ";
		$sInspectedConditions .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";
		$sContractsConditions .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";
		$sDroppedConditions   .= " AND FIND_IN_SET(id, '{$_SESSION['AdminSchools']}') ";
	}

	if ($iPackage > 0)
	{
		$sSchools = getDbValue("schools", "tbl_packages", "id='$iPackage'");


		$sSchoolsConditions   .= " AND FIND_IN_SET(id, '$sSchools') ";
		$sInspectedConditions .= " AND FIND_IN_SET(s.id, '$sSchools') ";
		$sContractsConditions .= " AND FIND_IN_SET(s.id, '$sSchools') ";
		$sDroppedConditions   .= " AND FIND_IN_SET(id, '$sSchools') ";
	}

	if ($iProvince > 0)
	{
		$sDistricts = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_districts", "id='$iProvince' AND FIND_IN_SET(id, '{$_SESSION['AdminDistricts']}')");


		$sSchoolsConditions   .= " AND province_id='$iProvince' ";
		$sInspectedConditions .= " AND FIND_IN_SET(s.district_id, '$sDistricts') ";
		$sContractsConditions .= " AND FIND_IN_SET(s.district_id, '$sDistricts') ";
		$sDroppedConditions   .= " AND FIND_IN_SET(district_id, '$sDistricts') ";
	}

	if ($iDistrict > 0)
	{
		$sSchoolsConditions   .= " AND district_id='$iDistrict' ";
		$sInspectedConditions .= " AND s.district_id='$iDistrict' ";
		$sContractsConditions .= " AND s.district_id='$iDistrict' ";
		$sDroppedConditions   .= " AND district_id='$iDistrict' ";
	}


	if ($sType == "ProjectActivity")
	{
		$iMilestoneStageS = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='S'", "position DESC");
		$iMilestoneStageD = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='D'", "position DESC");
		$iMilestoneStageT = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='T'", "position DESC");
		$iMilestoneStageB = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='B'", "position DESC");
		$iMilestoneStages = array( );
		
		$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND ((`type`='S' AND position>'$iMilestoneStageS') OR (`type`='D' AND position>'$iMilestoneStageD') OR (`type`='T' AND position>'$iMilestoneStageT') OR (`type`='B' AND position>'$iMilestoneStageB')) ORDER BY position";
		$objDb->query($sSQL);
		
		$iCount = $objDb->getCount( );
		
		for ($i = 0; $i < $iCount; $i ++)
			$iMilestoneStages[] = $objDb->getField($i, 0);
			
		$sMilestoneStages = @implode(",", $iMilestoneStages);
		

	
		$iSchools = getDbValue("COUNT(1)", "tbl_schools", $sSchoolsConditions);
		$iActive  = getDbValue("COUNT(DISTINCT(s.id))", "tbl_schools s, tbl_inspections i", "{$sInspectedConditions} AND i.stage_id IN ($sMilestoneStages)");

		$fActive  = @round( (($iActive / $iSchools) * 100), 2);

		$fTotal      = $fActive;
		$sTitle      = "Active Schools";
		$sSubCaption = "{br}Qualified: {$iSchools}{br}Active: {$iActive}";
	}

	else if ($sType == "Contracts")
	{
		$iSchools    = getDbValue("COUNT(1)", "tbl_schools", $sSchoolsConditions);
		$iContracted = getDbValue("COUNT(DISTINCT(s.id))", "tbl_schools s, tbl_contracts c", $sContractsConditions);

		$fActive  = @round( (($iContracted / $iSchools) * 100), 2);


		$fTotal = $fActive;
		$sTitle = "Contracted Schools";
		
		
		$sSubCaption        = "{br}Qualified: {$iSchools}{br}Contracted: {$iContracted}";
		$sContractedSchools = "javascript:exportContractedSchools( )";
	}

	else if ($sType == "Dropped")
	{
		$iSchools = getDbValue("COUNT(1)", "tbl_schools", $sSchoolsConditions);
		$iDropped = getDbValue("COUNT(1)", "tbl_schools", $sDroppedConditions);

		$fDropped  = @round( (($iDropped / $iSchools) * 100), 2);

		$fTotal      = $fDropped;
		$sTitle      = "Dropped Schools";
		$sSubCaption = "{br}Qualified: {$iSchools}{br}Dropped: {$iDropped}";
	}


	header("Content-type: text/xml");
?>
<chart caption="<?= $sTitle ?>{br}<?= formatNumber($fTotal, true) ?>%" subCaption="<?= $sSubCaption ?>" bgcolor="ffffff" canvasBgColor="ffffff" bgAlpha="100" canvasbgAlpha="100" showBorder="0" animation="1" numberPrefix="%" showPercentageValues="1" isSmartLineSlanted="0" showValues="0" showLabels="0" showToolTip="1" showLegend="0" chartTopMargin="0" chartBottomMargin="0" pieRadius="96" clickURL="">
<set value="<?= formatNumber($fTotal) ?>" label="" color="f49e1d" alpha="100" link="<?= ((($sType == "Dropped") ? 'javascript:showDroppedSchools( )' : '').$sContractedSchools) ?>" />
<set value="<?= formatNumber(100 - $fTotal) ?>" label="" color="d1d1d2" alpha="100" link="<?= $sContractedSchools ?>" />

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