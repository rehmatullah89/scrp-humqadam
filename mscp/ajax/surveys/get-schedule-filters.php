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


	$iSchedules       = getDbValue("COUNT(1)", "tbl_survey_schedules");
	$sProvincesList   = getList("tbl_provinces", "id", "name");
	$sEnumeratorsList = getList("tbl_admins", "id", "name", "type_id='12' AND id IN (SELECT DISTINCT(admin_id) FROM tbl_survey_schedules)");

	
	print '<select id="Enumerator">';
	print '<option value="">All Enumerators</option>';

	foreach ($sEnumeratorsList as $iEnumerator => $sEnumerator)
	{
		print @utf8_encode('<option value="'.(($iSchedules > 50) ? $iEnumerator : $sEnumerator).'">'.$sEnumerator.'</option>');
	}

	print '</select>';
		

	if (count($sProvincesList) > 1)
	{
		print '<select id="District">';
		print '<option value="">All Districts</option>';

		foreach ($sProvincesList as $iProvince => $sProvince)
		{
			print @utf8_encode('<optgroup label="'.$sProvince.'">');


			$sDistrictsList = getList("tbl_districts", "id", "name", "province_id='$iProvince' AND id IN (SELECT DISTINCT(district_id) FROM tbl_survey_schedules)");

			foreach ($sDistrictsList as $iDistrict => $sDistrict)
			{
				print @utf8_encode('<option value="'.(($iSchedules > 50) ? $iDistrict : $sDistrict).'">'.$sDistrict.'</option>');
			}

			print '</optgroup>';
		}

		print '</select>';
	}


	print '<select id="Status">';
	print '<option value="">Any Status</option>';
	print @utf8_encode('<option value="'.(($iSchedules > 50) ? "C" : "Completed").'">Completed</option>');
	print @utf8_encode('<option value="'.(($iSchedules > 50) ? "P" : "Pending").'">Pending</option>');
	print '</select>';


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>