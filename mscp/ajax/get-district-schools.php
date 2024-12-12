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


	$iDistrict    = IO::intValue("District");
	$sInspections = IO::strValue("Inspections");

	if ($sInspections == "Y")
		$sSchoolsList = getList("tbl_schools", "id", "name", "id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE district_id='$iDistrict')");

	else
		$sSchoolsList = getList("tbl_schools", "id", "CONCAT(code, ' - ', name)", "district_id='$iDistrict'");
?>
		<option value=""></option>
<?
	foreach ($sSchoolsList as $iSchool => $sSchool)
	{
?>
		<option value="<?= $iSchool ?>"><?= $sSchool ?></option>
<?
	}


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>