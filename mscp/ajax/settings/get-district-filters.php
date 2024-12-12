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


	$iDistricts     = getDbValue("COUNT(1)", "tbl_districts");
	$sProvincesList = getList("tbl_provinces", "id", "name");

	if (count($sProvincesList) > 1)
	{
		print '<select id="Province">';
		print '<option value="">All Provinces</option>';

		foreach ($sProvincesList as $iProvince => $sProvince)
		{
			print @utf8_encode('<option value="'.(($iDistricts > 100) ? $iProvince : $sProvince).'">'.$sProvince.'</option>');
		}

		print '</select>';
	}

	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>