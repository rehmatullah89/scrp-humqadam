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


	$iSurveys       = getDbValue("COUNT(1)", "tbl_surveys");
	$sProvincesList = getList("tbl_provinces", "id", "name");

	if (count($sProvincesList) > 1)
	{
		print '<select id="District">';
		print '<option value="">All Districts</option>';

		foreach ($sProvincesList as $iProvince => $sProvince)
		{
			print @utf8_encode('<optgroup label="'.$sProvince.'">');


			$sDistrictsList = getList("tbl_districts", "id", "name", "province_id='$iProvince' AND id IN (SELECT DISTINCT(district_id) FROM tbl_sors)");

			foreach ($sDistrictsList as $iDistrict => $sDistrict)
			{
				print @utf8_encode('<option value="'.(($iSurveys > 50) ? $iDistrict : $sDistrict).'">'.$sDistrict.'</option>');
			}

			print '</optgroup>';
		}

		print '</select>';
	}


	print '<select id="SorStatus">';
	print '<option value="">Sor Status</option>';
	print @utf8_encode('<option value="'.(($iSurveys > 50) ? "Y" : "Completed").'">Completed</option>');
	print @utf8_encode('<option value="'.(($iSurveys > 50) ? "N" : "In-Complete").'">In-Complete</option>');
	print '</select>';
        
    print '<select id="SyncStatus">';
	print '<option value="">Sync Status</option>';
	print @utf8_encode('<option value="'.(($iSurveys > 50) ? "C" : "Synced").'">Synced</option>');
	print @utf8_encode('<option value="'.(($iSurveys > 50) ? "I" : "Syncing").'">Syncing</option>');
	print '</select>';
        

	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>