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


	$sTerm       = IO::strValue("term");
	$sConditions = " AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";
	
	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(id, '{$_SESSION['AdminSchools']}') ";


	$sSQL = "SELECT name, code FROM tbl_schools WHERE status='A' AND code LIKE '{$sTerm}%' $sConditions ORDER BY code LIMIT 10";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );


	print '[';

	for ($i = 0; $i < $iCount; $i ++)
	{
		$sName = $objDb->getField($i, "name");
		$sCode = $objDb->getField($i, "code");


		print ('{ "id":"'.$sCode.'", "label": "'.addslashes($sCode).' ('.addslashes($sName).')", "value": "'.$sCode.'" }');

		if ($i < ($iCount - 1))
			print ', ';
	}

	print ']';


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>