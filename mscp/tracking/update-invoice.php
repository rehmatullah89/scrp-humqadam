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

	$iContract    = IO::intValue("ddContract");
	$iSchool      = IO::intValue("ddSchool");
	$sInvoiceNo   = IO::strValue("txtInvoiceNo");
	$sTitle       = IO::strValue("txtTitle");
	$sDetails     = IO::strValue("txtDetails");
	$sDate        = IO::strValue("txtDate");
	$sInspections = @implode(",", IO::getArray("cbInspections"));
	$sChequeNo    = IO::strValue("txtChequeNo");
	$sStatus      = IO::strValue("ddStatus");


	if ($iContract == 0 || $iSchool == 0 || $sInvoiceNo == "" || $sDate == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_invoices WHERE invoice_no LIKE '$sInvoiceNo' AND id!='$iInvoiceId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "INVOICE_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		$sOldInspections = getDbValue("inspections", "tbl_invoices", "id='$iInvoiceId'");
		$fAmount         = getDbValue("SUM(amount)", "tbl_inspection_measurements", "school_id='$iSchool' AND FIND_IN_SET(inspection_id, '$sInspections')");


		$objDb->execute("BEGIN");


		$sSQL  = "UPDATE tbl_invoices SET contract_id = '$iContract',
										  school_id   = '$iSchool',
										  invoice_no  = '$sInvoiceNo',
										  title       = '$sTitle',
										  `date`      = '$sDate',
										  details     = '$sDetails',
										  inspections = '$sInspections',
										  amount      = '$fAmount',
										  status      = '$sStatus',
										  cheque_no   = '$sChequeNo',
										  modified_by = '{$_SESSION['AdminId']}',
										  modified_at = NOW( )
		         WHERE id='$iInvoiceId'";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == true)
		{
			$sSQL  = "UPDATE tbl_inspections SET invoice_id='$iInvoiceId' WHERE FIND_IN_SET(id, '$sInspections')";
			$bFlag = $objDb->execute($sSQL);
		}

		if ($bFlag == true)
		{
			$sSQL  = "UPDATE tbl_inspections SET invoice_id='0' WHERE FIND_IN_SET(id, '$sOldInspections') AND NOT FIND_IN_SET(id, '$sInspections')";
			$bFlag = $objDb->execute($sSQL);
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");
?>
<script type="text/javascript">
<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sInvoiceNo) ?>";
		sFields[1] = "<?= addslashes($sTitle) ?>";
		sFields[2] = "<?= addslashes(getDbValue("name", "tbl_schools", "id='$iSchool'")) ?>";
		sFields[3] = "<?= addslashes(getDbValue("title", "tbl_contracts", "id='$iContract'")) ?>";
		sFields[4] = "<?= formatDate($sDate, $_SESSION["DateFormat"]) ?>";
		sFields[5] = "<?= formatNumber($fAmount, false) ?>";
		sFields[6] = "<?= (($sStatus == "P") ? "Paid" : "Un-Paid") ?>";

		parent.updateRecord(<?= $iInvoiceId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Invoice Entry has been Updated successfully.");
-->
</script>
<?
			exit( );
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION['Flag'] = "DB_ERROR";
		}
	}
?>