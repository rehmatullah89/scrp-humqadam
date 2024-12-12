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


	$iReasonId = IO::intValue("ReasonId");
	$iIndex      = IO::intValue("Index");

	if ($_POST)
		@include("update-reason.php");


	$sSQL = "SELECT * FROM tbl_failure_reasons WHERE id='$iReasonId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sReason = $objDb->getField(0, "reason");
	$sStatus   = $objDb->getField(0, "status");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-reason.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="DuplicateReason" id="DuplicateReason" value="0" />
	<input type="hidden" name="ReasonId" id="ReasonId" value="<?= $iReasonId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<div id="RecordMsg" class="hidden"></div>

	<label for="txtReason">Reason</label>
	<div><input type="text" name="txtReason" id="txtReason" value="<?= formValue($sReason) ?>" maxlength="200" size="40" class="textbox" /></div>

	<div class="br10"></div>

	<label for="ddStatus">Status</label>

	<div>
	  <select name="ddStatus" id="ddStatus">
		<option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
		<option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
	  </select>
	</div>

	<br />
	<button id="BtnSave">Save Reason</button>
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