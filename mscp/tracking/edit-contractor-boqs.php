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


	$iContractorId = IO::intValue("ContractorId");

	if ($_POST)
		@include("save-contractor-boqs.php");


	$sSQL = "SELECT company FROM tbl_contractors WHERE id='$iContractorId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sCompany = $objDb->getField(0, "company");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-contractor-boqs.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="ContractorId" id="ContractorId" value="<?= $iContractorId ?>" />
	<div id="RecordMsg" class="hidden"></div>

	<b style="font-size:13px;">Company</b>
	<div style="font-size:13px;"><?= $sCompany ?></div>


	<h3 style="margin:30px 0px 15px 0px;">BOQ Details</h3>

	<div class="grid">
	  <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
		<tr class="header">
		  <td width="5%">#</td>
		  <td width="70%">BOQ Item</td>
		  <td width="10%">Unit</td>
		  <td width="15%" align="center">Rate (PKR)</td>
		</tr>
<?
	$sSQL = "INSERT INTO tbl_contractor_boqs (contractor_id, boq_id, `rate`) (SELECT '$iContractorId', id, '0' FROM tbl_boqs WHERE id NOT IN (SELECT cb.boq_id FROM tbl_contractor_boqs cb WHERE cb.contractor_id='$iContractorId'))";
	$objDb->execute($sSQL);



	$sSQL = "SELECT b.id, b.title, b.unit,
	                cb.rate
	         FROM tbl_contractor_boqs cb, tbl_boqs b
	         WHERE b.id=cb.boq_id AND cb.contractor_id='$iContractorId'
	         ORDER BY b.position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iBoq   = $objDb->getField($i, "id");
		$sTitle = $objDb->getField($i, "title");
		$sUnit  = $objDb->getField($i, "unit");
		$fRate  = $objDb->getField($i, "rate");
?>

		<tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		  <td><?= ($i + 1) ?></td>
		  <td><?= $sTitle ?></td>
		  <td><?= $sUnit ?></td>
		  <td align="center"><input type="text" name="txtRate<?= $iBoq ?>" id="txtRate<?= $iBoq ?>" value="<?= $fRate ?>" maxlength="10" size="10" class="textbox" /></td>
		</tr>
<?
	}
?>
	  </table>
	</div>


	<br />
	<button id="BtnSave">Save BOQs</button>
	<button id="BtnCancel">Cancel</button>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>