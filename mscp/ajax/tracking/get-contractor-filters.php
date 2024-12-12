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


	$iContractors = getDbValue('COUNT(1)', 'tbl_contractors');


	$sSQL = "SELECT DISTINCT(city) FROM tbl_contractors WHERE city!='' ORDER BY city";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	if ($iCount > 1)
	{
		print '<select id="City">';
		print '<option value="">All Cities</option>';

		for ($i = 0; $i < $iCount; $i ++)
		{
			$sCity = $objDb->getField($i, "city");

			print @utf8_encode('<option value="'.$sCity.'">'.$sCity.'</option>');
		}

		print '</select>';
	}



	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>