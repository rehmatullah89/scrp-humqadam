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
	$sDocumentsSQL = "FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sDocumentsSQL .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";	

	
	$iDocuments     = getDbValue('COUNT(1)', 'tbl_documents d, tbl_schools s', "d.school_id=s.id AND {$sDocumentsSQL}");
	$sProvincesList = getList("tbl_provinces", "id", "name");
        $sDocTypeList   = getList("tbl_document_types", "id", "title", "status='A'");

	if (count($sProvincesList) > 1)
	{
		print '<select id="District">';
		print '<option value="">All Districts</option>';

		foreach ($sProvincesList as $iProvince => $sProvince)
		{
			print @utf8_encode('<optgroup label="'.$sProvince.'">');


			$sDistrictsList = getList("tbl_districts", "id", "name", "province_id='$iProvince' AND id IN (SELECT DISTINCT(district_id) FROM tbl_documents)");

			foreach ($sDistrictsList as $iDistrict => $sDistrict)
			{
				print @utf8_encode('<option value="'.(($iDocuments > 50) ? $iDistrict : $sDistrict).'">'.$sDistrict.'</option>');
			}

			print '</optgroup>';
		}

		print '</select>';
	}

	
	print '<select id="DocType">';
	print '<option value="">All Document Types</option>';
	
	foreach ($sDocTypeList as $iDocType => $sDocType)
	{
		print @utf8_encode('<option value="'.(($iDocuments > 50) ? $iDocType : $sDocType).'">'.$sDocType.'</option>');
	}
	
	print '</select>';
	
	
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>