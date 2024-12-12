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

	$iProvince = IO::intValue("Province");
	
	
	$sSchoolsSQL = "FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sSchoolsSQL .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";	

	
	$iSchedules     = getDbValue('COUNT(1)', 'tbl_contract_schedules cs, tbl_schools s', "cs.school_id=s.id AND {$sSchoolsSQL}");
	$sContractsList = getList("tbl_contracts", "id", "title");
	$sProvincesList = getList("tbl_provinces", "id", "name");
	$sDistrictsList = getList("tbl_districts", "id", "name", "province_id='$iProvince' AND id IN (SELECT DISTINCT(district_id) FROM tbl_schools)");
	$sPackagesList  = getList("tbl_packages", "id", "title", "status='A'");

	
	print '<select id="Contract">';
	print '<option value="">All Contracts</option>';

	foreach ($sContractsList as $iContract => $sContract)
	{
		print @utf8_encode('<option value="'.(($iSchedules <= 50) ? $sContract : $iContract).'">'.$sContract.'</option>');
	}

	print '</select>';
       
	   
    if (count($sProvincesList) > 1)
	{
		print '<select id="Province">';
		print '<option value="">All Provinces</option>';
		
		foreach ($sProvincesList as $iProvince => $sProvince)
		{
			print @utf8_encode('<option value="'.(($iSchedules > 50) ? $iProvince : $sProvince).'">'.$sProvince.'</option>');
		}
		
		print '</select>';

		
		print '<select id="District">';
		print '<option value="">All Districts</option>';

		foreach ($sDistrictsList as $iDistrict => $sDistrict)
		{
			print @utf8_encode('<option value="'.(($iSchedules > 50) ? $iDistrict : $sDistrict).'">'.$sDistrict.'</option>');
		}

		print '</select>';
	}

	
	print '<select id="Package" style="width:290px;">';
	print '<option value="">All Packages</option>';
	
	foreach ($sPackagesList as $iPackage => $sPackage)
	{
		print @utf8_encode('<option value="'.(($iSchedules > 50) ? $iPackage : $sPackage).'">'.$sPackage.'</option>');
	}
	
	print '</select>';
	
        
    print '<select id="DesignType">';
	print '<option value="">All Design Types</option>';
	print @utf8_encode('<option value="'.(($iSchedules > 50) ? "R" : "Regular").'">Regular Design</option>');
	print @utf8_encode('<option value="'.(($iSchedules > 50) ? "B" : "Bespoke").'">Bespoke Design</option>');
	print '</select>';


	print '<select id="StoreyType">';
	print '<option value="">All Storey Types</option>';
	print @utf8_encode('<option value="'.(($iSchedules > 50) ? "S" : "Single").'">Single Storey</option>');
	print @utf8_encode('<option value="'.(($iSchedules > 50) ? "D" : "Double").'">Double Storey</option>');
	print '</select>';	

	
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>