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


	$iBoqs = getDbValue("COUNT(1)", "tbl_boqs");


	print '<select id="Unit">';
	print '<option value="">All Units</option>';
	print @utf8_encode('<option value="cft">cft</option>');
	print @utf8_encode('<option value="sft">sft</option>');
	print @utf8_encode('<option value="kg">kg</option>');
	print @utf8_encode('<option value="rft">rft</option>');
	print @utf8_encode('<option value="tons">tons</option>');
	print @utf8_encode('<option value="job">job</option>');
	print @utf8_encode('<option value="no.">no.</option>');
	print '</select>';


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>