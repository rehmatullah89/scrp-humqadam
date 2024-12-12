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


	$iAdmins = getDbValue('COUNT(1)', 'tbl_admins');

	if ($iAdmins > 50)
	{
		$sProvincesList = getList("tbl_provinces", "id", "name");
		$sDistrictsList = getList("tbl_districts", "id", "name");


		print '<select id="Province">';
		print '<option value="">All Provinces</option>';

		foreach ($sProvincesList as $iProvince => $sProvince)
		{
			print @utf8_encode('<option value="'.$iProvince.'">'.$sProvince.'</option>');
		}

		print '</select>';


		print '<select id="District">';
		print '<option value="">All Districts</option>';

		foreach ($sDistrictsList as $iDistrict => $sDistrict)
		{
			print @utf8_encode('<option value="'.$iDistrict.'">'.$sDistrict.'</option>');
		}

		print '</select>';
	}



	$sTypesList = getList("tbl_admin_types", "id", "title");

	print '<select id="Type">';
	print '<option value="">All Types</option>';

	foreach ($sTypesList as $iType => $sType)
	{
		print @utf8_encode('<option value="'.(($iAdmins > 50) ? $iType : $sType).'">'.$sType.'</option>');
	}

	print '</select>';


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>