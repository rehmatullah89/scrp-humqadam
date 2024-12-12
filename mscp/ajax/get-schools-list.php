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


	$sProvinces  = IO::strValue("Provinces");
	$sDistricts  = IO::strValue("Districts");
	$sSchool     = IO::strValue("School");
	$sContract   = IO::strValue("Contract");
	$iContractId = IO::intValue("ContractId");
	

	$sConditions = "";
	$sSchools    = array( );

	if ($sProvinces != "" && $sProvinces != "0")
		$sConditions .= " AND FIND_IN_SET(province_id, '$sProvinces') ";

	if ($sDistricts != "" && $sDistricts != "0")
		$sConditions .= " AND FIND_IN_SET(district_id, '$sDistricts') ";

	if ($sContract == "Y")
	{
		if ($iContractId > 0)
			$sConditions .= " AND id NOT IN (SELECT s.id FROM tbl_schools s, tbl_contracts c WHERE c.id!='$iContractId' AND s.id IN (c.schools)) ";
		
		else
			$sConditions .= " AND id NOT IN (SELECT s.id FROM tbl_schools s, tbl_contracts c WHERE s.id IN (c.schools)) ";
	}


	if (@is_numeric($sSchool))
		$sSQL = "SELECT id, name, code FROM tbl_schools WHERE code LIKE '{$sSchool}%' $sConditions ORDER BY code LIMIT 10";
	
	else
		$sSQL = "SELECT id, name, code FROM tbl_schools WHERE name LIKE '{$sSchool}%' $sConditions ORDER BY name LIMIT 10";
	
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId   = $objDb->getField($i, "id");
		$sName = $objDb->getField($i, "name");
		$sCode = $objDb->getField($i, "code");


		$sSchools[] = array("id" => $iId, "name" => "{$sName} ({$sCode})");
	}

	print @json_encode($sSchools);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>