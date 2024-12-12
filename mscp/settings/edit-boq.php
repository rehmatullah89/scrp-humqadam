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
	$objDb2      = new Database( );

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iBoqId = IO::intValue("BoqId");
	$iIndex = IO::intValue("Index");

	if ($_POST)
		@include("update-boq.php");


	$sSQL = "SELECT * FROM tbl_boqs WHERE id='$iBoqId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle  = $objDb->getField(0, "title");
	$sUnit   = $objDb->getField(0, "unit");
	$sStatus = $objDb->getField(0, "status");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-boq.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="BoqId" id="BoqId" value="<?= $iBoqId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<input type="hidden" name="DuplicateBoq" id="DuplicateBoq" value="0" />
	<div id="RecordMsg" class="hidden"></div>

    <label for="txtTitle">BOQ Title</label>
    <div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="1000" size="35" class="textbox" style="width:90%;" /></div>

	<div class="br10"></div>

	<label for="ddUnit">Measurement Unit</label>

	<div>
	  <select name="ddUnit" id="ddUnit">
		<option value=""<?= (($sUnit == '') ? ' selected' : '') ?>></option>
		<option value="cft"<?= (($sUnit == 'cft') ? ' selected' : '') ?>>cft</option>
		<option value="sft"<?= (($sUnit == 'sft') ? ' selected' : '') ?>>sft</option>
		<option value="kg"<?= (($sUnit == 'kg') ? ' selected' : '') ?>>kg</option>
		<option value="rft"<?= (($sUnit == 'rft') ? ' selected' : '') ?>>rft</option>
		<option value="tons"<?= (($sUnit == 'tons') ? ' selected' : '') ?>>tons</option>
		<option value="job"<?= (($sUnit == 'job') ? ' selected' : '') ?>>job</option>
		<option value="no."<?= (($sUnit == 'no.') ? ' selected' : '') ?>>no.</option>
	  </select>
	</div>

    <div class="br10"></div>

    <label for="ddStatus">Status</label>

    <div>
	  <select name="ddStatus" id="ddStatus">
	    <option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
  	    <option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
	  </select>
    </div>

    <br />
    <button id="BtnSave">Save BOQ</button>
    <button id="BtnCancel">Cancel</button>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>