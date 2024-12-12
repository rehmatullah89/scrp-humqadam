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


	$sSchoolsSQL = "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sSchoolsSQL .= " AND FIND_IN_SET(id, '{$_SESSION['AdminSchools']}') ";	

	
	$iSchools       = getDbValue("COUNT(1)", "tbl_schools", $sSchoolsSQL);
	$sProvincesList = getList("tbl_provinces", "id", "name");
	$sTypesList     = getList("tbl_school_types", "id", "`type`");

	if (count($sProvincesList) > 1)
	{
		print '<select id="District">';
		print '<option value="">All Districts</option>';

		foreach ($sProvincesList as $iProvince => $sProvince)
		{
			print @utf8_encode('<optgroup label="'.$sProvince.'">');


			$sDistrictsList = getList("tbl_districts", "id", "name", "province_id='$iProvince' AND id IN (SELECT DISTINCT(district_id) FROM tbl_schools)");

			foreach ($sDistrictsList as $iDistrict => $sDistrict)
			{
				print @utf8_encode('<option value="'.(($iSchools > 50) ? $iDistrict : $sDistrict).'">'.$sDistrict.'</option>');
			}

			print '</optgroup>';
		}

		print '</select>';
	}
	
	
	print '<select id="DesignType">';
	print '<option value="">All Design Types</option>';
	print @utf8_encode('<option value="'.(($iSchools > 50) ? "R" : "Regular").'">Regular Design</option>');
	print @utf8_encode('<option value="'.(($iSchools > 50) ? "B" : "Bespoke").'">Bespoke Design</option>');
	print '</select>';


	print '<select id="StoreyType">';
	print '<option value="">All Storey Types</option>';
	print @utf8_encode('<option value="'.(($iSchools > 50) ? "S" : "Single").'">Single Storey</option>');
	print @utf8_encode('<option value="'.(($iSchools > 50) ? "D" : "Double").'">Double Storey</option>');
	print @utf8_encode('<option value="'.(($iSchools > 50) ? "T" : "Triple").'">Triple Storey</option>');
	print '</select>';		
	
	
	print '<select id="Type">';
	print '<option value="">All Types</option>';

	foreach ($sTypesList as $iType => $sType)
	{
		print @utf8_encode('<option value="'.(($iSchools > 50) ? $iType : $sType).'">'.$sType.'</option>');
	}
	
	print '</select>';


	if ($iSchools > 50)
	{
		print '<select id="WorkType">';
		print '<option value="">All Work Types</option>';
		print @utf8_encode('<option value="N">New Construction</option>');
		print @utf8_encode('<option value="B">New Construction & Rehabilitation</option>');
		print @utf8_encode('<option value="R">Rehabilitation Only</option>');
		print '</select>';

		print '<select id="Status">';
		print '<option value="">All Status</option>';
		print @utf8_encode('<option value="completed">Completed</option>');
		print @utf8_encode('<option value="active">Active</option>');
		print @utf8_encode('<option value="adopted">Adopted</option>');
		print @utf8_encode('<option value="qualified">Qualified</option>');
		print @utf8_encode('<option value="dropped">Dropped</option>');
		print '</select>';

	}

	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>