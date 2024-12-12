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


	$iInvoices      = getDbValue('COUNT(1)', 'tbl_invoices');
	$sContractsList = getList("tbl_contracts", "id", "title");

	print '<select id="Contract">';
	print '<option value="">All Contracts</option>';

	foreach ($sContractsList as $iContract => $sContract)
	{
		print @utf8_encode('<option value="'.(($iInvoices <= 100) ? $sContract : $iContract).'">'.$sContract.'</option>');
	}

	print '</select>';


	print '<select id="Status">';
	print '<option value="">Any Status</option>';
	print @utf8_encode('<option value="'.(($iInvoices <= 100) ? 'Paid' : 'P').'">Paid</option>');
	print @utf8_encode('<option value="'.(($iInvoices <= 100) ? 'Un-Paid' : 'U').'">Un-Paid</option>');
	print '</select>';



	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>