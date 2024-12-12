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

	$_SESSION["Flag"] = "";

	$iContract    = IO::intValue("ddContract");
	$iSchool      = IO::intValue("ddSchool");
	$sInvoiceNo   = IO::strValue("txtInvoiceNo");
	$sTitle       = IO::strValue("txtTitle");
	$sDetails     = IO::strValue("txtDetails");
	$sDate        = IO::strValue("txtDate");
	$sInspections = @implode(",", IO::getArray("cbInspections"));
	$bError       = true;


	if ($iContract == 0 || $iSchool == 0 || $sInvoiceNo == "" || $sTitle == "" || $sDate == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_invoices WHERE invoice_no LIKE '$sInvoiceNo'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "INVOICE_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		$objDb->execute("BEGIN");


		$fAmount  = getDbValue("SUM(amount)", "tbl_inspection_measurements", "school_id='$iSchool' AND FIND_IN_SET(inspection_id, '$sInspections')");
		$iInvoice = getNextId("tbl_invoices");

		$sSQL  = "INSERT INTO tbl_invoices SET id          = '$iInvoice',
											   contract_id = '$iContract',
											   school_id   = '$iSchool',
											   invoice_no  = '$sInvoiceNo',
											   title       = '$sTitle',
											   `date`      = '$sDate',
											   details     = '$sDetails',
											   inspections = '$sInspections',
											   amount      = '$fAmount',
											   status      = 'U',
											   cheque_no   = '',
											   created_by  = '{$_SESSION['AdminId']}',
											   created_at  = NOW( ),
											   modified_by = '{$_SESSION['AdminId']}',
											   modified_at = NOW( )";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == true)
		{
			$sSQL  = "UPDATE tbl_inspections SET invoice_id='$iInvoice' WHERE FIND_IN_SET(id, '$sInspections')";
			$bFlag = $objDb->execute($sSQL);
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			redirect("invoices.php", "INVOICE_ADDED");
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";
		}
	}
?>