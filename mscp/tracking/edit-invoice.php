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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iInvoiceId  = IO::intValue("InvoiceId");
	$iContractId = IO::intValue("ContractId");
	$iIndex      = IO::intValue("Index");

	if ($_POST)
		@include("update-invoice.php");


	$sSQL = "SELECT * FROM tbl_invoices WHERE id='$iInvoiceId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iContract    = $objDb->getField(0, "contract_id");
	$iSchool      = $objDb->getField(0, "school_id");
	$sInvoiceNo   = $objDb->getField(0, "invoice_no");
	$sTitle       = $objDb->getField(0, "title");
	$sDate        = $objDb->getField(0, "date");
	$sDetails     = $objDb->getField(0, "details");
	$sInspections = $objDb->getField(0, "inspections");
	$sChequeNo    = $objDb->getField(0, "cheque_no");
	$sStatus      = $objDb->getField(0, "status");


	$iInspections = @explode(",", $sInspections);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-invoice.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="InvoiceId" id="InvoiceId" value="<?= $iInvoiceId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<input type="hidden" name="DuplicateInvoice" id="DuplicateInvoice" value="0" />
	<div id="RecordMsg" class="hidden"></div>

	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr valign="top">
		<td width="500">
		  <label for="ddContract">Contract</label>

		  <div>
			<select name="ddContract" id="ddContract">
			  <option value=""></option>
<?
	$sContractsList = getList("tbl_contracts", "id", "title");

	foreach ($sContractsList as $iContractId => $sContract)
	{
?>
			  <option value="<?= $iContractId ?>"<?= (($iContractId == $iContract) ? ' selected' : '') ?>><?= $sContract ?></option>
<?
	}
?>
			</select>
		  </div>

		  <div class="br10"></div>

		  <label for="ddSchool">School</label>

		  <div>
			<select name="ddSchool" id="ddSchool" style="min-width:200px;">
			  <option value=""></option>
<?
	$sSchools     = getDbValue("schools", "tbl_contracts", "id='$iContract'");
	$sSchoolsList = getList("tbl_schools", "id", "name", "FIND_IN_SET(id, '$sSchools')");

	foreach ($sSchoolsList as $iSchoolId => $sSchool)
	{
?>
			  <option value="<?= $iSchoolId ?>"<?= (($iSchoolId == $iSchool) ? ' selected' : '') ?>><?= $sSchool ?></option>
<?
	}
?>
			</select>
		  </div>

		  <div class="br10"></div>

		  <label for="txtInvoiceNo">Invoice #</label>
		  <div><input type="text" name="txtInvoiceNo" id="txtInvoiceNo" value="<?= $sInvoiceNo ?>" maxlength="20" size="20" class="textbox" /></div>
		  
		  <div class="br10"></div>
		  		  
		  <label for="txtTitle">Invoice Title</label>
		  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="200" size="48" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtDetails" >Details <span>(Optional)</span></label>
		  <div><textarea name="txtDetails" id="txtDetails" style="width:350px; height:120px;"><?= $sDetails ?></textarea></div>

		  <div class="br10"></div>

		  <label for="txtChequeNo">Cheque No</label>
		  <div><input type="text" name="txtChequeNo" id="txtChequeNo" value="<?= $sChequeNo ?>" maxlength="20" size="20" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtDate">Date</label>
		  <div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= $sDate ?>" maxlength="10" size="10" class="textbox" readonly /></div>

		  <div class="br10"></div>

		  <label for="ddStatus">Status</label>

		  <div>
			<select name="ddStatus" id="ddStatus" >
			  <option value="P"<?= (($sStatus == 'P') ? ' selected' : '') ?>>Paid</option>
			  <option value="U"<?= (($sStatus == 'U') ? ' selected' : '') ?>>Un-Paid</option>
			</select>
		  </div>

		  <br />
		  <button id="BtnSave">Save Invoice</button>
		  <button id="BtnCancel">Cancel</button>
		</td>

		<td>
		  <label>Inspections</label>

		  <div id="Inspections" class="multiSelect" style="width:500px; height:280px;">
			<table border="0" cellpadding="0" cellspacing="1" width="100%">
<?
	$sSQL = "SELECT id, title, `date` FROM tbl_inspections WHERE ((status='A' AND invoice_id='0') OR invoice_id='$iInvoiceId') AND school_id='$iSchool' ORDER BY id DESC";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iInspection = $objDb->getField($i, "id");
		$sTitle      = $objDb->getField($i, "title");
		$sDate       = $objDb->getField($i, "date");
?>
			  <tr>
				<td width="25"><input type="checkbox" class="inspection" name="cbInspections[]" id="cbInspection<?= $i ?>" value="<?= $iInspection ?>" <?= ((@in_array($iInspection, $iInspections)) ? 'checked' : '') ?> /></td>
				<td><label for="cbInspection<?= $i ?>"><?= $sTitle ?> <span><?= formatDate($sDate, $_SESSION["DateFormat"]) ?></span></label></td>
			  </tr>
<?
	}
?>
			</table>
		  </div>
		</td>
	  </tr>
	</table>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>