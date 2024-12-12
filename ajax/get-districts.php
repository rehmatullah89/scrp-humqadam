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


	$iProvinceId = IO::intValue("Province");
	$sType       = IO::strValue("Type");
?>
            <option value=""></option>
<?
	if ($sType == "Surveys")
	{
		$sSQL = "SELECT id, name
				 FROM tbl_districts
				 WHERE province_id='$iProvinceId' AND FIND_IN_SET(id, '{$_SESSION['AdminDistricts']}') AND id IN (SELECT DISTINCT(district_id) FROM tbl_survey_schedules)
				 ORDER BY name";
	}
	
	else
	{
		$sSQL = "SELECT id, name
				 FROM tbl_districts
				 WHERE province_id='$iProvinceId' AND FIND_IN_SET(id, '{$_SESSION['AdminDistricts']}') AND id IN (SELECT DISTINCT(district_id) FROM tbl_schools WHERE status='A' AND dropped!='Y')
				 ORDER BY name";		
	}

	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId   = $objDb->getField($i, "id");
		$sName = $objDb->getField($i, "name");
?>
            <option value="<?= $iId ?>"><?= $sName ?></option>
<?
	}


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>