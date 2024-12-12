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
	$objDb3      = new Database( );

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iScheduleId = IO::intValue("ScheduleId");
	$iContractId = IO::intValue("ContractId");
	$iIndex      = IO::intValue("Index");

	if ($_POST)
		@include("update-schedule.php");


	$sSQL = "SELECT * FROM tbl_contract_schedules WHERE id='$iScheduleId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iContract  = $objDb->getField(0, "contract_id");
	$iSchool    = $objDb->getField(0, "school_id");
	$sStartDate = $objDb->getField(0, "start_date");
	$sEndDate   = $objDb->getField(0, "end_date");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-schedule.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="ScheduleId" id="ScheduleId" value="<?= $iScheduleId ?>" />
	<input type="hidden" name="ContractId" id="ContractId" value="<?= $iContract ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<div id="RecordMsg" class="hidden"></div>

    <label><b>Contract</b></label>
    <div><?= getDbValue("title", "tbl_contracts", "id='$iContract'") ?></div>

	<div class="br10"></div>

    <label><b>School</b></label>
    <div><?= getDbValue("name", "tbl_schools", "id='$iSchool'") ?></div>

    <div class="br10"></div>

    <label for="txtStartDate">Start Date</label>
    <div class="date"><input type="text" name="txtStartDate" id="txtStartDate" value="<?= $sStartDate ?>" maxlength="10" size="10" class="textbox" readonly /></div>

    <div class="br10"></div>

    <label for="txtEndDate">End Date</label>
    <div class="date"><input type="text" name="txtEndDate" id="txtEndDate" value="<?= $sEndDate ?>" maxlength="10" size="10" class="textbox" readonly /></div>

    <br />
    <br />
    <button id="BtnSave">Save Schedule</button>
    <button id="BtnCancel">Cancel</button>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>